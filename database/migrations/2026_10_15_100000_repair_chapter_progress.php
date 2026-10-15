<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Repara el progreso por capítulo y evita que se vuelva a romper (#1877).
 *
 * Dos averías distintas con el mismo efecto: el capítulo no se daba por terminado y el
 * curso se quedaba sin avanzar.
 *
 * 1. El quiz del capítulo se podía hacer desde el menú sin haber visto todo su contenido.
 *    A partir de ahora se exige, así que a quien ya aprobó el quiz se le dan por vistos
 *    los apartados que le faltaban: de lo contrario quedaría atascado por una regla que
 *    no existía cuando hizo el curso. En QA eran ocho capítulos, a todos les faltaba uno.
 * 2. Dos `firstOrCreate` a la vez podían crear dos filas para el mismo capítulo, y el
 *    progreso quedaba partido entre ambas. Se fusionan y se añade el índice único que
 *    faltaba, con el que Laravel resuelve solo la carrera.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->mergeDuplicatedChapters();
        $this->markContentsOfPassedQuizzes();

        if (! $this->hasUniqueIndex()) {
            Schema::table('user_course_chapters', function (Blueprint $table): void {
                $table->unique(['user_course_id', 'chapter_id'], 'user_course_chapters_enrollment_chapter_unique');
            });
        }
    }

    public function down(): void
    {
        // Las filas fusionadas no se pueden volver a separar y los apartados dados por
        // vistos no deberían retirarse, así que sólo se deshace el índice.
        if ($this->hasUniqueIndex()) {
            Schema::table('user_course_chapters', function (Blueprint $table): void {
                $table->dropUnique('user_course_chapters_enrollment_chapter_unique');
            });
        }
    }

    /** Reúne en la fila más antigua el progreso repartido entre filas gemelas. */
    private function mergeDuplicatedChapters(): void
    {
        $grupos = DB::table('user_course_chapters')
            ->select('user_course_id', 'chapter_id', DB::raw('MIN(id) as survivor'))
            ->groupBy('user_course_id', 'chapter_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($grupos as $grupo) {
            DB::transaction(function () use ($grupo): void {
                $perdedoras = DB::table('user_course_chapters')
                    ->where('user_course_id', $grupo->user_course_id)
                    ->where('chapter_id', $grupo->chapter_id)
                    ->where('id', '!=', $grupo->survivor)
                    ->pluck('id');

                $yaVistos = DB::table('user_course_chapter_contents')
                    ->where('user_course_chapter_id', $grupo->survivor)
                    ->pluck('content_id');

                // Lo que la superviviente no tenga se traspasa; lo repetido se descarta.
                DB::table('user_course_chapter_contents')
                    ->whereIn('user_course_chapter_id', $perdedoras)
                    ->whereNotIn('content_id', $yaVistos)
                    ->update(['user_course_chapter_id' => $grupo->survivor]);

                DB::table('user_course_chapter_contents')->whereIn('user_course_chapter_id', $perdedoras)->delete();

                $quizAprobado = DB::table('user_course_chapters')
                    ->where('user_course_id', $grupo->user_course_id)
                    ->where('chapter_id', $grupo->chapter_id)
                    ->max('quiz_result');

                if ((int) $quizAprobado === 1) {
                    DB::table('user_course_chapters')->where('id', $grupo->survivor)->update(['quiz_result' => 1]);
                }

                DB::table('user_course_chapters')->whereIn('id', $perdedoras)->delete();
            });
        }
    }

    /** Da por leídos los apartados que falten en capítulos con el quiz ya aprobado. */
    private function markContentsOfPassedQuizzes(): void
    {
        $capitulos = DB::table('user_course_chapters')->where('quiz_result', 1)->get(['id', 'chapter_id']);
        $ahora = now();

        foreach ($capitulos as $capitulo) {
            $todos = DB::table('chaptercontents')->where('chapter_id', $capitulo->chapter_id)->pluck('id');

            if ($todos->isEmpty()) {
                continue;
            }

            $vistos = DB::table('user_course_chapter_contents')
                ->where('user_course_chapter_id', $capitulo->id)
                ->pluck('content_id');

            $faltan = $todos->diff($vistos);

            if ($faltan->isEmpty()) {
                continue;
            }

            DB::table('user_course_chapter_contents')->insert(
                $faltan->map(fn (int $contentId): array => [
                    'user_course_chapter_id' => $capitulo->id,
                    'content_id' => $contentId,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ])->all()
            );
        }
    }

    private function hasUniqueIndex(): bool
    {
        foreach (DB::select('SHOW INDEX FROM user_course_chapters') as $index) {
            if ($index->Key_name === 'user_course_chapters_enrollment_chapter_unique') {
                return true;
            }
        }

        return false;
    }
};

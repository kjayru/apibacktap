<?php

namespace App\Services;

use App\Exceptions\CartException;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\User;
use App\Models\UserCourse;
use App\Models\UserCourseChapter;
use App\Models\UserCourseChapterContent;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Reglas del curso (tablero TAP Security, lista REGLAS DEL CURSO, fichas #525 y #526),
 * en un solo sitio para que el aprendizaje, el examen, el certificado y el carrito
 * decidan lo mismo:
 *
 *  - un quiz de capítulo se aprueba con el 75 % o más, con reintentos ilimitados, y hasta
 *    aprobarlo no se abre el capítulo siguiente;
 *  - el examen final admite 3 intentos; al tercer fallo el alumno puede reiniciar el
 *    curso una sola vez con 15 días, o comprarlo de nuevo;
 *  - la matrícula caduca al agotar sus días (los del curso, o los 15 del reinicio) y a
 *    partir de ahí sólo cabe recomprar;
 *  - se puede comprar el mismo curso varias veces —también después de aprobarlo— salvo
 *    mientras haya una matrícula en curso con días vigentes.
 */
class CourseAccessService
{
    public const PASS_THRESHOLD = 75;

    public const MAX_EXAM_ATTEMPTS = 3;

    public const RETAKE_DAYS = 15;

    /** La matrícula vigente es siempre la última: una recompra o un reinicio crean fila nueva. */
    public function latestEnrollment(User $user, Course $course): ?UserCourse
    {
        $userCourse = UserCourse::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->latest('id')
            ->first();

        return $userCourse ? $this->expireIfDue($userCourse) : null;
    }

    /**
     * Matrícula con la que se puede seguir cursando. Sin matrícula o caducada → 403 con el
     * estado, para que el frontend ofrezca reinicio o recompra en vez de una pantalla vacía.
     */
    public function resolve(User $user, Course $course): UserCourse
    {
        $userCourse = $this->latestEnrollment($user, $course);

        if (! $userCourse) {
            throw new HttpException(403, 'You do not have access to this course.');
        }

        if ((int) $userCourse->caducado === 1) {
            throw new HttpException(403, 'Your access to this course has expired. Please purchase the course again.');
        }

        return $userCourse;
    }

    /** Días que quedan, contados desde la fecha de inicio guardada (no desde created_at). */
    public function daysLeft(UserCourse $userCourse): ?int
    {
        if ($userCourse->dias_activo === null || $userCourse->dias_activo === '') {
            return null;
        }

        $start = $this->startDate($userCourse);

        return (int) $userCourse->dias_activo - (int) $start->startOfDay()->diffInDays(Carbon::now()->startOfDay());
    }

    /**
     * Caduca la matrícula si agotó sus días. Un curso aprobado no caduca nunca: la
     * certificación es permanente. Se aplica al leer, como hacía el sitio anterior
     * en "My courses", así no hace falta un cron.
     */
    public function expireIfDue(UserCourse $userCourse): UserCourse
    {
        if ((int) $userCourse->aprobado === 1 || (int) $userCourse->caducado === 1) {
            return $userCourse;
        }

        $daysLeft = $this->daysLeft($userCourse);

        if ($daysLeft !== null && $daysLeft <= 0) {
            $userCourse->forceFill(['caducado' => 1, 'finalizado' => 1])->save();
        }

        return $userCourse;
    }

    public function examLocked(UserCourse $userCourse): bool
    {
        return (int) $userCourse->aprobado !== 1 && (int) $userCourse->intentos >= self::MAX_EXAM_ATTEMPTS;
    }

    /** El reinicio gratuito es uno solo: la fila reiniciada no se vuelve a reiniciar. */
    public function canRestart(UserCourse $userCourse): bool
    {
        return $this->examLocked($userCourse)
            && (int) $userCourse->caducado !== 1
            && (int) $userCourse->reiniciado !== 1
            && ! UserCourse::where('parent_id', $userCourse->id)->exists();
    }

    public function canRepurchase(?UserCourse $userCourse): bool
    {
        if (! $userCourse) {
            return true;
        }

        return (int) $userCourse->aprobado === 1
            || (int) $userCourse->caducado === 1
            || $this->examLocked($userCourse);
    }

    /** Lo que el frontend necesita para pintar el estado sin volver a calcular reglas. */
    public function state(UserCourse $userCourse): array
    {
        $attemptsUsed = (int) $userCourse->intentos;

        return [
            'status' => match (true) {
                (int) $userCourse->aprobado === 1 => 'approved',
                (int) $userCourse->caducado === 1 => 'expired',
                $this->examLocked($userCourse) => 'exam_locked',
                default => 'active',
            },
            'days_left' => $this->daysLeft($userCourse) !== null ? max(0, $this->daysLeft($userCourse)) : null,
            'approved' => (int) $userCourse->aprobado === 1,
            'finished' => (int) $userCourse->finalizado === 1,
            'expired' => (int) $userCourse->caducado === 1,
            'restarted' => (int) $userCourse->reiniciado === 1,
            'exam_attempts_used' => $attemptsUsed,
            'exam_attempts_left' => max(0, self::MAX_EXAM_ATTEMPTS - $attemptsUsed),
            'exam_max_attempts' => self::MAX_EXAM_ATTEMPTS,
            'can_restart' => $this->canRestart($userCourse),
            'can_repurchase' => $this->canRepurchase($userCourse),
            'retake_days' => self::RETAKE_DAYS,
        ];
    }

    /** Regla de compra: se rechaza sólo mientras la última matrícula sigue en curso. */
    public function assertPurchasable(User $user, Course $course): void
    {
        $latest = $this->latestEnrollment($user, $course);

        if (! $this->canRepurchase($latest)) {
            throw new CartException('You still have this course in progress. Finish it or wait until it expires before purchasing it again.');
        }
    }

    /**
     * Reinicio tras agotar los intentos del examen: fila nueva con 15 días desde hoy y el
     * progreso a cero; la anterior queda cerrada. Los días que le quedaran a la compra
     * original se pierden (regla 7).
     */
    public function restart(UserCourse $userCourse): UserCourse
    {
        if ((int) $userCourse->aprobado === 1) {
            throw new CartException('You already have the course approved.');
        }

        if (! $this->examLocked($userCourse)) {
            throw new CartException('You can only restart the course after using your ' . self::MAX_EXAM_ATTEMPTS . ' exam attempts.');
        }

        if (! $this->canRestart($userCourse)) {
            throw new CartException('The course has already been restarted. Please purchase the course again.');
        }

        return DB::transaction(function () use ($userCourse): UserCourse {
            $userCourse->forceFill(['finalizado' => 1])->save();

            return UserCourse::create([
                'user_id' => $userCourse->user_id,
                'course_id' => $userCourse->course_id,
                'fecha_inicio' => Carbon::now()->toDateString(),
                'dias_activo' => self::RETAKE_DAYS,
                'aprobado' => 0,
                'intentos' => 0,
                'reiniciado' => 1,
                'caducado' => 0,
                'finalizado' => 0,
                'parent_id' => $userCourse->id,
            ]);
        });
    }

    /** Un capítulo está hecho cuando se vieron todos sus contenidos y, si tiene quiz, se aprobó. */
    public function chapterCompleted(UserCourse $userCourse, Chapter $chapter): bool
    {
        $userChapter = UserCourseChapter::where('user_course_id', $userCourse->id)
            ->where('chapter_id', $chapter->id)
            ->first();

        $totalContents = $chapter->chaptercontents()->count();
        $doneContents = $userChapter
            ? UserCourseChapterContent::where('user_course_chapter_id', $userChapter->id)->count()
            : 0;

        if ($totalContents === 0 || $doneContents < $totalContents) {
            return false;
        }

        $hasQuiz = $chapter->chapterquizzes()->exists() || filled($chapter->quiz);

        return ! $hasQuiz || ($userChapter && (int) $userChapter->quiz_result === 1);
    }

    /** Regla 1: no se sirve un capítulo hasta que todos los anteriores estén hechos. */
    public function assertChapterUnlocked(UserCourse $userCourse, Course $course, Chapter $chapter): void
    {
        $previous = Chapter::where('course_id', $course->id)
            ->where('order', '<', $chapter->order)
            ->orderBy('order')
            ->get();

        foreach ($previous as $previousChapter) {
            if (! $this->chapterCompleted($userCourse, $previousChapter)) {
                throw new HttpException(403, 'You need to pass the quiz of "' . $previousChapter->title . '" before continuing to this chapter.');
            }
        }
    }

    private function startDate(UserCourse $userCourse): Carbon
    {
        if (filled($userCourse->fecha_inicio)) {
            try {
                return Carbon::parse($userCourse->fecha_inicio);
            } catch (\Throwable) {
                // fecha_inicio es varchar heredado; si no se puede leer, vale la de creación.
            }
        }

        return Carbon::parse($userCourse->created_at ?? Carbon::now());
    }
}

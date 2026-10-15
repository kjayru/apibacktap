<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Pone de acuerdo los dos contadores de intentos del examen (#1870).
 *
 * El sitio muestra al alumno `user_courses.intentos` y el panel mostraba
 * `user_course_exams.intentos`. Al aprobar sólo subía el segundo, así que en las
 * matrículas aprobadas los dos números no coincidían: 142 en producción y 118 en QA.
 *
 * Sólo se tocan las matrículas **aprobadas**. En una sin aprobar, subir el contador
 * podría dejar al alumno sin intentos y cerrarle el examen, y eso no se arregla con una
 * migración.
 */
return new class extends Migration
{
    public function up(): void
    {
        $desajustadas = DB::table('user_courses as uc')
            ->join('user_course_exams as e', 'e.user_course_id', '=', 'uc.id')
            ->where('uc.aprobado', 1)
            ->whereRaw('COALESCE(uc.intentos, 0) < COALESCE(e.intentos, 0)')
            ->select('uc.id', DB::raw('MAX(e.intentos) as rendidos'))
            ->groupBy('uc.id')
            ->get();

        foreach ($desajustadas as $fila) {
            DB::table('user_courses')->where('id', $fila->id)->update(['intentos' => (int) $fila->rendidos]);
        }
    }

    public function down(): void
    {
        // No se revierte: devolver los contadores a un valor menor volvería a separarlos.
    }
};

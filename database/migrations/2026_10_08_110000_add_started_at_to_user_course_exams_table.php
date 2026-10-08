<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Hasta ahora el examen sólo caducaba si el alumno dejaba la pestaña abierta: el aviso de
 * "se acabó el tiempo" lo mandaba el navegador. Quien cerraba la ventana dejaba el intento
 * en el aire y no se le contaba (#1824). Con la hora de inicio guardada, el servidor sabe
 * cuándo venció el intento aunque nadie se lo diga.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_course_exams', function (Blueprint $table): void {
            if (! Schema::hasColumn('user_course_exams', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('tiempo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_course_exams', function (Blueprint $table): void {
            if (Schema::hasColumn('user_course_exams', 'started_at')) {
                $table->dropColumn('started_at');
            }
        });
    }
};

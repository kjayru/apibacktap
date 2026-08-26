<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * El formulario de empleo del sitio anterior pedía "If yes, what major" y
 * "Level completed" cuando el aspirante marcaba que se graduó de college.
 * Los dos campos se perdieron al migrar el front y la tabla nunca los tuvo,
 * así que hoy no hay dónde guardarlos.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('educations', function (Blueprint $table): void {
            if (! Schema::hasColumn('educations', 'whatmayor')) {
                $table->string('whatmayor')->nullable()->after('collageto');
            }
            if (! Schema::hasColumn('educations', 'completed')) {
                $table->string('completed')->nullable()->after('whatmayor');
            }
        });
    }

    public function down(): void
    {
        Schema::table('educations', function (Blueprint $table): void {
            foreach (['whatmayor', 'completed'] as $column) {
                if (Schema::hasColumn('educations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

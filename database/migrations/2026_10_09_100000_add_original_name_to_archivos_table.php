<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Los adjuntos del aspirante se guardaban con un nombre aleatorio y el panel sólo podía
 * mostrar ese, así que no se sabía qué documento era (#1736). Aquí se guarda el nombre
 * con el que el aspirante subió cada archivo.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('archivos', function (Blueprint $table): void {
            if (! Schema::hasColumn('archivos', 'original_name')) {
                $table->string('original_name')->nullable()->after('file');
            }
        });
    }

    public function down(): void
    {
        Schema::table('archivos', function (Blueprint $table): void {
            if (Schema::hasColumn('archivos', 'original_name')) {
                $table->dropColumn('original_name');
            }
        });
    }
};

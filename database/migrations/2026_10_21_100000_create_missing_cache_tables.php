<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * La caché va a base de datos, pero las tablas que necesita nunca llegaron por una
 * migración: en QA se crearon a mano. Sobre una base heredada de producción no están, y
 * sin ellas el panel no arranca.
 *
 * Mismo planteamiento que `2026_10_02_100000_create_missing_auth_tables`: se crean sólo
 * si faltan, para que la base de producción las reciba con el despliegue y QA no note
 * nada.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cache')) {
            Schema::create('cache', function (Blueprint $table): void {
                $table->string('key')->primary();
                $table->mediumText('value');
                $table->integer('expiration');
            });
        }

        if (! Schema::hasTable('cache_locks')) {
            Schema::create('cache_locks', function (Blueprint $table): void {
                $table->string('key')->primary();
                $table->string('owner');
                $table->integer('expiration');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};

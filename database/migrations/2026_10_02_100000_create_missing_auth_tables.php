<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Las bases heredadas de producción traen la tabla `users` del sistema viejo, así que
 * la migración inicial de Laravel nunca se pudo correr sobre ellas y se quedaron sin las
 * tablas que crea junto a esa: `password_reset_tokens` y `sessions`. Sin la primera,
 * "Forgot your password" revienta con "Server Error".
 *
 * Se crean sólo si faltan, para que también sirva al desplegar en producción.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table): void {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        if (! Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table): void {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Los adjuntos de las solicitudes de empleo que llegaron por el formulario nuevo se
 * guardaron en el disco por defecto (storage/app/private), fuera de /storage, y el
 * admin no podía abrirlos (#1454, #1669). Los del sitio anterior están en el disco
 * público; se mueven ahí los que falten. Idempotente: lo que ya está en el público no
 * se toca.
 *
 * En el server se ejecuta con `--path=database/migrations`.
 */
return new class extends Migration
{
    public function up(): void
    {
        $public = Storage::disk('public');
        $private = Storage::disk('local');

        foreach (DB::table('archivos')->pluck('file') as $path) {
            if (blank($path) || $public->exists($path) || ! $private->exists($path)) {
                continue;
            }

            $public->writeStream($path, $private->readStream($path));
            $private->delete($path);
        }
    }

    /** Mover los archivos de vuelta no tiene sentido: el admin no podría abrirlos. */
    public function down(): void {}
};

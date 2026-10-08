<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

/**
 * Quien se registró desde el front antes de #1825 quedó sin rol, y el listado
 * "User who is taking or has taken course(s)" filtra por el rol `usuario`: esas cuentas
 * no aparecían en el panel aunque hubieran comprado un curso. Se les asigna el rol que
 * les correspondía desde el principio.
 */
return new class extends Migration
{
    public function up(): void
    {
        $role = Role::where('name', 'usuario')->first();

        if (! $role) {
            return;
        }

        User::doesntHave('roles')->each(fn (User $user) => $user->assignRole($role));
    }

    public function down(): void
    {
        // Quitar el rol dejaría las cuentas como estaban por un fallo: no se revierte.
    }
};

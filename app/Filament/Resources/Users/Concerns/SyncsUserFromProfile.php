<?php

namespace App\Filament\Resources\Users\Concerns;

use App\Models\User;

trait SyncsUserFromProfile
{
    /**
     * El admin anterior, al guardar el perfil, copiaba nombre y correo a la cuenta
     * (UserController::update). Sin esto el listado seguiría mostrando el nombre viejo
     * después de editar, porque la columna Name sale de `users`.
     */
    protected function afterSave(): void
    {
        /** @var User $user */
        $user = $this->getRecord();
        $profile = $user->profile()->first();

        if (! $profile) {
            return;
        }

        $user->forceFill([
            'name' => $profile->firstname ?: $user->name,
            'email' => $profile->email ?: $user->email,
        ])->save();
    }
}

<?php

namespace App\Filament\Pages;

use Filament\Auth\Pages\EditProfile;

/**
 * "Configurations" del menú: el administrador actualiza su acceso (#1886).
 *
 * Hereda la pantalla de cuenta del panel, que pide la contraseña actual antes de
 * cambiarla, confirma la nueva y limita los intentos. Aquí sólo se le pone el nombre con
 * el que la conoce el cliente, para que coincida con el del menú.
 */
class Configurations extends EditProfile
{
    public static function getLabel(): string
    {
        return 'Configurations';
    }

    public function getTitle(): string
    {
        return 'Configurations';
    }

    public function getHeading(): string
    {
        return 'Configurations';
    }
}

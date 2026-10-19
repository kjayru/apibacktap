<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Auth\Pages\EditProfile;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * "Configurations" del menú: el administrador actualiza su acceso (#1886).
 *
 * Hereda la pantalla de cuenta del panel, que pide la contraseña actual antes de
 * cambiarla, confirma la nueva y limita los intentos. Aquí sólo se le pone el nombre con
 * el que la conoce el cliente, para que coincida con el del menú.
 */
class Configurations extends EditProfile
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Configurations';

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

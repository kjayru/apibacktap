<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class UserProfileForm
{
    /**
     * Lo que el admin anterior deja editar de un usuario no son sus credenciales sino
     * el formulario que rellenó al inscribirse, que vive en `profiles` (#1628, #1645).
     * Se respetan el orden y las etiquetas de backend/users/form/index.blade.php del
     * proyecto anterior para que el cliente encuentre los campos donde ya los conoce.
     *
     * El Fieldset con ->relationship() carga y guarda sobre el perfil, y lo crea si el
     * usuario todavía no tiene uno.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Profile')
                    ->relationship('profile')
                    ->columns(2)
                    ->schema([
                        TextInput::make('firstname')
                            ->label('First name')
                            ->required(),
                        TextInput::make('middlename')
                            ->label('Middle name'),
                        TextInput::make('lastname')
                            ->label('Last name')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required(),
                        TextInput::make('gender')
                            ->label('Gender'),
                        TextInput::make('birthday')
                            ->label('Date of Birth'),
                        TextInput::make('ssn')
                            ->label('Last 6 of SSN'),
                        TextInput::make('address1')
                            ->label('Address line 1'),
                        TextInput::make('address2')
                            ->label('Address line 2'),
                        TextInput::make('city')
                            ->label('City'),
                        TextInput::make('state')
                            ->label('State'),
                        TextInput::make('zipcode')
                            ->label('Zip/Postal code'),
                        TextInput::make('drivernumber')
                            ->label('Driver License Number'),
                        TextInput::make('driverstate')
                            ->label('Driver License State'),
                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel(),
                        TextInput::make('organization')
                            ->label('Organization'),
                        TextInput::make('emergencycontact')
                            ->label('Emergency Contact’s Name'),
                        TextInput::make('emergencyphone')
                            ->label('Phone contact')
                            ->tel(),
                        TextInput::make('relationship')
                            ->label('Relationship'),
                        TextInput::make('handguncaliber')
                            ->label('Handgun Caliber (optional)'),
                        TextInput::make('handguntype')
                            ->label('Handgun Type (optional)'),
                        TextInput::make('handgunrental')
                            ->label('Do you need a handgun rental? (optional)'),
                        TextInput::make('shootingshotgun')
                            ->label('Are you shooting shotgun? (optional)'),
                        TextInput::make('shotgungauce')
                            ->label('Shotgun Gauge (optional)'),
                        TextInput::make('shotgunrental')
                            ->label('Do you need a shotgun rental? (optional)'),
                    ]),
            ]);
    }
}

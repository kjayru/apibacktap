<?php

namespace App\Filament\Resources\Information\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InformationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('lastname')
                    ->required(),
                TextInput::make('firstname')
                    ->required(),
                TextInput::make('mi')
                    ->required(),
                TextInput::make('date')
                    ->required(),
                TextInput::make('address')
                    ->required(),
                TextInput::make('apartment')
                    ->default(null),
                TextInput::make('city')
                    ->required(),
                TextInput::make('state')
                    ->required(),
                TextInput::make('zipcode')
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('birthday')
                    ->required(),
                TextInput::make('socialnumber')
                    ->required(),
                TextInput::make('placebirth')
                    ->required(),
                TextInput::make('appliedpay')
                    ->required(),
                TextInput::make('whichshift')
                    ->required(),
                TextInput::make('whichday')
                    ->required(),
                TextInput::make('citizen')
                    ->required(),
                TextInput::make('authorized')
                    ->required(),
                TextInput::make('when')
                    ->default(null),
                TextInput::make('explain1')
                    ->default(null),
                TextInput::make('explain2')
                    ->default(null),
                TextInput::make('worked')
                    ->required(),
                TextInput::make('convicted')
                    ->required(),
                TextInput::make('indictment')
                    ->required(),
            ]);
    }
}

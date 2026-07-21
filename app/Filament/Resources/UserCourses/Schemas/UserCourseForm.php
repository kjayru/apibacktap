<?php

namespace App\Filament\Resources\UserCourses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserCourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('course_id')
                    ->required()
                    ->numeric(),
                TextInput::make('fecha_inicio')
                    ->required(),
                TextInput::make('dias_activo')
                    ->default(null),
                Toggle::make('aprobado'),
                Toggle::make('intentos'),
                Toggle::make('reiniciado'),
                Toggle::make('caducado'),
                Toggle::make('finalizado'),
                TextInput::make('parent_id')
                    ->numeric()
                    ->default(null),
            ]);
    }
}

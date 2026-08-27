<?php

namespace App\Filament\Resources\UserCourses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserCourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()->required(),
                Select::make('course_id')
                    ->relationship('course', 'titulo')
                    ->searchable()
                    ->preload()->required(),
                TextInput::make('fecha_inicio')
                    ->required(),
                TextInput::make('dias_activo')
                    ->default(null),
                Toggle::make('aprobado'),
                \Filament\Forms\Components\TextInput::make('intentos')
                    ->label('Exam attempts used')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(\App\Services\CourseAccessService::MAX_EXAM_ATTEMPTS)
                    ->default(0),
                Toggle::make('reiniciado'),
                Toggle::make('caducado'),
                Toggle::make('finalizado'),
                Select::make('parent_id')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload(),
            ]);
    }
}

<?php

namespace App\Filament\Resources\UserCourses\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserCourseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('course_id')
                    ->numeric(),
                TextEntry::make('fecha_inicio'),
                TextEntry::make('dias_activo')
                    ->placeholder('-'),
                IconEntry::make('aprobado')
                    ->boolean()
                    ->placeholder('-'),
                IconEntry::make('intentos')
                    ->boolean()
                    ->placeholder('-'),
                IconEntry::make('reiniciado')
                    ->boolean()
                    ->placeholder('-'),
                IconEntry::make('caducado')
                    ->boolean()
                    ->placeholder('-'),
                IconEntry::make('finalizado')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('parent_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}

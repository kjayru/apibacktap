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
                // `intentos` es un contador de 0 a 3, no un sí o no: pintado como icono,
                // un 2 salía como tic verde y un 0 como cruz roja (#1870). La tabla ya lo
                // mostraba bien; faltaba la ficha de detalle.
                TextEntry::make('intentos')
                    ->label('Exam attempts')
                    ->formatStateUsing(fn ($state): string => (int) $state . ' / ' . \App\Services\CourseAccessService::MAX_EXAM_ATTEMPTS)
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

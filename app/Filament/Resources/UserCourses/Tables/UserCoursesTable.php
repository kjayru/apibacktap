<?php

namespace App\Filament\Resources\UserCourses\Tables;

use App\Models\UserCourse;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserCoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // El primero de la lista debe ser el último registro creado.
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('user.email')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('course.titulo')
                    ->label('Course')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fecha_inicio')
                    ->label('Start date')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('dias_activo')
                    ->label('Active days')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('aprobado')
                    ->label('Approved')
                    ->boolean(),
                // `intentos` es el número de intentos fallidos del examen final (máximo 3),
                // no un booleano: pintarlo como toggle lo pisaba a 0/1 desde el admin.
                \Filament\Tables\Columns\TextColumn::make('intentos')
                    ->label('Exam attempts')
                    ->formatStateUsing(fn ($state): string => (int) $state . ' / ' . \App\Services\CourseAccessService::MAX_EXAM_ATTEMPTS),
                IconColumn::make('reiniciado')
                    ->label('Restarted')
                    ->boolean(),
                IconColumn::make('caducado')
                    ->label('Expired')
                    ->boolean(),
                IconColumn::make('finalizado')
                    ->label('Finished')
                    ->boolean(),
                TextColumn::make('parent_id')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('certificate')
                    ->label('Certificate')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn (Model $record): string => route('admin.user-courses.certificate', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (Model $record): bool => (bool) $record->aprobado),
                Action::make('restartCourse')
                    ->label('Restart')
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->modalHeading('Restart course')
                    ->modalDescription('This creates a new active enrollment linked to this enrollment. Existing historical progress is preserved.')
                    ->action(function (Model $record): void {
                        /** @var UserCourse $record */
                        $newUserCourse = new UserCourse();
                        $newUserCourse->user_id = $record->user_id;
                        $newUserCourse->course_id = $record->course_id;
                        $newUserCourse->fecha_inicio = Carbon::now()->format('Y-m-d');
                        $newUserCourse->dias_activo = $record->course?->tiempovalido ?: $record->dias_activo ?: 15;
                        $newUserCourse->aprobado = false;
                        $newUserCourse->reiniciado = true;
                        $newUserCourse->parent_id = $record->id;
                        $newUserCourse->save();

                        Notification::make()
                            ->title('Course restarted')
                            ->success()
                            ->send();
                    }),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

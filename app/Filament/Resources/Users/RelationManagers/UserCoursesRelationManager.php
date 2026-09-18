<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Models\UserCourse;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserCoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'userCourses';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('course_id')
                    ->label('Course')
                    ->relationship('course', 'titulo')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('fecha_inicio')
                    ->label('Start date')
                    ->required(),
                TextInput::make('dias_activo')
                    ->label('Active days')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('course.titulo')
            ->columns([
                TextColumn::make('course.titulo')
                    ->label('Course')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fecha_inicio')
                    ->label('Start date')
                    ->sortable(),
                TextColumn::make('dias_activo')
                    ->label('Active days')
                    ->sortable(),
                IconColumn::make('aprobado')
                    ->label('Approved')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->createAnother(false),
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
                    ->action(function (Model $record): void {
                        /** @var UserCourse $record */
                        $newUserCourse = new UserCourse;
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
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

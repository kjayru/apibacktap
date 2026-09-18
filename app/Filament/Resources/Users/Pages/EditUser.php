<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\Course;
use App\Models\UserCourse;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('enrollCourse')
                ->label('Enroll course')
                ->icon('heroicon-o-academic-cap')
                ->form([
                    Select::make('course_id')
                        ->label('Course')
                        ->options(fn (): array => Course::query()
                            ->orderBy('titulo')
                            ->pluck('titulo', 'id')
                            ->all())
                        ->required()
                        ->searchable()
                        ->preload(),
                ])
                ->action(function (array $data): void {
                    $exists = UserCourse::query()
                        ->where('user_id', $this->record->id)
                        ->where('course_id', $data['course_id'])
                        ->exists();

                    if ($exists) {
                        Notification::make()
                            ->title('User is already enrolled in this course')
                            ->warning()
                            ->send();

                        return;
                    }

                    $course = Course::query()->findOrFail($data['course_id']);

                    $userCourse = new UserCourse;
                    $userCourse->user_id = $this->record->id;
                    $userCourse->course_id = $course->id;
                    $userCourse->fecha_inicio = Carbon::now()->format('Y-m-d');
                    $userCourse->dias_activo = $course->tiempovalido ?: 30;
                    $userCourse->aprobado = false;
                    $userCourse->save();

                    Notification::make()
                        ->title('Course enrolled')
                        ->success()
                        ->send();
                }),
            DeleteAction::make(),
        ];
    }
}

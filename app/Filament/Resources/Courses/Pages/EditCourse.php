<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use App\Models\Exam;
use App\Models\ExamCourse;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCourse extends EditRecord
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('assignExam')
                ->label('Assign exam')
                ->icon('heroicon-o-clipboard-document-check')
                ->form([
                    Select::make('exam_id')
                        ->label('Exam')
                        ->options(fn (): array => Exam::query()
                            ->orderBy('title')
                            ->pluck('title', 'id')
                            ->all())
                        ->default(fn (): ?int => $this->record->examcourse?->exam_id)
                        ->required()
                        ->searchable()
                        ->preload(),
                ])
                ->action(function (array $data): void {
                    ExamCourse::query()->updateOrCreate(
                        ['course_id' => $this->record->id],
                        ['exam_id' => $data['exam_id']],
                    );

                    Notification::make()
                        ->title('Exam assigned')
                        ->success()
                        ->send();
                }),
            DeleteAction::make(),
        ];
    }
}

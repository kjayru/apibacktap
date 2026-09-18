<?php

namespace App\Filament\Resources\ExamQuestions\Pages;

use App\Filament\Concerns\CreatesAndReturnsToList;
use App\Filament\Resources\ExamQuestions\ExamQuestionResource;
use App\Filament\Resources\Exams\ExamResource;
use App\Filament\Support\ExamBreadcrumbs;
use App\Models\Exam;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Attributes\Locked;

/**
 * La pregunta se crea dentro de su examen: se llega desde "Create Question", que pasa
 * el examen por la URL, y al guardar se vuelve a las preguntas de ese examen (#1723).
 */
class CreateExamQuestion extends CreateRecord
{
    use CreatesAndReturnsToList;

    protected static string $resource = ExamQuestionResource::class;

    protected static ?string $title = 'Create Question';

    #[Locked]
    public int $examId;

    public function mount(): void
    {
        $this->examId = Exam::query()->findOrFail(request()->integer('exam'))->getKey();

        parent::mount();
    }

    public function getBreadcrumbs(): array
    {
        return [...ExamBreadcrumbs::questions(Exam::query()->findOrFail($this->examId)), 'Create'];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['exam_id'] = $this->examId;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return ExamResource::getUrl('questions', ['record' => $this->examId]);
    }
}

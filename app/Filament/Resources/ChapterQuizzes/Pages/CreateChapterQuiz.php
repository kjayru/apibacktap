<?php

namespace App\Filament\Resources\ChapterQuizzes\Pages;

use App\Filament\Concerns\CreatesAndReturnsToList;
use App\Filament\Resources\ChapterQuizzes\ChapterQuizResource;
use App\Filament\Resources\Chapters\ChapterResource;
use App\Filament\Support\CourseBreadcrumbs;
use App\Models\Chapter;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Attributes\Locked;

/**
 * Una pregunta siempre se crea dentro de un capítulo: se llega desde "Create Question"
 * del quiz del capítulo, que pasa el capítulo por la URL, y al guardar se vuelve a ese
 * quiz (#1712).
 */
class CreateChapterQuiz extends CreateRecord
{
    use CreatesAndReturnsToList;

    protected static string $resource = ChapterQuizResource::class;

    protected static ?string $title = 'Create Question';

    #[Locked]
    public int $chapterId;

    public function mount(): void
    {
        $this->chapterId = Chapter::query()->findOrFail(request()->integer('chapter'))->getKey();

        parent::mount();
    }

    public function getBreadcrumbs(): array
    {
        return [
            ...CourseBreadcrumbs::chapter($this->chapter()),
            ChapterResource::getUrl('quizzes', ['record' => $this->chapterId]) => 'Quiz',
            'Create',
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['chapter_id'] = $this->chapterId;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return ChapterResource::getUrl('quizzes', ['record' => $this->chapterId]);
    }

    private function chapter(): Chapter
    {
        return Chapter::query()->with('course')->findOrFail($this->chapterId);
    }
}

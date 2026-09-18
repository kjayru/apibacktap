<?php

namespace App\Filament\Resources\ChapterQuizzes\Pages;

use App\Filament\Resources\ChapterQuizzes\ChapterQuizResource;
use App\Filament\Resources\Chapters\ChapterResource;
use App\Filament\Support\CourseBreadcrumbs;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

/** Pregunta y respuestas en la misma pantalla; al guardar o borrar, de vuelta a su quiz (#1712). */
class EditChapterQuiz extends EditRecord
{
    protected static string $resource = ChapterQuizResource::class;

    protected static ?string $title = 'Edit Question';

    public function getBreadcrumbs(): array
    {
        return [
            ...CourseBreadcrumbs::chapter($this->getRecord()->chapter),
            $this->quizUrl() => 'Quiz',
            'Edit',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->successRedirectUrl(fn (): string => $this->quizUrl()),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->quizUrl();
    }

    private function quizUrl(): string
    {
        return ChapterResource::getUrl('quizzes', ['record' => $this->getRecord()->chapter_id]);
    }
}

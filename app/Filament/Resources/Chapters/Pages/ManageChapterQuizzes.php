<?php

namespace App\Filament\Resources\Chapters\Pages;

use App\Filament\Resources\ChapterQuizzes\ChapterQuizResource;
use App\Filament\Resources\Chapters\ChapterResource;
use App\Filament\Support\CourseBreadcrumbs;
use App\Models\ChapterQuiz;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * El quiz de un capítulo en pantalla propia, como "Evaluations" en producción: número,
 * pregunta, respuesta correcta y fecha (#1588, #1589, #1715, #1716). Crear y editar
 * llevan a la pantalla de la pregunta, donde están también sus respuestas (#1710);
 * el modal de antes sólo tenía el enunciado (#1705).
 */
class ManageChapterQuizzes extends ManageRelatedRecords
{
    protected static string $resource = ChapterResource::class;

    protected static string $relationship = 'chapterquizzes';

    protected static ?string $title = 'Quiz';

    public function getSubheading(): ?string
    {
        return $this->getOwnerRecord()->title;
    }

    public function getBreadcrumbs(): array
    {
        return [...CourseBreadcrumbs::chapter($this->getOwnerRecord()), 'Quiz'];
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question')
            ->modifyQueryUsing(fn (Builder $query) => $query->with('options'))
            ->defaultSort('id')
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('question')
                    ->label('Question')
                    ->wrap(),
                TextColumn::make('answer')
                    ->label('Answer')
                    ->state(fn (ChapterQuiz $record): ?string => $record->options->firstWhere('estado', 1)?->option)
                    ->placeholder('No correct answer')
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y'),
            ])
            ->headerActions([
                Action::make('createQuestion')
                    ->label('Create Question')
                    ->url(fn (): string => ChapterQuizResource::getUrl('create', ['chapter' => $this->getOwnerRecord()->getKey()])),
            ])
            ->recordUrl(fn (ChapterQuiz $record): string => ChapterQuizResource::getUrl('edit', ['record' => $record]))
            ->recordActions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->url(fn (ChapterQuiz $record): string => ChapterQuizResource::getUrl('edit', ['record' => $record])),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

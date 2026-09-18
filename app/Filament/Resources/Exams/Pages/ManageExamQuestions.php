<?php

namespace App\Filament\Resources\Exams\Pages;

use App\Filament\Resources\ExamQuestions\ExamQuestionResource;
use App\Filament\Resources\Exams\ExamResource;
use App\Filament\Support\ExamBreadcrumbs;
use App\Models\ExamQuestion;
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
 * Las preguntas de un examen en su propia pantalla, a la que se llega con "Options
 * exam" desde el listado, como en producción (#1608, #1720). Antes estaban en un bloque
 * dentro de la edición del examen, y el botón "Questions" abría el listado global
 * buscando por el título, con preguntas de todos los exámenes (#1719).
 *
 * Número, pregunta, respuesta correcta y fecha, sin buscador (#1613, #1614, #1726,
 * #1729, #1730). Crear y editar llevan a la pantalla de la pregunta con sus opciones
 * (#1728).
 */
class ManageExamQuestions extends ManageRelatedRecords
{
    protected static string $resource = ExamResource::class;

    protected static string $relationship = 'examquestions';

    protected static ?string $title = 'Exam question';

    public function getSubheading(): ?string
    {
        return $this->getOwnerRecord()->title;
    }

    public function getBreadcrumbs(): array
    {
        return [...ExamBreadcrumbs::exam($this->getOwnerRecord()), 'Exam question'];
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question')
            ->modifyQueryUsing(fn (Builder $query) => $query->with('examquestionoptions'))
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
                    ->state(fn (ExamQuestion $record): ?string => $record->examquestionoptions->firstWhere('resultado', 1)?->opcion)
                    ->placeholder('No correct answer')
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y'),
            ])
            ->headerActions([
                Action::make('createQuestion')
                    ->label('Create Question')
                    ->url(fn (): string => ExamQuestionResource::getUrl('create', ['exam' => $this->getOwnerRecord()->getKey()])),
            ])
            ->recordUrl(fn (ExamQuestion $record): string => ExamQuestionResource::getUrl('edit', ['record' => $record]))
            ->recordActions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->url(fn (ExamQuestion $record): string => ExamQuestionResource::getUrl('edit', ['record' => $record])),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

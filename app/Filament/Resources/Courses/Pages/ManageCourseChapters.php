<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Chapters\ChapterResource;
use App\Filament\Resources\Chapters\Schemas\ChapterForm;
use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Support\CourseBreadcrumbs;
use App\Models\Chapter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Los capítulos de un curso en su propia pantalla, a la que se llega con el botón
 * "Chapters" del listado, como en el admin de producción. Antes ese botón abría el
 * listado general buscando por el título del curso, y salían capítulos de otros
 * cursos (#1696, #1698); y el bloque de capítulos dentro de la edición del curso
 * sobraba (#1697).
 */
class ManageCourseChapters extends ManageRelatedRecords
{
    protected static string $resource = CourseResource::class;

    protected static string $relationship = 'chapters';

    protected static ?string $title = 'Chapters';

    public function getSubheading(): ?string
    {
        return $this->getOwnerRecord()->titulo;
    }

    public function getBreadcrumbs(): array
    {
        return [...CourseBreadcrumbs::course($this->getOwnerRecord()), 'Chapters'];
    }

    /** El mismo formulario que la ficha del capítulo, sin el selector de curso. */
    public function form(Schema $schema): Schema
    {
        return ChapterForm::configure($schema, withCourse: false);
    }

    /**
     * Columnas del listado de capítulos de producción. Contents y Quiz llevan cada uno a
     * su pantalla; antes los dos abrían la edición del capítulo y el Quiz caía en los
     * contenidos (#1563, #1704).
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('order')
            ->columns([
                TextColumn::make('order')
                    ->label('Order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Title')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('chaptercontents_count')
                    ->label('Contents')
                    ->counts('chaptercontents')
                    ->badge()
                    ->color('danger')
                    ->formatStateUsing(fn ($state): string => "{$state} · Contents")
                    ->url(fn (Chapter $record): string => ChapterResource::getUrl('contents', ['record' => $record])),
                TextColumn::make('chapterquizzes_count')
                    ->label('Quiz')
                    ->counts('chapterquizzes')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state): string => "{$state} · Quiz")
                    ->url(fn (Chapter $record): string => ChapterResource::getUrl('quizzes', ['record' => $record])),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New chapter')
                    ->modalHeading('Create Chapter')
                    ->createAnother(false)
                    ->closeModalByClickingAway(false),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading(fn (Chapter $record): string => "Edit {$record->title}")
                    ->closeModalByClickingAway(false),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

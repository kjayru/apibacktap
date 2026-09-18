<?php

namespace App\Filament\Resources\Chapters\Pages;

use App\Filament\Resources\Chaptercontents\Schemas\ChaptercontentForm;
use App\Filament\Resources\Chaptercontents\Tables\ChaptercontentsTable;
use App\Filament\Resources\Chapters\ChapterResource;
use App\Filament\Support\CourseBreadcrumbs;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

/** Contenidos de un capítulo, desde el botón "Contents" de los capítulos del curso. */
class ManageChapterContents extends ManageRelatedRecords
{
    protected static string $resource = ChapterResource::class;

    protected static string $relationship = 'chaptercontents';

    protected static ?string $title = 'Contents';

    public function getSubheading(): ?string
    {
        return $this->getOwnerRecord()->title;
    }

    public function getBreadcrumbs(): array
    {
        return [...CourseBreadcrumbs::chapter($this->getOwnerRecord()), 'Contents'];
    }

    public function form(Schema $schema): Schema
    {
        return ChaptercontentForm::configure($schema, withChapter: false);
    }

    /**
     * El modal no se cierra al pulsar fuera: subir un vídeo lleva su rato y un clic
     * despistado tiraba todo lo rellenado sin avisar (#1702).
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('titulo')
            ->defaultSort('order')
            ->columns(ChaptercontentsTable::columns())
            ->headerActions([
                CreateAction::make()
                    ->label('Create Content')
                    ->modalHeading('Create Content')
                    ->createAnother(false)
                    ->closeModalByClickingAway(false),
            ])
            ->recordActions([
                EditAction::make()
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

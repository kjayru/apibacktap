<?php

namespace App\Filament\Resources\Chapters\RelationManagers;

use App\Filament\Resources\Chaptercontents\Schemas\ChaptercontentForm;
use App\Filament\Resources\Chaptercontents\Tables\ChaptercontentsTable;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ChaptercontentsRelationManager extends RelationManager
{
    protected static string $relationship = 'chaptercontents';

    protected static ?string $title = 'Contents';

    /** El mismo formulario y las mismas columnas que la sección Chaptercontent. */
    public function form(Schema $schema): Schema
    {
        return ChaptercontentForm::configure($schema, withChapter: false);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('titulo')
            ->defaultSort('order')
            ->columns(ChaptercontentsTable::columns())
            ->headerActions([
                // Nombre del botón como en producción (#1581).
                CreateAction::make()
                    ->label('Create Content')
                    ->modalHeading('Create Content')
                    ->createAnother(false),
            ])
            ->recordActions([
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

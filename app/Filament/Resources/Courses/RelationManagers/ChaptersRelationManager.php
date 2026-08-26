<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Filament\Resources\Chapters\ChapterResource;
use App\Filament\Resources\Chapters\Schemas\ChapterForm;
use App\Models\Chapter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ChaptersRelationManager extends RelationManager
{
    protected static string $relationship = 'chapters';

    protected static ?string $title = 'Chapters';

    /** El mismo formulario que la ficha del capítulo, sin el selector de curso. */
    public function form(Schema $schema): Schema
    {
        return ChapterForm::configure($schema, withCourse: false);
    }

    /**
     * Las mismas columnas que el listado de capítulos de producción: el número de
     * contenidos y de preguntas van dentro de su botón (#1562, #1563), y se añade la
     * fecha de creación (#1564).
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
                    ->url(fn (Chapter $record): string => ChapterResource::getUrl('edit', ['record' => $record])),
                TextColumn::make('chapterquizzes_count')
                    ->label('Quiz')
                    ->counts('chapterquizzes')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state): string => "{$state} · Quiz")
                    ->url(fn (Chapter $record): string => ChapterResource::getUrl('edit', ['record' => $record])),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->headerActions([
                // Sin "create & create another": los capítulos se crean de uno en uno (#1569).
                CreateAction::make()
                    ->label('New chapter')
                    ->modalHeading('Create Chapter')
                    ->createAnother(false),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading(fn (Chapter $record): string => "Edit {$record->title}"),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

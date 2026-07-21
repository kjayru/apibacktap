<?php

namespace App\Filament\Resources\Chapters\RelationManagers;

use App\Filament\Resources\ChapterQuizzes\ChapterQuizResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ChapterquizzesRelationManager extends RelationManager
{
    protected static string $relationship = 'chapterquizzes';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('question')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question')
            ->columns([
                TextColumn::make('question')
                    ->searchable()
                    ->limit(90),
                TextColumn::make('chapterquizoptions_count')
                    ->label('Options')
                    ->counts('chapterquizoptions')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                Action::make('options')
                    ->label('Options')
                    ->icon('heroicon-o-list-bullet')
                    ->url(fn (Model $record): string => ChapterQuizResource::getUrl('edit', ['record' => $record])),
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

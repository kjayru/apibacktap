<?php

namespace App\Filament\Resources\ChapterQuizzes\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ChapterquizoptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'chapterquizoptions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('option')
                    ->required()
                    ->maxLength(255),
                Toggle::make('estado')
                    ->label('Correct answer'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('option')
            ->columns([
                TextColumn::make('option')
                    ->searchable(),
                IconColumn::make('estado')
                    ->label('Correct')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
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

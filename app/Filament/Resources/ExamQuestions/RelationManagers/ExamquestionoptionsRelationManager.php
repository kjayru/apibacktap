<?php

namespace App\Filament\Resources\ExamQuestions\RelationManagers;

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

class ExamquestionoptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'examquestionoptions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('opcion')
                    ->required()
                    ->maxLength(255),
                Toggle::make('resultado')
                    ->label('Correct answer'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('opcion')
            ->columns([
                TextColumn::make('opcion')
                    ->searchable(),
                IconColumn::make('resultado')
                    ->label('Correct')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
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

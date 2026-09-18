<?php

namespace App\Filament\Resources\Information\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReferencesRelationManager extends RelationManager
{
    protected static string $relationship = 'references';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('fullname')
                    ->required()
                    ->maxLength(255),
                TextInput::make('relationship')
                    ->maxLength(255),
                TextInput::make('companyref')
                    ->label('Company')
                    ->maxLength(255),
                TextInput::make('phoneref')
                    ->label('Phone')
                    ->maxLength(255),
                TextInput::make('addressreference')
                    ->label('Address')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('fullname')
            ->columns([
                TextColumn::make('fullname')
                    ->searchable(),
                TextColumn::make('relationship')
                    ->searchable(),
                TextColumn::make('companyref')
                    ->label('Company')
                    ->searchable(),
                TextColumn::make('phoneref')
                    ->label('Phone')
                    ->searchable(),
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

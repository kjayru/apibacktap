<?php

namespace App\Filament\Resources\Information\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmploymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'employments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('company')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phoneemp')
                    ->label('Phone')
                    ->maxLength(255),
                TextInput::make('addressempl')
                    ->label('Address')
                    ->maxLength(255),
                TextInput::make('supervisor')
                    ->maxLength(255),
                TextInput::make('jobtitle')
                    ->label('Job title')
                    ->maxLength(255),
                TextInput::make('starting')
                    ->maxLength(255),
                TextInput::make('ending')
                    ->maxLength(255),
                TextInput::make('from')
                    ->maxLength(255),
                TextInput::make('to')
                    ->maxLength(255),
                Textarea::make('reason')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('company')
            ->columns([
                TextColumn::make('company')
                    ->searchable(),
                TextColumn::make('jobtitle')
                    ->label('Job title')
                    ->searchable(),
                TextColumn::make('supervisor')
                    ->searchable(),
                TextColumn::make('phoneemp')
                    ->label('Phone')
                    ->searchable(),
                TextColumn::make('from')
                    ->searchable(),
                TextColumn::make('to')
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

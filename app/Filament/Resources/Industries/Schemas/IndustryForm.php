<?php

namespace App\Filament\Resources\Industries\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class IndustryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titulo')
                    ->required(),
                FileUpload::make('banner')
                    ->disk('public')
                    ->directory('banner')
                    ->image(),
                FileUpload::make('card')
                    ->disk('public')
                    ->directory('card')
                    ->image(),
                Textarea::make('contenido')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('orden')
                    ->numeric()
                    ->default(null),
                Select::make('category_id')
                    ->relationship('Category', 'name')
                    ->searchable()
                    ->preload()->required(),
            ]);
    }
}

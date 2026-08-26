<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                FileUpload::make('card')
                    ->disk('public')
                    ->directory('card')
                    ->image(),
                FileUpload::make('banner')
                    ->disk('public')
                    ->directory('banner')
                    ->image(),
                TextInput::make('orden')
                    ->numeric()
                    ->default(null),
                Select::make('parent_id')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload(),
            ]);
    }
}

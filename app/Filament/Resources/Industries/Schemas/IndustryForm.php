<?php

namespace App\Filament\Resources\Industries\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class IndustryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titulo')
                    ->label('Title')
                    ->required(),
                FileUpload::make('banner')
                    ->label('Banner')
                    ->disk('public')
                    ->directory('banner')
                    ->image()
                    ->helperText('Suggested size: 720 x 580 px.'),
                FileUpload::make('card')
                    ->label('Card')
                    ->disk('public')
                    ->directory('card')
                    ->image()
                    ->helperText('Suggested size: 550 x 230 px.'),
                RichEditor::make('contenido')
                    ->label('Content')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('orden')
                    ->label('Order')
                    ->numeric()
                    ->default(null),
                Select::make('category_id')
                    ->label('Category')
                    ->relationship('Category', 'name')
                    ->searchable()
                    ->preload()->required(),
            ]);
    }
}

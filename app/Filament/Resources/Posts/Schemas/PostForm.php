<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titulo')
                    ->label('Title')
                    ->required(),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required(),
                FileUpload::make('card')
                    ->label('Card')
                    ->disk('public')
                    ->directory('card')
                    ->image(),
                FileUpload::make('banner')
                    ->label('Banner')
                    ->disk('public')
                    ->directory('banner')
                    ->image()
                    ->required(),
                RichEditor::make('contenido')
                    ->label('Content')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('resumen')
                    ->label('Summary')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}

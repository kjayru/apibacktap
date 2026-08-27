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
                FileUpload::make('card')
                    ->label('Card')
                    ->disk('public')
                    ->directory('card')
                    ->image()
                    ->helperText('Medida sugerida: 600 x 400 px.'),
                FileUpload::make('banner')
                    ->label('Banner')
                    ->disk('public')
                    ->directory('banner')
                    ->image()
                    ->required()
                    ->helperText('Medida sugerida: 1920 x 480 px.'),
                Textarea::make('resumen')
                    ->label('Summary')
                    ->default(null)
                    ->columnSpanFull(),
                RichEditor::make('contenido')
                    ->label('Content')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}

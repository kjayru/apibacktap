<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CategoryForm
{
    /**
     * Sin "Parent Id" (#1531): las categorías del sitio no anidan. Las medidas van como
     * leyenda bajo cada imagen (#1529, #1530) para que se suban ya con el tamaño bueno.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
                TextInput::make('orden')
                    ->label('Order')
                    ->numeric()
                    ->default(null),
                FileUpload::make('card')
                    ->label('Card')
                    ->disk('public')
                    ->directory('card')
                    ->image()
                    ->helperText('Medida sugerida: 600 x 400 px.')
                    ->columnSpanFull(),
                FileUpload::make('banner')
                    ->label('Banner')
                    ->disk('public')
                    ->directory('banner')
                    ->image()
                    ->helperText('Medida sugerida: 1920 x 480 px.')
                    ->columnSpanFull(),
            ]);
    }
}

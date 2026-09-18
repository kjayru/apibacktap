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
                    ->helperText('Suggested size: 380 x 340 px.')
                    ->columnSpanFull(),
                FileUpload::make('banner')
                    ->label('Banner')
                    ->disk('public')
                    ->directory('banner')
                    ->image()
                    ->helperText('Suggested size: 1080 x 674 px.')
                    ->columnSpanFull(),
            ]);
    }
}

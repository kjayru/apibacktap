<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;

class EventForm
{
    /**
     * Los mismos campos que el admin anterior: un evento de formación ocurre un día
     * concreto, así que no hay rango de fechas sino "Event date" (#1399, #1400), y
     * desaparece el resumen (#1397). La descripción se escribe con editor, como con
     * el CKEditor de antes, y la API ya la envía al front como description_html
     * (#1398, #1402).
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Qué significa el asterisco (#1666).
                Text::make('Fields marked with * are required.')
                    ->columnSpanFull(),
                TextInput::make('title')
                    ->label('Title')
                    ->required(),
                TextInput::make('price')
                    ->label('Price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('duration')
                    ->label('Duration hours')
                    ->required()
                    ->numeric(),
                DatePicker::make('start_date')
                    ->label('Event date')
                    ->native(false)
                    ->displayFormat('M d, Y')
                    ->required(),
                TimePicker::make('start_hour')
                    ->label('Start time')
                    ->required(),
                RichEditor::make('description')
                    ->label('Description')
                    ->columnSpanFull(),
            ]);
    }
}

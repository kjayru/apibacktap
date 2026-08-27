<?php

namespace App\Filament\Resources\Forms8850;

use App\Filament\Resources\Forms8850\Pages\ListForms8850;
use App\Filament\Resources\Forms8850\Pages\ViewForm8850;
use App\Filament\Resources\Forms8850\Schemas\Form8850Infolist;
use App\Filament\Resources\Forms8850\Tables\Forms8850Table;
use App\Models\Form;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * El formulario 8850 de la web lleva guardándose en `forms` desde el sitio anterior
 * —375 envíos— pero no había ninguna pantalla para leerlos (#1453). Es un formulario
 * recibido, así que sólo se consulta.
 */
class Form8850Resource extends Resource
{
    protected static ?string $model = Form::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Form 8850';

    protected static ?string $modelLabel = 'Form 8850';

    protected static ?string $pluralModelLabel = 'Form 8850';

    protected static string|UnitEnum|null $navigationGroup = 'Operations';

    protected static ?string $slug = 'form-8850';

    protected static ?int $navigationSort = 35;

    public static function infolist(Schema $schema): Schema
    {
        return Form8850Infolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return Forms8850Table::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListForms8850::route('/'),
            'view' => ViewForm8850::route('/{record}'),
        ];
    }
}

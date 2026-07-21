<?php

namespace App\Filament\Resources\Information;

use App\Filament\Resources\Information\Pages\CreateInformation;
use App\Filament\Resources\Information\Pages\EditInformation;
use App\Filament\Resources\Information\Pages\ListInformation;
use App\Filament\Resources\Information\Pages\ViewInformation;
use App\Filament\Resources\Information\RelationManagers\EmploymentsRelationManager;
use App\Filament\Resources\Information\RelationManagers\ReferencesRelationManager;
use App\Filament\Resources\Information\Schemas\InformationForm;
use App\Filament\Resources\Information\Schemas\InformationInfolist;
use App\Filament\Resources\Information\Tables\InformationTable;
use App\Models\Information;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class InformationResource extends Resource
{
    protected static ?string $model = Information::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;

    protected static ?string $navigationLabel = 'Applicants';

    protected static ?string $modelLabel = 'Applicant';

    protected static ?string $pluralModelLabel = 'Applicants';

    protected static string|UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return InformationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InformationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InformationTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ReferencesRelationManager::class,
            EmploymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInformation::route('/'),
            'create' => CreateInformation::route('/create'),
            'view' => ViewInformation::route('/{record}'),
            'edit' => EditInformation::route('/{record}/edit'),
        ];
    }
}

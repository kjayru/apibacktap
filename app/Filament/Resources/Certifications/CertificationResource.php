<?php

namespace App\Filament\Resources\Certifications;

use App\Filament\Resources\Certifications\Pages\CreateCertification;
use App\Filament\Resources\Certifications\Pages\EditCertification;
use App\Filament\Resources\Certifications\Pages\ListCertifications;
use App\Filament\Resources\Certifications\Pages\ViewCertification;
use App\Filament\Resources\Certifications\Schemas\CertificationForm;
use App\Filament\Resources\Certifications\Schemas\CertificationInfolist;
use App\Filament\Resources\Certifications\Tables\CertificationsTable;
use App\Models\Certification;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CertificationResource extends Resource
{
    protected static ?string $model = Certification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;

    protected static ?string $navigationLabel = 'Certifications';

    protected static ?string $modelLabel = 'Certification';

    protected static ?string $pluralModelLabel = 'Certifications';

    protected static string|UnitEnum|null $navigationGroup = 'Courses';

    protected static ?int $navigationSort = 30;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return CertificationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CertificationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CertificationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCertifications::route('/'),
            'create' => CreateCertification::route('/create'),
            'view' => ViewCertification::route('/{record}'),
            'edit' => EditCertification::route('/{record}/edit'),
        ];
    }
}

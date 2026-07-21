<?php

namespace App\Filament\Resources\ChapterQuizOptions;

use App\Filament\Resources\ChapterQuizOptions\Pages\CreateChapterQuizOption;
use App\Filament\Resources\ChapterQuizOptions\Pages\EditChapterQuizOption;
use App\Filament\Resources\ChapterQuizOptions\Pages\ListChapterQuizOptions;
use App\Filament\Resources\ChapterQuizOptions\Pages\ViewChapterQuizOption;
use App\Filament\Resources\ChapterQuizOptions\Schemas\ChapterQuizOptionForm;
use App\Filament\Resources\ChapterQuizOptions\Schemas\ChapterQuizOptionInfolist;
use App\Filament\Resources\ChapterQuizOptions\Tables\ChapterQuizOptionsTable;
use App\Models\ChapterQuizOption;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ChapterQuizOptionResource extends Resource
{
    protected static ?string $model = ChapterQuizOption::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return ChapterQuizOptionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ChapterQuizOptionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChapterQuizOptionsTable::configure($table);
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
            'index' => ListChapterQuizOptions::route('/'),
            'create' => CreateChapterQuizOption::route('/create'),
            'view' => ViewChapterQuizOption::route('/{record}'),
            'edit' => EditChapterQuizOption::route('/{record}/edit'),
        ];
    }
}

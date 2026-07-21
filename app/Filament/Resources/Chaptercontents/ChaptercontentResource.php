<?php

namespace App\Filament\Resources\Chaptercontents;

use App\Filament\Resources\Chaptercontents\Pages\CreateChaptercontent;
use App\Filament\Resources\Chaptercontents\Pages\EditChaptercontent;
use App\Filament\Resources\Chaptercontents\Pages\ListChaptercontents;
use App\Filament\Resources\Chaptercontents\Pages\ViewChaptercontent;
use App\Filament\Resources\Chaptercontents\Schemas\ChaptercontentForm;
use App\Filament\Resources\Chaptercontents\Schemas\ChaptercontentInfolist;
use App\Filament\Resources\Chaptercontents\Tables\ChaptercontentsTable;
use App\Models\Chaptercontent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ChaptercontentResource extends Resource
{
    protected static ?string $model = Chaptercontent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return ChaptercontentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ChaptercontentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChaptercontentsTable::configure($table);
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
            'index' => ListChaptercontents::route('/'),
            'create' => CreateChaptercontent::route('/create'),
            'view' => ViewChaptercontent::route('/{record}'),
            'edit' => EditChaptercontent::route('/{record}/edit'),
        ];
    }
}

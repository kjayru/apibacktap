<?php

namespace App\Filament\Resources\Chapters;

use App\Filament\Resources\Chapters\Pages\CreateChapter;
use App\Filament\Resources\Chapters\Pages\EditChapter;
use App\Filament\Resources\Chapters\Pages\ListChapters;
use App\Filament\Resources\Chapters\Pages\ManageChapterContents;
use App\Filament\Resources\Chapters\Pages\ManageChapterQuizzes;
use App\Filament\Resources\Chapters\Schemas\ChapterForm;
use App\Filament\Resources\Chapters\Schemas\ChapterInfolist;
use App\Filament\Resources\Chapters\Tables\ChaptersTable;
use App\Models\Chapter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ChapterResource extends Resource
{
    protected static ?string $model = Chapter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $navigationLabel = 'Chapters';

    protected static ?string $modelLabel = 'Chapter';

    protected static ?string $pluralModelLabel = 'Chapters';

    protected static string|UnitEnum|null $navigationGroup = 'Courses';

    protected static ?int $navigationSort = 20;

    /**
     * Un capítulo suelto no dice a qué curso pertenece, así que deja de tener sección
     * propia y se gestiona desde el curso (#1560). El recurso sigue existiendo porque
     * es la pantalla a la que llevan los botones Contents y Quiz del listado.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return ChapterForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ChapterInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChaptersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChapters::route('/'),
            'create' => CreateChapter::route('/create'),
            'edit' => EditChapter::route('/{record}/edit'),
            'contents' => ManageChapterContents::route('/{record}/contents'),
            'quizzes' => ManageChapterQuizzes::route('/{record}/quizzes'),
        ];
    }
}

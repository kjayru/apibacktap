<?php

namespace App\Filament\Resources\ChapterQuizzes;

use App\Filament\Resources\ChapterQuizzes\Pages\CreateChapterQuiz;
use App\Filament\Resources\ChapterQuizzes\Pages\EditChapterQuiz;
use App\Filament\Resources\ChapterQuizzes\Pages\ListChapterQuizzes;
use App\Filament\Resources\ChapterQuizzes\RelationManagers\ChapterquizoptionsRelationManager;
use App\Filament\Resources\ChapterQuizzes\Schemas\ChapterQuizForm;
use App\Filament\Resources\ChapterQuizzes\Schemas\ChapterQuizInfolist;
use App\Filament\Resources\ChapterQuizzes\Tables\ChapterQuizzesTable;
use App\Models\ChapterQuiz;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ChapterQuizResource extends Resource
{
    protected static ?string $model = ChapterQuiz::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return ChapterQuizForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ChapterQuizInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChapterQuizzesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ChapterquizoptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChapterQuizzes::route('/'),
            'create' => CreateChapterQuiz::route('/create'),
            'edit' => EditChapterQuiz::route('/{record}/edit'),
        ];
    }
}

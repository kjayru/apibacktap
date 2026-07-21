<?php

namespace App\Filament\Resources\ExamQuestionOptions;

use App\Filament\Resources\ExamQuestionOptions\Pages\CreateExamQuestionOption;
use App\Filament\Resources\ExamQuestionOptions\Pages\EditExamQuestionOption;
use App\Filament\Resources\ExamQuestionOptions\Pages\ListExamQuestionOptions;
use App\Filament\Resources\ExamQuestionOptions\Pages\ViewExamQuestionOption;
use App\Filament\Resources\ExamQuestionOptions\Schemas\ExamQuestionOptionForm;
use App\Filament\Resources\ExamQuestionOptions\Schemas\ExamQuestionOptionInfolist;
use App\Filament\Resources\ExamQuestionOptions\Tables\ExamQuestionOptionsTable;
use App\Models\ExamQuestionOption;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ExamQuestionOptionResource extends Resource
{
    protected static ?string $model = ExamQuestionOption::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return ExamQuestionOptionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExamQuestionOptionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExamQuestionOptionsTable::configure($table);
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
            'index' => ListExamQuestionOptions::route('/'),
            'create' => CreateExamQuestionOption::route('/create'),
            'view' => ViewExamQuestionOption::route('/{record}'),
            'edit' => EditExamQuestionOption::route('/{record}/edit'),
        ];
    }
}

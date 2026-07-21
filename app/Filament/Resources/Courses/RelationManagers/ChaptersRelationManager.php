<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Filament\Resources\Chapters\ChapterResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ChaptersRelationManager extends RelationManager
{
    protected static string $relationship = 'chapters';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->maxLength(255),
                TextInput::make('video')
                    ->maxLength(255),
                TextInput::make('audio')
                    ->maxLength(255),
                Textarea::make('reading')
                    ->columnSpanFull(),
                Select::make('quiz')
                    ->label('Quiz')
                    ->options([
                        0 => 'No',
                        1 => 'Yes',
                    ])
                    ->native(false),
                TextInput::make('order')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('chaptercontents_count')
                    ->label('Contents')
                    ->counts('chaptercontents')
                    ->sortable(),
                TextColumn::make('quiz')
                    ->badge()
                    ->formatStateUsing(fn (?int $state): string => $state ? 'Yes' : 'No'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                Action::make('contents')
                    ->label('Contents')
                    ->icon('heroicon-o-list-bullet')
                    ->url(fn (Model $record): string => ChapterResource::getUrl('edit', ['record' => $record])),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

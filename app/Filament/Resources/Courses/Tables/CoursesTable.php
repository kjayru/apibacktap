<?php

namespace App\Filament\Resources\Courses\Tables;

use App\Filament\Resources\Courses\CourseResource;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamCourse;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CoursesTable
{
    /**
     * Mismo listado que el admin de producción, para que el cliente siga la lógica que
     * ya conoce: título, extracto, precio y fecha, con "Assign exam" y "Chapters" en cada
     * curso (#1507, #1509).
     */
    public static function configure(Table $table): Table
    {
        return $table
            // El primero de la lista debe ser el último registro creado.
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('titulo')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('banner')
                    ->label('Banner')
                    ->disk('public'),
                TextColumn::make('resumen')
                    ->label('Excerpt')
                    ->wrap()
                    ->limit(90),
                TextColumn::make('precio')
                    ->label('Price')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->recordActions([
                // El examen final se asigna desde el curso, como en producción. Cada
                // curso tiene uno; reasignarlo sustituye al anterior.
                Action::make('assignExam')
                    ->label('Assign exam')
                    ->icon(Heroicon::OutlinedClipboardDocumentCheck)
                    ->color('success')
                    ->modalHeading(fn (Course $record): string => "Assign exam: {$record->titulo}")
                    ->modalSubmitActionLabel('Save')
                    ->fillForm(fn (Course $record): array => ['exam_id' => $record->examcourse?->exam_id])
                    ->schema([
                        Select::make('exam_id')
                            ->label('Exam')
                            ->options(fn (): array => Exam::query()->orderBy('title')->pluck('title', 'id')->all())
                            ->native(true)
                            ->required(),
                    ])
                    ->action(function (array $data, Course $record): void {
                        ExamCourse::query()->updateOrCreate(
                            ['course_id' => $record->getKey()],
                            ['exam_id' => $data['exam_id']],
                        );

                        Notification::make()->title('Exam assigned')->success()->send();
                    }),
                Action::make('chapters')
                    ->label('Chapters')
                    ->icon(Heroicon::OutlinedBookOpen)
                    ->color('warning')
                    ->url(fn (Course $record): string => CourseResource::getUrl('chapters', ['record' => $record])),
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

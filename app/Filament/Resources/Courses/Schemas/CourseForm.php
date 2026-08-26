<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Models\Certification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CourseForm
{
    /**
     * Los campos van en el orden que pide el tablero, que es el del admin anterior,
     * para que el cliente siga la misma lógica que ya tiene aprendida. Las etiquetas
     * están en inglés porque las columnas conservan sus nombres originales en español.
     * Todos los campos son obligatorios y se oculta el asterisco, como pide el tablero.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titulo')
                    ->label('Title')
                    ->required()
                    ->markAsRequired(false),
                TextInput::make('responsable')
                    ->label('Responsible')
                    ->required()
                    ->markAsRequired(false),
                TextInput::make('precio')
                    ->label('Price')
                    ->numeric()
                    ->prefix('$')
                    ->required()
                    ->markAsRequired(false),
                FileUpload::make('banner')
                    ->label('Banner')
                    ->disk('public')
                    ->directory('banner')
                    ->image()
                    ->helperText('Medida sugerida: 1920 x 480 px.')
                    ->columnSpanFull()
                    ->required()
                    ->markAsRequired(false),
                FileUpload::make('video')
                    ->label('Video')
                    ->disk('public')
                    ->directory('video')
                    ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                    ->maxSize(512000)
                    ->helperText('MP4, WebM u OGG. Peso máximo 500 MB.')
                    ->columnSpanFull()
                    ->required()
                    ->markAsRequired(false),
                DatePicker::make('disponible')
                    ->label('Available')
                    ->native(false)
                    ->displayFormat('M d, Y')
                    ->placeholder('Selecciona la fecha en el calendario')
                    ->required()
                    ->markAsRequired(false),
                Select::make('capitulos')
                    ->label('Chapters')
                    ->options(fn (): array => array_combine(range(1, 20), range(1, 20)))
                    ->required()
                    ->markAsRequired(false),
                Select::make('nivel')
                    ->label('Level')
                    ->options([
                        'Beginner' => 'Beginner',
                        'Intermediate' => 'Intermediate',
                        'Advanced' => 'Advanced',
                    ])
                    ->required()
                    ->markAsRequired(false),
                Select::make('audio')
                    ->label('Audio')
                    ->options([
                        'Yes' => 'Yes',
                        'No' => 'No',
                    ])
                    ->required()
                    ->markAsRequired(false),
                Select::make('language')
                    ->label('Audio language')
                    ->options([
                        'English' => 'English',
                        'Spanish' => 'Spanish',
                    ])
                    ->required()
                    ->markAsRequired(false),
                TextInput::make('tiempovalido')
                    ->label('Access')
                    ->numeric()
                    ->suffix('días')
                    ->required()
                    ->markAsRequired(false),
                Select::make('certification_id')
                    ->label('Certificate')
                    ->options(fn (): array => Certification::query()
                        ->orderBy('id')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->preload()
                    ->required()
                    ->markAsRequired(false),
                TextInput::make('resumen')
                    ->label('Excerpt')
                    ->required()
                    ->markAsRequired(false)
                    ->columnSpanFull(),
                RichEditor::make('contenido')
                    ->label('Description')
                    ->required()
                    ->markAsRequired(false)
                    ->columnSpanFull(),
            ]);
    }
}

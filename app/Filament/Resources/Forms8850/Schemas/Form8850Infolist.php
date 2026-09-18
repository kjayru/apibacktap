<?php

namespace App\Filament\Resources\Forms8850\Schemas;

use App\Models\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Form8850Infolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Applicant')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('yourname')
                            ->label('Name'),
                        TextEntry::make('socialnumber')
                            ->label('Social security number'),
                        TextEntry::make('birthday')
                            ->label('Date of birth')
                            ->placeholder('-'),
                        TextEntry::make('telephone')
                            ->label('Phone')
                            ->placeholder('-'),
                        TextEntry::make('address')
                            ->label('Address')
                            ->placeholder('-'),
                        TextEntry::make('citystate')
                            ->label('City / State')
                            ->placeholder('-'),
                        TextEntry::make('country')
                            ->label('Country')
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->label('Date')
                            ->dateTime('M d, Y H:i')
                            ->placeholder('-'),
                    ]),
                Section::make('Checked statements')
                    ->schema([
                        // El texto completo del enunciado, no sólo su número: el número
                        // suelto no dice nada a quien revisa el envío.
                        TextEntry::make('checked_statements')
                            ->hiddenLabel()
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->state(fn (Form $record): array => array_map(
                                fn (int $n): string => Form::statementNumber($n).'. '.(Form::STATEMENTS[$n] ?? 'Unknown statement'),
                                $record->checked_statements,
                            ))
                            ->placeholder('The applicant did not check any statement.'),
                    ]),
            ]);
    }
}

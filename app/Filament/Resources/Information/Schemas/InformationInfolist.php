<?php

namespace App\Filament\Resources\Information\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InformationInfolist
{
    /**
     * Los bloques van en el mismo orden que el formulario público y cada etiqueta
     * repite la pregunta tal cual la ve el aspirante, para poder cotejar respuesta a
     * respuesta sin traducir nombres de columna.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Applicant Information')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('lastname')->label('Last name'),
                        TextEntry::make('firstname')->label('First name'),
                        TextEntry::make('mi')->label('M.I.'),
                        TextEntry::make('email')
                            ->label('Email')
                            // Sin esto el correo se partía en dos líneas.
                            ->columnSpanFull(),
                        TextEntry::make('phone')->label('Phone'),
                        TextEntry::make('date')->label('Date')->date('M d, Y'),
                        TextEntry::make('birthday')->label('Date of birth')->date('M d, Y'),
                        TextEntry::make('socialnumber')->label('Social Security No.'),
                        TextEntry::make('placebirth')->label('Place of birth'),
                        TextEntry::make('address')->label('Street address'),
                        TextEntry::make('apartment')->label('Apartment / Unit #')->placeholder('-'),
                        TextEntry::make('city')->label('City'),
                        TextEntry::make('state')->label('State'),
                        TextEntry::make('zipcode')->label('Zip code'),
                        TextEntry::make('appliedpay')
                            ->label('Position applied for and desired pay')
                            // Respuesta larga: a tres columnas se cortaba.
                            ->columnSpanFull(),
                        TextEntry::make('whichshift')
                            ->label('Which shift are you applying for?'),
                        TextEntry::make('whichday')
                            ->label('Which days are you available?')
                            ->columnSpan(2),
                        TextEntry::make('citizen')
                            ->label('Are you a citizen of the United States?'),
                        TextEntry::make('authorized')
                            ->label('If no, are you authorized to work in the U.S.?')
                            ->placeholder('-')
                            ->columnSpan(2),
                        TextEntry::make('worked')
                            ->label('Have you ever worked for this company?'),
                        TextEntry::make('when')
                            ->label('If yes, when?')
                            ->placeholder('-')
                            ->columnSpan(2),
                        TextEntry::make('convicted')
                            ->label('Have you ever been convicted of a felony?'),
                        TextEntry::make('explain1')
                            ->label('If yes, explain')
                            ->placeholder('-')
                            ->columnSpan(2),
                        TextEntry::make('indictment')
                            ->label('Are you currently under indictment for a crime?'),
                        TextEntry::make('explain2')
                            ->label('If yes, explain')
                            ->placeholder('-')
                            ->columnSpan(2),
                    ]),
                // Las etiquetas repiten la pregunta tal cual la ve el aspirante en el
                // formulario, y en el mismo orden, para poder cotejar respuesta a
                // respuesta.
                Section::make('Education and Training')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('education.graduatehigh')
                            ->label('Did you graduate high school?')->placeholder('-'),
                        TextEntry::make('education.hightschool')
                            ->label('High school name')->placeholder('-'),
                        TextEntry::make('education.highfrom')
                            ->label('From')->placeholder('-'),
                        TextEntry::make('education.hightto')
                            ->label('To')->placeholder('-'),
                        TextEntry::make('education.graduatecollage')
                            ->label('Did you graduate college?')->placeholder('-'),
                        TextEntry::make('education.collaganame')
                            ->label('College name')->placeholder('-'),
                        TextEntry::make('education.collagefrom')
                            ->label('From')->placeholder('-'),
                        TextEntry::make('education.collageto')
                            ->label('To')->placeholder('-'),
                        TextEntry::make('education.whatmayor')
                            ->label('If yes, what major')->placeholder('-'),
                        TextEntry::make('education.completed')
                            ->label('Level completed')->placeholder('-'),
                        TextEntry::make('education.activecard')
                            ->label('Do you have an active security registration card?')->placeholder('-'),
                        TextEntry::make('education.officer')
                            ->label('If yes, what level security officer are you?')->placeholder('-'),
                        TextEntry::make('education.firearm')
                            ->label('If level 3, do you currently have a firearm?')->placeholder('-'),
                        TextEntry::make('education.holster')
                            ->label('If yes, what level holster are you currently using?')->placeholder('-'),
                        TextEntry::make('education.others')
                            ->label('Any other certifications')->placeholder('-')->columnSpanFull(),
                    ]),
                // El aspirante puede rellenar hasta tres referencias y tres empleos:
                // se listan todos los que haya guardado, no solo el primero.
                Section::make('References')
                    ->schema([
                        RepeatableEntry::make('references')
                            ->hiddenLabel()
                            ->columns(3)
                            ->schema([
                                TextEntry::make('fullname')->label('Full name')->placeholder('-'),
                                TextEntry::make('relationship')->label('Relationship')->placeholder('-'),
                                TextEntry::make('companyref')->label('Company')->placeholder('-'),
                                TextEntry::make('phoneref')->label('Phone')->placeholder('-'),
                                TextEntry::make('addressreference')->label('Address')->placeholder('-')->columnSpan(2),
                            ]),
                    ]),
                Section::make('Previous Employment')
                    ->schema([
                        RepeatableEntry::make('employments')
                            ->hiddenLabel()
                            ->columns(3)
                            ->schema([
                                TextEntry::make('company')->label('Company')->placeholder('-'),
                                TextEntry::make('phoneemp')->label('Phone')->placeholder('-'),
                                TextEntry::make('addressempl')->label('Address')->placeholder('-'),
                                TextEntry::make('supervisor')->label('Supervisor')->placeholder('-'),
                                TextEntry::make('jobtitle')->label('Job title')->placeholder('-'),
                                TextEntry::make('references')->label('May we contact?')->placeholder('-'),
                                TextEntry::make('starting')->label('Starting salary')->placeholder('-'),
                                TextEntry::make('ending')->label('Ending salary')->placeholder('-'),
                                TextEntry::make('from')->label('From')->placeholder('-'),
                                TextEntry::make('to')->label('To')->placeholder('-'),
                                TextEntry::make('reason')->label('Reason for leaving')->placeholder('-')->columnSpan(2),
                            ]),
                    ]),
                // El orden de To y From replica el del formulario anterior.
                Section::make('Military Service')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('military.branch')->label('Branch')->placeholder('-'),
                        TextEntry::make('military.to')->label('To')->placeholder('-'),
                        TextEntry::make('military.from')->label('From')->placeholder('-'),
                        TextEntry::make('military.rank')->label('Rank at discharge')->placeholder('-'),
                        TextEntry::make('military.type')->label('Type of discharge')->placeholder('-'),
                        TextEntry::make('military.explain')
                            ->label('If other than honorable, explain')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),
                Section::make('Disclaimer and Signature')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('disclaimer.signature')->label('Signature')->placeholder('-'),
                        TextEntry::make('disclaimer.datedisclamer')->label('Date')->placeholder('-'),
                        // Los adjuntos se guardan como lista separada por comas.
                        TextEntry::make('disclaimer.fileid')
                            ->label('Attached files')
                            ->placeholder('Sin archivos adjuntos')
                            ->columnSpanFull()
                            ->formatStateUsing(fn (?string $state): string => filled($state)
                                ? implode(', ', array_filter(array_map('trim', explode(',', $state))))
                                : '')
                            ->url(fn (?string $state): ?string => filled($state) && ! str_contains($state, ',')
                                ? asset('storage/' . ltrim($state, '/'))
                                : null)
                            ->openUrlInNewTab(),
                    ]),
            ]);
    }
}

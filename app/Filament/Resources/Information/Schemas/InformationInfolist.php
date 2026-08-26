<?php

namespace App\Filament\Resources\Information\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InformationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal information')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('lastname'),
                        TextEntry::make('firstname'),
                        TextEntry::make('mi'),
                        TextEntry::make('email')->label('Email address'),
                        TextEntry::make('phone'),
                        TextEntry::make('birthday'),
                        TextEntry::make('socialnumber')->label('SSN'),
                        TextEntry::make('placebirth')->label('Place of birth'),
                        TextEntry::make('date'),
                    ]),
                Section::make('Address and position')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('address'),
                        TextEntry::make('apartment')->placeholder('-'),
                        TextEntry::make('city'),
                        TextEntry::make('state'),
                        TextEntry::make('zipcode'),
                        TextEntry::make('appliedpay'),
                        TextEntry::make('whichshift'),
                        TextEntry::make('whichday'),
                    ]),
                Section::make('Eligibility')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('citizen'),
                        TextEntry::make('authorized'),
                        TextEntry::make('worked'),
                        TextEntry::make('convicted'),
                        TextEntry::make('indictment'),
                        TextEntry::make('when')->placeholder('-'),
                        TextEntry::make('explain1')->placeholder('-')->columnSpanFull(),
                        TextEntry::make('explain2')->placeholder('-')->columnSpanFull(),
                    ]),
                // Las etiquetas repiten la pregunta tal cual la ve el aspirante en el
                // formulario, y en el mismo orden, para poder cotejar respuesta a
                // respuesta.
                Section::make('Education')
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
                Section::make('Military and disclaimer')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('military.branch')->placeholder('-'),
                        TextEntry::make('military.rank')->placeholder('-'),
                        TextEntry::make('military.type')->placeholder('-'),
                        TextEntry::make('military.explain')->placeholder('-')->columnSpanFull(),
                        TextEntry::make('disclaimer.signature')->placeholder('-'),
                        TextEntry::make('disclaimer.fileid')->placeholder('-'),
                        TextEntry::make('disclaimer.datedisclamer')->label('Disclaimer date')->placeholder('-'),
                    ]),
            ]);
    }
}

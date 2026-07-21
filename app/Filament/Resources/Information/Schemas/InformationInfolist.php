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
                Section::make('Education')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('education.graduatehigh')->label('High school graduate')->placeholder('-'),
                        TextEntry::make('education.hightschool')->label('High school')->placeholder('-'),
                        TextEntry::make('education.graduatecollage')->label('College graduate')->placeholder('-'),
                        TextEntry::make('education.activecard')->label('Active card')->placeholder('-'),
                        TextEntry::make('education.firearm')->label('Firearm')->placeholder('-'),
                        TextEntry::make('education.others')->label('Others')->placeholder('-'),
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

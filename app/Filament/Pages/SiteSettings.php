<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Los ajustes globales del sitio público: analítica, SEO común y datos de la empresa.
 *
 * Hasta ahora estaban escritos a mano en el código del sitio —el teléfono, repetido en
 * siete archivos—, así que cambiarlos exigía un programador. Convive con
 * "Configurations", que es otra cosa: el cambio de contraseña del propio administrador.
 *
 * Edita siempre la misma fila, la única de la tabla: aquí no se crea ni se borra nada.
 */
class SiteSettings extends Page
{
    use InteractsWithFormActions;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Site Settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteSetting::current()->attributesToArray());
    }

    public static function getLabel(): string
    {
        return 'Site Settings';
    }

    public function getTitle(): string
    {
        return 'Site Settings';
    }

    public function getHeading(): string
    {
        return 'Site Settings';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Analytics')
                    ->description('Tracking codes for the public site. Changes apply on the next visit; no need to republish.')
                    ->schema([
                        TextInput::make('google_ads_id')
                            ->label('Google Ads ID')
                            ->placeholder('AW-000000000')
                            ->helperText('Leave empty to turn Google Ads off.'),
                        TextInput::make('google_ads_conversion_label')
                            ->label('Google Ads conversion label')
                            ->helperText('The part after the slash in the conversion tag.'),
                        TextInput::make('ga4_measurement_id')
                            ->label('Google Analytics 4 ID')
                            ->placeholder('G-XXXXXXXXXX')
                            ->helperText('Create the property in Google Analytics and paste its ID here. Leave empty to turn it off.'),
                    ])
                    ->columns(2),

                Section::make('SEO')
                    ->description('Used where a page does not set its own. Search engines pick these up the next time the site is published.')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Site name')
                            ->helperText('Added after every page title, like "About Us | TAP Security".'),
                        TextInput::make('google_site_verification')
                            ->label('Google Search Console verification')
                            ->helperText('Only the content value of the verification meta tag.'),
                        Textarea::make('default_meta_description')
                            ->label('Default description')
                            ->rows(3)
                            ->maxLength(160)
                            ->helperText('Up to 160 characters: Google cuts it off around there.')
                            ->columnSpanFull(),
                        FileUpload::make('default_share_image')
                            ->label('Share image')
                            ->disk('public')
                            ->directory('site')
                            ->image()
                            ->helperText('Shown when someone shares a link. Suggested size: 1200 x 630 px.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Company')
                    ->description('Shown in the footer, the contact and employment pages, and in the business card Google displays.')
                    ->schema([
                        TextInput::make('legal_name')->label('Legal name'),
                        TextInput::make('phone')
                            ->label('Phone')
                            ->helperText('International format, like +1-210-399-1116.'),
                        TextInput::make('email')->label('Email')->email(),
                        TextInput::make('street_address')->label('Street address'),
                        TextInput::make('locality')->label('City'),
                        TextInput::make('region')->label('State')->maxLength(2),
                        TextInput::make('postal_code')->label('ZIP code'),
                        TextInput::make('country')->label('Country')->maxLength(2),
                        TextInput::make('psp_license')
                            ->label('PSP license')
                            ->helperText('Shown across the site.'),
                        TextInput::make('psp_academy_license')
                            ->label('PSP license (Academy)')
                            ->helperText('Shown on Training Academy, the calendar and the courses, which operate under a separate license.'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([$this->getFormContentComponent()]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make($this->getFormActions())->key('form-actions'),
            ]);
    }

    /** @return array<Action> */
    public function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save changes')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        SiteSetting::current()->update($this->form->getState());

        Notification::make()
            ->success()
            ->title('Settings saved')
            ->send();
    }
}

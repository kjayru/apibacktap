<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Configurations;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        // Un archivo subido se marca en verde, para que se vea de un vistazo que el vídeo
        // terminó de subir (#1694, #1703). Sólo el estado "terminado": antes se pintaba
        // verde con !important cualquier estado, también el de error, así que una subida
        // fallida se veía igual que una correcta.
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): string => <<<'HTML'
                <style>
                    [data-filepond-item-state='processing-complete'] .filepond--item-panel { background-color: #16a34a; }
                    [data-filepond-item-state='processing-complete'] .filepond--file-status { color: #ffffff; }
                </style>
                HTML,
        );
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->brandName('TAP')
            // El panel no declaraba icono y el favicon.ico del proyecto estaba vacío, así
            // que la pestaña salía sin marca (#1879).
            ->favicon(asset('images/favicon-tap.png'))
            // Configurations: el administrador cambia su propia contraseña (#1886). Se usa
            // la pantalla de cuenta que ya trae el panel, que pide la contraseña actual y
            // limita los intentos, en lugar de escribir una a mano. `isSimple: false` la
            // muestra dentro del panel, con su menú, y no como una página suelta.
            ->profile(Configurations::class, isSimple: false)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Operations'),
                NavigationGroup::make()
                    ->label('Content'),
                NavigationGroup::make()
                    ->label('Courses'),
                NavigationGroup::make()
                    ->label('Settings')
                    ->collapsed(),
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

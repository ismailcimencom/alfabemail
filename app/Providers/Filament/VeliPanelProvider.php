<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Portal\Pages\PortalDashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class VeliPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('veli')
            ->path('/veli')
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn () => Blade::render('<a href="{{ route(\'home\') }}" style="display:inline-block; margin-bottom: 20px; color: #7fa7ff; text-decoration: none; font-weight: bold;">← Ana Sayfa</a>'),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn () => view('partials.hata-bildir')->render(),
            )
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn () => view('partials.kayit-link')->render(),
            )
            ->authGuard('web')
            ->login()
            ->colors([
                'primary' => Color::Blue,
                'gray' => Color::Slate,
            ])
            ->brandName(fn () => auth()->user()?->bagli_okul?->ad
                ?? auth()->user()?->ogrenci?->sinif?->okul?->ad
                ?? auth()->user()?->veli?->ogrenciler?->first()?->sinif?->okul?->ad
                ?? 'ALFABE Veli Paneli')
            ->favicon(asset('favicon.png'))
            ->discoverResources(in: app_path('Filament/Portal/Resources'), for: 'App\Filament\Portal\Resources')
            ->resources([])
            ->discoverPages(in: app_path('Filament/Portal/Pages'), for: 'App\Filament\Portal\Pages')
            ->pages([
                PortalDashboard::class,
            ])
            ->widgets([
                \App\Filament\Portal\Widgets\VeliDashboardWidget::class,
                \App\Filament\Portal\Widgets\OgrenciAktiviteWidget::class,
                \App\Filament\Portal\Widgets\HaftalikProgramWidget::class,
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
                'role:veli',
            ]);
    }
}

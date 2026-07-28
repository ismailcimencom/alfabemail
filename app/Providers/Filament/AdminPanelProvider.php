<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\AdminDashboard;
use App\Filament\Pages\MailcowSettings;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Filament\Navigation\NavigationItem;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use App\Filament\Resources\Yetki\YetkiManagement;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('/admin')
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn () => Blade::render('<a href="{{ route(\'home\') }}" style="display:inline-block; margin-bottom: 20px; color: #7fa7ff; text-decoration: none; font-weight: bold;">← Ana Sayfa</a>'),
            )
            ->navigationItems([

            ])
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn () => auth()->check()
                    ? view('partials.hata-bildir')->render()
                        . view('filament.admin.widgets.chat-widget')->render()
                    : '',
            )
            ->authGuard('web')
            ->login()
            ->colors([
                'primary' => Color::Amber,
                'gray' => Color::Slate,
            ])
            ->favicon(asset('favicon.png'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                AdminDashboard::class,
                MailcowSettings::class,
            ])
            ->resources([
                YetkiManagement::class,
                \App\Filament\Resources\ActivityLogs\ActivityLogResource::class,
                \App\Filament\Resources\Users\UserResource::class,
                \App\Filament\Portal\Resources\Ogrencis\OgrenciResource::class,
                \App\Filament\Resources\Sponsors\SponsorResource::class,
                \App\Filament\Resources\HataBildirisis\HataBildirisiResource::class,
            ])
            ->widgets([
                \App\Filament\Widgets\AdminStatsOverview::class,
                \App\Filament\Widgets\RegistrationChart::class,
                \App\Filament\Widgets\OnlineAdminsWidget::class,
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
                'role:admin',
            ]);
    }
}
<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class MitraPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('mitra')
            ->path('mitra')
            ->brandName('Salestracker')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->renderHook(
                'panels::head.end',
                fn () => new \Illuminate\Support\HtmlString('<link rel="stylesheet" href="/css/custom.css?v=1.0.2">')
            )
            ->renderHook(
                'panels::auth.login.form.after',
                fn () => new \Illuminate\Support\HtmlString('<div class="text-center mt-6"><a href="https://wa.me/6281234567890?text=Ask%20Admin%20to%20Sign%20Up" target="_blank" class="text-primary-600 hover:underline text-sm font-semibold" style="color: var(--primary-600) !important; font-weight: 600;">Ask Admin to Sign Up</a></div>')
            )
            ->discoverResources(in: app_path('Filament/Mitra/Resources'), for: 'App\\Filament\\Mitra\\Resources')
            ->discoverPages(in: app_path('Filament/Mitra/Pages'), for: 'App\\Filament\\Mitra\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Mitra/Widgets'), for: 'App\\Filament\\Mitra\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authGuard('mitra');
    }
}

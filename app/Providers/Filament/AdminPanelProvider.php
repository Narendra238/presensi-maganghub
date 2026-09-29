<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Navigation\NavigationItem;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use App\Filament\Pages\Auth\EditProfile;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Prunacatalin\FilamentLocaleSwitcher\Http\Middleware\ApplyLocale;
use Prunacatalin\FilamentLocaleSwitcher\LocaleSwitchPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile(EditProfile::class, isSimple: false)
            ->homeUrl('/')
            ->brandName('Balai Diklat Industri Yogyakarta')
            ->darkMode(true)
            ->themeSwitcher(true)
            ->plugin(LocaleSwitchPlugin::make())
            ->navigationItems([
                NavigationItem::make('profile')
                    ->label(fn (): string => match (app()->getLocale()) {
                        'en' => 'My Profile',
                        'ar' => 'الملف الشخصي',
                        default => 'Profil Saya',
                    })
                    ->url(fn (): string => \Filament\Facades\Filament::getProfileUrl())
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.auth.profile'))
                    ->sort(99),
            ])
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
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
                ApplyLocale::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

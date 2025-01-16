<?php

namespace App\Providers\Filament;

// Middleware do Laravel
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

// Middleware do Filament
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;

// Models
use App\Models\User;

// Filament
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;

// Enums do Filament
use Filament\Enums\ThemeMode;

// Plugins e pacotes externos
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Provider;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;

// Middleware customizado
use App\Http\Middleware\MercadoLivreTokenRefreshMiddleware;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Destak Ferramentas')
            ->favicon(asset('img/icon.png'))
            ->brandLogo(asset('img/logo.png'))
            ->brandLogoHeight('3rem')
            ->defaultThemeMode(ThemeMode::Light)
            ->colors([
                'primary' => Color::Lime,
            ])
            ->plugins([
                FilamentBackgroundsPlugin::make()
                    ->showAttribution(false)
                    ->imageProvider(
                        MyImages::make()->directory('img/backgrounds')
                    ),
                FilamentSocialitePlugin::make()
                    ->providers([
                        Provider::make('mercadolibre')
                            ->label('Mercado Livre')
                            ->icon('fas-handshake')
                            ->color(Color::hex('#2d3277'))
                            ->outlined(false)
                            ->stateless(false)
                            ->scopes(['write', 'offline_access']),
                    ])
                    ->registration(true)
                    ->createUserUsing(function (string $provider, SocialiteUserContract $oauthUser, FilamentSocialitePlugin $plugin) {
                        return User::create([
                            'nickname' => $oauthUser->getNickname(),
                            'name' => $oauthUser->getName(),
                            'email' => $oauthUser->getEmail(),
                            'avatar_url' => $oauthUser->getAvatar(),
                            'mercadolibre_token' => $oauthUser->token,
                            'mercadolibre_refresh_token' => $oauthUser->refreshToken,
                            'mercadolibre_expires_at' => now()->addSeconds($oauthUser->expiresIn),
                        ]);
                    })
                    ->resolveUserUsing(function (string $provider, SocialiteUserContract $oauthUser, FilamentSocialitePlugin $plugin) {
                        $user =  User::where('email', $oauthUser->getEmail())->first();
                        if ($user) {
                            $user->update([
                                'avatar_url' => $oauthUser->getAvatar(),
                                'mercadolibre_token' => $oauthUser->token,
                                'mercadolibre_refresh_token' => $oauthUser->refreshToken,
                                'mercadolibre_expires_at' => now()->addSeconds($oauthUser->expiresIn),
                            ]);
                            return $user;
                        }
                    }),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
                MercadoLivreTokenRefreshMiddleware::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

use App\Helpers\CustomSettings;
use App\Helpers\MenuBuilder;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /**
         * ===========================
         * Auth email customizations
         * ===========================
         * Runs in HTTP + queues/console.
         * Views you should have:
         * - resources/views/emails/auth/verify.blade.php
         * - resources/views/emails/auth/reset.blade.php
         */

        // Resolve brand colors safely (works in queue/console)
        $resolveColors = function (): array {
            $defaults = [
                'main_color' => '#FF2D20',
                'sub_color'  => '#1A202C',
                'text_color' => '#22223B',
                'icon_color' => '#000000',
            ];

            try {
                $c = CustomSettings::colors();
                return [
                    'main_color' => $c['main_color'] ?? $defaults['main_color'],
                    'sub_color'  => $c['sub_color']  ?? $defaults['sub_color'],
                    'text_color' => $c['text_color'] ?? $defaults['text_color'],
                    'icon_color' => $c['icon_color'] ?? $defaults['icon_color'],
                ];
            } catch (\Throwable $e) {
                return $defaults;
            }
        };

        // Verify Email
        VerifyEmail::toMailUsing(function ($notifiable, string $url) use ($resolveColors) {
            $colors = $resolveColors();
            $locale = method_exists($notifiable, 'preferred_locale')
                ? ($notifiable->preferred_locale() ?: (App::getLocale() ?: 'en'))
                : (App::getLocale() ?: 'en');

            // Optional SPA override:
            // $url = rtrim(config('app.frontend_url', config('app.url')), '/')
            //      . '/email/verify?verify_url=' . urlencode($url);

            return (new MailMessage)
                ->subject(__('adminlte::adminlte.verify_email_subject', ['app' => config('app.name')], $locale))
                ->view('emails.auth.verify', [
                    'user'            => $notifiable,
                    'verificationUrl' => $url,
                    'appName'         => config('app.name'),
                    'locale'          => $locale,
                    'colors'          => $colors,
                    'logoUrl'         => config('app.logo_url', ''), // optional
                    'preheader'       => __('adminlte::adminlte.verify_email_preheader', [], $locale),
                ]);
        });

        // Reset Password
        ResetPassword::toMailUsing(function ($notifiable, string $token) use ($resolveColors) {
            $colors = $resolveColors();
            $locale = method_exists($notifiable, 'preferred_locale')
                ? ($notifiable->preferred_locale() ?: (App::getLocale() ?: 'en'))
                : (App::getLocale() ?: 'en');

            // Default web reset URL
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            // Optional SPA override:
            // $url = rtrim(config('app.frontend_url', config('app.url')), '/')
            //      . '/reset-password?token=' . urlencode($token)
            //      . '&email=' . urlencode($notifiable->getEmailForPasswordReset());

            $pwdBroker = config('auth.defaults.passwords');
            $minutes   = (int) (config("auth.passwords.$pwdBroker.expire") ?? 60);

            return (new MailMessage)
                ->subject(__('adminlte::adminlte.reset_password_subject', ['app' => config('app.name')], $locale))
                ->view('emails.auth.reset', [
                    'user'      => $notifiable,
                    'resetUrl'  => $url,
                    'appName'   => config('app.name'),
                    'expiresIn' => $minutes,
                    'locale'    => $locale,
                    'colors'    => $colors,
                    'logoUrl'   => config('app.logo_url', asset('images/logo.png')),
                    'preheader' => __('adminlte::adminlte.reset_preheader', ['app' => config('app.name')], $locale),
                ]);
        });

        /**
         * ===========================
         * HTTP-only bootstrapping
         * ===========================
         */
        if (App::runningInConsole()) {
            return;
        }

        // Set locale from session (if present)
        try {
            if (Session::has('locale')) {
                App::setLocale(Session::get('locale'));
            }
        } catch (\Throwable $e) {
            // ignore
        }

        /**
         * One view composer: shares colors + injects AdminLTE menu per request
         * The menu is assembled by App\Helpers\MenuBuilder::build($user, $iconColor)
         */
        View::composer('*', function ($view) use ($resolveColors) {
            // Colors (shared with all views)
            $colors    = $resolveColors();
            $mainColor = $colors['main_color'];
            $subColor  = $colors['sub_color'];
            $textColor = $colors['text_color'];
            $iconColor = $colors['icon_color'];

            // Build menu only when user is present
            $user = Auth::user();
            $menu = $user ? MenuBuilder::build($user, $iconColor) : [];

            // Set AdminLTE menu for this request
            Config::set('adminlte.menu', $menu);

            // Share colors
            $view->with(compact('mainColor', 'subColor', 'textColor', 'iconColor'));
        });
    }
}

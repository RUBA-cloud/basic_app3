<?php // app/Providers/AppServiceProvider.php

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

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        /**
         * 0) Customize Auth emails (runs in HTTP + console).
         *    Uses Blade views at resources/views/emails/auth/verify.blade.php
         *    and resources/views/emails/auth/reset.blade.php
         */

        // Helper to pull theme colors safely (works in queue/console too)
        $resolveColors = function () {
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

        // 1) Verify Email customization
        VerifyEmail::toMailUsing(function ($notifiable, string $url) use ($resolveColors) {
            $colors = $resolveColors();
            $locale = method_exists($notifiable, 'preferred_locale')
                ? $notifiable->preferred_locale()
                : (App::getLocale() ?: 'en');

            // Optional: if you have a SPA route, replace $url here:
            // $url = rtrim(config('app.frontend_url', config('app.url')), '/').'/email/verify?verify_url='.urlencode($url);

            return (new MailMessage)
                ->subject(__('auth.verify_email_subject', ['app' => config('app.name')], $locale))
                ->view('emails.auth.verify', [
                    'user'            => $notifiable,
                    'verificationUrl' => $url,
                    'appName'         => config('app.name'),
                    'locale'          => $locale,
                    'colors'          => $colors,
                    // Optional: company logo path
                    'logoUrl'         => asset('images/logo.png'),
                    'preheader'       => __('auth.verify_email_preheader', [], $locale),
                ]);
        });

        // 2) Reset Password customization
        ResetPassword::toMailUsing(function ($notifiable, string $token) use ($resolveColors) {
            $colors = $resolveColors();
            $locale = method_exists($notifiable, 'preferred_locale')
                ? $notifiable->preferred_locale()
                : (App::getLocale() ?: 'en');

            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            // SPA example:
            // $url = rtrim(config('app.frontend_url', config('app.url')), '/')
            //      . '/reset-password?token='.$token.'&email='.urlencode($notifiable->getEmailForPasswordReset());

            $minutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

            return (new MailMessage)
                ->subject(__('auth.reset_password_subject', ['app' => config('app.name')], $locale))
                ->view('auth.passwords.reset' ,[
                    'user'      => $notifiable,
                    'resetUrl'  => $url,
                    'appName'   => config('app.name'),
                    'expiresIn' => $minutes,
                    'locale'    => $locale,
                    'colors'    => $colors,
                    'logoUrl'   => asset('images/logo.png'),
                    'preheader' => __('auth.reset_password_preheader', ['minutes' => $minutes], $locale),
                ]);
        });

        // ----- The rest of your existing boot(...) remains unchanged -----
        if (App::runningInConsole()) {
            return;
        }

        try {
            if (Session::has('locale')) {
                App::setLocale(Session::get('locale'));
            }
        } catch (\Throwable $e) {}

        View::composer('*', function ($view) {
            $mainColor = '#FF2D20';
            $subColor  = '#1A202C';
            $textColor = '#22223B';
            $iconColor = '#000000';

            try {
                $colors    = CustomSettings::colors(); // may hit DB
                $mainColor = $colors['main_color'] ?? $mainColor;
                $subColor  = $colors['sub_color'] ?? $subColor;
                $textColor = $colors['text_color'] ?? $textColor;
                $iconColor = $colors['icon_color'] ?? $iconColor;
            } catch (\Throwable $e) {}

            $view->with(compact('mainColor', 'subColor', 'textColor', 'iconColor'));
        });

        View::composer('*', function () {
            $iconColor = '#000000';
            try {
                $colors    = CustomSettings::colors();
                $iconColor = $colors['icon_color'] ?? $iconColor;
            } catch (\Throwable $e) {}

            $menu = [];
            $menu[] = [
                'text'       => 'dashboard',
                'url'        => '/',
                'icon'       => 'fas fa-fw fa-tachometer-alt',
                'icon_color' => $iconColor,
            ];

            $menu[] = [
                'text'       => 'permissions',
                'url'        => '/permissions',
                'icon'       => 'fas fa-fw fa-user-shield',
                'icon_color' => $iconColor,
            ];
            $menu[] = [
                'text'       => 'modules',
                'url'        => '/modules',
                'icon'       => 'fas fa-fw fa-cogs',
                'icon_color' => $iconColor,
            ];

            $modules = Auth::check() ? (Auth::user()->modulesHistory ?? null) : null;

            if ($modules?->company_info_module) {
                $menu[] = ['text'=>'company_info','url'=>'/companyInfo','icon'=>'fas fa-fw fa-info-circle','icon_color'=>$iconColor];
            }
            if ($modules?->company_branch_module) {
                $menu[] = ['text'=>'branches','url'=>'/branches','icon'=>'fas fa-fw fa-code-branch','icon_color'=>$iconColor];
            }
            if ($modules?->company_category_module) {
                $menu[] = ['text'=>'category','url'=>'/categories','icon'=>'fas fa-fw fa-list','icon_color'=>$iconColor];
            }
            if ($modules?->company_size_module) {
                $menu[] = ['text'=>'size','url'=>'/sizes','icon'=>'fas fa-fw fa-square','icon_color'=>$iconColor];
            }

            $menu[] = ['text'=>'additional','url'=>'/additional','icon'=>'fas fa-fw fa-plus-square','icon_color'=>$iconColor];

            if ($modules?->company_type_module) {
                $menu[] = ['text'=>'type','url'=>'/type','icon'=>'fas fa-fw fa-list','icon_color'=>$iconColor];
            }
            if ($modules?->company_offers_type_module) {
                $menu[] = ['text'=>'offers_type','url'=>'/offers_type','icon'=>'fas fa-fw fa-list','icon_color'=>$iconColor];
            }
            if ($modules?->company_offers_module) {
                $menu[] = ['text'=>'offers','url'=>'/offers','icon'=>'fas fa-fw fa-heart','icon_color'=>$iconColor];
            }
            if ($modules?->product_module) {
                $menu[] = ['text'=>'products','url'=>'/product','icon'=>'fas fa-fw fa-box','icon_color'=>$iconColor];
            }
            if ($modules?->employee_module) {
                $menu[] = ['text'=>'employees','url'=>'/employees','icon'=>'fas fa-fw fa-users','icon_color'=>$iconColor];
            }
            if ($modules?->order_module) {
                $menu[] = ['text'=>'orders','url'=>'/orders','icon'=>'fas fa-fw fa-shopping-cart','icon_color'=>$iconColor];
                if ($modules?->order_status_module) {
                    $menu[] = ['text'=>'order_status','url'=>'/order_status','icon'=>'fas fa-fw fa-clipboard-check','icon_color'=>$iconColor];
                }
            }
            if ($modules?->region_module) {
                $menu[] = ['text'=>'regions','url'=>'/regions','icon'=>'fas fa-fw fa-map-marked-alt','icon_color'=>$iconColor];
            }
            if ($modules?->payment_module) {
                $menu[] = ['text'=>'payment','url'=>'/payment','icon'=>'fas fa-fw fa-credit-card','icon_color'=>$iconColor];
            }
            if ($modules?->company_delivery_module) {
                $menu[] = ['text'=>'company_delivery_model','url'=>'/company_delivery','icon'=>'fas fa-fw fa-truck','icon_color'=>$iconColor];
            }

            Config::set('adminlte.menu', $menu);
        });
    }
}

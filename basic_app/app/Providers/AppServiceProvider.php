<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use App\Helpers\CustomSettings;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * Customize the verification email.
         * This runs for HTTP + console (safe).
         */

        /**
         * Only do session/DB/view work during HTTP requests.
         */
        if (App::runningInConsole()) {
            return;
        }

        // Locale from session (if set)
        try {
            if (Session::has('locale')) {
                App::setLocale(Session::get('locale'));
            }
        } catch (\Throwable $e) {
            // ignore if session not started yet
        }

        /**
         * 1) Share theme colors with all views (fail-safe if DB isn’t ready).
         */
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
            } catch (\Throwable $e) {
                // keep defaults if anything fails early
            }

            $view->with(compact('mainColor', 'subColor', 'textColor', 'iconColor'));
        });

        /**
         * 2) Build AdminLTE menu dynamically per-request.
         */
        View::composer('*', function () {
            // Default icon color
            $iconColor = '#000000';
            try {
                $colors    = CustomSettings::colors();
                $iconColor = $colors['icon_color'] ?? $iconColor;
            } catch (\Throwable $e) {
                // keep default
            }

            $menu = [];

            // Always show dashboard
            $menu[] = [
                'text'       => 'dashboard',
                'url'        => '/',
                'icon'       => 'fas fa-fw fa-tachometer-alt',
                'icon_color' => $iconColor,
            ];

            // Permissions & Modules (adjust with RBAC if needed)
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

            // Get current user modules (nullable)
            $modules = Auth::check() ? (Auth::user()->modulesHistory ?? null) : null;

            if ($modules?->company_info_module) {
                $menu[] = [
                    'text'       => 'company_info',
                    'url'        => '/companyInfo',
                    'icon'       => 'fas fa-fw fa-info-circle',
                    'icon_color' => $iconColor,
                ];
            }

            if ($modules?->company_branch_module) {
                $menu[] = [
                    'text'       => 'branches',
                    'url'        => '/branches',
                    'icon'       => 'fas fa-fw fa-code-branch',
                    'icon_color' => $iconColor,
                ];
            }

            if ($modules?->company_category_module) {
                $menu[] = [
                    'text'       => 'category',
                    'url'        => '/categories',
                    'icon'       => 'fas fa-fw fa-list',
                    'icon_color' => $iconColor,
                ];
            }

            if ($modules?->company_size_module) {
                $menu[] = [
                    'text'       => 'size',
                    'url'        => '/sizes',
                    'icon'       => 'fas fa-fw fa-square',
                    'icon_color' => $iconColor,
                ];
            }

            // Additional (always visible; gate it if needed)
            $menu[] = [
                'text'       => 'additional',
                'url'        => '/additional',
                'icon'       => 'fas fa-fw fa-plus-square',
                'icon_color' => $iconColor,
            ];

            if ($modules?->company_type_module) {
                $menu[] = [
                    'text'       => 'type',
                    'url'        => '/type',
                    'icon'       => 'fas fa-fw fa-list',
                    'icon_color' => $iconColor,
                ];
            }

            if ($modules?->company_offers_type_module) {
                $menu[] = [
                    'text'       => 'offers_type',
                    'url'        => '/offers_type',
                    'icon'       => 'fas fa-fw fa-list',
                    'icon_color' => $iconColor,
                ];
            }

            if ($modules?->company_offers_module) {
                $menu[] = [
                    'text'       => 'offers',
                    'url'        => '/offers',
                    'icon'       => 'fas fa-fw fa-heart',
                    'icon_color' => $iconColor,
                ];
            }

            if ($modules?->product_module) {
                $menu[] = [
                    'text'       => 'products',
                    'url'        => '/product',
                    'icon'       => 'fas fa-fw fa-box',
                    'icon_color' => $iconColor,
                ];
            }

            if ($modules?->employee_module) {
                $menu[] = [
                    'text'       => 'employees',
                    'url'        => '/employees',
                    'icon'       => 'fas fa-fw fa-users',
                    'icon_color' => $iconColor,
                ];
            }

            if ($modules?->order_module) {
                $menu[] = [
                    'text'       => 'orders',
                    'url'        => '/orders',
                    'icon'       => 'fas fa-fw fa-shopping-cart',
                    'icon_color' => $iconColor,
                ];

                if ($modules?->order_status_module) {
                    $menu[] = [
                        'text'       => 'order_status',
                        'url'        => '/order_status',
                        'icon'       => 'fas fa-fw fa-clipboard-check',
                        'icon_color' => $iconColor,
                    ];
                }
            }

            if ($modules?->region_module) {
                $menu[] = [
                    'text'       => 'regions',
                    'url'        => '/regions',
                    'icon'       => 'fas fa-fw fa-map-marked-alt',
                    'icon_color' => $iconColor,
                ];
            }

            if ($modules?->payment_module) {
                $menu[] = [
                    'text'       => 'payment',
                    'url'        => '/payment',
                    'icon'       => 'fas fa-fw fa-credit-card',
                    'icon_color' => $iconColor,
                ];
            }

            if ($modules?->company_delivery_module) {
                $menu[] = [
                    'text'       => 'company_delivery_model',
                    'url'        => '/company_delivery',
                    'icon'       => 'fas fa-fw fa-truck',
                    'icon_color' => $iconColor,
                ];
            }

            // Apply dynamic menu for this request
            Config::set('adminlte.menu', $menu);
        });
    }
}

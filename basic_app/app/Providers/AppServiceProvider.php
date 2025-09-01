<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
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
        if (session()->has('locale')) {
            app()->setLocale(session('locale'));
        }

        // Share colors with all views
        View::composer('*', function ($view) {
            $colors = CustomSettings::colors(); // returns array
            $mainColor = $colors['main_color'] ?? '#FF2D20';
            $subColor = $colors['sub_color'] ?? '#1A202C';
            $textColor = $colors['text_color'] ?? '#22223B';
            $iconColor = $colors['icon_color'] ?? '#000000'; // Icon color
            $view->with(compact('mainColor', 'subColor', 'textColor', 'iconColor'));
        });

        // Inject dynamic menu with icon color
        $colors = CustomSettings::colors();
        $iconColor = $colors['icon_color'] ?? '#000000';

        // Dynamically apply icon color to the menu icons
        config([
            'adminlte.menu' => [
                // Dashboard
                [
                    'text' => 'dashboard',
                    'url'  => '/',
                    'icon' => 'fas fa-fw fa-tachometer-alt',
                    'icon_color' => $iconColor,
                ],

                // Company Info
                [
                    'text' => 'company_info',
                    'url'  => 'companyInfo/',
                    'icon' => 'fas fa-fw fa-info-circle',
                    'icon_color' => $iconColor,
                ],

                // Branches
                [
                    'text' => 'branches',
                    'url'  => 'branches/',
                    'icon' => 'fas fa-fw fa-code-branch',
                    'icon_color' => $iconColor,
                ],

                // Category
                [
                    'text' => 'category',
                    'url'  => 'categories/',
                    'icon' => 'fas fa-fw fa-list',
                    'icon_color' => $iconColor,
                ],

                // Size
                [
                    'text' => 'size',
                    'url'  => 'sizes/',
                    'icon' => 'fa-solid fa-size',
                    'icon_color' => $iconColor,
                ],

                // Type
                [
                    'text' => 'type',
                    'url'  => 'type/',
                    'icon' => 'fas fa-fw fa-list',
                    'icon_color' => $iconColor,
                ],

                // Additional
                [
                    'text' => 'additional',
                    'url'  => 'additional/',
                    'icon' => 'fas fa-fw fa-add',
                    'icon_color' => $iconColor,
                ],

                // Offers Type
                [
                    'text' => 'offers_type',
                    'url'  => 'offers_type/',
                    'icon' => 'fas fa-fw fa-list',
                    'icon_color' => $iconColor,
                ],

                // Products
                [
                    'text' => 'products',
                    'url'  => 'product/',
                    'icon' => 'fas fa-fw fa-list',
                    'icon_color' => $iconColor,
                ],

                // Offers
                [
                    'text' => 'offers',
                    'url'  => 'offers/',
                    'icon' => 'fa-solid fa-heart',
                    'icon_color' => $iconColor,
                ],

                // Settings Section
            ],
        ]);

        // Set icon color globally for the menu
    }
}

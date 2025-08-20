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
            $view->with(compact('mainColor', 'subColor', 'textColor'));
        });

        // Inject dynamic menu with icon color
        $colors = CustomSettings::colors();
        $iconColor = $colors['icon_color'] ?? '#000000';

        config([
            'adminlte.menu' => [
    // Dashboard
    [
        'text' => 'dashboard',
        'url'  => '/',
        'icon' => 'fas fa-fw fa-tachometer-alt',
    ],

    // Company Info
    [
        'text' => 'company_info',
        'url'  => 'companyInfo/',
        'icon' => 'fas fa-fw fa-info-circle',
    ],

    // Branches
    [
        'text' => 'branches',
        'url'  => 'branches/',
        'icon' => 'fas fa-fw fa-code-branch',
    ],

     // category
    [
        'text' => 'category',
        'url'  => 'categories/',
        'icon' => 'fas fa-fw fa-code-category',
    ],
    // Employees
    [
        'text' => 'employees',
        'url'  => 'employees/',
        'icon' => 'fas fa-fw fa-users',
    ],

    // Settings Section

],
]);

        // Set icon color

        // Share colors with all views
    }
}

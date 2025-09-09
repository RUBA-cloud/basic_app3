<?php

namespace App\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Respect session locale during web requests
        if (!app()->runningInConsole() && session()->has('locale')) {
            app()->setLocale(session('locale'));
        }

        // Build AdminLTE menu per request (permission-aware)
        $this->app['view']->composer('*', function () {
            // Default icon color
            $iconColor = '#000000';
            try {
                $colors = \App\Helpers\CustomSettings::colors();
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

            // Current user, modules history, and permissions (if available)
            $user    = Auth::user();
            $modules = $user?->modulesHistory;               // legacy flags
            $perms   = $user?->permissions?->keyBy('module_key'); // expects module_key, can_view, can_add...

            // Map of menu sections
            $map = [
                'company_info' => [
                    'label'       => 'company_info',
                    'index_url'   => 'companyInfo/',
                    'create_url'  => 'companyInfo/create',
                    'icon'        => 'fas fa-fw fa-info-circle',
                    'module_flag' => 'company_info_module',
                ],
                'branches' => [
                    'label'       => 'branches',
                    'index_url'   => 'branches/',
                    'create_url'  => 'branches/create',
                    'icon'        => 'fas fa-fw fa-code-branch',
                    'module_flag' => 'company_branch_module',
                ],
                'categories' => [
                    'label'       => 'category',
                    'index_url'   => 'categories/',
                    'create_url'  => 'categories/create',
                    'icon'        => 'fas fa-fw fa-list',
                    'module_flag' => 'company_category_module',
                ],
                'sizes' => [
                    'label'       => 'size',
                    'index_url'   => 'sizes/',
                    'create_url'  => 'sizes/create',
                    'icon'        => 'fas fa-fw fa-square',
                    'module_flag' => 'company_size_module',
                ],
                'types' => [
                    'label'       => 'type',
                    'index_url'   => 'type/',
                    'create_url'  => 'type/create',
                    'icon'        => 'fas fa-fw fa-list',
                    'module_flag' => 'company_type_module',
                ],
                'offers_types' => [
                    'label'       => 'offers_type',
                    'index_url'   => 'offers_type/',
                    'create_url'  => 'offers_type/create',
                    'icon'        => 'fas fa-fw fa-list',
                    'module_flag' => 'company_offers_type_module',
                ],
                'offers' => [
                    'label'       => 'offers',
                    'index_url'   => 'offers/',
                    'create_url'  => 'offers/create',
                    'icon'        => 'fas fa-fw fa-heart',
                    'module_flag' => 'company_offers_module',
                ],
                'products' => [
                    'label'       => 'products',
                    'index_url'   => 'product/',
                    'create_url'  => 'product/create',
                    'icon'        => 'fas fa-fw fa-box',
                    'module_flag' => 'product_module',
                ],
                'employees' => [
                    'label'       => 'employees',
                    'index_url'   => 'employees/',
                    'create_url'  => 'employees/create',
                    'icon'        => 'fas fa-fw fa-users',
                    'module_flag' => 'employee_module',
                ],
                'orders' => [
                    'label'       => 'orders',
                    'index_url'   => 'orders/',
                    'create_url'  => 'orders/create',
                    'icon'        => 'fas fa-fw fa-shopping-cart',
                    'module_flag' => 'order_module',
                ],
                'permissions' => [
                    'label'       => 'permissions',
                    'index_url'   => 'permissions/',
                    'create_url'  => 'permissions/create',
                    'icon'        => 'fas fa-fw fa-list',
                    'module_flag' => null,
                ],
            ];

            // Add item only if user can view it (or module is enabled)
            $addItem = function (string $key, array $cfg) use (&$menu, $perms, $modules, $iconColor) {
                $permRow   = $perms[$key] ?? null;
                $canView   = (bool) Arr::get($permRow, 'can_view', false);
                $canCreate = (bool) Arr::get($permRow, 'can_add', false);

                // Fallback to legacy module flags if no explicit permission
                $moduleEnabled = $cfg['module_flag']
                    ? (bool) Arr::get($modules ?? [], $cfg['module_flag'], false)
                    : false;

                if (!$canView && !$moduleEnabled) {
                    return;
                }

                $item = [
                    'text'       => $cfg['label'],
                    'url'        => $cfg['index_url'],
                    'icon'       => $cfg['icon'],
                    'icon_color' => $iconColor,
                ];

                if ($canCreate && !empty($cfg['create_url'])) {
                    $item['submenu'] = [
                        [
                            'text' => 'add',
                            'url'  => $cfg['create_url'],
                            'icon' => 'fas fa-plus',
                        ],
                    ];
                }

                $menu[] = $item;
            };

            foreach ($map as $key => $cfg) {
                $addItem($key, $cfg);
            }

            Config::set('adminlte.menu', $menu);
        });
    }
}

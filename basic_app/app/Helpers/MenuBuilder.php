<?php

namespace App\Helpers;

use App\Models\User;

class MenuBuilder
{
    public static function build(User $user, string $iconColor = '#000000'): array
    {
        $menu = [
            [
                'text'       => 'dashboard',
                'url'        => '/home',
                'icon'       => 'fas fa-fw fa-tachometer-alt',
                'icon_color' => $iconColor,
                'can'        => $user->canUseModule('company_dashboard_module'),
            ],
        ];

        $add = function(array $item) use (&$menu) {
            if (!empty($item['can'])) $menu[] = $item;
        };

        $add([
            'text'       => 'company_info',
            'url'        => '/companyInfo',
            'icon'       => 'fas fa-fw fa-info-circle',
            'icon_color' => $iconColor,
            'can'        => $user->canUseModule('company_info_module'),
        ]);

        $add([
            'text'       => 'branches',
            'url'        => '/companyBranch',
            'icon'       => 'fas fa-fw fa-code-branch',
            'icon_color' => $iconColor,
            'can'        => $user->canUseModule('company_branch_module'),
        ]);

        $add([
            'text'       => 'category',
            'url'        => '/categories',
            'icon'       => 'fas fa-fw fa-list',
            'icon_color' => $iconColor,
            'can'        => $user->canUseModule('company_category_module'),
        ]);

        $add([
            'text'       => 'type',
            'url'        => '/type',
            'icon'       => 'fas fa-fw fa-list',
            'icon_color' => $iconColor,
            'can'        => $user->canUseModule('company_type_module'),
        ]);

        $add([
            'text'       => 'size',
            'url'        => '/sizes',
            'icon'       => 'fas fa-fw fa-square',
            'icon_color' => $iconColor,
            'can'        => $user->canUseModule('company_size_module'),
        ]);

        $add([
            'text'       => 'offers_type',
            'url'        => '/offers_type',
            'icon'       => 'fas fa-fw fa-list',
            'icon_color' => $iconColor,
            'can'        => $user->canUseModule('company_offers_type_module'),
        ]);

        $add([
            'text'       => 'offers',
            'url'        => '/offers',
            'icon'       => 'fas fa-fw fa-heart',
            'icon_color' => $iconColor,
            'can'        => $user->canUseModule('company_offers_module'),
        ]);

        $add([
            'text'       => 'products',
            'url'        => '/product',
            'icon'       => 'fas fa-fw fa-box',
            'icon_color' => $iconColor,
            'can'        => $user->canUseModule('product_module'),
        ]);

        $add([
            'text'       => 'employees',
            'url'        => '/employees',
            'icon'       => 'fas fa-fw fa-users',
            'icon_color' => $iconColor,
            'can'        => $user->canUseModule('employee_module'),
        ]);

        $add([
            'text'       => 'orders',
            'url'        => '/orders',
            'icon'       => 'fas fa-fw fa-shopping-cart',
            'icon_color' => $iconColor,
            'can'        => $user->canUseModule('order_module'),
        ]);

        $add([
            'text'       => 'regions',
            'url'        => '/regions',
            'icon'       => 'fas fa-fw fa-map-marked-alt',
            'icon_color' => $iconColor,
            'can'        => $user->canUseModule('region_module'),
        ]);

        $add([
            'text'       => 'payment',
            'url'        => '/payment',
            'icon'       => 'fas fa-fw fa-credit-card',
            'icon_color' => $iconColor,
            'can'        => $user->canUseModule('payment_module'),
        ]);

        $add([
            'text'       => 'company_delivery_model',
            'url'        => '/company_delivery',
            'icon'       => 'fas fa-fw fa-truck',
            'icon_color' => $iconColor,
            'can'        => $user->canUseModule('company_delivery_module'),
        ]);

        // Optional admin area
        if (($user->role ?? null) === 'admin') {
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
        }

        // Filter out items with 'can' = false
        return array_values(array_filter($menu, fn($i) => !array_key_exists('can', $i) || $i['can']));
    }
}

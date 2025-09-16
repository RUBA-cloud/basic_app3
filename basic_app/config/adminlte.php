<?php

return [

    // -----------------------------
    // Title
    // -----------------------------
    'title' => 'AdminLTE 3',
    'title_prefix' => '',
    'title_postfix' => '',

    /**
     * Make RTL a static flag (don’t call app()->getLocale() in config)
     */
    'layout_rtl' => env('ADMINLTE_RTL', false),

    // -----------------------------
    // Favicon
    // -----------------------------
    'use_ico_only' => false,
    'use_full_favicon' => false,

    // -----------------------------
    // Google Fonts
    // -----------------------------
    'google_fonts' => [
        'allowed' => true,
    ],

    // -----------------------------
    // Admin Panel Logo
    // -----------------------------
    // Admin Panel Logo
'logo'           => '',   // text you want to show
'logo_img'       => null,
'logo_img_class'   => 'brand-image elevation-3 brand-image-40', // <— custom size class
               // <- remove image
          // optional
'logo_img_xl'    => null,                   // optional
'logo_img_xl_class' => '',                  // optional
'logo_img_alt'   => '',
                 // optional

    // -----------------------------
    // Preloader
    // -----------------------------
    'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',


    ],

    // -----------------------------
    // User Menu
    // -----------------------------
    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    // -----------------------------
    // Layout toggles
    // -----------------------------
    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    // -----------------------------
    // Classes
    // -----------------------------
    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    // -----------------------------
    // Sidebar
    // -----------------------------
    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    // -----------------------------
    // Control Sidebar (Right)
    // -----------------------------
    'right_sidebar' => true,
    'right_sidebar_icon' => 'fas fa-bell',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    // -----------------------------
    // URLs
    // -----------------------------
    'use_route_url' => false,     // we’ll feed absolute URLs via AppServiceProvider
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    'disable_darkmode_routes' => false,

    // -----------------------------
    // Laravel Asset Bundling
    // -----------------------------
    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    // -----------------------------
    // Menu
    // -----------------------------
    // Keep empty here — we will inject it per-request in AppServiceProvider.
    'menu' => [],

    // -----------------------------
    // Menu Filters
    // -----------------------------
    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    // -----------------------------
    // Plugins (unchanged)
    // -----------------------------
    'plugins' => [
        'Datatables' => [
            'active' => false,
            'files' => [
                ['type' => 'js',  'asset' => false, 'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js'],
                ['type' => 'js',  'asset' => false, 'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js'],
                ['type' => 'css', 'asset' => false, 'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css'],
            ],
        ],
        'TempusDominusBs4' => [
            'active' => false,
            'files' => [
                ['type' => 'js',  'asset' => true, 'location' => 'vendor/moment/moment.min.js'],
                ['type' => 'js',  'asset' => true, 'location' => 'vendor/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js'],
                ['type' => 'css', 'asset' => true, 'location' => 'vendor/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css'],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                ['type' => 'js',  'asset' => false, 'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js'],
                ['type' => 'css', 'asset' => false, 'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css'],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                ['type' => 'js',  'asset' => false, 'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js'],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                ['type' => 'js',  'asset' => false, 'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8'],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                ['type' => 'css', 'asset' => false, 'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css'],
                ['type' => 'js',  'asset' => false, 'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js'],
            ],
        ],
    ],

    // -----------------------------
    // IFrame
    // -----------------------------
    'iframe' => [
        'default_tab' => ['url' => null, 'title' => null],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    // -----------------------------
    // Livewire
    // -----------------------------
    'livewire' => true,
];

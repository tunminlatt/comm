<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#61-title
    |
    */

    'title' => 'YYAT',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#62-logo
    |
    */

    'logo' => '<b>YYAT</b>',
    'logo_img' => 'logo.png',
    'logo_img_class' => 'brand-image-xl',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'YYAT',

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#63-layout
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,

    /*
    |--------------------------------------------------------------------------
    | Extra Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#64-classes
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_header' => 'container-fluid',
    'classes_content' => 'container-fluid',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand-md',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#65-sidebar
    |
    */

    'sidebar_mini' => true,
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#66-control-sidebar-right-sidebar
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#67-urls
    |
    */

    'use_route_url' => false,

    'dashboard_url' => 'home',

    'logout_url' => 'logout',

    'login_url' => 'login',

    'register_url' => false,

    'password_reset_url' => 'password/reset',

    'password_email_url' => 'password/email',

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#68-laravel-mix
    |
    */

    'enabled_laravel_mix' => false,

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#69-menu
    |
    */

    'menu' => [
        ['header' => 'Pages'],
        [
            'text'        => ' Stations',
            'url'         => 'stations',
            'icon'        => 'fas fa-satellite-dish',
            'id'        => 'stations',
        ],
        [
            'text'        => ' Station Managers',
            'url'         => 'stationManagers',
            'icon'        => 'fas fa-user-tie',
            'id'         => 'stationManagers',
        ],
        [
            'text'        => ' Volunteers',
            'url'         => 'volunteers',
            'icon'        => 'fas fa-headphones-alt',
            'id'         => 'volunteers',
        ],
        // [
        //     'text'        => ' Audios',
        //     'url'         => 'audios',
        //     'icon'        => 'fas fa-microphone',
        //     'id'         => 'audios',
        // ],
        [
            'text'        => '  YYAT Vol',
            'url'         => 'contents',
            'icon'        => 'fas fa-microphone',
            'id'         => 'contents',
        ],
        [
            'text'        => '  YYAT',
            'url'         => 'programmes',
            'icon'        => 'fas fa-clipboard-list',
            'id'         => 'programmes',
        ],
        // [
        //     'text'        => ' Share',
        //     'url'         => 'shares',
        //     'icon'        => 'fas fa-share-alt',
        //     'id'         => 'shares',
        // ],
        // [
        //     'text'        => ' Documents',
        //     'url'         => 'documents',
        //     'icon'        => 'fas fa-file',
        //     'id'         => 'documents',
        // ],
        [
            'text'        => ' Admins',
            'url'         => 'users',
            'icon'        => 'fas fa-user-shield',
            'id'         => 'users',
        ],
        /*[
            'text'        => 'Andriod Version (Developer Only)',
            'url'         => 'andriodVersions',
            'icon'        => 'fas fa-mobile',
            'id'         => 'andriodVersions',
        ]*/
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#610-menu-filters
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SubmenuFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        App\Helpers\MenuFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#611-plugins
    |
    */

    'plugins' => [
        [
            'name' => 'Datatables',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/datatables.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'css/datatables.min.css',
                ],
            ],
        ],
        [
            'name' => 'Select2',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'css/select2.css',
                ],
            ],
        ],
        [
            'name' => 'FancyBox',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery.fancybox.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'css/jquery.fancybox.min.css',
                ],
            ],
        ],
        [
            'name' => 'Toastr',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/toastr.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'css/toastr.min.css',
                ],
            ],
        ],
        [
            'name' => 'ScrollUp',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/jquery.scrollUp.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'css/tab.css',
                ],
            ],
        ],
        [
            'name' => 'Moment',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/moment.min.js',
                ],
            ],
        ],
        [
            'name' => 'ComboTree',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/comboTreePlugin.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'css/comboTreePlugin.min.css',
                ],
            ],
        ],
        [
            'name' => 'Bootbox',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/bootbox.min.js',
                ],
            ],
        ],
        [
            'name' => 'Global',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/fileUpload.js?v1.3',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/general.js?v2.0',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'css/global.css?v1.7',
                ],
            ],
        ],
        [
            'name' => 'Chartjs',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'js/Chart.bundle.min.js',
                ],
            ],
        ],
        [
            'name' => 'Sweetalert2',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/sweetalert2.min.js',
                ],
            ],
        ],
        [
            'name' => 'Pace',
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'css/pace-theme-minimal.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/pace.min.js',
                ],
            ],
        ],
        [
            'name' => 'Bootstrap DateRangePicker',
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'css/daterangepicker.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/daterangepicker.min.js',
                ],
            ],
        ],
        [
            'name' => 'Bootstrap DateTimePicker',
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'css/bootstrap-datetimepicker.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'js/bootstrap-datetimepicker.min.js',
                ],
            ],
        ],
    ],
];

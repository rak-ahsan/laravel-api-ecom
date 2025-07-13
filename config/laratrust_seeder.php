<?php

return [
    /**
     * Control if the seeder should create a user per role while seeding the data.
     */
    'create_users' => false,

    /**
     * Control if all the laratrust tables should be truncated before running the seeder.
     */
    'truncate_tables' => true,

    'roles_structure' => [
        'superadmin' => [
            'abouts'                 => 'c,r,u,d',
            'areas'                  => 'c,r,u,d',
            'attributes'             => 'c,r,u,d',
            'attribute-values'       => 'c,r,u,d',
            'banners'                => 'c,r,u,d',
            'brands'                 => 'c,r,u,d',
            'blog-posts'             => 'c,r,u,d',
            'campaign'               => 'c,r,u',
            'campaigns'              => 'c,r,u,d',
            'coupons'                => 'c,r,u,d',
            'contacts'               => 'c,r,u,d',
            'categories'             => 'c,r,u,d',
            'user-categories'        => 'c,r,u,d',
            'dashboards'             => 'r',
            'districts'              => 'c,r,u,d',
            'delivery-gateways'      => 'c,r,u,d',
            'expenses'               => 'c,r,u,d',
            'expense-categories'     => 'c,r,u,d',
            'faqs'                   => 'c,r,u,d',
            'free-delivery'          => 'c,r,u,d',
            'orders'                 => 'c,r,u,d',
            'order-froms'            => 'c,r,u,d',
            'order-guards'            => 'c,r,u,d',
            'orders-prepare'         => 'u',
            'orders-team-list'       => 'r',
            'orders-prepare-restore' => 'u',
            'products'               => 'c,r,u,d',
            'permissions'            => 'c,r,u,d',
            'purchases'              => 'c,r,u,d',
            'payment-gateways'       => 'c,r,u,d',
            'privacy-policies'       => 'c,r,u,d',
            'reviews'                => 'c,r,u,d',
            'roles'                  => 'c,r,u,d',
            'raw-materials'          => 'c,r,u,d',
            'reports'                => 'r',
            'sliders'                => 'c,r,u,d',
            'statuses'               => 'c,r,u,d',
            'sub-categories'         => 'c,r,u,d',
            'settings'               => 'c,r,u,d',
            'setting-category'       => 'c,r,u,d',
            'sections'               => 'c,r,u,d',
            'suppliers'              => 'c,r,u,d',
            'time-schedules'         => 'c,r,u,d',
            'tags'                   => 'c,r,u,d',
            'teams'                  => 'c,r,u,d',
            'terms-and-conditions'   => 'c,r,u,d',
            'users'                  => 'c,r,u,d',
            'warranties'             => 'c,r,u,d',
            'zones'                  => 'c,r,u,d',
            'social-medias'          => 'c,r,u,d',
            'pathao'                 => 'u',
            'stead-fast'             => 'u',
            'redx'                   => 'u',
        ],

        'admin' => [],
    ],

    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete',
    ],
];

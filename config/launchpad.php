<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Installation Settings
    |--------------------------------------------------------------------------
    */
    'installation' => [
        'enabled' => false,
        'route_prefix' => 'install',
        'route_middleware' => ['web'],
        'completed_file' => storage_path('app/installed.lock'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Update Settings
    |--------------------------------------------------------------------------
    */
    'update' => [
        'enabled' => false,
        'route_prefix' => 'update',
        'route_middleware' => ['web'],
        'version_file' => storage_path('app/version.lock'),
        'current_version' => '1.0.0',
    ],

    /*
    |--------------------------------------------------------------------------
    | Environment Requirements
    |--------------------------------------------------------------------------
    | Define PHP version, extensions, and directory permissions required
    */
    'requirements' => [
        'php' => [
            'min_version' => '8.0.0',
            'recommended_version' => '8.2.0',
        ],
        'extensions' => [
            'required' => [
                'openssl',
                'pdo',
                'mbstring',
                'tokenizer',
                'xml',
                'ctype',
                'json',
                'bcmath',
                'curl',
                'fileinfo',
                'zip',
            ],
            'recommended' => [
                'gd',
                'imagick',
                'redis',
                'memcached',
            ],
        ],
        'directories' => [
            'writable' => [
                'storage',
                'storage/app',
                'storage/framework',
                'storage/framework/cache',
                'storage/framework/cache/data',
                'storage/framework/sessions',
                'storage/framework/views',
                'storage/logs',
                'bootstrap/cache',
            ],
        ],
        'functions' => [
            'enabled' => [
                'exec',
                'shell_exec',
                'file_get_contents',
                'file_put_contents',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | License Verification
    |--------------------------------------------------------------------------
    */
    'license' => [
        'enabled' => true,
        'validator_class' => 'App\\Services\\EnvatoLicenseChecker',
        'server_url' => null,
        'timeout' => 30,
        'cache_duration' => 3600, // Cache license verification for 1 hour
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    */
    'database' => [
        'test_connection' => true,
        'supported_drivers' => ['mysql', 'pgsql', 'sqlite', 'sqlsrv'],
        'import_options' => [
            'dump_file' => [
                'enabled' => true,
                'path' => database_path('dump.sql'),
                'description' => 'Import initial database dump',
            ],
            'migrations' => [
                'enabled' => true,
                'description' => 'Run database migrations',
            ],
            'seeders' => [
                'enabled' => true,
                'description' => 'Run database seeders',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin User Configuration
    |--------------------------------------------------------------------------
    */
    'admin' => [
        'enabled' => true,
        'model' => 'App\\Models\\User',
        'fields' => [
            'name' => [
                'type' => 'text',
                'label' => 'Full Name',
                'required' => true,
                'validation' => 'required|string|max:255',
            ],
            'email' => [
                'type' => 'email',
                'label' => 'Email Address',
                'required' => true,
                'validation' => 'required|email|unique:users,email',
            ],
            'password' => [
                'type' => 'password',
                'label' => 'Password',
                'required' => true,
                'validation' => 'required|min:8|confirmed',
            ],
            'password_confirmation' => [
                'type' => 'password',
                'label' => 'Confirm Password',
                'required' => true,
                'validation' => 'required',
            ],
        ],
        'default_data' => [
            'role' => 'admin',
            'is_active' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Additional Installation Fields
    |--------------------------------------------------------------------------
    | Define custom fields for the installation process
    */
    'additional_fields' => [
        'site_settings' => [
            'group_label' => 'Site Settings',
            'fields' => [
                'app_name' => [
                    'type' => 'text',
                    'label' => 'Application Name',
                    'required' => true,
                    'validation' => 'required|string|max:255',
                    'env_key' => 'APP_NAME',
                ],
                'app_url' => [
                    'type' => 'url',
                    'label' => 'Application URL',
                    'required' => true,
                    'validation' => 'required|url',
                    'env_key' => 'APP_URL',
                    'default' => 'http://localhost',
                ],
                'timezone' => [
                    'type' => 'select',
                    'label' => 'Timezone',
                    'required' => true,
                    'validation' => 'required|string',
                    'env_key' => 'APP_TIMEZONE',
                    'options' => 'timezones', // Will be populated with timezone list
                    'default' => 'UTC',
                ],
            ],
        ],
        'mail_settings' => [
            'group_label' => 'Mail Settings (Optional)',
            'fields' => [
                'mail_mailer' => [
                    'type' => 'select',
                    'label' => 'Mail Driver',
                    'required' => false,
                    'validation' => 'nullable|string',
                    'env_key' => 'MAIL_MAILER',
                    'options' => [
                        'smtp' => 'SMTP',
                        'sendmail' => 'Sendmail',
                        'mailgun' => 'Mailgun',
                        'ses' => 'Amazon SES',
                        'log' => 'Log (Testing)',
                    ],
                    'default' => 'smtp',
                ],
                'mail_host' => [
                    'type' => 'text',
                    'label' => 'SMTP Host',
                    'required' => false,
                    'validation' => 'nullable|string',
                    'env_key' => 'MAIL_HOST',
                    'show_if' => ['mail_mailer' => 'smtp'],
                ],
                'mail_port' => [
                    'type' => 'number',
                    'label' => 'SMTP Port',
                    'required' => false,
                    'validation' => 'nullable|integer',
                    'env_key' => 'MAIL_PORT',
                    'default' => 587,
                    'show_if' => ['mail_mailer' => 'smtp'],
                ],
                'mail_from_address' => [
                    'type' => 'email',
                    'label' => 'From Email',
                    'required' => false,
                    'validation' => 'nullable|email',
                    'env_key' => 'MAIL_FROM_ADDRESS',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Post Installation Actions
    |--------------------------------------------------------------------------
    */
    'post_install' => [
        'actions' => [
            'generate_app_key' => true,
            'cache_clear' => true,
            'config_cache' => true,
            'route_cache' => false,
            'view_cache' => false,
        ],
        'redirect_url' => '/admin',
    ],

    /*
    |--------------------------------------------------------------------------
    | Update Configuration
    |--------------------------------------------------------------------------
    */
    'update_options' => [
        'dump_file' => [
            'enabled' => true,
            'path' => database_path('updates/update.sql'),
            'description' => 'Import update database dump',
        ],
        'migrations' => [
            'enabled' => true,
            'description' => 'Run update migrations',
        ],
        'cache_clear' => true,
        'config_cache' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Configuration
    |--------------------------------------------------------------------------
    */
    'ui' => [
        'app_name' => env('APP_NAME', 'Laravel Application'),
        'logo_url' => null,
        'primary_color' => '#3B82F6',
        'footer' => [
            'show_credits' => true,
            'package_name' => 'Laravel Launchpad',
            'github_url' => 'https://github.com/sabitahmadumid/laravel-launchpad',
        ],
    ],
];

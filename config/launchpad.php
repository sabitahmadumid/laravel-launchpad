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
        'current_version' => '2.2.0',
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
    | License Verification - SIMPLE & SECURE
    |--------------------------------------------------------------------------
    | 
    | DEVELOPER-FRIENDLY LICENSE SYSTEM
    | 
    | This license system provides a simple but secure license validation
    | with developer-friendly options for local development.
    |
    | 🚀 QUICK DEVELOPMENT SETUP:
    | 
    |   Option 1: Disable license checks (Local/testing only)
    |   php artisan launchpad:license disable
    |   php artisan launchpad:license disable --install    # Disable for install routes only
    |   php artisan launchpad:license disable --update     # Disable for update routes only
    | 
    |   Option 2: Use development license keys
    |   php artisan launchpad:license-stub publish
    |   Use license key: "dev-license-key" or other development keys
    |
    | 🔒 PRODUCTION SECURITY:
    | - License validation is ALWAYS required in production environments
    | - Development bypasses are ignored in production
    | - SimpleLicenseValidator provides secure validation with domain binding
    |
    | �️ COMMANDS:
    | php artisan launchpad:license disable               # Disable license checks
    | php artisan launchpad:license enable                # Enable license checks  
    | php artisan launchpad:license disable --install     # Disable for install routes only
    | php artisan launchpad:license disable --update      # Disable for update routes only
    | php artisan launchpad:license-stub publish          # Publish validator stub
    |
    */
    'license' => [
        // License verification key from environment
        'key' => env('LAUNCHPAD_LICENSE_KEY'),

        // Custom validator class (SimpleLicenseValidator provides secure validation)
        'validator_class' => env('LAUNCHPAD_VALIDATOR_CLASS', 'App\\Services\\SimpleLicenseValidator'),

        // Request timeout for license validation
        'timeout' => env('LAUNCHPAD_LICENSE_TIMEOUT', 30),

        // Cache duration for license validation results (in seconds)
        'cache_duration' => env('LAUNCHPAD_LICENSE_CACHE', 3600),

        // Development bypass options (only works in local/testing environments)
        'development' => [
            // Accept development license keys in local environment
            'accept_dev_keys' => env('LAUNCHPAD_ACCEPT_DEV_KEYS', true),
            
            // Development license keys that always work in local/testing
            'dev_keys' => [
                'dev-license-key',
                'local-development', 
                'testing-license',
                'bypass-license-check',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Language & Internationalization
    |--------------------------------------------------------------------------
    | Configure multi-language support for the installation and update wizards.
    | Users can switch between available languages during the process.
    */
    'language' => [
        // Default language for the package
        'default' => env('LAUNCHPAD_DEFAULT_LANGUAGE', 'en'),

        // Available languages
        'available' => [
            'en' => [
                'name' => 'English',
                'native' => 'English',
                'flag' => '🇺🇸',
                'rtl' => false,
            ],
            'bn' => [
                'name' => 'Bengali',
                'native' => 'বাংলা',
                'flag' => '🇧🇩',
                'rtl' => false,
            ],
        ],

        // Auto-detect language from browser preferences
        'auto_detect' => env('LAUNCHPAD_AUTO_DETECT_LANGUAGE', true),

        // Store language preference in session
        'session_key' => 'launchpad_language',

        // Language switcher display settings
        'switcher' => [
            'enabled' => true,
            'show_flags' => true,
            'show_native_names' => true,
            'position' => 'top-right', // top-left, top-right, bottom-left, bottom-right
        ],
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

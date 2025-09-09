# 🚀 Laravel Launchpad

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sabitahmadumid/laravel-launchpad.svg?style=flat-square)](https://packagist.org/packages/sabitahmadumid/laravel-launchpad)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/sabitahmadumid/laravel-launchpad/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/sabitahmadumid/laravel-launchpad/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/sabitahmadumid/laravel-launchpad/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/sabitahmadumid/laravel-launchpad/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/sabitahmadumid/laravel-launchpad.svg?style=flat-square)](https://packagist.org/packages/sabitahmadumid/laravel-launchpad)

**Laravel Launchpad** is a comprehensive installation and update wizard package that makes it incredibly easy for developers to ship their Laravel applications and for end-users to install them. With professional UI components, license validation, environment checking, and a streamlined 3-step process, Launchpad transforms complex deployments into simple, guided experiences.

Perfect for SaaS applications, commercial Laravel products, or any Laravel application that needs professional installation and update capabilities.

## ✨ Features

- **🎯 Installation Wizard** - 5-step guided installation process for fresh deployments
- **🔄 Update Wizard** - 5-step guided update process for version upgrades  
- **⚠️ Mutually Exclusive Modes** - Installation and update wizards are designed to run independently (never simultaneously)
- **🛡️ License Validation** - Flexible license verification system with external server support
- **⚙️ Environment Checking** - PHP version, extensions, and directory permissions validation
- **🎨 Professional UI** - Modern, responsive interface built with Tailwind CSS and Alpine.js
- **🔧 Highly Configurable** - Customize every aspect through configuration files
- **🏗️ Clean Architecture** - Service-oriented design with proper separation of concerns
- **🔒 Security First** - Middleware protection and secure installation tracking
- **📱 Mobile Responsive** - Works perfectly on all devices and screen sizes
- **🎉 User Experience** - Smooth animations, progress indicators, and celebration effects

## 📋 Requirements

- PHP 8.1 or higher
- Laravel 10.0 or higher
- Composer

## 📦 Installation

Install the package via Composer:

```bash
composer require sabitahmadumid/laravel-launchpad
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag="laravel-launchpad-config"
```

Optionally, publish the views for customization:

```bash
php artisan vendor:publish --tag="laravel-launchpad-views"
```

## ⚙️ Configuration

The package comes with a comprehensive configuration file located at `config/launchpad.php`. Here are the key configuration options:

### Basic Settings

```php
return [
    'ui' => [
        'app_name' => 'Your App Name',
        'app_url' => env('APP_URL', 'http://localhost'),
        'admin_route' => '/admin',
        'logo_url' => null, // Optional logo URL
    ],
    
    'requirements' => [
        'php_version' => '8.1.0',
        'extensions' => [
            'openssl',
            'pdo',
            'mbstring',
            'tokenizer',
            'xml',
            'ctype',
            'json',
            'bcmath',
            'curl',
            'gd',
            'zip',
        ],
        'directories' => [
            'storage/app' => 0755,
            'storage/framework' => 0755,
            'storage/logs' => 0755,
            'bootstrap/cache' => 0755,
        ],
    ],
    
    'license' => [
        'enabled' => true,
        'validation_url' => 'https://your-license-server.com/validate',
        'timeout' => 30,
        'allow_local' => env('APP_ENV') === 'local',
    ],
    
    'database' => [
        'supported_drivers' => ['mysql', 'pgsql', 'sqlite'],
        'test_connection' => true,
        'default_charset' => 'utf8mb4',
        'default_collation' => 'utf8mb4_unicode_ci',
        'import_options' => [
            // ⚠️ IMPORTANT: Choose ONE database setup method
            'dump_file' => [
                'enabled' => true,  // ✅ Use SQL dump import
                'path' => database_path('dump.sql'),
                'description' => 'Import initial database dump',
            ],
            'migrations' => [
                'enabled' => false, // ❌ Disable when using dump
                'description' => 'Run database migrations',
            ],
            'seeders' => [
                'enabled' => true,  // Optional: Can be used with either method
                'description' => 'Run database seeders',
            ],
        ],
    ],
    
    'admin_fields' => [
        [
            'name' => 'name',
            'label' => 'Full Name',
            'type' => 'text',
            'required' => true,
        ],
        [
            'name' => 'email',
            'label' => 'Email Address',
            'type' => 'email',
            'required' => true,
        ],
        [
            'name' => 'password',
            'label' => 'Password',
            'type' => 'password',
            'required' => true,
        ],
    ],
];
```

### Dynamic Fields

You can add custom fields to the admin creation step:

```php
'additional_fields' => [
    [
        'name' => 'company',
        'label' => 'Company Name',
        'type' => 'text',
        'required' => false,
        'placeholder' => 'Enter your company name',
    ],
    [
        'name' => 'phone',
        'label' => 'Phone Number',
        'type' => 'tel',
        'required' => false,
    ],
],
```

### Database Setup Methods

> **⚠️ IMPORTANT: Database Import Conflicts**
> 
> **SQL Dumps** and **Migrations** are mutually exclusive and should NEVER be enabled simultaneously. This would cause database conflicts and unpredictable behavior. Choose the appropriate method based on your application structure:
>
> - **SQL Dump Method**: For applications with complex initial data or specific database structure
> - **Migrations Method**: For applications using Laravel's migration system

#### Method 1: SQL Dump Import (Recommended for Complex Apps)

**When to use**: Applications with complex initial data, views, stored procedures, or non-Laravel database structures.

```php
// config/launchpad.php - Database configuration
'database' => [
    'import_options' => [
        'dump_file' => [
            'enabled' => true,  // ✅ Enable SQL dump import
            'path' => database_path('dump.sql'),
            'description' => 'Import initial database dump',
        ],
        'migrations' => [
            'enabled' => false, // ❌ Disable migrations when using dump
            'description' => 'Run database migrations',
        ],
        'seeders' => [
            'enabled' => true,  // ✅ Optional: Additional seed data
            'description' => 'Run database seeders',
        ],
    ],
],
```

**Setup Steps**:
1. **Create Database Dump**: Export your complete database structure and data
2. **Place Dump File**: Save as `database/dump.sql` (or configure custom path)
3. **Configure Settings**: Enable dump import, disable migrations
4. **Test Import**: Ensure dump file imports successfully

**Advantages**:
- ✅ Handles complex database structures
- ✅ Includes initial data and configurations
- ✅ Faster installation for large datasets
- ✅ Works with non-Laravel database designs

#### Method 2: Laravel Migrations (Recommended for Standard Laravel Apps)

**When to use**: Standard Laravel applications using migration system for database structure.

```php
// config/launchpad.php - Database configuration
'database' => [
    'import_options' => [
        'dump_file' => [
            'enabled' => false, // ❌ Disable dump when using migrations
            'path' => database_path('dump.sql'),
            'description' => 'Import initial database dump',
        ],
        'migrations' => [
            'enabled' => true,  // ✅ Enable Laravel migrations
            'description' => 'Run database migrations',
        ],
        'seeders' => [
            'enabled' => true,  // ✅ Run database seeders after migrations
            'description' => 'Run database seeders',
        ],
    ],
],
```

**Setup Steps**:
1. **Prepare Migrations**: Ensure all migration files are ready
2. **Create Seeders**: Prepare initial data seeders (optional)
3. **Configure Settings**: Enable migrations, disable dump import
4. **Test Migrations**: Verify migrations run successfully

**Advantages**:
- ✅ Version-controlled database changes
- ✅ Laravel-native approach
- ✅ Easier to maintain and update
- ✅ Better for team development

#### Update Process Database Methods

For updates, you can also choose between SQL dumps or migrations:

```php
// config/launchpad.php - Update configuration
'update_options' => [
    // For SQL-based updates
    'dump_file' => [
        'enabled' => true,  // ✅ Use update SQL file
        'path' => database_path('updates/update.sql'),
        'description' => 'Import update database dump',
    ],
    'migrations' => [
        'enabled' => false, // ❌ Disable when using dump
        'description' => 'Run update migrations',
    ],
    
    // OR for migration-based updates
    'dump_file' => [
        'enabled' => false, // ❌ Disable when using migrations
        'path' => database_path('updates/update.sql'),
        'description' => 'Import update database dump',
    ],
    'migrations' => [
        'enabled' => true,  // ✅ Use Laravel migrations
        'description' => 'Run update migrations',
    ],
],
```

#### Database Configuration Examples

**Example 1: E-commerce App with Complex Data**
```php
// Uses SQL dump for complex product catalogs and configurations
'import_options' => [
    'dump_file' => ['enabled' => true, 'path' => database_path('ecommerce.sql')],
    'migrations' => ['enabled' => false],
    'seeders' => ['enabled' => true], // Additional demo products
],
```

**Example 2: Standard Laravel CRM**
```php
// Uses migrations for clean, version-controlled structure
'import_options' => [
    'dump_file' => ['enabled' => false],
    'migrations' => ['enabled' => true],
    'seeders' => ['enabled' => true], // Demo contacts and companies
],
```

**Example 3: Legacy Database Integration**
```php
// Uses SQL dump to preserve existing database structure
'import_options' => [
    'dump_file' => ['enabled' => true, 'path' => database_path('legacy.sql')],
    'migrations' => ['enabled' => false],
    'seeders' => ['enabled' => false], // Data already in dump
],
```

## 🎯 Usage

> **⚠️ IMPORTANT: Installation vs Update Modes**
> 
> **Installation** and **Update** wizards are mutually exclusive and should NEVER be enabled simultaneously. This would create routing conflicts and confuse users. Choose the appropriate mode based on your deployment phase:
>
> - **Installation Mode**: For fresh deployments (new installs)
> - **Update Mode**: For existing installations (version updates)

### Installation Mode (For New Deployments)

**When to use**: Deploying your Laravel application for the first time to end users.

**Configuration**:
```php
// config/launchpad.php
'installation' => [
    'enabled' => true,  // ✅ Enable installation wizard
],
'update' => [
    'enabled' => false, // ❌ Disable update wizard
],
```

**Setup Steps**:
1. **Configure Settings** - Customize `config/launchpad.php` for installation
2. **Set License Validation** - Configure your license server (optional)
3. **Deploy Application** - Upload files WITHOUT running migrations
4. **Share Install URL** - Provide users with `https://yourapp.com/install`

**Installation Flow** - Users complete these steps:

```
https://yourapp.com/install
```

1. **Welcome & Overview** - Introduction to the installation process
2. **Environment Check** - Validates PHP version, extensions, and permissions
3. **License Validation** - Verifies license key (if enabled)
4. **Database Setup** - Tests and configures database connection
5. **Admin Creation** - Creates the administrator account
6. **Success** - Installation completion with admin panel access

### Update Mode (For Existing Installations)

**When to use**: Updating an already installed Laravel application to a newer version.

**Configuration**:
```php
// config/launchpad.php
'installation' => [
    'enabled' => false, // ❌ Disable installation wizard
],
'update' => [
    'enabled' => true,  // ✅ Enable update wizard
    'current_version' => '2.0.0', // Your new version
],
```

**Setup Steps**:
1. **Switch to Update Mode** - Disable installation, enable updates
2. **Upload New Files** - Replace application files with new version
3. **Update Version** - Set `current_version` in config
4. **Share Update URL** - Provide users with `https://yourapp.com/update`

**Update Flow** - Users complete these steps:

```
https://yourapp.com/update
```

1. **Update Overview** - Shows current and target versions
2. **Environment Check** - Ensures environment compatibility
3. **License Verification** - Validates license for updates
4. **Update Process** - Performs file updates, migrations, and optimizations
5. **Success** - Update completion with celebration

### Deployment Workflow Examples

#### Scenario 1: Initial Product Launch
```php
// For first release v1.0.0
'installation' => ['enabled' => true],
'update' => ['enabled' => false],
```

#### Scenario 2: Releasing Update v1.1.0
```php
// For update release
'installation' => ['enabled' => false],
'update' => [
    'enabled' => true,
    'current_version' => '1.1.0',
],
```

#### Scenario 3: Supporting Both (NOT RECOMMENDED)
```php
// ⚠️ DO NOT DO THIS - Will cause conflicts!
'installation' => ['enabled' => true],  // ❌ BAD
'update' => ['enabled' => true],        // ❌ BAD
```

### Environment Variables

Control modes via environment variables:

```env
# For installation mode
LAUNCHPAD_INSTALLATION_ENABLED=true
LAUNCHPAD_UPDATE_ENABLED=false

# For update mode  
LAUNCHPAD_INSTALLATION_ENABLED=false
LAUNCHPAD_UPDATE_ENABLED=true
APP_VERSION=1.1.0
```

### Middleware Protection

The package includes middleware to:

- Redirect to installation if not installed
- Prevent access to installation if already installed
- Protect update routes based on configuration

### Programmatic Usage

You can also interact with the package programmatically:

```php
use SabitAhmad\LaravelLaunchpad\Services\InstallationService;
use SabitAhmad\LaravelLaunchpad\Services\LicenseService;
use SabitAhmad\LaravelLaunchpad\Services\DatabaseService;

// Check if application is installed
$installationService = app(InstallationService::class);
if ($installationService->isInstalled()) {
    // Application is installed
}

// Validate license
$licenseService = app(LicenseService::class);
$isValid = $licenseService->validateLicense('your-license-key');

// Test database connection
$databaseService = app(DatabaseService::class);
$canConnect = $databaseService->testConnection([
    'host' => 'localhost',
    'database' => 'your_db',
    'username' => 'user',
    'password' => 'pass',
]);
```

## 🎨 Customization

### Views

You can customize all views by publishing them:

```bash
php artisan vendor:publish --tag="laravel-launchpad-views"
```

Views are located in:
- `resources/views/vendor/launchpad/install/` - Installation wizard views
- `resources/views/vendor/launchpad/update/` - Update wizard views
- `resources/views/vendor/launchpad/layout.blade.php` - Main layout

### Styling

The package uses Tailwind CSS via CDN. You can:

1. **Override CSS** - Add custom styles to your layout
2. **Replace CDN** - Use your own Tailwind build
3. **Customize Components** - Modify the view files directly

### License Validation

Implement custom license validation:

```php
// In your license server
Route::post('/validate', function (Request $request) {
    $licenseKey = $request->input('license_key');
    
    // Your validation logic
    
    return response()->json([
        'valid' => true,
        'message' => 'License is valid',
        'expires_at' => '2024-12-31',
    ]);
});
```

## 🛡️ Security

### Environment Protection

The package includes several security measures:

- **Installation tracking** prevents re-installation
- **Middleware protection** controls access to wizard routes
- **Environment validation** ensures secure configuration
- **License validation** prevents unauthorized usage

### Best Practices

1. **Remove installation routes** in production after installation
2. **Secure your license server** with proper authentication
3. **Validate user input** in custom fields
4. **Use HTTPS** for license validation requests

## 🔧 Troubleshooting

### Common Configuration Issues

#### ❌ Problem: Both Installation and Update Enabled
```php
// BAD - Will cause route conflicts!
'installation' => ['enabled' => true],
'update' => ['enabled' => true],
```

**Symptoms**:
- Route conflicts and 404 errors
- Users reaching wrong wizard
- Middleware confusion

**Solution**:
```php
// GOOD - Choose ONE mode
'installation' => ['enabled' => true],  // For new installs
'update' => ['enabled' => false],

// OR for updates
'installation' => ['enabled' => false],
'update' => ['enabled' => true],
```

#### ❌ Problem: Both SQL Dump and Migrations Enabled
```php
// BAD - Will cause database conflicts!
'import_options' => [
    'dump_file' => ['enabled' => true],   // ❌ Conflict
    'migrations' => ['enabled' => true], // ❌ Conflict
],
```

**Symptoms**:
- Database import errors
- Duplicate table creation attempts
- Migration failures
- Inconsistent database state

**Solution**:
```php
// GOOD - Choose ONE database method
'import_options' => [
    'dump_file' => ['enabled' => true],   // ✅ Use SQL dump
    'migrations' => ['enabled' => false], // ❌ Disable migrations
],

// OR use migrations
'import_options' => [
    'dump_file' => ['enabled' => false],  // ❌ Disable dump
    'migrations' => ['enabled' => true],  // ✅ Use migrations
],
```

#### ❌ Problem: Wrong Database Method for Application Type

**Scenario**: Using migrations for complex legacy database structures

**Solution**: Choose the right method:
- **Complex/Legacy databases** → SQL dump method
- **Standard Laravel apps** → Migrations method

#### ❌ Problem: Wrong Mode for Deployment Phase

**Scenario**: Enabling installation wizard for an update deployment

**Solution**: Match the wizard to your deployment phase:
- **Fresh deployment** → Installation mode
- **Version update** → Update mode

#### ❌ Problem: Users Can't Access Wizard

**Symptoms**: 404 errors when visiting `/install` or `/update`

**Check**:
1. Correct mode is enabled in config
2. Routes are not cached (`php artisan route:clear`)
3. Web server is configured properly

### Mode Switching Guide

#### Switching from Installation to Update Mode

After successful initial deployment:

```php
// config/launchpad.php - Change from:
'installation' => ['enabled' => true],
'update' => ['enabled' => false],

// To:
'installation' => ['enabled' => false],
'update' => ['enabled' => true, 'current_version' => '1.1.0'],
```

#### Environment Variable Method

```env
# .env - Switch modes without code changes
LAUNCHPAD_INSTALLATION_ENABLED=false
LAUNCHPAD_UPDATE_ENABLED=true
APP_VERSION=1.1.0
```

### Validation Commands

Check your current configuration:

```bash
# Check installation status
php artisan tinker
>>> app(\SabitAhmad\LaravelLaunchpad\Services\InstallationService::class)->isInstalled()

# Verify configuration
>>> config('launchpad.installation.enabled')
>>> config('launchpad.update.enabled')

# Check database configuration
>>> config('launchpad.database.import_options.dump_file.enabled')
>>> config('launchpad.database.import_options.migrations.enabled')

# Verify both dump and migrations are not enabled
>>> $dump = config('launchpad.database.import_options.dump_file.enabled');
>>> $migrations = config('launchpad.database.import_options.migrations.enabled');
>>> if ($dump && $migrations) echo "⚠️ CONFLICT: Both dump and migrations enabled!";

# List available routes
php artisan route:list | grep launchpad
```

### Database Configuration Validation

**Quick Configuration Check Script**:

```php
// Create a simple validation script: check-config.php
<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check installation vs update conflict
$installEnabled = config('launchpad.installation.enabled');
$updateEnabled = config('launchpad.update.enabled');

if ($installEnabled && $updateEnabled) {
    echo "❌ CONFLICT: Both installation and update are enabled!\n";
} else {
    echo "✅ Installation/Update configuration is valid\n";
}

// Check database method conflict
$dumpEnabled = config('launchpad.database.import_options.dump_file.enabled');
$migrationsEnabled = config('launchpad.database.import_options.migrations.enabled');

if ($dumpEnabled && $migrationsEnabled) {
    echo "❌ CONFLICT: Both SQL dump and migrations are enabled!\n";
    echo "   Choose one: either dump_file OR migrations\n";
} else {
    echo "✅ Database configuration is valid\n";
    if ($dumpEnabled) {
        $dumpPath = config('launchpad.database.import_options.dump_file.path');
        if (file_exists($dumpPath)) {
            echo "✅ SQL dump file found: {$dumpPath}\n";
        } else {
            echo "⚠️  SQL dump file not found: {$dumpPath}\n";
        }
    }
    if ($migrationsEnabled) {
        echo "✅ Using Laravel migrations\n";
    }
}
```

**Run the validation**:
```bash
php check-config.php
```

## 🧪 Testing

Run the package tests:

```bash
composer test
```

Run tests with coverage:

```bash
composer test:coverage
```

Run static analysis:

```bash
composer analyse
```

## 📝 Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## 🤝 Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## 🔒 Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## 👨‍💻 Credits

- [Sabit Ahmad](https://github.com/sabitahmadumid)
- [All Contributors](../../contributors)

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

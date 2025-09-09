# 🚀 Laravel Launchpad

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sabitahmadumid/laravel-launchpad.svg?style=flat-square)](https://packagist.org/packages/sabitahmadumid/laravel-launchpad)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/sabitahmadumid/laravel-launchpad/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/sabitahmadumid/laravel-launchpad/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/sabitahmadumid/laravel-launchpad/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/sabitahmadumid/laravel-launchpad/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/sabitahmadumid/laravel-launchpad.svg?style=flat-square)](https://packagist.org/packages/sabitahmadumid/laravel-launchpad)

**Laravel Launchpad** is a comprehensive installation and update wizard package that makes it incredibly easy for developers to ship their Laravel applications and for end-users to install them. With professional UI components, license validation, environment checking, and a streamlined automatic process, Launchpad transforms complex deployments into simple, guided experiences.

Perfect for SaaS applications, commercial Laravel products, or any Laravel application that needs professional installation and update capabilities.

## ✨ Features

- **🎯 Automatic Installation Wizard** - 5-step guided installation process with automatic configuration-based setup
- **🔄 Automatic Update Wizard** - 5-step guided update process with automatic version upgrades  
- **⚠️ Mutually Exclusive Modes** - Installation and update wizards are designed to run independently (never simultaneously)
- **🤖 Configuration-Driven Flow** - No user choices required - all operations automatic based on configuration
- **🔒 Auto-Security** - Installation/update routes automatically disabled after successful completion
- **🛡️ License Validation** - Flexible license verification system with external server support
- **⚙️ Environment Checking** - PHP version, extensions, and directory permissions validation
- **🎨 Professional UI** - Modern, responsive interface built with Tailwind CSS and Alpine.js
- **🔧 Self-Contained Configuration** - All settings managed in config files, minimal environment dependencies
- **🏗️ Clean Architecture** - Service-oriented design with proper separation of concerns
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

The package comes with a comprehensive configuration file located at `config/launchpad.php`. **All Launchpad settings are managed directly in the configuration file** (no environment file dependencies except for `app_name`), providing better version control and easier deployment management.

### Basic Settings

```php
return [
    'installation' => [
        'enabled' => false, // Set to true for new installations
        'route_prefix' => 'install',
        'route_middleware' => ['web'],
        'completed_file' => storage_path('app/installed.lock'),
    ],
    
    'update' => [
        'enabled' => false, // Set to true for updates
        'route_prefix' => 'update',
        'route_middleware' => ['web'],
        'version_file' => storage_path('app/version.lock'),
        'current_version' => '1.0.0', // Update this for new versions
    ],
    
    'license' => [
        'enabled' => true,
        'validator_class' => 'App\\Services\\EnvatoLicenseChecker',
        'server_url' => null, // Set your license server URL
        'timeout' => 30,
    ],
    
    'ui' => [
        'app_name' => env('APP_NAME', 'Laravel Application'), // Only env dependency
        'logo_url' => null, // Optional logo URL
        'primary_color' => '#3B82F6',
    ],
    
    'requirements' => [
        'php' => [
            'min_version' => '8.0.0',
            'recommended_version' => '8.2.0',
        ],
        'extensions' => [
            'required' => [
                'openssl', 'pdo', 'mbstring', 'tokenizer', 'xml',
                'ctype', 'json', 'bcmath', 'curl', 'fileinfo', 'zip',
            ],
            'recommended' => ['gd', 'imagick', 'redis', 'memcached'],
        ],
        'directories' => [
            'writable' => [
                'storage', 'storage/app', 'storage/framework',
                'storage/logs', 'bootstrap/cache',
            ],
        ],
    ],
    
    'importOptions' => [
        // Database setup configuration (choose one method)
        'dump_file' => [
            'enabled' => true,  // ✅ Use SQL dump import (recommended for complex apps)
            'path' => database_path('dump.sql'),
            'description' => 'Import initial database dump',
        ],
        'migrations' => [
            'enabled' => false, // ❌ Disable when using dump (mutually exclusive)
            'description' => 'Run database migrations',
        ],
        'seeders' => [
            'enabled' => true,  // ✅ Optional: Can be used with either method
            'description' => 'Run database seeders',
        ],
    ],
    
    'update_options' => [
        // Update process configuration (choose one method)
        'dump_file' => [
            'enabled' => true,
            'path' => database_path('updates/update.sql'),
            'description' => 'Import update database dump',
        ],
        'migrations' => [
            'enabled' => false, // ❌ Disable when using dump 
            'description' => 'Run update migrations',
        ],
        'cache_clear' => true,  // Automatically clear cache after updates
        'config_cache' => true, // Automatically cache config after updates
    ],
    
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
        ],
    ],
    
    'post_install' => [
        'redirect_url' => '/admin',
        'actions' => [
            'generate_app_key' => true,
            'optimize_clear' => true,
            'config_cache' => true,
            'route_cache' => false,
            'view_cache' => false,
        ],
    ],
];
```

### 🔑 Key Configuration Changes

**Self-Contained Configuration**: All Launchpad-specific settings are stored directly in the config file rather than environment variables, making deployment and version control easier.

**Environment Dependencies Removed**: 
- `installation.enabled` - Set directly in config (no `LAUNCHPAD_INSTALLATION_ENABLED`)
- `update.enabled` - Set directly in config (no `LAUNCHPAD_UPDATE_ENABLED`)  
- `update.current_version` - Set directly in config (no `APP_VERSION` dependency)
- `license.enabled` - Set directly in config (no `LAUNCHPAD_LICENSE_ENABLED`)
- All other Launchpad settings use direct values

**Only APP_NAME Uses Environment**: 
```php
'app_name' => env('APP_NAME', 'Laravel Application'), // Only env dependency kept
```

### Automatic Operation Flow

**All operations are now automatic based on configuration** - users don't make choices during installation/update:

- **Database Setup**: Automatically runs all enabled options from `importOptions`
- **Update Process**: Automatically runs all enabled options from `update_options`  
- **Route Security**: Installation/update routes automatically disabled after completion
- **No User Checkboxes**: Configuration determines what operations run

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

### 🤖 Automatic Operation Flow

**All installation and update operations are now completely automatic** - no user choices or checkboxes! The system automatically:

- ✅ **Runs all enabled operations** from configuration
- ✅ **Disables routes after completion** for security
- ✅ **Updates config files directly** (no env dependencies)
- ✅ **Provides clear progress feedback** to users
- ✅ **Handles errors gracefully** with proper messaging

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

// Configure automatic database setup
'importOptions' => [
    'dump_file' => ['enabled' => true, 'path' => database_path('dump.sql')],
    'migrations' => ['enabled' => false], // Choose one method
    'seeders' => ['enabled' => true],
],
```

**Setup Steps**:
1. **Configure Settings** - Set operations in `config/launchpad.php`
2. **Set License Validation** - Configure your license server (optional)
3. **Deploy Application** - Upload files WITHOUT running migrations
4. **Share Install URL** - Provide users with `https://yourapp.com/install`

**Installation Flow** - Users experience this automatic process:

```
https://yourapp.com/install
```

1. **Welcome & Overview** - Introduction to the automatic installation process
2. **Environment Check** - Validates PHP version, extensions, and permissions
3. **License Validation** - Verifies license key (if enabled)
4. **Database Setup** - **AUTOMATIC**: Runs all configured database operations
5. **Admin Creation** - Creates the administrator account
6. **Success** - Installation completion + **routes automatically disabled**

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

// Configure automatic update operations
'update_options' => [
    'dump_file' => ['enabled' => true, 'path' => database_path('updates/update.sql')],
    'migrations' => ['enabled' => false], // Choose one method
    'cache_clear' => true,
    'config_cache' => true,
],
```

**Setup Steps**:
1. **Switch to Update Mode** - Disable installation, enable updates
2. **Upload New Files** - Replace application files with new version
3. **Update Version** - Set `current_version` in config
4. **Share Update URL** - Provide users with `https://yourapp.com/update`

**Update Flow** - Users experience this automatic process:

```
https://yourapp.com/update
```

1. **Update Overview** - Shows current and target versions
2. **Environment Check** - Ensures environment compatibility
3. **License Verification** - Validates license for updates
4. **Update Process** - **AUTOMATIC**: Runs all configured update operations
5. **Success** - Update completion + **routes automatically disabled**

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

### Configuration Management

**Self-Contained Approach**: All settings are managed directly in the config file for better version control and deployment:

```php
// config/launchpad.php - Direct configuration management
'installation' => ['enabled' => true],  // No env dependency
'update' => ['enabled' => false],       // No env dependency
'license' => ['enabled' => true],       // No env dependency
'update' => ['current_version' => '1.1.0'], // No env dependency
```

**Only APP_NAME uses environment**:
```php
'ui' => [
    'app_name' => env('APP_NAME', 'Laravel Application'), // Only env dependency
],
```

**Automatic Route Security**: After successful installation/update completion:
- Installation routes automatically disabled (`installation.enabled` → `false`)
- Update routes automatically disabled (`update.enabled` → `false`)
- Config file updated and cached automatically
- No manual intervention required

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
'importOptions' => [
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
'importOptions' => [
    'dump_file' => ['enabled' => true],   // ✅ Use SQL dump
    'migrations' => ['enabled' => false], // ❌ Disable migrations
],

// OR use migrations
'importOptions' => [
    'dump_file' => ['enabled' => false],  // ❌ Disable dump
    'migrations' => ['enabled' => true],  // ✅ Use migrations
],
```

#### ❌ Problem: Routes Not Automatically Disabled

**Symptoms**: Installation/update routes still accessible after completion

**Check**:
1. Config file permissions are writable
2. Config cache has been cleared
3. No syntax errors in config file

**Solution**:
```bash
# Manually disable if automatic didn't work
php artisan config:clear
# Then edit config/launchpad.php manually:
'installation' => ['enabled' => false],
'update' => ['enabled' => false],
```

#### ❌ Problem: Config Changes Not Taking Effect

**Symptoms**: Old configuration still being used

**Solution**:
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
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

After successful initial deployment, update your configuration:

```php
// config/launchpad.php - Change from:
'installation' => ['enabled' => true],
'update' => ['enabled' => false],

// To:
'installation' => ['enabled' => false],
'update' => ['enabled' => true, 'current_version' => '1.1.0'],
```

**Note**: Routes are automatically disabled after successful completion, so manual switching is primarily for new deployments.

#### Deployment Workflow

**For New Version Releases**:
1. Update `config/launchpad.php` in your codebase
2. Set `update.enabled = true` and `installation.enabled = false`  
3. Update `update.current_version` to new version
4. Deploy files to server
5. Share update URL with users
6. Routes automatically disable after successful updates

**Configuration Template for Updates**:
```php
// config/launchpad.php - Update release template
'installation' => ['enabled' => false], // Always false for updates
'update' => [
    'enabled' => true,
    'current_version' => '2.0.0', // ← Update this
],
'update_options' => [
    'dump_file' => ['enabled' => true], // Your update method
    'migrations' => ['enabled' => false],
    'cache_clear' => true,
    'config_cache' => true,
],
```

### Validation Commands

Check your current configuration:

```bash
# Check installation status
php artisan tinker
>>> app(\SabitAhmad\LaravelLaunchpad\Services\InstallationService::class)->isInstalled()

# Verify configuration (new config-based approach)
>>> config('launchpad.installation.enabled')
>>> config('launchpad.update.enabled')

# Check database configuration
>>> config('launchpad.importOptions.dump_file.enabled')
>>> config('launchpad.importOptions.migrations.enabled')
>>> config('launchpad.update_options.dump_file.enabled')
>>> config('launchpad.update_options.migrations.enabled')

# Verify both dump and migrations are not enabled (mutual exclusion)
>>> $dumpInstall = config('launchpad.importOptions.dump_file.enabled');
>>> $migrationsInstall = config('launchpad.importOptions.migrations.enabled');
>>> if ($dumpInstall && $migrationsInstall) echo "⚠️ CONFLICT: Both dump and migrations enabled for installation!";

>>> $dumpUpdate = config('launchpad.update_options.dump_file.enabled');
>>> $migrationsUpdate = config('launchpad.update_options.migrations.enabled');
>>> if ($dumpUpdate && $migrationsUpdate) echo "⚠️ CONFLICT: Both dump and migrations enabled for updates!";

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
    if ($installEnabled) echo "   → Installation mode active\n";
    if ($updateEnabled) echo "   → Update mode active\n";
}

// Check database method conflicts for installation
$dumpEnabled = config('launchpad.importOptions.dump_file.enabled');
$migrationsEnabled = config('launchpad.importOptions.migrations.enabled');

if ($dumpEnabled && $migrationsEnabled) {
    echo "❌ CONFLICT: Both SQL dump and migrations enabled for installation!\n";
    echo "   Choose one: either dump_file OR migrations in importOptions\n";
} else {
    echo "✅ Installation database configuration is valid\n";
    if ($dumpEnabled) {
        $dumpPath = config('launchpad.importOptions.dump_file.path');
        if (file_exists($dumpPath)) {
            echo "✅ Installation SQL dump file found: {$dumpPath}\n";
        } else {
            echo "⚠️  Installation SQL dump file not found: {$dumpPath}\n";
        }
    }
    if ($migrationsEnabled) {
        echo "✅ Using Laravel migrations for installation\n";
    }
}

// Check database method conflicts for updates
$updateDumpEnabled = config('launchpad.update_options.dump_file.enabled');
$updateMigrationsEnabled = config('launchpad.update_options.migrations.enabled');

if ($updateDumpEnabled && $updateMigrationsEnabled) {
    echo "❌ CONFLICT: Both SQL dump and migrations enabled for updates!\n";
    echo "   Choose one: either dump_file OR migrations in update_options\n";
} else {
    echo "✅ Update database configuration is valid\n";
    if ($updateDumpEnabled) {
        $updateDumpPath = config('launchpad.update_options.dump_file.path');
        if (file_exists($updateDumpPath)) {
            echo "✅ Update SQL dump file found: {$updateDumpPath}\n";
        } else {
            echo "⚠️  Update SQL dump file not found: {$updateDumpPath}\n";
        }
    }
    if ($updateMigrationsEnabled) {
        echo "✅ Using Laravel migrations for updates\n";
    }
}

// Check automatic security
echo "\n🔒 Automatic Security Features:\n";
echo "   → Routes automatically disabled after successful completion\n";
echo "   → Config file updated directly (no env dependencies)\n";
echo "   → All operations automatic based on configuration\n";
```

**Run the validation**:
```bash
php check-config.php
```

## 🎉 Version 2.0 Key Improvements

### 🤖 Fully Automatic Operation Flow

**Before**: Users had to choose what database operations to run via checkboxes
**Now**: All operations are automatic based on configuration - zero user decisions required

- ✅ **No User Checkboxes**: Configuration determines all operations
- ✅ **Streamlined Flow**: "Start Automatic Installation/Update" buttons
- ✅ **Consistent Experience**: Same flow every time based on your config
- ✅ **Error-Free**: No risk of users choosing wrong options

### 🔒 Enhanced Security & Management

**Before**: Routes remained active after installation/update completion
**Now**: Automatic route disabling and config-based management

- ✅ **Auto-Disable Routes**: Installation/update routes disabled after completion
- ✅ **Config File Management**: Direct config file updates instead of env dependencies
- ✅ **Self-Contained**: All settings in version-controlled config files
- ✅ **Runtime Updates**: Both file and memory config updated simultaneously

### ⚙️ Simplified Configuration

**Before**: Heavy reliance on environment variables
**Now**: Direct config file management with minimal env dependencies

- ✅ **Single Source of Truth**: All settings in `config/launchpad.php`
- ✅ **Version Control Friendly**: Config changes tracked in git
- ✅ **Deployment Friendly**: No env file manipulation required
- ✅ **Only APP_NAME env dependency**: Everything else config-based

### 🚀 Developer Experience Improvements

- ✅ **Automatic Mode Switching**: Routes disable themselves after completion
- ✅ **Clear Progress Indicators**: Users see exactly what's happening
- ✅ **Better Error Handling**: Graceful failures with helpful messages  
- ✅ **Simpler Deployment**: Update config, deploy files, share URL

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

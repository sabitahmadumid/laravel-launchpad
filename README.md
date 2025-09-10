# 🚀 Laravel Launchpad

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sabitahmadumid/laravel-launchpad.svg?style=flat-square)](https://packagist.org/packages/sabitahmadumid/laravel-launchpad)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/sabitahmadumid/laravel-launchpad/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/sabitahmadumid/laravel-launchpad/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/sabitahmadumid/laravel-launchpad/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https:/### 🛡️ Security Features

- **Automatic Environment Storage**: License keys automatically saved to `.env` file during verification
- **Encrypted Local Backup**: Secondary encrypted storage with restricted permissions
- **BYPASS PROTECTION**: **Cannot be disabled via config in production** - License validation is **ALWAYS** required in production environments
- **Multi-Layer Environment Detection**: Uses multiple methods to detect environment and prevent tampering
- **Localhost Verification**: Additional checks to ensure local development is genuine
- **Encrypted Local Flags**: Local enforcement managed through encrypted storage, not config files
- **Grace Period**: Temporary failures don't immediately block access
- **Retry Mechanism**: Automatic retry with exponential backoff for network issues.com/sabitahmadumid/laravel-launchpad/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/sabitahmadumid/laravel-launchpad.svg?style=flat-square)](https://packagist.org/packages/sabitahmadumid/laravel-launchpad)

**Laravel Launchpad** is a comprehensive installation and update wizard package that makes it incredibly easy for developers to ship their Laravel applications and for end-users to install them. With professional UI components, license validation, environment checking, and a streamlined automatic process, Launchpad transforms complex deployments into simple, guided experiences.

Perfect for SaaS applications, commercial Laravel products, or any Laravel application that needs professional installation and update capabilities.

## 📸 Screenshots

<div align="center">

### Installation Wizard
![Laravel Launchpad Installation Wizard](https://raw.githubusercontent.com/sabitahmadumid/laravel-launchpad/main/.github/screenshots/install.png)

### Update Wizard
![Laravel Launchpad Update Wizard](https://raw.githubusercontent.com/sabitahmadumid/laravel-launchpad/main/.github/screenshots/update.png)

</div>

> **📝 Note**: Screenshots show the default Tailwind CSS styling. All views are fully customizable through Blade templates.

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

The package comes with a comprehensive configuration file located at `config/launchpad.php`. Most settings can be controlled via environment variables for better security and deployment management.

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
        // License requirement is primarily controlled by environment variables
        'enabled' => env('LAUNCHPAD_LICENSE_ENABLED', true),
        'enforce_local' => env('LAUNCHPAD_ENFORCE_LOCAL', false),
        'validator_class' => env('LAUNCHPAD_VALIDATOR_CLASS', 'App\\Services\\EnvatoLicenseChecker'),
        'server_url' => env('LAUNCHPAD_LICENSE_SERVER'),
        'timeout' => env('LAUNCHPAD_LICENSE_TIMEOUT', 30),
        'cache_duration' => env('LAUNCHPAD_LICENSE_CACHE', 3600),
        'retry_attempts' => 3,
        'grace_period' => 24, // hours
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

The package includes several security measures:

- **Installation tracking** prevents re-installation
- **Middleware protection** controls access to wizard routes
- **Environment validation** ensures secure configuration
- **License validation** prevents unauthorized usage
- **Routes automatically disabled** after successful completion

## 🔧 Troubleshooting

### Common Issues

#### Both Installation and Update Enabled
```php
// ❌ BAD - Will cause route conflicts!
'installation' => ['enabled' => true],
'update' => ['enabled' => true],

// ✅ GOOD - Choose ONE mode
'installation' => ['enabled' => true],  // For new installs
'update' => ['enabled' => false],
```

#### Both SQL Dump and Migrations Enabled
```php
// ❌ BAD - Will cause database conflicts!
'importOptions' => [
    'dump_file' => ['enabled' => true],
    'migrations' => ['enabled' => true], // ❌ Conflict
],

// ✅ GOOD - Choose ONE database method
'importOptions' => [
    'dump_file' => ['enabled' => true],
    'migrations' => ['enabled' => false],
],
```

#### Routes Not Automatically Disabled
If routes remain accessible after completion:
```bash
php artisan config:clear
```

#### Config Changes Not Taking Effect
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

## 🎉 Version 2.0 Key Improvements

### 🤖 Fully Automatic Operation Flow
- **No User Checkboxes**: Configuration determines all operations
- **Streamlined Flow**: "Start Automatic Installation/Update" buttons
- **Consistent Experience**: Same flow every time based on your config
- **Error-Free**: No risk of users choosing wrong options

### 🔒 Enhanced Security & Management
- **Auto-Disable Routes**: Installation/update routes disabled after completion
- **Config File Management**: Direct config file updates instead of env dependencies
- **Self-Contained**: All settings in version-controlled config files
- **Runtime Updates**: Both file and memory config updated simultaneously

### ⚙️ Simplified Configuration
- **Single Source of Truth**: All settings in `config/launchpad.php`
- **Version Control Friendly**: Config changes tracked in git
- **Deployment Friendly**: No env file manipulation required
- **Only APP_NAME env dependency**: Everything else config-based

### 🚀 Developer Experience Improvements
- **Automatic Mode Switching**: Routes disable themselves after completion
- **Clear Progress Indicators**: Users see exactly what's happening
- **Better Error Handling**: Graceful failures with helpful messages  
- **Simpler Deployment**: Update config, deploy files, share URL

## 🔐 Enhanced License System

Laravel Launchpad includes a robust license validation system that **automatically** handles license key storage during the installation and update process. Users simply enter their license key during setup, and the system handles everything automatically.

### � How It Works

#### During Installation/Update Flow
1. **User enters license key** in the installation or update wizard
2. **System validates** the license with your license server
3. **Automatic storage** - If valid, the license key is automatically saved to the project's `.env` file
4. **Future verification** - The `isLicenseVerified()` method automatically checks the stored license

#### For Developers (Simple API)
```php
use SabitAhmad\LaravelLaunchpad\Services\LicenseService;

$licenseService = app(LicenseService::class);

// Simple boolean check - handles everything automatically
if ($licenseService->isLicenseVerified()) {
    // License is valid, proceed with functionality
    return view('premium-feature');
} else {
    // License is invalid or missing
    return redirect()->route('license.required');
}
```

### 🛡️ Security Features

- **Automatic Environment Storage**: License keys automatically saved to `.env` file during verification
- **Encrypted Local Backup**: Secondary encrypted storage with restricted permissions
- **Bypass Protection**: Cannot be easily disabled via config manipulation in production
- **Grace Period**: Temporary failures don't immediately block access
- **Retry Mechanism**: Automatic retry with exponential backoff for network issues

### ⚙️ Optional Environment Configuration

While license keys are automatically managed, you can optionally configure these settings in your `.env` file:

```bash
# Optional: License server URL (if using remote validation)
LAUNCHPAD_LICENSE_SERVER=https://your-license-server.com/api/validate

# Optional: Custom license validator class
LAUNCHPAD_VALIDATOR_CLASS=App\\Services\\CustomLicenseValidator

# Optional: Request timeout and cache duration
LAUNCHPAD_LICENSE_TIMEOUT=30
LAUNCHPAD_LICENSE_CACHE=3600
```

**Note**: The `LAUNCHPAD_LICENSE_KEY` is automatically added to your `.env` file when users verify their license during installation or update.

**IMPORTANT SECURITY NOTICE**: License validation **cannot be disabled via configuration** in production environments. This is by design to prevent easy bypassing.

### 🎛️ Command Line Management

Laravel Launchpad includes a powerful command-line interface for license management:

#### Check License Status
```bash
php artisan launchpad:license status
```

#### Manually Verify License Key
```bash
# Interactive mode (will automatically save to .env if valid)
php artisan launchpad:license verify

# With key parameter (will automatically save to .env if valid)
php artisan launchpad:license verify --key=your-license-key
```

#### Remove Stored License
```bash
# With confirmation
php artisan launchpad:license remove

# Force removal without confirmation
php artisan launchpad:license remove --force
```

#### Clear License Cache
```bash
php artisan launchpad:license clear-cache
```

#### Local Development License Management
```bash
# Enable license enforcement in local environment
php artisan launchpad:license enable-local

# Disable license enforcement in local environment  
php artisan launchpad:license disable-local
```

**Note**: Local enforcement commands only work in local development environment and use encrypted flags for security.

### 📊 Detailed License Information

For more detailed license information in your application:

```php
$licenseService = app(LicenseService::class);

// Get comprehensive license status
$status = $licenseService->getLicenseStatus();
/*
Returns array:
[
    'has_license' => true,
    'is_valid' => true,
    'source' => 'environment', // 'environment' or 'storage'
    'message' => 'License is valid'
]
*/

// Check if license validation is required
if ($licenseService->isLicenseRequired()) {
    // License validation is enabled
}
```

### 🔧 Custom License Validator

Create your own license validator by implementing the `LicenseValidatorInterface`:

```php
<?php

namespace App\Services;

use SabitAhmad\LaravelLaunchpad\Contracts\LicenseValidatorInterface;

class CustomLicenseValidator implements LicenseValidatorInterface
{
    public function validate(string $licenseKey, array $additionalData = []): array
    {
        // Your custom validation logic here
        $isValid = $this->performCustomValidation($licenseKey, $additionalData);
        
        return [
            'valid' => $isValid,
            'message' => $isValid ? 'License is valid' : 'License validation failed',
            'data' => [], // Additional data if needed
        ];
    }
    
    private function performCustomValidation(string $licenseKey, array $data): bool
    {
        // Implement your license validation logic
        // Could be API calls, database checks, file validation, etc.
        return true; // or false
    }
}
```

Then register it in your `.env` file:
```bash
LAUNCHPAD_VALIDATOR_CLASS=App\\Services\\CustomLicenseValidator
```

### � End User Experience

#### Installation Process
1. User visits `/install` route
2. Goes through requirements check
3. **Enters license key** in the license verification step
4. System automatically validates and stores the license key
5. Continues with database setup and admin creation
6. Installation complete - license key is ready for use

#### Update Process
1. User visits `/update` route
2. Goes through requirements check
3. **Enters license key** in the license verification step (if not already stored)
4. System automatically validates and stores/updates the license key
5. Continues with update process
6. Update complete - license key is ready for use

### �🛠️ Troubleshooting

#### License Not Found After Installation
```bash
# Check current status
php artisan launchpad:license status

# If needed, manually verify (will auto-save to .env)
php artisan launchpad:license verify
```

#### Validation Failures
```bash
# Clear cache and retry
php artisan launchpad:license clear-cache

# Check if license exists in environment
php artisan launchpad:license status
```

#### Permission Issues
```bash
# Fix storage permissions if needed
chmod 600 storage/app/.license

# Check .env file permissions
ls -la .env
```

### 🔄 Migration from Old System

If you're upgrading from an older version where users manually added license keys:

**Before (Manual)**:
```bash
# Users had to manually add to .env
LAUNCHPAD_LICENSE_KEY=manually-added-key
```

**After (Automatic)**:
- Users enter license key during installation/update flow
- System automatically adds `LAUNCHPAD_LICENSE_KEY=their-key` to `.env`
- Developers use simple `isLicenseVerified()` method

### 🏢 Production Best Practices

1. **Use the automatic flow** - Let users enter license keys during installation/update
2. **Monitor license validation** - Set up alerts for validation failures
3. **Use HTTPS for license server** - Ensure encrypted communication
4. **Regular backups** - Include `.env` file in backups
5. **Grace period configuration** - Allow temporary server outages
6. **Log license events** - Track validation attempts and failures

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

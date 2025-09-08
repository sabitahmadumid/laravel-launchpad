# 🚀 Laravel Launchpad

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sabitahmadumid/laravel-launchpad.svg?style=flat-square)](https://packagist.org/packages/sabitahmadumid/laravel-launchpad)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/sabitahmadumid/laravel-launchpad/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/sabitahmadumid/laravel-launchpad/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/sabitahmadumid/laravel-launchpad/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/sabitahmadumid/laravel-launchpad/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/sabitahmadumid/laravel-launchpad.svg?style=flat-square)](https://packagist.org/packages/sabitahmadumid/laravel-launchpad)

**Laravel Launchpad** is a comprehensive installation and update wizard package that makes it incredibly easy for developers to ship their Laravel applications and for end-users to install them. With professional UI components, license validation, environment checking, and a streamlined 3-step process, Launchpad transforms complex deployments into simple, guided experiences.

Perfect for SaaS applications, commercial Laravel products, or any Laravel application that needs professional installation and update capabilities.

## ✨ Features

- **🎯 3-Step Installation Process** - Simplified wizard with environment checks, license validation, and database setup
- **🔄 Seamless Update System** - Safe and guided update process with progress tracking
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

## 🎯 Usage

### Setting Up Installation

1. **Add Routes** - The package automatically registers routes when not installed
2. **Configure Settings** - Customize `config/launchpad.php` to match your needs
3. **Set Up License Validation** - Configure your license server endpoint (optional)

### Installation Flow

Once configured, users can install your application by visiting:

```
https://yourapp.com/install
```

The installation process includes:

1. **Welcome & Overview** - Introduction to the installation process
2. **Environment Check** - Validates PHP version, extensions, and permissions
3. **License Validation** - Verifies license key (if enabled)
4. **Database Setup** - Tests and configures database connection
5. **Admin Creation** - Creates the administrator account
6. **Success** - Installation completion with admin panel access

### Update Flow

For application updates, users can visit:

```
https://yourapp.com/update
```

The update process includes:

1. **Update Overview** - Shows current and target versions
2. **Environment Check** - Ensures environment compatibility
3. **License Verification** - Validates license for updates
4. **Update Process** - Performs file updates, migrations, and optimizations
5. **Success** - Update completion with celebration

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

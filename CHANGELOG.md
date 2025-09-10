# Changelog

All notable changes to `laravel-launchpad` will be documented in this file.

## [2.2.0] - 2025-09-11

### 🔒 Ultra-Secure License System - Major Security Overhaul

This release implements an enterprise-grade license validation system that is virtually impossible to bypass for normal users while maintaining developer-friendly automation.

### ✨ New Features

- **🛡️ Bypass-Proof License System**
  - **ZERO config-based bypasses** allowed in production environments
  - License validation **ALWAYS required** in production
  - Multi-layer environment detection prevents tampering
  - Secure localhost verification for genuine local development

- **🔐 Automatic License Management**
  - License keys **automatically saved to .env** during installation/update flow
  - Users simply enter license key - system handles everything automatically  
  - Enhanced installation/update controllers with automatic license storage
  - Encrypted local storage fallback with restricted file permissions

- **🎛️ Advanced Command Line Interface**
  - `php artisan launchpad:license status` - Comprehensive license status
  - `php artisan launchpad:license verify` - Auto-save verified licenses to .env
  - `php artisan launchpad:license enable-local` - Encrypted local enforcement
  - `php artisan launchpad:license disable-local` - Secure local development mode
  - `php artisan launchpad:license remove` - Clean license removal
  - `php artisan launchpad:license clear-cache` - Cache management

- **🔧 Encrypted Local Development Control**
  - Local enforcement managed through encrypted flags only
  - No config-based bypasses in any environment
  - Secure command-based local development management
  - File permissions automatically set to 600 (owner-only)

### 🔄 Changed (Breaking Changes)

- **License Validation (Breaking Changes)**
  - `BREAKING:` Removed `LAUNCHPAD_DISABLE_LICENSE` environment variable
  - `BREAKING:` Removed `config('launchpad.license.enabled')` bypass option
  - `BREAKING:` Removed `enforce_local` config-based setting
  - `BREAKING:` License validation **cannot be disabled via config** in production
  - `BREAKING:` Local development control requires encrypted flags only

- **Enhanced API Methods**
  - `validateLicense()` now automatically saves valid licenses to .env file
  - Enhanced response with `env_updated` status indicator
  - Improved error handling with fallback to encrypted local storage

### 🛡️ Security Improvements

- **Multi-Layer Environment Detection**
  - Uses 3 different methods to detect environment (env, config, app instance)
  - If any method reports "production", treats as production
  - Inconsistent detection defaults to production (most secure)

- **Localhost Verification System**
  - Verifies actual localhost addresses (127.0.0.1, localhost, ::1)
  - Checks for development environment indicators (vendor/, composer.json)
  - Must pass ALL checks to qualify as local development

- **Encrypted Flag Management**
  - Local enforcement stored in encrypted `storage/app/.license_enforce` file
  - Uses Laravel's encryption system (not easily tamperable)
  - Automatic file permission management (600)

### 📚 Documentation Updates

- **Security-Focused Documentation**
  - Emphasized bypass protection throughout README
  - Removed misleading environment variable options
  - Added comprehensive security notices
  - Updated examples to reflect automatic workflow

- **Command Line Documentation**
  - Complete command reference with security examples
  - Local development management instructions
  - Production deployment best practices

### 🏗️ Technical Implementation

- **LicenseService Enhancements**
  - `updateEnvFile()` method for automatic .env writing (follows DatabaseService pattern)
  - `getSecureEnvironment()` with multi-method validation
  - `isLocalDevelopment()` with comprehensive local verification
  - `enableLocalEnforcement()` / `disableLocalEnforcement()` for encrypted flag management

- **Controller Improvements**
  - InstallationController enhanced with automatic license storage
  - UpdateController enhanced with automatic license storage  
  - Both return `env_updated` status in API responses

- **DefaultLicenseValidator Security**
  - Removed ALL bypass mechanisms from validator
  - License requirement checking moved to LicenseService only
  - Clean separation of concerns

### 🚀 User Experience

- **Installation Flow**: Users enter license → System validates & saves to .env → Installation continues
- **Update Flow**: Users enter license → System validates & saves to .env → Update continues  
- **Development**: Simple commands for local enforcement management
- **Production**: Zero bypass options - always secure

## [2.0.0] - 2025-09-09

### 🚀 Major Version Release - Breaking Changes

This release represents a complete overhaul of the installation and update experience, focusing on automation, security, and developer experience.

### ✨ New Features

- **🤖 Fully Automatic Operation Flow**
  - Removed all user checkboxes and manual decisions
  - Configuration-driven operations based on config settings
  - Streamlined "Start Automatic Installation/Update" workflow
  - Zero user choices required - everything automatic

- **🔒 Enhanced Security & Auto-Management**
  - Installation routes automatically disabled after successful completion
  - Update routes automatically disabled after successful completion
  - Config file-based route management (no manual intervention required)
  - Self-securing installation process

- **⚙️ Self-Contained Configuration Management**
  - Removed environment file dependencies (except APP_NAME)
  - All Launchpad settings managed directly in config/launchpad.php
  - Direct config file updates instead of .env modifications
  - Better version control and deployment management
  - Runtime config updates using Config::set()

- **🎨 Streamlined User Experience**
  - Updated UI messaging for automatic flow
  - Enhanced progress indicators with clear status
  - Professional completion screens with security notifications
  - Consistent experience across all installations

### 🔄 Changed (Breaking Changes)

- **Database Setup Flow**
  - `BREAKING:` Removed user database setup checkboxes
  - `BREAKING:` Operations now automatic based on `importOptions` config
  - `BREAKING:` Config structure updated from `import_options` to `importOptions`

- **Update Process Flow**
  - `BREAKING:` Removed user update option checkboxes  
  - `BREAKING:` Operations now automatic based on `update_options` config
  - `BREAKING:` Routes automatically disabled after completion

- **Configuration Structure**
  - `BREAKING:` Removed env dependencies: `LAUNCHPAD_INSTALLATION_ENABLED`, `LAUNCHPAD_UPDATE_ENABLED`, `APP_VERSION`
  - `BREAKING:` Direct config values instead of `env()` calls
  - `BREAKING:` Only `APP_NAME` still uses environment variable

- **Route Management**
  - `BREAKING:` Routes automatically disabled (no manual configuration needed)
  - `BREAKING:` Config-based route control instead of middleware-only

### 🛠️ Technical Improvements

- Added Config facade to controllers for runtime updates
- Enhanced error handling and logging
- Regex-based config file modification system
- Improved service architecture
- Better separation of concerns

### 📝 Documentation

- Complete README.md overhaul with v2.0 focus
- New configuration examples and guides
- Updated troubleshooting documentation
- Added validation scripts for new config structure
- Migration guide for v1.x users

### 🔧 Developer Experience

- Simpler deployment workflow (update config, deploy, share URL)
- Better version control integration (all settings in config files)
- Cleaner configuration management
- Automated post-completion security
- Enhanced error messaging and validation

### 📦 Migration from v1.x

To migrate from v1.x to v2.0:

1. **Update configuration** (`config/launchpad.php`):
   ```php
   // OLD (v1.x)
   'enabled' => env('LAUNCHPAD_INSTALLATION_ENABLED', false),
   'current_version' => env('APP_VERSION', '1.0.0'),
   
   // NEW (v2.0)
   'enabled' => false, // Set directly
   'current_version' => '1.0.0', // Set directly
   ```

2. **Remove environment variables** (except APP_NAME):
   - Remove `LAUNCHPAD_INSTALLATION_ENABLED`
   - Remove `LAUNCHPAD_UPDATE_ENABLED` 
   - Remove `APP_VERSION` (if only used for Launchpad)

3. **Update config structure**:
   - `import_options` → `importOptions`
   - Direct boolean/string values instead of env() calls

4. **Enjoy automatic flow**:
   - No user choices required
   - Routes automatically disabled after completion
   - All operations based on your configuration

## [1.0.0] - 2025-09-08

### Added
- 🚀 **Complete Installation Wizard System**
  - 5-step guided installation process (Welcome → Requirements → License → Database → Admin → Success)
  - Professional UI with Tailwind CSS and Alpine.js integration
  - System requirements validation (PHP version, extensions, directory permissions)
  - Dynamic database configuration with multiple driver support (MySQL, PostgreSQL, SQLite)
  - Admin user creation with customizable fields
  - Environment file management and configuration updates

- 🔄 **Comprehensive Update Wizard**
  - 4-step update process (Welcome → Requirements → License → Update → Success)
  - Version management and tracking
  - Safe database migration execution
  - Post-update cache optimization and cleanup
  - Real-time progress tracking with animations

- 🛡️ **Flexible License Validation System**
  - Interface-based license validator architecture
  - Default HTTP client integration with external license servers
  - Caching system for license validation responses
  - Support for custom license validation logic
  - Envato marketplace integration template
  - Artisan command to publish customizable license checker stubs

- 🎨 **Professional User Interface**
  - Responsive design that works on all devices
  - Modern Tailwind CSS styling with consistent design patterns
  - Interactive components with Alpine.js
  - Step progress indicators and navigation
  - Success animations and celebration effects
  - Comprehensive error handling and user feedback

- 🔧 **Developer-Friendly Configuration**
  - Highly configurable through `config/launchpad.php`
  - Customizable requirements, license settings, database options
  - Dynamic admin fields and additional form fields
  - Configurable post-installation and post-update actions
  - Flexible route configuration and middleware options

- 🔒 **Security & Protection**
  - Middleware protection for installation routes
  - Installation state tracking to prevent re-installation
  - Secure environment file handling
  - License validation with proper error handling
  - Directory permission validation

- 📖 **Complete Documentation**
  - Comprehensive README with installation and usage guides
  - Configuration examples and customization instructions
  - Security best practices and deployment recommendations
  - API documentation for all services and controllers

### Technical Implementation
- Service-oriented architecture with clean separation of concerns
- InstallationService, DatabaseService, LicenseService for core functionality
- HTTP controllers for installation and update workflows
- Middleware for route protection and access control
- View templates with consistent UI patterns
- Artisan commands for package management
- Interface-based design for extensibility

### Package Features
- Laravel 10+ compatibility
- PHP 8.0+ support
- Guzzle HTTP client integration
- Spatie Laravel Package Tools foundation
- PSR-4 autoloading
- Comprehensive test coverage setup
- Static analysis with PHPStan
- Code formatting with Laravel Pint

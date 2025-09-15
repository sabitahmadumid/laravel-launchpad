# Changelog

All notable changes to `laravel-launchpad` will be documented in this file.

## [2.4.0] - 2025-09-15

### 🔒 Simplified License System & Developer Experience Improvements

This release introduces a streamlined license validation system designed to be both secure and developer-friendly, along with significant documentation improvements and codebase cleanup.

### ✨ New Features

- **🔒 Simplified License System**
  - **SimpleLicenseValidator Only** - Removed complex validators, kept secure but simple system
  - **Developer-Friendly Commands** - Simple enable/disable with route-specific controls
  - **Route-Specific Bypasses** - Granular control for installation vs update routes
  - **Enhanced Security** - Hard to bypass while maintaining developer convenience

- **🛠️ Improved License Commands**
  - **Unified Command Structure** - All license commands consolidated into logical groups
  - **Route-Specific Options** - `--install` and `--update` flags for granular control
  - **Development Keys** - Built-in bypass keys for local development
  - **Force Options** - `--force` flag for automated scripts

- **📚 Enhanced Documentation**
  - **Complete License Examples** - Real-world Envato CodeCanyon integration
  - **Custom Server Integration** - Full implementation examples
  - **Streamlined README** - Focused on essential developer information
  - **Consolidated Command Reference** - Single source for all license commands

### 🧹 Codebase Cleanup

- **Removed Duplicate Commands**
  - Eliminated `PublishLicenseStubCommand` (old)
  - Removed `LicenseCommand` (duplicate functionality)
  - Cleaned up old stub files and directories

- **Simplified Service Provider**
  - Removed unused command registrations
  - Clean imports and dependencies
  - Updated to reflect current command structure

- **Documentation Consolidation**
  - Merged 3 separate license command sections into 1
  - Removed outdated test references
  - Updated configuration examples

### 🔧 Technical Improvements

- **PHPStan Level 8 Compliance** - All code passes strict static analysis
- **Simplified Architecture** - Removed unnecessary complexity while maintaining functionality
- **Better Error Handling** - Improved license validation error messages
- **Environment Integration** - Streamlined .env configuration

### 📊 License System Commands

```bash
# Basic License Management
php artisan launchpad:license disable
php artisan launchpad:license enable
php artisan launchpad:license disable --install
php artisan launchpad:license disable --update

# Advanced Management
php artisan launchpad:license status
php artisan launchpad:license verify
php artisan launchpad:license remove
php artisan launchpad:license clear-cache

# Development Commands
php artisan launchpad:license enable-local
php artisan launchpad:license disable-local
```

### 💡 Migration Notes

- **License Validators**: Only `SimpleLicenseValidator` is supported (complex validators removed)
- **Commands**: Use new `launchpad:license` commands instead of old `launchpad:license-disable`
- **Configuration**: Simplified license config section in `config/launchpad.php`

## [2.3.0] - 2025-09-14

### 🌍 Complete Translation System & Multi-Language Support

This release introduces a comprehensive, production-ready translation system that transforms Laravel Launchpad into a truly international installation wizard with seamless language switching and dynamic field translation capabilities.

### ✨ New Features

- **🌐 Complete Multi-Language Support**
  - **Seamless Language Switching** - Switch between languages instantly during installation
  - **Bengali Translation Support** - Full Bengali (বাংলা) translation alongside English
  - **RTL Language Foundation** - Ready for right-to-left languages
  - **Session-Based Language Persistence** - Language choice persists across installation steps

- **🔄 Dynamic Field Translation System**
  - **Automatic Field Translation** - All form fields, labels, placeholders automatically translated
  - **Smart Fallback System** - Graceful fallbacks when translations are missing
  - **Translation Helper Class** - `FieldTranslator` for advanced translation management
  - **Dynamic Configuration Support** - Translate any admin form configuration on-the-fly

- **📱 Enhanced User Interface**
  - **Language Switcher Component** - Beautiful dropdown with flags and native names
  - **Improved Error Handling** - Robust JavaScript with safe DOM manipulation
  - **Better Navigation** - All buttons and navigation elements fully translated
  - **Progress Indicators** - Step-by-step progress with localized labels

- **🛠️ Developer Experience**
  - **Translation Key Structure** - Organized, hierarchical translation keys
  - **Automatic Translation Loading** - Laravel's translation system fully integrated
  - **Extensible Language System** - Easy to add new languages
  - **Comprehensive Testing** - Full test coverage for translation functionality

### 🎯 Translation Coverage

- **Installation Steps**: Welcome, Requirements, License, Database, Admin, Final, Success
- **Form Fields**: Labels, placeholders, help text, validation messages
- **Navigation**: Buttons, links, progress indicators, step names
- **Status Messages**: Success, error, loading, processing states
- **Dynamic Content**: Database types, field options, configuration summaries

### 🚀 Technical Improvements

- **Enhanced Language Service**
  - Improved session handling with fallbacks
  - Better locale detection and setting
  - Translation cache clearing for fresh translations
  - Graceful handling when session is unavailable

- **Middleware Enhancements**
  - Better middleware order for proper language initialization
  - Session availability checks before language operations
  - Automatic locale setting in Laravel application
  - Translation namespace flushing for immediate updates

- **View Template Conversion**
  - All installation pages converted from hardcoded English to translation functions
  - Dynamic field system with automatic translation lookup
  - Consistent use of `launchpad::` translation namespace
  - Backward compatibility with existing configurations

### 🧪 Quality Assurance

- **Comprehensive Test Suite**
  - Language switching functionality tests
  - Dynamic field translation tests
  - Translation fallback mechanism tests
  - License command testing

- **Error Handling Improvements**
  - Safe JavaScript DOM manipulation
  - CSRF token handling improvements
  - Network error recovery in language switcher
  - Graceful degradation when translations missing

### 🏗️ Architecture

- **Translation File Structure**
  ```
  resources/lang/
  ├── en/
  │   ├── common.php      # Common UI elements
  │   └── install.php     # Installation-specific translations
  └── bn/
      ├── common.php      # Bengali common translations
      └── install.php     # Bengali installation translations
  ```

- **Dynamic Field Translation Keys**
  ```
  launchpad::install.fields.{group}.{field_name}
  launchpad::install.field_groups.{group_key}
  launchpad::install.field_placeholders.{field_name}
  launchpad::install.field_options.{field_name}.{option_value}
  ```

### 📖 Documentation

- **Complete Translation Guide** - Integrated into README with examples
- **Developer Guide** - How to add new languages and customize translations
- **Migration Guide** - Easy transition from hardcoded text to translations
- **Configuration Examples** - Real-world usage patterns

### 💡 Benefits

- **User Experience**: Professional, localized installation experience
- **Developer Friendly**: Easy to extend with new languages
- **Maintainable**: Centralized translation management
- **Future Proof**: Scalable system for any number of languages
- **Backward Compatible**: Existing configurations continue to work

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

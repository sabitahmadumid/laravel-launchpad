<?php

namespace SabitAhmad\LaravelLaunchpad\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use SabitAhmad\LaravelLaunchpad\Services\DatabaseService;
use SabitAhmad\LaravelLaunchpad\Services\InstallationService;
use SabitAhmad\LaravelLaunchpad\Services\LicenseService;

class InstallationController extends Controller
{
    protected InstallationService $installationService;

    protected DatabaseService $databaseService;

    protected LicenseService $licenseService;

    public function __construct(
        InstallationService $installationService,
        DatabaseService $databaseService,
        LicenseService $licenseService
    ) {
        $this->installationService = $installationService;
        $this->databaseService = $databaseService;
        $this->licenseService = $licenseService;
    }

    public function welcome(): View
    {
        return view('launchpad::install.welcome');
    }

    public function requirements(): View
    {
        $requirements = $this->installationService->checkRequirements();
        $allMet = $this->installationService->allRequirementsMet();

        return view('launchpad::install.requirements', compact('requirements', 'allMet'));
    }

    public function checkRequirements(): JsonResponse
    {
        $requirements = $this->installationService->checkRequirements();
        $allMet = $this->installationService->allRequirementsMet();

        return response()->json([
            'success' => $allMet,
            'requirements' => $requirements,
            'message' => $allMet ? 'All requirements met!' : 'Some requirements are not met.',
        ]);
    }

    public function license(): View|RedirectResponse
    {
        if (! $this->licenseService->isLicenseRequired()) {
            return redirect()->route('launchpad.install.database');
        }

        return view('launchpad::install.license');
    }

    public function verifyLicense(Request $request): JsonResponse
    {
        if (! $this->licenseService->isLicenseRequired()) {
            return response()->json([
                'success' => true,
                'message' => 'License validation is disabled.',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'license_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a valid license key.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Validate license and automatically save to .env if valid
        $result = $this->licenseService->validateLicense($request->license_key);

        // Store verification status in session
        session(['license_verified' => $result['valid']]);

        // Return enhanced response with env update status
        return response()->json([
            'success' => $result['valid'],
            'valid' => $result['valid'],
            'message' => $result['message'],
            'env_updated' => $result['env_updated'] ?? false,
            'data' => $result['data'] ?? [],
        ]);
    }

    public function database(): View
    {
        $supportedDrivers = config('launchpad.database.supported_drivers', ['mysql']);
        $importOptions = config('launchpad.database.import_options', []);

        return view('launchpad::install.database', compact('supportedDrivers', 'importOptions'));
    }

    public function testDatabase(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'connection' => 'required|string',
            'host' => 'required_unless:connection,sqlite|string',
            'port' => 'required_unless:connection,sqlite|integer',
            'database' => 'required|string',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please fill all required fields.',
                'errors' => $validator->errors(),
            ], 200); // Return 200 instead of 422 to avoid JavaScript catch block
        }

        try {
            $result = $this->databaseService->testConnection($request->all());

            if ($result['success']) {
                session(['database_config' => $request->all()]);
            }

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database test failed: '.$e->getMessage(),
            ]);
        }
    }

    public function setupDatabase(Request $request): JsonResponse
    {
        try {
            // Validate that database connection was tested first
            $databaseConfig = session('database_config');
            if (! $databaseConfig) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please test database connection first.',
                ], 400);
            }

            // Handle database setup (migrations, seeders, etc.)
            $this->handleDatabaseSetup($request);

            // Save database configuration to .env file
            $this->databaseService->updateEnvFile($databaseConfig);

            // Clear database config from session since it's now saved
            session()->forget('database_config');

            return response()->json([
                'success' => true,
                'message' => 'Database setup completed successfully!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database setup failed: '.$e->getMessage(),
            ], 500);
        }
    }

    public function admin(): View
    {
        // Ensure language is properly initialized
        $languageService = app(\SabitAhmad\LaravelLaunchpad\Services\LanguageService::class);
        $languageService->initializeLanguage();

        $adminConfig = config('launchpad.admin', []);
        $additionalFields = config('launchpad.additional_fields', []);

        return view('launchpad::install.admin', compact('adminConfig', 'additionalFields', 'languageService'));
    }

    public function createAdmin(Request $request): JsonResponse
    {
        $adminConfig = config('launchpad.admin', []);
        $additionalFields = config('launchpad.additional_fields', []);

        // Build validation rules
        $rules = [];
        foreach ($adminConfig['fields'] ?? [] as $field => $config) {
            $rules[$field] = $config['validation'] ?? 'nullable';
        }

        foreach ($additionalFields as $group => $groupConfig) {
            foreach ($groupConfig['fields'] ?? [] as $field => $config) {
                $rules[$field] = $config['validation'] ?? 'nullable';
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Store admin data in session for validation in complete() method
            session(['admin_data' => $request->all()]);

            // Create admin user immediately
            $this->createAdminUserFromRequest($request);

            // Update additional environment variables
            $this->updateAdditionalEnvVars($request);

            return response()->json([
                'success' => true,
                'message' => 'Admin user created and configuration saved successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create admin user: '.$e->getMessage(),
            ], 500);
        }
    }

    public function final(): View
    {
        $databaseOptions = config('launchpad.database.import_options', []);

        return view('launchpad::install.final', compact('databaseOptions'));
    }

    public function complete(Request $request): JsonResponse
    {
        try {
            // Validate that we can proceed with installation
            $this->validateInstallationPreconditions();

            // Generate app key if needed
            if (config('launchpad.post_install.actions.generate_app_key', true)) {
                Artisan::call('key:generate', ['--force' => true]);
            }

            // Mark as installed ONLY after everything succeeds
            $this->installationService->markAsInstalled();

            // Disable installation routes BEFORE running post-install actions
            $this->disableInstallationRoutes();

            // Run post-installation actions (this may cache config, so routes should be disabled first)
            $this->runPostInstallActions();

            // Clear session data
            session()->forget(['database_config', 'admin_data', 'license_verified']);

            return response()->json([
                'success' => true,
                'message' => 'Installation completed successfully!',
                'redirect_url' => config('launchpad.post_install.redirect_url', '/admin'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Installation failed: '.$e->getMessage(),
            ], 500);
        }
    }

    public function success(): View
    {
        $redirectUrl = config('launchpad.post_install.redirect_url', '/admin');

        return view('launchpad::install.success', compact('redirectUrl'));
    }

    protected function validateInstallationPreconditions()
    {
        // Check if already installed
        if ($this->installationService->isInstalled()) {
            throw new \Exception('Application is already installed');
        }

        // Check if database connection is working (config should be in .env now)
        try {
            // Read current database configuration from environment
            $currentDbConfig = [
                'connection' => config('database.default'),
                'host' => config('database.connections.'.config('database.default').'.host'),
                'port' => config('database.connections.'.config('database.default').'.port'),
                'database' => config('database.connections.'.config('database.default').'.database'),
                'username' => config('database.connections.'.config('database.default').'.username'),
                'password' => config('database.connections.'.config('database.default').'.password'),
            ];

            $result = $this->databaseService->testConnection($currentDbConfig);
            if (! $result['success']) {
                throw new \Exception('Database connection test failed: '.$result['message']);
            }
        } catch (\Exception $e) {
            throw new \Exception('Database configuration not found or invalid. Please complete database setup first.');
        }

        // Check if license is required and verified
        $licenseConfig = config('launchpad.license', []);
        if ($licenseConfig['enabled'] ?? false) {
            if (! session('license_verified')) {
                throw new \Exception('License verification required but not completed');
            }
        }

        // Check if admin data is present when admin creation is enabled
        $adminConfig = config('launchpad.admin', []);
        if ($adminConfig['enabled'] ?? false) {
            $adminData = session('admin_data');
            if (! $adminData) {
                throw new \Exception('Admin user data not found. Please complete admin setup first.');
            }
        }
    }

    protected function updateAdditionalEnvVars(?Request $request = null)
    {
        $adminData = $request ? $request->all() : session('admin_data', []);
        $additionalFields = config('launchpad.additional_fields', []);

        $envUpdates = [];

        foreach ($additionalFields as $group => $groupConfig) {
            foreach ($groupConfig['fields'] ?? [] as $field => $config) {
                if (isset($config['env_key']) && isset($adminData[$field])) {
                    $envUpdates[$config['env_key']] = $adminData[$field];
                }
            }
        }

        if (! empty($envUpdates)) {
            $this->updateEnvFile($envUpdates);
        }
    }

    protected function updateEnvFile(array $updates)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        foreach ($updates as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}=".(empty($value) ? '""' : '"'.$value.'"');

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        file_put_contents($envFile, $envContent);
    }

    protected function handleDatabaseSetup(Request $request)
    {
        $options = $request->get('database_options', []);
        $importOptions = config('launchpad.database.import_options', []);

        // Handle SQL dump import
        if (in_array('dump_file', $options)) {
            $dumpConfig = $importOptions['dump_file'] ?? [];
            if ($dumpConfig['enabled'] ?? false) {
                $result = $this->databaseService->importDumpFile($dumpConfig['path']);
                if (! $result['success']) {
                    throw new \Exception('Database dump import failed: '.$result['message']);
                }
            } else {
                throw new \Exception('SQL dump import is not enabled in configuration');
            }
        }

        // Handle migrations
        if (in_array('migrations', $options)) {
            $migrationsConfig = $importOptions['migrations'] ?? [];
            if ($migrationsConfig['enabled'] ?? false) {
                $result = $this->databaseService->runMigrations();
                if (! $result['success']) {
                    throw new \Exception('Database migration failed: '.$result['message']);
                }
            } else {
                throw new \Exception('Migrations are not enabled in configuration');
            }
        }

        // Handle seeders - ONLY if enabled in config AND requested
        if (in_array('seeders', $options)) {
            $seedersConfig = $importOptions['seeders'] ?? [];
            if ($seedersConfig['enabled'] ?? false) {
                $result = $this->databaseService->runSeeders();
                if (! $result['success']) {
                    throw new \Exception('Database seeding failed: '.$result['message']);
                }
            } else {
                // Don't throw error for seeders, just skip silently
                // This allows installation to continue without seeders
            }
        }
    }

    protected function createAdminUserFromRequest(Request $request)
    {
        $adminConfig = config('launchpad.admin');
        if (! ($adminConfig['enabled'] ?? false)) {
            return;
        }

        $userModel = $adminConfig['model'] ?? 'App\\Models\\User';

        if (! class_exists($userModel)) {
            throw new \Exception("User model {$userModel} not found");
        }

        $userData = [];
        foreach ($adminConfig['fields'] ?? [] as $field => $config) {
            if ($request->has($field)) {
                $value = $request->input($field);

                if ($field === 'password') {
                    $value = Hash::make($value);
                }

                $userData[$field] = $value;
            }
        }

        // Add default data
        $userData = array_merge($userData, $adminConfig['default_data'] ?? []);

        // Remove password confirmation
        unset($userData['password_confirmation']);

        $userModel::create($userData);
    }

    protected function runPostInstallActions()
    {
        $actions = config('launchpad.post_install.actions', []);

        if ($actions['cache_clear'] ?? false) {
            Artisan::call('cache:clear');
        }

        if ($actions['config_cache'] ?? false) {
            Artisan::call('config:cache');
        }

        if ($actions['route_cache'] ?? false) {
            Artisan::call('route:cache');
        }

        if ($actions['view_cache'] ?? false) {
            Artisan::call('view:cache');
        }
    }

    /**
     * Disable installation routes by setting the installation enabled flag to false in config
     */
    protected function disableInstallationRoutes(): void
    {
        try {
            $configPath = config_path('launchpad.php');

            if (File::exists($configPath)) {
                $content = File::get($configPath);

                // Update the 'enabled' => false in the installation section
                $pattern = "/('installation'\s*=>\s*\[(?:[^[\]]*(?:\[[^\]]*\])*)*'enabled'\s*=>\s*)true/s";
                $replacement = '${1}false';

                $updatedContent = preg_replace($pattern, $replacement, $content);

                if ($updatedContent && $updatedContent !== $content) {
                    File::put($configPath, $updatedContent);

                    // Set in runtime config immediately
                    Config::set('launchpad.installation.enabled', false);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to disable installation routes: '.$e->getMessage());
        }
    }
}

<?php

namespace SabitAhmad\LaravelLaunchpad\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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

    /**
     * @return \Illuminate\View\View
     */
    public function welcome()
    {
        return view('launchpad::install.welcome');
    }

    /**
     * @return \Illuminate\View\View
     */
    public function requirements()
    {
        $requirements = $this->installationService->checkRequirements();
        $allMet = $this->installationService->allRequirementsMet();

        return view('launchpad::install.requirements', compact('requirements', 'allMet'));
    }

    public function checkRequirements()
    {
        $requirements = $this->installationService->checkRequirements();
        $allMet = $this->installationService->allRequirementsMet();

        return response()->json([
            'success' => $allMet,
            'requirements' => $requirements,
            'message' => $allMet ? 'All requirements met!' : 'Some requirements are not met.',
        ]);
    }

    /**
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function license()
    {
        if (! $this->licenseService->isLicenseRequired()) {
            return redirect()->route('launchpad.install.database');
        }

        return view('launchpad::install.license');
    }

    public function verifyLicense(Request $request)
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

        $result = $this->licenseService->validateLicense($request->license_key);

        session(['license_verified' => $result['valid']]);

        return response()->json($result);
    }

    /**
     * @return \Illuminate\View\View
     */
    public function database()
    {
        $supportedDrivers = config('launchpad.database.supported_drivers', ['mysql']);

        return view('launchpad::install.database', compact('supportedDrivers'));
    }

    public function testDatabase(Request $request)
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

    /**
     * @return \Illuminate\View\View
     */
    public function admin()
    {
        $adminConfig = config('launchpad.admin', []);
        $additionalFields = config('launchpad.additional_fields', []);

        return view('launchpad::install.admin', compact('adminConfig', 'additionalFields'));
    }

    public function createAdmin(Request $request)
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

        session(['admin_data' => $request->all()]);

        return response()->json([
            'success' => true,
            'message' => 'Admin configuration saved.',
        ]);
    }

    /**
     * @return \Illuminate\View\View
     */
    public function final()
    {
        $databaseOptions = config('launchpad.database.import_options', []);

        return view('launchpad::install.final', compact('databaseOptions'));
    }

    public function complete(Request $request)
    {
        try {
            // Update database configuration
            $databaseConfig = session('database_config');
            if ($databaseConfig) {
                $this->databaseService->updateEnvFile($databaseConfig);
            }

            // Update additional environment variables
            $this->updateAdditionalEnvVars();

            // Generate app key if needed
            if (config('launchpad.post_install.actions.generate_app_key', true)) {
                Artisan::call('key:generate', ['--force' => true]);
            }

            // Handle database setup
            $this->handleDatabaseSetup($request);

            // Create admin user
            $this->createAdminUser();

            // Run post-installation actions
            $this->runPostInstallActions();

            // Mark as installed
            $this->installationService->markAsInstalled();

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

    /**
     * @return \Illuminate\View\View
     */
    public function success()
    {
        $redirectUrl = config('launchpad.post_install.redirect_url', '/admin');

        return view('launchpad::install.success', compact('redirectUrl'));
    }

    protected function updateAdditionalEnvVars()
    {
        $adminData = session('admin_data', []);
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

        if (in_array('dump_file', $options)) {
            $dumpConfig = config('launchpad.database.import_options.dump_file');
            if ($dumpConfig['enabled'] ?? false) {
                $result = $this->databaseService->importDumpFile($dumpConfig['path']);
                if (! $result['success']) {
                    throw new \Exception($result['message']);
                }
            }
        }

        if (in_array('migrations', $options)) {
            $result = $this->databaseService->runMigrations();
            if (! $result['success']) {
                throw new \Exception($result['message']);
            }
        }

        if (in_array('seeders', $options)) {
            $result = $this->databaseService->runSeeders();
            if (! $result['success']) {
                throw new \Exception($result['message']);
            }
        }
    }

    protected function createAdminUser()
    {
        $adminConfig = config('launchpad.admin');
        if (! ($adminConfig['enabled'] ?? false)) {
            return;
        }

        $adminData = session('admin_data', []);
        $userModel = $adminConfig['model'] ?? 'App\\Models\\User';

        if (! class_exists($userModel)) {
            throw new \Exception("User model {$userModel} not found");
        }

        $userData = [];
        foreach ($adminConfig['fields'] ?? [] as $field => $config) {
            if (isset($adminData[$field])) {
                $value = $adminData[$field];

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
}

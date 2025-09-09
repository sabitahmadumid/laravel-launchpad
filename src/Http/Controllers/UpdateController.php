<?php

namespace SabitAhmad\LaravelLaunchpad\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use SabitAhmad\LaravelLaunchpad\Services\DatabaseService;
use SabitAhmad\LaravelLaunchpad\Services\InstallationService;
use SabitAhmad\LaravelLaunchpad\Services\LicenseService;

class UpdateController extends Controller
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
        $currentVersion = $this->getCurrentVersion();
        $newVersion = config('launchpad.update.current_version', '1.0.0');

        return view('launchpad::update.welcome', compact('currentVersion', 'newVersion'));
    }

    /**
     * @return \Illuminate\View\View
     */
    public function requirements()
    {
        $requirements = $this->installationService->checkRequirements();
        $allMet = $this->installationService->allRequirementsMet();

        return view('launchpad::update.requirements', compact('requirements', 'allMet'));
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
            return redirect()->route('launchpad.update.update');
        }

        return view('launchpad::update.license');
    }

    public function verifyLicense(Request $request)
    {
        if (! $this->licenseService->isLicenseRequired()) {
            return response()->json([
                'success' => true,
                'message' => 'License validation is disabled.',
            ]);
        }

        $validator = \Validator::make($request->all(), [
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
    public function update()
    {
        $updateOptions = config('launchpad.update_options', []);
        $currentVersion = $this->getCurrentVersion();
        $newVersion = config('launchpad.update.current_version', '1.0.0');

        return view('launchpad::update.update', compact('updateOptions', 'currentVersion', 'newVersion'));
    }

    public function runUpdate(Request $request)
    {
        try {
            $options = $request->get('update_options', []);
            $results = [];

            // Import dump file if selected
            if (in_array('dump_file', $options)) {
                $dumpConfig = config('launchpad.update_options.dump_file');
                if ($dumpConfig['enabled'] ?? false) {
                    $result = $this->databaseService->importDumpFile($dumpConfig['path']);
                    $results['dump_file'] = $result;

                    if (! $result['success']) {
                        throw new \Exception($result['message']);
                    }
                }
            }

            // Run migrations if selected
            if (in_array('migrations', $options)) {
                $result = $this->databaseService->runMigrations();
                $results['migrations'] = $result;

                if (! $result['success']) {
                    throw new \Exception($result['message']);
                }
            }

            // Run post-update actions
            $this->runPostUpdateActions();

            // Update version file
            $this->updateVersionFile();

            // Disable update routes after successful completion
            $this->disableUpdateRoutes();

            return response()->json([
                'success' => true,
                'message' => 'Update completed successfully!',
                'results' => $results,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * @return \Illuminate\View\View
     */
    public function success()
    {
        $newVersion = config('launchpad.update.current_version', '1.0.0');

        return view('launchpad::update.success', compact('newVersion'));
    }

    protected function getCurrentVersion(): string
    {
        $versionFile = config('launchpad.update.version_file');

        if (File::exists($versionFile)) {
            $content = File::get($versionFile);
            $data = json_decode($content, true);

            return $data['version'] ?? '1.0.0';
        }

        // Try to get from installation file
        $installationData = $this->installationService->getInstallationData();
        if ($installationData) {
            return $installationData['version'] ?? '1.0.0';
        }

        return '1.0.0';
    }

    protected function updateVersionFile(): void
    {
        $newVersion = config('launchpad.update.current_version', '1.0.0');

        // Update version file
        $versionFile = config('launchpad.update.version_file');
        $directory = dirname($versionFile);

        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        File::put($versionFile, json_encode([
            'version' => $newVersion,
            'updated_at' => now()->toISOString(),
        ]));

        // Also update the config file with the new version for future updates
        $this->updateVersionInConfig($newVersion);
    }

    /**
     * Update the current version in the config file
     */
    protected function updateVersionInConfig(string $newVersion): void
    {
        try {
            $configPath = config_path('launchpad.php');

            if (File::exists($configPath)) {
                $content = File::get($configPath);

                // Update the 'current_version' in the update section
                $pattern = "/('update'\s*=>\s*\[(?:[^[\]]*(?:\[[^\]]*\])*)*'current_version'\s*=>\s*)'[^']*'/s";
                $replacement = "\${1}'$newVersion'";

                $updatedContent = preg_replace($pattern, $replacement, $content);

                if ($updatedContent && $updatedContent !== $content) {
                    File::put($configPath, $updatedContent);

                    // Set in runtime config
                    Config::set('launchpad.update.current_version', $newVersion);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to update version in config: '.$e->getMessage());
        }
    }

    protected function runPostUpdateActions(): void
    {
        $options = config('launchpad.update_options', []);

        if ($options['cache_clear'] ?? false) {
            Artisan::call('cache:clear');
        }

        if ($options['config_cache'] ?? false) {
            Artisan::call('config:cache');
        }
    }

    /**
     * Disable update routes by setting the update enabled flag to false in config
     */
    protected function disableUpdateRoutes(): void
    {
        try {
            $configPath = config_path('launchpad.php');

            if (File::exists($configPath)) {
                $content = File::get($configPath);

                // Update the 'enabled' => false in the update section
                $pattern = "/('update'\s*=>\s*\[(?:[^[\]]*(?:\[[^\]]*\])*)*'enabled'\s*=>\s*)true/s";
                $replacement = '${1}false';

                $updatedContent = preg_replace($pattern, $replacement, $content);

                if ($updatedContent && $updatedContent !== $content) {
                    File::put($configPath, $updatedContent);

                    // Clear config cache to ensure the new setting takes effect
                    Artisan::call('config:clear');

                    // Also set in runtime config
                    Config::set('launchpad.update.enabled', false);
                }
            }
        } catch (\Exception $e) {
            // Log the error but don't fail the update process
            Log::warning('Failed to disable update routes: '.$e->getMessage());
        }
    }
}

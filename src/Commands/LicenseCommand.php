<?php

namespace SabitAhmad\LaravelLaunchpad\Commands;

use Illuminate\Console\Command;
use SabitAhmad\LaravelLaunchpad\Services\LicenseService;

class LicenseCommand extends Command
{
    protected $signature = 'launchpad:license 
                           {action : The action to perform (status|verify|remove|clear-cache|enable-local|disable-local)}
                           {--key= : License key for verification}
                           {--force : Force the action without confirmation}';

    protected $description = 'Manage Laravel Launchpad license';

    protected LicenseService $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        parent::__construct();
        $this->licenseService = $licenseService;
    }

    public function handle(): int
    {
        $action = $this->argument('action');

        return match ($action) {
            'status' => $this->showStatus(),
            'verify' => $this->verifyLicense(),
            'remove' => $this->removeLicense(),
            'clear-cache' => $this->clearCache(),
            'enable-local' => $this->enableLocalEnforcement(),
            'disable-local' => $this->disableLocalEnforcement(),
            default => $this->showError("Invalid action: {$action}")
        };
    }

    protected function showStatus(): int
    {
        $status = $this->licenseService->getLicenseStatus();

        $this->info('Laravel Launchpad License Status');
        $this->line('=====================================');

        $this->line('Has License: '.($status['has_license'] ? '✅ Yes' : '❌ No'));
        $this->line('Is Valid: '.($status['is_valid'] ? '✅ Yes' : '❌ No'));

        if ($status['has_license']) {
            $this->line('Source: '.ucfirst($status['source']));
        }

        $this->line('Message: '.$status['message']);

        if ($this->licenseService->isLicenseRequired()) {
            $this->line('License Required: ✅ Yes');
        } else {
            $this->line('License Required: ❌ No');
        }

        return 0;
    }

    protected function verifyLicense(): int
    {
        $licenseKey = $this->option('key');

        if (! $licenseKey) {
            $licenseKey = $this->ask('Please enter your license key');

            if (! $licenseKey) {
                $this->error('License key is required.');

                return 1;
            }
        }

        $this->info('Verifying license...');

        try {
            $result = $this->licenseService->validateLicense($licenseKey);

            if ($result['valid']) {
                $this->info('✅ License verified successfully!');
                $this->line('Message: '.($result['message'] ?? 'License is valid'));

                if ($result['env_updated'] ?? false) {
                    $this->info('📄 License key automatically saved to .env file');
                } else {
                    $this->warn('⚠️  License key stored locally (could not update .env file)');
                }

                return 0;
            } else {
                $this->error('❌ License verification failed.');
                $this->line('Message: '.($result['message'] ?? 'Invalid license'));

                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ License verification error: '.$e->getMessage());

            return 1;
        }
    }

    protected function removeLicense(): int
    {
        if (! $this->option('force')) {
            if (! $this->confirm('Are you sure you want to remove the stored license?')) {
                $this->info('Operation cancelled.');

                return 0;
            }
        }

        try {
            $this->licenseService->removeLicense();
            $this->info('✅ License removed successfully.');

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error removing license: '.$e->getMessage());

            return 1;
        }
    }

    protected function clearCache(): int
    {
        try {
            $this->licenseService->invalidateCache();
            $this->info('✅ License cache cleared successfully.');

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error clearing cache: '.$e->getMessage());

            return 1;
        }
    }

    protected function enableLocalEnforcement(): int
    {
        if (!app()->environment('local')) {
            $this->error('❌ This command can only be used in local environment.');
            return 1;
        }

        try {
            $this->licenseService->enableLocalEnforcement();
            $this->info('✅ License enforcement enabled for local environment.');
            $this->warn('⚠️  You will now need a valid license key even in local development.');
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error enabling local enforcement: ' . $e->getMessage());
            return 1;
        }
    }

    protected function disableLocalEnforcement(): int
    {
        if (!app()->environment('local')) {
            $this->error('❌ This command can only be used in local environment.');
            return 1;
        }

        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to disable license enforcement in local environment?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        try {
            $this->licenseService->disableLocalEnforcement();
            $this->info('✅ License enforcement disabled for local environment.');
            $this->line('🔓 License validation will be skipped in local development.');
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error disabling local enforcement: ' . $e->getMessage());
            return 1;
        }
    }

    protected function showError(string $message): int
    {
        $this->error($message);
        $this->line('');
        $this->line('Available actions:');
        $this->line('  status       - Show license status');
        $this->line('  verify       - Verify a license key');
        $this->line('  remove       - Remove stored license');
        $this->line('  clear-cache  - Clear license cache');
        $this->line('  enable-local - Enable license enforcement in local environment');
        $this->line('  disable-local- Disable license enforcement in local environment');
        $this->line('');
        $this->line('Examples:');
        $this->line('  php artisan launchpad:license status');
        $this->line('  php artisan launchpad:license verify --key=YOUR_LICENSE_KEY');
        $this->line('  php artisan launchpad:license remove --force');
        $this->line('  php artisan launchpad:license enable-local');
        $this->line('  php artisan launchpad:license disable-local --force');

        return 1;
    }
}

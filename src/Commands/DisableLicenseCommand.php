<?php

namespace SabitAhmad\LaravelLaunchpad\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use SabitAhmad\LaravelLaunchpad\Services\LicenseService;

class DisableLicenseCommand extends Command
{
    protected $signature = 'launchpad:license 
                           {action : enable or disable}
                           {--install : Apply to installation routes only}
                           {--update : Apply to update routes only}
                           {--force : Force action without confirmation}';

    protected $description = 'Enable or disable license verification for installation and update processes';

    protected LicenseService $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        parent::__construct();
        $this->licenseService = $licenseService;
    }

    public function handle(): int
    {
        $action = $this->argument('action');

        if (!in_array($action, ['enable', 'disable'])) {
            $this->error('❌ Invalid action. Use "enable" or "disable".');
            $this->showUsage();
            return 1;
        }

        return match ($action) {
            'enable' => $this->enableLicense(),
            'disable' => $this->disableLicense(),
        };
    }

    protected function enableLicense(): int
    {
        $this->info('🔐 Enabling license verification...');
        
        if ($this->option('install')) {
            $this->enableForInstallation();
            $this->info('✅ License verification enabled for installation routes.');
        } elseif ($this->option('update')) {
            $this->enableForUpdate();
            $this->info('✅ License verification enabled for update routes.');
        } else {
            $this->enableForAll();
            $this->info('✅ License verification enabled for all routes.');
        }

        $this->showStatus();
        return 0;
    }

    protected function disableLicense(): int
    {
        // Check if we're in a safe environment
        if ($this->isProductionEnvironment() && !$this->option('force')) {
            $this->error('❌ Cannot disable license verification in production environment.');
            $this->warn('💡 Use --force flag only if you know what you are doing.');
            return 1;
        }

        $this->info('🔓 Disabling license verification...');

        if ($this->option('install')) {
            $this->disableForInstallation();
            $this->info('✅ License verification disabled for installation routes.');
        } elseif ($this->option('update')) {
            $this->disableForUpdate();
            $this->info('✅ License verification disabled for update routes.');
        } else {
            $this->disableForAll();
            $this->info('✅ License verification disabled for all routes.');
        }

        $this->showWarnings();
        $this->showStatus();
        return 0;
    }

    protected function enableForInstallation(): void
    {
        $this->removeBypassFile('install');
        $this->licenseService->enableLocalEnforcement();
    }

    protected function enableForUpdate(): void
    {
        $this->removeBypassFile('update');
        $this->licenseService->enableLocalEnforcement();
    }

    protected function enableForAll(): void
    {
        $this->removeBypassFile('install');
        $this->removeBypassFile('update');
        $this->removeBypassFile('global');
        $this->licenseService->enableLocalEnforcement();
    }

    protected function disableForInstallation(): void
    {
        $this->createBypassFile('install');
    }

    protected function disableForUpdate(): void
    {
        $this->createBypassFile('update');
    }

    protected function disableForAll(): void
    {
        $this->licenseService->disableLocalEnforcement();
        $this->createBypassFile('global');
    }

    protected function createBypassFile(string $type): void
    {
        $bypassFile = storage_path("app/.license_bypass_{$type}");
        $bypassData = [
            'type' => $type,
            'created_at' => now()->toISOString(),
            'created_by' => get_current_user() ?: 'unknown',
            'environment' => app()->environment(),
        ];

        File::put($bypassFile, encrypt(json_encode($bypassData)));
        chmod($bypassFile, 0600);
    }

    protected function removeBypassFile(string $type): void
    {
        $bypassFile = storage_path("app/.license_bypass_{$type}");
        if (File::exists($bypassFile)) {
            File::delete($bypassFile);
        }
    }

    protected function showStatus(): void
    {
        $this->line('');
        $this->info('� Current License Status:');
        $this->line('==========================');
        
        $env = app()->environment();
        $this->line("Environment: <fg=cyan>{$env}</>");
        
        $isRequired = $this->licenseService->isLicenseRequired();
        $this->line('License Required: ' . ($isRequired ? '<fg=red>Yes</>' : '<fg=green>No</>'));
        
        // Check specific bypasses
        $installBypass = File::exists(storage_path('app/.license_bypass_install'));
        $updateBypass = File::exists(storage_path('app/.license_bypass_update'));
        $globalBypass = File::exists(storage_path('app/.license_bypass_global'));
        
        $this->line('');
        $this->info('🚫 Active Bypasses:');
        $this->line('Installation Routes: ' . ($installBypass ? '<fg=yellow>Disabled</>' : '<fg=green>Enabled</>'));
        $this->line('Update Routes: ' . ($updateBypass ? '<fg=yellow>Disabled</>' : '<fg=green>Enabled</>'));
        $this->line('Global Bypass: ' . ($globalBypass ? '<fg=red>Active</>' : '<fg=gray>Inactive</>'));

        if ($isRequired) {
            $status = $this->licenseService->getLicenseStatus();
            $this->line('');
            $this->line('License Valid: ' . ($status['is_valid'] ? '<fg=green>Yes</>' : '<fg=red>No</>'));
            if (isset($status['message'])) {
                $this->line("Status: {$status['message']}");
            }
        }
    }

    protected function showWarnings(): void
    {
        $this->line('');
        $this->warn('⚠️  License verification has been disabled!');
        $this->warn('⚠️  This should only be used in development environments.');
        if ($this->isProductionEnvironment()) {
            $this->error('🚨 WARNING: You are in production environment!');
        }
    }

    protected function showUsage(): void
    {
        $this->line('');
        $this->info('💡 Usage Examples:');
        $this->line('  php artisan launchpad:license disable                # Disable all license checks');
        $this->line('  php artisan launchpad:license enable                 # Enable all license checks');
        $this->line('  php artisan launchpad:license disable --install      # Disable for installation only');
        $this->line('  php artisan launchpad:license disable --update       # Disable for updates only');
        $this->line('  php artisan launchpad:license enable --install       # Enable for installation only');
        $this->line('');
        $this->info('� Additional Commands:');
        $this->line('  php artisan launchpad:license-stub publish           # Publish license validator');
        $this->line('  php artisan launchpad:license status                 # Check detailed status');
    }

    protected function isProductionEnvironment(): bool
    {
        return app()->environment('production');
    }
}

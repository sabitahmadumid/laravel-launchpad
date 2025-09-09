<?php

namespace SabitAhmad\LaravelLaunchpad\Commands;

use Illuminate\Console\Command;

class LaravelLaunchpadCommand extends Command
{
    public $signature = 'launchpad:status';

    public $description = 'Display the status of Laravel Launchpad installation and update features';

    public function handle(): int
    {
        $this->info('Laravel Launchpad Status');
        $this->line('========================');

        // Check installation status
        $installationEnabled = config('launchpad.installation.enabled', false);
        $this->line('Installation Routes: '.($installationEnabled ? '<fg=green>Enabled</>' : '<fg=red>Disabled</>'));

        // Check update status
        $updateEnabled = config('launchpad.update.enabled', false);
        $this->line('Update Routes: '.($updateEnabled ? '<fg=green>Enabled</>' : '<fg=red>Disabled</>'));

        // Check if installed
        $installedFile = config('launchpad.installation.completed_file', storage_path('app/installed.lock'));
        $isInstalled = file_exists($installedFile);
        $this->line('Installation Status: '.($isInstalled ? '<fg=green>Completed</>' : '<fg=yellow>Not Completed</>'));

        // Check version
        $currentVersion = config('launchpad.update.current_version', 'Unknown');
        $this->line('Current Version: <fg=cyan>'.$currentVersion.'</>');

        return self::SUCCESS;
    }
}

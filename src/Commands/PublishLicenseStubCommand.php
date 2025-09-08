<?php

namespace SabitAhmad\LaravelLaunchpad\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishLicenseStubCommand extends Command
{
    protected $signature = 'launchpad:publish-license-stub';

    protected $description = 'Publish the license validator stub to the application';

    public function handle()
    {
        $stubPath = __DIR__.'/../../stubs/EnvatoLicenseChecker.php.stub';
        $targetPath = app_path('Services/EnvatoLicenseChecker.php');

        if (File::exists($targetPath)) {
            if (! $this->confirm('License validator already exists. Do you want to overwrite it?')) {
                $this->info('License validator publishing cancelled.');

                return;
            }
        }

        $targetDirectory = dirname($targetPath);
        if (! File::exists($targetDirectory)) {
            File::makeDirectory($targetDirectory, 0755, true);
        }

        File::copy($stubPath, $targetPath);

        $this->info('License validator stub published successfully!');
        $this->line('You can now customize the license validation logic in:');
        $this->line($targetPath);
        $this->line('');
        $this->line('Don\'t forget to update your .env file:');
        $this->line('LAUNCHPAD_LICENSE_VALIDATOR=App\\Services\\EnvatoLicenseChecker');
        $this->line('LAUNCHPAD_LICENSE_SERVER_URL=https://your-license-server.com/api/verify');
    }
}

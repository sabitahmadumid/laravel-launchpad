<?php

namespace SabitAhmad\LaravelLaunchpad\Commands;

use Illuminate\Console\Command;

class PublishLanguageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'launchpad:publish-lang {--force : Overwrite existing language files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish Laravel Launchpad language files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Publishing Laravel Launchpad language files...');

        $params = [
            '--provider' => "SabitAhmad\LaravelLaunchpad\LaravelLaunchpadServiceProvider",
            '--tag' => 'laravel-launchpad-lang',
        ];

        if ($this->option('force')) {
            $params['--force'] = true;
        }

        $this->call('vendor:publish', $params);

        $this->info('Language files published successfully!');
        $this->line('');
        $this->line('Available languages:');
        $this->line('- English (en)');
        $this->line('- Bengali (bn)');
        $this->line('');
        $this->line('You can customize the translations in:');
        $this->line('- resources/lang/vendor/launchpad/en/');
        $this->line('- resources/lang/vendor/launchpad/bn/');
        $this->line('');
        $this->line('To add more languages, create additional directories and translation files.');
    }
}

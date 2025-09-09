<?php

namespace SabitAhmad\LaravelLaunchpad;

use SabitAhmad\LaravelLaunchpad\Commands\LaravelLaunchpadCommand;
use SabitAhmad\LaravelLaunchpad\Commands\PublishLicenseStubCommand;
use SabitAhmad\LaravelLaunchpad\Http\Middleware\CheckInstallation;
use SabitAhmad\LaravelLaunchpad\Http\Middleware\CheckLicense;
use SabitAhmad\LaravelLaunchpad\Http\Middleware\RedirectIfInstalled;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelLaunchpadServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-launchpad')
            ->hasConfigFile('launchpad')
            ->hasViews()
            ->hasMigration('create_launchpad_table')
            ->hasCommands([
                LaravelLaunchpadCommand::class,
                PublishLicenseStubCommand::class,
            ]);
    }

    public function packageBooted(): void
    {
        // Only load routes conditionally based on configuration
        $this->loadConditionalRoutes();

        // Register middleware
        $this->app['router']->aliasMiddleware('check.installation', CheckInstallation::class);
        $this->app['router']->aliasMiddleware('redirect.if.installed', RedirectIfInstalled::class);
        $this->app['router']->aliasMiddleware('check.license', CheckLicense::class);
        $this->app['router']->aliasMiddleware('ensure.file.session', \SabitAhmad\LaravelLaunchpad\Http\Middleware\EnsureFileSession::class);
        $this->app['router']->aliasMiddleware('ensure.file.cache', \SabitAhmad\LaravelLaunchpad\Http\Middleware\EnsureFileCache::class);

        // Explicitly publish config file with tag
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/launchpad.php' => config_path('launchpad.php'),
            ], 'laravel-launchpad-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/launchpad'),
            ], 'laravel-launchpad-views');

            // Also register without specific tags for --all option
            $this->publishes([
                __DIR__.'/../config/launchpad.php' => config_path('launchpad.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/launchpad'),
            ], 'views');
        }
    }

    /**
     * Load routes conditionally based on configuration
     */
    protected function loadConditionalRoutes(): void
    {
        $installationEnabled = config('launchpad.installation.enabled', false);
        $updateEnabled = config('launchpad.update.enabled', false);

        if ($installationEnabled || $updateEnabled) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }
    }

    public function packageRegistered(): void
    {
        $this->app->singleton('laravel-launchpad', function () {
            return new LaravelLaunchpad;
        });

        // Register services
        $this->app->bind(
            \SabitAhmad\LaravelLaunchpad\Contracts\LicenseValidatorInterface::class,
            \SabitAhmad\LaravelLaunchpad\Services\DefaultLicenseValidator::class
        );
    }
}

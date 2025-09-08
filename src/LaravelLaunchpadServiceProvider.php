<?php

namespace SabitAhmad\LaravelLaunchpad;

use SabitAhmad\LaravelLaunchpad\Commands\LaravelLaunchpadCommand;
use SabitAhmad\LaravelLaunchpad\Commands\PublishLicenseStubCommand;
use SabitAhmad\LaravelLaunchpad\Http\Middleware\CheckInstallation;
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
            ->hasConfigFile()
            ->hasViews()
            ->hasRoute('web')
            ->hasMigration('create_launchpad_table')
            ->hasCommands([
                LaravelLaunchpadCommand::class,
                PublishLicenseStubCommand::class,
            ]);
    }

    public function packageBooted(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Register middleware
        $this->app['router']->aliasMiddleware('check.installation', CheckInstallation::class);
        $this->app['router']->aliasMiddleware('redirect.if.installed', RedirectIfInstalled::class);
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

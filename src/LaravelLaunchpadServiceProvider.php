<?php

namespace SabitAhmad\LaravelLaunchpad;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use SabitAhmad\LaravelLaunchpad\Commands\LaravelLaunchpadCommand;

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
            ->hasMigration('create_laravel_launchpad_table')
            ->hasCommand(LaravelLaunchpadCommand::class);
    }
}

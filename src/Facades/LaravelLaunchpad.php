<?php

namespace SabitAhmad\LaravelLaunchpad\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \SabitAhmad\LaravelLaunchpad\LaravelLaunchpad
 */
class LaravelLaunchpad extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \SabitAhmad\LaravelLaunchpad\LaravelLaunchpad::class;
    }
}

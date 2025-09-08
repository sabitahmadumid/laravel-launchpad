<?php

namespace SabitAhmad\LaravelLaunchpad\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use SabitAhmad\LaravelLaunchpad\Services\InstallationService;

class CheckInstallation
{
    protected InstallationService $installationService;

    public function __construct(InstallationService $installationService)
    {
        $this->installationService = $installationService;
    }

    public function handle(Request $request, Closure $next)
    {
        if (! $this->installationService->isInstalled()) {
            return redirect()->route('launchpad.install.welcome');
        }

        return $next($request);
    }
}

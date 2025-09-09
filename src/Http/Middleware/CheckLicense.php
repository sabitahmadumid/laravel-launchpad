<?php

namespace SabitAhmad\LaravelLaunchpad\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLicense
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $licenseEnabled = config('launchpad.license.enabled', false);

        // If license is not enabled, allow access
        if (! $licenseEnabled) {
            return $next($request);
        }

        // Check if license has been verified in session
        $licenseVerified = session('license_verified', false);

        // Allow access to license verification routes
        $allowedRoutes = [
            'launchpad.install.welcome',
            'launchpad.install.requirements',
            'launchpad.install.requirements.check',
            'launchpad.install.license',
            'launchpad.install.license.verify',
            'launchpad.update.welcome',
            'launchpad.update.requirements',
            'launchpad.update.requirements.check',
            'launchpad.update.license',
            'launchpad.update.license.verify',
        ];

        if (in_array($request->route()->getName(), $allowedRoutes)) {
            return $next($request);
        }

        // If license is not verified, redirect to license verification
        if (! $licenseVerified) {
            $isInstallRoute = str_contains($request->route()->getName(), 'install');
            $redirectRoute = $isInstallRoute ? 'launchpad.install.license' : 'launchpad.update.license';

            return redirect()->route($redirectRoute)->with('error', 'Please verify your license to continue.');
        }

        return $next($request);
    }
}

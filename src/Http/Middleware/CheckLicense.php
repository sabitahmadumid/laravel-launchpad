<?php

namespace SabitAhmad\LaravelLaunchpad\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use SabitAhmad\LaravelLaunchpad\Services\LicenseService;
use Symfony\Component\HttpFoundation\Response;

class CheckLicense
{
    protected LicenseService $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If license is not required, allow access
        if (!$this->licenseService->isLicenseRequired()) {
            return $next($request);
        }

        // Check if license is verified
        $licenseVerified = $this->licenseService->isLicenseVerified();

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
        if (!$licenseVerified) {
            $isInstallRoute = str_contains($request->route()->getName(), 'install');
            $redirectRoute = $isInstallRoute ? 'launchpad.install.license' : 'launchpad.update.license';

            // Store original intended URL
            session(['intended_url' => $request->url()]);

            return redirect()->route($redirectRoute)->with('error', 'Please verify your license to continue.');
        }

        return $next($request);
    }
}

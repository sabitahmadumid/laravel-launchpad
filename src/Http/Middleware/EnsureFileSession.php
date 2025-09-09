<?php

namespace SabitAhmad\LaravelLaunchpad\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use SabitAhmad\LaravelLaunchpad\Services\InstallationService;

class EnsureFileSession
{
    protected InstallationService $installationService;

    public function __construct(InstallationService $installationService)
    {
        $this->installationService = $installationService;
    }

    public function handle(Request $request, Closure $next)
    {
        // Force file-based sessions during installation to avoid database dependency
        if (!$this->installationService->isInstalled()) {
            $this->ensureFileSessionConfiguration();
        }

        return $next($request);
    }

    /**
     * Ensure session is configured for file-based storage
     */
    protected function ensureFileSessionConfiguration(): void
    {
        // Ensure session storage directory exists
        $sessionPath = storage_path('framework/sessions');
        if (!file_exists($sessionPath)) {
            mkdir($sessionPath, 0755, true);
        }

        // Only reconfigure if we're not already using file sessions
        if (config('session.driver') !== 'file') {
            // Store original config for potential restoration
            if (!app()->bound('original_session_config')) {
                app()->singleton('original_session_config', function () {
                    return [
                        'driver' => config('session.driver'),
                        'connection' => config('session.connection'),
                        'table' => config('session.table'),
                    ];
                });
            }

            // Override session configuration
            config([
                'session.driver' => 'file',
                'session.connection' => null,
                'session.table' => null,
            ]);

            // If session has already been resolved, we need to rebuild it
            $this->rebuildSessionManager();
        }
    }

    /**
     * Rebuild the session manager with new configuration
     */
    protected function rebuildSessionManager(): void
    {
        if (app()->resolved('session')) {
            app()->forgetInstance('session');
        }
        
        if (app()->resolved('session.store')) {
            app()->forgetInstance('session.store');
        }

        // Force Laravel to rebuild the session manager with file driver
        app()->make('session');
    }
}

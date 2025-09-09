<?php

namespace SabitAhmad\LaravelLaunchpad\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use SabitAhmad\LaravelLaunchpad\Services\InstallationService;

class EnsureFileCache
{
    protected InstallationService $installationService;

    public function __construct(InstallationService $installationService)
    {
        $this->installationService = $installationService;
    }

    public function handle(Request $request, Closure $next)
    {
        // Force file-based cache during installation to avoid database dependency
        if (!$this->installationService->isInstalled()) {
            $this->ensureFileCacheConfiguration();
        }

        return $next($request);
    }

    /**
     * Ensure cache is configured for file-based storage during installation
     */
    protected function ensureFileCacheConfiguration(): void
    {
        // Ensure cache storage directory exists
        $cachePath = storage_path('framework/cache/data');
        if (!file_exists($cachePath)) {
            mkdir($cachePath, 0755, true);
        }

        // Only reconfigure if we're not already using file cache
        if (config('cache.default') !== 'file') {
            // Store original config for potential restoration
            if (!app()->bound('original_cache_config')) {
                app()->singleton('original_cache_config', function () {
                    return [
                        'default' => config('cache.default'),
                    ];
                });
            }

            // Override cache configuration to use file driver
            config([
                'cache.default' => 'file',
            ]);

            // If cache has already been resolved, we need to rebuild it
            $this->rebuildCacheManager();
        }
    }

    /**
     * Rebuild the cache manager with new configuration
     */
    protected function rebuildCacheManager(): void
    {
        if (app()->resolved('cache')) {
            app()->forgetInstance('cache');
        }
        
        if (app()->resolved('cache.store')) {
            app()->forgetInstance('cache.store');
        }

        // Force Laravel to rebuild the cache manager with file driver
        app()->make('cache');
    }
}

<?php

namespace SabitAhmad\LaravelLaunchpad\Services;

use Illuminate\Support\Facades\File;

class InstallationService
{
    public function isInstalled(): bool
    {
        $completedFile = config('launchpad.installation.completed_file');

        return File::exists($completedFile);
    }

    public function markAsInstalled(): void
    {
        $completedFile = config('launchpad.installation.completed_file');
        $directory = dirname($completedFile);

        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $content = json_encode([
            'installed_at' => now()->toISOString(),
            'version' => config('launchpad.update.current_version', '1.0.0'),
        ]);
        
        if ($content === false) {
            throw new \Exception('Failed to encode installation data');
        }

        File::put($completedFile, $content);
    }

    public function resetInstallation(): void
    {
        $completedFile = config('launchpad.installation.completed_file');

        if (File::exists($completedFile)) {
            File::delete($completedFile);
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getInstallationData(): ?array
    {
        $completedFile = config('launchpad.installation.completed_file');

        if (! File::exists($completedFile)) {
            return null;
        }

        $content = File::get($completedFile);

        return json_decode($content, true);
    }

    /**
     * @return array<string, mixed>
     */
    public function checkRequirements(): array
    {
        $requirements = config('launchpad.requirements', []);
        $results = [];

        // Check PHP version
        if (isset($requirements['php'])) {
            $results['php'] = $this->checkPhpVersion($requirements['php']);
        }

        // Check extensions
        if (isset($requirements['extensions'])) {
            $results['extensions'] = $this->checkExtensions($requirements['extensions']);
        }

        // Check directories
        if (isset($requirements['directories'])) {
            $results['directories'] = $this->checkDirectories($requirements['directories']);
        }

        // Check functions
        if (isset($requirements['functions'])) {
            $results['functions'] = $this->checkFunctions($requirements['functions']);
        }

        return $results;
    }

    /**
     * @param array<string, mixed> $phpConfig
     * @return array<string, mixed>
     */
    protected function checkPhpVersion(array $phpConfig): array
    {
        $currentVersion = PHP_VERSION;
        $minVersion = $phpConfig['min_version'] ?? '8.0.0';
        $recommendedVersion = $phpConfig['recommended_version'] ?? '8.2.0';

        return [
            'current' => $currentVersion,
            'minimum' => $minVersion,
            'recommended' => $recommendedVersion,
            'meets_minimum' => version_compare($currentVersion, $minVersion, '>='),
            'meets_recommended' => version_compare($currentVersion, $recommendedVersion, '>='),
        ];
    }

    /**
     * @param array<string, mixed> $extensionsConfig
     * @return array<string, mixed>
     */
    protected function checkExtensions(array $extensionsConfig): array
    {
        $results = [
            'required' => [],
            'recommended' => [],
        ];

        foreach ($extensionsConfig['required'] ?? [] as $extension) {
            $results['required'][$extension] = extension_loaded($extension);
        }

        foreach ($extensionsConfig['recommended'] ?? [] as $extension) {
            $results['recommended'][$extension] = extension_loaded($extension);
        }

        return $results;
    }

    /**
     * @param array<string, mixed> $directoriesConfig
     * @return array<string, mixed>
     */
    protected function checkDirectories(array $directoriesConfig): array
    {
        $results = [];

        foreach ($directoriesConfig['writable'] ?? [] as $directory) {
            $path = base_path($directory);
            $exists = File::exists($path);
            $writable = $exists && File::isWritable($path);

            $results[$directory] = [
                'exists' => $exists,
                'writable' => $writable,
                'path' => $path,
            ];
        }

        return $results;
    }

    /**
     * @param array<string, mixed> $functionsConfig
     * @return array<string, mixed>
     */
    protected function checkFunctions(array $functionsConfig): array
    {
        $results = [];

        foreach ($functionsConfig['enabled'] ?? [] as $function) {
            $results[$function] = function_exists($function);
        }

        return $results;
    }

    public function allRequirementsMet(): bool
    {
        $requirements = $this->checkRequirements();

        // Check PHP version
        if (isset($requirements['php']) && ! $requirements['php']['meets_minimum']) {
            return false;
        }

        // Check required extensions
        if (isset($requirements['extensions']['required'])) {
            foreach ($requirements['extensions']['required'] as $loaded) {
                if (! $loaded) {
                    return false;
                }
            }
        }

        // Check writable directories
        if (isset($requirements['directories'])) {
            foreach ($requirements['directories'] as $directory) {
                if (! $directory['exists'] || ! $directory['writable']) {
                    return false;
                }
            }
        }

        // Check enabled functions
        if (isset($requirements['functions'])) {
            foreach ($requirements['functions'] as $enabled) {
                if (! $enabled) {
                    return false;
                }
            }
        }

        return true;
    }
}

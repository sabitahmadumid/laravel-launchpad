<?php

namespace SabitAhmad\LaravelLaunchpad\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use SabitAhmad\LaravelLaunchpad\Contracts\LicenseValidatorInterface;

class LicenseService
{
    protected LicenseValidatorInterface $validator;

    protected string $licenseFile;

    protected string $cacheKey = 'launchpad_license_status';

    public function __construct()
    {
        $validatorClass = config('launchpad.license.validator_class');
        $this->licenseFile = storage_path('app/.license');

        if ($validatorClass && class_exists($validatorClass)) {
            $this->validator = App::make($validatorClass);
        } else {
            $this->validator = App::make(LicenseValidatorInterface::class);
        }
    }

    /**
     * Check if license is verified and valid
     */
    public function isLicenseVerified(): bool
    {
        // Check if license validation is required
        if (! $this->isLicenseRequired()) {
            return true;
        }

        // Check cached status first
        $cachedStatus = Cache::get($this->cacheKey);
        if ($cachedStatus !== null) {
            return $cachedStatus;
        }

        // Get license key from environment or storage
        $licenseKey = $this->getLicenseKey();
        if (! $licenseKey) {
            return false;
        }

        // Validate license
        $result = $this->validateLicense($licenseKey);
        $isValid = $result['valid'] ?? false;

        // Cache the result
        $cacheDuration = config('launchpad.license.cache_duration', 3600);
        Cache::put($this->cacheKey, $isValid, $cacheDuration);

        return $isValid;
    }

    /**
     * Validate license with the given key and automatically save to .env if valid
     */
    public function validateLicense(string $licenseKey, array $additionalData = []): array
    {
        $result = $this->validator->validate($licenseKey, $additionalData);
        
        // If license is valid, automatically save it to .env file
        if ($result['valid']) {
            try {
                $this->updateEnvFile($licenseKey);
                $result['env_updated'] = true;
                $result['message'] = ($result['message'] ?? 'License is valid') . ' License key saved automatically.';
            } catch (\Exception $e) {
                // Don't fail the validation if env update fails, just store locally
                $this->storeLicenseKey($licenseKey);
                $result['env_updated'] = false;
                $result['message'] = ($result['message'] ?? 'License is valid') . ' License key stored locally (could not update .env file).';
            }
        }
        
        // Clear cache when validating
        Cache::forget($this->cacheKey);

        return $result;
    }

    /**
     * Update .env file with license key (similar to DatabaseService approach)
     */
    public function updateEnvFile(string $licenseKey): void
    {
        $envFile = base_path('.env');

        if (!file_exists($envFile)) {
            throw new \Exception('.env file not found');
        }

        // Create backup of .env file
        $backupFile = $envFile . '.backup.' . time();
        if (!copy($envFile, $backupFile)) {
            throw new \Exception('Could not create .env backup file');
        }

        try {
            $envContent = file_get_contents($envFile);

            $key = 'LAUNCHPAD_LICENSE_KEY';
            $value = $licenseKey;
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}=\"" . str_replace('"', '\"', $value) . "\"";

            if (preg_match($pattern, $envContent)) {
                // Update existing key
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                // Add new key at the end
                $envContent = rtrim($envContent) . "\n\n# Laravel Launchpad License\n{$replacement}\n";
            }

            if (!file_put_contents($envFile, $envContent)) {
                throw new \Exception('Could not write to .env file');
            }

            // Clean up backup file if successful
            @unlink($backupFile);

        } catch (\Exception $e) {
            // Restore backup on failure
            if (file_exists($backupFile)) {
                copy($backupFile, $envFile);
                @unlink($backupFile);
            }
            throw new \Exception('Failed to update .env file: ' . $e->getMessage());
        }
    }

    /**
     * Get the stored license key
     */
    public function getLicenseKey(): ?string
    {
        // Try environment first
        $envKey = env('LAUNCHPAD_LICENSE_KEY');
        if ($envKey) {
            return $envKey;
        }

        // Try storage file
        if (file_exists($this->licenseFile)) {
            $encrypted = file_get_contents($this->licenseFile);

            return $this->decryptLicenseKey($encrypted);
        }

        return null;
    }

    /**
     * Store license key securely (fallback method when .env update fails)
     */
    protected function storeLicenseKey(string $licenseKey): void
    {
        // Don't store if already in environment
        if (env('LAUNCHPAD_LICENSE_KEY')) {
            return;
        }

        $encrypted = $this->encryptLicenseKey($licenseKey);
        file_put_contents($this->licenseFile, $encrypted);

        // Make file readable only by owner
        chmod($this->licenseFile, 0600);
    }

    /**
     * Check if license validation is required
     * This method is harder to bypass as it checks multiple conditions
     */
    public function isLicenseRequired(): bool
    {
        // Check if we're in local environment
        if (app()->environment('local', 'testing')) {
            return config('launchpad.license.enforce_local', false);
        }

        // Production always requires license unless explicitly disabled via environment
        if (env('LAUNCHPAD_DISABLE_LICENSE') === 'true') {
            return false;
        }

        // Check config (but this can be overridden by environment)
        return config('launchpad.license.enabled', true);
    }

    /**
     * Invalidate license cache
     */
    public function invalidateCache(): void
    {
        Cache::forget($this->cacheKey);
    }

    /**
     * Remove stored license
     */
    public function removeLicense(): void
    {
        if (file_exists($this->licenseFile)) {
            unlink($this->licenseFile);
        }
        $this->invalidateCache();
    }

    /**
     * Get license status information
     */
    public function getLicenseStatus(): array
    {
        $licenseKey = $this->getLicenseKey();

        if (! $licenseKey) {
            return [
                'has_license' => false,
                'is_valid' => false,
                'source' => null,
                'message' => 'No license key found',
            ];
        }

        $isValid = $this->isLicenseVerified();
        $source = env('LAUNCHPAD_LICENSE_KEY') ? 'environment' : 'storage';

        return [
            'has_license' => true,
            'is_valid' => $isValid,
            'source' => $source,
            'message' => $isValid ? 'License is valid' : 'License validation failed',
        ];
    }

    /**
     * Encrypt license key for storage
     */
    protected function encryptLicenseKey(string $licenseKey): string
    {
        $key = config('app.key');
        $cipher = config('app.cipher', 'AES-256-CBC');

        return encrypt($licenseKey);
    }

    /**
     * Decrypt license key from storage
     */
    protected function decryptLicenseKey(string $encrypted): ?string
    {
        try {
            return decrypt($encrypted);
        } catch (\Exception $e) {
            return null;
        }
    }
}

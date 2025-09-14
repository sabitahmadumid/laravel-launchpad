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
     * @param array<string, mixed> $additionalData
     * @return array<string, mixed>
     */
    public function validateLicense(string $licenseKey, array $additionalData = []): array
    {
        $result = $this->validator->validate($licenseKey, $additionalData);

        // If license is valid, automatically save it to .env file
        if ($result['valid']) {
            try {
                $this->updateEnvFile($licenseKey);
                $result['env_updated'] = true;
                $result['message'] = ($result['message'] ?? 'License is valid').' License key saved automatically.';
            } catch (\Exception $e) {
                // Don't fail the validation if env update fails, just store locally
                $this->storeLicenseKey($licenseKey);
                $result['env_updated'] = false;
                $result['message'] = ($result['message'] ?? 'License is valid').' License key stored locally (could not update .env file).';
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

        if (! file_exists($envFile)) {
            throw new \Exception('.env file not found');
        }

        // Create backup of .env file
        $backupFile = $envFile.'.backup.'.time();
        if (! copy($envFile, $backupFile)) {
            throw new \Exception('Could not create .env backup file');
        }

        try {
            $envContent = file_get_contents($envFile);
            
            if ($envContent === false) {
                throw new \Exception('Could not read .env file');
            }

            $key = 'LAUNCHPAD_LICENSE_KEY';
            $value = $licenseKey;
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}=\"".str_replace('"', '\"', $value).'"';

            if (preg_match($pattern, $envContent)) {
                // Update existing key
                $updatedContent = preg_replace($pattern, $replacement, $envContent);
                if ($updatedContent === null) {
                    throw new \Exception('Failed to update license key in .env file');
                }
                $envContent = $updatedContent;
            } else {
                // Add new key at the end
                $envContent = rtrim($envContent)."\n\n# Laravel Launchpad License\n{$replacement}\n";
            }

            if (! file_put_contents($envFile, $envContent)) {
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
            throw new \Exception('Failed to update .env file: '.$e->getMessage());
        }
    }

    /**
     * Get the stored license key
     */
    public function getLicenseKey(): ?string
    {
        // Try environment first
        $envKey = config('launchpad.license.key');
        if ($envKey) {
            return $envKey;
        }

        // Try storage file
        if (file_exists($this->licenseFile)) {
            $encrypted = file_get_contents($this->licenseFile);
            
            if ($encrypted === false) {
                return null;
            }

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
        if (config('launchpad.license.key')) {
            return;
        }

        $encrypted = $this->encryptLicenseKey($licenseKey);
        file_put_contents($this->licenseFile, $encrypted);

        // Make file readable only by owner
        chmod($this->licenseFile, 0600);
    }

    /**
     * Check if license validation is required - SECURE VERSION
     * This method is designed to be very difficult to bypass
     */
    public function isLicenseRequired(): bool
    {
        // Get environment name through multiple methods to prevent tampering
        $env = $this->getSecureEnvironment();

        // Only skip license validation in very specific local development scenarios
        if ($this->isLocalDevelopment($env)) {
            // Even in local, check if enforcement is enabled via encrypted flag
            return $this->isLocalEnforcementEnabled();
        }

        // For production/staging/any non-local environment: ALWAYS require license
        // No config-based bypasses allowed in production
        return true;
    }

    /**
     * Get environment name through multiple secure methods
     */
    protected function getSecureEnvironment(): string
    {
        // Check multiple sources to prevent easy tampering
        $env1 = config('app.env', 'production');
        $env2 = config('app.env', 'production');
        $env3 = app()->environment();

        // If any method returns production, treat as production
        if (in_array('production', [$env1, $env2, $env3])) {
            return 'production';
        }

        // For consistency, all methods should agree on local
        if ($env1 === 'local' && $env2 === 'local' && $env3 === 'local') {
            return 'local';
        }

        // If inconsistent, default to production (most secure)
        return 'production';
    }

    /**
     * Check if this is truly a local development environment
     */
    protected function isLocalDevelopment(string $env): bool
    {
        if ($env !== 'local') {
            return false;
        }

        // Additional checks to verify it's actually local development
        $isLocalhost = in_array($_SERVER['HTTP_HOST'] ?? '', [
            'localhost',
            '127.0.0.1',
            '::1',
            'localhost:8000',
            '127.0.0.1:8000',
        ]);

        $hasVendorDir = is_dir(base_path('vendor'));
        $hasComposerJson = file_exists(base_path('composer.json'));

        // Must be localhost AND have development files
        return $isLocalhost && $hasVendorDir && $hasComposerJson;
    }

    /**
     * Check if local enforcement is enabled via encrypted flag
     */
    protected function isLocalEnforcementEnabled(): bool
    {
        $flagFile = storage_path('app/.license_enforce');

        if (! file_exists($flagFile)) {
            return false;
        }

        try {
            $encrypted = file_get_contents($flagFile);
            
            if ($encrypted === false) {
                return false;
            }
            
            $decrypted = decrypt($encrypted);

            return $decrypted === 'enforce_license_locally';
        } catch (\Exception $e) {
            return false;
        }
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
     * Enable license enforcement in local environment
     */
    public function enableLocalEnforcement(): void
    {
        $flagFile = storage_path('app/.license_enforce');
        $encrypted = encrypt('enforce_license_locally');
        file_put_contents($flagFile, $encrypted);
        chmod($flagFile, 0600);
    }

    /**
     * Disable license enforcement in local environment
     */
    public function disableLocalEnforcement(): void
    {
        $flagFile = storage_path('app/.license_enforce');
        if (file_exists($flagFile)) {
            unlink($flagFile);
        }
    }

    /**
     * Get license status information
     * @return array<string, mixed>
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
        $source = config('launchpad.license.key') ? 'environment' : 'storage';

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

<?php

namespace SabitAhmad\LaravelLaunchpad\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use SabitAhmad\LaravelLaunchpad\Contracts\LicenseValidatorInterface;

class DefaultLicenseValidator implements LicenseValidatorInterface
{
    protected Client $httpClient;

    protected InstallationService $installationService;

    public function __construct(Client $httpClient, InstallationService $installationService)
    {
        $this->httpClient = $httpClient;
        $this->installationService = $installationService;
    }

    public function validate(string $licenseKey, array $additionalData = []): array
    {
        // Check environment override first
        if (env('LAUNCHPAD_DISABLE_LICENSE') === 'true') {
            return [
                'valid' => true,
                'message' => 'License validation is disabled via environment.',
            ];
        }

        // Skip validation in local environment unless enforced
        if (app()->environment('local') && ! config('launchpad.license.enforce_local', false)) {
            return [
                'valid' => true,
                'message' => 'License validation skipped in local environment.',
            ];
        }

        // During installation, skip caching to avoid database dependency
        if (! $this->installationService->isInstalled()) {
            return $this->performValidation($licenseKey, $additionalData);
        }

        // Use cache only after installation is complete
        $cacheKey = 'launchpad_license_'.md5($licenseKey.serialize($additionalData));
        $cacheDuration = config('launchpad.license.cache_duration', 3600);

        try {
            return Cache::remember($cacheKey, $cacheDuration, function () use ($licenseKey, $additionalData) {
                return $this->performValidation($licenseKey, $additionalData);
            });
        } catch (\Exception $e) {
            // If cache fails (e.g., database not ready), fall back to direct validation
            return $this->performValidation($licenseKey, $additionalData);
        }
    }

    protected function performValidation(string $licenseKey, array $additionalData): array
    {
        $serverUrl = config('launchpad.license.server_url');

        if (! $serverUrl) {
            return [
                'valid' => false,
                'message' => 'License server URL is not configured.',
            ];
        }

        $retryAttempts = config('launchpad.license.retry_attempts', 3);
        $lastException = null;

        for ($attempt = 1; $attempt <= $retryAttempts; $attempt++) {
            try {
                // Get domain and IP from request context or use defaults
                $domain = parse_url(config('app.url', 'localhost'), PHP_URL_HOST);
                $ip = '127.0.0.1';

                // Try to get more specific info if we're in a web request context
                if (app()->bound('request')) {
                    $request = app('request');
                    $domain = $request->getHost();
                    $ip = $request->ip();
                }

                $response = $this->httpClient->post($serverUrl, [
                    'timeout' => config('launchpad.license.timeout', 30),
                    'json' => array_merge([
                        'license_key' => $licenseKey,
                        'domain' => $domain,
                        'ip' => $ip,
                        'version' => config('launchpad.update.current_version', '1.0.0'),
                        'app_name' => config('app.name', 'Laravel App'),
                    ], $additionalData),
                    'headers' => [
                        'User-Agent' => 'Laravel-Launchpad/'.config('launchpad.update.current_version', '1.0.0'),
                        'Accept' => 'application/json',
                    ],
                ]);

                $data = json_decode($response->getBody()->getContents(), true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Invalid JSON response from license server');
                }

                return [
                    'valid' => $data['valid'] ?? false,
                    'message' => $data['message'] ?? 'Unknown response from license server.',
                    'data' => $data['data'] ?? [],
                    'attempt' => $attempt,
                ];

            } catch (RequestException $e) {
                $lastException = $e;

                // Don't retry on client errors (4xx)
                if ($e->hasResponse() && $e->getResponse()->getStatusCode() >= 400 && $e->getResponse()->getStatusCode() < 500) {
                    break;
                }

                // Wait before retry (exponential backoff)
                if ($attempt < $retryAttempts) {
                    sleep(pow(2, $attempt - 1));
                }
            } catch (\Exception $e) {
                $lastException = $e;

                // Wait before retry
                if ($attempt < $retryAttempts) {
                    sleep(1);
                }
            }
        }

        // Check if we're in grace period
        if ($this->isInGracePeriod()) {
            return [
                'valid' => true,
                'message' => 'License validation failed, but within grace period.',
                'grace_period' => true,
            ];
        }

        return [
            'valid' => false,
            'message' => 'License validation failed after '.$retryAttempts.' attempts: '.
                        ($lastException ? $lastException->getMessage() : 'Unknown error'),
            'attempts' => $retryAttempts,
        ];
    }

    /**
     * Check if we're within the grace period for license validation failures
     */
    protected function isInGracePeriod(): bool
    {
        $gracePeriod = config('launchpad.license.grace_period', 24); // hours
        $lastValidation = Cache::get('launchpad_last_successful_validation');

        if (! $lastValidation) {
            return false;
        }

        $graceExpiry = $lastValidation + ($gracePeriod * 3600); // Convert hours to seconds

        return time() < $graceExpiry;
    }
}

<?php

namespace SabitAhmad\LaravelLaunchpad\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use SabitAhmad\LaravelLaunchpad\Contracts\LicenseValidatorInterface;

class DefaultLicenseValidator implements LicenseValidatorInterface
{
    protected Client $httpClient;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function validate(string $licenseKey, array $additionalData = []): array
    {
        if (!config('launchpad.license.enabled')) {
            return [
                'valid' => true,
                'message' => 'License validation is disabled.',
            ];
        }

        $cacheKey = 'launchpad_license_' . md5($licenseKey);
        $cacheDuration = config('launchpad.license.cache_duration', 3600);

        return Cache::remember($cacheKey, $cacheDuration, function () use ($licenseKey, $additionalData) {
            return $this->performValidation($licenseKey, $additionalData);
        });
    }

    protected function performValidation(string $licenseKey, array $additionalData): array
    {
        $serverUrl = config('launchpad.license.server_url');
        
        if (!$serverUrl) {
            return [
                'valid' => false,
                'message' => 'License server URL is not configured.',
            ];
        }

        try {
            $response = $this->httpClient->post($serverUrl, [
                'timeout' => config('launchpad.license.timeout', 30),
                'json' => array_merge([
                    'license_key' => $licenseKey,
                    'domain' => request()->getHost(),
                    'ip' => request()->ip(),
                ], $additionalData),
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'valid' => $data['valid'] ?? false,
                'message' => $data['message'] ?? 'Unknown response from license server.',
                'data' => $data['data'] ?? [],
            ];

        } catch (RequestException $e) {
            return [
                'valid' => false,
                'message' => 'License server connection failed: ' . $e->getMessage(),
            ];
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'message' => 'License validation error: ' . $e->getMessage(),
            ];
        }
    }
}

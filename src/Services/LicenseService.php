<?php

namespace SabitAhmad\LaravelLaunchpad\Services;

use Illuminate\Support\Facades\App;
use SabitAhmad\LaravelLaunchpad\Contracts\LicenseValidatorInterface;

class LicenseService
{
    protected LicenseValidatorInterface $validator;

    public function __construct()
    {
        $validatorClass = config('launchpad.license.validator_class');

        if ($validatorClass && class_exists($validatorClass)) {
            $this->validator = App::make($validatorClass);
        } else {
            $this->validator = App::make(LicenseValidatorInterface::class);
        }
    }

    public function validateLicense(string $licenseKey, array $additionalData = []): array
    {
        return $this->validator->validate($licenseKey, $additionalData);
    }

    public function isLicenseRequired(): bool
    {
        return config('launchpad.license.enabled', true);
    }
}

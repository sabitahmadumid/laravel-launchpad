<?php

namespace SabitAhmad\LaravelLaunchpad\Contracts;

interface LicenseValidatorInterface
{
    public function validate(string $licenseKey, array $additionalData = []): array;
}

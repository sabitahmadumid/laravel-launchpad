<?php

namespace SabitAhmad\LaravelLaunchpad\Contracts;

interface LicenseValidatorInterface
{
    /**
     * @param array<string, mixed> $additionalData
     * @return array<string, mixed>
     */
    public function validate(string $licenseKey, array $additionalData = []): array;
}

<?php

namespace SabitAhmad\LaravelLaunchpad\Helpers;

class FieldTranslator
{
    /**
     * Translate a field configuration value
     *
     * @param  mixed  $value  The value to translate (could be a translation key or plain text)
     * @param  string  $fallback  Fallback text if translation key is not found
     * @return string
     */
    public static function translate($value, string $fallback = '')
    {
        if (empty($value)) {
            return $fallback;
        }

        // If it looks like a translation key (contains :: or starts with launchpad.)
        if (is_string($value) && (str_contains($value, '::') || str_starts_with($value, 'launchpad.'))) {
            // Try to translate it
            $translated = __($value);

            // If translation was found (not the same as the key), return it
            if ($translated !== $value) {
                return $translated;
            }

            // If translation not found, return fallback or the value without namespace
            if (! empty($fallback)) {
                return $fallback;
            }

            // Extract the last part of the key as fallback
            $parts = explode('.', str_replace('::', '.', $value));

            return ucwords(str_replace('_', ' ', end($parts)));
        }

        // Return as-is if not a translation key
        return $value;
    }

    /**
     * Translate field configuration array
     * @param array<string, mixed> $fieldConfig
     * @return array<string, mixed>
     */
    public static function translateFieldConfig(array $fieldConfig): array
    {
        $translatableKeys = ['label', 'placeholder', 'description', 'help_text'];

        foreach ($translatableKeys as $key) {
            if (isset($fieldConfig[$key])) {
                $fieldConfig[$key] = self::translate($fieldConfig[$key]);
            }
        }

        // Handle options array for select fields
        if (isset($fieldConfig['options']) && is_array($fieldConfig['options'])) {
            foreach ($fieldConfig['options'] as $optionKey => $optionValue) {
                $fieldConfig['options'][$optionKey] = self::translate($optionValue, $optionValue);
            }
        }

        return $fieldConfig;
    }

    /**
     * Translate group label
     */
    public static function translateGroupLabel(string $groupLabel): string
    {
        return self::translate($groupLabel, $groupLabel);
    }
}

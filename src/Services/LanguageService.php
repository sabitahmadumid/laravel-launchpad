<?php

namespace SabitAhmad\LaravelLaunchpad\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageService
{
    /**
     * Available languages
     */
    const AVAILABLE_LANGUAGES = [
        'en' => [
            'name' => 'English',
            'native' => 'English',
            'flag' => '🇺🇸',
            'rtl' => false,
        ],
        'bn' => [
            'name' => 'Bengali',
            'native' => 'বাংলা',
            'flag' => '🇧🇩',
            'rtl' => false,
        ],
    ];

    /**
     * Default language
     */
    const DEFAULT_LANGUAGE = 'en';

    /**
     * Get available languages
     *
     * @return array<string, array<string, mixed>>
     */
    public function getAvailableLanguages(): array
    {
        return self::AVAILABLE_LANGUAGES;
    }

    /**
     * Get current language
     */
    public function getCurrentLanguage(): string
    {
        // Ensure we have a session before trying to access it
        if (app()->bound('session') && app('session')->isStarted()) {
            return Session::get('launchpad_language', $this->getDefaultLanguage());
        }

        // Fallback to app locale if session is not available
        return app()->getLocale() ?: $this->getDefaultLanguage();
    }

    /**
     * Get default language
     */
    public function getDefaultLanguage(): string
    {
        return config('launchpad.language.default', self::DEFAULT_LANGUAGE);
    }

    /**
     * Set current language
     */
    public function setLanguage(string $language): bool
    {
        if (! $this->isLanguageAvailable($language)) {
            return false;
        }

        // Only set session if it's available and started
        if (app()->bound('session') && app('session')->isStarted()) {
            Session::put('launchpad_language', $language);
        }

        // Always set Laravel's locale
        App::setLocale($language);

        return true;
    }

    /**
     * Check if language is available
     */
    public function isLanguageAvailable(string $language): bool
    {
        return array_key_exists($language, self::AVAILABLE_LANGUAGES);
    }

    /**
     * Get language info
     *
     * @return array<string, mixed>|null
     */
    public function getLanguageInfo(?string $language = null): ?array
    {
        $language = $language ?? $this->getCurrentLanguage();

        return self::AVAILABLE_LANGUAGES[$language] ?? null;
    }

    /**
     * Initialize language for request
     */
    public function initializeLanguage(): void
    {
        $currentLanguage = $this->getCurrentLanguage();

        if ($this->isLanguageAvailable($currentLanguage)) {
            // Set Laravel's locale - this will automatically load the correct translations
            App::setLocale($currentLanguage);
        } else {
            $defaultLanguage = $this->getDefaultLanguage();
            $this->setLanguage($defaultLanguage);
        }
    }

    /**
     * Get language direction (LTR/RTL)
     */
    public function getLanguageDirection(?string $language = null): string
    {
        $languageInfo = $this->getLanguageInfo($language);

        return ($languageInfo['rtl'] ?? false) ? 'rtl' : 'ltr';
    }

    /**
     * Check if current language is RTL
     */
    public function isRtl(): bool
    {
        return $this->getLanguageDirection() === 'rtl';
    }

    /**
     * Get translated string with fallback
     *
     * @param  array<string, mixed>  $replace
     */
    public function trans(string $key, array $replace = [], ?string $locale = null): string
    {
        $locale = $locale ?? $this->getCurrentLanguage();

        // Try to get translation in current locale
        $translation = __($key, $replace, $locale);

        // If translation not found and locale is not default, try default locale
        if ($translation === $key && $locale !== self::DEFAULT_LANGUAGE) {
            $translation = __($key, $replace, self::DEFAULT_LANGUAGE);
        }

        return $translation;
    }
}

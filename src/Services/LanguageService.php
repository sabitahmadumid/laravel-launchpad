<?php

namespace SabitAhmad\LaravelLaunchpad\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
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
     * @return array
     */
    public function getAvailableLanguages(): array
    {
        return self::AVAILABLE_LANGUAGES;
    }

    /**
     * Get current language
     *
     * @return string
     */
    public function getCurrentLanguage(): string
    {
        return Session::get('launchpad_language', $this->getDefaultLanguage());
    }

    /**
     * Get default language
     *
     * @return string
     */
    public function getDefaultLanguage(): string
    {
        return config('launchpad.language.default', self::DEFAULT_LANGUAGE);
    }

    /**
     * Set current language
     *
     * @param string $language
     * @return bool
     */
    public function setLanguage(string $language): bool
    {
        if (!$this->isLanguageAvailable($language)) {
            return false;
        }

        Session::put('launchpad_language', $language);
        App::setLocale($language);

        return true;
    }

    /**
     * Check if language is available
     *
     * @param string $language
     * @return bool
     */
    public function isLanguageAvailable(string $language): bool
    {
        return array_key_exists($language, self::AVAILABLE_LANGUAGES);
    }

    /**
     * Get language info
     *
     * @param string|null $language
     * @return array|null
     */
    public function getLanguageInfo(?string $language = null): ?array
    {
        $language = $language ?? $this->getCurrentLanguage();
        
        return self::AVAILABLE_LANGUAGES[$language] ?? null;
    }

    /**
     * Initialize language for request
     *
     * @return void
     */
    public function initializeLanguage(): void
    {
        $currentLanguage = $this->getCurrentLanguage();
        
        if ($this->isLanguageAvailable($currentLanguage)) {
            App::setLocale($currentLanguage);
        } else {
            $defaultLanguage = $this->getDefaultLanguage();
            $this->setLanguage($defaultLanguage);
        }
    }

    /**
     * Get language direction (LTR/RTL)
     *
     * @param string|null $language
     * @return string
     */
    public function getLanguageDirection(?string $language = null): string
    {
        $languageInfo = $this->getLanguageInfo($language);
        
        return ($languageInfo['rtl'] ?? false) ? 'rtl' : 'ltr';
    }

    /**
     * Check if current language is RTL
     *
     * @return bool
     */
    public function isRtl(): bool
    {
        return $this->getLanguageDirection() === 'rtl';
    }

    /**
     * Get translated string with fallback
     *
     * @param string $key
     * @param array $replace
     * @param string|null $locale
     * @return string
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

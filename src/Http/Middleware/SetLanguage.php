<?php

namespace SabitAhmad\LaravelLaunchpad\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use SabitAhmad\LaravelLaunchpad\Services\LanguageService;

class SetLanguage
{
    protected LanguageService $languageService;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Ensure session is started
        if (! $request->hasSession()) {
            return $next($request);
        }

        // Check if language is set in request
        if ($request->has('lang')) {
            $requestedLanguage = $request->get('lang');
            if ($this->languageService->isLanguageAvailable($requestedLanguage)) {
                $this->languageService->setLanguage($requestedLanguage);
            }
        }

        // Always initialize language for the request to ensure proper locale
        $this->languageService->initializeLanguage();
        
        // Ensure locale is set in Laravel
        $currentLanguage = $this->languageService->getCurrentLanguage();
        app()->setLocale($currentLanguage);
        
        // Clear any translation cache to ensure fresh translations
        if (app()->bound('translator')) {
            $translator = app('translator');
            if (method_exists($translator, 'flushNamespace')) {
                $translator->flushNamespace('launchpad');
            }
        }

        return $next($request);
    }
}

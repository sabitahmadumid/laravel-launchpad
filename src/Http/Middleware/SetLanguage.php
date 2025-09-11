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
        // Check if language is set in request
        if ($request->has('lang')) {
            $requestedLanguage = $request->get('lang');
            if ($this->languageService->isLanguageAvailable($requestedLanguage)) {
                $this->languageService->setLanguage($requestedLanguage);
            }
        }

        // Initialize language for the request
        $this->languageService->initializeLanguage();

        return $next($request);
    }
}

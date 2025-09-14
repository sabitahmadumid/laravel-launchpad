<?php

namespace SabitAhmad\LaravelLaunchpad\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use SabitAhmad\LaravelLaunchpad\Services\LanguageService;

class LanguageController extends Controller
{
    protected LanguageService $languageService;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    /**
     * Switch language
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function switch(Request $request): JsonResponse|RedirectResponse
    {
        $language = $request->get('language');

        if (! $language) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Language parameter is required',
                ], 400);
            }

            return redirect()->back()->with('error', 'Language parameter is required');
        }

        $success = $this->languageService->setLanguage($language);

        if (! $success) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid language selected',
                ], 400);
            }

            return redirect()->back()->with('error', 'Invalid language selected');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Language changed successfully',
                'language' => $language,
                'language_info' => $this->languageService->getLanguageInfo($language),
            ]);
        }

        $redirect = $request->get('redirect');
        if ($redirect) {
            return redirect($redirect);
        }

        return redirect()->back()->with('success', 'Language changed successfully');
    }

    /**
     * Get available languages
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function available(): JsonResponse
    {
        return response()->json([
            'languages' => $this->languageService->getAvailableLanguages(),
            'current' => $this->languageService->getCurrentLanguage(),
            'default' => $this->languageService->getDefaultLanguage(),
        ]);
    }

    /**
     * Get current language info
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function current(): JsonResponse
    {
        $currentLanguage = $this->languageService->getCurrentLanguage();

        return response()->json([
            'language' => $currentLanguage,
            'info' => $this->languageService->getLanguageInfo($currentLanguage),
            'direction' => $this->languageService->getLanguageDirection($currentLanguage),
        ]);
    }
}

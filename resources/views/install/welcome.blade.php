@extends('launchpad::layout')

@section('title', __('launchpad::install.welcome_title'))

@section('step-indicator', __('launchpad::common.step_of', ['current' => 1, 'total' => 5]))

@section('progress')
    @php $isRtl = app(\SabitAhmad\LaravelLaunchpad\Services\LanguageService::class)->isRtl(); @endphp
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4 {{ $isRtl ? 'space-x-reverse' : '' }}">
            <div class="flex items-center">
                <div class="step-active w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium">1</div>
                <span class="ml-2 text-sm font-medium text-gray-900 {{ $isRtl ? 'mr-2 ml-0' : '' }}">{{ __('launchpad::install.steps.welcome') }}</span>
            </div>
            <div class="flex-1 h-1 bg-gray-200 mx-4"></div>
            <div class="flex items-center">
                <div class="step-inactive w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium">2</div>
                <span class="ml-2 text-sm text-gray-500 {{ $isRtl ? 'mr-2 ml-0' : '' }}">{{ __('launchpad::install.steps.requirements') }}</span>
            </div>
            <div class="flex-1 h-1 bg-gray-200 mx-4"></div>
            <div class="flex items-center">
                <div class="step-inactive w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium">3</div>
                <span class="ml-2 text-sm text-gray-500 {{ $isRtl ? 'mr-2 ml-0' : '' }}">{{ __('launchpad::install.steps.license') }}</span>
            </div>
            <div class="flex-1 h-1 bg-gray-200 mx-4"></div>
            <div class="flex items-center">
                <div class="step-inactive w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium">4</div>
                <span class="ml-2 text-sm text-gray-500 {{ $isRtl ? 'mr-2 ml-0' : '' }}">{{ __('launchpad::install.steps.database') }}</span>
            </div>
            <div class="flex-1 h-1 bg-gray-200 mx-4"></div>
            <div class="flex items-center">
                <div class="step-inactive w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium">5</div>
                <span class="ml-2 text-sm text-gray-500 {{ $isRtl ? 'mr-2 ml-0' : '' }}">{{ __('launchpad::install.steps.final') }}</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-lg shadow-sm p-8">
    <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-6">
            <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
        </div>
        
        <h2 class="text-3xl font-bold text-gray-900 mb-4">
            {{ __('launchpad::install.welcome_message') }}
        </h2>
        
        <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
            {{ __('launchpad::install.welcome_description') }}
        </p>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-blue-900 mb-4">{{ __('launchpad::install.getting_started') }}:</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                <div class="flex items-start space-x-3 {{ $isRtl ? 'space-x-reverse' : '' }}">
                    <svg class="h-5 w-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-blue-800">{{ __('launchpad::install.step_descriptions.requirements') }}</span>
                </div>
                <div class="flex items-start space-x-3 {{ $isRtl ? 'space-x-reverse' : '' }}">
                    <svg class="h-5 w-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-blue-800">{{ __('launchpad::install.step_descriptions.license') }}</span>
                </div>
                <div class="flex items-start space-x-3 {{ $isRtl ? 'space-x-reverse' : '' }}">
                    <svg class="h-5 w-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-blue-800">{{ __('launchpad::install.step_descriptions.database') }}</span>
                </div>
                <div class="flex items-start space-x-3 {{ $isRtl ? 'space-x-reverse' : '' }}">
                    <svg class="h-5 w-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-blue-800">{{ __('launchpad::install.step_descriptions.admin') }}</span>
                </div>
            </div>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-8">
            <div class="flex items-start space-x-3 {{ $isRtl ? 'space-x-reverse' : '' }}">
                <svg class="h-5 w-5 text-amber-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <div class="text-left {{ $isRtl ? 'text-right' : '' }}">
                    <h4 class="text-sm font-medium text-amber-800">{{ __('launchpad::install.getting_started') }}:</h4>
                    <p class="text-sm text-amber-700 mt-1">
                        {{ __('launchpad::install.lets_begin') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="flex justify-center">
            <a href="{{ route('launchpad.install.requirements') }}" 
               class="bg-blue-600 text-white px-8 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors duration-200 flex items-center space-x-2 {{ $isRtl ? 'space-x-reverse' : '' }}">
                <span>{{ __('launchpad::common.continue') }}</span>
                <svg class="h-5 w-5 {{ $isRtl ? 'rtl-flip' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </a>
        </div>
    </div>
</div>
@endsection

@php
    $languageService = app(\SabitAhmad\LaravelLaunchpad\Services\LanguageService::class);
    $currentLanguage = $languageService->getCurrentLanguage();
    $isRtl = $languageService->isRtl();
    $direction = $languageService->getLanguageDirection();
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLanguage }}" dir="{{ $direction }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ __('launchpad::common.app_name') }}</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '{{ config("launchpad.ui.primary_color", "#3B82F6") }}'
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        .step-active { @apply bg-blue-600 text-white; }
        .step-completed { @apply bg-green-600 text-white; }
        .step-inactive { @apply bg-gray-300 text-gray-600; }
        
        @if($isRtl)
        /* RTL Support */
        body { direction: rtl; }
        .rtl-flip { transform: scaleX(-1); }
        @endif
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-4xl mx-auto px-4 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4 {{ $isRtl ? 'space-x-reverse' : '' }}">
                        @if(config('launchpad.ui.logo_url'))
                            <img src="{{ config('launchpad.ui.logo_url') }}" alt="Logo" class="h-8">
                        @endif
                        <h1 class="text-2xl font-bold text-gray-900">
                            {{ config('launchpad.ui.app_name', __('launchpad::common.app_name')) }}
                        </h1>
                    </div>
                    <div class="flex items-center space-x-4 {{ $isRtl ? 'space-x-reverse' : '' }}">
                        @include('launchpad::components.language-switcher')
                        <div class="text-sm text-gray-500">
                            @yield('step-indicator', '')
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Progress Bar -->
        @hasSection('progress')
            <div class="bg-white border-b">
                <div class="max-w-4xl mx-auto px-4 py-4">
                    @yield('progress')
                </div>
            </div>
        @endif

        <!-- Main Content -->
        <main class="flex-1 py-6">
            <div class="max-w-4xl mx-auto px-4">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t">
            <div class="max-w-4xl mx-auto px-4 py-6">
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <div>
                        @if(config('launchpad.ui.footer.show_credits', true))
                            Powered by 
                            <a href="{{ config('launchpad.ui.footer.github_url') }}" 
                               target="_blank" 
                               class="text-blue-600 hover:text-blue-800 font-medium">
                                {{ config('launchpad.ui.footer.package_name', 'Laravel Launchpad') }}
                            </a>
                        @endif
                    </div>
                    <div>
                        @yield('footer-content', '')
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Global JavaScript -->
    <script>
        // Global utilities
        window.showNotification = function(message, type = 'info') {
            // Simple notification system
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
            } text-white`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 5000);
        };
    </script>

    @stack('scripts')
</body>
</html>

{{-- Language Switcher Component --}}
@php
    $languageService = app(\SabitAhmad\LaravelLaunchpad\Services\LanguageService::class);
    $currentLanguage = $languageService->getCurrentLanguage();
    $availableLanguages = $languageService->getAvailableLanguages();
    $switcherConfig = config('launchpad.language.switcher', []);
@endphp

@if(config('launchpad.language.switcher.enabled', true) && count($availableLanguages) > 1)
<div class="language-switcher" x-data="languageSwitcher()">
    <div class="relative">
        <button 
            @click="open = !open" 
            class="flex items-center space-x-2 px-3 py-2 text-sm bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            type="button"
        >
            @if(config('launchpad.language.switcher.show_flags', true))
                <span class="text-lg">{{ $availableLanguages[$currentLanguage]['flag'] ?? '🌐' }}</span>
            @endif
            
            <span class="font-medium">
                @if(config('launchpad.language.switcher.show_native_names', true))
                    {{ $availableLanguages[$currentLanguage]['native'] ?? $availableLanguages[$currentLanguage]['name'] }}
                @else
                    {{ $availableLanguages[$currentLanguage]['name'] }}
                @endif
            </span>
            
            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <div 
            x-show="open" 
            @click.away="open = false"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute {{ config('launchpad.language.switcher.position', 'top-right') === 'top-left' ? 'left-0' : 'right-0' }} mt-2 w-48 bg-white border border-gray-300 rounded-md shadow-lg z-50"
            style="display: none;"
        >
            <div class="py-1">
                @foreach($availableLanguages as $langCode => $langInfo)
                    <button 
                        @click="switchLanguage('{{ $langCode }}')"
                        class="flex items-center w-full px-4 py-2 text-sm text-left hover:bg-gray-100 {{ $langCode === $currentLanguage ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}"
                    >
                        @if(config('launchpad.language.switcher.show_flags', true))
                            <span class="mr-3 text-lg">{{ $langInfo['flag'] ?? '🌐' }}</span>
                        @endif
                        
                        <div class="flex flex-col">
                            @if(config('launchpad.language.switcher.show_native_names', true))
                                <span class="font-medium">{{ $langInfo['native'] }}</span>
                                @if($langInfo['native'] !== $langInfo['name'])
                                    <span class="text-xs text-gray-500">{{ $langInfo['name'] }}</span>
                                @endif
                            @else
                                <span class="font-medium">{{ $langInfo['name'] }}</span>
                            @endif
                        </div>
                        
                        @if($langCode === $currentLanguage)
                            <span class="ml-auto">
                                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
function languageSwitcher() {
    return {
        open: false,
        
        switchLanguage(language) {
            // Close dropdown
            this.open = false;
            
            // Show loading state
            const button = this.$el.querySelector('button');
            const originalContent = button.innerHTML;
            button.innerHTML = '<span class="animate-spin">⏳</span> {{ __("launchpad::common.loading") }}';
            button.disabled = true;
            
            // Create form data
            const formData = new FormData();
            formData.append('language', language);
            formData.append('redirect', window.location.href);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            // Submit language change
            fetch('{{ route("launchpad.language.switch") }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload page to apply new language
                    window.location.reload();
                } else {
                    // Restore button and show error
                    button.innerHTML = originalContent;
                    button.disabled = false;
                    alert(data.message || 'Failed to change language');
                }
            })
            .catch(error => {
                // Restore button and show error
                button.innerHTML = originalContent;
                button.disabled = false;
                console.error('Language switch error:', error);
                alert('Failed to change language');
            });
        }
    }
}
</script>
@endif

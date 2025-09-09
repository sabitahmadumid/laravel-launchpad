@extends('launchpad::layout')

@section('title', 'Running Update')

@section('content')
<div x-data="updateRunner()" class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm p-8">
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-6">
                <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
            </div>
            
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Apply Update</h2>
            <p class="text-lg text-gray-600">
                The update will automatically apply all configured options and update to version {{ $newVersion }}.
            </p>
        </div>

        <!-- Version Info -->
        <div class="bg-gray-50 rounded-lg p-6 mb-8 max-w-md mx-auto">
            <div class="flex justify-between items-center">
                <div class="text-left">
                    <div class="text-sm text-gray-500">Current Version</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $currentVersion }}</div>
                </div>
                <div class="text-2xl">→</div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Update To</div>
                    <div class="text-lg font-semibold text-blue-600">{{ $newVersion }}</div>
                </div>
            </div>
        </div>

        <form @submit.prevent="runUpdate()" class="max-w-2xl mx-auto space-y-6">
            
            <!-- Update Options -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                    </svg>
                    Update Configuration
                </h3>
                
                <div class="space-y-4">
                    @foreach($updateOptions as $optionKey => $optionConfig)
                        @if($optionConfig['enabled'] ?? false)
                        <div class="flex items-start space-x-3 p-4 border rounded-lg bg-white">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="h-4 w-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">{{ $optionConfig['description'] }}</div>
                                @if(isset($optionConfig['path']))
                                    <div class="text-sm text-gray-500 mt-1">
                                        File: {{ $optionConfig['path'] }}
                                    </div>
                                @endif
                                <div class="text-sm text-green-600 mt-1">✓ Automatically included</div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
                
                <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                    <div class="flex items-start space-x-3">
                        <svg class="h-5 w-5 text-amber-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-amber-800">Important:</h4>
                            <ul class="text-sm text-amber-700 mt-1 space-y-1">
                                <li>• Always backup your data before running updates</li>
                                <li>• Do not close this browser tab during the update</li>
                                <li>• The update will automatically run all configured options</li>
                                <li>• Update routes will be disabled after successful completion</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update Progress -->
            <div x-show="updating" x-cloak class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <svg class="animate-spin h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <h4 class="text-lg font-semibold text-blue-900">Applying Update...</h4>
                </div>
                
                <div class="space-y-2">
                    <div class="flex items-center space-x-2" :class="{'text-green-600': updateSteps.database, 'text-blue-600': !updateSteps.database}">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" x-show="updateSteps.database">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24" x-show="!updateSteps.database" x-cloak>
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm">Updating database...</span>
                    </div>
                    
                    <div class="flex items-center space-x-2" :class="{'text-green-600': updateSteps.cache, 'text-gray-400': !updateSteps.cache && !updateSteps.database, 'text-blue-600': !updateSteps.cache && updateSteps.database}">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" x-show="updateSteps.cache">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24" x-show="!updateSteps.cache && updateSteps.database" x-cloak>
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm">Clearing cache...</span>
                    </div>
                    
                    <div class="flex items-center space-x-2" :class="{'text-green-600': updateSteps.finalize, 'text-gray-400': !updateSteps.finalize && !updateSteps.cache, 'text-blue-600': !updateSteps.finalize && updateSteps.cache}">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" x-show="updateSteps.finalize">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24" x-show="!updateSteps.finalize && updateSteps.cache" x-cloak>
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm">Finalizing update...</span>
                    </div>
                </div>
            </div>

            <!-- Error Message -->
            <div x-show="errorMessage" x-cloak>
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-start space-x-3">
                        <svg class="h-5 w-5 text-red-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-red-800">Update Failed</h4>
                            <p class="text-sm text-red-700 mt-1" x-text="errorMessage"></p>
                        </div>
                    </div>
                </div>
            </div>

            <button 
                type="submit"
                :disabled="updating"
                class="w-full bg-orange-600 text-white py-4 px-6 rounded-lg font-medium text-lg hover:bg-orange-700 transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed"
                x-show="!updating"
            >
                🚀 Start Automatic Update
            </button>
        </form>

        <!-- Actions -->
        <div class="flex justify-between items-center pt-6 border-t mt-8" x-show="!updating">
            <a href="{{ route('launchpad.update.license') }}" 
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                ← Back
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateRunner() {
    return {
        selectedOptions: {!! json_encode(array_keys(array_filter($updateOptions, function($option) { return $option['enabled'] ?? false; }))) !!}, // Automatically select all enabled options
        updating: false,
        updateSteps: {
            database: false,
            cache: false,
            finalize: false
        },
        errorMessage: '',
        
        async runUpdate() {
            this.updating = true;
            this.errorMessage = '';
            
            // Reset progress
            this.updateSteps = {
                database: false,
                cache: false,
                finalize: false
            };
            
            try {
                const response = await fetch('{{ route("launchpad.update.run") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        update_options: this.selectedOptions
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Simulate progress for better UX
                    setTimeout(() => this.updateSteps.database = true, 1000);
                    setTimeout(() => this.updateSteps.cache = true, 2000);
                    setTimeout(() => this.updateSteps.finalize = true, 3000);
                    
                    setTimeout(() => {
                        window.location.href = '{{ route("launchpad.update.success") }}';
                    }, 3500);
                } else {
                    this.errorMessage = data.message || 'Update failed. Please try again.';
                    this.updating = false;
                    this.showNotification(this.errorMessage, 'error');
                }
            } catch (error) {
                this.errorMessage = 'Network error occurred. Please try again.';
                this.updating = false;
                this.showNotification('Network error occurred', 'error');
            }
        },

        showNotification(message, type = 'info') {
            // Use global showNotification if available, otherwise create a simple fallback
            if (typeof window.showNotification === 'function') {
                window.showNotification(message, type);
            } else {
                // Fallback notification system
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
            }
        }
    }
}
</script>
@endpush
@endsection

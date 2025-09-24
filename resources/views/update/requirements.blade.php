@extends('launchpad::layout')

@section('title', __('launchpad::update.requirements_title'))

@section('content')
<div x-data="updateRequirementsChecker()" class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm p-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ __('launchpad::update.requirements_title') }}</h2>
            <p class="text-lg text-gray-600">
                {{ __('launchpad::update.requirements_description') }}
            </p>
        </div>

        <!-- PHP Version -->
        @if(isset($requirements['php']))
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="h-5 w-5 mr-2 {{ $requirements['php']['meets_minimum'] ? 'text-green-600' : 'text-red-600' }}" fill="currentColor" viewBox="0 0 20 20">
                    @if($requirements['php']['meets_minimum'])
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    @else
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    @endif
                </svg>
                {{ __('launchpad::install.php_version') }}
            </h3>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-sm text-gray-600">{{ __('launchpad::update.current_version') }}:</span>
                        <span class="font-medium">{{ $requirements['php']['current'] }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">{{ __('launchpad::common.required') }}:</span>
                        <span class="font-medium">{{ $requirements['php']['minimum'] }}+</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Required Extensions -->
        @if(isset($requirements['extensions']['required']))
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('launchpad::install.php_extensions') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($requirements['extensions']['required'] as $extension => $loaded)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="font-medium">{{ $extension }}</span>
                    @if($loaded)
                        <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    @else
                        <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Directory Permissions -->
        @if(isset($requirements['directories']))
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('launchpad::install.directory_permissions') }}</h3>
            <div class="space-y-3">
                @foreach($requirements['directories'] as $directory => $status)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <span class="font-medium">{{ $directory }}</span>
                        <div class="text-sm text-gray-500">{{ $status['path'] }}</div>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($status['exists'])
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">{{ __('launchpad::install.exists') }}</span>
                        @else
                            <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">{{ __('launchpad::install.missing') }}</span>
                        @endif
                        
                        @if($status['writable'])
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">{{ __('launchpad::install.writable') }}</span>
                        @else
                            <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">{{ __('launchpad::install.not_writable') }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="flex justify-between items-center pt-6 border-t">
            <a href="{{ route('launchpad.update.welcome') }}" 
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                ← {{ __('launchpad::common.back') }}
            </a>
            
            <div class="flex items-center space-x-4">
                <button @click="checkRequirements()" 
                        :disabled="loading"
                        class="px-6 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors duration-200 disabled:opacity-50">
                    <span x-show="!loading">{{ __('launchpad::update.requirements_recheck') }}</span>
                    <span x-show="loading" x-cloak>{{ __('launchpad::common.checking') }}</span>
                </button>
                
                @if($allMet)
                    <a href="{{ route('launchpad.update.license') }}" 
                       class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        {{ __('launchpad::common.continue') }} →
                    </a>
                @else
                    <button disabled class="px-6 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed">
                        {{ __('launchpad::update.requirements_some_failed') }}
                    </button>
                @endif
            </div>
        </div>

        @if(!$allMet)
        <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-start space-x-3">
                <svg class="h-5 w-5 text-red-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <div>
                    <h4 class="text-sm font-medium text-red-800">{{ __('launchpad::update.requirements_some_failed') }}</h4>
                    <p class="text-sm text-red-700 mt-1">
                        {{ __('launchpad::update.requirements_description') }}
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateRequirementsChecker() {
    return {
        loading: false,
        
        async checkRequirements() {
            this.loading = true;
            
            try {
                const response = await fetch('{{ route("launchpad.update.requirements.check") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showNotification('{{ __('launchpad::update.requirements_check') }}', 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    this.showNotification(data.message, 'warning');
                }
            } catch (error) {
                this.showNotification('{{ __('launchpad::common.error') }}', 'error');
            } finally {
                this.loading = false;
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
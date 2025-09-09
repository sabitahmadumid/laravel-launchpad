@extends('launchpad::layout')

@section('title', 'Final Installation')

@section('content')
<div x-data="finalInstallation()" class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm p-8">
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-6">
                <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
            </div>
            
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Complete Installation</h2>
            <p class="text-lg text-gray-600">
                Great! Your database is configured and admin user is created. Click below to finalize the installation.
            </p>
        </div>

        <form @submit.prevent="completeInstallation()" class="max-w-2xl mx-auto space-y-6">
            
            <!-- Installation Summary -->
            <div class="bg-green-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Installation Progress
                </h3>
                
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-gray-700">System requirements checked</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-gray-700">License verified</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-gray-700">Database configured and set up</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-gray-700">Admin user created</span>
                    </div>
                </div>
            </div>
                            <h4 class="text-sm font-medium text-amber-800">Important Notes:</h4>
                            <ul class="text-sm text-amber-700 mt-1 space-y-1">
                                <li>• If you have existing data, choose carefully to avoid data loss</li>
                                <li>• Dump files will overwrite existing data</li>
                                <li>• Migrations and seeders are recommended for new installations</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Installation Progress -->
            <div x-show="installing" x-cloak class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <svg class="animate-spin h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <h4 class="text-lg font-semibold text-blue-900">Installing Application...</h4>
                </div>
                
                <div class="space-y-2">
                    <div class="flex items-center space-x-2" :class="{'text-green-600': installationSteps.config, 'text-blue-600': !installationSteps.config}">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" x-show="installationSteps.config">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24" x-show="!installationSteps.config" x-cloak>
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm">Updating configuration files...</span>
                    </div>
                    
                    <div class="flex items-center space-x-2" :class="{'text-green-600': installationSteps.database, 'text-gray-400': !installationSteps.database && !installationSteps.config, 'text-blue-600': !installationSteps.database && installationSteps.config}">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" x-show="installationSteps.database">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24" x-show="!installationSteps.database && installationSteps.config" x-cloak>
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm">Setting up database...</span>
                    </div>
                    
                    <div class="flex items-center space-x-2" :class="{'text-green-600': installationSteps.admin, 'text-gray-400': !installationSteps.admin && !installationSteps.database, 'text-blue-600': !installationSteps.admin && installationSteps.database}">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" x-show="installationSteps.admin">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24" x-show="!installationSteps.admin && installationSteps.database" x-cloak>
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm">Creating admin account...</span>
                    </div>
                    
                    <div class="flex items-center space-x-2" :class="{'text-green-600': installationSteps.finalize, 'text-gray-400': !installationSteps.finalize && !installationSteps.admin, 'text-blue-600': !installationSteps.finalize && installationSteps.admin}">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" x-show="installationSteps.finalize">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24" x-show="!installationSteps.finalize && installationSteps.admin" x-cloak>
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm">Finalizing installation...</span>
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
                            <h4 class="text-sm font-medium text-red-800">Installation Failed</h4>
                            <p class="text-sm text-red-700 mt-1" x-text="errorMessage"></p>
                        </div>
                    </div>
                </div>
            </div>

            <button 
                type="submit"
                :disabled="installing"
                class="w-full bg-green-600 text-white py-4 px-6 rounded-lg font-medium text-lg hover:bg-green-700 transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed"
                x-show="!installing"
            >
                🚀 Complete Installation
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function finalInstallation() {
    return {
        installing: false,
        installationSteps: {
            config: false,
            finalize: false
        },
        errorMessage: '',
        
        async completeInstallation() {
            this.installing = true;
            this.errorMessage = '';
            
            // Reset progress
            this.installationSteps = {
                config: false,
                finalize: false
            };
            
            try {
                const response = await fetch('{{ route("launchpad.install.complete") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Simulate progress for better UX
                    setTimeout(() => this.installationSteps.config = true, 500);
                    setTimeout(() => this.installationSteps.database = true, 1500);
                    setTimeout(() => this.installationSteps.admin = true, 2500);
                    setTimeout(() => this.installationSteps.finalize = true, 3500);
                    
                    setTimeout(() => {
                        window.location.href = '{{ route("launchpad.install.success") }}';
                    }, 4000);
                } else {
                    this.errorMessage = data.message || 'Installation failed. Please try again.';
                    this.installing = false;
                    this.showNotification(this.errorMessage, 'error');
                }
            } catch (error) {
                this.errorMessage = 'Network error occurred. Please try again.';
                this.installing = false;
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

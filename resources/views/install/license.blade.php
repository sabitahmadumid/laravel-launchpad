@extends('launchpad::layout')

@section('title', 'License Verification')

@section('step-indicator', 'Step 3 of 5')

@section('progress')
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="flex items-center">
                <div class="step-completed w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium">✓</div>
                <span class="ml-2 text-sm text-gray-500">Welcome</span>
            </div>
            <div class="flex-1 h-1 bg-green-200 mx-4"></div>
            <div class="flex items-center">
                <div class="step-completed w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium">✓</div>
                <span class="ml-2 text-sm text-gray-500">Requirements</span>
            </div>
            <div class="flex-1 h-1 bg-green-200 mx-4"></div>
            <div class="flex items-center">
                <div class="step-active w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium">3</div>
                <span class="ml-2 text-sm font-medium text-gray-900">License</span>
            </div>
            <div class="flex-1 h-1 bg-gray-200 mx-4"></div>
            <div class="flex items-center">
                <div class="step-inactive w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium">4</div>
                <span class="ml-2 text-sm text-gray-500">Database</span>
            </div>
            <div class="flex-1 h-1 bg-gray-200 mx-4"></div>
            <div class="flex items-center">
                <div class="step-inactive w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium">5</div>
                <span class="ml-2 text-sm text-gray-500">Complete</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div x-data="licenseVerifier()" class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm p-8">
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-6">
                <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            
            <h2 class="text-3xl font-bold text-gray-900 mb-4">License Verification</h2>
            <p class="text-lg text-gray-600">
                Please enter your license key to verify your purchase and continue with the installation.
            </p>
        </div>

        <form @submit.prevent="verifyLicense()" class="max-w-md mx-auto space-y-6">
            <div>
                <label for="license_key" class="block text-sm font-medium text-gray-700 mb-2">
                    License Key / Purchase Code
                </label>
                <input 
                    type="text" 
                    id="license_key"
                    x-model="licenseKey"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Enter your license key"
                    required
                    :disabled="loading || verified"
                >
                <p class="text-sm text-gray-500 mt-2">
                    You can find your license key in your purchase confirmation email or download package.
                </p>
            </div>

            <!-- Verification Status -->
            <div x-show="message" x-cloak>
                <div class="p-4 rounded-lg" :class="{
                    'bg-green-50 border border-green-200': verified,
                    'bg-red-50 border border-red-200': !verified && message
                }">
                    <div class="flex items-start space-x-3">
                        <svg x-show="verified" class="h-5 w-5 text-green-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <svg x-show="!verified && message" class="h-5 w-5 text-red-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium" :class="{
                                'text-green-800': verified,
                                'text-red-800': !verified && message
                            }">
                                <span x-show="verified">License Verified!</span>
                                <span x-show="!verified && message">Verification Failed</span>
                            </h4>
                            <p class="text-sm mt-1" :class="{
                                'text-green-700': verified,
                                'text-red-700': !verified && message
                            }" x-text="message"></p>
                        </div>
                    </div>
                </div>
            </div>

            <button 
                type="submit"
                :disabled="loading || !licenseKey.trim() || verified"
                class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed"
            >
                <span x-show="!loading">Verify License</span>
                <span x-show="loading" x-cloak class="flex items-center justify-center space-x-2">
                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Verifying...</span>
                </span>
            </button>
        </form>

        <!-- Help Section -->
        <div class="mt-8 bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Need Help?</h3>
            <div class="space-y-3 text-sm text-gray-600">
                <div class="flex items-start space-x-3">
                    <svg class="h-5 w-5 text-gray-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                    <span>Your license key is typically a long alphanumeric string found in your purchase confirmation.</span>
                </div>
                <div class="flex items-start space-x-3">
                    <svg class="h-5 w-5 text-gray-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                    <span>Make sure you're connected to the internet for license verification.</span>
                </div>
                <div class="flex items-start space-x-3">
                    <svg class="h-5 w-5 text-gray-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                    <span>If you're having trouble, please contact support with your purchase details.</span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-between items-center pt-6 border-t mt-8">
            <a href="{{ route('launchpad.install.requirements') }}" 
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                ← Back
            </a>
            
            <a x-show="verified" 
               href="{{ route('launchpad.install.database') }}" 
               class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
               x-cloak>
                Continue →
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
function licenseVerifier() {
    return {
        licenseKey: '',
        loading: false,
        verified: false,
        message: '',
        
        async verifyLicense() {
            if (!this.licenseKey.trim()) {
                return;
            }
            
            this.loading = true;
            this.message = '';
            
            try {
                const response = await fetch('{{ route("launchpad.install.license.verify") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        license_key: this.licenseKey
                    })
                });
                
                const data = await response.json();
                
                this.verified = data.valid || false;
                this.message = data.message || '';
                
                if (this.verified) {
                    window.showNotification('License verified successfully!', 'success');
                } else {
                    window.showNotification(this.message, 'error');
                }
            } catch (error) {
                this.message = 'Error verifying license. Please try again.';
                window.showNotification('Network error occurred', 'error');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endpush
@endsection

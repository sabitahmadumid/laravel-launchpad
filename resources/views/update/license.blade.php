@extends('launchpad::layout')

@section('title', 'Update License Verification')

@section('content')
<div x-data="updateLicenseVerifier()" class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm p-8">
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-6">
                <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            
            <h2 class="text-3xl font-bold text-gray-900 mb-4">License Verification</h2>
            <p class="text-lg text-gray-600">
                Please verify your license to proceed with the update.
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

        <!-- Actions -->
        <div class="flex justify-between items-center pt-6 border-t mt-8">
            <a href="{{ route('launchpad.update.requirements') }}" 
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                ← Back
            </a>
            
            <a x-show="verified" 
               href="{{ route('launchpad.update.update') }}" 
               class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
               x-cloak>
                Continue →
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateLicenseVerifier() {
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
                const response = await fetch('{{ route("launchpad.update.license.verify") }}', {
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
                    this.showNotification('License verified successfully!', 'success');
                } else {
                    this.showNotification(this.message, 'error');
                }
            } catch (error) {
                this.message = 'Error verifying license. Please try again.';
                this.showNotification('Network error occurred', 'error');
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
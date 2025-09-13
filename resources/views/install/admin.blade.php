@extends('launchpad::layout')

@section('title'            <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ __('launchpad::install.admin_title') }}</h2>
            <p class="text-lg text-gray-600">
                {{ __('launchpad::install.admin_description') }}</p>_('launchpad::install.admin_title'))

@section('step-indicator', __('launchpad::common.step_of', ['current' => 5, 'total' => 5]))

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
                <div class="step-completed w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium">✓</div>
                <span class="ml-2 text-sm text-gray-500">License</span>
            </div>
            <div class="flex-1 h-1 bg-green-200 mx-4"></div>
            <div class="flex items-center">
                <div class="step-completed w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium">✓</div>
                <span class="ml-2 text-sm text-gray-500">Database</span>
            </div>
            <div class="flex-1 h-1 bg-green-200 mx-4"></div>
            <div class="flex items-center">
                <div class="step-active w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium">5</div>
                <span class="ml-2 text-sm font-medium text-gray-900">{{ __('launchpad::install.steps.admin') }}</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div x-data="adminSetup()" class="space-y-4">
    <div class="bg-white rounded-lg shadow-sm p-8">
        <div class="text-center mb-6">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-4">
                <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Admin Account & Settings</h2>
            <p class="text-lg text-gray-600">
                Create your administrator account and configure basic application settings.
            </p>
        </div>

        <form @submit.prevent="saveConfiguration()" class="max-w-4xl mx-auto space-y-6">
            
            <!-- Admin Account Section -->
            @if($adminConfig['enabled'] ?? false)
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    {{ __('launchpad::install.admin_account') }}
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($adminConfig['fields'] ?? [] as $fieldName => $fieldConfig)
                        <div class="{{ in_array($fieldConfig['type'], ['password']) ? 'md:col-span-1' : 'md:col-span-1' }}">
                            <label for="admin_{{ $fieldName }}" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __("launchpad::install.fields.admin.{$fieldName}", [], $fieldConfig['label'] ?? ucwords(str_replace('_', ' ', $fieldName))) }}
                                @if($fieldConfig['required'] ?? false)
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            
                            @if($fieldConfig['type'] === 'select')
                                <select 
                                    id="admin_{{ $fieldName }}"
                                    x-model="formData.{{ $fieldName }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    {{ ($fieldConfig['required'] ?? false) ? 'required' : '' }}
                                >
                                    <option value="">{{ __('launchpad::common.select') }} {{ __("launchpad::install.fields.admin.{$fieldName}", [], $fieldConfig['label'] ?? ucwords(str_replace('_', ' ', $fieldName))) }}</option>
                                    @if(isset($fieldConfig['options']))
                                        @if(is_array($fieldConfig['options']))
                                            @foreach($fieldConfig['options'] as $value => $label)
                                                <option value="{{ $value }}">{{ __("launchpad::install.field_options.{$fieldName}.{$value}", [], $label) }}</option>
                                            @endforeach
                                        @endif
                                    @endif
                                </select>
                            @else
                                <input 
                                    type="{{ $fieldConfig['type'] }}" 
                                    id="admin_{{ $fieldName }}"
                                    x-model="formData.{{ $fieldName }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="{{ __("launchpad::install.field_placeholders.{$fieldName}", [], $fieldConfig['placeholder'] ?? '') }}"
                                    {{ ($fieldConfig['required'] ?? false) ? 'required' : '' }}
                                >
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Additional Settings Sections -->
            @foreach($additionalFields as $groupKey => $groupConfig)
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ __("launchpad::install.field_groups.{$groupKey}", [], $groupConfig['group_label'] ?? ucwords(str_replace('_', ' ', $groupKey))) }}
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($groupConfig['fields'] ?? [] as $fieldName => $fieldConfig)
                        <div class="{{ in_array($fieldConfig['type'], ['url', 'email']) ? 'md:col-span-2' : 'md:col-span-1' }}"
                             x-show="shouldShowField('{{ $fieldName }}', {{ json_encode($fieldConfig) }})"
                             x-transition>
                            <label for="{{ $fieldName }}" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __("launchpad::install.fields.{$groupKey}.{$fieldName}", [], $fieldConfig['label'] ?? ucwords(str_replace('_', ' ', $fieldName))) }}
                                @if($fieldConfig['required'] ?? false)
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            
                            @if($fieldConfig['type'] === 'select')
                                <select 
                                    id="{{ $fieldName }}"
                                    x-model="formData.{{ $fieldName }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    {{ ($fieldConfig['required'] ?? false) ? 'required' : '' }}
                                >
                                    <option value="">{{ __('launchpad::common.select') }} {{ __("launchpad::install.fields.{$groupKey}.{$fieldName}", [], $fieldConfig['label'] ?? ucwords(str_replace('_', ' ', $fieldName))) }}</option>
                                    @if(isset($fieldConfig['options']))
                                        @if(is_array($fieldConfig['options']))
                                            @foreach($fieldConfig['options'] as $value => $label)
                                                <option value="{{ $value }}">{{ __("launchpad::install.field_options.{$fieldName}.{$value}", [], $label) }}</option>
                                            @endforeach
                                        @elseif($fieldConfig['options'] === 'timezones')
                                            @foreach(timezone_identifiers_list() as $timezone)
                                                <option value="{{ $timezone }}">{{ $timezone }}</option>
                                            @endforeach
                                        @endif
                                    @endif
                                </select>
                            @elseif($fieldConfig['type'] === 'textarea')
                                <textarea 
                                    id="{{ $fieldName }}"
                                    x-model="formData.{{ $fieldName }}"
                                    rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="{{ __("launchpad::install.field_placeholders.{$fieldName}", [], $fieldConfig['placeholder'] ?? '') }}"
                                    {{ ($fieldConfig['required'] ?? false) ? 'required' : '' }}
                                ></textarea>
                            @else
                                <input 
                                    type="{{ $fieldConfig['type'] }}" 
                                    id="{{ $fieldName }}"
                                    x-model="formData.{{ $fieldName }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="{{ __("launchpad::install.field_placeholders.{$fieldName}", [], $fieldConfig['placeholder'] ?? '') }}"
                                    {{ ($fieldConfig['required'] ?? false) ? 'required' : '' }}
                                >
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endforeach

            <!-- Form Status -->
            <div x-show="message" x-cloak>
                <div class="p-4 rounded-lg" :class="{
                    'bg-green-50 border border-green-200': success,
                    'bg-red-50 border border-red-200': !success && message
                }">
                    <div class="flex items-start space-x-3">
                        <svg x-show="success" class="h-5 w-5 text-green-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <svg x-show="!success && message" class="h-5 w-5 text-red-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium" :class="{
                                'text-green-800': success,
                                'text-red-800': !success && message
                            }">
                                <span x-show="success">Configuration Saved!</span>
                                <span x-show="!success && message">Validation Failed</span>
                            </h4>
                            <p class="text-sm mt-1" :class="{
                                'text-green-700': success,
                                'text-red-700': !success && message
                            }" x-text="message"></p>
                        </div>
                    </div>
                </div>
            </div>

            <button 
                type="submit"
                :disabled="loading"
                class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed"
            >
                <span x-show="!loading">{{ __('launchpad::install.create_admin') }}</span>
                <span x-show="loading" x-cloak class="flex items-center justify-center space-x-2">
                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>{{ __('launchpad::common.processing') }}</span>
                </span>
            </button>
        </form>

        <!-- Actions -->
        <div class="flex justify-between items-center pt-4 border-t mt-6">
            <a href="{{ route('launchpad.install.database') }}" 
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                ← {{ __('launchpad::common.back') }}
            </a>
            
            <a x-show="success" 
               href="{{ route('launchpad.install.final') }}" 
               class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
               x-cloak>
                {{ __('launchpad::common.continue') }} →
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function adminSetup() {
    return {
        formData: {
            // Admin fields
            @foreach($adminConfig['fields'] ?? [] as $fieldName => $fieldConfig)
            {{ $fieldName }}: '{{ $fieldConfig['default'] ?? '' }}',
            @endforeach
            
            // Additional fields with defaults
            @foreach($additionalFields as $groupKey => $groupConfig)
                @foreach($groupConfig['fields'] ?? [] as $fieldName => $fieldConfig)
                {{ $fieldName }}: '{{ $fieldConfig['default'] ?? '' }}',
                @endforeach
            @endforeach
        },
        loading: false,
        success: false,
        message: '',
        
        shouldShowField(fieldName, fieldConfig) {
            if (!fieldConfig.show_if) {
                return true;
            }
            
            for (const [dependentField, requiredValue] of Object.entries(fieldConfig.show_if)) {
                if (this.formData[dependentField] !== requiredValue) {
                    return false;
                }
            }
            
            return true;
        },
        
        async saveConfiguration() {
            this.loading = true;
            this.message = '';
            
            try {
                const response = await fetch('{{ route("launchpad.install.admin.create") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.formData)
                });
                
                const data = await response.json();
                
                this.success = data.success || false;
                this.message = data.message || '';
                
                if (data.errors) {
                    // Handle validation errors
                    const errorMessages = Object.values(data.errors).flat();
                    this.message = errorMessages.join(', ');
                }
                
                if (this.success) {
                    this.showNotification('Configuration saved successfully!', 'success');
                } else {
                    this.showNotification(this.message, 'error');
                }
            } catch (error) {
                this.message = 'Error saving configuration. Please try again.';
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
        },
        
        init() {
            // Set default values for dynamic fields
            @foreach($additionalFields as $groupKey => $groupConfig)
                @foreach($groupConfig['fields'] ?? [] as $fieldName => $fieldConfig)
                    @if(isset($fieldConfig['default']))
                        @if($fieldConfig['default'] === request()->getSchemeAndHttpHost())
                            this.formData.{{ $fieldName }} = window.location.origin;
                        @endif
                    @endif
                @endforeach
            @endforeach
        }
    }
}
</script>
@endpush
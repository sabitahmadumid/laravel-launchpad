@extends('launchpad::layout')

@section('title', 'Update Requirements')

@section('content')
<div x-data="updateRequirementsChecker()" class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm p-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">System Requirements Check</h2>
            <p class="text-lg text-gray-600">
                Verifying that your server meets the requirements for the update.
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
                PHP Version
            </h3>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-sm text-gray-600">Current Version:</span>
                        <span class="font-medium">{{ $requirements['php']['current'] }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Required:</span>
                        <span class="font-medium">{{ $requirements['php']['minimum'] }}+</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Required Extensions -->
        @if(isset($requirements['extensions']['required']))
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Required PHP Extensions</h3>
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
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Directory Permissions</h3>
            <div class="space-y-3">
                @foreach($requirements['directories'] as $directory => $status)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <span class="font-medium">{{ $directory }}</span>
                        <div class="text-sm text-gray-500">{{ $status['path'] }}</div>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($status['exists'])
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Exists</span>
                        @else
                            <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">Missing</span>
                        @endif
                        
                        @if($status['writable'])
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Writable</span>
                        @else
                            <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">Not Writable</span>
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
                ← Back
            </a>
            
            <div class="flex items-center space-x-4">
                <button @click="checkRequirements()" 
                        :disabled="loading"
                        class="px-6 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors duration-200 disabled:opacity-50">
                    <span x-show="!loading">Re-check</span>
                    <span x-show="loading" x-cloak>Checking...</span>
                </button>
                
                @if($allMet)
                    <a href="{{ route('launchpad.update.license') }}" 
                       class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        Continue →
                    </a>
                @else
                    <button disabled class="px-6 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed">
                        Fix Requirements First
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
                    <h4 class="text-sm font-medium text-red-800">Requirements Not Met</h4>
                    <p class="text-sm text-red-700 mt-1">
                        Please fix the above issues before continuing with the update. Contact your hosting provider if you need assistance.
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

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
                    window.showNotification('Requirements check completed!', 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    window.showNotification(data.message, 'warning');
                }
            } catch (error) {
                window.showNotification('Error checking requirements', 'error');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endpush
@endsection

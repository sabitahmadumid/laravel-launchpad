@extends('launchpad::layout')

@section('title', 'Update Complete')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm p-8">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
                <svg class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            
            <h2 class="text-4xl font-bold text-gray-900 mb-4">🎉 Update Complete!</h2>
            <p class="text-xl text-gray-600 mb-8">
                Congratulations! {{ config('launchpad.ui.app_name', 'Your application') }} has been successfully updated to version {{ $newVersion }}.
            </p>

            <!-- Update Summary -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8 max-w-2xl mx-auto">
                <h3 class="text-lg font-semibold text-green-900 mb-4">What was updated:</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-left">
                    <div class="flex items-center space-x-2">
                        <svg class="h-4 w-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-green-800">Application files updated</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="h-4 w-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-green-800">Database migrated</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="h-4 w-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-green-800">Cache optimized</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="h-4 w-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-green-800">Version updated</span>
                    </div>
                </div>
            </div>

            <!-- New Features/Changes -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8 max-w-2xl mx-auto text-left">
                <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707" />
                    </svg>
                    What's New in v{{ $newVersion }}:
                </h3>
                <div class="space-y-3 text-sm text-blue-800">
                    <div class="flex items-start space-x-3">
                        <span class="bg-blue-200 text-blue-900 rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold mt-0.5">✨</span>
                        <span>Performance improvements and bug fixes</span>
                    </div>
                    <div class="flex items-start space-x-3">
                        <span class="bg-blue-200 text-blue-900 rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold mt-0.5">🔒</span>
                        <span>Enhanced security features</span>
                    </div>
                    <div class="flex items-start space-x-3">
                        <span class="bg-blue-200 text-blue-900 rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold mt-0.5">📱</span>
                        <span>Improved user interface</span>
                    </div>
                    <div class="flex items-start space-x-3">
                        <span class="bg-blue-200 text-blue-900 rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold mt-0.5">⚡</span>
                        <span>New features and functionality</span>
                    </div>
                </div>
            </div>

            <!-- Important Notes -->
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-6 mb-8 max-w-2xl mx-auto text-left">
                <div class="flex items-start space-x-3">
                    <svg class="h-6 w-6 text-amber-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <h4 class="text-sm font-semibold text-amber-800 mb-2">📝 Post-Update Notes</h4>
                        <div class="text-sm text-amber-700 space-y-1">
                            <p>Please take a moment to:</p>
                            <ul class="list-disc list-inside mt-2 space-y-1">
                                <li>Test your application functionality</li>
                                <li>Check that all features are working correctly</li>
                                <li>Review any new settings or configurations</li>
                                <li>Update your documentation if needed</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="/admin" 
                   class="inline-flex items-center px-8 py-4 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-lg">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4" />
                    </svg>
                    Access Admin Panel
                </a>
                
                <a href="/" 
                   class="inline-flex items-center px-8 py-4 border-2 border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Visit Homepage
                </a>
            </div>

            <!-- Support Information -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-500 mb-4">
                    Need help with the new features? Check out our updated documentation.
                </p>
                
                <div class="flex flex-wrap justify-center gap-4 text-sm">
                    <a href="#" class="text-blue-600 hover:text-blue-800 flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Release Notes
                    </a>
                    <a href="#" class="text-blue-600 hover:text-blue-800 flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Documentation
                    </a>
                    <a href="#" class="text-blue-600 hover:text-blue-800 flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Celebration animation for successful update
    function createCelebration() {
        const colors = ['#3B82F6', '#10B981', '#F59E0B', '#8B5CF6'];
        const celebrationCount = 50;
        
        for (let i = 0; i < celebrationCount; i++) {
            setTimeout(() => {
                const particle = document.createElement('div');
                particle.style.position = 'fixed';
                particle.style.left = Math.random() * window.innerWidth + 'px';
                particle.style.top = '-10px';
                particle.style.width = '8px';
                particle.style.height = '8px';
                particle.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                particle.style.borderRadius = '50%';
                particle.style.pointerEvents = 'none';
                particle.style.zIndex = '9999';
                particle.style.animation = 'celebrationFall 3s linear forwards';
                
                document.body.appendChild(particle);
                
                setTimeout(() => {
                    particle.remove();
                }, 3000);
            }, i * 30);
        }
    }
    
    // Add CSS animation for celebration
    const style = document.createElement('style');
    style.textContent = `
        @keyframes celebrationFall {
            0% {
                transform: translateY(-10px) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
    
    // Trigger celebration after a short delay
    setTimeout(createCelebration, 500);
});
</script>
@endpush
@endsection

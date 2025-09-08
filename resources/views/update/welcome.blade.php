@extends('launchpad::layout')

@section('title', 'Update Available')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm p-8">
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-6">
                <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
            </div>
            
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Update Available</h2>
            <p class="text-lg text-gray-600 mb-8">
                A new version of {{ config('launchpad.ui.app_name', 'your application') }} is ready to be installed.
            </p>

            <!-- Version Information -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8 max-w-md mx-auto">
                <div class="flex justify-between items-center">
                    <div class="text-left">
                        <div class="text-sm text-gray-500">Current Version</div>
                        <div class="text-lg font-semibold text-gray-900">{{ $currentVersion }}</div>
                    </div>
                    <div class="text-2xl">→</div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500">New Version</div>
                        <div class="text-lg font-semibold text-blue-600">{{ $newVersion }}</div>
                    </div>
                </div>
            </div>

            <!-- Update Process Overview -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-blue-900 mb-4">Update Process:</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-left">
                    <div class="flex items-start space-x-3">
                        <div class="bg-blue-200 text-blue-900 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold">1</div>
                        <div>
                            <div class="font-medium text-blue-900">System Check</div>
                            <div class="text-sm text-blue-700">Verify requirements</div>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="bg-blue-200 text-blue-900 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold">2</div>
                        <div>
                            <div class="font-medium text-blue-900">License Check</div>
                            <div class="text-sm text-blue-700">Validate license</div>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="bg-blue-200 text-blue-900 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold">3</div>
                        <div>
                            <div class="font-medium text-blue-900">Apply Update</div>
                            <div class="text-sm text-blue-700">Update database & files</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Important Notes -->
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-6 mb-8">
                <div class="flex items-start space-x-3">
                    <svg class="h-5 w-5 text-amber-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <div class="text-left">
                        <h4 class="text-sm font-medium text-amber-800">⚠️ Before You Update:</h4>
                        <ul class="text-sm text-amber-700 mt-2 space-y-1">
                            <li>• <strong>Backup your database and files</strong> before proceeding</li>
                            <li>• Ensure you have your license key ready</li>
                            <li>• The update process may take a few minutes</li>
                            <li>• Do not close this browser tab during the update</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('launchpad.update.requirements') }}" 
                   class="inline-flex items-center px-8 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Start Update Process
                </a>
                
                <a href="/" 
                   class="inline-flex items-center px-8 py-3 border-2 border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancel
                </a>
            </div>

            <!-- Support Information -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-500 mb-4">
                    Need help with the update process? Contact our support team.
                </p>
                
                <div class="flex flex-wrap justify-center gap-4 text-sm">
                    <a href="#" class="text-blue-600 hover:text-blue-800 flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Update Guide
                    </a>
                    <a href="#" class="text-blue-600 hover:text-blue-800 flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Get Support
                    </a>
                    <a href="#" class="text-blue-600 hover:text-blue-800 flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Change Log
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

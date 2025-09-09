@extends('launchpad::layout')

@section('title', 'Database Configuration')

@section('step-indicator', 'Step 4 of 5')

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
                <div class="step-active w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium">4</div>
                <span class="ml-2 text-sm font-medium text-gray-900">Database</span>
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
<div x-data="databaseConfiguration()" class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm p-8">
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-6">
                <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                </svg>
            </div>
            
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Database Configuration</h2>
            <p class="text-lg text-gray-600">
                Configure your database connection settings. We'll test the connection before proceeding.
            </p>
        </div>

        <form @submit.prevent="testConnection()" class="max-w-2xl mx-auto space-y-6">
            <div>
                <label for="connection" class="block text-sm font-medium text-gray-700 mb-2">
                    Database Type
                </label>
                <select 
                    id="connection"
                    x-model="config.connection"
                    @change="resetConnectionStatus()"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required
                >
                    <option value="">Select Database Type</option>
                    @foreach($supportedDrivers as $driver)
                        <option value="{{ $driver }}">
                            {{ ucfirst($driver) }}
                            @if($driver === 'mysql') (MySQL/MariaDB) @endif
                            @if($driver === 'pgsql') (PostgreSQL) @endif
                            @if($driver === 'sqlite') (SQLite) @endif
                            @if($driver === 'sqlsrv') (SQL Server) @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div x-show="config.connection && config.connection !== 'sqlite'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="host" class="block text-sm font-medium text-gray-700 mb-2">
                        Database Host
                    </label>
                    <input 
                        type="text" 
                        id="host"
                        x-model="config.host"
                        @input="resetConnectionStatus()"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="localhost"
                        required
                    >
                </div>

                <div>
                    <label for="port" class="block text-sm font-medium text-gray-700 mb-2">
                        Port
                    </label>
                    <input 
                        type="number" 
                        id="port"
                        x-model="config.port"
                        @input="resetConnectionStatus()"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        :placeholder="getDefaultPort()"
                        required
                    >
                </div>
            </div>

            <div>
                <label for="database" class="block text-sm font-medium text-gray-700 mb-2">
                    <span x-show="config.connection === 'sqlite'">Database File Path</span>
                    <span x-show="config.connection !== 'sqlite'">Database Name</span>
                </label>
                <input 
                    type="text" 
                    id="database"
                    x-model="config.database"
                    @input="resetConnectionStatus()"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    :placeholder="config.connection === 'sqlite' ? '/path/to/database.sqlite' : 'your_database_name'"
                    required
                >
            </div>

            <div x-show="config.connection && config.connection !== 'sqlite'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        Username
                    </label>
                    <input 
                        type="text" 
                        id="username"
                        x-model="config.username"
                        @input="resetConnectionStatus()"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="database_user"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <input 
                        type="password" 
                        id="password"
                        x-model="config.password"
                        @input="resetConnectionStatus()"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="••••••••"
                    >
                </div>
            </div>

            <!-- Connection Status -->
            <div x-show="connectionMessage" x-cloak>
                <div class="p-4 rounded-lg" :class="{
                    'bg-green-50 border border-green-200': connectionSuccess,
                    'bg-red-50 border border-red-200': !connectionSuccess && connectionMessage
                }">
                    <div class="flex items-start space-x-3">
                        <svg x-show="connectionSuccess" class="h-5 w-5 text-green-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <svg x-show="!connectionSuccess && connectionMessage" class="h-5 w-5 text-red-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium" :class="{
                                'text-green-800': connectionSuccess,
                                'text-red-800': !connectionSuccess && connectionMessage
                            }">
                                <!-- Use only connectionMessage for both success and error -->
                                <span x-show="connectionSuccess">Database Connection Successful!</span>
                                <span x-show="!connectionSuccess && connectionMessage">Database Connection Failed</span>
                            </h4>
                            <p class="text-sm mt-1" :class="{
                                'text-green-700': connectionSuccess,
                                'text-red-700': !connectionSuccess && connectionMessage
                            }" x-text="connectionMessage"></p>
                        </div>
                    </div>
                </div>
            </div>

            <button 
                type="submit"
                :disabled="loading || !isFormValid()"
                class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed"
            >
                <span x-show="!loading">Test Database Connection</span>
                <span x-show="loading" x-cloak class="flex items-center justify-center space-x-2">
                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Testing Connection...</span>
                </span>
            </button>
        </form>

        <!-- Database Help -->
        <div class="mt-8 bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Database Setup Tips</h3>
            <div class="space-y-3 text-sm text-gray-600">
                <div class="flex items-start space-x-3">
                    <svg class="h-5 w-5 text-gray-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                    <span>Make sure the database exists before testing the connection.</span>
                </div>
                <div class="flex items-start space-x-3">
                    <svg class="h-5 w-5 text-gray-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                    <span>The database user should have CREATE, ALTER, INSERT, UPDATE, DELETE, and SELECT privileges.</span>
                </div>
                <div class="flex items-start space-x-3">
                    <svg class="h-5 w-5 text-gray-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                    <span>For SQLite, make sure the directory is writable and the file path is absolute.</span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-between items-center pt-6 border-t mt-8">
            <a href="{{ route('launchpad.install.license') }}" 
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                ← Back
            </a>
            
            <a x-show="connectionSuccess" 
               href="{{ route('launchpad.install.admin') }}" 
               class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
               x-cloak>
                Continue →
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
function databaseConfiguration() {
    return {
        config: {
            connection: '',
            host: 'localhost',
            port: '',
            database: '',
            username: '',
            password: ''
        },
        loading: false,
        connectionSuccess: false,
        connectionMessage: '',
        
        getDefaultPort() {
            const ports = {
                mysql: '3306',
                pgsql: '5432',
                sqlsrv: '1433'
            };
            return ports[this.config.connection] || '';
        },
        
        isFormValid() {
            if (!this.config.connection || !this.config.database) {
                return false;
            }
            
            if (this.config.connection === 'sqlite') {
                return true;
            }
            
            return this.config.host && this.config.port;
        },
        
        resetConnectionStatus() {
            this.connectionSuccess = false;
            this.connectionMessage = '';
        },
        
        async testConnection() {
            if (!this.isFormValid()) {
                return;
            }
            
            this.loading = true;
            // Reset connection status before testing
            this.resetConnectionStatus();
            
            try {
                const response = await fetch('{{ route("launchpad.install.database.test") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.config)
                });
                
                const data = await response.json();
                console.log('Database test response:', data); // Debug log
                
                // Handle both success and validation error responses
                if (response.ok && data.success) {
                    this.connectionSuccess = true;
                    this.connectionMessage = data.message || 'Database connection successful!';
                    this.showNotification('Database connection successful!', 'success');
                } else {
                    this.connectionSuccess = false;
                    this.connectionMessage = data.message || 'Database connection failed.';
                    this.showNotification(this.connectionMessage, 'error');
                }
                
            } catch (error) {
                console.error('Database test error:', error);
                this.connectionSuccess = false;
                this.connectionMessage = 'Error testing connection. Please try again.';
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
            // Set default port when connection type changes
            this.$watch('config.connection', (value) => {
                if (value && !this.config.port) {
                    this.config.port = this.getDefaultPort();
                }
                // Reset connection status when connection type changes
                this.resetConnectionStatus();
            });

            // Reset connection status when any config changes
            this.$watch('config', () => {
                this.resetConnectionStatus();
            }, { deep: true });
        }
    }
}
</script>
@endpush
@endsection

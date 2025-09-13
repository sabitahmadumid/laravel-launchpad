@extends('launchpad::layout')

@section('title', __('launchpad::install.database_title'))

@section('step-indicator', __('launchpad::common.step_of', ['current' => 4, 'total' => 5]))

@secti                <span x-show="!loading">{{ __('launchpad::install.test_connection') }}</span>
                <span x-show="loading" x-cloak class="flex items-center justify-center space-x-2">
                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>{{ __('launchpad::install.testing_connection') }}</span>
                </span>ress')
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="step-completed w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium">✓</div>
            <span class="ml-2 text-sm text-gray-500">Welcome</span>
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
                <span class="ml-2 text-sm font-medium text-gray-900">{{ __('launchpad::install.steps.database') }}</span>
            </div>
            <div class="flex-1 h-1 bg-gray-200 mx-4"></div>
            <div class="flex items-center">
                <div class="step-inactive w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium">5</div>
                <span class="ml-2 text-sm text-gray-500">{{ __('launchpad::install.steps.final') }}</span>
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
            
            <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ __('launchpad::install.database_title') }}</h2>
            <p class="text-lg text-gray-600">
                {{ __('launchpad::install.database_description') }} {{ __('launchpad::common.test_connection_note') }}
            </p>
        </div>

        <form @submit.prevent="testConnection()" class="max-w-2xl mx-auto space-y-6">
            <div>
                <label for="connection" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('launchpad::install.database_type') }}
                </label>
                <select 
                    id="connection"
                    x-model="config.connection"
                    @change="resetConnectionStatus()"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required
                >
                    <option value="">{{ __('launchpad::install.select_database_type') }}</option>
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
                        {{ __('launchpad::install.database_host') }}
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
                        {{ __('launchpad::install.database_port') }}
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
                    <span x-show="config.connection === 'sqlite'">{{ __('launchpad::install.database_file_path') }}</span>
                    <span x-show="config.connection !== 'sqlite'">{{ __('launchpad::install.database_name') }}</span>
                </label>
                <input 
                    type="text" 
                    id="database"
                    x-model="config.database"
                    @input="resetConnectionStatus()"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    :placeholder="config.connection === 'sqlite' ? '/path/to/database.sqlite' : '{{ __('launchpad::install.database_name_placeholder') }}'"
                    required
                >
            </div>

            <div x-show="config.connection && config.connection !== 'sqlite'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('launchpad::install.database_username') }}
                    </label>
                    <input 
                        type="text" 
                        id="username"
                        x-model="config.username"
                        @input="resetConnectionStatus()"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="{{ __('launchpad::install.database_username_placeholder') }}"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('launchpad::install.database_password') }}
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
                                <span x-show="connectionSuccess">{{ __('launchpad::install.connection_successful') }}</span>
                                <span x-show="!connectionSuccess && connectionMessage">{{ __('launchpad::install.connection_failed') }}</span>
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

            <!-- Database Setup Section - Only shown after successful connection test -->
            <div x-show="connectionSuccess" x-cloak class="mt-8 pt-8 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('launchpad::install.database_setup_required') }}</h3>
                <p class="text-gray-600 mb-6">
                    {{ __('launchpad::install.setup_steps_description') }}
                </p>

                <div class="space-y-3 mb-6">
                    @if(isset($importOptions['migrations']) && ($importOptions['migrations']['enabled'] ?? false))
                    <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg">
                        <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ __('launchpad::install.database_migrations') }}</div>
                            <div class="text-sm text-gray-500">{{ __('launchpad::install.database_migrations_desc') }}</div>
                        </div>
                    </div>
                    @endif

                    @if(isset($importOptions['seeders']) && ($importOptions['seeders']['enabled'] ?? false))
                    <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg">
                        <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ __('launchpad::install.database_seeders') }}</div>
                            <div class="text-sm text-gray-500">{{ __('launchpad::install.database_seeders_desc') }}</div>
                        </div>
                    </div>
                    @endif

                    @if(isset($importOptions['dump_file']) && ($importOptions['dump_file']['enabled'] ?? false))
                    <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg">
                        <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ __('launchpad::install.import_sql_dump') }}</div>
                            <div class="text-sm text-gray-500">{{ __('launchpad::install.import_sql_dump_desc', ['path' => $importOptions['dump_file']['path'] ?? __('launchpad::install.sql_file')]) }}</div>
                        </div>
                    </div>
                    @endif

                    @if(
                        (!isset($importOptions['migrations']) || !($importOptions['migrations']['enabled'] ?? false)) &&
                        (!isset($importOptions['seeders']) || !($importOptions['seeders']['enabled'] ?? false)) &&
                        (!isset($importOptions['dump_file']) || !($importOptions['dump_file']['enabled'] ?? false))
                    )
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <svg class="h-5 w-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ __('launchpad::install.no_database_setup') }}</div>
                            <div class="text-sm text-gray-500">{{ __('launchpad::install.no_database_setup_desc') }}</div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Setup Progress -->
                <div x-show="setupLoading" x-cloak class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-blue-800">{{ __('launchpad::install.setting_up_database') }}</h4>
                            <p class="text-sm text-blue-700">{{ __('launchpad::install.setting_up_database_desc') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Success Message -->
                <div x-show="setupSuccess" x-cloak class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-green-800">{{ __('launchpad::install.database_setup_complete') }}</h4>
                            <p class="text-sm text-green-700">{{ __('launchpad::install.database_setup_successful') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Database Setup Section -->
        <div x-show="connectionSuccess" x-cloak class="mt-8">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('launchpad::install.setup_database') }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ __('launchpad::install.setup_operations_description') }}</p>
                    </div>
                </div>

                <!-- Setup Options Display -->
                <div class="space-y-3">
                    @if(config('launchpad.import_options.run_migrations', true))
                    <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-800">{{ __('launchpad::install.run_migrations') }}</p>
                            <p class="text-sm text-blue-600">{{ __('launchpad::install.run_migrations_desc') }}</p>
                        </div>
                    </div>
                    @endif

                    @if(config('launchpad.import_options.run_seeders', false))
                    <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-green-800">{{ __('launchpad::install.run_seeders') }}</p>
                            <p class="text-sm text-green-600">{{ __('launchpad::install.run_seeders_desc') }}</p>
                        </div>
                    </div>
                    @endif

                    @if(config('launchpad.import_options.import_dump', false))
                    <div class="flex items-center space-x-3 p-3 bg-purple-50 rounded-lg">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-purple-800">{{ __('launchpad::install.import_database_dump') }}</p>
                            <p class="text-sm text-purple-600">{{ __('launchpad::install.import_database_dump_desc') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Setup Progress -->
                <div x-show="setupLoading" x-cloak class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 818-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 7.962 7.962 0 0 1 4 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-blue-800">{{ __('launchpad::install.setting_up_database') }}</h4>
                            <p class="text-sm text-blue-700">{{ __('launchpad::install.setting_up_database_desc') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Setup Success -->
                <div x-show="setupSuccess" x-cloak class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-green-800">{{ __('launchpad::install.database_setup_complete') }}</h4>
                            <p class="text-sm text-green-700">{{ __('launchpad::install.database_setup_complete_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Database Help -->
                <!-- Database Help -->
        <div class="mt-8 bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('launchpad::install.database_setup_tips') }}</h3>
            <div class="space-y-3 text-sm text-gray-600">
                <div class="flex items-start space-x-3">
                    <svg class="h-5 w-5 text-gray-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ __('launchpad::install.database_exists_tip') }}</span>
                </div>
                <div class="flex items-start space-x-3">
                    <svg class="h-5 w-5 text-gray-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ __('launchpad::install.database_privileges_tip') }}</span>
                </div>
                <div class="flex items-start space-x-3">
                    <svg class="h-5 w-5 text-gray-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>

        <!-- Actions -->
        <div class="flex justify-between items-center pt-6 border-t mt-8">
            <a href="{{ route('launchpad.install.license') }}" 
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                ← {{ __('launchpad::common.back') }}
            </a>
            
            <button x-show="connectionSuccess && !setupSuccess" 
                   @click="handleContinue()"
                   :disabled="setupLoading"
                   class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed"
                   x-cloak>
                <span x-show="!setupLoading">{{ __('launchpad::common.continue') }} →</span>
                <span x-show="setupLoading" x-cloak class="flex items-center space-x-2">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>{{ __('launchpad::install.setting_up') }}</span>
                </span>
            </button>
            
            <a x-show="setupSuccess" 
               href="{{ route('launchpad.install.admin') }}" 
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
        setupLoading: false,
        connectionSuccess: false,
        setupSuccess: false,
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
            this.setupSuccess = false;
            this.connectionMessage = '';
        },
        
        async testConnection() {
            if (!this.isFormValid()) {
                return;
            }
            
            this.loading = true;
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

        async handleContinue() {
            this.setupLoading = true;
            
            try {
                const automaticOptions = [];
                
                @if(isset($importOptions['migrations']) && ($importOptions['migrations']['enabled'] ?? false))
                automaticOptions.push('migrations');
                @endif
                
                @if(isset($importOptions['seeders']) && ($importOptions['seeders']['enabled'] ?? false))
                automaticOptions.push('seeders');
                @endif
                
                @if(isset($importOptions['dump_file']) && ($importOptions['dump_file']['enabled'] ?? false))
                automaticOptions.push('dump_file');
                @endif
                
                const setupResponse = await fetch('{{ route("launchpad.install.database.setup") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        database_options: automaticOptions
                    })
                });
                
                const setupData = await setupResponse.json();
                
                if (!setupResponse.ok || !setupData.success) {
                    this.showNotification(setupData.message || 'Database setup failed.', 'error');
                    return;
                }
                
                // Mark setup as successful
                this.setupSuccess = true;
                this.showNotification('Database configuration and setup completed successfully!', 'success');
                
                // Navigate to admin setup after a brief delay
                setTimeout(() => {
                    window.location.href = '{{ route("launchpad.install.admin") }}';
                }, 1500);
                
            } catch (error) {
                console.error('Database setup error:', error);
                this.showNotification('Error during database setup. Please try again.', 'error');
            } finally {
                this.setupLoading = false;
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
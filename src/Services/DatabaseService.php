<?php

namespace SabitAhmad\LaravelLaunchpad\Services;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseService
{
    public function testConnection(array $config): array
    {
        try {
            $connection = $this->createConnection($config);
            $connection->getPdo();

            return [
                'success' => true,
                'message' => 'Database connection successful!',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Database connection failed: '.$e->getMessage(),
            ];
        }
    }

    public function updateEnvFile(array $config): void
    {
        $envFile = base_path('.env');
        
        if (!file_exists($envFile)) {
            throw new \Exception('.env file not found');
        }

        // Create backup of .env file
        $backupFile = $envFile . '.backup.' . time();
        if (!copy($envFile, $backupFile)) {
            throw new \Exception('Could not create .env backup file');
        }

        try {
            $envContent = File::get($envFile);

            $replacements = [
                'DB_CONNECTION' => $config['connection'],
                'DB_HOST' => $config['host'],
                'DB_PORT' => $config['port'],
                'DB_DATABASE' => $config['database'],
                'DB_USERNAME' => $config['username'],
                'DB_PASSWORD' => $config['password'],
                // Set session driver to file during installation to prevent database dependency
                'SESSION_DRIVER' => 'file',
            ];

            foreach ($replacements as $key => $value) {
                $pattern = "/^{$key}=.*/m";
                $replacement = "{$key}=".(empty($value) ? '""' : '"'.str_replace('"', '\"', $value).'"');

                if (preg_match($pattern, $envContent)) {
                    $envContent = preg_replace($pattern, $replacement, $envContent);
                } else {
                    $envContent .= "\n{$replacement}";
                }
            }

            if (!File::put($envFile, $envContent)) {
                throw new \Exception('Could not write to .env file');
            }

            // Clean up backup file if successful
            @unlink($backupFile);

        } catch (\Exception $e) {
            // Restore backup on failure
            if (file_exists($backupFile)) {
                copy($backupFile, $envFile);
                @unlink($backupFile);
            }
            throw new \Exception('Failed to update .env file: ' . $e->getMessage());
        }
    }

    public function runMigrations(): array
    {
        try {
            // Clear any cached config to ensure fresh database connection
            Artisan::call('config:clear');
            
            // Run migrations with force flag
            Artisan::call('migrate', ['--force' => true]);

            return [
                'success' => true,
                'message' => 'Migrations completed successfully!',
                'output' => Artisan::output(),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Migration failed: '.$e->getMessage(),
            ];
        }
    }

    public function runSeeders(): array
    {
        try {
            // Ensure database connection is fresh
            Artisan::call('config:clear');
            
            // Run seeders with force flag
            Artisan::call('db:seed', ['--force' => true]);

            return [
                'success' => true,
                'message' => 'Seeders completed successfully!',
                'output' => Artisan::output(),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Seeder failed: '.$e->getMessage(),
            ];
        }
    }

    public function importDumpFile(string $filePath): array
    {
        try {
            if (! File::exists($filePath)) {
                return [
                    'success' => false,
                    'message' => 'Dump file not found: '.$filePath,
                ];
            }

            $sql = File::get($filePath);
            DB::unprepared($sql);

            return [
                'success' => true,
                'message' => 'Database dump imported successfully!',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Import failed: '.$e->getMessage(),
            ];
        }
    }

    protected function createConnection(array $config)
    {
        $connectionConfig = [
            'driver' => $config['connection'],
            'host' => $config['host'],
            'port' => $config['port'],
            'database' => $config['database'],
            'username' => $config['username'],
            'password' => $config['password'],
        ];

        if ($config['connection'] === 'sqlite') {
            $connectionConfig = [
                'driver' => 'sqlite',
                'database' => $config['database'],
            ];
        }

        // Create a temporary connection to test with the provided config
        config(['database.connections.temp_test' => $connectionConfig]);
        
        return DB::connection('temp_test');
    }
}

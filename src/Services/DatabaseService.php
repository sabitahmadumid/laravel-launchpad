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
        $envContent = File::get($envFile);

        $replacements = [
            'DB_CONNECTION' => $config['connection'],
            'DB_HOST' => $config['host'],
            'DB_PORT' => $config['port'],
            'DB_DATABASE' => $config['database'],
            'DB_USERNAME' => $config['username'],
            'DB_PASSWORD' => $config['password'],
        ];

        foreach ($replacements as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}=".(empty($value) ? '""' : $value);

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        File::put($envFile, $envContent);
    }

    public function runMigrations(): array
    {
        try {
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

        return DB::connection()->getPdo() ?
            DB::connection() :
            new \Illuminate\Database\Capsule\Manager;
    }
}

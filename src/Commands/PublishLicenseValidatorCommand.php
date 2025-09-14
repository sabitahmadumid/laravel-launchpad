<?php

namespace SabitAhmad\LaravelLaunchpad\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishLicenseValidatorCommand extends Command
{
    protected $signature = 'launchpad:license-stub 
                           {--force : Overwrite existing files}';

    protected $description = 'Publish SimpleLicenseValidator stub for license validation';

    /**
     * @var array{name: string, description: string, file: string, class: string}
     */
    protected array $validator = [
        'name' => 'SimpleLicenseValidator',
        'description' => 'Secure validator with development bypass keys and domain binding',
        'file' => 'SimpleLicenseValidator.php',
        'class' => 'App\\Services\\SimpleLicenseValidator',
    ];

    public function handle(): int
    {
        return $this->publishValidator();
    }

    protected function publishValidator(): int
    {
        $this->info('🚀 Laravel Launchpad License Validator Setup');
        $this->line('');

        $this->info('Publishing SimpleLicenseValidator...');
        $this->line("  <fg=cyan>SimpleLicenseValidator</> - {$this->validator['description']}");
        $this->line('');

        if ($this->publishValidatorFile()) {
            $this->line('');
            $this->info('✅ SimpleLicenseValidator published successfully!');
            $this->showNextSteps();
            return self::SUCCESS;
        }

        return self::FAILURE;
    }

    protected function publishValidatorFile(): bool
    {
        $validator = $this->validator;
        $stubPath = $this->getStubPath($validator['file']);
        $targetPath = $this->getTargetPath($validator['file']);

        if (!File::exists($stubPath)) {
            $this->error("❌ Stub file not found: {$stubPath}");
            return false;
        }

        // Check if target file exists
        if (File::exists($targetPath) && !$this->option('force')) {
            if (!$this->confirm("File {$targetPath} already exists. Overwrite?")) {
                $this->info("⏭️  Skipped {$validator['name']}");
                return false;
            }
        }

        // Create directory if it doesn't exist
        $targetDir = dirname($targetPath);
        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        // Copy the stub file
        if (File::copy($stubPath, $targetPath)) {
            $this->info("✅ Published {$validator['name']} to {$targetPath}");
            return true;
        }

        $this->error("❌ Failed to publish {$validator['name']}");
        return false;
    }

    protected function getStubPath(string $file): string
    {
        return __DIR__ . '/../../database/stubs/' . $file . '.stub';
    }

    protected function getTargetPath(string $file): string
    {
        return app_path('Services/' . $file);
    }

    protected function showNextSteps(): void
    {
        $this->line('');
        $this->info('🎉 Next Steps:');
        $this->line('');
        $this->line('1. Add to your .env file:');
        $this->line('   <fg=yellow>LAUNCHPAD_VALIDATOR_CLASS=App\\Services\\SimpleLicenseValidator</>');
        $this->line('');
        $this->line('2. For development, use license key:');
        $this->line('   <fg=yellow>LAUNCHPAD_LICENSE_KEY=dev-license-key</>');
        $this->line('');
        $this->line('3. Or disable license checks in development:');
        $this->line('   <fg=yellow>php artisan launchpad:license disable</>');
        $this->line('');
        $this->line('4. For production, obtain a valid license key.');
        $this->line('');
        $this->info('📚 The SimpleLicenseValidator includes:');
        $this->line('   • Secure license validation with domain binding');
        $this->line('   • Development bypass keys for local testing');
        $this->line('   • Hash-based security to prevent easy tampering');
        $this->line('   • Admin override capabilities');
        $this->line('');
    }
}

<?php

namespace Rubel\LaravelVueBestCrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SetupAuth extends Command
{
    protected $signature = 'crud-generator:setup-auth {--force : Overwrite existing files}';
    protected $description = 'Setup complete authentication system (Backend + Frontend)';

    public function handle(): int
    {
        $this->info('╔══════════════════════════════════════════════════════════╗');
        $this->info('║                                                          ║');
        $this->info('║   🔐 Setting Up Authentication System                    ║');
        $this->info('║                                                          ║');
        $this->info('╚══════════════════════════════════════════════════════════╝');
        $this->newLine();

        $stubPath = __DIR__ . '/../../stubs';
        $frontendPath = __DIR__ . '/../../stubs-frontend';

        $this->generateBackendAuth($stubPath);
        $this->generateFrontendAuth($frontendPath);
        $this->updateBootstrap();

        $this->newLine();
        $this->info('╔══════════════════════════════════════════════════════════╗');
        $this->info('║                                                          ║');
        $this->info('║   ✅ Authentication System Setup Complete!              ║');
        $this->info('║                                                          ║');
        $this->info('╚══════════════════════════════════════════════════════════╝');

        $this->newLine();
        $this->info('🚀 Next steps:');
        $this->info('   1. php artisan migrate');
        $this->info('   2. npm run dev');
        $this->info('   3. Visit /login to test authentication');
        $this->newLine(2);

        return Command::SUCCESS;
    }

    protected function generateBackendAuth(string $stubPath): void
    {
        $this->info('📦 Generating Backend Auth Files...');

        $files = [
            "{$stubPath}/auth-controller.stub" => app_path('Modules/Auth/Controllers/Api/V1/AuthController.php'),
            "{$stubPath}/auth-user-model.stub" => app_path('Models/User.php'),
            "{$stubPath}/auth-user-resource.stub" => app_path('Modules/Auth/Resources/UserResource.php'),
            "{$stubPath}/auth-routes.stub" => base_path('routes/auth-api.php'),
        ];

        foreach ($files as $stub => $destination) {
            if (!File::exists($stub)) {
                $this->warn("  ⚠️  Missing: {$stub}");
                continue;
            }

            if (File::exists($destination) && !$this->option('force')) {
                $this->warn("  ⏭️  Skipped: " . str_replace(base_path() . '/', '', $destination));
                continue;
            }

            File::ensureDirectoryExists(dirname($destination));
            File::copy($stub, $destination);
            $this->info("  ✅ " . str_replace(base_path() . '/', '', $destination));
        }
    }

    protected function generateFrontendAuth(string $frontendPath): void
    {
        $this->newLine();
        $this->info('📦 Generating Frontend Auth Files...');

        $maps = [
            // Auth Types
            "{$frontendPath}/Types/Auth/auth.ts" => resource_path('js/Types/Auth/auth.ts'),
            
            // Auth Store
            "{$frontendPath}/Stores/Auth/authStore.ts" => resource_path('js/Stores/Auth/authStore.ts'),
            
            // Auth Composable
            "{$frontendPath}/Composables/Auth/useAuth.ts" => resource_path('js/Composables/Auth/useAuth.ts'),
            
            // Auth Pages
            "{$frontendPath}/Pages/Auth/Login.vue" => resource_path('js/Pages/Auth/Login.vue'),
            "{$frontendPath}/Pages/Auth/Register.vue" => resource_path('js/Pages/Auth/Register.vue'),
        ];

        foreach ($maps as $stub => $destination) {
            if (!File::exists($stub)) {
                $this->warn("  ⚠️  Missing: {$stub}");
                continue;
            }

            if (File::exists($destination) && !$this->option('force')) {
                $this->warn("  ⏭️  Skipped: " . str_replace(base_path() . '/', '', $destination));
                continue;
            }

            File::ensureDirectoryExists(dirname($destination));
            File::copy($stub, $destination);
            $this->info("  ✅ " . str_replace(base_path() . '/', '', $destination));
        }
    }

    protected function updateBootstrap(): void
    {
        $this->newLine();
        $this->info('📦 Updating Bootstrap...');

        $bootstrapStub = __DIR__ . '/../../stubs-frontend/bootstrap/app.php';
        $bootstrapDest = base_path('bootstrap/app.php');

        if (File::exists($bootstrapStub)) {
            if (File::exists($bootstrapDest) && !$this->option('force')) {
                $this->warn("  ⏭️  Skipped: bootstrap/app.php (already exists)");
            } else {
                File::ensureDirectoryExists(dirname($bootstrapDest));
                File::copy($bootstrapStub, $bootstrapDest);
                $this->info("  ✅ bootstrap/app.php");
            }
        }

        // Update routes/api.php to include auth routes
        $this->newLine();
        $this->info('📌 Add this to routes/api.php:');
        $this->info('');
        $this->info("require __DIR__.'/auth-api.php';");
        $this->info('');
    }
}

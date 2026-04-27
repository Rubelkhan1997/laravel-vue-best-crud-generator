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
        $this->updateRoutes();
        $this->mergeBootstrap();

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

        $backendStubPath = __DIR__ . '/../../stubs-backend';
        
        $files = [
            "{$stubPath}/auth-controller.stub" => app_path('Modules/Auth/Controllers/Api/V1/AuthController.php'),
            "{$stubPath}/auth-user-model.stub" => app_path('Models/User.php'),
            "{$stubPath}/auth-user-resource.stub" => app_path('Modules/Auth/Resources/UserResource.php'),
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

    protected function updateRoutes(): void
    {
        $this->newLine();
        $this->info('📦 Merging Authentication Routes...');

        // Merge API routes into routes/api.php
        $this->mergeApiRoutes();

        // Merge Web routes into routes/web.php
        $this->mergeWebRoutes();
    }

    /**
     * Merge auth API routes directly into routes/api.php
     */
    protected function mergeApiRoutes(): void
    {
        $apiPath = base_path('routes/api.php');
        
        // API routes code to inject
        $apiRoutes = <<<'PHP'

// ─────────────────────────────────────────────────────────
// Authentication API Routes
// ─────────────────────────────────────────────────────────
use App\Modules\Auth\Controllers\Api\V1\AuthController;

Route::prefix('v1/auth')->group(function () {
    // Public routes
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    });
});
PHP;

        $this->mergeCodeIntoFile($apiPath, $apiRoutes, 'Authentication API routes');
    }

    /**
     * Merge auth web routes directly into routes/web.php
     */
    protected function mergeWebRoutes(): void
    {
        $webPath = base_path('routes/web.php');
        
        // Web routes code to inject
        $webRoutes = <<<'PHP'

// ─────────────────────────────────────────────────────────
// Authentication Web Routes (Inertia)
// ─────────────────────────────────────────────────────────
use Inertia\Inertia;

// Dashboard & Home Routes
Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::middleware(['auth.token'])->get('/dashboard', function () {
    return Inertia::render('Dashboard/Index');
})->name('dashboard');

// Auth Pages (only for guests)
Route::middleware(['guest'])->group(function (): void {
    Route::get('/login', function () {
        return Inertia::render('Auth/Login');
    })->name('login');
    
    Route::get('/register', function () {
        return Inertia::render('Auth/Register');
    })->name('register');
});
PHP;

        $this->mergeCodeIntoFile($webPath, $webRoutes, 'Authentication web routes');
    }

    /**
     * Smart merge: inject code into file only if not already present
     */
    protected function mergeCodeIntoFile(string $filePath, string $code, string $description): void
    {
        // Create file if it doesn't exist
        if (!File::exists($filePath)) {
            $isApi = strpos($filePath, 'api.php') !== false;
            $stubPath = __DIR__ . '/../../stubs';
            
            if ($isApi && File::exists("{$stubPath}/auth-routes.stub")) {
                // Use stub if exists
                $content = File::get("{$stubPath}/auth-routes.stub");
            } else {
                // Create basic route file
                $content = "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n";
            }
            
            $content .= "\n{$code}\n";
            File::put($filePath, $content);
            $this->info("  ✅ Created {$filePath} with {$description}");
            return;
        }

        $content = File::get($filePath);
        
        // Check if code already exists (look for key identifiers)
        $alreadyExists = strpos($content, 'auth.login') !== false 
            || strpos($content, 'AuthController') !== false
            || strpos($content, 'Authentication API Routes') !== false
            || strpos($content, 'Authentication web routes') !== false;

        if ($alreadyExists) {
            $this->info("  ℹ️  {$description} already exist in {$filePath}");
            return;
        }

        // Append at the end
        File::append($filePath, "{$code}\n");
        $this->info("  ✅ Merged {$description} into {$filePath}");
    }

    protected function mergeBootstrap(): void
    {
        $this->newLine();
        $this->info('📦 Merging Bootstrap Configuration...');

        $bootstrapPath = base_path('bootstrap/app.php');

        if (!File::exists($bootstrapPath)) {
            $this->warn('  ⚠️  bootstrap/app.php not found. Skipping bootstrap merge.');
            $this->info('  💡 Run: php artisan crud-generator:publish-assets --force');
            return;
        }

        $content = File::get($bootstrapPath);
        $changes = [];

        // 1. Add SecurityHeaders middleware
        if (strpos($content, 'SecurityHeaders') === false) {
            $content = str_replace(
                '->withMiddleware(function (Middleware $middleware): void {',
                "->withMiddleware(function (Middleware \$middleware): void {\n        // Security headers\n        \$middleware->append(\\App\\Http\\Middleware\\SecurityHeaders::class);",
                $content
            );
            $changes[] = 'Added SecurityHeaders middleware';
        }

        // 2. Add middleware aliases
        if (strpos($content, 'auth.token') === false) {
            $content = preg_replace(
                '/(->withMiddleware\(function\s*\(Middleware\s*\$middleware\):\s*void\s*\{)/',
                "$1\n        // Middleware aliases\n        \$middleware->alias([\n            'auth.token' => \\App\\Modules\\Auth\\Middleware\\AuthenticateByToken::class,\n            'permission' => \\Spatie\\Permission\\Middleware\\PermissionMiddleware::class,\n            'role' => \\Spatie\\Permission\\Middleware\\RoleMiddleware::class,\n            'role_or_permission' => \\Spatie\\Permission\\Middleware\\RoleOrPermissionMiddleware::class,\n        ]);",
                $content
            );
            $changes[] = 'Added middleware aliases (auth.token, permission, role)';
        }

        // 3. Add HandleInertiaRequests to web middleware
        if (strpos($content, 'HandleInertiaRequests') === false) {
            if (strpos($content, "->middleware(") !== false) {
                $content = str_replace(
                    "->middleware(",
                    "// Inertia middleware for sharing props\n        \$middleware->web(append: [\n            \\App\\Http\\Middleware\\HandleInertiaRequests::class,\n        ]);\n\n        \$middleware->web(append: [",
                    $content
                );
                $changes[] = 'Added HandleInertiaRequests to web middleware';
            }
        }

        // 4. Add encryptCookies for auth_token
        if (strpos($content, "encryptCookies(['auth_token'])") === false) {
            $content = str_replace(
                "->withMiddleware(function (Middleware \$middleware): void {",
                "->withMiddleware(function (Middleware \$middleware): void {\n        // Encrypt auth token cookie\n        \$middleware->encryptCookies(['auth_token']);",
                $content
            );
            $changes[] = 'Added encryptCookies for auth_token';
        }

        if (empty($changes)) {
            $this->info('  ℹ️  Bootstrap configuration is already up to date!');
            return;
        }

        // Write updated content
        File::put($bootstrapPath, $content);

        foreach ($changes as $change) {
            $this->info("  ✅ {$change}");
        }
    }
}

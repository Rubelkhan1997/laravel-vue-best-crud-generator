<?php

namespace Rubel\LaravelVueBestCrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MergeBootstrap extends Command
{
    protected $signature = 'crud-generator:merge-bootstrap {--force : Force merge without confirmation}';
    protected $description = 'Merge CRUD generator bootstrap configuration into existing bootstrap/app.php';

    public function handle(): int
    {
        $this->info('╔══════════════════════════════════════════════════════════╗');
        $this->info('║                                                          ║');
        $this->info('║   🔧 Merging Bootstrap Configuration                    ║');
        $this->info('║                                                          ║');
        $this->info('╚══════════════════════════════════════════════════════════╝');
        $this->newLine();

        $bootstrapPath = base_path('bootstrap/app.php');

        if (!File::exists($bootstrapPath)) {
            $this->error('❌ bootstrap/app.php not found in your project!');
            return Command::FAILURE;
        }

        $content = File::get($bootstrapPath);
        $changes = [];

        // 1. Add SecurityHeaders middleware
        if (strpos($content, 'SecurityHeaders') === false) {
            $content = str_replace(
                '->withMiddleware(function (Middleware $middleware): void {',
                "->withMiddleware(function (Middleware $middleware): void {\n        // Security headers\n        \$middleware->append(\\App\\Http\\Middleware\\SecurityHeaders::class);",
                $content
            );
            $changes[] = '✅ Added SecurityHeaders middleware';
        }

        // 2. Add middleware aliases
        if (strpos($content, 'auth.token') === false) {
            $content = preg_replace(
                '/->withMiddleware\(function\s*\(Middleware\s*\$middleware\):\s*void\s*\{/',
                "->withMiddleware(function (Middleware \$middleware): void {\n        // Middleware aliases\n        \$middleware->alias([\n            'auth.token' => \\App\\Modules\\Auth\\Middleware\\AuthenticateByToken::class,\n            'permission' => \\Spatie\\Permission\\Middleware\\PermissionMiddleware::class,\n            'role' => \\Spatie\\Permission\\Middleware\\RoleMiddleware::class,\n            'role_or_permission' => \\Spatie\\Permission\\Middleware\\RoleOrPermissionMiddleware::class,\n        ]);",
                $content
            );
            $changes[] = '✅ Added middleware aliases (auth.token, permission, role)';
        }

        // 3. Add HandleInertiaRequests to web middleware
        if (strpos($content, 'HandleInertiaRequests') === false) {
            $content = str_replace(
                '// API routes',
                "// Inertia middleware for sharing props\n        \$middleware->web(append: [\n            \\App\\Http\\Middleware\\HandleInertiaRequests::class,\n        ]);\n\n        // API routes",
                $content
            );
            $changes[] = '✅ Added HandleInertiaRequests to web middleware';
        }

        // 4. Add encryptCookies for auth_token
        if (strpos($content, "encryptCookies(['auth_token'])") === false) {
            $content = str_replace(
                "->withMiddleware(function (Middleware \$middleware): void {",
                "->withMiddleware(function (Middleware \$middleware): void {\n        // Encrypt auth token cookie\n        \$middleware->encryptCookies(['auth_token']);",
                $content
            );
            $changes[] = '✅ Added encryptCookies for auth_token';
        }

        if (empty($changes)) {
            $this->info('ℹ️  Bootstrap configuration is already up to date!');
            return Command::SUCCESS;
        }

        // Write updated content
        File::put($bootstrapPath, $content);

        $this->info('╔══════════════════════════════════════════════════════════╗');
        $this->info('║                                                          ║');
        $this->info('║   ✅ Bootstrap Merge Complete!                          ║');
        $this->info('║                                                          ║');
        $this->info('╚══════════════════════════════════════════════════════════╝');
        $this->newLine();

        foreach ($changes as $change) {
            $this->info("  {$change}");
        }

        $this->newLine();
        $this->info('💡 Next: Make sure required middleware files exist:');
        $this->info('   - app/Http/Middleware/SecurityHeaders.php');
        $this->info('   - app/Http/Middleware/HandleInertiaRequests.php');
        $this->info('   - app/Modules/Auth/Middleware/AuthenticateByToken.php');
        $this->newLine(2);

        return Command::SUCCESS;
    }
}

<?php

namespace Rubel\LaravelVueBestCrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PublishFrontendAssets extends Command
{
    protected $signature = 'crud-generator:publish-assets {--force : Overwrite existing files}';
    protected $description = 'Publish default frontend and backend assets';

    public function handle(): int
    {
        $this->info('Publishing assets...');

        $stubPath = __DIR__ . '/../../stubs-frontend';
        $backendStubPath = __DIR__ . '/../../stubs-backend';
        $targetPath = resource_path('js');

        $maps = [
            // Components
            "{$stubPath}/Components/index.ts" => "{$targetPath}/Components/index.ts",
            "{$stubPath}/Components/Table.vue" => "{$targetPath}/Components/Table.vue",
            "{$stubPath}/Components/Modal.vue" => "{$targetPath}/Components/Modal.vue",
            "{$stubPath}/Components/LanguageSwitcher.vue" => "{$targetPath}/Components/LanguageSwitcher.vue",
            "{$stubPath}/Components/Form/index.ts" => "{$targetPath}/Components/Form/index.ts",
            "{$stubPath}/Components/Form/FormInput.vue" => "{$targetPath}/Components/Form/FormInput.vue",
            "{$stubPath}/Components/Form/FormSelect.vue" => "{$targetPath}/Components/Form/FormSelect.vue",
            "{$stubPath}/Components/Form/FormButton.vue" => "{$targetPath}/Components/Form/FormButton.vue",
            "{$stubPath}/Components/Form/FormTextarea.vue" => "{$targetPath}/Components/Form/FormTextarea.vue",
            "{$stubPath}/Components/Form/FormRadio.vue" => "{$targetPath}/Components/Form/FormRadio.vue",
            "{$stubPath}/Components/Form/DatePicker.vue" => "{$targetPath}/Components/Form/DatePicker.vue",
            "{$stubPath}/Components/Form/TimePicker.vue" => "{$targetPath}/Components/Form/TimePicker.vue",
            "{$stubPath}/Components/Form/FileUpload.vue" => "{$targetPath}/Components/Form/FileUpload.vue",

            // Composables
            "{$stubPath}/Composables/useLoading.ts" => "{$targetPath}/Composables/useLoading.ts",
            "{$stubPath}/Composables/usePolling.ts" => "{$targetPath}/Composables/usePolling.ts",
            "{$stubPath}/Composables/usePermissionService.ts" => "{$targetPath}/Composables/usePermissionService.ts",
            "{$stubPath}/Composables/useMessage.ts" => "{$targetPath}/Composables/useMessage.ts",
            "{$stubPath}/Composables/useI18n.ts" => "{$targetPath}/Composables/useI18n.ts",
            "{$stubPath}/Composables/index.ts" => "{$targetPath}/Composables/index.ts",

            // Helpers
            "{$stubPath}/Helpers/error.ts" => "{$targetPath}/Helpers/error.ts",
            "{$stubPath}/Helpers/auth.ts" => "{$targetPath}/Helpers/auth.ts",
            "{$stubPath}/Helpers/index.ts" => "{$targetPath}/Helpers/index.ts",

            // Layouts
            "{$stubPath}/Layouts/AppLayout.vue" => "{$targetPath}/Layouts/AppLayout.vue",

            // Locales
            "{$stubPath}/Locales/index.ts" => "{$targetPath}/Locales/index.ts",
            "{$stubPath}/Locales/en.ts" => "{$targetPath}/Locales/en.ts",
            "{$stubPath}/Locales/bn.ts" => "{$targetPath}/Locales/bn.ts",
            "{$stubPath}/Locales/fr.ts" => "{$targetPath}/Locales/fr.ts",
            "{$stubPath}/Locales/ar.ts" => "{$targetPath}/Locales/ar.ts",

            // Plugins
            "{$stubPath}/Plugins/toast.ts" => "{$targetPath}/Plugins/toast.ts",
            "{$stubPath}/Plugins/confirm.ts" => "{$targetPath}/Plugins/confirm.ts",
            "{$stubPath}/Plugins/index.ts" => "{$targetPath}/Plugins/index.ts",
            "{$stubPath}/Plugins/directives/index.ts" => "{$targetPath}/Plugins/directives/index.ts",
            "{$stubPath}/Plugins/directives/permission.ts" => "{$targetPath}/Plugins/directives/permission.ts",
            "{$stubPath}/Plugins/directives/focus.ts" => "{$targetPath}/Plugins/directives/focus.ts",
            "{$stubPath}/Plugins/directives/clickOutside.ts" => "{$targetPath}/Plugins/directives/clickOutside.ts",

            // Services
            "{$stubPath}/Services/apiClient.ts" => "{$targetPath}/Services/apiClient.ts",
            "{$stubPath}/Services/index.ts" => "{$targetPath}/Services/index.ts",

            // Stores
            "{$stubPath}/Stores/index.ts" => "{$targetPath}/Stores/index.ts",
            "{$stubPath}/Stores/languageStore.ts" => "{$targetPath}/Stores/languageStore.ts",

            // Styles
            "{$stubPath}/Styles/index.css" => "{$targetPath}/Styles/index.css",
            "{$stubPath}/Styles/main.css" => "{$targetPath}/Styles/main.css",
            "{$stubPath}/Styles/mixins.scss" => "{$targetPath}/Styles/mixins.scss",
            "{$stubPath}/Styles/variables.scss" => "{$targetPath}/Styles/variables.scss",

            // Types
            "{$stubPath}/Types/api.ts" => "{$targetPath}/Types/api.ts",
            "{$stubPath}/Types/common.ts" => "{$targetPath}/Types/common.ts",
            "{$stubPath}/Types/index.ts" => "{$targetPath}/Types/index.ts",
            "{$stubPath}/Types/global.d.ts" => "{$targetPath}/Types/global.d.ts",

            // Utils
            "{$stubPath}/Utils/format.ts" => "{$targetPath}/Utils/format.ts",
            "{$stubPath}/Utils/date.ts" => "{$targetPath}/Utils/date.ts",
            "{$stubPath}/Utils/validation.ts" => "{$targetPath}/Utils/validation.ts",
            "{$stubPath}/Utils/storage.ts" => "{$targetPath}/Utils/storage.ts",
            "{$stubPath}/Utils/authToken.ts" => "{$targetPath}/Utils/authToken.ts",
            "{$stubPath}/Utils/constants.ts" => "{$targetPath}/Utils/constants.ts",
            "{$stubPath}/Utils/rtl.ts" => "{$targetPath}/Utils/rtl.ts",
            "{$stubPath}/Utils/index.ts" => "{$targetPath}/Utils/index.ts",

            // Entry point
            "{$stubPath}/app.ts" => "{$targetPath}/app.ts",
            "{$stubPath}/bootstrap.ts" => "{$targetPath}/bootstrap.ts",

            // TypeScript Configuration
            "{$stubPath}/tsconfig.json" => base_path('tsconfig.json'),
            "{$stubPath}/env.d.ts" => "{$targetPath}/env.d.ts",

            // Vite Configuration
            "{$stubPath}/vite.config.js" => base_path('vite.config.js'),

            // Backend view
            "{$backendStubPath}/Views/app.blade.php" => resource_path('views/app.blade.php'),

            // Backend middleware
            "{$backendStubPath}/Middleware/SecurityHeaders.php" => app_path('Http/Middleware/SecurityHeaders.php'),
            "{$backendStubPath}/Middleware/HandleInertiaRequests.php" => app_path('Http/Middleware/HandleInertiaRequests.php'),
            "{$backendStubPath}/Middleware/AuthenticateByToken.php" => app_path('Modules/Auth/Middleware/AuthenticateByToken.php'),
        ];

        $published = 0;
        $skipped = 0;

        foreach ($maps as $stub => $destination) {
            if (!File::exists($stub)) {
                $this->warn("Missing stub: {$stub}");
                continue;
            }

            if (File::exists($destination) && !$this->option('force')) {
                $skipped++;
                continue;
            }

            File::ensureDirectoryExists(dirname($destination));
            File::copy($stub, $destination);
            $published++;

            $relativeDest = str_replace(base_path() . '/', '', $destination);
            $this->info("  Published: {$relativeDest}");
        }

        $this->mergeBootstrapConfiguration($backendStubPath);

        $this->newLine();
        $this->info("Published: {$published} files");
        $this->info("Skipped: {$skipped} files (use --force to overwrite)");

        return Command::SUCCESS;
    }

    protected function mergeBootstrapConfiguration(string $backendStubPath): void
    {
        $bootstrapPath = base_path('bootstrap/app.php');
        $bootstrapStubPath = "{$backendStubPath}/bootstrap-app.php";

        if (!File::exists($bootstrapPath)) {
            if (File::exists($bootstrapStubPath)) {
                File::ensureDirectoryExists(dirname($bootstrapPath));
                File::copy($bootstrapStubPath, $bootstrapPath);
                $this->info('  bootstrap/app.php created from stub');
            } else {
                $this->warn('  bootstrap/app.php missing and bootstrap stub not found');
            }

            return;
        }

        $content = File::get($bootstrapPath);
        $updated = $content;
        $changes = [];

        if (!Str::contains($updated, "encryptCookies(['auth_token'])")) {
            $updated = str_replace(
                '->withMiddleware(function (Middleware $middleware): void {',
                "->withMiddleware(function (Middleware \$middleware): void {\n        // Encrypt auth token cookie\n        \$middleware->encryptCookies(['auth_token']);",
                $updated
            );
            $changes[] = 'Added encryptCookies for auth_token';
        }

        if (!Str::contains($updated, 'SecurityHeaders::class')) {
            $updated = str_replace(
                '->withMiddleware(function (Middleware $middleware): void {',
                "->withMiddleware(function (Middleware \$middleware): void {\n        // Security headers\n        \$middleware->append(\\App\\Http\\Middleware\\SecurityHeaders::class);",
                $updated
            );
            $changes[] = 'Added SecurityHeaders middleware';
        }

        if (!Str::contains($updated, "'auth.token'")) {
            $updated = preg_replace(
                '/(->withMiddleware\(function\s*\(Middleware\s*\$middleware\):\s*void\s*\{)/',
                "$1\n        // Middleware aliases\n        \$middleware->alias([\n            'auth.token' => \\App\\Modules\\Auth\\Middleware\\AuthenticateByToken::class,\n            'permission' => \\Spatie\\Permission\\Middleware\\PermissionMiddleware::class,\n            'role' => \\Spatie\\Permission\\Middleware\\RoleMiddleware::class,\n            'role_or_permission' => \\Spatie\\Permission\\Middleware\\RoleOrPermissionMiddleware::class,\n        ]);",
                $updated
            );
            $changes[] = 'Added middleware aliases';
        }

        if (!Str::contains($updated, 'HandleInertiaRequests::class')) {
            $updated = preg_replace(
                '/(->withMiddleware\(function\s*\(Middleware\s*\$middleware\):\s*void\s*\{)/',
                "$1\n        // Inertia middleware for sharing props\n        \$middleware->web(append: [\n            \\App\\Http\\Middleware\\HandleInertiaRequests::class,\n        ]);",
                $updated
            );
            $changes[] = 'Added HandleInertiaRequests middleware';
        }

        if ($updated !== $content) {
            File::put($bootstrapPath, $updated);
            foreach ($changes as $change) {
                $this->info("  {$change}");
            }
        } else {
            $this->info('  bootstrap/app.php already configured');
        }
    }
}

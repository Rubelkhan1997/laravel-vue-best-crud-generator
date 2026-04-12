<?php

namespace Rubel\LaravelVueBestCrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishFrontendAssets extends Command
{
    protected $signature = 'crud-generator:publish-assets {--force : Overwrite existing files}';
    protected $description = 'Publish default frontend assets to resources/js/';

    public function handle(): int
    {
        $this->info('╔══════════════════════════════════════════════════════════╗');
        $this->info('║                                                          ║');
        $this->info('║   📦 Publishing Frontend Assets                         ║');
        $this->info('║                                                          ║');
        $this->info('╚══════════════════════════════════════════════════════════╝');
        $this->newLine();

        $stubPath = __DIR__ . '/../../stubs-frontend';
        $targetPath = resource_path('js');

        $maps = [
            // Helpers
            "{$stubPath}/Helpers/error.ts" => "{$targetPath}/Helpers/error.ts",
            "{$stubPath}/Helpers/auth.ts" => "{$targetPath}/Helpers/auth.ts",
            "{$stubPath}/Helpers/index.ts" => "{$targetPath}/Helpers/index.ts",
            
            // Services
            "{$stubPath}/Services/apiClient.ts" => "{$targetPath}/Services/apiClient.ts",
            "{$stubPath}/Services/index.ts" => "{$targetPath}/Services/index.ts",
            
            // Composables
            "{$stubPath}/Composables/useLoading.ts" => "{$targetPath}/Composables/useLoading.ts",
            "{$stubPath}/Composables/usePolling.ts" => "{$targetPath}/Composables/usePolling.ts",
            "{$stubPath}/Composables/usePermissionService.ts" => "{$targetPath}/Composables/usePermissionService.ts",
            "{$stubPath}/Composables/useMessage.ts" => "{$targetPath}/Composables/useMessage.ts",
            "{$stubPath}/Composables/useI18n.ts" => "{$targetPath}/Composables/useI18n.ts",
            "{$stubPath}/Composables/index.ts" => "{$targetPath}/Composables/index.ts",
            "{$stubPath}/Composables/Auth/useAuth.ts" => "{$targetPath}/Composables/Auth/useAuth.ts",
            
            // Plugins
            "{$stubPath}/Plugins/toast.ts" => "{$targetPath}/Plugins/toast.ts",
            "{$stubPath}/Plugins/confirm.ts" => "{$targetPath}/Plugins/confirm.ts",
            "{$stubPath}/Plugins/index.ts" => "{$targetPath}/Plugins/index.ts",
            "{$stubPath}/Plugins/directives/index.ts" => "{$targetPath}/Plugins/directives/index.ts",
            "{$stubPath}/Plugins/directives/permission.ts" => "{$targetPath}/Plugins/directives/permission.ts",
            "{$stubPath}/Plugins/directives/focus.ts" => "{$targetPath}/Plugins/directives/focus.ts",
            "{$stubPath}/Plugins/directives/clickOutside.ts" => "{$targetPath}/Plugins/directives/clickOutside.ts",
            
            // Utils
            "{$stubPath}/Utils/format.ts" => "{$targetPath}/Utils/format.ts",
            "{$stubPath}/Utils/date.ts" => "{$targetPath}/Utils/date.ts",
            "{$stubPath}/Utils/validation.ts" => "{$targetPath}/Utils/validation.ts",
            "{$stubPath}/Utils/storage.ts" => "{$targetPath}/Utils/storage.ts",
            "{$stubPath}/Utils/authToken.ts" => "{$targetPath}/Utils/authToken.ts",
            "{$stubPath}/Utils/constants.ts" => "{$targetPath}/Utils/constants.ts",
            "{$stubPath}/Utils/rtl.ts" => "{$targetPath}/Utils/rtl.ts",
            "{$stubPath}/Utils/index.ts" => "{$targetPath}/Utils/index.ts",
            
            // Locales
            "{$stubPath}/Locales/index.ts" => "{$targetPath}/Locales/index.ts",
            "{$stubPath}/Locales/en.ts" => "{$targetPath}/Locales/en.ts",
            "{$stubPath}/Locales/bn.ts" => "{$targetPath}/Locales/bn.ts",
            "{$stubPath}/Locales/fr.ts" => "{$targetPath}/Locales/fr.ts",
            "{$stubPath}/Locales/ar.ts" => "{$targetPath}/Locales/ar.ts",
            
            // Types
            "{$stubPath}/Types/api.ts" => "{$targetPath}/Types/api.ts",
            "{$stubPath}/Types/common.ts" => "{$targetPath}/Types/common.ts",
            "{$stubPath}/Types/index.ts" => "{$targetPath}/Types/index.ts",
            "{$stubPath}/Types/global.d.ts" => "{$targetPath}/Types/global.d.ts",
            
            // Stores
            "{$stubPath}/Stores/index.ts" => "{$targetPath}/Stores/index.ts",
            "{$stubPath}/Stores/languageStore.ts" => "{$targetPath}/Stores/languageStore.ts",
            "{$stubPath}/Stores/Auth/authStore.ts" => "{$targetPath}/Stores/Auth/authStore.ts",
            
            // Layouts
            "{$stubPath}/Layouts/AppLayout.vue" => "{$targetPath}/Layouts/AppLayout.vue",
            
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
            
            // Entry point
            "{$stubPath}/app.ts" => "{$targetPath}/app.ts",

            // TypeScript Configuration
            "{$stubPath}/tsconfig.json" => base_path('tsconfig.json'),
            "{$stubPath}/env.d.ts" => "{$targetPath}/env.d.ts",

            // Vite Configuration
            "{$stubPath}/vite.config.js" => base_path('vite.config.js'),
        ];

        $published = 0;
        $skipped = 0;

        foreach ($maps as $stub => $destination) {
            if (!File::exists($stub)) {
                $this->warn("⚠️  Missing stub: {$stub}");
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
            $this->info("  ✅ {$relativeDest}");
        }

        $this->newLine();
        $this->info("╔══════════════════════════════════════════════════════════╗");
        $this->info("║                                                          ║");
        $this->info("║   ✅ Published: {$published} files                          ║");
        $this->info("║   ⏭️  Skipped: {$skipped} files (use --force to overwrite)  ║");
        $this->info("║                                                          ║");
        $this->info("╚══════════════════════════════════════════════════════════╝");

        $this->newLine();
        $this->info("🚀 Next steps:");
        $this->info("   1. composer require spatie/laravel-permission");
        $this->info("   2. npm install axios pinia @inertiajs/vue3");
        $this->info("   3. npm run dev");
        $this->info("   4. php artisan make:rubel-crud-module");
        $this->newLine(2);

        return Command::SUCCESS;
    }
}

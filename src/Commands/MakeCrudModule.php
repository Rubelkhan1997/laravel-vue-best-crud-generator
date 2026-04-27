<?php

namespace Rubel\LaravelVueBestCrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;
use function Laravel\Prompts\error;
use function Laravel\Prompts\spin;

class MakeCrudModule extends Command
{
    protected $signature = 'make:rubel-crud-module {module?}';
    protected $description = 'Generate a complete CRUD module for Laravel + Vue + Inertia + Pinia';

    protected string $moduleName;
    protected string $featureName;
    protected string $modelName;
    protected string $tableName;
    protected string $apiRoute;
    protected string $webRoute;
    protected string $routeName;
    protected array $fields = [];
    protected array $relationships = [];
    protected array $enums = [];
    protected string $permissionName;
    protected bool $softDeletes;
    protected bool $generateTests;

    public function handle(): int
    {
        $this->softDeletes = (bool) config('laravel-vue-best-crud-generator.soft_deletes', true);
        $this->generateTests = (bool) config('laravel-vue-best-crud-generator.generate_tests', true);

        $this->displayHeader();

        // Interactive prompts
        $this->collectModuleInfo();
        $this->collectFields();
        $this->collectRelationships();
        $this->collectEnums();
        $this->confirmGeneration();

        // Generate all files
        spin(function () {
            $this->generateBackendFiles();
            $this->generateFrontendFiles();
            $this->generateTestFiles();
            $this->updateRoutes();
        }, '🚀 Generating CRUD module...');

        $this->displaySuccess();

        return Command::SUCCESS;
    }

    protected function displayHeader(): void
    {
        info("
╔══════════════════════════════════════════════════════════╗
║                                                          ║
║   🎨 Laravel Vue CRUD Generator v1.0                     ║
║   Complete CRUD for Laravel + Vue 3 + Inertia + Pinia   ║
║                                                          ║
╚══════════════════════════════════════════════════════════╝
        ");
    }

    protected function collectModuleInfo(): void
    {
        $this->moduleName = text(
            label: 'Module Name (e.g., FrontDesk, Admin, Billing)',
            required: 'Module name is required',
            default: 'FrontDesk',
            hint: 'This will be the namespace for your module'
        );

        $this->featureName = text(
            label: 'Feature Name (e.g., Hotel, Room, Guest, Reservation)',
            required: 'Feature name is required',
            hint: 'Singular PascalCase name of your feature'
        );

        $this->modelName = $this->featureName;
        
        $this->tableName = text(
            label: 'Table Name',
            required: 'Table name is required',
            default: Str::plural(strtolower($this->featureName)),
            hint: 'Database table name (plural, snake_case)'
        );

        $this->apiRoute = text(
            label: 'API Route',
            required: 'API route is required',
            default: '/' . Str::kebab($this->moduleName) . '/' . Str::plural(Str::kebab($this->featureName)),
            hint: 'API endpoint path'
        );

        $this->webRoute = text(
            label: 'Web Route',
            required: 'Web route is required',
            default: '/' . Str::plural(Str::kebab($this->featureName)),
            hint: 'Web route path for Inertia pages'
        );

        $this->routeName = Str::plural(Str::kebab($this->featureName));
        $this->permissionName = Str::plural(Str::kebab($this->featureName));
    }

    protected function collectFields(): void
    {
        info("\n📝 Define your database fields:");
        info("Format: field_name:type:constraints");
        info("Example: name:string:required, email:string:nullable:unique");
        info("Type 'done' when finished\n");

        while (true) {
            $input = text(
                label: 'Field #' . (count($this->fields) + 1),
                required: false,
                hint: 'Format: field_name:type:constraints (or type "done")'
            );

            if (strtolower($input) === 'done' || empty($input)) {
                break;
            }

            $parts = explode(':', $input);
            $fieldName = trim($parts[0]);
            $fieldType = trim($parts[1] ?? 'string');
            $constraints = trim(implode(':', array_slice($parts, 2)));

            $this->fields[] = [
                'name' => $fieldName,
                'type' => $fieldType,
                'constraints' => $constraints,
                'nullable' => str_contains($constraints, 'nullable'),
                'required' => !str_contains($constraints, 'nullable'),
                'unique' => str_contains($constraints, 'unique'),
            ];

            info("✅ Added: {$fieldName}");
        }

        // Add timestamps
        $this->fields[] = ['name' => 'created_at', 'type' => 'timestamp', 'constraints' => ''];
        $this->fields[] = ['name' => 'updated_at', 'type' => 'timestamp', 'constraints' => ''];
        
        if ($this->softDeletes) {
            $this->fields[] = ['name' => 'deleted_at', 'type' => 'timestamp', 'constraints' => 'nullable'];
        }
    }

    protected function collectRelationships(): void
    {
        $hasRelationships = confirm(
            label: 'Does this model have relationships?',
            default: false
        );

        if (!$hasRelationships) {
            return;
        }

        info("\n🔗 Define relationships:");
        info("Format: type:Model:method_name");
        info("Example: belongsTo:Hotel:hotel, hasMany:Room:rooms");
        info("Type 'done' when finished\n");

        while (true) {
            $input = text(
                label: 'Relationship #' . (count($this->relationships) + 1),
                required: false,
                hint: 'Format: type:Model:method (or type "done")'
            );

            if (strtolower($input) === 'done' || empty($input)) {
                break;
            }

            $parts = explode(':', $input);
            $this->relationships[] = [
                'type' => trim($parts[0]),
                'model' => trim($parts[1]),
                'method' => trim($parts[2] ?? Str::camel(trim($parts[1]))),
            ];

            info("✅ Added: {$parts[0]} -> {$parts[1]}");
        }
    }

    protected function collectEnums(): void
    {
        $hasEnums = confirm(
            label: 'Does this model have enum fields?',
            default: false
        );

        if (!$hasEnums) {
            return;
        }

        info("\n📋 Define enums:");
        info("Format: enum_name:field_name:value1,value2,value3");
        info("Example: StatusEnum:status:active,inactive,pending");
        info("Type 'done' when finished\n");

        while (true) {
            $input = text(
                label: 'Enum #' . (count($this->enums) + 1),
                required: false,
                hint: 'Format: EnumName:field_name:val1,val2 (or type "done")'
            );

            if (strtolower($input) === 'done' || empty($input)) {
                break;
            }

            $parts = explode(':', $input);
            $values = explode(',', trim($parts[2] ?? ''));
            
            $this->enums[] = [
                'name' => trim($parts[0]),
                'field' => trim($parts[1]),
                'values' => array_map('trim', $values),
            ];

            info("✅ Added: {$parts[0]}");
        }
    }

    protected function confirmGeneration(): void
    {
        info("\n📋 Summary:");
        info("   Module: {$this->moduleName}");
        info("   Feature: {$this->featureName}");
        info("   Model: {$this->modelName}");
        info("   Table: {$this->tableName}");
        info("   Fields: " . count($this->fields));
        info("   Relationships: " . count($this->relationships));
        info("   Enums: " . count($this->enums));

        $confirmed = confirm(
            label: 'Generate all files?',
            default: true
        );

        if (!$confirmed) {
            warning('❌ Generation cancelled.');
            exit(0);
        }
    }

    protected function generateBackendFiles(): void
    {
        $this->generateMigration();
        $this->generateModel();
        $this->generateService();
        $this->generateApiController();
        $this->generateWebController();
        $this->generateStoreRequest();
        $this->generateUpdateRequest();
        $this->generateResource();
        $this->generateData();
        $this->generateAction();
    }

    protected function generateFrontendFiles(): void
    {
        $this->generateTypes();
        $this->generateMappers();
        $this->generatePiniaStore();
        $this->generateComposable();
        $this->generateIndexPage();
        $this->generateCreatePage();
        $this->generateEditPage();
        $this->generateShowPage();
    }

    protected function generateTestFiles(): void
    {
        if (!$this->generateTests) {
            return;
        }
        $this->generateFeatureTest();
    }

    protected function updateRoutes(): void
    {
        // Show route snippets to user
        $this->info("\n📌 Add these routes to your routes files:");
        
        $this->info("\n--- routes/api.php ---");
        $this->info($this->getApiRouteSnippet());
        
        $this->info("\n--- routes/web.php ---");
        $this->info($this->getWebRouteSnippet());
    }

    // File generation methods will be implemented with stubs
    protected function generateMigration(): void
    {
        $content = $this->getStub('migration');
        $content = $this->replacePlaceholders($content);
        
        $fileName = date('Y_m_d_His') . '_create_' . $this->tableName . '_table.php';
        $path = database_path('migrations/' . $fileName);
        
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);
        
        $this->info("✅ Migration: {$path}");
    }

    protected function generateModel(): void
    {
        $content = $this->getStub('model');
        $content = $this->replacePlaceholders($content);
        
        $path = base_path("app/Modules/{$this->moduleName}/Models/{$this->modelName}.php");
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);
        
        $this->info("✅ Model: {$path}");
    }

    protected function generateService(): void
    {
        $content = $this->getStub('service');
        $content = $this->replacePlaceholders($content);
        
        $path = base_path("app/Modules/{$this->moduleName}/Services/{$this->modelName}Service.php");
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);
        
        $this->info("✅ Service: {$path}");
    }

    protected function generateApiController(): void
    {
        $content = $this->getStub('api-controller');
        $content = $this->replacePlaceholders($content);
        
        $path = base_path("app/Modules/{$this->moduleName}/Controllers/Api/V1/{$this->modelName}Controller.php");
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);
        
        $this->info("✅ API Controller: {$path}");
    }

    protected function generateWebController(): void
    {
        $content = $this->getStub('web-controller');
        $content = $this->replacePlaceholders($content);
        
        $path = base_path("app/Modules/{$this->moduleName}/Controllers/Web/{$this->modelName}Controller.php");
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);
        
        $this->info("✅ Web Controller: {$path}");
    }

    protected function generateStoreRequest(): void
    {
        $content = $this->getStub('form-request');
        $content = str_replace('[REQUEST_TYPE]', 'Store', $content);
        $content = $this->replacePlaceholders($content);
        $content = str_replace('[permission_action]', 'create', $content);

        $path = base_path("app/Modules/{$this->moduleName}/Requests/Store{$this->modelName}Request.php");
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);

        $this->info("✅ Store Request: {$path}");
    }

    protected function generateUpdateRequest(): void
    {
        $content = $this->getStub('form-request');
        $content = str_replace('[REQUEST_TYPE]', 'Update', $content);
        $content = $this->replacePlaceholders($content);
        $content = str_replace('[permission_action]', 'edit', $content);

        $path = base_path("app/Modules/{$this->moduleName}/Requests/Update{$this->modelName}Request.php");
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);

        $this->info("✅ Update Request: {$path}");
    }

    protected function generateResource(): void
    {
        $content = $this->getStub('resource');
        $content = $this->replacePlaceholders($content);

        $path = base_path("app/Modules/{$this->moduleName}/Resources/{$this->modelName}Resource.php");
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);

        $this->info("✅ Resource: {$path}");
    }

    protected function generateData(): void
    {
        $content = $this->getStub('data');
        $content = $this->replacePlaceholders($content);

        $path = base_path("app/Modules/{$this->moduleName}/Data/{$this->modelName}Data.php");
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);

        $this->info("✅ Data: {$path}");
    }

    protected function generateAction(): void
    {
        $content = $this->getStub('action');
        $content = $this->replacePlaceholders($content);

        $path = base_path("app/Modules/{$this->moduleName}/Actions/Create{$this->modelName}Action.php");
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);

        $this->info("✅ Action: {$path}");
    }

    protected function generateTypes(): void
    {
        $content = $this->getStub('types');
        $content = $this->replacePlaceholders($content);
        
        $path = base_path("resources/js/Types/{$this->moduleName}/" . strtolower($this->modelName) . ".ts");
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);
        
        $this->info("✅ Types: {$path}");
    }

    protected function generateMappers(): void
    {
        $content = $this->getStub('mappers');
        $content = $this->replacePlaceholders($content);
        
        $path = base_path("resources/js/Utils/Mappers/" . strtolower($this->modelName) . ".ts");
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);
        
        $this->info("✅ Mappers: {$path}");
    }

    protected function generatePiniaStore(): void
    {
        $content = $this->getStub('store');
        $content = $this->replacePlaceholders($content);
        
        $path = base_path("resources/js/Stores/{$this->moduleName}/" . strtolower($this->modelName) . "Store.ts");
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);
        
        $this->info("✅ Pinia Store: {$path}");
    }

    protected function generateComposable(): void
    {
        $content = $this->getStub('composable');
        $content = $this->replacePlaceholders($content);
        
        $path = base_path("resources/js/Composables/{$this->moduleName}/use" . Str::plural($this->modelName) . ".ts");
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);
        
        $this->info("✅ Composable: {$path}");
    }

    protected function generateIndexPage(): void
    {
        $content = $this->getStub('page-index');
        $content = $this->replacePlaceholders($content);
        
        $path = base_path("resources/js/Pages/{$this->moduleName}/{$this->featureName}/Index.vue");
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);
        
        $this->info("✅ Index Page: {$path}");
    }

    protected function generateCreatePage(): void
    {
        $content = $this->getStub('page-create');
        $content = $this->replacePlaceholders($content);
        
        $path = base_path("resources/js/Pages/{$this->moduleName}/{$this->featureName}/Create.vue");
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);
        
        $this->info("✅ Create Page: {$path}");
    }

    protected function generateEditPage(): void
    {
        $content = $this->getStub('page-edit');
        $content = $this->replacePlaceholders($content);
        
        $path = base_path("resources/js/Pages/{$this->moduleName}/{$this->featureName}/Edit.vue");
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);
        
        $this->info("✅ Edit Page: {$path}");
    }

    protected function generateShowPage(): void
    {
        $content = $this->getStub('page-show');
        $content = $this->replacePlaceholders($content);
        
        $path = base_path("resources/js/Pages/{$this->moduleName}/{$this->featureName}/Show.vue");
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);
        
        $this->info("✅ Show Page: {$path}");
    }

    protected function generateFeatureTest(): void
    {
        $content = $this->getStub('test');
        $content = $this->replacePlaceholders($content);
        
        $path = base_path("tests/Feature/{$this->modelName}Test.php");
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);
        
        $this->info("✅ Test: {$path}");
    }

    protected function getStub(string $name): string
    {
        $stubPath = config('laravel-vue-best-crud-generator.stubs_path', __DIR__ . '/../../stubs');
        $stubFile = $stubPath . '/' . $name . '.stub';
        
        if (File::exists($stubFile)) {
            return File::get($stubFile);
        }
        
        // Fallback to embedded stubs
        return $this->getEmbeddedStub($name);
    }

    protected function replacePlaceholders(string $content): string
    {
        $search = [
            '[MODULE]',
            '[MODULE_NAME]',
            '[MODEL]',
            '[MODEL_NAME]',
            '[SERVICE]',
            '[TABLE]',
            '[TABLE_NAME]',
            '[FEATURE_NAME]',
            '[feature_name]',
            '[feature_plural]',
            '[model_name]',
            '[web_route]',
            '[api_route]',
            '[route_name]',
            '[permission_name]',
            '[FILLABLE]',
            '[CASTS]',
            '[RELATIONSHIPS]',
            '[SEARCH_COLUMNS]',
            '[FIELDS]',
            '[COLUMNS]',
            '[END_FILLABLE]',
            '[END_CASTS]',
            '[END_RELATIONSHIPS]',
            '[END_SEARCH_COLUMNS]',
            '[FIELDS_FORM]',
            '[FIELDS_FORM_DATA]',
            '[FIELDS_FORM_DATA_EDIT]',
            '[VALIDATION_RULES_FORM]',
            '[TYPE_FIELDS]',
            '[CREATE_DTO_FIELDS]',
            '[UPDATE_DTO_FIELDS]',
            '[FIELDS_PAYLOAD_CREATE]',
            '[FIELDS_PAYLOAD_EDIT]',
            '[FIELDS_BACKEND_MAP]',
            '[FIELDS_HEADERS]',
            '[FIELDS_COLUMNS]',
            '[FIELDS_SHOW]',
            '[FIELDS_SHOW_TITLE]',
            '[DATA_PROPERTIES]',
            '[VALIDATION_MESSAGES]',
            '[VALIDATION_RULES_ARRAY]',
            '[permission_action]',
            '[REQUEST_TYPE]',
        ];

        $replace = [
            $this->moduleName,
            $this->moduleName,
            $this->modelName,
            $this->modelName,
            $this->modelName . 'Service',
            $this->tableName,
            $this->tableName,
            $this->featureName,
            strtolower($this->featureName),
            strtolower(Str::plural($this->featureName)),
            strtolower($this->modelName),
            trim($this->webRoute, '/'),
            trim($this->apiRoute, '/'),
            $this->routeName,
            $this->permissionName,
            $this->getFillableFields(),
            $this->getCasts(),
            $this->getRelationships(),
            $this->getSearchColumns(),
            $this->getFieldsList(),
            $this->getMigrationColumns(),
            '',
            '',
            '',
            '',
            $this->getFieldsForm(),
            $this->getFieldsFormData(),
            $this->getFieldsFormDataEdit(),
            $this->getValidationRulesForm(),
            $this->getTypeFields(),
            $this->getCreateDtoFields(),
            $this->getUpdateDtoFields(),
            $this->getFieldsPayloadCreate(),
            $this->getFieldsPayloadEdit(),
            $this->getFieldsBackendMap(),
            $this->getFieldsHeaders(),
            $this->getFieldsColumns(),
            $this->getFieldsShow(),
            $this->getFieldsShowTitle(),
            $this->getDataProperties(),
            $this->getValidationMessages(),
            $this->getValidationRulesArray(),
            'edit',
        ];

        return str_replace($search, $replace, $content);
    }

    protected function getFillableFields(): string
    {
        $fields = collect($this->fields)
            ->filter(fn($f) => !in_array($f['name'], ['id', 'created_at', 'updated_at', 'deleted_at']))
            ->map(fn($f) => "        '{$f['name']}',")
            ->implode("\n");
        
        return $fields;
    }

    protected function getCasts(): string
    {
        if (empty($this->enums)) {
            return '';
        }

        $casts = collect($this->enums)
            ->map(fn($enum) => "        '{$enum['field']}' => \\App\\Enums\\{$enum['name']}::class,")
            ->implode("\n");
        
        return $casts;
    }

    protected function getRelationships(): string
    {
        if (empty($this->relationships)) {
            return '';
        }

        $methods = [];
        foreach ($this->relationships as $rel) {
            $returnType = match ($rel['type']) {
                'belongsTo' => 'BelongsTo',
                'hasMany' => 'HasMany',
                'hasOne' => 'HasOne',
                'belongsToMany' => 'BelongsToMany',
                default => 'Relation',
            };

            $methods[] = "\n    /**\n     * Get the {$rel['method']}.\n     */\n    public function {$rel['method']}(): {$returnType}\n    {\n        return \$this->{$rel['type']}({$rel['model']}::class);\n    }";
        }

        return implode("\n", $methods);
    }

    protected function getSearchColumns(): string
    {
        $columns = collect($this->fields)
            ->filter(fn($f) => in_array($f['type'], ['string', 'text']))
            ->take(5)
            ->map(function ($f, $index) {
                $method = $index === 0 ? 'where' : 'orWhere';
                return "                \$q->{$method}('{$f['name']}', 'like', \"%{\$search}%\")";
            })
            ->implode("\n");

        return $columns;
    }

    protected function getFieldsList(): string
    {
        return collect($this->fields)
            ->filter(fn($f) => !in_array($f['name'], ['id', 'created_at', 'updated_at', 'deleted_at']))
            ->map(function ($f) {
                $fieldName = $f['name'];

                // Handle enum fields
                $enum = collect($this->enums)->first(fn($e) => $e['field'] === $fieldName);
                if ($enum) {
                    return "            '{$fieldName}' => \$this->{$fieldName}?->value,\n            '{$fieldName}_label' => \$this->{$fieldName}?->label(),";
                }

                // Handle date/datetime fields
                if (in_array($f['type'], ['date', 'datetime', 'timestamp'])) {
                    return "            '{$fieldName}' => \$this->{$fieldName}?->format('Y-m-d'),";
                }

                return "            '{$fieldName}' => \$this->{$fieldName},";
            })
            ->implode("\n");
    }

    /**
     * Generate TypeScript type fields with proper optional markers.
     * Used in types.stub for [MODEL_NAME] interface.
     */
    protected function getTypeFields(): string
    {
        return collect($this->fields)
            ->filter(fn($f) => !in_array($f['name'], ['id', 'created_at', 'updated_at', 'deleted_at']))
            ->map(function ($f) {
                $tsType = $this->getTypeScriptType($f['type']);
                $optional = in_array($f['type'], ['bool', 'boolean', 'decimal', 'float', 'double']) || $f['nullable'] ? '?' : '';

                return "    {$f['name']}{$optional}: {$tsType};";
            })
            ->implode("\n");
    }

    /**
     * Generate Create DTO fields (required fields required, nullable optional).
     */
    protected function getCreateDtoFields(): string
    {
        return collect($this->fields)
            ->filter(fn($f) => !in_array($f['name'], ['id', 'created_at', 'updated_at', 'deleted_at']))
            ->map(function ($f) {
                $tsType = $this->getTypeScriptType($f['type']);
                $optional = $f['nullable'] ? '?' : '';

                return "    {$f['name']}{$optional}: {$tsType};";
            })
            ->implode("\n");
    }

    /**
     * Generate Update DTO fields (all optional).
     */
    protected function getUpdateDtoFields(): string
    {
        return collect($this->fields)
            ->filter(fn($f) => !in_array($f['name'], ['id', 'created_at', 'updated_at', 'deleted_at']))
            ->map(function ($f) {
                $tsType = $this->getTypeScriptType($f['type']);

                return "    {$f['name']}?: {$tsType};";
            })
            ->implode("\n");
    }

    /**
     * Map database type to TypeScript type.
     */
    protected function getTypeScriptType(string $dbType): string
    {
        return match ($dbType) {
            'int', 'integer', 'bigint', 'tinyint', 'smallint', 'mediumint' => 'number',
            'bool', 'boolean' => 'boolean',
            'decimal', 'float', 'double' => 'number',
            'json' => 'Record<string, any>',
            default => 'string',
        };
    }

    /**
     * Generate typed payload for create page.
     * Sends undefined for empty optional fields.
     */
    protected function getFieldsPayloadCreate(): string
    {
        $userFields = collect($this->fields)
            ->filter(fn($f) => !in_array($f['name'], ['id', 'created_at', 'updated_at', 'deleted_at']));

        if ($userFields->isEmpty()) {
            return '';
        }

        return $userFields->map(function ($f) {
            $fieldName = $f['name'];

            if ($f['required']) {
                return "                {$fieldName}: form.{$fieldName},";
            }

            return "                {$fieldName}: form.{$fieldName} || undefined,";
        })->implode("\n");
    }

    /**
     * Generate typed payload for edit page.
     * Sends undefined for empty optional fields.
     */
    protected function getFieldsPayloadEdit(): string
    {
        $userFields = collect($this->fields)
            ->filter(fn($f) => !in_array($f['name'], ['id', 'created_at', 'updated_at', 'deleted_at']));

        if ($userFields->isEmpty()) {
            return '';
        }

        return $userFields->map(function ($f) {
            $fieldName = $f['name'];

            if ($f['required']) {
                return "                {$fieldName}: form.{$fieldName},";
            }

            return "                {$fieldName}: form.{$fieldName} || undefined,";
        })->implode("\n");
    }

    /**
     * Generate backend field mapping for error key conversion.
     * Maps snake_case backend keys to camelCase frontend form keys.
     */
    protected function getFieldsBackendMap(): string
    {
        $userFields = collect($this->fields)
            ->filter(fn($f) => !in_array($f['name'], ['id', 'created_at', 'updated_at', 'deleted_at']));

        if ($userFields->isEmpty()) {
            return '';
        }

        return $userFields->map(function ($f) {
            $fieldName = $f['name'];

            return "            {$fieldName}: '{$fieldName}',";
        })->implode("\n");
    }

    /**
     * Generate custom validation messages for form requests.
     */
    protected function getValidationMessages(): string
    {
        $userFields = collect($this->fields)
            ->filter(fn($f) => !in_array($f['name'], ['id', 'created_at', 'updated_at', 'deleted_at']));

        if ($userFields->isEmpty()) {
            return '';
        }

        $messages = [];
        foreach ($userFields as $f) {
            $fieldName = $f['name'];
            $label = Str::headline($fieldName);

            if ($f['required']) {
                $messages[] = "            '{$fieldName}.required' => '{$label} is required',";
            }
            if ($f['unique']) {
                $messages[] = "            '{$fieldName}.unique' => '{$label} has already been taken',";
            }
            if ($f['type'] === 'email') {
                $messages[] = "            '{$fieldName}.email' => 'Please enter a valid email address',";
            }
        }

        return implode("\n", $messages);
    }

    /**
     * Generate validation rules as PHP array format for Form Requests.
     * Uses Laravel's array-style validation rules.
     */
    protected function getValidationRulesArray(): string
    {
        $userFields = collect($this->fields)
            ->filter(fn($f) => !in_array($f['name'], ['id', 'created_at', 'updated_at', 'deleted_at']));

        if ($userFields->isEmpty()) {
            return '';
        }

        return $userFields->map(function ($f) {
            $fieldName = $f['name'];
            $rules = [];

            if ($f['required']) {
                $rules[] = "'required'";
            } else {
                $rules[] = "'nullable'";
            }

            // Add type rules
            if (in_array($f['type'], ['string', 'text', 'varchar'])) {
                $rules[] = "'string'";
                $rules[] = "'max:255'";
            }
            if ($f['type'] === 'email') {
                $rules[] = "'email'";
                $rules[] = "'max:255'";
            }
            if (in_array($f['type'], ['int', 'integer', 'bigint'])) {
                $rules[] = "'integer'";
            }
            if (in_array($f['type'], ['bool', 'boolean'])) {
                $rules[] = "'boolean'";
            }
            if (in_array($f['type'], ['decimal', 'float', 'double'])) {
                $rules[] = "'numeric'";
            }

            // Add unique rule
            if ($f['unique']) {
                $rules[] = "'unique:{$this->tableName},{$fieldName}'";
            }

            return "            '{$fieldName}' => [" . implode(', ', $rules) . "],";
        })->implode("\n");
    }

    /**
     * Generate Data class constructor properties.
     */
    protected function getDataProperties(): string
    {
        $userFields = collect($this->fields)
            ->filter(fn($f) => !in_array($f['name'], ['id', 'created_at', 'updated_at', 'deleted_at']));

        if ($userFields->isEmpty()) {
            return '';
        }

        return $userFields->map(function ($f) {
            $fieldName = $f['name'];
            $phpType = $this->getPhpType($f['type']);
            $nullable = $f['nullable'];

            if ($nullable) {
                return "        public ?{$phpType} \${$fieldName} = null,";
            }

            return "        public {$phpType} \${$fieldName},";
        })->implode("\n");
    }

    /**
     * Map database type to PHP type.
     */
    protected function getPhpType(string $dbType): string
    {
        return match ($dbType) {
            'int', 'integer', 'bigint', 'tinyint', 'smallint', 'mediumint' => 'int',
            'bool', 'boolean' => 'bool',
            'decimal', 'float', 'double' => 'float',
            'json' => 'array',
            default => 'string',
        };
    }

    /**
     * Generate table header definitions for index page.
     */
    protected function getFieldsHeaders(): string
    {
        $userFields = collect($this->fields)
            ->filter(fn($f) => !in_array($f['name'], ['id', 'created_at', 'updated_at', 'deleted_at', 'password']))
            ->take(5);

        if ($userFields->isEmpty()) {
            return "        { key: 'id', label: 'ID' },\n        { key: 'actions', label: t('[feature_plural].actions'), align: 'right' as const },";
        }

        $headers = $userFields->map(function ($f) {
            $label = $f['name'];

            return "        { key: '{$f['name']}', label: t('[feature_plural].{$label}') },";
        })->implode("\n");

        return "{$headers}\n        { key: 'actions', label: t('[feature_plural].actions'), align: 'right' as const },";
    }

    /**
     * Generate table column definitions for index page.
     */
    protected function getFieldsColumns(): string
    {
        $userFields = collect($this->fields)
            ->filter(fn($f) => !in_array($f['name'], ['id', 'created_at', 'updated_at', 'deleted_at', 'password']))
            ->take(5);

        if ($userFields->isEmpty()) {
            return "        { key: 'id', className: 'font-medium text-slate-900' },";
        }

        return $userFields->map(function ($f, $index) {
            $className = $index === 0 ? "        { key: '{$f['name']}', className: 'font-medium text-slate-900' }," : "        { key: '{$f['name']}', fallback: t('na') },";

            return $className;
        })->implode("\n");
    }

    /**
     * Generate detail field markup for show page.
     */
    protected function getFieldsShow(): string
    {
        $userFields = collect($this->fields)
            ->filter(fn($f) => !in_array($f['name'], ['id', 'created_at', 'updated_at', 'deleted_at', 'password']));

        if ($userFields->isEmpty()) {
            return '';
        }

        // Group into pairs for 2-column grid
        $fieldGroups = $userFields->chunk(2);

        return $fieldGroups->map(function ($group) {
            return $group->map(function ($f) {
                $fieldName = $f['name'];
                $labelKey = "[feature_plural].{$fieldName}";

                return <<<VUE
                    <div>
                        <label class="block text-sm font-medium text-slate-500 mb-1">{{ t('{$labelKey}') }}</label>
                        <p class="text-slate-900">{{ [model_name]Data.{$fieldName} || t('na') }}</p>
                    </div>
                VUE;
            })->implode("\n\n");
        })->implode("\n\n");
    }

    /**
     * Generate the property accessor for page title in show view.
     * Uses the first string/text field as the display name.
     */
    protected function getFieldsShowTitle(): string
    {
        $titleField = collect($this->fields)
            ->first(fn($f) => in_array($f['type'], ['string', 'text']) && !in_array($f['name'], ['id', 'password']));

        if (!$titleField) {
            return '.id';
        }

        return ".{$titleField['name']}";
    }

    protected function getMigrationColumns(): string
    {
        return collect($this->fields)
            ->filter(fn($f) => !in_array($f['name'], ['id', 'created_at', 'updated_at', 'deleted_at']))
            ->map(function ($f) {
                $type = match ($f['type']) {
                    'string' => "string",
                    'text' => "text",
                    'int' => "integer",
                    'bool' => "boolean",
                    'decimal' => "decimal",
                    default => "string",
                };

                $nullable = $f['nullable'] ? '->nullable()' : '';
                $unique = $f['unique'] ? '->unique()' : '';

                return "            \$table->{$type}('{$f['name']}'){$nullable}{$unique};";
            })
            ->implode("\n");
    }

    /**
     * Generate Vue form field markup for [FIELDS_FORM] placeholder.
     *
     * Produces form input components (FormInput, FormTextarea, FormSelect, etc.)
     * matching the actual resources/js/Components/Form/ conventions.
     * Fields are wrapped in grid layout: grid grid-cols-1 md:grid-cols-2 gap-6.
     */
    protected function getFieldsForm(): string
    {
        $userFields = collect($this->fields)
            ->filter(fn($f) => !in_array($f['name'], ['id', 'created_at', 'updated_at', 'deleted_at']));

        if ($userFields->isEmpty()) {
            return '';
        }

        // Group fields into rows of 2 for the grid
        $fieldGroups = $userFields->chunk(2);

        $htmlBlocks = [];

        foreach ($fieldGroups as $group) {
            $fieldsHtml = $group->map(function ($f) {
                $label = Str::headline($f['name']);
                $fieldName = $f['name'];
                $errorKey = $fieldName;
                $vueType = $this->getVueFieldType($f['type']);
                $requiredAttr = $f['required'] ? ':required="true"' : '';

                return match ($vueType) {
                    'textarea' => <<<VUE
                        <FormTextarea
                            id="{$fieldName}"
                            v-model="form.{$fieldName}"
                            :label="t('[feature_plural].{$fieldName}')"
                            :placeholder="t('[feature_plural].{$fieldName}_placeholder')"
                            :rows="3"
                            :error="form.errors.{$errorKey}"
                            wrapper-class="mb-0"
                        />
                    VUE,

                    'email' => <<<VUE
                        <FormInput
                            id="{$fieldName}"
                            v-model="form.{$fieldName}"
                            type="email"
                            :label="t('[feature_plural].{$fieldName}')"
                            :placeholder="t('[feature_plural].{$fieldName}_placeholder')"
                            {$requiredAttr}
                            :error="form.errors.{$errorKey}"
                            wrapper-class="mb-0"
                        />
                    VUE,

                    'number' => <<<VUE
                        <FormInput
                            id="{$fieldName}"
                            v-model="form.{$fieldName}"
                            type="number"
                            :label="t('[feature_plural].{$fieldName}')"
                            :placeholder="t('[feature_plural].{$fieldName}_placeholder')"
                            {$requiredAttr}
                            :error="form.errors.{$errorKey}"
                            wrapper-class="mb-0"
                        />
                    VUE,

                    'password' => <<<VUE
                        <FormInput
                            id="{$fieldName}"
                            v-model="form.{$fieldName}"
                            type="password"
                            :label="t('[feature_plural].{$fieldName}')"
                            :placeholder="t('[feature_plural].{$fieldName}_placeholder')"
                            {$requiredAttr}
                            :error="form.errors.{$errorKey}"
                            wrapper-class="mb-0"
                        />
                    VUE,

                    'date' => <<<VUE
                        <FormInput
                            id="{$fieldName}"
                            v-model="form.{$fieldName}"
                            type="date"
                            :label="t('[feature_plural].{$fieldName}')"
                            :placeholder="t('[feature_plural].{$fieldName}_placeholder')"
                            {$requiredAttr}
                            :error="form.errors.{$errorKey}"
                            wrapper-class="mb-0"
                        />
                    VUE,

                    'time' => <<<VUE
                        <FormInput
                            id="{$fieldName}"
                            v-model="form.{$fieldName}"
                            type="time"
                            :label="t('[feature_plural].{$fieldName}')"
                            :placeholder="t('[feature_plural].{$fieldName}_placeholder')"
                            {$requiredAttr}
                            :error="form.errors.{$errorKey}"
                            wrapper-class="mb-0"
                        />
                    VUE,

                    'checkbox' => <<<VUE
                        <FormInput
                            id="{$fieldName}"
                            v-model="form.{$fieldName}"
                            type="checkbox"
                            :label="t('[feature_plural].{$fieldName}')"
                            {$requiredAttr}
                            :error="form.errors.{$errorKey}"
                            wrapper-class="mb-0"
                        />
                    VUE,

                    'select' => $this->generateSelectField($fieldName, $label, $errorKey, $f),

                    default => <<<VUE
                        <FormInput
                            id="{$fieldName}"
                            v-model="form.{$fieldName}"
                            :label="t('[feature_plural].{$fieldName}')"
                            :placeholder="t('[feature_plural].{$fieldName}_placeholder')"
                            {$requiredAttr}
                            :error="form.errors.{$errorKey}"
                            wrapper-class="mb-0"
                        />
                    VUE,
                };
            })->implode("\n\n");

            $htmlBlocks[] = "                    <div class=\"grid grid-cols-1 md:grid-cols-2 gap-6\">\n{$fieldsHtml}\n                    </div>";
        }

        return implode("\n\n", $htmlBlocks);
    }

    /**
     * Generate initial form data for create page [FIELDS_FORM_DATA].
     */
    protected function getFieldsFormData(): string
    {
        $userFields = collect($this->fields)
            ->filter(fn($f) => !in_array($f['name'], ['id', 'created_at', 'updated_at', 'deleted_at']));

        if ($userFields->isEmpty()) {
            return '';
        }

        return $userFields->map(function ($f) {
            $fieldName = $f['name'];
            $defaultValue = $this->getFormDefaultValue($f['type']);

            return "        {$fieldName}: {$defaultValue},";
        })->implode("\n");
    }

    /**
     * Generate initial form data for edit page [FIELDS_FORM_DATA_EDIT].
     *
     * Binds each field to the existing record's data via [model_name]Data.
     */
    protected function getFieldsFormDataEdit(): string
    {
        $userFields = collect($this->fields)
            ->filter(fn($f) => !in_array($f['name'], ['id', 'created_at', 'updated_at', 'deleted_at']));

        if ($userFields->isEmpty()) {
            return '';
        }

        return $userFields->map(function ($f) {
            $fieldName = $f['name'];

            return "        {$fieldName}: [model_name]Data.{$fieldName},";
        })->implode("\n");
    }

    /**
     * Generate client-side validation rules mapping for [VALIDATION_RULES_FORM].
     *
     * Uses array format matching validateInertiaForm() convention:
     *   name: [required],
     *   email: [nullable, email],
     */
    protected function getValidationRulesForm(): string
    {
        $userFields = collect($this->fields)
            ->filter(fn($f) => !in_array($f['name'], ['id', 'created_at', 'updated_at', 'deleted_at']));

        if ($userFields->isEmpty()) {
            return '';
        }

        return $userFields->map(function ($f) {
            $fieldName = $f['name'];
            $rules = [];

            if ($f['required']) {
                $rules[] = 'required';
            }

            $typeRules = $this->getClientTypeRules($f['type']);
            $rules = array_merge($rules, $typeRules);

            $rulesStr = implode(', ', $rules);

            return "            {$fieldName}: [{$rulesStr}],";
        })->implode("\n");
    }

    /**
     * Map database field type to Vue input type.
     */
    protected function getVueFieldType(string $dbType): string
    {
        return match ($dbType) {
            'text', 'longtext', 'mediumtext' => 'textarea',
            'email' => 'email',
            'int', 'integer', 'bigint', 'tinyint', 'smallint', 'mediumint', 'decimal', 'float', 'double' => 'number',
            'bool', 'boolean' => 'checkbox',
            'date' => 'date',
            'time' => 'time',
            'datetime', 'timestamp' => 'date',
            'password', 'secret' => 'password',
            default => 'text',
        };
    }

    /**
     * Get default value for form data based on field type.
     */
    protected function getFormDefaultValue(string $dbType): string
    {
        return match ($this->getVueFieldType($dbType)) {
            'checkbox' => 'false',
            'number' => 'null',
            default => "''",
        };
    }

    /**
     * Get client-side validation rules for a field type.
     */
    protected function getClientTypeRules(string $dbType): array
    {
        return match ($this->getVueFieldType($dbType)) {
            'email' => ['email'],
            default => [],
        };
    }

    /**
     * Generate a select/dropdown field with options.
     */
    protected function generateSelectField(string $fieldName, string $label, string $errorKey, array $field): string
    {
        // Check if this field is associated with an enum
        $enum = collect($this->enums)->first(fn($e) => $e['field'] === $fieldName);

        if ($enum) {
            $options = collect($enum['values'])
                ->map(fn($val) => "{ label: t('enums.{$enum['name']}.{$val}'), value: '{$val}' }")
                ->implode(",\n                            ");

            return <<<VUE
                        <FormSelect
                            id="{$fieldName}"
                            v-model="form.{$fieldName}"
                            :label="t('[feature_plural].{$fieldName}')"
                            :options="[
                            {$options}
                            ]"
                            :error="form.errors.{$errorKey}"
                            wrapper-class="mb-0"
                        />
            VUE;
        }

        // Check if this is a relationship field
        $relationship = collect($this->relationships)->first(fn($r) => $r['method'] === $fieldName || $r['model'] === Str::studly($fieldName));

        if ($relationship) {
            $relatedModelPlural = strtolower(Str::plural($relationship['model']));

            return <<<VUE
                        <FormSelect
                            id="{$fieldName}"
                            v-model="form.{$fieldName}"
                            :label="t('[feature_plural].{$fieldName}')"
                            :options="{$relatedModelPlural}"
                            option-label="name"
                            option-value="id"
                            :error="form.errors.{$errorKey}"
                            wrapper-class="mb-0"
                        />
            VUE;
        }

        // Generic select fallback
        return <<<VUE
                        <FormSelect
                            id="{$fieldName}"
                            v-model="form.{$fieldName}"
                            :label="t('[feature_plural].{$fieldName}')"
                            :options="[]"
                            :error="form.errors.{$errorKey}"
                            wrapper-class="mb-0"
                        />
        VUE;
    }

    protected function getApiRouteSnippet(): string
    {
        return "use App\\Modules\\{$this->moduleName}\\Controllers\\Api\\V1\\{$this->modelName}Controller as {$this->modelName}ApiController;

Route::middleware('auth:sanctum')
    ->prefix('v1/" . trim($this->apiRoute, '/') . "')
    ->name('api.{$this->routeName}.')
    ->group(function () {
        Route::get('/', [{$this->modelName}ApiController::class, 'index'])->name('index');
        Route::post('/', [{$this->modelName}ApiController::class, 'store'])->name('store');
        Route::get('/{id}', [{$this->modelName}ApiController::class, 'show'])->name('show');
        Route::put('/{id}', [{$this->modelName}ApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [{$this->modelName}ApiController::class, 'destroy'])->name('destroy');
    });";
    }

    protected function getWebRouteSnippet(): string
    {
        return "use App\\Modules\\{$this->moduleName}\\Controllers\\Web\\{$this->modelName}Controller as {$this->modelName}WebController;

Route::middleware('auth')
    ->prefix('" . trim($this->webRoute, '/') . "')
    ->name('{$this->routeName}.')
    ->group(function () {
        Route::get('/', [{$this->modelName}WebController::class, 'index'])->name('index');
        Route::get('/create', [{$this->modelName}WebController::class, 'create'])->name('create');
        Route::get('/{id}', [{$this->modelName}WebController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [{$this->modelName}WebController::class, 'edit'])->name('edit');
    });";
    }

    protected function displaySuccess(): void
    {
        $this->newLine(2);
        info("╔══════════════════════════════════════════════════════════╗");
        info("║                                                          ║");
        info("║   ✅ CRUD Module Generated Successfully!                ║");
        info("║                                                          ║");
        info("╚══════════════════════════════════════════════════════════╝");
        
        $this->newLine();
        info("📁 Files created:");
        info("   • Backend: 10 files (Migration, Model, Service, Controllers, Requests, Resource)");
        info("   • Frontend: 8 files (Types, Mappers, Store, Composable, Pages)");
        info("   • Tests: 1 file");
        
        $this->newLine();
        info("🚀 Next steps:");
        info("   1. Run: php artisan migrate");
        info("   2. Run: npm run dev");
        info("   3. Add routes to routes/api.php and routes/web.php");
        info("   4. Visit: {$this->webRoute}");
        
        $this->newLine(2);
    }

    protected function getEmbeddedStub(string $name): string
    {
        // This will load from the stubs directory
        return '';
    }
}

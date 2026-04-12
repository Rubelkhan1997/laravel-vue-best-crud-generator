# Laravel Vue Best CRUD Generator

Reusable CRUD scaffolding package extracted from a Laravel + Vue + Inertia project with a structured module-first architecture.

It is designed for projects that follow patterns like:

- Backend modules in `app/Modules/[Module]/...`
- Inertia pages in `resources/js/Pages/[Module]/[Feature]/...`
- Pinia stores in `resources/js/Stores/[Module]/...`
- Shared frontend structure such as `Helpers`, `Composables`, `Utils`, `Plugins`, `Layouts`, and `Components`

## What The Package Provides

### 1. CRUD module generator

```bash
php artisan make:rubel-crud-module
```

Generates:

- Migration
- Model
- Service
- API controller
- Web controller
- Store request
- Update request
- Resource
- TypeScript type
- Mapper
- Pinia store
- Vue composable
- Inertia pages: `Index`, `Create`, `Edit`, `Show`
- Feature test

Total generated files by default: `17`

### 2. Shared frontend asset publisher

```bash
php artisan crud-generator:publish-assets
```

Publishes reusable frontend structure from `stubs-frontend/`.

### 3. Default auth scaffolding

```bash
php artisan crud-generator:setup-auth
```

Publishes a basic auth starter for backend and frontend.

## Target Stack

The package assumes a Laravel app already using or ready to use:

- PHP `^8.2`
- Laravel `^11.0|^12.0|^13.0`
- `inertiajs/inertia-laravel` `^2.0`
- Vue 3
- `@inertiajs/vue3`
- Pinia
- Axios
- TypeScript

Recommended when using the shared auth and permission-aware frontend stubs:

- `laravel/sanctum`
- `spatie/laravel-permission`

## Dependencies

### Composer dependencies

Required by package:

- `php`
- `laravel/framework`
- `inertiajs/inertia-laravel`

Recommended in host application:

- `laravel/sanctum`
- `spatie/laravel-permission`

### NPM dependencies expected in host application

- `vue`
- `@inertiajs/vue3`
- `pinia`
- `axios`
- `typescript`

Your host app will typically also already have:

- `vite`
- `@vitejs/plugin-vue`
- `laravel-vite-plugin`

## Installation

### A. Install from a local `packages/` directory

If the package lives inside your current Laravel project:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "packages/rubel/laravel-vue-best-crud-generator",
            "options": {
                "symlink": true
            }
        }
    ]
}
```

Then run:

```bash
composer require rubel/laravel-vue-best-crud-generator:*
```

### B. Install from Packagist or VCS later

```bash
composer require rubel/laravel-vue-best-crud-generator
```

## Recommended Host App Setup

For a fresh or partially prepared Laravel + Vue project:

```bash
composer require inertiajs/inertia-laravel laravel/sanctum spatie/laravel-permission
npm install vue @inertiajs/vue3 pinia axios typescript
```

If these are already present in your existing project, do not reinstall them unnecessarily.

## Publishable Assets

### Publish config

```bash
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-config
```

Config file:

```text
config/laravel-vue-best-crud-generator.php
```

### Publish CRUD stubs

```bash
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-stubs
```

Published to:

```text
stubs/laravel-vue-best-crud-generator/
```

### Publish frontend stubs directory

```bash
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-frontend
```

Published to:

```text
stubs/laravel-vue-best-crud-generator-frontend/
```

## Commands

### `php artisan make:rubel-crud-module`

Interactive generator for a new module and feature.

Typical prompt flow:

1. Module name
2. Feature name
3. Table name
4. API route
5. Web route
6. Fields
7. Relationships
8. Enums
9. Confirm generation

Example:

```bash
php artisan make:rubel-crud-module
```

### `php artisan crud-generator:publish-assets`

Copies shared frontend assets into the host application.

```bash
php artisan crud-generator:publish-assets
php artisan crud-generator:publish-assets --force
```

This command may publish files such as:

- `resources/js/app.ts`
- `resources/js/Helpers/*`
- `resources/js/Composables/*`
- `resources/js/Utils/*`
- `resources/js/Plugins/*`
- `resources/js/Layouts/AppLayout.vue`
- `resources/js/Components/*`
- `app/Http/Middleware/HandleInertiaRequests.php`
- `app/Modules/Auth/Middleware/AuthenticateByToken.php`
- `bootstrap/app.php`

Use `--force` only when you intentionally want to overwrite existing files.

### `php artisan crud-generator:setup-auth`

Sets up default auth starter files.

```bash
php artisan crud-generator:setup-auth
php artisan crud-generator:setup-auth --force
```

This command may create or overwrite files such as:

- `app/Models/User.php`
- `app/Modules/Auth/...`
- `routes/auth-api.php`
- `resources/js/Pages/Auth/Login.vue`
- `resources/js/Pages/Auth/Register.vue`
- `resources/js/Stores/Auth/authStore.ts`
- `resources/js/Composables/Auth/useAuth.ts`
- `bootstrap/app.php`

For an already structured existing project, review before using `--force`.

## Recommended Install Flow

### Existing structured project

Best when your project already has its own auth, app shell, middleware, and frontend setup:

```bash
composer require rubel/laravel-vue-best-crud-generator:*
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-config
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-stubs
php artisan make:rubel-crud-module
```

Optional:

```bash
php artisan crud-generator:publish-assets
php artisan crud-generator:setup-auth
```

Only run those optional commands after checking file conflicts.

### New or starter-like project

```bash
composer require rubel/laravel-vue-best-crud-generator:*
composer require inertiajs/inertia-laravel laravel/sanctum spatie/laravel-permission
npm install vue @inertiajs/vue3 pinia axios typescript
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-config
php artisan crud-generator:publish-assets
php artisan crud-generator:setup-auth
php artisan make:rubel-crud-module
php artisan migrate
npm run dev
```

## Generated Structure

### Backend

```text
database/migrations/*_create_[table]_table.php
app/Modules/[Module]/Models/[Model].php
app/Modules/[Module]/Services/[Model]Service.php
app/Modules/[Module]/Controllers/Api/V1/[Model]Controller.php
app/Modules/[Module]/Controllers/Web/[Model]Controller.php
app/Modules/[Module]/Http/Requests/[Model]StoreRequest.php
app/Modules/[Module]/Http/Requests/[Model]UpdateRequest.php
app/Modules/[Module]/Resources/[Model]Resource.php
```

### Frontend

```text
resources/js/Types/[Module]/[model].ts
resources/js/Utils/Mappers/[model].ts
resources/js/Stores/[Module]/[model]Store.ts
resources/js/Composables/[Module]/use[Model]s.ts
resources/js/Pages/[Module]/[Feature]/Index.vue
resources/js/Pages/[Module]/[Feature]/Create.vue
resources/js/Pages/[Module]/[Feature]/Edit.vue
resources/js/Pages/[Module]/[Feature]/Show.vue
```

### Tests

```text
tests/Feature/[Model]Test.php
```

## Configuration

After publishing config, adjust:

```php
return [
    'default_module' => 'FrontDesk',
    'stubs_path' => base_path('stubs/laravel-vue-best-crud-generator'),
    'per_page' => 15,
    'soft_deletes' => true,
    'generate_tests' => true,
    'generate_resources' => true,
];
```

## Notes For Existing Projects

- `crud-generator:publish-assets` is not a safe blind overwrite command for mature projects.
- `crud-generator:setup-auth` is best for bootstrap/starter projects, not for a project that already has finished auth.
- The CRUD generator itself is the safest feature to reuse first.
- Publish stubs and adjust templates if your project naming or structure differs.

## Local Package Development

When developing the package inside a monorepo or local Laravel app:

```bash
composer dump-autoload
php artisan package:discover
php artisan list | findstr crud-generator
```

Useful checks:

```bash
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-config
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-stubs
php artisan crud-generator:publish-assets
php artisan make:rubel-crud-module
```

## License

MIT

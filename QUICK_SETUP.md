# Quick Setup

This guide is for using `rubel/laravel-vue-best-crud-generator` from a local `packages/` directory inside an existing Laravel project.

## 1. Add The Local Package Repository

Update your root `composer.json`:

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

## 2. Install The Package

```bash
composer require rubel/laravel-vue-best-crud-generator:@dev
```

## 3. Install Or Verify Host App Dependencies

### Required for package usage

```bash
composer require inertiajs/inertia-laravel
npm install vue @inertiajs/vue3 pinia axios typescript @vitejs/plugin-vue --save-dev
```

### Recommended for auth and permission-aware stubs

```bash
composer require laravel/sanctum spatie/laravel-permission
```

If your project already has these dependencies, skip reinstalling them.

## 4. Publish Config And Stubs

```bash
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-config
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-stubs
```

Optional:

```bash
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-frontend
```

## 5. Generate A CRUD Module

```bash
php artisan make:rubel-crud-module
```

After generation:

```bash
php artisan migrate
npm run dev
```

## 6. Optional Commands

### Publish shared frontend assets

```bash
php artisan crud-generator:publish-assets
```

Force overwrite:

```bash
php artisan crud-generator:publish-assets --force
```

Warning:

- This command can overwrite `resources/js/app.ts`
- It can also overwrite `bootstrap/app.php`
- In a mature existing project, review before using `--force`

### Setup default auth

```bash
php artisan crud-generator:setup-auth
```

Force overwrite:

```bash
php artisan crud-generator:setup-auth --force
```

Warning:

- This command can overwrite `app/Models/User.php`
- It can add `routes/auth-api.php`
- It can overwrite auth pages and `bootstrap/app.php`
- Best for starter projects, not fully customized auth systems

## Recommended Flow By Project Type

### Existing well-structured project

Use this order:

```bash
composer require rubel/laravel-vue-best-crud-generator:@dev
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-config
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-stubs
php artisan make:rubel-crud-module
```

Only use these if you really need them:

```bash
php artisan crud-generator:publish-assets
php artisan crud-generator:setup-auth
```

### New project or starter template

```bash
composer require rubel/laravel-vue-best-crud-generator:@dev
composer require inertiajs/inertia-laravel laravel/sanctum spatie/laravel-permission
npm install vue @inertiajs/vue3 pinia axios typescript @vitejs/plugin-vue --save-dev
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-config
php artisan crud-generator:publish-assets
php artisan crud-generator:setup-auth
php artisan make:rubel-crud-module
php artisan migrate
npm run dev
```

## Commands Summary

```bash
php artisan make:rubel-crud-module
php artisan crud-generator:publish-assets
php artisan crud-generator:publish-assets --force
php artisan crud-generator:setup-auth
php artisan crud-generator:setup-auth --force
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-config
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-stubs
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-frontend
```

## Generated Files Summary

By default, `make:rubel-crud-module` generates `17` files:

- `8` backend files
- `8` frontend files
- `1` test file

## Troubleshooting

### Command not found

```bash
composer dump-autoload
php artisan package:discover
php artisan optimize:clear
```

### Config or stubs not publishing

```bash
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-config --force
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-stubs --force
```

### Frontend build issues

```bash
npm install
npm run dev
```

### Package commands list

```bash
php artisan list | findstr crud-generator
```

### Stable version error (`could not find a matching version`)

```bash
composer require rubel/laravel-vue-best-crud-generator:@dev
```

Alternative (global): set `"minimum-stability": "dev"` in your host app `composer.json`, but `@dev` is safer because it only affects this package.

## Last Updated

April 12, 2026

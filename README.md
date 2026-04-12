# Laravel Vue Best CRUD Generator

Reusable CRUD scaffolding package for Laravel + Vue + Inertia projects.

## Requirements

- PHP `8.2+`
- Laravel `11`, `12`, or `13`
- `inertiajs/inertia-laravel` `2.x`
- Vue `3`
- TypeScript

Recommended in host project:

```bash
composer require laravel/sanctum spatie/laravel-permission
npm install vue @inertiajs/vue3 pinia axios typescript
```

## Installation

```bash
composer require rubel/laravel-vue-best-crud-generator
```

## Publish

```bash
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-config
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-stubs
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-frontend
```

## Commands

```bash
php artisan make:rubel-crud-module
php artisan crud-generator:publish-assets
php artisan crud-generator:setup-auth
```

## Recommended Use

### Existing project

```bash
composer require rubel/laravel-vue-best-crud-generator
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-config
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-stubs
php artisan make:rubel-crud-module
```

### New project

```bash
composer require rubel/laravel-vue-best-crud-generator
composer require inertiajs/inertia-laravel laravel/sanctum spatie/laravel-permission
npm install vue @inertiajs/vue3 pinia axios typescript
php artisan crud-generator:publish-assets
php artisan crud-generator:setup-auth
php artisan make:rubel-crud-module
```

## Notes

- `crud-generator:publish-assets` may overwrite frontend setup files
- `crud-generator:setup-auth` may overwrite auth-related files
- `make:rubel-crud-module` is the safest command to start with

## License

MIT

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
composer require laravel/sanctum spatie/laravel-permission spatie/laravel-data
npm install vue @inertiajs/vue3 pinia axios typescript
```

## Installation

> **Important:** This package is hosted on GitHub as a VCS repository.
> Before installing, add the repository to your project's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/Rubelkhan1997/laravel-vue-best-crud-generator"
        }
    ]
}
```

Then install via Composer:

```bash
composer require rubel/laravel-vue-best-crud-generator:@dev
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
php artisan crud-generator:merge-bootstrap  # Merge bootstrap config into existing project
```

## Recommended Use

### Existing project

```bash
composer require rubel/laravel-vue-best-crud-generator:@dev
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-config
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-stubs
php artisan make:rubel-crud-module
```

### New project

```bash
composer require rubel/laravel-vue-best-crud-generator:@dev
composer require inertiajs/inertia-laravel laravel/sanctum spatie/laravel-permission spatie/laravel-data
php artisan crud-generator:publish-assets
npm install
npm run dev
php artisan make:rubel-crud-module
```

## Frontend Setup

After publishing assets, the following files are automatically created:

- `tsconfig.json` - TypeScript configuration with path aliases
- `vite.config.js` - Vite configuration with Vue and Laravel plugins
- `resources/js/env.d.ts` - TypeScript declarations for Vue SFC
- `resources/js/**` - All frontend source files

**Install required dependencies:**

```bash
npm install vue @inertiajs/vue3 pinia axios typescript
npm install -D @vitejs/plugin-vue laravel-vite-plugin vite @types/node
```

For detailed setup instructions and troubleshooting, see `stubs-frontend/SETUP_GUIDE.md`.

## Notes

- `crud-generator:publish-assets` may overwrite frontend setup files
- `crud-generator:setup-auth` may overwrite auth-related files
- `make:rubel-crud-module` is the safest command to start with
- Make sure `tsconfig.json` and `vite.config.js` have matching path aliases (`@/*` → `resources/js/*`)

## License

MIT

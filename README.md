# 🚀 Laravel Vue Best CRUD Generator

A powerful CRUD generator for Laravel + Vue 3 projects using **Inertia.js**, **Pinia**, **TypeScript**, and **Spatie Permission**.

> Generates **17 files** (8 backend + 8 frontend + 1 test) with a single artisan command.

---

## 📦 Requirements

| Dependency | Version |
|---|---|
| PHP | ^8.1 |
| Laravel | ^10.0 or ^11.0 |
| Vue | ^3.0 |
| Inertia.js | ^1.0 |
| Node.js | ^18.0 |

---

## ⚡ Quick Installation

### Step 1 — Add Local Repository

If you're using this package from a local `packages/` directory, update your root `composer.json`:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/Rubelkhan1997/laravel-vue-best-crud-generator"
    }
],
```

### Step 2 — Install the Package

```bash
composer require rubel/laravel-vue-best-crud-generator:@dev
```

> If you get a `could not find a matching version` error, make sure you're using `:@dev`. Using `"minimum-stability": "dev"` globally in `composer.json` is an alternative but `:@dev` is safer.

### Step 3 — Install Frontend & Backend Dependencies

**Required:**
```bash
php artisan install:api
composer require inertiajs/inertia-laravel
composer require laravel/sanctum
composer spatie/laravel-permission
composer spatie/laravel-data

php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate

npm install vue @inertiajs/vue3 pinia axios typescript
```
 
> Skip any dependency you already have installed.

### Step 4 — Publish Config & Stubs

```bash
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-config
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-stubs
```

Optional — publish frontend assets:
```bash
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-frontend
```

### Step 5 — Generate a CRUD Module

```bash
php artisan make:rubel-crud-module
```

Then run:
```bash
php artisan migrate
npm run dev
```

---

## 📋 All Available Commands

| Command | Description |
|---|---|
| `php artisan vendor:publish --tag=laravel-vue-best-crud-generator-config` | Publish config file |
| `php artisan vendor:publish --tag=laravel-vue-best-crud-generator-stubs` | Publish stub files |
| `php artisan vendor:publish --tag=laravel-vue-best-crud-generator-frontend` | Publish frontend files |
| `php artisan crud-generator:publish-assets` | Publish shared frontend assets |
| `php artisan crud-generator:publish-assets --force` | Force overwrite frontend assets |
| `php artisan crud-generator:setup-auth` | Setup default auth scaffolding |
| `php artisan crud-generator:setup-auth --force` | Force overwrite auth files |
| `php artisan make:rubel-crud-module` | Generate a full CRUD module |

---

## 🗂️ Generated Files

Running `make:rubel-crud-module` generates **17 files**:

```
Backend  (8 files)
├── Model
├── Migration
├── Controller
├── Form Request (Store + Update)
├── Resource
├── Policy
└── Route

Frontend (8 files)
├── Index.vue
├── Create.vue
├── Edit.vue
├── Show.vue
├── composable
├── store (Pinia)
├── types
└── api service

Test     (1 file)
└── Feature Test
```

---

## 🔀 Recommended Flow by Project Type

### ✅ Existing Well-Structured Project

```bash
composer require rubel/laravel-vue-best-crud-generator:@dev
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-config
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-stubs
php artisan make:rubel-crud-module
php artisan migrate
npm run dev
```

> Only use `publish-assets` and `setup-auth` if you really need them.

### 🆕 New Project / Starter Template

```bash
composer require rubel/laravel-vue-best-crud-generator:@dev
composer require inertiajs/inertia-laravel laravel/sanctum spatie/laravel-permission spatie/laravel-data
npm install vue @inertiajs/vue3 pinia axios typescript
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-config
php artisan crud-generator:publish-assets
php artisan crud-generator:setup-auth
php artisan make:rubel-crud-module
php artisan migrate
npm run dev
```

---

## ⚠️ Important Warnings

### `crud-generator:publish-assets`
- Can overwrite `resources/js/app.ts`
- Can overwrite `bootstrap/app.php`
- Review carefully before using `--force` in a mature project

### `crud-generator:setup-auth`
- Can overwrite `app/Models/User.php`
- Adds `routes/auth-api.php`
- Can overwrite auth pages and `bootstrap/app.php`
- Best for **starter projects** — avoid on fully customized auth systems

---

## 🛠️ Troubleshooting

**Command not found:**
```bash
composer dump-autoload
php artisan package:discover
php artisan optimize:clear
```

**Config or stubs not publishing:**
```bash
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-config --force
php artisan vendor:publish --tag=laravel-vue-best-crud-generator-stubs --force
```

**Frontend build issues:**
```bash
npm install
npm run dev
```

**List all package commands:**
```bash
php artisan list | grep crud-generator
```

---

## 📄 License

MIT License © [rubel](https://github.com/rubel)
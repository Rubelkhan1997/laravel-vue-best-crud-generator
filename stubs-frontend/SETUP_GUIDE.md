# Frontend Setup Guide

## Required Dependencies

After publishing frontend assets, install these dependencies in your Laravel project:

```bash
# Core dependencies
npm install vue @inertiajs/vue3 pinia axios typescript

# Vite plugins
npm install -D @vitejs/plugin-vue laravel-vite-plugin vite

# Optional but recommended
npm install @vueuse/core
npm install -D @types/node
```

## TypeScript Configuration

The `tsconfig.json` file has been published to your project root. It includes:

- Path aliases: `@/*` maps to `resources/js/*`
- Strict mode enabled
- Vue SFC support
- Vite client types

## Vite Configuration

The `vite.config.js` file includes:

- Laravel Vite plugin
- Vue plugin with proper SFC handling
- Path aliases matching tsconfig.json

## Environment Types

The `env.d.ts` file provides TypeScript declarations for:
- `.vue` file imports
- Vite client types

## Setup Steps

1. **Publish assets** (if not done):
   ```bash
   php artisan crud-generator:publish-assets
   ```

2. **Install dependencies**:
   ```bash
   npm install
   ```

3. **Build or develop**:
   ```bash
   npm run dev     # Development with HMR
   npm run build   # Production build
   ```

## Troubleshooting

### Import errors like "Cannot find module '@inertiajs/vue3'"

```bash
npm install @inertiajs/vue3
npm install @types/node -D
```

### TypeScript errors for .vue imports

Ensure `env.d.ts` exists in `resources/js/` and contains Vue module declarations.

### Path alias not working (`@/*`)

Verify:
1. `tsconfig.json` has correct `paths` configuration
2. `vite.config.js` has matching `resolve.alias` configuration
3. TypeScript server is restarted in your IDE

### Vue imports showing errors

Make sure your `tsconfig.json` includes:
```json
{
  "compilerOptions": {
    "moduleResolution": "bundler",
    "allowSyntheticDefaultImports": true
  },
  "include": ["resources/js/**/*.vue"]
}
```

## Package.json Scripts

Ensure your `package.json` has these scripts:

```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build"
  }
}
```

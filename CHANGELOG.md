# Changelog

## [Unreleased]

### Fixed
- **Authentication routes now properly registered**: Both web (`/login`, `/register`) and API (`/api/v1/auth/*`) routes are now automatically added to `routes/web.php` and `routes/api.php`
- **Bootstrap configuration no longer overwritten**: Moved `bootstrap/app.php` from `stubs-frontend/bootstrap/` to `stubs-backend/bootstrap-app.php` to prevent overwriting existing project configuration
- **Smart bootstrap merging**: `SetupAuth` command now intelligently merges middleware and configuration into existing `bootstrap/app.php` instead of replacing it

### Added
- **New command: `crud-generator:merge-bootstrap`**: Manually merge bootstrap configuration into existing projects without overwriting
- **`auth-web-routes.stub`**: New stub file for web-based authentication routes (Inertia pages)
- **Automatic route inclusion**: `setup-auth` command now automatically adds `require` statements for `auth-api.php` and `auth-web.php` to respective route files
- **TypeScript configuration files**:
  - `tsconfig.json` - Proper TypeScript setup with path aliases (`@/*` → `resources/js/*`)
  - `env.d.ts` - Vue SFC type declarations
  - `vite.config.js` - Vite configuration with Laravel and Vue plugins

### Improved
- **SetupAuth command**: Now handles both route registration and bootstrap merging in one command
- **Better error handling**: Clear warnings when files are missing or skipped
- **Non-destructive updates**: Existing route files and bootstrap configuration are preserved and enhanced, not replaced

### Migration Guide

#### For existing projects using the package:

1. **Run setup-auth again** (safe, won't overwrite):
   ```bash
   php artisan crud-generator:setup-auth
   ```
   This will:
   - Create `routes/auth-api.php` (API routes)
   - Create `routes/auth-web.php` (web routes for Inertia)
   - Add include lines to `routes/api.php` and `routes/web.php`
   - Merge bootstrap configuration into `bootstrap/app.php`

2. **Or use merge-bootstrap separately**:
   ```bash
   php artisan crud-generator:merge-bootstrap
   ```

3. **Verify routes are working**:
   ```bash
   php artisan route:list --path=login
   php artisan route:list --path=api/v1/auth
   ```

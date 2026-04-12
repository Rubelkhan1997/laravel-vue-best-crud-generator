

/**
 * Plugins Barrel Export
 */

// ── Plugin objects (app.use() এর জন্য) ──
export { default as toast } from './toast';
export { default as confirm } from './confirm';
export { default as directives } from './directives';

// // ── Utilities (composable হিসেবে ব্যবহারের জন্য) ──
// export { toast as toastType } from './toast';
// export { confirm as confirmType } from './confirm';

// // ── Types ──
// export type { ToastOptions, Toast } from './toast';
// export type { ConfirmOptions } from './confirm';

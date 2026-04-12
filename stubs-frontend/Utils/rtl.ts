/**
 * RTL (Right-to-Left) Utility Functions
 * Helpers for RTL language support
 */

import { useLanguageStore } from '@/Stores/languageStore';

/**
 * RTL language codes
 */
export const RTL_LANGUAGES = ['ar', 'fa', 'he', 'ur'] as const;

export type RTLLanguageCode = typeof RTL_LANGUAGES[number];

/**
 * Check if a language code is RTL
 */
export function isRTL(code: string): boolean {
  return RTL_LANGUAGES.includes(code as RTLLanguageCode);
}

/**
 * Get text direction for a language code
 */
export function getTextDirection(code: string): 'ltr' | 'rtl' {
  return isRTL(code) ? 'rtl' : 'ltr';
}

/**
 * Get current text direction from language store
 */
export function getCurrentTextDirection(): 'ltr' | 'rtl' {
  const languageStore = useLanguageStore();
  return languageStore.isRTL ? 'rtl' : 'ltr';
}

/**
 * Set document direction
 */
export function setDocumentDirection(direction: 'ltr' | 'rtl'): void {
  if (typeof document !== 'undefined') {
    document.documentElement.dir = direction;
  }
}

/**
 * Set document language and direction
 */
export function setDocumentLocale(code: string, direction: 'ltr' | 'rtl'): void {
  if (typeof document !== 'undefined') {
    document.documentElement.lang = code;
    document.documentElement.dir = direction;
  }
}

/**
 * Get alignment based on direction
 */
export function getAlignment(direction: 'ltr' | 'rtl'): {
  start: 'left' | 'right';
  end: 'left' | 'right';
} {
  return direction === 'rtl'
    ? { start: 'right', end: 'left' }
    : { start: 'left', end: 'right' };
}

/**
 * Get CSS class for text alignment
 */
export function getTextAlignClass(direction: 'ltr' | 'rtl'): string {
  return direction === 'rtl' ? 'text-right' : 'text-left';
}

/**
 * Get flex direction class
 */
export function getFlexDirectionClass(direction: 'ltr' | 'rtl'): string {
  return direction === 'rtl' ? 'flex-row-reverse' : 'flex-row';
}

/**
 * Mirror CSS transform for RTL (for icons, arrows, etc.)
 */
export function mirrorForRTL(direction: 'ltr' | 'rtl'): string {
  return direction === 'rtl' ? 'scaleX(-1)' : 'scaleX(1)';
}

/**
 * Check if current environment is SSR (server-side rendering)
 */
export function isSSR(): boolean {
  return typeof document === 'undefined';
}

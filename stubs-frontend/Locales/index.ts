/**
 * Locales Barrel Export
 * Central export file for all translations
 */

import en from './en';
import bn from './bn';
import fr from './fr';
import ar from './ar';

export const locales = {
  en,
  bn,
  fr,
  ar,
} as const;

/**
 * Available locale codes
 */
export type LocaleCode = keyof typeof locales;

/**
 * Type for a single locale's translations
 */
export type Locale = typeof locales[LocaleCode];

/**
 * Available languages configuration
 */
export const availableLanguages = [
  {
    code: 'en' as LocaleCode,
    name: 'English',
    nativeName: 'English',
    flag:  '🇺🇸',
    direction: 'ltr' as const,
  },
  {
    code: 'bn' as LocaleCode,
    name: 'Bangla',
    nativeName: 'বাংলা',
    flag: '🇧🇩',
    direction: 'ltr' as const,
  },
  {
    code: 'fr' as LocaleCode,
    name: 'French',
    nativeName: 'Français',
    flag: '🇫🇷',
    direction: 'ltr' as const,
  },
  {
    code: 'ar' as LocaleCode,
    name: 'Arabic',
    nativeName: 'العربية',
    flag: '🇸🇦',
    direction: 'rtl' as const,
  },
] as const;

/**
 * Get default locale (first in list)
 */
export const defaultLocale: LocaleCode = 'en';

export default locales;

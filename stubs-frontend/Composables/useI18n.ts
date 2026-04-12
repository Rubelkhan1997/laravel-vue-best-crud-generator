import { computed } from 'vue';
import { useLanguageStore } from '@/Stores/languageStore';
import { locales, LocaleCode, defaultLocale } from '@/Locales';

/**
 * Composable for internationalization (i18n)
 * 
 * Provides translation functionality using a custom composable approach
 * without requiring external libraries.
 * 
 * @example
 * ```typescript
 * // In a Vue component
 * import { useI18n } from '@/Composables/useI18n';
 * 
 * const { t, currentLang, isRTL } = useI18n();
 * 
 * // Usage in template
 * // <h1>{{ t('welcome') }}</h1>
 * // <p>{{ t('navigation.dashboard') }}</p>
 * // <button>{{ t('actions.save') }}</button>
 * ```
 * 
 * @example
 * ```typescript
 * // With parameters
 * const { t } = useI18n();
 * t('validation.min_length', { min: '5' });
 * ```
 */
export function useI18n() {
  const languageStore = useLanguageStore();

  /**
   * Current language code
   */
  const currentLang = computed(() => languageStore.currentLanguage as LocaleCode);

  /**
   * Check if current language is RTL
   */
  const isRTL = computed(() => languageStore.isRTL);

  /**
   * Translate a key to current language
   * 
   * Supports nested keys with dot notation and parameter replacement.
   * Falls back to English if translation is not found.
   * Falls back to key if translation is not found in any language.
   * 
   * @param key - Translation key (e.g., 'welcome', 'navigation.dashboard')
   * @param params - Optional parameters to replace in translation (e.g., { min: '5' })
   * @returns Translated string or key if not found
   */
  function t(key: string, params: Record<string, string> = {}): string {
    const keys = key.split('.');
    let value: any = locales[currentLang.value];

    // Traverse nested object to find translation
    for (const k of keys) {
      value = value?.[k];
    }

    // Fallback to English if not found in current language
    if (value === undefined && currentLang.value !== defaultLocale) {
      value = getNestedValue(locales[defaultLocale], keys);
    }

    // Return key if still not found (with warning in development)
    if (value === undefined) {
      if (import.meta.env.DEV) {
        console.warn(`Translation missing for key: "${key}" in language: "${currentLang.value}"`);
      }
      return key;
    }

    // Replace parameters if provided
    if (typeof value === 'string' && Object.keys(params).length > 0) {
      Object.entries(params).forEach(([paramKey, paramValue]) => {
        value = value.replace(`:${paramKey}`, paramValue);
      });
    }

    return value;
  }

  /**
   * Helper to get nested value from object
   */
  function getNestedValue(obj: any, keys: string[]): any {
    return keys.reduce((acc, key) => acc?.[key], obj);
  }

  /**
   * Check if translation exists for a key in current language
   */
  function has(key: string): boolean {
    const keys = key.split('.');
    let value: any = locales[currentLang.value];

    for (const k of keys) {
      if (value?.[k] === undefined) {
        return false;
      }
      value = value[k];
    }

    return true;
  }

  /**
   * Check if translation exists in any language
   */
  function exists(key: string): boolean {
    return has(key) || getNestedValue(locales[defaultLocale], key.split('.')) !== undefined;
  }

  /**
   * Get all available locale codes
   */
  function getAvailableLocales(): LocaleCode[] {
    return Object.keys(locales) as LocaleCode[];
  }

  /**
   * Change current language
   */
  async function setLocale(code: LocaleCode): Promise<void> {
    await languageStore.setLanguage(code);
  }

  return {
    // Translation function
    t,

    // Check functions
    has,
    exists,

    // Locale info
    currentLang,
    isRTL,
    getAvailableLocales,

    // Actions
    setLocale,
  };
}


import { defineStore } from 'pinia';
import { availableLanguages, defaultLocale, LocaleCode } from '@/Locales';

export interface Language {
  code: LocaleCode;
  name: string;
  nativeName: string;
  flag: string;
  direction: 'ltr' | 'rtl';
}

export interface LanguageState {
  currentLanguage: LocaleCode;
  availableLanguages: Language[];
  loading: boolean;
}

const DEFAULT_LANGUAGE: LocaleCode = defaultLocale;

export const useLanguageStore = defineStore('language', {

  // ─────────────────────────────────────────────────────
  // State
  // ─────────────────────────────────────────────────────
  state: (): LanguageState => ({
    currentLanguage: DEFAULT_LANGUAGE,
    availableLanguages: availableLanguages as unknown as Language[],
    loading: false,
  }),

  // ─────────────────────────────────────────────────────
  // Getters
  // ─────────────────────────────────────────────────────
  getters: {
    currentLanguageObj: (state): Language | undefined => {
      return state.availableLanguages.find(
        (lang) => lang.code === state.currentLanguage
      );
    },

    isRTL: (state): boolean => {
      const lang = state.availableLanguages.find(
        (l) => l.code === state.currentLanguage
      );
      return lang?.direction === 'rtl';
    },
  },

  // ─────────────────────────────────────────────────────
  // Actions
  // ─────────────────────────────────────────────────────
  actions: {
    initialize(): void {
      const saved = localStorage.getItem('language') as LocaleCode | null;
      if (saved && this.availableLanguages.some((l) => l.code === saved)) {
        this.currentLanguage = saved;
      }
    },

    async setLanguage(code: LocaleCode): Promise<void> {
      const language = this.availableLanguages.find((l) => l.code === code);
      if (!language) return;

      this.loading = true;
      try {
        this.currentLanguage = code;
        localStorage.setItem('language', code);
        this.updateDocumentDirection(language.direction);
      } catch (error) {
        console.error('Language change error:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    updateDocumentDirection(direction: 'ltr' | 'rtl'): void {
      if (typeof document !== 'undefined') {
        document.documentElement.dir = direction;
        document.documentElement.lang = this.currentLanguage;
      }
    },

    $reset(): void {
      this.$patch({
        currentLanguage: DEFAULT_LANGUAGE,
        loading: false,
      });
    },
  },
});
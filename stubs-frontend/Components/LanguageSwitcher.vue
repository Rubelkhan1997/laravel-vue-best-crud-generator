<template>
  <div class="relative" v-if="languages.length > 1 && currentLanguage">
    <!-- Language Button -->
    <button
      @click="isOpen = !isOpen"
      @keydown.escape="isOpen = false"
      class="flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 hover:border-cyan-500 hover:bg-cyan-50 transition focus:outline-none focus:ring-2 focus:ring-cyan-500"
      :aria-label="'Switch language'"
      :aria-expanded="isOpen"
      :aria-haspopup="true"
    >
      <span class="text-lg" aria-hidden="true">{{ currentLanguage.flag }}</span>
      <span class="text-sm font-medium text-slate-700 hidden sm:inline">{{ currentLanguage.name }}</span>
      <svg
        class="w-4 h-4 text-slate-500"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        aria-hidden="true"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M19 9l-7 7-7-7"
        />
      </svg>
    </button>

    <!-- Dropdown -->
    <div
      v-if="isOpen"
      class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-slate-200 z-50 overflow-hidden"
      v-click-outside="() => (isOpen = false)"
      role="menu"
      :aria-orientation="'vertical'"
    >
      <div class="py-1 max-h-64 overflow-y-auto">
        <button
          v-for="lang in languages"
          :key="lang.code"
          @click="selectLanguage(lang)"
          @keydown.enter="selectLanguage(lang)"
          @keydown.space.prevent="selectLanguage(lang)"
          role="menuitem"
          class="w-full px-4 py-2.5 text-left hover:bg-cyan-50 transition flex items-center gap-3 focus:outline-none focus:bg-cyan-50"
          :class="{ 'bg-cyan-50 text-cyan-700': lang.code === currentLanguage.code }"
          :aria-current="lang.code === currentLanguage.code ? 'true' : undefined"
        >
          <span class="text-xl" aria-hidden="true">{{ lang.flag }}</span>
          <div class="flex-1">
            <div class="font-medium text-slate-800">{{ lang.name }}</div>
            <div class="text-xs text-slate-500">{{ lang.nativeName }}</div>
          </div>
          <svg
            v-if="lang.code === currentLanguage.code"
            class="w-5 h-5 text-cyan-600"
            fill="currentColor"
            viewBox="0 0 20 20"
            aria-hidden="true"
          >
            <path
              fill-rule="evenodd"
              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>
    </div>

    <!-- Backdrop for mobile -->
    <div
      v-if="isOpen"
      class="fixed inset-0 z-40 lg:hidden"
      @click="isOpen = false"
      aria-hidden="true"
    />
  </div>
</template>

<script setup lang="ts">
  import { computed, ref } from 'vue';
  import { useLanguageStore } from '@/Stores/languageStore';
  import type { Language } from '@/Stores/languageStore';

  // Store
  const languageStore = useLanguageStore();

  // State
  const isOpen = ref(false);

  // Computed
  const languages = computed(() => languageStore.availableLanguages);
  const currentLanguage = computed<Language | null>(() => {
    return languageStore.currentLanguageObj ?? languages.value[0] ?? null;
  });

  // Methods
  async function selectLanguage(lang: Language) {
    if (lang.code === languageStore.currentLanguage) {
      isOpen.value = false;
      return;
    }

    await languageStore.setLanguage(lang.code);
    isOpen.value = false;
  }
</script>

<style scoped>
  .overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
  }

  .overflow-y-auto::-webkit-scrollbar {
    width: 6px;
  }

  .overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
  }

  .overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 3px;
  }
</style>

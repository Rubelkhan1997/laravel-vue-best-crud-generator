<template>
    <div class="form-group">
        <button
            :type="type"
            :disabled="disabled || loading"
            :class="[
                'btn inline-flex items-center justify-center rounded-lg px-4 py-2 transition disabled:opacity-50 disabled:cursor-not-allowed',
                colorClass,
                buttonClass,
            ]"
        >
            <!-- Loading Spinner -->
            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            
            <!-- Button Text -->
            <span v-if="loading">{{ loadingText }}</span>
            <slot v-else>{{ name }}</slot>
        </button>
    </div>
</template>

<script setup lang="ts">
    import { computed } from 'vue';

    // Public API for the reusable button component.
    const props = withDefaults(defineProps<{
        type?: 'button' | 'submit' | 'reset';
        color?: 'primary' | 'secondary' | 'success' | 'danger' | 'warning' | string;
        name?: string;
        disabled?: boolean;
        loading?: boolean;
        loadingText?: string;
        buttonClass?: string;
    }>(), {
        type: 'button',
        color: 'primary',
        name: '',
        disabled: false,
        loading: false,
        loadingText: 'Loading...',
        buttonClass: '',
    });

    // Maps semantic color names to Tailwind utility classes.
    const colorClass = computed(() => {
        const map: Record<string, string> = {
            primary: 'bg-blue-600 text-white hover:bg-blue-700',
            secondary: 'bg-slate-200 text-slate-700 hover:bg-slate-300',
            success: 'bg-green-600 text-white hover:bg-green-700',
            danger: 'bg-red-600 text-white hover:bg-red-700',
            warning: 'bg-amber-500 text-white hover:bg-amber-600',
        };

        return map[props.color] || props.color;
    });
</script>

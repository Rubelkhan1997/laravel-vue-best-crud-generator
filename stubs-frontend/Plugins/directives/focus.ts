/**
 * Focus Directive
 * Auto-focus directive for Vue 3
 *
 * Usage:
 * <input v-focus />
 * <input v-focus:select />
 * <input v-focus="{ delay: 500, select: true }" />
 */

import { App, Directive, nextTick } from 'vue';

export interface FocusOptions {
    delay?: number;
    select?: boolean;
    preventScroll?: boolean;
}

const defaultOptions: FocusOptions = {
    delay: 0,
    select: false,
    preventScroll: false
};

/**
 * Focus element with options
 */
function focusElement(el: HTMLElement, options: FocusOptions): void {
    const { delay = 0, select = false, preventScroll = false } = { ...defaultOptions, ...options };

    const doFocus = () => {
        if (el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement) {
            el.focus({ preventScroll });

            if (select) {
                el.select();
            }
        } else if (el instanceof HTMLElement) {
            el.focus({ preventScroll });
        }
    };

    if (delay > 0) {
        setTimeout(doFocus, delay);
    } else {
        nextTick(doFocus);
    }
}

/**
 * Parse binding value to get options
 */
function parseOptions(bindingValue: any): FocusOptions {
    if (bindingValue === undefined || bindingValue === true) {
        return defaultOptions;
    }

    if (typeof bindingValue === 'string') {
        // v-focus:select
        return { select: bindingValue === 'select' };
    }

    if (typeof bindingValue === 'object') {
        return bindingValue;
    }

    return defaultOptions;
}

/**
 * Focus directive definition
 */
export const focusDirective: Directive<HTMLInputElement | HTMLElement, FocusOptions | string> = {
    mounted(el, binding) {
        const options = parseOptions(binding.value);
        focusElement(el, options);
    },
    updated(el, binding) {
        if (binding.value !== binding.oldValue) {
            const options = parseOptions(binding.value);
            focusElement(el, options);
        }
    }
};

/**
 * Install plugin
 */
export function install(app: App): void {
    app.directive('focus', focusDirective);
}

// Composable for programmatic usage
export function useFocus() {
    function focus(el: HTMLElement | null, options?: FocusOptions): void {
        if (el) {
            focusElement(el, options || defaultOptions);
        }
    }

    function focusAndSelect(el: HTMLElement | null, options?: Omit<FocusOptions, 'select'>): void {
        if (el) {
            focusElement(el, { ...options, select: true });
        }
    }

    function focusWithDelay(el: HTMLElement | null, delay: number): void {
        if (el) {
            focusElement(el, { delay });
        }
    }

    return {
        focus,
        focusAndSelect,
        focusWithDelay
    };
}

export default { install };

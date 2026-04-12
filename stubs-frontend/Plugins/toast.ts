import { App, ref, readonly, h, createVNode, render } from 'vue';

export interface ToastOptions {
    message: string;
    type?: 'success' | 'error' | 'warning' | 'info';
    duration?: number;
    position?: 'top-right' | 'top-left' | 'bottom-right' | 'bottom-left' | 'top-center' | 'bottom-center';
}

export interface Toast {
    id: number;
    message: string;
    type: 'success' | 'error' | 'warning' | 'info';
    duration: number;
}

const toasts = ref<Toast[]>([]);
let toastId = 0;

const DEFAULT_DURATION = 5000;
const DEFAULT_POSITION = 'top-right';
const MAX_TOASTS = 5;

/**
 * Show toast notification
 */
function showToast(options: ToastOptions): number {
    const {
        message,
        type = 'info',
        duration = DEFAULT_DURATION,
        position = DEFAULT_POSITION
    } = options;

    const id = ++toastId;

    // Remove oldest toast if max count reached
    if (toasts.value.length >= MAX_TOASTS) {
        toasts.value.shift();
    }

    toasts.value.push({
        id,
        message,
        type,
        duration
    });

    // Auto remove after duration
    setTimeout(() => {
        removeToast(id);
    }, duration);

    return id;
}

/**
 * Remove toast by ID
 */
function removeToast(id: number): void {
    const index = toasts.value.findIndex(t => t.id === id);
    if (index !== -1) {
        toasts.value.splice(index, 1);
    }
}

/**
 * Clear all toasts
 */
function clearToasts(): void {
    toasts.value = [];
}

// Convenience methods
const toast = {
    success: (message: string, duration?: number) => showToast({ message, type: 'success', duration }),
    error: (message: string, duration?: number) => showToast({ message, type: 'error', duration }),
    warning: (message: string, duration?: number) => showToast({ message, type: 'warning', duration }),
    info: (message: string, duration?: number) => showToast({ message, type: 'info', duration }),
    show: showToast,
    remove: removeToast,
    clear: clearToasts,
    useToasts: () => readonly(toasts)
};

/**
 * Toast Container Component
 */
const ToastContainer = {
    name: 'ToastContainer',
    setup() {
        return () => h('div', { class: 'fixed z-50 top-4 right-4 space-y-2' }, [
            toasts.value.map(t => h('div', {
                key: t.id,
                class: `px-4 py-3 rounded-lg shadow-lg text-white max-w-sm animate-slide-in ${
                    t.type === 'success' ? 'bg-green-500' :
                    t.type === 'error' ? 'bg-red-500' :
                    t.type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
                }`
            }, [
                h('div', { class: 'flex items-center justify-between' }, [
                    h('span', {}, t.message),
                    h('button', {
                        class: 'ml-4 text-white hover:text-gray-200',
                        onClick: () => removeToast(t.id)
                    }, '×')
                ])
            ]))
        ]);
    }
};

/**
 * Install plugin
 */
export function install(app: App): void {
    // Provide toast methods globally
    app.config.globalProperties.$toast = toast;

    // Provide composable
    app.provide('toast', toast);

    // Mount toast container
    if (typeof document !== 'undefined') {
        const container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
        render(createVNode(ToastContainer), container);
    }
}

// Type declarations
declare module 'vue' {
    interface ComponentCustomProperties {
        $toast: typeof toast;
    }
}

export { toast, ToastContainer };
export default { install };

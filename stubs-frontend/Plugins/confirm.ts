import { App, ref, readonly, h, createVNode, render } from 'vue';

export interface ConfirmOptions {
    title?: string;
    message: string;
    confirmText?: string;
    cancelText?: string;
    variant?: 'danger' | 'warning' | 'info';
    icon?: boolean;
}

interface ConfirmDialog {
    id: number;
    title: string;
    message: string;
    confirmText: string;
    cancelText: string;
    variant: 'danger' | 'warning' | 'info';
    icon: boolean;
    resolve: (value: boolean) => void;
}

const dialogs = ref<ConfirmDialog[]>([]);
let dialogId = 0;

const VARIANTS = {
    danger: {
        iconBg: '#FCEBEB',
        iconColor: '#E24B4A',
        btnBg: '#E24B4A',
        icon: `<path d="M3 6h18M19 6l-1 14H6L5 6M10 6V4h4v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>`
    },
    warning: {
        iconBg: '#FAEEDA',
        iconColor: '#BA7517',
        btnBg: '#BA7517',
        icon: `<path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>`
    },
    info: {
        iconBg: '#E6F1FB',
        iconColor: '#378ADD',
        btnBg: '#378ADD',
        icon: `<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>`
    }
};

function showConfirm(options: ConfirmOptions): Promise<boolean> {
    const {
        title = 'Are you sure?',
        message,
        confirmText = 'Confirm',
        cancelText = 'Cancel',
        variant = 'danger',
        icon = true
    } = options;

    return new Promise((resolve) => {
        dialogs.value.push({ id: ++dialogId, title, message, confirmText, cancelText, variant, icon, resolve });
    });
}

function closeDialog(id: number, confirmed: boolean): void {
    const index = dialogs.value.findIndex(d => d.id === id);
    if (index !== -1) {
        dialogs.value[index].resolve(confirmed);
        dialogs.value.splice(index, 1);
    }
}

function clearDialogs(): void {
    dialogs.value.forEach(d => d.resolve(false));
    dialogs.value = [];
}

// ── Inject global styles once ───────────────────────────────────────────────
function injectStyles() {
    if (document.getElementById('confirm-dialog-styles')) return;
    const style = document.createElement('style');
    style.id = 'confirm-dialog-styles';
    style.textContent = `
        @keyframes confirm-backdrop-in {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        @keyframes confirm-dialog-in {
            from { opacity: 0; transform: scale(0.93) translateY(10px); }
            to   { opacity: 1; transform: scale(1)    translateY(0);    }
        }
        .confirm-backdrop {
            animation: confirm-backdrop-in 0.18s ease both;
        }
        .confirm-card {
            animation: confirm-dialog-in 0.25s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }
        .confirm-btn-cancel {
            padding: 9px 18px;
            border-radius: 10px;
            border: 1px solid rgba(0,0,0,0.12);
            background: rgba(0,0,0,0.05);
            color: #374151;
            font-size: 14px;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.15s;
        }
        .confirm-btn-cancel:hover { background: rgba(0,0,0,0.09); }
        .confirm-btn-confirm {
            padding: 9px 18px;
            border-radius: 10px;
            border: none;
            color: #fff;
            font-size: 14px;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
            transition: filter 0.15s;
        }
        .confirm-btn-confirm:hover { filter: brightness(1.1); }
    `;
    document.head.appendChild(style);
}

// ── Render function ──────────────────────────────────────────────────────────
const ConfirmDialogComponent = {
    name: 'ConfirmDialogComponent',
    setup() {
        return () => {
            if (dialogs.value.length === 0) return null;

            return h('div', {
                class: 'confirm-backdrop',
                style: 'position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(10,10,15,0.5);backdrop-filter:blur(3px);'
            }, [
                // Click outside → cancel
                h('div', {
                    style: 'position:absolute;inset:0;',
                    onClick: () => closeDialog(dialogs.value[dialogs.value.length - 1].id, false)
                }),

                // Top dialog only (stack one at a time)
                ...dialogs.value.slice(-1).map(d => {
                    const v = VARIANTS[d.variant];

                    return h('div', {
                        key: d.id,
                        class: 'confirm-card',
                        style: 'position:relative;background:#fff;border-radius:20px;box-shadow:0 24px 48px -8px rgba(0,0,0,0.18),0 0 0 0.5px rgba(0,0,0,0.08);width:100%;max-width:400px;margin:0 16px;overflow:hidden;'
                    }, [
                        h('div', { style: 'padding:28px 28px 0;display:flex;gap:16px;align-items:flex-start;' }, [
                            d.icon && h('div', {
                                style: `flex-shrink:0;width:42px;height:42px;border-radius:12px;background:${v.iconBg};display:flex;align-items:center;justify-content:center;`
                            }, [
                                h('svg', { width: 20, height: 20, viewBox: '0 0 24 24', fill: 'none', stroke: v.iconColor, 'stroke-width': 2, 'stroke-linecap': 'round', 'stroke-linejoin': 'round', innerHTML: v.icon })
                            ]),

                            h('div', { style: 'flex:1;padding-top:2px;' }, [
                                h('p', { style: 'font-size:15px;font-weight:600;color:#111827;margin:0 0 6px;line-height:1.4;' }, d.title),
                                h('p', { style: 'font-size:13.5px;color:#6b7280;line-height:1.65;margin:0 0 28px;' }, d.message),
                            ])
                        ]),
                        
                        // Actions
                        h('div', { style: 'padding:0 28px 24px;display:flex;gap:10px;justify-content:flex-end;' }, [
                            h('button', {
                                class: 'confirm-btn-cancel',
                                onClick: () => closeDialog(d.id, false)
                            }, d.cancelText),

                            h('button', {
                                class: 'confirm-btn-confirm',
                                style: `background:${v.btnBg};`,
                                onClick: () => closeDialog(d.id, true)
                            }, d.confirmText)
                        ])
                    ]);
                })
            ]);
        };
    }
};

const confirm = {
    show: showConfirm,
    close: closeDialog,
    clear: clearDialogs,
    useDialogs: () => readonly(dialogs)
};

export type ConfirmType = typeof confirm;

export function install(app: App): void {
    injectStyles();
    app.config.globalProperties.$confirm = confirm;
    app.provide('confirm', confirm);

    if (typeof document !== 'undefined') {
        const container = document.createElement('div');
        container.id = 'confirm-dialog-container';
        document.body.appendChild(container);
        render(createVNode(ConfirmDialogComponent), container);
    }
}

declare module 'vue' {
    interface ComponentCustomProperties {
        $confirm: typeof confirm;
    }
}

export { confirm, ConfirmDialogComponent };
export default { install };

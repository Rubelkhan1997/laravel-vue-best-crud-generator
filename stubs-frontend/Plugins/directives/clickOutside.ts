/**
 * Click Outside Directive
 * Trigger callback when user clicks outside the bound element.
 *
 * Usage:
 * <div v-click-outside="onOutsideClick" />
 */

import { App, Directive, DirectiveBinding } from 'vue';

type ClickOutsideBinding = ((event: Event) => void) | undefined;
type ClickOutsideElement = HTMLElement & {
    __click_outside_handler__?: (event: Event) => void;
    __click_outside_timer__?: ReturnType<typeof setTimeout>;
};

function onDocumentInteraction(el: ClickOutsideElement, binding: DirectiveBinding<ClickOutsideBinding>, event: Event): void {
    const callback = binding.value;

    if (!callback || typeof callback !== 'function') {
        return;
    }

    const target = event.target as Node | null;

    if (!target || el.contains(target)) {
        return;
    }

    callback(event);
}

export const clickOutsideDirective: Directive<HTMLElement, ClickOutsideBinding> = {
    mounted(el, binding) {
        const targetEl = el as ClickOutsideElement;
        targetEl.__click_outside_handler__ = (event: Event) => onDocumentInteraction(targetEl, binding, event);

        // Delay listener registration to avoid immediately closing on the same click
        // event that opened the dropdown.
        targetEl.__click_outside_timer__ = setTimeout(() => {
            if (!targetEl.__click_outside_handler__) {
                return;
            }

            document.addEventListener('click', targetEl.__click_outside_handler__);
            document.addEventListener('touchstart', targetEl.__click_outside_handler__);
        }, 0);
    },
    unmounted(el) {
        const targetEl = el as ClickOutsideElement;

        if (targetEl.__click_outside_timer__) {
            clearTimeout(targetEl.__click_outside_timer__);
            delete targetEl.__click_outside_timer__;
        }

        if (!targetEl.__click_outside_handler__) {
            return;
        }

        document.removeEventListener('click', targetEl.__click_outside_handler__);
        document.removeEventListener('touchstart', targetEl.__click_outside_handler__);
        delete targetEl.__click_outside_handler__;
    }
};

export function install(app: App): void {
    app.directive('click-outside', clickOutsideDirective);
}

export default { install };

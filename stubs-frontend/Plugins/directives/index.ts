/**
 * Directives Barrel Export
 * Central export file for all Vue directives
 */

// Permission directive
export {
    permissionDirective,
    setupPermission,
    usePermission,
    install as installPermission
} from './permission';

// Focus directive
export {
    focusDirective,
    useFocus,
    install as installFocus
} from './focus';

// Click outside directive
export {
    clickOutsideDirective,
    install as installClickOutside
} from './clickOutside';

import { App } from 'vue';
import { install as installPermissionDirective } from './permission';
import { install as installFocusDirective } from './focus';
import { install as installClickOutsideDirective } from './clickOutside';

export function install(app: App): void {
    installPermissionDirective(app);
    installFocusDirective(app);
    installClickOutsideDirective(app);
}

export default { install };

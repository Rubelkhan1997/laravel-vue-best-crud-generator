/**
 * Permission Directive
 * Role-based permission directive for Vue 3
 *
 * Usage:
 * <button v-permission="['admin', 'manager']">Admin Only</button>
 * <button v-permission="'admin'">Admin Only</button>
 */

import { App, Directive } from 'vue';

const PMS_USER_KEY = 'pms_user';

export interface PermissionOptions {
    getUserRoles?: () => string[];
    getUserPermissions?: () => string[];
    onDenied?: (el: HTMLElement) => void;
}

const defaultOptions: Required<PermissionOptions> = {
    getUserRoles: () => {
        const userData = getUserData();
        return userData?.roles || (userData?.role ? [userData.role] : []) || [];
    },
    getUserPermissions: () => {
        const userData = getUserData();
        return userData?.permissions || [];
    },
    onDenied: (el: HTMLElement) => {
        el.style.display = 'none';
    }
};

function getUserData(): Record<string, any> | null {
    const user = localStorage.getItem(PMS_USER_KEY);
    if (user) {
        try {
            return JSON.parse(user);
        } catch {
            return null;
        }
    }

    const page = (window as any)?.__page;
    return page?.props?.auth?.user ?? null;
}

let options = { ...defaultOptions };

/**
 * Check if user has permission
 */
function hasPermission(requiredRoles: string | string[]): boolean {
    const userRoles = options.getUserRoles();
    const userPermissions = options.getUserPermissions();

    if ((!userRoles || userRoles.length === 0) && (!userPermissions || userPermissions.length === 0)) {
        return false;
    }

    const roles = Array.isArray(requiredRoles) ? requiredRoles : [requiredRoles];

    const isPermissionCheck = roles.some(role => role.includes(' '));

    if (isPermissionCheck) {
        return roles.some(permission => userPermissions.includes(permission));
    }

    return roles.some(role => userRoles.includes(role));
}

/**
 * Permission directive definition
 */
export const permissionDirective: Directive<HTMLElement, string | string[]> = {
    mounted(el, binding) {
        const requiredRoles = binding.value;

        if (!hasPermission(requiredRoles)) {
            options.onDenied(el);
            el.setAttribute('data-permission-denied', 'true');
        }
    },
    updated(el, binding) {
        const requiredRoles = binding.value;

        if (!hasPermission(requiredRoles)) {
            options.onDenied(el);
            el.setAttribute('data-permission-denied', 'true');
        } else {
            el.style.display = '';
            el.removeAttribute('data-permission-denied');
        }
    }
};

/**
 * Setup directive with custom options
 */
export function setupPermission(customOptions: PermissionOptions): void {
    options = { ...defaultOptions, ...customOptions };
}

/**
 * Install plugin
 */
export function install(app: App, customOptions?: PermissionOptions): void {
    if (customOptions) {
        setupPermission(customOptions);
    }

    const permission = usePermission();
    app.provide('permission', permission);
    app.config.globalProperties.$permission = permission;
    app.directive('permission', permissionDirective);
}

// Composable for programmatic usage
export function usePermission() {
    function check(requiredRoles: string | string[]): boolean {
        return hasPermission(requiredRoles);
    }

    function hasRole(role: string): boolean {
        return hasPermission(role);
    }

    function hasAnyRole(roles: string[]): boolean {
        return hasPermission(roles);
    }

    function hasAllRoles(roles: string[]): boolean {
        const userRoles = options.getUserRoles();
        return roles.every(role => userRoles.includes(role));
    }

    return {
        check,
        hasRole,
        hasAnyRole,
        hasAllRoles
    };
}

declare module 'vue' {
    interface ComponentCustomProperties {
        $permission: ReturnType<typeof usePermission>;
    }
}

export default { install, setupPermission };

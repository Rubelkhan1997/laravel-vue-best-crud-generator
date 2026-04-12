import { inject } from 'vue';
import { usePermission } from '@/Plugins/directives/permission';

export interface PermissionService {
    check: (requiredRoles: string | string[]) => boolean;
    hasRole: (role: string) => boolean;
    hasAnyRole: (roles: string[]) => boolean;
    hasAllRoles: (roles: string[]) => boolean;
}

const fallbackPermission = usePermission();

export function usePermissionService(): PermissionService {
    return inject<PermissionService>('permission', fallbackPermission);
}

/**
 * Auth Helper Functions
 * Authentication utilities
 */

/**
 * Get token value from cookie
 */
export function getTokenFromCookie(cookieString: string, name: string): string | null {
    const nameEQ = name + '=';
    const cookies = cookieString.split(';');
    
    for (let i = 0; i < cookies.length; i++) {
        let cookie = cookies[i].trim();
        if (cookie.indexOf(nameEQ) === 0) {
            return decodeURIComponent(cookie.substring(nameEQ.length));
        }
    }
    
    return null;
}

/**
 * Check if token exists in cookie
 */
export function hasTokenInCookie(cookieString: string, name: string): boolean {
    return cookieString.includes(name + '=');
}

/**
 * Check if user is authenticated
 */
export function isAuthenticated(): boolean {
    const token = localStorage.getItem('auth_token');
    return !!token || hasTokenInCookie(document.cookie, 'auth_token');
}

/**
 * Get authenticated user from storage
 */
export function getAuthUser(): Record<string, any> | null {
    const user = localStorage.getItem('auth_user');
    return user ? JSON.parse(user) : null;
}

/**
 * Check if user has permission
 */
export function hasPermission(permission: string): boolean {
    const user = getAuthUser();
    if (!user || !user.permissions) return false;
    return user.permissions.includes(permission);
}

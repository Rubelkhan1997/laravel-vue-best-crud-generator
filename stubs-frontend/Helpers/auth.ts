/**
 * Authentication Helper Functions (Pure)
 */

/**
 * Check if cookie string contains a token.
 */
export function hasTokenInCookie(cookieString: string, tokenKey = 'auth_token'): boolean {
    if (!cookieString) return false;
    return cookieString
        .split(';')
        .some((part) => part.trim().startsWith(`${tokenKey}=`));
}

/**
 * Extract token value from cookie string.
 */
export function getTokenFromCookie(cookieString: string, tokenKey = 'auth_token'): string | null {
    if (!cookieString) return null;
    const cookie = cookieString
        .split(';')
        .find((part) => part.trim().startsWith(`${tokenKey}=`));

    if (!cookie) return null;
    return cookie.split('=')[1] ?? null;
}

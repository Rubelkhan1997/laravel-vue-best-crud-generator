/**
 * Error Utility Functions
 * Extract and format error messages
 */

/**
 * Extract error message from API errors
 *
 * @param err - Unknown error object
 * @param fallback - Fallback message if no error found
 * @returns Error message string
 */
export function getErrorMessage(err: unknown, fallback: string): string {
    if (
        typeof err === 'object' &&
        err !== null &&
        'response' in err &&
        typeof (err as Record<string, any>).response?.data?.message === 'string'
    ) {
        return (err as Record<string, any>).response.data.message;
    }
    return fallback;
}

/**
 * Alias for getErrorMessage (for composables)
 *
 * @param err - Unknown error object
 * @param fallback - Fallback message if no error found
 * @returns Error message string
 */
export const getApiError = getErrorMessage;

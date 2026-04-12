/**
 * Date Utility Functions
 * Date formatting and manipulation
 */

/**
 * Format date to readable string
 */
// FILE: resources/js/Utils/date.ts

/**
 * Date Utility Functions
 * Date formatting and manipulation
 */

/**
 * Format date to readable string
 */
export function formatDate(date: Date | string, format: string = 'DD-MM-YYYY'): string {
    const d = typeof date === 'string' ? new Date(date) : date;

    if (isNaN(d.getTime())) {
        return 'Invalid Date';
    }

    const monthsShort = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const monthShort = monthsShort[d.getMonth()];
    const day = String(d.getDate()).padStart(2, '0');
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    const seconds = String(d.getSeconds()).padStart(2, '0');

    return format
        .replace('YYYY', String(year))
        .replace('MM', monthShort) // must be before MM
        .replace('MM', month)
        .replace('DD', day)
        .replace('HH', hours)
        .replace('mm', minutes)
        .replace('ss', seconds);
}

/**
 * Format date to relative time (e.g., "2 hours ago")
 */
export function formatRelativeTime(date: Date | string): string {
    const d = typeof date === 'string' ? new Date(date) : date;
    const now = new Date();
    const diffMs = now.getTime() - d.getTime();
    const diffMins = Math.floor(diffMs / (1000 * 60));
    const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

    if (diffMins < 1) {
        return 'Just now';
    } else if (diffMins < 60) {
        return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
    } else if (diffHours < 24) {
        return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
    } else if (diffDays < 7) {
        return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
    } else {
        return formatDate(d, 'MM DD, YYYY');
    }
}

/**
 * Check if date is today
 */
export function isToday(date: Date | string): boolean {
    const d = typeof date === 'string' ? new Date(date) : date;
    const today = new Date();
    return d.toDateString() === today.toDateString();
}

/**
 * Check if date is in the past
 */
export function isPast(date: Date | string): boolean {
    const d = typeof date === 'string' ? new Date(date) : date;
    return d.getTime() < new Date().getTime();
}

/**
 * Check if date is in the future
 */
export function isFuture(date: Date | string): boolean {
    const d = typeof date === 'string' ? new Date(date) : date;
    return d.getTime() > new Date().getTime();
}

/**
 * Add days to a date
 */
export function addDays(date: Date | string, days: number): Date {
    const d = typeof date === 'string' ? new Date(date) : date;
    const result = new Date(d);
    result.setDate(result.getDate() + days);
    return result;
}

/**
 * Get difference in days between two dates
 */
export function diffDays(date1: Date | string, date2: Date | string): number {
    const d1 = typeof date1 === 'string' ? new Date(date1) : date1;
    const d2 = typeof date2 === 'string' ? new Date(date2) : date2;
    const diffMs = Math.abs(d2.getTime() - d1.getTime());
    return Math.floor(diffMs / (1000 * 60 * 60 * 24));
}

/**
 * Format duration (minutes) to human readable string
 */
export function formatDuration(minutes: number): string {
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;

    if (hours === 0) {
        return `${mins}m`;
    } else if (mins === 0) {
        return `${hours}h`;
    } else {
        return `${hours}h ${mins}m`;
    }
}

/**
 * Get start of day
 */
export function startOfDay(date: Date | string = new Date()): Date {
    const d = typeof date === 'string' ? new Date(date) : date;
    return new Date(d.getFullYear(), d.getMonth(), d.getDate(), 0, 0, 0, 0);
}

/**
 * Get end of day
 */
export function endOfDay(date: Date | string = new Date()): Date {
    const d = typeof date === 'string' ? new Date(date) : date;
    return new Date(d.getFullYear(), d.getMonth(), d.getDate(), 23, 59, 59, 999);
}

/**
 * Calculate number of nights between check-in and check-out dates
 *
 * @param checkIn - Check-in date
 * @param checkOut - Check-out date
 * @returns Number of nights
 */
export function calculateNights(checkIn: Date | string, checkOut: Date | string): number {
    const d1 = typeof checkIn === 'string' ? new Date(checkIn) : checkIn;
    const d2 = typeof checkOut === 'string' ? new Date(checkOut) : checkOut;

    if (isNaN(d1.getTime()) || isNaN(d2.getTime())) {
        return 0;
    }

    const diffTime = Math.abs(d2.getTime() - d1.getTime());
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
}

/**
 * Format Utility Functions
 * Number, currency, and string formatting
 */

import { formatDate } from './date';

/**
 * Format number with thousand separators
 */
export function formatNumber(num: number, decimals: number = 0): string {
    return num.toLocaleString('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
}

/**
 * Format currency (BDT)
 */
export function formatCurrency(amount: number, currency: string = 'BDT'): string {
    const symbols: Record<string, string> = {
        BDT: '৳',
        USD: '$',
        EUR: '€',
        GBP: '£',
        INR: '₹',
        JPY: '¥'
    };

    const symbol = symbols[currency] || currency;
    const formatted = formatNumber(amount, 2);

    return `${symbol}${formatted}`;
}

/**
 * Format percentage
 */
export function formatPercentage(value: number, decimals: number = 0): string {
    return `${formatNumber(value, decimals)}%`;
}

/**
 * Format phone number (Bangladesh format)
 */
export function formatPhone(phone: string): string {
    // Remove all non-digits
    const cleaned = phone.replace(/\D/g, '');

    // Bangladesh format: +880 XX XXXXXX
    if (cleaned.length === 11 && cleaned.startsWith('0')) {
        return `+880 ${cleaned.slice(1, 3)} ${cleaned.slice(3)}`;
    } else if (cleaned.length === 13 && cleaned.startsWith('880')) {
        return `+${cleaned.slice(0, 3)} ${cleaned.slice(3, 5)} ${cleaned.slice(5)}`;
    }

    return phone;
}

/**
 * Format email with ellipsis if too long
 */
export function formatEmail(email: string, maxLength: number = 30): string {
    if (email.length <= maxLength) {
        return email;
    }

    const [local, domain] = email.split('@');
    const truncatedLocal = local.slice(0, maxLength - domain.length - 4);
    return `${truncatedLocal}...@${domain}`;
}

/**
 * Format name (capitalize first letter of each word)
 */
export function formatName(name: string): string {
    return name
        .toLowerCase()
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
}

/**
 * Truncate text with ellipsis
 */
export function truncate(text: string, maxLength: number, suffix: string = '...'): string {
    if (text.length <= maxLength) {
        return text;
    }
    return text.slice(0, maxLength) + suffix;
}

/**
 * Format file size to human readable
 */
export function formatFileSize(bytes: number): string {
    if (bytes === 0) return '0 B';

    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return `${formatNumber(bytes / Math.pow(k, i), 2)} ${sizes[i]}`;
}

/**
 * Format room number with leading zeros
 */
export function formatRoomNumber(number: string | number, digits: number = 3): string {
    return String(number).padStart(digits, '0');
}

/**
 * Format reservation status to human readable
 */
export function formatStatus(status: string): string {
    return status.replace('_', ' ').toUpperCase();
}

/**
 * Parse and format currency string to number
 */
export function parseCurrency(value: string): number {
    // Remove currency symbols and commas
    const cleaned = value.replace(/[^0-9.-]/g, '');
    const parsed = parseFloat(cleaned);
    return isNaN(parsed) ? 0 : parsed;
}

/**
 * Format date range
 */
export function formatDateRange(start: Date | string, end: Date | string): string {
    const startDate = typeof start === 'string' ? new Date(start) : start;
    const endDate = typeof end === 'string' ? new Date(end) : end;

    const startStr = formatDate(startDate, 'MMM DD');
    const endStr = formatDate(endDate, 'DD, YYYY');

    return `${startStr} - ${endStr}`;
}

/**
 * Clamp number between min and max
 */
export function clamp(num: number, min: number, max: number): number {
    return Math.min(Math.max(num, min), max);
}

/**
 * Round to specific decimal places
 */
export function roundTo(num: number, decimals: number = 2): number {
    const multiplier = Math.pow(10, decimals);
    return Math.round(num * multiplier) / multiplier;
}

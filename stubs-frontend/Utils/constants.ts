/**
 * Application Constants
 * Central configuration for the application
 */

// ─────────────────────────────────────────────────────────
// API Configuration
// ─────────────────────────────────────────────────────────

export const API_CONFIG = {
    BASE_URL: import.meta.env.VITE_API_BASE_URL || '/api',
    VERSION: 'v1',
    TIMEOUT: 30000, // 30 seconds
    MAX_RETRIES: 3
} as const;

// ─────────────────────────────────────────────────────────
// Pagination Defaults
// ─────────────────────────────────────────────────────────

export const PAGINATION = {
    DEFAULT_PER_PAGE: 15,
    PER_PAGE_OPTIONS: [10, 15, 25, 50, 100],
    MAX_PER_PAGE: 100
} as const;

// ─────────────────────────────────────────────────────────
// Date & Time
// ─────────────────────────────────────────────────────────

export const DATE_FORMATS = {
    DISPLAY: 'MMM DD, YYYY',
    DISPLAY_TIME: 'MMM DD, YYYY HH:mm',
    INPUT: 'YYYY-MM-DD',
    INPUT_DATETIME: 'YYYY-MM-DD HH:mm:ss',
    ISO: 'YYYY-MM-DDTHH:mm:ss.SSSZ'
} as const;

export const TIMEZONE = {
    DEFAULT: 'Asia/Dhaka',
    UTC: 'UTC'
} as const;

// ─────────────────────────────────────────────────────────
// Reservation Status
// ─────────────────────────────────────────────────────────

export const RESERVATION_STATUS = {
    PENDING: 'pending',
    CONFIRMED: 'confirmed',
    CHECKED_IN: 'checked_in',
    CHECKED_OUT: 'checked_out',
    CANCELLED: 'cancelled',
    NO_SHOW: 'no_show'
} as const;

export const RESERVATION_STATUS_LABELS: Record<string, string> = {
    pending: 'Pending',
    confirmed: 'Confirmed',
    checked_in: 'Checked In',
    checked_out: 'Checked Out',
    cancelled: 'Cancelled',
    no_show: 'No Show'
} as const;

export const RESERVATION_STATUS_COLORS: Record<string, string> = {
    pending: 'yellow',
    confirmed: 'green',
    checked_in: 'blue',
    checked_out: 'gray',
    cancelled: 'red',
    no_show: 'orange'
} as const;

// ─────────────────────────────────────────────────────────
// Room Status
// ─────────────────────────────────────────────────────────

export const ROOM_STATUS = {
    AVAILABLE: 'available',
    OCCUPIED: 'occupied',
    MAINTENANCE: 'maintenance',
    DIRTY: 'dirty'
} as const;

export const ROOM_STATUS_LABELS: Record<string, string> = {
    available: 'Available',
    occupied: 'Occupied',
    maintenance: 'Maintenance',
    dirty: 'Dirty'
} as const;

export const ROOM_STATUS_COLORS: Record<string, string> = {
    available: 'green',
    occupied: 'blue',
    maintenance: 'red',
    dirty: 'yellow'
} as const;

// ─────────────────────────────────────────────────────────
// Task Priority
// ─────────────────────────────────────────────────────────

export const PRIORITY = {
    LOW: 'low',
    MEDIUM: 'medium',
    HIGH: 'high',
    URGENT: 'urgent'
} as const;

export const PRIORITY_LABELS: Record<string, string> = {
    low: 'Low',
    medium: 'Medium',
    high: 'High',
    urgent: 'Urgent'
} as const;

export const PRIORITY_COLORS: Record<string, string> = {
    low: 'gray',
    medium: 'blue',
    high: 'orange',
    urgent: 'red'
} as const;

// ─────────────────────────────────────────────────────────
// File Upload
// ─────────────────────────────────────────────────────────

export const FILE_UPLOAD = {
    MAX_SIZE: 10 * 1024 * 1024, // 10MB
    ALLOWED_IMAGE_TYPES: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
    ALLOWED_DOCUMENT_TYPES: ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
    ALLOWED_SPREADSHEET_TYPES: ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
} as const;

// ─────────────────────────────────────────────────────────
// User Roles
// ─────────────────────────────────────────────────────────

export const USER_ROLES = {
    ADMIN: 'admin',
    MANAGER: 'manager',
    RECEPTIONIST: 'receptionist',
    HOUSEKEEPING: 'housekeeping',
    ACCOUNTANT: 'accountant'
} as const;

export const USER_ROLE_LABELS: Record<string, string> = {
    admin: 'Administrator',
    manager: 'Manager',
    receptionist: 'Receptionist',
    housekeeping: 'Housekeeping Staff',
    accountant: 'Accountant'
} as const;

// ─────────────────────────────────────────────────────────
// LocalStorage Keys
// ─────────────────────────────────────────────────────────

export const STORAGE_KEYS = {
    AUTH_TOKEN: 'auth_token',
    USER: 'user',
    PREFERENCES: 'preferences',
    THEME: 'theme',
    LANGUAGE: 'language',
    LAST_ROUTE: 'last_route'
} as const;

// ─────────────────────────────────────────────────────────
// Cache Duration (in minutes)
// ─────────────────────────────────────────────────────────

export const CACHE_DURATION = {
    SHORT: 5,
    MEDIUM: 30,
    LONG: 60,
    VERY_LONG: 1440 // 24 hours
} as const;

// ─────────────────────────────────────────────────────────
// Polling Intervals (in milliseconds)
// ─────────────────────────────────────────────────────────

export const POLLING_INTERVALS = {
    FAST: 10000,    // 10 seconds
    NORMAL: 30000,  // 30 seconds
    SLOW: 60000     // 1 minute
} as const;

// ─────────────────────────────────────────────────────────
// Debounce & Throttle
// ─────────────────────────────────────────────────────────

export const DEBOUNCE = {
    SHORT: 200,
    NORMAL: 300,
    LONG: 500
} as const;

export const THROTTLE = {
    SHORT: 100,
    NORMAL: 200,
    LONG: 300
} as const;

// ─────────────────────────────────────────────────────────
// UI Constants
// ─────────────────────────────────────────────────────────

export const UI = {
    TABLE: {
        DEFAULT_PAGE_SIZE: 15,
        PAGE_SIZE_OPTIONS: [10, 15, 25, 50, 100]
    },
    MODAL: {
        CLOSE_ON_OUTSIDE_CLICK: true,
        CLOSE_ON_ESCAPE: true
    },
    TOAST: {
        DURATION: 5000,
        MAX_COUNT: 5
    },
    CONFIRM: {
        TITLE: 'Are you sure?',
        CONFIRM_TEXT: 'Yes',
        CANCEL_TEXT: 'No'
    }
} as const;

// ─────────────────────────────────────────────────────────
// Export all as default
// ─────────────────────────────────────────────────────────

export default {
    API_CONFIG,
    PAGINATION,
    DATE_FORMATS,
    TIMEZONE,
    RESERVATION_STATUS,
    ROOM_STATUS,
    PRIORITY,
    FILE_UPLOAD,
    USER_ROLES,
    STORAGE_KEYS,
    CACHE_DURATION,
    POLLING_INTERVALS,
    DEBOUNCE,
    THROTTLE,
    UI
};

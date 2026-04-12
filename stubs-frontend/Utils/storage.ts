/**
 * Storage Utility Functions
 * LocalStorage and SessionStorage helpers
 */

export type StorageType = 'local' | 'session';

export interface StorageOptions {
    type?: StorageType;
    prefix?: string;
}

const defaultOptions: Required<StorageOptions> = {
    type: 'local',
    prefix: 'pms_'
};

/**
 * Get storage object by type
 */
function getStorage(type: StorageType): Storage {
    return type === 'local' ? window.localStorage : window.sessionStorage;
}

/**
 * Get full key with prefix
 */
function getFullKey(key: string, prefix: string): string {
    return `${prefix}${key}`;
}

/**
 * Set item in storage
 */
export function setItem<T = any>(
    key: string,
    value: T,
    options: StorageOptions = {}
): void {
    const { type, prefix } = { ...defaultOptions, ...options };
    const storage = getStorage(type);
    const fullKey = getFullKey(key, prefix);

    try {
        const serialized = JSON.stringify(value);
        storage.setItem(fullKey, serialized);
    } catch (error) {
        console.error('Error setting item in storage:', error);
    }
}

/**
 * Get item from storage
 */
export function getItem<T = any>(
    key: string,
    defaultValue?: T,
    options: StorageOptions = {}
): T | undefined {
    const { type, prefix } = { ...defaultOptions, ...options };
    const storage = getStorage(type);
    const fullKey = getFullKey(key, prefix);

    try {
        const item = storage.getItem(fullKey);
        if (item === null) {
            return defaultValue;
        }
        return JSON.parse(item) as T;
    } catch (error) {
        console.error('Error getting item from storage:', error);
        return defaultValue;
    }
}

/**
 * Remove item from storage
 */
export function removeItem(key: string, options: StorageOptions = {}): void {
    const { type, prefix } = { ...defaultOptions, ...options };
    const storage = getStorage(type);
    const fullKey = getFullKey(key, prefix);

    try {
        storage.removeItem(fullKey);
    } catch (error) {
        console.error('Error removing item from storage:', error);
    }
}

/**
 * Clear all items with prefix from storage
 */
export function clearStorage(options: StorageOptions = {}): void {
    const { type, prefix } = { ...defaultOptions, ...options };
    const storage = getStorage(type);

    try {
        const keys = storage.keys();
        keys.forEach((key: string) => {
            if (key.startsWith(prefix)) {
                storage.removeItem(key);
            }
        });
    } catch (error) {
        console.error('Error clearing storage:', error);
    }
}

/**
 * Check if item exists in storage
 */
export function hasItem(key: string, options: StorageOptions = {}): boolean {
    const { type, prefix } = { ...defaultOptions, ...options };
    const storage = getStorage(type);
    const fullKey = getFullKey(key, prefix);

    return storage.getItem(fullKey) !== null;
}

/**
 * Get all keys with prefix
 */
export function getAllKeys(options: StorageOptions = {}): string[] {
    const { type, prefix } = { ...defaultOptions, ...options };
    const storage = getStorage(type);

    const keys: string[] = [];
    for (let i = 0; i < storage.length; i++) {
        const key = storage.key(i);
        if (key && key.startsWith(prefix)) {
            keys.push(key.replace(prefix, ''));
        }
    }
    return keys;
}

/**
 * Get all values with prefix
 */
export function getAllItems<T = any>(options: StorageOptions = {}): Record<string, T> {
    const keys = getAllKeys(options);
    const items: Record<string, T> = {};

    keys.forEach(key => {
        const value = getItem<T>(key, undefined, options);
        if (value !== undefined) {
            items[key] = value;
        }
    });

    return items;
}

/**
 * Set item with expiration time (in minutes)
 */
export function setItemWithExpiry<T = any>(
    key: string,
    value: T,
    ttlMinutes: number,
    options: StorageOptions = {}
): void {
    const { type, prefix } = { ...defaultOptions, ...options };
    const storage = getStorage(type);
    const fullKey = getFullKey(key, prefix);

    try {
        const now = new Date();
        const item = {
            value,
            expiry: now.getTime() + (ttlMinutes * 60 * 1000)
        };
        storage.setItem(fullKey, JSON.stringify(item));
    } catch (error) {
        console.error('Error setting item with expiry:', error);
    }
}

/**
 * Get item with expiration check
 */
export function getItemWithExpiry<T = any>(
    key: string,
    defaultValue?: T,
    options: StorageOptions = {}
): T | undefined {
    const { type, prefix } = { ...defaultOptions, ...options };
    const storage = getStorage(type);
    const fullKey = getFullKey(key, prefix);

    try {
        const item = storage.getItem(fullKey);
        if (item === null) {
            return defaultValue;
        }

        const parsed = JSON.parse(item);
        const now = new Date();

        if (now.getTime() > parsed.expiry) {
            storage.removeItem(fullKey);
            return defaultValue;
        }

        return parsed.value as T;
    } catch (error) {
        console.error('Error getting item with expiry:', error);
        return defaultValue;
    }
}

/**
 * Storage wrapper class for reactive usage
 */
export class StorageWrapper {
    private options: Required<StorageOptions>;

    constructor(options: StorageOptions = {}) {
        this.options = { ...defaultOptions, ...options };
    }

    set<T = any>(key: string, value: T): void {
        setItem(key, value, this.options);
    }

    get<T = any>(key: string, defaultValue?: T): T | undefined {
        return getItem(key, defaultValue, this.options);
    }

    remove(key: string): void {
        removeItem(key, this.options);
    }

    has(key: string): boolean {
        return hasItem(key, this.options);
    }

    clear(): void {
        clearStorage(this.options);
    }

    keys(): string[] {
        return getAllKeys(this.options);
    }

    all<T = any>(): Record<string, T> {
        return getAllItems<T>(this.options);
    }
}

/**
 * Common/Shared Types
 * Reusable across all modules
 */

export interface ApiResponse<T = any> {
    success: boolean;
    data: T;
    message?: string;
    errors?: Record<string, string[]>;
}

export interface PaginatedResponse<T = any> {
    data: T[];
    current_page: number;
    per_page: number;
    total: number;
    last_page: number;
    from: number;
    to: number;
}

export interface PaginationParams {
    page?: number;
    per_page?: number;
    sort?: string;
    order?: 'asc' | 'desc';
}

export interface BaseResource {
    id: number;
    created_at?: string;
    updated_at?: string;
    deleted_at?: string | null;
}

export interface User {
    id: number;
    name: string;
    email: string;
    role?: UserRole;
    avatar?: string;
    is_active?: boolean;
    created_at?: string;
    updated_at?: string;
}

export type UserRole = 'admin' | 'manager' | 'receptionist' | 'housekeeping' | 'accountant';

export interface SelectOption {
    value: string | number;
    label: string;
    disabled?: boolean;
}

export interface TablePagination {
    current_page: number;
    per_page: number;
    total: number;
    last_page: number;
}

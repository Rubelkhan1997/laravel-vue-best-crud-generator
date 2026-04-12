/**
 * Auth Types
 */

import type { ApiResponse } from '@/Types/api';

// ============================================================================
// User Types
// ============================================================================
export interface User {
    id: number;
    name: string;
    email: string;
    role: UserRole;
    roles?: string[];
    permissions?: string[];
    email_verified_at?: string;
    created_at?: string;
    updated_at?: string;
}

export type UserRole = 'admin' | 'staff' | 'manager';

// ============================================================================
// DTO Types (Form Data)
// ============================================================================
export interface LoginDto {
    email: string;
    password: string;
    remember?: boolean;
}

export interface RegisterDto {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
    role?: UserRole;
}

// ============================================================================
// Auth State
// ============================================================================
export interface AuthState {
    user: User | null;
    isAuthenticated: boolean;
    loading: boolean;
    loadingAuth: boolean;
    error: string | null;
}

// ============================================================================
// Auth Response Types
// ============================================================================
export interface AuthUserResponse {
    user: User;
    token: string;
}

export type LoginResponse = ApiResponse<AuthUserResponse>;
export type RegisterResponse = ApiResponse<AuthUserResponse>;
export type MeResponse = ApiResponse<User>;
export type LogoutResponse = ApiResponse<void>;

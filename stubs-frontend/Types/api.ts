/**
 * API Types
 * For API requests and responses
 */

import type { PaginatedResponse } from './common';

// ============================================================================
// Re-export common types for convenience
// ============================================================================
export type { PaginatedResponse } from './common';

/**
 * Standard API Response
 * Your API returns: { status: 0|1, data: T|null, message: string }
 */
export interface ApiResponse<T = any> {
    status: 0 | 1;
    data: T | null;
    message: string;
}

export interface ApiError {
    message: string;
    errors?: Record<string, string[]>;
    status?: number;
}

export interface ApiSuccess<T = any> {
    message?: string;
    data: T;
}

// Request types
export interface ListRequestParams {
    page?: number;
    limit?: number;
    search?: string;
    sort?: string;
    order?: 'asc' | 'desc';
    filters?: Record<string, any>;
}

export interface CreateRequest<T> {
    data: T;
}

export interface UpdateRequest<T> {
    id: number;
    data: T;
}

export interface DeleteRequest {
    id: number;
}

// Response types
export type ListResponse<T> = PaginatedResponse<T>;

export interface ActionResponse {
    success: boolean;
    message: string;
    data?: any;
}

// Upload types
export interface UploadResponse {
    url: string;
    path: string;
    filename: string;
    size: number;
    mime_type: string;
}

export interface UploadRequest {
    file: File;
    folder?: string;
}

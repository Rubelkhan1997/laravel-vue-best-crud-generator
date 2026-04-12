/**
 * Types Barrel Export
 * Central export file for all TypeScript types
 * 
 * Usage:
 *   // Direct import (recommended for stores & components)
 *   import type { Reservation, ReservationStatus } from '@/types';
 * 
 *   // Global namespace (for template hints)
 *   const res: PMS.Reservation = null;
 */

// Common types
export type { 
    User, 
    UserRole 
} from './common';

export type {
    ApiResponse,
    PaginatedResponse,
    PaginationParams,
    BaseResource,
    SelectOption,
    TablePagination
} from './common';

// API types
export type {
    ApiError,
    ApiSuccess,
    ListRequestParams,
    UploadResponse
} from './api';

// Payment types
export type { 
    Payment, 
    PaymentMethod, 
    PaymentStatus 
} from './payment';

// FrontDesk module types
export type {
    Reservation,
    ReservationStatus,
    Guest,
    Room,
    ReservationFilters,
    ReservationPagination,
    CreateReservationDto,
    UpdateReservationDto,
} from './FrontDesk/reservation';

// FrontDesk module types
export type {
    LoginDto,
    RegisterDto,
    AuthState,
    AuthUserResponse,
} from './Auth/auth';



// Housekeeping module types
// export type { HousekeepingTask, TaskStatus, TaskPriority } from './Housekeeping/task';

// ============================================================================
// Note: Global PMS namespace moved to global.d.ts
// This file only handles explicit exports for direct imports
// ============================================================================

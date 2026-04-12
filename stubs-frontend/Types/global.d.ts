/**
 * Global TypeScript Declarations
 * For ambient type definitions and namespace augmentation
 */

// ============================================================================
// PMS Global Namespace
// For template type hints and global type access
// ============================================================================
declare global {
    namespace PMS {
        // Common types
        type User = import('./common').User;
        type UserRole = import('./common').UserRole;
        type ApiResponse<T = any> = import('./common').ApiResponse<T>;
        type PaginatedResponse<T = any> = import('./common').PaginatedResponse<T>;
        type PaginationParams = import('./common').PaginationParams;
        type BaseResource = import('./common').BaseResource;
        type SelectOption = import('./common').SelectOption;
        type TablePagination = import('./common').TablePagination;

        // API types
        type ApiError = import('./api').ApiError;
        type ApiSuccess<T = any> = import('./api').ApiSuccess<T>;
        type ListRequestParams = import('./api').ListRequestParams;
        type UploadResponse = import('./api').UploadResponse;

        // FrontDesk types
        type Reservation = import('./FrontDesk/reservation').Reservation;
        type ReservationStatus = import('./FrontDesk/reservation').ReservationStatus;
        type ReservationGuest = import('./FrontDesk/reservation').Guest;
        type ReservationRoom = import('./FrontDesk/reservation').Room;

        // Room types
        type RoomStatus = import('./FrontDesk/room').RoomStatus;
        type RoomType = import('./FrontDesk/room').RoomType;

        // Guest types
        type GuestProfile = import('./FrontDesk/guest').GuestProfile;
        type GuestType = import('./FrontDesk/guest').GuestType;

        // Housekeeping types
        type HousekeepingTask = import('./Housekeeping/task').HousekeepingTask;
        type TaskStatus = import('./Housekeeping/task').TaskStatus;
        type TaskPriority = import('./Housekeeping/task').TaskPriority;

        // Payment types
        type Payment = import('./payment').Payment;
        type PaymentMethod = import('./payment').PaymentMethod;
        type PaymentStatus = import('./payment').PaymentStatus;
    }
}

/**
 * Explicit export to make this file a module
 * This prevents the global declarations from affecting global scope
 */
export {};

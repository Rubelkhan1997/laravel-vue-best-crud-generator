/**
 * Composables Barrel Export
 * Central export file for all composables
 */

// Generic composables (Vue dependent)
export { useLoading } from './useLoading';
export { useMessage } from './useMessage';
export { usePolling } from './usePolling';

// i18n composable
export { useI18n } from './useI18n';
export { usePermissionService } from './usePermissionService';

// Auth composable
export { useAuth } from './Auth/useAuth';

// FrontDesk module composables
// export { useReservations } from './FrontDesk/useReservations';
// export { useRooms } from './FrontDesk/useRooms';
// export type {
//     ReservationFilters,
//     UseReservationOptions
// } from './FrontDesk/useReservations';

 

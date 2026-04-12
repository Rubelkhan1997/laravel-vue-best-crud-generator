import { computed, onMounted, onUnmounted, inject } from 'vue';
import { useAuthStore } from '@/Stores/Auth/authStore';
import { useLoading } from '@/Composables/useLoading';
import { getApiError } from '@/Helpers/error';
import type { ApiResponse } from '@/Types/api';
import type { toast as ToastType } from '@/Plugins/toast';
import type { LoginDto, RegisterDto, User, LoginResponse, RegisterResponse } from '@/Types/Auth/auth';

// ─────────────────────────────────────────────────────────
// Types
// ─────────────────────────────────────────────────────────

export interface UseAuthOptions {
    autoFetch?: boolean;
    pollingInterval?: number;
}

// ─────────────────────────────────────────────────────────
// Composable
// ─────────────────────────────────────────────────────────

export function useAuth(options: UseAuthOptions = {}) {

    const {
        autoFetch = false,
        pollingInterval = 0,
    } = options;

    // ─── Store (internal) ────────────────────────────────
    const store = useAuthStore();

    // ─── Inject Toast ────────────────────────────────────
    const toast = inject('toast') as typeof ToastType;

    // ─── UI Helpers ──────────────────────────────────────
    const { loading: _loading, start: startLoading, stop: stopLoading } = useLoading();
    const { loading: _saving, start: startSaving, stop: stopSaving } = useLoading();

    // ─────────────────────────────────────────────────────
    // Reactive State
    // ─────────────────────────────────────────────────────

    const user = computed(() => store.user);
    const isAuthenticated = computed(() => store.isAuthenticated);
    const error = computed(() => store.error);

    const loading = computed(() => _loading.value || store.loading);
    const loadingAuth = computed(() => _saving.value || store.loadingAuth);

    // ─── Derived (Store Getters) ─────────────────────────
    const isAdmin = computed(() => store.isAdmin);
    const isStaff = computed(() => store.isStaff);
    const isManager = computed(() => store.isManager);
    const userName = computed(() => store.userName);
    const userEmail = computed(() => store.userEmail);
    const userRole = computed(() => store.userRole);

    // ─────────────────────────────────────────────────────
    // Actions
    // ─────────────────────────────────────────────────────

    /**
     * Login user
     */
    async function login(dto: LoginDto): Promise<LoginResponse> {
        startSaving();
        try {
            const result = await store.login(dto);
            // Show toast based on API response status
            if (result.status === 1) {
                toast.success(result.message);
            } else {
                toast.error(result.message);
            }
            return result;
        } catch (err: unknown) {
            toast.error(getApiError(err, 'Login failed'));
            throw err;
        } finally {
            stopSaving();
        }
    }

    /**
     * Register new user
     */
    async function register(dto: RegisterDto): Promise<RegisterResponse> {
        startSaving();
        try {
            const result = await store.register(dto);
            // Show toast based on API response status
            if (result.status === 1) {
                toast.success(result.message);
            } else {
                toast.error(result.message);
            }
            return result;
        } catch (err: unknown) {
            toast.error(getApiError(err, 'Registration failed'));
            throw err;
        } finally {
            stopSaving();
        }
    }

    /**
     * Logout user
     */
    async function logout(): Promise<ApiResponse<void>> {
        startLoading();
        try {
            const result = await store.logout();
            // Show toast based on API response status
            if (result.status === 1) {
                toast.success(result.message);
            } else {
                toast.error(result.message);
            }
            return result;
        } catch (err: unknown) {
            toast.error(getApiError(err, 'Logout failed'));
            throw err;
        } finally {
            stopLoading();
        }
    }

    /**
     * Fetch current user
     */
    async function fetchUser(): Promise<ApiResponse<User>> {
        startLoading();
        try {
            const result = await store.fetchUser();
            return result;
        } catch (err: unknown) {
            toast.error(getApiError(err, 'Failed to fetch user'));
            throw err;
        } finally {
            stopLoading();
        }
    }

    /**
     * Check if user has specific role
     */
    function hasRole(role: string): boolean {
        return store.user?.role === role;
    }

    /**
     * Check if user has any of the given roles
     */
    function hasAnyRole(roles: string[]): boolean {
        return roles.includes(store.user?.role ?? '');
    }

    /**
     * Initialize user from Inertia props
     */
    function initializeFromInertia(user: User | null): void {
        store.initializeFromInertia(user);
    }

    /**
     * Clear error
     */
    function clearError(): void {
        store.clearError();
    }

    // ─── Lifecycle ───────────────────────────────────────

    if (autoFetch) {
        onMounted(() => {
            fetchUser();
        });

        onUnmounted(() => {
            // Cleanup if needed
        });
    }

    // ─────────────────────────────────────────────────────
    // Public API
    // ─────────────────────────────────────────────────────

    return {
        // State
        user,
        isAuthenticated,
        loading,
        loadingAuth,
        error,

        // Derived
        isAdmin,
        isStaff,
        isManager,
        userName,
        userEmail,
        userRole,

        // Actions
        login,
        register,
        logout,
        fetchUser,
        hasRole,
        hasAnyRole,
        initializeFromInertia,
        clearError,
    };
}

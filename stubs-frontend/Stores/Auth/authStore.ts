import { defineStore } from 'pinia';
import apiClient from '@/Services/apiClient';
import { getErrorMessage } from '@/Helpers/error';
import { removeToken, setToken } from '@/Utils/authToken';
import type {
    User,
    AuthState,
    LoginDto,
    RegisterDto,
    LoginResponse,
    RegisterResponse,
    MeResponse,
    LogoutResponse,
} from '@/Types/Auth/auth';

// ─────────────────────────────────────────────────────────
// Initial State
// ─────────────────────────────────────────────────────────
const createInitialState = (): AuthState => ({
    user: null,
    isAuthenticated: false,
    loading: false,
    loadingAuth: false,
    error: null,
});

// ─────────────────────────────────────────────────────────
// Store
// ─────────────────────────────────────────────────────────
export const useAuthStore = defineStore('auth', {

    state: (): AuthState => createInitialState(),

    // ─────────────────────────────────────────────────────
    // Getters
    // ─────────────────────────────────────────────────────
    getters: {
        isAdmin: (state): boolean => state.user?.role === 'admin',
        isStaff: (state): boolean => state.user?.role === 'staff',
        isManager: (state): boolean => state.user?.role === 'manager',

        userName: (state): string => state.user?.name ?? 'Guest',
        userEmail: (state): string => state.user?.email ?? '',
        userRole: (state): string => state.user?.role ?? 'guest',
    },

    // ─────────────────────────────────────────────────────
    // Actions
    // ─────────────────────────────────────────────────────
    actions: {

        /**
         * Set authenticated user
         */
        setUser(user: User | null): void {
            this.user = user;
            this.isAuthenticated = !!user;
            this.error = null;
        },

        /**
         * Clear user data (logout)
         */
        clearUser(): void {
            removeToken();
             
            this.$patch({
                user: null,
                isAuthenticated: false,
                error: null,
            });
        },

        /**
         * Set loading state
         */
        setLoading(loading: boolean): void {
            this.loading = loading;
        },

        /**
         * Set auth loading state
         */
        setLoadingAuth(loading: boolean): void {
            this.loadingAuth = loading;
        },

        /**
         * Set error
         */
        setError(error: string | null): void {
            this.error = error;
        },

        /**
         * Clear error
         */
        clearError(): void {
            this.error = null;
        },

        /**
         * Login user
         */
        async login(dto: LoginDto): Promise<LoginResponse> {
            this.loadingAuth = true;
            this.error = null;
            try {
                const { data } = await apiClient.v1.post('/auth/login', dto);
                const response = data as LoginResponse;

                if (response.status === 1 && response.data) {
                    this.setUser(response.data.user);
                    // Save token to localStorage AND cookie
                    if (response.data) {
                        setToken(response.data, 1);
                    }
                }

                return response;
            } catch (err: unknown) {
                this.error = getErrorMessage(err, 'Login failed');
                throw err;
            } finally {
                this.loadingAuth = false;
            }
        },

        /**
         * Register new user
         */
        async register(dto: RegisterDto): Promise<RegisterResponse> {
            this.loadingAuth = true;
            this.error = null;
            try {
                const { data } = await apiClient.v1.post('/auth/register', dto);
                const response = data as RegisterResponse;

                if (response.status === 1 && response.data) {
                    this.setUser(response.data.user);
                    // Save token to localStorage AND cookie
                    if (response.data) {
                        setToken(response.data, 1);
                    }
                }

                return response;
            } catch (err: unknown) {
                this.error = getErrorMessage(err, 'Registration failed');
                throw err;
            } finally {
                this.loadingAuth = false;
            }
        },

        /**
         * Logout user
         */
        async logout(): Promise<LogoutResponse> {
            this.loading = true;
            this.error = null;
            try {
                const { data } = await apiClient.v1.post('/auth/logout');
                const response = data as LogoutResponse;

                if (response.status === 1) {
                    this.clearUser();
                }

                return response;
            } catch (err: unknown) {
                this.error = getErrorMessage(err, 'Logout failed');
                throw err;
            } finally {
                this.loading = false;
            }
        },

        /**
         * Fetch current user
         */
        async fetchUser(): Promise<MeResponse> {
            this.loading = true;
            this.error = null;
            try {
                const { data } = await apiClient.v1.get('/auth/me');
                const response = data as MeResponse;

                if (response.status === 1 && response.data) {
                    this.setUser(response.data);
                } else {
                    this.clearUser();
                }

                return response;
            } catch (err: unknown) {
                this.clearUser();
                this.error = getErrorMessage(err, 'Failed to fetch user');
                throw err;
            } finally {
                this.loading = false;
            }
        },

        /**
         * Initialize auth state from Inertia props
         */
        initializeFromInertia(user: User | null): void {
            this.setUser(user);
        },

        /**
         * Reset store to initial state
         */
        $reset(): void {
            this.$patch(createInitialState());
        },
    },
});

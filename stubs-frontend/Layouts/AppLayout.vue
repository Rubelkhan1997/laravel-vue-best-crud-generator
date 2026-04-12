<template>
    <div class="min-h-screen bg-gradient-to-b from-cyan-50 to-white">
        <!-- Header -->
        <header class="border-b border-cyan-200 bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 py-4">
                <div class="flex items-center justify-between">
                    <!-- Logo / Title -->
                    <div class="flex items-center gap-4">
                        <h1 class="text-xl font-bold text-slate-800">App Layout PMS</h1>
                        <nav class="hidden md:flex gap-4">
                            <Link
                                href="/dashboard"
                                class="text-sm text-slate-600 hover:text-cyan-600 transition"
                            >
                                Dashboard
                            </Link>
                        </nav>
                    </div>

                    <LanguageSwitcher></LanguageSwitcher>

                    <!-- User Menu -->
                    <div class="flex items-center gap-4">
                        <!-- User Info -->
                        <div class="hidden sm:block text-right">
                            <p class="text-sm font-medium text-slate-700">{{ userName }}</p>
                            <p class="text-xs text-slate-500 capitalize">{{ userRole }}</p>
                        </div>

                        <!-- Logout Button -->
                        <button
                            @click="handleLogout"
                            :disabled="isLoggingOut"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                            title="Logout"
                        >
                            <svg
                                v-if="isLoggingOut"
                                class="animate-spin h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                />
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                />
                            </svg>
                            <svg
                                v-else
                                class="h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                />
                            </svg>
                            <span class="hidden sm:inline">Logout</span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="mx-auto max-w-7xl p-4">
            <slot />
        </main>
    </div>
</template>

<script setup lang="ts">
    import { router, usePage } from '@inertiajs/vue3';
    import { computed, inject, onMounted } from 'vue';
    import { useAuth } from '@/Composables/Auth/useAuth';
    import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
    import type { confirm as ConfirmType } from '@/Plugins/confirm';


    // ─── Inject Confirm ─────────────────────────────────────
    const confirm = inject('confirm') as typeof ConfirmType;

    // ─────────────────────────────────────────────────────────
    // Composable
    // ─────────────────────────────────────────────────────────
    const { logout, userName, userRole, loading, initializeFromInertia } = useAuth();

    // ─────────────────────────────────────────────────────────
    // Initialize user from Inertia props
    // ─────────────────────────────────────────────────────────
    const page = usePage();
    
    onMounted(() => {
        // Initialize auth state from Inertia shared props (HandleInertiaRequests)
        const authUser = (page.props as any).auth?.user ?? null;

        if (authUser) {
            initializeFromInertia(authUser);
        }
    });

    // ─────────────────────────────────────────────────────────
    // Computed
    // ─────────────────────────────────────────────────────────
    const isLoggingOut = computed(() => loading.value);

    // ─────────────────────────────────────────────────────────
    // Methods
    // ─────────────────────────────────────────────────────────
    async function handleLogout() {
        const confirmed = await confirm.show({
            title: 'Logout?',
            message: `Are you sure you want to logout?`,
            confirmText: 'Yes',
            cancelText: 'No',
            variant: 'danger',
            icon: false,
        });

        if (!confirmed) return;

        try {
            await logout();
            // Redirect to login after successful logout
            router.visit('/login');
        } catch (error) {
            // Force logout even if API fails
            router.visit('/login');
        }
    }
</script>

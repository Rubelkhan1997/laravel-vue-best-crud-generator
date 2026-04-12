<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-b from-cyan-50 to-white py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo/Title -->
            <div class="text-center">
                <h2 class="mt-6 text-3xl font-extrabold text-slate-900">
                    Sign in to your account
                </h2>
                <p class="mt-2 text-sm text-slate-600">
                    Or
                    <Link href="/register" class="font-medium text-cyan-600 hover:text-cyan-500">
                        create a new account
                    </Link>
                </p>
            </div>

            <!-- Login Form -->
            <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
                <!-- Email -->
                <FormInput
                    v-model="form.email"
                    id="email"
                    label="Email address"
                    type="email"
                    placeholder="Enter your email"
                    :required="true"
                    :error="form.errors.email"
                />

                <!-- Password -->
                <FormInput
                    v-model="form.password"
                    id="password"
                    label="Password"
                    type="password"
                    placeholder="Enter your password"
                    :required="true"
                    :error="form.errors.password"
                />

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input
                            id="remember"
                            v-model="form.remember"
                            type="checkbox"
                            class="h-4 w-4 text-cyan-600 focus:ring-cyan-500 border-slate-300 rounded"
                        />
                        <label for="remember" class="ml-2 block text-sm text-slate-900">
                            Remember me
                        </label>
                    </div>
                </div>

                <!-- Error Alert -->
                <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-sm text-red-800">{{ error }}</p>
                </div>

                <!-- Submit Button -->
                <div>
                    <FormButton
                        type="submit"
                        :loading="loadingAuth"
                        :loading-text="'Signing in...'"
                        class="w-full"
                    >
                        Sign in
                    </FormButton>
                </div>
            </form>
        </div>
    </div>
</template>


<script setup lang="ts">
    import { computed } from 'vue';
    import { useForm, router } from '@inertiajs/vue3';
    import { useAuth } from '@/Composables/Auth/useAuth';
    import { useI18n } from '@/Composables/useI18n';
    import { hasToken } from '@/Utils/authToken';
    import { required, email as emailRule, validateInertiaForm } from '@/Utils/validation';
    import type { LoginDto } from '@/Types/Auth/auth';


    // ─── Layout ──────────────────────────────────────────────
    // Disable the default layout - this is a standalone auth page
    defineOptions({ layout: null });

    // ─── i18n ────────────────────────────────────────────────
    // useI18n: provides translation function 't'
    const { t } = useI18n();

    // ─── Composable ──────────────────────────────────────────
    // useAuth: provides login function and loading state
    // login: sends POST request with email/password
    // loadingAuth: boolean indicating if login API call is in progress
    const { login, loadingAuth } = useAuth();

    // ─── Guard: already authenticated (check token) ──────────
    // If user already has a token, redirect to dashboard immediately
    // Why: Logged-in users shouldn't see the login page
    if (hasToken()) {
        router.visit('/dashboard');
    }

    // ─── Form ────────────────────────────────────────────────
    // useForm: creates reactive form object with email, password, remember fields
    // form.errors: tracks validation errors per field
    // form.processing: true while form is being submitted
    const form = useForm<LoginDto & { remember: boolean }>({
        email:    '',
        password: '',
        remember: false,
    });

    // isSaving: true if form is processing OR login API call is in progress
    const isLoading   = computed(() => form.processing || loadingAuth.value);
    
    // submitLabel: dynamic button text
    // Shows "Signing in..." while loading, "Sign In" otherwise
    const submitLabel = computed(() => isLoading.value ? t('auth.signing_in') : t('auth.sign_in'));

    // ─── Submit ────────────────────────────────────────────── 
    async function submit(): Promise<void> {
        // Clear all previous validation errors
        form.clearErrors();
 
        // If any rule fails, validateForm returns false
        if (!validateForm()) {
            scrollToFirstError();
            return;
        }

        try {
            // Send login request with email, password, and remember preference
            const result = await login({ 
                email: form.email, 
                password: form.password, 
                remember: form.remember 
            });

            // On success, redirect to dashboard
            if (result?.status == 1) {
                form.reset();  
                router.visit('/dashboard');
            }
        } catch (err: unknown) {
            // Handle API errors
            const apiErr = err as Record<string, any>;

            // 422 = Validation Error (e.g., invalid email format)
            // Backend returns: { response: { data: { errors: { field: ['message'] } } } }
            if (apiErr?.response?.status === 422) {
                const backendErrors: Record<string, string[]> = apiErr.response.data?.errors ?? {};
                Object.entries(backendErrors).forEach(([key, messages]) => {
                    form.setError(key as any, messages[0]);  // Set first error message for each field
                });
                scrollToFirstError();
            } 
            // 401 = Unauthorized (wrong email/password combination)
            else if (apiErr?.response?.status === 401) {
                // Show generic "invalid credentials" message on email field
                form.setError('email', t('auth.invalid_credentials'));
            }
        }
    }

    // ─── Validation ────────────────────────────────────────── 
    function validateForm(): boolean {
        return validateInertiaForm(form, {
            email:    [required, emailRule],
            password: [required],
        });
    }

    // scrollToFirstError: auto-scrolls page to first field with validation error
    // Why: Improves UX by showing user which field needs attention
    // How: Finds first element with .border-red-500 class (applied on error)
    function scrollToFirstError(): void {
        setTimeout(() => {
            // Wait 100ms to ensure DOM has updated with error classes
            const firstError = document.querySelector('.border-red-500');
            // Scroll the error field into view with smooth animation
            firstError?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 100);
    }
</script>


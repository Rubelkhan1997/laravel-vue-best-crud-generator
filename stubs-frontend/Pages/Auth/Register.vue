<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-b from-cyan-50 to-white py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo/Title -->
            <div class="text-center">
                <h2 class="mt-6 text-3xl font-extrabold text-slate-900">
                    Create your account
                </h2>
                <p class="mt-2 text-sm text-slate-600">
                    Already have an account?
                    <Link href="/login" class="font-medium text-cyan-600 hover:text-cyan-500">
                        Sign in
                    </Link>
                </p>
            </div>

            <!-- Register Form -->
            <form class="mt-8 space-y-6" @submit.prevent="handleRegister">
                <!-- Name -->
                <FormInput
                    v-model="form.name"
                    id="name"
                    label="Full Name"
                    type="text"
                    placeholder="Enter your name"
                    :required="true"
                    :error="form.errors.name"
                />

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
                    placeholder="Create a password"
                    :required="true"
                    :error="form.errors.password"
                />

                <!-- Confirm Password -->
                <FormInput
                    v-model="form.password_confirmation"
                    id="password_confirmation"
                    label="Confirm Password"
                    type="password"
                    placeholder="Confirm your password"
                    :required="true"
                />

                <!-- Error Alert -->
                <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-sm text-red-800">{{ error }}</p>
                </div>

                <!-- Submit Button -->
                <div>
                    <FormButton
                        type="submit"
                        :loading="loadingAuth"
                        :loading-text="'Creating account...'"
                        class="w-full"
                    >
                        Create account
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
    import { required, email as emailRule, minLength, confirmed, validateInertiaForm } from '@/Utils/validation';
    import type { RegisterDto } from '@/Types/Auth/auth';

    // ─── Layout ──────────────────────────────────────────────
    // Disable the default layout - this is a standalone auth page
    defineOptions({ layout: null });

    // ─── i18n ────────────────────────────────────────────────
    // useI18n: provides translation function 't'
    const { t } = useI18n();

    // ─── Composable ──────────────────────────────────────────
    // useAuth: provides register function and loading state
    // register: sends POST request with user data to create account
    // loadingAuth: boolean indicating if register API call is in progress
    const { register, loadingAuth } = useAuth();

    // ─── Guard: already authenticated (check token) ──────────
    // If user already has a token, redirect to dashboard immediately
    // Why: Logged-in users shouldn't see the registration page
    if (hasToken()) {
        router.visit('/dashboard');
    }

    // ─── Form ────────────────────────────────────────────────
    // useForm: creates reactive form object with registration fields
    // form.errors: tracks validation errors per field
    // form.processing: true while form is being submitted
    const form = useForm<RegisterDto>({
        name:                  '',
        email:                 '',
        password:              '',
        password_confirmation: '',
        role:                  'staff',
    });

    // isLoading: true if form is processing OR register API call is in progress
    // Used to disable submit button during registration
    const isLoading   = computed(() => form.processing || loadingAuth.value);
    
    // submitLabel: dynamic button text
    // Shows "Creating account..." while loading, "Create Account" otherwise
    const submitLabel = computed(() => isLoading.value ? t('auth.creating_account') : t('auth.create_account'));

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
            // Send registration data to backend API
            const result = await register({
                name:                  form.name,
                email:                 form.email,
                password:              form.password,
                password_confirmation: form.password_confirmation,
                role:                  form.role,
            });

            // On success, redirect to dashboard
            if (result?.status == 1) {
                router.visit('/dashboard');
            }

        } catch (err: unknown) {
            // Handle API errors
            const apiErr = err as Record<string, any>;

            // 422 = Validation Error (e.g., email already taken, password too short)
            // Backend returns: { response: { data: { errors: { field: ['message'] } } } }
            if (apiErr?.response?.status === 422) {
                const backendErrors: Record<string, string[]> = apiErr.response.data?.errors ?? {};
                Object.entries(backendErrors).forEach(([key, messages]) => {
                    form.setError(key as any, messages[0]);  // Set first error message for each field
                });
                scrollToFirstError();
            }
        }
    }

    // ─── Validation ──────────────────────────────────────────
    function validateForm(): boolean {
        return validateInertiaForm(form, {
            name:                  [required],
            email:                 [required, emailRule],
            password:              [required, minLength(8)],
            password_confirmation: [required, confirmed(() => form.password)],
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

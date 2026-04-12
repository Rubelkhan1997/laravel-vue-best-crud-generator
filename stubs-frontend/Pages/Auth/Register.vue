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
import { reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { useAuth } from '@/Composables/Auth/useAuth';

// FILE: resources/js/Pages/Auth/Register.vue

const { register, loadingAuth, error } = useAuth();

const form = reactive({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    errors: {} as Record<string, string>,
});

async function handleRegister(): Promise<void> {
    form.errors = {};

    try {
        const result = await register({
            name: form.name,
            email: form.email,
            password: form.password,
            password_confirmation: form.password_confirmation,
        });

        if (result.status === 1) {
            router.visit('/dashboard');
        }
    } catch (err: unknown) {
        const apiErr = err as Record<string, any>;
        if (apiErr?.response?.data?.errors) {
            const backendErrors: Record<string, string[]> = apiErr.response.data.errors;
            Object.entries(backendErrors).forEach(([key, messages]) => {
                form.errors[key] = messages[0];
            });
        }
    }
}
</script>

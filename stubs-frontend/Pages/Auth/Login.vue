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
import { reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { useAuth } from '@/Composables/Auth/useAuth';

// FILE: resources/js/Pages/Auth/Login.vue

const { login, loadingAuth, error } = useAuth();

const form = reactive({
    email: '',
    password: '',
    remember: false,
    errors: {} as Record<string, string>,
});

async function handleLogin(): Promise<void> {
    form.errors = {};

    try {
        const result = await login({
            email: form.email,
            password: form.password,
            remember: form.remember,
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

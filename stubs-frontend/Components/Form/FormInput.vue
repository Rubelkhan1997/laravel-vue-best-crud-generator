<template>
    <div :class="wrapperClass">
        <label
            v-if="label"
            :for="resolvedId"
            :class="['text-gray-700 font-medium mb-2', labelClass || 'text-[16px] block']"
        >
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>

        <input
            :id="resolvedId"
            :name="name || resolvedId"
            :type="type"
            :value="modelValue ?? ''"
            :required="required"
            :placeholder="placeholder"
            :min="min"
            :max="max"
            :step="step"
            :class="[
                'w-full px-3 py-2 border rounded-state focus:!outline-0 focus:!ring-0 focus:!border-primary placeholder:text-gray-600 placeholder:text-[14.5px]',
                error ? 'border-red-500' : 'border-gray-300',
                inputClass,
            ]"
            @input="onInput"
            @blur="$emit('blur', $event)"
        />

        <span v-if="error" class="text-red-500 pt-1 block text-sm">{{ error }}</span>
    </div>
</template>

<script setup lang="ts">
    import { computed } from 'vue';

    // Configurable props to support text/email/number and validation feedback.
    const props = withDefaults(defineProps<{
        modelValue?: string | number | null;
        label?: string | null;
        id?: string;
        name?: string;
        type?: string;
        placeholder?: string;
        required?: boolean;
        min?: string | number;
        max?: string | number;
        step?: string | number;
        error?: string;
        wrapperClass?: string;
        labelClass?: string;
        inputClass?: string;
    }>(), {
        modelValue: '',
        label: null,
        id: '',
        name: '',
        type: 'text',
        placeholder: '',
        required: false,
        min: '',
        max: '',
        step: '',
        error: '',
        wrapperClass: 'relative mb-4',
        labelClass: '',
        inputClass: '',
    });

    // Emits v-model updates and blur event for parent form handling.
    const emit = defineEmits<{
        'update:modelValue': [value: string];
        blur: [event: FocusEvent];
    }>();

    // Resolve a stable input id from explicit id, fallback name.
    const resolvedId = computed(() => props.id || props.name || undefined);

    // Normalize input event payload to string for v-model binding.
    function onInput(event: Event): void {
        const target = event.target as HTMLInputElement;
        emit('update:modelValue', target.value);
    }
</script>

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
            :name="name"
            type="date"
            :value="modelValue || ''"
            :required="required"
            :min="min"
            :max="max"
            :disabled="disabled"
            :class="[
                'w-full px-3 py-2 border rounded-state focus:!outline-0 focus:!ring-0 focus:!border-primary placeholder:text-gray-600 placeholder:text-[14.5px]',
                error ? 'border-red-500' : 'border-gray-300',
                inputClass,
            ]"
            @input="onInput"
            @blur="$emit('blur', $event)"
        />

        <p v-if="helperText" class="text-gray-500 pt-1 block text-sm">{{ helperText }}</p>
        <span v-if="error" class="text-red-500 pt-1 block text-sm">{{ error }}</span>
    </div>
</template>

<script setup lang="ts">
    import { computed } from 'vue';

    // Date picker props with validation and optional helper text.
    const props = withDefaults(defineProps<{
        modelValue?: string | null;
        label?: string | null;
        id?: string;
        name?: string;
        required?: boolean;
        min?: string;
        max?: string;
        disabled?: boolean;
        error?: string;
        helperText?: string;
        wrapperClass?: string;
        labelClass?: string;
        inputClass?: string;
    }>(), {
        modelValue: '',
        label: null,
        id: '',
        name: '',
        required: false,
        min: '',
        max: '',
        disabled: false,
        error: '',
        helperText: '',
        wrapperClass: 'relative mb-4',
        labelClass: '',
        inputClass: '',
    });

    // Emits selected date and blur event.
    const emit = defineEmits<{
        'update:modelValue': [value: string];
        blur: [event: FocusEvent];
    }>();

    // Resolve id for label-to-input connection.
    const resolvedId = computed(() => props.id || props.name || undefined);

    // Emit date value whenever user changes the input.
    function onInput(event: Event): void {
        const target = event.target as HTMLInputElement;
        emit('update:modelValue', target.value);
    }
</script>

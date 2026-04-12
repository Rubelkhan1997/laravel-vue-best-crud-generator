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

        <textarea
            :id="resolvedId"
            :name="name || resolvedId"
            :rows="rows"
            :required="required"
            :placeholder="placeholder"
            :class="[
                'w-full px-3 py-2 border rounded-state focus:!outline-0 focus:!ring-0 focus:!border-primary placeholder:text-gray-600 placeholder:text-[14.5px]',
                error ? 'border-red-500' : 'border-gray-300',
                inputClass,
            ]"
            :value="modelValue ?? ''"
            @input="onInput"
            @blur="$emit('blur', $event)"
        ></textarea>

        <span v-if="error" class="text-red-500 pt-1 block text-sm">{{ error }}</span>
    </div>
</template>

<script setup lang="ts">
    import { computed } from 'vue';

    // Textarea props mirror FormInput behavior for consistent API.
    const props = withDefaults(defineProps<{
        modelValue?: string | null;
        label?: string | null;
        id?: string;
        name?: string;
        rows?: number;
        placeholder?: string;
        required?: boolean;
        error?: string;
        wrapperClass?: string;
        labelClass?: string;
        inputClass?: string;
    }>(), {
        modelValue: '',
        label: null,
        id: '',
        name: '',
        rows: 3,
        placeholder: '',
        required: false,
        error: '',
        wrapperClass: 'relative mb-4',
        labelClass: '',
        inputClass: '',
    });

    // Emits textarea value updates and blur event.
    const emit = defineEmits<{
        'update:modelValue': [value: string];
        blur: [event: FocusEvent];
    }>();

    // Resolve textarea id from explicit id or name.
    const resolvedId = computed(() => props.id || props.name || undefined);

    // Emit current textarea content on each input event.
    function onInput(event: Event): void {
        const target = event.target as HTMLTextAreaElement;
        emit('update:modelValue', target.value);
    }
</script>

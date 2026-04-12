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
            type="file"
            :accept="accept"
            :multiple="multiple"
            :required="required"
            :disabled="disabled"
            :class="[
                'w-full px-3 py-2 border rounded-state focus:!outline-0 focus:!ring-0 focus:!border-primary file:mr-3 file:px-3 file:py-1 file:rounded file:border-0 file:bg-slate-100 file:text-slate-700',
                error ? 'border-red-500' : 'border-gray-300',
                inputClass,
            ]"
            @change="onFileChange"
        />

        <p v-if="selectedText" class="text-gray-600 pt-1 block text-sm">{{ selectedText }}</p>
        <p v-if="helperText" class="text-gray-500 pt-1 block text-sm">{{ helperText }}</p>
        <span v-if="error" class="text-red-500 pt-1 block text-sm">{{ error }}</span>
    </div>
</template>

<script setup lang="ts">
    import { computed } from 'vue';

    // File upload props for single/multiple mode and UI feedback.
    const props = withDefaults(defineProps<{
        modelValue?: File | File[] | null;
        label?: string | null;
        id?: string;
        name?: string;
        accept?: string;
        multiple?: boolean;
        required?: boolean;
        disabled?: boolean;
        error?: string;
        helperText?: string;
        wrapperClass?: string;
        labelClass?: string;
        inputClass?: string;
    }>(), {
        modelValue: null,
        label: null,
        id: '',
        name: '',
        accept: '',
        multiple: false,
        required: false,
        disabled: false,
        error: '',
        helperText: '',
        wrapperClass: 'relative mb-4',
        labelClass: '',
        inputClass: '',
    });

    // Emits selected file(s) for parent form handling.
    const emit = defineEmits<{
        'update:modelValue': [value: File | File[] | null];
    }>();

    // Resolve id for accessibility and label linking.
    const resolvedId = computed(() => props.id || props.name || undefined);

    // Builds a readable selected file name text for UI display.
    const selectedText = computed(() => {
        if (!props.modelValue) {
            return '';
        }

        if (Array.isArray(props.modelValue)) {
            return props.modelValue.map((file) => file.name).join(', ');
        }

        return props.modelValue.name;
    });

    // Normalizes file input output to File | File[] | null and emits it.
    function onFileChange(event: Event): void {
        const target = event.target as HTMLInputElement;
        const files = target.files ? Array.from(target.files) : [];

        if (files.length === 0) {
            emit('update:modelValue', null);
            return;
        }

        if (props.multiple) {
            emit('update:modelValue', files);
            return;
        }

        emit('update:modelValue', files[0]);
    }
</script>

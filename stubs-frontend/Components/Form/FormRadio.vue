<template>
    <div class="form-control flex-row">
        <label class="label cursor-pointer flex items-center gap-2">
            <input
                type="radio"
                :name="name"
                :value="value"
                :checked="isChecked"
                class="radio radio-sm radio-primary"
                @change="onChange"
            />
            <span class="label-text text-base">{{ label }}</span>
        </label>
    </div>
</template>

<script setup lang="ts">
    import { computed } from 'vue';

    // Radio option contract: supports both v-model and legacy current value.
    const props = withDefaults(defineProps<{
        label?: string;
        name: string;
        value: string | number | boolean;
        modelValue?: string | number | boolean | null;
        current?: string | number | boolean | null;
    }>(), {
        label: '',
        modelValue: null,
        current: null,
    });

    // Emits selected value to parent v-model.
    const emit = defineEmits<{
        'update:modelValue': [value: string | number | boolean];
    }>();

    // Priority: use modelValue when available, otherwise fallback to current.
    const isChecked = computed(() => {
        if (props.modelValue !== null && props.modelValue !== undefined) {
            return props.modelValue === props.value;
        }

        return props.current === props.value;
    });

    // Push selected option value to parent.
    function onChange(): void {
        emit('update:modelValue', props.value);
    }
</script>

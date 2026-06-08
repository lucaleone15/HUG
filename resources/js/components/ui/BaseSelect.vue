<script setup>
defineOptions({ inheritAttrs: false })

defineProps({
    modelValue: { type: [String, Number], default: undefined },
    label:      { type: String, default: null },
    error:      { type: String, default: null },
    required:   { type: Boolean, default: false },
    disabled:   { type: Boolean, default: false },
    name:       { type: String, default: null },
})

defineEmits(['update:modelValue'])
</script>

<template>
    <label class="form-control w-full">
        <div v-if="label" class="label">
            <span class="label-text font-medium">
                {{ label }}<span v-if="required" class="text-error ml-0.5">*</span>
            </span>
        </div>
        <select
            v-bind="$attrs"
            :name="name"
            :value="modelValue"
            :required="required"
            :disabled="disabled"
            class="select select-bordered w-full"
            :class="error ? 'select-error' : ''"
            @change="$emit('update:modelValue', $event.target.value)"
        >
            <slot />
        </select>
        <div v-if="error" class="label pt-0">
            <span class="label-text-alt text-error">{{ error }}</span>
        </div>
    </label>
</template>

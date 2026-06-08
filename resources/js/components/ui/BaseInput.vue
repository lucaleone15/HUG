<script setup>
defineOptions({ inheritAttrs: false })

defineProps({
    modelValue: { type: [String, Number], default: undefined },
    label:      { type: String, default: null },
    error:      { type: String, default: null },
    type:       { type: String, default: 'text' },
    placeholder:{ type: String, default: '' },
    required:   { type: Boolean, default: false },
    disabled:   { type: Boolean, default: false },
    name:       { type: String, default: null },
    hint:       { type: String, default: null },
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
        <!-- pose sur l'input tout ce qu'on écrit sur <BaseInput> mais qui n'est pas dans defineProps (ex. id, autocomplete) -->
        <input
            v-bind="$attrs"
            :type="type"
            :name="name"
            :value="modelValue"
            :placeholder="placeholder"
            :required="required"
            :disabled="disabled"
            class="input input-bordered w-full"
            :class="error ? 'input-error' : ''"
            @input="$emit('update:modelValue', $event.target.value)"
            @wheel="type === 'number' ? ($event.preventDefault() || $event.target.blur()) : undefined"
        >
        <div v-if="hint && !error" class="label pt-0">
            <span class="label-text-alt text-base-content/50">{{ hint }}</span>
        </div>
        <div v-if="error" class="label pt-0">
            <span class="label-text-alt text-error">{{ error }}</span>
        </div>
    </label>
</template>

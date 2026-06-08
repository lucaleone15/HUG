<script setup>
defineProps({
    companies: { type: Array, required: true },
    modelValue: { type: [Number, String], default: '' },
    allLabel: { type: String, default: null },
    label: { type: String, default: null },
})

defineEmits(['update:modelValue'])
</script>

<template>
    <div class="form-control w-full max-w-xs">
        <div v-if="label" class="label">
            <span class="label-text font-medium">{{ label }}</span>
        </div>
        <select class="select select-bordered" :value="modelValue"
            @change="$emit('update:modelValue', $event.target.value)">
            <option v-if="allLabel" value="">{{ allLabel }}</option>
            <option v-else value="" disabled>-</option>
            <option v-for="c in companies" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
    </div>
</template>

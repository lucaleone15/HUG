<script setup>
defineProps({
    sectors:    { type: Array, required: true }, // Array<string>
    modelValue: { type: String, default: null }, // null = tous
    allLabel:   { type: String, default: 'Tous' },
})

defineEmits(['update:modelValue'])
</script>

<template>
    <div class="flex flex-wrap gap-2 mb-8">
        <button
            class="badge badge-lg cursor-pointer select-none transition-colors"
            :class="modelValue === null
                ? 'bg-[#E30613] text-white border-transparent'
                : 'badge-ghost hover:badge-neutral'"
            @click="$emit('update:modelValue', null)"
        >
            {{ allLabel }}
        </button>
        <button
            v-for="sector in sectors"
            :key="sector"
            class="badge badge-lg cursor-pointer select-none transition-colors"
            :class="modelValue === sector
                ? 'bg-[#E30613] text-white border-transparent'
                : 'badge-ghost hover:badge-neutral'"
            @click="$emit('update:modelValue', sector)"
        >
            {{ sector }}
        </button>
    </div>
</template>

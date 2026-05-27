<script setup>
defineProps({
    counters: {
        type: Array,
        required: true,
        // [{ value: Number|String, label: String, color: String (tailwind class) }]
    },
    cols: { type: Number, default: null },
    dark: { type: Boolean, default: false }, // red background, white text
})
</script>

<template>
    <section
        class="py-12 px-6"
        :class="dark ? 'bg-brand text-white' : 'bg-base-200'"
    >
        <div
            class="max-w-5xl mx-auto grid gap-6"
            :class="cols
                ? `grid-cols-${cols}`
                : 'grid-cols-2 md:grid-cols-3 lg:grid-cols-5'"
        >
            <div
                v-for="c in counters"
                :key="c.label"
                class="text-center"
                :class="dark ? '' : 'card bg-base-100 shadow-sm p-4'"
            >
                <div class="text-3xl font-bold" :class="dark ? 'text-white' : c.color">
                    {{ typeof c.value === 'number' ? c.value.toLocaleString() : c.value }}
                </div>
                <div class="text-xs mt-1 leading-tight" :class="dark ? 'text-white/80' : 'text-base-content/60'">
                    {{ c.label }}
                </div>
            </div>
        </div>
    </section>
</template>

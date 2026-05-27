<script setup>
defineProps({
    counters: {
        type: Array,
        required: true,
        // [{ value: Number|String, label: String, color: String (tailwind class) }]
    },
    cols: { type: Number, default: null }, // override auto columns
})
</script>

<template>
    <section class="py-16 px-6 bg-base-200">
        <div
            class="max-w-4xl mx-auto grid gap-6"
            :class="cols
                ? `grid-cols-${cols}`
                : 'grid-cols-2 md:grid-cols-3 lg:grid-cols-5'"
        >
            <div
                v-for="c in counters"
                :key="c.label"
                class="card bg-base-100 shadow-sm text-center p-4"
            >
                <div class="text-3xl font-bold" :class="c.color">
                    {{ typeof c.value === 'number' ? c.value.toLocaleString() : c.value }}
                </div>
                <div class="text-xs text-base-content/60 mt-1 leading-tight">{{ c.label }}</div>
            </div>
        </div>
    </section>
</template>

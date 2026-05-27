<script setup>
defineProps({
    title:       { type: String, required: true },
    description: { type: String, default: null },
    cta:         { type: Object, default: null }, // { label, href }
    bgColor:     { type: String, default: '#E30613' },
})
</script>

<template>
    <section class="py-16 px-6 text-white" :style="`background-color: ${bgColor}`">
        <div class="max-w-2xl mx-auto text-center">
            <h2 class="text-2xl md:text-3xl font-bold mb-4">{{ title }}</h2>
            <p v-if="description" class="text-white/80 mb-8 max-w-md mx-auto">{{ description }}</p>
            <a
                v-if="cta"
                :href="cta.href"
                class="btn bg-white text-brand hover:bg-white/90 border-none font-semibold px-8"
            >
                {{ cta.label }}
            </a>
            <slot />
        </div>
    </section>
</template>

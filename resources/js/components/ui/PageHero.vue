<script setup>
defineProps({
    title:    { type: String, required: true },
    subtitle: { type: String, default: null },
    cta:      { type: Object, default: null }, // { label, href }
    bgColor:  { type: String, default: '#E30613' },
    light:    { type: Boolean, default: false }, // light bg, dark text
})
</script>

<template>
    <section
        class="py-20 px-6"
        :class="light ? 'bg-base-200' : 'text-white'"
        :style="!light ? `background-color: ${bgColor}` : ''"
    >
        <div class="max-w-3xl mx-auto text-center">
            <slot name="above" />
            <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">{{ title }}</h1>
            <p
                v-if="subtitle"
                class="text-lg mb-8 max-w-xl mx-auto"
                :class="light ? 'text-base-content/60' : 'text-white/80'"
            >
                {{ subtitle }}
            </p>
            <a
                v-if="cta"
                :href="cta.href"
                class="btn font-semibold px-8 border-none"
                :class="light
                    ? 'bg-[#E30613] hover:bg-[#c0051f] text-white'
                    : 'bg-white text-[#E30613] hover:bg-white/90'"
            >
                {{ cta.label }}
            </a>
            <slot />
        </div>
    </section>
</template>

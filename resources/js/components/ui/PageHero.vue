<script setup>
defineProps({
    title:    { type: String, required: true },
    subtitle: { type: String, default: null },
    cta:      { type: Object, default: null }, // { label, href }
    bgColor:  { type: String, default: null }, // if set: colored bg + white text (legacy)
    light:    { type: Boolean, default: false }, // base-200 bg (legacy)
})
</script>

<template>
    <!-- Colored background mode (legacy, used by SectionCTA-like usage) -->
    <section
        v-if="bgColor || light"
        class="py-20 px-6"
        :class="light ? 'bg-base-200' : 'text-white'"
        :style="bgColor ? `background-color: ${bgColor}` : ''"
    >
        <div class="max-w-3xl mx-auto text-center">
            <slot name="above" />
            <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">{{ title }}</h1>
            <p v-if="subtitle" class="text-lg mb-8 max-w-xl mx-auto"
                :class="light ? 'text-base-content/60' : 'text-white/80'">
                {{ subtitle }}
            </p>
            <a v-if="cta" :href="cta.href"
                class="btn font-semibold px-8 border-none"
                :class="light ? 'bg-brand hover:bg-brand-dark text-white' : 'bg-white text-brand hover:bg-white/90'">
                {{ cta.label }}
            </a>
            <slot />
        </div>
    </section>

    <!-- Two-column white hero (default) -->
    <section v-else class="py-16 px-6 bg-white">
        <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-12 items-center">
            <div>
                <slot name="above" />
                <h1 class="text-4xl md:text-5xl font-bold mb-5 leading-tight text-base-content">{{ title }}</h1>
                <p v-if="subtitle" class="text-lg text-base-content/60 mb-8 leading-relaxed">{{ subtitle }}</p>
                <a v-if="cta" :href="cta.href"
                    class="btn bg-brand hover:bg-brand-dark text-white border-none font-semibold px-8">
                    {{ cta.label }}
                </a>
                <slot />
            </div>
            <div>
                <slot name="visual">
                    <!-- Default placeholder when no visual provided -->
                    <div class="aspect-[4/3] bg-base-200 rounded-xl flex items-center justify-center text-base-content/20 text-sm">
                        Visuel
                    </div>
                </slot>
            </div>
        </div>
    </section>
</template>

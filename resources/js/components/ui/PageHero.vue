<script setup>
defineProps({
    title:    { type: String, required: true },
    subtitle: { type: String, default: null },
    cta:      { type: Object, default: null }, // { label, href }
    bgColor:  { type: String, default: null }, // legacy: colored bg + white text
    light:    { type: Boolean, default: false }, // legacy: base-200 bg
})
</script>

<template>
    <!-- Legacy colored mode -->
    <section
        v-if="bgColor || light"
        class="py-20 px-6"
        :class="light ? 'bg-base-200' : 'text-white'"
        :style="bgColor ? `background-color: ${bgColor}` : ''"
    >
        <div class="max-w-3xl mx-auto text-center">
            <slot name="above" />
            <h1 class="font-extrabold leading-[0.93] tracking-tight mb-5"
                style="font-size: clamp(2.5rem, 5vw, 3.75rem);">{{ title }}</h1>
            <p v-if="subtitle" class="mb-8 leading-relaxed"
               style="font-size: clamp(1rem, 1.5vw, 1.075rem); max-width: 52ch; margin-inline: auto;"
               :class="light ? 'text-base-content/55' : 'text-white/75'">
                {{ subtitle }}
            </p>
            <a v-if="cta" :href="cta.href"
                class="btn font-semibold px-8 border-none rounded-sm uppercase text-sm tracking-wide"
                :class="light ? 'bg-brand hover:bg-brand-dark text-white' : 'bg-white text-brand hover:bg-white/90'">
                {{ cta.label }}
            </a>
            <slot />
        </div>
    </section>

    <!-- Default: two-column white hero -->
    <section v-else class="py-16 px-6 bg-white">
        <div class="max-w-5xl mx-auto grid md:grid-cols-[1.1fr_0.9fr] gap-12 items-center">
            <div class="page-hero-text">
                <slot name="above" />
                <h1 class="font-extrabold leading-[0.93] tracking-tight text-base-content mb-5"
                    style="font-size: clamp(2.5rem, 5.5vw, 3.75rem); max-width: 16ch;">
                    {{ title }}
                </h1>
                <p v-if="subtitle" class="text-base-content/55 mb-8 leading-relaxed"
                   style="font-size: clamp(1rem, 1.5vw, 1.075rem); max-width: 44ch;">
                    {{ subtitle }}
                </p>
                <a v-if="cta" :href="cta.href"
                    class="btn bg-brand hover:bg-brand-dark text-white border-none font-semibold px-8">
                    {{ cta.label }}
                </a>
                <slot />
            </div>
            <div class="page-hero-visual">
                <slot name="visual">
                    <div class="aspect-[4/3] bg-base-200 rounded-xl flex items-center justify-center text-base-content/20 text-sm select-none">
                        Visuel
                    </div>
                </slot>
            </div>
        </div>
    </section>
</template>

<script setup>
defineProps({
    title:       { type: String, required: true },
    description: { type: String, default: null },
    href:        { type: String, required: true },
    image:       { type: String, default: null },
})
</script>

<template>
    <a :href="href" class="row group flex flex-col md:flex-row md:items-center gap-3 md:gap-6 py-9">

        <!-- Texte + flèche mobile sur la même ligne -->
        <div class="flex items-start justify-between gap-4 flex-1 min-w-0">
            <div class="min-w-0">
                <h3 class="title font-bold mb-2"
                    style="font-size: clamp(1.1rem, 2vw, 1.4rem);">
                    {{ title }}
                </h3>
                <p v-if="description"
                   class="text-base-content/55 text-sm leading-relaxed"
                   style="max-width: 58ch;">
                    {{ description }}
                </p>
            </div>
            <!-- Flèche mobile uniquement -->
            <span class="arrow text-base-content/30 shrink-0 mt-1 text-xl select-none md:hidden">→</span>
        </div>

        <!-- Visuel -->
        <div v-if="$slots.visual || image"
             class="visual flex shrink-0 rounded-lg overflow-hidden items-center justify-center self-center w-[130px] md:w-[150px]"
             style="aspect-ratio: 3/2;">
            <slot name="visual">
                <img :src="image" :alt="title" class="img-zoom w-full h-full object-cover">
            </slot>
        </div>

        <!-- Flèche desktop uniquement -->
        <span class="arrow text-base-content/30 shrink-0 mt-0.5 text-xl select-none hidden md:block">→</span>

    </a>
</template>

<style scoped>
/* Press feedback on the whole row */
.row {
    transition: transform 140ms cubic-bezier(0.23, 1, 0.32, 1);
}
.row:active {
    transform: scale(0.99);
}

/* Title color on hover */
.title {
    transition: color 150ms ease;
}
.group:hover .title {
    color: var(--color-brand);
}

/* Visual: toujours visible, animation au hover */
.visual {
    clip-path: inset(0 0% 0 0 round 8px);
    transform: scale(1);
    transition: transform 300ms cubic-bezier(0.23, 1, 0.32, 1),
                box-shadow 300ms ease;
    box-shadow: 0 2px 8px rgba(25,5,7,0.08);
}

/* Arrow nudge */
.arrow {
    transition: color 150ms ease, transform 200ms cubic-bezier(0.23, 1, 0.32, 1);
}

/* Hover: only on true pointer devices */
@media (hover: hover) and (pointer: fine) {
    .group:hover .visual {
        transform: scale(1.07) rotate(-1.5deg);
        box-shadow: 0 8px 24px rgba(25,5,7,0.14);
    }

    .group:hover .arrow {
        color: var(--color-brand);
        transform: translateX(4px);
    }

    .img-zoom {
        transition: transform 380ms cubic-bezier(0.23, 1, 0.32, 1);
    }
    .group:hover .img-zoom {
        transform: scale(1.06);
    }
}

@media (prefers-reduced-motion: reduce) {
    .visual, .row { transition: none; }
    .group:hover .visual { transform: none; box-shadow: none; }
}
</style>

<script setup>
defineProps({
    num:         { type: String, required: true },
    title:       { type: String, required: true },
    description: { type: String, default: null },
    href:        { type: String, required: true },
    image:       { type: String, default: null },
})
</script>

<template>
    <a :href="href" class="row group grid gap-6 md:gap-12 py-9"
       style="grid-template-columns: 3rem 1fr;">
        <span class="font-mono text-xs text-base-content/30 pt-1 select-none">{{ num }}</span>
        <div class="flex items-center justify-between gap-6">
            <div class="flex-1 min-w-0">
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

            <div v-if="$slots.visual || image"
                 class="visual hidden md:flex shrink-0 rounded-lg overflow-hidden items-center justify-center"
                 style="width: 116px; aspect-ratio: 3/2;">
                <slot name="visual">
                    <img :src="image" :alt="title" class="img-zoom w-full h-full object-cover">
                </slot>
            </div>

            <span class="arrow text-base-content/30 shrink-0 mt-0.5 text-xl select-none">→</span>
        </div>
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

/* Visual: clip-path reveal left→right + subtle scale */
.visual {
    clip-path: inset(0 100% 0 0 round 8px);
    transform: scale(0.94);
    transition: clip-path 300ms cubic-bezier(0.23, 1, 0.32, 1),
                transform 300ms cubic-bezier(0.23, 1, 0.32, 1);
}

/* Arrow nudge */
.arrow {
    transition: color 150ms ease, transform 200ms cubic-bezier(0.23, 1, 0.32, 1);
}

/* Hover: only on true pointer devices */
@media (hover: hover) and (pointer: fine) {
    .group:hover .visual {
        clip-path: inset(0 0% 0 0 round 8px);
        transform: scale(1);
    }

    .group:hover .arrow {
        color: var(--color-brand);
        transform: translateX(4px);
    }

    /* Subtle image zoom inside the panel */
    .img-zoom {
        transition: transform 380ms cubic-bezier(0.23, 1, 0.32, 1);
    }
    .group:hover .img-zoom {
        transform: scale(1.06);
    }
}

/* Reduced motion: only fade, no clip/scale movement */
@media (prefers-reduced-motion: reduce) {
    .visual {
        clip-path: none;
        opacity: 0;
        transform: none;
        transition: opacity 150ms ease;
    }
    .group:hover .visual {
        opacity: 1;
        transform: none;
    }
    .row {
        transition: none;
    }
}
</style>

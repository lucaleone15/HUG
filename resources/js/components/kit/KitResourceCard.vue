<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps({
    resourceKey: { type: String, required: true },
    file:        { type: String, required: true },
    n:           { type: Number, required: true },
    variant:     { type: String, default: 'standard' }, // 'featured' | 'standard' | 'wide'
})

const imageError = ref(false)
</script>

<template>
    <a :href="file" download class="kit-card" :class="`kit-card--${variant}`">

        <!-- ── Zone aperçu ──────────────────────────────────────── -->
        <div class="kit-card__media">

            <img v-if="!imageError"
                 :src="`/downloads/kit/preview-${resourceKey}.jpg`"
                 :alt="t(`kit.resource_${resourceKey}_title`)"
                 class="kit-card__img"
                 loading="lazy"
                 @error="imageError = true" />

            <div v-else class="kit-card__placeholder">
                <span class="kit-card__bg-num" aria-hidden="true">
                    {{ String(n).padStart(2, '0') }}
                </span>
                <!-- Indicateurs carrousel (carte wide uniquement) -->
                <div v-if="variant === 'wide'" class="kit-card__slides" aria-hidden="true">
                    <span v-for="i in 5" :key="i"
                          class="kit-card__dot"
                          :class="{ 'kit-card__dot--on': i === 1 }">
                    </span>
                </div>
                <span v-else class="kit-card__fmt-hint">
                    {{ t(`kit.resource_${resourceKey}_format`) }}
                </span>
            </div>

            <!-- Trait rouge bas animé au hover -->
            <div class="kit-card__bar" aria-hidden="true"></div>
        </div>

        <!-- ── Zone infos ───────────────────────────────────────── -->
        <div class="kit-card__body">
            <span class="kit-card__format">{{ t(`kit.resource_${resourceKey}_format`) }}</span>
            <h4 class="kit-card__title">{{ t(`kit.resource_${resourceKey}_title`) }}</h4>
            <p class="kit-card__desc">{{ t(`kit.resource_${resourceKey}_desc`) }}</p>
            <div class="kit-card__dl">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                {{ t('kit.download_label') }}
            </div>
        </div>

    </a>
</template>

<style scoped>
/* ── Base ───────────────────────────────────────────────────── */
.kit-card {
    display: flex;
    flex-direction: column;
    border-radius: 0.75rem;
    overflow: hidden;
    background: #ffffff;
    text-decoration: none;
    height: 100%;
    box-shadow: 0 1px 3px rgba(25, 5, 7, 0.07), 0 1px 2px rgba(25, 5, 7, 0.04);
    transition: transform 220ms cubic-bezier(0.23, 1, 0.32, 1),
                box-shadow 220ms ease;
}
@media (hover: hover) and (pointer: fine) {
    .kit-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 42px rgba(25, 5, 7, 0.13), 0 4px 10px rgba(25, 5, 7, 0.06);
    }
    .kit-card:hover .kit-card__bar {
        opacity: 1;
        transform: scaleX(1);
    }
}

/* ── Aperçu ─────────────────────────────────────────────────── */
.kit-card__media {
    position: relative;
    background: #190507;
    overflow: hidden;
    flex-shrink: 0;
}

/* standard : ratio 3/2 */
.kit-card--standard .kit-card__media {
    aspect-ratio: 3 / 2;
}

/* featured (tall, 1col×2row) : aperçu remplit l'espace dispo */
.kit-card--featured .kit-card__media {
    flex: 1;
    min-height: 180px;
}

/* landscape (wide, 2col×1row) : aperçu remplit la hauteur, infos en bas */
.kit-card--landscape .kit-card__media {
    flex: 1;
    min-height: 130px;
}

/* wide (social, 3col) : horizontal sur desktop */
.kit-card--wide .kit-card__media {
    aspect-ratio: 3 / 2;
}
@media (min-width: 640px) {
    .kit-card--wide {
        flex-direction: row;
    }
    .kit-card--wide .kit-card__media {
        aspect-ratio: unset;
        width: 44%;
        flex-shrink: 0;
    }
    .kit-card--wide .kit-card__body {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
}

/* Image */
.kit-card__img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* ── Placeholder ────────────────────────────────────────────── */
.kit-card__placeholder {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 1.25rem;
    user-select: none;
}

.kit-card__bg-num {
    font-weight: 900;
    line-height: 1;
    font-variant-numeric: tabular-nums;
    color: rgba(255, 255, 255, 0.06);
    font-size: clamp(3.5rem, 10vw, 5.5rem);
}
.kit-card--featured .kit-card__bg-num {
    font-size: clamp(5rem, 14vw, 8.5rem);
}
.kit-card--landscape .kit-card__bg-num {
    font-size: clamp(4rem, 10vw, 7rem);
}
.kit-card--wide .kit-card__bg-num {
    font-size: clamp(2.5rem, 6vw, 4.5rem);
}

.kit-card__fmt-hint {
    font-size: 0.6rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.2em;
    color: rgba(255, 255, 255, 0.18);
}

/* Dots carrousel */
.kit-card__slides {
    display: flex;
    align-items: center;
    gap: 0.35rem;
}
.kit-card__dot {
    display: block;
    width: 0.4rem;
    height: 0.4rem;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    transition: background 150ms ease, width 150ms ease, border-radius 150ms ease;
}
.kit-card__dot--on {
    background: #D32C37;
    width: 0.9rem;
    border-radius: 0.2rem;
}

/* ── Trait rouge ────────────────────────────────────────────── */
.kit-card__bar {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: #D32C37;
    opacity: 0.4;
    transform: scaleX(0.7);
    transform-origin: left;
    transition: opacity 220ms ease, transform 220ms ease;
}
.kit-card--featured .kit-card__bar {
    height: 4px;
    opacity: 0.65;
    transform: scaleX(1);
}

/* ── Corps ──────────────────────────────────────────────────── */
.kit-card__body {
    padding: 1rem 1.125rem 1.125rem;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    flex-shrink: 0;
}
.kit-card--featured .kit-card__body {
    padding: 1.125rem 1.375rem 1.375rem;
    gap: 0.4rem;
}
.kit-card--wide .kit-card__body {
    padding: 1.25rem 1.5rem;
    gap: 0.4rem;
}

/* landscape : infos en ligne sur desktop (titre gauche / dl droite) */
@media (min-width: 640px) {
    .kit-card--landscape .kit-card__body {
        flex-direction: row;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1.25rem;
        padding: 1rem 1.375rem 1.25rem;
    }
    .kit-card--landscape .kit-card__body > .kit-card__dl {
        margin-top: 0;
        flex-shrink: 0;
        align-self: flex-end;
        padding-bottom: 0.1rem;
    }
}

.kit-card__format {
    font-size: 0.5625rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: rgba(25, 5, 7, 0.25);
    line-height: 1;
}

.kit-card__title {
    font-weight: 700;
    font-size: 0.875rem;
    line-height: 1.2;
    color: #190507;
    margin: 0;
}
.kit-card--featured .kit-card__title   { font-size: 1.05rem; }
.kit-card--landscape .kit-card__title  { font-size: 1rem; }
.kit-card--wide .kit-card__title       { font-size: 1rem; }

.kit-card__desc {
    font-size: 0.7rem;
    line-height: 1.65;
    color: rgba(25, 5, 7, 0.42);
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.kit-card--featured .kit-card__desc,
.kit-card--wide .kit-card__desc {
    font-size: 0.775rem;
    -webkit-line-clamp: 3;
}
/* landscape : description cachée sur desktop (espace insuffisant en ligne) */
@media (min-width: 640px) {
    .kit-card--landscape .kit-card__desc { display: none; }
}

.kit-card__dl {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    margin-top: 0.5rem;
    font-size: 0.7rem;
    font-weight: 700;
    color: #D32C37;
}
.kit-card--featured .kit-card__dl,
.kit-card--wide .kit-card__dl {
    font-size: 0.775rem;
    gap: 0.375rem;
    margin-top: 0.625rem;
}

@media (prefers-reduced-motion: reduce) {
    .kit-card, .kit-card__bar, .kit-card__dot { transition: none; }
}
</style>

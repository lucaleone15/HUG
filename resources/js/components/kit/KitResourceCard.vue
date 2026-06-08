<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const props = defineProps({
    resourceKey: { type: String, required: true },
    file: { type: String, required: true },
    n: { type: Number, required: true },
    image: { type: String, default: null },
    video: { type: String, default: null },
    imageRatio: { type: String, default: null },
    slides: { type: Array, default: null },
});

const imageError = ref(false);
const slideIndex = ref(0);
const videoEl = ref(null);
let timer = null;
let replayTimer = null;

const currentImage = computed(() => {
    if (props.slides?.length) return props.slides[slideIndex.value];
    return props.image;
});

onMounted(() => {
    if (props.slides?.length > 1) {
        timer = setInterval(() => {
            slideIndex.value = (slideIndex.value + 1) % props.slides.length;
        }, 2500);
    }

    if (props.video && videoEl.value) {
        videoEl.value.addEventListener("ended", () => {
            videoEl.value.currentTime = 0;
            replayTimer = setTimeout(() => {
                videoEl.value?.play();
            }, 800);
        });
    }
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
    if (replayTimer) clearTimeout(replayTimer);
});
</script>

<template>
    <a :href="file" download class="kit-card">
        <!-- Zone aperçu -->
        <div
            class="kit-card__media"
            :style="
                imageRatio
                    ? { aspectRatio: imageRatio, flex: 'none' }
                    : { minHeight: '180px' }
            "
        >
            <video
                v-if="video"
                ref="videoEl"
                class="kit-card__img"
                :src="video"
                autoplay
                muted
                playsinline
            ></video>

            <template v-else-if="currentImage && !imageError">
                <Transition name="kit-fade">
                    <img
                        :key="currentImage"
                        :src="currentImage"
                        :alt="t(`kit.resource_${resourceKey}_title`)"
                        class="kit-card__img"
                        loading="lazy"
                        @error="imageError = true"
                    />
                </Transition>
            </template>

            <div v-else class="kit-card__placeholder">
                <span class="kit-card__bg-num" aria-hidden="true">
                    {{ String(n).padStart(2, "0") }}
                </span>
                <span class="kit-card__fmt-hint">
                    {{ t(`kit.resource_${resourceKey}_format`) }}
                </span>
            </div>

            <!-- Trait rouge bas animé au hover -->
            <div class="kit-card__bar" aria-hidden="true"></div>
        </div>

        <!-- Zone infos -->
        <div class="kit-card__body">
            <span class="kit-card__format">{{
                t(`kit.resource_${resourceKey}_format`)
            }}</span>
            <h4 class="kit-card__title">
                {{ t(`kit.resource_${resourceKey}_title`) }}
            </h4>
            <p class="kit-card__desc">
                {{ t(`kit.resource_${resourceKey}_desc`) }}
            </p>
            <div class="kit-card__dl">
                {{ t("kit.download_label") }}
            </div>
        </div>
    </a>
</template>

<style scoped>
.kit-card {
    display: flex;
    flex-direction: column;
    border-radius: 0.75rem;
    overflow: hidden;
    background: #ffffff;
    text-decoration: none;
    box-shadow:
        0 1px 3px rgba(25, 5, 7, 0.07),
        0 1px 2px rgba(25, 5, 7, 0.04);
    transition:
        transform 220ms cubic-bezier(0.23, 1, 0.32, 1),
        box-shadow 220ms ease;
}
@media (hover: hover) and (pointer: fine) {
    .kit-card:hover {
        transform: translateY(-3px);
        box-shadow:
            0 14px 42px rgba(25, 5, 7, 0.13),
            0 4px 10px rgba(25, 5, 7, 0.06);
    }
    .kit-card:hover .kit-card__bar {
        opacity: 1;
        transform: scaleX(1);
    }
}

.kit-card__media {
    position: relative;
    background: #f8f8f8;
    overflow: hidden;
    flex-shrink: 0;
}

.kit-card__img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 0;
}

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
    background: #190507;
}

.kit-card__bg-num {
    font-weight: 800;
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
    transition:
        background 150ms ease,
        width 150ms ease,
        border-radius 150ms ease;
}
.kit-card__dot--on {
    background: #d32c37;
    width: 0.9rem;
    border-radius: 0.2rem;
}

.kit-card__bar {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 1;
    height: 3px;
    background: #d32c37;
    opacity: 0;
    transform: scaleX(0);
    transform-origin: left;
    transition:
        opacity 220ms ease,
        transform 220ms ease;
}
.kit-card--featured .kit-card__bar {
    height: 4px;
    opacity: 0.65;
    transform: scaleX(1);
}

.kit-card__body {
    padding: 1rem 1.125rem 1.125rem;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    flex: 1;
}
.kit-card--featured .kit-card__body {
    padding: 1.125rem 1.375rem 1.375rem;
    gap: 0.4rem;
}
.kit-card--wide .kit-card__body {
    padding: 1.25rem 1.5rem;
    gap: 0.4rem;
}

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
.kit-card--featured .kit-card__title {
    font-size: 1.05rem;
}
.kit-card--landscape .kit-card__title {
    font-size: 1rem;
}
.kit-card--wide .kit-card__title {
    font-size: 1rem;
}

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
@media (min-width: 640px) {
    .kit-card--landscape .kit-card__desc {
        display: none;
    }
}

.kit-card__dl {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    margin-top: auto;
    padding-top: 0.5rem;
    font-size: 0.7rem;
    font-weight: 700;
    color: #d32c37;
}
.kit-card--featured .kit-card__dl,
.kit-card--wide .kit-card__dl {
    font-size: 0.775rem;
    gap: 0.375rem;
    margin-top: 0.625rem;
}

.kit-fade-enter-active,
.kit-fade-leave-active {
    transition: opacity 0.8s ease;
}
.kit-fade-enter-from,
.kit-fade-leave-to {
    opacity: 0;
}

@media (prefers-reduced-motion: reduce) {
    .kit-card,
    .kit-card__bar,
    .kit-card__dot {
        transition: none;
    }
    .kit-fade-enter-active,
    .kit-fade-leave-active {
        transition: none;
    }
}
</style>

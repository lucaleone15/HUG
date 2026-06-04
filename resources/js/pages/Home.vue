<script setup>
import { computed, ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import NavBar from '../components/ui/NavBar.vue'
import Footer from '../components/ui/Footer.vue'
import ProgrammeRow from '../components/home/ProgrammeRow.vue'

const { t } = useI18n()

const props = defineProps({
    stats: Object,
    eligible_count: Number,
    entreprises_count: Number,
})

const counters = computed(() => [
    { value: props.stats?.donations_count     ?? 0, label: t('home.stats_donations') },
    { value: props.stats?.lives_saved         ?? 0, label: t('home.stats_lives')     },
    { value: props.stats?.hug_hospitals_count ?? 0, label: t('home.stats_hospitals') },
    { value: props.eligible_count             ?? 0, label: t('home.stats_eligible')  },
    { value: props.entreprises_count          ?? 0, label: t('home.stats_companies') },
])

const marqueeItems = computed(() => [...counters.value, ...counters.value])

// Reusable IO factory
function makeObserver(targetRef, visibleRef, threshold = 0.15) {
    const io = new IntersectionObserver(
        ([e]) => { if (e.isIntersecting) { visibleRef.value = true; io.disconnect() } },
        { threshold }
    )
    if (targetRef.value) io.observe(targetRef.value)
}

const howRef  = ref(null); const howVisible  = ref(false)
const progRef = ref(null); const progVisible = ref(false)
const ctaRef  = ref(null); const ctaVisible  = ref(false)

onMounted(() => {
    makeObserver(howRef,  howVisible)
    makeObserver(progRef, progVisible, 0.1)
    makeObserver(ctaRef,  ctaVisible,  0.2)
})

const steps = computed(() => [
    { title: t('home.how_step_1_title'), desc: t('home.how_step_1_desc') },
    { title: t('home.how_step_2_title'), desc: t('home.how_step_2_desc') },
    { title: t('home.how_step_3_title'), desc: t('home.how_step_3_desc') },
])
</script>

<template>
    <div class="min-h-screen bg-base-100 flex flex-col overflow-x-hidden">
        <NavBar :transparent="true" />

        <!-- ── HERO ─────────────────────────────────────────────────────────── -->
        <section class="relative min-h-[100dvh] flex flex-col justify-center px-6 py-20 overflow-hidden">

            <!-- Image — calque séparé pour Ken Burns -->
            <div class="hero-bg absolute inset-0"></div>

            <!-- Scrim directionnel -->
            <div class="absolute inset-0 pointer-events-none"
                 style="background: linear-gradient(108deg, rgba(25,5,7,0.88) 28%, rgba(25,5,7,0.62) 58%, rgba(25,5,7,0.40) 100%);"></div>

            <div class="relative z-10 max-w-5xl mx-auto w-full">
                <span class="h-badge inline-block text-xs tracking-[0.06em] text-white/65 mb-8">
                    {{ t('home.edition_badge', { year: new Date().getFullYear() }) }}
                </span>
                <h1 class="font-extrabold leading-[1.0] tracking-tight text-white mb-7"
                    style="font-size: clamp(2rem, 6vw, 4.5rem);">
                    <span class="h-line1 block">{{ t('home.hero_line1') }}</span>
                    <span class="h-line2 block"><template v-if="t('home.hero_line2')">{{ t('home.hero_line2') }} </template><span class="h-highlight text-brand">{{ t('home.hero_highlight') }}</span></span>
                </h1>
                <p class="h-sub text-white/55 mb-10 leading-relaxed"
                   style="font-size: clamp(1rem, 1.5vw, 1.075rem); max-width: 42ch;">
                    {{ t('home.hero_subtitle') }}
                </p>
                <div class="h-ctas flex flex-wrap gap-4">
                    <a href="/label"
                        class="btn bg-brand hover:bg-brand-dark text-white border-none font-semibold px-8">
                        {{ t('home.cta') }}
                    </a>
                    <a href="/inscription"
                        class="btn bg-white hover:bg-white/90 text-brand border-none font-semibold px-8">
                        {{ t('home.register_cta') }}
                    </a>
                </div>
            </div>
        </section>

        <!-- ── STATS MARQUEE ──────────────────────────────────────────────────── -->
        <div class="bg-brand py-5 overflow-hidden marquee-container" :aria-label="t('home.stats_aria')">
            <div class="marquee-track flex items-baseline whitespace-nowrap">
                <template v-for="(item, i) in marqueeItems" :key="`m-${i}`">
                    <span class="text-white font-extrabold tabular-nums shrink-0 mx-4 leading-none"
                          style="font-size: clamp(1.75rem, 3vw, 2.5rem);">
                        {{ typeof item.value === 'number' ? item.value.toLocaleString() : item.value }}
                    </span>
                    <span class="text-white/75 tracking-[0.04em] shrink-0 mx-4"
                          style="font-size: clamp(0.8rem, 1.2vw, 0.9rem);">
                        {{ item.label }}
                    </span>
                    <span class="text-white/50 shrink-0 mx-3 leading-none" style="font-size: clamp(1.75rem, 3vw, 2.5rem);" aria-hidden="true">·</span>
                </template>
            </div>
        </div>

        <!-- ── HOW IT WORKS ───────────────────────────────────────────────────── -->
        <section class="py-24 px-6 bg-site-ink" ref="howRef">
            <div class="max-w-5xl mx-auto">

                <p class="reveal-up how-eyebrow"
                   :class="{ 'reveal-up--visible': howVisible }">
                    {{ t('home.how_title') }}
                </p>

                <div class="how-steps-grid">

                    <article class="how-step reveal-up" :class="{ 'reveal-up--visible': howVisible }" style="transition-delay: 120ms;">
                        <svg class="how-step-icon" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3 21h18"/>
                            <path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"/>
                            <path d="M9 8h1"/><path d="M14 8h1"/>
                            <path d="M9 12h1"/><path d="M14 12h1"/>
                            <path d="M9 16h1"/><path d="M14 16h1"/>
                        </svg>
                        <h3 class="how-step-title">{{ steps[0].title }}</h3>
                        <p class="how-step-desc">{{ steps[0].desc }}</p>
                    </article>

                    <article class="how-step reveal-up" :class="{ 'reveal-up--visible': howVisible }" style="transition-delay: 240ms;">
                        <svg class="how-step-icon" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <h3 class="how-step-title">{{ steps[1].title }}</h3>
                        <p class="how-step-desc">{{ steps[1].desc }}</p>
                    </article>

                    <article class="how-step reveal-up" :class="{ 'reveal-up--visible': howVisible }" style="transition-delay: 360ms;">
                        <svg class="how-step-icon" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/>
                        </svg>
                        <h3 class="how-step-title">{{ steps[2].title }}</h3>
                        <p class="how-step-desc">{{ steps[2].desc }}</p>
                    </article>

                </div>

            </div>
        </section>

        <!-- ── PROGRAMME ─────────────────────────────────────────────────────── -->
        <section class="py-20 px-6 bg-base-100" ref="progRef">
            <div class="max-w-5xl mx-auto">
                <h2 class="font-bold mb-8 reveal-up"
                    :class="{ 'reveal-up--visible': progVisible }"
                    style="font-size: clamp(1.25rem, 2.5vw, 1.75rem); letter-spacing: -0.01em;">
                    {{ t('home.programme_label') }}
                </h2>
                <div class="flex flex-col gap-3">

                    <div class="reveal-up prog-row-wrap" :class="{ 'reveal-up--visible': progVisible }">
                        <ProgrammeRow :title="t('trophee.title')" :description="t('trophee.subtitle')" href="/trophee">
                            <template #visual>
                                <div class="w-full h-full bg-site-ink group-hover:bg-brand flex items-center justify-center p-4 transition-colors duration-150">
                                    <img :src="'/images/trophee-rouge-icon.svg'" alt="" class="h-full w-auto max-w-full transition-[filter] duration-150 group-hover:brightness-0 group-hover:invert" aria-hidden="true" decoding="async" />
                                </div>
                            </template>
                        </ProgrammeRow>
                    </div>

                    <div class="reveal-up prog-row-wrap" :class="{ 'reveal-up--visible': progVisible }">
                        <ProgrammeRow :title="t('label.title')" :description="t('label.subtitle')" href="/label">
                            <template #visual>
                                <div class="w-full h-full bg-site-ink group-hover:bg-brand flex items-center justify-center p-3 transition-colors duration-150">
                                    <img :src="'/images/label-plein.svg'" alt="" class="w-full h-full object-contain" aria-hidden="true" decoding="async" />
                                </div>
                            </template>
                        </ProgrammeRow>
                    </div>

                    <div class="reveal-up prog-row-wrap" :class="{ 'reveal-up--visible': progVisible }">
                        <ProgrammeRow :title="t('kit.title')" :description="t('kit.subtitle')" href="/kit-promo">
                            <template #visual>
                                <div class="w-full h-full bg-site-ink group-hover:bg-brand flex items-center justify-center transition-colors duration-150">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" stroke="none" class="text-white/70 group-hover:text-white transition-colors duration-150" aria-hidden="true">
                                        <path d="M3.375 3C2.339 3 1.5 3.84 1.5 4.875v.75c0 1.036.84 1.875 1.875 1.875h17.25c1.035 0 1.875-.84 1.875-1.875v-.75C22.5 3.839 21.66 3 20.625 3H3.375z"/>
                                        <path d="M3.087 9l.54 9.176A3 3 0 006.62 21h10.757a3 3 0 002.995-2.824L20.913 9H3.087z"/>
                                    </svg>
                                </div>
                            </template>
                        </ProgrammeRow>
                    </div>

                </div>
            </div>
        </section>

        <!-- ── CTA ───────────────────────────────────────────────────────────── -->
        <section class="py-24 px-6 bg-brand text-white relative overflow-hidden" ref="ctaRef">
            <div class="absolute inset-0 cta-dot-grid pointer-events-none" aria-hidden="true"></div>
            <div class="max-w-5xl mx-auto relative z-10 grid md:grid-cols-2 gap-12 items-center">

                <div class="reveal-up" :class="{ 'reveal-up--visible': ctaVisible }">
                    <h2 class="font-bold mb-6 leading-tight"
                        style="font-size: clamp(1.75rem, 4vw, 2.75rem);">
                        {{ t('home.cta_section_title') }}
                    </h2>
                    <p class="text-white/65 mb-10 leading-relaxed" style="max-width: 44ch;">
                        {{ t('home.cta_section_description') }}
                    </p>
                    <a href="/inscription"
                        class="btn bg-white hover:bg-white/92 text-brand border-none font-bold px-10 rounded-sm text-sm">
                        {{ t('home.register_cta') }}
                    </a>
                </div>

                <!-- CTA visual: photo + texte superposé -->
                <div class="reveal-up relative aspect-[4/3] rounded-xl overflow-hidden select-none"
                     :class="{ 'reveal-up--visible': ctaVisible }"
                     style="transition-delay: 160ms; background-image: url('https://images.unsplash.com/photo-1697192156499-d85cfe1452c0?auto=format&fit=crop&w=900&q=80'); background-size: cover; background-position: center;">
                    <!-- Scrim pour lisibilité du texte -->
                    <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(25,5,7,0.85) 35%, rgba(25,5,7,0.25) 100%);"></div>
                    <!-- Top-right -->
                    <div class="absolute top-6 right-6 text-right z-10">
                        <div class="text-white/55 text-[0.7rem] mb-1">{{ t('home.impact_lives') }}</div>
                        <div class="font-extrabold leading-none text-white"
                             style="font-size: clamp(2rem, 4vw, 3rem);">3</div>
                    </div>
                    <!-- Bottom strip -->
                    <div class="absolute bottom-0 left-0 right-0 px-6 py-5 z-10">
                        <div class="text-[0.7rem] text-white/45 mb-1">{{ t('home.campaign_badge') }}</div>
                        <div class="font-extrabold text-white leading-tight"
                             style="font-size: clamp(1.2rem, 2.5vw, 1.6rem);">{{ t('home.cta_section_title') }}</div>
                    </div>
                </div>

            </div>
        </section>

        <Footer />
    </div>
</template>

<style scoped>
/* ── Hero : Ken Burns ──────────────────────────────────────────────────── */
.hero-bg {
    background-image: url('/images/home-background.webp');
    background-size: cover;
    background-position: 68% center;
    animation: hero-zoom 8s cubic-bezier(0.25, 0, 0, 1) forwards;
    will-change: transform;
}
/* Sur mobile, recentre pour que la goutte reste visible */
@media (max-width: 640px) {
    .hero-bg { background-position: 80% center; }
}
@keyframes hero-zoom {
    from { transform: scale(1.06); }
    to   { transform: scale(1.0); }
}

/* ── Hero : stagger par élément ────────────────────────────────────────── */
.h-badge { animation: h-rise 420ms cubic-bezier(0.23, 1, 0.32, 1) both; animation-delay:   0ms; }
.h-line1 { animation: h-rise 520ms cubic-bezier(0.23, 1, 0.32, 1) both; animation-delay:  80ms; }
.h-line2 { animation: h-rise 520ms cubic-bezier(0.23, 1, 0.32, 1) both; animation-delay: 170ms; }
.h-sub   { animation: h-rise 480ms cubic-bezier(0.23, 1, 0.32, 1) both; animation-delay: 310ms; }
.h-ctas  { animation: h-rise 480ms cubic-bezier(0.23, 1, 0.32, 1) both; animation-delay: 410ms; }

@keyframes h-rise {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── "vies." — pop final après que la ligne soit apparue (170+520=690ms) ── */
.h-highlight {
    display: inline-block;
    opacity: 0;
    animation: highlight-pop 500ms cubic-bezier(0.23, 1, 0.32, 1) both;
    animation-delay: 740ms;
}
@keyframes highlight-pop {
    from { opacity: 0; transform: translateY(10px) scale(0.9); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

/* ── Stats marquee ─────────────────────────────────────────────────────── */
.marquee-track {
    animation: marquee 30s linear infinite;
    will-change: transform;
}
.marquee-container:hover .marquee-track {
    animation-play-state: paused;
}
@keyframes marquee {
    from { transform: translateX(0); }
    to   { transform: translateX(-50%); }
}

/* ── Shared scroll reveal ──────────────────────────────────────────────── */
.reveal-up {
    opacity: 0;
    transform: translateY(18px);
    transition: opacity 440ms cubic-bezier(0.23, 1, 0.32, 1),
                transform 440ms cubic-bezier(0.23, 1, 0.32, 1);
}
.reveal-up--visible {
    opacity: 1;
    transform: translateY(0);
}

/* ── How it works — dark editorial ────────────────────────────────────── */
.how-eyebrow {
    font-size: clamp(1.25rem, 2.5vw, 1.75rem);
    font-weight: 700;
    letter-spacing: -0.01em;
    color: oklch(92% 0.006 24);
    margin-bottom: 2.75rem;
}
.how-steps-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
}
.how-step {
    display: flex;
    flex-direction: column;
    gap: 1.125rem;
}
.how-step-icon {
    color: var(--color-brand);
    flex-shrink: 0;
}
.how-step-title {
    font-weight: 700;
    line-height: 1.25;
    letter-spacing: -0.015em;
    color: oklch(96% 0.006 24);
    font-size: clamp(1.1rem, 2vw, 1.4rem);
}
.how-step-desc {
    font-size: 0.8125rem;
    line-height: 1.75;
    color: oklch(58% 0.008 24);
}
@media (max-width: 680px) {
    .how-steps-grid {
        grid-template-columns: 1fr;
        gap: 2.5rem;
    }
}

/* ── Programme rows — shadow lift ──────────────────────────────────────── */
.prog-row-wrap {
    background: white;
    border-radius: 0.75rem;
    padding: 0 1.5rem;
    box-shadow: 0 1px 3px rgba(25, 5, 7, 0.08), 0 1px 2px rgba(25, 5, 7, 0.06);
    transition: box-shadow 200ms cubic-bezier(0.23, 1, 0.32, 1),
                transform   200ms cubic-bezier(0.23, 1, 0.32, 1);
}
@media (hover: hover) and (pointer: fine) {
    .prog-row-wrap:hover {
        box-shadow: 0 10px 30px rgba(25, 5, 7, 0.12), 0 4px 8px rgba(25, 5, 7, 0.08);
        transform: translateY(-2px);
    }
}

/* ── CTA dot grid ──────────────────────────────────────────────────────── */
.cta-dot-grid {
    background-image: radial-gradient(circle, rgba(255,255,255,0.11) 1px, transparent 1px);
    background-size: 26px 26px;
}

/* ── Reduced motion ────────────────────────────────────────────────────── */
@media (prefers-reduced-motion: reduce) {
    .hero-bg { animation: none; }
    .h-badge, .h-line1, .h-line2, .h-sub, .h-ctas { animation: none; opacity: 1; transform: none; }
    .h-highlight { animation: none; opacity: 1; transform: none; }
    .marquee-track { animation: none; }
    .reveal-up { opacity: 1; transform: none; transition: none; }
}
</style>

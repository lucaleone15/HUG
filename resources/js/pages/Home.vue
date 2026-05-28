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
        <NavBar />

        <!-- ── HERO ─────────────────────────────────────────────────────────── -->
        <section class="relative min-h-[100dvh] flex flex-col justify-center px-6 py-20 overflow-hidden">

            <!-- Image — calque séparé pour Ken Burns -->
            <div class="hero-bg absolute inset-0"
                 style="background-image: url('https://images.unsplash.com/photo-1615461066841-6116e61058f4?auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center 35%;"></div>

            <!-- Scrim directionnel -->
            <div class="absolute inset-0 pointer-events-none"
                 style="background: linear-gradient(108deg, rgba(25,5,7,0.88) 28%, rgba(25,5,7,0.62) 58%, rgba(25,5,7,0.40) 100%);"></div>

            <div class="relative z-10 max-w-5xl mx-auto w-full">
                <span class="h-badge inline-block text-xs uppercase tracking-[0.2em] text-white/35 mb-8">
                    {{ t('home.edition_badge', { year: new Date().getFullYear() }) }}
                </span>
                <h1 class="font-extrabold leading-[1.0] tracking-tight text-white mb-7"
                    style="font-size: clamp(2.75rem, 6vw, 4.5rem);">
                    <span class="h-line1 block">{{ t('home.hero_line1') }}</span>
                    <span class="h-line2 block">{{ t('home.hero_line2') }} <span class="h-highlight text-brand">{{ t('home.hero_highlight') }}</span></span>
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
                        class="btn text-white font-semibold px-8 border-white/25 bg-white/8 hover:bg-white/15">
                        {{ t('home.register_cta') }}
                    </a>
                </div>
            </div>
        </section>

        <!-- ── STATS MARQUEE ──────────────────────────────────────────────────── -->
        <div class="bg-brand py-5 overflow-hidden marquee-container" aria-label="Statistiques de la campagne">
            <div class="marquee-track flex items-baseline whitespace-nowrap">
                <template v-for="(item, i) in marqueeItems" :key="`m-${i}`">
                    <span class="text-white font-extrabold tabular-nums shrink-0 mx-4 leading-none"
                          style="font-size: clamp(1.75rem, 3vw, 2.5rem);">
                        {{ typeof item.value === 'number' ? item.value.toLocaleString() : item.value }}
                    </span>
                    <span class="text-white/55 uppercase tracking-[0.14em] shrink-0 mx-4"
                          style="font-size: clamp(0.65rem, 1vw, 0.75rem);">
                        {{ item.label }}
                    </span>
                    <span class="text-white/25 shrink-0 mx-3 leading-none" style="font-size: clamp(1.75rem, 3vw, 2.5rem);" aria-hidden="true">·</span>
                </template>
            </div>
        </div>

        <!-- ── HOW IT WORKS ───────────────────────────────────────────────────── -->
        <section class="py-24 px-6 bg-base-200/40" ref="howRef">
            <div class="max-w-5xl mx-auto">
                <h2 class="font-bold mb-14 reveal-up"
                    :class="{ 'reveal-up--visible': howVisible }"
                    style="font-size: clamp(1.5rem, 3vw, 2.25rem);">
                    {{ t('home.how_title') }}
                </h2>
                <div class="divide-y divide-base-300/60">
                    <div v-for="(step, i) in steps" :key="i"
                        class="grid py-9 gap-4 items-start reveal-up"
                        :class="{ 'reveal-up--visible': howVisible }"
                        :style="`grid-template-columns: 3rem 1fr; transition-delay: ${(i + 1) * 130}ms`">
                        <span class="font-mono text-xs text-base-content/28 pt-1 select-none">0{{ i + 1 }}</span>
                        <div>
                            <h3 class="font-semibold mb-1.5" style="font-size: clamp(1rem, 1.5vw, 1.125rem);">
                                {{ step.title }}
                            </h3>
                            <p class="text-base-content/50 text-sm leading-relaxed" style="max-width: 52ch;">
                                {{ step.desc }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── PROGRAMME ─────────────────────────────────────────────────────── -->
        <section class="py-20 px-6 bg-white" ref="progRef">
            <div class="max-w-5xl mx-auto">
                <p class="text-xs uppercase tracking-[0.25em] text-base-content/30 mb-10 reveal-up"
                   :class="{ 'reveal-up--visible': progVisible }">
                    {{ t('home.programme_label') }}
                </p>
                <div class="divide-y divide-base-200">

                    <div class="reveal-up" :class="{ 'reveal-up--visible': progVisible }" style="transition-delay: 80ms;">
                        <ProgrammeRow num="01" :title="t('trophee.title')" :description="t('trophee.subtitle')" href="/trophee">
                            <template #visual>
                                <div class="w-full h-full bg-site-ink flex items-center justify-center">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M6 2h12v7a6 6 0 01-12 0V2z"/>
                                        <path d="M6 5.5H3v1.5a3 3 0 003 3"/>
                                        <path d="M18 5.5h3v1.5a3 3 0 01-3 3"/>
                                        <line x1="12" y1="15" x2="12" y2="18"/>
                                        <line x1="8" y1="18" x2="16" y2="18"/>
                                    </svg>
                                </div>
                            </template>
                        </ProgrammeRow>
                    </div>

                    <div class="reveal-up" :class="{ 'reveal-up--visible': progVisible }" style="transition-delay: 200ms;">
                        <ProgrammeRow num="02" :title="t('label.title')" :description="t('label.subtitle')" href="/label">
                            <template #visual>
                                <div class="w-full h-full bg-brand flex items-center justify-center">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M12 2l2.6 7.9H22l-6.7 4.9 2.6 7.9L12 18.2l-5.9 4.5 2.6-7.9L2 9.9h7.4z"/>
                                    </svg>
                                </div>
                            </template>
                        </ProgrammeRow>
                    </div>

                    <div class="reveal-up" :class="{ 'reveal-up--visible': progVisible }" style="transition-delay: 320ms;">
                        <ProgrammeRow num="03" :title="t('kit.title')" :description="t('kit.subtitle')" href="/kit-promo">
                            <template #visual>
                                <div class="w-full h-full bg-base-300 flex items-center justify-center">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" class="text-base-content/55" aria-hidden="true">
                                        <path d="M12 2L2 7v10l10 5 10-5V7z"/>
                                        <polyline points="2,7 12,12 22,7"/>
                                        <line x1="12" y1="12" x2="12" y2="22"/>
                                        <line x1="7" y1="4.5" x2="17" y2="9.5"/>
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
                        class="btn bg-white hover:bg-white/92 text-brand border-none font-bold px-10 rounded-sm uppercase text-sm tracking-widest">
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
                        <div class="text-white/55 text-[0.6rem] uppercase tracking-[0.22em] mb-1">{{ t('home.impact_lives') }}</div>
                        <div class="font-extrabold leading-none text-white"
                             style="font-size: clamp(2rem, 4vw, 3rem);">3</div>
                    </div>
                    <!-- Bottom strip -->
                    <div class="absolute bottom-0 left-0 right-0 px-6 py-5 z-10">
                        <div class="text-[0.6rem] uppercase tracking-[0.22em] text-white/45 mb-1">{{ t('home.campaign_badge') }}</div>
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
    animation: hero-zoom 8s cubic-bezier(0.25, 0, 0, 1) forwards;
    will-change: transform;
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

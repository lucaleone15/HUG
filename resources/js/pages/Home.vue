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
        <section class="relative min-h-[100dvh] flex flex-col justify-center px-6 py-20 bg-white">
            <div class="max-w-5xl mx-auto w-full grid md:grid-cols-[1.1fr_0.9fr] gap-12 items-center">

                <div class="hero-text">
                    <span class="inline-block text-xs uppercase tracking-[0.2em] text-base-content/35 mb-8">
                        {{ t('home.edition_badge', { year: new Date().getFullYear() }) }}
                    </span>
                    <h1 class="font-extrabold leading-[0.93] tracking-tight text-base-content mb-7"
                        style="font-size: clamp(2.75rem, 6vw, 4.5rem); max-width: 15ch;">
                        {{ t('home.hero_title') }}
                    </h1>
                    <p class="text-base-content/50 mb-10 leading-relaxed"
                       style="font-size: clamp(1rem, 1.5vw, 1.075rem); max-width: 42ch;">
                        {{ t('home.hero_subtitle') }}
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="/label"
                            class="btn bg-brand hover:bg-brand-dark text-white border-none font-semibold px-8">
                            {{ t('home.cta') }}
                        </a>
                        <a href="/inscription"
                            class="btn btn-outline border-base-300 text-base-content hover:bg-base-100 hover:border-base-400 font-semibold px-8">
                            {{ t('home.register_cta') }}
                        </a>
                    </div>
                </div>

                <!-- Visual panel -->
                <div class="hero-visual relative aspect-[4/3] bg-site-ink rounded-xl overflow-hidden select-none">
                    <!-- Ghost year -->
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none overflow-hidden">
                        <span class="font-extrabold leading-none text-white/[0.055]"
                              style="font-size: clamp(7rem, 18vw, 13rem);">2026</span>
                    </div>
                    <!-- Arcs -->
                    <svg class="absolute top-0 right-0 w-52 h-52 opacity-[0.09]" viewBox="0 0 200 200" fill="none" aria-hidden="true">
                        <circle cx="200" cy="0" r="180" stroke="white" stroke-width="1"/>
                        <circle cx="200" cy="0" r="130" stroke="white" stroke-width="1"/>
                        <circle cx="200" cy="0" r="80"  stroke="white" stroke-width="0.5"/>
                    </svg>
                    <!-- Bottom brand gradient -->
                    <div class="absolute bottom-0 left-0 right-0 h-28 bg-gradient-to-t from-brand/30 to-transparent pointer-events-none"></div>
                    <!-- Core metric -->
                    <div class="absolute inset-0 flex items-center justify-center text-white z-10">
                        <div class="text-center">
                            <div class="font-extrabold leading-none"
                                 style="font-size: clamp(3.75rem, 7.5vw, 6rem);">1</div>
                            <div class="text-white/40 text-[0.68rem] uppercase tracking-[0.35em] my-3">
                                {{ t('home.impact_unit') }}
                            </div>
                            <div class="flex items-center justify-center gap-3 mb-3">
                                <div class="w-8 h-px bg-white/15"></div>
                                <svg width="9" height="13" viewBox="0 0 10 14" fill="none" aria-hidden="true">
                                    <path d="M5 0.5C5 0.5 0.5 6.5 0.5 9C0.5 11.5 2.5 13.5 5 13.5C7.5 13.5 9.5 11.5 9.5 9C9.5 6.5 5 0.5 5 0.5Z"
                                          fill="white" fill-opacity="0.5"/>
                                </svg>
                                <div class="w-8 h-px bg-white/15"></div>
                            </div>
                            <div class="font-extrabold leading-none text-brand"
                                 style="font-size: clamp(3.75rem, 7.5vw, 6rem);">3</div>
                            <div class="text-white/40 text-[0.68rem] uppercase tracking-[0.35em] mt-3">
                                {{ t('home.impact_lives') }}
                            </div>
                        </div>
                    </div>
                    <!-- Bottom strip -->
                    <div class="absolute bottom-0 left-0 right-0 px-5 py-4 z-10 flex items-center justify-between">
                        <span class="text-[0.6rem] uppercase tracking-[0.22em] text-white/35">{{ t('home.campaign_badge') }}</span>
                        <span class="text-[0.6rem] uppercase tracking-[0.22em] text-white/35 font-mono">HUG × CTS</span>
                    </div>
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

                <!-- CTA visual: editorial number panel -->
                <div class="reveal-up relative aspect-[4/3] rounded-xl overflow-hidden bg-white/8 select-none"
                     :class="{ 'reveal-up--visible': ctaVisible }"
                     style="transition-delay: 160ms;">
                    <!-- Large ghosted number -->
                    <div class="absolute inset-0 flex items-center justify-center overflow-hidden pointer-events-none">
                        <span class="font-extrabold leading-none text-white/[0.07]"
                              style="font-size: clamp(9rem, 22vw, 16rem);">3</span>
                    </div>
                    <!-- Arcs bottom-left -->
                    <svg class="absolute bottom-0 left-0 w-48 h-48 opacity-[0.10]" viewBox="0 0 200 200" fill="none" aria-hidden="true">
                        <circle cx="0" cy="200" r="160" stroke="white" stroke-width="1"/>
                        <circle cx="0" cy="200" r="110" stroke="white" stroke-width="1"/>
                        <circle cx="0" cy="200" r="60"  stroke="white" stroke-width="0.5"/>
                    </svg>
                    <!-- Top-right content -->
                    <div class="absolute top-6 right-6 text-right">
                        <div class="text-white/40 text-[0.6rem] uppercase tracking-[0.22em] mb-1">{{ t('home.impact_lives') }}</div>
                        <div class="font-extrabold leading-none text-white/90"
                             style="font-size: clamp(2rem, 4vw, 3rem);">3</div>
                    </div>
                    <!-- Bottom strip -->
                    <div class="absolute bottom-0 left-0 right-0 px-6 py-5 border-t border-white/12">
                        <div class="text-[0.6rem] uppercase tracking-[0.22em] text-white/30 mb-1">{{ t('home.campaign_badge') }}</div>
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
/* ── Hero entrance ────────────────────────────────────────────────────── */
.hero-text {
    animation: hero-rise 580ms cubic-bezier(0.23, 1, 0.32, 1) both;
    animation-delay: 60ms;
}
.hero-visual {
    animation: hero-rise 680ms cubic-bezier(0.23, 1, 0.32, 1) both;
    animation-delay: 180ms;
}
@keyframes hero-rise {
    from { opacity: 0; transform: translateY(22px); }
    to   { opacity: 1; transform: translateY(0);    }
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
    .hero-text, .hero-visual { animation: none; opacity: 1; transform: none; }
    .marquee-track { animation: none; }
    .reveal-up { opacity: 1; transform: none; transition: none; }
}
</style>

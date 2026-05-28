<script setup>
import { computed, ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import NavBar from '../components/ui/NavBar.vue'
import Footer from '../components/ui/Footer.vue'
import PageHero from '../components/ui/PageHero.vue'

const { t } = useI18n()

function makeObserver(targetRef, visibleRef, threshold = 0.15) {
    const io = new IntersectionObserver(
        ([e]) => { if (e.isIntersecting) { visibleRef.value = true; io.disconnect() } },
        { threshold }
    )
    if (targetRef.value) io.observe(targetRef.value)
}

const criteriaRef = ref(null); const criteriaVisible = ref(false)
const rankRef     = ref(null); const rankVisible     = ref(false)
const palmRef     = ref(null); const palmVisible     = ref(false)
const ctaRef      = ref(null); const ctaVisible      = ref(false)

onMounted(() => {
    makeObserver(criteriaRef, criteriaVisible, 0.1)
    makeObserver(rankRef,     rankVisible,     0.1)
    makeObserver(palmRef,     palmVisible,     0.1)
    makeObserver(ctaRef,      ctaVisible,      0.2)
})

const props = defineProps({ winners: Array })

const winner1 = computed(() => (props.winners ?? []).find(w => w.trophy_rank === 1) ?? null)
const others  = computed(() => (props.winners ?? []).filter(w => w.trophy_rank > 1).sort((a, b) => a.trophy_rank - b.trophy_rank))

const previousWinners = [
    { year: 2025, name: 'Rolex SA',                            type: 'horlogerie',       eligible: 142, rate: '68 %', color: '#006039', jury: '94 %', employees: 5800, victories: 5 },
    { year: 2024, name: 'Groupe Mutuel',                       type: 'assurance',        eligible: 389, rate: '78 %', color: '#003F87' },
    { year: 2023, name: 'Banque Cantonale de Genève',          type: 'banque',           eligible: 201, rate: '82 %', color: '#00205B' },
    { year: 2022, name: 'SIG — Services Industriels Genève',   type: 'services_publics', eligible: 310, rate: '71 %', color: '#00A650' },
    { year: 2021, name: 'Kudelski Group',                      type: 'technologie',      eligible: 245, rate: '68 %', color: '#C8001A' },
    { year: 2020, name: 'Hôpital de la Tour',                  type: 'sante',            eligible: 433, rate: '74 %', color: '#0072CE' },
    { year: 2019, name: 'Swissquote Bank',                     type: 'fintech',          eligible: 178, rate: '63 %', color: '#FF6600' },
]

const criteria = computed(() => [0, 1, 2, 3].map(i => ({
    title: t(`trophee.criteria_${i}_title`),
    desc:  t(`trophee.criteria_${i}_desc`),
})))
</script>

<template>
    <div class="min-h-screen bg-base-100 flex flex-col">
        <NavBar />

        <PageHero
            :title="t('trophee.title')"
            :subtitle="t('trophee.subtitle')"
            :cta="{ label: t('nav.inscription'), href: '/inscription' }"
        >
            <template #visual>
                <div class="aspect-[4/3] bg-site-ink rounded-xl overflow-hidden flex flex-col items-start justify-end p-8 relative select-none">
                    <span class="absolute top-6 right-6 text-[0.65rem] uppercase tracking-[0.2em] text-white/40 font-semibold">
                        {{ new Date().getFullYear() }}
                    </span>
                    <!-- Decorative arc -->
                    <svg class="absolute top-0 right-0 w-48 h-48 opacity-10" viewBox="0 0 200 200" fill="none" aria-hidden="true">
                        <circle cx="200" cy="0" r="160" stroke="white" stroke-width="1.5"/>
                        <circle cx="200" cy="0" r="120" stroke="white" stroke-width="1"/>
                        <circle cx="200" cy="0" r="80"  stroke="white" stroke-width="0.5"/>
                    </svg>
                    <div class="relative z-10">
                        <div class="text-white/40 text-xs uppercase tracking-[0.25em] mb-3">{{ t('trophee.visual_badge') }}</div>
                        <div class="font-extrabold leading-tight text-white"
                             style="font-size: clamp(2rem, 4.5vw, 3.25rem);">{{ t('trophee.visual_line1') }}</div>
                        <div class="font-extrabold leading-tight text-brand"
                             style="font-size: clamp(2rem, 4.5vw, 3.25rem);">{{ t('trophee.visual_line2') }}</div>
                    </div>
                </div>
            </template>
        </PageHero>

        <!-- Critères — liste numérotée sur fond rouge -->
        <section class="py-16 px-6 bg-brand text-white" ref="criteriaRef">
            <div class="max-w-5xl mx-auto">
                <h2 class="font-bold mb-3 leading-tight reveal-up"
                    :class="{ 'reveal-up--visible': criteriaVisible }"
                    style="font-size: clamp(1.5rem, 3vw, 2.25rem);">
                    {{ t('trophee.how_title') }}
                </h2>
                <p class="text-white/80 mb-10 reveal-up"
                   :class="{ 'reveal-up--visible': criteriaVisible }"
                   style="max-width: 52ch; transition-delay: 80ms;">
                    {{ t('trophee.how_text') }}
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-16 gap-y-7 mb-8">
                    <div v-for="(c, i) in criteria" :key="c.title"
                        class="flex gap-4 reveal-up"
                        :class="{ 'reveal-up--visible': criteriaVisible }"
                        :style="`transition-delay: ${(i + 2) * 90}ms`">
                        <span class="font-mono text-xs text-white/30 mt-1 shrink-0 w-5 select-none">0{{ i + 1 }}</span>
                        <div>
                            <h3 class="font-bold mb-1">{{ c.title }}</h3>
                            <p class="text-sm text-white/70 leading-relaxed">{{ c.desc }}</p>
                        </div>
                    </div>
                </div>

                <p class="text-sm text-white/50 italic border-t border-white/15 pt-6 reveal-up"
                   :class="{ 'reveal-up--visible': criteriaVisible }"
                   style="max-width: 58ch; transition-delay: 500ms;">
                    {{ t('trophee.how_note') }}
                </p>
            </div>
        </section>

        <!-- Classement édition en cours -->
        <main class="max-w-5xl mx-auto px-6 py-12 flex-1 w-full" ref="rankRef">
            <div v-if="!winners?.length" class="text-base-content/50 py-8 reveal-up"
                 :class="{ 'reveal-up--visible': rankVisible }">
                {{ t('trophee.no_winners') }}
            </div>

            <template v-if="winners?.length">
                <h2 class="font-bold mb-1 reveal-up"
                    :class="{ 'reveal-up--visible': rankVisible }"
                    style="font-size: clamp(1.35rem, 2.5vw, 1.75rem);">
                    {{ t('trophee.rank_title') }} <span class="text-brand">{{ new Date().getFullYear() }}</span>
                </h2>
                <p class="text-base-content/50 text-sm mb-6 reveal-up"
                   :class="{ 'reveal-up--visible': rankVisible }"
                   style="transition-delay: 60ms;">
                    {{ t('trophee.rank_subtitle') }}
                </p>
            </template>

            <template v-if="others.length || winner1">
                <ul class="divide-y divide-base-200">
                    <li v-for="w in [winner1, ...others].filter(Boolean)" :key="w.id"
                        class="flex items-center gap-4 py-4">
                        <div v-if="w.logo_url"
                            class="w-10 h-10 rounded-full bg-base-200 overflow-hidden flex items-center justify-center shrink-0">
                            <img :src="w.logo_url" :alt="w.name" class="max-h-8 max-w-full object-contain">
                        </div>
                        <div v-else class="w-10 h-10 rounded-full shrink-0"
                            :style="`background-color: ${w.primary_color}`"></div>
                        <div class="flex-1">
                            <div class="font-semibold">{{ w.name }}</div>
                            <div v-if="w.type" class="text-xs text-base-content/40">{{ t('inscription.type_' + w.type) }}</div>
                        </div>
                        <span class="text-lg font-bold text-base-content/30">#{{ w.trophy_rank }}</span>
                    </li>
                </ul>
            </template>
        </main>

        <!-- Palmarès -->
        <section class="py-16 px-6 bg-base-200" ref="palmRef">
            <div class="max-w-5xl mx-auto">
                <h2 class="font-bold mb-1 reveal-up"
                    :class="{ 'reveal-up--visible': palmVisible }"
                    style="font-size: clamp(1.35rem, 2.5vw, 1.75rem);">
                    {{ t('trophee.palmares_title') }}
                </h2>
                <p class="text-base-content/50 text-sm mb-10 reveal-up"
                   :class="{ 'reveal-up--visible': palmVisible }"
                   style="transition-delay: 60ms;">
                    {{ t('trophee.palmares_subtitle') }}
                </p>

                <!-- Gagnant 2025 — éditoriale -->
                <div class="bg-white rounded-xl px-8 py-8 mb-4 reveal-up"
                     :class="{ 'reveal-up--visible': palmVisible }"
                     style="transition-delay: 130ms;">
                    <div class="flex items-center gap-2 mb-6">
                        <span class="text-xs uppercase tracking-[0.2em] text-brand font-semibold">
                            {{ t('trophee.winner_label') }}
                        </span>
                        <span class="text-xs text-base-content/35 font-mono">{{ previousWinners[0].year }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-xl shrink-0"
                                 :style="`background-color: ${previousWinners[0].color}`">
                                {{ previousWinners[0].name[0] }}
                            </div>
                            <div>
                                <h3 class="font-bold" style="font-size: clamp(1.1rem, 2vw, 1.4rem);">
                                    {{ previousWinners[0].name }}
                                </h3>
                                <p class="text-sm text-base-content/40">{{ t('inscription.type_' + previousWinners[0].type) }}</p>
                            </div>
                        </div>
                        <div class="flex gap-8 shrink-0">
                            <div class="text-right">
                                <div class="text-2xl font-bold text-brand">{{ previousWinners[0].eligible }}</div>
                                <div class="text-xs text-base-content/40 mt-0.5">{{ t('trophee.stat_donations') }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold">{{ previousWinners[0].rate }}</div>
                                <div class="text-xs text-base-content/40 mt-0.5">{{ t('trophee.stat_participation') }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold">{{ previousWinners[0].victories }}<sup class="text-sm">e</sup></div>
                                <div class="text-xs text-base-content/40 mt-0.5">{{ t('trophee.stat_consecutive') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Éditions précédentes -->
                <div class="divide-y divide-base-300 bg-white rounded-xl overflow-hidden reveal-up"
                     :class="{ 'reveal-up--visible': palmVisible }"
                     style="transition-delay: 220ms;">
                    <div v-for="w in previousWinners.slice(1)" :key="w.year"
                        class="grid gap-4 px-6 py-4 items-center"
                        style="grid-template-columns: 3.5rem 1fr auto;">
                        <span class="font-mono text-xs text-base-content/35">{{ w.year }}</span>
                        <div class="flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0"
                                  :style="`background-color: ${w.color}`"></span>
                            <div>
                                <div class="font-semibold text-sm leading-tight">{{ w.name }}</div>
                                <div class="text-xs text-base-content/40">{{ t('inscription.type_' + w.type) }}</div>
                            </div>
                        </div>
                        <div class="flex gap-6 text-right">
                            <div>
                                <div class="text-sm font-bold text-brand">{{ w.eligible }}</div>
                                <div class="text-xs text-base-content/35">{{ t('trophee.stat_donations') }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-bold">{{ w.rate }}</div>
                                <div class="text-xs text-base-content/35">{{ t('trophee.stat_participation') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA candidatures -->
        <section class="py-16 px-6 bg-site-ink text-white" ref="ctaRef">
            <div class="max-w-5xl mx-auto">
                <h2 class="font-bold mb-6 leading-tight reveal-up"
                    :class="{ 'reveal-up--visible': ctaVisible }"
                    style="font-size: clamp(1.75rem, 4vw, 2.75rem);">
                    {{ t('trophee.cta_section_title') }}
                </h2>
                <p class="text-white/55 text-sm mb-8 reveal-up"
                   :class="{ 'reveal-up--visible': ctaVisible }"
                   style="max-width: 46ch; transition-delay: 90ms;">
                    {{ t('trophee.cta_section_description') }}
                </p>
                <a href="/inscription"
                    class="btn bg-white text-black hover:bg-white/90 border-none font-semibold px-8 rounded-sm uppercase text-sm tracking-wide reveal-up active:scale-[0.97]"
                    :class="{ 'reveal-up--visible': ctaVisible }"
                    style="transition-delay: 180ms;">
                    {{ t('trophee.cta_button') }}
                </a>
            </div>
        </section>

        <Footer />
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import NavBar from '../components/ui/NavBar.vue'
import Footer from '../components/ui/Footer.vue'
import PageHero from '../components/ui/PageHero.vue'

const { t } = useI18n()

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

        <!-- Hero 2 colonnes -->
        <PageHero
            :title="t('trophee.title')"
            :subtitle="t('trophee.subtitle')"
            :cta="{ label: t('nav.inscription'), href: '/inscription' }"
        >
            <template #visual>
                <div class="aspect-[4/3] bg-base-200 rounded-xl flex items-center justify-center text-base-content/20 text-sm italic">
                    Visuelle du trophée
                </div>
            </template>
        </PageHero>

        <!-- Section critères (rouge) -->
        <section class="py-16 px-6 bg-brand text-white">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-2xl md:text-3xl font-bold mb-3 leading-tight">
                    {{ t('trophee.how_title') }}
                </h2>
                <p class="text-white/80 mb-10 max-w-2xl">{{ t('trophee.how_text') }}</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                    <div v-for="c in criteria" :key="c.title"
                        class="bg-white/10 rounded-xl p-5">
                        <h3 class="font-bold mb-1">{{ c.title }}</h3>
                        <p class="text-sm text-white/70">{{ c.desc }}</p>
                    </div>
                </div>
                <p class="text-sm text-white/60 italic">{{ t('trophee.how_note') }}</p>
            </div>
        </section>

        <main class="max-w-5xl mx-auto px-6 py-12 flex-1 w-full">

            <div v-if="!winners?.length" class="text-base-content/50 py-8">
                {{ t('trophee.no_winners') }}
            </div>

            <!-- Classement édition en cours -->
            <template v-if="winners?.length">
                <h2 class="text-2xl font-bold mb-1">
                    {{ t('trophee.rank_title') }} <span class="text-brand">{{ new Date().getFullYear() }}</span>
                </h2>
                <p class="text-base-content/50 text-sm mb-6">{{ t('trophee.rank_subtitle') }}</p>
            </template>

            <template v-if="others.length || winner1">
                <ul class="divide-y divide-base-200">
                    <li v-for="w in [winner1, ...others].filter(Boolean)" :key="w.id" class="flex items-center gap-4 py-4">
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

        <!-- Palmarès éditions précédentes -->
        <section class="py-16 px-6 bg-base-200">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-2xl font-bold mb-2">{{ t('trophee.palmares_title') }}</h2>
                <p class="text-base-content/50 text-sm mb-10">{{ t('trophee.palmares_subtitle') }}</p>
                <!-- Gagnant 2025 — carte mise en avant -->
                <div class="bg-white rounded-2xl p-10 text-center mb-6 shadow-sm border border-base-200">
                    <p class="text-2xl font-bold text-left mb-8">
                        {{ t('trophee.winner_label') }} <span class="text-brand">{{ previousWinners[0].year }}</span>
                    </p>
                    <div class="flex items-center justify-center mb-4 min-h-[80px]">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center text-white text-2xl font-bold"
                            :style="`background-color: ${previousWinners[0].color}`">
                            {{ previousWinners[0].name[0] }}
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold mb-1">{{ previousWinners[0].name }}</h3>
                    <p class="text-base-content/40 text-sm mb-8">{{ t('inscription.type_' + previousWinners[0].type) }}</p>
                    <div class="flex justify-center divide-x divide-base-200">
                        <div class="px-8 text-center">
                            <div class="text-3xl font-bold">{{ previousWinners[0].eligible }}</div>
                            <div class="text-xs text-base-content/40 mt-1">{{ t('trophee.stat_donations') }}</div>
                        </div>
                        <div class="px-8 text-center">
                            <div class="text-3xl font-bold">{{ previousWinners[0].rate }}</div>
                            <div class="text-xs text-base-content/40 mt-1">{{ t('trophee.stat_participation') }}</div>
                        </div>
                        <div class="px-8 text-center">
                            <div class="text-3xl font-bold">{{ previousWinners[0].victories }}<sup class="text-lg">e</sup></div>
                            <div class="text-xs text-base-content/40 mt-1">{{ t('trophee.stat_consecutive') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Éditions 2024–2019 -->
                <div class="space-y-3">
                    <div v-for="w in previousWinners.slice(1)" :key="w.year"
                        class="bg-white rounded-xl px-6 py-5 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div class="shrink-0 bg-brand text-white text-sm font-bold rounded-lg px-3 py-1.5 min-w-[60px] text-center">
                            {{ w.year }}
                        </div>
                        <div class="flex items-center gap-3 flex-1">
                            <span class="w-3 h-3 rounded-full shrink-0" :style="`background-color: ${w.color}`"></span>
                            <div>
                                <div class="font-semibold leading-tight">{{ w.name }}</div>
                                <div class="text-xs text-base-content/40 mt-0.5">{{ t('inscription.type_' + w.type) }}</div>
                            </div>
                        </div>
                        <div class="flex gap-6 shrink-0">
                            <div class="text-right">
                                <div class="text-lg font-bold text-brand">{{ w.eligible }}</div>
                                <div class="text-xs text-base-content/40">{{ t('trophee.stat_donations') }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold">{{ w.rate }}</div>
                                <div class="text-xs text-base-content/40">{{ t('trophee.stat_participation') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA candidatures ouvertes -->
        <section class="py-16 px-6 bg-black text-white">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-3xl font-bold mb-6 leading-tight">{{ t('trophee.cta_section_title') }}</h2>
                <p class="text-white/60 text-sm mb-8 max-w-lg">{{ t('trophee.cta_section_description') }}</p>
                <a href="/inscription"
                    class="btn bg-white text-black hover:bg-white/90 border-none font-semibold px-8 rounded-sm uppercase text-sm tracking-wide">
                    {{ t('trophee.cta_button') }}
                </a>
            </div>
        </section>

        <Footer />
    </div>
</template>

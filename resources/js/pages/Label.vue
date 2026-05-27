<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import NavBar from '../components/ui/NavBar.vue'
import Footer from '../components/ui/Footer.vue'
import PageHero from '../components/ui/PageHero.vue'
import LabelCard from '../components/label/LabelCard.vue'
import SectorFilter from '../components/label/SectorFilter.vue'

const { t } = useI18n()

const props = defineProps({ entreprises: Array })

const selectedSector = ref(null)

const sectors = computed(() => {
    const all = (props.entreprises ?? []).map(e => e.type).filter(Boolean)
    return [...new Set(all)].sort()
})

const filtered = computed(() => {
    if (!selectedSector.value) return props.entreprises ?? []
    return (props.entreprises ?? []).filter(e => e.type === selectedSector.value)
})
</script>

<template>
    <div class="min-h-screen bg-base-100 flex flex-col">
        <NavBar />

        <!-- Hero 2 colonnes -->
        <PageHero
            :title="t('label.title')"
            :subtitle="t('label.subtitle')"
            :cta="{ label: t('label.hero_cta'), href: '/inscription' }"
        >
            <template #visual>
                <div class="aspect-[4/3] bg-base-200 rounded-xl flex items-center justify-center text-base-content/20 text-sm italic">
                    Visuelle du label
                </div>
            </template>
        </PageHero>

        <!-- Section explication (rouge) -->
        <section class="py-16 px-6 bg-brand text-white">
            <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-10 items-start">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold leading-tight">
                        {{ t('label.section_title') }}
                    </h2>
                </div>
                <div class="space-y-4">
                    <p class="text-white/80 leading-relaxed">{{ t('label.section_text_1') }}</p>
                    <p class="text-white/80 leading-relaxed">{{ t('label.section_text_2') }}</p>
                </div>
            </div>
        </section>

        <main class="max-w-5xl mx-auto px-6 py-12 flex-1 w-full">

            <!-- Compteur -->
            <div class="mb-8">
                <span class="text-6xl font-bold text-base-content">{{ filtered.length }}</span>
                <p class="text-base-content/50 text-lg mt-1">{{ t('label.count_label') }}</p>
            </div>

            <SectorFilter v-if="sectors.length" :sectors="sectors" v-model="selectedSector" />

            <div v-if="!filtered.length" class="text-base-content/50 py-4">{{ t('label.no_label') }}</div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                <LabelCard v-for="e in filtered" :key="e.id" :entreprise="e" />
            </div>
        </main>

        <!-- CTA "Votre entreprise ne figure pas ?" -->
        <section class="py-16 px-6 border-t border-base-200">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-3xl font-bold mb-2 leading-tight">{{ t('label.cta_section_title') }}</h2>
                <p class="text-2xl font-bold italic text-brand mb-6">{{ t('label.cta_italic') }}</p>
                <p class="text-base-content/60 mb-8 max-w-lg mx-auto leading-relaxed">
                    {{ t('label.cta_section_description') }}
                </p>
                <a href="/inscription"
                    class="btn bg-black hover:bg-black/80 text-white border-none font-semibold px-8 rounded-sm uppercase text-sm tracking-wide">
                    {{ t('label.cta_button') }}
                </a>
            </div>
        </section>

        <Footer />
    </div>
</template>

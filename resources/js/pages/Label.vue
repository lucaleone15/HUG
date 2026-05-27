<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import NavBar from '../components/ui/NavBar.vue'
import Footer from '../components/ui/Footer.vue'
import LabelHero from '../components/label/LabelHero.vue'
import LabelCard from '../components/label/LabelCard.vue'
import SectorFilter from '../components/label/SectorFilter.vue'
import SectionCTA from '../components/trophee/SectionCTA.vue'

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
        <main class="max-w-5xl mx-auto px-6 py-12 flex-1 w-full">
            <LabelHero :title="t('label.title')" :subtitle="t('label.subtitle')" />
            <SectorFilter v-if="sectors.length" :sectors="sectors" v-model="selectedSector" />
            <div v-if="!filtered.length" class="text-base-content/50">{{ t('label.no_label') }}</div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <LabelCard v-for="e in filtered" :key="e.id" :entreprise="e" />
            </div>
        </main>
        <SectionCTA
            :title="t('label.cta_section_title')"
            :description="t('label.cta_section_description')"
            :cta="{ label: t('nav.inscription'), href: '/inscription' }"
        />
        <Footer />
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import NavBar from '../components/ui/NavBar.vue'
import Footer from '../components/ui/Footer.vue'
import TrophyHero from '../components/trophee/TrophyHero.vue'
import TrophyFeedCard from '../components/trophee/TrophyFeedCard.vue'
import SectionCTA from '../components/trophee/SectionCTA.vue'

const { t } = useI18n()

const props = defineProps({ winners: Array })

const podium = computed(() => (props.winners ?? []).filter(w => w.trophy_rank <= 3))
const others = computed(() => (props.winners ?? []).filter(w => w.trophy_rank > 3))
</script>

<template>
    <div class="min-h-screen bg-base-100 flex flex-col">
        <NavBar />
        <main class="max-w-4xl mx-auto px-6 py-12 flex-1 w-full">
            <TrophyHero :title="t('trophee.title')" :subtitle="t('trophee.subtitle')" />
            <div v-if="!winners?.length" class="text-base-content/50">{{ t('trophee.no_winners') }}</div>
            <div v-if="podium.length" class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
                <TrophyFeedCard v-for="w in podium" :key="w.id" :winner="w" mode="podium" />
            </div>
            <ul v-if="others.length" class="divide-y divide-base-200">
                <TrophyFeedCard v-for="w in others" :key="w.id" :winner="w" mode="list" />
            </ul>
        </main>
        <SectionCTA
            :title="t('trophee.cta_section_title')"
            :description="t('trophee.cta_section_description')"
            :cta="{ label: t('nav.inscription'), href: '/inscription' }"
        />
        <Footer />
    </div>
</template>

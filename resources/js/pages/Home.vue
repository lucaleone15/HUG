<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import NavBar from '../components/ui/NavBar.vue'
import Footer from '../components/ui/Footer.vue'
import PageHero from '../components/ui/PageHero.vue'
import StatsBanner from '../components/ui/StatsBanner.vue'
import PageCard from '../components/home/PageCard.vue'
import SectionCTA from '../components/trophee/SectionCTA.vue'

const { t } = useI18n()

const props = defineProps({
    stats: Object,
    eligible_count: Number,
    entreprises_count: Number,
})

const counters = computed(() => [
    { value: props.stats?.donations_count     ?? 0, label: t('home.stats_donations'), color: 'text-[#E30613]'   },
    { value: props.stats?.lives_saved         ?? 0, label: t('home.stats_lives'),     color: 'text-emerald-600' },
    { value: props.stats?.hug_hospitals_count ?? 0, label: t('home.stats_hospitals'), color: 'text-blue-600'    },
    { value: props.eligible_count             ?? 0, label: t('home.stats_eligible'),  color: 'text-violet-600'  },
    { value: props.entreprises_count          ?? 0, label: t('home.stats_companies'), color: 'text-amber-600'   },
])

const cards = computed(() => [
    { icon: '🏆', title: t('nav.trophee'), description: t('trophee.subtitle'), href: '/trophee'   },
    { icon: '🏅', title: t('nav.label'),   description: t('label.subtitle'),   href: '/label'     },
    { icon: '📦', title: t('nav.kit'),     description: t('kit.subtitle'),     href: '/kit-promo' },
])
</script>

<template>
    <div class="min-h-screen bg-base-100 flex flex-col">
        <NavBar />
        <PageHero
            :title="t('home.hero_title')"
            :subtitle="t('home.hero_subtitle')"
            :cta="{ label: t('home.cta'), href: '/label' }"
        />
        <StatsBanner :counters="counters" />
        <section class="py-16 px-6">
            <div class="max-w-4xl mx-auto grid md:grid-cols-3 gap-6">
                <PageCard v-for="c in cards" :key="c.href" v-bind="c" />
            </div>
        </section>
        <SectionCTA
            :title="t('home.cta_section_title')"
            :description="t('home.cta_section_description')"
            :cta="{ label: t('nav.inscription'), href: '/inscription' }"
        />
        <Footer />
    </div>
</template>

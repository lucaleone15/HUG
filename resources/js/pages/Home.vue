<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import NavBar from '../components/ui/NavBar.vue'
import Footer from '../components/ui/Footer.vue'
import PageHero from '../components/ui/PageHero.vue'
import StatsBanner from '../components/ui/StatsBanner.vue'
import PageCard from '../components/home/PageCard.vue'

const { t } = useI18n()

const props = defineProps({
    stats: Object,
    eligible_count: Number,
    entreprises_count: Number,
})

const counters = computed(() => [
    { value: props.stats?.donations_count     ?? 0, label: t('home.stats_donations'), color: 'text-white' },
    { value: props.stats?.lives_saved         ?? 0, label: t('home.stats_lives'),     color: 'text-white' },
    { value: props.stats?.hug_hospitals_count ?? 0, label: t('home.stats_hospitals'), color: 'text-white' },
    { value: props.eligible_count             ?? 0, label: t('home.stats_eligible'),  color: 'text-white' },
    { value: props.entreprises_count          ?? 0, label: t('home.stats_companies'), color: 'text-white' },
])

const cards = computed(() => [
    { icon: '🏆', title: t('trophee.title'), description: t('trophee.subtitle'), href: '/trophee',   cta: t('home.read_more') },
    { icon: '🏅', title: t('label.title'),   description: t('label.subtitle'),   href: '/label',     cta: t('home.read_more') },
    { icon: '📦', title: t('kit.title'),     description: t('kit.subtitle'),     href: '/kit-promo', cta: t('home.read_more') },
])
</script>

<template>
    <div class="min-h-screen bg-base-100 flex flex-col">
        <NavBar />

        <!-- Hero 2 colonnes -->
        <PageHero
            :title="t('home.hero_title')"
            :subtitle="t('home.hero_subtitle')"
            :cta="{ label: t('home.cta'), href: '/label' }"
        >
            <template #visual>
                <div class="relative aspect-[4/3] bg-base-200 rounded-xl overflow-hidden flex items-center justify-center text-base-content/20 text-sm italic">
                    Visuelle d'une goute
                    <span class="absolute top-3 right-3 bg-black text-white text-xs font-semibold px-3 py-1 rounded">
                        {{ t('home.edition_badge', { year: new Date().getFullYear() }) }}
                    </span>
                </div>
            </template>
        </PageHero>

        <!-- Stats banner rouge -->
        <StatsBanner :counters="counters" :dark="true" />

        <!-- Comment ça se passe -->
        <section class="py-16 px-6 bg-white">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-2xl md:text-3xl font-bold mb-10">{{ t('home.how_title') }}</h2>
                <div class="aspect-video bg-base-200 rounded-xl flex items-center justify-center text-base-content/30 text-lg font-light italic">
                    Vidéo
                </div>
            </div>
        </section>

        <!-- Cards -->
        <section class="py-16 px-6">
            <div class="max-w-5xl mx-auto grid md:grid-cols-3 gap-6">
                <PageCard v-for="c in cards" :key="c.href" v-bind="c" />
            </div>
        </section>

        <!-- CTA Organiser une collecte -->
        <section class="py-16 px-6 bg-brand text-white">
            <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-10 items-center">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold mb-6 leading-tight">
                        {{ t('home.cta_section_title') }}
                    </h2>
                    <p class="text-white/80 mb-8 leading-relaxed">{{ t('home.cta_section_description') }}</p>
                    <a href="/inscription"
                        class="btn bg-black hover:bg-black/80 text-white border-none font-semibold px-8 rounded-sm uppercase text-sm tracking-wide">
                        {{ t('home.register_cta') }}
                    </a>
                </div>
                <div class="aspect-[4/3] bg-white/10 rounded-xl flex items-center justify-center text-white/20 text-sm italic">
                    Visuelle
                </div>
            </div>
        </section>

        <Footer />
    </div>
</template>

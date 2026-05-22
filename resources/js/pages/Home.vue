<script setup>
import { useI18n } from 'vue-i18n'
import NavBar from '../components/NavBar.vue'

const { t } = useI18n()

const props = defineProps({
    stats: Object,
    eligible_count: Number,
    entreprises_count: Number,
})

const counters = [
    { value: () => props.stats?.donations_count ?? 0,   key: 'home.stats_donations', color: 'text-[#E30613]' },
    { value: () => props.stats?.lives_saved ?? 0,        key: 'home.stats_lives',     color: 'text-emerald-600' },
    { value: () => props.stats?.hug_hospitals_count ?? 0,key: 'home.stats_hospitals', color: 'text-blue-600' },
    { value: () => props.eligible_count ?? 0,            key: 'home.stats_eligible',  color: 'text-violet-600' },
    { value: () => props.entreprises_count ?? 0,         key: 'home.stats_companies', color: 'text-amber-600' },
]
</script>

<template>
    <div class="min-h-screen bg-base-100">
        <NavBar />

        <!-- Hero -->
        <section class="bg-[#E30613] text-white py-20 px-6">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">
                    {{ t('home.hero_title') }}
                </h1>
                <p class="text-lg text-white/80 mb-8 max-w-xl mx-auto">
                    {{ t('home.hero_subtitle') }}
                </p>
                <a href="/label" class="btn bg-white text-[#E30613] hover:bg-white/90 border-none font-semibold px-8">
                    {{ t('home.cta') }}
                </a>
            </div>
        </section>

        <!-- Stats -->
        <section class="py-16 px-6 bg-base-200">
            <div class="max-w-4xl mx-auto grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                <div
                    v-for="c in counters"
                    :key="c.key"
                    class="card bg-base-100 shadow-sm text-center p-4"
                >
                    <div class="text-3xl font-bold" :class="c.color">
                        {{ c.value().toLocaleString() }}
                    </div>
                    <div class="text-xs text-base-content/60 mt-1 leading-tight">
                        {{ t(c.key) }}
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA sections -->
        <section class="py-16 px-6">
            <div class="max-w-4xl mx-auto grid md:grid-cols-3 gap-6">
                <a href="/trophee" class="card bg-base-100 border border-base-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="card-body">
                        <div class="text-3xl mb-2">🏆</div>
                        <h3 class="card-title text-base">{{ t('nav.trophee') }}</h3>
                        <p class="text-sm text-base-content/60">{{ t('trophee.subtitle') }}</p>
                    </div>
                </a>
                <a href="/label" class="card bg-base-100 border border-base-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="card-body">
                        <div class="text-3xl mb-2">🏅</div>
                        <h3 class="card-title text-base">{{ t('nav.label') }}</h3>
                        <p class="text-sm text-base-content/60">{{ t('label.subtitle') }}</p>
                    </div>
                </a>
                <a href="/kit-promo" class="card bg-base-100 border border-base-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="card-body">
                        <div class="text-3xl mb-2">📦</div>
                        <h3 class="card-title text-base">{{ t('nav.kit') }}</h3>
                        <p class="text-sm text-base-content/60">{{ t('kit.subtitle') }}</p>
                    </div>
                </a>
            </div>
        </section>
    </div>
</template>

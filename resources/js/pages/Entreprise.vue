<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'
import Footer from '../components/ui/Footer.vue'
import LangSwitcher from '../components/ui/LangSwitcher.vue'
import { sendAnalytics, getDevice } from '../composables/useAnalytics.js'

const { t } = useI18n()

const hugLogoError = ref(false)

const props = defineProps({ entreprise: Object })

const c1 = computed(() => props.entreprise.primary_color   || '#E30613')
const c2 = computed(() => props.entreprise.secondary_color || c1.value)

// Retourne '#111111' ou '#ffffff' selon la luminance WCAG du fond
const textOn = (hex) => {
    if (!hex || !/^#[0-9A-Fa-f]{6}$/.test(hex)) return '#ffffff'
    const [r, g, b] = [hex.slice(1,3), hex.slice(3,5), hex.slice(5,7)].map(h => {
        const c = parseInt(h, 16) / 255
        return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4)
    })
    const L = 0.2126 * r + 0.7152 * g + 0.0722 * b
    return L > 0.179 ? '#111111' : '#ffffff'
}

const t1 = computed(() => textOn(c1.value))
const t2 = computed(() => textOn(c2.value))

const openFaq = ref(null)

const faqs = computed(() =>
    [0, 1, 2, 3, 4, 5, 6, 7, 8].map(i => ({
        short: t(`entreprise.faq_${i}_short`),
        q:     t(`entreprise.faq_${i}_q`),
        a:     t(`entreprise.faq_${i}_a`),
    }))
)

onMounted(() => {
    sendAnalytics('page_viewed', props.entreprise.id, null, {
        referrer: document.referrer || 'direct',
        device:   getDevice(),
    })
    if (props.entreprise.rdv_date) {
        updateCountdown()
        timer = setInterval(updateCountdown, 60000)
    }
})

onBeforeUnmount(() => { if (timer) clearInterval(timer) })

// --- Countdown ---
const countdown = ref({ days: 0, hours: 0, minutes: 0 })
let timer = null

const hasCountdown = computed(() => !!props.entreprise.rdv_date)

const pad = (n) => String(n).padStart(2, '0')

const updateCountdown = () => {
    const target = new Date(props.entreprise.rdv_date).getTime()
    const diff   = target - Date.now()
    if (diff <= 0) { countdown.value = { days: 0, hours: 0, minutes: 0 }; return }
    countdown.value = {
        days:    Math.floor(diff / 86400000),
        hours:   Math.floor((diff % 86400000) / 3600000),
        minutes: Math.floor((diff % 3600000) / 60000),
    }
}
</script>

<template>
    <div class="min-h-screen bg-white flex flex-col" :style="`--c1: ${c1}; --c2: ${c2}; --t1: ${t1}; --t2: ${t2}`">

        <!-- Header co-brandé -->
        <header class="border-b border-base-200 bg-white sticky top-0 z-50 px-6 py-3">
            <div class="max-w-5xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="/" class="flex items-center">
                        <img v-if="!hugLogoError" :src="'/images/hug-logo.svg'" alt="HUG" class="h-8 w-auto"
                            @error="hugLogoError = true">
                        <span v-else class="font-bold" style="color: var(--c1)">HUG</span>
                    </a>
                    <span class="text-base-content/30 text-lg font-light">×</span>
                    <div v-if="entreprise.logo_url"
                        class="bg-white border border-base-200 rounded-lg p-1 flex items-center justify-center h-9">
                        <img :src="entreprise.logo_url" :alt="entreprise.name" class="max-h-6 max-w-[80px] object-contain">
                    </div>
                    <span v-else class="font-semibold text-sm">{{ entreprise.name }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <a
                        :href="`/c/${entreprise.slug}/quiz`"
                        class="btn btn-sm btn-co border-none rounded-sm uppercase text-xs font-semibold tracking-wide"
                    >
                        {{ t('entreprise.quiz_cta') }}
                    </a>
                    <LangSwitcher />
                </div>
            </div>
        </header>

        <!-- Hero avec countdown -->
        <section class="py-16 px-6 bg-white">
            <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <p v-if="entreprise.is_active === false" class="text-error text-sm mb-4 font-medium">
                        {{ t('entreprise.inactive') }}
                    </p>
                    <h1 class="text-4xl md:text-5xl font-bold leading-tight mb-5 text-base-content">
                        {{ t('entreprise.hero_title_line1') }} <span style="color: var(--c1)">{{ t('entreprise.hero_title_highlight') }}</span><br>
                        {{ t('entreprise.hero_title_line2') }}
                    </h1>
                    <p class="text-base-content/60 mb-8 leading-relaxed">
                        {{ t('entreprise.hero_description', { company: entreprise.name }) }}
                    </p>
                    <a
                        :href="`/c/${entreprise.slug}/quiz`"
                        class="btn btn-co border-none font-semibold px-8 rounded-sm uppercase text-sm tracking-wide"
                    >
                        {{ t('entreprise.quiz_discover') }}
                    </a>
                </div>

                <!-- Countdown / date prochaine collecte -->
                <div v-if="hasCountdown" class="flex flex-col items-center gap-2">
                    <p class="text-sm text-base-content/50 mb-2">{{ t('entreprise.countdown_label') }}</p>
                    <div class="flex items-center gap-3">
                        <div class="text-center">
                            <div class="text-5xl font-bold tabular-nums rounded-xl px-4 py-3 min-w-[80px]" style="background-color: var(--c1); color: var(--t1)">
                                {{ pad(countdown.days) }}
                            </div>
                            <div class="text-xs text-base-content/50 mt-1">{{ t('entreprise.countdown_days') }}</div>
                        </div>
                        <span class="text-3xl font-bold text-base-content/30 mb-4">:</span>
                        <div class="text-center">
                            <div class="text-5xl font-bold tabular-nums rounded-xl px-4 py-3 min-w-[80px]" style="background-color: var(--c2); color: var(--t2)">
                                {{ pad(countdown.hours) }}
                            </div>
                            <div class="text-xs text-base-content/50 mt-1">{{ t('entreprise.countdown_hours') }}</div>
                        </div>
                        <span class="text-3xl font-bold text-base-content/30 mb-4">:</span>
                        <div class="text-center">
                            <div class="text-5xl font-bold tabular-nums rounded-xl px-4 py-3 min-w-[80px]" style="background-color: var(--c1); color: var(--t1)">
                                {{ pad(countdown.minutes) }}
                            </div>
                            <div class="text-xs text-base-content/50 mt-1">{{ t('entreprise.countdown_minutes') }}</div>
                        </div>
                    </div>
                    <p v-if="entreprise.rdv_date" class="text-sm text-base-content/40 mt-3">
                        {{ t('entreprise.collect_date') }} :
                        <strong>{{ new Date(entreprise.rdv_date).toLocaleDateString('fr-CH', { day: 'numeric', month: 'long', year: 'numeric' }) }}</strong>
                    </p>
                </div>
                <div v-else class="aspect-[4/3] bg-base-200 rounded-xl flex items-center justify-center text-base-content/20 text-sm italic">
                    Visuelle
                </div>
            </div>
        </section>

        <!-- C'est quoi le don du sang -->
        <section class="py-16 px-6 bg-base-100">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-2xl md:text-3xl font-bold mb-4">{{ t('entreprise.faq_section_title') }}</h2>
                <p class="text-base-content/60 mb-10 max-w-xl leading-relaxed">
                    {{ t('entreprise.faq_section_subtitle') }}
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="(faq, i) in faqs" :key="i"
                        class="faq-card bg-white border border-base-200 rounded-xl p-5 cursor-pointer select-none transition-shadow hover:shadow-md"
                        @click="openFaq = openFaq === i ? null : i">
                        <p class="text-xs font-semibold uppercase tracking-widest opacity-40 mb-1">{{ faq.short }}</p>
                        <p class="font-semibold mb-3 leading-tight">{{ faq.q }}</p>
                        <p v-show="openFaq === i" class="text-sm text-base-content/60 leading-relaxed">{{ faq.a }}</p>
                        <p v-show="openFaq !== i" class="text-xs" :style="`color: var(--c1)`">{{ t('entreprise.faq_read_more') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats de l'entreprise -->
        <section class="py-16 px-6" style="background-color: var(--c2); color: var(--t2)">
            <div class="max-w-5xl mx-auto">
                <p class="text-sm mb-6 uppercase tracking-widest font-semibold opacity-70">{{ t('entreprise.stats_label') }}</p>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
                    <div v-if="entreprise.eligible_count" class="text-center">
                        <div class="text-4xl font-bold">{{ entreprise.eligible_count.toLocaleString() }}</div>
                        <div class="text-sm mt-1 opacity-70">{{ t('home.stats_eligible') }}</div>
                    </div>
                    <div v-if="entreprise.submissions_count" class="text-center">
                        <div class="text-4xl font-bold">{{ entreprise.submissions_count.toLocaleString() }}</div>
                        <div class="text-sm mt-1 opacity-70">{{ t('entreprise.stats_quiz') }}</div>
                    </div>
                    <div v-if="entreprise.employee_count" class="text-center">
                        <div class="text-4xl font-bold">{{ entreprise.employee_count.toLocaleString() }}</div>
                        <div class="text-sm mt-1 opacity-70">{{ t('entreprise.employees') }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Countdown grand format (si date disponible) -->
        <section v-if="hasCountdown" class="py-16 px-6 bg-white text-center">
            <div class="max-w-2xl mx-auto">
                <div class="flex items-center justify-center gap-4">
                    <div class="text-center">
                        <div class="text-7xl font-bold tabular-nums rounded-2xl px-6 py-5 min-w-[110px]" style="background-color: var(--c1); color: var(--t1)">
                            {{ pad(countdown.days) }}
                        </div>
                        <div class="text-sm text-base-content/50 mt-2">{{ t('entreprise.countdown_days') }}</div>
                    </div>
                    <span class="text-4xl font-bold text-base-content/20 mb-6">:</span>
                    <div class="text-center">
                        <div class="text-7xl font-bold tabular-nums rounded-2xl px-6 py-5 min-w-[110px]" style="background-color: var(--c2); color: var(--t2)">
                            {{ pad(countdown.hours) }}
                        </div>
                        <div class="text-sm text-base-content/50 mt-2">{{ t('entreprise.countdown_hours') }}</div>
                    </div>
                    <span class="text-4xl font-bold text-base-content/20 mb-6">:</span>
                    <div class="text-center">
                        <div class="text-7xl font-bold tabular-nums rounded-2xl px-6 py-5 min-w-[110px]" style="background-color: var(--c1); color: var(--t1)">
                            {{ pad(countdown.minutes) }}
                        </div>
                        <div class="text-sm text-base-content/50 mt-2">{{ t('entreprise.countdown_minutes') }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA quiz -->
        <section class="py-16 px-6 bg-white border-t border-base-200">
            <div class="max-w-2xl mx-auto text-center">
                <h2 class="text-2xl md:text-3xl font-bold mb-6 leading-tight">
                    {{ t('entreprise.cta_section_title') }}
                </h2>
                <p class="text-base-content/60 leading-relaxed mb-1">{{ t('entreprise.cta_section_line1') }}</p>
                <p class="text-base-content/60 leading-relaxed mb-1">{{ t('entreprise.cta_section_line2') }}</p>
                <p class="text-base-content/60 leading-relaxed mb-8">{{ t('entreprise.cta_section_line3') }}</p>
                <p class="text-base-content/50 text-sm mb-8 max-w-md mx-auto leading-relaxed">
                    {{ t('entreprise.cta_section_description', { company: entreprise.name }) }}
                </p>
                <a
                    :href="`/c/${entreprise.slug}/quiz`"
                    class="btn btn-co border-none font-semibold px-10 rounded-sm uppercase text-sm tracking-wide"
                >
                    {{ t('entreprise.quiz_discover') }}
                </a>
            </div>
        </section>

        <Footer />
    </div>
</template>

<style scoped>
.btn-co {
    background-color: var(--c1);
    color: var(--t1);
}
.btn-co:hover {
    filter: brightness(0.88);
}
</style>

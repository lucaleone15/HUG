<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'
import Footer from '../components/ui/Footer.vue'
import LangSwitcher from '../components/ui/LangSwitcher.vue'
import CorkboardFaq from '../components/ui/CorkboardFaq.vue'
import { sendAnalytics, getDevice } from '../composables/useAnalytics.js'

const { t, locale } = useI18n()

const DATE_LOCALES = { fr: 'fr-CH', de: 'de-CH', it: 'it-CH', en: 'en-GB' }

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

function makeObserver(targetRef, visibleRef, threshold = 0.15) {
    const io = new IntersectionObserver(
        ([e]) => { if (e.isIntersecting) { visibleRef.value = true; io.disconnect() } },
        { threshold }
    )
    if (targetRef.value) io.observe(targetRef.value)
}

const faqRef    = ref(null); const faqVisible    = ref(false)
const statsRef  = ref(null); const statsVisible  = ref(false)
const ctaERef   = ref(null); const ctaEVisible   = ref(false)


onMounted(() => {
    sendAnalytics('page_viewed', props.entreprise.id, null, {
        referrer: document.referrer || 'direct',
        device:   getDevice(),
    })
    if (props.entreprise.rdv_date) {
        updateCountdown()
        timer = setInterval(updateCountdown, 60000)
    }
    makeObserver(faqRef,   faqVisible,   0.08)
    makeObserver(statsRef, statsVisible, 0.15)
    makeObserver(ctaERef,  ctaEVisible,  0.2)
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
    <div class="min-h-screen bg-white flex flex-col overflow-x-hidden" :style="`--c1: ${c1}; --c2: ${c2}; --t1: ${t1}; --t2: ${t2}`">

        <!-- Header co-brandé -->
        <header class="border-b border-base-200 bg-white sticky top-0 z-50 px-3 md:px-6 py-3">
            <div class="max-w-5xl mx-auto flex items-center justify-between gap-2">
                <!-- Logos -->
                <div class="flex items-center gap-2 min-w-0 overflow-hidden">
                    <a href="/" class="flex items-center shrink-0">
                        <img v-if="!hugLogoError" :src="'/images/hug-logo.svg'" alt="HUG" class="h-7 w-auto"
                            @error="hugLogoError = true">
                        <span v-else class="font-bold text-sm" style="color: var(--c1)">HUG</span>
                    </a>
                    <span class="text-base-content/30 font-light shrink-0">×</span>
                    <div v-if="entreprise.logo_url"
                        class="border border-base-200 rounded p-1 flex items-center justify-center shrink-0">
                        <img :src="entreprise.logo_url" :alt="entreprise.name" class="h-5 max-w-[52px] object-contain">
                    </div>
                    <span v-else class="font-semibold text-sm truncate min-w-0">{{ entreprise.name }}</span>
                </div>
                <!-- Actions -->
                <div class="flex items-center gap-2 shrink-0">
                    <a
                        :href="`/c/${entreprise.slug}/quiz`"
                        class="hidden sm:inline-flex btn btn-sm btn-co border-none rounded-sm uppercase text-xs font-semibold tracking-wide"
                    >
                        {{ t('entreprise.quiz_cta') }}
                    </a>
                    <LangSwitcher />
                </div>
            </div>
        </header>

        <!-- Bottom nav — mobile uniquement -->
        <nav class="fixed bottom-0 left-0 right-0 z-50 lg:hidden bg-white border-t border-base-200"
             style="padding-bottom: env(safe-area-inset-bottom);">
            <div class="flex items-center gap-3 px-4 py-2 min-h-[56px]">
                <!-- Retour accueil HUG -->
                <a href="/"
                   class="flex flex-col items-center justify-center gap-1 px-3 shrink-0 text-base-content/40 hover:text-base-content/70 transition-colors">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span class="text-[0.6rem] tracking-[0.04em] leading-none">HUG</span>
                </a>
                <!-- CTA quiz — occupe tout l'espace restant -->
                <a :href="`/c/${entreprise.slug}/quiz`"
                   class="flex-1 btn border-none rounded-sm uppercase text-xs font-semibold tracking-wide"
                   :style="`background-color: var(--c1); color: var(--t1);`">
                    {{ t('entreprise.quiz_cta') }}
                </a>
            </div>
        </nav>

        <!-- Hero avec countdown -->
        <section class="py-10 md:py-16 px-4 md:px-6 bg-white">
            <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-8 md:gap-12 items-center">
                <div class="page-hero-text">
                    <p v-if="entreprise.is_active === false" class="text-error text-sm mb-4 font-medium">
                        {{ t('entreprise.inactive') }}
                    </p>
                    <h1 class="font-extrabold leading-tight mb-5 text-base-content"
                        style="font-size: clamp(1.6rem, 5vw, 3.5rem); overflow-wrap: break-word;">
                        {{ t('entreprise.hero_title_line1') }} <span style="color: var(--c1)">{{ t('entreprise.hero_title_highlight') }}</span><br>
                        {{ t('entreprise.hero_title_line2') }}
                    </h1>
                    <p class="text-base-content/60 mb-8 leading-relaxed">
                        {{ t('entreprise.hero_description', { company: entreprise.name }) }}
                    </p>
                    <a
                        :href="`/c/${entreprise.slug}/quiz`"
                        class="btn btn-co border-none font-semibold px-6 md:px-8 rounded-sm uppercase text-sm tracking-wide w-full sm:w-auto"
                    >
                        {{ t('entreprise.quiz_discover') }}
                    </a>
                </div>

                <!-- Countdown / date prochaine collecte -->
                <div v-if="hasCountdown" class="flex flex-col items-center gap-2 page-hero-visual w-full">
                    <p class="text-sm text-base-content/50 mb-2">{{ t('entreprise.countdown_label') }}</p>
                    <div class="flex items-center gap-2 w-full">
                        <div class="text-center flex-1 min-w-0">
                            <div class="text-3xl md:text-5xl font-bold tabular-nums rounded-xl py-2 md:py-3" style="background-color: var(--c1); color: var(--t1)">
                                {{ pad(countdown.days) }}
                            </div>
                            <div class="text-xs text-base-content/50 mt-1">{{ t('entreprise.countdown_days') }}</div>
                        </div>
                        <span class="text-xl font-bold text-base-content/30 mb-4 shrink-0">:</span>
                        <div class="text-center flex-1 min-w-0">
                            <div class="text-3xl md:text-5xl font-bold tabular-nums rounded-xl py-2 md:py-3" style="background-color: var(--c2); color: var(--t2)">
                                {{ pad(countdown.hours) }}
                            </div>
                            <div class="text-xs text-base-content/50 mt-1">{{ t('entreprise.countdown_hours') }}</div>
                        </div>
                        <span class="text-xl font-bold text-base-content/30 mb-4 shrink-0">:</span>
                        <div class="text-center flex-1 min-w-0">
                            <div class="text-3xl md:text-5xl font-bold tabular-nums rounded-xl py-2 md:py-3" style="background-color: var(--c1); color: var(--t1)">
                                {{ pad(countdown.minutes) }}
                            </div>
                            <div class="text-xs text-base-content/50 mt-1">{{ t('entreprise.countdown_minutes') }}</div>
                        </div>
                    </div>
                    <p v-if="entreprise.rdv_date" class="text-sm text-base-content/40 mt-3 text-center">
                        {{ t('entreprise.collect_date') }} :
                        <strong>{{ new Date(entreprise.rdv_date).toLocaleDateString(DATE_LOCALES[locale] ?? 'fr-CH', { day: 'numeric', month: 'long', year: 'numeric' }) }}</strong>
                    </p>
                </div>
                <div v-else class="aspect-[4/3] rounded-xl overflow-hidden flex flex-col items-start justify-end p-8 relative page-hero-visual"
                     :style="`background-color: var(--c1); color: var(--t1)`">
                    <svg class="absolute inset-0 w-full h-full opacity-10" viewBox="0 0 400 300" fill="none" aria-hidden="true">
                        <circle cx="360" cy="-20" r="220" stroke="currentColor" stroke-width="1.5"/>
                        <circle cx="360" cy="-20" r="160" stroke="currentColor" stroke-width="1"/>
                        <circle cx="360" cy="-20" r="100" stroke="currentColor" stroke-width="0.5"/>
                    </svg>
                    <div class="relative z-10">
                        <div class="text-[0.65rem] uppercase tracking-[0.25em] opacity-50 mb-2">{{ entreprise.name }}</div>
                        <div class="font-extrabold leading-tight opacity-90"
                             style="font-size: clamp(1.5rem, 3.5vw, 2.25rem);">
                            {{ t('entreprise.quiz_cta') }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- C'est quoi le don du sang — Corkboard interactif -->
        <section class="py-8 md:py-10 px-2 md:px-6 bg-base-100" ref="faqRef">
            <div class="max-w-5xl mx-auto reveal-up" :class="{ 'reveal-up--visible': faqVisible }">
                <CorkboardFaq :primary-color="c1" :secondary-color="c2" />
            </div>
        </section>

        <!-- Stats de l'entreprise -->
        <section class="py-10 md:py-16 px-4 md:px-6" style="background-color: var(--c2); color: var(--t2)" ref="statsRef">
            <div class="max-w-5xl mx-auto">
                <p class="text-xs mb-8 uppercase tracking-[0.2em] font-semibold opacity-50 reveal-up"
                   :class="{ 'reveal-up--visible': statsVisible }">{{ t('entreprise.stats_label') }}</p>
                <div class="grid grid-cols-2 md:flex md:flex-wrap gap-6 md:gap-x-16">
                    <div v-if="entreprise.eligible_count"
                         class="reveal-up" :class="{ 'reveal-up--visible': statsVisible }"
                         style="transition-delay: 80ms;">
                        <div class="font-bold leading-none mb-1"
                             style="font-size: clamp(1.75rem, 6vw, 3rem);">
                            {{ entreprise.eligible_count.toLocaleString() }}
                        </div>
                        <div class="text-xs opacity-60 uppercase tracking-[0.12em]">{{ t('home.stats_eligible') }}</div>
                    </div>
                    <div v-if="entreprise.submissions_count"
                         class="reveal-up" :class="{ 'reveal-up--visible': statsVisible }"
                         style="transition-delay: 160ms;">
                        <div class="font-bold leading-none mb-1"
                             style="font-size: clamp(1.75rem, 6vw, 3rem);">
                            {{ entreprise.submissions_count.toLocaleString() }}
                        </div>
                        <div class="text-xs opacity-60 uppercase tracking-[0.12em]">{{ t('entreprise.stats_quiz') }}</div>
                    </div>
                    <div v-if="entreprise.employee_count"
                         class="reveal-up" :class="{ 'reveal-up--visible': statsVisible }"
                         style="transition-delay: 240ms;">
                        <div class="font-bold leading-none mb-1"
                             style="font-size: clamp(1.75rem, 6vw, 3rem);">
                            {{ entreprise.employee_count.toLocaleString() }}
                        </div>
                        <div class="text-xs opacity-60 uppercase tracking-[0.12em]">{{ t('entreprise.employees') }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Countdown grand format (si date disponible) -->
        <section v-if="hasCountdown" class="py-10 md:py-16 px-4 md:px-6 bg-white text-center">
            <div class="flex items-center gap-2 md:gap-4 max-w-md md:max-w-2xl mx-auto">
                <div class="text-center flex-1 min-w-0">
                    <div class="text-4xl md:text-7xl font-bold tabular-nums rounded-xl md:rounded-2xl py-3 md:py-5" style="background-color: var(--c1); color: var(--t1)">
                        {{ pad(countdown.days) }}
                    </div>
                    <div class="text-xs md:text-sm text-base-content/50 mt-1 md:mt-2">{{ t('entreprise.countdown_days') }}</div>
                </div>
                <span class="text-xl md:text-4xl font-bold text-base-content/20 mb-4 md:mb-6 shrink-0">:</span>
                <div class="text-center flex-1 min-w-0">
                    <div class="text-4xl md:text-7xl font-bold tabular-nums rounded-xl md:rounded-2xl py-3 md:py-5" style="background-color: var(--c2); color: var(--t2)">
                        {{ pad(countdown.hours) }}
                    </div>
                    <div class="text-xs md:text-sm text-base-content/50 mt-1 md:mt-2">{{ t('entreprise.countdown_hours') }}</div>
                </div>
                <span class="text-xl md:text-4xl font-bold text-base-content/20 mb-4 md:mb-6 shrink-0">:</span>
                <div class="text-center flex-1 min-w-0">
                    <div class="text-4xl md:text-7xl font-bold tabular-nums rounded-xl md:rounded-2xl py-3 md:py-5" style="background-color: var(--c1); color: var(--t1)">
                        {{ pad(countdown.minutes) }}
                    </div>
                    <div class="text-xs md:text-sm text-base-content/50 mt-1 md:mt-2">{{ t('entreprise.countdown_minutes') }}</div>
                </div>
            </div>
        </section>

        <!-- CTA quiz -->
        <section class="py-10 md:py-16 px-4 md:px-6 bg-white border-t border-base-200" ref="ctaERef">
            <div class="max-w-5xl mx-auto grid md:grid-cols-[1.2fr_0.8fr] gap-8 md:gap-16 items-end">

                <!-- Gauche : titre + faits -->
                <div>
                    <h2 class="font-bold mb-5 md:mb-8 leading-tight reveal-up"
                        :class="{ 'reveal-up--visible': ctaEVisible }"
                        style="font-size: clamp(1.4rem, 3vw, 2.25rem); overflow-wrap: break-word;">
                        {{ t('entreprise.cta_section_title') }}
                    </h2>
                    <div class="space-y-3 border-t border-base-200 pt-5 md:pt-8 reveal-up"
                         :class="{ 'reveal-up--visible': ctaEVisible }"
                         style="transition-delay: 80ms;">
                        <p class="font-semibold leading-snug"
                           style="font-size: clamp(1rem, 2vw, 1.15rem);">
                            {{ t('entreprise.cta_section_line1') }}
                        </p>
                        <p class="font-semibold leading-snug"
                           style="font-size: clamp(1rem, 2vw, 1.15rem);">
                            {{ t('entreprise.cta_section_line2') }}
                        </p>
                        <p class="font-semibold leading-snug"
                           style="color: var(--c1); font-size: clamp(1rem, 2vw, 1.15rem);">
                            {{ t('entreprise.cta_section_line3') }}
                        </p>
                    </div>
                </div>

                <!-- Droite : description + CTA -->
                <div class="reveal-up" :class="{ 'reveal-up--visible': ctaEVisible }"
                     style="transition-delay: 160ms;">
                    <p class="text-base-content/55 text-sm mb-8 leading-relaxed" style="max-width: 42ch;">
                        {{ t('entreprise.cta_section_description', { company: entreprise.name }) }}
                    </p>
                    <a
                        :href="`/c/${entreprise.slug}/quiz`"
                        class="btn btn-co border-none font-semibold px-6 md:px-10 rounded-sm uppercase text-sm tracking-wide active:scale-[0.97] w-full md:w-auto"
                    >
                        {{ t('entreprise.quiz_discover') }}
                    </a>
                </div>

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

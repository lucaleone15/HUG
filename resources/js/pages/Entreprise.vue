<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'
import Footer from '../components/ui/Footer.vue'
import LangSwitcher from '../components/ui/LangSwitcher.vue'
import CorkboardFaq from '../components/ui/CorkboardFaq.vue'
import { sendAnalytics, getDevice } from '../composables/useAnalytics.js'
import LogoContainer from '../components/ui/LogoContainer.vue'
import { useLogoBg } from '../composables/useLogoBg.js'

const { t, locale } = useI18n()

const DATE_LOCALES = { fr: 'fr-CH', de: 'de-CH', it: 'it-CH', en: 'en-GB' }

const hugLogoError = ref(false)

const props = defineProps({
    entreprise: Object,
    collectes:  { type: Array, default: () => [] },
})

const c1 = computed(() => props.entreprise.primary_color   || '#E30613')
const c2 = computed(() => props.entreprise.secondary_color || c1.value)

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


const { bg: headerLogoBg } = useLogoBg(
    () => props.entreprise.logo_url,
    () => props.entreprise.primary_color,
)

// --- Collectes ---
const upcomingCollectes = computed(() => props.collectes.filter(c => c.is_active))
const pastCollectes     = computed(() => props.collectes.filter(c => !c.is_active))
const nextCollecte      = computed(() => upcomingCollectes.value[0] ?? null)

// --- Countdown (basé sur la prochaine collecte active) ---
const countdown = ref({ days: 0, hours: 0, minutes: 0 })
let timer = null
const hasCountdown = computed(() => !!nextCollecte.value?.rdv_date)
const pad = (n) => String(n).padStart(2, '0')

const updateCountdown = () => {
    const target = new Date(nextCollecte.value.rdv_date).getTime()
    const diff   = target - Date.now()
    if (diff <= 0) { countdown.value = { days: 0, hours: 0, minutes: 0 }; return }
    countdown.value = {
        days:    Math.floor(diff / 86400000),
        hours:   Math.floor((diff % 86400000) / 3600000),
        minutes: Math.floor((diff % 3600000) / 60000),
    }
}

// --- Helpers date ---
const monthShort = (dateStr) => {
    if (!dateStr) return ''
    return new Date(dateStr).toLocaleDateString(DATE_LOCALES[locale.value] ?? 'fr-CH', { month: 'short' })
}
const dayNum = (dateStr) => {
    if (!dateStr) return ''
    return new Date(dateStr).getDate()
}
const fullDate = (dateStr) => {
    if (!dateStr) return ''
    return new Date(dateStr).toLocaleDateString(DATE_LOCALES[locale.value] ?? 'fr-CH', { day: 'numeric', month: 'long', year: 'numeric' })
}

// --- Intersection observers ---
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
    if (hasCountdown.value) {
        updateCountdown()
        timer = setInterval(updateCountdown, 60000)
    }
    makeObserver(faqRef,   faqVisible,   0.08)
    makeObserver(statsRef, statsVisible, 0.15)
    makeObserver(ctaERef,  ctaEVisible,  0.2)
})

onBeforeUnmount(() => { if (timer) clearInterval(timer) })
</script>

<template>
    <div class="min-h-screen bg-base-100 flex flex-col overflow-x-hidden" :style="`--c1: ${c1}; --c2: ${c2}; --t1: ${t1}; --t2: ${t2}`">

        <!-- Header co-brandé -->
        <header class="bg-white sticky top-0 z-50 px-3 md:px-6 py-3" style="border-bottom: 3px solid var(--c1)">
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
                         class="rounded p-1 shrink-0 flex items-center justify-center"
                         style="height: 36px; width: auto; min-width: 36px;"
                         :style="`background-color: ${headerLogoBg}`">
                        <img :src="entreprise.logo_url" :alt="entreprise.name"
                             class="h-7 max-w-[56px] object-contain">
                    </div>
                    <span v-else class="font-semibold text-sm truncate min-w-0">{{ entreprise.name }}</span>
                </div>
                <!-- Actions -->
                <div class="flex items-center gap-2 shrink-0">
                    <a
                        :href="`/c/${entreprise.access_token}/quiz`"
                        class="hidden sm:inline-flex btn btn-sm btn-co border-none rounded-sm uppercase text-xs font-semibold tracking-wide"
                    >
                        {{ t('entreprise.quiz_cta') }}
                    </a>
                    <LangSwitcher />
                </div>
            </div>
        </header>

        <!-- Bottom nav — mobile uniquement -->
        <nav class="fixed bottom-0 left-0 right-0 z-50 lg:hidden"
             style="background-color: var(--c1); padding-bottom: env(safe-area-inset-bottom);">
            <div class="flex items-center gap-3 px-4 py-2 min-h-[56px]">
                <!-- Retour accueil HUG -->
                <a href="/"
                   class="flex flex-col items-center justify-center gap-1 px-3 shrink-0 transition-colors"
                   style="color: var(--t1); opacity: 0.80;">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span class="text-[0.6rem] tracking-[0.04em] leading-none">HUG</span>
                </a>
                <!-- CTA quiz — occupe tout l'espace restant -->
                <a :href="`/c/${entreprise.access_token}/quiz`"
                   class="flex-1 btn border-none rounded-sm uppercase text-xs font-semibold tracking-wide"
                   style="background-color: var(--t1); color: var(--c1);">
                    {{ t('entreprise.quiz_cta') }}
                </a>
            </div>
        </nav>

        <!-- Hero avec countdown -->
        <section class="py-12 md:py-20 px-4 md:px-6" style="background-color: var(--c1)">
            <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-8 md:gap-12 items-stretch mb-8 md:mb-12">
                <div class="page-hero-text">
                    <h1 class="font-extrabold leading-tight mb-5"
                        style="color: var(--t1); font-size: clamp(1.6rem, 5vw, 3.5rem); overflow-wrap: break-word;">
                        {{ t('entreprise.hero_title_line1') }}
                        <span class="inline-block px-1 -mx-1 rounded-md" style="background-color: var(--t1); color: var(--c1);">{{ t('entreprise.hero_title_highlight') }}</span><br>
                        {{ t('entreprise.hero_title_line2') }}
                    </h1>
                    <p class="mb-8 leading-relaxed" style="color: var(--t1); opacity: 0.85;">
                        {{ t('entreprise.hero_description', { company: entreprise.name }) }}
                    </p>
                    <a :href="`/c/${entreprise.access_token}/quiz`"
                       class="btn border-none font-semibold px-6 md:px-8 rounded-sm uppercase text-sm tracking-wide w-full sm:w-auto"
                       style="background-color: var(--t1); color: var(--c1);">
                        {{ t('entreprise.quiz_discover') }}
                    </a>
                </div>

                <!-- Countdown -->
                <div v-if="hasCountdown" class="flex flex-col justify-center gap-5 page-hero-visual w-full">
                    <p class="text-sm font-semibold"
                       style="color: var(--t1); opacity: 0.72;">{{ t('entreprise.countdown_label') }}</p>

                    <div class="flex items-end gap-3 md:gap-5">
                        <div class="flex-1 text-center">
                            <div class="font-extrabold tabular-nums leading-none"
                                 style="color: var(--t1); font-size: clamp(2.5rem, 7vw, 4.5rem);">{{ pad(countdown.days) }}</div>
                            <div class="text-[0.65rem] uppercase tracking-[0.14em] mt-2 font-semibold"
                                 style="color: var(--t1); opacity: 0.82;">{{ t('entreprise.countdown_days') }}</div>
                        </div>
                        <span class="pb-6 font-bold shrink-0 text-2xl"
                              style="color: var(--t1); opacity: 0.25;">:</span>
                        <div class="flex-1 text-center">
                            <div class="font-extrabold tabular-nums leading-none"
                                 style="color: var(--t1); font-size: clamp(2.5rem, 7vw, 4.5rem);">{{ pad(countdown.hours) }}</div>
                            <div class="text-[0.65rem] uppercase tracking-[0.14em] mt-2 font-semibold"
                                 style="color: var(--t1); opacity: 0.82;">{{ t('entreprise.countdown_hours') }}</div>
                        </div>
                        <span class="pb-6 font-bold shrink-0 text-2xl"
                              style="color: var(--t1); opacity: 0.25;">:</span>
                        <div class="flex-1 text-center">
                            <div class="font-extrabold tabular-nums leading-none"
                                 style="color: var(--t1); font-size: clamp(2.5rem, 7vw, 4.5rem);">{{ pad(countdown.minutes) }}</div>
                            <div class="text-[0.65rem] uppercase tracking-[0.14em] mt-2 font-semibold"
                                 style="color: var(--t1); opacity: 0.82;">{{ t('entreprise.countdown_minutes') }}</div>
                        </div>
                    </div>

                    <p v-if="nextCollecte?.rdv_date" class="text-sm pt-4"
                       :style="`color: var(--t1); opacity: 0.82; border-top: 1px solid color-mix(in srgb, var(--t1) 20%, transparent);`">
                        {{ t('entreprise.collect_date') }} :
                        <strong>{{ fullDate(nextCollecte.rdv_date) }}</strong>
                    </p>
                </div>
                <div v-else class="h-full min-h-[280px] rounded-xl overflow-hidden flex flex-col items-start justify-end p-8 relative page-hero-visual"
                     style="background-image: url('https://images.unsplash.com/photo-1615461066159-fea0960485d5?auto=format&fit=crop&w=900&q=80'); background-size: cover; background-position: center;">
                    <div class="absolute inset-0" style="background-color: rgba(0,0,0,0.5);"></div>
                    <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(0,0,0,0.5) 0%, transparent 55%);"></div>
                    <div class="relative z-10 text-white">
                        <div class="font-extrabold leading-tight uppercase tracking-wide" style="font-size: clamp(1.25rem, 3vw, 2rem);">{{ entreprise.name }}</div>
                    </div>
                </div>
            </div>

            <div class="max-w-5xl mx-auto">

                <!-- Collectes à venir -->
                <template v-if="upcomingCollectes.length">
                    <div class="flex flex-col gap-3">
                        <a v-for="c in upcomingCollectes" :key="c.id"
                           :href="`/c/${entreprise.access_token}/quiz`"
                           class="flex items-center gap-4 rounded-xl px-4 py-4 hover:opacity-90 transition-opacity group"
                           style="background-color: var(--t1);">

                            <!-- Badge date -->
                            <div class="shrink-0 rounded-lg w-14 text-center py-2"
                                 style="background-color: var(--c1); color: var(--t1);">
                                <div class="text-2xl font-extrabold leading-none tabular-nums">{{ c.rdv_date ? dayNum(c.rdv_date) : '—' }}</div>
                                <div class="text-[0.6rem] uppercase tracking-wider opacity-90 mt-0.5">{{ c.rdv_date ? monthShort(c.rdv_date) : '' }}</div>
                            </div>

                            <!-- Infos -->
                            <div class="flex-1 min-w-0">
                                <div class="font-bold leading-snug truncate" style="color: var(--c1);">
                                    {{ c.label || t('entreprise.collecte_default_label') }}
                                </div>
                                <div v-if="c.rdv_date" class="text-xs mt-0.5" style="color: var(--c1); opacity: 0.68;">
                                    {{ fullDate(c.rdv_date) }}
                                </div>
                            </div>

                            <!-- CTA -->
                            <div class="shrink-0">
                                <span class="btn btn-co btn-sm border-none rounded-sm uppercase text-xs font-semibold tracking-wide">
                                    {{ t('entreprise.collecte_rdv_cta_short') }}
                                </span>
                            </div>
                        </a>
                    </div>
                </template>

                <!-- Aucune collecte active -->
                <template v-else>
                    <div class="py-4">
                        <p class="text-sm font-semibold mb-3"
                           style="color: var(--t1); opacity: 0.72;">
                            {{ t('entreprise.collecte_none_label') }}
                        </p>
                        <p class="font-bold mb-2" style="color: var(--t1); font-size: clamp(1rem, 2vw, 1.25rem);">
                            {{ t('entreprise.collecte_none_title') }}
                        </p>
                        <p class="text-sm" style="color: var(--t1); opacity: 0.82; max-width: 42ch;">
                            {{ t('entreprise.collecte_none_description', { company: entreprise.name }) }}
                        </p>
                    </div>
                </template>

            </div>
        </section>

        <!-- C'est quoi le don du sang — Corkboard interactif -->
        <section class="py-8 md:py-10 px-2 md:px-6 bg-base-100" ref="faqRef">
            <div class="max-w-5xl mx-auto reveal-up" :class="{ 'reveal-up--visible': faqVisible }">
                <CorkboardFaq :primary-color="c1" :secondary-color="c2" />
            </div>
        </section>

        <!-- Stats de l'entreprise -->
        <section class="py-10 md:py-16 px-4 md:px-6" style="background-color: var(--c2); color: var(--t2);" ref="statsRef">
            <div class="max-w-5xl mx-auto">
                <p class="text-xs mb-8 uppercase tracking-[0.2em] font-semibold opacity-75 reveal-up"
                   :class="{ 'reveal-up--visible': statsVisible }">{{ t('entreprise.stats_label') }}</p>
                <div class="grid grid-cols-2 md:flex md:flex-wrap gap-6 md:gap-x-16">
                    <div v-if="entreprise.eligible_count"
                         class="reveal-up" :class="{ 'reveal-up--visible': statsVisible }"
                         style="transition-delay: 80ms;">
                        <div class="font-bold leading-none mb-1" style="font-size: clamp(1.75rem, 6vw, 3rem);">
                            {{ entreprise.eligible_count.toLocaleString() }}
                        </div>
                        <div class="text-xs opacity-80 uppercase tracking-[0.12em]">{{ t('home.stats_eligible') }}</div>
                    </div>
                    <div v-if="entreprise.submissions_count"
                         class="reveal-up" :class="{ 'reveal-up--visible': statsVisible }"
                         style="transition-delay: 160ms;">
                        <div class="font-bold leading-none mb-1" style="font-size: clamp(1.75rem, 6vw, 3rem);">
                            {{ entreprise.submissions_count.toLocaleString() }}
                        </div>
                        <div class="text-xs opacity-80 uppercase tracking-[0.12em]">{{ t('entreprise.stats_quiz') }}</div>
                    </div>
                    <div v-if="entreprise.employee_count"
                         class="reveal-up" :class="{ 'reveal-up--visible': statsVisible }"
                         style="transition-delay: 240ms;">
                        <div class="font-bold leading-none mb-1" style="font-size: clamp(1.75rem, 6vw, 3rem);">
                            {{ entreprise.employee_count.toLocaleString() }}
                        </div>
                        <div class="text-xs opacity-80 uppercase tracking-[0.12em]">{{ t('entreprise.employees') }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA quiz -->
        <section class="py-10 md:py-16 px-4 md:px-6" style="background-color: var(--c1);" ref="ctaERef">
            <div class="max-w-5xl mx-auto">
                <h2 class="font-bold mb-6 md:mb-10 leading-tight reveal-up"
                    :class="{ 'reveal-up--visible': ctaEVisible }"
                    style="color: var(--t1); font-size: clamp(1.4rem, 3vw, 2.25rem); overflow-wrap: break-word;">
                    {{ t('entreprise.cta_section_title') }}
                </h2>
                <div class="grid md:grid-cols-[1.2fr_0.8fr] gap-8 md:gap-16 items-start"
                     :style="`border-top: 1px solid color-mix(in srgb, var(--t1) 22%, transparent);`">
                    <div class="pt-6 md:pt-8 space-y-3 reveal-up"
                         :class="{ 'reveal-up--visible': ctaEVisible }"
                         style="transition-delay: 80ms;">
                        <p class="font-semibold leading-snug" style="color: var(--t1); font-size: clamp(1rem, 2vw, 1.15rem);">{{ t('entreprise.cta_section_line1') }}</p>
                        <p class="font-semibold leading-snug" style="color: var(--t1); font-size: clamp(1rem, 2vw, 1.15rem);">{{ t('entreprise.cta_section_line2') }}</p>
                        <p class="font-semibold leading-snug" style="color: var(--t1); opacity: 0.82; font-size: clamp(1rem, 2vw, 1.15rem);">{{ t('entreprise.cta_section_line3') }}</p>
                    </div>
                    <div class="pt-6 md:pt-8 reveal-up" :class="{ 'reveal-up--visible': ctaEVisible }" style="transition-delay: 160ms;">
                        <p class="text-sm mb-8 leading-relaxed" style="color: var(--t1); opacity: 0.82; max-width: 42ch;">
                            {{ t('entreprise.cta_section_description', { company: entreprise.name }) }}
                        </p>
                        <a :href="`/c/${entreprise.access_token}/quiz`"
                           class="btn border-none font-semibold px-6 md:px-10 rounded-sm uppercase text-sm tracking-wide active:scale-[0.97] w-full md:w-auto"
                           style="background-color: var(--t1); color: var(--c1);">
                            {{ t('entreprise.quiz_discover') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Collectes précédentes -->
        <section v-if="pastCollectes.length" class="py-10 md:py-14 px-4 md:px-6 bg-base-100 border-t border-base-200">
            <div class="max-w-5xl mx-auto">
                <h2 class="font-bold mb-6" style="font-size: clamp(1.2rem, 2.5vw, 1.75rem);">
                    <span style="color: var(--c1)">{{ t('entreprise.collectes_past_highlight') }}</span>
                    {{ t('entreprise.collectes_past_title') }}
                </h2>
                <div class="flex flex-col gap-3">
                    <div v-for="c in pastCollectes" :key="c.id"
                         class="flex items-center gap-4 rounded-xl border border-base-200 bg-base-100 px-4 py-4 opacity-60">

                        <!-- Badge date grisé -->
                        <div class="shrink-0 rounded-lg w-14 text-center py-2 bg-base-300 text-base-content/50">
                            <div class="text-2xl font-extrabold leading-none tabular-nums">{{ c.rdv_date ? dayNum(c.rdv_date) : '—' }}</div>
                            <div class="text-[0.6rem] uppercase tracking-wider mt-0.5">{{ c.rdv_date ? monthShort(c.rdv_date) : '' }}</div>
                        </div>

                        <!-- Infos -->
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-base-content/70 leading-snug truncate">
                                {{ c.label || t('entreprise.collecte_default_label') }}
                            </div>
                            <div v-if="c.rdv_date" class="text-xs text-base-content/40 mt-0.5">
                                {{ fullDate(c.rdv_date) }}
                            </div>
                        </div>

                        <!-- Badge terminée -->
                        <span class="shrink-0 badge badge-ghost badge-sm uppercase tracking-wide font-semibold text-base-content/50">
                            {{ t('entreprise.collecte_past_badge') }}
                        </span>
                    </div>
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

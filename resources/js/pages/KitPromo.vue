<script setup>
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import NavBar from '../components/ui/NavBar.vue'
import Footer from '../components/ui/Footer.vue'
import PageHero from '../components/ui/PageHero.vue'
import KitResourceCard from '../components/kit/KitResourceCard.vue'

const { t } = useI18n()

function makeObserver(targetRef, visibleRef, threshold = 0.1) {
    const io = new IntersectionObserver(
        ([e]) => { if (e.isIntersecting) { visibleRef.value = true; io.disconnect() } },
        { threshold }
    )
    if (targetRef.value) io.observe(targetRef.value)
}

const listRef = ref(null); const listVisible = ref(false)
const dlRef   = ref(null); const dlVisible   = ref(false)
const ctaRef  = ref(null); const ctaVisible  = ref(false)

onMounted(() => {
    makeObserver(listRef, listVisible, 0.1)
    makeObserver(dlRef,   dlVisible,   0.08)
    makeObserver(ctaRef,  ctaVisible,  0.2)
})

// Bento grid — 4 variantes, tailles mélangées
// Layout sm+ :
//   [affiches — landscape 2col×1row] [flyers — tall 1col×2row]
//   [rollup — std]  [banners — std]  [↑ suite flyers          ]
//   [email — landscape 2col×1row  ]  [tv — std                ]
//   [social ——————— full 3col ——————————————————————————————  ]
const resources = [
    { key: 'affiches', file: '/downloads/kit/affiches.zip',           n: 1, variant: 'landscape', span: 'sm:col-span-2' },
    { key: 'flyers',   file: '/downloads/kit/flyers.zip',             n: 2, variant: 'featured',  span: 'sm:row-span-2' },
    { key: 'rollup',   file: '/downloads/kit/rollup.pdf',             n: 3, variant: 'standard',  span: '' },
    { key: 'banners',  file: '/downloads/kit/bannieres-intranet.zip', n: 4, variant: 'standard',  span: '' },
    { key: 'email',    file: '/downloads/kit/email-invitation.zip',   n: 5, variant: 'landscape', span: 'sm:col-span-2' },
    { key: 'tv',       file: '/downloads/kit/infographie-tv.zip',     n: 6, variant: 'standard',  span: '' },
    { key: 'social',   file: '/downloads/kit/reseaux-sociaux.zip',    n: 7, variant: 'wide',      span: 'sm:col-span-3' },
]

// Flat list for the red summary section
const allResources = resources
</script>

<template>
    <div class="min-h-screen bg-base-100 flex flex-col">
        <NavBar />

        <PageHero
            title-html="Kit de<br>communication"
            :subtitle="t('kit.subtitle')"
            :cta="{ label: t('kit.hero_cta'), href: '#telechargements' }"
        >
            <template #visual>
                <div class="w-full max-w-[220px] mx-auto py-4" aria-hidden="true">
                    <svg viewBox="0 0 220 220" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
                        <rect x="44" y="62" width="108" height="128" rx="6" fill="#f0f0f0" stroke="#e6e6e6" stroke-width="1.5"/>
                        <rect x="30" y="48" width="108" height="128" rx="6" fill="#f6f6f6" stroke="#e2e2e2" stroke-width="1.5"/>
                        <rect x="58" y="34" width="108" height="128" rx="6" fill="white" stroke="#d9d9d9" stroke-width="1.5"/>
                        <path d="M136 34 L166 64 L136 64 Z" fill="#ebebeb"/>
                        <path d="M136 34 L166 64" stroke="#d9d9d9" stroke-width="1.5"/>
                        <line x1="74" y1="78" x2="150" y2="78" stroke="#e8e8e8" stroke-width="2" stroke-linecap="round"/>
                        <line x1="74" y1="90" x2="138" y2="90" stroke="#e8e8e8" stroke-width="2" stroke-linecap="round"/>
                        <line x1="74" y1="106" x2="150" y2="106" stroke="#ededed" stroke-width="1.5" stroke-linecap="round"/>
                        <line x1="74" y1="116" x2="128" y2="116" stroke="#ededed" stroke-width="1.5" stroke-linecap="round"/>
                        <line x1="74" y1="126" x2="142" y2="126" stroke="#ededed" stroke-width="1.5" stroke-linecap="round"/>
                        <line x1="74" y1="136" x2="116" y2="136" stroke="#ededed" stroke-width="1.5" stroke-linecap="round"/>
                        <circle cx="164" cy="142" r="26" fill="#D32C37"/>
                        <path d="M157 139 L164 148 L171 139" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="164" y1="132" x2="164" y2="148" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
                        <line x1="155" y1="153" x2="173" y2="153" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
                    </svg>
                </div>
            </template>
        </PageHero>

        <!-- ── Contenu du kit — fond rouge ─────────────────────────────── -->
        <section class="py-10 px-6 bg-brand text-white" ref="listRef">
            <div class="max-w-5xl mx-auto">
                <div class="flex flex-col sm:flex-row sm:items-baseline gap-2 sm:gap-6 mb-6 reveal-up"
                     :class="{ 'reveal-up--visible': listVisible }">
                    <h2 class="font-bold leading-tight"
                        style="font-size: clamp(1.25rem, 2.5vw, 1.75rem);">
                        {{ t('kit.contents_section_title') }}
                    </h2>
                    <p class="text-white/70 text-sm" style="transition-delay: 60ms;">
                        {{ t('kit.contents_section_desc') }}
                    </p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-x-8 gap-y-4">
                    <div v-for="(r, i) in allResources" :key="r.key"
                         class="flex items-center gap-3 reveal-up"
                         :class="{ 'reveal-up--visible': listVisible }"
                         :style="`transition-delay: ${(i + 1) * 60}ms`">
                        <span class="kit-num shrink-0" aria-hidden="true">{{ String(r.n).padStart(2, '0') }}</span>
                        <div>
                            <p class="font-semibold text-sm leading-snug">
                                {{ t(`kit.resource_${r.key}_title`) }}
                            </p>
                            <p class="text-xs text-white/55 mt-0.5">
                                {{ t(`kit.resource_${r.key}_format`) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Téléchargements — fond gris ──────────────────────────────── -->
        <section id="telechargements" class="py-16 px-6 bg-base-200" ref="dlRef">
            <div class="max-w-5xl mx-auto">

                <!-- ── Brochure avec viewer PDF intégré ── -->
                <div class="bg-white rounded-xl overflow-hidden mb-10 reveal-up"
                     :class="{ 'reveal-up--visible': dlVisible }">
                    <!-- Header -->
                    <div class="px-8 py-6 flex flex-col sm:flex-row sm:items-start justify-between gap-4 border-b border-base-100">
                        <div>
                            <span class="text-xs uppercase tracking-[0.2em] text-brand font-semibold">
                                {{ t('kit.brochure_label') }}
                            </span>
                            <h3 class="font-bold mt-1.5 mb-2" style="font-size: clamp(1.1rem, 2vw, 1.35rem);">
                                {{ t('kit.brochure_title') }}
                            </h3>
                            <p class="text-sm text-base-content/50 leading-relaxed" style="max-width: 50ch;">
                                {{ t('kit.brochure_desc') }}
                            </p>
                        </div>
                        <a href="/downloads/kit/brochure.pdf"
                           download
                           class="btn bg-brand hover:bg-brand-dark text-white border-none font-semibold px-6 gap-2 shrink-0 active:scale-[0.97]">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            {{ t('kit.brochure_download') }}
                        </a>
                    </div>
                    <!-- PDF viewer -->
                    <iframe
                        src="/downloads/kit/brochure.pdf"
                        class="w-full block border-0"
                        style="height: 580px;"
                        :title="t('kit.brochure_title')"
                    ></iframe>
                </div>

                <!-- ── Bento grid ── -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 reveal-up"
                     :class="{ 'reveal-up--visible': dlVisible }"
                     style="transition-delay: 100ms; grid-auto-rows: auto;">
                    <KitResourceCard
                        v-for="r in resources"
                        :key="r.key"
                        :resource-key="r.key"
                        :file="r.file"
                        :n="r.n"
                        :variant="r.variant"
                        :class="r.span"
                    />
                </div>

            </div>
        </section>

        <!-- ── CTA Contact — fond sombre ────────────────────────────────── -->
        <section class="py-16 px-6 bg-site-ink text-white" ref="ctaRef">
            <div class="max-w-5xl mx-auto">
                <h2 class="font-bold mb-4 leading-tight reveal-up"
                    :class="{ 'reveal-up--visible': ctaVisible }"
                    style="font-size: clamp(1.5rem, 3vw, 2.25rem);">
                    {{ t('kit.cta_section_title') }}
                </h2>
                <p class="text-white/55 text-sm mb-8 reveal-up"
                   :class="{ 'reveal-up--visible': ctaVisible }"
                   style="max-width: 44ch; transition-delay: 80ms;">
                    {{ t('kit.cta_section_desc') }}
                </p>
                <a href="/contact"
                   class="btn bg-white text-black hover:bg-white/90 border-none font-semibold px-8 rounded-sm uppercase text-sm tracking-wide reveal-up active:scale-[0.97]"
                   :class="{ 'reveal-up--visible': ctaVisible }"
                   style="transition-delay: 160ms;">
                    {{ t('kit.contact_link') }}
                </a>
            </div>
        </section>

        <Footer />
    </div>
</template>

<style scoped>
.kit-num {
    display: block;
    font-weight: 800;
    line-height: 1;
    font-size: 1.1rem;
    font-variant-numeric: tabular-nums;
    color: rgba(255, 255, 255, 0.35);
    min-width: 1.75rem;
}


.reveal-up {
    opacity: 0;
    transform: translateY(18px);
    transition: opacity 440ms cubic-bezier(0.23, 1, 0.32, 1),
                transform 440ms cubic-bezier(0.23, 1, 0.32, 1);
}
.reveal-up--visible {
    opacity: 1;
    transform: translateY(0);
}

@media (prefers-reduced-motion: reduce) {
    .reveal-up { opacity: 1; transform: none; transition: none; }
    .resource-card { transition: none; }
}
</style>

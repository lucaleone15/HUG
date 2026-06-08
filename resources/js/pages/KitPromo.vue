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
// imageRatio = largeur/hauteur exacte de l'image source
const resources = [
    { key: 'affiches', file: '/downloads/kit/affiches.zip', n: 1, span: '', imageRatio: '848/1200',
      slides: [
          '/images/kit/slides/affiches/loupe-fondblanc.png',
          '/images/kit/slides/affiches/loupe-fondnoir.png',
          '/images/kit/slides/affiches/poche-fondblanc.png',
          '/images/kit/slides/affiches/poche-fondnoir.png',
      ]},
    { key: 'flyers', file: '/downloads/kit/flyers.zip', n: 2, span: '', imageRatio: '845/1200',
      slides: [
          '/images/kit/slides/flyers/loupe-fondblanc.png',
          '/images/kit/slides/flyers/loupe-fondnoir.png',
          '/images/kit/slides/flyers/poche-fondblanc.png',
          '/images/kit/slides/flyers/poche-fondnoir.png',
      ]},
    { key: 'rollup',  file: '/downloads/kit/rollup.zip',         n: 3, span: '',              image: '/images/kit/preview-rollup.png', imageRatio: '3000/1206' },
    { key: 'banners', file: '/downloads/kit/bannieres.zip',       n: 4, span: 'sm:col-span-2', imageRatio: '1200/396',
      slides: [
          '/images/kit/slides/banners/composite-black-md.png',
          '/images/kit/slides/banners/composite-white-md.png',
          '/images/kit/slides/banners/composite-black-sm.png',
          '/images/kit/slides/banners/composite-white-sm.png',
      ]},
    { key: 'tv',     file: '/downloads/kit/infographie-tv.zip',  n: 5, span: '', image: null, video: '/pdfs/02%20Communication%20digitale/InfoTV/InfoTV.mp4', imageRatio: '16/9' },
    { key: 'social', file: '/downloads/kit/reseaux-sociaux.zip', n: 6, span: 'sm:col-span-3', imageRatio: '2160/2700',
      slides: [
          '/images/kit/slides/social/carousel-1.png',
          '/images/kit/slides/social/carousel-2.png',
          '/images/kit/slides/social/carousel-3.png',
          '/images/kit/slides/social/carousel-4.png',
          '/images/kit/slides/social/carousel-5.png',
      ]},
    { key: 'email',  file: '/downloads/kit/email-invitation.zip', n: 7, span: 'sm:col-span-2', image: null, imageRatio: null },
]

// Flat list for the red summary section
const allResources = resources

const resourceProps = (key) => {
    const r = resources.find(x => x.key === key)
    return {
        resourceKey: r.key,
        file: r.file,
        n: r.n,
        image: r.image ?? null,
        video: r.video ?? null,
        imageRatio: r.imageRatio,
        slides: r.slides ?? null,
    }
}
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
                <div class="kit-hero-visual" aria-hidden="true">
                    <img :src="'/images/kit/slides/affiches/poche-fondnoir.png'"
                         class="kit-hero-img kit-hero-img--back" alt="" />
                    <img :src="'/images/kit/slides/affiches/loupe-fondblanc.png'"
                         class="kit-hero-img kit-hero-img--front" alt="" />
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
                        <a href="/pdfs/Kit_Promotionnel.pdf"
                           download
                           class="btn bg-brand hover:bg-brand-dark text-white border-none font-semibold px-6 shrink-0 active:scale-[0.97]">
                            {{ t('kit.brochure_download') }}
                        </a>
                    </div>
                    <!-- PDF viewer -->
                    <iframe
                        src="/pdfs/Kit_Promotionnel.pdf"
                        class="w-full block border-0"
                        style="height: 580px;"
                        :title="t('kit.brochure_title')"
                    ></iframe>
                </div>

                <!-- ── Bento grid ── -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 reveal-up"
                     :class="{ 'reveal-up--visible': dlVisible }"
                     style="transition-delay: 100ms;">

                    <!-- Affiches -->
                    <KitResourceCard v-bind="resourceProps('affiches')" />
                    <!-- Flyers -->
                    <KitResourceCard v-bind="resourceProps('flyers')" />
                    <!-- Rollup + TV empilés dans la même colonne -->
                    <div class="flex flex-col gap-4">
                        <KitResourceCard v-bind="resourceProps('rollup')" />
                        <KitResourceCard v-bind="resourceProps('tv')" class="flex-1" />
                    </div>
                    <!-- Bannières + Réseaux sociaux côte à côte -->
                    <KitResourceCard v-bind="resourceProps('banners')" class="sm:col-span-2" />
                    <KitResourceCard v-bind="resourceProps('social')" />
                    <!-- Email d'invitation (placeholder) -->
                    <KitResourceCard v-bind="resourceProps('email')" class="sm:col-span-2" />
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
                   class="btn bg-white text-black hover:bg-white/90 border-none font-semibold px-8 rounded-sm reveal-up active:scale-[0.97]"
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
/* ── Hero visual ─────────────────────────────────────────────── */
.kit-hero-visual {
    position: relative;
    width: 300px;
    height: 420px;
    margin: auto;
}
.kit-hero-img {
    position: absolute;
    width: 220px;
    border-radius: 8px;
    box-shadow: 0 16px 48px rgba(25, 5, 7, 0.20), 0 4px 12px rgba(25, 5, 7, 0.10);
}
.kit-hero-img--back {
    transform: rotate(-7deg) translate(-22px, 40px);
    z-index: 1;
}
.kit-hero-img--front {
    transform: rotate(4deg) translate(28px, 22px);
    z-index: 2;
}

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

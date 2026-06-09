<script setup>
import { ref, computed, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import { EMAIL_TEMPLATE, FIXED_IMAGES, LOGO_IMG_DEFAULT } from "../data/emailTemplate.js";
import NavBar from "../components/ui/NavBar.vue";
import Footer from "../components/ui/Footer.vue";
import PageHero from "../components/ui/PageHero.vue";
import KitResourceCard from "../components/kit/KitResourceCard.vue";

const { t } = useI18n();

function repAll(s, f, v) { return s.split(f).join(v); }

const emailPreviewHtml = computed(() => {
    let html = EMAIL_TEMPLATE;
    // Translations
    for (const [ph, key] of [
        ["[EMAIL_PREHEADER]",       "email_preheader"],
        ["[EMAIL_BAD_DISPLAY]",     "email_bad_display"],
        ["[EMAIL_VIEW_ONLINE]",     "email_view_online"],
        ["[EMAIL_DOSSIER_BADGE]",   "email_dossier_badge"],
        ["[EMAIL_MISSION_BADGE]",   "email_mission_badge"],
        ["[EMAIL_ALT_LOUPE]",       "email_alt_loupe"],
        ["[EMAIL_HERO_VERB]",       "email_hero_verb"],
        ["[EMAIL_HERO_NOUN]",       "email_hero_noun"],
        ["[EMAIL_HERO_ADJ]",        "email_hero_adj"],
        ["[EMAIL_BODY1]",           "email_body1"],
        ["[EMAIL_BODY2]",           "email_body2"],
        ["[EMAIL_BODY3]",           "email_body3"],
        ["[EMAIL_BODY4]",           "email_body4"],
        ["[EMAIL_BODY5]",           "email_body5"],
        ["[EMAIL_CTA_ELIG]",        "email_cta_elig"],
        ["[EMAIL_COLLECTE1]",       "email_collecte1"],
        ["[EMAIL_COLLECTE2]",       "email_collecte2"],
        ["[EMAIL_CHEZ]",            "email_chez"],
        ["[EMAIL_LABEL_DATE]",      "email_label_date"],
        ["[EMAIL_LABEL_HORAIRES]",  "email_label_horaires"],
        ["[EMAIL_LABEL_LIEU]",      "email_label_lieu"],
        ["[EMAIL_LABEL_ADRESSE]",   "email_label_adresse"],
        ["[EMAIL_LABEL_STATUT]",    "email_label_statut"],
        ["[EMAIL_STATUT_VALUE]",    "email_statut_value"],
        ["[EMAIL_LINK_RDV]",        "email_link_rdv"],
        ["[EMAIL_INTERRO_BADGE]",   "email_interro_badge"],
        ["[EMAIL_ALT_CHAPEAU]",     "email_alt_chapeau"],
        ["[EMAIL_ETES_VOUS]",       "email_etes_vous"],
        ["[EMAIL_ELIGIBLE]",        "email_eligible"],
        ["[EMAIL_QUIZ_DESC1]",      "email_quiz_desc1"],
        ["[EMAIL_QUIZ_DESC2]",      "email_quiz_desc2"],
        ["[EMAIL_CTA_ENQUETE]",     "email_cta_enquete"],
        ["[EMAIL_QUIZ_BADGE]",      "email_quiz_badge"],
        ["[EMAIL_FOOTER_ORG]",      "email_footer_org"],
        ["[EMAIL_FOOTER_HOSPITAL]", "email_footer_hospital"],
        ["[EMAIL_FOOTER_LINKS]",    "email_footer_links"],
        ["[EMAIL_FOOTER_LEARN]",    "email_footer_learn"],
        ["[EMAIL_FOOTER_COPY]",     "email_footer_copy"],
    ]) {
        html = repAll(html, ph, t("kit." + key));
    }
    // Data placeholders
    html = repAll(html, "[VOTRE ENTREPRISE]",             t("kit.email_ph_company"));
    html = repAll(html, "[JJ MOIS AAAA]",                 t("kit.email_ph_date"));
    html = repAll(html, "[HHhMM &ndash; HHhMM]",          t("kit.email_ph_horaires"));
    html = repAll(html, "[B&Acirc;TIMENT / SALLE]",       t("kit.email_ph_salle"));
    html = repAll(html, "[RUE ET NUM&Eacute;RO, VILLE]",   t("kit.email_ph_adresse"));
    html = repAll(html, "[URL_ELIGIBILITE]",  "#");
    html = repAll(html, "[URL_RESERVATION_DON]", "#");
    html = repAll(html, "[URL_PAGE]", "#");
    // Images (local URL — même origine, pas besoin de base64)
    for (const img of FIXED_IMAGES) {
        html = repAll(html, img.url, img.localUrl);
    }
    // Logo placeholder
    html = repAll(html, LOGO_IMG_DEFAULT,
        `<span style="display:inline-block;border:1px dashed rgba(255,255,255,0.3);padding:0 14px;font-family:'Cooper Hewitt','Helvetica Neue',Arial,sans-serif;font-size:10px;font-weight:700;letter-spacing:2px;color:rgba(255,255,255,0.35);height:44px;line-height:44px;vertical-align:middle;border-radius:2px;">LOGO</span>`
    );
    // Strip elements not relevant in the kit preview
    html = html.replace(/<!--KIT_HIDE-->[\s\S]*?<!--\/KIT_HIDE-->/g, '');
    return html;
});

function makeObserver(targetRef, visibleRef, threshold = 0.1) {
    const io = new IntersectionObserver(
        ([e]) => {
            if (e.isIntersecting) {
                visibleRef.value = true;
                io.disconnect();
            }
        },
        { threshold },
    );
    if (targetRef.value) io.observe(targetRef.value);
}

const listRef = ref(null);
const listVisible = ref(false);
const dlRef = ref(null);
const dlVisible = ref(false);
const ctaRef = ref(null);
const ctaVisible = ref(false);

onMounted(() => {
    makeObserver(listRef, listVisible, 0.1);
    makeObserver(dlRef, dlVisible, 0.08);
    makeObserver(ctaRef, ctaVisible, 0.2);
});

const resources = [
    {
        key: "affiches",
        file: "/downloads/kit/affiches.zip",
        n: 1,
        span: "",
        imageRatio: "848/1200",
        slides: [
            "/images/kit/slides/affiches/loupe-fondblanc.png",
            "/images/kit/slides/affiches/loupe-fondnoir.png",
            "/images/kit/slides/affiches/poche-fondblanc.png",
            "/images/kit/slides/affiches/poche-fondnoir.png",
        ],
    },
    {
        key: "flyers",
        file: "/downloads/kit/flyers.zip",
        n: 2,
        span: "",
        imageRatio: "845/1200",
        slides: [
            "/images/kit/slides/flyers/loupe-fondblanc.png",
            "/images/kit/slides/flyers/loupe-fondnoir.png",
            "/images/kit/slides/flyers/poche-fondblanc.png",
            "/images/kit/slides/flyers/poche-fondnoir.png",
        ],
    },
    {
        key: "rollup",
        file: "/downloads/kit/rollup.zip",
        n: 3,
        span: "",
        image: "/images/kit/preview-rollup.png",
        imageRatio: "3000/1206",
    },
    {
        key: "banners",
        file: "/downloads/kit/bannieres.zip",
        n: 4,
        span: "sm:col-span-2",
        imageRatio: "1200/396",
        slides: [
            "/images/kit/slides/banners/composite-black-md.png",
            "/images/kit/slides/banners/composite-white-md.png",
            "/images/kit/slides/banners/composite-black-sm.png",
            "/images/kit/slides/banners/composite-white-sm.png",
        ],
    },
    {
        key: "tv",
        file: "/downloads/kit/infographie-tv.zip",
        n: 5,
        span: "",
        image: null,
        video: "/pdfs/02%20Communication%20digitale/InfoTV/InfoTV.mp4",
        imageRatio: "16/9",
    },
    {
        key: "social",
        file: "/downloads/kit/reseaux-sociaux.zip",
        n: 6,
        span: "sm:col-span-3",
        imageRatio: "2160/2700",
        slides: [
            "/images/kit/slides/social/carousel-1.png",
            "/images/kit/slides/social/carousel-2.png",
            "/images/kit/slides/social/carousel-3.png",
            "/images/kit/slides/social/carousel-4.png",
            "/images/kit/slides/social/carousel-5.png",
        ],
    },
    {
        key: "email",
        file: null,
        customHref: "/generateur-email",
        n: 7,
        span: "sm:col-span-2",
        image: null,
        imageRatio: "4/3",
        useEmailPreview: true,
    },
    {
        key: "goodies",
        file: "/downloads/kit/goodies.zip",
        n: 8,
        span: "",
        imageRatio: "1/1",
        slides: [
            "/images/kit/slides/goodies/sticker-1.png",
            "/images/kit/slides/goodies/sticker-2.png",
            "/images/kit/slides/goodies/sticker-3.png",
        ],
    },
];

const allResources = resources;

const resourceProps = (key) => {
    const r = resources.find((x) => x.key === key);
    return {
        resourceKey: r.key,
        file: r.file ?? "#",
        customHref: r.customHref ?? null,
        actionLabel: r.customHref ? t("kit.generate_label") : null,
        n: r.n,
        image: r.image ?? null,
        video: r.video ?? null,
        imageRatio: r.imageRatio,
        slides: r.slides ?? null,
        previewHtml: r.useEmailPreview ? emailPreviewHtml.value : null,
    };
};
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
                    <img
                        :src="'/images/kit/slides/affiches/poche-fondnoir.png'"
                        class="kit-hero-img kit-hero-img--back"
                        alt=""
                    />
                    <img
                        :src="'/images/kit/slides/affiches/loupe-fondblanc.png'"
                        class="kit-hero-img kit-hero-img--front"
                        alt=""
                    />
                </div>
            </template>
        </PageHero>

        <!-- Contenu du kit -->
        <section class="py-10 px-6 bg-brand text-white" ref="listRef">
            <div class="max-w-5xl mx-auto">
                <div
                    class="flex flex-col sm:flex-row sm:items-baseline gap-2 sm:gap-6 mb-6 reveal-up"
                    :class="{ 'reveal-up--visible': listVisible }"
                >
                    <h2
                        class="font-bold leading-tight"
                        style="font-size: clamp(1.25rem, 2.5vw, 1.75rem)"
                    >
                        {{ t("kit.contents_section_title") }}
                    </h2>
                    <p
                        class="text-white/70 text-sm"
                        style="transition-delay: 60ms"
                    >
                        {{ t("kit.contents_section_desc") }}
                    </p>
                </div>

                <div
                    class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-x-8 gap-y-4"
                >
                    <div
                        v-for="(r, i) in allResources"
                        :key="r.key"
                        class="flex items-center gap-3 reveal-up"
                        :class="{ 'reveal-up--visible': listVisible }"
                        :style="`transition-delay: ${(i + 1) * 60}ms`"
                    >
                        <span class="kit-num shrink-0" aria-hidden="true">{{
                            String(r.n).padStart(2, "0")
                        }}</span>
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

        <!-- Téléchargements -->
        <section
            id="telechargements"
            class="py-16 px-6 bg-base-200"
            ref="dlRef"
        >
            <div class="max-w-5xl mx-auto">
                <!-- Brochure avec viewer PDF intégré -->
                <div
                    class="bg-white rounded-xl overflow-hidden mb-10 reveal-up"
                    :class="{ 'reveal-up--visible': dlVisible }"
                >
                    <!-- Header -->
                    <div
                        class="px-8 py-6 flex flex-col sm:flex-row sm:items-start justify-between gap-4 border-b border-base-100"
                    >
                        <div>
                            <span
                                class="text-xs uppercase tracking-[0.2em] text-brand font-semibold"
                            >
                                {{ t("kit.brochure_label") }}
                            </span>
                            <h3
                                class="font-bold mt-1.5 mb-2"
                                style="font-size: clamp(1.1rem, 2vw, 1.35rem)"
                            >
                                {{ t("kit.brochure_title") }}
                            </h3>
                            <p
                                class="text-sm text-base-content/50 leading-relaxed"
                                style="max-width: 50ch"
                            >
                                {{ t("kit.brochure_desc") }}
                            </p>
                        </div>
                        <a
                            href="/pdfs/Kit_Promotionnel_entreprises.pdf"
                            download
                            class="btn bg-brand hover:bg-brand-dark text-white border-none font-semibold px-6 shrink-0 active:scale-[0.97]"
                        >
                            {{ t("kit.brochure_download") }}
                        </a>
                    </div>
                    <!-- PDF viewer -->
                    <iframe
                        src="/pdfs/Kit_Promotionnel_entreprises.pdf"
                        class="w-full block border-0"
                        style="height: 580px"
                        :title="t('kit.brochure_title')"
                    ></iframe>
                </div>

                <!-- Grid -->
                <div
                    class="grid grid-cols-1 sm:grid-cols-3 gap-4 reveal-up"
                    :class="{ 'reveal-up--visible': dlVisible }"
                    style="transition-delay: 100ms"
                >
                    <!-- Affiches -->
                    <KitResourceCard v-bind="resourceProps('affiches')" />
                    <!-- Flyers -->
                    <KitResourceCard v-bind="resourceProps('flyers')" />
                    <!-- Rollup + TV empilés dans la même colonne -->
                    <div class="flex flex-col gap-4">
                        <KitResourceCard v-bind="resourceProps('rollup')" />
                        <KitResourceCard
                            v-bind="resourceProps('tv')"
                            class="flex-1"
                        />
                    </div>
                    <!-- Bannières (2 col) + Social (1 col) — même rangée -->
                    <KitResourceCard v-bind="resourceProps('banners')" class="sm:col-span-2" />
                    <KitResourceCard v-bind="resourceProps('social')" />
                    <!-- Email (2 col) + Goodies (1 col) — même rangée → bas aligné automatiquement -->
                    <KitResourceCard v-bind="resourceProps('email')" class="sm:col-span-2" />
                    <KitResourceCard v-bind="resourceProps('goodies')" />
                </div>
            </div>
        </section>

        <!-- CTA Contact — fond sombre -->
        <section class="py-16 px-6 bg-site-ink text-white" ref="ctaRef">
            <div class="max-w-5xl mx-auto">
                <h2
                    class="font-bold mb-4 leading-tight reveal-up"
                    :class="{ 'reveal-up--visible': ctaVisible }"
                    style="font-size: clamp(1.5rem, 3vw, 2.25rem)"
                >
                    {{ t("kit.cta_section_title") }}
                </h2>
                <p
                    class="text-white/55 text-sm mb-8 reveal-up"
                    :class="{ 'reveal-up--visible': ctaVisible }"
                    style="max-width: 44ch; transition-delay: 80ms"
                >
                    {{ t("kit.cta_section_desc") }}
                </p>
                <a
                    href="/contact"
                    class="btn bg-white text-black hover:bg-white/90 border-none font-semibold px-8 rounded-sm reveal-up active:scale-[0.97]"
                    :class="{ 'reveal-up--visible': ctaVisible }"
                    style="transition-delay: 160ms"
                >
                    {{ t("kit.contact_link") }}
                </a>
            </div>
        </section>

        <Footer />
    </div>
</template>

<style scoped>
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
    box-shadow:
        0 16px 48px rgba(25, 5, 7, 0.2),
        0 4px 12px rgba(25, 5, 7, 0.1);
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
    transition:
        opacity 440ms cubic-bezier(0.23, 1, 0.32, 1),
        transform 440ms cubic-bezier(0.23, 1, 0.32, 1);
}
.reveal-up--visible {
    opacity: 1;
    transform: translateY(0);
}

@media (prefers-reduced-motion: reduce) {
    .reveal-up {
        opacity: 1;
        transform: none;
        transition: none;
    }
    .resource-card {
        transition: none;
    }
}
</style>

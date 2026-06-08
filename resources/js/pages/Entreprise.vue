<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { useI18n } from "vue-i18n";
import Footer from "../components/ui/Footer.vue";
import LangSwitcher from "../components/ui/LangSwitcher.vue";
import CorkboardFaq from "../components/ui/CorkboardFaq.vue";
import { sendAnalytics, getDevice } from "../composables/useAnalytics.js";
import LogoContainer from "../components/ui/LogoContainer.vue";
import { useLogoBg } from "../composables/useLogoBg.js";

const { t, locale } = useI18n();

const DATE_LOCALES = { fr: "fr-CH", de: "de-CH", it: "it-CH", en: "en-GB" };

const hugLogoError = ref(false);

const props = defineProps({
    entreprise: Object,
    collectes: { type: Array, default: () => [] },
});

const c1 = computed(() => props.entreprise.primary_color || "#E30613");
const c2 = computed(() => props.entreprise.secondary_color || c1.value);

const textOn = (hex) => {
    if (!hex || !/^#[0-9A-Fa-f]{6}$/.test(hex)) return "#ffffff";
    const [r, g, b] = [hex.slice(1, 3), hex.slice(3, 5), hex.slice(5, 7)].map(
        (h) => {
            const c = parseInt(h, 16) / 255;
            return c <= 0.03928
                ? c / 12.92
                : Math.pow((c + 0.055) / 1.055, 2.4);
        },
    );
    const L = 0.2126 * r + 0.7152 * g + 0.0722 * b;
    return L > 0.179 ? "#111111" : "#ffffff";
};

const t1 = computed(() => textOn(c1.value));
const t2 = computed(() => textOn(c2.value));

const accentColor = computed(() =>
    props.entreprise.secondary_color && c2.value !== c1.value
        ? c2.value
        : "#E22C1B",
);

// c1 safe to use as text on a white background (falls back when c1 is too light)
const c1OnWhite = computed(() =>
    t1.value === "#ffffff" ? c1.value : accentColor.value,
);

const { bg: headerLogoBg } = useLogoBg(
    () => props.entreprise.logo_url,
    () => props.entreprise.primary_color,
);

const upcomingCollectes = computed(() =>
    props.collectes.filter((c) => c.is_active),
);
const pastCollectes = computed(() =>
    props.collectes.filter((c) => !c.is_active),
);
const nextCollecte = computed(() => upcomingCollectes.value[0] ?? null);

const countdown = ref({ days: 0, hours: 0, minutes: 0 });
let timer = null;
const hasCountdown = computed(() => !!nextCollecte.value?.rdv_date);
const countdownActive = computed(
    () =>
        countdown.value.days > 0 ||
        countdown.value.hours > 0 ||
        countdown.value.minutes > 0,
);
const pad = (n) => String(n).padStart(2, "0");
const isDatePast = (dateStr) =>
    !!dateStr && new Date(dateStr).getTime() < Date.now();

const updateCountdown = () => {
    const target = new Date(nextCollecte.value.rdv_date).getTime();
    const diff = target - Date.now();
    if (diff <= 0) {
        countdown.value = { days: 0, hours: 0, minutes: 0 };
        return;
    }
    countdown.value = {
        days: Math.floor(diff / 86400000),
        hours: Math.floor((diff % 86400000) / 3600000),
        minutes: Math.floor((diff % 3600000) / 60000),
    };
};

const monthShort = (dateStr) => {
    if (!dateStr) return "";
    return new Date(dateStr).toLocaleDateString(
        DATE_LOCALES[locale.value] ?? "fr-CH",
        { month: "short" },
    );
};
const dayNum = (dateStr) => {
    if (!dateStr) return "";
    return new Date(dateStr).getDate();
};
const fullDate = (dateStr) => {
    if (!dateStr) return "";
    return new Date(dateStr).toLocaleDateString(
        DATE_LOCALES[locale.value] ?? "fr-CH",
        { day: "numeric", month: "long", year: "numeric" },
    );
};

function makeObserver(targetRef, visibleRef, threshold = 0.15) {
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

const faqRef = ref(null);
const faqVisible = ref(false);
const statsRef = ref(null);
const statsVisible = ref(false);
const ctaERef = ref(null);
const ctaEVisible = ref(false);

onMounted(() => {
    sendAnalytics("page_viewed", props.entreprise.id, null, {
        referrer: document.referrer || "direct",
        device: getDevice(),
    });
    if (hasCountdown.value) {
        updateCountdown();
        timer = setInterval(updateCountdown, 60000);
    }
    makeObserver(faqRef, faqVisible, 0.08);
    makeObserver(statsRef, statsVisible, 0.15);
    makeObserver(ctaERef, ctaEVisible, 0.2);
});

onBeforeUnmount(() => {
    if (timer) clearInterval(timer);
});
</script>

<template>
    <div
        class="min-h-screen bg-base-100 flex flex-col overflow-x-hidden"
        :style="`--c1: ${c1}; --c2: ${c2}; --t1: ${t1}; --t2: ${t2}; --c1-on-white: ${c1OnWhite}`"
    >
        <!-- Header co-brandé -->
        <header
            class="bg-white sticky top-0 z-50 px-3 md:px-6 py-3"
            style="border-bottom: 3px solid var(--c1)"
        >
            <div
                class="max-w-5xl mx-auto flex items-center justify-between gap-2"
            >
                <!-- Logos -->
                <div class="flex items-center gap-2 min-w-0 overflow-hidden">
                    <a href="/" class="flex items-center shrink-0">
                        <img
                            v-if="!hugLogoError"
                            :src="'/images/hug-logo.svg'"
                            alt="HUG"
                            class="h-7 w-auto"
                            @error="hugLogoError = true"
                        />
                        <span
                            v-else
                            class="font-bold text-sm"
                            style="color: var(--c1)"
                            >HUG</span
                        >
                    </a>
                    <span class="text-base-content/30 font-light shrink-0"
                        >×</span
                    >
                    <div
                        v-if="entreprise.logo_url"
                        class="rounded p-1 shrink-0 flex items-center justify-center"
                        style="height: 36px; width: auto; min-width: 36px"
                        :style="`background-color: ${headerLogoBg}`"
                    >
                        <img
                            :src="entreprise.logo_url"
                            :alt="entreprise.name"
                            class="h-7 max-w-[56px] object-contain"
                        />
                    </div>
                    <span
                        v-else
                        class="font-semibold text-sm truncate min-w-0"
                        >{{ entreprise.name }}</span
                    >
                </div>
                <!-- Actions -->
                <div class="flex items-center gap-2 shrink-0">
                    <a
                        :href="`/c/${entreprise.access_token}/quiz`"
                        class="hidden sm:inline-flex btn btn-sm btn-co border-none rounded-sm text-xs font-semibold"
                    >
                        {{ t("entreprise.quiz_cta") }}
                    </a>
                    <LangSwitcher />
                </div>
            </div>
        </header>

        <!-- Bottom nav — mobile uniquement -->
        <nav
            class="fixed bottom-0 left-0 right-0 z-50 lg:hidden"
            style="
                background-color: var(--c1);
                padding-bottom: env(safe-area-inset-bottom);
            "
        >
            <div class="flex items-center px-4 py-2 min-h-[56px]">
                <a
                    :href="`/c/${entreprise.access_token}/quiz`"
                    class="flex-1 btn border-none rounded-sm text-xs font-semibold"
                    style="background-color: var(--t1); color: var(--c1)"
                >
                    {{ t("entreprise.quiz_cta") }}
                </a>
            </div>
        </nav>

        <!-- Hero -->
        <section
            class="relative overflow-hidden min-h-[60vh] md:min-h-[95vh]"
            style="background-color: var(--c1)"
        >
            <!-- Inspecteur -->
            <svg
                aria-hidden="true"
                class="absolute inset-0 w-full h-full pointer-events-none entreprise-hero-img"
                viewBox="800 0 1120 1128"
                preserveAspectRatio="xMaxYMax meet"
                xmlns="http://www.w3.org/2000/svg"
            >
                <g clip-path="url(#insp-clip)">
                    <path
                        d="M933.799 1499.7C933.799 1499.7 950.576 1006.89 1025.04 933.053C1099.51 859.21 1143.94 732.146 1386.42 639.995C1628.03 548.139 1645.24 571.295 1645.24 571.295C1645.24 571.295 1780.06 580.492 1846.21 595.391C1912.24 610.286 2303.63 748.876 2396.36 872.46C2396.36 872.46 2332.11 980.342 1963.5 1160C1647.77 1313.88 1011.01 1450.8 933.799 1499.7Z"
                        fill="#E62F2E"
                    />
                    <path
                        d="M812.229 1028.01L974.547 870.144L935.954 369.91L1163.98 319.895L1393.4 63.9816L1839.23 502.317L2101.3 503.105L2117 536.8L812.229 1028.01Z"
                        fill="#111111"
                    />
                    <path
                        d="M999.017 993.27L938.056 1087.17L1506.92 972.352C1518.22 970.051 1529.09 965.871 1538.95 959.969L2145.17 598.385L2054.82 583.075L999.017 993.27Z"
                        fill="#111111"
                    />
                    <path
                        d="M1225.66 939.31L1508.32 844.193C1508.32 844.193 1503.03 946.769 1409.08 975.177C1315.12 1003.58 1289.48 985.623 1225.66 939.31Z"
                        fill="white"
                    />
                    <path
                        d="M1522.23 840.268C1522.23 840.268 1600.84 901.792 1668.87 868.171C1736.88 834.506 1775.54 714.934 1775.54 714.934L1515.2 843.755"
                        fill="white"
                    />
                </g>
                <defs>
                    <clipPath id="insp-clip">
                        <rect width="1920" height="1128" fill="white" />
                    </clipPath>
                </defs>
            </svg>

            <div
                class="relative z-10 max-w-5xl mx-auto py-12 md:py-20 px-4 md:px-6 flex items-center min-h-[60vh] md:min-h-[95vh]"
            >
                <div
                    class="page-hero-text hero-text-bg w-full md:w-1/2 text-center md:text-left"
                >
                    <h1
                        class="font-extrabold leading-tight mb-5"
                        style="
                            color: var(--t1);
                            font-size: clamp(1.6rem, 5vw, 3.5rem);
                            overflow-wrap: break-word;
                        "
                    >
                        {{ t("entreprise.hero_title_line1") }}
                        <span
                            class="inline-block px-1 -mx-1 rounded-md"
                            style="
                                background-color: var(--t1);
                                color: var(--c1);
                            "
                            >{{ t("entreprise.hero_title_highlight") }}</span
                        ><br />
                        {{ t("entreprise.hero_title_line2") }}
                    </h1>
                    <p
                        class="mb-8 leading-relaxed"
                        style="color: var(--t1); opacity: 0.85"
                    >
                        {{
                            t("entreprise.hero_description", {
                                company: entreprise.name,
                            })
                        }}
                    </p>
                    <a
                        :href="`/c/${entreprise.access_token}/quiz`"
                        class="btn border-none font-semibold px-6 md:px-8 rounded-sm w-full sm:w-auto"
                        style="background-color: var(--t1); color: var(--c1)"
                    >
                        {{ t("entreprise.quiz_discover") }}
                    </a>
                </div>
            </div>
        </section>

        <!-- Countdown + Collectes -->
        <section
            class="px-4 md:px-6 bg-base-100"
            :class="
                hasCountdown || upcomingCollectes.length
                    ? 'py-10 md:py-14'
                    : 'py-6 md:py-8'
            "
        >
            <div class="max-w-5xl mx-auto">
                <!-- Countdown -->
                <div
                    v-if="hasCountdown"
                    class="mb-8 pb-8 border-b border-base-200"
                >
                    <p class="text-sm font-semibold mb-4 text-base-content/60">
                        {{ t("entreprise.countdown_label") }}
                    </p>
                    <div v-if="countdownActive" class="flex items-end gap-3">
                        <div class="flex-1 text-center">
                            <div
                                class="font-extrabold tabular-nums leading-none"
                                style="
                                    color: var(--c1-on-white);
                                    font-size: clamp(2rem, 5vw, 3rem);
                                "
                            >
                                {{ pad(countdown.days) }}
                            </div>
                            <div class="text-sm mt-2 text-base-content/60">
                                {{ t("entreprise.countdown_days") }}
                            </div>
                        </div>
                        <span
                            class="pb-5 font-bold shrink-0 text-xl text-base-content/40"
                            >:</span
                        >
                        <div class="flex-1 text-center">
                            <div
                                class="font-extrabold tabular-nums leading-none"
                                style="
                                    color: var(--c1-on-white);
                                    font-size: clamp(2rem, 5vw, 3rem);
                                "
                            >
                                {{ pad(countdown.hours) }}
                            </div>
                            <div class="text-sm mt-2 text-base-content/60">
                                {{ t("entreprise.countdown_hours") }}
                            </div>
                        </div>
                        <span
                            class="pb-5 font-bold shrink-0 text-xl text-base-content/40"
                            >:</span
                        >
                        <div class="flex-1 text-center">
                            <div
                                class="font-extrabold tabular-nums leading-none"
                                style="
                                    color: var(--c1-on-white);
                                    font-size: clamp(2rem, 5vw, 3rem);
                                "
                            >
                                {{ pad(countdown.minutes) }}
                            </div>
                            <div class="text-sm mt-2 text-base-content/60">
                                {{ t("entreprise.countdown_minutes") }}
                            </div>
                        </div>
                    </div>
                    <p
                        v-if="nextCollecte?.rdv_date"
                        class="text-sm mt-4 text-base-content/60"
                    >
                        {{ t("entreprise.collect_date") }} :
                        <strong class="text-base-content">{{
                            fullDate(nextCollecte.rdv_date)
                        }}</strong>
                    </p>
                </div>
                <div v-else>
                    <p class="text-sm font-semibold mb-2 text-base-content/60">
                        {{ t("entreprise.collecte_none_label") }}
                    </p>
                    <p
                        class="font-bold mb-1 text-base-content"
                        style="font-size: clamp(1rem, 2vw, 1.2rem)"
                    >
                        {{ t("entreprise.collecte_none_title") }}
                    </p>
                    <p
                        class="text-sm text-base-content/60"
                        style="max-width: 38ch"
                    >
                        {{
                            t("entreprise.collecte_none_description", {
                                company: entreprise.name,
                            })
                        }}
                    </p>
                </div>

                <!-- Collectes à venir -->
                <template v-if="upcomingCollectes.length">
                    <div class="flex flex-col gap-3">
                        <template
                            v-for="c in upcomingCollectes"
                            :key="c.id"
                        >
                            <!-- Collecte dont la date est passée (admin pas encore désactivée) -->
                            <div
                                v-if="isDatePast(c.rdv_date)"
                                class="flex items-center gap-4 rounded-xl border border-base-200 bg-base-100 px-4 py-4 opacity-60"
                            >
                                <div
                                    class="shrink-0 rounded-lg w-14 text-center py-2 bg-base-300 text-base-content/50"
                                >
                                    <div
                                        class="text-2xl font-extrabold leading-none tabular-nums"
                                    >
                                        {{ c.rdv_date ? dayNum(c.rdv_date) : "—" }}
                                    </div>
                                    <div
                                        class="text-[0.6rem] uppercase tracking-wider mt-0.5"
                                    >
                                        {{ c.rdv_date ? monthShort(c.rdv_date) : "" }}
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div
                                        class="font-semibold text-base-content/70 leading-snug truncate"
                                    >
                                        {{
                                            c.label ||
                                            t("entreprise.collecte_default_label")
                                        }}
                                    </div>
                                    <div
                                        v-if="c.rdv_date"
                                        class="text-xs text-base-content/40 mt-0.5"
                                    >
                                        {{ fullDate(c.rdv_date) }}
                                    </div>
                                </div>
                                <span
                                    class="shrink-0 badge badge-ghost badge-sm uppercase tracking-wide font-semibold text-base-content/50"
                                >
                                    {{ t("entreprise.collecte_past_badge") }}
                                </span>
                            </div>

                            <!-- Collecte à venir (date future ou pas encore fixée) -->
                            <a
                                v-else
                                :href="`/c/${entreprise.access_token}/quiz`"
                                class="flex items-center gap-4 rounded-xl px-4 py-4 collecte-card"
                                style="background-color: var(--c1)"
                            >
                                <div
                                    class="shrink-0 rounded-lg w-14 text-center py-2"
                                    style="
                                        background-color: color-mix(
                                            in srgb,
                                            var(--t1) 15%,
                                            transparent
                                        );
                                        color: var(--t1);
                                    "
                                >
                                    <div
                                        class="text-2xl font-extrabold leading-none tabular-nums"
                                    >
                                        {{ c.rdv_date ? dayNum(c.rdv_date) : "—" }}
                                    </div>
                                    <div
                                        class="text-[0.6rem] uppercase tracking-wider opacity-90 mt-0.5"
                                    >
                                        {{
                                            c.rdv_date ? monthShort(c.rdv_date) : ""
                                        }}
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div
                                        class="font-bold leading-snug truncate"
                                        style="color: var(--t1)"
                                    >
                                        {{
                                            c.label ||
                                            t("entreprise.collecte_default_label")
                                        }}
                                    </div>
                                    <div
                                        v-if="c.rdv_date"
                                        class="text-xs mt-0.5"
                                        style="color: var(--t1); opacity: 0.68"
                                    >
                                        {{ fullDate(c.rdv_date) }}
                                    </div>
                                </div>
                                <!-- CTA -->
                                <div class="shrink-0">
                                    <span
                                        class="btn btn-co btn-sm border-none rounded-sm text-xs font-semibold"
                                    >
                                        {{ t("entreprise.collecte_rdv_cta_short") }}
                                    </span>
                                </div>
                            </a>
                        </template>
                    </div>
                </template>
            </div>
        </section>

        <!-- Stats -->
        <section
            class="px-4 md:px-6 py-10 md:py-14"
            style="background-color: var(--c1)"
            ref="statsRef"
        >
            <div class="max-w-5xl mx-auto">
                <div
                    class="reveal-up"
                    :class="{ 'reveal-up--visible': statsVisible }"
                >
                    <p
                        class="text-sm font-semibold mb-5"
                        style="color: var(--t1); opacity: 0.72"
                    >
                        {{ t("entreprise.stats_label") }}
                    </p>
                    <div class="flex flex-wrap gap-6 gap-x-12">
                        <div
                            v-if="entreprise.eligible_count"
                            style="transition-delay: 80ms"
                        >
                            <div
                                class="font-bold leading-none mb-1"
                                style="
                                    color: var(--t1);
                                    font-size: clamp(2rem, 5vw, 3rem);
                                "
                            >
                                {{ entreprise.eligible_count.toLocaleString() }}
                            </div>
                            <div
                                class="text-sm"
                                style="color: var(--t1); opacity: 0.72"
                            >
                                {{ t("home.stats_eligible") }}
                            </div>
                        </div>
                        <div
                            v-if="entreprise.submissions_count"
                            style="transition-delay: 160ms"
                        >
                            <div
                                class="font-bold leading-none mb-1"
                                style="
                                    color: var(--t1);
                                    font-size: clamp(2rem, 5vw, 3rem);
                                "
                            >
                                {{
                                    entreprise.submissions_count.toLocaleString()
                                }}
                            </div>
                            <div
                                class="text-sm"
                                style="color: var(--t1); opacity: 0.72"
                            >
                                {{ t("entreprise.stats_quiz") }}
                            </div>
                        </div>
                        <div
                            v-if="entreprise.employee_count"
                            style="transition-delay: 240ms"
                        >
                            <div
                                class="font-bold leading-none mb-1"
                                style="
                                    color: var(--t1);
                                    font-size: clamp(2rem, 5vw, 3rem);
                                "
                            >
                                {{ entreprise.employee_count.toLocaleString() }}
                            </div>
                            <div
                                class="text-sm"
                                style="color: var(--t1); opacity: 0.72"
                            >
                                {{ t("entreprise.employees") }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Corkboard interactif -->
        <section class="py-8 md:py-10 px-2 md:px-6 bg-base-100" ref="faqRef">
            <div
                class="max-w-5xl mx-auto reveal-up"
                :class="{ 'reveal-up--visible': faqVisible }"
            >
                <CorkboardFaq :primary-color="c1" :secondary-color="c2" />
            </div>
        </section>

        <!-- CTA quiz -->
        <section
            class="py-10 md:py-16 px-4 md:px-6"
            style="background-color: var(--c1)"
            ref="ctaERef"
        >
            <div class="max-w-5xl mx-auto">
                <h2
                    class="font-bold mb-6 md:mb-10 leading-tight reveal-up"
                    :class="{ 'reveal-up--visible': ctaEVisible }"
                    style="
                        color: var(--t1);
                        font-size: clamp(1.4rem, 3vw, 2.25rem);
                        overflow-wrap: break-word;
                    "
                >
                    {{ t("entreprise.cta_section_title") }}
                </h2>
                <div
                    class="grid md:grid-cols-[1.2fr_0.8fr] gap-8 md:gap-16 items-start"
                    :style="`border-top: 1px solid color-mix(in srgb, var(--t1) 22%, transparent);`"
                >
                    <div
                        class="pt-6 md:pt-8 space-y-3 reveal-up"
                        :class="{ 'reveal-up--visible': ctaEVisible }"
                        style="transition-delay: 80ms"
                    >
                        <p
                            class="font-semibold leading-snug"
                            style="
                                color: var(--t1);
                                font-size: clamp(1rem, 2vw, 1.15rem);
                            "
                        >
                            {{ t("entreprise.cta_section_line1") }}
                        </p>
                        <p
                            class="font-semibold leading-snug"
                            style="
                                color: var(--t1);
                                font-size: clamp(1rem, 2vw, 1.15rem);
                            "
                        >
                            {{ t("entreprise.cta_section_line2") }}
                        </p>
                        <p
                            class="font-semibold leading-snug"
                            style="
                                color: var(--t1);
                                opacity: 0.82;
                                font-size: clamp(1rem, 2vw, 1.15rem);
                            "
                        >
                            {{ t("entreprise.cta_section_line3") }}
                        </p>
                        <a
                            href="https://www.hug.ch/don-du-sang"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn border-2 bg-transparent font-semibold px-6 rounded-sm active:scale-[0.97] w-full md:w-auto mt-4"
                            style="border-color: var(--t1); color: var(--t1)"
                        >
                            {{ t("entreprise.learn_more_cta") }}
                        </a>
                    </div>
                    <div
                        class="pt-6 md:pt-8 reveal-up"
                        :class="{ 'reveal-up--visible': ctaEVisible }"
                        style="transition-delay: 160ms"
                    >
                        <p
                            class="text-sm mb-8 leading-relaxed"
                            style="
                                color: var(--t1);
                                opacity: 0.82;
                                max-width: 42ch;
                            "
                        >
                            {{
                                t("entreprise.cta_section_description", {
                                    company: entreprise.name,
                                })
                            }}
                        </p>
                        <a
                            :href="`/c/${entreprise.access_token}/quiz`"
                            class="btn border-none font-semibold px-6 md:px-10 rounded-sm active:scale-[0.97] w-full md:w-auto"
                            style="
                                background-color: var(--t1);
                                color: var(--c1);
                            "
                        >
                            {{ t("entreprise.quiz_discover") }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Collectes précédentes -->
        <section
            v-if="pastCollectes.length"
            class="py-10 md:py-14 px-4 md:px-6 bg-base-100 border-t border-base-200"
        >
            <div class="max-w-5xl mx-auto">
                <h2
                    class="font-bold mb-6"
                    style="font-size: clamp(1.2rem, 2.5vw, 1.75rem)"
                >
                    <span style="color: var(--c1)">{{
                        t("entreprise.collectes_past_highlight")
                    }}</span>
                    {{ t("entreprise.collectes_past_title") }}
                </h2>
                <div class="flex flex-col gap-3">
                    <div
                        v-for="c in pastCollectes"
                        :key="c.id"
                        class="flex items-center gap-4 rounded-xl border border-base-200 bg-base-100 px-4 py-4 opacity-60"
                    >
                        <!-- Badge date grisé -->
                        <div
                            class="shrink-0 rounded-lg w-14 text-center py-2 bg-base-300 text-base-content/50"
                        >
                            <div
                                class="text-2xl font-extrabold leading-none tabular-nums"
                            >
                                {{ c.rdv_date ? dayNum(c.rdv_date) : "—" }}
                            </div>
                            <div
                                class="text-[0.6rem] uppercase tracking-wider mt-0.5"
                            >
                                {{ c.rdv_date ? monthShort(c.rdv_date) : "" }}
                            </div>
                        </div>

                        <!-- Infos -->
                        <div class="flex-1 min-w-0">
                            <div
                                class="font-semibold text-base-content/70 leading-snug truncate"
                            >
                                {{
                                    c.label ||
                                    t("entreprise.collecte_default_label")
                                }}
                            </div>
                            <div
                                v-if="c.rdv_date"
                                class="text-xs text-base-content/40 mt-0.5"
                            >
                                {{ fullDate(c.rdv_date) }}
                            </div>
                        </div>

                        <!-- Badge terminée -->
                        <span
                            class="shrink-0 badge badge-ghost badge-sm uppercase tracking-wide font-semibold text-base-content/50"
                        >
                            {{ t("entreprise.collecte_past_badge") }}
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <Footer />
    </div>
</template>

<style scoped>
.collecte-card {
    transition:
        transform 180ms var(--ease-out),
        box-shadow 180ms var(--ease-out);
}
@media (hover: hover) and (pointer: fine) {
    .collecte-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18);
    }
}
.collecte-card:active {
    transform: scale(0.98);
    transition-duration: 100ms;
}

.entreprise-hero-img {
    animation: inspector-enter 600ms var(--ease-out) both;
    animation-delay: 200ms;
}
@media (min-width: 768px) {
    .entreprise-hero-img {
        width: 50%;
        top: 0;
        right: 0;
        bottom: 0;
        left: auto;
    }
}
@keyframes inspector-enter {
    from {
        opacity: 0;
        transform: scale(1.04);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
@media (prefers-reduced-motion: reduce) {
    .entreprise-hero-img {
        animation: none;
    }
}

@media (max-width: 767px) {
    .hero-text-bg {
        background-color: color-mix(in srgb, var(--c1) 85%, transparent);
        border-radius: 1rem;
        padding: 1.25rem 1.25rem 1.5rem;
    }
}
@media (max-width: 767px) {
    .hero-text-bg {
        background-color: color-mix(in srgb, var(--c1) 85%, transparent);
        border-radius: 1rem;
        padding: 1.25rem 1.25rem 1.5rem;
    }
}

.btn-co {
    background-color: var(--c1);
    color: var(--t1);
}
.btn-co:hover {
    filter: brightness(0.88);
}
</style>

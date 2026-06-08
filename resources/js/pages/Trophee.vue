<script setup>
import { computed, ref, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import NavBar from "../components/ui/NavBar.vue";
import LogoContainer from "../components/ui/LogoContainer.vue";
import Footer from "../components/ui/Footer.vue";
import PageHero from "../components/ui/PageHero.vue";

const { t } = useI18n();

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

const criteriaRef = ref(null);
const criteriaVisible = ref(false);
const rankRef = ref(null);
const rankVisible = ref(false);
const palmRef = ref(null);
const palmVisible = ref(false);
const downloadRef = ref(null);
const downloadVisible = ref(false);
const ctaRef = ref(null);
const ctaVisible = ref(false);

onMounted(() => {
    makeObserver(criteriaRef, criteriaVisible, 0.1);
    makeObserver(rankRef, rankVisible, 0.1);
    makeObserver(palmRef, palmVisible, 0.1);
    makeObserver(downloadRef, downloadVisible, 0.15);
    makeObserver(ctaRef, ctaVisible, 0.2);
});

const props = defineProps({ winners: Array });

const winner1 = computed(
    () => (props.winners ?? []).find((w) => w.trophy_rank === 1) ?? null,
);
const others = computed(() =>
    (props.winners ?? [])
        .filter((w) => w.trophy_rank > 1)
        .sort((a, b) => a.trophy_rank - b.trophy_rank),
);

const previousWinners = [
    {
        year: 2025,
        name: "Rolex SA",
        type: "horlogerie",
        eligible: 142,
        rate: "68 %",
        color: "#006039",
        logo: "/images/palmares-logos/rolex.svg",
        jury: "94 %",
        employees: 5800,
        victories: 5,
    },
    {
        year: 2024,
        name: "Groupe Mutuel",
        type: "assurance",
        eligible: 389,
        rate: "78 %",
        color: "#003F87",
        logo: "/images/palmares-logos/groupe-mutuel.png",
    },
    {
        year: 2023,
        name: "Banque Cantonale de Genève",
        type: "banque",
        eligible: 201,
        rate: "82 %",
        color: "#00205B",
        logo: "/images/palmares-logos/bcge.svg",
    },
    {
        year: 2022,
        name: "SIG : Services Industriels Genève",
        type: "services_publics",
        eligible: 310,
        rate: "71 %",
        color: "#00A650",
        logo: "/images/palmares-logos/sig.svg",
    },
    {
        year: 2021,
        name: "Kudelski Group",
        type: "technologie",
        eligible: 245,
        rate: "68 %",
        color: "#C8001A",
        logo: "/images/palmares-logos/kudelski.svg",
    },
    {
        year: 2020,
        name: "Givaudan",
        type: "industrie",
        eligible: 433,
        rate: "74 %",
        color: "#0072CE",
        logo: "/images/palmares-logos/givaudan.svg",
    },
    {
        year: 2019,
        name: "Swissquote Bank",
        type: "fintech",
        eligible: 178,
        rate: "63 %",
        color: "#FF6600",
        logo: "/images/palmares-logos/swissquote.svg",
    },
];

const criteria = computed(() =>
    [0, 1, 2, 3].map((i) => ({
        title: t(`trophee.criteria_${i}_title`),
        desc: t(`trophee.criteria_${i}_desc`),
    })),
);

const criteriaIcons = [
    // Taux de participation : pourcentage
    '<line x1="19" y1="5" x2="5" y2="19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/>',
    // Fidélité : répétition
    '<path d="M17 2l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 22l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/>',
    // Impact : goutte de sang
    '<path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/>',
    // Engagement interne : collaborateurs
    '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
];
</script>

<template>
    <div class="min-h-screen bg-base-100 flex flex-col">
        <NavBar />

        <PageHero
            :title="t('trophee.title')"
            :subtitle="t('trophee.subtitle')"
            :cta="{ label: t('home.register_cta'), href: '/inscription' }"
        >
            <template #visual>
                <img
                    :src="'/images/trophee-rouge.svg'"
                    alt="Trophée HUG"
                    class="w-4/5 mx-auto h-auto object-contain"
                    decoding="async"
                />
            </template>
        </PageHero>

        <!-- Critères -->
        <section class="py-16 px-6 bg-brand text-white" ref="criteriaRef">
            <div class="max-w-5xl mx-auto">
                <h2
                    class="font-bold mb-3 leading-tight reveal-up"
                    :class="{ 'reveal-up--visible': criteriaVisible }"
                    style="font-size: clamp(1.5rem, 3vw, 2.25rem)"
                >
                    {{ t("trophee.how_title") }}
                </h2>
                <p
                    class="text-white/80 mb-10 reveal-up"
                    :class="{ 'reveal-up--visible': criteriaVisible }"
                    style="max-width: 52ch; transition-delay: 80ms"
                >
                    {{ t("trophee.how_text") }}
                </p>

                <div
                    class="grid grid-cols-1 sm:grid-cols-2 gap-x-16 gap-y-7 mb-8"
                >
                    <div
                        v-for="(c, i) in criteria"
                        :key="c.title"
                        class="flex items-start gap-5 reveal-up"
                        :class="{ 'reveal-up--visible': criteriaVisible }"
                        :style="`transition-delay: ${(i + 2) * 90}ms`"
                    >
                        <span class="criteria-num" aria-hidden="true"
                            >0{{ i + 1 }}</span
                        >
                        <div>
                            <h3 class="font-bold mb-1">{{ c.title }}</h3>
                            <p class="text-sm text-white/70 leading-relaxed">
                                {{ c.desc }}
                            </p>
                        </div>
                    </div>
                </div>

                <p
                    class="text-sm text-white/50 italic border-t border-white/15 pt-6 reveal-up"
                    :class="{ 'reveal-up--visible': criteriaVisible }"
                    style="max-width: 58ch; transition-delay: 500ms"
                >
                    {{ t("trophee.how_note") }}
                </p>
            </div>
        </section>

        <!-- Classement édition en cours -->
        <main class="max-w-5xl mx-auto px-6 py-12 flex-1 w-full" ref="rankRef">
            <div
                v-if="!winners?.length"
                class="text-base-content/50 py-8 reveal-up"
                :class="{ 'reveal-up--visible': rankVisible }"
            >
                {{ t("trophee.no_winners") }}
            </div>

            <template v-if="winners?.length">
                <h2
                    class="font-bold mb-1 reveal-up"
                    :class="{ 'reveal-up--visible': rankVisible }"
                    style="font-size: clamp(1.35rem, 2.5vw, 1.75rem)"
                >
                    {{ t("trophee.rank_title") }}
                    <span class="text-brand">{{
                        new Date().getFullYear()
                    }}</span>
                </h2>
                <p
                    class="text-base-content/50 text-sm mb-6 reveal-up"
                    :class="{ 'reveal-up--visible': rankVisible }"
                    style="transition-delay: 60ms"
                >
                    {{ t("trophee.rank_subtitle") }}
                </p>
            </template>

            <template v-if="others.length || winner1">
                <div class="ranking-card">
                    <!-- PODIUM top 3 -->
                    <div
                        class="podium-wrap"
                        v-if="[winner1, ...others].filter(Boolean).length >= 1"
                    >
                        <div class="podium-stage">
                            <!-- #2 -->
                            <div
                                class="podium-slot podium-slot--2"
                                v-if="others[0]"
                            >
                                <LogoContainer
                                    :logo-url="others[0].logo_url"
                                    :primary-color="others[0].primary_color"
                                    :name="others[0].name"
                                    size="w-[72px] h-[72px]"
                                    rounded="rounded-xl"
                                    class="podium-avatar"
                                />
                                <p class="podium-name">{{ others[0].name }}</p>
                                <div class="podium-block podium-block--2">
                                    <span class="podium-rank">2</span>
                                </div>
                            </div>
                            <!-- #1 -->
                            <div
                                class="podium-slot podium-slot--1"
                                v-if="winner1"
                            >
                                <LogoContainer
                                    :logo-url="winner1.logo_url"
                                    :primary-color="winner1.primary_color"
                                    :name="winner1.name"
                                    size="w-[88px] h-[88px]"
                                    rounded="rounded-xl"
                                    class="podium-avatar podium-avatar--1"
                                />
                                <p class="podium-name podium-name--1">
                                    {{ winner1.name }}
                                </p>
                                <div class="podium-block podium-block--1">
                                    <span class="podium-rank">1</span>
                                </div>
                            </div>
                            <!-- #3 -->
                            <div
                                class="podium-slot podium-slot--3"
                                v-if="others[1]"
                            >
                                <LogoContainer
                                    :logo-url="others[1].logo_url"
                                    :primary-color="others[1].primary_color"
                                    :name="others[1].name"
                                    size="w-[72px] h-[72px]"
                                    rounded="rounded-xl"
                                    class="podium-avatar"
                                />
                                <p class="podium-name">{{ others[1].name }}</p>
                                <div class="podium-block podium-block--3">
                                    <span class="podium-rank">3</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Liste #4+ -->
                    <ul v-if="others.length > 2" class="ranking-list">
                        <li
                            v-for="w in others.slice(2)"
                            :key="w.id"
                            class="ranking-row"
                        >
                            <span class="ranking-row-num">{{
                                w.trophy_rank
                            }}</span>
                            <LogoContainer
                                :logo-url="w.logo_url"
                                :primary-color="w.primary_color"
                                :name="w.name"
                                size="w-10 h-10"
                                rounded="rounded-xl"
                            />
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-sm truncate">
                                    {{ w.name }}
                                </div>
                                <div
                                    v-if="w.type"
                                    class="text-xs text-base-content/40"
                                >
                                    {{ t("inscription.type_" + w.type) }}
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </template>
        </main>

        <!-- Palmarès -->
        <section class="py-16 px-6 bg-base-200" ref="palmRef">
            <div class="max-w-5xl mx-auto">
                <h2
                    class="font-bold mb-1 reveal-up"
                    :class="{ 'reveal-up--visible': palmVisible }"
                    style="font-size: clamp(1.35rem, 2.5vw, 1.75rem)"
                >
                    {{ t("trophee.palmares_title") }}
                </h2>
                <p
                    class="text-base-content/50 text-sm mb-10 reveal-up"
                    :class="{ 'reveal-up--visible': palmVisible }"
                    style="transition-delay: 60ms"
                >
                    {{ t("trophee.palmares_subtitle") }}
                </p>

                <!-- Gagnant 2025 -->
                <div
                    class="bg-white rounded-xl px-8 py-8 mb-4 reveal-up"
                    :class="{ 'reveal-up--visible': palmVisible }"
                    style="transition-delay: 130ms"
                >
                    <div class="flex items-center gap-2 mb-6">
                        <span
                            class="text-xs uppercase tracking-[0.2em] text-brand font-semibold"
                        >
                            {{ t("trophee.winner_label") }}
                        </span>
                        <span class="text-xs text-base-content/35">{{
                            previousWinners[0].year
                        }}</span>
                    </div>
                    <div
                        class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 sm:gap-6"
                    >
                        <div class="flex items-center gap-4">
                            <img
                                v-if="previousWinners[0].logo"
                                :src="previousWinners[0].logo"
                                :alt="previousWinners[0].name"
                                class="w-12 h-12 sm:w-20 sm:h-20 rounded-xl object-contain bg-white border border-base-300 p-1.5 sm:p-2.5 shrink-0"
                                loading="lazy"
                                decoding="async"
                            />
                            <div
                                v-else
                                class="w-12 h-12 sm:w-20 sm:h-20 rounded-xl flex items-center justify-center text-white font-bold text-xl sm:text-3xl shrink-0"
                                :style="`background-color: ${previousWinners[0].color}`"
                            >
                                {{ previousWinners[0].name[0] }}
                            </div>
                            <div>
                                <h3
                                    class="font-bold"
                                    style="
                                        font-size: clamp(1.1rem, 2vw, 1.4rem);
                                    "
                                >
                                    {{ previousWinners[0].name }}
                                </h3>
                                <p class="text-sm text-base-content/40">
                                    {{
                                        t(
                                            "inscription.type_" +
                                                previousWinners[0].type,
                                        )
                                    }}
                                </p>
                            </div>
                        </div>
                        <div
                            class="grid grid-cols-[auto_1fr] items-center gap-x-3 gap-y-2 w-full sm:flex sm:flex-row sm:gap-8 sm:w-auto sm:shrink-0"
                        >
                            <div class="contents sm:block sm:text-right">
                                <div class="text-2xl font-bold text-brand">
                                    {{ previousWinners[0].eligible }}
                                </div>
                                <div
                                    class="text-xs text-base-content/40 sm:mt-0.5"
                                >
                                    {{ t("trophee.stat_donations") }}
                                </div>
                            </div>
                            <div class="contents sm:block sm:text-right">
                                <div class="text-2xl font-bold">
                                    {{ previousWinners[0].rate }}
                                </div>
                                <div
                                    class="text-xs text-base-content/40 sm:mt-0.5"
                                >
                                    {{ t("trophee.stat_participation") }}
                                </div>
                            </div>
                            <div class="contents sm:block sm:text-right">
                                <div class="text-2xl font-bold">
                                    {{ previousWinners[0].victories
                                    }}<sup class="text-sm">e</sup>
                                </div>
                                <div
                                    class="text-xs text-base-content/40 sm:mt-0.5"
                                >
                                    {{ t("trophee.stat_consecutive") }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Éditions précédentes -->
                <div
                    class="divide-y divide-base-300 bg-white rounded-xl overflow-hidden reveal-up"
                    :class="{ 'reveal-up--visible': palmVisible }"
                    style="transition-delay: 220ms"
                >
                    <div
                        v-for="w in previousWinners.slice(1)"
                        :key="w.year"
                        class="grid grid-cols-[2.5rem_1fr] sm:grid-cols-[3.5rem_1fr_auto] items-center gap-x-3 sm:gap-x-4 gap-y-2 px-5 sm:px-6 py-4"
                    >
                        <span class="text-xs text-base-content/35">{{
                            w.year
                        }}</span>
                        <div class="flex items-center gap-3 min-w-0">
                            <img
                                v-if="w.logo"
                                :src="w.logo"
                                :alt="w.name"
                                class="w-7 h-7 sm:w-12 sm:h-12 rounded-md object-contain bg-white border border-base-300 p-1 sm:p-1.5 shrink-0"
                                loading="lazy"
                                decoding="async"
                            />
                            <span
                                v-else
                                class="w-2.5 h-2.5 rounded-full shrink-0"
                                :style="`background-color: ${w.color}`"
                            ></span>
                            <div class="min-w-0">
                                <div
                                    class="font-semibold text-sm leading-tight truncate"
                                >
                                    {{ w.name }}
                                </div>
                                <div class="text-xs text-base-content/40">
                                    {{ t("inscription.type_" + w.type) }}
                                </div>
                            </div>
                        </div>
                        <div
                            class="col-span-2 sm:col-span-1 flex gap-6 pl-[5.75rem] sm:pl-0 sm:text-right"
                        >
                            <div>
                                <div class="text-sm font-bold text-brand">
                                    {{ w.eligible }}
                                </div>
                                <div class="text-xs text-base-content/35">
                                    {{ t("trophee.stat_donations") }}
                                </div>
                            </div>
                            <div>
                                <div class="text-sm font-bold">
                                    {{ w.rate }}
                                </div>
                                <div class="text-xs text-base-content/35">
                                    {{ t("trophee.stat_participation") }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Téléchargement du visuel -->
        <section class="py-16 px-6 bg-base-200 border-t border-base-300" ref="downloadRef">
            <div
                class="max-w-5xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6"
            >
                <div
                    class="reveal-up"
                    :class="{ 'reveal-up--visible': downloadVisible }"
                >
                    <h2
                        class="font-bold mb-2 leading-tight"
                        style="font-size: clamp(1.35rem, 2.5vw, 1.85rem)"
                    >
                        {{ t("trophee.download_section_title") }}
                    </h2>
                    <p
                        class="text-base-content/55 text-sm leading-relaxed"
                        style="max-width: 50ch"
                    >
                        {{ t("trophee.download_section_text") }}
                    </p>
                </div>
                <a
                    href="/images/trophee-rouge.svg"
                    download="trophee-sangsationnel.svg"
                    class="btn bg-brand hover:bg-brand-dark text-white border-none font-semibold px-8 rounded-sm shrink-0 reveal-up active:scale-[0.97]"
                    :class="{ 'reveal-up--visible': downloadVisible }"
                    style="transition-delay: 80ms"
                >
                    {{ t("trophee.download_visual") }}
                </a>
            </div>
        </section>

        <!-- CTA candidatures -->
        <section class="py-20 px-6 border-t border-base-200" ref="ctaRef">
            <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-10 items-end">
                <div>
                    <h2
                        class="font-bold mb-3 leading-tight reveal-up"
                        :class="{ 'reveal-up--visible': ctaVisible }"
                        style="font-size: clamp(1.75rem, 4vw, 2.75rem)"
                    >
                        {{ t("trophee.cta_section_title") }}
                    </h2>
                    <p
                        class="text-brand font-bold italic mb-6 reveal-up"
                        :class="{ 'reveal-up--visible': ctaVisible }"
                        style="
                            font-size: clamp(1.1rem, 2vw, 1.35rem);
                            transition-delay: 80ms;
                        "
                    >
                        {{ t("trophee.cta_italic") }}
                    </p>
                </div>
                <div>
                    <p
                        class="text-base-content/60 mb-8 leading-relaxed reveal-up"
                        :class="{ 'reveal-up--visible': ctaVisible }"
                        style="max-width: 46ch; transition-delay: 160ms"
                    >
                        {{ t("trophee.cta_section_description") }}
                    </p>
                    <a
                        href="/inscription"
                        class="btn bg-black hover:bg-black/80 text-white border-none font-semibold px-8 rounded-sm reveal-up active:scale-[0.97]"
                        :class="{ 'reveal-up--visible': ctaVisible }"
                        style="transition-delay: 240ms"
                    >
                        {{ t("trophee.cta_button") }}
                    </a>
                </div>
            </div>
        </section>

        <Footer />
    </div>
</template>

<style scoped>
.ranking-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1.5px solid rgba(0, 0, 0, 0.1);
    padding: 0 1.5rem 1rem;
    margin-bottom: 2rem;
}

.ranking-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 0 0 0.75rem;
}
.ranking-row {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #f7f7f7;
    border-radius: 12px;
    padding: 10px 14px;
}
.ranking-row-num {
    font-size: 0.8rem;
    font-weight: 700;
    color: rgba(0, 0, 0, 0.28);
    min-width: 18px;
    text-align: center;
}

.podium-wrap {
    margin: 2rem 0 1.5rem;
}
.podium-stage {
    display: flex;
    align-items: flex-end;
    justify-content: center;
    gap: 6px;
}

.podium-slot {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    max-width: 220px;
}

.podium-avatar {
    margin-bottom: 10px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
    flex-shrink: 0;
}
.podium-avatar--1 {
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
}
.podium-avatar-img {
    max-height: 100%;
    max-width: 100%;
    object-fit: contain;
}
.podium-avatar-fallback {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 800;
    font-size: 1.5rem;
    border-radius: 10px;
}

.podium-crown {
    font-size: 1.4rem;
    margin-bottom: 4px;
    color: #e8c84a;
    filter: drop-shadow(0 2px 4px rgba(232, 200, 74, 0.4));
    line-height: 1;
}

.podium-name {
    font-size: 0.75rem;
    font-weight: 600;
    text-align: center;
    color: #374151;
    margin-bottom: 8px;
    line-height: 1.3;
    max-width: 130px;
}
.podium-name--1 {
    font-size: 0.82rem;
    font-weight: 700;
}

.podium-block {
    width: 100%;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    border-radius: 8px 8px 0 0;
    padding-bottom: 12px;
}
.podium-block--1 {
    height: 140px;
    background: var(--color-brand);
}
.podium-block--2 {
    height: 104px;
    background: #ffffff;
    border: 2px solid var(--color-brand);
}
.podium-block--3 {
    height: 76px;
    background: #ffffff;
    border: 2px solid var(--color-brand);
}
.podium-block--2 .podium-rank,
.podium-block--3 .podium-rank {
    color: var(--color-brand);
}
.podium-rank {
    font-size: 3rem;
    font-weight: 800;
    color: #ffffff;
    line-height: 1;
    letter-spacing: -0.04em;
    font-variant-numeric: tabular-nums;
}

.podium-trophy {
    width: 44px;
    height: auto;
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.25));
}
.podium-trophy--1 {
    width: 54px;
}

.criteria-num {
    display: block;
    font-weight: 800;
    line-height: 1;
    font-size: clamp(2.5rem, 5vw, 3.75rem);
    font-variant-numeric: tabular-nums;
    color: rgba(255, 255, 255, 0.35);
    flex-shrink: 0;
    width: 3.5rem;
}
</style>

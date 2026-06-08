<script setup>
import { ref, computed, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import NavBar from "../components/ui/NavBar.vue";
import Footer from "../components/ui/Footer.vue";
import PageHero from "../components/ui/PageHero.vue";
import LabelCard from "../components/label/LabelCard.vue";
import SectorFilter from "../components/label/SectorFilter.vue";

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

const sectionRef = ref(null);
const sectionVisible = ref(false);
const gridRef = ref(null);
const gridVisible = ref(false);
const ctaRef = ref(null);
const ctaVisible = ref(false);

onMounted(() => {
    makeObserver(sectionRef, sectionVisible, 0.1);
    makeObserver(gridRef, gridVisible, 0.08);
    makeObserver(ctaRef, ctaVisible, 0.15);
});

const props = defineProps({ entreprises: Array });

const selectedSector = ref(null);

const sectors = computed(() => {
    const all = (props.entreprises ?? []).map((e) => e.type).filter(Boolean);
    return [...new Set(all)].sort();
});

const filtered = computed(() => {
    if (!selectedSector.value) return props.entreprises ?? [];
    return (props.entreprises ?? []).filter(
        (e) => e.type === selectedSector.value,
    );
});

const colorSwatches = computed(() =>
    (props.entreprises ?? [])
        .slice(0, 30)
        .map((e) => e.primary_color)
        .filter(Boolean),
);
</script>

<template>
    <div class="min-h-screen bg-base-100 flex flex-col">
        <NavBar />

        <PageHero
            title-html="Label<br>100 pour Sang"
            :subtitle="t('label.subtitle')"
            :cta="{ label: t('label.hero_cta'), href: '/inscription' }"
        >
            <template #visual>
                <img
                    :src="'/images/label-plein.svg'"
                    alt="Label HUG"
                    class="w-2/3 max-w-xs mx-auto h-auto object-contain"
                    decoding="async"
                />
            </template>
        </PageHero>

        <!-- Explication -->
        <section class="py-16 px-6 bg-brand text-white" ref="sectionRef">
            <div
                class="max-w-5xl mx-auto grid md:grid-cols-2 gap-10 items-start"
            >
                <h2
                    class="font-bold leading-tight reveal-up"
                    :class="{ 'reveal-up--visible': sectionVisible }"
                    style="font-size: clamp(1.5rem, 3vw, 2.25rem)"
                >
                    {{ t("label.section_title") }}
                </h2>
                <div class="space-y-4">
                    <p
                        class="text-white/80 leading-relaxed reveal-up"
                        :class="{ 'reveal-up--visible': sectionVisible }"
                        style="transition-delay: 100ms"
                    >
                        {{ t("label.section_text_1") }}
                    </p>
                    <p
                        class="text-white/80 leading-relaxed reveal-up"
                        :class="{ 'reveal-up--visible': sectionVisible }"
                        style="transition-delay: 180ms"
                    >
                        {{ t("label.section_text_2") }}
                    </p>
                </div>
            </div>
        </section>

        <main class="max-w-5xl mx-auto px-6 py-12 flex-1 w-full" ref="gridRef">
            <!-- Compteur -->
            <div
                class="mb-8 reveal-up"
                :class="{ 'reveal-up--visible': gridVisible }"
            >
                <div
                    class="font-extrabold leading-none mb-2"
                    style="font-size: clamp(3rem, 8vw, 5rem)"
                >
                    {{ filtered.length }}
                </div>
                <div class="text-base-content/45 text-sm">
                    {{ t("label.count_label") }}
                </div>
            </div>

            <!-- Filtre secteur -->
            <div
                class="mb-6 reveal-up"
                :class="{ 'reveal-up--visible': gridVisible }"
                style="transition-delay: 80ms"
            >
                <SectorFilter
                    v-if="sectors.length"
                    :sectors="sectors"
                    v-model="selectedSector"
                />
            </div>

            <div
                v-if="!filtered.length"
                class="text-base-content/50 py-4 reveal-up"
                :class="{ 'reveal-up--visible': gridVisible }"
                style="transition-delay: 140ms"
            >
                {{ t("label.no_label") }}
            </div>
            <div
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 reveal-up"
                :class="{ 'reveal-up--visible': gridVisible }"
                style="transition-delay: 140ms"
            >
                <LabelCard v-for="e in filtered" :key="e.id" :entreprise="e" />
            </div>
        </main>

        <!-- CTA -->
        <section class="py-20 px-6 border-t border-base-200" ref="ctaRef">
            <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-10 items-end">
                <div>
                    <h2
                        class="font-bold mb-3 leading-tight reveal-up"
                        :class="{ 'reveal-up--visible': ctaVisible }"
                        style="font-size: clamp(1.75rem, 4vw, 2.75rem)"
                    >
                        {{ t("label.cta_section_title") }}
                    </h2>
                    <p
                        class="text-brand font-bold italic mb-6 reveal-up"
                        :class="{ 'reveal-up--visible': ctaVisible }"
                        style="
                            font-size: clamp(1.1rem, 2vw, 1.35rem);
                            transition-delay: 80ms;
                        "
                    >
                        {{ t("label.cta_italic") }}
                    </p>
                </div>
                <div>
                    <p
                        class="text-base-content/60 mb-8 leading-relaxed reveal-up"
                        :class="{ 'reveal-up--visible': ctaVisible }"
                        style="max-width: 46ch; transition-delay: 160ms"
                    >
                        {{ t("label.cta_section_description") }}
                    </p>
                    <a
                        href="/inscription"
                        class="btn bg-black hover:bg-black/80 text-white border-none font-semibold px-8 rounded-sm reveal-up active:scale-[0.97]"
                        :class="{ 'reveal-up--visible': ctaVisible }"
                        style="transition-delay: 240ms"
                    >
                        {{ t("label.cta_button") }}
                    </a>
                </div>
            </div>
        </section>

        <Footer />
    </div>
</template>

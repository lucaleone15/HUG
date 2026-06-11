<script setup>
import { useI18n } from "vue-i18n";
import NavBar from "../components/ui/NavBar.vue";
import Footer from "../components/ui/Footer.vue";

const { t } = useI18n();

const cookies = [0, 1].map((i) => ({
    name: t(`privacy.cookies_${i}_name`),
    purpose: t(`privacy.cookies_${i}_purpose`),
    duration: t(`privacy.cookies_${i}_duration`),
}));
</script>

<template>
    <div class="min-h-screen bg-base-100 flex flex-col">
        <NavBar />

        <main class="max-w-3xl mx-auto px-6 py-16 flex-1 w-full">
            <div class="mb-12 page-hero-text">
                <h1
                    class="font-extrabold leading-tight text-base-content mb-2"
                    style="font-size: clamp(2rem, 5vw, 3.25rem)"
                >
                    {{ t("privacy.title") }}
                </h1>
                <p class="text-base-content/60">{{ t("privacy.subtitle") }}</p>
                <p class="text-xs text-base-content/40 mt-3">{{ t("privacy.updated") }}</p>
            </div>

            <div class="space-y-10 border-t border-base-200 pt-10">
                <section>
                    <h2 class="font-bold mb-2" style="font-size: clamp(1.1rem, 2vw, 1.4rem)">
                        {{ t("privacy.intro_title") }}
                    </h2>
                    <p class="text-base-content/70 leading-relaxed">{{ t("privacy.intro_text") }}</p>
                </section>

                <section>
                    <h2 class="font-bold mb-2" style="font-size: clamp(1.1rem, 2vw, 1.4rem)">
                        {{ t("privacy.controller_title") }}
                    </h2>
                    <p class="text-base-content/70 leading-relaxed">{{ t("privacy.controller_text") }}</p>
                </section>

                <section>
                    <h2 class="font-bold mb-2" style="font-size: clamp(1.1rem, 2vw, 1.4rem)">
                        {{ t("privacy.quiz_title") }}
                    </h2>
                    <p class="text-base-content/70 leading-relaxed">{{ t("privacy.quiz_text") }}</p>
                </section>

                <section>
                    <h2 class="font-bold mb-2" style="font-size: clamp(1.1rem, 2vw, 1.4rem)">
                        {{ t("privacy.inscription_title") }}
                    </h2>
                    <p class="text-base-content/70 leading-relaxed">{{ t("privacy.inscription_text") }}</p>
                </section>

                <section>
                    <h2 class="font-bold mb-2" style="font-size: clamp(1.1rem, 2vw, 1.4rem)">
                        {{ t("privacy.contact_title") }}
                    </h2>
                    <p class="text-base-content/70 leading-relaxed">{{ t("privacy.contact_text") }}</p>
                </section>

                <section>
                    <h2 class="font-bold mb-2" style="font-size: clamp(1.1rem, 2vw, 1.4rem)">
                        {{ t("privacy.analytics_title") }}
                    </h2>
                    <p class="text-base-content/70 leading-relaxed">{{ t("privacy.analytics_text") }}</p>
                </section>

                <section>
                    <h2 class="font-bold mb-2" style="font-size: clamp(1.1rem, 2vw, 1.4rem)">
                        {{ t("privacy.cookies_title") }}
                    </h2>
                    <p class="text-base-content/70 leading-relaxed mb-4">{{ t("privacy.cookies_intro") }}</p>

                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ t("privacy.cookies_table_name") }}</th>
                                    <th>{{ t("privacy.cookies_table_purpose") }}</th>
                                    <th>{{ t("privacy.cookies_table_duration") }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="c in cookies" :key="c.name">
                                    <td class="font-mono text-sm whitespace-nowrap">{{ c.name }}</td>
                                    <td class="text-base-content/70">{{ c.purpose }}</td>
                                    <td class="text-base-content/70 whitespace-nowrap">{{ c.duration }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section>
                    <h2 class="font-bold mb-2" style="font-size: clamp(1.1rem, 2vw, 1.4rem)">
                        {{ t("privacy.rights_title") }}
                    </h2>
                    <p class="text-base-content/70 leading-relaxed">{{ t("privacy.rights_text") }}</p>
                </section>

                <section>
                    <h2 class="font-bold mb-2" style="font-size: clamp(1.1rem, 2vw, 1.4rem)">
                        {{ t("privacy.academic_title") }}
                    </h2>
                    <p class="text-base-content/70 leading-relaxed">{{ t("privacy.academic_text") }}</p>
                </section>
            </div>
        </main>

        <Footer />
    </div>
</template>

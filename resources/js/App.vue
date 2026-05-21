<script setup>
import { useI18n } from "vue-i18n";
import { setLocale } from "./i18n";

const { t } = useI18n();

const switchLang = async (locale) => {
    await setLocale(locale);
    // Optionnel : recharger la page pour sync tout le DOM (certains plugins nécessitent un refresh)
    // window.location.reload()
};
</script>

<template>
    <div class="min-h-screen bg-base-200 flex items-center justify-center p-6">
        <div class="card w-full max-w-md bg-base-100 shadow-2xl">
            <div class="card-body">
                <h1 class="card-title text-3xl">
                    {{ t("welcome") }}
                </h1>

                <p class="text-base-content/70">
                    {{ t("items", { count: 3 }) }}
                </p>

                <div class="divider"></div>

                <div class="flex gap-2 justify-end">
                    <button class="btn btn-primary" @click="switchLang('en')">
                        🇬🇧 English
                    </button>

                    <button class="btn btn-secondary" @click="switchLang('fr')">
                        🇫🇷 Français
                    </button>
                </div>

                <div class="mt-4">
                    <div class="alert alert-success">
                        <span>DaisyUI fonctionne correctement 🎉</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

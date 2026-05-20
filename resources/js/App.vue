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
    <div>
        <h1>{{ $t("welcome") }}</h1>
        <p>{{ $t("items", { count: 3 }) }}</p>
        <button @click="switchLang('en')">🇬🇧 English</button>
        <button @click="switchLang('fr')">🇫🇷 Français</button>
    </div>
</template>

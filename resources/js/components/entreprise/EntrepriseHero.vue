<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps({
    entreprise: {
        type: Object,
        required: true,
        // { name, access_token, logo_url, primary_color, employee_count }
    },
})
</script>

<template>
    <header class="py-16 px-6 text-white" :style="`background-color: ${entreprise.primary_color}`">
        <div class="max-w-2xl mx-auto text-center">
            <div v-if="entreprise.logo_url" class="flex justify-center mb-6 hero-item" style="--delay:0ms">
                <div class="bg-white rounded-xl p-3 shadow-sm">
                    <img
                        :src="entreprise.logo_url"
                        :alt="entreprise.name"
                        class="h-14 max-w-[140px] object-contain"
                    >
                </div>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold mb-3 hero-item" :style="`--delay:${entreprise.logo_url ? 80 : 0}ms`">{{ entreprise.name }}</h1>
            <p v-if="entreprise.employee_count" class="text-white/70 mb-6 hero-item" :style="`--delay:${entreprise.logo_url ? 160 : 80}ms`">
                {{ entreprise.employee_count }} {{ t('entreprise.employees') }}
            </p>
            <a
                :href="`/c/${entreprise.access_token}/quiz`"
                class="btn bg-white font-semibold border-none px-8 hero-item"
                :style="`color: ${entreprise.primary_color}; --delay:${entreprise.logo_url ? 240 : 160}ms`"
            >
                {{ t('entreprise.quiz_cta') }}
            </a>
        </div>
    </header>
</template>

<style scoped>
.hero-item {
    animation-name: hero-fade;
    animation-duration: 400ms;
    animation-timing-function: cubic-bezier(0.23, 1, 0.32, 1);
    animation-delay: var(--delay, 0ms);
    animation-fill-mode: both;
}

@keyframes hero-fade {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (prefers-reduced-motion: reduce) {
    .hero-item { animation: none; }
}
</style>

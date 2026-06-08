<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps({
    entreprise: {
        type: Object,
        required: true,

    },
})
</script>

<template>
    <header class="relative py-20 px-6 text-white overflow-hidden"
        style="background-image: url('https://images.unsplash.com/photo-1615461066159-fea0960485d5?auto=format&fit=crop&w=1400&q=80'); background-size: cover; background-position: center 30%;">
        <!-- Overlay couleur primaire -->
        <div class="absolute inset-0" :style="`background-color: ${entreprise.primary_color}; opacity: 0.78;`"></div>

        <div class="absolute inset-0 pointer-events-none"
            style="background: linear-gradient(to top, rgba(0,0,0,0.30) 0%, transparent 55%);"></div>

        <div class="relative z-10 max-w-2xl mx-auto text-center">
            <div v-if="entreprise.logo_url" class="flex justify-center mb-6 hero-item" style="--delay:0ms">
                <div class="bg-white/15 rounded-xl p-3">
                    <img :src="entreprise.logo_url" :alt="entreprise.name" class="h-14 max-w-[140px] object-contain">
                </div>
            </div>
            <!-- infos sur l'entreprise -->
            <h1 class="text-3xl md:text-4xl font-bold mb-3 hero-item"
                :style="`--delay:${entreprise.logo_url ? 80 : 0}ms`">{{ entreprise.name }}</h1>
            <p v-if="entreprise.employee_count" class="text-white/70 mb-6 hero-item"
                :style="`--delay:${entreprise.logo_url ? 160 : 80}ms`">
                {{ entreprise.employee_count }} {{ t('entreprise.employees') }}
            </p>
            <a :href="`/c/${entreprise.access_token}/quiz`"
                class="btn bg-white font-semibold border-none px-8 hero-item"
                :style="`color: ${entreprise.primary_color}; --delay:${entreprise.logo_url ? 240 : 160}ms`">
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
    .hero-item {
        animation: none;
    }
}
</style>

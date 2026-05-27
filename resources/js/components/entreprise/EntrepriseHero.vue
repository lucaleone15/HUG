<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps({
    entreprise: {
        type: Object,
        required: true,
        // { name, slug, logo_url, primary_color, employee_count }
    },
})
</script>

<template>
    <header class="py-16 px-6 text-white" :style="`background-color: ${entreprise.primary_color}`">
        <div class="max-w-2xl mx-auto text-center">
            <div v-if="entreprise.logo_url" class="flex justify-center mb-6">
                <div class="bg-white rounded-xl p-3 shadow-sm">
                    <img
                        :src="entreprise.logo_url"
                        :alt="entreprise.name"
                        class="h-14 max-w-[140px] object-contain"
                    >
                </div>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold mb-3">{{ entreprise.name }}</h1>
            <p v-if="entreprise.employee_count" class="text-white/70 mb-6">
                {{ entreprise.employee_count }} {{ t('entreprise.employees') }}
            </p>
            <a
                :href="`/c/${entreprise.slug}/quiz`"
                class="btn bg-white font-semibold border-none px-8"
                :style="`color: ${entreprise.primary_color}`"
            >
                {{ t('entreprise.quiz_cta') }}
            </a>
        </div>
    </header>
</template>

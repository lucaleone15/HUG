<script setup>
import { onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import NavBar from '../components/NavBar.vue'
import { sendAnalytics } from '../composables/useAnalytics.js'

const { t } = useI18n()

const props = defineProps({
    entreprise: Object,
    submission: Object,
})

const eligible = props.submission?.is_eligible ?? false

// Fired here car is_eligible n'est connu qu'après le redirect serveur
onMounted(() => {
    sendAnalytics('quiz_completed', props.entreprise.id, null, {
        is_eligible: eligible,
    })
})

const onRdvClick = () => {
    sendAnalytics('rdv_clicked', props.entreprise.id, null, {})
}
</script>

<template>
    <div class="min-h-screen bg-base-200">
        <NavBar />

        <!-- Company strip -->
        <div class="py-3 px-6 text-white text-center text-sm font-medium"
             :style="`background-color: ${entreprise.primary_color}`">
            {{ entreprise.name }}
        </div>

        <main class="max-w-lg mx-auto px-6 py-12 text-center">

            <!-- Eligible -->
            <template v-if="eligible">
                <div class="text-7xl mb-6">🩸</div>
                <h1 class="text-2xl font-bold text-emerald-600 mb-3">
                    {{ t('result.eligible_title') }}
                </h1>
                <p class="text-base-content/70 mb-8 leading-relaxed">
                    {{ t('result.eligible_message') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a
                        href="https://dondesang.cts-ge.ch"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn text-white border-none px-8"
                        :style="`background-color: ${entreprise.primary_color}`"
                        @click="onRdvClick"
                    >
                        {{ t('result.eligible_cta') }}
                    </a>
                    <a href="/" class="btn btn-ghost">{{ t('result.back_home') }}</a>
                </div>
            </template>

            <!-- Not eligible -->
            <template v-else>
                <div class="text-7xl mb-6">💙</div>
                <h1 class="text-2xl font-bold text-base-content mb-3">
                    {{ t('result.ineligible_title') }}
                </h1>
                <p class="text-base-content/70 mb-8 leading-relaxed">
                    {{ t('result.ineligible_message') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a
                        href="https://dondesang.cts-ge.ch"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn btn-outline"
                    >
                        {{ t('result.ineligible_cta') }}
                    </a>
                    <a href="/" class="btn btn-ghost">{{ t('result.back_home') }}</a>
                </div>
            </template>

        </main>
    </div>
</template>

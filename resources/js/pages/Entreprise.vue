<script setup>
import { onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import NavBar from '../components/NavBar.vue'
import { sendAnalytics, getDevice } from '../composables/useAnalytics.js'

const { t } = useI18n()

const props = defineProps({
    entreprise: Object,
})

onMounted(() => {
    sendAnalytics('page_viewed', props.entreprise.id, null, {
        referrer: document.referrer || 'direct',
        device:   getDevice(),
    })
})
</script>

<template>
    <div class="min-h-screen bg-base-100">
        <NavBar />

        <!-- Company hero banner -->
        <header
            class="py-16 px-6 text-white"
            :style="`background-color: ${entreprise.primary_color}`"
        >
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

        <!-- Info section -->
        <main class="max-w-2xl mx-auto px-6 py-12 text-center">
            <div class="card bg-base-200 shadow-sm">
                <div class="card-body">
                    <p class="text-base-content/70 text-sm leading-relaxed">
                        Participez au quiz d'éligibilité au don du sang proposé par <strong>{{ entreprise.name }}</strong>
                        en partenariat avec le HUG et le Centre de Transfusion Sanguine.
                    </p>
                    <div class="card-actions justify-center mt-4">
                        <a
                            :href="`/c/${entreprise.slug}/quiz`"
                            class="btn text-white border-none"
                            :style="`background-color: ${entreprise.primary_color}`"
                        >
                            {{ t('entreprise.quiz_cta') }}
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

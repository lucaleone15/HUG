<script setup>
import { onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import NavBar from '../components/ui/NavBar.vue'
import Footer from '../components/ui/Footer.vue'
import EntrepriseHero from '../components/entreprise/EntrepriseHero.vue'
import { sendAnalytics, getDevice } from '../composables/useAnalytics.js'

const { t } = useI18n()

const props = defineProps({ entreprise: Object })

onMounted(() => {
    sendAnalytics('page_viewed', props.entreprise.id, null, {
        referrer: document.referrer || 'direct',
        device:   getDevice(),
    })
})
</script>

<template>
    <div class="min-h-screen bg-base-100 flex flex-col">
        <NavBar />
        <EntrepriseHero :entreprise="entreprise" />
        <main class="max-w-2xl mx-auto px-6 py-12 text-center flex-1">
            <div class="card bg-base-200 shadow-sm">
                <div class="card-body">
                    <p class="text-base-content/70 text-sm leading-relaxed">
                        Participez au quiz d'éligibilité au don du sang proposé par
                        <strong>{{ entreprise.name }}</strong>
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
        <Footer />
    </div>
</template>

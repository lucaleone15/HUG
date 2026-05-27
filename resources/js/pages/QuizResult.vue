<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import NavBar from '../components/ui/NavBar.vue'
import { sendAnalytics } from '../composables/useAnalytics.js'

const { t } = useI18n()

const props = defineProps({
    entreprise: Object,
    submission: Object,
})

const eligible        = props.submission?.is_eligible ?? false
const reasons         = props.submission?.disqualification_reasons ?? []
const needsEvaluation = props.submission?.needs_evaluation ?? false
const copied        = ref(false)
const messageCopied = ref(false)
const shareUrl      = `${window.location.origin}/c/${props.entreprise.slug}`

const onRdvClick = () => sendAnalytics('rdv_clicked', props.entreprise.id, null, {})

const copyLink = async () => {
    await navigator.clipboard.writeText(shareUrl)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2000)
}

const copyEmailMessage = async () => {
    const text = t('result.referral_email_body', { company: props.entreprise.name, url: shareUrl })
    await navigator.clipboard.writeText(text)
    messageCopied.value = true
    setTimeout(() => { messageCopied.value = false }, 2000)
}

const whatsappHref = computed(() => {
    const text = encodeURIComponent(t('result.referral_email_body', { company: props.entreprise.name, url: shareUrl }))
    return `https://wa.me/?text=${text}`
})

// Fired here car is_eligible n'est connu qu'après le redirect serveur
onMounted(() => {
    sendAnalytics('quiz_completed', props.entreprise.id, null, { is_eligible: eligible })
})
</script>

<template>
    <div class="min-h-screen bg-base-200 flex flex-col">
        <NavBar />

        <!-- Company strip -->
        <div class="py-3 px-6 text-white text-center text-sm font-medium"
             :style="`background-color: ${entreprise.primary_color}`">
            {{ entreprise.name }}
        </div>

        <main class="max-w-lg mx-auto px-6 py-12 text-center flex-1">

            <!-- Eligible -->
            <template v-if="eligible">
                <div class="text-7xl mb-6">🩸</div>
                <h1 class="text-2xl font-bold text-emerald-600 mb-3">
                    {{ t('result.eligible_title') }}
                </h1>
                <p class="text-base-content/70 mb-8 leading-relaxed">
                    {{ t('result.eligible_message') }}
                </p>
                <p v-if="entreprise.rdv_date" class="text-sm text-base-content/60 mb-4">
                    {{ t('entreprise.collect_date') }} :
                    <strong>{{ new Date(entreprise.rdv_date).toLocaleDateString('fr-CH', { day: 'numeric', month: 'long', year: 'numeric' }) }}</strong>
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a v-if="entreprise.rdv_url"
                        :href="entreprise.rdv_url"
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
                <p class="text-base-content/70 mb-6 leading-relaxed">
                    {{ t('result.ineligible_message') }}
                </p>

                <!-- Raisons de non-éligibilité (présentes seulement sur le premier affichage, pas sur refresh) -->
                <div
                    v-if="reasons.length > 0"
                    class="text-left bg-base-100 border border-base-300 rounded-xl p-4 mb-6 shadow-sm"
                >
                    <p class="text-sm font-semibold mb-3 flex items-center gap-2">
                        <span>ℹ️</span>
                        {{ t('result.ineligible_reasons_title') }}
                    </p>
                    <ul class="space-y-2">
                        <li
                            v-for="(reason, i) in reasons"
                            :key="i"
                            class="flex items-start gap-2 text-sm text-base-content/75 leading-snug"
                        >
                            <span class="text-error mt-0.5 shrink-0">•</span>
                            <span>{{ reason }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Encadré contact — visible uniquement si des raisons sont présentes -->
                <div
                    v-if="reasons.length > 0"
                    class="text-left bg-base-100 border border-base-300 rounded-xl p-4 mb-6 shadow-sm flex items-start gap-3"
                >
                    <span class="text-2xl shrink-0">💬</span>
                    <div>
                        <p class="text-sm font-semibold mb-1">{{ t('result.contact_box_title') }}</p>
                        <p class="text-sm text-base-content/70 leading-snug mb-3">{{ t('result.contact_box_message') }}</p>
                        <a href="/contact" class="btn btn-sm btn-outline">
                            {{ t('result.contact_box_cta') }}
                        </a>
                    </div>
                </div>

                <!-- Évaluation médicale complémentaire (birth_check — origines géographiques) -->
                <div
                    v-if="needsEvaluation"
                    class="text-left bg-info/10 border border-info/30 rounded-xl p-4 mb-6"
                >
                    <p class="text-sm font-semibold text-info mb-1 flex items-center gap-2">
                        <span>🔬</span>
                        {{ t('result.needs_evaluation_title') }}
                    </p>
                    <p class="text-sm text-base-content/70 leading-snug">
                        {{ t('result.needs_evaluation_message') }}
                    </p>
                </div>

                <!-- Bloc parrainage -->
                <div class="card bg-base-100 shadow-sm text-left mb-8">
                    <div class="card-body gap-4">
                        <div class="flex items-center gap-3">
                            <span class="text-3xl">🤝</span>
                            <div>
                                <h2 class="font-bold text-base">{{ t('result.referral_title') }}</h2>
                                <p class="text-sm text-base-content/60 mt-0.5">{{ t('result.referral_subtitle') }}</p>
                            </div>
                        </div>

                        <!-- Lien à copier -->
                        <div class="flex items-center gap-2 bg-base-200 rounded-lg px-3 py-2">
                            <span class="text-xs text-base-content/50 flex-1 truncate font-mono">{{ shareUrl }}</span>
                            <button class="btn btn-xs border-none text-white shrink-0 transition-colors"
                                :style="`background-color: ${copied ? '#059669' : entreprise.primary_color}`"
                                @click="copyLink">
                                {{ copied ? t('result.referral_copied') : t('result.referral_copy') }}
                            </button>
                        </div>

                        <!-- Boutons partage -->
                        <div class="flex flex-col sm:flex-row gap-2">
                            <button class="btn btn-sm flex-1 gap-2 transition-colors"
                                :class="messageCopied ? 'btn-success text-white border-none' : 'btn-outline'"
                                @click="copyEmailMessage">
                                ✉️ {{ messageCopied ? t('result.referral_message_copied') : t('result.referral_email') }}
                            </button>
                            <a :href="whatsappHref" target="_blank" rel="noopener noreferrer"
                               class="btn btn-sm flex-1 gap-2 text-white border-none" style="background-color:#25D366">
                                💬 {{ t('result.referral_whatsapp') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="https://www.hug.ch/don-du-sang" target="_blank" rel="noopener noreferrer" class="btn btn-outline">
                        {{ t('result.ineligible_cta') }}
                    </a>
                    <a href="/" class="btn btn-ghost">{{ t('result.back_home') }}</a>
                </div>
            </template>

        </main>
    </div>
</template>

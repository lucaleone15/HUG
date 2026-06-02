<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useApi } from '../composables/useApi.js'
import { useAuth } from '../composables/useAuth.js'
import { useEntreprisesStore } from '../stores/entreprisesStore.js'
import CompanySelector from '../components/admin/CompanySelector.vue'
import BaseButton from '../components/ui/BaseButton.vue'

const { t, locale } = useI18n()
const api = useApi()
const { token } = useAuth()
const { entreprises, fetch: fetchEntreprises } = useEntreprisesStore()

const activeEntreprises = computed(() => entreprises.value.filter(e => e.is_active))

const data        = ref(null)
const loading     = ref(false)
const downloading = ref(false)
const error       = ref(null)
const selectedId  = ref('')

onMounted(fetchEntreprises)

const generate = async () => {
    if (!selectedId.value) return
    loading.value = true
    error.value   = null
    data.value    = null
    try {
        data.value = await api.get(`/admin/report?entreprise_id=${selectedId.value}`)
    } catch (e) {
        error.value = e.message
    } finally {
        loading.value = false
    }
}

const downloadPdf = async () => {
    if (!data.value) return
    downloading.value = true
    try {
        const res = await api.get(`/admin/report?entreprise_id=${selectedId.value}&format=pdf&locale=${locale.value}`)
        window.open(res.url, '_blank')
    } catch (e) {
        alert(t('admin.pdf_error') + e.message)
    } finally {
        downloading.value = false
    }
}

const pct = (a, b) => b > 0 ? (a / b * 100).toFixed(1) + '%' : '—'
const fmt = (n)   => n?.toLocaleString('fr-CH') ?? '—'
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold mb-6">{{ t('admin.report_title') }}</h1>

        <!-- Sélecteur -->
        <div class="card bg-base-100 shadow-sm max-w-md mb-6">
            <div class="card-body gap-3">
                <CompanySelector
                    :companies="activeEntreprises"
                    v-model="selectedId"
                    :label="t('admin.choose_company')"
                />
                <BaseButton size="sm" :disabled="!selectedId" :loading="loading" @click="generate">
                    {{ t('admin.generate_report') }}
                </BaseButton>
            </div>
        </div>

        <div v-if="error" class="alert alert-error">{{ error }}</div>

        <!-- Rapport -->
        <template v-if="data">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Infos entreprise -->
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="font-semibold text-lg">{{ data.entreprise.name }}</h2>
                        <div class="divide-y divide-base-200 mt-2">
                            <div class="flex justify-between py-2 text-sm">
                                <span class="text-base-content/50">{{ t('inscription.contact_name') }}</span>
                                <span>{{ data.entreprise.contact_name ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between py-2 text-sm">
                                <span class="text-base-content/50">{{ t('inscription.contact_email') }}</span>
                                <span class="text-xs">{{ data.entreprise.contact_email ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between py-2 text-sm">
                                <span class="text-base-content/50">{{ t('inscription.employee_count') }}</span>
                                <span>{{ fmt(data.entreprise.employee_count) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KPIs participation -->
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="font-semibold mb-3">{{ t('admin.participation_title') }}</h2>
                        <div class="divide-y divide-base-200">
                            <div class="flex items-center justify-between py-2.5">
                                <span class="text-sm text-base-content/55">{{ t('admin.quiz_started_stat') }}</span>
                                <span class="font-bold tabular-nums">{{ fmt(data.participation.quiz_started) }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2.5">
                                <span class="text-sm text-base-content/55">{{ t('admin.quiz_completed_stat') }}</span>
                                <span class="font-bold tabular-nums">{{ fmt(data.participation.quiz_completed) }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2.5">
                                <span class="text-sm text-base-content/55">{{ t('admin.funnel_eligible') }}</span>
                                <span class="font-bold tabular-nums text-brand">{{ fmt(data.participation.eligible) }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2.5">
                                <span class="text-sm text-base-content/55">{{ t('admin.rdv_clicked_stat') }}</span>
                                <span class="font-bold tabular-nums">{{ fmt(data.participation.rdv_clicked) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Taux -->
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="font-semibold mb-3">{{ t('admin.key_rates') }}</h2>
                        <div class="space-y-3">
                            <div v-for="([label, a, b]) in [
                                [t('admin.participation_rate'), data.participation.quiz_started, data.entreprise.employee_count],
                                [t('admin.eligibility_rate'),   data.participation.eligible,     data.participation.total_submissions],
                                [t('admin.conversion_rate'),    data.participation.rdv_clicked,  data.participation.eligible],
                            ]" :key="label">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-base-content/60">{{ label }}</span>
                                    <span class="font-semibold">{{ pct(a, b) }}</span>
                                </div>
                                <div class="bg-base-200 rounded-full h-1.5">
                                    <div class="bg-brand h-1.5 rounded-full"
                                        :style="`width:${b > 0 ? Math.min(a/b*100, 100).toFixed(1) : 0}%`"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comportement -->
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="font-semibold mb-3">{{ t('admin.behavior_title') }}</h2>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-base-content/55">{{ t('admin.avg_duration_stat') }}</span>
                            <strong>{{ data.behavior.avg_duration_s ? data.behavior.avg_duration_s + 's' : '—' }}</strong>
                        </div>
                        <div v-if="Object.keys(data.behavior.abandon_by_question ?? {}).length" class="mt-3">
                            <p class="text-xs text-base-content/40 mb-2">{{ t('admin.abandon_by_question') }}</p>
                            <div class="space-y-1 max-h-40 overflow-y-auto">
                                <div v-for="(count, q) in data.behavior.abandon_by_question" :key="q"
                                    class="flex gap-2 text-xs text-base-content/60">
                                    <span class="w-8">Q{{ parseInt(q) + 1 }}</span>
                                    <span>{{ count }} {{ t('admin.abandons') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="flex justify-end mt-6">
                <BaseButton size="sm" :loading="downloading" @click="downloadPdf">
                    {{ t('admin.download_pdf') }}
                </BaseButton>
            </div>
        </template>
    </div>
</template>

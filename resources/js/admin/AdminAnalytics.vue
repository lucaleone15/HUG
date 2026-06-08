<script setup>
import { ref, watch, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useApi } from '../composables/useApi.js'
import { useAsyncData } from '../composables/useAsyncData.js'
import { useEntreprisesStore } from '../stores/entreprisesStore.js'
import CompanySelector from '../components/admin/CompanySelector.vue'

const { t } = useI18n()
const api = useApi()
const { entreprises, fetch: fetchEntreprises } = useEntreprisesStore()

const filterEntreprise = ref('')

const { data, loading, error, execute } = useAsyncData(() => {
    const params = filterEntreprise.value ? `?entreprise_id=${filterEntreprise.value}` : ''
    return api.get(`/admin/analytics${params}`)
})

onMounted(async () => {
    await fetchEntreprises()
    execute()
})

watch(filterEntreprise, execute)

const pct    = (a, b) => b > 0 ? (a / b * 100).toFixed(1) + '%' : '-'
const maxVal = (obj) => Math.max(...Object.values(obj).map(Number), 1)

const funnelKey = {
    page_viewed:    'admin.funnel_visitors',
    quiz_started:   'admin.funnel_started',
    quiz_completed: 'admin.funnel_completed',
    eligible:       'admin.funnel_eligible',
    rdv_clicked:    'admin.funnel_rdv',
}
const deviceKey = {
    mobile:  'admin.device_mobile',
    tablet:  'admin.device_tablet',
    desktop: 'admin.device_desktop',
}
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">{{ t('admin.analytics_title') }}</h1>
            <CompanySelector
                :companies="entreprises"
                v-model="filterEntreprise"
                :all-label="t('admin.all_companies')"
                class="select-sm"
            />
        </div>

        <div v-if="loading" class="flex justify-center py-16">
            <span class="loading loading-spinner loading-lg text-brand"></span>
        </div>
        <div v-else-if="error" class="alert alert-error">{{ error }}</div>

        <template v-else-if="data">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Entonnoir -->
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="font-semibold mb-3">{{ t('admin.funnel_title') }}</h2>
                        <div class="space-y-2">
                            <div v-for="(val, key) in data.funnel" :key="key" class="flex items-center gap-3">
                                <span class="text-xs w-36 text-base-content/50">{{ t(funnelKey[key] ?? 'admin.funnel_visitors') }}</span>
                                <div class="flex-1 bg-base-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-brand h-2 rounded-full transition-all"
                                        :style="`width:${(Math.min(val, maxVal(data.funnel)) / maxVal(data.funnel) * 100).toFixed(1)}%`"></div>
                                </div>
                                <span class="text-sm font-medium w-12 text-right">{{ val?.toLocaleString('fr-CH') }}</span>
                            </div>
                        </div>
                        <div class="mt-3 text-sm text-base-content/50">
                            {{ t('admin.avg_duration') }} : <strong>{{ data.avg_duration_s ? data.avg_duration_s + 's' : '-' }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Abandon par question -->
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="font-semibold mb-3">{{ t('admin.abandon_title') }}</h2>
                        <div v-if="Object.keys(data.abandon_by_question ?? {}).length" class="space-y-1.5 max-h-64 overflow-y-auto">
                            <div v-for="(count, q) in data.abandon_by_question" :key="q" class="flex items-center gap-3">
                                <span class="text-xs w-8 text-base-content/50">Q{{ parseInt(q) + 1 }}</span>
                                <div class="flex-1 bg-base-200 rounded-full h-2">
                                    <div class="bg-amber-500 h-2 rounded-full"
                                        :style="`width:${(count / maxVal(data.abandon_by_question) * 100).toFixed(1)}%`"></div>
                                </div>
                                <span class="text-xs w-6 text-right">{{ count }}</span>
                            </div>
                        </div>
                        <p v-else class="text-base-content/40 text-sm">{{ t('admin.no_data') }}</p>
                    </div>
                </div>

                <!-- Device -->
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="font-semibold mb-3">{{ t('admin.device_title') }}</h2>
                        <div class="divide-y divide-base-200 w-full">
                            <div v-for="(count, device) in data.by_device" :key="device"
                                 class="flex items-center justify-between py-2.5">
                                <span class="text-sm text-base-content/60">{{ t(deviceKey[device] ?? device) }}</span>
                                <span class="font-semibold text-sm tabular-nums">{{ count?.toLocaleString('fr-CH') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Referrer -->
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="font-semibold mb-3">{{ t('admin.referrer_title') }}</h2>
                        <div class="space-y-2">
                            <div v-for="(count, ref) in data.by_referrer" :key="ref" class="flex items-center gap-3">
                                <span class="text-xs w-24 truncate text-base-content/60">{{ ref }}</span>
                                <div class="flex-1 bg-base-200 rounded-full h-2">
                                    <div class="bg-[--color-info] h-2 rounded-full"
                                        :style="`width:${(count / maxVal(data.by_referrer) * 100).toFixed(1)}%`"></div>
                                </div>
                                <span class="text-xs w-8 text-right">{{ count }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </template>
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useApi } from '../composables/useApi.js'
import { useAsyncData } from '../composables/useAsyncData.js'
import { useEntreprisesStore } from '../stores/entreprisesStore.js'

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

const pct    = (a, b) => b > 0 ? (a / b * 100).toFixed(1) + '%' : '—'
const maxVal = (obj) => Math.max(...Object.values(obj).map(Number), 1)
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Analytics</h1>
            <select v-model="filterEntreprise" class="select select-bordered select-sm">
                <option value="">Toutes les entreprises</option>
                <option v-for="e in entreprises" :key="e.id" :value="e.id">{{ e.name }}</option>
            </select>
        </div>

        <div v-if="loading" class="flex justify-center py-16">
            <span class="loading loading-spinner loading-lg text-[#E30613]"></span>
        </div>
        <div v-else-if="error" class="alert alert-error">{{ error }}</div>

        <template v-else-if="data">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Entonnoir -->
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="font-semibold mb-3">Entonnoir</h2>
                        <div class="space-y-2">
                            <div v-for="(val, key) in data.funnel" :key="key" class="flex items-center gap-3">
                                <span class="text-xs w-36 text-base-content/50 capitalize">{{ key.replace('_', ' ') }}</span>
                                <div class="flex-1 bg-base-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-[#E30613] h-2 rounded-full transition-all"
                                        :style="`width:${(Math.min(val, maxVal(data.funnel)) / maxVal(data.funnel) * 100).toFixed(1)}%`"></div>
                                </div>
                                <span class="text-sm font-medium w-12 text-right">{{ val?.toLocaleString('fr-CH') }}</span>
                            </div>
                        </div>
                        <div class="mt-3 text-sm text-base-content/50">
                            Durée moyenne quiz : <strong>{{ data.avg_duration_s ? data.avg_duration_s + 's' : '—' }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Abandon par question -->
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="font-semibold mb-3">Abandon par question</h2>
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
                        <p v-else class="text-base-content/40 text-sm">Pas de données</p>
                    </div>
                </div>

                <!-- Device -->
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="font-semibold mb-3">Device</h2>
                        <div class="flex gap-4 flex-wrap">
                            <div v-for="(count, device) in data.by_device" :key="device" class="text-center">
                                <div class="text-3xl">{{ device === 'mobile' ? '📱' : device === 'tablet' ? '📲' : '🖥️' }}</div>
                                <div class="font-semibold">{{ count?.toLocaleString('fr-CH') }}</div>
                                <div class="text-xs text-base-content/50 capitalize">{{ device }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Referrer -->
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="font-semibold mb-3">Source d'acquisition</h2>
                        <div class="space-y-2">
                            <div v-for="(count, ref) in data.by_referrer" :key="ref" class="flex items-center gap-3">
                                <span class="text-xs w-24 truncate text-base-content/60">{{ ref }}</span>
                                <div class="flex-1 bg-base-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full"
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

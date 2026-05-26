<script setup>
import { ref, computed, onMounted } from 'vue'
import { useApi } from '../composables/useApi.js'
import { useAuth } from '../composables/useAuth.js'
import { useEntreprisesStore } from '../stores/entreprisesStore.js'

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
        const res = await fetch(`/api/admin/report?entreprise_id=${selectedId.value}&format=pdf`, {
            headers: {
                'Authorization': `Bearer ${token.value}`,
                'Accept': 'application/pdf',
            },
        })
        if (!res.ok) throw new Error(`HTTP ${res.status}`)
        const blob = await res.blob()
        const url  = URL.createObjectURL(blob)
        const a    = document.createElement('a')
        a.href     = url
        a.download = `rapport-${data.value.entreprise.slug}.pdf`
        a.click()
        URL.revokeObjectURL(url)
    } catch (e) {
        alert('Erreur lors de la génération du PDF : ' + e.message)
    } finally {
        downloading.value = false
    }
}

const pct = (a, b) => b > 0 ? (a / b * 100).toFixed(1) + '%' : '—'
const fmt = (n)   => n?.toLocaleString('fr-CH') ?? '—'
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold mb-6">Rapport par entreprise</h1>

        <!-- Sélecteur -->
        <div class="card bg-base-100 shadow-sm max-w-md mb-6">
            <div class="card-body gap-3">
                <label class="form-control">
                    <div class="label"><span class="label-text">Choisir une entreprise</span></div>
                    <select v-model="selectedId" class="select select-bordered select-sm">
                        <option value="" disabled>— sélectionner —</option>
                        <option v-for="e in activeEntreprises" :key="e.id" :value="e.id">{{ e.name }}</option>
                    </select>
                </label>
                <button class="btn btn-sm bg-[#E30613] hover:bg-[#c0051f] text-white border-none"
                    :disabled="!selectedId || loading" @click="generate">
                    <span v-if="loading" class="loading loading-spinner loading-xs"></span>
                    Générer le rapport
                </button>
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
                        <div class="text-sm space-y-1 text-base-content/60 mt-2">
                            <div>👤 {{ data.entreprise.contact_name ?? '—' }}</div>
                            <div>✉️ {{ data.entreprise.contact_email ?? '—' }}</div>
                            <div>👥 {{ fmt(data.entreprise.employee_count) }} employés</div>
                        </div>
                    </div>
                </div>

                <!-- KPIs participation -->
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="font-semibold mb-3">Participation</h2>
                        <div class="grid grid-cols-2 gap-3 text-center">
                            <div>
                                <div class="text-2xl font-bold text-violet-600">{{ fmt(data.participation.quiz_started) }}</div>
                                <div class="text-xs text-base-content/50">quiz démarrés</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-amber-600">{{ fmt(data.participation.quiz_completed) }}</div>
                                <div class="text-xs text-base-content/50">quiz complétés</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-emerald-600">{{ fmt(data.participation.eligible) }}</div>
                                <div class="text-xs text-base-content/50">éligibles</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-[#E30613]">{{ fmt(data.participation.rdv_clicked) }}</div>
                                <div class="text-xs text-base-content/50">RDV cliqués</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Taux -->
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="font-semibold mb-3">Taux clés</h2>
                        <div class="space-y-3">
                            <div v-for="([label, a, b]) in [
                                ['Taux de participation', data.participation.quiz_started, data.entreprise.employee_count],
                                ['Taux d\'éligibilité',   data.participation.eligible,     data.participation.total_submissions],
                                ['Taux de conversion',    data.participation.rdv_clicked,  data.participation.eligible],
                            ]" :key="label">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-base-content/60">{{ label }}</span>
                                    <span class="font-semibold">{{ pct(a, b) }}</span>
                                </div>
                                <div class="bg-base-200 rounded-full h-1.5">
                                    <div class="bg-[#E30613] h-1.5 rounded-full"
                                        :style="`width:${b > 0 ? Math.min(a/b*100, 100).toFixed(1) : 0}%`"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comportement -->
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="font-semibold mb-3">Comportement</h2>
                        <div class="text-sm space-y-2 text-base-content/60">
                            <div>⏱ Durée moyenne : <strong>{{ data.behavior.avg_duration_s ? data.behavior.avg_duration_s + 's' : '—' }}</strong></div>
                        </div>
                        <div v-if="Object.keys(data.behavior.abandon_by_question ?? {}).length" class="mt-3">
                            <p class="text-xs text-base-content/40 mb-2">Abandons par question</p>
                            <div class="space-y-1 max-h-40 overflow-y-auto">
                                <div v-for="(count, q) in data.behavior.abandon_by_question" :key="q"
                                    class="flex gap-2 text-xs text-base-content/60">
                                    <span class="w-8">Q{{ parseInt(q) + 1 }}</span>
                                    <span>{{ count }} abandons</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="flex justify-end mt-6">
                <button class="btn btn-sm bg-[#E30613] hover:bg-[#c0051f] text-white border-none gap-2"
                    :disabled="downloading" @click="downloadPdf">
                    <span v-if="downloading" class="loading loading-spinner loading-xs"></span>
                    <span v-else>📄</span>
                    Télécharger PDF
                </button>
            </div>
        </template>
    </div>
</template>

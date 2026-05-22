<script setup>
import { ref, onMounted, computed } from 'vue'
import { useApi } from '../composables/useApi.js'

const api  = useApi()
const data = ref(null)
const loading = ref(true)
const error   = ref(null)

onMounted(async () => {
    try {
        data.value = await api.get('/admin/dashboard')
    } catch (e) {
        error.value = e.message
    } finally {
        loading.value = false
    }
})

const pct = (a, b) => (b > 0 ? (a / b * 100).toFixed(1) + '%' : '—')
const fmt = (n) => n?.toLocaleString('fr-CH') ?? '—'
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold mb-6">Tableau de bord</h1>

        <div v-if="loading" class="flex justify-center py-16">
            <span class="loading loading-spinner loading-lg text-[#E30613]"></span>
        </div>

        <div v-else-if="error" class="alert alert-error">{{ error }}</div>

        <template v-else>
            <!-- Entonnoir KPI -->
            <section class="mb-8">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-base-content/40 mb-3">Entonnoir</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                    <div class="stat bg-base-100 rounded-xl shadow-sm p-4">
                        <div class="stat-title text-xs">Visiteurs</div>
                        <div class="stat-value text-xl text-blue-600">{{ fmt(data.funnel.page_viewed) }}</div>
                    </div>
                    <div class="stat bg-base-100 rounded-xl shadow-sm p-4">
                        <div class="stat-title text-xs">Quiz démarrés</div>
                        <div class="stat-value text-xl text-violet-600">{{ fmt(data.funnel.quiz_started) }}</div>
                        <div class="stat-desc">{{ pct(data.funnel.quiz_started, data.funnel.page_viewed) }} des visiteurs</div>
                    </div>
                    <div class="stat bg-base-100 rounded-xl shadow-sm p-4">
                        <div class="stat-title text-xs">Quiz complétés</div>
                        <div class="stat-value text-xl text-amber-600">{{ fmt(data.funnel.quiz_completed) }}</div>
                        <div class="stat-desc">{{ pct(data.funnel.quiz_completed, data.funnel.quiz_started) }} de complétion</div>
                    </div>
                    <div class="stat bg-base-100 rounded-xl shadow-sm p-4">
                        <div class="stat-title text-xs">Éligibles</div>
                        <div class="stat-value text-xl text-emerald-600">{{ fmt(data.funnel.eligible) }}</div>
                        <div class="stat-desc">{{ pct(data.funnel.eligible, data.funnel.quiz_completed) }} d'éligibilité</div>
                    </div>
                    <div class="stat bg-base-100 rounded-xl shadow-sm p-4">
                        <div class="stat-title text-xs">RDV cliqués</div>
                        <div class="stat-value text-xl text-[#E30613]">{{ fmt(data.funnel.rdv_clicked) }}</div>
                        <div class="stat-desc">{{ pct(data.funnel.rdv_clicked, data.funnel.eligible) }} des éligibles</div>
                    </div>
                </div>
            </section>

            <!-- Par entreprise -->
            <section>
                <h2 class="text-sm font-semibold uppercase tracking-widest text-base-content/40 mb-3">Par entreprise</h2>
                <div class="card bg-base-100 shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="table table-sm">
                            <thead>
                                <tr class="text-xs text-base-content/50">
                                    <th>#</th>
                                    <th>Entreprise</th>
                                    <th class="text-right">Éligibles</th>
                                    <th class="text-right">Soumissions</th>
                                    <th class="text-right">Taux éligibilité</th>
                                    <th class="text-right">Participation</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(e, i) in data.by_entreprise" :key="e.id" class="hover">
                                    <td class="text-base-content/40 text-xs">{{ i + 1 }}</td>
                                    <td class="font-medium">{{ e.name }}</td>
                                    <td class="text-right font-semibold text-emerald-600">{{ fmt(e.eligible_count) }}</td>
                                    <td class="text-right text-base-content/60">{{ fmt(e.submission_count) }}</td>
                                    <td class="text-right">
                                        <span v-if="e.eligibility_rate !== null">
                                            {{ (e.eligibility_rate * 100).toFixed(1) }}%
                                        </span>
                                        <span v-else class="text-base-content/30">—</span>
                                    </td>
                                    <td class="text-right">
                                        <span v-if="e.participation_rate !== null">
                                            {{ (e.participation_rate * 100).toFixed(1) }}%
                                        </span>
                                        <span v-else class="text-base-content/30">—</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </template>
    </div>
</template>

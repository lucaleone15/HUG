<script setup>
import { ref, watch, onMounted } from 'vue'
import { useApi } from '../composables/useApi.js'
import { usePagination } from '../composables/usePagination.js'
import { useEntreprisesStore } from '../stores/entreprisesStore.js'

const api = useApi()
const { entreprises, fetch: fetchEntreprises } = useEntreprisesStore()

const filterEligible   = ref('')
const filterEntreprise = ref('')

const { data, loading, error, page, lastPage, total, isFirst, isLast, load, prev, next, reset } =
    usePagination((p) => {
        const params = new URLSearchParams({ page: p })
        if (filterEligible.value !== '')   params.set('is_eligible',   filterEligible.value)
        if (filterEntreprise.value !== '') params.set('entreprise_id', filterEntreprise.value)
        return api.get(`/admin/submissions?${params}`)
    })

onMounted(async () => {
    await fetchEntreprises()
    load()
})

watch([filterEligible, filterEntreprise], reset)

const fmt = (iso) => iso ? new Date(iso).toLocaleString('fr-CH', { dateStyle: 'short', timeStyle: 'short' }) : '—'
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold mb-6">Soumissions</h1>

        <!-- Filtres -->
        <div class="flex flex-wrap gap-3 mb-4">
            <select v-model="filterEntreprise" class="select select-bordered select-sm">
                <option value="">Toutes les entreprises</option>
                <option v-for="e in entreprises" :key="e.id" :value="e.id">{{ e.name }}</option>
            </select>
            <select v-model="filterEligible" class="select select-bordered select-sm">
                <option value="">Tous</option>
                <option value="1">Éligibles</option>
                <option value="0">Non éligibles</option>
            </select>
        </div>

        <div v-if="loading" class="flex justify-center py-16">
            <span class="loading loading-spinner loading-lg text-[#E30613]"></span>
        </div>
        <div v-else-if="error" class="alert alert-error">{{ error }}</div>

        <template v-else-if="data">
            <div class="mb-2 text-sm text-base-content/50">
                {{ total.toLocaleString('fr-CH') }} soumissions
            </div>
            <div class="card bg-base-100 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr class="text-xs text-base-content/50">
                                <th>Entreprise</th>
                                <th>Éligibilité</th>
                                <th>Soumis le</th>
                                <th>Token</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="s in data" :key="s.id" class="hover">
                                <td class="font-medium text-sm">{{ s.entreprise?.name ?? '—' }}</td>
                                <td>
                                    <span v-if="s.is_eligible === true"  class="badge badge-success badge-sm">Éligible</span>
                                    <span v-else-if="s.is_eligible === false" class="badge badge-error badge-sm">Non éligible</span>
                                    <span v-else class="badge badge-ghost badge-sm">—</span>
                                </td>
                                <td class="text-sm text-base-content/60">{{ fmt(s.completed_at) }}</td>
                                <td class="font-mono text-xs text-base-content/30 max-w-[100px] truncate">{{ s.id }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div v-if="lastPage > 1" class="flex justify-center mt-4 gap-2">
                <button class="btn btn-sm btn-ghost" :disabled="isFirst" @click="prev">←</button>
                <span class="text-sm self-center">{{ page }} / {{ lastPage }}</span>
                <button class="btn btn-sm btn-ghost" :disabled="isLast" @click="next">→</button>
            </div>
        </template>
    </div>
</template>

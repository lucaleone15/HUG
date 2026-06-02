<script setup>
import { ref, watch, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useApi } from '../composables/useApi.js'
import { usePagination } from '../composables/usePagination.js'
import { useEntreprisesStore } from '../stores/entreprisesStore.js'
import CompanySelector from '../components/admin/CompanySelector.vue'

const { t } = useI18n()
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

const fmt = (iso) => iso ? new Date(iso).toLocaleString('fr-CH', { dateStyle: 'short', timeStyle: 'short' }) : '-'
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold mb-6">{{ t('admin.submissions_title') }}</h1>

        <!-- Filtres -->
        <div class="flex flex-wrap gap-3 mb-4">
            <CompanySelector
                :companies="entreprises"
                v-model="filterEntreprise"
                :all-label="t('admin.all_companies')"
                class="select-sm"
            />
            <select v-model="filterEligible" class="select select-bordered select-sm">
                <option value="">{{ t('admin.filter_all') }}</option>
                <option value="1">{{ t('admin.filter_eligible') }}</option>
                <option value="0">{{ t('admin.filter_ineligible') }}</option>
            </select>
        </div>

        <div v-if="loading" class="flex justify-center py-16">
            <span class="loading loading-spinner loading-lg text-brand"></span>
        </div>
        <div v-else-if="error" class="alert alert-error">{{ error }}</div>

        <template v-else-if="data">
            <div class="mb-2 text-sm text-base-content/50">
                {{ t('admin.submissions_count', { n: total.toLocaleString('fr-CH') }) }}
            </div>

            <!-- Vue mobile : cartes -->
            <div class="md:hidden card bg-base-100 shadow-sm divide-y divide-base-200">
                <div v-for="s in data" :key="'m-' + s.id" class="flex items-center gap-3 p-4">
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-sm truncate">{{ s.entreprise?.name ?? '-' }}</div>
                        <div class="text-xs text-base-content/50 mt-0.5">{{ fmt(s.completed_at) }}</div>
                    </div>
                    <span v-if="s.is_eligible === true"
                        class="badge badge-success badge-sm shrink-0 whitespace-nowrap">
                        {{ t('admin.eligible_badge') }}
                    </span>
                    <span v-else-if="s.is_eligible === false"
                        class="badge badge-error badge-sm shrink-0 whitespace-nowrap">
                        {{ t('admin.ineligible_badge') }}
                    </span>
                    <span v-else class="badge badge-ghost badge-sm shrink-0">-</span>
                </div>
            </div>

            <!-- Vue desktop : tableau -->
            <div class="hidden md:block card bg-base-100 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr class="text-xs text-base-content/50">
                                <th>{{ t('admin.col_company') }}</th>
                                <th>{{ t('admin.col_eligibility') }}</th>
                                <th>{{ t('admin.col_submitted_at') }}</th>
                                <th>{{ t('admin.col_token') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="s in data" :key="s.id" class="hover">
                                <td class="font-medium text-sm">{{ s.entreprise?.name ?? '-' }}</td>
                                <td class="whitespace-nowrap">
                                    <span v-if="s.is_eligible === true"
                                        class="badge badge-success badge-sm whitespace-nowrap">
                                        {{ t('admin.eligible_badge') }}
                                    </span>
                                    <span v-else-if="s.is_eligible === false"
                                        class="badge badge-error badge-sm whitespace-nowrap">
                                        {{ t('admin.ineligible_badge') }}
                                    </span>
                                    <span v-else class="badge badge-ghost badge-sm">-</span>
                                </td>
                                <td class="text-sm text-base-content/60 whitespace-nowrap">{{ fmt(s.completed_at) }}</td>
                                <td class="text-xs text-base-content/30 max-w-[100px] truncate">{{ s.id }}</td>
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

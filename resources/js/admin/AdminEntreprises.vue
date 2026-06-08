<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useApi } from '../composables/useApi.js'
import LogoContainer from '../components/ui/LogoContainer.vue'
import { usePagination } from '../composables/usePagination.js'
import { useEntreprisesStore } from '../stores/entreprisesStore.js'
import BaseButton from '../components/ui/BaseButton.vue'
import BaseModal from '../components/ui/BaseModal.vue'
import StatusBadge from '../components/admin/StatusBadge.vue'

const api = useApi()
const router = useRouter()
const store = useEntreprisesStore()
const { t, locale } = useI18n()

const search = ref('')
const sectorFilter = ref('')
const statusFilter = ref('')

const { data, loading, error, page, lastPage, perPage, total, isFirst, isLast, load, prev, next, reset } =
    usePagination((p) => api.get(
        `/admin/entreprises?page=${p}` +
        (search.value ? `&search=${encodeURIComponent(search.value)}` : '') +
        (sectorFilter.value ? `&type=${encodeURIComponent(sectorFilter.value)}` : '') +
        (statusFilter.value ? `&status=${encodeURIComponent(statusFilter.value)}` : '')
    ))

const deleting = ref(null)
const accepting = ref(null)
const deleteTarget = ref(null)
const deleteModal = ref(false)

let searchTimer = null
//gère le champ de recherche
watch(search, () => {
    clearTimeout(searchTimer)
    searchTimer = setTimeout(() => reset(), 400)
})
//on repart sur la première page aux changements dans les selects
watch([sectorFilter, statusFilter], () => reset())

onMounted(load)

const paginationFrom = computed(() => total.value === 0 ? 0 : (page.value - 1) * perPage.value + 1)
const paginationTo = computed(() => Math.min(page.value * perPage.value, total.value))

const pageNumbers = computed(() => {
    const pages = []
    const start = Math.max(1, page.value - 1)
    const end = Math.min(lastPage.value, page.value + 1)
    for (let i = start; i <= end; i++) pages.push(i)
    return pages
})

const goShow = (id) => router.push(`/admin/entreprises/${id}`)
const goEdit = (id) => router.push(`/admin/entreprises/${id}/edit`)
const goCreate = () => router.push('/admin/entreprises/new')

const askDelete = (e) => {
    deleteTarget.value = e
    deleteModal.value = true
}

const confirmDelete = async () => {
    const e = deleteTarget.value
    deleteModal.value = false
    deleteTarget.value = null
    deleting.value = e.id
    try {
        await api.del(`/admin/entreprises/${e.id}`)
        await load()
        store.refresh()
    } catch (err) {
        alert(err.message)
    } finally {
        deleting.value = null
    }
}

const accept = async (e) => {
    accepting.value = e.id
    try {
        const fd = new FormData()
        fd.append('is_active', '1')
        fd.append('is_validated', '1')
        fd.append('is_labelled', '1')
        fd.append('locale', locale.value)
        await api.upload(`/admin/entreprises/${e.id}`, fd)
        await load()
        store.refresh()
    } catch (err) {
        alert(err.message)
    } finally {
        accepting.value = null
    }
}

const SECTORS = [
    'banque', 'assurance', 'industrie', 'commerce', 'service',
    'technologie', 'sante', 'education', 'horlogerie', 'services_publics', 'fintech', 'autre',
]

const goPage = (n) => {
    if (n !== page.value) {
        page.value = n
        load()
    }
}
</script>

<template>
    <div>

        <div class="flex items-start justify-between mb-1">
            <div>
                <h1 class="text-2xl font-bold">{{ t('admin.nav_entreprises') }}</h1>
                <p v-if="total > 0" class="text-sm text-base-content/50 mt-0.5">
                    {{ t('admin.companies_registered_count', { n: total }) }}
                </p>
            </div>
            <BaseButton size="sm" @click="goCreate">{{ t('admin.new_company') }}</BaseButton>
        </div>

        <!-- Filtres -->
        <div class="flex flex-wrap gap-3 mt-4 mb-5">
            <label class="input input-sm input-bordered flex items-center gap-2 flex-1 min-w-[200px] max-w-sm">
                <svg class="w-4 h-4 text-base-content/40 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input v-model="search" type="text" :placeholder="t('admin.search_company')"
                    class="grow bg-transparent outline-none text-sm" />
            </label>

            <select v-model="sectorFilter" class="select select-sm select-bordered">
                <option value="">{{ t('admin.filter_sector') }}</option>
                <option v-for="s in SECTORS" :key="s" :value="s">
                    {{ t('inscription.type_' + s, s) }}
                </option>
            </select>

            <select v-model="statusFilter" class="select select-sm select-bordered">
                <option value="">{{ t('admin.filter_status') }}</option>
                <option value="active">{{ t('admin.status_active') }}</option>
                <option value="draft">{{ t('admin.status_draft') }}</option>
                <option value="inactive">{{ t('admin.status_suspended') }}</option>
                <option value="private">{{ t('admin.status_private') }}</option>
            </select>
        </div>

        <div v-if="loading" class="flex justify-center py-16">
            <span class="loading loading-spinner loading-lg text-brand"></span>
        </div>
        <div v-else-if="error" class="alert alert-error">{{ error }}</div>

        <template v-else-if="data">
            <!-- vue mobile avec les cartes -->
            <div class="md:hidden card bg-base-100 shadow-sm divide-y divide-base-200">
                <div v-for="e in data" :key="'m-' + e.id"
                    class="flex items-center gap-3 p-4 cursor-pointer active:bg-base-200" @click="goShow(e.id)">
                    <LogoContainer :logo-url="e.logo_url" :primary-color="e.primary_color" :name="e.name"
                        size="w-10 h-10" rounded="rounded" />

                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-sm truncate">{{ e.name }}</div>
                        <div class="text-xs text-base-content/50 mt-0.5">
                            {{ e.type ? t('inscription.type_' + e.type, e.type) : '-' }}
                            <span v-if="e.employee_count"> · {{ e.employee_count.toLocaleString('fr-CH') }} {{
                                t('admin.employees') }}</span>
                        </div>
                        <div class="text-xs font-semibold text-brand mt-1">{{ t('admin.col_collections').toUpperCase()
                            }}</div>
                    </div>

                    <div class="flex flex-col items-end gap-1.5 shrink-0">
                        <div class="flex items-center gap-1">
                            <span v-if="e.is_labelled" class="badge badge-xs badge-success font-semibold">2026</span>
                            <StatusBadge :entreprise="e" />
                        </div>
                    </div>

                    <svg class="w-4 h-4 text-base-content/30 shrink-0" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                    </svg>
                </div>
            </div>

            <!-- vue desktop avec le tableau -->
            <div class="hidden md:block card bg-base-100 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr class="text-xs text-brand font-semibold uppercase tracking-wide">
                                <th>{{ t('admin.col_name') }}</th>
                                <th>{{ t('admin.col_sector') }}</th>
                                <th class="text-right">{{ t('admin.col_headcount') }}</th>
                                <th class="text-right">{{ t('admin.col_collections') }}</th>
                                <th>{{ t('admin.col_label') }}</th>
                                <th>{{ t('admin.col_status') }}</th>
                                <th class="text-right">{{ t('admin.col_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="e in data" :key="e.id" class="hover cursor-pointer" @click="goShow(e.id)">
                                <td>
                                    <div class="flex items-center gap-2">
                                        <LogoContainer v-if="e.logo_url" :logo-url="e.logo_url"
                                            :primary-color="e.primary_color" :name="e.name" size="w-7 h-7"
                                            rounded="rounded" />
                                        <div v-else class="w-2.5 h-2.5 rounded-full shrink-0"
                                            :style="`background:${e.primary_color}`"></div>
                                        <span class="font-medium text-sm">{{ e.name }}</span>
                                    </div>
                                </td>
                                <td class="text-sm text-base-content/60">
                                    {{ e.type ? t('inscription.type_' + e.type, e.type) : '-' }}
                                </td>
                                <td class="text-right text-sm tabular-nums">
                                    {{ e.employee_count ? e.employee_count.toLocaleString('fr-CH') : '-' }}
                                </td>
                                <td class="text-right text-sm tabular-nums text-base-content/50">-</td>
                                <td>
                                    <span v-if="e.is_labelled"
                                        class="badge badge-sm badge-success font-semibold">2026</span>
                                    <span v-else class="text-base-content/30 text-sm">-</span>
                                </td>
                                <td>
                                    <StatusBadge :entreprise="e" />
                                </td>
                                <td @click.stop>
                                    <div class="flex gap-1 justify-end flex-wrap">
                                        <button v-if="!e.is_active || !e.is_validated"
                                            class="btn btn-xs btn-success text-white" :disabled="accepting === e.id"
                                            @click="accept(e)" :title="t('admin.accept_title')">
                                            <span v-if="accepting === e.id"
                                                class="loading loading-spinner loading-xs"></span>
                                            <span v-else>{{ t('admin.accept') }}</span>
                                        </button>
                                        <BaseButton variant="ghost" size="xs" @click="goShow(e.id)"
                                            :title="t('admin.show_title')">
                                            {{ t('admin.view_title') }}
                                        </BaseButton>
                                        <BaseButton variant="ghost" size="xs" @click="goEdit(e.id)"
                                            :title="t('admin.edit_title')">
                                            {{ t('admin.edit_title') }}
                                        </BaseButton>
                                        <BaseButton variant="ghost" size="xs" class="text-error" :disabled="deleting === e.id"
                                            @click="askDelete(e)" :title="t('admin.delete_title')">
                                            {{ t('admin.delete_title') }}
                                        </BaseButton>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="lastPage > 1" class="flex flex-col sm:flex-row items-center justify-between mt-4 gap-3">
                <p class="text-sm text-base-content/50">
                    {{ t('admin.showing_entries', { from: paginationFrom, to: paginationTo, total }) }}
                </p>
                <div class="flex items-center gap-1">
                    <BaseButton variant="ghost" size="sm" class="btn-square" :disabled="isFirst" @click="prev">‹</BaseButton>
                    <BaseButton v-for="n in pageNumbers" :key="n" size="sm" class="btn-square"
                        :variant="n === page ? 'primary' : 'ghost'"
                        @click="goPage(n)">
                        {{ n }}
                    </BaseButton>
                    <BaseButton variant="ghost" size="sm" class="btn-square" :disabled="isLast" @click="next">›</BaseButton>
                </div>
            </div>
        </template>


        <BaseModal v-model="deleteModal" :title="t('admin.delete_confirm_title')">
            <p class="text-sm text-base-content/70">
                {{ t('admin.delete_confirm_text', { name: deleteTarget?.name }) }}
            </p>
            <template #footer>
                <BaseButton variant="ghost" @click="deleteModal = false">{{ t('admin.cancel') }}</BaseButton>
                <BaseButton variant="outline" class="text-error border-error hover:bg-error hover:text-white"
                    @click="confirmDelete">
                    {{ t('admin.delete_title') }}
                </BaseButton>
            </template>
        </BaseModal>
    </div>
</template>

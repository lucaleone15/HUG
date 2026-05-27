<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useApi } from '../composables/useApi.js'
import { usePagination } from '../composables/usePagination.js'
import { useEntreprisesStore } from '../stores/entreprisesStore.js'
import BaseButton from '../components/ui/BaseButton.vue'
import BaseModal from '../components/ui/BaseModal.vue'
import StatusBadge from '../components/admin/StatusBadge.vue'

const api    = useApi()
const router = useRouter()
const store  = useEntreprisesStore()
const { t, locale } = useI18n()

const { data, loading, error, page, lastPage, isFirst, isLast, load, prev, next } =
    usePagination((p) => api.get(`/admin/entreprises?page=${p}`))

const deleting     = ref(null)
const kitSent      = ref(null)
const accepting    = ref(null)
const deleteTarget = ref(null)
const deleteModal  = ref(false)

onMounted(load)

const goShow   = (id) => router.push(`/admin/entreprises/${id}`)
const goEdit   = (id) => router.push(`/admin/entreprises/${id}/edit`)
const goCreate = ()   => router.push('/admin/entreprises/new')

const askDelete = (e) => {
    deleteTarget.value = e
    deleteModal.value  = true
}

const confirmDelete = async () => {
    const e = deleteTarget.value
    deleteModal.value  = false
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
        fd.append('is_active',    '1')
        fd.append('is_validated', '1')
        fd.append('is_labelled',  '1')
        fd.append('locale',       locale.value)
        await api.upload(`/admin/entreprises/${e.id}`, fd)
        await load()
        store.refresh()
    } catch (err) {
        alert(err.message)
    } finally {
        accepting.value = null
    }
}

const sendKit = async (e) => {
    try {
        await api.post(`/admin/entreprises/${e.id}/send-kit`)
        kitSent.value = e.id
        setTimeout(() => { kitSent.value = null }, 3000)
    } catch (err) {
        alert(err.message)
    }
}
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">{{ t('admin.nav_entreprises') }}</h1>
            <BaseButton size="sm" @click="goCreate">{{ t('admin.new_company') }}</BaseButton>
        </div>

        <div v-if="loading" class="flex justify-center py-16">
            <span class="loading loading-spinner loading-lg text-brand"></span>
        </div>
        <div v-else-if="error" class="alert alert-error">{{ error }}</div>

        <template v-else-if="data">
            <div class="card bg-base-100 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr class="text-xs text-base-content/50">
                                <th>{{ t('admin.col_company') }}</th>
                                <th>{{ t('admin.col_type') }}</th>
                                <th>{{ t('admin.col_status') }}</th>
                                <th class="text-right">{{ t('admin.col_eligible') }}</th>
                                <th class="text-right">{{ t('admin.col_trophy') }}</th>
                                <th class="text-right">{{ t('admin.col_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="e in data" :key="e.id" class="hover">
                                <td>
                                    <div class="flex items-center gap-2">
                                        <img v-if="e.logo_url" :src="e.logo_url" :alt="e.name"
                                            class="w-7 h-7 rounded object-contain bg-base-200">
                                        <div v-else class="w-1.5 h-6 rounded-full" :style="`background:${e.primary_color}`"></div>
                                        <div>
                                            <div class="font-medium text-sm">{{ e.name }}</div>
                                            <div class="text-xs text-base-content/40">{{ e.slug }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-xs text-base-content/60">{{ e.type ? t('inscription.type_' + e.type) : '—' }}</td>
                                <td><StatusBadge :entreprise="e" /></td>
                                <td class="text-right text-sm">
                                    <span class="text-emerald-600 font-semibold">{{ e.eligible_count ?? '—' }}</span>
                                    <span v-if="e.submission_count" class="text-base-content/40 text-xs"> / {{ e.submission_count }}</span>
                                </td>
                                <td class="text-right text-sm">
                                    <span v-if="e.trophy_rank">🏆 #{{ e.trophy_rank }}</span>
                                    <span v-else class="text-base-content/30">—</span>
                                </td>
                                <td>
                                    <div class="flex gap-1 justify-end flex-wrap">
                                        <button
                                            v-if="!e.is_active || !e.is_validated"
                                            class="btn btn-xs btn-success text-white"
                                            :disabled="accepting === e.id"
                                            @click="accept(e)"
                                            :title="t('admin.accept_title')"
                                        >
                                            <span v-if="accepting === e.id" class="loading loading-spinner loading-xs"></span>
                                            <span v-else>{{ t('admin.accept') }}</span>
                                        </button>
                                        <button class="btn btn-ghost btn-xs" @click="goShow(e.id)" :title="t('admin.show_title')">👁️</button>
                                        <button class="btn btn-ghost btn-xs" @click="goEdit(e.id)" :title="t('admin.edit_title')">✏️</button>
                                        <button
                                            class="btn btn-ghost btn-xs"
                                            :class="kitSent === e.id ? 'text-success' : ''"
                                            :disabled="!e.contact_email"
                                            @click="sendKit(e)"
                                            :title="t('admin.send_kit_title')"
                                        >
                                            {{ kitSent === e.id ? '✅' : '📦' }}
                                        </button>
                                        <button
                                            class="btn btn-ghost btn-xs text-error"
                                            :disabled="deleting === e.id"
                                            @click="askDelete(e)"
                                            :title="t('admin.delete_title')"
                                        >🗑️</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="lastPage > 1" class="flex justify-center mt-4 gap-2">
                <button class="btn btn-sm btn-ghost" :disabled="isFirst" @click="prev">←</button>
                <span class="text-sm self-center">{{ page }} / {{ lastPage }}</span>
                <button class="btn btn-sm btn-ghost" :disabled="isLast" @click="next">→</button>
            </div>
        </template>

        <!-- Modal de confirmation de suppression -->
        <BaseModal v-model="deleteModal" :title="t('admin.delete_confirm_title')">
            <p class="text-sm text-base-content/70">
                {{ t('admin.delete_confirm_text', { name: deleteTarget?.name }) }}
            </p>
            <template #footer>
                <BaseButton variant="ghost" @click="deleteModal = false">{{ t('admin.cancel') }}</BaseButton>
                <BaseButton variant="outline" class="text-error border-error hover:bg-error hover:text-white" @click="confirmDelete">
                    {{ t('admin.delete_title') }}
                </BaseButton>
            </template>
        </BaseModal>
    </div>
</template>

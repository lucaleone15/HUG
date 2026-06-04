<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useApi } from '../composables/useApi.js'
import { useI18n } from 'vue-i18n'
import StatusBadge from '../components/admin/StatusBadge.vue'
import BaseButton from '../components/ui/BaseButton.vue'
import LogoContainer from '../components/ui/LogoContainer.vue'

const route  = useRoute()
const router = useRouter()
const api    = useApi()
const { t } = useI18n()

const loading = ref(true)
const e       = ref(null)

// Collectes
const collectes        = ref([])
const collectesLoading = ref(false)
const showNewForm      = ref(false)
const newCollecte      = ref({ ondoc_url: '', rdv_date: '', label: '', is_active: true })
const savingCollecte   = ref(false)

onMounted(async () => {
    try {
        e.value = await api.get(`/admin/entreprises/${route.params.id}`)
    } catch (err) {
        alert(t('admin.form_error_load') + err.message)
        router.push('/admin/entreprises')
    } finally {
        loading.value = false
    }
    loadCollectes()
})

async function loadCollectes() {
    collectesLoading.value = true
    try {
        const res = await api.get(`/admin/entreprises/${route.params.id}/collectes`)
        collectes.value = res.data ?? []
    } finally {
        collectesLoading.value = false
    }
}

async function addCollecte() {
    if (!newCollecte.value.ondoc_url) return
    savingCollecte.value = true
    try {
        await api.post(`/admin/entreprises/${route.params.id}/collectes`, {
            ondoc_url: newCollecte.value.ondoc_url,
            rdv_date:  newCollecte.value.rdv_date  || null,
            label:     newCollecte.value.label     || null,
            is_active: newCollecte.value.is_active,
        })
        newCollecte.value = { ondoc_url: '', rdv_date: '', label: '', is_active: true }
        showNewForm.value = false
        await loadCollectes()
    } finally {
        savingCollecte.value = false
    }
}

async function toggleCollecte(c) {
    await api.put(`/admin/collectes/${c.id}`, { is_active: !c.is_active })
    await loadCollectes()
}

async function deleteCollecte(c) {
    if (!confirm(t('admin.collecte_confirm_delete'))) return
    await api.del(`/admin/collectes/${c.id}`)
    await loadCollectes()
}

const goEdit = () => router.push(`/admin/entreprises/${route.params.id}/edit`)
</script>

<template>
    <div class="w-full max-w-2xl">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6 flex-wrap">
            <button class="btn btn-ghost btn-sm" @click="router.back()">{{ t('admin.form_back') }}</button>
            <h1 class="text-xl sm:text-2xl font-bold flex-1">{{ t('admin.show_title') }}</h1>
            <BaseButton size="sm" @click="goEdit">{{ t('admin.edit_title') }}</BaseButton>
        </div>

        <div v-if="loading" class="flex justify-center py-16">
            <span class="loading loading-spinner loading-lg"></span>
        </div>

        <template v-else-if="e">
            <!-- Identité -->
            <div class="card bg-base-100 shadow-sm mb-4">
                <div class="card-body gap-4">
                    <div class="divider text-xs text-base-content/40 mt-0">{{ t('admin.form_section_identity') }}</div>

                    <div class="flex items-center gap-4">
                        <LogoContainer
                            :logo-url="e.logo_url"
                            :primary-color="e.primary_color"
                            :name="e.name"
                            size="w-14 h-14"
                            class="text-xl"
                        />
                        <div class="flex-1 min-w-0">
                            <div class="text-lg font-bold truncate">{{ e.name }}</div>
                            <div class="text-sm text-base-content/40 truncate">{{ e.slug }}</div>
                        </div>
                        <div class="shrink-0">
                            <StatusBadge :entreprise="e" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-base-content/50 text-xs uppercase tracking-wide">{{ t('admin.form_sector') }}</span>
                            <div class="font-medium mt-0.5">{{ e.type ? t('inscription.type_' + e.type) : '-' }}</div>
                        </div>
                        <div>
                            <span class="text-base-content/50 text-xs uppercase tracking-wide">{{ t('admin.form_employee_count') }}</span>
                            <div class="font-medium mt-0.5">{{ e.employee_count ?? '-' }}</div>
                        </div>
                    </div>

                    <div v-if="e.access_token" class="mt-2">
                        <span class="text-base-content/50 text-xs uppercase tracking-wide">{{ t('admin.company_url') }}</span>
                        <a :href="`/c/${e.access_token}`" target="_blank"
                           class="block text-xs text-primary hover:underline truncate mt-0.5 font-mono">
                            {{ `/c/${e.access_token}` }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Couleurs -->
            <div class="card bg-base-100 shadow-sm mb-4">
                <div class="card-body gap-3">
                    <div class="divider text-xs text-base-content/40 mt-0">{{ t('admin.form_section_colors') }}</div>
                    <div class="flex flex-wrap gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded border border-base-300 shrink-0"
                                :style="`background:${e.primary_color}`"></span>
                            <span class="text-xs">{{ e.primary_color }}</span>
                            <span class="text-base-content/50 text-xs">{{ t('admin.form_primary_color') }}</span>
                        </div>
                        <div v-if="e.secondary_color" class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded border border-base-300 shrink-0"
                                :style="`background:${e.secondary_color}`"></span>
                            <span class="text-xs">{{ e.secondary_color }}</span>
                            <span class="text-base-content/50 text-xs">{{ t('admin.form_secondary_color') }}</span>
                        </div>
                        <span v-else class="text-base-content/30 text-xs self-center">{{ t('admin.show_no_secondary_color') }}</span>
                    </div>
                </div>
            </div>

            <!-- Contact -->
            <div class="card bg-base-100 shadow-sm mb-4">
                <div class="card-body gap-3">
                    <div class="divider text-xs text-base-content/40 mt-0">{{ t('admin.form_section_contact') }}</div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-base-content/50 text-xs uppercase tracking-wide">{{ t('admin.form_contact_name') }}</span>
                            <div class="font-medium mt-0.5">{{ e.contact_name || '-' }}</div>
                        </div>
                        <div>
                            <span class="text-base-content/50 text-xs uppercase tracking-wide">{{ t('admin.form_contact_email') }}</span>
                            <div class="font-medium mt-0.5 break-all">
                                <a v-if="e.contact_email" :href="`mailto:${e.contact_email}`"
                                    class="link link-hover text-primary">{{ e.contact_email }}</a>
                                <span v-else>-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statut & Trophée -->
            <div class="card bg-base-100 shadow-sm mb-4">
                <div class="card-body gap-3">
                    <div class="divider text-xs text-base-content/40 mt-0">{{ t('admin.form_section_status') }}</div>
                    <div class="flex flex-wrap gap-3">
                        <div v-for="flag in [
                            { key: 'is_active',    label: t('admin.form_is_active') },
                            { key: 'is_validated', label: t('admin.form_is_validated') },
                            { key: 'is_labelled',  label: t('admin.form_is_labelled') },
                            { key: 'wants_trophy', label: t('admin.form_wants_trophy') },
                        ]" :key="flag.key" class="flex items-center gap-2 text-sm">
                            <span :class="e[flag.key] ? 'badge badge-success badge-sm' : 'badge badge-ghost badge-sm'">
                                {{ e[flag.key] ? '✓' : '✗' }}
                            </span>
                            {{ flag.label }}
                        </div>
                    </div>
                    <div v-if="e.trophy_rank" class="flex items-center gap-2 mt-1 text-sm">
                        <span class="text-brand font-bold">#{{ e.trophy_rank }}</span>
                        <span class="font-semibold">{{ t('admin.show_trophy_rank', { rank: e.trophy_rank }) }}</span>
                    </div>
                </div>
            </div>

            <!-- Collectes (campagnes OnDoc) -->
            <div class="card bg-base-100 shadow-sm mb-4">
                <div class="card-body gap-3">
                    <div class="flex items-center justify-between mb-1">
                        <div class="divider text-xs text-base-content/40 mt-0 flex-1">{{ t('admin.form_section_collectes') }}</div>
                        <button class="btn btn-ghost btn-xs ml-3" @click="showNewForm = !showNewForm">
                            {{ showNewForm ? t('admin.collecte_cancel') : t('admin.collecte_add') }}
                        </button>
                    </div>

                    <!-- Formulaire ajout collecte -->
                    <form v-if="showNewForm" @submit.prevent="addCollecte" class="bg-base-200 rounded-xl p-4 flex flex-col gap-3 mb-2">
                        <div>
                            <label class="text-xs text-base-content/50 uppercase tracking-wide block mb-1">{{ t('admin.form_ondoc_url') }} *</label>
                            <input v-model="newCollecte.ondoc_url" type="url" required
                                class="input input-bordered input-sm w-full font-mono text-xs"
                                placeholder="https://www.onedoc.ch/fr/..." />
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs text-base-content/50 uppercase tracking-wide block mb-1">{{ t('admin.form_rdv_date') }}</label>
                                <input v-model="newCollecte.rdv_date" type="date" class="input input-bordered input-sm w-full" />
                            </div>
                            <div>
                                <label class="text-xs text-base-content/50 uppercase tracking-wide block mb-1">{{ t('admin.collecte_label') }}</label>
                                <input v-model="newCollecte.label" type="text" class="input input-bordered input-sm w-full"
                                    :placeholder="t('admin.collecte_label_placeholder')" />
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <input v-model="newCollecte.is_active" type="checkbox" class="checkbox checkbox-sm checkbox-success" id="new-collecte-active" />
                            <label for="new-collecte-active" class="text-sm">{{ t('admin.collecte_is_active') }}</label>
                        </div>
                        <BaseButton type="submit" size="sm" :loading="savingCollecte">
                            {{ t('admin.collecte_save') }}
                        </BaseButton>
                    </form>

                    <!-- Liste des collectes -->
                    <div v-if="collectesLoading" class="flex justify-center py-4">
                        <span class="loading loading-spinner loading-sm"></span>
                    </div>
                    <div v-else-if="!collectes.length" class="text-sm text-base-content/40 italic py-2">
                        {{ t('admin.collecte_empty') }}
                    </div>
                    <ul v-else class="flex flex-col gap-2">
                        <li v-for="c in collectes" :key="c.id"
                            class="flex items-start gap-3 p-3 rounded-lg border border-base-200 text-sm">
                            <span :class="c.is_active ? 'badge badge-success badge-sm mt-0.5 shrink-0' : 'badge badge-ghost badge-sm mt-0.5 shrink-0'">
                                {{ c.is_active ? t('admin.collecte_status_active') : t('admin.collecte_status_inactive') }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <a :href="c.ondoc_url" target="_blank" rel="noopener"
                                   class="link link-hover text-primary text-xs font-mono break-all block">
                                    {{ c.ondoc_url }}
                                </a>
                                <div v-if="c.label || c.rdv_date" class="text-xs text-base-content/50 mt-0.5">
                                    <span v-if="c.label">{{ c.label }}</span>
                                    <span v-if="c.label && c.rdv_date"> · </span>
                                    <span v-if="c.rdv_date">{{ c.rdv_date }}</span>
                                </div>
                            </div>
                            <div class="flex gap-1 shrink-0">
                                <button class="btn btn-ghost btn-xs" @click="toggleCollecte(c)">
                                    {{ c.is_active ? t('admin.collecte_deactivate') : t('admin.collecte_activate') }}
                                </button>
                                <button class="btn btn-ghost btn-xs text-error" @click="deleteCollecte(c)">
                                    {{ t('admin.delete') }}
                                </button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Stats -->
            <div class="card bg-base-100 shadow-sm mb-4">
                <div class="card-body gap-3">
                    <div class="divider text-xs text-base-content/40 mt-0">{{ t('admin.show_section_stats') }}</div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-base-200 rounded-xl p-4 text-center">
                            <div class="text-xs text-base-content/50 uppercase tracking-wide mb-1">{{ t('admin.col_eligible') }}</div>
                            <div class="text-2xl font-bold text-emerald-600 tabular-nums">{{ e.eligible_count ?? '-' }}</div>
                            <div v-if="e.submission_count" class="text-xs text-base-content/40 mt-0.5">
                                / {{ e.submission_count }} {{ t('admin.show_submissions') }}
                            </div>
                        </div>
                        <div class="bg-base-200 rounded-xl p-4 text-center">
                            <div class="text-xs text-base-content/50 uppercase tracking-wide mb-1">{{ t('admin.col_trophy') }}</div>
                            <div class="text-2xl font-bold tabular-nums">
                                <span v-if="e.trophy_rank" class="text-brand">#{{ e.trophy_rank }}</span>
                                <span v-else class="text-base-content/30 text-lg">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

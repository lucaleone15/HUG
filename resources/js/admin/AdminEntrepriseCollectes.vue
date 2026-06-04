<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useApi } from '../composables/useApi.js'
import { useI18n } from 'vue-i18n'
import BaseButton from '../components/ui/BaseButton.vue'

const route  = useRoute()
const router = useRouter()
const api    = useApi()
const { t } = useI18n()

const loading = ref(true)
const e       = ref(null)

const collectes        = ref([])
const collectesLoading = ref(false)
const showNewForm      = ref(false)
const newCollecte      = ref({ ondoc_url: '', rdv_date: '', label: '', is_active: true })
const savingCollecte   = ref(false)

const editingId  = ref(null)
const editForm   = ref({ ondoc_url: '', rdv_date: '', label: '', is_active: true })
const savingEdit = ref(false)

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

function startEdit(c) {
    editingId.value = c.id
    editForm.value = {
        ondoc_url: c.ondoc_url,
        rdv_date:  c.rdv_date ?? '',
        label:     c.label    ?? '',
        is_active: c.is_active,
    }
}

function cancelEdit() {
    editingId.value = null
}

async function saveEdit(c) {
    savingEdit.value = true
    try {
        await api.put(`/admin/collectes/${c.id}`, {
            ondoc_url: editForm.value.ondoc_url,
            rdv_date:  editForm.value.rdv_date  || null,
            label:     editForm.value.label     || null,
            is_active: editForm.value.is_active,
        })
        editingId.value = null
        await loadCollectes()
    } finally {
        savingEdit.value = false
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
</script>

<template>
    <div class="w-full max-w-2xl">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6 flex-wrap">
            <button class="btn btn-ghost btn-sm" @click="router.push(`/admin/entreprises/${route.params.id}`)">
                {{ t('admin.form_back') }}
            </button>
            <h1 class="text-xl sm:text-2xl font-bold flex-1">
                {{ t('admin.form_section_collectes') }}
                <span v-if="e" class="text-base-content/40 font-normal text-base ml-2">— {{ e.name }}</span>
            </h1>
        </div>

        <div v-if="loading" class="flex justify-center py-16">
            <span class="loading loading-spinner loading-lg"></span>
        </div>

        <template v-else-if="e">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-3">
                    <!-- Section header + add button -->
                    <div class="flex items-center justify-between mb-1">
                        <div class="divider text-xs text-base-content/40 mt-0 flex-1">{{ t('admin.form_section_collectes') }}</div>
                        <button class="btn btn-ghost btn-xs ml-3" @click="showNewForm = !showNewForm">
                            {{ showNewForm ? t('admin.collecte_cancel') : t('admin.collecte_add') }}
                        </button>
                    </div>

                    <!-- Formulaire ajout -->
                    <form v-if="showNewForm" @submit.prevent="addCollecte"
                        class="bg-base-200 rounded-xl p-4 flex flex-col gap-3 mb-2">
                        <div>
                            <label class="text-xs text-base-content/50 uppercase tracking-wide block mb-1">
                                {{ t('admin.form_ondoc_url') }} *
                            </label>
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
                            <input v-model="newCollecte.is_active" type="checkbox"
                                class="checkbox checkbox-sm checkbox-success" id="new-collecte-active" />
                            <label for="new-collecte-active" class="text-sm">{{ t('admin.collecte_is_active') }}</label>
                        </div>
                        <BaseButton type="submit" size="sm" :loading="savingCollecte">
                            {{ t('admin.collecte_save') }}
                        </BaseButton>
                    </form>

                    <!-- Liste -->
                    <div v-if="collectesLoading" class="flex justify-center py-4">
                        <span class="loading loading-spinner loading-sm"></span>
                    </div>
                    <div v-else-if="!collectes.length" class="text-sm text-base-content/40 italic py-2">
                        {{ t('admin.collecte_empty') }}
                    </div>
                    <ul v-else class="flex flex-col gap-2">
                        <li v-for="c in collectes" :key="c.id"
                            class="rounded-lg border border-base-200 text-sm overflow-hidden">

                            <!-- Mode affichage -->
                            <div v-if="editingId !== c.id" class="flex items-start gap-3 p-3">
                                <span :class="c.is_active
                                    ? 'badge badge-success badge-sm mt-0.5 shrink-0'
                                    : 'badge badge-ghost badge-sm mt-0.5 shrink-0'">
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
                                    <button class="btn btn-ghost btn-xs" @click="startEdit(c)">
                                        {{ t('admin.collecte_edit') }}
                                    </button>
                                    <button class="btn btn-ghost btn-xs" @click="toggleCollecte(c)">
                                        {{ c.is_active ? t('admin.collecte_deactivate') : t('admin.collecte_activate') }}
                                    </button>
                                    <button class="btn btn-ghost btn-xs text-error" @click="deleteCollecte(c)">
                                        {{ t('admin.delete') }}
                                    </button>
                                </div>
                            </div>

                            <!-- Mode édition inline -->
                            <form v-else @submit.prevent="saveEdit(c)"
                                class="bg-base-200 p-4 flex flex-col gap-3">
                                <div>
                                    <label class="text-xs text-base-content/50 uppercase tracking-wide block mb-1">
                                        {{ t('admin.form_ondoc_url') }} *
                                    </label>
                                    <input v-model="editForm.ondoc_url" type="url" required
                                        class="input input-bordered input-sm w-full font-mono text-xs" />
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-xs text-base-content/50 uppercase tracking-wide block mb-1">{{ t('admin.form_rdv_date') }}</label>
                                        <input v-model="editForm.rdv_date" type="date" class="input input-bordered input-sm w-full" />
                                    </div>
                                    <div>
                                        <label class="text-xs text-base-content/50 uppercase tracking-wide block mb-1">{{ t('admin.collecte_label') }}</label>
                                        <input v-model="editForm.label" type="text" class="input input-bordered input-sm w-full"
                                            :placeholder="t('admin.collecte_label_placeholder')" />
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input v-model="editForm.is_active" type="checkbox"
                                        class="checkbox checkbox-sm checkbox-success" :id="`edit-active-${c.id}`" />
                                    <label :for="`edit-active-${c.id}`" class="text-sm">{{ t('admin.collecte_is_active') }}</label>
                                </div>
                                <div class="flex gap-2 justify-end">
                                    <button type="button" class="btn btn-ghost btn-sm" @click="cancelEdit">
                                        {{ t('admin.collecte_cancel') }}
                                    </button>
                                    <BaseButton type="submit" size="sm" :loading="savingEdit">
                                        {{ t('admin.collecte_update') }}
                                    </BaseButton>
                                </div>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </template>
    </div>
</template>

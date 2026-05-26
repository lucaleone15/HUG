<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useApi } from '../composables/useApi.js'
import { usePagination } from '../composables/usePagination.js'
import { useEntreprisesStore } from '../stores/entreprisesStore.js'

const api    = useApi()
const router = useRouter()
const store  = useEntreprisesStore()
const { locale } = useI18n()

const { data, loading, error, page, lastPage, isFirst, isLast, load, prev, next } =
    usePagination((p) => api.get(`/admin/entreprises?page=${p}`))

const deleting  = ref(null)
const kitSent   = ref(null)
const accepting = ref(null)

onMounted(load)

const goEdit   = (id) => router.push(`/admin/entreprises/${id}/edit`)
const goCreate = ()   => router.push('/admin/entreprises/new')

const destroy = async (e) => {
    if (!confirm(`Supprimer « ${e.name} » ? Cette action est irréversible.`)) return
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

const statusBadge = (e) => {
    if (!e.is_active && !e.is_validated) return { label: 'En attente', class: 'badge-warning' }
    if (e.is_validated && e.is_active)   return { label: 'Active',     class: 'badge-success' }
    if (e.is_validated && !e.is_active)  return { label: 'Suspendue',  class: 'badge-error' }
    return                                      { label: 'Brouillon',  class: 'badge-ghost' }
}
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Entreprises</h1>
            <button class="btn btn-sm bg-[#E30613] hover:bg-[#c0051f] text-white border-none" @click="goCreate">
                + Nouvelle entreprise
            </button>
        </div>

        <div v-if="loading" class="flex justify-center py-16">
            <span class="loading loading-spinner loading-lg text-[#E30613]"></span>
        </div>
        <div v-else-if="error" class="alert alert-error">{{ error }}</div>

        <template v-else-if="data">
            <div class="card bg-base-100 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr class="text-xs text-base-content/50">
                                <th>Entreprise</th>
                                <th>Type</th>
                                <th>Statut</th>
                                <th class="text-right">Éligibles</th>
                                <th class="text-right">Trophée</th>
                                <th class="text-right">Actions</th>
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
                                <td class="text-xs text-base-content/60 capitalize">{{ e.type ?? '—' }}</td>
                                <td>
                                    <span class="badge badge-sm" :class="statusBadge(e).class">
                                        {{ statusBadge(e).label }}
                                    </span>
                                    <span v-if="e.is_labelled" class="badge badge-sm badge-ghost ml-1">Label</span>
                                </td>
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
                                            title="Accepter l'entreprise"
                                        >
                                            <span v-if="accepting === e.id" class="loading loading-spinner loading-xs"></span>
                                            <span v-else>✓ Accepter</span>
                                        </button>
                                        <button class="btn btn-ghost btn-xs" @click="goEdit(e.id)" title="Modifier">✏️</button>
                                        <button
                                            class="btn btn-ghost btn-xs"
                                            :class="kitSent === e.id ? 'text-success' : ''"
                                            :disabled="!e.contact_email"
                                            @click="sendKit(e)"
                                            title="Envoyer kit"
                                        >
                                            {{ kitSent === e.id ? '✅' : '📦' }}
                                        </button>
                                        <button
                                            class="btn btn-ghost btn-xs text-error"
                                            :disabled="deleting === e.id"
                                            @click="destroy(e)"
                                            title="Supprimer"
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
    </div>
</template>

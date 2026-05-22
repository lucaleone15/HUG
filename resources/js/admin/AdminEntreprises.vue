<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useApi } from '../composables/useApi.js'

const api    = useApi()
const router = useRouter()

const data    = ref(null)
const loading = ref(true)
const error   = ref(null)
const page    = ref(1)
const deleting = ref(null)
const kitSent  = ref(null)

const load = async () => {
    loading.value = true
    try {
        data.value = await api.get(`/admin/entreprises?page=${page.value}`)
    } catch (e) {
        error.value = e.message
    } finally {
        loading.value = false
    }
}

onMounted(load)

const goEdit   = (id) => router.push(`/admin/entreprises/${id}/edit`)
const goCreate = ()   => router.push('/admin/entreprises/new')

const destroy = async (e) => {
    if (!confirm(`Supprimer « ${e.name} » ?`)) return
    deleting.value = e.id
    try {
        await api.del(`/admin/entreprises/${e.id}`)
        await load()
    } catch (err) {
        alert(err.message)
    } finally {
        deleting.value = null
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
    if (!e.is_active)   return { label: 'En attente',  class: 'badge-warning' }
    if (e.is_validated) return { label: 'Validée',     class: 'badge-success' }
    return                     { label: 'Active',      class: 'badge-info' }
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

        <template v-else>
            <div class="card bg-base-100 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr class="text-xs text-base-content/50">
                                <th>Entreprise</th>
                                <th>Type</th>
                                <th>Statut</th>
                                <th class="text-right">Employés</th>
                                <th class="text-right">Trophée</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="e in data.data" :key="e.id" class="hover">
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-6 rounded-full" :style="`background:${e.primary_color}`"></div>
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
                                <td class="text-right text-sm">{{ e.employee_count?.toLocaleString('fr-CH') ?? '—' }}</td>
                                <td class="text-right text-sm">
                                    <span v-if="e.trophy_rank">🏆 #{{ e.trophy_rank }}</span>
                                    <span v-else class="text-base-content/30">—</span>
                                </td>
                                <td>
                                    <div class="flex gap-1 justify-end">
                                        <button class="btn btn-ghost btn-xs" @click="goEdit(e.id)" title="Modifier">✏️</button>
                                        <button class="btn btn-ghost btn-xs" :class="kitSent === e.id ? 'text-success' : ''"
                                            @click="sendKit(e)" title="Envoyer kit">
                                            {{ kitSent === e.id ? '✅' : '📦' }}
                                        </button>
                                        <button class="btn btn-ghost btn-xs text-error"
                                            :disabled="deleting === e.id"
                                            @click="destroy(e)" title="Supprimer">🗑️</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="data.meta.last_page > 1" class="flex justify-center mt-4 gap-2">
                <button class="btn btn-sm btn-ghost" :disabled="page === 1" @click="page--; load()">←</button>
                <span class="text-sm self-center">{{ page }} / {{ data.meta.last_page }}</span>
                <button class="btn btn-sm btn-ghost" :disabled="page === data.meta.last_page" @click="page++; load()">→</button>
            </div>
        </template>
    </div>
</template>

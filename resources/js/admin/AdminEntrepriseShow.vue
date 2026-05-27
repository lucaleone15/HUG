<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useApi } from '../composables/useApi.js'
import { useI18n } from 'vue-i18n'
import StatusBadge from '../components/admin/StatusBadge.vue'
import BaseButton from '../components/ui/BaseButton.vue'

const route  = useRoute()
const router = useRouter()
const api    = useApi()
const { t } = useI18n()

const loading = ref(true)
const e       = ref(null)

onMounted(async () => {
    try {
        e.value = await api.get(`/admin/entreprises/${route.params.id}`)
    } catch (err) {
        alert(t('admin.form_error_load') + err.message)
        router.push('/admin/entreprises')
    } finally {
        loading.value = false
    }
})

const goEdit = () => router.push(`/admin/entreprises/${route.params.id}/edit`)
</script>

<template>
    <div class="max-w-2xl">
        <div class="flex items-center gap-3 mb-6">
            <button class="btn btn-ghost btn-sm" @click="router.back()">{{ t('admin.form_back') }}</button>
            <h1 class="text-2xl font-bold">{{ t('admin.show_title') }}</h1>
            <div class="ml-auto">
                <BaseButton size="sm" @click="goEdit">✏️ {{ t('admin.edit_title') }}</BaseButton>
            </div>
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
                        <img v-if="e.logo_url" :src="e.logo_url" :alt="e.name"
                            class="w-16 h-16 rounded-lg object-contain bg-base-200 p-1">
                        <div v-else
                            class="w-16 h-16 rounded-lg flex items-center justify-center text-white font-bold text-xl"
                            :style="`background:${e.primary_color}`">
                            {{ e.name?.charAt(0) }}
                        </div>
                        <div>
                            <div class="text-xl font-bold">{{ e.name }}</div>
                            <div class="text-sm text-base-content/40 font-mono">{{ e.slug }}</div>
                        </div>
                        <div class="ml-auto">
                            <StatusBadge :entreprise="e" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-base-content/50 text-xs uppercase tracking-wide">{{ t('admin.form_sector') }}</span>
                            <div class="font-medium mt-0.5">{{ e.type ? t('inscription.type_' + e.type) : '—' }}</div>
                        </div>
                        <div>
                            <span class="text-base-content/50 text-xs uppercase tracking-wide">{{ t('admin.form_employee_count') }}</span>
                            <div class="font-medium mt-0.5">{{ e.employee_count ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Couleurs -->
            <div class="card bg-base-100 shadow-sm mb-4">
                <div class="card-body gap-3">
                    <div class="divider text-xs text-base-content/40 mt-0">{{ t('admin.form_section_colors') }}</div>
                    <div class="flex gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <span
                                class="w-6 h-6 rounded border border-base-300"
                                :style="`background:${e.primary_color}`">
                            </span>
                            <span class="font-mono text-xs">{{ e.primary_color }}</span>
                            <span class="text-base-content/50 text-xs">{{ t('admin.form_primary_color') }}</span>
                        </div>
                        <div v-if="e.secondary_color" class="flex items-center gap-2">
                            <span
                                class="w-6 h-6 rounded border border-base-300"
                                :style="`background:${e.secondary_color}`">
                            </span>
                            <span class="font-mono text-xs">{{ e.secondary_color }}</span>
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
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-base-content/50 text-xs uppercase tracking-wide">{{ t('admin.form_contact_name') }}</span>
                            <div class="font-medium mt-0.5">{{ e.contact_name || '—' }}</div>
                        </div>
                        <div>
                            <span class="text-base-content/50 text-xs uppercase tracking-wide">{{ t('admin.form_contact_email') }}</span>
                            <div class="font-medium mt-0.5">
                                <a v-if="e.contact_email" :href="`mailto:${e.contact_email}`"
                                    class="link link-hover text-primary">{{ e.contact_email }}</a>
                                <span v-else>—</span>
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
                        <div class="flex items-center gap-2 text-sm">
                            <span :class="e.is_active ? 'badge badge-success badge-sm' : 'badge badge-ghost badge-sm'">
                                {{ e.is_active ? '✓' : '✗' }}
                            </span>
                            {{ t('admin.form_is_active') }}
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <span :class="e.is_validated ? 'badge badge-success badge-sm' : 'badge badge-ghost badge-sm'">
                                {{ e.is_validated ? '✓' : '✗' }}
                            </span>
                            {{ t('admin.form_is_validated') }}
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <span :class="e.is_labelled ? 'badge badge-success badge-sm' : 'badge badge-ghost badge-sm'">
                                {{ e.is_labelled ? '✓' : '✗' }}
                            </span>
                            {{ t('admin.form_is_labelled') }}
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <span :class="e.wants_trophy ? 'badge badge-warning badge-sm' : 'badge badge-ghost badge-sm'">
                                {{ e.wants_trophy ? '🏆' : '✗' }}
                            </span>
                            {{ t('admin.form_wants_trophy') }}
                        </div>
                    </div>

                    <div v-if="e.trophy_rank" class="flex items-center gap-2 mt-1 text-sm">
                        <span class="text-2xl">🏆</span>
                        <span class="font-semibold">{{ t('admin.show_trophy_rank', { rank: e.trophy_rank }) }}</span>
                    </div>
                </div>
            </div>

            <!-- Collecte CTS -->
            <div class="card bg-base-100 shadow-sm mb-4">
                <div class="card-body gap-3">
                    <div class="divider text-xs text-base-content/40 mt-0">{{ t('admin.form_section_cts') }}</div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-base-content/50 text-xs uppercase tracking-wide">{{ t('admin.form_rdv_date') }}</span>
                            <div class="font-medium mt-0.5">{{ e.rdv_date || '—' }}</div>
                        </div>
                        <div>
                            <span class="text-base-content/50 text-xs uppercase tracking-wide">{{ t('admin.form_rdv_url') }}</span>
                            <div class="font-medium mt-0.5">
                                <a v-if="e.rdv_url" :href="e.rdv_url" target="_blank" rel="noopener"
                                    class="link link-hover text-primary text-xs break-all">{{ e.rdv_url }}</a>
                                <span v-else>—</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="card bg-base-100 shadow-sm mb-4">
                <div class="card-body gap-3">
                    <div class="divider text-xs text-base-content/40 mt-0">{{ t('admin.show_section_stats') }}</div>
                    <div class="stats stats-horizontal w-full">
                        <div class="stat">
                            <div class="stat-title text-xs">{{ t('admin.col_eligible') }}</div>
                            <div class="stat-value text-2xl text-emerald-600">{{ e.eligible_count ?? '—' }}</div>
                            <div v-if="e.submission_count" class="stat-desc">/ {{ e.submission_count }} {{ t('admin.show_submissions') }}</div>
                        </div>
                        <div class="stat">
                            <div class="stat-title text-xs">{{ t('admin.col_trophy') }}</div>
                            <div class="stat-value text-2xl">
                                <span v-if="e.trophy_rank">🏆 #{{ e.trophy_rank }}</span>
                                <span v-else class="text-base-content/30 text-lg">—</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

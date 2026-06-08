<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useApi } from '../composables/useApi.js'
import { useI18n } from 'vue-i18n'
import StatusBadge from '../components/admin/StatusBadge.vue'
import BaseButton from '../components/ui/BaseButton.vue'
import LogoContainer from '../components/ui/LogoContainer.vue'

const route = useRoute()
const router = useRouter()
const api = useApi()
const { t } = useI18n()

const loading = ref(true)
const e = ref(null)

const collecteCount = ref(null)

onMounted(async () => {
    try {
        e.value = await api.get(`/admin/entreprises/${route.params.id}`)
    } catch (err) {
        alert(t('admin.form_error_load') + err.message)
        router.push('/admin/entreprises')
    } finally {
        loading.value = false
    }
    api.get(`/admin/entreprises/${route.params.id}/collectes`).then(res => {
        const list = res.data ?? []
        collecteCount.value = { total: list.length, active: list.filter(c => c.is_active).length }
    }).catch(() => { })
})

const goEdit = () => router.push(`/admin/entreprises/${route.params.id}/edit`)
const goCollectes = () => router.push(`/admin/entreprises/${route.params.id}/collectes`)

const copied = ref(false)
function copyLink() {
    const url = `${window.location.origin}/c/${e.value.access_token}`
    navigator.clipboard.writeText(url).then(() => {
        copied.value = true
        setTimeout(() => { copied.value = false }, 2000)
    })
}
</script>

<template>
    <div class="w-full max-w-2xl">

        <div class="flex items-center gap-3 mb-6 flex-wrap">
            <BaseButton variant="ghost" size="sm" @click="router.back()">{{ t('admin.form_back') }}</BaseButton>
            <h1 class="text-xl sm:text-2xl font-bold flex-1">{{ t('admin.show_title') }}</h1>
            <BaseButton size="sm" @click="goEdit">{{ t('admin.edit_title') }}</BaseButton>
        </div>

        <div v-if="loading" class="flex justify-center py-16">
            <span class="loading loading-spinner loading-lg"></span>
        </div>

        <template v-else-if="e">
            <!-- Identité de l'entreprise -->
            <div class="card bg-base-100 shadow-sm mb-4">
                <div class="card-body gap-4">
                    <div class="divider text-xs text-base-content/40 mt-0">{{ t('admin.form_section_identity') }}</div>

                    <div class="flex items-center gap-4">
                        <LogoContainer :logo-url="e.logo_url" :primary-color="e.primary_color" :name="e.name"
                            size="w-14 h-14" class="text-xl" />
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
                            <span class="text-base-content/50 text-xs uppercase tracking-wide">{{ t('admin.form_sector')
                                }}</span>
                            <div class="font-medium mt-0.5">{{ e.type ? t('inscription.type_' + e.type) : '-' }}</div>
                        </div>
                        <div>
                            <span class="text-base-content/50 text-xs uppercase tracking-wide">{{
                                t('admin.form_employee_count') }}</span>
                            <div class="font-medium mt-0.5">{{ e.employee_count ?? '-' }}</div>
                        </div>
                    </div>

                    <div v-if="e.access_token" class="mt-2">
                        <span class="text-base-content/50 text-xs uppercase tracking-wide">{{ t('admin.company_url')
                            }}</span>
                        <div class="flex items-center gap-2 mt-0.5">
                            <a :href="`/c/${e.access_token}`" target="_blank"
                                class="text-xs text-primary hover:underline truncate font-mono flex-1">
                                {{ `/c/${e.access_token}` }}
                            </a>
                            <BaseButton variant="ghost" size="xs" class="shrink-0" @click="copyLink">
                                {{ copied ? t('admin.copied') : t('admin.copy') }}
                            </BaseButton>
                        </div>
                    </div>
                </div>
            </div>

            <!-- couleurs -->
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
                        <span v-else class="text-base-content/30 text-xs self-center">{{
                            t('admin.show_no_secondary_color') }}</span>
                    </div>
                </div>
            </div>

            <!-- Section pour le contact -->
            <div class="card bg-base-100 shadow-sm mb-4">
                <div class="card-body gap-3">
                    <div class="divider text-xs text-base-content/40 mt-0">{{ t('admin.form_section_contact') }}</div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-base-content/50 text-xs uppercase tracking-wide">{{
                                t('admin.form_contact_name') }}</span>
                            <div class="font-medium mt-0.5">{{ e.contact_name || '-' }}</div>
                        </div>
                        <div>
                            <span class="text-base-content/50 text-xs uppercase tracking-wide">{{
                                t('admin.form_contact_email') }}</span>
                            <div class="font-medium mt-0.5 break-all">
                                <a v-if="e.contact_email" :href="`mailto:${e.contact_email}`"
                                    class="link link-hover text-primary">{{ e.contact_email }}</a>
                                <span v-else>-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- statut & trophée -->
            <div class="card bg-base-100 shadow-sm mb-4">
                <div class="card-body gap-3">
                    <div class="divider text-xs text-base-content/40 mt-0">{{ t('admin.form_section_status') }}</div>
                    <div class="flex flex-wrap gap-3">
                        <div v-for="flag in [
                            { key: 'is_active', label: t('admin.form_is_active') },
                            { key: 'is_validated', label: t('admin.form_is_validated') },
                            { key: 'is_labelled', label: t('admin.form_is_labelled') },
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

            <!-- Collectes avec un résumé et lien -->
            <div class="card bg-base-100 shadow-sm mb-4">
                <div class="card-body gap-3">
                    <div class="flex items-center justify-between mb-1">
                        <div class="divider text-xs text-base-content/40 mt-0 flex-1">{{
                            t('admin.form_section_collectes') }}</div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-base-content/60">
                            <span v-if="collecteCount === null" class="loading loading-spinner loading-xs"></span>
                            <template v-else-if="collecteCount.total === 0">
                                {{ t('admin.collecte_empty') }}
                            </template>
                            <template v-else>
                                <span class="font-semibold text-base-content">{{ collecteCount.active }}</span>
                                {{ t('admin.collecte_status_active') }}
                                <span class="text-base-content/30 mx-1">·</span>
                                {{ collecteCount.total }} {{ t('admin.collecte_total') }}
                            </template>
                        </div>
                        <BaseButton variant="ghost" size="sm" @click="goCollectes">
                            {{ t('admin.collecte_manage') }}
                        </BaseButton>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="card bg-base-100 shadow-sm mb-4">
                <div class="card-body gap-3">
                    <div class="divider text-xs text-base-content/40 mt-0">{{ t('admin.show_section_stats') }}</div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-base-200 rounded-xl p-4 text-center">
                            <div class="text-xs text-base-content/50 uppercase tracking-wide mb-1">{{
                                t('admin.col_eligible') }}
                            </div>
                            <div class="text-2xl font-bold text-emerald-600 tabular-nums">{{ e.eligible_count ?? '-' }}
                            </div>
                            <div v-if="e.submission_count" class="text-xs text-base-content/40 mt-0.5">
                                / {{ e.submission_count }} {{ t('admin.show_submissions') }}
                            </div>
                        </div>
                        <div class="bg-base-200 rounded-xl p-4 text-center">
                            <div class="text-xs text-base-content/50 uppercase tracking-wide mb-1">{{
                                t('admin.col_trophy') }}</div>
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

<script setup>
import { onMounted, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { RouterLink } from 'vue-router'
import { useApi } from '../composables/useApi.js'
import { useAsyncData } from '../composables/useAsyncData.js'
import BaseButton from '../components/ui/BaseButton.vue'

const { t } = useI18n()
const api = useApi()

const { data, loading, error, execute } = useAsyncData(async () => {
    const [dashboard, stats] = await Promise.all([
        api.get('/admin/dashboard'),
        api.get('/admin/campaign-stats').catch(() => null),
    ])
    return { ...dashboard, campaignStats: stats }
})

onMounted(execute)

const fmt = (n) => n?.toLocaleString('fr-CH') ?? '-'

const downloadKitCts = () => window.open('/pdfs/Kit_Communication_CTS.pdf', '_blank')

const usageKpis = computed(() => [
    { label: t('admin.funnel_visitors'), value: fmt(data.value?.funnel.page_viewed) },
    { label: t('admin.funnel_started'), value: fmt(data.value?.funnel.quiz_started) },
    { label: t('admin.funnel_completed'), value: fmt(data.value?.funnel.quiz_completed) },
    { label: t('admin.funnel_eligible'), value: fmt(data.value?.funnel.eligible) },
    { label: t('admin.funnel_rdv'), value: fmt(data.value?.funnel.rdv_clicked) },
])
</script>

<template>
    <div>
        <div v-if="loading" class="flex justify-center py-16">
            <span class="loading loading-spinner loading-lg text-brand"></span>
        </div>

        <div v-else-if="error" class="alert alert-error">{{ error }}</div>

        <template v-else-if="data">

            <section class="mb-8">
                <h2 class="text-lg font-bold mb-4">{{ t('admin.stats_activity') }}</h2>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-base-100 rounded-xl shadow-sm p-5">
                        <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wider mb-2">{{
                            t('admin.kpi_active_companies') }}</div>
                        <div class="text-3xl font-bold text-brand tabular-nums">{{ data.by_entreprise.length }}</div>
                    </div>
                    <div class="bg-base-100 rounded-xl shadow-sm p-5">
                        <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wider mb-2">{{
                            t('admin.kpi_collections_year') }}</div>
                        <div class="text-3xl font-bold text-brand tabular-nums">-</div>
                    </div>
                    <div class="bg-base-100 rounded-xl shadow-sm p-5">
                        <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wider mb-2">{{
                            t('admin.kpi_donations_total') }}</div>
                        <div class="text-3xl font-bold text-brand tabular-nums">{{
                            fmt(data.campaignStats?.donations_count) }}</div>
                        <div v-if="data.campaignStats?.lives_saved" class="text-xs text-emerald-600 mt-1 font-medium">
                            {{ fmt(data.campaignStats.lives_saved) }} {{ t('admin.lives_saved_stat') }}
                        </div>
                    </div>
                    <div class="bg-base-100 rounded-xl shadow-sm p-5">
                        <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wider mb-2">{{
                            t('admin.kpi_trophy_applications') }}</div>
                        <div class="text-3xl font-bold text-brand tabular-nums">-</div>
                    </div>
                </div>
            </section>

            <!-- Stats utilisation + classement trophee -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Stats utilisation -->
                <section>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold">{{ t('admin.stats_usage') }}</h2>
                        <RouterLink to="/admin/analytics" class="btn btn-ghost btn-xs text-brand gap-1">
                            {{ t('admin.see_all') }}
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                            </svg>
                        </RouterLink>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div v-for="kpi in usageKpis" :key="kpi.label" class="bg-base-100 rounded-xl shadow-sm p-4">
                            <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wider mb-1">{{
                                kpi.label }}</div>
                            <div class="text-2xl font-bold text-brand tabular-nums">{{ kpi.value }}</div>
                        </div>
                    </div>
                </section>

                <!-- trophee -->
                <section>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold">{{ t('admin.trophy_leaderboard') }}</h2>
                        <RouterLink to="/admin/entreprises" class="btn btn-ghost btn-xs text-brand gap-1">
                            {{ t('admin.see_all') }}
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                            </svg>
                        </RouterLink>
                    </div>
                    <div class="bg-base-100 rounded-xl shadow-sm overflow-hidden">
                        <table class="table table-sm">
                            <thead>
                                <tr class="text-xs text-brand font-semibold uppercase tracking-wide">
                                    <th class="w-8">#</th>
                                    <th>{{ t('admin.col_company') }}</th>
                                    <th class="hidden sm:table-cell">{{ t('admin.col_edition') }}</th>
                                    <th class="text-right">{{ t('admin.col_eligibility_rate_short') }}</th>
                                    <th class="hidden sm:table-cell text-right">{{ t('admin.col_rdv_count') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(e, i) in data.by_entreprise.slice(0, 5)" :key="e.id" class="hover">
                                    <td class="text-base-content/40 text-xs font-medium">{{ i + 1 }}</td>
                                    <td class="font-medium text-sm">{{ e.name }}</td>
                                    <td class="hidden sm:table-cell text-base-content/40 text-xs">-</td>
                                    <td class="text-right text-sm">
                                        <span v-if="e.eligibility_rate !== null" class="font-semibold"
                                            :class="e.eligibility_rate >= 0.3 ? 'text-brand' : ''">
                                            {{ (e.eligibility_rate * 100).toFixed(0) }}%
                                        </span>
                                        <span v-else class="text-base-content/30">-</span>
                                    </td>
                                    <td class="hidden sm:table-cell text-right text-sm font-semibold tabular-nums">{{
                                        fmt(e.eligible_count) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <!-- Détail par entreprise -->
            <section>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold uppercase tracking-widest text-base-content/40">{{
                        t('admin.by_company') }}</h2>
                    <RouterLink to="/admin/entreprises" class="btn btn-ghost btn-xs text-brand gap-1">
                        {{ t('admin.see_all') }}
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                        </svg>
                    </RouterLink>
                </div>
                <div class="card bg-base-100 shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="table table-sm">
                            <thead>
                                <tr class="text-xs text-base-content/50">
                                    <th>#</th>
                                    <th>{{ t('admin.col_company') }}</th>
                                    <th class="text-right">{{ t('admin.col_eligible') }}</th>
                                    <th class="hidden sm:table-cell text-right">{{ t('admin.col_submissions') }}</th>
                                    <th class="text-right">{{ t('admin.col_eligibility_rate') }}</th>
                                    <th class="hidden md:table-cell text-right">{{ t('admin.col_participation') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(e, i) in data.by_entreprise" :key="e.id" class="hover">
                                    <td class="text-base-content/40 text-xs">{{ i + 1 }}</td>
                                    <td class="font-medium">{{ e.name }}</td>
                                    <td class="text-right font-semibold text-emerald-600">{{ fmt(e.eligible_count) }}
                                    </td>
                                    <td class="hidden sm:table-cell text-right text-base-content/60">{{
                                        fmt(e.submission_count) }}</td>
                                    <td class="text-right">
                                        <span v-if="e.eligibility_rate !== null">{{ (e.eligibility_rate *
                                            100).toFixed(1) }}%</span>
                                        <span v-else class="text-base-content/30">-</span>
                                    </td>
                                    <td class="hidden md:table-cell text-right">
                                        <span v-if="e.participation_rate !== null">{{ (e.participation_rate *
                                            100).toFixed(1) }}%</span>
                                        <span v-else class="text-base-content/30">-</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Ressources CTS -->
            <section class="mt-8">
                <h2 class="text-lg font-bold mb-3">{{ t('admin.settings_kit_cts_section') }}</h2>
                <div class="card bg-base-100 shadow-sm overflow-hidden">
                    <!-- Mobile : nom + bouton icône -->
                    <div class="sm:hidden flex items-center gap-3 p-4">
                        <p class="font-semibold text-sm flex-1 truncate">Kit_Communication_CTS.pdf</p>
                        <BaseButton variant="ghost" size="sm" class="text-brand shrink-0 btn-square" @click="downloadKitCts">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                        </BaseButton>
                    </div>

                    <!-- Desktop : aperçu PDF + infos -->
                    <div class="hidden sm:flex">
                        <div class="w-36 shrink-0 overflow-hidden border-r border-base-200 cursor-pointer bg-white" style="height: calc(144px * 297 / 210)" @click="downloadKitCts">
                            <iframe
                                src="/pdfs/Kit_Communication_CTS.pdf#toolbar=0&navpanes=0&scrollbar=0&view=FitH&page=1"
                                class="w-full h-full pointer-events-none border-0"
                            />
                        </div>
                        <div class="flex-1 p-4 flex items-center justify-between gap-4 min-w-0">
                            <div class="min-w-0">
                                <p class="font-semibold text-sm">Kit_Communication_CTS.pdf</p>
                                <p class="text-xs text-base-content/40 mt-1 leading-relaxed">{{ t('admin.settings_kit_cts_desc') }}</p>
                            </div>
                            <BaseButton variant="ghost" size="sm" class="text-brand shrink-0 gap-2" @click="downloadKitCts">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                {{ t('admin.settings_kit_cts_download') }}
                            </BaseButton>
                        </div>
                    </div>
                </div>
            </section>
        </template>
    </div>
</template>

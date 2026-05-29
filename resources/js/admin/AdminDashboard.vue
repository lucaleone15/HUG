<script setup>
import { onMounted, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { RouterLink } from 'vue-router'
import { useApi } from '../composables/useApi.js'
import { useAsyncData } from '../composables/useAsyncData.js'

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

const fmt = (n) => n?.toLocaleString('fr-CH') ?? '—'
const pct = (a, b) => (b > 0 ? (a / b * 100).toFixed(1) + '%' : '—')

const usageKpis = computed(() => [
    { label: t('admin.funnel_visitors'),  value: fmt(data.value?.funnel.page_viewed)    },
    { label: t('admin.funnel_started'),   value: fmt(data.value?.funnel.quiz_started)   },
    { label: t('admin.funnel_completed'), value: fmt(data.value?.funnel.quiz_completed) },
    { label: t('admin.funnel_eligible'),  value: fmt(data.value?.funnel.eligible)       },
    { label: t('admin.funnel_rdv'),       value: fmt(data.value?.funnel.rdv_clicked)    },
])
</script>

<template>
    <div>
        <div v-if="loading" class="flex justify-center py-16">
            <span class="loading loading-spinner loading-lg text-brand"></span>
        </div>

        <div v-else-if="error" class="alert alert-error">{{ error }}</div>

        <template v-else-if="data">
            <!-- Statistiques d'activité -->
            <section class="mb-8">
                <h2 class="text-lg font-bold mb-4">{{ t('admin.stats_activity') }}</h2>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-base-100 rounded-xl shadow-sm p-5">
                        <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wider mb-2">{{ t('admin.kpi_active_companies') }}</div>
                        <div class="text-3xl font-bold text-brand tabular-nums">{{ data.by_entreprise.length }}</div>
                    </div>
                    <div class="bg-base-100 rounded-xl shadow-sm p-5">
                        <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wider mb-2">{{ t('admin.kpi_collections_year') }}</div>
                        <div class="text-3xl font-bold text-brand tabular-nums">—</div>
                    </div>
                    <div class="bg-base-100 rounded-xl shadow-sm p-5">
                        <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wider mb-2">{{ t('admin.kpi_donations_total') }}</div>
                        <div class="text-3xl font-bold text-brand tabular-nums">{{ fmt(data.campaignStats?.donations_count) }}</div>
                        <div v-if="data.campaignStats?.lives_saved" class="text-xs text-emerald-600 mt-1 font-medium">
                            {{ fmt(data.campaignStats.lives_saved) }} {{ t('admin.lives_saved_stat') }}
                        </div>
                    </div>
                    <div class="bg-base-100 rounded-xl shadow-sm p-5">
                        <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wider mb-2">{{ t('admin.kpi_trophy_applications') }}</div>
                        <div class="text-3xl font-bold text-brand tabular-nums">—</div>
                    </div>
                </div>
            </section>

            <!-- Statistiques d'utilisation + Classement Trophée -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Statistiques d'utilisation -->
                <section>
                    <h2 class="text-lg font-bold mb-4">{{ t('admin.stats_usage') }}</h2>
                    <div class="grid grid-cols-2 gap-3">
                        <div
                            v-for="kpi in usageKpis"
                            :key="kpi.label"
                            class="bg-base-100 rounded-xl shadow-sm p-4"
                        >
                            <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wider mb-1">{{ kpi.label }}</div>
                            <div class="text-2xl font-bold text-brand tabular-nums">{{ kpi.value }}</div>
                        </div>
                    </div>
                </section>

                <!-- Classement Trophée -->
                <section>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold">{{ t('admin.trophy_leaderboard') }}</h2>
                        <RouterLink to="/admin/entreprises" class="text-sm text-brand hover:underline font-medium">
                            {{ t('admin.see_all') }} →
                        </RouterLink>
                    </div>
                    <div class="bg-base-100 rounded-xl shadow-sm overflow-hidden">
                        <table class="table table-sm">
                            <thead>
                                <tr class="text-xs text-brand font-semibold uppercase tracking-wide">
                                    <th class="w-8">#</th>
                                    <th>{{ t('admin.col_company') }}</th>
                                    <th>{{ t('admin.col_edition') }}</th>
                                    <th class="text-right">{{ t('admin.col_eligibility_rate_short') }}</th>
                                    <th class="text-right">{{ t('admin.col_rdv_count') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(e, i) in data.by_entreprise.slice(0, 5)"
                                    :key="e.id"
                                    class="hover"
                                >
                                    <td class="text-base-content/40 text-xs font-medium">{{ i + 1 }}</td>
                                    <td class="font-medium text-sm">{{ e.name }}</td>
                                    <td class="text-base-content/40 text-xs">—</td>
                                    <td class="text-right text-sm">
                                        <span v-if="e.eligibility_rate !== null" class="font-semibold" :class="e.eligibility_rate >= 0.3 ? 'text-brand' : ''">
                                            {{ (e.eligibility_rate * 100).toFixed(0) }}%
                                        </span>
                                        <span v-else class="text-base-content/30">—</span>
                                    </td>
                                    <td class="text-right text-sm font-semibold tabular-nums">{{ fmt(e.eligible_count) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Lien mobile voir classement complet -->
                    <RouterLink
                        to="/admin/entreprises"
                        class="lg:hidden mt-3 flex items-center justify-center w-full bg-site-ink text-white rounded-xl py-3 text-sm font-semibold"
                    >
                        {{ t('admin.see_all_leaderboard') }}
                    </RouterLink>
                </section>
            </div>

            <!-- Détail par entreprise (entonnoir complet) -->
            <section class="mt-8">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-base-content/40 mb-3">{{ t('admin.by_company') }}</h2>
                <div class="card bg-base-100 shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="table table-sm">
                            <thead>
                                <tr class="text-xs text-base-content/50">
                                    <th>#</th>
                                    <th>{{ t('admin.col_company') }}</th>
                                    <th class="text-right">{{ t('admin.col_eligible') }}</th>
                                    <th class="text-right">{{ t('admin.col_submissions') }}</th>
                                    <th class="text-right">{{ t('admin.col_eligibility_rate') }}</th>
                                    <th class="text-right">{{ t('admin.col_participation') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(e, i) in data.by_entreprise" :key="e.id" class="hover">
                                    <td class="text-base-content/40 text-xs">{{ i + 1 }}</td>
                                    <td class="font-medium">{{ e.name }}</td>
                                    <td class="text-right font-semibold text-emerald-600">{{ fmt(e.eligible_count) }}</td>
                                    <td class="text-right text-base-content/60">{{ fmt(e.submission_count) }}</td>
                                    <td class="text-right">
                                        <span v-if="e.eligibility_rate !== null">{{ (e.eligibility_rate * 100).toFixed(1) }}%</span>
                                        <span v-else class="text-base-content/30">—</span>
                                    </td>
                                    <td class="text-right">
                                        <span v-if="e.participation_rate !== null">{{ (e.participation_rate * 100).toFixed(1) }}%</span>
                                        <span v-else class="text-base-content/30">—</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </template>
    </div>
</template>

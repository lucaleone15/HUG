<script setup>
import { onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useApi } from '../composables/useApi.js'
import { useAsyncData } from '../composables/useAsyncData.js'

const { t } = useI18n()
const api = useApi()
const { data, loading, error, execute } = useAsyncData(() => api.get('/admin/dashboard'))

onMounted(execute)

const pct = (a, b) => (b > 0 ? (a / b * 100).toFixed(1) + '%' : '—')
const fmt = (n) => n?.toLocaleString('fr-CH') ?? '—'
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold mb-6">{{ t('admin.dashboard_title') }}</h1>

        <div v-if="loading" class="flex justify-center py-16">
            <span class="loading loading-spinner loading-lg text-brand"></span>
        </div>

        <div v-else-if="error" class="alert alert-error">{{ error }}</div>

        <template v-else-if="data">
            <!-- Entonnoir KPI -->
            <section class="mb-8">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-base-content/40 mb-3">{{ t('admin.funnel_title') }}</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                    <div class="stat bg-base-100 rounded-xl shadow-sm p-4">
                        <div class="stat-title text-xs">{{ t('admin.funnel_visitors') }}</div>
                        <div class="stat-value text-xl text-blue-600">{{ fmt(data.funnel.page_viewed) }}</div>
                    </div>
                    <div class="stat bg-base-100 rounded-xl shadow-sm p-4">
                        <div class="stat-title text-xs">{{ t('admin.funnel_started') }}</div>
                        <div class="stat-value text-xl text-violet-600">{{ fmt(data.funnel.quiz_started) }}</div>
                        <div class="stat-desc">{{ pct(data.funnel.quiz_started, data.funnel.page_viewed) }} {{ t('admin.funnel_of_visitors') }}</div>
                    </div>
                    <div class="stat bg-base-100 rounded-xl shadow-sm p-4">
                        <div class="stat-title text-xs">{{ t('admin.funnel_completed') }}</div>
                        <div class="stat-value text-xl text-amber-600">{{ fmt(data.funnel.quiz_completed) }}</div>
                        <div class="stat-desc">{{ pct(data.funnel.quiz_completed, data.funnel.quiz_started) }} {{ t('admin.funnel_completion') }}</div>
                    </div>
                    <div class="stat bg-base-100 rounded-xl shadow-sm p-4">
                        <div class="stat-title text-xs">{{ t('admin.funnel_eligible') }}</div>
                        <div class="stat-value text-xl text-emerald-600">{{ fmt(data.funnel.eligible) }}</div>
                        <div class="stat-desc">{{ pct(data.funnel.eligible, data.funnel.quiz_completed) }} {{ t('admin.funnel_eligibility') }}</div>
                    </div>
                    <div class="stat bg-base-100 rounded-xl shadow-sm p-4">
                        <div class="stat-title text-xs">{{ t('admin.funnel_rdv') }}</div>
                        <div class="stat-value text-xl text-brand">{{ fmt(data.funnel.rdv_clicked) }}</div>
                        <div class="stat-desc">{{ pct(data.funnel.rdv_clicked, data.funnel.eligible) }} {{ t('admin.funnel_of_eligible') }}</div>
                    </div>
                </div>
            </section>

            <!-- Par entreprise -->
            <section>
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
                                        <span v-if="e.eligibility_rate !== null">
                                            {{ (e.eligibility_rate * 100).toFixed(1) }}%
                                        </span>
                                        <span v-else class="text-base-content/30">—</span>
                                    </td>
                                    <td class="text-right">
                                        <span v-if="e.participation_rate !== null">
                                            {{ (e.participation_rate * 100).toFixed(1) }}%
                                        </span>
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

<script setup>
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useApi } from '../composables/useApi.js'
import BaseButton from '../components/ui/BaseButton.vue'

const { t } = useI18n()
const api     = useApi()
const current = ref(null)
const loading = ref(true)
const saving  = ref(false)
const saved   = ref(false)
const error       = ref(null)
const saveError   = ref(null)
const fieldErrors = ref({})

const form = ref({ donations_count: 0, lives_saved: 0, hug_hospitals_count: 0 })

onMounted(async () => {
    try {
        current.value = await api.get('/admin/campaign-stats')
        form.value = {
            donations_count:     current.value.donations_count,
            lives_saved:         current.value.lives_saved,
            hug_hospitals_count: current.value.hug_hospitals_count,
        }
    } catch (e) {
        error.value = e.message
    } finally {
        loading.value = false
    }
})

const save = async () => {
    saving.value      = true
    saved.value       = false
    saveError.value   = null
    fieldErrors.value = {}
    try {
        current.value = await api.put('/admin/campaign-stats', form.value)
        saved.value = true
        setTimeout(() => { saved.value = false }, 3000)
    } catch (e) {
        if (e.errors) fieldErrors.value = e.errors
        else saveError.value = e.message
    } finally {
        saving.value = false
    }
}

const fieldError = (key) => fieldErrors.value[key]?.[0]

const fmt = (n) => n?.toLocaleString('fr-CH') ?? '-'
</script>

<template>
    <div class="max-w-xl">
        <h1 class="text-2xl font-bold mb-6">{{ t('admin.campaign_stats_title') }}</h1>

        <div v-if="loading" class="flex justify-center py-16">
            <span class="loading loading-spinner loading-lg text-brand"></span>
        </div>
        <div v-else-if="error" class="alert alert-error">{{ error }}</div>

        <template v-else>
            <!-- Stats actuelles -->
            <div class="bg-base-100 rounded-xl shadow-sm divide-y divide-base-200 mb-6">
                <div class="flex items-center justify-between px-5 py-3.5">
                    <span class="text-sm text-base-content/55">{{ t('admin.donations_collected') }}</span>
                    <span class="font-bold tabular-nums text-brand">{{ fmt(current.donations_count) }}</span>
                </div>
                <div class="flex items-center justify-between px-5 py-3.5">
                    <span class="text-sm text-base-content/55">{{ t('admin.lives_saved_stat') }}</span>
                    <span class="font-bold tabular-nums">{{ fmt(current.lives_saved) }}</span>
                </div>
                <div class="flex items-center justify-between px-5 py-3.5">
                    <span class="text-sm text-base-content/55">{{ t('admin.hug_hospitals') }}</span>
                    <span class="font-bold tabular-nums">{{ fmt(current.hug_hospitals_count) }}</span>
                </div>
            </div>

            <!-- Calculés (lecture seule) -->
            <div class="card bg-base-200 mb-6">
                <div class="card-body py-3 px-4 text-sm text-base-content/60 flex flex-row gap-6 flex-wrap">
                    <span>{{ fmt(current.eligible_count) }} {{ t('admin.eligible_participants') }}</span>
                    <span>{{ fmt(current.entreprises_count) }} {{ t('admin.active_companies') }}</span>
                    <span>{{ fmt(current.labelled_count) }} {{ t('admin.labelled_companies') }}</span>
                    <span v-if="current.updated_by">
                        {{ t('admin.updated_by') }} <strong>{{ current.updated_by.name }}</strong>
                    </span>
                </div>
            </div>

            <!-- Formulaire de mise à jour -->
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-4">
                    <h2 class="font-semibold">{{ t('admin.update_stats_title') }}</h2>
                    <p class="text-sm text-base-content/50">{{ t('admin.update_stats_desc') }}</p>

                    <div v-if="saved" class="text-sm text-success font-medium py-1">{{ t('admin.saved_success') }}</div>
                    <div v-if="saveError" class="alert alert-error text-sm py-2">{{ saveError }}</div>

                    <form class="grid grid-cols-1 gap-3" @submit.prevent="save">
                        <label class="form-control">
                            <div class="label"><span class="label-text">{{ t('admin.donations_count_label') }}</span></div>
                            <input v-model.number="form.donations_count" type="number" min="0" required
                                class="input input-bordered input-sm" :class="fieldError('donations_count') ? 'input-error' : ''">
                            <div v-if="fieldError('donations_count')" class="label">
                                <span class="label-text-alt text-error">{{ fieldError('donations_count') }}</span>
                            </div>
                        </label>
                        <label class="form-control">
                            <div class="label"><span class="label-text">{{ t('admin.lives_saved_label') }}</span></div>
                            <input v-model.number="form.lives_saved" type="number" min="0" required
                                class="input input-bordered input-sm" :class="fieldError('lives_saved') ? 'input-error' : ''">
                            <div v-if="fieldError('lives_saved')" class="label">
                                <span class="label-text-alt text-error">{{ fieldError('lives_saved') }}</span>
                            </div>
                        </label>
                        <label class="form-control">
                            <div class="label"><span class="label-text">{{ t('admin.hug_hospitals_label') }}</span></div>
                            <input v-model.number="form.hug_hospitals_count" type="number" min="0" required
                                class="input input-bordered input-sm" :class="fieldError('hug_hospitals_count') ? 'input-error' : ''">
                            <div v-if="fieldError('hug_hospitals_count')" class="label">
                                <span class="label-text-alt text-error">{{ fieldError('hug_hospitals_count') }}</span>
                            </div>
                        </label>
                        <div class="card-actions justify-end mt-2">
                            <BaseButton type="submit" size="sm" :loading="saving">{{ t('admin.save') }}</BaseButton>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
</template>

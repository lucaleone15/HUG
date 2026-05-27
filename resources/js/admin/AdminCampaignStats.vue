<script setup>
import { ref, onMounted } from 'vue'
import { useApi } from '../composables/useApi.js'
import BaseButton from '../components/ui/BaseButton.vue'

const api     = useApi()
const current = ref(null)
const loading = ref(true)
const saving  = ref(false)
const saved   = ref(false)
const error   = ref(null)

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
    saving.value = true
    saved.value  = false
    try {
        current.value = await api.put('/admin/campaign-stats', form.value)
        saved.value = true
        setTimeout(() => { saved.value = false }, 3000)
    } catch (e) {
        alert(e.message)
    } finally {
        saving.value = false
    }
}

const fmt = (n) => n?.toLocaleString('fr-CH') ?? '—'
</script>

<template>
    <div class="max-w-xl">
        <h1 class="text-2xl font-bold mb-6">Stats de campagne</h1>

        <div v-if="loading" class="flex justify-center py-16">
            <span class="loading loading-spinner loading-lg text-[#E30613]"></span>
        </div>
        <div v-else-if="error" class="alert alert-error">{{ error }}</div>

        <template v-else>
            <!-- Stats actuelles -->
            <div class="grid grid-cols-3 gap-3 mb-6">
                <div class="stat bg-base-100 rounded-xl shadow-sm p-4 text-center">
                    <div class="text-2xl font-bold text-[#E30613]">{{ fmt(current.donations_count) }}</div>
                    <div class="text-xs text-base-content/50 mt-1">dons collectés</div>
                </div>
                <div class="stat bg-base-100 rounded-xl shadow-sm p-4 text-center">
                    <div class="text-2xl font-bold text-emerald-600">{{ fmt(current.lives_saved) }}</div>
                    <div class="text-xs text-base-content/50 mt-1">vies sauvées</div>
                </div>
                <div class="stat bg-base-100 rounded-xl shadow-sm p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ fmt(current.hug_hospitals_count) }}</div>
                    <div class="text-xs text-base-content/50 mt-1">hôpitaux HUG</div>
                </div>
            </div>

            <!-- Calculés (lecture seule) -->
            <div class="card bg-base-200 mb-6">
                <div class="card-body py-3 px-4 text-sm text-base-content/60 flex flex-row gap-6 flex-wrap">
                    <span>{{ fmt(current.eligible_count) }} participants éligibles</span>
                    <span>{{ fmt(current.entreprises_count) }} entreprises actives</span>
                    <span>{{ fmt(current.labelled_count) }} entreprises labelisées</span>
                    <span v-if="current.updated_by">
                        Mis à jour par <strong>{{ current.updated_by.name }}</strong>
                    </span>
                </div>
            </div>

            <!-- Formulaire de mise à jour -->
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-4">
                    <h2 class="font-semibold">Mettre à jour les chiffres CTS</h2>
                    <p class="text-sm text-base-content/50">Ces données sont saisies manuellement depuis les rapports officiels CTS/HUG.</p>

                    <div v-if="saved" class="alert alert-success text-sm py-2">✅ Enregistré avec succès</div>

                    <form class="grid grid-cols-1 gap-3" @submit.prevent="save">
                        <label class="form-control">
                            <div class="label"><span class="label-text">Nombre de dons collectés</span></div>
                            <input v-model.number="form.donations_count" type="number" min="0" required class="input input-bordered input-sm">
                        </label>
                        <label class="form-control">
                            <div class="label"><span class="label-text">Vies sauvées</span></div>
                            <input v-model.number="form.lives_saved" type="number" min="0" required class="input input-bordered input-sm">
                        </label>
                        <label class="form-control">
                            <div class="label"><span class="label-text">Hôpitaux HUG impliqués</span></div>
                            <input v-model.number="form.hug_hospitals_count" type="number" min="0" required class="input input-bordered input-sm">
                        </label>
                        <div class="card-actions justify-end mt-2">
                            <BaseButton type="submit" size="sm" :loading="saving">Enregistrer</BaseButton>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
</template>

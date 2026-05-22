<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useApi } from '../composables/useApi.js'

const route  = useRoute()
const router = useRouter()
const api    = useApi()

const isEdit  = computed(() => !!route.params.id)
const loading = ref(false)
const saving  = ref(false)
const errors  = ref({})

const form = ref({
    name: '', slug: '', type: '', employee_count: '',
    contact_name: '', contact_email: '',
    primary_color: '#E30613', logo_url: '',
    is_active: true, is_labelled: false, is_validated: false,
    trophy_rank: '',
})

const types = ['banque','assurance','industrie','commerce','service','technologie','sante','education','autre']

onMounted(async () => {
    if (!isEdit.value) return
    loading.value = true
    try {
        const e = await api.get(`/admin/entreprises/${route.params.id}`)
        Object.keys(form.value).forEach(k => {
            if (e[k] !== undefined) form.value[k] = e[k] ?? ''
        })
        form.value.primary_color = e.primary_color ?? '#E30613'
    } finally {
        loading.value = false
    }
})

const save = async () => {
    saving.value = true
    errors.value = {}
    try {
        const payload = { ...form.value }
        if (!payload.trophy_rank) payload.trophy_rank = null
        if (!payload.employee_count) payload.employee_count = null

        if (isEdit.value) {
            await api.put(`/admin/entreprises/${route.params.id}`, payload)
        } else {
            await api.post('/admin/entreprises', payload)
        }
        router.push('/admin/entreprises')
    } catch (e) {
        if (e.errors) errors.value = e.errors
        else alert(e.message)
    } finally {
        saving.value = false
    }
}

const fieldError = (key) => errors.value[key]?.[0]
</script>

<template>
    <div class="max-w-2xl">
        <div class="flex items-center gap-3 mb-6">
            <button class="btn btn-ghost btn-sm" @click="router.back()">← Retour</button>
            <h1 class="text-2xl font-bold">{{ isEdit ? 'Modifier' : 'Nouvelle entreprise' }}</h1>
        </div>

        <div v-if="loading" class="flex justify-center py-16">
            <span class="loading loading-spinner loading-lg"></span>
        </div>

        <form v-else class="card bg-base-100 shadow-sm" @submit.prevent="save">
            <div class="card-body gap-4">

                <div class="divider text-xs text-base-content/40">Informations</div>

                <div class="grid grid-cols-2 gap-4">
                    <label class="form-control col-span-2">
                        <div class="label"><span class="label-text">Nom *</span></div>
                        <input v-model="form.name" type="text" required class="input input-bordered input-sm"
                            :class="fieldError('name') ? 'input-error' : ''">
                        <div v-if="fieldError('name')" class="label"><span class="label-text-alt text-error">{{ fieldError('name') }}</span></div>
                    </label>

                    <label class="form-control">
                        <div class="label"><span class="label-text">Slug</span></div>
                        <input v-model="form.slug" type="text" class="input input-bordered input-sm font-mono"
                            :class="fieldError('slug') ? 'input-error' : ''" placeholder="auto-généré">
                        <div v-if="fieldError('slug')" class="label"><span class="label-text-alt text-error">{{ fieldError('slug') }}</span></div>
                    </label>

                    <label class="form-control">
                        <div class="label"><span class="label-text">Secteur *</span></div>
                        <select v-model="form.type" required class="select select-bordered select-sm">
                            <option value="" disabled>—</option>
                            <option v-for="t in types" :key="t" :value="t" class="capitalize">{{ t }}</option>
                        </select>
                    </label>

                    <label class="form-control">
                        <div class="label"><span class="label-text">Effectif</span></div>
                        <input v-model="form.employee_count" type="number" min="1" class="input input-bordered input-sm">
                    </label>

                    <label class="form-control">
                        <div class="label"><span class="label-text">Rang trophée</span></div>
                        <input v-model="form.trophy_rank" type="number" min="1" class="input input-bordered input-sm" placeholder="null = non classée">
                    </label>
                </div>

                <label class="form-control">
                    <div class="label"><span class="label-text">URL logo</span></div>
                    <input v-model="form.logo_url" type="url" class="input input-bordered input-sm">
                </label>

                <label class="form-control">
                    <div class="label">
                        <span class="label-text">Couleur principale</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="color" v-model="form.primary_color" class="w-10 h-9 rounded border border-base-300 cursor-pointer p-1">
                        <span class="badge text-white font-mono text-xs px-3 py-3" :style="`background:${form.primary_color}`">
                            {{ form.primary_color }}
                        </span>
                    </div>
                </label>

                <div class="divider text-xs text-base-content/40">Contact</div>

                <div class="grid grid-cols-2 gap-4">
                    <label class="form-control">
                        <div class="label"><span class="label-text">Nom contact</span></div>
                        <input v-model="form.contact_name" type="text" class="input input-bordered input-sm">
                    </label>
                    <label class="form-control">
                        <div class="label"><span class="label-text">Email contact</span></div>
                        <input v-model="form.contact_email" type="email" class="input input-bordered input-sm">
                    </label>
                </div>

                <div class="divider text-xs text-base-content/40">Statut</div>

                <div class="flex flex-wrap gap-4">
                    <label class="label gap-2 cursor-pointer">
                        <input type="checkbox" v-model="form.is_active" class="checkbox checkbox-sm">
                        <span class="label-text">Active</span>
                    </label>
                    <label class="label gap-2 cursor-pointer">
                        <input type="checkbox" v-model="form.is_validated" class="checkbox checkbox-sm">
                        <span class="label-text">Validée</span>
                    </label>
                    <label class="label gap-2 cursor-pointer">
                        <input type="checkbox" v-model="form.is_labelled" class="checkbox checkbox-sm">
                        <span class="label-text">Labelisée</span>
                    </label>
                </div>

                <div class="card-actions justify-end mt-2">
                    <button type="button" class="btn btn-ghost btn-sm" @click="router.back()">Annuler</button>
                    <button type="submit" class="btn btn-sm bg-[#E30613] hover:bg-[#c0051f] text-white border-none" :disabled="saving">
                        <span v-if="saving" class="loading loading-spinner loading-xs"></span>
                        {{ isEdit ? 'Enregistrer' : 'Créer' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

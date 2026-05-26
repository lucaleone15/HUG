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
    name: '', slug: '', type: '',
    employee_count: '',
    contact_name: '', contact_email: '',
    primary_color: '#E30613',
    secondary_color: '',
    logo_url: '',
    is_active: true, is_labelled: false, is_validated: false,
    trophy_rank: '',
    wants_trophy: false,
    rdv_url: '',
    rdv_date: '',
})

const logoFile     = ref(null)
const logoPreview  = ref(null)
const logoInputRef = ref(null)

const types = ['banque','assurance','industrie','commerce','service','technologie','sante','education','autre']

onMounted(async () => {
    if (!isEdit.value) return
    loading.value = true
    try {
        const e = await api.get(`/admin/entreprises/${route.params.id}`)

        // Champs texte / numérique — on écrase même si vide pour effacer les defaults
        const textFields = ['name', 'slug', 'type', 'contact_name', 'contact_email', 'logo_url']
        textFields.forEach(k => { form.value[k] = e[k] ?? '' })

        // Valeurs nullables numériques
        form.value.employee_count = e.employee_count ?? ''
        form.value.trophy_rank    = e.trophy_rank    ?? ''

        // Couleurs
        form.value.primary_color   = e.primary_color   ?? '#E30613'
        form.value.secondary_color = e.secondary_color ?? ''

        // Booléens
        form.value.is_active    = !!e.is_active
        form.value.is_labelled  = !!e.is_labelled
        form.value.is_validated = !!e.is_validated
        form.value.wants_trophy = !!e.wants_trophy

        // CTS
        form.value.rdv_url  = e.rdv_url  ?? ''
        form.value.rdv_date = e.rdv_date ?? ''

        // Prévisualisation logo
        if (e.logo_url) logoPreview.value = e.logo_url

    } catch (err) {
        alert('Impossible de charger les données : ' + err.message)
        router.push('/admin/entreprises')
    } finally {
        loading.value = false
    }
})

const onFileChange = (event) => {
    const file = event.target.files[0]
    if (!file) return
    logoFile.value   = file
    logoPreview.value = URL.createObjectURL(file)
}

const clearLogo = () => {
    logoFile.value    = null
    logoPreview.value = form.value.logo_url || null
    if (logoInputRef.value) logoInputRef.value.value = ''
}

const save = async () => {
    saving.value = true
    errors.value = {}
    try {
        const fd = new FormData()

        // Champs texte / bool
        const fields = ['name','slug','type','employee_count','contact_name','contact_email',
                        'primary_color','secondary_color','logo_url','trophy_rank','rdv_url','rdv_date']
        fields.forEach(k => {
            if (form.value[k] !== '' && form.value[k] !== null) fd.append(k, form.value[k])
        })
        fd.append('is_active',    form.value.is_active    ? '1' : '0')
        fd.append('is_labelled',  form.value.is_labelled  ? '1' : '0')
        fd.append('is_validated', form.value.is_validated ? '1' : '0')
        fd.append('wants_trophy', form.value.wants_trophy ? '1' : '0')

        // Fichier logo (prioritaire sur logo_url si sélectionné)
        if (logoFile.value) fd.append('logo', logoFile.value)

        const path = isEdit.value
            ? `/admin/entreprises/${route.params.id}`
            : '/admin/entreprises'

        await api.upload(path, fd)
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
            <h1 class="text-2xl font-bold">{{ isEdit ? 'Modifier l\'entreprise' : 'Nouvelle entreprise' }}</h1>
        </div>

        <div v-if="loading" class="flex justify-center py-16">
            <span class="loading loading-spinner loading-lg"></span>
        </div>

        <form v-else class="card bg-base-100 shadow-sm" @submit.prevent="save">
            <div class="card-body gap-4">

                <!-- Section : Identité -->
                <div class="divider text-xs text-base-content/40">Identité</div>

                <div class="grid grid-cols-2 gap-4">
                    <label class="form-control col-span-2">
                        <div class="label"><span class="label-text">Nom *</span></div>
                        <input v-model="form.name" type="text" required class="input input-bordered input-sm"
                            :class="fieldError('name') ? 'input-error' : ''">
                        <div v-if="fieldError('name')" class="label"><span class="label-text-alt text-error">{{ fieldError('name') }}</span></div>
                    </label>

                    <label class="form-control">
                        <div class="label"><span class="label-text">Slug (URL)</span></div>
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
                        <input v-model="form.trophy_rank" type="number" min="1" class="input input-bordered input-sm" placeholder="vide = non classée">
                    </label>
                </div>

                <!-- Section : Logo -->
                <div class="divider text-xs text-base-content/40">Logo</div>

                <!-- Prévisualisation -->
                <div v-if="logoPreview" class="flex items-center gap-4 p-3 bg-base-200 rounded-lg">
                    <img :src="logoPreview" alt="Logo" class="h-14 w-14 object-contain rounded bg-white p-1">
                    <div class="flex-1 text-sm text-base-content/60 truncate">{{ logoFile?.name ?? form.logo_url }}</div>
                    <button type="button" class="btn btn-ghost btn-xs text-error" @click="clearLogo">✕</button>
                </div>

                <label class="form-control">
                    <div class="label"><span class="label-text">Uploader un fichier</span></div>
                    <input ref="logoInputRef" type="file" accept="image/*"
                        class="file-input file-input-bordered file-input-sm w-full"
                        @change="onFileChange">
                    <div class="label"><span class="label-text-alt text-base-content/50">PNG, JPG, SVG — 2 Mo max</span></div>
                </label>

                <label class="form-control">
                    <div class="label"><span class="label-text">Ou URL du logo</span></div>
                    <input v-model="form.logo_url" type="text" class="input input-bordered input-sm"
                        placeholder="https://exemple.com/logo.png"
                        :disabled="!!logoFile">
                </label>

                <!-- Section : Couleurs -->
                <div class="divider text-xs text-base-content/40">Couleurs</div>

                <div class="flex flex-col gap-3">
                    <!-- Couleur principale -->
                    <div>
                        <p class="text-sm font-medium mb-2">Couleur principale</p>
                        <div class="flex items-center gap-3">
                            <input type="color" v-model="form.primary_color"
                                class="w-10 h-9 rounded border border-base-300 cursor-pointer p-1">
                            <span class="badge text-white font-mono text-xs px-3 py-3"
                                :style="`background:${form.primary_color}`">{{ form.primary_color }}</span>
                        </div>
                    </div>

                    <!-- Couleur secondaire -->
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <p class="text-sm font-medium">Couleur secondaire</p>
                            <span class="text-xs text-base-content/40">optionnelle</span>
                        </div>
                        <div v-if="form.secondary_color" class="flex items-center gap-3">
                            <input type="color" v-model="form.secondary_color"
                                class="w-10 h-9 rounded border border-base-300 cursor-pointer p-1">
                            <span class="badge text-white font-mono text-xs px-3 py-3"
                                :style="`background:${form.secondary_color}`">{{ form.secondary_color }}</span>
                            <button type="button" class="btn btn-ghost btn-xs text-base-content/40"
                                @click="form.secondary_color = ''">✕ Retirer</button>
                        </div>
                        <button v-else type="button" class="btn btn-ghost btn-xs border border-dashed border-base-300 text-base-content/50"
                            @click="form.secondary_color = '#CCCCCC'">
                            + Ajouter une couleur secondaire
                        </button>
                    </div>
                </div>

                <!-- Section : Contact -->
                <div class="divider text-xs text-base-content/40">Contact</div>

                <div class="grid grid-cols-2 gap-4">
                    <label class="form-control">
                        <div class="label"><span class="label-text">Nom du responsable</span></div>
                        <input v-model="form.contact_name" type="text" class="input input-bordered input-sm">
                    </label>
                    <label class="form-control">
                        <div class="label"><span class="label-text">Email de contact</span></div>
                        <input v-model="form.contact_email" type="email" class="input input-bordered input-sm">
                        <div v-if="fieldError('contact_email')" class="label"><span class="label-text-alt text-error">{{ fieldError('contact_email') }}</span></div>
                    </label>
                </div>

                <!-- Section : Statut -->
                <div class="divider text-xs text-base-content/40">Statut</div>

                <div class="flex flex-wrap gap-6">
                    <label class="label gap-2 cursor-pointer">
                        <input type="checkbox" v-model="form.is_active" class="checkbox checkbox-sm">
                        <span class="label-text">Active (visible sur le site)</span>
                    </label>
                    <label class="label gap-2 cursor-pointer">
                        <input type="checkbox" v-model="form.is_validated" class="checkbox checkbox-sm">
                        <span class="label-text">Validée</span>
                    </label>
                    <label class="label gap-2 cursor-pointer">
                        <input type="checkbox" v-model="form.is_labelled" class="checkbox checkbox-sm">
                        <span class="label-text">Labelisée</span>
                    </label>
                    <label class="label gap-2 cursor-pointer">
                        <input type="checkbox" v-model="form.wants_trophy" class="checkbox checkbox-sm">
                        <span class="label-text">
                            Souhaite participer au trophée
                            <span class="text-base-content/40 text-xs ml-1">(déclaré par l'entreprise)</span>
                        </span>
                    </label>
                </div>

                <!-- Section : Collecte CTS -->
                <div class="divider text-xs text-base-content/40">Collecte CTS</div>
                <p class="text-xs text-base-content/50 -mt-2">Ces informations sont renseignées par le CTS après concertation avec l'entreprise.</p>

                <div class="grid grid-cols-2 gap-4">
                    <label class="form-control col-span-2">
                        <div class="label"><span class="label-text">Lien de prise de rendez-vous</span></div>
                        <input v-model="form.rdv_url" type="url" class="input input-bordered input-sm"
                            placeholder="https://calendly.com/... ou Doodle">
                        <div v-if="fieldError('rdv_url')" class="label">
                            <span class="label-text-alt text-error">{{ fieldError('rdv_url') }}</span>
                        </div>
                    </label>
                    <label class="form-control">
                        <div class="label"><span class="label-text">Date de la collecte</span></div>
                        <input v-model="form.rdv_date" type="date" class="input input-bordered input-sm">
                        <div v-if="fieldError('rdv_date')" class="label">
                            <span class="label-text-alt text-error">{{ fieldError('rdv_date') }}</span>
                        </div>
                    </label>
                </div>

                <div class="card-actions justify-end mt-2 gap-2">
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

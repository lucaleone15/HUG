<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import NavBar from '../components/NavBar.vue'

const { t } = useI18n()

const props = defineProps({
    success: { type: Boolean, default: false },
    errors:  { type: Object,  default: () => ({}) },
})

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? ''

const types = [
    'banque', 'assurance', 'industrie', 'commerce',
    'service', 'technologie', 'sante', 'education', 'autre',
]

const primaryColor   = ref('#E30613')
const secondaryColor = ref('')
const logoPreview    = ref(null)
const logoFileRef    = ref(null)

const onFileChange = (e) => {
    const file = e.target.files[0]
    if (file) logoPreview.value = URL.createObjectURL(file)
}

const clearLogo = () => {
    logoPreview.value = null
    if (logoFileRef.value) logoFileRef.value.value = ''
}
</script>

<template>
    <div class="min-h-screen bg-base-100">
        <NavBar />

        <main class="max-w-xl mx-auto px-6 py-12">

            <!-- Success -->
            <template v-if="success">
                <div class="text-center py-12">
                    <div class="text-6xl mb-6">🎉</div>
                    <h1 class="text-2xl font-bold mb-3">{{ t('inscription.success_title') }}</h1>
                    <p class="text-base-content/60 mb-8">{{ t('inscription.success_message') }}</p>
                    <a href="/" class="btn bg-[#E30613] hover:bg-[#c0051f] text-white border-none">
                        {{ t('result.back_home') }}
                    </a>
                </div>
            </template>

            <!-- Formulaire -->
            <template v-else>
                <div class="mb-8">
                    <h1 class="text-3xl font-bold mb-2">{{ t('inscription.title') }}</h1>
                    <p class="text-base-content/60">{{ t('inscription.subtitle') }}</p>
                </div>

                <form action="/inscription" method="POST" enctype="multipart/form-data" class="flex flex-col gap-5">
                    <input type="hidden" name="_token" :value="csrfToken">

                    <!-- Section : Entreprise -->
                    <div class="divider text-xs text-base-content/40 uppercase tracking-widest">
                        {{ t('inscription.section_company') }}
                    </div>

                    <label class="form-control">
                        <div class="label"><span class="label-text font-medium">{{ t('inscription.name') }} *</span></div>
                        <input type="text" name="name" required maxlength="255"
                            class="input input-bordered" :class="errors.name ? 'input-error' : ''">
                        <div v-if="errors.name" class="label">
                            <span class="label-text-alt text-error">{{ errors.name[0] }}</span>
                        </div>
                    </label>

                    <label class="form-control">
                        <div class="label"><span class="label-text font-medium">{{ t('inscription.type') }} *</span></div>
                        <select name="type" required class="select select-bordered"
                            :class="errors.type ? 'select-error' : ''">
                            <option value="" disabled selected>—</option>
                            <option v-for="type in types" :key="type" :value="type">
                                {{ t(`inscription.type_${type}`) }}
                            </option>
                        </select>
                        <div v-if="errors.type" class="label">
                            <span class="label-text-alt text-error">{{ errors.type[0] }}</span>
                        </div>
                    </label>

                    <label class="form-control">
                        <div class="label"><span class="label-text font-medium">{{ t('inscription.employee_count') }}</span></div>
                        <input type="number" name="employee_count" min="1" max="999999"
                            class="input input-bordered" :class="errors.employee_count ? 'input-error' : ''">
                    </label>

                    <!-- Logo -->
                    <div class="form-control gap-2">
                        <div class="label pb-0"><span class="label-text font-medium">{{ t('inscription.logo') }}</span></div>

                        <div v-if="logoPreview" class="flex items-center gap-3 p-3 bg-base-200 rounded-lg">
                            <img :src="logoPreview" alt="Logo" class="h-12 w-12 object-contain rounded bg-white p-1">
                            <span class="flex-1 text-sm text-base-content/50 truncate">{{ t('inscription.logo_hint') }}</span>
                            <button type="button" class="btn btn-ghost btn-xs text-error" @click="clearLogo">✕</button>
                        </div>

                        <input ref="logoFileRef" type="file" name="logo" accept="image/*"
                            class="file-input file-input-bordered w-full"
                            :class="errors.logo ? 'file-input-error' : ''"
                            @change="onFileChange">
                        <div class="label pt-0">
                            <span class="label-text-alt text-base-content/50">{{ t('inscription.logo_hint') }}</span>
                        </div>
                        <div v-if="errors.logo" class="label pt-0">
                            <span class="label-text-alt text-error">{{ errors.logo[0] }}</span>
                        </div>
                    </div>

                    <label class="form-control">
                        <div class="label"><span class="label-text font-medium">{{ t('inscription.logo_url') }}</span></div>
                        <input type="url" name="logo_url" maxlength="2048"
                            class="input input-bordered" placeholder="https://exemple.com/logo.png"
                            :disabled="!!logoPreview">
                        <div class="label pt-0">
                            <span class="label-text-alt text-base-content/50">{{ logoPreview ? '← Désactivé : fichier sélectionné ci-dessus' : '' }}</span>
                        </div>
                    </label>

                    <!-- Couleurs -->
                    <div class="flex flex-col gap-3">
                        <!-- Couleur principale -->
                        <div>
                            <p class="label-text font-medium mb-1">{{ t('inscription.primary_color') }}</p>
                            <p class="text-xs text-base-content/50 mb-2">{{ t('inscription.color_hint') }}</p>
                            <div class="flex items-center gap-3">
                                <input type="color" name="primary_color" v-model="primaryColor"
                                    class="w-12 h-10 rounded-lg border border-base-300 cursor-pointer p-1">
                                <span class="badge text-white font-mono text-xs px-3 py-3"
                                    :style="`background-color: ${primaryColor}`">{{ primaryColor }}</span>
                            </div>
                        </div>

                        <!-- Couleur secondaire -->
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <p class="label-text font-medium">{{ t('inscription.secondary_color') }}</p>
                                <span class="text-xs text-base-content/40">{{ t('inscription.secondary_color_hint') }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="color" name="secondary_color" v-model="secondaryColor"
                                    class="w-12 h-10 rounded-lg border border-base-300 cursor-pointer p-1">
                                <span v-if="secondaryColor" class="badge text-white font-mono text-xs px-3 py-3"
                                    :style="`background-color: ${secondaryColor}`">{{ secondaryColor }}</span>
                                <button v-if="secondaryColor" type="button"
                                    class="btn btn-ghost btn-xs text-base-content/40"
                                    @click="secondaryColor = ''">✕</button>
                                <span v-else class="text-xs text-base-content/30 italic">Aucune</span>
                            </div>
                        </div>
                    </div>

                    <!-- Section : Contact -->
                    <div class="divider text-xs text-base-content/40 uppercase tracking-widest">
                        {{ t('inscription.section_contact') }}
                    </div>

                    <label class="form-control">
                        <div class="label"><span class="label-text font-medium">{{ t('inscription.contact_name') }} *</span></div>
                        <input type="text" name="contact_name" required maxlength="255"
                            class="input input-bordered" :class="errors.contact_name ? 'input-error' : ''">
                        <div v-if="errors.contact_name" class="label">
                            <span class="label-text-alt text-error">{{ errors.contact_name[0] }}</span>
                        </div>
                    </label>

                    <label class="form-control">
                        <div class="label"><span class="label-text font-medium">{{ t('inscription.contact_email') }} *</span></div>
                        <input type="email" name="contact_email" required maxlength="255"
                            class="input input-bordered" :class="errors.contact_email ? 'input-error' : ''">
                        <div v-if="errors.contact_email" class="label">
                            <span class="label-text-alt text-error">{{ errors.contact_email[0] }}</span>
                        </div>
                    </label>

                    <!-- Trophée -->
                    <div class="divider text-xs text-base-content/40 uppercase tracking-widest">
                        {{ t('inscription.section_trophy') }}
                    </div>

                    <label class="label gap-3 cursor-pointer justify-start">
                        <input type="checkbox" name="wants_trophy" value="1" class="checkbox checkbox-sm">
                        <span class="label-text">{{ t('inscription.wants_trophy') }}</span>
                    </label>
                    <p class="text-xs text-base-content/50 -mt-3">{{ t('inscription.wants_trophy_hint') }}</p>

                    <div class="alert alert-info text-sm mt-2">
                        <span>{{ t('inscription.pending_note') }}</span>
                    </div>

                    <button type="submit" class="btn bg-[#E30613] hover:bg-[#c0051f] text-white border-none mt-2">
                        {{ t('inscription.submit') }}
                    </button>
                </form>
            </template>
        </main>
    </div>
</template>

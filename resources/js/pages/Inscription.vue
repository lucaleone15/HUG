<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import NavBar from '../components/ui/NavBar.vue'
import Footer from '../components/ui/Footer.vue'
import BaseInput from '../components/ui/BaseInput.vue'
import BaseButton from '../components/ui/BaseButton.vue'

const { t, locale } = useI18n()

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
    <div class="min-h-screen bg-base-100 flex flex-col">
        <NavBar />

        <!-- Hero header -->
        <section class="grid md:grid-cols-2">
            <div class="flex flex-col justify-center px-8 py-12 bg-white">
                <h1 class="text-2xl md:text-3xl font-bold leading-tight">
                    {{ t('inscription.hero_line1') }}<br>
                    <span class="text-brand">{{ t('inscription.hero_program_name') }}</span><br>
                    {{ t('inscription.hero_line2') }}
                </h1>
            </div>
            <div class="relative flex flex-col justify-center px-8 py-12 bg-brand-muted text-white">
                <span class="absolute top-4 right-4 bg-black text-white text-xs font-semibold px-3 py-1">
                    {{ t('inscription.hero_badge') }}
                </span>
                <p class="mb-5 leading-relaxed">{{ t('inscription.hero_short_subtitle') }}</p>
                <p class="leading-relaxed text-white/75">{{ t('inscription.hero_text') }}</p>
            </div>
        </section>

        <main class="max-w-3xl mx-auto px-6 py-12 flex-1 w-full">

            <!-- Success -->
            <template v-if="success">
                <div class="text-center py-12">
                    <div class="text-6xl mb-6">🎉</div>
                    <h1 class="text-2xl font-bold mb-3">{{ t('inscription.success_title') }}</h1>
                    <p class="text-base-content/60 mb-8">{{ t('inscription.success_message') }}</p>
                    <BaseButton href="/" class="w-full">{{ t('result.back_home') }}</BaseButton>
                </div>
            </template>

            <!-- Formulaire -->
            <template v-else>
                <h1 class="text-3xl font-bold text-brand mb-10">{{ t('inscription.form_title') }}</h1>

                <form action="/inscription" method="POST" enctype="multipart/form-data" class="flex flex-col gap-6">
                    <input type="hidden" name="_token" :value="csrfToken">
                    <input type="hidden" name="locale" :value="locale">

                    <!-- Nom -->
                    <div>
                        <label class="block text-sm font-semibold mb-1">{{ t('inscription.name') }} *</label>
                        <input name="name" required :placeholder="t('inscription.name_placeholder')"
                            class="input input-bordered w-full"
                            :class="errors.name ? 'input-error' : ''">
                        <p v-if="errors.name" class="text-error text-xs mt-1">{{ errors.name[0] }}</p>
                    </div>

                    <!-- Secteur -->
                    <div>
                        <label class="block text-sm font-semibold mb-1">{{ t('inscription.type') }}</label>
                        <select name="type" class="select select-bordered w-full"
                            :class="errors.type ? 'select-error' : ''">
                            <option value="" disabled selected>{{ t('inscription.select_placeholder') }}</option>
                            <option v-for="type in types" :key="type" :value="type">
                                {{ t(`inscription.type_${type}`) }}
                            </option>
                        </select>
                        <p v-if="errors.type" class="text-error text-xs mt-1">{{ errors.type[0] }}</p>
                    </div>

                    <!-- Taille de l'entreprise (pleine largeur) -->
                    <div>
                        <label class="block text-sm font-semibold mb-1">{{ t('inscription.field_size') }} *</label>
                        <input name="employee_count" type="number" :placeholder="t('inscription.size_placeholder')"
                            class="input input-bordered w-full">
                    </div>

                    <!-- Logo fichier + URL côte à côte -->
                    <div class="grid grid-cols-2 gap-4 items-start">
                        <div>
                            <label class="block text-sm font-semibold mb-1">{{ t('inscription.logo') }}</label>
                            <div v-if="logoPreview" class="flex items-center gap-2 mb-2">
                                <img :src="logoPreview" class="h-8 w-8 object-contain rounded border border-base-300 bg-white p-0.5">
                                <button type="button" class="text-xs text-error underline" @click="clearLogo">{{ t('inscription.logo_delete') }}</button>
                            </div>
                            <input ref="logoFileRef" type="file" name="logo" accept="image/*"
                                class="file-input file-input-bordered w-full text-sm"
                                :class="errors.logo ? 'file-input-error' : ''"
                                @change="onFileChange">
                            <p v-if="errors.logo" class="text-error text-xs mt-1">{{ errors.logo[0] }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">{{ t('inscription.logo_url') }} <span class="font-normal text-base-content/40">{{ t('inscription.logo_url_alt') }}</span></label>
                            <input type="url" name="logo_url" maxlength="2048"
                                class="input input-bordered w-full"
                                :placeholder="t('inscription.logo_url_placeholder')"
                                :disabled="!!logoPreview">
                            <p v-if="logoPreview" class="text-xs text-base-content/30 italic mt-1">{{ t('inscription.logo_url_disabled') }}</p>
                            <p v-else class="text-xs text-base-content/40 mt-1">{{ t('inscription.logo_hint') }}</p>
                        </div>
                    </div>

                    <!-- Couleurs -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1">{{ t('inscription.primary_color') }} *</label>
                            <div class="flex items-center border border-base-300 rounded-lg overflow-hidden">
                                <input type="color" name="primary_color" v-model="primaryColor"
                                    class="w-10 h-10 cursor-pointer border-none shrink-0 p-1 bg-transparent">
                                <input type="text" v-model="primaryColor" maxlength="7"
                                    class="flex-1 px-3 py-2 text-sm font-mono outline-none bg-transparent"
                                    placeholder="#000000">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">{{ t('inscription.secondary_color') }}</label>
                            <div class="flex items-center border border-base-300 rounded-lg overflow-hidden">
                                <input type="color" name="secondary_color" v-model="secondaryColor"
                                    class="w-10 h-10 cursor-pointer border-none shrink-0 p-1 bg-transparent">
                                <input type="text" v-model="secondaryColor" maxlength="7"
                                    class="flex-1 px-3 py-2 text-sm font-mono outline-none bg-transparent"
                                    placeholder="#000000">
                            </div>
                        </div>
                    </div>

                    <!-- Section Contact référent -->
                    <h2 class="text-2xl font-bold text-brand mt-4">{{ t('inscription.section_contact_ref') }}</h2>

                    <!-- Nom -->
                    <div>
                        <label class="block text-sm font-semibold mb-1">{{ t('inscription.contact_name') }} *</label>
                        <input name="contact_name" required :placeholder="t('inscription.contact_name_placeholder')"
                            class="input input-bordered w-full"
                            :class="errors.contact_name ? 'input-error' : ''">
                        <p v-if="errors.contact_name" class="text-error text-xs mt-1">{{ errors.contact_name[0] }}</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold mb-1">{{ t('inscription.contact_email') }} *</label>
                        <input name="contact_email" type="email" required :placeholder="t('inscription.contact_email_placeholder')"
                            class="input input-bordered w-full"
                            :class="errors.contact_email ? 'input-error' : ''">
                        <p v-if="errors.contact_email" class="text-error text-xs mt-1">{{ errors.contact_email[0] }}</p>
                    </div>

                    <!-- Trophée -->
                    <div>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="wants_trophy" value="1" class="checkbox checkbox-sm">
                            <span class="text-sm">{{ t('inscription.wants_trophy') }}</span>
                        </label>
                        <p class="text-xs text-base-content/40 mt-1 ml-7">{{ t('inscription.wants_trophy_hint') }}</p>
                    </div>

                    <div class="alert alert-info text-sm">
                        <span>{{ t('inscription.pending_note') }}</span>
                    </div>

                    <button type="submit"
                        class="w-full bg-black hover:bg-black/80 text-white font-semibold py-4 rounded-sm uppercase tracking-wide text-sm transition-colors">
                        {{ t('inscription.submit_label') }}
                    </button>
                </form>
            </template>
        </main>
        <Footer />
    </div>
</template>

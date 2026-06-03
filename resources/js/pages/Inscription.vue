<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import NavBar from '../components/ui/NavBar.vue'
import Footer from '../components/ui/Footer.vue'
import BaseInput from '../components/ui/BaseInput.vue'
import BaseButton from '../components/ui/BaseButton.vue'

const { t, locale } = useI18n()

const props = defineProps({
    success: { type: Boolean, default: false },
    errors:  { type: Object,  default: () => ({}) },
    old:     { type: Object,  default: () => ({}) },
})

const hasErrors = computed(() => Object.keys(props.errors).length > 0)

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? ''

const types = [
    'banque', 'assurance', 'industrie', 'commerce',
    'service', 'technologie', 'sante', 'education', 'autre',
]

const selectedType    = ref(props.old?.type || '')
const primaryColor    = ref(props.old?.primary_color || '')
const secondaryColor  = ref(props.old?.secondary_color || '')

const isValidHex = (v) => /^#[0-9A-Fa-f]{6}$/.test(v ?? '')
const logoPreview     = ref(null)
const logoFileRef     = ref(null)

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

        <!-- Hero -->
        <section class="border-b border-base-200">
            <div class="max-w-5xl mx-auto px-6 py-14 grid md:grid-cols-[1fr_1.4fr] gap-12 items-center">
                <div class="page-hero-text">
                    <p class="text-xs uppercase tracking-[0.2em] text-base-content/35 mb-5">
                        {{ t('inscription.hero_badge') }}
                    </p>
                    <h1 class="font-extrabold leading-tight text-base-content"
                        style="font-size: clamp(1.75rem, 4vw, 2.75rem);">
                        {{ t('inscription.hero_line1') }}<br>
                        <span class="text-brand">{{ t('inscription.hero_program_name') }}</span>
                    </h1>
                </div>
                <div class="bg-brand-dark text-white rounded-xl px-8 py-8 relative overflow-hidden page-hero-visual">
                    <p class="font-semibold mb-3 leading-tight"
                       style="font-size: clamp(1rem, 1.8vw, 1.15rem);">
                        {{ t('inscription.hero_short_subtitle') }}
                    </p>
                    <p class="text-white/70 text-sm leading-relaxed" style="max-width: 42ch;">
                        {{ t('inscription.hero_text') }}
                    </p>
                </div>
            </div>
        </section>

        <main class="max-w-3xl mx-auto px-6 py-14 flex-1 w-full">

            <!-- Succès -->
            <template v-if="success">
                <div class="py-12 border-b border-base-200 mb-8">
                    <h2 class="font-bold mb-3 leading-tight"
                        style="font-size: clamp(1.35rem, 2.5vw, 1.75rem);">
                        {{ t('inscription.success_title') }}
                    </h2>
                    <p class="text-base-content/60 mb-8 leading-relaxed" style="max-width: 52ch;">
                        {{ t('inscription.success_message') }}
                    </p>
                    <a href="/" class="text-sm text-base-content/50 hover:text-base-content"
                       style="transition: color 150ms ease;">← {{ t('result.back_home') }}</a>
                </div>
            </template>

            <!-- Formulaire -->
            <template v-else>
                <h2 class="font-bold mb-10 leading-tight"
                    style="font-size: clamp(1.35rem, 2.5vw, 1.75rem);">
                    {{ t('inscription.form_title') }}
                </h2>

                <!-- Bandeau d'erreur global -->
                <div v-if="hasErrors"
                     class="mb-6 flex items-start gap-3 rounded-lg border border-error/30 bg-error/5 px-4 py-3 text-sm text-error">
                    <svg class="mt-0.5 shrink-0 w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ t('inscription.form_errors_banner') }}</span>
                </div>

                <form action="/inscription" method="POST" enctype="multipart/form-data" class="flex flex-col gap-6">
                    <input type="hidden" name="_token" :value="csrfToken">
                    <input type="hidden" name="locale" :value="locale">

                    <!-- Nom -->
                    <BaseInput
                        name="name"
                        :label="t('inscription.name')"
                        :placeholder="t('inscription.name_placeholder')"
                        :error="errors.name?.[0]"
                        :model-value="old?.name"
                        required
                    />

                    <!-- Secteur -->
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium">{{ t('inscription.type') }}</label>
                        <select name="type" v-model="selectedType" class="select select-bordered w-full"
                            :class="errors.type ? 'select-error' : ''">
                            <option value="" disabled>{{ t('inscription.select_placeholder') }}</option>
                            <option v-for="type in types" :key="type" :value="type">
                                {{ t(`inscription.type_${type}`) }}
                            </option>
                        </select>
                        <span v-if="errors.type" class="text-error text-xs">{{ errors.type[0] }}</span>
                    </div>

                    <!-- Effectif -->
                    <BaseInput
                        name="employee_count"
                        type="number"
                        :label="t('inscription.field_size')"
                        :placeholder="t('inscription.size_placeholder')"
                        :error="errors.employee_count?.[0]"
                        :model-value="old?.employee_count"
                        required
                    />

                    <!-- Logo -->
                    <div class="grid grid-cols-2 gap-4 items-start">
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium">{{ t('inscription.logo') }}</label>
                            <div v-if="logoPreview" class="flex items-center gap-2 mb-1">
                                <img :src="logoPreview" class="h-8 w-8 object-contain rounded border border-base-300 bg-white p-0.5">
                                <button type="button" class="text-xs text-error hover:underline" @click="clearLogo">
                                    {{ t('inscription.logo_delete') }}
                                </button>
                            </div>
                            <input ref="logoFileRef" type="file" name="logo" accept="image/*,.svg"
                                class="file-input file-input-bordered w-full text-sm"
                                :class="errors.logo ? 'file-input-error' : ''"
                                @change="onFileChange">
                            <span v-if="errors.logo" class="text-error text-xs">{{ errors.logo[0] }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium">
                                {{ t('inscription.logo_url') }}
                                <span class="font-normal text-base-content/40">{{ t('inscription.logo_url_alt') }}</span>
                            </label>
                            <input type="url" name="logo_url" maxlength="2048"
                                class="input input-bordered w-full"
                                :placeholder="t('inscription.logo_url_placeholder')"
                                :value="old?.logo_url"
                                :disabled="!!logoPreview">
                            <span class="text-xs text-base-content/40">
                                {{ logoPreview ? t('inscription.logo_url_disabled') : t('inscription.logo_hint') }}
                            </span>
                        </div>
                    </div>

                    <!-- Couleurs -->
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Couleur principale (obligatoire) -->
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium">
                                {{ t('inscription.primary_color') }}<span class="text-error ml-0.5">*</span>
                            </label>
                            <div class="flex items-center rounded-lg overflow-hidden"
                                 :class="errors?.primary_color ? 'border border-error' : 'border border-base-300'">
                                <!-- Swatch cliquable — picker toujours présent, invisible -->
                                <label class="w-10 h-10 shrink-0 relative block cursor-pointer overflow-hidden border-r border-base-300">
                                    <input type="color"
                                        :value="isValidHex(primaryColor) ? primaryColor : '#D32C37'"
                                        @change="e => primaryColor = e.target.value"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer border-none">
                                    <div class="w-full h-full flex items-center justify-center"
                                         :style="isValidHex(primaryColor) ? `background-color: ${primaryColor}` : ''"
                                         :class="!isValidHex(primaryColor) ? 'bg-base-200' : ''">
                                        <svg v-if="!isValidHex(primaryColor)"
                                             class="w-4 h-4 text-base-content/25" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                             aria-hidden="true">
                                            <circle cx="12" cy="12" r="9"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3m.08 4h.01"/>
                                        </svg>
                                    </div>
                                </label>
                                <!-- Champ texte — soumet la valeur -->
                                <input type="text" name="primary_color" v-model="primaryColor"
                                    maxlength="7" required
                                    class="flex-1 px-3 py-2 text-sm outline-none bg-transparent"
                                    placeholder="#RRGGBB">
                            </div>
                            <span v-if="errors?.primary_color" class="text-error text-xs">{{ errors.primary_color[0] }}</span>
                        </div>
                        <!-- Couleur secondaire (optionnelle) -->
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium">{{ t('inscription.secondary_color') }}</label>
                            <div class="flex items-center border border-base-300 rounded-lg overflow-hidden">
                                <label class="w-10 h-10 shrink-0 relative block cursor-pointer overflow-hidden border-r border-base-300">
                                    <input type="color"
                                        :value="isValidHex(secondaryColor) ? secondaryColor : '#888888'"
                                        @change="e => secondaryColor = e.target.value"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer border-none">
                                    <div class="w-full h-full flex items-center justify-center"
                                         :style="isValidHex(secondaryColor) ? `background-color: ${secondaryColor}` : ''"
                                         :class="!isValidHex(secondaryColor) ? 'bg-base-200' : ''">
                                        <svg v-if="!isValidHex(secondaryColor)"
                                             class="w-4 h-4 text-base-content/25" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                             aria-hidden="true">
                                            <circle cx="12" cy="12" r="9"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3m.08 4h.01"/>
                                        </svg>
                                    </div>
                                </label>
                                <input type="text" name="secondary_color" v-model="secondaryColor"
                                    maxlength="7"
                                    class="flex-1 px-3 py-2 text-sm outline-none bg-transparent"
                                    placeholder="#RRGGBB">
                            </div>
                        </div>
                    </div>

                    <!-- Séparateur section contact -->
                    <div class="pt-4 border-t border-base-200">
                        <p class="text-xs uppercase tracking-[0.2em] text-base-content/35 mb-6">
                            {{ t('inscription.section_contact_ref') }}
                        </p>
                    </div>

                    <BaseInput
                        name="contact_name"
                        :label="t('inscription.contact_name')"
                        :placeholder="t('inscription.contact_name_placeholder')"
                        :error="errors.contact_name?.[0]"
                        :model-value="old?.contact_name"
                        required
                    />

                    <BaseInput
                        name="contact_email"
                        type="email"
                        :label="t('inscription.contact_email')"
                        :placeholder="t('inscription.contact_email_placeholder')"
                        :error="errors.contact_email?.[0]"
                        :model-value="old?.contact_email"
                        required
                    />

                    <!-- Trophée -->
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="wants_trophy" value="1" class="checkbox checkbox-sm mt-0.5 shrink-0"
                               :checked="old?.wants_trophy === '1'">
                        <div>
                            <span class="text-sm">{{ t('inscription.wants_trophy') }}</span>
                            <p class="text-xs text-base-content/40 mt-1">{{ t('inscription.wants_trophy_hint') }}</p>
                        </div>
                    </label>

                    <!-- Note -->
                    <p class="text-xs text-base-content/45 leading-relaxed border-l-2 border-base-300 pl-3"
                       style="max-width: 52ch;">
                        {{ t('inscription.pending_note') }}
                    </p>

                    <button type="submit"
                        class="w-full bg-black hover:bg-black/80 text-white font-semibold py-4 rounded-sm uppercase tracking-wide text-sm active:scale-[0.97]"
                        style="transition: background-color 150ms ease, transform 140ms cubic-bezier(0.23,1,0.32,1);">
                        {{ t('inscription.submit_label') }}
                    </button>
                </form>
            </template>
        </main>

        <Footer />
    </div>
</template>

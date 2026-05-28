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

const primaryColor   = ref('#D32C37')
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

                <form action="/inscription" method="POST" enctype="multipart/form-data" class="flex flex-col gap-6">
                    <input type="hidden" name="_token" :value="csrfToken">
                    <input type="hidden" name="locale" :value="locale">

                    <!-- Nom -->
                    <BaseInput
                        name="name"
                        :label="t('inscription.name') + ' *'"
                        :placeholder="t('inscription.name_placeholder')"
                        :error="errors.name?.[0]"
                        required
                    />

                    <!-- Secteur -->
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium">{{ t('inscription.type') }}</label>
                        <select name="type" class="select select-bordered w-full"
                            :class="errors.type ? 'select-error' : ''">
                            <option value="" disabled selected>{{ t('inscription.select_placeholder') }}</option>
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
                        :label="t('inscription.field_size') + ' *'"
                        :placeholder="t('inscription.size_placeholder')"
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
                            <input ref="logoFileRef" type="file" name="logo" accept="image/*"
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
                                :disabled="!!logoPreview">
                            <span class="text-xs text-base-content/40">
                                {{ logoPreview ? t('inscription.logo_url_disabled') : t('inscription.logo_hint') }}
                            </span>
                        </div>
                    </div>

                    <!-- Couleurs -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium">{{ t('inscription.primary_color') }} *</label>
                            <div class="flex items-center border border-base-300 rounded-lg overflow-hidden">
                                <input type="color" name="primary_color" v-model="primaryColor"
                                    class="w-10 h-10 cursor-pointer border-none shrink-0 p-1 bg-transparent">
                                <input type="text" v-model="primaryColor" maxlength="7"
                                    class="flex-1 px-3 py-2 text-sm font-mono outline-none bg-transparent"
                                    placeholder="#000000">
                            </div>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium">{{ t('inscription.secondary_color') }}</label>
                            <div class="flex items-center border border-base-300 rounded-lg overflow-hidden">
                                <input type="color" name="secondary_color" v-model="secondaryColor"
                                    class="w-10 h-10 cursor-pointer border-none shrink-0 p-1 bg-transparent">
                                <input type="text" v-model="secondaryColor" maxlength="7"
                                    class="flex-1 px-3 py-2 text-sm font-mono outline-none bg-transparent"
                                    placeholder="#000000">
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
                        :label="t('inscription.contact_name') + ' *'"
                        :placeholder="t('inscription.contact_name_placeholder')"
                        :error="errors.contact_name?.[0]"
                        required
                    />

                    <BaseInput
                        name="contact_email"
                        type="email"
                        :label="t('inscription.contact_email') + ' *'"
                        :placeholder="t('inscription.contact_email_placeholder')"
                        :error="errors.contact_email?.[0]"
                        required
                    />

                    <!-- Trophée -->
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="wants_trophy" value="1" class="checkbox checkbox-sm mt-0.5 shrink-0">
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

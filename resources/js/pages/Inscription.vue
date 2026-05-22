<script setup>
import { ref, computed } from 'vue'
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

const color = ref('#E30613')
</script>

<template>
    <div class="min-h-screen bg-base-100">
        <NavBar />

        <main class="max-w-xl mx-auto px-6 py-12">

            <!-- Success state -->
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

            <!-- Form -->
            <template v-else>
                <div class="mb-8">
                    <h1 class="text-3xl font-bold mb-2">{{ t('inscription.title') }}</h1>
                    <p class="text-base-content/60">{{ t('inscription.subtitle') }}</p>
                </div>

                <form action="/inscription" method="POST" class="flex flex-col gap-5">
                    <input type="hidden" name="_token" :value="csrfToken">

                    <!-- Divider: Entreprise -->
                    <div class="divider text-xs text-base-content/40 uppercase tracking-widest">
                        {{ t('inscription.section_company') }}
                    </div>

                    <label class="form-control">
                        <div class="label"><span class="label-text font-medium">{{ t('inscription.name') }} *</span></div>
                        <input type="text" name="name" required maxlength="255"
                            class="input input-bordered"
                            :class="errors.name ? 'input-error' : ''">
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
                            class="input input-bordered"
                            :class="errors.employee_count ? 'input-error' : ''">
                    </label>

                    <label class="form-control">
                        <div class="label">
                            <span class="label-text font-medium">{{ t('inscription.primary_color') }}</span>
                            <span class="label-text-alt text-base-content/50">{{ t('inscription.color_hint') }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <input type="color" name="primary_color" v-model="color"
                                class="w-12 h-10 rounded-lg border border-base-300 cursor-pointer p-1">
                            <span
                                class="badge text-white font-mono text-xs px-3 py-3"
                                :style="`background-color: ${color}`"
                            >{{ color }}</span>
                        </div>
                    </label>

                    <!-- Divider: Contact -->
                    <div class="divider text-xs text-base-content/40 uppercase tracking-widest">
                        {{ t('inscription.section_contact') }}
                    </div>

                    <label class="form-control">
                        <div class="label"><span class="label-text font-medium">{{ t('inscription.contact_name') }} *</span></div>
                        <input type="text" name="contact_name" required maxlength="255"
                            class="input input-bordered"
                            :class="errors.contact_name ? 'input-error' : ''">
                        <div v-if="errors.contact_name" class="label">
                            <span class="label-text-alt text-error">{{ errors.contact_name[0] }}</span>
                        </div>
                    </label>

                    <label class="form-control">
                        <div class="label"><span class="label-text font-medium">{{ t('inscription.contact_email') }} *</span></div>
                        <input type="email" name="contact_email" required maxlength="255"
                            class="input input-bordered"
                            :class="errors.contact_email ? 'input-error' : ''">
                        <div v-if="errors.contact_email" class="label">
                            <span class="label-text-alt text-error">{{ errors.contact_email[0] }}</span>
                        </div>
                    </label>

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

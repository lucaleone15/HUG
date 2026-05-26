<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import NavBar from '../components/NavBar.vue'

const { t, locale } = useI18n()

const props = defineProps({
    success: { type: String, default: null },
    errors:  { type: Object, default: () => ({}) },
})

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? ''

const types = ['type_general', 'type_partnership', 'type_technical', 'type_other']
</script>

<template>
    <div class="min-h-screen bg-base-100">
        <NavBar />

        <main class="max-w-xl mx-auto px-6 py-12">
            <h1 class="text-3xl font-bold mb-2">{{ t('contact.title') }}</h1>
            <p class="text-base-content/60 mb-8">{{ t('contact.subtitle') }}</p>

            <div v-if="success" class="alert alert-success mb-6">
                <span>{{ t('contact.success') }}</span>
            </div>

            <form v-if="!success" action="/contact" method="POST" class="flex flex-col gap-5">
                <input type="hidden" name="_token" :value="csrfToken">
                <input type="hidden" name="locale" :value="locale">

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium">{{ t('contact.name') }}</label>
                    <input type="text" name="name" required maxlength="255"
                        class="input input-bordered w-full"
                        :class="errors.name ? 'input-error' : ''">
                    <span v-if="errors.name" class="text-error text-xs">{{ errors.name[0] }}</span>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium">{{ t('contact.email') }}</label>
                    <input type="email" name="email" required maxlength="255"
                        class="input input-bordered w-full"
                        :class="errors.email ? 'input-error' : ''">
                    <span v-if="errors.email" class="text-error text-xs">{{ errors.email[0] }}</span>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium">{{ t('contact.type') }}</label>
                    <select name="type" required class="select select-bordered w-full">
                        <option value="" disabled selected>—</option>
                        <option v-for="type in types" :key="type" :value="type">
                            {{ t(`contact.${type}`) }}
                        </option>
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium">{{ t('contact.message') }}</label>
                    <textarea name="message" required maxlength="5000" rows="5"
                        class="textarea textarea-bordered resize-none w-full"
                        :class="errors.message ? 'textarea-error' : ''"></textarea>
                    <span v-if="errors.message" class="text-error text-xs">{{ errors.message[0] }}</span>
                </div>

                <button type="submit" class="btn bg-[#E30613] hover:bg-[#c0051f] text-white border-none w-full">
                    {{ t('contact.submit') }}
                </button>
            </form>
        </main>
    </div>
</template>

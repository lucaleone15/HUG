<script setup>
import { useI18n } from 'vue-i18n'
import NavBar from '../components/ui/NavBar.vue'
import Footer from '../components/ui/Footer.vue'
import BaseInput from '../components/ui/BaseInput.vue'
import BaseButton from '../components/ui/BaseButton.vue'

const { t, locale } = useI18n()

const props = defineProps({
    success: { type: String, default: null },
    errors:  { type: Object, default: () => ({}) },
})

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? ''

const types = ['type_general', 'type_partnership', 'type_technical', 'type_other']
</script>

<template>
    <div class="min-h-screen bg-base-100 flex flex-col">
        <NavBar />
        <main class="max-w-xl mx-auto px-6 py-12 flex-1 w-full">
            <h1 class="text-3xl font-bold mb-2">{{ t('contact.title') }}</h1>
            <p class="text-base-content/60 mb-8">{{ t('contact.subtitle') }}</p>

            <div v-if="success" class="alert alert-success mb-6">
                <span>{{ t('contact.success') }}</span>
            </div>

            <form v-if="!success" action="/contact" method="POST" class="flex flex-col gap-5">
                <input type="hidden" name="_token" :value="csrfToken">
                <input type="hidden" name="locale" :value="locale">

                <BaseInput
                    name="name"
                    :label="t('contact.name')"
                    :error="errors.name?.[0]"
                    required
                />

                <BaseInput
                    name="email"
                    type="email"
                    :label="t('contact.email')"
                    :error="errors.email?.[0]"
                    required
                />

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

                <BaseButton type="submit" full>{{ t('contact.submit') }}</BaseButton>
            </form>
        </main>
        <Footer />
    </div>
</template>

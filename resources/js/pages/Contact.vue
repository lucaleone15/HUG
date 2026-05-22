<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import NavBar from '../components/NavBar.vue'

const { t } = useI18n()

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

            <form action="/contact" method="POST" class="flex flex-col gap-4">
                <input type="hidden" name="_token" :value="csrfToken">

                <label class="form-control">
                    <div class="label"><span class="label-text">{{ t('contact.name') }}</span></div>
                    <input type="text" name="name" required maxlength="255"
                        class="input input-bordered"
                        :class="errors.name ? 'input-error' : ''">
                    <div v-if="errors.name" class="label">
                        <span class="label-text-alt text-error">{{ errors.name[0] }}</span>
                    </div>
                </label>

                <label class="form-control">
                    <div class="label"><span class="label-text">{{ t('contact.email') }}</span></div>
                    <input type="email" name="email" required maxlength="255"
                        class="input input-bordered"
                        :class="errors.email ? 'input-error' : ''">
                    <div v-if="errors.email" class="label">
                        <span class="label-text-alt text-error">{{ errors.email[0] }}</span>
                    </div>
                </label>

                <label class="form-control">
                    <div class="label"><span class="label-text">{{ t('contact.type') }}</span></div>
                    <select name="type" required class="select select-bordered">
                        <option v-for="type in types" :key="type" :value="type">
                            {{ t(`contact.${type}`) }}
                        </option>
                    </select>
                </label>

                <label class="form-control">
                    <div class="label"><span class="label-text">{{ t('contact.message') }}</span></div>
                    <textarea name="message" required maxlength="5000" rows="5"
                        class="textarea textarea-bordered resize-none"
                        :class="errors.message ? 'textarea-error' : ''"></textarea>
                    <div v-if="errors.message" class="label">
                        <span class="label-text-alt text-error">{{ errors.message[0] }}</span>
                    </div>
                </label>

                <button type="submit" class="btn bg-[#E30613] hover:bg-[#c0051f] text-white border-none">
                    {{ t('contact.submit') }}
                </button>
            </form>
        </main>
    </div>
</template>

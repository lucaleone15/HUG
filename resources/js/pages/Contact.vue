<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import NavBar from '../components/ui/NavBar.vue'
import Footer from '../components/ui/Footer.vue'
import BaseInput from '../components/ui/BaseInput.vue'
import BaseButton from '../components/ui/BaseButton.vue'

const { t, locale } = useI18n()

const formRef     = ref(null)
const formVisible = ref(false)

onMounted(() => {
    const io = new IntersectionObserver(
        ([e]) => { if (e.isIntersecting) { formVisible.value = true; io.disconnect() } },
        { threshold: 0.08 }
    )
    if (formRef.value) io.observe(formRef.value)
})

const props = defineProps({
    success: { type: String, default: null },
    errors:  { type: Object, default: () => ({}) },
    old:     { type: Object, default: () => ({}) },
})

const hasErrors = computed(() => Object.keys(props.errors).length > 0)

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? ''

const types = ['type_general', 'type_partnership', 'type_technical', 'type_other']

const selectedType = ref(props.old?.type || '')
const message      = ref(props.old?.message || '')
</script>

<template>
    <div class="min-h-screen bg-base-100 flex flex-col">
        <NavBar />

        <main class="max-w-5xl mx-auto px-6 py-16 flex-1 w-full">

            <!-- En-tête -->
            <div class="mb-14 page-hero-text">
                <h1 class="font-extrabold leading-tight text-base-content"
                    style="font-size: clamp(2rem, 5vw, 3.25rem);">
                    {{ t('contact.title') }}
                </h1>
            </div>

            <!-- Succès -->
            <div v-if="success"
                 class="py-12 text-center border-t border-base-200">
                <div class="text-brand font-bold mb-3"
                     style="font-size: clamp(1.25rem, 2.5vw, 1.75rem);">
                    {{ t('contact.success') }}
                </div>
                <a href="/" class="text-sm text-base-content/50 hover:text-base-content"
                   style="transition: color 150ms ease;">← {{ t('result.back_home') }}</a>
            </div>

            <!-- 2 colonnes : infos + formulaire -->
            <div v-else class="grid md:grid-cols-[1fr_1.6fr] gap-16 border-t border-base-200 pt-12" ref="formRef">

                <!-- Colonne gauche : info -->
                <div class="space-y-8 reveal-up" :class="{ 'reveal-up--visible': formVisible }">
                    <p class="text-base-content/60 leading-relaxed" style="max-width: 36ch;">
                        {{ t('contact.subtitle') }}
                    </p>
                    <div class="space-y-1">
                        <p class="text-xs uppercase tracking-[0.2em] text-base-content/35 mb-2">{{ t('contact.email_label') }}</p>
                        <a href="mailto:info@donnez-votre-sang.ch"
                           class="text-sm font-medium hover:text-brand"
                           style="transition: color 150ms ease;">
                            info@donnez-votre-sang.ch
                        </a>
                    </div>
                </div>

                <!-- Colonne droite : formulaire -->
                <form action="/contact" method="POST" class="flex flex-col gap-5 reveal-up"
                      :class="{ 'reveal-up--visible': formVisible }"
                      style="transition-delay: 120ms;">
                    <input type="hidden" name="_token" :value="csrfToken">
                    <input type="hidden" name="locale" :value="locale">

                    <!-- Bandeau d'erreur global -->
                    <div v-if="hasErrors"
                         class="flex items-start gap-3 rounded-lg border border-error/30 bg-error/5 px-4 py-3 text-sm text-error">
                        <svg class="mt-0.5 shrink-0 w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ t('inscription.form_errors_banner') }}</span>
                    </div>

                    <BaseInput
                        name="name"
                        :label="t('contact.name')"
                        :error="errors.name?.[0]"
                        :model-value="old?.name"
                        required
                    />

                    <BaseInput
                        name="email"
                        type="email"
                        :label="t('contact.email')"
                        :error="errors.email?.[0]"
                        :model-value="old?.email"
                        required
                    />

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium">{{ t('contact.type') }}</label>
                        <select name="type" v-model="selectedType" required
                                class="select select-bordered w-full"
                                :class="errors.type ? 'select-error' : ''">
                            <option value="" disabled>{{ t('contact.type_placeholder') }}</option>
                            <option v-for="type in types" :key="type" :value="type">
                                {{ t(`contact.${type}`) }}
                            </option>
                        </select>
                        <span v-if="errors.type" class="text-error text-xs">{{ errors.type[0] }}</span>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium">{{ t('contact.message') }}</label>
                        <textarea name="message" v-model="message" required maxlength="5000" rows="5"
                            class="textarea textarea-bordered resize-none w-full"
                            :class="errors.message ? 'textarea-error' : ''"></textarea>
                        <span v-if="errors.message" class="text-error text-xs">{{ errors.message[0] }}</span>
                    </div>

                    <BaseButton type="submit" full>{{ t('contact.submit') }}</BaseButton>
                </form>
            </div>

        </main>
        <Footer />
    </div>
</template>

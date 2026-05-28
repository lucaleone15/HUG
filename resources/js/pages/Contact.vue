<script setup>
import { ref, onMounted } from 'vue'
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
})

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? ''

const types = ['type_general', 'type_partnership', 'type_technical', 'type_other']
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
                        <p class="text-xs uppercase tracking-[0.2em] text-base-content/35 mb-2">Email</p>
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
                            <option value="" disabled selected>{{ t('contact.type_placeholder') }}</option>
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
            </div>

        </main>
        <Footer />
    </div>
</template>

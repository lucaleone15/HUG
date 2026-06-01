<script setup>
import { computed, ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import NavBar from '../components/ui/NavBar.vue'
import Footer from '../components/ui/Footer.vue'

const { t } = useI18n()

const gridRef     = ref(null)
const gridVisible = ref(false)

onMounted(() => {
    const io = new IntersectionObserver(
        ([e]) => { if (e.isIntersecting) { gridVisible.value = true; io.disconnect() } },
        { threshold: 0.1 }
    )
    if (gridRef.value) io.observe(gridRef.value)
})

const items = computed(() => [
    { num: '01', label: t('kit.item_1') },
    { num: '02', label: t('kit.item_2') },
    { num: '03', label: t('kit.item_3') },
    { num: '04', label: t('kit.item_4') },
])
</script>

<template>
    <div class="min-h-screen bg-base-100 flex flex-col">
        <NavBar />

        <main class="max-w-5xl mx-auto px-6 py-16 flex-1 w-full">

            <div class="mb-14 border-b border-base-200 pb-12 page-hero-text">
                <p class="text-xs uppercase tracking-[0.2em] text-base-content/35 mb-5">{{ t('nav.kit') }}</p>
                <h1 class="font-extrabold leading-tight text-base-content mb-5"
                    style="font-size: clamp(2rem, 5vw, 3.25rem);">
                    {{ t('kit.title') }}
                </h1>
                <p class="text-base-content/60 leading-relaxed"
                   style="max-width: 58ch; font-size: clamp(0.95rem, 1.5vw, 1.05rem);">
                    {{ t('kit.subtitle') }}
                </p>
            </div>

            <div class="grid md:grid-cols-[1fr_1.4fr] gap-16" ref="gridRef">

                <!-- Contenu du kit -->
                <div class="reveal-up" :class="{ 'reveal-up--visible': gridVisible }">
                    <p class="text-xs uppercase tracking-[0.2em] text-base-content/35 mb-6">{{ t('kit.contents_label') }}</p>
                    <ul class="divide-y divide-base-200">
                        <li v-for="item in items" :key="item.num"
                            class="flex items-center gap-5 py-4">
                            <span class="text-xs text-base-content/30 shrink-0 w-5">{{ item.num }}</span>
                            <span class="text-sm font-medium text-base-content/70">{{ item.label }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Statut disponibilité -->
                <div class="flex flex-col justify-start pt-0.5 reveal-up"
                     :class="{ 'reveal-up--visible': gridVisible }"
                     style="transition-delay: 120ms;">
                    <div class="border border-base-200 rounded-xl p-8">
                        <p class="text-xs uppercase tracking-[0.2em] text-base-content/35 mb-4">{{ t('kit.status_label') }}</p>
                        <p class="font-bold leading-tight mb-3"
                           style="font-size: clamp(1.1rem, 2vw, 1.35rem);">
                            {{ t('kit.coming_soon') }}
                        </p>
                        <p class="text-sm text-base-content/45 leading-relaxed" style="max-width: 38ch;">
                            {{ t('kit.status_desc') }}
                        </p>
                        <div class="mt-6 pt-6 border-t border-base-200">
                            <p class="text-xs text-base-content/35">
                                {{ t('kit.question_prompt') }} <a href="/contact"
                                    class="text-base-content hover:text-brand"
                                    style="transition: color 150ms ease;">{{ t('kit.contact_link') }}</a>
                            </p>
                        </div>
                    </div>
                </div>

            </div>

        </main>
        <Footer />
    </div>
</template>

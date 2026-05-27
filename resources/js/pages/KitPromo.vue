<script setup>
import { useI18n } from 'vue-i18n'
import NavBar from '../components/ui/NavBar.vue'
import Footer from '../components/ui/Footer.vue'

const { t } = useI18n()
</script>

<template>
    <div class="min-h-screen bg-base-100 flex flex-col">
        <NavBar />
        <main class="max-w-2xl mx-auto px-6 py-16 text-center flex-1">
            <div class="text-6xl mb-6">📦</div>
            <h1 class="text-3xl font-bold mb-3">{{ t('kit.title') }}</h1>
            <p class="text-base-content/60 mb-8 max-w-md mx-auto">{{ t('kit.subtitle') }}</p>
            <div class="alert alert-info max-w-sm mx-auto">
                <span>{{ t('kit.coming_soon') }}</span>
            </div>
        </main>
        <Footer />
    </div>
</template>

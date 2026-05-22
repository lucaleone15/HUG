<script setup>
import { useI18n } from 'vue-i18n'
import NavBar from '../components/NavBar.vue'

const { t } = useI18n()

const props = defineProps({
    entreprises: Array,
})
</script>

<template>
    <div class="min-h-screen bg-base-100">
        <NavBar />

        <main class="max-w-5xl mx-auto px-6 py-12">
            <h1 class="text-3xl font-bold mb-2">{{ t('label.title') }}</h1>
            <p class="text-base-content/60 mb-10">{{ t('label.subtitle') }}</p>

            <div v-if="!entreprises?.length" class="text-base-content/50">
                {{ t('label.no_label') }}
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <a
                    v-for="e in entreprises"
                    :key="e.id"
                    :href="`/c/${e.slug}`"
                    class="card bg-base-100 border border-base-200 shadow-sm hover:shadow-md transition-shadow"
                >
                    <div class="card-body">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-1 self-stretch rounded-full" :style="`background-color: ${e.primary_color}`"></div>
                            <div v-if="e.logo_url" class="bg-white border border-base-200 rounded-lg p-1.5 w-14 h-10 flex items-center justify-center">
                                <img :src="e.logo_url" :alt="e.name" class="max-h-7 max-w-full object-contain">
                            </div>
                        </div>
                        <h2 class="card-title text-base">{{ e.name }}</h2>
                        <div class="flex gap-2 flex-wrap mt-1">
                            <span v-if="e.type" class="badge badge-ghost badge-sm">{{ e.type }}</span>
                            <span v-if="e.employee_count" class="badge badge-ghost badge-sm">
                                {{ e.employee_count }} {{ t('entreprise.employees') }}
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        </main>
    </div>
</template>

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
                        <div
                            class="h-1 rounded-full mb-3"
                            :style="`background-color: ${e.primary_color}`"
                        ></div>
                        <img v-if="e.logo_url" :src="e.logo_url" :alt="e.name" class="h-8 object-contain mb-2">
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

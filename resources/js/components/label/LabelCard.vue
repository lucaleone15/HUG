<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps({
    entreprise: {
        type: Object,
        required: true,
        // { id, name, slug, logo_url, primary_color, type, employee_count }
    },
})
</script>

<template>
    <a
        :href="`/c/${entreprise.slug}`"
        class="card bg-base-100 border border-base-200 shadow-sm hover:shadow-md transition-shadow"
    >
        <div class="card-body">
            <div class="flex items-center gap-3 mb-3">
                <div
                    class="w-1 self-stretch rounded-full"
                    :style="`background-color: ${entreprise.primary_color}`"
                ></div>
                <div
                    v-if="entreprise.logo_url"
                    class="bg-white border border-base-200 rounded-lg p-1.5 w-14 h-10 flex items-center justify-center"
                >
                    <img
                        :src="entreprise.logo_url"
                        :alt="entreprise.name"
                        class="max-h-7 max-w-full object-contain"
                    >
                </div>
            </div>
            <h2 class="card-title text-base">{{ entreprise.name }}</h2>
            <div class="flex gap-2 flex-wrap mt-1">
                <span v-if="entreprise.type" class="badge badge-ghost badge-sm">{{ t('inscription.type_' + entreprise.type) }}</span>
                <span v-if="entreprise.employee_count" class="badge badge-ghost badge-sm">
                    {{ entreprise.employee_count }} {{ t('entreprise.employees') }}
                </span>
            </div>
        </div>
    </a>
</template>

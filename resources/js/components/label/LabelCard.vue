<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps({
    entreprise: {
        type: Object,
        required: true,
        // { id, name, access_token, logo_url, primary_color, type, employee_count }
    },
})
</script>

<template>
    <a
        :href="`/c/${entreprise.access_token}`"
        class="card bg-base-100 border border-base-200 shadow-sm hover:shadow-lg hover:-translate-y-0.5 overflow-hidden"
        :style="`border-top-color: ${entreprise.primary_color}; border-top-width: 3px; transition: transform 200ms cubic-bezier(0.23,1,0.32,1), box-shadow 200ms cubic-bezier(0.23,1,0.32,1);`"
    >
        <div class="card-body">
            <div class="flex items-center gap-3 mb-3">
                <div
                    v-if="entreprise.logo_url"
                    class="bg-white border border-base-200 rounded-lg p-2 w-20 h-14 flex items-center justify-center shrink-0"
                >
                    <img
                        :src="entreprise.logo_url"
                        :alt="entreprise.name"
                        class="max-h-10 max-w-full object-contain"
                    >
                </div>
                <div
                    v-else
                    class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold shrink-0 select-none"
                    :style="`background-color: ${entreprise.primary_color}`"
                >
                    {{ entreprise.name[0] }}
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

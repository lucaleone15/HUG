<script setup>
import { useI18n } from 'vue-i18n'
import LogoContainer from '../ui/LogoContainer.vue'

const { t } = useI18n()

defineProps({
    entreprise: {
        type: Object,
        required: true,
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
                <LogoContainer
                    :logo-url="entreprise.logo_url"
                    :primary-color="entreprise.primary_color"
                    :name="entreprise.name"
                    size="w-20 h-14"
                    init-rounded="rounded-lg"
                />
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

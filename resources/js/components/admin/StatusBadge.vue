<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()

defineProps({
    entreprise: { type: Object, required: true },
})

const badge = (e) => {
    if (!e.is_active && !e.is_validated) return { label: t('admin.status_pending'),   cls: 'badge-warning' }
    if (e.is_validated && e.is_active)   return { label: t('admin.status_active'),    cls: 'badge-success' }
    if (e.is_validated && !e.is_active)  return { label: t('admin.status_suspended'), cls: 'badge-error' }
    return                                      { label: t('admin.status_draft'),     cls: 'badge-ghost' }
}
</script>

<template>
    <div class="flex gap-1 items-center">
        <span class="badge badge-sm" :class="badge(entreprise).cls">{{ badge(entreprise).label }}</span>
        <span v-if="entreprise.is_labelled" class="badge badge-sm badge-ghost">Label</span>
    </div>
</template>

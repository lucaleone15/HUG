<script setup>
defineProps({
    entreprise: { type: Object, required: true },
})

const badge = (e) => {
    if (!e.is_active && !e.is_validated) return { label: 'En attente', cls: 'badge-warning' }
    if (e.is_validated && e.is_active)   return { label: 'Active',     cls: 'badge-success' }
    if (e.is_validated && !e.is_active)  return { label: 'Suspendue',  cls: 'badge-error' }
    return                                      { label: 'Brouillon',  cls: 'badge-ghost' }
}
</script>

<template>
    <div class="flex gap-1 items-center">
        <span class="badge badge-sm" :class="badge(entreprise).cls">{{ badge(entreprise).label }}</span>
        <span v-if="entreprise.is_labelled" class="badge badge-sm badge-ghost">Label</span>
    </div>
</template>

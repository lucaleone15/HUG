<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()

defineProps({
    sectors:    { type: Array,  required: true },
    modelValue: { type: String, default: null },
    allLabel:   { type: String, default: null },
})

defineEmits(['update:modelValue'])
</script>

<template>
    <div class="flex flex-wrap gap-2 mb-8">
        <button
            class="badge badge-lg cursor-pointer select-none transition-colors"
            :class="modelValue === null
                ? 'bg-brand text-white border-transparent'
                : 'badge-ghost hover:badge-neutral'"
            @click="$emit('update:modelValue', null)"
        >
            {{ allLabel ?? t('label.filter_all') }}
        </button>
        <button
            v-for="sector in sectors"
            :key="sector"
            class="badge badge-lg cursor-pointer select-none transition-colors"
            :class="modelValue === sector
                ? 'bg-brand text-white border-transparent'
                : 'badge-ghost hover:badge-neutral'"
            @click="$emit('update:modelValue', sector)"
        >
            {{ t('inscription.type_' + sector) }}
        </button>
    </div>
</template>

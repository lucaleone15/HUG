<script setup>
import { computed } from 'vue'

const props = defineProps({
    currentPage: { type: Number, required: true },
    totalPages:  { type: Number, required: true },
})

const emit = defineEmits(['update:currentPage'])

const pages = computed(() => {
    const range = []
    for (let i = 1; i <= props.totalPages; i++) range.push(i)
    return range
})
</script>

<template>
    <div v-if="totalPages > 1" class="flex justify-center gap-1 mt-8">
        <button
            class="btn btn-sm btn-ghost"
            :disabled="currentPage <= 1"
            @click="emit('update:currentPage', currentPage - 1)"
        >‹</button>
        <button
            v-for="p in pages"
            :key="p"
            class="btn btn-sm"
            :class="p === currentPage
                ? 'bg-brand border-none text-white hover:bg-brand-dark'
                : 'btn-ghost'"
            @click="emit('update:currentPage', p)"
        >{{ p }}</button>
        <button
            class="btn btn-sm btn-ghost"
            :disabled="currentPage >= totalPages"
            @click="emit('update:currentPage', currentPage + 1)"
        >›</button>
    </div>
</template>

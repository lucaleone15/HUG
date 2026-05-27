<script setup>
defineProps({
    title:    { type: String, required: true },
    subtitle: { type: String, default: null },
})
</script>

<template>
    <div class="mb-10">
        <h1 class="text-3xl font-bold mb-2">{{ title }}</h1>
        <p v-if="subtitle" class="text-base-content/60">{{ subtitle }}</p>
    </div>
</template>

<script setup>
defineProps({
    icon:        { type: String, required: true },
    title:       { type: String, required: true },
    description: { type: String, default: null },
    href:        { type: String, required: true },
})
</script>

<template>
    <a
        :href="href"
        class="card bg-base-100 border border-base-200 shadow-sm hover:shadow-md transition-shadow"
    >
        <div class="card-body">
            <div class="text-3xl mb-2">{{ icon }}</div>
            <h3 class="card-title text-base">{{ title }}</h3>
            <p v-if="description" class="text-sm text-base-content/60">{{ description }}</p>
        </div>
    </a>
</template>

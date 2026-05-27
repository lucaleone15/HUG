<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()

const props = defineProps({
    icon:        { type: String, required: true },
    title:       { type: String, required: true },
    description: { type: String, default: null },
    href:        { type: String, required: true },
    cta:         { type: String, default: null },
})
</script>

<template>
    <a
        :href="href"
        class="card bg-base-100 border border-base-200 shadow-sm hover:shadow-md transition-shadow group"
    >
        <div class="aspect-video bg-base-200 rounded-t-2xl flex items-center justify-center text-4xl overflow-hidden">
            <span class="group-hover:scale-110 transition-transform duration-200">{{ icon }}</span>
        </div>
        <div class="card-body gap-3">
            <h3 class="card-title text-base font-bold">{{ title }}</h3>
            <p v-if="description" class="text-sm text-base-content/60 leading-relaxed flex-1">{{ description }}</p>
            <div class="mt-2">
                <span class="btn btn-sm bg-brand hover:bg-brand-dark text-white border-none rounded-sm uppercase text-xs font-semibold tracking-wide">
                    {{ cta ?? t('home.read_more') }}
                </span>
            </div>
        </div>
    </a>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
import { setLocale, SUPPORTED_LOCALES } from '../../i18n'

const { locale } = useI18n()
const langLabels = { fr: 'FR', de: 'DE', it: 'IT', en: 'EN' }

defineProps({ light: { type: Boolean, default: false } })
</script>

<template>
    <div class="dropdown dropdown-end">
        <button tabindex="0" class="btn btn-ghost btn-sm text-xs font-mono"
                :class="light ? 'text-white hover:text-white hover:bg-white/15' : ''">
            {{ langLabels[locale] ?? locale.toUpperCase() }}
        </button>
        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box shadow-lg border border-base-200 p-1 w-20 z-50">
            <li v-for="lang in SUPPORTED_LOCALES" :key="lang">
                <button
                    class="text-xs font-mono justify-center px-2 py-1.5 rounded"
                    :class="locale === lang ? 'font-bold text-brand' : 'text-base-content/70'"
                    @click="setLocale(lang)"
                >
                    {{ langLabels[lang] }}
                </button>
            </li>
        </ul>
    </div>
</template>

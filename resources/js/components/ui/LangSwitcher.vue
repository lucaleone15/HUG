<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { setLocale, SUPPORTED_LOCALES } from '../../i18n'

const { locale } = useI18n()
const langLabels = { fr: 'FR', de: 'DE', it: 'IT', en: 'EN' }

const props = defineProps({ light: { type: Boolean, default: false } })

const isOpen      = ref(false)
const containerRef = ref(null)

const toggle = () => { isOpen.value = !isOpen.value }

const selectLang = (lang) => {
    setLocale(lang)
    isOpen.value = false
}

const onClickOutside = (e) => {
    if (containerRef.value && !containerRef.value.contains(e.target)) {
        isOpen.value = false
    }
}

onMounted(()    => document.addEventListener('pointerdown', onClickOutside, true))
onUnmounted(() => document.removeEventListener('pointerdown', onClickOutside, true))
</script>

<template>
    <div class="relative" ref="containerRef">
        <button
            class="btn btn-ghost btn-sm text-xs font-mono"
            :class="light ? 'text-white hover:text-white hover:bg-white/15' : ''"
            @click="toggle"
            :aria-expanded="isOpen"
            aria-haspopup="listbox"
        >
            {{ langLabels[locale] ?? locale.toUpperCase() }}
        </button>

        <Transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <ul v-if="isOpen"
                role="listbox"
                class="absolute right-0 top-full mt-1 bg-base-100 rounded-box shadow-lg border border-base-200 p-1 w-20 z-[9999]"
                style="transform-origin: top right;">
                <li v-for="lang in SUPPORTED_LOCALES" :key="lang" role="option">
                    <button
                        class="w-full text-xs font-mono text-center px-2 py-1.5 rounded"
                        :class="locale === lang ? 'font-bold text-brand' : 'text-base-content/70 hover:bg-base-200'"
                        @click="selectLang(lang)"
                    >
                        {{ langLabels[lang] }}
                    </button>
                </li>
            </ul>
        </Transition>
    </div>
</template>

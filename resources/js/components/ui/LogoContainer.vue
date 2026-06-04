<script setup>
import { useLogoBg } from '../../composables/useLogoBg.js'
import { initBg } from '../../utils/logoBg.js'

const props = defineProps({
    logoUrl:      { type: String,  default: null },
    primaryColor: { type: String,  default: null },
    name:         { type: String,  default: '' },
    // Taille du conteneur en classes Tailwind (ex: "w-20 h-14", "w-10 h-10")
    size:         { type: String,  default: 'w-14 h-14' },
    rounded:      { type: String,  default: 'rounded-lg' },
    initRounded:  { type: String,  default: 'rounded-full' },
})

const { bg } = useLogoBg(
    () => props.logoUrl,
    () => props.primaryColor,
)
</script>

<template>
    <!-- Logo image -->
    <div v-if="logoUrl"
         class="flex items-center justify-center shrink-0 p-1.5 overflow-hidden"
         :class="[size, rounded]"
         :style="`background-color: ${bg}`"
    >
        <img :src="logoUrl" :alt="name"
             class="max-h-full max-w-full object-contain">
    </div>

    <!-- Initiale (fallback sans logo) -->
    <div v-else
         class="flex items-center justify-center shrink-0 text-white font-bold select-none"
         :class="[size, initRounded]"
         :style="`background-color: ${initBg(primaryColor)}`"
    >
        {{ name?.charAt(0)?.toUpperCase() ?? '?' }}
    </div>
</template>

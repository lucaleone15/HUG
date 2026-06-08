<script setup>
import { ref, watchEffect } from "vue";
import { useLogoBg } from "../../composables/useLogoBg.js";
import { initBg } from "../../utils/logoBg.js";

const props = defineProps({
    logoUrl: { type: String, default: null },
    primaryColor: { type: String, default: null },
    name: { type: String, default: "" },
    size: { type: String, default: "w-14 h-14" },
    rounded: { type: String, default: "rounded-xl" },
    initRounded: { type: String, default: "rounded-xl" },
});

const { bg } = useLogoBg(
    () => props.logoUrl,
    () => props.primaryColor,
);

const imgError = ref(false);
watchEffect(() => {
    if (props.logoUrl) imgError.value = false;
});
</script>

<template>
    <!-- Logo image -->
    <div
        v-if="logoUrl && !imgError"
        class="flex items-center justify-center shrink-0 p-1.5 overflow-hidden"
        :class="[size, rounded]"
        :style="`background-color: ${bg}`"
    >
        <img
            :src="logoUrl"
            :alt="name"
            class="max-h-full max-w-full object-contain"
            @error="imgError = true"
        />
    </div>

    <!-- Initiale -->
    <div
        v-else
        class="flex items-center justify-center shrink-0 text-white font-bold select-none"
        :class="[size, initRounded]"
        :style="`background-color: ${initBg(primaryColor)}`"
    >
        {{ name?.charAt(0)?.toUpperCase() ?? "?" }}
    </div>
</template>

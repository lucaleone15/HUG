<script setup>
defineProps({
    modelValue: { type: Boolean, default: false },
    title:      { type: String, default: null },
})

defineEmits(['update:modelValue'])
</script>

<template>
    <dialog class="modal" :class="modelValue ? 'modal-open' : ''">
        <div class="modal-box">
            <button
                class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
                @click="$emit('update:modelValue', false)"
            >✕</button>
            <h3 v-if="title" class="font-bold text-lg mb-4">{{ title }}</h3>
            <slot />
            <div v-if="$slots.footer" class="modal-action">
                <slot name="footer" />
            </div>
        </div>
        <div class="modal-backdrop" @click="$emit('update:modelValue', false)"></div>
    </dialog>
</template>

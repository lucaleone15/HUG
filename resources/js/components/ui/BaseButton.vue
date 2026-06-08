<script setup>
const props = defineProps({
    variant: { type: String, default: "primary" },
    size: { type: String, default: "md" },
    href: { type: String, default: null },
    target: { type: String, default: null },
    rel: { type: String, default: null },
    type: { type: String, default: "button" },
    loading: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    full: { type: Boolean, default: false },
});

const variantClasses = {
    primary: "bg-brand hover:bg-brand-dark text-white border-none",
    dark: "bg-site-ink hover:bg-site-ink/80 text-white border-none",
    ghost: "btn-ghost",
    outline: "btn-outline",
    white: "bg-white text-brand hover:bg-white/90 border-none",
};

const sizeClasses = {
    sm: "btn-sm",
    md: "",
    lg: "btn-lg",
};
</script>

<template>
    <component
        :is="href ? 'a' : 'button'"
        :href="href"
        :target="href ? target : undefined"
        :rel="href && target === '_blank' ? rel || 'noopener noreferrer' : rel"
        :type="href ? undefined : type"
        :disabled="disabled || loading"
        class="btn"
        :class="[
            variantClasses[variant],
            sizeClasses[size],
            full ? 'w-full' : '',
        ]"
    >
        <span v-if="loading" class="loading loading-spinner loading-sm"></span>
        <slot />
    </component>
</template>

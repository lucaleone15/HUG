<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const year = new Date().getFullYear()

const links = [
    { href: '/label',   key: 'nav.label' },
    { href: '/trophee', key: 'nav.trophee' },
    { href: '/contact', key: 'nav.contact' },
]
</script>

<template>
    <footer class="border-t border-base-200 py-8 px-6 mt-auto">
        <div class="max-w-4xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <span class="font-bold text-sm">
                <span class="text-[#E30613]">HUG</span>
                <span class="text-base-content/40 mx-1">×</span>
                <span>CTS</span>
                <span class="text-base-content/40 ml-2">— {{ year }}</span>
            </span>
            <nav class="flex gap-4">
                <a
                    v-for="link in links"
                    :key="link.href"
                    :href="link.href"
                    class="text-sm text-base-content/50 hover:text-base-content transition-colors"
                >
                    {{ t(link.key) }}
                </a>
            </nav>
        </div>
    </footer>
</template>

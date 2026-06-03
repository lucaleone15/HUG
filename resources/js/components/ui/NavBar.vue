<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'
import LangSwitcher from './LangSwitcher.vue'

const { t } = useI18n()

const props = defineProps({
    transparent: { type: Boolean, default: false },
})

const path = window.location.pathname

const scrolled = ref(false)
let removeScroll = null
onMounted(() => {
    if (props.transparent) {
        const onScroll = () => { scrolled.value = window.scrollY > 60 }
        window.addEventListener('scroll', onScroll, { passive: true })
        removeScroll = () => window.removeEventListener('scroll', onScroll)
    }
})
onUnmounted(() => removeScroll?.())

const isOpaque = computed(() => !props.transparent || scrolled.value)

const links = [
    {
        href: '/',
        key: 'nav.home',
        d: 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
        dSolid: ['M12 3L2 11h2v10h16V11h2L12 3z M10 15h4v6h-4z'],
        solidFillRule: 'evenodd',
    },
    {
        href: '/trophee',
        key: 'nav.trophee',
        d: 'M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z',
        dSolid: ['M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z'],
    },
    {
        href: '/label',
        key: 'nav.label',
        d: 'M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-.702 3.142 3.745 3.745 0 01-3.142.702 3.745 3.745 0 01-3.068 1.593c-1.268 0-2.39-.63-3.068-1.593a3.745 3.745 0 01-3.142-.702 3.745 3.745 0 01-.702-3.142A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 01.702-3.142 3.745 3.745 0 013.142-.702A3.745 3.745 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.745 3.745 0 013.142.702 3.745 3.745 0 01.702 3.142A3.745 3.745 0 0121 12z',
        dSolid: ['M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.491 4.491 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.491 4.491 0 01-1.307 3.497 4.491 4.491 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.491 4.491 0 01-3.497-1.307 4.491 4.491 0 01-1.307-3.497A4.49 4.49 0 012.25 12a4.49 4.49 0 011.549-3.397 4.491 4.491 0 011.307-3.497 4.49 4.49 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z'],
    },
    {
        href: '/kit-promo',
        key: 'nav.kit',
        d: 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z',
        dSolid: [
            'M3.375 3C2.339 3 1.5 3.84 1.5 4.875v.75c0 1.036.84 1.875 1.875 1.875h17.25c1.035 0 1.875-.84 1.875-1.875v-.75C22.5 3.839 21.66 3 20.625 3H3.375z',
            'M3.087 9l.54 9.176A3 3 0 006.62 21h10.757a3 3 0 002.995-2.824L20.913 9H3.087z',
        ],
    },
    {
        href: '/contact',
        key: 'nav.contact',
        d: 'M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75',
        dSolid: [
            'M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z',
            'M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z',
        ],
    },
]

const isActive = (href) =>
    href === '/' ? path === '/' : path.startsWith(href)

const logoError = ref(false)
</script>

<template>
    <!-- ── Barre du haut ────────────────────────────────────────────────────── -->
    <nav class="navbar top-0 z-50 px-6 nav-bar w-full"
         :class="[
             props.transparent ? 'fixed' : 'sticky',
             isOpaque ? 'nav-bar--opaque' : 'nav-bar--transparent'
         ]">
        <div class="max-w-5xl mx-auto w-full flex items-center">

            <div class="navbar-start">
                <a href="/" class="flex items-center">
                    <template v-if="!logoError">
                        <img :src="isOpaque ? '/images/hug-logo_mark.svg' : '/images/hug-logo_mark_blanc.svg'" alt="HUG"
                             class="h-9 w-auto nav-logo lg:hidden"
                             @error="logoError = true">
                        <img :src="isOpaque ? '/images/hug-logo.svg' : '/images/hug-logo_blanc.svg'" alt="HUG"
                             class="h-9 w-auto nav-logo hidden lg:block"
                             @error="logoError = true">
                    </template>
                    <span v-else class="font-bold text-lg" :class="isOpaque ? 'text-brand' : 'text-white'">HUG</span>
                </a>
            </div>

            <!-- Navigation desktop -->
            <div class="navbar-center hidden lg:flex">
                <ul class="menu menu-horizontal px-1 gap-1">
                    <li v-for="link in links" :key="link.href">
                        <a :href="link.href"
                           class="rounded-lg text-sm"
                           :class="isActive(link.href)
                               ? (isOpaque ? 'font-semibold text-brand bg-red-50' : 'font-semibold text-white bg-white/15')
                               : (isOpaque ? 'text-base-content/70 hover:text-base-content' : 'text-white/75 hover:text-white')">
                            {{ t(link.key) }}
                        </a>
                    </li>
                </ul>
            </div>

            <div class="navbar-end gap-2">
                <a href="/inscription"
                   class="btn btn-sm border-none flex rounded-sm text-xs font-semibold text-white shadow-none"
                   :class="isActive('/inscription') ? 'bg-brand-dark' : 'bg-brand hover:bg-brand-dark'">
                    {{ t('nav.cta') }}
                </a>
                <LangSwitcher :light="!isOpaque" />
            </div>

        </div>
    </nav>

    <!-- ── Navigation bas — mobile / tablette uniquement ───────────────────── -->
    <nav class="fixed bottom-0 left-0 right-0 z-50 lg:hidden bg-base-100 border-t border-base-200"
         style="padding-bottom: env(safe-area-inset-bottom);">
        <div class="flex items-stretch">
            <a v-for="link in links" :key="link.href"
               :href="link.href"
               class="flex flex-col items-center justify-center flex-1 py-2 gap-1 min-h-[56px] transition-colors"
               :class="isActive(link.href) ? 'text-brand' : 'text-base-content/40 hover:text-base-content/70'">
                <!-- Icône outline (inactif) -->
                <svg v-if="!isActive(link.href)"
                     class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.5"
                     stroke-linecap="round" stroke-linejoin="round"
                     aria-hidden="true">
                    <path :d="link.d" />
                </svg>
                <!-- Icône solid (actif) -->
                <svg v-else
                     class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="currentColor" stroke="none"
                     :fill-rule="link.solidFillRule ?? 'nonzero'"
                     aria-hidden="true">
                    <path v-for="(p, pi) in link.dSolid" :key="pi" :d="p" />
                </svg>
                <span class="text-[0.6rem] tracking-[0.04em] leading-none">{{ t(link.key) }}</span>
            </a>
        </div>
    </nav>
</template>

<style scoped>
.nav-bar {
    transition: background-color 250ms ease, border-color 250ms ease, backdrop-filter 250ms ease;
}
.nav-bar--opaque {
    background-color: var(--color-base-100, #fff);
    border-bottom: 1px solid var(--color-base-200, #e5e7eb);
}
.nav-bar--transparent {
    background-color: transparent;
    border-bottom: 1px solid transparent;
}
</style>

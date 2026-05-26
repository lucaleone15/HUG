<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'
import { setLocale, SUPPORTED_LOCALES } from '../i18n'

const { t, locale } = useI18n()

const path = window.location.pathname

const links = [
    { href: '/',          key: 'nav.home' },
    { href: '/trophee',   key: 'nav.trophee' },
    { href: '/label',     key: 'nav.label' },
    { href: '/kit-promo', key: 'nav.kit' },
    { href: '/contact',   key: 'nav.contact' },
]

const langLabels = { fr: 'FR', de: 'DE', it: 'IT', en: 'EN' }

const isActive = (href) =>
    href === '/' ? path === '/' : path.startsWith(href)

const selectLang = async (lang) => {
    await setLocale(lang)
}

// Menu mobile — toggle Vue (le dropdown DaisyUI CSS ne fonctionne pas au toucher)
const menuOpen  = ref(false)
const menuRef   = ref(null)

const closeMenu = () => { menuOpen.value = false }

const onClickOutside = (e) => {
    if (menuRef.value && !menuRef.value.contains(e.target)) {
        menuOpen.value = false
    }
}

onMounted(()        => document.addEventListener('click', onClickOutside))
onBeforeUnmount(()  => document.removeEventListener('click', onClickOutside))
</script>

<template>
    <nav class="navbar bg-base-100 border-b border-base-200 sticky top-0 z-50">
        <div class="navbar-start">
            <a href="/" class="flex items-center gap-2 font-bold text-lg">
                <span class="text-[#E30613]">HUG</span>
                <span class="text-base-content/40">×</span>
                <span>CTS</span>
            </a>
        </div>

        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1 gap-1">
                <li v-for="link in links" :key="link.href">
                    <a
                        :href="link.href"
                        class="rounded-lg text-sm"
                        :class="isActive(link.href) ? 'font-semibold text-[#E30613] bg-red-50' : 'text-base-content/70 hover:text-base-content'"
                    >
                        {{ t(link.key) }}
                    </a>
                </li>
            </ul>
        </div>

        <div class="navbar-end gap-2">
            <a
                href="/inscription"
                class="btn btn-sm text-white border-none hidden lg:flex"
                :class="isActive('/inscription') ? 'bg-[#a0040e]' : 'bg-[#E30613] hover:bg-[#c0051f]'"
            >
                {{ t('nav.inscription') }}
            </a>

            <div class="dropdown dropdown-end">
                <button tabindex="0" class="btn btn-ghost btn-sm text-xs font-mono">
                    {{ langLabels[locale] ?? locale.toUpperCase() }}
                </button>
                <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box shadow-lg border border-base-200 p-1 w-20 z-50">
                    <li v-for="lang in SUPPORTED_LOCALES" :key="lang">
                        <button
                            class="text-xs font-mono justify-center px-2 py-1.5 rounded"
                            :class="locale === lang ? 'font-bold text-[#E30613]' : 'text-base-content/70'"
                            @click="selectLang(lang)"
                        >
                            {{ langLabels[lang] }}
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Burger mobile -->
            <div class="relative lg:hidden" ref="menuRef">
                <button
                    class="btn btn-ghost btn-square btn-sm"
                    @click.stop="menuOpen = !menuOpen"
                    aria-label="Menu"
                >
                    <svg v-if="!menuOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <ul
                    v-if="menuOpen"
                    class="absolute right-0 top-full mt-1 w-52 bg-base-100 rounded-box shadow-lg border border-base-200 p-2 z-50"
                >
                    <li v-for="link in links" :key="link.href">
                        <a
                            :href="link.href"
                            class="flex px-3 py-2 rounded-lg text-sm hover:bg-base-200"
                            :class="isActive(link.href) ? 'font-semibold text-[#E30613]' : 'text-base-content/70'"
                            @click="closeMenu"
                        >
                            {{ t(link.key) }}
                        </a>
                    </li>
                    <li class="mt-1 border-t border-base-200 pt-1">
                        <a
                            href="/inscription"
                            class="flex px-3 py-2 rounded-lg text-sm font-semibold text-[#E30613] hover:bg-red-50"
                            @click="closeMenu"
                        >
                            {{ t('nav.inscription') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

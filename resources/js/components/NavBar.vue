<script setup>
import { useI18n } from 'vue-i18n'
import { setLocale } from '../i18n'

const { t, locale } = useI18n()

const path = window.location.pathname

const links = [
    { href: '/',          key: 'nav.home' },
    { href: '/trophee',   key: 'nav.trophee' },
    { href: '/label',     key: 'nav.label' },
    { href: '/kit-promo', key: 'nav.kit' },
    { href: '/contact',   key: 'nav.contact' },
]

const isActive = (href) =>
    href === '/' ? path === '/' : path.startsWith(href)

const toggleLang = async () => {
    await setLocale(locale.value === 'fr' ? 'en' : 'fr')
}
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
            <button class="btn btn-ghost btn-sm text-xs font-mono" @click="toggleLang">
                {{ t('lang') }}
            </button>
            <!-- Mobile menu -->
            <div class="dropdown dropdown-end lg:hidden">
                <button class="btn btn-ghost btn-square btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <ul class="dropdown-content menu bg-base-100 rounded-box z-10 w-52 p-2 shadow-lg border border-base-200">
                    <li v-for="link in links" :key="link.href">
                        <a :href="link.href" :class="isActive(link.href) ? 'font-semibold text-[#E30613]' : ''">
                            {{ t(link.key) }}
                        </a>
                    </li>
                    <li>
                        <a href="/inscription" class="font-semibold text-[#E30613]">
                            {{ t('nav.inscription') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

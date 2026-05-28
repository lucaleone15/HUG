<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'
import LangSwitcher from './LangSwitcher.vue'

const { t } = useI18n()

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

// Menu mobile — toggle Vue (le dropdown DaisyUI CSS ne fonctionne pas au toucher)
const logoError = ref(false)
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
    <nav class="navbar bg-base-100 border-b border-base-200 sticky top-0 z-50 px-6">
        <div class="max-w-5xl mx-auto w-full flex items-center">

            <div class="navbar-start">
                <a href="/" class="flex items-center">
                    <img v-if="!logoError" :src="'/images/hug-logo.svg'" alt="HUG" class="h-9 w-auto"
                        @error="logoError = true">
                    <span v-else class="font-bold text-lg text-brand">HUG</span>
                </a>
            </div>

            <div class="navbar-center hidden lg:flex">
                <ul class="menu menu-horizontal px-1 gap-1">
                    <li v-for="link in links" :key="link.href">
                        <a
                            :href="link.href"
                            class="rounded-lg text-sm"
                            :class="isActive(link.href) ? 'font-semibold text-brand bg-red-50' : 'text-base-content/70 hover:text-base-content'"
                        >
                            {{ t(link.key) }}
                        </a>
                    </li>
                </ul>
            </div>

            <div class="navbar-end gap-2">
                <a
                    href="/inscription"
                    class="btn btn-sm text-white border-none hidden lg:flex rounded-sm uppercase text-xs tracking-wide font-semibold"
                    :class="isActive('/inscription') ? 'bg-brand-dark' : 'bg-brand hover:bg-brand-dark'"
                >
                    {{ t('nav.cta') }}
                </a>

                <LangSwitcher />

                <!-- Burger mobile -->
                <div class="relative lg:hidden" ref="menuRef">
                    <button
                        class="btn btn-ghost btn-square btn-sm"
                        @click.stop="menuOpen = !menuOpen"
                        :aria-label="t('nav.menu_aria')"
                    >
                        <svg v-if="!menuOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <Transition name="menu">
                    <ul
                        v-if="menuOpen"
                        class="absolute right-0 top-full mt-1 w-52 bg-base-100 rounded-box shadow-lg border border-base-200 p-2 z-50"
                    >
                        <li v-for="link in links" :key="link.href">
                            <a
                                :href="link.href"
                                class="flex px-3 py-2 rounded-lg text-sm hover:bg-base-200"
                                :class="isActive(link.href) ? 'font-semibold text-brand' : 'text-base-content/70'"
                                @click="closeMenu"
                            >
                                {{ t(link.key) }}
                            </a>
                        </li>
                        <li class="mt-1 border-t border-base-200 pt-1">
                            <a
                                href="/inscription"
                                class="flex px-3 py-2 rounded-lg text-sm font-semibold text-brand hover:bg-red-50"
                                @click="closeMenu"
                            >
                                {{ t('nav.inscription') }}
                            </a>
                        </li>
                    </ul>
                    </Transition>
                </div>
            </div>

        </div>
    </nav>
</template>

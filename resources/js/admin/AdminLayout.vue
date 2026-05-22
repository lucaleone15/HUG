<script setup>
import { RouterView, RouterLink, useRoute } from 'vue-router'
import { useAuth } from '../composables/useAuth.js'
import { useApi }  from '../composables/useApi.js'
import { useRouter } from 'vue-router'

const { user, clearAuth } = useAuth()
const api    = useApi()
const router = useRouter()
const route  = useRoute()

const nav = [
    { to: '/admin/dashboard',    label: 'Tableau de bord', icon: '📊' },
    { to: '/admin/entreprises',  label: 'Entreprises',     icon: '🏢' },
    { to: '/admin/submissions',  label: 'Soumissions',     icon: '📋' },
    { to: '/admin/analytics',    label: 'Analytics',       icon: '📈' },
    { to: '/admin/campaign-stats', label: 'Stats campagne', icon: '🩸' },
    { to: '/admin/report',       label: 'Rapport',         icon: '📄' },
]

const isActive = (path) => route.path.startsWith(path)

const logout = async () => {
    try { await api.post('/auth/logout') } catch {}
    clearAuth()
    router.push('/admin/login')
}
</script>

<template>
    <div class="drawer lg:drawer-open min-h-screen bg-base-200">
        <input id="admin-drawer" type="checkbox" class="drawer-toggle">

        <div class="drawer-content flex flex-col">
            <!-- Topbar mobile -->
            <header class="navbar bg-base-100 border-b border-base-200 lg:hidden sticky top-0 z-10">
                <label for="admin-drawer" class="btn btn-ghost btn-square drawer-button">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </label>
                <span class="font-bold text-sm ml-2">HUG × CTS — Admin</span>
            </header>

            <!-- Page content -->
            <main class="flex-1 p-6">
                <RouterView />
            </main>
        </div>

        <!-- Sidebar -->
        <div class="drawer-side z-20">
            <label for="admin-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
            <nav class="flex flex-col bg-base-100 border-r border-base-200 w-64 min-h-full">

                <!-- Logo -->
                <div class="p-5 border-b border-base-200">
                    <div class="flex items-center gap-2">
                        <span class="text-[#E30613] font-bold text-lg">HUG</span>
                        <span class="text-base-content/30 text-sm">×</span>
                        <span class="font-semibold text-sm">Admin</span>
                    </div>
                    <p v-if="user" class="text-xs text-base-content/50 mt-1 truncate">{{ user.email }}</p>
                </div>

                <!-- Navigation -->
                <ul class="menu flex-1 p-3 gap-0.5">
                    <li v-for="item in nav" :key="item.to">
                        <RouterLink
                            :to="item.to"
                            class="rounded-lg text-sm"
                            :class="isActive(item.to)
                                ? 'bg-[#E30613]/10 text-[#E30613] font-semibold'
                                : 'text-base-content/70 hover:text-base-content hover:bg-base-200'"
                        >
                            <span>{{ item.icon }}</span>
                            {{ item.label }}
                        </RouterLink>
                    </li>
                </ul>

                <!-- Logout -->
                <div class="p-3 border-t border-base-200">
                    <button class="btn btn-ghost btn-sm w-full justify-start text-base-content/60" @click="logout">
                        🚪 Déconnexion
                    </button>
                </div>
            </nav>
        </div>
    </div>
</template>

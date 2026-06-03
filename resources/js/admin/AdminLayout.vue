<script setup>
import { computed } from "vue";
import { RouterView, RouterLink, useRoute } from "vue-router";
import { useI18n } from "vue-i18n";
import { useAuth } from "../composables/useAuth.js";
import { useApi } from "../composables/useApi.js";
import { useRouter } from "vue-router";
import LangSwitcher from "../components/ui/LangSwitcher.vue";

const { t } = useI18n();
const { user, clearAuth } = useAuth();
const api = useApi();
const router = useRouter();
const route = useRoute();

const ICONS = {
    home:     'm2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
    settings: 'M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75',
    building: 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21',
    inbox:    'M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z',
    chart:    'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z',
    trophy:   'M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0',
    cog:      'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z',
    document: 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z',
    logout:   'M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9',
};

const nav = computed(() => [
    { to: "/admin/dashboard",      label: t("admin.nav_dashboard"),      icon: "home"     },
    { to: "/admin/entreprises",    label: t("admin.nav_entreprises"),    icon: "building" },
    { to: "/admin/submissions",    label: t("admin.nav_submissions"),    icon: "inbox"    },
    { to: "/admin/analytics",      label: t("admin.nav_analytics"),      icon: "chart"    },
    { to: "/admin/campaign-stats", label: t("admin.nav_campaign_stats"), icon: "cog"      },
    { to: "/admin/report",         label: t("admin.nav_report"),         icon: "document" },
    { to: "/admin/trophees",       label: t("admin.nav_trophees"),       icon: "trophy"   },
]);

const isActive = (path) => route.path.startsWith(path);

const userInitial = computed(() =>
    (user.value?.name?.[0] || user.value?.email?.[0] || "?").toUpperCase()
);
const userName = computed(() => user.value?.name || user.value?.email || "");

const closeDrawer = () => {
    const el = document.getElementById("admin-drawer");
    if (el) el.checked = false;
};

const logout = async () => {
    closeDrawer();
    try {
        await api.post("/auth/logout");
    } catch {}
    clearAuth();
    router.push("/admin/login");
};
</script>

<template>
    <div class="drawer lg:drawer-open min-h-screen bg-base-200">
        <input id="admin-drawer" type="checkbox" class="drawer-toggle" />

        <div class="drawer-content flex flex-col">
            <!-- Topbar -->
            <header class="bg-base-100 border-b border-base-200 sticky top-0 z-10 px-4 h-14 flex items-center gap-3">
                <!-- Hamburger (mobile only) -->
                <label for="admin-drawer" class="btn btn-ghost btn-sm btn-square drawer-button lg:hidden -ml-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </label>

                <!-- Mobile logo -->
                <a href="/" class="lg:hidden">
                    <img :src="'/images/hug-logo.svg'" alt="HUG" class="h-6" />
                </a>

                <!-- Year selector -->
                <button class="flex items-center gap-1.5 bg-brand text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-brand-dark transition-colors">
                    2026
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>

                <div class="flex-1"></div>

                <LangSwitcher />
            </header>

            <!-- Page content -->
            <main class="flex-1 p-6">
                <RouterView />
            </main>
        </div>

        <!-- Sidebar -->
        <div class="drawer-side z-20">
            <label for="admin-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
            <nav class="flex flex-col bg-brand w-64 min-h-full">
                <!-- Logo -->
                <div class="px-5 py-4 border-b border-white/10">
                    <a href="/" class="flex items-center gap-2">
                        <img :src="'/images/hug-logo_blanc.svg'" alt="HUG" class="h-7" />
                    </a>
                </div>

                <!-- Navigation principale -->
                <ul class="flex-1 p-3 flex flex-col gap-0.5">
                    <li v-for="item in nav" :key="item.to">
                        <RouterLink
                            :to="item.to"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors"
                            :class="
                                isActive(item.to)
                                    ? 'bg-white/15 text-white font-semibold'
                                    : 'text-white/70 hover:text-white hover:bg-white/10'
                            "
                            @click="closeDrawer"
                        >
                            <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="ICONS[item.icon]" />
                            </svg>
                            {{ item.label }}
                        </RouterLink>
                    </li>
                </ul>

                <!-- Réglages (juste au-dessus de la section utilisateur) -->
                <div class="px-3 pb-1">
                    <div class="border-t border-white/10 pt-2">
                        <RouterLink
                            to="/admin/settings"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors"
                            :class="
                                isActive('/admin/settings')
                                    ? 'bg-white/15 text-white font-semibold'
                                    : 'text-white/70 hover:text-white hover:bg-white/10'
                            "
                            @click="closeDrawer"
                        >
                            <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="ICONS.settings" />
                            </svg>
                            {{ t('admin.nav_settings') }}
                        </RouterLink>
                    </div>
                </div>

                <!-- User section -->
                <div class="p-3 border-t border-white/10">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-white font-bold text-sm shrink-0">
                            {{ userInitial }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-white text-sm font-medium truncate">{{ userName }}</p>
                        </div>
                        <button
                            @click="logout"
                            class="p-1.5 text-white/60 hover:text-white rounded transition-colors"
                            :title="t('admin.logout')"
                        >
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="ICONS.logout" />
                            </svg>
                        </button>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</template>

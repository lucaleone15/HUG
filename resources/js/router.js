import { createRouter, createWebHistory } from 'vue-router'
import { useAuth } from './composables/useAuth.js'

import AdminLogin          from './admin/AdminLogin.vue'
import AdminLayout         from './admin/AdminLayout.vue'
import AdminDashboard      from './admin/AdminDashboard.vue'
import AdminEntreprises    from './admin/AdminEntreprises.vue'
import AdminEntrepriseForm from './admin/AdminEntrepriseForm.vue'
import AdminEntrepriseShow from './admin/AdminEntrepriseShow.vue'
import AdminSubmissions    from './admin/AdminSubmissions.vue'
import AdminAnalytics      from './admin/AdminAnalytics.vue'
import AdminCampaignStats  from './admin/AdminCampaignStats.vue'
import AdminReport         from './admin/AdminReport.vue'

const routes = [
    { path: '/admin/login', component: AdminLogin, meta: { public: true } },
    {
        path: '/admin',
        component: AdminLayout,
        children: [
            { path: '',                     redirect: '/admin/dashboard' },
            { path: 'dashboard',            component: AdminDashboard,      name: 'dashboard' },
            { path: 'entreprises',          component: AdminEntreprises,    name: 'entreprises' },
            { path: 'entreprises/new',      component: AdminEntrepriseForm, name: 'entreprise-new' },
            { path: 'entreprises/:id',      component: AdminEntrepriseShow, name: 'entreprise-show' },
            { path: 'entreprises/:id/edit', component: AdminEntrepriseForm, name: 'entreprise-edit' },
            { path: 'submissions',          component: AdminSubmissions,    name: 'submissions' },
            { path: 'analytics',            component: AdminAnalytics,      name: 'analytics' },
            { path: 'campaign-stats',       component: AdminCampaignStats,  name: 'campaign-stats' },
            { path: 'report',               component: AdminReport,         name: 'report' },
        ],
    },
    // Catch-all : redirige vers login (pas vers /admin pour éviter une boucle de redirects)
    { path: '/:pathMatch(.*)*', redirect: '/admin/login' },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach((to) => {
    const { isAuthenticated } = useAuth()

    if (to.meta.public) {
        if (to.path === '/admin/login' && isAuthenticated.value) {
            return '/admin/dashboard'
        }
        return true
    }

    if (!isAuthenticated.value) {
        return '/admin/login'
    }

    return true
})

export default router

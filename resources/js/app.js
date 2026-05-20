import { createApp } from 'vue'
import App from './App.vue'
import { i18n, setLocale } from './i18n'

const app = createApp(App)
app.use(i18n)

// 1. Détermination de la locale initiale
const getInitialLocale = () => {
    // Priorité 1 : paramètre URL ?locale=en
    const urlLocale = new URLSearchParams(window.location.search).get('locale')
    if (['fr', 'en'].includes(urlLocale)) return urlLocale

    // Priorité 2 : sauvegarde locale (localStorage)
    const saved = localStorage.getItem('locale')
    if (saved) return saved

    // Priorité 3 : langue du navigateur ou fallback
    return navigator.language.slice(0, 2) || 'fr'
}

// ⚡ 2. Chargement des traductions, puis montage du DOM
setLocale(getInitialLocale()).then(() => {
    app.mount('#app')
})

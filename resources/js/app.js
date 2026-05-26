import { createApp } from 'vue'
import App from './App.vue'
import { i18n, setLocale, SUPPORTED_LOCALES } from './i18n'
import router from './router.js'

const pages = import.meta.glob('./pages/*.vue', { eager: true })

const getInitialLocale = () => {
    const urlLocale = new URLSearchParams(window.location.search).get('locale')
    if (SUPPORTED_LOCALES.includes(urlLocale)) return urlLocale
    const saved = localStorage.getItem('locale')
    if (saved && SUPPORTED_LOCALES.includes(saved)) return saved
    const browser = navigator.language?.slice(0, 2)
    return SUPPORTED_LOCALES.includes(browser) ? browser : 'fr'
}

const el = document.getElementById('app')
const pageName = el?.dataset.page ?? ''
const propsEl = document.getElementById('page-props')
const pageProps = propsEl ? JSON.parse(propsEl.textContent) : {}

const PageComponent = pageName ? pages[`./pages/${pageName}.vue`]?.default : null
const root = PageComponent ?? App

const app = createApp(root, PageComponent ? pageProps : {})
app.use(i18n)
if (!PageComponent) app.use(router)

setLocale(getInitialLocale()).then(() => {
    app.mount('#app')
})
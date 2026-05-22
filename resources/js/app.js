import { createApp } from 'vue'
import App from './App.vue'
import { i18n, setLocale } from './i18n'
import router from './router.js'

const pages = import.meta.glob('./pages/*.vue', { eager: true })

const getInitialLocale = () => {
    const urlLocale = new URLSearchParams(window.location.search).get('locale')
    if (['fr', 'en'].includes(urlLocale)) return urlLocale
    const saved = localStorage.getItem('locale')
    if (saved && ['fr', 'en'].includes(saved)) return saved
    return navigator.language?.slice(0, 2) === 'en' ? 'en' : 'fr'
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
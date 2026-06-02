// src/i18n/index.js
import { createI18n } from 'vue-i18n'

const LOCALE = import.meta.env.APP_LOCALE || 'fr'
const FALLBACK = import.meta.env.APP_FALLBACK_LOCALE || 'en'

const isValidLocale = (l) => /^[a-z]{2}(-[A-Z]{2})?$/.test(l)
const locale = isValidLocale(LOCALE) ? LOCALE : 'fr'
const fallbackLocale = isValidLocale(FALLBACK) ? FALLBACK : 'en'

export const i18n = createI18n({
    legacy: false,
    locale,
    fallbackLocale,
    messages: {},
    missingWarn: false,
    fallbackWarn: false,
})

export async function loadLocaleMessages(locale) {
    if (!i18n.global.availableLocales.includes(locale)) {
        const module = await import(`../locales/${locale}.json`)
        const messages = module.default || module
        i18n.global.setLocaleMessage(locale, messages)
    }
}

export const SUPPORTED_LOCALES = ['fr', 'de', 'it', 'en']

export async function setLocale(locale) {
    if (!SUPPORTED_LOCALES.includes(locale)) return
    await loadLocaleMessages(locale)
    i18n.global.locale.value = locale
    localStorage.setItem('locale', locale)
    document.cookie = `locale=${locale}; path=/; max-age=31536000; SameSite=Lax`
}

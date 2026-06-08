import { ref, watchEffect } from 'vue'

/**
 * Composable qui choisit un fond adapté pour un logo.
 *
 * Stratégie :
 *  1. PNG/JPG same-origin → canvas sampling (pixels non-transparents)
 *  2. SVG same-origin     → fetch source + parse fills hexadécimaux
 *  3. Échec (CORS, SVG tainted) → analyse luminance de primary_color
 *
 * Retourne un ref `bg` réactif (string CSS color).
 */
export function useLogoBg(getLogoUrl, getPrimaryColor) {
    const bg = ref('#f0f0f0')

    watchEffect(() => {
        const url = typeof getLogoUrl === 'function' ? getLogoUrl() : getLogoUrl
        const primaryColor = typeof getPrimaryColor === 'function' ? getPrimaryColor() : getPrimaryColor

        if (!url) { bg.value = '#f0f0f0'; return }

        bg.value = '#f0f0f0'

        detect(url, primaryColor).then(color => { bg.value = color })
    })

    return { bg }
}

// Fonctions internes

async function detect(url, primaryColor) {
    const ext = url.split('?')[0].toLowerCase().split('.').pop()
    try {
        if (ext === 'svg') return await detectSvg(url)
        return await detectRaster(url, primaryColor)
    } catch {
        return fallback(primaryColor)
    }
}

/** PNG / JPG / WEBP */
async function detectRaster(url, primaryColor) {
    return new Promise((resolve) => {
        const img = new Image()
        img.crossOrigin = 'anonymous'
        img.onload = () => {
            try {
                const size = 32
                const canvas = document.createElement('canvas')
                canvas.width = size; canvas.height = size
                const ctx = canvas.getContext('2d')
                ctx.drawImage(img, 0, 0, size, size)
                const { data } = ctx.getImageData(0, 0, size, size)
                resolve(bgFromPixels(data, primaryColor))
            } catch { resolve(fallback(primaryColor)) }
        }
        img.onerror = () => resolve(fallback(primaryColor))
        img.src = url
    })
}

/** SVG */
async function detectSvg(url) {
    const res = await fetch(url, { cache: 'force-cache' })
    const text = await res.text()

    const hexFills = [...text.matchAll(/fill[^:>\n]*?[:\s"']+#([0-9A-Fa-f]{3,6})\b/g)]
        .map(m => normalizeHex(m[1]))
        .filter(Boolean)

    const named = { white: '#ffffff', black: '#000000', '#000': '#000000', '#fff': '#ffffff' }
    const namedFills = [...text.matchAll(/fill[^:>\n]*?[:\s"']+(\b(?:white|black)\b)/gi)]
        .map(m => named[m[1].toLowerCase()])
        .filter(Boolean)

    const allFills = [...hexFills, ...namedFills]

    if (!allFills.length) return '#f0f0f0'

    const avgL = allFills.reduce((sum, h) => sum + luminance(h), 0) / allFills.length
    return pickBg(avgL)
}

/** Analyse pixels */
function bgFromPixels(data, primaryColor) {
    let light = 0, dark = 0
    for (let i = 0; i < data.length; i += 4) {
        if (data[i + 3] < 20) continue // transparent
        const L = luminance(`#${toHex(data[i])}${toHex(data[i + 1])}${toHex(data[i + 2])}`)
        if (L > 0.65) light++
        else if (L < 0.15) dark++
    }
    const total = light + dark
    if (total < 8) return fallback(primaryColor)
    return pickBg(light / total)
}

/** Choisit le fond selon la proportion de pixels clairs */
function pickBg(lightRatio) {
    if (lightRatio > 0.55) return '#3a3a3a'  // logo clair → gris foncé (pas noir)
    return '#f0f0f0'                          // logo sombre ou mixte → gris clair
}

/** Fallback */
function fallback(_hex) {
    return '#f0f0f0'
}

// Utilitaires

function luminance(hex) {
    if (!hex || !/^#[0-9A-Fa-f]{6}$/i.test(hex)) return 0.5
    const [r, g, b] = [hex.slice(1, 3), hex.slice(3, 5), hex.slice(5, 7)].map(h => {
        const c = parseInt(h, 16) / 255
        return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4)
    })
    return 0.2126 * r + 0.7152 * g + 0.0722 * b
}

function toHex(n) { return n.toString(16).padStart(2, '0') }

function normalizeHex(h) {
    if (h.length === 3) h = h[0] + h[0] + h[1] + h[1] + h[2] + h[2]
    return h.length === 6 ? `#${h}` : null
}

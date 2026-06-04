/**
 * Fond neutre fixe pour les conteneurs de logo (image).
 * Toujours #f0f0f0 — garantit la lisibilité de n'importe quel logo
 * (blanc, noir, coloré) sans dépendre de la couleur de marque.
 */
export function logoBg() {
    return '#f0f0f0'
}

/**
 * Fond pour les initiales (fallback sans logo).
 * Utilise primary_color sauf si trop clair → bascule sur un gris foncé.
 */
export function initBg(hex) {
    if (!hex || !/^#[0-9A-Fa-f]{6}$/i.test(hex)) return '#374151'
    const [r, g, b] = [hex.slice(1,3), hex.slice(3,5), hex.slice(5,7)].map(h => {
        const c = parseInt(h, 16) / 255
        return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4)
    })
    return (0.2126 * r + 0.7152 * g + 0.0722 * b) > 0.4 ? '#374151' : hex
}

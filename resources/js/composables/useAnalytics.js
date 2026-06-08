const ENDPOINT = '/api/analytics'

const getDevice = () => {
    const ua = navigator.userAgent
    if (/tablet|ipad/i.test(ua)) return 'tablet'
    if (/mobile|android|iphone/i.test(ua)) return 'mobile'
    return 'desktop'
}

/**
 * Envoie un événement analytics — fire & forget, jamais bloquant.
 * @param {string} type         Type d'événement (voir AnalyticsEvent::TYPES)
 * @param {number|null} entrepriseId
 * @param {string|null} sessionToken
 * @param {object} metadata
 */
export const sendAnalytics = (type, entrepriseId = null, sessionToken = null, metadata = {}) => {
    const payload = JSON.stringify({ type, entreprise_id: entrepriseId, session_token: sessionToken, metadata })

    // sendBeacon pour les événements fired dans beforeunload (plus fiable que fetch)
    if (type === 'quiz_abandoned' && navigator.sendBeacon) {
        navigator.sendBeacon(ENDPOINT, new Blob([payload], { type: 'application/json' }))
        return
    }

    fetch(ENDPOINT, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: payload,
    }).catch(() => { })
}

export { getDevice }

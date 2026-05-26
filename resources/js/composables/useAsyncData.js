import { ref } from 'vue'

export function useAsyncData(fn) {
    const data    = ref(null)
    const loading = ref(false)
    const error   = ref(null)

    const execute = async (...args) => {
        loading.value = true
        error.value   = null

        try {
            data.value = await fn(...args)
        } catch (e) {
            error.value = e.message ?? 'Erreur inconnue'
            data.value  = null
        } finally {
            loading.value = false
        }
    }

    return { data, loading, error, execute }
}

import { ref } from 'vue'
import { useApi } from '../composables/useApi.js'

const entreprises = ref([])
const loading     = ref(false)
const error       = ref(null)
const loaded      = ref(false)

export function useEntreprisesStore() {
    const api = useApi()

    const fetch = async () => {
        if (loaded.value) return

        loading.value = true
        error.value   = null

        try {
            const res         = await api.get('/admin/entreprises?per_page=100')
            entreprises.value = res.data
            loaded.value      = true
        } catch (e) {
            error.value = e.message ?? 'Erreur inconnue'
        } finally {
            loading.value = false
        }
    }

    const refresh = async () => {
        loaded.value = false
        await fetch()
    }

    return { entreprises, loading, error, loaded, fetch, refresh }
}

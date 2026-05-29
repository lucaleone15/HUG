import { ref, computed } from 'vue'

export function usePagination(fetchFn) {
    const page     = ref(1)
    const lastPage = ref(1)
    const total    = ref(0)
    const perPage  = ref(25)
    const loading  = ref(false)
    const error    = ref(null)
    const data     = ref(null)

    const isFirst = computed(() => page.value === 1)
    const isLast  = computed(() => page.value >= lastPage.value)

    const load = async () => {
        loading.value = true
        error.value   = null

        try {
            const res      = await fetchFn(page.value)
            data.value     = res.data
            lastPage.value = res.meta.last_page
            total.value    = res.meta.total ?? 0
            perPage.value  = res.meta.per_page ?? 25
        } catch (e) {
            error.value = e.message ?? 'Erreur inconnue'
            data.value  = null
        } finally {
            loading.value = false
        }
    }

    const prev  = () => { if (!isFirst.value) { page.value--; load() } }
    const next  = () => { if (!isLast.value)  { page.value++; load() } }
    const reset = () => { page.value = 1; load() }

    return { data, loading, error, page, lastPage, total, perPage, isFirst, isLast, load, prev, next, reset }
}

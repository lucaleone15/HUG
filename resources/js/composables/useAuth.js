import { ref, computed } from 'vue'

const token = ref(sessionStorage.getItem('admin_token') ?? null)
const user  = ref(JSON.parse(sessionStorage.getItem('admin_user') ?? 'null'))

export function useAuth() {
    const isAuthenticated = computed(() => !!token.value)

    const setAuth = (t, u) => {
        token.value = t
        user.value  = u
        if (t) {
            sessionStorage.setItem('admin_token', t)
            sessionStorage.setItem('admin_user', JSON.stringify(u))
        } else {
            sessionStorage.removeItem('admin_token')
            sessionStorage.removeItem('admin_user')
        }
    }

    const clearAuth = () => setAuth(null, null)

    return { token, user, isAuthenticated, setAuth, clearAuth }
}

import { ref, computed } from 'vue'

const token = ref(localStorage.getItem('admin_token') ?? null)
const user  = ref(JSON.parse(localStorage.getItem('admin_user') ?? 'null'))

export function useAuth() {
    const isAuthenticated = computed(() => !!token.value)

    const setAuth = (t, u) => {
        token.value = t
        user.value  = u
        if (t) {
            localStorage.setItem('admin_token', t)
            localStorage.setItem('admin_user', JSON.stringify(u))
        } else {
            localStorage.removeItem('admin_token')
            localStorage.removeItem('admin_user')
        }
    }

    const clearAuth = () => setAuth(null, null)

    return { token, user, isAuthenticated, setAuth, clearAuth }
}

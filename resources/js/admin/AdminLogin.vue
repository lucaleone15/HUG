<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../composables/useAuth.js'

const router = useRouter()
const { setAuth } = useAuth()

const email    = ref('')
const password = ref('')
const loading  = ref(false)
const error    = ref(null)

const submit = async () => {
    error.value   = null
    loading.value = true
    try {
        const res = await fetch('/api/auth/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ email: email.value, password: password.value }),
        })
        const data = await res.json()
        if (!res.ok) {
            error.value = data.message ?? 'Erreur de connexion'
            return
        }
        setAuth(data.token, data.user)
        router.push('/admin/dashboard')
    } catch {
        error.value = 'Impossible de contacter le serveur.'
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <div class="min-h-screen bg-base-200 flex items-center justify-center p-4">
        <div class="card bg-base-100 shadow-xl w-full max-w-sm">
            <div class="card-body gap-5">
                <div class="text-center">
                    <div class="text-4xl mb-3">🏥</div>
                    <h1 class="text-xl font-bold">HUG × CTS</h1>
                    <p class="text-base-content/50 text-sm">Interface d'administration</p>
                </div>

                <div v-if="error" class="alert alert-error text-sm py-2">
                    {{ error }}
                </div>

                <form class="flex flex-col gap-3" @submit.prevent="submit">
                    <label class="form-control">
                        <div class="label py-1"><span class="label-text text-sm">Email</span></div>
                        <input v-model="email" type="email" required autocomplete="email"
                            class="input input-bordered input-sm" placeholder="admin@hug-ge.ch">
                    </label>

                    <label class="form-control">
                        <div class="label py-1"><span class="label-text text-sm">Mot de passe</span></div>
                        <input v-model="password" type="password" required autocomplete="current-password"
                            class="input input-bordered input-sm">
                    </label>

                    <button type="submit" class="btn bg-[#E30613] hover:bg-[#c0051f] text-white border-none mt-2"
                        :disabled="loading">
                        <span v-if="loading" class="loading loading-spinner loading-sm"></span>
                        <span v-else>Se connecter</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

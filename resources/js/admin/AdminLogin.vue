<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuth } from '../composables/useAuth.js'
import LangSwitcher from '../components/ui/LangSwitcher.vue'

const router = useRouter()
const { t } = useI18n()
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
            error.value = data.message ?? t('admin.login_error_server')
            return
        }
        setAuth(data.token, data.user)
        router.push('/admin/dashboard')
    } catch {
        error.value = t('admin.login_error_server')
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <div class="min-h-screen bg-base-200 flex items-center justify-center p-4">
        <div class="card bg-base-100 shadow-xl w-full max-w-sm">
            <div class="card-body gap-5">
                <div class="flex justify-end">
                    <LangSwitcher />
                </div>
                <div class="text-center">
                    <img :src="'/images/hug-logo.svg'" alt="HUG × CTS" class="h-10 mx-auto mb-3">
                    <p class="text-base-content/50 text-sm">{{ t('admin.login_subtitle') }}</p>
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
                        <div class="label py-1"><span class="label-text text-sm">{{ t('admin.login_password') }}</span></div>
                        <input v-model="password" type="password" required autocomplete="current-password"
                            class="input input-bordered input-sm">
                    </label>

                    <button type="submit" class="btn bg-brand hover:bg-brand-dark text-white border-none mt-2"
                        :disabled="loading">
                        <span v-if="loading" class="loading loading-spinner loading-sm"></span>
                        <span v-else>{{ t('admin.login_submit') }}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

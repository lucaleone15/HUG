<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuth } from '../composables/useAuth.js'
import LangSwitcher from '../components/ui/LangSwitcher.vue'
import BaseButton from '../components/ui/BaseButton.vue'

const router = useRouter()
const { t } = useI18n()
const { setAuth } = useAuth()

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref(null)

const submit = async () => {
    error.value = null
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
                <!-- gestion langues -->
                <div class="flex justify-end">
                    <LangSwitcher />
                </div>
                <div class="text-center">
                    <a href="/">
                        <img :src="'/images/hug-logo.svg'" alt="HUG" class="h-10 mx-auto mb-4" />
                    </a>
                    <div class="bg-brand rounded-lg py-2.5 px-4">
                        <h1 class="text-white font-bold tracking-widest text-sm uppercase">{{
                            t('admin.login_title_banner') }}</h1>
                    </div>
                </div>

                <div v-if="error" class="alert alert-error text-sm py-2">
                    {{ error }}
                </div>

                <form class="flex flex-col gap-4" @submit.prevent="submit">
                    <div class="form-control">
                        <label class="label py-1">
                            <span class="label-text text-sm font-medium">{{ t('admin.login_email_label') }}</span>
                        </label>
                        <input v-model="email" type="email" required autocomplete="email"
                            class="input input-bordered input-sm" :placeholder="t('admin.login_email_placeholder')" />
                    </div>

                    <div class="form-control">
                        <label class="label py-1">
                            <span class="label-text text-sm font-medium">{{ t('admin.login_password') }}</span>
                        </label>
                        <input v-model="password" type="password" required autocomplete="current-password"
                            class="input input-bordered input-sm" placeholder="••••••" />
                        <div class="label pt-1">
                            <span></span>
                            <a href="#" class="label-text-alt text-xs underline text-base-content/60 hover:text-brand">
                                {{ t('admin.login_forgot_password') }}
                            </a>
                        </div>
                    </div>

                    <BaseButton type="submit" :loading="loading" :disabled="loading" class="mt-1">
                        {{ t('admin.login_submit') }}
                    </BaseButton>
                </form>
            </div>
        </div>
    </div>
</template>

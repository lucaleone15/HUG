<script setup>
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useApi } from '../composables/useApi.js'
import { useAuth } from '../composables/useAuth.js'
import BaseButton from '../components/ui/BaseButton.vue'
import BaseModal from '../components/ui/BaseModal.vue'

const { t } = useI18n()
const api = useApi()
const { user: currentUser } = useAuth()

const admins      = ref([])
const loading     = ref(true)
const saving      = ref(false)
const loadError   = ref(null)
const formError   = ref(null)
const fieldErrors = ref({})
const success     = ref(false)
const deleteModal = ref(false)
const deleteTarget = ref(null)
const deleting    = ref(null)

const form = ref({ name: '', email: '', password: '' })

const load = async () => {
    loading.value   = true
    loadError.value = null
    try {
        admins.value = await api.get('/admin/users')
    } catch (e) {
        loadError.value = e.message
    } finally {
        loading.value = false
    }
}

onMounted(load)

const addAdmin = async () => {
    saving.value     = true
    fieldErrors.value = {}
    formError.value  = null
    success.value    = false
    try {
        const newAdmin = await api.post('/admin/users', form.value)
        admins.value.push(newAdmin)
        form.value = { name: '', email: '', password: '' }
        success.value = true
        setTimeout(() => { success.value = false }, 4000)
    } catch (e) {
        if (e.errors) fieldErrors.value = e.errors
        else formError.value = e.message
    } finally {
        saving.value = false
    }
}

const askDelete = (admin) => {
    deleteTarget.value = admin
    deleteModal.value  = true
}

const confirmDelete = async () => {
    const admin = deleteTarget.value
    deleteModal.value  = false
    deleteTarget.value = null
    deleting.value = admin.id
    try {
        await api.del(`/admin/users/${admin.id}`)
        admins.value = admins.value.filter(a => a.id !== admin.id)
    } catch (e) {
        alert(e.message)
    } finally {
        deleting.value = null
    }
}

const fmt = (iso) => iso ? new Date(iso).toLocaleDateString('fr-CH') : '-'
const fieldError = (k) => fieldErrors.value[k]?.[0]
const isSelf = (admin) => admin.id === currentUser.value?.id
</script>

<template>
    <div class="w-full max-w-2xl">
        <h1 class="text-2xl font-bold mb-6">{{ t('admin.nav_settings') }}</h1>

        <!-- Section : Gestion des administrateurs -->
        <section>
            <div class="mb-4">
                <h2 class="text-lg font-semibold">{{ t('admin.settings_admins_section') }}</h2>
                <p class="text-sm text-base-content/50 mt-0.5">{{ t('admin.settings_admins_desc') }}</p>
            </div>

            <!-- Liste des admins -->
            <div class="card bg-base-100 shadow-sm mb-6">
                <div v-if="loading" class="flex justify-center py-10">
                    <span class="loading loading-spinner loading-md text-brand"></span>
                </div>
                <div v-else-if="loadError" class="p-4">
                    <div class="alert alert-error text-sm">{{ loadError }}</div>
                </div>
                <template v-else>
                    <!-- Mobile : cartes -->
                    <div class="sm:hidden divide-y divide-base-200">
                        <div v-for="admin in admins" :key="admin.id" class="flex items-center gap-3 p-4">
                            <div class="w-9 h-9 rounded-full bg-brand/10 flex items-center justify-center text-brand font-bold text-sm shrink-0">
                                {{ (admin.name?.[0] || admin.email[0]).toUpperCase() }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-sm truncate">{{ admin.name || '-' }}</div>
                                <div class="text-xs text-base-content/50 truncate">{{ admin.email }}</div>
                                <div class="text-xs text-base-content/30 mt-0.5">{{ t('admin.settings_admin_since') }} {{ fmt(admin.created_at) }}</div>
                            </div>
                            <div class="shrink-0">
                                <span v-if="isSelf(admin)" class="badge badge-ghost badge-sm">{{ t('admin.settings_you') }}</span>
                                <button v-else
                                    class="btn btn-ghost btn-xs text-error"
                                    :disabled="deleting === admin.id"
                                    @click="askDelete(admin)">
                                    <span v-if="deleting === admin.id" class="loading loading-spinner loading-xs"></span>
                                    <span v-else>{{ t('admin.settings_admin_remove') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop : tableau -->
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="table table-sm">
                            <thead>
                                <tr class="text-xs text-brand font-semibold uppercase tracking-wide">
                                    <th>{{ t('admin.settings_admin_name') }}</th>
                                    <th>{{ t('admin.settings_admin_email') }}</th>
                                    <th>{{ t('admin.settings_admin_since') }}</th>
                                    <th class="text-right">{{ t('admin.col_actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="admin in admins" :key="admin.id" class="hover">
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-brand/10 flex items-center justify-center text-brand font-bold text-xs shrink-0">
                                                {{ (admin.name?.[0] || admin.email[0]).toUpperCase() }}
                                            </div>
                                            <span class="font-medium text-sm">{{ admin.name || '-' }}</span>
                                            <span v-if="isSelf(admin)" class="badge badge-ghost badge-xs">{{ t('admin.settings_you') }}</span>
                                        </div>
                                    </td>
                                    <td class="text-sm text-base-content/60">{{ admin.email }}</td>
                                    <td class="text-sm text-base-content/50">{{ fmt(admin.created_at) }}</td>
                                    <td class="text-right">
                                        <button
                                            v-if="!isSelf(admin)"
                                            class="btn btn-ghost btn-xs text-error"
                                            :disabled="deleting === admin.id"
                                            @click="askDelete(admin)">
                                            <span v-if="deleting === admin.id" class="loading loading-spinner loading-xs"></span>
                                            <span v-else>{{ t('admin.settings_admin_remove') }}</span>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>

            <!-- Formulaire ajout admin -->
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-4">
                    <h3 class="font-semibold text-base">{{ t('admin.settings_admin_add') }}</h3>

                    <div v-if="success" class="alert alert-success text-sm py-2">
                        {{ t('admin.settings_admin_added') }}
                    </div>
                    <div v-if="formError" class="alert alert-error text-sm py-2">
                        {{ formError }}
                    </div>

                    <form class="grid grid-cols-1 sm:grid-cols-2 gap-4" @submit.prevent="addAdmin">
                        <label class="form-control">
                            <div class="label py-1"><span class="label-text text-sm">{{ t('admin.settings_admin_name') }}</span></div>
                            <input v-model="form.name" type="text" required
                                class="input input-bordered input-sm"
                                :class="fieldError('name') ? 'input-error' : ''">
                            <div v-if="fieldError('name')" class="label">
                                <span class="label-text-alt text-error">{{ fieldError('name') }}</span>
                            </div>
                        </label>

                        <label class="form-control">
                            <div class="label py-1"><span class="label-text text-sm">{{ t('admin.settings_admin_email') }}</span></div>
                            <input v-model="form.email" type="email" required
                                class="input input-bordered input-sm"
                                :class="fieldError('email') ? 'input-error' : ''">
                            <div v-if="fieldError('email')" class="label">
                                <span class="label-text-alt text-error">{{ fieldError('email') }}</span>
                            </div>
                        </label>

                        <label class="form-control col-span-1 sm:col-span-2">
                            <div class="label py-1"><span class="label-text text-sm">{{ t('admin.settings_admin_password') }}</span></div>
                            <input v-model="form.password" type="password" required minlength="8"
                                class="input input-bordered input-sm"
                                :class="fieldError('password') ? 'input-error' : ''"
                                placeholder="••••••••">
                            <div v-if="fieldError('password')" class="label">
                                <span class="label-text-alt text-error">{{ fieldError('password') }}</span>
                            </div>
                        </label>

                        <div class="col-span-1 sm:col-span-2 flex justify-end">
                            <BaseButton type="submit" size="sm" :loading="saving">
                                {{ t('admin.settings_admin_add_btn') }}
                            </BaseButton>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Modal confirmation suppression -->
        <BaseModal v-model="deleteModal" :title="t('admin.delete_confirm_title')">
            <p class="text-sm text-base-content/70">
                {{ t('admin.settings_remove_confirm', { name: deleteTarget?.name || deleteTarget?.email }) }}
            </p>
            <template #footer>
                <BaseButton variant="ghost" @click="deleteModal = false">{{ t('admin.cancel') }}</BaseButton>
                <BaseButton variant="outline" class="text-error border-error hover:bg-error hover:text-white" @click="confirmDelete">
                    {{ t('admin.settings_admin_remove') }}
                </BaseButton>
            </template>
        </BaseModal>
    </div>
</template>

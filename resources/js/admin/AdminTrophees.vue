<script setup>
import { ref, onMounted } from 'vue'
import { useApi } from '../composables/useApi.js'
import LogoContainer from '../components/ui/LogoContainer.vue'
import { useI18n } from 'vue-i18n'
import BaseButton from '../components/ui/BaseButton.vue'

const api = useApi()
const { t } = useI18n()

const loading = ref(false)
const saving = ref(false)
const ranked = ref([])
const unranked = ref([])

onMounted(async () => {
    loading.value = true
    try {
        const data = await api.get('/admin/trophees')
        ranked.value = data.ranked
        unranked.value = data.unranked
    } finally {
        loading.value = false
    }
})

const moveUp = (index) => {
    if (index === 0) return
    const tmp = ranked.value[index - 1]
    ranked.value[index - 1] = ranked.value[index]
    ranked.value[index] = tmp
}

const moveDown = (index) => {
    if (index === ranked.value.length - 1) return
    const tmp = ranked.value[index + 1]
    ranked.value[index + 1] = ranked.value[index]
    ranked.value[index] = tmp
}

const removeFromRanking = (index) => {
    const [e] = ranked.value.splice(index, 1)
    unranked.value = [...unranked.value, e].sort((a, b) => a.name.localeCompare(b.name))
}

const addToRanking = (index) => {
    const [e] = unranked.value.splice(index, 1)
    ranked.value.push(e)
}

const save = async () => {
    saving.value = true
    try {
        await api.put('/admin/trophees', {
            ranking: ranked.value.map(e => e.id),
        })
    } catch (e) {
        alert(e.message)
    } finally {
        saving.value = false
    }
}
</script>

<template>
    <div class="w-full max-w-2xl">
        <div class="flex items-center justify-between mb-6 gap-3 flex-wrap">
            <h1 class="text-xl sm:text-2xl font-bold">{{ t('admin.nav_trophees') }}</h1>
            <BaseButton size="sm" :loading="saving" @click="save">
                {{ t('admin.trophees_save') }}
            </BaseButton>
        </div>

        <div v-if="loading" class="flex justify-center py-16">
            <span class="loading loading-spinner loading-lg"></span>
        </div>

        <template v-else>
            <!-- Classement actuel -->
            <div class="card bg-base-100 shadow-sm mb-6">
                <div class="card-body gap-2">
                    <div class="divider text-xs text-base-content/40 mt-0">{{ t('admin.trophees_ranked') }}</div>

                    <div v-if="ranked.length === 0" class="text-sm text-base-content/40 text-center py-4">
                        {{ t('admin.trophees_empty') }}
                    </div>

                    <div v-for="(e, i) in ranked" :key="e.id"
                        class="flex items-center gap-3 py-2 border-b border-base-200 last:border-0">
                        <!-- Rangs-->
                        <span class="badge badge-neutral font-bold w-8 shrink-0 justify-center">#{{ i + 1 }}</span>

                        <!-- Gestion logo ou initiale-->
                        <LogoContainer :logo-url="e.logo_url" :primary-color="e.primary_color" :name="e.name"
                            size="w-8 h-8" rounded="rounded" init-rounded="rounded" class="text-xs" />

                        <!-- Couleur primaire -->
                        <div v-if="e.primary_color" class="h-4 w-4 rounded-full shrink-0 border border-base-200"
                            :style="`background:${e.primary_color}`" :title="e.primary_color"></div>

                        <!-- Nom -->
                        <span class="flex-1 font-medium text-sm truncate min-w-0">{{ e.name }}</span>

                        <!-- Flèches pour faciliter la compréhension -->
                        <div class="flex gap-1 shrink-0">
                            <BaseButton variant="ghost" size="xs" class="px-1.5" :disabled="i === 0" @click="moveUp(i)"
                                :aria-label="t('admin.trophees_move_up')">▲</BaseButton>
                            <BaseButton variant="ghost" size="xs" class="px-1.5" :disabled="i === ranked.length - 1"
                                @click="moveDown(i)" :aria-label="t('admin.trophees_move_down')">▼</BaseButton>
                        </div>

                        <!-- Retirer -->
                        <BaseButton variant="ghost" size="xs" class="text-error shrink-0" @click="removeFromRanking(i)">✕</BaseButton>
                    </div>
                </div>
            </div>

            <!-- Pour les entreprises non classées -->
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-2">
                    <div class="divider text-xs text-base-content/40 mt-0">{{ t('admin.trophees_unranked') }}</div>

                    <div v-if="unranked.length === 0" class="text-sm text-base-content/40 text-center py-4">
                        {{ t('admin.trophees_all_ranked') }}
                    </div>

                    <div v-for="(e, i) in unranked" :key="e.id"
                        class="flex items-center gap-3 py-2 border-b border-base-200 last:border-0">
                        <!-- gestion logo -->
                        <LogoContainer :logo-url="e.logo_url" :primary-color="e.primary_color" :name="e.name"
                            size="w-8 h-8" rounded="rounded" init-rounded="rounded" class="text-xs" />

                        <!-- Couleur primaire -->
                        <div v-if="e.primary_color" class="h-4 w-4 rounded-full shrink-0 border border-base-200"
                            :style="`background:${e.primary_color}`" :title="e.primary_color"></div>

                        <!-- Nom -->
                        <span class="flex-1 text-sm truncate min-w-0 text-base-content/70">{{ e.name }}</span>

                        <!-- Ajouter entreprise-->
                        <BaseButton variant="ghost" size="xs" class="text-brand shrink-0" @click="addToRanking(i)">+ {{ t('admin.trophees_add') }}</BaseButton>
                    </div>
                </div>
            </div>


            <div class="flex justify-end mt-4">
                <BaseButton size="sm" :loading="saving" @click="save">
                    {{ t('admin.trophees_save') }}
                </BaseButton>
            </div>
        </template>
    </div>
</template>

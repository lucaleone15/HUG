<script setup>
import { useI18n } from 'vue-i18n'
import NavBar from '../components/NavBar.vue'

const { t } = useI18n()

const props = defineProps({
    winners: Array,
})

const rankMedal = (rank) => ({ 1: '🥇', 2: '🥈', 3: '🥉' }[rank] ?? '🏅')
const rankLabel = (rank) => ({ 1: t('trophee.gold') ?? 'Or', 2: t('trophee.silver') ?? 'Argent', 3: t('trophee.bronze') ?? 'Bronze' }[rank] ?? `#${rank}`)

const podium = (props.winners ?? []).filter(w => w.trophy_rank <= 3)
const others = (props.winners ?? []).filter(w => w.trophy_rank > 3)
</script>

<template>
    <div class="min-h-screen bg-base-100">
        <NavBar />

        <main class="max-w-4xl mx-auto px-6 py-12">
            <h1 class="text-3xl font-bold mb-2">{{ t('trophee.title') }}</h1>
            <p class="text-base-content/60 mb-10">{{ t('trophee.subtitle') }}</p>

            <div v-if="!winners?.length" class="text-base-content/50">
                {{ t('trophee.no_winners') }}
            </div>

            <!-- Podium top 3 -->
            <div v-if="podium.length" class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
                <div
                    v-for="w in podium"
                    :key="w.id"
                    class="card shadow-md text-white text-center"
                    :style="`background-color: ${w.primary_color}`"
                >
                    <div class="card-body items-center">
                        <div class="text-4xl">{{ rankMedal(w.trophy_rank) }}</div>
                        <img v-if="w.logo_url" :src="w.logo_url" :alt="w.name" class="h-10 object-contain my-2 brightness-0 invert">
                        <h2 class="card-title text-base justify-center">{{ w.name }}</h2>
                        <div class="badge badge-outline text-white border-white/50 text-xs">
                            {{ rankLabel(w.trophy_rank) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Others -->
            <ul v-if="others.length" class="divide-y divide-base-200">
                <li
                    v-for="w in others"
                    :key="w.id"
                    class="flex items-center gap-4 py-3"
                >
                    <span class="text-2xl w-8 text-center">{{ rankMedal(w.trophy_rank) }}</span>
                    <div
                        class="w-1 h-8 rounded-full"
                        :style="`background-color: ${w.primary_color}`"
                    ></div>
                    <span class="font-medium">{{ w.name }}</span>
                    <span class="text-xs text-base-content/50 ml-auto">#{{ w.trophy_rank }}</span>
                </li>
            </ul>
        </main>
    </div>
</template>

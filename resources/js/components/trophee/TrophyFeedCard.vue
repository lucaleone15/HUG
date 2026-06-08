<script setup>
import { useI18n } from "vue-i18n";

const { t } = useI18n();

defineProps({
    winner: {
        type: Object,
        required: true,
    },
    mode: { type: String, default: "list" }, // 'podium' | 'list'
});

const medal = (rank) => ({ 1: "🥇", 2: "🥈", 3: "🥉" })[rank] ?? "🏅";

const rankLabel = (rank) =>
    ({
        1: t("trophee.gold"),
        2: t("trophee.silver"),
        3: t("trophee.bronze"),
    })[rank] ?? `#${rank}`;
</script>

<template>
    <!-- Podium card -->
    <div
        v-if="mode === 'podium'"
        class="card shadow-md text-white text-center"
        :style="`background-color: ${winner.primary_color}`"
    >
        <div class="card-body items-center">
            <div class="text-4xl">{{ medal(winner.trophy_rank) }}</div>
            <div
                v-if="winner.logo_url"
                class="bg-white/15 rounded-lg p-2 my-2 w-20 h-14 flex items-center justify-center"
            >
                <img
                    :src="winner.logo_url"
                    :alt="winner.name"
                    class="max-h-10 max-w-full object-contain"
                />
            </div>
            <h2 class="card-title text-base justify-center">
                {{ winner.name }}
            </h2>
            <div class="badge badge-outline text-white border-white/50 text-xs">
                {{ rankLabel(winner.trophy_rank) }}
            </div>
        </div>
    </div>

    <!-- List row -->
    <li v-else class="flex items-center gap-4 py-3">
        <span class="text-2xl w-8 text-center">{{
            medal(winner.trophy_rank)
        }}</span>
        <div
            class="w-1 h-8 rounded-full"
            :style="`background-color: ${winner.primary_color}`"
        ></div>
        <span class="font-medium">{{ winner.name }}</span>
        <span class="text-xs text-base-content/50 ml-auto"
            >#{{ winner.trophy_rank }}</span
        >
    </li>
</template>

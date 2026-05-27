<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'
import NavBar from '../components/ui/NavBar.vue'
import { sendAnalytics } from '../composables/useAnalytics.js'

const { t } = useI18n()

const props = defineProps({
    entreprise:    Object,
    questions:     Array,
    session_token: { type: String, default: null },
})

const answers   = ref({})
const formRef   = ref(null)
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? ''
const startedAt = Date.now()

const elapsedSeconds = () => Math.round((Date.now() - startedAt) / 1000)

// --- Conditional logic ---

const conditionsMet = (q) =>
    q.conditions.every(c => answers.value[c.depends_on] === c.expects)

const activeQuestions = computed(() =>
    (props.questions ?? []).filter(conditionsMet)
)

// First unanswered active question
const currentQuestion = computed(() =>
    activeQuestions.value.find(q => !(q.id in answers.value)) ?? null
)

const answeredActive = computed(() =>
    activeQuestions.value.filter(q => q.id in answers.value)
)

const total      = computed(() => activeQuestions.value.length)
const progress   = computed(() => total.value ? Math.round((answeredActive.value.length / total.value) * 100) : 0)
const isComplete = computed(() => !currentQuestion.value && total.value > 0)

// --- Analytics ---

onMounted(() => {
    sendAnalytics('quiz_started', props.entreprise.id, props.session_token, {})
})

const handleBeforeUnload = () => {
    if (isComplete.value) return
    const lastQ     = answeredActive.value.at(-1)
    const lastIndex = lastQ
        ? (props.questions ?? []).findIndex(q => q.id === lastQ.id)
        : 0
    sendAnalytics('quiz_abandoned', props.entreprise.id, props.session_token, {
        last_question_index: lastIndex,
        total_questions:     (props.questions ?? []).length,
        session_duration_s:  elapsedSeconds(),
    })
}

onMounted(()    => window.addEventListener('beforeunload', handleBeforeUnload))
onBeforeUnmount(() => window.removeEventListener('beforeunload', handleBeforeUnload))

// --- Actions ---

const selectAnswer = (optionId) => {
    if (!currentQuestion.value) return
    const qId = currentQuestion.value.id
    const next = { ...answers.value, [qId]: optionId }

    // Cascade: clear answers for questions now inactive
    props.questions.forEach(q => {
        if (q.id !== qId && q.id in next && !q.conditions.every(c => next[c.depends_on] === c.expects)) {
            delete next[q.id]
        }
    })
    answers.value = next

    sendAnalytics('question_answered', props.entreprise.id, props.session_token, {
        question_index:    answeredActive.value.length,
        session_duration_s: elapsedSeconds(),
    })
}

const goBack = () => {
    const last = answeredActive.value.at(-1)
    if (!last) return
    const next = { ...answers.value }
    delete next[last.id]
    answers.value = next
}
</script>

<template>
    <div class="min-h-screen bg-base-200">
        <NavBar />

        <!-- Company header -->
        <div class="py-4 px-6 text-white text-center text-sm font-medium"
             :style="`background-color: ${entreprise.primary_color}`">
            {{ entreprise.name }} &mdash; {{ t('quiz.title') }}
        </div>

        <main class="max-w-xl mx-auto px-4 py-8">

            <!-- Progress -->
            <div class="mb-6">
                <div class="flex justify-between text-xs text-base-content/60 mb-1">
                    <span>{{ answeredActive.length }} / {{ total }}</span>
                    <span>{{ progress }}%</span>
                </div>
                <progress class="progress w-full" :value="progress" max="100"
                          :style="`--tw-progress-fill: ${entreprise.primary_color}`"
                          style="accent-color: var(--tw-progress-fill)"></progress>
            </div>

            <!-- Current question -->
            <transition name="fade" mode="out-in">
                <div v-if="currentQuestion" :key="currentQuestion.id" class="card bg-base-100 shadow-md">
                    <div class="card-body gap-4">
                        <p class="text-xs font-semibold uppercase tracking-widest text-base-content/40">
                            {{ currentQuestion.category }}
                        </p>
                        <h2 class="text-lg font-semibold leading-snug">
                            {{ currentQuestion.content }}
                        </h2>
                        <p v-if="currentQuestion.hint" class="text-sm text-base-content/60 italic">
                            {{ currentQuestion.hint }}
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 pt-2">
                            <button
                                v-for="opt in currentQuestion.options"
                                :key="opt.id"
                                class="btn flex-1 transition-all"
                                :style="answers[currentQuestion.id] === opt.id
                                    ? `background-color: ${entreprise.primary_color}; color: white; border-color: transparent`
                                    : ''"
                                :class="answers[currentQuestion.id] === opt.id ? '' : 'btn-outline'"
                                @click="selectAnswer(opt.id)"
                            >
                                {{ opt.label }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Complete -->
                <div v-else-if="isComplete" class="card bg-base-100 shadow-md text-center">
                    <div class="card-body items-center gap-4">
                        <div class="text-5xl">✅</div>
                        <h2 class="card-title">{{ t('quiz.complete_title') }}</h2>
                        <p class="text-base-content/60 text-sm">{{ t('quiz.complete_subtitle') }}</p>
                        <form :action="`/c/${entreprise.slug}/quiz`" method="POST" ref="formRef">
                            <input type="hidden" name="_token" :value="csrfToken">
                            <input
                                v-for="(value, key) in answers"
                                :key="key"
                                type="hidden"
                                :name="`answers[${key}]`"
                                :value="value"
                            >
                            <button
                                type="submit"
                                class="btn text-white border-none px-8"
                                :style="`background-color: ${entreprise.primary_color}`"
                            >
                                {{ t('quiz.submit') }}
                            </button>
                        </form>
                    </div>
                </div>
            </transition>

            <!-- Back button -->
            <div class="mt-4 min-h-[36px]">
                <button
                    v-if="answeredActive.length > 0 && !isComplete"
                    class="btn btn-ghost btn-sm text-base-content/60"
                    @click="goBack"
                >
                    {{ t('quiz.back') }}
                </button>
            </div>
        </main>
    </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .15s ease; }
.fade-enter-from, .fade-leave-to       { opacity: 0; }
</style>

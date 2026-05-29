<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'
import NavBar from '../components/ui/NavBar.vue'
import { sendAnalytics } from '../composables/useAnalytics.js'
import { countries } from '../data/countries.js'
import { COUNTRY_REGION_MAP } from '../data/countryRegions.js'

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
const today     = new Date().toISOString().split('T')[0]

const elapsedSeconds = () => Math.round((Date.now() - startedAt) / 1000)

// ---------------------------------------------------------------------------
// Conditional logic
// ---------------------------------------------------------------------------

const conditionsMet = (q) =>
    q.conditions.every(c => answers.value[c.depends_on] === c.expects)

const activeQuestions = computed(() =>
    (props.questions ?? []).filter(conditionsMet)
)

const currentQuestion = computed(() =>
    activeQuestions.value.find(q => !(q.id in answers.value)) ?? null
)

const answeredActive = computed(() =>
    activeQuestions.value.filter(q => q.id in answers.value)
)

const total      = computed(() => activeQuestions.value.length)
const progress   = computed(() => total.value ? Math.round((answeredActive.value.length / total.value) * 100) : 0)
const isComplete = computed(() => !currentQuestion.value && total.value > 0)

// ---------------------------------------------------------------------------
// Local state for complex question types
// ---------------------------------------------------------------------------

// checklist
const checklistSelected = ref([])

// birth_check
const birthSelected  = ref([])   // item IDs sélectionnés
const birthCountries = ref({})   // { itemId: 'Maroc' }

// travel_check
const travelTrips = ref([])      // [{ country: '', region: '', return_date: '' }]

const travelTripsValid = computed(() =>
    travelTrips.value.length > 0 &&
    travelTrips.value.every(t => t.country && t.return_date)
)

// Synchronise l'état local quand la question courante change (retour arrière inclus)
watch(currentQuestion, (q) => {
    if (!q) return
    const existing = answers.value[q.id]
    switch (q.type) {
        case 'checklist':
            checklistSelected.value = Array.isArray(existing) ? [...existing] : []
            break
        case 'birth_check':
            birthSelected.value  = Array.isArray(existing?.items) ? [...existing.items] : []
            birthCountries.value = {}
            break
        case 'travel_check':
            travelTrips.value = Array.isArray(existing?.trips) ? existing.trips.map(t => ({ country: t.country ?? '', region: t.region ?? '', return_date: t.return_date ?? '' })) : []
            break
    }
}, { immediate: true })

// ---------------------------------------------------------------------------
// Analytics
// ---------------------------------------------------------------------------

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

// ---------------------------------------------------------------------------
// Réponse — logique commune (cascade + reset état local)
// ---------------------------------------------------------------------------

const setAnswer = (qId, value) => {
    const next = { ...answers.value, [qId]: value }

    // Cascade : effacer les réponses des questions devenues inactives
    props.questions.forEach(q => {
        if (q.id !== qId && q.id in next && !q.conditions.every(c => next[c.depends_on] === c.expects)) {
            delete next[q.id]
        }
    })

    answers.value = next

    // Reset de l'état local — le watch se chargera de réinitialiser si besoin
    checklistSelected.value = []
    birthSelected.value     = []
    birthCountries.value    = {}
    travelTrips.value       = []

    sendAnalytics('question_answered', props.entreprise.id, props.session_token, {
        question_index:     answeredActive.value.length,
        session_duration_s: elapsedSeconds(),
    })
}

// yes_no + option "aucun/non" des autres types
const selectAnswer = (optionId) => {
    if (!currentQuestion.value) return
    setAnswer(currentQuestion.value.id, optionId)
}

// checklist — toggle d'un item
const toggleChecklist = (itemId) => {
    const i = checklistSelected.value.indexOf(itemId)
    if (i === -1) checklistSelected.value.push(itemId)
    else checklistSelected.value.splice(i, 1)
}

const confirmChecklist = () => {
    if (!currentQuestion.value || checklistSelected.value.length === 0) return
    setAnswer(currentQuestion.value.id, [...checklistSelected.value])
}

// birth_check — toggle d'un item
const toggleBirth = (itemId) => {
    const i = birthSelected.value.indexOf(itemId)
    if (i === -1) birthSelected.value.push(itemId)
    else {
        birthSelected.value.splice(i, 1)
        delete birthCountries.value[itemId]
    }
}

const confirmBirth = () => {
    if (!currentQuestion.value || birthSelected.value.length === 0) return
    setAnswer(currentQuestion.value.id, {
        items:     [...birthSelected.value],
        countries: birthSelected.value.map(id => birthCountries.value[id] ?? ''),
    })
}

// travel_check
const addTrip    = () => travelTrips.value.push({ country: '', region: '', return_date: '' })
const removeTrip = (i) => travelTrips.value.splice(i, 1)
const updateTrip = (i, field, value) => { travelTrips.value[i][field] = value }
const updateTripCountry = (i, country) => {
    travelTrips.value[i].country = country
    travelTrips.value[i].region  = COUNTRY_REGION_MAP[country] ?? ''
}

const confirmTravel = () => {
    if (!currentQuestion.value || !travelTripsValid.value) return
    setAnswer(currentQuestion.value.id, { trips: travelTrips.value.map(t => ({ ...t })) })
}

// Retour arrière
const goBack = () => {
    const last = answeredActive.value.at(-1)
    if (!last) return
    const next = { ...answers.value }
    delete next[last.id]
    answers.value = next
}
</script>

<template>
    <div class="min-h-screen bg-base-200 flex flex-col">
        <NavBar />

        <!-- Company header -->
        <div class="py-4 px-6 text-white text-center text-sm font-medium"
             :style="`background-color: ${entreprise.primary_color}`">
            {{ entreprise.name }} &mdash; {{ t('quiz.title') }}
        </div>

        <main class="max-w-xl mx-auto px-4 py-8 flex-1">

            <!-- Progress -->
            <div class="mb-6">
                <div class="flex justify-end text-xs text-base-content/60 mb-1">
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

                        <!-- ================================================ -->
                        <!-- yes_no — deux boutons Oui / Non                  -->
                        <!-- ================================================ -->
                        <template v-if="!currentQuestion.type || currentQuestion.type === 'yes_no'">
                            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                                <button
                                    v-for="opt in currentQuestion.options"
                                    :key="opt.id"
                                    class="btn flex-1"
                                    style="transition: background-color 150ms ease, border-color 150ms ease, color 150ms ease, transform 140ms cubic-bezier(0.23,1,0.32,1);"
                                    :style="answers[currentQuestion.id] === opt.id
                                        ? `background-color: ${entreprise.primary_color}; color: white; border-color: transparent`
                                        : ''"
                                    :class="answers[currentQuestion.id] === opt.id ? '' : 'btn-outline'"
                                    @click="selectAnswer(opt.id)"
                                >
                                    {{ opt.label }}
                                </button>
                            </div>
                        </template>

                        <!-- ================================================ -->
                        <!-- checklist — cases à cocher + bouton Aucun        -->
                        <!-- ================================================ -->
                        <template v-else-if="currentQuestion.type === 'checklist'">
                            <div class="space-y-2 pt-2">
                                <label
                                    v-for="item in currentQuestion.items"
                                    :key="item.id"
                                    class="flex items-start gap-3 p-3 rounded-lg border cursor-pointer transition-colors hover:bg-base-200"
                                    :class="checklistSelected.includes(item.id)
                                        ? 'border-primary bg-primary/5'
                                        : 'border-base-300'"
                                >
                                    <input
                                        type="checkbox"
                                        class="checkbox checkbox-primary mt-0.5 shrink-0"
                                        :checked="checklistSelected.includes(item.id)"
                                        @change="toggleChecklist(item.id)"
                                    >
                                    <span class="leading-snug">
                                        <span class="text-sm">{{ item.label }}</span>
                                        <span v-if="item.sublabel" class="block text-xs text-base-content/50 mt-0.5">{{ item.sublabel }}</span>
                                    </span>
                                </label>
                            </div>

                            <div class="flex flex-col gap-2 pt-1">
                                <button
                                    v-if="checklistSelected.length > 0"
                                    class="btn text-white border-none"
                                    :style="`background-color: ${entreprise.primary_color}`"
                                    @click="confirmChecklist"
                                >
                                    {{ t('quiz.checklist_confirm', { n: checklistSelected.length }) }}
                                </button>
                                <button
                                    v-for="opt in currentQuestion.options"
                                    :key="opt.id"
                                    class="btn btn-outline"
                                    @click="selectAnswer(opt.id)"
                                >
                                    {{ opt.label }}
                                </button>
                            </div>
                        </template>

                        <!-- ================================================ -->
                        <!-- birth_check — cases + champ pays + bouton Aucun  -->
                        <!-- ================================================ -->
                        <template v-else-if="currentQuestion.type === 'birth_check'">
                            <div class="space-y-3 pt-2">
                                <div
                                    v-for="item in currentQuestion.items"
                                    :key="item.id"
                                >
                                    <label
                                        class="flex items-start gap-3 p-3 rounded-lg border cursor-pointer transition-colors hover:bg-base-200"
                                        :class="birthSelected.includes(item.id)
                                            ? 'border-primary bg-primary/5'
                                            : 'border-base-300'"
                                    >
                                        <input
                                            type="checkbox"
                                            class="checkbox checkbox-primary mt-0.5 shrink-0"
                                            :checked="birthSelected.includes(item.id)"
                                            @change="toggleBirth(item.id)"
                                        >
                                        <span class="text-sm leading-snug">{{ item.label }}</span>
                                    </label>
                                    <select
                                        v-if="birthSelected.includes(item.id)"
                                        class="select select-bordered select-sm w-full mt-1"
                                        :value="birthCountries[item.id] ?? ''"
                                        @change="e => birthCountries[item.id] = e.target.value"
                                    >
                                        <option value="">{{ t('quiz.birth_country_placeholder') }}</option>
                                        <option v-for="country in countries" :key="country" :value="country">
                                            {{ country }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex flex-col gap-2 pt-1">
                                <button
                                    v-if="birthSelected.length > 0"
                                    class="btn text-white border-none"
                                    :style="`background-color: ${entreprise.primary_color}`"
                                    @click="confirmBirth"
                                >
                                    {{ t('quiz.birth_confirm') }}
                                </button>
                                <button
                                    v-for="opt in currentQuestion.options"
                                    :key="opt.id"
                                    class="btn btn-outline"
                                    @click="selectAnswer(opt.id)"
                                >
                                    {{ opt.label }}
                                </button>
                            </div>
                        </template>

                        <!-- ================================================ -->
                        <!-- travel_check — ajout de voyages + bouton Non     -->
                        <!-- ================================================ -->
                        <template v-else-if="currentQuestion.type === 'travel_check'">
                            <div class="space-y-3 pt-2">

                                <!-- Liste des voyages saisis -->
                                <div
                                    v-for="(trip, i) in travelTrips"
                                    :key="i"
                                    class="p-3 rounded-lg border border-base-300 space-y-2"
                                >
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-base-content/70">
                                            {{ t('quiz.travel_trip') }} {{ i + 1 }}
                                        </span>
                                        <button
                                            class="btn btn-ghost btn-xs text-error"
                                            @click="removeTrip(i)"
                                        >✕</button>
                                    </div>

                                    <!-- Sélecteur de pays -->
                                    <select
                                        class="select select-bordered w-full select-sm"
                                        :value="trip.country"
                                        @change="e => updateTripCountry(i, e.target.value)"
                                    >
                                        <option value="">{{ t('quiz.travel_select_country') }}</option>
                                        <option
                                            v-for="country in countries"
                                            :key="country"
                                            :value="country"
                                        >
                                            {{ country }}
                                        </option>
                                    </select>

                                    <!-- Région détectée automatiquement -->
                                    <div v-if="trip.country && trip.region"
                                         class="text-xs text-base-content/50 flex items-center gap-1">
                                        <svg class="w-3 h-3 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-2.076 3.208-4.461 3.208-7.327a6.5 6.5 0 00-13 0c0 2.866 1.264 5.25 3.208 7.327a19.58 19.58 0 002.683 2.282 16.974 16.974 0 001.144.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ trip.region }}</span>
                                    </div>


                                    <!-- Avertissement risques si région à risque -->
                                    <div
                                        v-if="trip.region && currentQuestion.risk_map[trip.region]?.diseases?.length"
                                        class="flex items-start gap-2 text-xs text-warning bg-warning/10 rounded px-2 py-1.5"
                                    >
                                        <span>⚠</span>
                                        <span>
                                            <strong>{{ t('quiz.travel_risk_warning') }}</strong>
                                            {{ currentQuestion.risk_map[trip.region].diseases.join(', ') }}
                                        </span>
                                    </div>

                                    <!-- Date de retour -->
                                    <div>
                                        <p class="text-xs text-base-content/50 mb-1">
                                            {{ t('quiz.travel_return_date') }}
                                        </p>
                                        <input
                                            type="date"
                                            class="input input-bordered w-full input-sm"
                                            :value="trip.return_date"
                                            :max="today"
                                            @change="e => updateTrip(i, 'return_date', e.target.value)"
                                        >
                                    </div>
                                </div>

                                <!-- Bouton ajouter un voyage -->
                                <button
                                    class="btn btn-outline btn-sm w-full"
                                    @click="addTrip"
                                >
                                    {{ t('quiz.travel_add') }}
                                </button>

                                <!-- Confirmer les voyages (actif quand tous les champs sont remplis) -->
                                <button
                                    v-if="travelTripsValid"
                                    class="btn text-white border-none w-full"
                                    :style="`background-color: ${entreprise.primary_color}`"
                                    @click="confirmTravel"
                                >
                                    {{ t('quiz.travel_confirm') }}
                                </button>
                            </div>

                            <!-- Option "pas de voyage" -->
                            <div class="flex flex-col gap-2 pt-1">
                                <button
                                    v-for="opt in currentQuestion.options"
                                    :key="opt.id"
                                    class="btn btn-outline"
                                    @click="selectAnswer(opt.id)"
                                >
                                    {{ opt.label }}
                                </button>
                            </div>
                        </template>

                    </div>
                </div>

                <!-- Écran de confirmation finale -->
                <div v-else-if="isComplete" class="card bg-base-100 shadow-md text-center">
                    <div class="card-body items-center gap-4">
                        <div class="flex justify-center">
                            <svg width="52" height="52" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="12" cy="12" r="11" fill="#D32C37" opacity="0.1"/>
                                <path d="M7 12.5l3.5 3.5 6.5-7" stroke="#D32C37" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h2 class="card-title">{{ t('quiz.complete_title') }}</h2>
                        <p class="text-base-content/60 text-sm">{{ t('quiz.complete_subtitle') }}</p>
                        <form :action="`/c/${entreprise.slug}/quiz`" method="POST" ref="formRef">
                            <input type="hidden" name="_token" :value="csrfToken">
                            <!--
                                Sérialisation des réponses selon le type :
                                - string  (yes_no, option "aucun/non")  → answers[qN]=value
                                - Array   (checklist items)             → answers[qN][]=v1 & answers[qN][]=v2
                                - Object  (travel_check / birth_check)  → answers[qN]={"trips":[…]}  (JSON décodé côté PHP)
                            -->
                            <template v-for="(value, key) in answers" :key="key">
                                <template v-if="Array.isArray(value)">
                                    <input
                                        v-for="item in value"
                                        :key="item"
                                        type="hidden"
                                        :name="`answers[${key}][]`"
                                        :value="item"
                                    >
                                </template>
                                <template v-else-if="value !== null && typeof value === 'object'">
                                    <input
                                        type="hidden"
                                        :name="`answers[${key}]`"
                                        :value="JSON.stringify(value)"
                                    >
                                </template>
                                <template v-else>
                                    <input
                                        type="hidden"
                                        :name="`answers[${key}]`"
                                        :value="value"
                                    >
                                </template>
                            </template>
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

            <!-- Bouton retour -->
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
.fade-enter-active {
    transition: opacity 200ms cubic-bezier(0.23, 1, 0.32, 1),
                transform 200ms cubic-bezier(0.23, 1, 0.32, 1);
}
.fade-leave-active {
    transition: opacity 120ms ease,
                transform 120ms ease;
}
.fade-enter-from {
    opacity: 0;
    transform: translateY(8px);
}
.fade-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}
</style>

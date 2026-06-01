<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'
import { sendAnalytics } from '../composables/useAnalytics.js'
import { countries } from '../data/countries.js'
import { COUNTRY_REGION_MAP } from '../data/countryRegions.js'
import BaseButton from '../components/ui/BaseButton.vue'

const { t } = useI18n()

const props = defineProps({
    entreprise:    Object,
    questions:     Array,
    session_token: { type: String, default: null },
})

// ─── Phase ──────────────────────────────────────────────────────────────────
const phase = ref('intro')   // 'intro' | 'quiz'
const dossierCode = 'SANG-' + new Date().getFullYear().toString().slice(-2)

const closeQuiz = () => window.history.back()

const startQuiz = () => { phase.value = 'quiz' }


const pad = (n) => String(n ?? 0).padStart(2, '0')

// ─── Answers ────────────────────────────────────────────────────────────────
const answers   = ref({})
const formRef   = ref(null)
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? ''
const startedAt = Date.now()
const today     = new Date().toISOString().split('T')[0]
const elapsedSeconds = () => Math.round((Date.now() - startedAt) / 1000)

// ─── Conditional logic ───────────────────────────────────────────────────────
const conditionsMet   = (q) => q.conditions.every(c => answers.value[c.depends_on] === c.expects)
const activeQuestions = computed(() => (props.questions ?? []).filter(conditionsMet))
const currentQuestion = computed(() => activeQuestions.value.find(q => !(q.id in answers.value)) ?? null)
const answeredActive  = computed(() => activeQuestions.value.filter(q => q.id in answers.value))
const total      = computed(() => activeQuestions.value.length)
const progress   = computed(() => total.value ? Math.round((answeredActive.value.length / total.value) * 100) : 0)
const isComplete = computed(() => !currentQuestion.value && total.value > 0)

// ─── Local state for complex types ──────────────────────────────────────────
const checklistSelected = ref([])
const birthSelected     = ref([])
const birthCountries    = ref({})
const travelTrips       = ref([])

const travelTripsValid = computed(() =>
    travelTrips.value.length > 0 &&
    travelTrips.value.every(t => t.country && t.return_date)
)

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
            travelTrips.value = Array.isArray(existing?.trips)
                ? existing.trips.map(t => ({ country: t.country ?? '', region: t.region ?? '', return_date: t.return_date ?? '' }))
                : []
            break
    }
}, { immediate: true })

// ─── Analytics ───────────────────────────────────────────────────────────────
onMounted(() => { sendAnalytics('quiz_started', props.entreprise.id, props.session_token, {}) })

const handleBeforeUnload = () => {
    if (isComplete.value) return
    const lastQ     = answeredActive.value.at(-1)
    const lastIndex = lastQ ? (props.questions ?? []).findIndex(q => q.id === lastQ.id) : 0
    sendAnalytics('quiz_abandoned', props.entreprise.id, props.session_token, {
        last_question_index: lastIndex,
        total_questions:     (props.questions ?? []).length,
        session_duration_s:  elapsedSeconds(),
    })
}
onMounted(()    => window.addEventListener('beforeunload', handleBeforeUnload))
onBeforeUnmount(() => window.removeEventListener('beforeunload', handleBeforeUnload))

// ─── Answer handlers ─────────────────────────────────────────────────────────
const setAnswer = (qId, value) => {
    const next = { ...answers.value, [qId]: value }
    props.questions.forEach(q => {
        if (q.id !== qId && q.id in next && !q.conditions.every(c => next[c.depends_on] === c.expects))
            delete next[q.id]
    })
    answers.value = next
    checklistSelected.value = []
    birthSelected.value     = []
    birthCountries.value    = {}
    travelTrips.value       = []
    sendAnalytics('question_answered', props.entreprise.id, props.session_token, {
        question_index: answeredActive.value.length,
        session_duration_s: elapsedSeconds(),
    })
}

const selectAnswer = (optionId) => { if (currentQuestion.value) setAnswer(currentQuestion.value.id, optionId) }

const toggleChecklist = (id) => {
    const i = checklistSelected.value.indexOf(id)
    i === -1 ? checklistSelected.value.push(id) : checklistSelected.value.splice(i, 1)
}
const confirmChecklist = () => {
    if (currentQuestion.value && checklistSelected.value.length > 0)
        setAnswer(currentQuestion.value.id, [...checklistSelected.value])
}

const toggleBirth = (id) => {
    const i = birthSelected.value.indexOf(id)
    if (i === -1) birthSelected.value.push(id)
    else { birthSelected.value.splice(i, 1); delete birthCountries.value[id] }
}
const confirmBirth = () => {
    if (currentQuestion.value && birthSelected.value.length > 0)
        setAnswer(currentQuestion.value.id, {
            items:     [...birthSelected.value],
            countries: birthSelected.value.map(id => birthCountries.value[id] ?? ''),
        })
}

const addTrip    = () => travelTrips.value.push({ country: '', region: '', return_date: '' })
const removeTrip = (i) => travelTrips.value.splice(i, 1)
const updateTrip = (i, field, value) => { travelTrips.value[i][field] = value }
const updateTripCountry = (i, country) => {
    travelTrips.value[i].country = country
    travelTrips.value[i].region  = COUNTRY_REGION_MAP[country] ?? ''
}
const confirmTravel = () => {
    if (currentQuestion.value && travelTripsValid.value)
        setAnswer(currentQuestion.value.id, { trips: travelTrips.value.map(t => ({ ...t })) })
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
<div class="quiz-root">

    <!-- ══════════════════════════════════════════════════════════════════════ -->
    <!-- PHASE INTRO                                                            -->
    <!-- ══════════════════════════════════════════════════════════════════════ -->
    <Transition name="q-fade" mode="out-in">
    <div v-if="phase === 'intro'" key="intro" class="quiz-intro">

        <!-- Close button (both) -->
        <button class="intro-close-btn" @click="closeQuiz" :aria-label="t('quiz.back')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <!-- Bureau narrative + dossier (all screens) -->
        <div class="intro-grid">

            <!-- Left: bureau narrative -->
            <div class="intro-bureau">
                <div class="intro-bureau-content">
                    <div class="accès-badge">{{ t('quiz.intro_badge') }}</div>
                    <h1 class="intro-title">{{ t('quiz.intro_title') }}</h1>
                    <p class="intro-body">{{ t('quiz.intro_p1') }}</p>
                    <p class="intro-body intro-body--gap">{{ t('quiz.intro_p2') }}</p>
                </div>
                <div class="intro-cta-wrap">
                    <BaseButton variant="primary" :full="true" @click="startQuiz">
                        {{ t('quiz.intro_cta') }}
                    </BaseButton>
                </div>
            </div>

            <!-- Right: dossier folder (desktop only) -->
            <div class="intro-dossier" aria-hidden="true">
                <div class="dossier-folder">
                    <div class="dossier-folder-inner">
                        <p class="dossier-republic">République et canton de Genève</p>
                        <p class="dossier-hospital">Hôpitaux universitaires de Genève<br>Centre de transfusion sanguine</p>
                        <div class="dossier-confidential-badge">CONFIDENTIAL</div>
                        <div class="dossier-logo-stamp">
                            <img v-if="entreprise.logo_url" :src="entreprise.logo_url" :alt="entreprise.name" class="dossier-logo-img">
                            <span v-else class="dossier-logo-text">{{ entreprise.name }}</span>
                        </div>
                        <p class="dossier-folder-title">DOSSIER<br>{{ dossierCode }}</p>
                        <div class="dossier-folder-meta">
                            <p>Date d'ouverture : _ / _ / {{ new Date().getFullYear() }}</p>
                            <p>Inspecteur assigné : CTS</p>
                            <p>Statut : en attente</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </Transition>

    <!-- ══════════════════════════════════════════════════════════════════════ -->
    <!-- PHASE QUIZ                                                             -->
    <!-- ══════════════════════════════════════════════════════════════════════ -->
    <div v-if="phase === 'quiz'" class="quiz-screen flex flex-col min-h-screen">

        <!-- ── Top bar ────────────────────────────────────────────────────── -->
        <header class="quiz-topbar">
            <!-- Progress bar strip (full width) -->
            <div class="topbar-progress" role="progressbar" :aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100">
                <div class="topbar-progress-fill" :style="`width: ${progress}%`"></div>
            </div>
            <!-- Controls row -->
            <div class="topbar-controls">
                <button
                    v-if="answeredActive.length > 0 && !isComplete"
                    class="topbar-back-btn"
                    :title="t('quiz.back')"
                    @click="goBack"
                    aria-label="Question précédente"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <div v-else class="topbar-spacer"></div>
                <span class="topbar-pct">{{ progress }}%</span>
            </div>
        </header>

        <!-- ── Main content ─────────────────────────────────────────────── -->
        <main class="quiz-main flex-1 flex flex-col items-center px-4 py-6 lg:py-10 lg:px-8 overflow-x-hidden">

            <Transition name="q-slide" mode="out-in">

                <!-- ── QUESTION card ────────────────────────────────────── -->
                <div
                    v-if="currentQuestion"
                    :key="currentQuestion.id"
                    class="document-wrapper w-full max-w-5xl"
                >
                    <!-- Document tab (desktop) -->
                    <div class="hidden lg:flex document-tab justify-end">
                        <span class="doc-tab-inner">N° {{ dossierCode }}</span>
                    </div>

                    <!-- The paper document -->
                    <div class="document-card">

                        <!-- Tape (always visible, mobile prominent) -->
                        <div class="flex justify-center pt-1 pb-0">
                            <div class="tape-strip"></div>
                        </div>

                        <!-- Mobile only: HUG decoration -->
                        <div class="lg:hidden mobile-card-header">
                            <div class="hug-deco" aria-hidden="true">
                                <div class="hug-paper-lines"></div>
                                <span class="hug-logo-text">HUG</span>
                            </div>
                        </div>

                        <!-- Desktop header -->
                        <div class="hidden lg:block document-card-header">
                            DOSSIER {{ dossierCode }}
                        </div>

                        <!-- Two-panel grid (single col on mobile, two cols on desktop) -->
                        <div class="document-body">

                            <!-- ▌LEFT PANEL — Question ▐ -->
                            <div class="doc-left">

                                <!-- Classified stamp (all screens) -->
                                <div class="classified-stamp">
                                    <span class="classified-title">{{ t('quiz.classified_header') }}</span>
                                    <span class="classified-sub">{{ t('quiz.classified_sub') }} {{ dossierCode }}</span>
                                </div>

                                <!-- Question reference -->
                                <p class="piece-ref">Question {{ pad(currentQuestion.order) }}</p>
                                <div class="panel-divider"></div>

                                <!-- Category label: desktop only -->
                                <p class="hidden lg:block category-label">{{ currentQuestion.category }}</p>

                                <h2 class="question-text">{{ currentQuestion.content }}</h2>

                                <!-- Hint / Inspector note -->
                                <div v-if="currentQuestion.hint" class="hint-block">
                                    <p class="hint-label">
                                        {{ currentQuestion.type === 'checklist' ? t('quiz.symptoms_label') : t('quiz.inspector_label') }}
                                    </p>
                                    <ul v-if="currentQuestion.type === 'checklist'" class="hint-list">
                                        <li v-for="item in currentQuestion.items" :key="item.id" class="hint-list-item">
                                            {{ item.label }}
                                        </li>
                                    </ul>
                                    <p v-else class="hint-text">{{ currentQuestion.hint }}</p>
                                </div>

                            </div>

                            <!-- ▌RIGHT PANEL — Answers ▐ -->
                            <div class="doc-right">

                                <!-- Company logo stamp (desktop only) -->
                                <div v-if="entreprise.logo_url" class="hidden lg:flex doc-logo-wrap">
                                    <img :src="entreprise.logo_url" :alt="entreprise.name" class="doc-logo-img">
                                </div>

                                <!-- "Votre réponse" label: desktop only -->
                                <p class="your-answer-label hidden lg:block">{{ t('quiz.your_answer') }}</p>

                                <!-- ── yes_no ── -->
                                <template v-if="!currentQuestion.type || currentQuestion.type === 'yes_no'">
                                    <div class="answers-stack">
                                        <button
                                            v-for="(opt, i) in currentQuestion.options"
                                            :key="opt.id"
                                            :class="['quiz-btn', i === 0 ? 'quiz-btn--dark' : 'quiz-btn--red',
                                                     answers[currentQuestion.id] === opt.id ? 'quiz-btn--selected' : '']"
                                            @click="selectAnswer(opt.id)"
                                        >
                                            {{ opt.label }}
                                        </button>
                                    </div>
                                    <p class="choose-hint-text">{{ t('quiz.choose_hint') }}</p>
                                </template>

                                <!-- ── checklist ── -->
                                <template v-else-if="currentQuestion.type === 'checklist'">
                                    <div class="checklist-grid">
                                        <label
                                            v-for="item in currentQuestion.items"
                                            :key="item.id"
                                            :class="['check-item', checklistSelected.includes(item.id) ? 'check-item--on' : '']"
                                            @click.prevent="toggleChecklist(item.id)"
                                        >
                                            <span :class="['check-box', checklistSelected.includes(item.id) ? 'check-box--on' : '']">
                                                <svg v-if="checklistSelected.includes(item.id)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="w-3 h-3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </span>
                                            <div class="check-text">
                                                <span class="check-label-text">{{ item.label }}</span>
                                                <span v-if="item.sublabel" class="check-sublabel-text">{{ item.sublabel }}</span>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="answers-stack mt-3">
                                        <button v-if="checklistSelected.length > 0"
                                            class="quiz-btn quiz-btn--red"
                                            @click="confirmChecklist">
                                            {{ t('quiz.checklist_confirm', { n: checklistSelected.length }) }}
                                        </button>
                                        <button v-for="opt in currentQuestion.options" :key="opt.id"
                                            class="quiz-btn quiz-btn--dark"
                                            @click="selectAnswer(opt.id)">
                                            {{ opt.label }}
                                        </button>
                                    </div>
                                    <p class="choose-hint-text">{{ t('quiz.choose_hint') }}</p>
                                </template>

                                <!-- ── birth_check ── -->
                                <template v-else-if="currentQuestion.type === 'birth_check'">
                                    <div class="checklist-grid">
                                        <div v-for="item in currentQuestion.items" :key="item.id">
                                            <label
                                                :class="['check-item', birthSelected.includes(item.id) ? 'check-item--on' : '']"
                                                @click.prevent="toggleBirth(item.id)"
                                            >
                                                <span :class="['check-box', birthSelected.includes(item.id) ? 'check-box--on' : '']">
                                                    <svg v-if="birthSelected.includes(item.id)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="w-3 h-3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </span>
                                                <span class="check-label-text">{{ item.label }}</span>
                                            </label>
                                            <select v-if="birthSelected.includes(item.id)"
                                                class="quiz-select mt-1"
                                                :value="birthCountries[item.id] ?? ''"
                                                @change="e => birthCountries[item.id] = e.target.value">
                                                <option value="">{{ t('quiz.birth_country_placeholder') }}</option>
                                                <option v-for="c in countries" :key="c" :value="c">{{ c }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="answers-stack mt-3">
                                        <button v-if="birthSelected.length > 0"
                                            class="quiz-btn quiz-btn--red"
                                            @click="confirmBirth">
                                            {{ t('quiz.birth_confirm') }}
                                        </button>
                                        <button v-for="opt in currentQuestion.options" :key="opt.id"
                                            class="quiz-btn quiz-btn--dark"
                                            @click="selectAnswer(opt.id)">
                                            {{ opt.label }}
                                        </button>
                                    </div>
                                    <p class="choose-hint-text">{{ t('quiz.choose_hint') }}</p>
                                </template>

                                <!-- ── travel_check ── -->
                                <template v-else-if="currentQuestion.type === 'travel_check'">
                                    <div class="travel-list">
                                        <div v-for="(trip, i) in travelTrips" :key="i" class="travel-trip">
                                            <div class="trip-header">
                                                <span class="trip-num">{{ t('quiz.travel_trip') }} {{ i + 1 }}</span>
                                                <button class="trip-remove" @click="removeTrip(i)" :aria-label="`Supprimer voyage ${i + 1}`">✕</button>
                                            </div>
                                            <label class="quiz-field-label">{{ t('quiz.travel_region') }} {{ i + 1 }}</label>
                                            <select class="quiz-select" :value="trip.country"
                                                @change="e => updateTripCountry(i, e.target.value)">
                                                <option value="">{{ t('quiz.travel_select_country') }}</option>
                                                <option v-for="c in countries" :key="c" :value="c">{{ c }}</option>
                                            </select>
                                            <div v-if="trip.region" class="trip-region-tag">
                                                <span>📍</span> {{ trip.region }}
                                            </div>
                                            <div v-if="trip.region && currentQuestion.risk_map[trip.region]?.diseases?.length"
                                                class="trip-warning">
                                                ⚠ {{ t('quiz.travel_risk_warning') }}
                                                {{ currentQuestion.risk_map[trip.region].diseases.join(', ') }}
                                            </div>
                                            <label class="quiz-field-label mt-2">{{ t('quiz.travel_return_date') }}</label>
                                            <input type="date" class="quiz-input" :value="trip.return_date" :max="today"
                                                @change="e => updateTrip(i, 'return_date', e.target.value)">
                                        </div>
                                    </div>
                                    <div class="answers-stack mt-2">
                                        <button class="quiz-btn quiz-btn--outline" @click="addTrip">
                                            {{ t('quiz.travel_add') }}
                                        </button>
                                        <button v-if="travelTripsValid" class="quiz-btn quiz-btn--red" @click="confirmTravel">
                                            {{ t('quiz.travel_confirm') }}
                                        </button>
                                        <button v-for="opt in currentQuestion.options" :key="opt.id"
                                            class="quiz-btn quiz-btn--dark"
                                            @click="selectAnswer(opt.id)">
                                            {{ opt.label }}
                                        </button>
                                    </div>
                                    <p class="choose-hint-text">{{ t('quiz.choose_hint') }}</p>
                                </template>


                            </div><!-- /doc-right -->
                        </div><!-- /document-body -->
                    </div><!-- /document-card -->
                </div><!-- /document-wrapper -->

                <!-- ── COMPLETE screen ──────────────────────────────────── -->
                <div v-else-if="isComplete" key="complete" class="document-wrapper w-full max-w-5xl">

                    <!-- Document tab (desktop) -->
                    <div class="hidden lg:flex document-tab justify-end">
                        <span class="doc-tab-inner">N° {{ dossierCode }}</span>
                    </div>

                    <div class="document-card">

                        <!-- Tape (mobile only) -->
                        <div class="lg:hidden flex justify-center pt-1 pb-2">
                            <div class="tape-strip"></div>
                        </div>

                        <!-- Document header (desktop only) -->
                        <div class="hidden lg:block document-card-header">
                            DOSSIER {{ dossierCode }}
                        </div>

                        <!-- Two-panel layout — mirrors the question card -->
                        <div class="document-body">

                            <!-- LEFT: Confirmation visuelle -->
                            <div class="doc-left">
                                <div class="classified-stamp classified-stamp--complete">
                                    <span class="classified-title">Dossier complet</span>
                                    <span class="classified-sub">{{ dossierCode }}</span>
                                </div>

                                <p class="piece-ref">Question {{ pad(total) }}</p>
                                <div class="panel-divider"></div>

                                <p class="category-label">{{ dossierCode }}</p>

                                <h2 class="question-text">{{ t('quiz.complete_title') }}</h2>

                                <!-- Fingerprint decorative -->
                                <div class="fingerprint" aria-hidden="true">
                                    <svg viewBox="0 0 80 96" fill="none" stroke="currentColor" stroke-width="1" opacity="0.12">
                                        <path d="M40 8C22 8 8 22 8 40c0 8 3 16 8 22"/>
                                        <path d="M40 14C25 14 14 25 14 40c0 7 2.5 13 6.5 18"/>
                                        <path d="M40 20C28 20 20 28 20 40c0 6 2 11 5 15.5"/>
                                        <path d="M40 26C31 26 26 31 26 40c0 4.5 1.5 8.5 4 11.5"/>
                                        <path d="M40 32C34 32 32 34 32 40c0 3 1 5.5 2.5 7.5"/>
                                        <path d="M40 38c-1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2"/>
                                        <path d="M40 8c18 0 32 14 32 32 0 8-3 16-8 22"/>
                                        <path d="M40 14c15 0 26 11 26 26 0 7-2.5 13-6.5 18"/>
                                        <path d="M40 20c12 0 20 8 20 20 0 6-2 11-5 15.5"/>
                                        <path d="M40 26c9 0 14 5 14 14 0 4.5-1.5 8.5-4 11.5"/>
                                        <path d="M40 32c6 0 8 2 8 8 0 3-1 5.5-2.5 7.5"/>
                                        <path d="M20 62c4 8 12 14 20 16M60 62c-4 8-12 14-20 16"/>
                                        <path d="M14 50c2 12 14 26 26 28M66 50c-2 12-14 26-26 28"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- RIGHT: Subtitle + CTA -->
                            <div class="doc-right">
                                <p class="your-answer-label">{{ t('quiz.complete_cta_label') }}</p>
                                <p class="hint-text mb-6">{{ t('quiz.complete_subtitle') }}</p>

                                <form :action="`/c/${entreprise.slug}/quiz`" method="POST" ref="formRef">
                                    <input type="hidden" name="_token" :value="csrfToken">
                                    <template v-for="(value, key) in answers" :key="key">
                                        <template v-if="Array.isArray(value)">
                                            <input v-for="item in value" :key="item" type="hidden" :name="`answers[${key}][]`" :value="item">
                                        </template>
                                        <template v-else-if="value !== null && typeof value === 'object'">
                                            <input type="hidden" :name="`answers[${key}]`" :value="JSON.stringify(value)">
                                        </template>
                                        <template v-else>
                                            <input type="hidden" :name="`answers[${key}]`" :value="value">
                                        </template>
                                    </template>
                                    <BaseButton variant="primary" :full="true" type="submit">
                                        {{ t('quiz.submit') }}
                                    </BaseButton>
                                </form>

                            </div>

                        </div><!-- /document-body -->
                    </div><!-- /document-card -->
                </div><!-- /document-wrapper -->

            </Transition>
        </main>


    </div><!-- /quiz-screen -->

</div><!-- /quiz-root -->
</template>

<style scoped>
/* ═══════════════════════════════════════════════════════════════════
   ROOT
═══════════════════════════════════════════════════════════════════ */
.quiz-root {
    min-height: 100vh;
    background-color: #0C0C0C;
    color: #fff;
    font-family: 'Cooper Hewitt', ui-sans-serif, system-ui, sans-serif;
    position: relative;
}
/* Film grain overlay on dark backgrounds */
.quiz-root::after {
    content: '';
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 9999;
    opacity: 0.022;
    background-image: url("data:image/svg+xml;utf8,<svg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/></filter><rect width='100%' height='100%' filter='url(%23n)'/></svg>");
    background-size: 200px;
}

/* ═══════════════════════════════════════════════════════════════════
   KEYFRAMES
═══════════════════════════════════════════════════════════════════ */
@keyframes quiz-rise {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes badge-rise {
    from { opacity: 0; transform: translateY(14px) rotate(-10deg); }
    to   { opacity: 1; transform: translateY(0) rotate(-10deg); }
}
@keyframes folder-arrive {
    from { opacity: 0; transform: translateY(-28px) scale(0.93); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes tape-draw {
    from { transform: scaleX(0); }
    to   { transform: scaleX(1); }
}

/* ═══════════════════════════════════════════════════════════════════
   TRANSITIONS
═══════════════════════════════════════════════════════════════════ */
.q-fade-enter-active { transition: opacity 300ms ease; }
.q-fade-leave-active { transition: opacity 200ms ease; }
.q-fade-enter-from, .q-fade-leave-to { opacity: 0; }

.q-slide-enter-active { transition: opacity 260ms cubic-bezier(0.23,1,0.32,1), transform 260ms cubic-bezier(0.23,1,0.32,1); }
.q-slide-leave-active { transition: opacity 160ms ease, transform 160ms ease; }
.q-slide-enter-from   { opacity: 0; transform: translateY(12px) scale(0.99); }
.q-slide-leave-to     { opacity: 0; transform: translateY(-8px) scale(1.005); }

/* ═══════════════════════════════════════════════════════════════════
   INTRO — close button
═══════════════════════════════════════════════════════════════════ */
.quiz-intro {
    position: relative;
}
.intro-close-btn {
    position: absolute;
    top: 1rem;
    left: 1rem;
    z-index: 20;
    width: 36px;
    height: 36px;
    border: none;
    background: transparent;
    color: rgba(255,255,255,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: color 150ms;
}
.intro-close-btn:hover { color: white; }

/* ═══════════════════════════════════════════════════════════════════
   INTRO — grid layout
═══════════════════════════════════════════════════════════════════ */
.intro-grid {
    display: grid;
    grid-template-columns: 1fr;
    min-height: 100svh;
}
@media (min-width: 1024px) {
    .intro-grid { grid-template-columns: 56fr 44fr; }
}

/* ── Bureau narrative panel ────────────────────────────────────── */
.intro-bureau {
    background: #0D0D0D;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 5rem 1.75rem 3.5rem;
    order: 1;
    min-height: 100svh;
}
@media (min-width: 1024px) {
    .intro-bureau {
        padding: 5rem 5rem 4rem 5rem;
        min-height: auto;
        justify-content: space-between;
    }
}

/* ── Dossier folder panel (desktop only) ───────────────────────── */
.intro-dossier {
    display: none;
    background: #0D0D0D;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
    padding: 4rem 3rem 4rem;
    order: 2;
}
@media (min-width: 1024px) {
    .intro-dossier { display: flex; }
}

/* ── Dossier folder card ────────────────────────────────────────── */
.dossier-folder {
    width: 100%;
    max-width: 400px;
    background: #C9A96E;
    border-radius: 8px;
    padding: 20px 18px 24px;
    box-shadow: 0 40px 80px rgba(0,0,0,0.75);
    animation: folder-arrive 620ms cubic-bezier(0.23,1,0.32,1) both;
    position: relative;
}
.dossier-folder-inner {
    background: #F0E8D0;
    border-radius: 4px;
    padding: 18px 16px;
    position: relative;
}
.dossier-republic {
    font-size: 8px;
    font-weight: 700;
    color: #5A4A30;
    letter-spacing: 0.04em;
    padding-bottom: 8px;
    border-bottom: 1px solid #C4B080;
    margin-bottom: 4px;
}
.dossier-hospital {
    font-size: 8px;
    color: #7A6A50;
    letter-spacing: 0.03em;
    line-height: 1.6;
    margin-bottom: 14px;
}
.dossier-confidential-badge {
    position: absolute;
    top: 14px;
    right: 14px;
    border: 2px solid #D32C37;
    color: #D32C37;
    font-size: 10px;
    font-weight: 900;
    letter-spacing: 0.12em;
    padding: 3px 8px;
    transform: rotate(5deg);
    text-transform: uppercase;
    line-height: 1;
}
.dossier-logo-stamp {
    border: 1.5px solid #C4A674;
    background: white;
    padding: 16px 12px;
    margin: 0 0 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 72px;
}
.dossier-logo-img {
    max-height: 48px;
    max-width: 200px;
    object-fit: contain;
}
.dossier-logo-text {
    font-size: 15px;
    font-weight: 800;
    color: #1A1A1A;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.dossier-folder-title {
    font-size: 1.6rem;
    font-weight: 900;
    color: #1A1A1A;
    text-transform: uppercase;
    line-height: 1.1;
    letter-spacing: 0.02em;
    margin-bottom: 10px;
}
.dossier-folder-meta {
    font-size: 8px;
    color: #7A6A50;
    letter-spacing: 0.03em;
    line-height: 2;
    border-top: 1px solid #C4B080;
    padding-top: 8px;
}

/* ═══════════════════════════════════════════════════════════════════
   INTRO — common elements
═══════════════════════════════════════════════════════════════════ */
.accès-badge {
    display: inline-block;
    align-self: flex-start;
    border: 1.5px solid #D32C37;
    color: #D32C37;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.18em;
    padding: 4px 12px;
    margin-bottom: 1.5rem;
    text-transform: uppercase;
    transform: rotate(-10deg);
}

/* ── Intro entry stagger ───────────────────────────────────────── */
.accès-badge     { animation: badge-rise 420ms cubic-bezier(0.23,1,0.32,1) both 120ms; }
.intro-title     { animation: quiz-rise 520ms cubic-bezier(0.23,1,0.32,1) both 180ms; }
.intro-dept      { animation: quiz-rise 380ms cubic-bezier(0.23,1,0.32,1) both 260ms; }
.intro-rule      { animation: quiz-rise 280ms cubic-bezier(0.23,1,0.32,1) both 320ms; }
.intro-body      { animation: quiz-rise 400ms cubic-bezier(0.23,1,0.32,1) both 380ms; }
.intro-body--gap { animation-delay: 440ms; }
.intro-accent    { animation: quiz-rise 400ms cubic-bezier(0.23,1,0.32,1) both 520ms; }
.intro-cta-wrap  { animation: quiz-rise 480ms cubic-bezier(0.23,1,0.32,1) both 620ms; }


/* ── Bureau narrative content ──────────────────────────────────── */
.intro-title {
    font-size: clamp(1.9rem, 3.5vw, 3.25rem);
    font-weight: 900;
    line-height: 1.05;
    letter-spacing: -0.02em;
    margin-bottom: 0.5rem;
    white-space: nowrap;
}
.intro-body {
    font-size: clamp(0.88rem, 1.4vw, 1rem);
    color: rgba(255,255,255,0.6);
    line-height: 1.7;
    max-width: 46ch;
}
.intro-body--gap { margin-top: 0.75rem; }
.intro-accent {
    font-size: clamp(1rem, 1.5vw, 1.1rem);
    font-weight: 800;
    color: #D32C37;
    margin-top: 1.75rem;
    max-width: 36ch;
}
.intro-bureau-content {
    display: flex;
    flex-direction: column;
}
.intro-cta-wrap {
    margin-top: 2.5rem;
    max-width: 360px;
}


/* ═══════════════════════════════════════════════════════════════════
   QUIZ — TOP BAR
═══════════════════════════════════════════════════════════════════ */
.quiz-topbar {
    display: flex;
    flex-direction: column;
    position: sticky;
    top: 0;
    z-index: 10;
    background: #0D0D0D;
    border-bottom: 1px solid #1A1A1A;
}
.topbar-progress {
    width: 100%;
    height: 3px;
    background: #2A2A2A;
    flex-shrink: 0;
}
.topbar-progress-fill {
    height: 100%;
    background: #D32C37;
    transition: width 400ms cubic-bezier(0.23,1,0.32,1);
}
.topbar-controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 14px;
}
.topbar-spacer {
    width: 36px;
    height: 36px;
    flex-shrink: 0;
}
.topbar-back-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 2px solid #333;
    background: transparent;
    color: rgba(255,255,255,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
    transition: border-color 150ms, color 150ms;
}
.topbar-back-btn:hover { border-color: #666; color: white; }
.topbar-pct {
    font-size: 12px;
    font-weight: 600;
    color: rgba(255,255,255,0.45);
    min-width: 36px;
    text-align: right;
    font-variant-numeric: tabular-nums;
}

/* ═══════════════════════════════════════════════════════════════════
   DOCUMENT — wrapper & tab
═══════════════════════════════════════════════════════════════════ */
.document-wrapper { position: relative; }

.document-tab {
    margin-right: 3rem;
    margin-bottom: -1px;
    position: relative;
    z-index: 1;
}
.doc-tab-inner {
    display: inline-block;
    background: #EDE4C8;
    color: #7A6A50;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.1em;
    padding: 5px 16px;
    border-radius: 4px 4px 0 0;
    border: 1px solid #C4B080;
    border-bottom: none;
}

/* ═══════════════════════════════════════════════════════════════════
   DOCUMENT — card
═══════════════════════════════════════════════════════════════════ */
.document-card {
    background:
        radial-gradient(ellipse 70% 40% at 25% 12%, rgba(200,165,90,0.1) 0%, transparent 70%),
        radial-gradient(ellipse 55% 45% at 78% 85%, rgba(175,140,75,0.07) 0%, transparent 70%),
        #EBE1C4;
    color: #1A1A1A;
    border-radius: 4px;
    box-shadow:
        0 2px 0 rgba(255,255,255,0.04) inset,
        0 40px 90px rgba(0,0,0,0.85),
        0 8px 20px rgba(0,0,0,0.4);
    overflow: hidden;
    position: relative;
}

.tape-strip {
    width: 72px;
    height: 14px;
    background: rgba(180,170,150,0.6);
    border-radius: 2px;
    display: block;
    margin-bottom: 8px;
    transform-origin: left center;
    animation: tape-draw 380ms cubic-bezier(0.23,1,0.32,1) both 80ms;
}

.document-body {
    display: grid;
    grid-template-columns: 1fr;
    min-height: 440px;
}
@media (min-width: 1024px) {
    .document-body {
        grid-template-columns: 1fr 1fr;
        border-top: 1px solid #C4B080;
    }
}

/* ─── Left panel ──────────────────────────────────────────────── */
.doc-left {
    padding: 20px 20px 24px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    position: relative;
    overflow: hidden;
}
@media (min-width: 1024px) {
    .doc-left {
        padding: 28px 28px 32px;
        border-right: 1px solid #C4B080;
    }
}

.classified-stamp {
    display: inline-flex;
    flex-direction: column;
    align-items: flex-start;
    background: #D32C37;
    padding: 5px 11px 4px;
    align-self: flex-start;
    transform: rotate(-2deg);
    box-shadow: 0 1px 3px rgba(0,0,0,0.18), inset 0 1px 0 rgba(255,255,255,0.08);
}
.classified-stamp--complete {
    background: #5A4A30;
}
.classified-title {
    font-size: 12px;
    font-weight: 900;
    color: white;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}
.classified-sub {
    font-size: 9px;
    color: rgba(255,255,255,0.65);
    letter-spacing: 0.07em;
    text-transform: uppercase;
    font-weight: 700;
}

.piece-ref {
    font-size: 11px;
    font-weight: 700;
    color: #D32C37;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}
.panel-divider {
    height: 1px;
    background: #C4B080;
}
.category-label {
    font-size: 12px;
    font-weight: 600;
    color: #9A8870;
    letter-spacing: 0.01em;
}
.question-text {
    font-size: 1.4rem;
    font-weight: 800;
    line-height: 1.25;
    color: #1A1A1A;
    letter-spacing: -0.01em;
}
@media (min-width: 1024px) {
    .question-text { font-size: 1.75rem; }
}

.hint-block { margin-top: 4px; }
.hint-label {
    font-size: 10px;
    font-weight: 800;
    color: #8A7A60;
    letter-spacing: 0.04em;
    margin-bottom: 6px;
}
.hint-text {
    font-size: 13px;
    color: #6A5840;
    line-height: 1.6;
}
.hint-list { padding-left: 0; list-style: none; }
.hint-list-item {
    font-size: 13px;
    color: #5A4A30;
    padding: 2px 0;
}
.hint-list-item::before { content: '• '; color: #D32C37; }

.fingerprint {
    position: absolute;
    bottom: -8px;
    left: 12px;
    width: 80px;
    height: 96px;
    color: #8A7A60;
    pointer-events: none;
    opacity: 0.7;
}

/* ─── Right panel ─────────────────────────────────────────────── */
.doc-right {
    padding: 20px 20px 24px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    background: #EDE4C8;
}
@media (min-width: 1024px) {
    .doc-right { padding: 28px 28px 32px; }
}

.your-answer-label {
    font-size: 11px;
    font-weight: 700;
    color: #8A7A60;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.answers-stack { display: flex; flex-direction: column; gap: 10px; }

/* ── Quiz buttons (in-document answer choices) ────────────────── */
.quiz-btn {
    display: block;
    width: 100%;
    padding: 14px 20px;
    font-size: 15px;
    font-weight: 700;
    font-family: 'Cooper Hewitt', ui-sans-serif, system-ui, sans-serif;
    text-align: center;
    border: none;
    cursor: pointer;
    border-radius: 3px;
    transition: background 120ms ease, transform 180ms cubic-bezier(0.23,1,0.32,1), box-shadow 180ms cubic-bezier(0.23,1,0.32,1), opacity 120ms ease;
}
.quiz-btn:hover { transform: translateY(-2px); }
.quiz-btn:active { transform: translateY(1px) scale(0.98); }
.quiz-btn--dark {
    background: #191919;
    color: white;
    box-shadow: 0 3px 0 #000, 0 4px 10px rgba(0,0,0,0.22);
}
.quiz-btn--dark:hover {
    background: #111;
    box-shadow: 0 4px 0 #000, 0 6px 14px rgba(0,0,0,0.28);
}
.quiz-btn--dark:active { box-shadow: 0 1px 0 #000, 0 2px 6px rgba(0,0,0,0.15); }
.quiz-btn--red {
    background: #D32C37;
    color: white;
    box-shadow: 0 3px 0 #921d24, 0 4px 10px rgba(211,44,55,0.22);
}
.quiz-btn--red:hover {
    background: #C02030;
    box-shadow: 0 4px 0 #7a1820, 0 6px 16px rgba(211,44,55,0.3);
}
.quiz-btn--red:active { box-shadow: 0 1px 0 #921d24, 0 2px 6px rgba(211,44,55,0.15); }
.quiz-btn--outline {
    background: transparent;
    color: #1A1A1A;
    border: 1.5px solid rgba(26,26,26,0.5);
    box-shadow: none;
}
.quiz-btn--outline:hover {
    background: rgba(0,0,0,0.04);
    border-color: #1A1A1A;
}
.quiz-btn--selected {
    opacity: 0.45;
    transform: none;
    box-shadow: none;
}
.quiz-btn--selected:hover { transform: none; box-shadow: none; }

/* ── Checklist ──────────────────────────────────────────────────── */
.checklist-grid { display: flex; flex-direction: column; gap: 6px; }
.check-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 11px 14px;
    border: 1.5px solid #C4B080;
    cursor: pointer;
    transition: background 120ms ease;
    border-radius: 2px;
}
.check-item:hover { background: rgba(180,155,100,0.15); }
.check-item--on { background: rgba(180,155,100,0.2); border-color: #9A7A40; }
.check-box {
    width: 18px;
    height: 18px;
    border: 2px solid #1A1A1A;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 2px;
    margin-top: 1px;
}
.check-box--on { background: #1A1A1A; color: white; }
.check-text { display: flex; flex-direction: column; gap: 2px; }
.check-label-text { font-size: 14px; font-weight: 600; color: #1A1A1A; }
.check-sublabel-text { font-size: 12px; color: #7A6A50; }

/* ── Travel ───────────────────────────────────────────────────── */
.travel-list { display: flex; flex-direction: column; gap: 12px; }
.travel-trip {
    border: 1.5px solid #C4B080;
    padding: 12px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    border-radius: 2px;
}
.trip-header { display: flex; justify-content: space-between; align-items: center; }
.trip-num { font-size: 11px; font-weight: 700; color: #7A6A50; text-transform: uppercase; letter-spacing: 0.08em; }
.trip-remove { background: none; border: none; color: #D32C37; font-size: 14px; cursor: pointer; padding: 0 4px; }
.trip-region-tag { font-size: 11px; color: #7A6A50; }
.trip-warning { font-size: 11px; color: #B8620A; background: rgba(184,98,10,0.08); padding: 6px 10px; border-radius: 2px; }
.quiz-field-label { font-size: 11px; font-weight: 600; color: #7A6A50; letter-spacing: 0.05em; }
.quiz-select, .quiz-input {
    width: 100%;
    background: rgba(255,255,255,0.4);
    border: 1.5px solid #C4B080;
    border-radius: 2px;
    padding: 8px 10px;
    font-size: 13px;
    font-family: 'Cooper Hewitt', ui-sans-serif, system-ui, sans-serif;
    color: #1A1A1A;
    outline: none;
}
.quiz-select:focus, .quiz-input:focus { border-color: #8A7A60; }



/* ═══════════════════════════════════════════════════════════════════
   MOBILE — card header (HUG decoration + stamp)
═══════════════════════════════════════════════════════════════════ */
.mobile-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 8px 16px 4px;
}

/* HUG paper decoration */
.hug-deco {
    width: 56px;
    height: 64px;
    background: #C4A674;
    border-radius: 3px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
    padding-bottom: 8px;
    position: relative;
    transform: rotate(-3deg);
    box-shadow: 1px 2px 6px rgba(0,0,0,0.2);
}
.hug-paper-lines {
    position: absolute;
    top: 6px;
    left: 5px;
    right: 5px;
    bottom: 22px;
    background: #F0E8D0;
    border-radius: 1px;
}
.hug-paper-lines::after {
    content: '';
    display: block;
    position: absolute;
    top: 5px; left: 4px; right: 4px;
    height: 2px;
    background: rgba(90,70,40,0.15);
    box-shadow: 0 5px 0 rgba(90,70,40,0.1), 0 10px 0 rgba(90,70,40,0.08);
}
.hug-logo-text {
    font-size: 11px;
    font-weight: 900;
    color: #F0E8D0;
    letter-spacing: 0.06em;
    z-index: 1;
}

/* ── Document card header (desktop) ──────────────────────────── */
.document-card-header {
    padding: 10px 28px 9px;
    font-family: 'Courier New', monospace;
    font-size: 13px;
    font-weight: 700;
    color: #8A7A60;
    letter-spacing: 0.08em;
    border-bottom: 1px solid #C4B080;
    text-transform: uppercase;
}

/* ── Company logo in doc-right ────────────────────────────────── */
.doc-logo-wrap {
    justify-content: flex-end;
    margin-bottom: 2px;
}
.doc-logo-img {
    max-height: 38px;
    max-width: 130px;
    object-fit: contain;
    border: 1px solid #C4B080;
    padding: 5px 9px;
    background: white;
    border-radius: 2px;
}

/* ── Choose hint text ─────────────────────────────────────────── */
.choose-hint-text {
    font-size: 11px;
    color: #A09080;
    font-style: italic;
    text-align: center;
    margin-top: 4px;
}

/* ═══════════════════════════════════════════════════════════════════
   REDUCED MOTION
═══════════════════════════════════════════════════════════════════ */
@media (prefers-reduced-motion: reduce) {
    .q-fade-enter-active, .q-fade-leave-active,
    .q-slide-enter-active, .q-slide-leave-active { transition: opacity 100ms ease; }
    .q-slide-enter-from, .q-slide-leave-to { transform: none; }
    .quiz-btn { transition: background 120ms ease; }
    .quiz-btn:hover, .quiz-btn:active { transform: none; box-shadow: inherit; }
    .topbar-progress-fill { transition: none; }
    .accès-badge, .intro-title, .intro-dept, .intro-rule,
    .intro-body, .intro-accent, .intro-cta-wrap, .tape-strip,
    .dossier-folder {
        animation: none;
        opacity: 1;
        transform: none;
    }
    .quiz-root::after { display: none; }
}
</style>

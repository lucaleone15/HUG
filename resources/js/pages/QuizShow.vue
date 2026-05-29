<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'
import { sendAnalytics } from '../composables/useAnalytics.js'
import { countries } from '../data/countries.js'
import { COUNTRY_REGION_MAP } from '../data/countryRegions.js'

const { t } = useI18n()

const props = defineProps({
    entreprise:    Object,
    questions:     Array,
    session_token: { type: String, default: null },
})

// ─── Phase ──────────────────────────────────────────────────────────────────
const phase = ref('intro')   // 'intro' | 'quiz'
const dossierCode = 'SANG-' + new Date().getFullYear().toString().slice(-2)

const startQuiz = () => { phase.value = 'quiz' }

// ─── Question text split (main + red accent = last ~30% of words) ───────────
const splitQ = (text) => {
    const words = text.trim().split(/\s+/)
    if (words.length <= 3) return { main: '', accent: text }
    const accentCount = Math.min(Math.max(2, Math.floor(words.length * 0.3)), 5)
    const at = words.length - accentCount
    return { main: words.slice(0, at).join(' ') + ' ', accent: words.slice(at).join(' ') }
}

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

        <!-- Mobile layout -->
        <div class="lg:hidden flex flex-col min-h-screen">

            <!-- Folder visual -->
            <div class="flex-1 flex items-center justify-center p-8">
                <div class="folder-card mx-auto">
                    <div class="folder-clip"></div>
                    <div class="folder-inner">
                        <div class="folder-confidential-stamp">CONFIDENTIEL</div>
                        <div v-if="entreprise.logo_url" class="flex justify-center mb-3">
                            <img :src="entreprise.logo_url" :alt="entreprise.name" class="h-10 object-contain">
                        </div>
                        <p class="folder-dossier-no">DOSSIER NO. {{ dossierCode }}</p>
                        <p class="folder-status">STATUT : EN ATTENTE</p>
                    </div>
                </div>
            </div>

            <!-- Bureau content -->
            <div class="bureau-panel px-6 pb-10 pt-2">
                <div class="accès-badge">{{ t('quiz.intro_badge') }}</div>
                <h1 class="bureau-title">{{ t('quiz.intro_title') }}</h1>
                <p class="bureau-dept">{{ t('quiz.intro_dept') }}</p>
                <div class="bureau-rule"></div>
                <p class="bureau-body">{{ t('quiz.intro_p1') }}</p>
                <p class="bureau-body mt-3">{{ t('quiz.intro_p2') }}</p>
                <p class="bureau-accent">{{ t('quiz.intro_accent') }}</p>
                <button class="quiz-cta-btn" @click="startQuiz">{{ t('quiz.intro_cta') }}</button>
            </div>
        </div>

        <!-- Desktop layout -->
        <div class="hidden lg:grid grid-cols-2 min-h-screen">

            <!-- Left: folder visual -->
            <div class="flex items-center justify-center bg-[#0D0D0D] p-16">
                <div class="folder-card-lg">
                    <div class="folder-clip-lg"></div>
                    <div class="folder-inner-lg">
                        <p class="folder-republic">RÉPUBLIQUE ET CANTON DE GENÈVE</p>
                        <p class="folder-hospital">HÔPITAUX UNIVERSITAIRES DE GENÈVE<br>CENTRE DE TRANSFUSION SANGUINE</p>
                        <!-- Company logo stamp -->
                        <div class="folder-stamp-box">
                            <img v-if="entreprise.logo_url" :src="entreprise.logo_url" :alt="entreprise.name" class="h-12 object-contain mx-auto">
                            <p v-else class="text-center text-sm font-bold text-gray-700">{{ entreprise.name }}</p>
                        </div>
                        <h2 class="folder-dossier-title">DOSSIER<br>{{ dossierCode }}</h2>
                        <div class="folder-confidential-stamp-lg">CONFIDENTIEL</div>
                        <div class="folder-meta">
                            <p>DATE D'OUVERTURE : __ / __ / {{ new Date().getFullYear() }}</p>
                            <p>INSPECTEUR ASSIGNÉ : CTS</p>
                            <p>STATUT : EN ATTENTE</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: bureau text -->
            <div class="bureau-panel-lg flex flex-col justify-center px-16 py-12">
                <div class="accès-badge mb-6">{{ t('quiz.intro_badge') }}</div>
                <h1 class="bureau-title-lg">{{ t('quiz.intro_title') }}</h1>
                <p class="bureau-dept-lg">{{ t('quiz.intro_dept') }}</p>
                <div class="bureau-rule-lg"></div>
                <p class="bureau-body-lg">{{ t('quiz.intro_p1') }}</p>
                <p class="bureau-body-lg mt-4">{{ t('quiz.intro_p2') }}</p>
                <p class="bureau-accent-lg">{{ t('quiz.intro_accent') }}</p>
                <button class="quiz-cta-btn mt-8" @click="startQuiz">{{ t('quiz.intro_cta') }}</button>
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
            <!-- Back button -->
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
            <div v-else class="w-9 h-9"></div>

            <!-- Piece reference (mobile) / Dossier label (desktop) -->
            <span v-if="currentQuestion" class="font-mono text-xs text-white/50 tracking-wider px-2">
                <span class="lg:hidden">{{ t('quiz.piece_label') }} #{{ pad(currentQuestion.order) }} / {{ total }}</span>
                <span class="hidden lg:inline">{{ t('quiz.dossier_label') }} {{ dossierCode }}</span>
            </span>
            <span v-else class="font-mono text-xs text-white/50 px-2 hidden lg:inline">{{ t('quiz.dossier_label') }} {{ dossierCode }}</span>
        </header>

        <!-- ── Main content ─────────────────────────────────────────────── -->
        <main class="flex-1 flex flex-col items-center px-4 py-4 lg:py-6 lg:px-8 overflow-x-hidden">

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

                        <!-- Tape (mobile only) -->
                        <div class="lg:hidden flex justify-center pt-1 pb-2">
                            <div class="tape-strip"></div>
                        </div>

                        <!-- Two-panel grid (single col on mobile, two cols on desktop) -->
                        <div class="document-body">

                            <!-- ▌LEFT PANEL — Question ▐ -->
                            <div class="doc-left">

                                <!-- Classified stamp -->
                                <div class="classified-stamp">
                                    <span class="classified-title">{{ t('quiz.classified_header') }}</span>
                                    <span class="classified-sub">{{ t('quiz.classified_sub') }} {{ dossierCode }}</span>
                                </div>

                                <!-- Piece reference -->
                                <p class="piece-ref">
                                    {{ t('quiz.piece_label') }} #{{ pad(currentQuestion.order) }}
                                    // {{ currentQuestion.category.toUpperCase() }}
                                </p>
                                <div class="panel-divider"></div>

                                <!-- Question number -->
                                <p class="question-num-label">{{ t('quiz.question_label') }} {{ pad(currentQuestion.order) }}</p>

                                <!-- Question text with red accent on last words -->
                                <h2 class="question-text">
                                    <span>{{ splitQ(currentQuestion.content).main }}</span>
                                    <span class="question-accent">{{ splitQ(currentQuestion.content).accent }}</span>
                                </h2>

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

                                <!-- Fingerprint (decorative) -->
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

                            <!-- ▌RIGHT PANEL — Answers ▐ -->
                            <div class="doc-right">

                                <!-- ── yes_no ──────────────────────────── -->
                                <template v-if="!currentQuestion.type || currentQuestion.type === 'yes_no'">
                                    <p class="your-answer-label">{{ t('quiz.your_answer') }}</p>
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
                                </template>

                                <!-- ── checklist ───────────────────────── -->
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
                                                <span class="check-label-text">{{ item.label.toUpperCase() }}</span>
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
                                </template>

                                <!-- ── birth_check ─────────────────────── -->
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
                                                <span class="check-label-text">{{ item.label.toUpperCase() }}</span>
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
                                </template>

                                <!-- ── travel_check ────────────────────── -->
                                <template v-else-if="currentQuestion.type === 'travel_check'">
                                    <div class="travel-list">
                                        <div v-for="(trip, i) in travelTrips" :key="i" class="travel-trip">
                                            <div class="trip-header">
                                                <span class="trip-num">{{ t('quiz.travel_trip') }} {{ i + 1 }}</span>
                                                <button class="trip-remove" @click="removeTrip(i)">✕</button>
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
                                </template>

                                <!-- PIÈCE À CONVICTION bottom stamp -->
                                <div class="conviction-stamp">
                                    <span>{{ t('quiz.piece_label') }}</span>
                                </div>

                            </div><!-- /doc-right -->
                        </div><!-- /document-body -->
                    </div><!-- /document-card -->
                </div><!-- /document-wrapper -->

                <!-- ── COMPLETE screen ──────────────────────────────────── -->
                <div v-else-if="isComplete" key="complete" class="document-wrapper w-full max-w-5xl">
                    <div class="document-card complete-card">
                        <div class="tape-strip mx-auto lg:hidden"></div>
                        <div class="complete-inner">
                            <div class="complete-check">
                                <svg viewBox="0 0 24 24" fill="none" class="w-10 h-10">
                                    <circle cx="12" cy="12" r="11" fill="#D32C37" opacity="0.12"/>
                                    <path d="M7 12.5l3.5 3.5 6.5-7" stroke="#D32C37" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <p class="piece-ref mb-2">{{ t('quiz.piece_label') }} #{{ pad(total) }} // FIN D'INTERROGATOIRE</p>
                            <h2 class="question-text mb-2">
                                <span>{{ splitQ(t('quiz.complete_title')).main }}</span>
                                <span class="question-accent">{{ splitQ(t('quiz.complete_title')).accent }}</span>
                            </h2>
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
                                <button type="submit" class="quiz-btn quiz-btn--red">
                                    {{ t('quiz.submit').toUpperCase() }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </Transition>
        </main>
        <!-- ── Barre de progression (bas) ─────────────────────────────── -->
        <footer class="quiz-bottom-bar">
            <div class="quiz-progress-track">
                <div class="quiz-progress-fill" :style="`width: ${progress}%`"></div>
            </div>
            <span class="quiz-progress-pct">{{ progress }}%</span>
        </footer>

    </div><!-- /quiz-screen -->

</div><!-- /quiz-root -->
</template>

<style scoped>
/* ═══════════════════════════════════════════════════════════════════
   ROOT
═══════════════════════════════════════════════════════════════════ */
.quiz-root {
    min-height: 100vh;
    background-color: #0D0D0D;
    color: #fff;
    font-family: inherit;
}

/* ═══════════════════════════════════════════════════════════════════
   TRANSITIONS
═══════════════════════════════════════════════════════════════════ */
.q-fade-enter-active { transition: opacity 300ms ease; }
.q-fade-leave-active { transition: opacity 200ms ease; }
.q-fade-enter-from, .q-fade-leave-to { opacity: 0; }

.q-slide-enter-active { transition: opacity 220ms cubic-bezier(0.23,1,0.32,1), transform 220ms cubic-bezier(0.23,1,0.32,1); }
.q-slide-leave-active { transition: opacity 140ms ease, transform 140ms ease; }
.q-slide-enter-from   { opacity: 0; transform: translateY(10px); }
.q-slide-leave-to     { opacity: 0; transform: translateY(-6px); }

/* ═══════════════════════════════════════════════════════════════════
   INTRO — common
═══════════════════════════════════════════════════════════════════ */
.accès-badge {
    display: inline-block;
    border: 2px solid #D32C37;
    color: #D32C37;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.2em;
    padding: 5px 14px;
    margin-bottom: 1rem;
    text-transform: uppercase;
}

.quiz-cta-btn {
    display: block;
    width: 100%;
    background: #D32C37;
    color: white;
    font-size: 16px;
    font-weight: 700;
    padding: 1rem 1.5rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 150ms ease, transform 140ms cubic-bezier(0.23,1,0.32,1);
    margin-top: 2rem;
}
.quiz-cta-btn:hover { background: #A9232C; }
.quiz-cta-btn:active { transform: scale(0.97); }

/* ── Folder visual ─────────────────────────────────────────────── */
.folder-card {
    width: 220px;
    background: #C4A674;
    border-radius: 8px;
    padding: 20px 16px 24px;
    position: relative;
    box-shadow: 0 20px 60px rgba(0,0,0,0.6);
}
.folder-clip {
    width: 36px;
    height: 20px;
    background: #888;
    border-radius: 4px 4px 0 0;
    margin: -20px auto 12px;
    position: relative;
}
.folder-clip::after {
    content: '';
    position: absolute;
    inset: 3px 6px;
    background: #0D0D0D;
    border-radius: 2px;
}
.folder-inner {
    background: #EDE4C8;
    border-radius: 4px;
    padding: 12px;
    text-align: center;
}
.folder-confidential-stamp {
    display: inline-block;
    border: 2px solid #D32C37;
    color: #D32C37;
    font-size: 10px;
    font-weight: 900;
    letter-spacing: 0.15em;
    padding: 3px 8px;
    margin-bottom: 8px;
    transform: rotate(-4deg);
}
.folder-dossier-no {
    font-size: 9px;
    font-weight: 700;
    color: #5A4A30;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    margin-top: 8px;
}
.folder-status {
    font-size: 8px;
    color: #8A7A60;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    margin-top: 2px;
}

/* ── Bureau text (mobile) ──────────────────────────────────────── */
.bureau-panel {
    background: #0D0D0D;
    border-top: 1px solid #222;
}
.bureau-title {
    font-size: 2rem;
    font-weight: 900;
    line-height: 1.1;
    text-transform: uppercase;
    letter-spacing: -0.01em;
    margin-bottom: 0.5rem;
}
.bureau-dept {
    font-size: 11px;
    color: rgba(255,255,255,0.45);
    letter-spacing: 0.15em;
    text-transform: uppercase;
    font-family: monospace;
    margin-bottom: 1rem;
}
.bureau-rule {
    height: 1px;
    background: #333;
    margin: 1rem 0;
}
.bureau-body { font-size: 15px; color: rgba(255,255,255,0.72); line-height: 1.6; }
.bureau-accent {
    font-size: 17px;
    font-weight: 700;
    color: #D32C37;
    margin-top: 1.25rem;
    font-style: italic;
}

/* ── Bureau text (desktop) ─────────────────────────────────────── */
.bureau-panel-lg { background: #111; }
.bureau-title-lg {
    font-size: 3.5rem;
    font-weight: 900;
    text-transform: uppercase;
    line-height: 1.05;
    letter-spacing: -0.02em;
    margin-bottom: 0.5rem;
}
.bureau-dept-lg {
    font-size: 12px;
    color: rgba(255,255,255,0.4);
    letter-spacing: 0.18em;
    text-transform: uppercase;
    font-family: monospace;
    margin-bottom: 1.25rem;
}
.bureau-rule-lg { height: 1px; background: #333; margin: 1.25rem 0; }
.bureau-body-lg { font-size: 16px; color: rgba(255,255,255,0.7); line-height: 1.7; }
.bureau-accent-lg {
    font-size: 20px;
    font-weight: 700;
    color: #D32C37;
    margin-top: 1.5rem;
    font-style: italic;
}

/* ── Desktop folder (large) ────────────────────────────────────── */
.folder-card-lg {
    width: 100%;
    max-width: 480px;
    background: #C4A674;
    border-radius: 10px;
    padding: 28px 24px 32px;
    box-shadow: 0 40px 80px rgba(0,0,0,0.7);
    position: relative;
}
.folder-clip-lg {
    width: 48px;
    height: 26px;
    background: #8A8A8A;
    border-radius: 5px 5px 0 0;
    margin: -28px auto 16px;
    position: relative;
}
.folder-clip-lg::after {
    content: '';
    position: absolute;
    inset: 4px 8px;
    background: #C4A674;
    border-radius: 3px;
}
.folder-inner-lg {
    background: #F0E8D0;
    border-radius: 4px;
    padding: 20px;
}
.folder-republic {
    font-size: 9px;
    font-family: monospace;
    color: #6A5A40;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    margin-bottom: 4px;
}
.folder-hospital {
    font-size: 9px;
    font-family: monospace;
    color: #6A5A40;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    margin-bottom: 16px;
    line-height: 1.5;
}
.folder-stamp-box {
    border: 1.5px solid #C4A674;
    padding: 12px;
    margin-bottom: 16px;
    background: white;
}
.folder-dossier-title {
    font-family: monospace;
    font-size: 2rem;
    font-weight: 900;
    color: #1A1A1A;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    margin-bottom: 12px;
    line-height: 1.1;
}
.folder-confidential-stamp-lg {
    display: inline-block;
    border: 3px solid #CC1B27;
    color: #CC1B27;
    font-size: 14px;
    font-weight: 900;
    letter-spacing: 0.15em;
    padding: 6px 14px;
    transform: rotate(-8deg);
    margin: 8px 0 16px;
}
.folder-meta {
    font-family: monospace;
    font-size: 9px;
    color: #7A6A50;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    line-height: 1.8;
    border-top: 1px solid #C4A874;
    padding-top: 10px;
    margin-top: 8px;
}

/* ═══════════════════════════════════════════════════════════════════
   QUIZ — TOP BAR
═══════════════════════════════════════════════════════════════════ */
.quiz-topbar {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    position: sticky;
    top: 0;
    z-index: 10;
    background: #0D0D0D;
    border-bottom: 1px solid #1A1A1A;
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

/* ── Bottom progress bar ──────────────────────────────────────── */
.quiz-bottom-bar {
    position: sticky;
    bottom: 0;
    z-index: 10;
    background: #0D0D0D;
    border-top: 1px solid #1A1A1A;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.quiz-progress-track {
    flex: 1;
    height: 4px;
    background: #2A2A2A;
    border-radius: 2px;
    overflow: hidden;
}
.quiz-progress-fill {
    height: 100%;
    background: #D32C37;
    border-radius: 2px;
    transition: width 400ms cubic-bezier(0.23,1,0.32,1);
}
.quiz-progress-pct {
    font-size: 12px;
    font-weight: 600;
    color: rgba(255,255,255,0.45);
    min-width: 36px;
    text-align: right;
    font-variant-numeric: tabular-nums;
}

/* ═══════════════════════════════════════════════════════════════════
   QUIZ — DOSSIER LABEL
═══════════════════════════════════════════════════════════════════ */
.dossier-label-text {
    font-family: monospace;
    font-size: 13px;
    font-weight: 700;
    color: rgba(255,255,255,0.35);
    letter-spacing: 0.12em;
    text-transform: uppercase;
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
    font-family: monospace;
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
    background: #EDE4C8;
    color: #1A1A1A;
    border-radius: 4px;
    box-shadow: 0 30px 80px rgba(0,0,0,0.8);
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
    border: 2px solid #D32C37;
    padding: 6px 12px;
    align-self: flex-start;
}
.classified-title {
    font-size: 12px;
    font-weight: 900;
    color: #D32C37;
    letter-spacing: 0.15em;
    text-transform: uppercase;
}
.classified-sub {
    font-size: 9px;
    color: #D32C37;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    font-weight: 600;
}

.piece-ref {
    font-family: monospace;
    font-size: 11px;
    font-weight: 700;
    color: #D32C37;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}
.panel-divider {
    height: 1px;
    background: #C4B080;
}
.question-num-label {
    font-family: monospace;
    font-size: 11px;
    font-weight: 600;
    color: #8A7A60;
    letter-spacing: 0.1em;
    text-transform: uppercase;
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
.question-accent { color: #D32C37; }

.hint-block { margin-top: 4px; }
.hint-label {
    font-family: monospace;
    font-size: 10px;
    font-weight: 700;
    color: #8A7A60;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    margin-bottom: 6px;
}
.hint-text {
    font-size: 13px;
    color: #5A4A30;
    line-height: 1.55;
    font-style: italic;
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
    font-family: monospace;
    font-size: 11px;
    font-weight: 700;
    color: #8A7A60;
    letter-spacing: 0.15em;
    text-transform: uppercase;
}

.answers-stack { display: flex; flex-direction: column; gap: 10px; }

.choose-hint {
    font-family: monospace;
    font-size: 9px;
    color: #A09070;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    text-align: center;
    margin-top: 4px;
}

/* ── Quiz buttons ─────────────────────────────────────────────── */
.quiz-btn {
    display: block;
    width: 100%;
    padding: 14px 20px;
    font-size: 15px;
    font-weight: 700;
    text-align: center;
    border: none;
    cursor: pointer;
    border-radius: 8px;
    transition: background 140ms ease, opacity 120ms ease, transform 140ms cubic-bezier(0.23,1,0.32,1);
}
.quiz-btn:active { transform: scale(0.97); }
.quiz-btn--dark { background: #1A1A1A; color: white; }
.quiz-btn--dark:hover { background: #000; }
.quiz-btn--red { background: #D32C37; color: white; }
.quiz-btn--red:hover { background: #A9232C; }
.quiz-btn--outline {
    background: transparent;
    color: #1A1A1A;
    border: 2px solid #1A1A1A;
}
.quiz-btn--outline:hover { background: rgba(0,0,0,0.05); }
.quiz-btn--selected { opacity: 0.6; }

/* ── Checklist ──────────────────────────────────────────────────── */
.checklist-grid { display: flex; flex-direction: column; gap: 6px; }
.check-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 12px 14px;
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
.check-label-text { font-size: 12px; font-weight: 700; color: #1A1A1A; letter-spacing: 0.04em; }
.check-sublabel-text { font-size: 11px; color: #7A6A50; }

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
.quiz-field-label { font-size: 11px; font-weight: 600; color: #7A6A50; text-transform: uppercase; letter-spacing: 0.08em; }
.quiz-select, .quiz-input {
    width: 100%;
    background: rgba(255,255,255,0.4);
    border: 1.5px solid #C4B080;
    border-radius: 2px;
    padding: 8px 10px;
    font-size: 13px;
    color: #1A1A1A;
    outline: none;
}
.quiz-select:focus, .quiz-input:focus { border-color: #8A7A60; }

/* ── Conviction stamp ─────────────────────────────────────────── */
.conviction-stamp {
    align-self: flex-end;
    margin-top: auto;
    border: 1.5px solid #D32C37;
    color: #D32C37;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 0.12em;
    padding: 4px 10px;
    text-transform: uppercase;
    font-family: monospace;
    text-align: center;
}

/* ── Complete card ────────────────────────────────────────────── */
.complete-card { padding: 32px 28px; }
.complete-inner { display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0; }
.complete-check { margin-bottom: 16px; }

/* ═══════════════════════════════════════════════════════════════════
   REDUCED MOTION
═══════════════════════════════════════════════════════════════════ */
@media (prefers-reduced-motion: reduce) {
    .q-fade-enter-active, .q-fade-leave-active,
    .q-slide-enter-active, .q-slide-leave-active,
    .quiz-btn, .quiz-progress-fill { transition: none; }
}
</style>

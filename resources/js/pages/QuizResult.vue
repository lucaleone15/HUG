<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { sendAnalytics } from '../composables/useAnalytics.js'
import BaseButton from '../components/ui/BaseButton.vue'

const { t } = useI18n()

const props = defineProps({
    entreprise: Object,
    submission: Object,
})

const eligible        = props.submission?.is_eligible ?? false
const reasons         = props.submission?.disqualification_reasons ?? []
const needsEvaluation = props.submission?.needs_evaluation ?? false
const copied          = ref(false)
const messageCopied   = ref(false)
const shareUrl        = `${window.location.origin}/c/${props.entreprise.slug}`
const dossierCode     = 'SANG-' + new Date().getFullYear().toString().slice(-2)

const formattedDate = computed(() => {
    if (!props.entreprise.rdv_date) return null
    return new Date(props.entreprise.rdv_date).toLocaleDateString('fr-CH', {
        day: 'numeric', month: 'long', year: 'numeric',
    })
})

const onRdvClick = () => sendAnalytics('rdv_clicked', props.entreprise.id, null, {})

const copyLink = async () => {
    await navigator.clipboard.writeText(shareUrl)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2000)
}

const copyEmailMessage = async () => {
    const text = t('result.referral_email_body', { company: props.entreprise.name, url: shareUrl })
    await navigator.clipboard.writeText(text)
    messageCopied.value = true
    setTimeout(() => { messageCopied.value = false }, 2000)
}

const whatsappHref = computed(() => {
    const text = encodeURIComponent(t('result.referral_email_body', { company: props.entreprise.name, url: shareUrl }))
    return `https://wa.me/?text=${text}`
})
</script>

<template>
<div class="result-root">

    <!-- ── Top bar (progress at 100%) ──────────────────────────────────────── -->
    <header class="result-topbar">
        <div class="topbar-progress" aria-hidden="true">
            <div class="topbar-progress-fill"></div>
        </div>
        <div class="topbar-controls">
            <a href="/" class="topbar-back-btn" :aria-label="t('result.back_home')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <span class="topbar-pct">100%</span>
        </div>
    </header>

    <!-- ── Main ──────────────────────────────────────────────────────────────── -->
    <main class="result-main">

        <!-- Dossier Agent card -->
        <div class="dossier-wrapper">

            <!-- Tab desktop -->
            <div class="hidden lg:flex justify-end pr-14 mb-[-1px] relative z-10">
                <span class="doc-tab-inner">N° {{ dossierCode }}</span>
            </div>

            <div class="dossier-card">

                <!-- Tape mobile -->
                <div class="lg:hidden flex justify-center pt-2 pb-1">
                    <div class="tape-strip"></div>
                </div>

                <!-- Card header -->
                <div class="dossier-header">
                    <h1 class="dossier-title">Dossier Agent</h1>
                </div>
                <div class="dossier-rule"></div>

                <!-- Body: narrative left, portrait right -->
                <div class="dossier-body">

                    <!-- LEFT: narrative text + reasons + action inside dossier -->
                    <div class="dossier-narrative">
                        <template v-if="eligible">
                            <p class="narrative-text">{{ t('result.eligible_narrative') }}</p>
                            <!-- RDV button inside dossier -->
                            <div v-if="entreprise.rdv_url" class="dossier-action-block">
                                <p v-if="formattedDate" class="dossier-action-date">
                                    {{ t('entreprise.collect_date') }} : <strong>{{ formattedDate }}</strong>
                                </p>
                                <a :href="entreprise.rdv_url" target="_blank" rel="noopener noreferrer"
                                   class="dossier-action-btn dossier-action-btn--rdv"
                                   @click="onRdvClick">
                                    {{ t('result.rdv_cta') }}
                                </a>
                            </div>
                        </template>
                        <template v-else>
                            <p class="narrative-text">{{ t('result.ineligible_narrative_p1') }}</p>
                            <div v-if="reasons.length > 0" class="dossier-reasons">
                                <p class="dossier-reasons-title">{{ t('result.ineligible_reasons_title') }} :</p>
                                <ul class="dossier-reasons-list">
                                    <li v-for="(r, i) in reasons" :key="i" class="dossier-reason-item">
                                        <span class="reason-bullet">•</span>
                                        <span>{{ r }}</span>
                                    </li>
                                </ul>
                                <!-- Contact link inside dossier -->
                                <div class="dossier-action-block">
                                    <a href="/contact" class="dossier-action-btn dossier-action-btn--contact">
                                        {{ t('result.contact_box_cta') }}
                                    </a>
                                </div>
                            </div>
                            <p v-if="needsEvaluation" class="narrative-text narrative-text--gap narrative-text--info">
                                {{ t('result.needs_evaluation_message') }}
                            </p>
                            <p class="narrative-text narrative-text--gap">{{ t('result.ineligible_narrative_p2') }}</p>
                        </template>
                    </div>

                    <!-- RIGHT: portrait + fingerprints -->
                    <div class="dossier-right-col">

                        <!-- Photo frame -->
                        <div class="photo-frame">
                            <div class="photo-tape"></div>
                            <svg viewBox="0 0 140 165" xmlns="http://www.w3.org/2000/svg" class="w-full block">
                                <rect width="140" height="165" fill="#EDE8D4"/>
                                <!-- body / shoulders -->
                                <path d="M8 148 L22 110 Q70 100 118 110 L132 148 L132 165 L8 165 Z" fill="#1A1A1A"/>
                                <!-- neck -->
                                <rect x="62" y="102" width="16" height="12" fill="#1A1A1A" rx="1"/>
                                <!-- head -->
                                <circle cx="70" cy="68" r="34" fill="#1A1A1A"/>
                            </svg>
                        </div>

                        <!-- Fingerprints -->
                        <div class="fp-box">
                            <p class="fp-title">Empreintes digitales</p>
                            <div class="fp-pair">
                                <div class="fp-item">
                                    <svg viewBox="0 0 56 64" fill="none" stroke="#2A1A0A" stroke-width="0.9" class="fp-svg">
                                        <path d="M28 4C14 4 4 14 4 28c0 6 2 11 5 15"/>
                                        <path d="M28 8C16 8 8 16 8 28c0 5 1.5 9 4 12.5"/>
                                        <path d="M28 12C18 12 12 18 12 28c0 4 1 7 3 10"/>
                                        <path d="M28 16C20 16 16 20 16 28c0 3 0.8 5.5 2 7.5"/>
                                        <path d="M28 20C22 20 20 22 20 28c0 2 0.5 3.5 1.5 5"/>
                                        <circle cx="28" cy="28" r="1.8" fill="#2A1A0A"/>
                                        <path d="M28 4C42 4 52 14 52 28c0 6-2 11-5 15"/>
                                        <path d="M28 8C40 8 48 16 48 28c0 5-1.5 9-4 12.5"/>
                                        <path d="M28 12C38 12 44 18 44 28c0 4-1 7-3 10"/>
                                        <path d="M28 16C36 16 40 20 40 28c0 3-0.8 5.5-2 7.5"/>
                                        <path d="M28 20C34 20 36 22 36 28c0 2-0.5 3.5-1.5 5"/>
                                        <path d="M9 43c2 8 10 16 19 17M47 43c-2 8-10 16-19 17"/>
                                        <path d="M4 35c1 10 11 22 24 24M52 35c-1 10-11 22-24 24"/>
                                    </svg>
                                    <p class="fp-label">Index gauche</p>
                                </div>
                                <div class="fp-item">
                                    <svg viewBox="0 0 56 64" fill="none" stroke="#2A1A0A" stroke-width="0.9" class="fp-svg">
                                        <path d="M28 4C14 4 4 14 4 28c0 6 2 11 5 15"/>
                                        <path d="M28 8C16 8 8 16 8 28c0 5 1.5 9 4 12.5"/>
                                        <path d="M28 12C18 12 12 18 12 28c0 4 1 7 3 10"/>
                                        <path d="M28 16C20 16 16 20 16 28c0 3 0.8 5.5 2 7.5"/>
                                        <path d="M28 20C22 20 20 22 20 28c0 2 0.5 3.5 1.5 5"/>
                                        <circle cx="28" cy="28" r="1.8" fill="#2A1A0A"/>
                                        <path d="M28 4C42 4 52 14 52 28c0 6-2 11-5 15"/>
                                        <path d="M28 8C40 8 48 16 48 28c0 5-1.5 9-4 12.5"/>
                                        <path d="M28 12C38 12 44 18 44 28c0 4-1 7-3 10"/>
                                        <path d="M28 16C36 16 40 20 40 28c0 3-0.8 5.5-2 7.5"/>
                                        <path d="M28 20C34 20 36 22 36 28c0 2-0.5 3.5-1.5 5"/>
                                        <path d="M9 43c2 8 10 16 19 17M47 43c-2 8-10 16-19 17"/>
                                        <path d="M4 35c1 10 11 22 24 24M52 35c-1 10-11 22-24 24"/>
                                    </svg>
                                    <p class="fp-label">Index droite</p>
                                </div>
                            </div>
                        </div>

                    </div><!-- /dossier-right-col -->
                </div><!-- /dossier-body -->

                <!-- Eligibility stamp (diagonal, absolute) -->
                <div :class="['agent-stamp', eligible ? 'agent-stamp--eligible' : 'agent-stamp--ineligible']">
                    {{ eligible ? t('result.stamp_eligible') : t('result.stamp_ineligible') }}
                </div>

            </div><!-- /dossier-card -->
        </div><!-- /dossier-wrapper -->

        <!-- ── CTA section ─────────────────────────────────────────────────── -->
        <div class="result-cta-section">

            <template v-if="!eligible">
                <div class="share-row">
                    <button class="share-action-btn" @click="copyLink">
                        {{ copied ? t('result.referral_copied') : t('result.referral_colleague') }}
                    </button>
                    <button class="share-action-btn" @click="copyEmailMessage">
                        {{ messageCopied ? t('result.referral_message_copied') : t('result.share_mail') }}
                    </button>
                    <a :href="whatsappHref" target="_blank" rel="noopener noreferrer" class="share-action-btn">
                        {{ t('result.referral_whatsapp') }}
                    </a>
                </div>
            </template>

        </div>

    </main>

</div>
</template>

<style scoped>
/* ═══════════════════════════════════════════════════════════════════
   KEYFRAMES
═══════════════════════════════════════════════════════════════════ */
@keyframes dossier-enter {
    from { opacity: 0; transform: translateY(28px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes stamp-land {
    0%   { opacity: 0; transform: translate(-50%, -50%) rotate(-18deg) scale(1.2); }
    55%  { opacity: 1; transform: translate(-50%, -50%) rotate(-11deg) scale(0.96); }
    75%  { transform: translate(-50%, -50%) rotate(-14deg) scale(1.01); }
    100% { opacity: 0.92; transform: translate(-50%, -50%) rotate(-13deg) scale(1); }
}
@keyframes cta-enter {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ═══════════════════════════════════════════════════════════════════
   ROOT
═══════════════════════════════════════════════════════════════════ */
.result-root {
    min-height: 100vh;
    background: #0D0D0D;
    color: white;
    display: flex;
    flex-direction: column;
    font-family: 'Cooper Hewitt', ui-sans-serif, system-ui, sans-serif;
}

/* ═══════════════════════════════════════════════════════════════════
   TOPBAR (progress bar + controls row — mirrors QuizShow)
═══════════════════════════════════════════════════════════════════ */
.result-topbar {
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
    width: 100%;
    height: 100%;
    background: #D32C37;
}
.topbar-controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 14px;
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
    text-decoration: none;
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
   MAIN
═══════════════════════════════════════════════════════════════════ */
.result-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 16px 16px 48px;
    gap: 28px;
}
@media (min-width: 1024px) {
    .result-main { padding: 24px 32px 56px; }
}

/* ═══════════════════════════════════════════════════════════════════
   DOSSIER AGENT CARD
═══════════════════════════════════════════════════════════════════ */
.dossier-wrapper {
    width: 100%;
    max-width: 900px;
    position: relative;
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

.dossier-card {
    background: #E8DFC0;
    color: #1A1A1A;
    border-radius: 4px;
    box-shadow: 0 30px 80px rgba(0,0,0,0.8);
    overflow: hidden;
    position: relative;
    animation: dossier-enter 700ms cubic-bezier(0.23,1,0.32,1) both;
}

.tape-strip {
    width: 72px;
    height: 13px;
    background: rgba(180,170,150,0.55);
    border-radius: 2px;
    display: block;
    margin-bottom: 4px;
}

/* ── Card header ──────────────────────────────────────────────── */
.dossier-header {
    padding: 20px 24px 10px;
    text-align: center;
}
.dossier-title {
    font-size: 1.5rem;
    font-weight: 900;
    letter-spacing: 0.12em;
    color: #1A1A1A;
    text-transform: uppercase;
}
@media (min-width: 1024px) {
    .dossier-header { padding: 28px 32px 12px; }
    .dossier-title  { font-size: 2rem; }
}

.dossier-rule {
    height: 1.5px;
    background: #C4A870;
    margin: 0 24px;
}
@media (min-width: 1024px) {
    .dossier-rule { margin: 0 32px; }
}

/* ── Body layout ─────────────────────────────────────────────── */
.dossier-body {
    display: grid;
    grid-template-columns: 1fr;
    padding: 20px 24px 120px;
    gap: 20px;
}
@media (min-width: 768px) {
    .dossier-body {
        grid-template-columns: 1fr auto;
        padding: 24px 32px 140px;
        gap: 36px;
    }
}

/* ── Narrative text (left) ───────────────────────────────────── */
.dossier-narrative {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.narrative-text {
    font-size: 13px;
    color: #3A2A1A;
    line-height: 1.75;
    font-style: italic;
}
@media (min-width: 768px) {
    .narrative-text { font-size: 14px; }
}
.narrative-text--gap { margin-top: 4px; }
.narrative-text--info {
    color: #2A5A8A;
    font-style: italic;
}

/* Reasons inside dossier */
.dossier-reasons {
    margin-top: 2px;
}
.dossier-reasons-title {
    font-size: 11px;
    font-weight: 700;
    color: #7A5A30;
    letter-spacing: 0.04em;
    margin-bottom: 6px;
}
.dossier-reasons-list {
    list-style: none;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.dossier-reason-item {
    display: flex;
    gap: 7px;
    font-size: 12px;
    color: #4A3A2A;
    line-height: 1.5;
    font-style: italic;
}
.reason-bullet {
    color: #D32C37;
    flex-shrink: 0;
    margin-top: 1px;
}

/* ── Action block inside dossier ─────────────────────────────── */
.dossier-action-block {
    margin-top: 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.dossier-action-date {
    font-size: 11px;
    color: #6A5A40;
    font-style: italic;
}
.dossier-action-btn {
    display: inline-block;
    padding: 10px 22px;
    font-size: 13px;
    font-weight: 700;
    font-family: 'Cooper Hewitt', ui-sans-serif, system-ui, sans-serif;
    text-decoration: none;
    border: none;
    border-radius: 40px;
    cursor: pointer;
    text-align: center;
    transition: opacity 150ms ease;
}
.dossier-action-btn:hover { opacity: 0.85; }
.dossier-action-btn--rdv {
    background: #D32C37;
    color: white;
    box-shadow: 0 3px 0 #921d24;
}
.dossier-action-btn--contact {
    background: #3A2A1A;
    color: #F0E8D0;
}

/* ── Right column ─────────────────────────────────────────────── */
.dossier-right-col {
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 100%;
}
@media (min-width: 768px) {
    .dossier-right-col { width: 210px; flex-shrink: 0; }
}

/* ── Photo frame ─────────────────────────────────────────────── */
.photo-frame {
    border: 1.5px solid #C4A870;
    background: #EDE8D4;
    overflow: hidden;
    position: relative;
}
.photo-tape {
    position: absolute;
    top: -5px;
    left: 50%;
    transform: translateX(-50%);
    width: 56px;
    height: 14px;
    background: rgba(180,170,150,0.6);
    border-radius: 2px;
    z-index: 2;
}

/* ── Fingerprints ─────────────────────────────────────────────── */
.fp-box {
    border: 1.5px solid #C4A870;
    padding: 10px;
    background: #E4D8B8;
}
.fp-title {
    font-size: 9px;
    font-weight: 800;
    color: #5A4A30;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    text-align: center;
    margin-bottom: 8px;
    border-bottom: 1px solid #C4A870;
    padding-bottom: 6px;
}
.fp-pair {
    display: flex;
    gap: 8px;
    justify-content: center;
}
.fp-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
}
.fp-svg { width: 52px; height: 60px; }
.fp-label {
    font-size: 8px;
    font-weight: 600;
    color: #7A6A50;
    text-align: center;
    letter-spacing: 0.02em;
    text-transform: uppercase;
}

/* ── Eligibility stamp (centered in dossier) ────────────────────── */
.agent-stamp {
    position: absolute;
    top: 50%;
    left: 46%;
    transform: translate(-50%, -50%) rotate(-13deg);
    border: 3.5px solid currentColor;
    padding: 12px 30px;
    font-size: 1.75rem;
    font-weight: 900;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    pointer-events: none;
    z-index: 5;
    white-space: nowrap;
    opacity: 0.92;
    animation: stamp-land 560ms cubic-bezier(0.23,1,0.32,1) both 440ms;
    font-family: 'Cooper Hewitt', ui-sans-serif, system-ui, sans-serif;
}
@media (min-width: 768px) {
    .agent-stamp { font-size: 2.25rem; padding: 14px 36px; left: 44%; }
}
/* Double border via pseudo-element */
.agent-stamp::before {
    content: '';
    position: absolute;
    inset: 6px;
    border: 1.5px solid currentColor;
    pointer-events: none;
}
.agent-stamp--eligible   { color: #2A8A3A; }
.agent-stamp--ineligible { color: #D32C37; }

/* ═══════════════════════════════════════════════════════════════════
   CTA SECTION
═══════════════════════════════════════════════════════════════════ */
.result-cta-section {
    width: 100%;
    max-width: 900px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    animation: cta-enter 560ms cubic-bezier(0.23,1,0.32,1) both 300ms;
}

.result-date-hint {
    font-size: 13px;
    color: rgba(255,255,255,0.5);
    text-align: center;
}

/* Share buttons row (non-eligible) */
.share-row {
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 100%;
}
@media (min-width: 640px) {
    .share-row { flex-direction: row; gap: 12px; }
}

.share-action-btn {
    flex: 1;
    display: block;
    background: #D32C37;
    color: white;
    font-family: 'Cooper Hewitt', ui-sans-serif, system-ui, sans-serif;
    font-size: 15px;
    font-weight: 700;
    padding: 16px 20px;
    border: none;
    border-radius: 40px;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    transition: background 150ms ease, transform 180ms cubic-bezier(0.23,1,0.32,1);
    box-shadow: 0 3px 0 #921d24, 0 4px 12px rgba(211,44,55,0.25);
}
.share-action-btn:hover {
    background: #C02030;
    transform: translateY(-2px);
    box-shadow: 0 4px 0 #7a1820, 0 6px 16px rgba(211,44,55,0.3);
}
.share-action-btn:active {
    transform: translateY(1px) scale(0.98);
    box-shadow: 0 1px 0 #921d24, 0 2px 6px rgba(211,44,55,0.15);
}

/* ═══════════════════════════════════════════════════════════════════
   REDUCED MOTION
═══════════════════════════════════════════════════════════════════ */
@media (prefers-reduced-motion: reduce) {
    .dossier-card { animation: none; opacity: 1; }
    .agent-stamp  { animation: none; opacity: 0.92; transform: translate(-50%, -50%) rotate(-13deg); }
    .result-cta-section { animation: none; opacity: 1; }
    .share-action-btn   { transition: background 120ms ease; }
    .share-action-btn:hover, .share-action-btn:active { transform: none; box-shadow: none; }
}
</style>

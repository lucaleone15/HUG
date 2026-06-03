<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'

const DATE_LOCALES = { fr: 'fr-CH', de: 'de-CH', it: 'it-CH', en: 'en-GB' }
import { sendAnalytics } from '../composables/useAnalytics.js'
import BaseButton from '../components/ui/BaseButton.vue'

const { t, locale } = useI18n()

const props = defineProps({
    entreprise: Object,
    submission: Object,
})

const eligible        = props.submission?.is_eligible ?? false
const reasons         = props.submission?.disqualification_reasons ?? []
const needsEvaluation = props.submission?.needs_evaluation ?? false
const copied = ref(false)
const shareUrl        = `${window.location.origin}/c/${props.entreprise.access_token}`
const dossierCode     = 'SANG-' + new Date().getFullYear().toString().slice(-2)

const formattedDate = computed(() => {
    if (!props.entreprise.rdv_date) return null
    const dateLocale = DATE_LOCALES[locale.value] ?? 'fr-CH'
    return new Date(props.entreprise.rdv_date).toLocaleDateString(dateLocale, {
        day: 'numeric', month: 'long', year: 'numeric',
    })
})

const onRdvClick = () => {
    sendAnalytics('rdv_clicked', props.entreprise.id, null, {})
    window.open(props.entreprise.rdv_url || 'https://www.onedoc.ch', '_blank', 'noopener,noreferrer')
}

const copyLink = async () => {
    try {
        await navigator.clipboard.writeText(shareUrl)
    } catch {
        const el = document.createElement('input')
        el.value = shareUrl
        el.style.position = 'fixed'
        el.style.opacity = '0'
        document.body.appendChild(el)
        el.select()
        document.execCommand('copy')
        document.body.removeChild(el)
    }
    copied.value = true
    setTimeout(() => { copied.value = false }, 2000)
}

const openMail = () => {
    const subject = encodeURIComponent(t('result.referral_email_subject', { company: props.entreprise.name }))
    const body    = encodeURIComponent(t('result.referral_email_body', { company: props.entreprise.name, url: shareUrl }))
    window.location.href = `mailto:?subject=${subject}&body=${body}`
}

const openWhatsapp = () => {
    const text = encodeURIComponent(t('result.referral_email_body', { company: props.entreprise.name, url: shareUrl }))
    window.open(`https://wa.me/?text=${text}`, '_blank', 'noopener,noreferrer')
}

const goToContact = () => {
    const params = new URLSearchParams()
    params.set('prefill', '1')
    if (props.entreprise?.name) params.set('company', props.entreprise.name)
    if (reasons.length > 0) params.set('reasons', reasons.join('||'))
    if (needsEvaluation) params.set('needs_eval', '1')
    window.location.href = `/contact?${params.toString()}`
}
</script>

<template>
<div class="result-root">

    <!-- ── Top bar (progress at 100%) ──────────────────────────────────────── -->
    <header class="result-topbar">
        <div class="topbar-progress" aria-hidden="true">
            <div class="topbar-progress-fill"></div>
        </div>
        <div class="topbar-controls">
            <a :href="`/c/${entreprise.access_token}`" class="topbar-back-btn" :aria-label="t('result.back_home')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
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
            <div class="hidden lg:flex justify-end pr-14 mb-[-1px] relative z-[1]">
                <span class="doc-tab-inner">N° {{ dossierCode }}</span>
            </div>

            <div class="dossier-card">

                <!-- Tape mobile -->
                <div class="lg:hidden flex justify-center pt-2 pb-1">
                    <div class="tape-strip"></div>
                </div>

                <!-- Card header -->
                <div class="dossier-header">
                    <h1 class="dossier-title">{{ t('result.dossier_title') }}</h1>
                </div>
                <div class="dossier-rule"></div>

                <!-- Body: narrative left, portrait right -->
                <div class="dossier-body">

                    <!-- LEFT: narrative text + reasons + action inside dossier -->
                    <div class="dossier-narrative">
                        <template v-if="eligible">
                            <p class="narrative-text">{{ t('result.eligible_narrative') }}</p>
                            <!-- RDV button inside dossier -->
                            <div class="dossier-action-block">
                                <p v-if="formattedDate" class="dossier-action-date">
                                    {{ t('entreprise.collect_date') }} : <strong>{{ formattedDate }}</strong>
                                </p>
                                <BaseButton variant="primary" class="!rounded-sm" @click="onRdvClick">
                                    {{ t('result.rdv_cta') }}
                                </BaseButton>
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
                            </div>
                            <p v-if="needsEvaluation" class="narrative-text narrative-text--gap narrative-text--info">
                                {{ t('result.needs_evaluation_message') }}
                            </p>
                            <p class="narrative-text narrative-text--gap">{{ t('result.ineligible_narrative_p2') }}</p>
                            <div class="dossier-action-block">
                                <BaseButton variant="dark" class="!rounded-sm" @click="goToContact">
                                    {{ t('result.contact_box_cta') }}
                                </BaseButton>
                            </div>
                        </template>
                    </div>

                    <!-- RIGHT: portrait + fingerprints (masqué sur mobile) -->
                    <div class="dossier-right-col hidden md:flex flex-col gap-[10px]">

                        <!-- Photo frame -->
                        <div class="photo-frame">
                            <div class="photo-tape"></div>
                            <div class="photo-icon-wrap">
                                <svg viewBox="4 8 56 50" preserveAspectRatio="xMidYMax meet" fill="currentColor" xmlns="http://www.w3.org/2000/svg" class="photo-icon">
                                    <circle cx="32" cy="22" r="13"/>
                                    <path d="M6 58c0-14.359 11.641-26 26-26s26 11.641 26 26H6z"/>
                                </svg>
                            </div>
                        </div>


                    </div><!-- /dossier-right-col -->
                </div><!-- /dossier-body -->

                <!-- Eligibility stamp (diagonal, absolute) -->
                <div :class="['agent-stamp', eligible ? 'agent-stamp--eligible' : 'agent-stamp--ineligible']">
                    {{ eligible ? t('result.stamp_eligible') : t('result.stamp_ineligible') }}
                </div>

                <!-- ── Boutons de partage intégrés dans le dossier ── -->
                <template v-if="!eligible">
                    <div class="dossier-rule"></div>
                    <div class="dossier-share">
                        <BaseButton variant="primary" class="!rounded-sm flex-1" @click="copyLink">
                            {{ copied ? t('result.referral_copied') : t('result.referral_colleague') }}
                        </BaseButton>
                        <BaseButton variant="primary" class="!rounded-sm flex-1" @click="openMail">
                            {{ t('result.share_mail') }}
                        </BaseButton>
                        <BaseButton variant="primary" class="!rounded-sm flex-1" @click="openWhatsapp">
                            {{ t('result.referral_whatsapp') }}
                        </BaseButton>
                    </div>
                </template>

            </div><!-- /dossier-card -->
        </div><!-- /dossier-wrapper -->

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
    0%   { opacity: 0; transform: rotate(3deg) scale(1.2); }
    55%  { opacity: 1; transform: rotate(10deg) scale(0.96); }
    75%  { transform: rotate(6deg) scale(1.01); }
    100% { opacity: 0.92; transform: rotate(8deg) scale(1); }
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
    z-index: 20;
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
    border-radius: 0;
    border: none;
    background: transparent;
    color: #e53e3e;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
    transition: color 150ms;
    text-decoration: none;
}
.topbar-back-btn:hover { color: #c53030; }
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
    padding: 28px 28px 36px;
    gap: 20px;
}
@media (min-width: 768px) {
    .dossier-body {
        grid-template-columns: 1fr auto;
        padding: 32px 36px 40px;
        gap: 36px;
    }
}

/* ── Narrative text (left) ───────────────────────────────────── */
.dossier-narrative {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.narrative-text {
    font-size: 14px;
    color: #3A2A1A;
    line-height: 1.8;
    font-style: italic;
}
@media (min-width: 768px) {
    .narrative-text { font-size: 15px; }
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
    font-size: 13px;
    color: #6A5A40;
    font-style: italic;
}

/* ── Right column ─────────────────────────────────────────────── */
.dossier-right-col {
    width: 210px;
    flex-shrink: 0;
}

/* ── Photo frame ─────────────────────────────────────────────── */
.photo-frame {
    border: 1.5px solid #C4A870;
    background: #EDE8D4;
    overflow: hidden;
    position: relative;
    aspect-ratio: 3/4;
    display: flex;
    align-items: center;
    justify-content: center;
}
.photo-icon-wrap {
    display: flex;
    align-items: flex-end;
    justify-content: center;
    width: 100%;
    height: 100%;
}
.photo-icon {
    width: 100%;
    height: 100%;
    color: #8A7A60;
    opacity: 0.45;
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


/* ── Eligibility stamp (bottom of dossier, in reserved padding zone) ── */
.agent-stamp {
    position: absolute;
    top: 18px;
    right: 20px;
    transform: rotate(8deg);
    border: 2.5px solid currentColor;
    padding: 8px 20px;
    font-size: 1.25rem;
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
    .agent-stamp { font-size: 1.75rem; padding: 10px 28px; top: 24px; right: 28px; border-width: 3px; }
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

/* ── Share inside dossier ──────────────────────────────────────── */
.dossier-share {
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 20px 24px 24px;
}
@media (min-width: 640px) {
    .dossier-share { flex-direction: row; padding: 20px 32px 28px; }
}




/* ═══════════════════════════════════════════════════════════════════
   REDUCED MOTION
═══════════════════════════════════════════════════════════════════ */
@media (prefers-reduced-motion: reduce) {
    .dossier-card { animation: none; opacity: 1; }
    .agent-stamp  { animation: none; opacity: 0.92; transform: rotate(8deg); }
    .result-cta-section { animation: none; opacity: 1; }
}
</style>

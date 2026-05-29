<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { sendAnalytics } from '../composables/useAnalytics.js'

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
const intakeYear      = new Date().getFullYear()

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

onMounted(() => {
    sendAnalytics('quiz_completed', props.entreprise.id, null, { is_eligible: eligible })
})
</script>

<template>
<div class="result-root">

    <!-- ── Top bar ───────────────────────────────────────────────────────── -->
    <header class="result-topbar">
        <a href="/" class="topbar-back-btn" aria-label="Retour à l'accueil">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <span class="result-topbar-label">DOSSIER CLÔTURÉ — RÉSULTAT</span>
    </header>

    <!-- Progress bar full (résultat = 100%) -->
    <div class="result-progress-bar" aria-hidden="true">
        <div class="result-progress-fill"></div>
    </div>

    <!-- ── Main ──────────────────────────────────────────────────────────── -->
    <main class="result-main">

        <!-- DOSSIER AGENT card -->
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
                    <h1 class="dossier-title">DOSSIER AGENT</h1>
                </div>
                <div class="dossier-rule"></div>

                <!-- Two-column body -->
                <div class="dossier-body">

                    <!-- LEFT: form fields -->
                    <div class="dossier-fields">
                        <div class="df">
                            <span class="df-key">NAME :</span>
                            <span class="df-line"></span>
                        </div>
                        <div class="df">
                            <span class="df-key">D.O.B. :</span>
                            <span class="df-val df-muted">__ / __ / ____</span>
                        </div>
                        <div class="df">
                            <span class="df-key">PLACE OF BIRTH :</span>
                            <span class="df-line"></span>
                        </div>
                        <div class="df">
                            <span class="df-key">ETHNICITY :</span>
                            <span class="df-line"></span>
                        </div>
                        <div class="df-row">
                            <div class="df flex-1">
                                <span class="df-key">AGE :</span>
                                <span class="df-line"></span>
                            </div>
                            <div class="df flex-1">
                                <span class="df-key">SEX :</span>
                                <span class="df-line"></span>
                            </div>
                        </div>
                        <div class="df-row">
                            <div class="df flex-1">
                                <span class="df-key">HAIR :</span>
                                <span class="df-line"></span>
                            </div>
                            <div class="df flex-1">
                                <span class="df-key">EYES :</span>
                                <span class="df-line"></span>
                            </div>
                        </div>
                        <div class="df-row">
                            <div class="df flex-1">
                                <span class="df-key">HEIGHT :</span>
                                <span class="df-line"></span>
                            </div>
                            <div class="df flex-1">
                                <span class="df-key">WEIGHT :</span>
                                <span class="df-line"></span>
                            </div>
                        </div>
                        <div class="dossier-rule my-2"></div>
                        <div class="df">
                            <span class="df-key">STATUS :</span>
                            <span :class="['df-status', eligible ? 'df-status--ok' : 'df-status--ko']">
                                {{ eligible ? 'ÉLIGIBLE AU DON' : 'NON ÉLIGIBLE AU DON' }}
                            </span>
                        </div>
                        <div class="dossier-rule my-2"></div>
                        <div class="df">
                            <span class="df-key">LOCATION :</span>
                            <span class="df-val">GENÈVE — SUISSE</span>
                        </div>
                        <div class="dossier-rule my-2"></div>
                        <div class="df">
                            <span class="df-key">INTAKE DATE :</span>
                            <span class="df-val df-muted">__ / __ / {{ intakeYear }}</span>
                        </div>
                    </div>

                    <!-- RIGHT: illustration + fingerprints -->
                    <div class="dossier-right-col">
                        <!-- Photo frame -->
                        <div class="photo-frame">
                            <svg viewBox="0 0 140 190" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                                <rect width="140" height="190" fill="#E4D8B8"/>
                                <!-- Body -->
                                <path d="M18 150 L32 115 Q70 106 108 115 L122 150 L122 190 L18 190 Z" fill="#4A4A4A"/>
                                <!-- Shirt collar -->
                                <path d="M57 115 L70 126 L83 115" fill="none" stroke="#E4D8B8" stroke-width="3"/>
                                <!-- Neck -->
                                <rect x="62" y="107" width="16" height="13" fill="#C4A078" rx="1"/>
                                <!-- Head -->
                                <ellipse cx="70" cy="78" rx="30" ry="32" fill="#C4A078"/>
                                <!-- Ears -->
                                <ellipse cx="40" cy="78" rx="5" ry="7" fill="#C4A078"/>
                                <ellipse cx="100" cy="78" rx="5" ry="7" fill="#C4A078"/>
                                <!-- Flat cap -->
                                <path d="M39 60 Q70 38 101 60 L101 66 Q70 48 39 66 Z" fill="#2A1608"/>
                                <rect x="37" y="60" width="66" height="11" rx="2" fill="#2A1608"/>
                                <!-- Eyes -->
                                <ellipse cx="58" cy="82" rx="5" ry="4.5" fill="#3A2008"/>
                                <ellipse cx="82" cy="79" rx="4" ry="3.5" fill="#3A2008"/>
                                <!-- Eye highlights -->
                                <circle cx="60" cy="80.5" r="1.5" fill="white" opacity="0.6"/>
                                <circle cx="83.5" cy="77.5" r="1.2" fill="white" opacity="0.6"/>
                                <!-- Nose hint -->
                                <path d="M70 88 Q67 95 62 97 Q70 99 78 97 Q73 95 70 88" fill="none" stroke="#9A7050" stroke-width="1.2"/>
                                <!-- Mouth -->
                                <path d="M62 105 Q70 109 78 105" stroke="#7A5030" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                            </svg>
                            <p class="photo-caption">CONFIDENTIEL — USAGE INTERNE</p>
                        </div>

                        <!-- Fingerprints -->
                        <div class="fp-box">
                            <p class="fp-title">EMPREINTES DIGITALES</p>
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
                                    <p class="fp-label">INDEX GAUCHE</p>
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
                                    <p class="fp-label">INDEX DROITE</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- STAMP (absolute, diagonal) -->
                <div :class="['agent-stamp', eligible ? 'agent-stamp--recruited' : 'agent-stamp--closed']">
                    {{ eligible ? 'AGENT RECRUTÉ' : 'DOSSIER CLASSÉ' }}
                </div>

            </div><!-- /dossier-card -->
        </div><!-- /dossier-wrapper -->

        <!-- ── CTA section ─────────────────────────────────────────────── -->
        <div class="result-cta-section">

            <template v-if="eligible">
                <p v-if="entreprise.rdv_date" class="result-date-hint">
                    {{ t('entreprise.collect_date') }} :
                    <strong>{{ new Date(entreprise.rdv_date).toLocaleDateString('fr-CH', { day: 'numeric', month: 'long', year: 'numeric' }) }}</strong>
                </p>
                <a v-if="entreprise.rdv_url"
                    :href="entreprise.rdv_url"
                    target="_blank" rel="noopener noreferrer"
                    class="result-main-btn"
                    @click="onRdvClick"
                >
                    Prendre rendez-vous
                </a>
                <a href="/" class="result-link">{{ t('result.back_home') }}</a>
            </template>

            <template v-else>
                <!-- Raisons -->
                <div v-if="reasons.length > 0" class="result-reasons">
                    <p class="reasons-title">{{ t('result.ineligible_reasons_title') }}</p>
                    <ul class="reasons-list">
                        <li v-for="(r, i) in reasons" :key="i" class="reason-item">
                            <span class="reason-bullet">•</span>
                            <span>{{ r }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Contact -->
                <div v-if="reasons.length > 0" class="result-box">
                    <p class="box-title">{{ t('result.contact_box_title') }}</p>
                    <p class="box-body">{{ t('result.contact_box_message') }}</p>
                    <a href="/contact" class="result-outline-btn">{{ t('result.contact_box_cta') }}</a>
                </div>

                <!-- Needs evaluation -->
                <div v-if="needsEvaluation" class="result-box result-box--info">
                    <p class="box-title">{{ t('result.needs_evaluation_title') }}</p>
                    <p class="box-body">{{ t('result.needs_evaluation_message') }}</p>
                </div>

                <!-- Referral -->
                <div class="result-box">
                    <p class="box-title">{{ t('result.referral_title') }}</p>
                    <p class="box-body">{{ t('result.referral_subtitle') }}</p>
                    <div class="share-link-row">
                        <span class="share-url">{{ shareUrl }}</span>
                        <button class="share-copy-btn"
                            :class="copied ? 'share-copy-btn--done' : ''"
                            @click="copyLink">
                            {{ copied ? t('result.referral_copied') : t('result.referral_copy') }}
                        </button>
                    </div>
                    <div class="share-btns">
                        <button class="result-outline-btn"
                            :class="messageCopied ? 'result-outline-btn--done' : ''"
                            @click="copyEmailMessage">
                            {{ messageCopied ? t('result.referral_message_copied') : t('result.referral_email') }}
                        </button>
                        <a :href="whatsappHref" target="_blank" rel="noopener noreferrer"
                            class="result-outline-btn result-outline-btn--whatsapp">
                            {{ t('result.referral_whatsapp') }}
                        </a>
                    </div>
                </div>

                <a href="/" class="result-link">{{ t('result.back_home') }}</a>
            </template>

        </div>

    </main>

    <!-- Progress bar (100%) at the bottom -->
    <div class="result-bottom-bar" aria-hidden="true">
        <div class="result-progress-bar-bottom">
            <div class="result-progress-fill-bottom"></div>
        </div>
        <span class="result-pct">100%</span>
    </div>

</div>
</template>

<style scoped>
/* ═══════════════════════════════════════════════════════════════════
   ROOT
═══════════════════════════════════════════════════════════════════ */
.result-root {
    min-height: 100vh;
    background: #0D0D0D;
    color: white;
    display: flex;
    flex-direction: column;
}

/* ═══════════════════════════════════════════════════════════════════
   TOPBAR
═══════════════════════════════════════════════════════════════════ */
.result-topbar {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 16px;
    background: #0D0D0D;
    border-bottom: 1px solid #1A1A1A;
    position: sticky;
    top: 0;
    z-index: 10;
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
.result-topbar-label {
    font-family: monospace;
    font-size: 11px;
    font-weight: 700;
    color: rgba(255,255,255,0.4);
    letter-spacing: 0.15em;
    text-transform: uppercase;
}
.result-progress-bar {
    height: 3px;
    background: #1A1A1A;
}
.result-progress-fill {
    height: 100%;
    width: 100%;
    background: #D32C37;
}

/* ═══════════════════════════════════════════════════════════════════
   MAIN
═══════════════════════════════════════════════════════════════════ */
.result-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 16px 16px 32px;
    gap: 24px;
}
@media (min-width: 1024px) {
    .result-main { padding: 24px 32px 48px; }
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
    font-family: monospace;
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
}

.tape-strip {
    width: 72px;
    height: 13px;
    background: rgba(180,170,150,0.55);
    border-radius: 2px;
    display: block;
    margin-bottom: 4px;
}

.dossier-header {
    padding: 20px 24px 10px;
    text-align: center;
}
.dossier-title {
    font-family: monospace;
    font-size: 1.5rem;
    font-weight: 900;
    letter-spacing: 0.2em;
    color: #1A1A1A;
}
@media (min-width: 1024px) {
    .dossier-header { padding: 28px 32px 12px; }
    .dossier-title { font-size: 2rem; }
}

.dossier-rule {
    height: 1.5px;
    background: #C4A870;
    margin: 0 24px;
}
@media (min-width: 1024px) {
    .dossier-rule { margin: 0 32px; }
}

/* ── Two-column body ──────────────────────────────────────────── */
.dossier-body {
    display: grid;
    grid-template-columns: 1fr;
    padding: 16px 24px 24px;
    gap: 20px;
}
@media (min-width: 768px) {
    .dossier-body {
        grid-template-columns: 1fr auto;
        padding: 20px 32px 32px;
        gap: 32px;
    }
}

/* ── Fields ───────────────────────────────────────────────────── */
.dossier-fields {
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex: 1;
}
.df {
    display: flex;
    align-items: baseline;
    gap: 8px;
    padding-bottom: 4px;
    border-bottom: 1px solid #C4A870;
    min-height: 28px;
}
.df-row {
    display: flex;
    gap: 16px;
}
.df-key {
    font-family: monospace;
    font-size: 11px;
    font-weight: 700;
    color: #5A4A30;
    letter-spacing: 0.06em;
    white-space: nowrap;
    flex-shrink: 0;
}
.df-line {
    flex: 1;
}
.df-val {
    font-family: monospace;
    font-size: 12px;
    font-weight: 600;
    color: #3A2A1A;
    letter-spacing: 0.05em;
}
.df-muted { color: #9A8A6A; }
.df-status {
    font-family: monospace;
    font-size: 13px;
    font-weight: 900;
    letter-spacing: 0.08em;
}
.df-status--ok  { color: #1A6A2A; }
.df-status--ko  { color: #8A1A1A; }

/* ── Right column ─────────────────────────────────────────────── */
.dossier-right-col {
    display: flex;
    flex-direction: column;
    gap: 12px;
    width: 100%;
}
@media (min-width: 768px) {
    .dossier-right-col { width: 200px; }
}

.photo-frame {
    border: 1.5px solid #C4A870;
    background: #E4D8B8;
    overflow: hidden;
    position: relative;
}
.photo-caption {
    font-family: monospace;
    font-size: 8px;
    color: #8A7A60;
    text-align: center;
    padding: 4px;
    background: #D4C8A0;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

/* ── Fingerprints ─────────────────────────────────────────────── */
.fp-box {
    border: 1.5px solid #C4A870;
    padding: 10px;
    background: #E4D8B8;
}
.fp-title {
    font-family: monospace;
    font-size: 9px;
    font-weight: 700;
    color: #5A4A30;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    text-align: center;
    margin-bottom: 8px;
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
    font-family: monospace;
    font-size: 7px;
    color: #7A6A50;
    text-align: center;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

/* ── Stamp ────────────────────────────────────────────────────── */
.agent-stamp {
    position: absolute;
    bottom: 80px;
    left: 50%;
    transform: translateX(-50%) rotate(-12deg);
    border: 4px solid #D32C37;
    color: #D32C37;
    font-family: monospace;
    font-size: 1.75rem;
    font-weight: 900;
    letter-spacing: 0.08em;
    padding: 10px 24px;
    text-transform: uppercase;
    pointer-events: none;
    z-index: 5;
    white-space: nowrap;
    opacity: 0.9;
}
@media (min-width: 768px) {
    .agent-stamp { font-size: 2.25rem; bottom: 100px; }
}
.agent-stamp--closed {
    border-color: #555;
    color: #555;
    transform: translateX(-50%) rotate(-12deg);
}

/* ═══════════════════════════════════════════════════════════════════
   CTA SECTION
═══════════════════════════════════════════════════════════════════ */
.result-cta-section {
    width: 100%;
    max-width: 900px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    align-items: stretch;
}

.result-date-hint {
    text-align: center;
    font-size: 14px;
    color: rgba(255,255,255,0.55);
}

.result-main-btn {
    display: block;
    width: 100%;
    background: #D32C37;
    color: white;
    text-align: center;
    font-size: 16px;
    font-weight: 700;
    padding: 16px 24px;
    border-radius: 8px;
    text-decoration: none;
    transition: background 150ms ease, transform 140ms cubic-bezier(0.23,1,0.32,1);
}
.result-main-btn:hover { background: #A9232C; }
.result-main-btn:active { transform: scale(0.97); }

.result-link {
    display: block;
    text-align: center;
    font-size: 14px;
    color: rgba(255,255,255,0.4);
    text-decoration: none;
    padding: 8px;
}
.result-link:hover { color: rgba(255,255,255,0.7); }

.result-reasons {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px;
    padding: 16px;
}
.reasons-title {
    font-size: 13px;
    font-weight: 600;
    color: rgba(255,255,255,0.8);
    margin-bottom: 10px;
}
.reasons-list { list-style: none; padding: 0; display: flex; flex-direction: column; gap: 6px; }
.reason-item {
    display: flex;
    gap: 8px;
    font-size: 13px;
    color: rgba(255,255,255,0.6);
    line-height: 1.5;
}
.reason-bullet { color: #D32C37; flex-shrink: 0; }

.result-box {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.result-box--info { border-color: rgba(4,189,251,0.3); background: rgba(4,189,251,0.05); }
.box-title { font-size: 14px; font-weight: 600; color: rgba(255,255,255,0.85); }
.box-body  { font-size: 13px; color: rgba(255,255,255,0.55); line-height: 1.5; }

.share-link-row {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.07);
    border-radius: 6px;
    padding: 8px 12px;
}
.share-url {
    flex: 1;
    font-size: 11px;
    color: rgba(255,255,255,0.4);
    font-family: monospace;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.share-copy-btn {
    background: #D32C37;
    color: white;
    font-size: 12px;
    font-weight: 600;
    padding: 5px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    flex-shrink: 0;
    transition: background 150ms;
}
.share-copy-btn:hover { background: #A9232C; }
.share-copy-btn--done { background: #04C852; }
.share-copy-btn--done:hover { background: #04C852; }

.share-btns { display: flex; gap: 8px; }
.result-outline-btn {
    flex: 1;
    background: transparent;
    color: rgba(255,255,255,0.7);
    border: 1.5px solid rgba(255,255,255,0.2);
    border-radius: 6px;
    padding: 10px 14px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    transition: border-color 150ms, color 150ms;
    display: block;
}
.result-outline-btn:hover { border-color: rgba(255,255,255,0.5); color: white; }
.result-outline-btn--done { border-color: #04C852; color: #04C852; }
.result-outline-btn--whatsapp:hover { border-color: #25D366; color: #25D366; }

/* ═══════════════════════════════════════════════════════════════════
   BOTTOM PROGRESS
═══════════════════════════════════════════════════════════════════ */
.result-bottom-bar {
    position: sticky;
    bottom: 0;
    background: #0D0D0D;
    border-top: 1px solid #1A1A1A;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.result-progress-bar-bottom {
    flex: 1;
    height: 4px;
    background: #2A2A2A;
    border-radius: 2px;
    overflow: hidden;
}
.result-progress-fill-bottom {
    width: 100%;
    height: 100%;
    background: #D32C37;
    border-radius: 2px;
}
.result-pct {
    font-size: 12px;
    font-weight: 600;
    color: rgba(255,255,255,0.45);
    min-width: 36px;
    text-align: right;
}

/* ═══════════════════════════════════════════════════════════════════
   REDUCED MOTION
═══════════════════════════════════════════════════════════════════ */
@media (prefers-reduced-motion: reduce) {
    .result-main-btn { transition: none; }
}
</style>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    primaryColor:   { type: String, default: '#E30613' },
    secondaryColor: { type: String, default: null },
})

const base = '/images/corkboard/'
const activeItem = ref(null)
const clickedId  = ref(null)
const boardRef   = ref(null)

// Mélange une couleur hex avec du blanc (factor 0=original, 1=blanc)
function tint(hex, factor) {
    if (!hex || !/^#[0-9A-Fa-f]{6}$/.test(hex)) hex = '#E30613'
    const r = parseInt(hex.slice(1, 3), 16)
    const g = parseInt(hex.slice(3, 5), 16)
    const b = parseInt(hex.slice(5, 7), 16)
    const t = (c) => Math.round(c + (255 - c) * factor).toString(16).padStart(2, '0')
    return `#${t(r)}${t(g)}${t(b)}`
}

function relativeLuminance(hex) {
    if (!hex || !/^#[0-9A-Fa-f]{6}$/.test(hex)) hex = '#E30613'
    const r = parseInt(hex.slice(1, 3), 16) / 255
    const g = parseInt(hex.slice(3, 5), 16) / 255
    const b = parseInt(hex.slice(5, 7), 16) / 255
    const lin = c => c <= 0.03928 ? c / 12.92 : ((c + 0.055) / 1.055) ** 2.4
    return 0.2126 * lin(r) + 0.7152 * lin(g) + 0.0722 * lin(b)
}
function contrastFg(hex) {
    return relativeLuminance(hex) > 0.179 ? 'rgba(0,0,0,0.80)' : 'rgba(255,255,255,0.90)'
}

// Tous les post-its dans la couleur primaire ; fallback si noir ou absent
const postitColors = computed(() => {
    const c1 = props.primaryColor || '#E30613'
    const bg = relativeLuminance(c1) < 0.02 ? '#E30613' : c1
    const color = { bg, fg: contrastFg(bg) }
    return Array(9).fill(color)
})

const ITEM_POSITIONS = [
    { id: 'pourquoi',  faqIndex: 0, rotation: -3,   pos: { left: '6%',  top: '30%', width: '15%' } },
    { id: 'frequence', faqIndex: 8, rotation: -1.5, pos: { left: '53%', top: '26%', width: '15%' } },
    { id: 'quantite',  faqIndex: 3, rotation:  2.5, pos: { left: '70%', top: '26%', width: '15%' } },
    { id: 'a-qui',     faqIndex: 7, rotation:  1.5, pos: { left: '26%', top: '38%', width: '15%' } },
    { id: 'jeun',      faqIndex: 5, rotation: -1.5, pos: { left: '58%', top: '47%', width: '15%' } },
    { id: 'sens-mal',  faqIndex: 4, rotation: -2.5, pos: { left: '5%',  top: '63%', width: '15%' } },
    { id: 'qui-peut',  faqIndex: 6, rotation:  1,   pos: { left: '31%', top: '63%', width: '16%' } },
    { id: 'mal',       faqIndex: 2, rotation:  2.5, pos: { left: '48%', top: '66%', width: '14%' } },
    { id: 'temps',     faqIndex: 1, rotation: -1,   pos: { left: '70%', top: '63%', width: '15%' } },
]

const items = computed(() =>
    ITEM_POSITIONS.map((p, i) => ({ ...p, color: postitColors.value[i] }))
)

const decorations = [
    { svg: 'squared-sheet.svg',  pos: { left: '42%', top: '24%', width: '11%' } },
    { svg: 'polaroid-1.svg',     pos: { left: '24%', top: '24%', width: '12%' } },
    { svg: 'polaroid-2.svg',     pos: { left: '44%', top: '45%', width: '12%' } },
    { svg: 'polaroid-3.svg',     pos: { left: '85%', top: '42%', width: '11%' } },
    { svg: '1 don 3 vies.svg',   pos: { left: '20%', top: '68%', width: '11%' } },
    { svg: 'coupure2presse.svg', pos: { left: '85%', top: '66%', width: '13%' } },
]

const MOBILE_GRID_TEMPLATE = [
    { type: 'item', id: 'm-0',  itemIndex: 0 },
    { type: 'deco', id: 'm-d0', svg: 'polaroid-1.svg' },
    { type: 'deco', id: 'm-d1', svg: 'polaroid-2.svg' },
    { type: 'item', id: 'm-1',  itemIndex: 1 },
    { type: 'item', id: 'm-2',  itemIndex: 2 },
    { type: 'item', id: 'm-3',  itemIndex: 3 },
    { type: 'deco', id: 'm-d2', svg: 'squared-sheet.svg' },
    { type: 'item', id: 'm-4',  itemIndex: 4 },
    { type: 'item', id: 'm-5',  itemIndex: 5 },
    { type: 'deco', id: 'm-d3', svg: 'polaroid-3.svg' },
    { type: 'item', id: 'm-6',  itemIndex: 6 },
    { type: 'deco', id: 'm-d4', svg: '1 don 3 vies.svg' },
    { type: 'item', id: 'm-7',  itemIndex: 7 },
    { type: 'item', id: 'm-8',  itemIndex: 8 },
    { type: 'deco', id: 'm-d5', svg: 'coupure2presse.svg' },
]

const mobileGrid = computed(() =>
    MOBILE_GRID_TEMPLATE.map(e =>
        e.type === 'item' ? { ...e, item: items.value[e.itemIndex] } : e
    )
)

function open(item) {
    clickedId.value = item.id
    setTimeout(() => {
        activeItem.value = item
        clickedId.value  = null
    }, 140)
}
function close() { activeItem.value = null }

function onKeydown(e) { if (e.key === 'Escape') close() }

let _observer = null
onMounted(() => {
    window.addEventListener('keydown', onKeydown)

    const assets = ['badge-scotch 1.svg', ...decorations.map(d => d.svg)]
    assets.forEach(name => {
        const link = Object.assign(document.createElement('link'), {
            rel: 'preload', as: 'image', href: base + name,
        })
        document.head.appendChild(link)
    })

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        boardRef.value?.querySelectorAll('.stagger-item')
            .forEach(el => el.classList.add('is-visible'))
        return
    }

    _observer = new IntersectionObserver(([entry]) => {
        if (!entry.isIntersecting) return
        _observer.disconnect()
        boardRef.value.querySelectorAll('.stagger-item').forEach((el, i) => {
            setTimeout(() => el.classList.add('is-visible'), i * 18)
        })
    }, { threshold: 0.08 })

    if (boardRef.value) _observer.observe(boardRef.value)
})
onUnmounted(() => {
    window.removeEventListener('keydown', onKeydown)
    _observer?.disconnect()
})
</script>

<template>
<div ref="boardRef">

    <!-- ═══════════════════════════════════
         MOBILE — portrait (< md)
    ═══════════════════════════════════ -->
    <div class="md:hidden cork-shell">
    <div class="cork-board relative overflow-hidden">

        <div class="relative px-3 pt-3 pb-5" style="z-index: 2;">

            <!-- En-tête : badge HUG + titre traduit -->
            <div class="flex items-center gap-2 mb-3">
                <img :src="base + 'badge-scotch 1.svg'" alt="HUG"
                     class="shrink-0 h-auto" style="width: 48px;"
                     fetchpriority="high" decoding="async" />
                <div class="flex-1 min-w-0">
                    <div class="cork-title-strip">
                        <span class="cork-tape cork-tape-l" aria-hidden="true"></span>
                        <span class="cork-tape cork-tape-r" aria-hidden="true"></span>
                        <p class="cork-title-text">{{ t('entreprise.faq_section_title') }}</p>
                    </div>
                </div>
            </div>

            <!-- Grille 2 colonnes -->
            <div class="grid grid-cols-2 gap-3">
                <template v-for="entry in mobileGrid" :key="entry.id">

                    <!-- Décoration -->
                    <div v-if="entry.type === 'deco'" class="flex items-center justify-center py-1">
                        <img :src="base + entry.svg" alt=""
                             class="stagger-item w-full h-auto block"
                             style="filter: drop-shadow(0 2px 6px rgba(0,0,0,0.22));"
                             loading="lazy" decoding="async" aria-hidden="true" />
                    </div>

                    <!-- Post-it CSS -->
                    <div v-else :class="['relative', { 'postit-pluck': clickedId === entry.item.id }]">
                        <button class="stagger-item postit-btn w-full border-0 bg-transparent p-0 cursor-pointer block"
                                :aria-label="t(`entreprise.faq_${entry.item.faqIndex}_q`)"
                                @click="open(entry.item)">
                            <div class="postit"
                                 :style="`transform: rotate(${entry.item.rotation}deg); --pi-bg:${entry.item.color.bg}; --pi-fg:${entry.item.color.fg}`">
                                <span class="pin" aria-hidden="true"></span>
                                <p class="postit-label">{{ t(`entreprise.faq_${entry.item.faqIndex}_short`) }}</p>
                            </div>
                        </button>
                    </div>

                </template>
            </div>

        </div>
    </div>
    </div><!-- /mobile -->


    <!-- ═══════════════════════════════════
         DESKTOP — paysage (≥ md)
    ═══════════════════════════════════ -->
    <div class="hidden md:block cork-shell">
    <div class="cork-board relative w-full overflow-hidden"
         style="aspect-ratio: 1529 / 1025;">

        <!-- Fil rouge avec ombre -->
        <svg viewBox="0 0 1529 1025" xmlns="http://www.w3.org/2000/svg"
             class="absolute inset-0 w-full h-full pointer-events-none"
             style="z-index: 5;" aria-hidden="true">
            <defs>
                <filter id="thread-shadow" x="-10%" y="-10%" width="120%" height="120%">
                    <feDropShadow dx="0" dy="2" stdDeviation="2.5" flood-color="rgba(0,0,0,0.55)" />
                </filter>
            </defs>
            <g stroke="#c62828" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"
               filter="url(#thread-shadow)" opacity="1">
                <path d="M 206,324 C 338,351 428,389 512,406 C 706,343 818,309 925,282 C 1052,277 1120,277 1185,282"/>
                <path d="M 1185,282 C 1148,383 1094,449 1001,498"/>
                <path d="M 1001,498 C 868,581 730,621 596,662 C 718,677 780,685 841,693 C 1008,679 1098,669 1185,662"/>
                <path d="M 1001,498 C 1094,581 1142,621 1185,662"/>
                <path d="M 512,406 C 398,489 292,575 191,662"/>
                <path d="M 191,662 C 388,662 490,662 596,662"/>
                <path d="M 206,324 C 200,443 195,553 191,662"/>
            </g>
        </svg>

        <!-- Badge HUG -->
        <img :src="base + 'badge-scotch 1.svg'" alt="HUG"
             class="absolute" style="left: 1%; top: 0.5%; width: 9%; z-index: 6;"
             fetchpriority="high" decoding="async" />

        <!-- Titre traduit -->
        <div class="absolute" style="left: 50%; top: 2%; width: 55%; transform: translateX(-50%); z-index: 3;">
            <div class="cork-title-strip">
                <span class="cork-tape cork-tape-l" aria-hidden="true"></span>
                <span class="cork-tape cork-tape-r" aria-hidden="true"></span>
                <p class="cork-title-text">{{ t('entreprise.faq_section_title') }}</p>
            </div>
        </div>

        <!-- Décorations non-interactives -->
        <img v-for="(d, i) in decorations" :key="i"
             :src="base + d.svg" alt=""
             class="stagger-item absolute pointer-events-none"
             :style="{ left: d.pos.left, top: d.pos.top, width: d.pos.width, zIndex: 2 }"
             loading="lazy" decoding="async" aria-hidden="true" />

        <!-- Vignette subtile -->
        <div class="absolute inset-0 pointer-events-none" style="z-index:8; background: radial-gradient(ellipse 85% 85% at 50% 50%, transparent 40%, rgba(0,0,0,0.50) 100%);" aria-hidden="true"></div>

        <!-- Post-its CSS — z-index 4 (sous le fil) -->
        <div v-for="item in items" :key="item.id"
             :class="['absolute', { 'postit-pluck': clickedId === item.id }]"
             :style="{ left: item.pos.left, top: item.pos.top, width: item.pos.width, zIndex: 4 }">
            <button class="stagger-item postit-btn w-full border-0 bg-transparent p-0 cursor-pointer block
                           focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-600
                           focus-visible:ring-offset-1 rounded-sm"
                    :aria-label="t(`entreprise.faq_${item.faqIndex}_q`)"
                    @click="open(item)">
                <div class="postit"
                     :style="`transform: rotate(${item.rotation}deg); --pi-bg:${item.color.bg}; --pi-fg:${item.color.fg}`">
                    <p class="postit-label">{{ t(`entreprise.faq_${item.faqIndex}_short`) }}</p>
                </div>
            </button>
        </div>

        <!-- Pins séparés — z-index 6 (au-dessus du fil) -->
        <span
            v-for="item in items"
            :key="item.id + '-pin'"
            class="pin"
            aria-hidden="true"
            :style="{
                position: 'absolute',
                left: `calc(${item.pos.left} + ${item.pos.width} / 2)`,
                top: item.pos.top,
                transform: 'translate(-50%, -52%)',
                zIndex: 7,
            }"
        />

    </div>
    </div><!-- /desktop -->

</div><!-- /boardRef -->

    <!-- ═══════════════════════════════════
         MODALE réponse
    ═══════════════════════════════════ -->
    <Teleport to="body">
        <Transition name="modal">
            <div v-if="activeItem"
                 class="fixed inset-0 flex items-center justify-center p-4"
                 style="z-index: 9999;" @click="close">
                <div class="modal-backdrop absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
                <div class="notebook-card relative w-full mx-4" style="max-width: 420px;" @click.stop>
                    <div class="tape"></div>
                    <button class="absolute top-4 right-4 w-7 h-7 flex items-center justify-center
                                   text-gray-500 hover:text-gray-800 text-2xl leading-none font-light"
                            @click="close"
                            :aria-label="t('admin.close')">×</button>
                    <div class="pt-10 pb-7 px-6">
                        <h3 class="font-bold text-gray-800 mb-3 leading-snug text-base">
                            {{ t(`entreprise.faq_${activeItem.faqIndex}_q`) }}
                        </h3>
                        <p class="text-gray-700 leading-relaxed text-sm">
                            {{ t(`entreprise.faq_${activeItem.faqIndex}_a`) }}
                        </p>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>

</template>


<style scoped>
/* ── Ombre + lift corkboard ──────────────────────────────────────── */
.cork-shell {
    border-radius: 6px;
    border: 10px solid #8e8e8e;
    box-shadow:
        inset 0 0 0 1px rgba(255,255,255,0.28),
        0 0 0 1px rgba(0,0,0,0.35),
        0 2px 4px rgba(0,0,0,0.12),
        0 8px 18px rgba(0,0,0,0.20),
        0 20px 40px rgba(0,0,0,0.22);
    transition:
        box-shadow 400ms cubic-bezier(0.23,1,0.32,1),
        transform   400ms cubic-bezier(0.23,1,0.32,1);
}
@media (hover: hover) and (pointer: fine) {
    .cork-shell:hover {
        transform: translateY(-4px);
        box-shadow:
            inset 0 0 0 1px rgba(255,255,255,0.32),
            0 0 0 1px rgba(0,0,0,0.40),
            0 2px 4px rgba(0,0,0,0.16),
            0 12px 24px rgba(0,0,0,0.26),
            0 28px 52px rgba(0,0,0,0.28);
    }
}

/* ── Stagger d'entrée ────────────────────────────────────────────── */
.stagger-item {
    opacity: 0;
    transform: translateY(8px);
    transition:
        opacity  220ms cubic-bezier(0.23,1,0.32,1),
        transform 220ms cubic-bezier(0.23,1,0.32,1);
}
.stagger-item.is-visible {
    opacity: 1;
    transform: translateY(0);
}

/* ── Pluck au clic ───────────────────────────────────────────────── */
@keyframes pluck {
    0%   { transform: translateY(0)    scale(1); }
    55%  { transform: translateY(-12px) scale(1.06); }
    100% { transform: translateY(-6px)  scale(1.03); }
}
.postit-pluck {
    animation: pluck 140ms cubic-bezier(0.23,1,0.32,1) forwards;
}

/* ── Fond board ──────────────────────────────────────────────────── */
.cork-board {
    background-color: #131313;
    background-image:
        repeating-linear-gradient(
            0deg,
            transparent 0px, transparent 2px,
            rgba(255,255,255,0.022) 2px, rgba(255,255,255,0.022) 3px
        ),
        repeating-linear-gradient(
            90deg,
            transparent 0px, transparent 2px,
            rgba(255,255,255,0.018) 2px, rgba(255,255,255,0.018) 3px
        ),
        url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='256' height='256'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.72 0.84' numOctaves='4' stitchTiles='stitch'/%3E%3CfeColorMatrix type='saturate' values='0'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.09'/%3E%3C/svg%3E");
    background-size: auto, auto, 256px 256px;
    background-position: center;
}

/* ── Titre (remplace title.svg) ──────────────────────────────────── */
.cork-title-strip {
    position: relative;
    background-color: #fdf3d8;
    background-image:
        url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='80'%3E%3Cfilter id='p'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23p)' opacity='0.06'/%3E%3C/svg%3E"),
        linear-gradient(180deg, #fffdf0 0%, #fdf3d8 55%, #f5e8b8 100%);
    padding: 8px 24px 12px;
    box-shadow: 3px 5px 14px rgba(0,0,0,0.28), inset 0 1px 0 rgba(255,255,240,0.8);
    clip-path: polygon(
        0% 10%, 1.5% 2%, 4% 8%, 6.5% 1%, 9% 7%, 12% 0%, 15% 6%, 18% 1%,
        21% 7%, 24% 0%, 27% 5%, 30% 0%, 33% 6%, 36% 1%, 39% 7%, 42% 0%,
        45% 6%, 48% 1%, 51% 7%, 54% 0%, 57% 5%, 60% 0%, 63% 6%, 66% 1%,
        69% 7%, 72% 0%, 75% 5%, 78% 0%, 81% 6%, 84% 1%, 87% 7%, 90% 2%,
        93% 8%, 96% 3%, 98.5% 9%, 100% 4%,
        100% 90%, 98% 96%, 95% 91%, 92% 97%, 89% 92%, 86% 99%, 83% 93%,
        80% 98%, 77% 92%, 74% 98%, 71% 93%, 68% 99%, 65% 94%, 62% 99%,
        59% 93%, 56% 98%, 53% 92%, 50% 97%, 47% 91%, 44% 97%, 41% 93%,
        38% 99%, 35% 94%, 32% 100%, 29% 95%, 26% 99%, 23% 94%, 20% 99%,
        17% 94%, 14% 99%, 11% 93%, 8% 98%, 5% 92%, 2% 96%, 0% 91%
    );
}
.cork-tape {
    position: absolute;
    width: 40px;
    height: 16px;
    background: linear-gradient(
        135deg,
        rgba(255,245,100,0.55) 0%,
        rgba(240,225,60,0.7) 50%,
        rgba(255,245,100,0.55) 100%
    );
    box-shadow: 0 1px 4px rgba(0,0,0,0.15), inset 0 0 8px rgba(255,255,180,0.3);
    top: -6px;
}
.cork-tape-l { left: 11%; transform: rotate(-9deg); }
.cork-tape-r { right: 11%; transform: rotate(6deg); }

.cork-title-text {
    font-family: 'Cooper Hewitt', ui-sans-serif, system-ui, sans-serif;
    font-weight: 800;
    text-transform: uppercase;
    text-align: center;
    font-size: clamp(0.75rem, 2.0vw, 1.6rem);
    letter-spacing: 0.12em;
    color: #1a0a0a;
    line-height: 1.1;
}

/* ── Post-its CSS (couleur via CSS vars) ─────────────────────────── */
.postit {
    --pi-bg: #fef08a;
    background: linear-gradient(175deg, rgba(255,255,255,0.30) 0%, transparent 42%), var(--pi-bg);
    box-shadow:
        0 10px 28px rgba(0,0,0,0.28),
        0 3px 8px rgba(0,0,0,0.16),
        inset 0 -3px 0 rgba(0,0,0,0.10);
    display: flex;
    align-items: center;
    justify-content: center;
    aspect-ratio: 1;
    padding: 28% 10% 12%;
    position: relative;
    border-radius: 3px;
}
.postit-label {
    font-family: 'Cooper Hewitt', ui-sans-serif, system-ui, sans-serif;
    font-weight: 700;
    font-size: clamp(0.52rem, 1.18vw, 0.92rem);
    text-align: center;
    color: var(--pi-fg, rgba(0,0,0,0.80));
    line-height: 1.28;
    overflow-wrap: break-word;
    hyphens: auto;
    position: relative;
    z-index: 1;
    letter-spacing: 0.01em;
}

/* ── Bouton post-it hover ────────────────────────────────────────── */
.postit-btn { cursor: pointer; }
.postit {
    transition:
        transform 200ms cubic-bezier(0.23,1,0.32,1),
        box-shadow 200ms cubic-bezier(0.23,1,0.32,1);
}
.postit-btn:active .postit {
    transform: scale(0.96);
    transition-duration: 120ms;
}
@media (hover: hover) and (pointer: fine) {
    .postit-btn:hover .postit {
        transform: scale(1.08) rotate(-1deg) translateY(-6px);
        box-shadow:
            0 20px 48px rgba(0,0,0,0.32),
            0 6px 14px rgba(0,0,0,0.18),
            inset 0 -3px 0 rgba(0,0,0,0.10);
    }
}

/* ── Pin ─────────────────────────────────────────────────────────── */
.pin {
    display: block;
    position: absolute;
    top: -11px;
    left: 50%;
    transform: translateX(-50%);
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: radial-gradient(circle at 38% 35%, #e8e8e8 0%, #9e9e9e 55%, #5a5a5a 100%);
    box-shadow:
        0 4px 12px rgba(0,0,0,0.65),
        0 1px 4px rgba(0,0,0,0.35),
        inset 0 2px 3px rgba(255,255,255,0.45);
    pointer-events: none;
    z-index: 10;
}
.pin::after {
    content: '';
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: rgba(0,0,0,0.28);
}

/* ── Carte réponse ───────────────────────────────────────────────── */
.notebook-card {
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 24px 64px rgba(0,0,0,0.22), 0 4px 12px rgba(0,0,0,0.08);
}
.tape { display: none; }

/* ── Overrides mobile ────────────────────────────────────────────── */
@media (max-width: 767px) {
    .postit {
        padding: 18% 8% 10%;
    }
    .postit-label {
        font-size: clamp(1.1rem, 6vw, 1.5rem);
        line-height: 1.2;
    }
    .pin {
        width: 20px;
        height: 20px;
        top: -10px;
    }
}

/* ── Pin hover (mobile — pin dans le bouton) ─────────────────────── */
@media (hover: hover) and (pointer: fine) {
    .postit-btn:hover .pin {
        transform: translateX(-50%) scale(1.2);
        transition: transform 200ms cubic-bezier(0.23,1,0.32,1);
    }
}

/* ── Backdrop modale ─────────────────────────────────────────────── */
.modal-backdrop {
    transition: opacity 220ms ease-out, backdrop-filter 220ms ease-out;
}
.modal-enter-from .modal-backdrop,
.modal-leave-to   .modal-backdrop { opacity: 0; backdrop-filter: blur(0px); }

/* ── Animations modale ───────────────────────────────────────────── */
.modal-enter-active { transition: opacity 220ms ease-out; }
.modal-enter-active .notebook-card {
    transition: transform 260ms cubic-bezier(0.23,1,0.32,1), opacity 220ms ease-out;
}
.modal-leave-active { transition: opacity 160ms ease-in; }
.modal-leave-active .notebook-card {
    transition: transform 140ms ease-in, opacity 140ms ease-in;
}
.modal-enter-from, .modal-leave-to { opacity: 0; }
.modal-enter-from .notebook-card { transform: scale(0.95) translateY(8px); opacity: 0; }
.modal-leave-to .notebook-card    { transform: scale(0.97) translateY(4px); opacity: 0; }

/* ── Reduced motion ──────────────────────────────────────────────── */
@media (prefers-reduced-motion: reduce) {
    .cork-shell { transition: none; }
    .cork-shell:hover { transform: none; }
    .stagger-item { opacity: 1; transform: none; transition: none; }
    .postit-pluck { animation: none; }
    .postit { transition: none; }
    .modal-backdrop { transition: opacity 100ms ease; }
    .modal-enter-active,
    .modal-leave-active { transition: opacity 100ms ease; }
    .modal-enter-active .notebook-card,
    .modal-leave-active .notebook-card { transition: opacity 100ms ease; }
    .modal-enter-from .notebook-card,
    .modal-leave-to .notebook-card { transform: none; }
}
</style>

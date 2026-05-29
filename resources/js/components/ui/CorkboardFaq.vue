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

// Génère une teinte d'une couleur hex en la mélangeant avec du blanc (factor 0=original, 1=blanc)
function tint(hex, factor) {
    if (!hex || !/^#[0-9A-Fa-f]{6}$/.test(hex)) hex = '#E30613'
    const r = parseInt(hex.slice(1, 3), 16)
    const g = parseInt(hex.slice(3, 5), 16)
    const b = parseInt(hex.slice(5, 7), 16)
    const t = (c) => Math.round(c + (255 - c) * factor).toString(16).padStart(2, '0')
    return `#${t(r)}${t(g)}${t(b)}`
}

// 9 couleurs de post-its dérivées des couleurs de l'entreprise
const postitColors = computed(() => {
    const c1 = props.primaryColor   || '#E30613'
    const c2 = props.secondaryColor || c1
    // Alterner c1/c2 avec des niveaux de teinte variés pour créer de la diversité
    const defs = [
        [c1, 0.76], [c2, 0.72], [c1, 0.63], [c2, 0.68],
        [c1, 0.82], [c2, 0.78], [c1, 0.70], [c2, 0.60], [c1, 0.58],
    ]
    return defs.map(([c, f]) => ({
        bg:   tint(c, f),
        grad: tint(c, Math.min(f + 0.10, 0.96)),
        dark: tint(c, Math.max(f - 0.14, 0.30)),
    }))
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

function open(item) { activeItem.value = item }
function close()    { activeItem.value = null }

function onKeydown(e) { if (e.key === 'Escape') close() }
onMounted(() => {
    window.addEventListener('keydown', onKeydown)
    // Précharge uniquement les décorations (post-its et fond sont maintenant en CSS)
    const assets = ['badge-scotch 1.svg', ...decorations.map(d => d.svg)]
    assets.forEach(name => {
        const link = Object.assign(document.createElement('link'), {
            rel: 'preload', as: 'image', href: base + name,
        })
        document.head.appendChild(link)
    })
})
onUnmounted(() => window.removeEventListener('keydown', onKeydown))
</script>

<template>

    <!-- ═══════════════════════════════════
         MOBILE — portrait (< md)
    ═══════════════════════════════════ -->
    <div class="md:hidden cork-frame" style="box-shadow: 0 10px 40px rgba(0,0,0,0.5);">
    <div class="cork-board relative rounded-sm overflow-hidden">

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
                             class="w-full h-auto block"
                             style="filter: drop-shadow(0 2px 6px rgba(0,0,0,0.22));"
                             loading="lazy" decoding="async" aria-hidden="true" />
                    </div>

                    <!-- Post-it CSS -->
                    <div v-else class="relative">
                        <button class="postit-btn w-full border-0 bg-transparent p-0 cursor-pointer block"
                                :aria-label="t(`entreprise.faq_${entry.item.faqIndex}_q`)"
                                @click="open(entry.item)">
                            <div class="postit"
                                 :style="`transform: rotate(${entry.item.rotation}deg); --pi-bg:${entry.item.color.bg}; --pi-grad:${entry.item.color.grad}; --pi-dk:${entry.item.color.dark}`">
                                <span class="pin" aria-hidden="true"></span>
                                <p class="postit-label">{{ t(`entreprise.faq_${entry.item.faqIndex}_short`) }}</p>
                            </div>
                        </button>
                    </div>

                </template>
            </div>

        </div>
    </div>
    </div><!-- /cork-frame mobile -->

    <!-- ═══════════════════════════════════
         DESKTOP — paysage (≥ md)
    ═══════════════════════════════════ -->
    <div class="hidden md:block cork-frame" style="box-shadow: 0 10px 40px rgba(0,0,0,0.5);">
    <div class="cork-board relative w-full overflow-hidden rounded-sm"
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
               filter="url(#thread-shadow)" opacity="0.92">
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
        <div class="absolute" style="left: 10%; top: 2%; width: 78%; z-index: 3;">
            <div class="cork-title-strip">
                <span class="cork-tape cork-tape-l" aria-hidden="true"></span>
                <span class="cork-tape cork-tape-r" aria-hidden="true"></span>
                <p class="cork-title-text">{{ t('entreprise.faq_section_title') }}</p>
            </div>
        </div>

        <!-- Décorations non-interactives -->
        <img v-for="(d, i) in decorations" :key="i"
             :src="base + d.svg" alt=""
             class="absolute pointer-events-none"
             :style="{ left: d.pos.left, top: d.pos.top, width: d.pos.width, zIndex: 2 }"
             loading="lazy" decoding="async" aria-hidden="true" />

        <!-- Vignette subtile -->
        <div class="absolute inset-0 pointer-events-none" style="z-index:8; background: radial-gradient(ellipse 80% 80% at 50% 50%, transparent 50%, rgba(0,0,0,0.18) 100%);" aria-hidden="true"></div>

        <!-- Post-its CSS — z-index 4 (sous le fil) -->
        <div v-for="item in items" :key="item.id"
             class="absolute"
             :style="{ left: item.pos.left, top: item.pos.top, width: item.pos.width, zIndex: 4 }">
            <button class="postit-btn w-full border-0 bg-transparent p-0 cursor-pointer block
                           focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-600
                           focus-visible:ring-offset-1 rounded-sm"
                    :aria-label="t(`entreprise.faq_${item.faqIndex}_q`)"
                    @click="open(item)">
                <div class="postit"
                     :style="`transform: rotate(${item.rotation}deg); --pi-bg:${item.color.bg}; --pi-grad:${item.color.grad}; --pi-dk:${item.color.dark}`">
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
    </div><!-- /cork-frame desktop -->

    <!-- ═══════════════════════════════════
         MODALE réponse
    ═══════════════════════════════════ -->
    <Teleport to="body">
        <Transition name="modal">
            <div v-if="activeItem"
                 class="fixed inset-0 flex items-center justify-center p-4"
                 style="z-index: 9999;" @click="close">
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
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
/* ── Cadre bois ──────────────────────────────────────────────────── */
.cork-frame {
    background:
        repeating-linear-gradient(
            92deg,
            transparent, transparent 3px,
            rgba(255,255,255,0.04) 3px, rgba(255,255,255,0.04) 4px
        ),
        linear-gradient(145deg, #8b5e38 0%, #5c3820 28%, #7a4e2a 56%, #4a2e14 78%, #6b4228 100%);
    padding: 16px;
    border-radius: 14px;
    box-shadow:
        inset 0 2px 4px rgba(255,255,255,0.08),
        inset 0 -3px 8px rgba(0,0,0,0.4);
}

/* ── Fond liège texturé ──────────────────────────────────────────── */
.cork-board {
    background-color: #c8975e;
    background-image:
        url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='256' height='256'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.78' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.28'/%3E%3C/svg%3E"),
        repeating-linear-gradient(
            74deg,
            transparent 0, transparent 4px,
            rgba(130,78,20,0.09) 4px, rgba(130,78,20,0.09) 5px
        ),
        repeating-linear-gradient(
            160deg,
            transparent 0, transparent 7px,
            rgba(190,132,55,0.06) 7px, rgba(190,132,55,0.06) 8px
        ),
        repeating-linear-gradient(
            35deg,
            transparent 0, transparent 11px,
            rgba(110,62,15,0.04) 11px, rgba(110,62,15,0.04) 12px
        ),
        radial-gradient(ellipse 45% 35% at 18% 22%, rgba(210,165,92,0.5) 0%, transparent 68%),
        radial-gradient(ellipse 38% 28% at 82% 78%, rgba(140,82,22,0.42) 0%, transparent 68%),
        radial-gradient(ellipse 32% 38% at 52% 48%, rgba(178,122,50,0.22) 0%, transparent 68%),
        radial-gradient(ellipse 22% 28% at 72% 16%, rgba(152,98,32,0.38) 0%, transparent 68%),
        radial-gradient(ellipse 30% 24% at 20% 82%, rgba(205,155,80,0.3) 0%, transparent 68%),
        radial-gradient(ellipse 18% 20% at 90% 30%, rgba(160,105,40,0.28) 0%, transparent 68%);
    box-shadow: inset 0 2px 8px rgba(70,35,5,0.25);
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
    font-family: 'Permanent Marker', 'Arial Black', cursive;
    font-weight: 400;
    text-transform: uppercase;
    text-align: center;
    font-size: clamp(0.9rem, 3.2vw, 2.7rem);
    letter-spacing: 0.03em;
    color: #1a1200;
    line-height: 1.1;
    text-shadow: 1px 2px 0 rgba(0,0,0,0.12), 0 1px 0 rgba(255,255,200,0.5);
}

/* ── Post-its CSS (couleur via CSS vars) ─────────────────────────── */
.postit {
    --pi-bg:   #fef08a;
    --pi-grad: #fff9b0;
    --pi-dk:   #e8d020;
    background-color: var(--pi-bg);
    background-image:
        /* réglure légère */
        repeating-linear-gradient(
            0deg,
            transparent 0, transparent 22px,
            rgba(0,0,0,0.055) 22px, rgba(0,0,0,0.055) 23px
        ),
        /* bruit papier */
        url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Cfilter id='p'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='1.2' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23p)' opacity='0.06'/%3E%3C/svg%3E"),
        /* dégradé directionnel */
        linear-gradient(155deg, var(--pi-grad) 0%, var(--pi-bg) 48%, var(--pi-dk) 100%);
    box-shadow:
        6px 12px 32px rgba(0,0,0,0.5),
        2px 3px 8px rgba(0,0,0,0.25),
        inset 0 1px 0 rgba(255,255,255,0.75);
    display: flex;
    align-items: center;
    justify-content: center;
    aspect-ratio: 1;
    padding: 24% 10% 14%;
    position: relative;
}
.postit-label {
    font-family: 'Permanent Marker', 'Arial Black', cursive;
    font-size: clamp(0.52rem, 1.18vw, 0.92rem);
    text-align: center;
    color: #181000;
    line-height: 1.28;
    overflow-wrap: break-word;
    hyphens: auto;
    text-shadow: 0 1px 2px rgba(255,255,255,0.35);
    position: relative;
    z-index: 1;
    letter-spacing: 0.005em;
}

/* ── Bouton post-it hover ────────────────────────────────────────── */
.postit-btn { transition: transform 180ms cubic-bezier(0.23,1,0.32,1), filter 180ms ease; }
.postit-btn:active { transform: scale(0.95) !important; }
@media (hover: hover) {
    .postit-btn:hover .postit {
        transform: scale(1.10) rotate(-2deg) translateY(-4px) !important;
        box-shadow:
            10px 20px 48px rgba(0,0,0,0.55),
            3px 5px 12px rgba(0,0,0,0.3),
            inset 0 1px 0 rgba(255,255,255,0.75) !important;
    }
}

/* ── Pin ─────────────────────────────────────────────────────────── */
.pin {
    display: block;
    position: absolute;
    top: 5px;
    left: 50%;
    transform: translateX(-50%);
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: radial-gradient(circle at 33% 26%, #ffcccc 0%, #f04040 25%, #b00000 58%, #4a0000 100%);
    box-shadow:
        0 4px 14px rgba(0,0,0,0.75),
        0 1px 4px rgba(0,0,0,0.45),
        inset 0 2px 4px rgba(255,200,200,0.55),
        inset 0 -2px 3px rgba(0,0,0,0.35);
    pointer-events: none;
    z-index: 10;
}
/* Reflet spéculaire */
.pin::before {
    content: '';
    position: absolute;
    top: 18%; left: 22%;
    width: 30%; height: 28%;
    background: radial-gradient(ellipse, rgba(255,255,255,0.7) 0%, transparent 100%);
    border-radius: 50%;
}
/* Tige + ombre portée sur le post-it */
.pin::after {
    content: '';
    position: absolute;
    top: 82%;
    left: 50%;
    transform: translateX(-50%);
    width: 3.5px;
    height: 14px;
    background: linear-gradient(to bottom, #aaa 0%, #555 60%, #333 100%);
    border-radius: 0 0 2px 2px;
    box-shadow: 1px 2px 5px rgba(0,0,0,0.5);
}

/* ── Carte réponse (style carnet à carreaux) ─────────────────────── */
.notebook-card {
    background-color: #fdf8e8;
    background-image: linear-gradient(#ddd8be 1px, transparent 1px);
    background-size: 100% 28px;
    background-position: 0 40px;
    border-radius: 3px;
    box-shadow: 0 12px 48px rgba(0,0,0,0.3), 0 2px 8px rgba(0,0,0,0.1);
    border: 1px solid rgba(0,0,0,0.06);
}
.tape {
    position: absolute;
    top: -13px;
    left: 50%;
    transform: translateX(-50%);
    width: 76px;
    height: 26px;
    background: rgba(185, 215, 240, 0.62);
    border-radius: 2px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.1);
}

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
        top: 4px;
    }
}

/* ── Animations modale ───────────────────────────────────────────── */
.modal-enter-active { transition: opacity 200ms ease; }
.modal-enter-active .notebook-card {
    transition: transform 240ms cubic-bezier(0.34, 1.56, 0.64, 1), opacity 200ms ease;
}
.modal-leave-active { transition: opacity 180ms ease; }
.modal-leave-active .notebook-card {
    transition: transform 160ms ease, opacity 160ms ease;
}
.modal-enter-from, .modal-leave-to { opacity: 0; }
.modal-enter-from .notebook-card { transform: scale(0.87) translateY(12px); opacity: 0; }
.modal-leave-to .notebook-card    { transform: scale(0.93) translateY(6px); opacity: 0; }
</style>

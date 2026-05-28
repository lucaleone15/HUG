<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const base = '/images/corkboard/'
const activeItem = ref(null)

const items = [
    { id: 'pourquoi',  faqIndex: 0, svg: 'Pourquoi.svg',         pos: { left: '6%',  top: '34%', width: '15%' } },
    { id: 'frequence', faqIndex: 8, svg: 'frequences.svg',        pos: { left: '53%', top: '30%', width: '15%' } },
    { id: 'quantite',  faqIndex: 3, svg: 'Quantite.svg',          pos: { left: '70%', top: '30%', width: '15%' } },
    { id: 'a-qui',     faqIndex: 7, svg: 'A qui _.svg',           pos: { left: '26%', top: '42%', width: '15%' } },
    { id: 'jeun',      faqIndex: 5, svg: 'Jeun.svg',              pos: { left: '58%', top: '51%', width: '15%' } },
    { id: 'sens-mal',  faqIndex: 4, svg: 'Si je me sens mal.svg', pos: { left: '5%',  top: '67%', width: '15%' } },
    { id: 'qui-peut',  faqIndex: 6, svg: 'Qui peut pas _.svg',    pos: { left: '31%', top: '67%', width: '16%' } },
    { id: 'mal',       faqIndex: 2, svg: 'Mal _.svg',             pos: { left: '48%', top: '70%', width: '14%' } },
    { id: 'temps',     faqIndex: 1, svg: 'Temps.svg',             pos: { left: '70%', top: '67%', width: '15%' } },
]

const decorations = [
    { svg: 'squared-sheet.svg',  pos: { left: '42%', top: '28%', width: '11%' } }, // centre, gap a-qui/frequence sous le titre
    { svg: 'polaroid-1.svg',     pos: { left: '24%', top: '28%', width: '12%' } }, // au-dessus de a-qui
    { svg: 'polaroid-2.svg',     pos: { left: '44%', top: '49%', width: '12%' } }, // centre, gap entre a-qui et jeun
    { svg: 'polaroid-3.svg',     pos: { left: '85%', top: '46%', width: '11%' } }, // bord droit mid, sous quantite
    { svg: '1 don 3 vies.svg',   pos: { left: '20%', top: '72%', width: '11%' } }, // bas gauche, gap sens-mal/qui-peut
    { svg: 'coupure2presse.svg', pos: { left: '85%', top: '70%', width: '13%' } }, // bas droit, à côté temps
]

// Ordre mobile : post-its et décos mélangés dans la grille
const mobileGrid = [
    { type: 'item', id: 'm-0', item: items[0] },                              // Pourquoi
    { type: 'deco', id: 'm-d0', svg: 'polaroid-1.svg' },
    { type: 'deco', id: 'm-d1', svg: 'polaroid-2.svg' },
    { type: 'item', id: 'm-1', item: items[1] },                              // Fréquences
    { type: 'item', id: 'm-2', item: items[2] },                              // Quantité
    { type: 'item', id: 'm-3', item: items[3] },                              // À qui
    { type: 'deco', id: 'm-d2', svg: 'squared-sheet.svg' },
    { type: 'item', id: 'm-4', item: items[4] },                              // Jeun
    { type: 'item', id: 'm-5', item: items[5] },                              // Si je me sens mal
    { type: 'deco', id: 'm-d3', svg: 'polaroid-3.svg' },
    { type: 'item', id: 'm-6', item: items[6] },                              // Qui peut pas
    { type: 'deco', id: 'm-d4', svg: '1 don 3 vies.svg' },
    { type: 'item', id: 'm-7', item: items[7] },                              // Mal
    { type: 'item', id: 'm-8', item: items[8] },                              // Temps
    { type: 'deco', id: 'm-d5', svg: 'coupure2presse.svg' },
]

function open(item) { activeItem.value = item }
function close()    { activeItem.value = null }

function onKeydown(e) { if (e.key === 'Escape') close() }
onMounted(()  => window.addEventListener('keydown', onKeydown))
onUnmounted(() => window.removeEventListener('keydown', onKeydown))
</script>

<template>

    <!-- ═══════════════════════════════════
         MOBILE — portrait (< md)
    ═══════════════════════════════════ -->
    <div
        class="md:hidden relative rounded-xl overflow-hidden"
        style="box-shadow: 0 4px 24px rgba(0,0,0,0.18);"
    >
        <!-- Fond liège -->
        <img
            :src="base + 'Corkboard.svg'"
            alt=""
            class="absolute inset-0 w-full h-full"
            style="object-fit: cover;"
            aria-hidden="true"
        />

        <!-- Fil rouge — répété sur toute la hauteur -->
        <div
            class="absolute inset-0 pointer-events-none"
            style="background-image: url('/images/corkboard/red-lines.svg'); background-size: 100% auto; background-repeat: repeat-y; z-index: 1; opacity: 0.85;"
            aria-hidden="true"
        />

        <div class="relative px-3 pt-3 pb-5" style="z-index: 2;">

            <!-- En-tête : badge HUG + titre -->
            <div class="flex items-center gap-2 mb-3">
                <img
                    :src="base + 'badge-scotch 1.svg'"
                    alt="HUG"
                    class="shrink-0 h-auto"
                    style="width: 48px;"
                />
                <div class="flex-1 min-w-0">
                    <img
                        :src="base + 'title.svg'"
                        alt="Le don du sang c'est quoi ?"
                        class="w-full h-auto block"
                    />
                </div>
            </div>

            <!-- Grille 2 colonnes : post-its et décos mélangés -->
            <div class="grid grid-cols-2 gap-3">
                <template v-for="entry in mobileGrid" :key="entry.id">

                    <!-- Décoration -->
                    <div v-if="entry.type === 'deco'" class="flex items-center justify-center py-1">
                        <img
                            :src="base + entry.svg"
                            alt=""
                            class="w-full h-auto block"
                            style="filter: drop-shadow(0 2px 6px rgba(0,0,0,0.22));"
                            aria-hidden="true"
                        />
                    </div>

                    <!-- Post-it interactif -->
                    <div v-else class="relative">
                        <button
                            class="postit-btn w-full border-0 bg-transparent p-0 cursor-pointer block"
                            :aria-label="t(`entreprise.faq_${entry.item.faqIndex}_q`)"
                            @click="open(entry.item)"
                        >
                            <img
                                :src="base + entry.item.svg"
                                alt=""
                                class="w-full h-auto block"
                                style="filter: drop-shadow(0 3px 8px rgba(0,0,0,0.28));"
                            />
                        </button>
                        <span class="pin" aria-hidden="true"></span>
                    </div>

                </template>
            </div>

        </div>
    </div>

    <!-- ═══════════════════════════════════
         DESKTOP — paysage (≥ md)
         Layout absolu sur aspect-ratio 1529/1025
    ═══════════════════════════════════ -->
    <div
        class="hidden md:block relative w-full overflow-hidden rounded-xl"
        style="aspect-ratio: 1529 / 1025; box-shadow: 0 4px 24px rgba(0,0,0,0.18);"
    >
        <!-- Fond liège -->
        <img
            :src="base + 'Corkboard.svg'"
            alt=""
            class="absolute inset-0 w-full h-full"
            style="object-fit: fill;"
            aria-hidden="true"
        />

        <!-- Fil rouge — tracé sur les pins réels (viewBox = corkboard 1529×1025) -->
        <svg
            viewBox="0 0 1529 1025"
            xmlns="http://www.w3.org/2000/svg"
            class="absolute inset-0 w-full h-full pointer-events-none"
            style="z-index: 5;"
            aria-hidden="true"
        >
            <!--
                Pin centers (top +5% → ×1025 +16px) :
                pourquoi  (206, 365)   a-qui    (512, 447)
                frequence (925, 323)   quantite (1185, 323)
                jeun      (1001, 539)  sens-mal (191, 703)
                qui-peut  (596, 703)   mal      (841, 734)
                temps     (1185, 703)
            -->
            <g stroke="#c0392b" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <!-- pourquoi → a-qui → frequence → quantite -->
                <path d="M 206,365 C 338,392 428,430 512,447 C 706,384 818,350 925,323 C 1052,318 1120,318 1185,323"/>
                <!-- quantite → jeun -->
                <path d="M 1185,323 C 1148,424 1094,490 1001,539"/>
                <!-- jeun → qui-peut → mal → temps -->
                <path d="M 1001,539 C 868,622 730,662 596,703 C 718,718 780,726 841,734 C 1008,720 1098,710 1185,703"/>
                <!-- jeun → temps (liaison directe droite) -->
                <path d="M 1001,539 C 1094,622 1142,662 1185,703"/>
                <!-- a-qui → sens-mal -->
                <path d="M 512,447 C 398,530 292,616 191,703"/>
                <!-- sens-mal → qui-peut -->
                <path d="M 191,703 C 388,703 490,703 596,703"/>
                <!-- pourquoi → sens-mal -->
                <path d="M 206,365 C 200,484 195,594 191,703"/>
            </g>
        </svg>

        <!-- Titre -->
        <img
            :src="base + 'title.svg'"
            alt="Le don du sang c'est quoi ?"
            class="absolute"
            style="left: 12%; top: 2%; width: 74%; z-index: 3;"
        />

        <!-- Badge HUG (coin supérieur gauche) -->
        <img
            :src="base + 'badge-scotch 1.svg'"
            alt="HUG"
            class="absolute"
            style="left: 1%; top: 0.5%; width: 9%; z-index: 6;"
        />

        <!-- Décorations non-interactives -->
        <img
            v-for="(d, i) in decorations"
            :key="i"
            :src="base + d.svg"
            alt=""
            class="absolute pointer-events-none"
            :style="{ left: d.pos.left, top: d.pos.top, width: d.pos.width, zIndex: 2 }"
            aria-hidden="true"
        />

        <!-- Post-its interactifs (sans pin, z-index 4) -->
        <div
            v-for="item in items"
            :key="item.id"
            class="absolute"
            :style="{ left: item.pos.left, top: item.pos.top, width: item.pos.width, zIndex: 4 }"
        >
            <button
                class="postit-btn w-full border-0 bg-transparent p-0 cursor-pointer block
                       focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-600
                       focus-visible:ring-offset-1 rounded-sm"
                :aria-label="t(`entreprise.faq_${item.faqIndex}_q`)"
                @click="open(item)"
            >
                <img :src="base + item.svg" alt="" class="w-full h-auto block" />
            </button>
        </div>

        <!-- Pins au-dessus du fil (z-index 6) — position = centre haut du post-it correspondant -->
        <span
            v-for="item in items"
            :key="item.id + '-pin'"
            class="pin"
            aria-hidden="true"
            :style="{
                position: 'absolute',
                left: `calc(${item.pos.left} + ${item.pos.width} / 2)`,
                top: `calc(${item.pos.top} + 8px)`,
                transform: 'translateX(-50%)',
                zIndex: 6,
            }"
        />
    </div>

    <!-- ═══════════════════════════════════
         MODALE réponse (partagée)
    ═══════════════════════════════════ -->
    <Teleport to="body">
        <Transition name="modal">
            <div
                v-if="activeItem"
                class="fixed inset-0 flex items-center justify-center p-4"
                style="z-index: 9999;"
                @click="close"
            >
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
                <div class="notebook-card relative w-full mx-4" style="max-width: 420px;" @click.stop>
                    <div class="tape"></div>
                    <button
                        class="absolute top-4 right-4 w-7 h-7 flex items-center justify-center
                               text-gray-500 hover:text-gray-800 text-2xl leading-none font-light"
                        @click="close"
                        aria-label="Fermer"
                    >×</button>
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
/* Hover uniquement sur les appareils qui le supportent (pas tactile) */
@media (hover: hover) {
    .postit-btn:hover {
        transform: scale(1.07) rotate(-1.5deg);
        filter: drop-shadow(0 5px 16px rgba(0, 0, 0, 0.3));
    }
}
.postit-btn {
    transition: transform 150ms ease, filter 150ms ease;
}
.postit-btn:active {
    transform: scale(0.96);
}

/* Pin — sibling du button dans le wrapper (position:absolute ou relative = containing block).
   Peint après le button dans le DOM → toujours par-dessus, pas besoin de z-index. */
.pin {
    display: block;
    position: absolute;
    top: 8px;
    left: 50%;
    transform: translateX(-50%);
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: radial-gradient(circle at 38% 30%, #ff9898, #d42222, #780000);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.65), inset 0 1px 2px rgba(255, 255, 255, 0.28);
    pointer-events: none;
}

/* Carte réponse (style carnet à carreaux) */
.notebook-card {
    background-color: #fdf8e8;
    background-image: linear-gradient(#ddd8be 1px, transparent 1px);
    background-size: 100% 28px;
    background-position: 0 40px;
    border-radius: 3px;
    box-shadow: 0 12px 48px rgba(0, 0, 0, 0.3), 0 2px 8px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0, 0, 0, 0.06);
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
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
}

/* Animations modale */
.modal-enter-active { transition: opacity 200ms ease; }
.modal-enter-active .notebook-card {
    transition: transform 240ms cubic-bezier(0.34, 1.56, 0.64, 1), opacity 200ms ease;
}
.modal-leave-active { transition: opacity 180ms ease; }
.modal-leave-active .notebook-card {
    transition: transform 160ms ease, opacity 160ms ease;
}
.modal-enter-from, .modal-leave-to { opacity: 0; }
.modal-enter-from .notebook-card {
    transform: scale(0.87) translateY(12px);
    opacity: 0;
}
.modal-leave-to .notebook-card {
    transform: scale(0.93) translateY(6px);
    opacity: 0;
}
</style>

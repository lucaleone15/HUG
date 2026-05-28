<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
const year      = new Date().getFullYear()
const logoError = ref(false)
</script>

<template>
    <footer class="bg-site-ink text-white mt-auto">

        <!-- Colonnes principales -->
        <div class="px-6 py-12">
            <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Branding -->
                <div>
                    <img v-if="!logoError" :src="'/images/hug-logo.svg'" alt="HUG"
                        class="h-8 w-auto mb-4 brightness-0 invert"
                        @error="logoError = true">
                    <span v-else class="font-bold text-lg mb-4 block text-white">HUG</span>
                    <p class="text-sm text-white/50 leading-relaxed mt-2">{{ t('footer.tagline') }}</p>
                </div>

                <!-- Programme -->
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-widest text-white/40 mb-4">{{ t('footer.nav_title') }}</h3>
                    <ul class="space-y-2">
                        <li><a href="/trophee"     class="text-sm text-white/60 hover:text-white transition-colors">{{ t('nav.trophee') }}</a></li>
                        <li><a href="/label"        class="text-sm text-white/60 hover:text-white transition-colors">{{ t('nav.label') }}</a></li>
                        <li><a href="/inscription"  class="text-sm text-white/60 hover:text-white transition-colors">{{ t('nav.inscription') }}</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-widest text-white/40 mb-4">{{ t('footer.contact_title') }}</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="mailto:info@donnez-votre-sang.ch" class="text-sm text-white/60 hover:text-white transition-colors">
                                info@donnez-votre-sang.ch
                            </a>
                        </li>
                        <li>
                            <a href="tel:+41223723311" class="text-sm text-white/60 hover:text-white transition-colors">
                                +41 (0)22 372 33 11
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Disclaimer académique -->
        <div class="border-t border-white/10 bg-white/5 px-6 py-4">
            <div class="max-w-5xl mx-auto flex flex-wrap items-center gap-x-5 gap-y-1">
                <span class="text-xs text-white/40 font-semibold uppercase tracking-wider">{{ t('footer.disclaimer_label') }}</span>
                <span class="text-xs text-white/30">{{ t('footer.disclaimer_text') }}</span>
                <a href="mailto:laurent.berthelot@heig-vd.ch" class="text-xs text-white/50 hover:text-white underline underline-offset-2 transition-colors whitespace-nowrap">
                    laurent.berthelot@heig-vd.ch
                </a>
                <a href="https://www.hug.ch/don-du-sang" target="_blank" rel="noopener noreferrer"
                    class="text-xs text-white/50 hover:text-white underline underline-offset-2 transition-colors whitespace-nowrap">
                    {{ t('footer.official_link') }}
                </a>
            </div>
        </div>

        <!-- Bottom bar -->
        <div class="border-t border-white/10 px-6 py-4">
            <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
                <span class="text-xs text-white/30">© {{ year }} HUG</span>
                <a href="/inscription"
                    class="btn btn-sm bg-white text-black hover:bg-white/90 border-none text-xs font-semibold rounded-sm uppercase tracking-wide">
                    {{ t('home.register_cta') }}
                </a>
            </div>
        </div>

    </footer>
</template>

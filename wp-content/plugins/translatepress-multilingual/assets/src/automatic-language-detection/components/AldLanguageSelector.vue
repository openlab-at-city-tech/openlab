<script setup>
import { computed, ref } from 'vue'
import { __ } from '@wordpress/i18n'
import LanguageItem from '../../language-switcher/components/LanguageItem.vue'

const props = defineProps({
    languages: {
        type: [Array, Object],
        default: () => [],
    },
    config: {
        type: Object,
        default: () => ({}),
    },
    compact: {
        type: Boolean,
        default: false,
    },
    maxDropdownItems: {
        type: Number,
        default: 2,
    },
})

const languageList = computed(() => {
    if (Array.isArray(props.languages))
        return props.languages

    const published = props.languages?.published || props.languages || {}

    return Object.entries(published).map(([code, settings]) => ({
        code,
        ...settings,
    }))
})

const displayedLanguages = computed(() => {
    const list = languageList.value.filter(Boolean)
    const maxLanguages = Math.max(2, (props.compact ? 2 : props.maxDropdownItems) + 1)

    if (list.length)
        return list.slice(0, maxLanguages)

    return [
        { code: 'en_US', name: __('English', 'translatepress-multilingual'), shortName: 'EN' },
        { code: 'es_ES', name: __('Spanish', 'translatepress-multilingual'), shortName: 'ES' },
        { code: 'fr_FR', name: __('French', 'translatepress-multilingual'), shortName: 'FR' },
    ].slice(0, maxLanguages)
})

const currentLanguage = computed(() => displayedLanguages.value[0])
const otherLanguages = computed(() => displayedLanguages.value.slice(1))
const isOpen = ref(false)
const listId = `trp-ald-preview-language-list-${Math.random().toString(36).slice(2, 9)}`
const websiteLanguageSelectorLabel = __('Website language selector', 'translatepress-multilingual')
const changeLanguageLabel = __('Change language', 'translatepress-multilingual')
const availableLanguagesLabel = __('Available languages', 'translatepress-multilingual')

const flagPosition = computed(() => props.config.aldSwitcherShowFlags === false ? 'hide' : 'before')
const languageNameMode = computed(() => props.config.aldSwitcherLanguageNames || 'full')
const flagShape = computed(() => props.config.aldSwitcherFlagShape || 'rect')

const switcherStyles = computed(() => ({
    '--trp-ald-switcher-bg': props.config.aldSwitcherBackgroundColor || '#ffffff',
    '--trp-ald-switcher-bg-hover': props.config.aldSwitcherBackgroundHoverColor || props.config.buttonBackgroundColor || '#143852',
    '--trp-ald-switcher-text': props.config.aldSwitcherTextColor || props.config.textColor || '#143852',
    '--trp-ald-switcher-text-hover': props.config.aldSwitcherTextHoverColor || props.config.buttonTextColor || '#ffffff',
    '--trp-ald-switcher-border': props.config.aldSwitcherBorderColor || props.config.borderColor || '#14385233',
    '--trp-ald-switcher-border-width': `${props.config.aldSwitcherBorderWidth ?? 1}px`,
    '--trp-ald-switcher-radius': `${props.config.aldSwitcherBorderRadius ?? 5}px`,
    '--flag-radius': `${props.config.aldSwitcherFlagRadius ?? 2}px`,
    '--aspect-ratio': flagShape.value === 'square' ? '1/1' : '4/3',
}))

const openSwitcher = () => {
    isOpen.value = true
}

const closeSwitcher = () => {
    isOpen.value = false
}

const onFocusOut = (event) => {
    if (!event.currentTarget.contains(event.relatedTarget))
        closeSwitcher()
}

const focusFirstOption = (target) => {
    target
        .closest('.trp-ald-language-selector')
        ?.querySelector('.trp-switcher-dropdown-list .trp-language-item')
        ?.focus?.({ preventScroll: true })
}

const onCurrentKeydown = (event) => {
    if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault()
        isOpen.value = !isOpen.value
        return
    }

    if (event.key === 'ArrowDown') {
        event.preventDefault()
        const currentTarget = event.currentTarget
        openSwitcher()
        requestAnimationFrame(() => focusFirstOption(currentTarget))
        return
    }

    if (event.key === 'Escape') {
        event.preventDefault()
        closeSwitcher()
    }
}

const onOptionKeydown = (event) => {
    if (event.key === 'Escape') {
        event.preventDefault()
        closeSwitcher()
        event.currentTarget
            .closest('.trp-ald-language-selector')
            ?.querySelector('.trp-ls-shortcode-current-language, .trp-language-item__current')
            ?.focus?.({ preventScroll: true })
    }
}

</script>

<template>
    <div
        class="trp-shortcode-switcher__wrapper trp-ald-language-selector-wrapper"
        :class="{ 'trp-ald-language-selector-wrapper--compact': props.compact }"
        :style="switcherStyles"
        @mouseenter="openSwitcher"
        @mouseleave="closeSwitcher"
        @focusout="onFocusOut"
    >
        <div
            class="trp-language-switcher trp-ls-dropdown trp-shortcode-switcher trp-shortcode-anchor trp-open-on-hover trp-ald-language-selector"
            aria-hidden="true"
            inert
        >
            <div class="trp-current-language-item__wrapper">
                <LanguageItem
                    v-if="currentLanguage"
                    :language="currentLanguage"
                    :dropdown="false"
                    class="trp-language-item__default trp-language-item__current"
                    :flag-pos="flagPosition"
                    :name-mode="languageNameMode"
                    :flag-aspect-ratio="flagShape"
                />

                <svg
                    class="trp-shortcode-arrow"
                    width="20"
                    height="20"
                    viewBox="0 0 20 21"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                    focusable="false"
                >
                    <path
                        d="M5 8L10 13L15 8"
                        stroke="var(--text)"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            </div>
        </div>

        <div
            class="trp-language-switcher trp-ls-dropdown trp-shortcode-switcher trp-shortcode-overlay trp-open-on-hover trp-ald-language-selector"
            :class="{ 'is-open': isOpen }"
            role="navigation"
            :aria-label="websiteLanguageSelectorLabel"
        >
            <div class="trp-language-switcher-inner">
                <div class="trp-current-language-item__wrapper">
                    <LanguageItem
                        v-if="currentLanguage"
                        :language="currentLanguage"
                        :dropdown="false"
                        class="trp-language-item__default trp-language-item__current"
                        role="button"
                        tabindex="0"
                        :aria-expanded="String(isOpen)"
                        :aria-label="changeLanguageLabel"
                        :aria-controls="listId"
                        @keydown="onCurrentKeydown"
                        :flag-pos="flagPosition"
                        :name-mode="languageNameMode"
                        :flag-aspect-ratio="flagShape"
                    />

                    <svg
                        class="trp-shortcode-arrow"
                        width="20"
                        height="20"
                        viewBox="0 0 20 21"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true"
                        focusable="false"
                    >
                        <path
                            d="M5 8L10 13L15 8"
                            stroke="var(--text)"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </div>

                <div
                    :id="listId"
                    class="trp-switcher-dropdown-list"
                    role="listbox"
                    :aria-label="availableLanguagesLabel"
                >
                    <LanguageItem
                        v-for="language in otherLanguages"
                        :key="language.code"
                        :language="language"
                        :dropdown="true"
                        role="option"
                        tabindex="0"
                        aria-selected="false"
                        @keydown="onOptionKeydown"
                        :flag-pos="flagPosition"
                        :name-mode="languageNameMode"
                        :flag-aspect-ratio="flagShape"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.trp-shortcode-switcher__wrapper {
    position: relative;
    width: max-content;
    max-width: 100%;
    border: none;
    --bg: var(--trp-ald-switcher-bg);
    --bg-hover: var(--trp-ald-switcher-bg-hover);
    --text: var(--trp-ald-switcher-text);
    --text-hover: var(--trp-ald-switcher-text-hover);
    --border-color: var(--trp-ald-switcher-border);
    --border: var(--trp-ald-switcher-border-width) solid var(--trp-ald-switcher-border);
    --border-radius: var(--trp-ald-switcher-radius);
    --font-size: 14px;
    --flag-size: 18px;
    --flag-radius: 2px;
    --aspect-ratio: 4/3;
    --transition-duration: 0.2s;
}

.trp-language-switcher {
    position: static;
    display: inline-block;
    overflow: hidden;
    width: 100%;
    min-height: 42px;
    padding: 0;
    border: var(--border);
    border-radius: var(--border-radius);
    background: var(--bg);
    box-shadow: 0 10px 20px 0 #0000000D;
    box-sizing: border-box;
}

.trp-shortcode-anchor:not(.trp-opposite-button) {
    visibility: hidden;
}

.trp-shortcode-overlay {
    position: absolute;
    left: 0;
    top: 0;
    z-index: 5;
}

.trp-language-switcher-inner {
    display: flex;
    flex-direction: column;
}

.trp-current-language-item__wrapper:not(.trp-hide-arrow) {
    display: flex;
    align-items: center;
    padding-right: 8px;
    justify-content: space-between;
}

.trp-switcher-dropdown-list {
    display: flex;
    flex-direction: column;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.2s ease-in-out;
    transition-duration: var(--transition-duration);
}

.trp-ls-dropdown.is-open .trp-switcher-dropdown-list,
.trp-ls-dropdown[aria-expanded="true"] .trp-switcher-dropdown-list,
.trp-shortcode-switcher.trp-open-on-hover.is-open .trp-switcher-dropdown-list,
.trp-shortcode-switcher[aria-expanded="true"] .trp-switcher-dropdown-list {
    max-height: calc(var(--trp-ald-preview-language-item-height, 40px) * var(--trp-ald-preview-dropdown-items, 2));
    overflow: hidden;
}

.trp-shortcode-switcher.is-open .trp-shortcode-arrow {
    transform: rotate(180deg);
}

.trp-shortcode-arrow {
    pointer-events: none;
    flex: 0 0 auto;
}

.trp-ald-language-selector :deep(.trp-language-item) {
    box-sizing: border-box;
    min-height: 40px;
    padding: 0 8px;
    text-decoration: none;
}

.trp-ald-language-selector :deep(.trp-switcher-dropdown-list .trp-language-item) {
    width: 100%;
}

.trp-ald-language-selector :deep(.trp-language-item__current) {
    flex: 0 1 auto;
    width: auto;
    min-width: 0;
}

.trp-ald-language-selector :deep(.trp-language-item:hover),
.trp-ald-language-selector :deep(.trp-language-item:focus-visible) {
    background: var(--bg-hover);
    color: var(--text-hover);
}

.trp-ald-language-selector :deep(.trp-language-item:focus-visible) {
    outline: 2px solid var(--text);
    outline-offset: -2px;
}

.trp-ald-language-selector :deep(.trp-language-item:hover .trp-language-item-name),
.trp-ald-language-selector :deep(.trp-language-item:focus-visible .trp-language-item-name) {
    color: var(--text-hover);
}

.trp-ald-language-selector :deep(.trp-language-item-name) {
    max-width: 105px;
}

.trp-ald-language-selector-wrapper--compact {
    --trp-ald-preview-dropdown-items: 2;
}
</style>

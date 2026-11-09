<script setup>
import { useSwitcherPreview } from "../composables/useSwitcherPreview"
import { computed, ref }        from "vue"

import LanguageItem from "./LanguageItem.vue"
import PoweredBy    from "./PoweredBy.vue"

import { __ } from '@wordpress/i18n'

const bgUrl = `url(${window.tpLangSwitcherData.misc.pluginUrl}assets/images/switcher-preview-bg.png)`

const props = defineProps({
    scope: {
      type: String,
      default: '',
      validator: v => ['floater','shortcode','menu'].includes(v)
    }
})

const isOpen = ref(false)

const toggleDropdown = () => openOnClick.value && ( isOpen.value = !isOpen.value )

const isShortcode = computed(() => props.scope === 'shortcode')

const previewBoxText = __( 'Hover over the language switcher to see it in action!', 'translatepress-multilingual')

const switcherEdgeClass = computed(() => {
    if (props.scope !== 'floater') return null

    if (switcherPosition.value?.startsWith('top')) return 'trp-switcher-position-top'
    if (switcherPosition.value?.startsWith('bottom')) return 'trp-switcher-position-bottom'

    return null
})

const {
    displayedList,
    isDropdown,
    switcherStyles,
    isOppositeMode,
    showPoweredBy,
    switcherPosition,
    openOnClick
} = useSwitcherPreview(props.scope)

const allowShortcodeToggle = computed(() => isShortcode.value && !isOppositeMode.value && openOnClick.value)

const shortcodeClasses = computed(() => {
    if (!isShortcode.value) return []
    return [
        isOppositeMode.value ? 'trp-opposite-button' : null,
        allowShortcodeToggle.value ? 'trp-open-on-click' : 'trp-open-on-hover',
        { 'trp-dropdown-open': isOpen.value && !isOppositeMode.value }
    ]
})

const onShortcodeClick = () => {
    if (!isShortcode.value || isOppositeMode.value) return
    if (openOnClick.value) isOpen.value = !isOpen.value
}
</script>

<template>
    <div class="trp-language-switcher-preview__container">
        <div class="trp-language-switcher-preview-box">
            <!-- Floater view, opposite mode -->
            <template v-if="isOppositeMode && !isShortcode">
                <div
                    class="trp-language-switcher trp-floating-switcher trp-opposite-button"
                    :style="switcherStyles"
                >
                    <LanguageItem
                        v-if="displayedList.length"
                        :key="displayedList[0].code"
                        :language="displayedList[0]"
                    />

                    <PoweredBy v-if="showPoweredBy" />
                </div>
            </template>

            <!-- Floater view, normal display -->
            <template v-else-if="!isShortcode">
                <div
                    v-if="isDropdown"
                    class="trp-language-switcher trp-floating-switcher trp-ls-dropdown"
                    :class="switcherEdgeClass"
                    :style="switcherStyles"
                >
                    <PoweredBy v-if="showPoweredBy" />

                    <div class="trp-language-switcher-inner">
                        <LanguageItem
                            v-if="displayedList.length"
                            :key="displayedList[0].code"
                            :language="displayedList[0]"
                            :dropdown="false"
                            class="trp-language-item__default"
                        />

                        <div class="trp-switcher-dropdown-list">
                            <LanguageItem
                                v-for="lang in displayedList.slice(1)"
                                :key="lang.code"
                                :language="lang"
                                :dropdown="true"
                            />
                        </div>
                    </div>
                </div>

                <div
                    v-else
                    class="trp-language-switcher trp-preview-ls-inline"
                    :class="switcherEdgeClass"
                    :style="switcherStyles"
                >
                    <PoweredBy v-if="showPoweredBy" />

                    <div class="trp-language-switcher-inner">
                        <LanguageItem
                            v-for="lang in displayedList"
                            :key="lang.code"
                            :language="lang"
                        />
                    </div>
                </div>
            </template>

            <!-- Shortcode view -->
            <template v-else>
                <div
                    class="trp-language-switcher trp-ls-dropdown trp-shortcode-switcher"
                    :class="shortcodeClasses"
                    :style="switcherStyles"
                    @click="onShortcodeClick"
                >
                    <div class="trp-language-switcher-inner">
                        <div class="trp-current-language-item__wrapper" :class="{ 'trp-hide-arrow': isOppositeMode }">
                            <LanguageItem
                                v-if="displayedList.length"
                                :key="displayedList[0].code"
                                :language="displayedList[0]"
                                :dropdown="false"
                                class="trp-language-item__default"
                            />

                            <!-- Arrow is visually hidden in opposite mode via CSS -->
                            <svg
                                class="trp-shortcode-arrow"
                                width="20"
                                height="20"
                                viewBox="0 0 20 21"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
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

                        <!-- Dropdown list is removed in opposite mode -->
                        <div
                            v-if="!isOppositeMode"
                            class="trp-switcher-dropdown-list"
                        >
                            <LanguageItem
                                v-for="lang in displayedList.slice(1)"
                                :key="lang.code"
                                :language="lang"
                                :dropdown="true"
                            />
                        </div>
                    </div>

                    <PoweredBy v-if="showPoweredBy && isOppositeMode" />
                </div>
            </template>

        </div>

        <span class="trp-language-switcher-preview-text trp-description-text">
            {{ previewBoxText }}
        </span>
    </div>
</template>

<style scoped>
    .trp-language-switcher-preview__container{
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .trp-language-switcher-preview-box {
        background-image: v-bind(bgUrl);
        background-repeat: no-repeat;
        background-size: cover;
        height: 220px;
        position: relative;
        border-radius: var(--trp-settings-radius-small);
        border: 1px solid #E2E2E4;
        overflow: hidden;
    }

    .trp-language-switcher-preview-box .trp-floating-switcher:hover .trp-switcher-dropdown-list {
        max-height: 150px;
    }

    .trp-floating-switcher:hover {
        overflow-y: auto;
    }

    .trp-language-switcher {
        display: flex;
        flex-direction: column-reverse;
        position: absolute;
        bottom: var(--bottom, unset);
        top: var(--top, unset);
        left: var(--left, unset);
        right: var(--right, unset);
        overflow: hidden;
        padding: var(--switcher-padding);
        border-width: var(--border-width);
        border-radius: var(--border-radius);
        border-color: var(--border-color);
        border-style: solid;
        background: var(--bg);
        width: var(--switcher-width);
        box-shadow: 0 10px 20px 0 #0000000D;
        transition: 0.2s ease;
    }

    .trp-switcher-position-top.trp-language-switcher {
        flex-direction: column;
    }

    .trp-language-switcher-inner {
        display: flex;
        flex-direction: column-reverse;
    }

    .trp-preview-ls-inline .trp-language-switcher-inner {
        flex-direction: row;
        justify-content: space-between;
    }

    .trp-switcher-position-top.trp-ls-dropdown .trp-language-switcher-inner, .trp-switcher-position-top.trp-ls-dropdown .trp-switcher-dropdown-list{
        flex-direction: column;
    }

    .trp-ls-dropdown:not(:hover) .trp-language-item {
        pointer-events: none;
    }

    .trp-switcher-dropdown-list {
        max-height: 0;
        overflow: hidden;
        transition: 0.2s ease-in-out;
        scrollbar-width: thin;
        scrollbar-color: var(--text) transparent;
        transition-duration: var(--transition-duration);
    }

    /* Shortcode styling */
    .trp-language-switcher.trp-shortcode-switcher {
        top: 35%;
        left: 50%;
        transform: translateX(-50%);
        border: var(--border-width) solid var(--border-color);
        overflow-y: hidden;
        transition: none;
        padding: 10px 0;
        width: auto;
        flex-direction: column;
    }

    .trp-language-switcher.trp-shortcode-switcher .trp-language-switcher-inner {
        flex-direction: column;
    }

    .trp-current-language-item__wrapper {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        padding-right: 8px;
    }

    .trp-shortcode-switcher.trp-dropdown-open .trp-shortcode-arrow {
        transform: rotate(180deg);
    }

    .trp-language-switcher.trp-shortcode-switcher.trp-open-on-hover:hover .trp-switcher-dropdown-list {
        max-height: 75px;
        overflow-y: auto;
    }

    .trp-open-on-click.trp-dropdown-open .trp-switcher-dropdown-list {
        max-height: 75px;
        overflow-y: auto;
    }

    .trp-open-on-click .trp-current-language-item__wrapper {
        cursor: pointer;
    }

    .trp-shortcode-arrow {
        pointer-events: none;
    }

    .trp-shortcode-switcher.trp-opposite-button {
        padding: var(--switcher-padding, 10px 12px);
        border-radius: var(--border-radius);
        width: auto;
        overflow: hidden;     /* no inner scroll in opposite */
        transition: none;     /* no hover expand */
    }

    /* Don’t treat current item like a toggle in opposite mode */
    .trp-shortcode-switcher.trp-opposite-button .trp-current-language-item__wrapper {
        cursor: default;
    }

    /* Hide the arrow when opposite */
    .trp-shortcode-switcher .trp-current-language-item__wrapper.trp-hide-arrow .trp-shortcode-arrow {
        display: none;
    }

    /* Ensure no dropdown animation space is reserved in opposite */
    .trp-shortcode-switcher.trp-opposite-button .trp-switcher-dropdown-list {
        display: none !important;
        max-height: 0 !important;
        overflow: hidden !important;
    }

    /* Prevent hover-expansion rules affecting opposite mode */
    .trp-shortcode-switcher.trp-opposite-button.trp-open-on-hover:hover .trp-switcher-dropdown-list {
        display: none !important;
    }

</style>
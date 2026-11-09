<script setup>
import { computed, ref, watch } from 'vue'
import { __ } from '@wordpress/i18n'
import AldLanguageSelector from './AldLanguageSelector.vue'

const props = defineProps({
    config: { type: Object, required: true },
    emptyPreviewText: { type: String, default: '' },
    backgroundImage: { type: String, default: '' },
    compact: { type: Boolean, default: false },
    languages: { type: [Array, Object], default: () => [] },
})

const previewStyles = computed(() => ({
    '--trp-ald-preview-bg': props.config.backgroundColor,
    '--trp-ald-preview-text': props.config.textColor,
    '--trp-ald-preview-button-bg': props.config.buttonBackgroundColor,
    '--trp-ald-preview-button-border': props.config.buttonBorderColor || props.config.buttonBackgroundColor,
    '--trp-ald-preview-button-hover': props.config.buttonHoverColor,
    '--trp-ald-preview-button-text': props.config.buttonTextColor,
    '--trp-ald-preview-button-hover-text': props.config.buttonHoverTextColor || props.config.buttonTextColor,
    '--trp-ald-preview-button-radius': `${props.config.buttonBorderRadius ?? 3}px`,
    '--trp-ald-preview-close': props.config.closeTextColor,
    '--trp-ald-preview-border': props.config.borderColor,
    '--trp-ald-preview-border-width': `${props.config.borderWidth || 0}px`,
    '--trp-ald-preview-radius': `${props.config.borderRadius || 0}px`,
    '--trp-ald-preview-overlay': props.config.overlayColor,
    '--trp-ald-preview-bg-image': props.backgroundImage,
}))

const isHelloBar = computed(() => props.config.popup_type === 'hello_bar')
const hasPopup = computed(() => props.config.popup_option === 'popup')
const closeLabel = __('Close', 'translatepress-multilingual')
const animationKey = ref(0)
const animationClass = computed(() => {
    if (props.compact || props.config.popupAnimationEnabled === false)
        return 'trp-ald-animation-none'

    return `trp-ald-animation-${props.config.popupAnimation || 'fade'}`
})

watch(
    () => [props.config.popupAnimationEnabled, props.config.popupAnimation, props.config.popup_type, props.config.popup_option],
    () => {
        animationKey.value += 1
    }
)
</script>

<template>
    <div
        class="trp-ald-preview"
        :class="{ 'is-hello-bar': isHelloBar, 'is-compact': props.compact }"
        :style="previewStyles"
    >
        <div v-if="!hasPopup" class="trp-ald-preview__empty">
            {{ emptyPreviewText }}
        </div>

        <div v-else-if="!isHelloBar" class="trp-ald-preview__overlay">
            <div
                :key="`popup-${animationKey}`"
                class="trp-ald-preview__popup trp-ald-preview__animated"
                :class="animationClass"
            >
                <div class="trp-ald-preview__text" v-html="config.popup_textarea" />

                <div class="trp-ald-preview__controls">
                    <AldLanguageSelector :languages="props.languages" :config="props.config" :compact="props.compact" />
                    <button type="button" class="trp-ald-preview__button">
                        {{ config.popup_textarea_button }}
                    </button>
                </div>

                <button type="button" class="trp-ald-preview__close">
                    <span class="trp-ald-preview__close-icon-before" aria-hidden="true" />
                    <span class="trp-ald-preview__close-text">
                        {{ config.popup_textarea_close_button }}
                    </span>
                </button>
            </div>
        </div>

        <div v-else class="trp-ald-preview__bar-stage">
            <div
                :key="`bar-${animationKey}`"
                class="trp-ald-preview__bar trp-ald-preview__animated"
                :class="animationClass"
            >
                <div class="trp-ald-preview__text" v-html="config.popup_textarea" />
                <div class="trp-ald-preview__bar-controls">
                    <AldLanguageSelector :languages="props.languages" :config="props.config" :compact="props.compact" />
                    <button type="button" class="trp-ald-preview__button">
                        {{ config.popup_textarea_button }}
                    </button>
                    <button type="button" class="trp-ald-preview__bar-close" :aria-label="closeLabel">
                        <span class="trp-ald-preview__bar-close-icon" aria-hidden="true" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.trp-ald-preview {
    box-sizing: border-box;
    width: 100%;
    height: 280px;
    border: 1px solid #e2e2e4;
    background-image: var(--trp-ald-preview-bg-image);
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    overflow: hidden;
    border-radius: var(--trp-settings-radius-small);
}

.trp-ald-preview__overlay {
    box-sizing: border-box;
    height: 100%;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding: 32px 20px;
    background: var(--trp-ald-preview-overlay);
}

.trp-ald-preview__bar-stage {
    box-sizing: border-box;
    height: 100%;
    display: flex;
    align-items: flex-start;
    background: transparent;
}

.trp-ald-preview__empty {
    min-height: 280px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    text-align: center;
    color: #50575e;
}

.trp-ald-preview__popup,
.trp-ald-preview__bar {
    box-sizing: border-box;
    background: var(--trp-ald-preview-bg);
    color: var(--trp-ald-preview-text);
    border: var(--trp-ald-preview-border-width) solid var(--trp-ald-preview-border);
    border-radius: var(--trp-ald-preview-radius);
    box-shadow: 0 4px 16px rgba(29, 35, 39, 0.18);
}

.trp-ald-preview__popup {
    width: fit-content;
    max-width: 100%;
    padding: 24px;
}

.trp-ald-preview:not(.is-compact) .trp-ald-preview__popup {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.trp-ald-preview__text {
    font-size: 15px;
    line-height: 1.5;
}

.trp-ald-preview__controls {
    display: flex;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 18px;
}

.trp-ald-preview:not(.is-compact) .trp-ald-preview__controls :deep(.trp-ald-language-selector-wrapper) {
    flex: 0 1 auto;
    width: max-content;
    min-width: 0;
    max-width: 58%;
}

.trp-ald-preview:not(.is-compact) .trp-ald-preview__controls,
.trp-ald-preview:not(.is-compact) .trp-ald-preview__close {
    margin-top: 0;
}

.trp-ald-preview__button {
    box-sizing: border-box;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: auto;
    min-height: 42px;
    padding: 8px 16px;
    border: 1px solid var(--trp-ald-preview-button-border);
    border-radius: var(--trp-ald-preview-button-radius);
    background: var(--trp-ald-preview-button-bg);
    color: var(--trp-ald-preview-button-text);
    cursor: pointer;
    line-height: 1.25;
    white-space: normal;
    overflow-wrap: anywhere;
}

.trp-ald-preview:not(.is-compact) .trp-ald-preview__button {
    box-sizing: border-box;
    flex: 0 1 auto;
    width: fit-content;
    min-width: 0;
    max-width: 100%;
}

.trp-ald-preview__button:hover {
    background: var(--trp-ald-preview-button-hover);
    border-color: var(--trp-ald-preview-button-hover);
    color: var(--trp-ald-preview-button-hover-text);
}

.trp-ald-preview__close,
.trp-ald-preview__bar-close {
    color: var(--trp-ald-preview-close);
    background: transparent;
    border: 0;
    cursor: pointer;
}

.trp-ald-preview__close {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-top: 10px;
    padding: 0;
    color: var(--trp-ald-preview-close);
    font-size: 14px;
    line-height: 20px;
    text-decoration: none;
    transition: opacity 0.2s ease;
}

.trp-ald-preview__close:hover,
.trp-ald-preview__close:focus-visible {
    opacity: 0.7;
}

:global(#tp-ald-popup-configurator-root .trp-ald-preview__close) {
    padding: 0;
    min-height: 0;
    background: transparent;
    border: 0;
    box-shadow: none;
    appearance: none;
}

.trp-ald-preview__close-icon-before {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 16px;
    width: 16px;
    height: 20px;
    color: transparent;
}

.trp-ald-preview__close-icon-before::before {
    content: "\f153";
    font: normal 16px/20px dashicons;
    color: var(--trp-ald-preview-close);
    background: transparent;
    -webkit-font-smoothing: antialiased;
}

.trp-ald-preview__close-text {
    text-decoration: underline;
    color: var(--trp-ald-preview-close);
}

.trp-ald-preview__bar {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: 14px;
    min-height: 76px;
    padding: 18px;
    border-radius: 0;
}

.trp-ald-preview__animated {
    --trp-ald-preview-animation-distance: 72px;
    animation-duration: 0.28s;
    animation-fill-mode: both;
    animation-timing-function: ease-out;
}

.trp-ald-animation-none {
    animation: none;
}

.trp-ald-animation-fade {
    animation-name: trp-ald-preview-fade-in;
}

.trp-ald-animation-slide_top {
    animation-name: trp-ald-preview-slide-from-top;
}

.trp-ald-animation-slide_right {
    animation-name: trp-ald-preview-slide-from-right;
}

.trp-ald-animation-slide_bottom {
    animation-name: trp-ald-preview-slide-from-bottom;
}

.trp-ald-animation-slide_left {
    animation-name: trp-ald-preview-slide-from-left;
}

@keyframes trp-ald-preview-fade-in {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes trp-ald-preview-slide-from-top {
    from {
        opacity: 0;
        transform: translateY(calc(-1 * var(--trp-ald-preview-animation-distance)));
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes trp-ald-preview-slide-from-right {
    from {
        opacity: 0;
        transform: translateX(var(--trp-ald-preview-animation-distance));
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes trp-ald-preview-slide-from-bottom {
    from {
        opacity: 0;
        transform: translateY(var(--trp-ald-preview-animation-distance));
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes trp-ald-preview-slide-from-left {
    from {
        opacity: 0;
        transform: translateX(calc(-1 * var(--trp-ald-preview-animation-distance)));
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.trp-ald-preview__bar .trp-ald-preview__text {
    width: 100%;
}

.trp-ald-preview__bar-controls {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    flex: 0 0 auto;
    flex-wrap: wrap;
    min-width: 0;
}

.trp-ald-preview:not(.is-compact) .trp-ald-preview__bar-controls :deep(.trp-ald-language-selector-wrapper) {
    flex: 0 1 auto;
    width: max-content;
    min-width: 0;
}

.trp-ald-preview:not(.is-compact) .trp-ald-preview__bar-controls .trp-ald-preview__button {
    min-height: 42px;
}

.trp-ald-preview:not(.is-compact) .trp-ald-preview__bar-controls :deep(.trp-ald-language-selector) {
    min-height: 42px;
}

.trp-ald-preview__bar-close {
    padding: 0;
    width: 40px;
    height: 42px;
    min-height: 42px;
}

.trp-ald-preview__bar-close-icon::before {
    content: "\f153";
    font: normal 35px/40px dashicons;
    color: var(--trp-ald-preview-close);
    background: transparent;
    -webkit-font-smoothing: antialiased;
}

.trp-ald-preview.is-compact {
    position: relative;
    width: 100%;
    height: 100%;
    min-height: 0;
    border: 0;
    border-radius: 0;
    overflow: visible;
}

.trp-ald-preview.is-compact .trp-ald-preview__overlay {
    position: absolute;
    inset: 0;
    height: auto;
    align-items: center;
    padding: 8px;
}

.trp-ald-preview.is-compact .trp-ald-preview__popup {
    width: fit-content;
    max-width: 100%;
    padding: 8px 10px;
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.trp-ald-preview.is-compact .trp-ald-preview__text {
    font-size: 11px;
    line-height: 1.2;
}

.trp-ald-preview.is-compact .trp-ald-preview__controls {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    align-items: flex-start;
    gap: 6px;
    margin-top: 6px;
    min-width: 0;
}

.trp-ald-preview.is-compact .trp-ald-preview__button {
    box-sizing: border-box;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 1 auto;
    width: fit-content;
    height: auto;
    max-width: 100%;
    min-width: 0;
    min-height: 30px;
    padding: 0 8px;
    font-size: 11px;
    line-height: 1.2;
    overflow: hidden;
    text-overflow: clip;
}

.trp-ald-preview.is-compact :deep(.trp-ald-language-selector .trp-language-item-name) {
    line-height: 1.4;
}

.trp-ald-preview.is-compact .trp-ald-preview__close {
    margin-top: 4px;
    font-size: 11px;
    line-height: 16px;
}

.trp-ald-preview.is-compact .trp-ald-preview__bar {
    min-height: 62px;
    padding: 10px;
    gap: 8px;
}

.trp-ald-preview.is-compact .trp-ald-preview__bar-controls {
    align-items: flex-start;
    gap: 6px;
    width: 100%;
}

.trp-ald-preview.is-compact .trp-ald-preview__bar-close {
    flex: 0 0 30px;
    width: 30px;
    height: 30px;
    min-height: 30px;
}

.trp-ald-preview.is-compact .trp-ald-preview__bar-close-icon::before {
    font: normal 24px/30px dashicons;
}

@media (prefers-reduced-motion: reduce) {
    .trp-ald-preview__animated {
        animation: none;
    }
}

.trp-ald-preview.is-compact :deep(.trp-ald-language-selector-wrapper) {
    flex: 0 1 auto;
    width: max-content;
    min-width: 0;
    max-width: 100%;
    --font-size: 11px;
    --flag-size: 14px;
    --trp-ald-preview-language-item-height: 28px;
}

.trp-ald-preview.is-compact :deep(.trp-ald-language-selector) {
    min-height: 30px;
}

.trp-ald-preview.is-compact :deep(.trp-ald-language-selector .trp-language-item) {
    min-height: 28px;
    padding: 0 8px;
}
</style>

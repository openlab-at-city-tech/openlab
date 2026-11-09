<script setup>
import { computed, provide, reactive } from 'vue'
import { __ } from '@wordpress/i18n'

import SettingsBox from '../shared/components/SettingsBox.vue'
import SettingsActions from '../shared/components/SettingsActions.vue'
import PresetApplier from '../shared/components/PresetApplier.vue'
import { useSettingsPersistence } from '../shared/composables/useSettingsPersistence'
import PopupPreview from './components/PopupPreview.vue'
import { popupPresets } from './presets/popupPresets'

const payload = window.tpAldConfiguratorData || {}
const cfg = reactive(payload.config || {})
const previewBgUrl = computed(() => `url(${payload.misc?.pluginUrl || ''}assets/images/switcher-preview-bg.png)`)
const languages = computed(() => payload.languages || {})

const persistence = useSettingsPersistence(cfg, {
    action: 'trp_ald_popup_config_save',
    nonce: payload.nonce,
})

provide('settingsPersistence', persistence)

const T = {
    preview: __('Popup Preview', 'translatepress-multilingual'),
    detectionMethod: __('User Language Detection Method', 'translatepress-multilingual'),
    notificationBehavior: __('Notification Behavior', 'translatepress-multilingual'),
    popupContent: __('Popup Content', 'translatepress-multilingual'),
    applyPreset: __('Apply a preset', 'translatepress-multilingual'),
    languageSwitcher: __('Language Switcher', 'translatepress-multilingual'),
    customizeDesign: __('Customize Design', 'translatepress-multilingual'),
    languageDetection: __('Language detection', 'translatepress-multilingual'),
    detectionDescription: __("Select how the language should be detected for first time visitors.<br>The visitor's last displayed language will be remembered through cookies.", 'translatepress-multilingual'),
    popupDisplay: __('Popup display', 'translatepress-multilingual'),
    popupLanguages: __('Conditionally display popup based on language', 'translatepress-multilingual'),
    contentRestrictionMode: __('Content Restriction Mode', 'translatepress-multilingual'),
    include: __('Include', 'translatepress-multilingual'),
    exclude: __('Exclude', 'translatepress-multilingual'),
    selectLanguage: __('Select language', 'translatepress-multilingual'),
    noLanguagesSelected: __('No languages selected', 'translatepress-multilingual'),
    popupType: __('Popup Type', 'translatepress-multilingual'),
    noPopupPreview: __('Visitors will be redirected directly when a different preferred language is detected.', 'translatepress-multilingual'),
    popupText: __('Popup Text', 'translatepress-multilingual'),
    buttonText: __('Button Text', 'translatepress-multilingual'),
    closeButtonText: __('Close Button Text', 'translatepress-multilingual'),
    backgroundColor: __('Background color', 'translatepress-multilingual'),
    textColor: __('Text color', 'translatepress-multilingual'),
    buttonBackgroundColor: __('Button background color', 'translatepress-multilingual'),
    buttonHoverColor: __('Button hover color', 'translatepress-multilingual'),
    buttonTextColor: __('Button text color', 'translatepress-multilingual'),
    buttonBorderRadius: __('Button border radius', 'translatepress-multilingual'),
    closeTextColor: __('Close icon color', 'translatepress-multilingual'),
    borderColor: __('Border color', 'translatepress-multilingual'),
    borderWidth: __('Border width', 'translatepress-multilingual'),
    borderRadius: __('Border radius', 'translatepress-multilingual'),
    overlayColor: __('Overlay color', 'translatepress-multilingual'),
    entranceAnimation: __('Entrance animation', 'translatepress-multilingual'),
    animation: __('Animation', 'translatepress-multilingual'),
    fade: __('Fade', 'translatepress-multilingual'),
    slideTop: __('Slide from top', 'translatepress-multilingual'),
    slideRight: __('Slide from right', 'translatepress-multilingual'),
    slideBottom: __('Slide from bottom', 'translatepress-multilingual'),
    slideLeft: __('Slide from left', 'translatepress-multilingual'),
    customCss: __('Enable custom CSS', 'translatepress-multilingual'),
    languageNames: __('Language names', 'translatepress-multilingual'),
    fullNames: __('Full names', 'translatepress-multilingual'),
    shortNames: __('Short names', 'translatepress-multilingual'),
    noNames: __('No names', 'translatepress-multilingual'),
    showFlags: __('Show flags', 'translatepress-multilingual'),
    flagShape: __('Flag shape', 'translatepress-multilingual'),
    rectangle: __('Rectangle (4:3)', 'translatepress-multilingual'),
    square: __('Square (1:1)', 'translatepress-multilingual'),
    flagRadius: __('Flag border radius', 'translatepress-multilingual'),
    switcherBackground: __('Switcher background', 'translatepress-multilingual'),
    switcherHoverBackground: __('Switcher hover background', 'translatepress-multilingual'),
    switcherText: __('Switcher text', 'translatepress-multilingual'),
    switcherHoverText: __('Switcher hover text', 'translatepress-multilingual'),
    switcherBorder: __('Switcher border', 'translatepress-multilingual'),
    switcherBorderWidth: __('Switcher border width', 'translatepress-multilingual'),
    switcherBorderRadius: __('Switcher border radius', 'translatepress-multilingual'),
}

const detectionOptions = computed(() => payload.detectionOptions || [
    { value: 'browser-ip', label: __('First by browser language, then IP address (recommended)', 'translatepress-multilingual') },
])

const popupOptions = computed(() => payload.popupOptions || [
    { value: 'popup', label: __('A popup appears asking the user if they want to be redirected', 'translatepress-multilingual') },
])

const popupTypeOptions = computed(() => payload.popupTypeOptions || [
    { value: 'normal_popup', label: __('Pop-up window over the content', 'translatepress-multilingual') },
    { value: 'hello_bar', label: __('Hello bar before the content', 'translatepress-multilingual') },
])

const languageOptions = computed(() => {
    const published = languages.value?.published || {}
    const pluginUrl = payload.misc?.pluginUrl || ''

    return Object.entries(published).map(([value, settings]) => ({
        value,
        label: settings.name || value,
        flagUrl: settings.flagPath || `${pluginUrl}assets/flags/4x3/${encodeURIComponent(String(value).replace(/-/g, '_') + '.svg')}`,
    }))
})

const notificationBehaviorFields = computed(() => [
    {
        key: 'popup_option',
        type: 'radio',
        label: T.popupDisplay,
        options: popupOptions.value,
        layout: 'column',
        visible: () => popupOptions.value.length > 1
    },
    {
        key: 'popup_type',
        type: 'radio',
        label: T.popupType,
        options: popupTypeOptions.value,
        layout: 'column',
        visible: cfg => cfg.popup_option === 'popup'
    },
    {
        key: 'popupLanguageConditionEnabled',
        type: 'languageCondition',
        label: T.popupLanguages,
        default: false,
        modeKey: 'popupLanguageConditionMode',
        languagesKey: 'popup_languages',
        options: languageOptions.value,
        modeLabel: T.contentRestrictionMode,
        includeLabel: T.include,
        excludeLabel: T.exclude,
        selectLabel: T.selectLanguage,
        emptyLabel: T.noLanguagesSelected,
        layout: 'column',
        visible: cfg => cfg.popup_option === 'popup'
    }
])

function getPresetSettings(preset, targetConfig = cfg) {
    const settings = { ...(preset.settings || {}) }

    if (preset.id === 'default' && targetConfig.popup_type === 'hello_bar')
        settings.borderWidth = 0

    return settings
}

function applyPopupPreset(preset, targetConfig) {
    Object.assign(targetConfig, getPresetSettings(preset, targetConfig))
}

function getPresetPreviewProps(previewConfig, preset) {
    const nextConfig = {
        ...previewConfig,
        ...getPresetSettings(preset, previewConfig),
    }

    return {
        emptyPreviewText: T.noPopupPreview,
        backgroundImage: previewBgUrl.value,
        compact: true,
        languages: languages.value,
        config: nextConfig,
    }
}

const customizeDesignFields = computed(() => [
    { key: 'backgroundColor', type: 'color', label: T.backgroundColor },
    { key: 'textColor', type: 'color', label: T.textColor },
    { key: 'closeTextColor', type: 'color', label: T.closeTextColor },
    { key: 'borderColor', type: 'color', label: T.borderColor },
    { key: 'borderWidth', type: 'number', label: T.borderWidth },
    { key: 'borderRadius', type: 'number', label: T.borderRadius },
    { key: 'overlayColor', type: 'color', label: T.overlayColor, visible: cfg => cfg?.popup_type !== 'hello_bar' },
    { type: 'separator' },
    { key: 'buttonBackgroundColor', type: 'color', label: T.buttonBackgroundColor },
    { key: 'buttonHoverColor', type: 'color', label: T.buttonHoverColor },
    { key: 'buttonTextColor', type: 'color', label: T.buttonTextColor },
    { key: 'buttonBorderRadius', type: 'number', label: T.buttonBorderRadius },
    { type: 'separator' },
    { key: 'popupAnimationEnabled', type: 'toggle', label: T.entranceAnimation, default: true },
    {
        key: 'popupAnimation',
        type: 'select',
        label: T.animation,
        default: 'fade',
        options: [
            { value: 'fade', label: T.fade },
            { value: 'slide_top', label: T.slideTop },
            { value: 'slide_right', label: T.slideRight },
            { value: 'slide_bottom', label: T.slideBottom },
            { value: 'slide_left', label: T.slideLeft }
        ],
        visible: cfg => cfg?.popupAnimationEnabled !== false
    },
    { type: 'separator' },
    { key: 'enableCustomCss', type: 'toggle', label: T.customCss, default: false },
    { key: 'customCss', type: 'customCss', label: '', scopeSelector: '.trp-ald-custom-popup', visible: cfg => cfg?.enableCustomCss === true }
])
</script>

<template>
    <div class="trp-ald-configurator">
        <div class="trp-ald-configurator__main">
            <div class="trp-ald-configurator__left">
                <div class="trp-ald-sticky-box">
                    <SettingsBox
                        :title="T.preview"
                        :config="cfg"
                    >
                        <PopupPreview
                            :config="cfg"
                            :empty-preview-text="T.noPopupPreview"
                            :background-image="previewBgUrl"
                            :languages="languages"
                        />
                    </SettingsBox>

                    <SettingsActions />
                </div>
            </div>

            <div class="trp-ald-configurator__right">

            <SettingsBox
                :title="T.notificationBehavior"
                :config="cfg"
                :fields="notificationBehaviorFields"
            />

            <SettingsBox
                :title="T.popupContent"
                :config="cfg"
                v-if="cfg.popup_option === 'popup'"
                :style="{ '--trp-field-label-width': '160px' }"
                :fields="[
                    {
                        key: 'popup_textarea',
                        type: 'textarea',
                        label: T.popupText
                    },
                    {
                        key: 'popup_textarea_button',
                        type: 'text',
                        label: T.buttonText
                    },
                    {
                        key: 'popup_textarea_close_button',
                        type: 'text',
                        label: T.closeButtonText
                    }
                ]"
            />

            <SettingsBox
                :title="T.applyPreset"
                :config="cfg"
                v-if="cfg.popup_option === 'popup'"
            >
                <PresetApplier
                    :config="cfg"
                    :presets="popupPresets"
                    :preview-component="PopupPreview"
                    :apply-preset="applyPopupPreset"
                    :get-preview-props="getPresetPreviewProps"
                    :preview-props="{
                        emptyPreviewText: T.noPopupPreview,
                        backgroundImage: previewBgUrl,
                        compact: true,
                        languages
                    }"
                />
            </SettingsBox>

            <SettingsBox
                :title="T.languageSwitcher"
                :config="cfg"
                v-if="cfg.popup_option === 'popup'"
                collapsible
                :style="{ '--trp-field-label-width': '190px' }"
                :fields="[
                    {
                        key: 'aldSwitcherLanguageNames',
                        type: 'radio',
                        label: T.languageNames,
                        default: 'full',
                        options: [
                            { value: 'full', label: T.fullNames },
                            { value: 'short', label: T.shortNames },
                            { value: 'none', label: T.noNames }
                        ],
                        layout: 'column'
                    },
                    { key: 'aldSwitcherShowFlags', type: 'toggle', label: T.showFlags, default: true, compact: true },
                    {
                        key: 'aldSwitcherFlagShape',
                        type: 'radio',
                        label: T.flagShape,
                        default: 'rect',
                        options: [
                            { value: 'rect', label: T.rectangle },
                            { value: 'square', label: T.square }
                        ],
                        layout: 'column',
                        visible: cfg => cfg?.aldSwitcherShowFlags !== false
                    },
                    { key: 'aldSwitcherFlagRadius', type: 'number', label: T.flagRadius, default: 2, visible: cfg => cfg?.aldSwitcherShowFlags !== false },
                    { type: 'separator' },
                    { key: 'aldSwitcherBackgroundColor', type: 'color', label: T.switcherBackground },
                    { key: 'aldSwitcherBackgroundHoverColor', type: 'color', label: T.switcherHoverBackground },
                    { key: 'aldSwitcherTextColor', type: 'color', label: T.switcherText },
                    { key: 'aldSwitcherTextHoverColor', type: 'color', label: T.switcherHoverText },
                    { type: 'separator' },
                    { key: 'aldSwitcherBorderColor', type: 'color', label: T.switcherBorder },
                    { key: 'aldSwitcherBorderWidth', type: 'number', label: T.switcherBorderWidth },
                    { key: 'aldSwitcherBorderRadius', type: 'number', label: T.switcherBorderRadius }
                ]"
            />

            <SettingsBox
                :title="T.customizeDesign"
                :config="cfg"
                v-if="cfg.popup_option === 'popup'"
                collapsible
                :style="{ '--trp-field-label-width': '190px' }"
                :fields="customizeDesignFields"
            />

            <SettingsBox
                :title="T.detectionMethod"
                :config="cfg"
                collapsible
                :fields="[
                    {
                        key: 'detection-method',
                        type: 'radio',
                        label: T.languageDetection,
                        options: detectionOptions,
                        columns: 2,
                        layout: 'column'
                    }
                ]"
            >
                <template #end>
                    <span class="trp-description-text" v-html="T.detectionDescription" />
                    <div
                        v-if="payload.ipWarningMessage"
                        class="trp-settings-warning"
                        v-html="payload.ipWarningMessage"
                    />
                </template>
            </SettingsBox>
            </div>
        </div>
    </div>
</template>

<style scoped>
.trp-ald-configurator {
    display: flex;
    flex-direction: column;
    gap: 16px;
    max-width: 1280px;
    width: 100%;
}

.trp-ald-configurator__main {
    display: flex;
    gap: 16px;
    width: 100%;
}

.trp-ald-configurator__left {
    min-width: 0;
    width: 40%;
}

.trp-ald-configurator__right {
    display: flex;
    flex-direction: column;
    gap: 16px;
    min-width: 0;
    width: 60%;
}

.trp-ald-sticky-box {
    position: sticky;
    top: 50px;
}

.trp-ald-configurator :deep(.trp-settings-warning) {
    width: 100%;
    max-width: none;
}
</style>

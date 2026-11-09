<script setup>
import { computed, provide } from 'vue'
import { onBeforeRouteLeave } from 'vue-router'

import SettingsBox from '../../shared/components/SettingsBox.vue'
import SettingsActions from '../../shared/components/SettingsActions.vue'
import LayoutCustomizerField from '../components/fields/LayoutCustomizerField.vue'
import LanguageSwitcherPreview from '../components/LanguageSwitcherPreview.vue'
import ShortcodeCopy from '../components/ShortcodeCopy.vue'

import { useLanguages, useSwitcherConfig } from '../composables/useSwitcherConfig'
import { useSwitcherPersistence }          from '../composables/useSwitcherPersistance'

import { useLayoutCustomizer } from "../composables/layoutCustomizer/useLayoutCustomizer"
import { __ }                  from '@wordpress/i18n'

const scope = 'shortcode'
const cfg = useSwitcherConfig(scope)
const persistence = useSwitcherPersistence(scope)
provide('settingsPersistence', persistence)

const { isDirty, revert } = persistence
const { positioning } = useLayoutCustomizer(scope)

const flagRatio = computed(() => cfg.flagShape)

const languageItemSettings = computed(() => ({
    flagPos: positioning.value.flagPos,
    nameMode: positioning.value.languageNames,
    flagRatio: flagRatio
}))
provide('languageItemSettings', languageItemSettings)

const oppositeLanguageFieldDescription = __(
    'Transforms the language switcher into a button showing the other available language, not the current one. <br> Only works when there are exactly two languages, the default one and a translation one.',
    'translatepress-multilingual'
)

const { published } = useLanguages()

const publishedLangCount = Object.keys(published).length

const aboveTwoLanguages = publishedLangCount > 2

const aboveTwoLanguagesTitle = aboveTwoLanguages
    ? __('This option only works when exactly two languages are published.', 'translatepress-multilingual')
    : ''

const T = {
    confirmLeave: __('You have unsaved changes. Leave anyway?', 'translatepress-multilingual'),
    switcherPreview: __('Switcher Preview', 'translatepress-multilingual'),
    shortcode: __('Shortcode', 'translatepress-multilingual'),
    customizeDesign: __('Customize Design', 'translatepress-multilingual'),
    backgroundColor: __('Background color', 'translatepress-multilingual'),
    backgroundHoverColor: __('Background hover color', 'translatepress-multilingual'),
    textColor: __('Text color', 'translatepress-multilingual'),
    textHoverColor: __('Text hover color', 'translatepress-multilingual'),
    switcherBorderColor: __('Switcher border color', 'translatepress-multilingual'),
    switcherBorderWidth: __('Switcher border width', 'translatepress-multilingual'),
    switcherBorderRadius: __('Switcher border radius', 'translatepress-multilingual'),
    flagTextSize: __('Flag and text size', 'translatepress-multilingual'),
    normal: __('Normal', 'translatepress-multilingual'),
    large: __('Large', 'translatepress-multilingual'),
    flagIconsShape: __('Flag icons shape', 'translatepress-multilingual'),
    rectangle: __('Rectangle (4:3)', 'translatepress-multilingual'),
    square: __('Square (1:1)', 'translatepress-multilingual'),
    flagRadius: __('Flag icons border radius', 'translatepress-multilingual'),
    enableCustomCss: __('Enable custom CSS', 'translatepress-multilingual'),
    customizeLayout: __('Customize Layout', 'translatepress-multilingual'),
    additionalSettings: __('Additional Settings', 'translatepress-multilingual'),
    openOnClick: __('Open language switcher only on click', 'translatepress-multilingual'),
    clickLangDesc: __('Open the language switcher shortcode by clicking on it instead of hovering. <br> Close it by clicking on it, anywhere else on the screen or by pressing the escape key. <br> This will affect only the shortcode language switcher.', 'translatepress-multilingual'),
    enableTransitions: __( 'Switcher animations', 'translatepress-multilingual' ),
    showOppositeLanguage: __('Show opposite language', 'translatepress-multilingual'),
}

const fieldComponents = {
    lCustomizer: LayoutCustomizerField,
}

onBeforeRouteLeave((_to, _from, next) => {
    if (!isDirty.value) return next()
    const confirmLeave = window.confirm(T.confirmLeave)
    confirmLeave ? (revert(), next()) : next(false)
})
</script>

<template>
    <div class="trp-floater-settings__wrapper">
        <div class="trp-floater-settings__left">
            <div class="trp-sticky-box">
                <SettingsBox :title="T.switcherPreview" :scope="scope">
                    <LanguageSwitcherPreview :scope="scope" />
                </SettingsBox>

                <SettingsActions />
            </div>
        </div>

        <div class="trp-floater-settings__right">
            <SettingsBox :title="T.shortcode" :scope="scope">
                <ShortcodeCopy />
            </SettingsBox>

            <SettingsBox
                :title="T.customizeDesign"
                :scope="scope"
                collapsible
                :style="{ '--trp-field-label-width': '190px' }"
                :fields="[
                    { key: 'bgColor',        type: 'color', label: T.backgroundColor,       default: '#ffffff' },
                    { key: 'bgHoverColor',   type: 'color', label: T.backgroundHoverColor,  default: '#f5f5f5' },
                    { key: 'textColor',      type: 'color', label: T.textColor,             default: '#1D2327' },
                    { key: 'textHoverColor', type: 'color', label: T.textHoverColor,        default: '#000000' },
                    { key: 'borderColor',    type: 'color', label: T.switcherBorderColor,   default: '#1438521A' },
                    { key: 'borderWidth',    type: 'number',label: T.switcherBorderWidth,   default: 1 },
                    { key: 'borderRadius',   type: 'number',label: T.switcherBorderRadius,  default: 5 },
                    { type: 'separator' },
                    {
                        key: 'enableTransitions',
                        type: 'toggle',
                        label: T.enableTransitions,
                        default: true
                    },
                    { type: 'separator' },
                    {
                        key: 'size',
                        type: 'radio',
                        label: T.flagTextSize,
                        default: 'normal',
                        options: [
                            { value: 'normal', label: T.normal },
                            { value: 'large',  label: T.large  }
                        ],
                        layout: 'column'
                    },
                    { type: 'separator' },
                    {
                        key: 'flagShape',
                        type: 'radio',
                        label: T.flagIconsShape,
                        default: 'rect',
                        options: [
                            { value: 'rect',   label: T.rectangle },
                            { value: 'square', label: T.square    }
                        ],
                        layout: 'column'
                    },
                    { key: 'flagRadius', type: 'number', label: T.flagRadius, default: 2 },
                    { type: 'separator' },
                    { key: 'enableCustomCss', type: 'toggle', label: T.enableCustomCss, default: false },
                    { key: 'customCss', type: 'customCss', label: '', visible: cfg => cfg?.enableCustomCss === true }
                ]"
            />

            <SettingsBox
                :title="T.customizeLayout"
                :scope="scope"
                collapsible
                :components="fieldComponents"
                :fields="[
                    { key: 'layoutCustomizer', type: 'lCustomizer', label: '' }
                ]"
            />

            <SettingsBox
                :title="T.additionalSettings"
                :scope="scope"
                collapsible
                :fields="[
                    {
                        key: 'clickLanguage',
                        type: 'checkbox',
                        default: false,
                        label: T.openOnClick,
                        description: T.clickLangDesc
                    },
                    {
                        key: 'oppositeLanguage',
                        type: 'checkbox',
                        default: false,
                        label: T.showOppositeLanguage,
                        description: oppositeLanguageFieldDescription,
                        disabled: aboveTwoLanguages,
                        title: aboveTwoLanguagesTitle
                    }
                ]"
            />
        </div>
    </div>
</template>

<style scoped>
/* Re-use the same structural styles as the Floater view for consistency */
.trp-floater-settings__wrapper {
    display:flex;
    flex-direction:row;
    gap:16px;
    max-width:1280px;
    width:100%;
}

.trp-floater-settings__right {
    display:flex;
    flex-direction:column;
    gap:16px;
    width:60%;
}

.trp-floater-settings__left {
    width:40%;
}

.trp-sticky-box {
    position:sticky; top:50px;
}
</style>

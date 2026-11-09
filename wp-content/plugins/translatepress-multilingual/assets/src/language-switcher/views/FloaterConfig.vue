<script setup>
import SettingsBox                         from '../../shared/components/SettingsBox.vue'
import PresetApplier                       from '../../shared/components/PresetApplier.vue'
import LanguageSwitcherPreview             from "../components/LanguageSwitcherPreview.vue"
import PresetPreviewLanguageSwitcher       from "../components/PresetPreviewLanguageSwitcher.vue"
import SettingsActions                     from "../../shared/components/SettingsActions.vue"
import LayoutCustomizerField               from "../components/fields/LayoutCustomizerField.vue"
import { switcherPresets, switcherPresetCssVars } from '../presets/switcherPresets'

import { useLanguages, useSwitcherConfig } from "../composables/useSwitcherConfig"
import { useLayoutCustomizer }             from "../composables/layoutCustomizer/useLayoutCustomizer"
import { useSwitcherPersistence }          from '../composables/useSwitcherPersistance'

import { computed, provide, watch } from "vue"
import { onBeforeRouteLeave }       from 'vue-router'

import { __ } from "@wordpress/i18n"

const scope = "floater"

const cfg = useSwitcherConfig( scope )

const persistence = useSwitcherPersistence( scope )

provide( 'settingsPersistence', persistence )

const { isDirty, revert } = persistence

const { positioning, switcherPosition } = useLayoutCustomizer( scope )

const flagRatio = computed( () => cfg.flagShape )

const languageItemSettings = computed(() => ({
    flagPos: positioning.value.flagPos,
    nameMode: positioning.value.languageNames,
    flagRatio: flagRatio
}))

const invertRadius = ( r ) => {
    if ( !Array.isArray( r ) || r.length !== 4 )
        return r

    const [tl, tr, br, bl] = r

    return [bl, br, tr, tl]
}

const getVertical = ( pos ) => {
    if ( typeof pos !== 'string' )
        return null

    return pos.startsWith( 'top' ) ? 'top' : 'bottom'
}

watch(
    () => switcherPosition.value,
    ( newPos, oldPos ) => {
        const newVertical = getVertical( newPos )
        const oldVertical = getVertical( oldPos )

        if ( !newVertical || !oldVertical )
            return

        if ( newVertical === oldVertical )
            return

        cfg.borderRadius = invertRadius( cfg.borderRadius )
    }
)
const T = {
    switcherPreview: __('Switcher Preview', 'translatepress-multilingual'),
    enableFloating: __('Enable Floating Switcher', 'translatepress-multilingual'),
    switcherEnabled: __('Switcher is enabled', 'translatepress-multilingual'),
    switcherDisabled: __('Switcher is disabled', 'translatepress-multilingual'),
    switcherType: __('Switcher Type', 'translatepress-multilingual'),
    showAsDropdown: __('Show languages as dropdown', 'translatepress-multilingual'),
    showSideBySide: __('Show languages side by side', 'translatepress-multilingual'),
    applyPreset: __('Apply a preset', 'translatepress-multilingual'),
    customizeDesign: __('Customize Design', 'translatepress-multilingual'),
    backgroundColor: __('Background color', 'translatepress-multilingual'),
    backgroundHoverColor: __('Background hover color', 'translatepress-multilingual'),
    textColor: __('Text color', 'translatepress-multilingual'),
    textHoverColor: __('Text hover color', 'translatepress-multilingual'),
    borderColor: __('Switcher border color', 'translatepress-multilingual'),
    borderWidth: __('Switcher border width', 'translatepress-multilingual'),
    borderRadius: __('Switcher border radius', 'translatepress-multilingual'),
    flagTextSize: __('Flag and text size', 'translatepress-multilingual'),
    normal: __('Normal', 'translatepress-multilingual'),
    large: __('Large', 'translatepress-multilingual'),
    flagIconsShape: __('Flag icons shape', 'translatepress-multilingual'),
    rectangle: __('Rectangle (4:3)', 'translatepress-multilingual'),
    square: __('Square (1:1)', 'translatepress-multilingual'),
    flagRadius: __('Flag icons border radius', 'translatepress-multilingual'),
    enableCustomCss: __('Enable custom CSS', 'translatepress-multilingual'),
    customizeLayout: __('Customize Layout', 'translatepress-multilingual'),
    showOppositeLanguage: __('Show opposite language', 'translatepress-multilingual'),
    showPoweredBy: __('Show "Powered by TranslatePress"', 'translatepress-multilingual'),
    poweredByDesc: __('Show the small Powered by TranslatePress label in the language switcher.', 'translatepress-multilingual'),
    leaveConfirm: __('You have unsaved changes. Leave anyway?', 'translatepress-multilingual'),
    enableTransitions: __( 'Switcher animations', 'translatepress-multilingual' )
}

const fieldComponents = {
    lCustomizer: LayoutCustomizerField,
}

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

onBeforeRouteLeave((to, from, next) => {
    if (!isDirty.value) return next()

    const ok = window.confirm(__('You have unsaved changes. Leave anyway?', 'translatepress-multilingual'))

    if (ok) {
        revert()
        next()
    }

    else
        next(false)
})

provide('languageItemSettings', languageItemSettings)
</script>

<template>
    <div class="trp-floater-settings__wrapper">
        <div class="trp-floater-settings__left">
            <div class="trp-sticky-box">
                <SettingsBox
                    :title="T.switcherPreview"
                    :scope=scope
                >
                    <LanguageSwitcherPreview :scope="scope"/>
                </SettingsBox>

                <SettingsActions />
            </div>
        </div>
        <div class="trp-floater-settings__right">
            <SettingsBox
                :title="T.enableFloating"
                :scope="scope"
                :style="{ flexDirection: 'row', gap: '75px' }"
                :fields="[{
                    key: 'enabled',
                    type: 'toggleStatus',
                    onText: T.switcherEnabled,
                    offText: T.switcherDisabled
                }]"
            />

            <SettingsBox
                :title="T.switcherType"
                :scope="scope"
                :fields="[{
                    key: 'type',
                    type: 'radio',
                    default: 'dropdown',
                    options: [
                        { value: 'dropdown', label: T.showAsDropdown },
                        { value: 'side-by-side', label: T.showSideBySide, disabled: aboveTwoLanguages, title: aboveTwoLanguagesTitle },
                    ]
                }]"
            />

            <SettingsBox
                :title="T.applyPreset"
                :scope="scope"
            >
                <PresetApplier
                    :config="cfg"
                    :presets="switcherPresets"
                    :preview-component="PresetPreviewLanguageSwitcher"
                    preview-config-prop=""
                    :preview-props="{ scope }"
                    :get-preview-style="switcherPresetCssVars"
                />
            </SettingsBox>

            <SettingsBox
                :title="T.customizeDesign"
                :scope="scope"
                collapsible
                :style="{ '--trp-field-label-width': '190px' }"
                :fields="[
                    { key:  'bgColor',        type: 'color',      label: T.backgroundColor,        default: '#ffffff' },
                    { key:  'bgHoverColor',   type: 'color',      label: T.backgroundHoverColor,   default: '#f5f5f5' },
                    { key:  'textColor',      type: 'color',      label: T.textColor,              default: '#000000' },
                    { key:  'textHoverColor', type: 'color',      label: T.textHoverColor,         default: '#000000' },
                    { key:  'borderColor',    type: 'color',      label: T.borderColor,            default: '#e2e2e4' },
                    { key:  'borderWidth',    type: 'number',     label: T.borderWidth,            default: 1 },
                    { key:  'borderRadius',   type: 'quadNumber', label: T.borderRadius,           default: [8, 8, 0 ,0], layout: 'column' },
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
                    { key: 'flagRadius',      type: 'number', label: T.flagRadius, default: 2 },
                    { type: 'separator' },
                    { key: 'enableCustomCss', type: 'toggle', label: T.enableCustomCss, default: false },
                    { key: 'customCss',       type: 'customCss', label: '', visible: cfg => cfg?.enableCustomCss === true }
                ]"
            />

            <SettingsBox
                :title="T.customizeLayout"
                :scope="scope"
                collapsible
                :fields="[{
                    key: 'layoutCustomizer',
                    type: 'lCustomizer',
                    label: ''
                }]"
                :components="fieldComponents"
            />

            <SettingsBox
                title="Additional Settings"
                :scope="scope"
                collapsible
                :fields="[{
                    key: 'oppositeLanguage',
                    type: 'checkbox',
                    default: false,
                    label: T.showOppositeLanguage,
                    description: oppositeLanguageFieldDescription,
                    disabled: aboveTwoLanguages,
                    title: aboveTwoLanguagesTitle
                },
                {
                    key: 'showPoweredBy',
                    type: 'checkbox',
                    default: false,
                    label: T.showPoweredBy,
                    description: T.poweredByDesc
                }]"
            />
        </div>
    </div>
</template>

<style scoped>
.trp-floater-settings__wrapper {
    display: flex;
    flex-direction: row;
    gap: 16px;
    max-width: 1280px;
    width: 100%;
}

.trp-floater-settings__right {
    display: flex;
    flex-direction: column;
    gap: 16px;
    width: 60%;
}

.trp-floater-settings__left {
    width: 40%;
}

.trp-sticky-box {
    position: sticky;
    top: 50px
}
</style>

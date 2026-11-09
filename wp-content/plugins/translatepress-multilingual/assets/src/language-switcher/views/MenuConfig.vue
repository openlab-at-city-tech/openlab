<script setup>
import { provide } from 'vue'
import { onBeforeRouteLeave } from 'vue-router'

import SettingsBox from '../../shared/components/SettingsBox.vue'
import SettingsActions from '../../shared/components/SettingsActions.vue'
import LayoutCustomizerField from '../components/fields/LayoutCustomizerField.vue'

import { useSwitcherConfig } from '../composables/useSwitcherConfig'
import { useSwitcherPersistence } from '../composables/useSwitcherPersistance'

import { __ } from '@wordpress/i18n'
import { sanitizeHtml }  from "../../shared/composables/utils/sanitizeHtml"

const scope = 'menu'
const cfg = useSwitcherConfig(scope)

const persistence = useSwitcherPersistence(scope)
provide('settingsPersistence', persistence)

const { isDirty, revert } = persistence

const T = {
    menuSwitcherLayout: __('Menu Switcher Layout', 'translatepress-multilingual'),
    unsavedChangesAlert: __('You have unsaved changes. Leave anyway?', 'translatepress-multilingual'),
    //[utm4]
    descriptionText: sanitizeHtml( __('Go to <a href="/wp-admin/nav-menus.php">Appearance → Menus</a> to add languages to the Language Switcher in any menu.<br> <a href="https://translatepress.com/docs/settings/language-switcher/?utm_source=tp-language-switcher&utm_medium=client-site&utm_campaign=ls-menu-item#menu-switcher" target="_blank">Learn more in our documentation.</a>', 'translatepress-multilingual') )
}

const fieldComponents = {
    lCustomizer: LayoutCustomizerField,
}

onBeforeRouteLeave((_to, _from, next) => {
    if (!isDirty.value) return next()
    const ok = window.confirm(T.unsavedChangesAlert)
    ok ? (revert(), next()) : next(false)
})
</script>

<template>
    <div class="trp-menu-settings__wrapper">
        <SettingsBox
            :title="T.menuSwitcherLayout"
            :scope="scope"
            :collapsible="false"
            :fields="[
        {
          key: 'layoutCustomizer',
          type: 'lCustomizer',
          label: ''
        }
      ]"
            :components="fieldComponents"
        >
            <template #end>
                <span class="trp-description-text" v-html="T.descriptionText" />
            </template>
        </SettingsBox>

        <SettingsActions />
    </div>
</template>


<style scoped>
.trp-menu-settings__wrapper {
    max-width: 640px;
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 16px;
}
</style>

<script setup>

import SubMenu from './components/SubMenu.vue'
import { provideSwitcherConfig } from './composables/useSwitcherConfig'

const el = document.getElementById('tp-language-switcher-root')
/** tpLangSwitcherData comes from wp_localize_script */
const { lsConfig: config, languages } = window.tpLangSwitcherData

provideSwitcherConfig( { config, languages } )

</script>

<template>
    <div class="trp-ls-wrapper">
        <SubMenu />
        <main class="trp-ls-view">
            <router-view />
        </main>
    </div>
</template>

<style scoped>
.trp-ls-wrapper {
    display: flex;
    flex-direction: column;
}
.trp-ls-view {
    padding: 0 32px;
    display: flex;
    justify-content: flex-start;
}
</style>

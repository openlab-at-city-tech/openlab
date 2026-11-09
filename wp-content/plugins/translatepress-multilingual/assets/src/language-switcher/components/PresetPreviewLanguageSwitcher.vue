<script setup>
import { useSwitcherPreview } from "../composables/useSwitcherPreview"
import LanguageItem           from "./LanguageItem.vue"

const props = defineProps({
    scope: {
        type: String,
        required: true,
        validator: v => ['floater','shortcode','menu'].includes(v)
    }
})

const { displayedList, isDropdown } = useSwitcherPreview(props.scope)
</script>

<template>
    <div v-if="isDropdown" class="trp-switcher-preview trp-dropdown-preview">
        <LanguageItem
            v-for="lang in displayedList"
            :key="lang.code"
            :language="lang"
            :dropdown="true"
        />
    </div>

    <div v-else class="trp-switcher-preview trp-preview-ls-inline">
        <LanguageItem
            v-for="lang in displayedList"
            :key="lang.code"
            :language="lang"
            :dropdown="false"
        />
    </div>
</template>


<style scoped>
/* DROPDOWN PREVIEW */
.trp-switcher-preview {
    border: 1px solid var(--border-color);
    border-radius: var(--trp-settings-radius-medium);
    background: var(--bg);
    width: fit-content;
    box-shadow: 0 10px 20px 0 #0000000D;
    overflow: hidden;
    padding: 5px 0;
}

.trp-language-item{
    display: flex;
    flex-direction: row;
    align-items: center;
}

.trp-dropdown-preview :deep(.trp-language-item-name) {
    max-width: 150px;
}

.trp-preview-ls-inline :deep(.trp-language-item-name) {
    width: 75px;
}

.trp-preview-ls-inline {
    display: flex;
    justify-content: space-between;
}

</style>

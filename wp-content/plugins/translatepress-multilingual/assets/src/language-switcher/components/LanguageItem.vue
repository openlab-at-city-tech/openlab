<script setup>
import { inject, computed } from 'vue'

const props = defineProps( {
    language : {
       type     : Object,
       required : true
    },
    dropdown : {
       type    : Boolean,
       default : false
    },
    flagPos         : String, // Inserted via injectedSettings
    nameMode        : String, // Inserted via injectedSettings
    flagAspectRatio : String  // Inserted via injectedSettings
})

// Injected global layout settings (optional override)
const injectedSettings = inject( 'languageItemSettings' )

const pluginUrl = window?.tpLangSwitcherData?.misc?.pluginUrl || window?.tpAldConfiguratorData?.misc?.pluginUrl || ''

const finalFlagPos = computed( () =>
    props.flagPos ?? injectedSettings?.value.flagPos ?? 'before'
)

const finalNameMode = computed( () =>
    props.nameMode ?? injectedSettings?.value.nameMode ?? 'full'
)

const showFlag = computed( () => finalFlagPos.value !== 'hide' )
const showName = computed( () => finalNameMode.value !== 'none' )

const isFlagBefore = computed( () => finalFlagPos.value === 'before' )
const isFlagAfter  = computed( () => finalFlagPos.value === 'after' )

const ratioFolder = computed(() => {
    const ratioSetting = props.flagAspectRatio ?? injectedSettings?.value.flagRatio?.value

    return ratioSetting === 'square' ? '1x1' : '4x3'
})

const flagUrl = computed(() => {
    if (!showFlag.value) return null

    // custom flag has priority
    if (props.language.flagPath) return props.language.flagPath

    const rawLocale =
              props.language.locale ??
              props.language.code ??
              props.language.slug ??
              ''
    const locale = String(rawLocale).trim()
    if (!locale || !pluginUrl) return null

    const file = locale.replace(/-/g, '_') + '.svg'
    return `${pluginUrl}assets/flags/${ratioFolder.value}/${encodeURIComponent(file)}`
})

const displayName = computed( () =>
    finalNameMode.value === 'short' ? props.language.shortName : props.language.name
)
</script>

<template>
    <a
        class="trp-language-item"
        :class="{ 'trp-dropdown-item': dropdown }"
    >
        <template v-if="isFlagBefore && showFlag">
            <img
                v-if="flagUrl"
                :src="flagUrl"
                class="trp-flag-image"
                aria-hidden="true"
                loading="lazy"
                decoding="async"
            />
        </template>

        <span v-if="showName" class="trp-language-item-name">
            {{ displayName }}
        </span>

        <template v-if="isFlagAfter && showFlag">
            <img
                v-if="flagUrl"
                :src="flagUrl"
                class="trp-flag-image"
                aria-hidden="true"
                loading="lazy"
                decoding="async"
            />
        </template>
    </a>
</template>

<style scoped>
.trp-language-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 5px 16px;
    font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
    font-size: var(--font-size, 14px);
    line-height: 1.2;
    color: var(--text);
    min-height: 19px;
    cursor: pointer;
    width: 100%;
}

.trp-language-item__default {
    pointer-events: none;
}

.trp-language-item:hover {
    background: var(--bg-hover);
    color: var(--text-hover);
}

.trp-flag-image {
    border-radius: var(--flag-radius, 2px);
    height: auto;
    image-rendering: pixelated;
    aspect-ratio: var(--aspect-ratio, 4/3);
    width: var(--flag-size, 18px);
}

.trp-language-item-name {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>

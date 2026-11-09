<script setup>
import { ref, watch, onMounted, onBeforeUnmount, shallowRef } from 'vue'
import { __ } from '@wordpress/i18n'
import { Codemirror } from 'vue-codemirror'
import { css } from '@codemirror/lang-css'

import debounce from '../../composables/utils/debounce'

const props = defineProps({
    label: { type: String, default: '' },
    visible: { type: Boolean, default: false },
    modelValue: { type: String, default: '' },
    scopeSelector: { type: String, default: '.trp-language-switcher' }
})
const emit = defineEmits(['update:modelValue'])

const rawCss     = ref(props.modelValue)
const scopedCss  = ref('')
const extensions = [css()]
const view       = shallowRef(null)
const placeholder = __('Write custom CSS here...', 'translatepress-multilingual')

const debouncedEmit = debounce( ( v) => {
    const scoped = scopeCustomCss(v)
    scopedCss.value = scoped
    emit('update:modelValue', scoped)
}, 300)

watch(() => props.modelValue, v => {
    if (v !== scopedCss.value && v !== rawCss.value) {
        rawCss.value = v
    }
})

watch(rawCss, v => debouncedEmit(v))

watch(scopedCss, v => {
    if (styleEl) {
        styleEl.textContent = v
    }
})

const handleReady = ({ view: v }) => (view.value = v)

/* Live <style> injection only when visible === true */
let styleEl = null

const injectStyle = () => {
    if (styleEl || !props.visible) return
    styleEl = document.createElement('style')
    styleEl.dataset.customCss = ''
    styleEl.textContent = scopeCustomCss(rawCss.value)
    document.head.appendChild(styleEl)
}

const removeStyle = () => {
    if (styleEl) {
        styleEl.remove()
        styleEl = null
    }
}

const scopeCustomCss = (css, scopeSelector = props.scopeSelector) => {
    // Parse into a temporary stylesheet via the browser’s CSSOM
    const styleEl = document.createElement('style');
    styleEl.textContent = css;
    document.head.appendChild(styleEl);
    const sheet = styleEl.sheet;
    document.head.removeChild(styleEl);

    const output = [];

    for (const rule of sheet.cssRules) {
        // Root-level style rules
        if (rule instanceof CSSStyleRule) {
            const scoped = rule.selectorText
                               .split(',')
                               .map(sel => {
                                   sel = sel.trim();
                                   // If already scoped, leave it
                                   if (sel.startsWith(scopeSelector))
                                       return sel;

                                   // Otherwise prefix
                                   return `${scopeSelector} ${sel}`
                               })
                               .join(', ')
            output.push(`${scoped} { ${rule.style.cssText} }`)
        }

        // @media blocks
        else if (rule instanceof CSSMediaRule) {
            const inner = [];
            for (const child of rule.cssRules) {
                if (child instanceof CSSStyleRule) {
                    const scoped = child.selectorText
                                        .split(',')
                                        .map(sel => {
                                            sel = sel.trim()
                                            return sel.startsWith(scopeSelector)
                                                ? sel
                                                : `${scopeSelector} ${sel}`
                                        })
                                        .join(', ')
                    inner.push(`${scoped} { ${child.style.cssText} }`)
                } else {
                    // e.g. nested @supports, @keyframes inside @media
                    inner.push(child.cssText)
                }
            }
            output.push(`@media ${rule.media.mediaText} {\n${inner.join('\n')}\n}`)
        }

        // Everything else (import, keyframes, supports)
        else
            output.push(rule.cssText)
    }

    return output.join('\n')
}

// Track visibility changes
watch(() => props.visible, (visible) => {
    if (visible) injectStyle()
    else removeStyle()
})

onMounted(() => {
    if (props.visible) injectStyle()
})
onBeforeUnmount(removeStyle)
</script>

<template>
    <div class="trp-custom-css-editor" v-show="visible">
        <label v-if="label" class="trp-field__label trp-primary-text-bold">
            {{ label }}
        </label>

        <Codemirror
            v-model="rawCss"
            :placeholder="placeholder"
            :style="{ height: '250px', width: '80%', fontSize: '14px', fontFamily: 'monospace' }"
            :indent-with-tab="true"
            :tab-size="2"
            :extensions="extensions"
            @ready="handleReady"
        />
    </div>
</template>

<style >
.cm-focused {
    outline: none !important;
}

.cm-editor {
    border: 1px solid var(--trp-settings-light-gray-border-color, #ccc);
    border-radius: 8px;
}

.cm-gutters {
    border-radius: 8px 0 0 8px;
}
</style>

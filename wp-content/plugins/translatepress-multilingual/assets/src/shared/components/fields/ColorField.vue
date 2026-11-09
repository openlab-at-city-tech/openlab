<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { __ } from '@wordpress/i18n'
import 'vue-color/style.css'
import { ChromePicker, tinycolor } from 'vue-color'

const props = defineProps({
    label      : { type: String, default: '' },
    modelValue : { type: String, required: true }
})
const emit = defineEmits(['update:modelValue'])
const openColourPickerLabel = __('Open colour picker', 'translatepress-multilingual')

// picker visibility + refs for click-outside
const showPicker = ref(false)
const swatchRef  = ref(null)
const pickerRef  = ref(null)

function togglePicker() {
    showPicker.value = !showPicker.value
}
function onClickOutside(e) {
    if (
        swatchRef.value && !swatchRef.value.contains(e.target) &&
        pickerRef.value && !pickerRef.value.contains(e.target)
    ) {
        showPicker.value = false
    }
}

onMounted(() => document.addEventListener('mousedown', onClickOutside))
onBeforeUnmount(() => document.removeEventListener('mousedown', onClickOutside))

const tinyColorModel = computed({
    get() {
        return tinycolor(props.modelValue)
    },
    set(tc) {
        // ensure we handle both hex-only and hex+alpha
        const c = tinycolor(tc)
        const hex  = c.toHexString().toUpperCase()
        const alpha = Math.round(c.getAlpha() * 255)
                          .toString(16)
                          .padStart(2,'0')
                          .toUpperCase()

        const out = alpha === 'FF' ? hex : `${hex}${alpha}`

        emit('update:modelValue', out)
    }
})
</script>

<template>
    <div :class="{ 'trp-color--picking': showPicker }">
        <span v-if="label" class="trp-field__label trp-primary-text-bold">
          {{ label }}
        </span>
        <div class="trp-color__wrapper">
            <div
                class="trp-color-input"
                :style="{ background: modelValue }"
                ref="swatchRef"
                @click="togglePicker"
                @keydown.enter.space="togglePicker"
                role="button"
                :aria-label="openColourPickerLabel"
                tabindex="0"
            />

            <span class="trp-color-code trp-primary-text">
                {{ modelValue.toUpperCase() }}
            </span>

            <div
                v-if="showPicker"
                class="trp-color__popover"
                ref="pickerRef"
                @mousedown.stop
            >
                <ChromePicker v-model:tinyColor="tinyColorModel" />
            </div>
        </div>
    </div>
</template>

<style scoped>
.trp-color__wrapper {
    position: relative;
    display: flex;
    align-items: center;
    gap: 8px;
}

.trp-color--picking {
    user-select: none;
}

.trp-color-input {
    width: 53px;
    height: 29px;
    border: 1px solid #E2E2E4;
    border-radius: 5px;
    cursor: pointer;
}

.trp-color-input:focus-visible {
    outline: 2px solid var(--trp-settings-accent-color);
}

.trp-color__popover {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    z-index: 1000;
}

.trp-color-code {
    text-transform: uppercase;
}
</style>

<script setup>
import { computed } from 'vue'

/**
 * A simple checkbox field for the settings UI.
 *
 * Props
 *  - modelValue  (Boolean)  – bound value (true = checked)
 *  - label       (String)   – bold label text
 *  - description (String)   – secondary text (HTML allowed)
 *
 * Emits
 *  - update:modelValue
 */
const props = defineProps({
    modelValue  : { type: Boolean, required: true },
    label       : { type: String,  default: '' },
    description : { type: String,  default: '' },
    disabled    : { type: Boolean, default: false },
    title       : { type: String, default: '' }
})

const emit = defineEmits(['update:modelValue'])

/* A stable unique ID so the <label> is associated with the <input>. */
const inputId = `trp-checkbox-${Math.random().toString(36).slice(2)}`

/* Computed wrapper to keep SettingsBox’s two-way binding contract intact. */
const checked = computed({
    get: () => props.modelValue === true,
    set: (val) => emit('update:modelValue', val)
})

</script>

<template>
    <div
        class="trp-settings-checkbox trp-settings-options-item"
        :title="props.title"
    >
        <input
            type="checkbox"
            :id="inputId"
            v-model="checked"
            :disabled="props.disabled"
        />

        <label :for="inputId" class="trp-checkbox-label">
            <div class="trp-checkbox-content">
                <span v-if="label" class="trp-primary-text-bold">{{ label }}</span>
                <span
                    v-if="description"
                    class="trp-description-text"
                    v-html="description"
                />
            </div>
        </label>
    </div>
</template>

<style scoped>
.trp-description-text {
    line-height: 150%;
}
.trp-settings-checkbox input:disabled {
    pointer-events: none;
    background: var(--trp-settings-disabled-color);
    border: none !important;
}
</style>

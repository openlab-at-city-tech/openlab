<script setup>
import { computed, ref, watch } from 'vue'
import { __ } from '@wordpress/i18n'

import NumberField from './NumberField.vue'

const props = defineProps({
    label:       { type: String, required: false, default: '' },
    modelValue:  { type: String, required: true },
    options:     {
        type: Array,
        required: true,
        validator: arr => arr.every(o =>
            'value' in o && 'label' in o &&
            (o.disabled === undefined || typeof o.disabled === 'boolean') &&
            (o.title    === undefined || typeof o.title    === 'string')
        )
    },
    columns:     { type: Number, default: 0 },
    customValue: { type: [Number, String] }, // (optional) a number v-model. If parent binds this prop, then “Custom” is automatically injected as an extra option.
})

const emit = defineEmits(['update:modelValue', 'update:customValue'])

const localCustom = ref(props.customValue)

// Sync parent -> localCustom whenever customValue prop changes
watch(
    () => props.customValue,
    (newVal) => {
        if (newVal !== localCustom.value) {
            localCustom.value = newVal
        }
    }
)

// Sync localCustom -> parent via update:customValue
watch(localCustom, (newVal) => {
    emit('update:customValue', newVal)
})

const shouldShowCustom = computed(() => props.customValue !== undefined)

/** Build the array of options we actually render.
 *  If shouldShowCustom is true, append { value: 'custom', label: 'Custom' }
 *  unless the user already provided an option whose value is "custom".
 */
const displayedOptions = computed(() => {
    if (!shouldShowCustom.value)
        return props.options

    // If parent’s options already include value = 'custom', don’t duplicate
    const hasCustomAlready = props.options.some(o => o.value === 'custom')

    if (hasCustomAlready)
        return props.options

    return [
        ...props.options,
        { value: 'custom', label: __('Custom', 'translatepress-multilingual') }
    ]
})
</script>

<template>
    <div class="trp-radio-group__wrapper">
        <span v-if="label" class="trp-field__label trp-primary-text-bold">{{ label }}</span>

        <div
            class="trp-radio-group"
            :class="{ 'trp-radio-group--grid': columns > 0 }"
            :style="columns > 0 ? { '--trp-radio-columns': columns } : null"
        >
            <div
                v-for="opt in displayedOptions"
                :key="opt.value"
                :class="['trp-radio-option', { 'is-disabled': opt.disabled }]"
                :title="opt.title"
            >
                <label class="trp-radio-label">
                    <input
                        type="radio"
                        :name="label || 'radio-group'"
                        :value="opt.value"
                        :checked="modelValue === opt.value"
                        :disabled="opt.disabled"
                        @change="() => emit('update:modelValue', opt.value)"
                    />
                    <span>{{ opt.label }}</span>
                </label>

                <NumberField
                    v-if="shouldShowCustom && opt.value === 'custom' && modelValue === 'custom'"
                    class="trp-lc-custom-number"
                    v-model="localCustom"
                    :label="''"
                    :min="0"
                />
            </div>

        </div>
    </div>
</template>

<style scoped>
.trp-radio-group__wrapper {
    gap: 16px;
}

.trp-radio-group {
    display:flex;
    gap:16px;
    flex-wrap: wrap;
    align-items: flex-start;
}

.trp-radio-group--grid {
    display: grid;
    grid-template-columns: repeat(var(--trp-radio-columns), minmax(0, 1fr));
    column-gap: 24px;
    row-gap: 14px;
}

.trp-radio-label {
    display: inline-flex;
    align-items: flex-start;
    gap: 8px;
    cursor: pointer;
    line-height: 20px;
}

.trp-lc-custom-number {
    margin-left: 8px;
}

.trp-radio-option {
    cursor: pointer;
    line-height: 20px;
    min-width: 140px;
    display: flex;
    align-items: flex-start;
}

.trp-radio-group--grid .trp-radio-option {
    min-width: 0;
}

.trp-radio-option.is-disabled {
    opacity: 0.5;
}

.trp-radio-option.is-disabled input {
    pointer-events: none;
    background: var(--trp-settings-disabled-color);
}

.trp-radio-option input {
    border: 1px solid var(--trp-settings-medium-gray-border-color);
    background: #fff;
    width: 20px;
    height: 20px;
    flex: 0 0 20px;
    margin: 0;
    position: relative;
}

.trp-radio-option input:checked:before {
    position: absolute;
    width: 10px;
    height: 10px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    margin: 0;
    background-color: var(--trp-settings-accent-color);
}
</style>

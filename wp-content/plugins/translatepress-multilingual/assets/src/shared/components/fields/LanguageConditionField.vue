<script setup>
import { computed, ref, onMounted, onBeforeUnmount } from 'vue'
import { __ } from '@wordpress/i18n'

const props = defineProps({
    label: { type: String, default: '' },
    modelValue: { type: Boolean, default: false },
    config: { type: Object, required: true },
    modeKey: { type: String, default: 'languageConditionMode' },
    languagesKey: { type: String, default: 'languages' },
    defaultMode: { type: String, default: 'include' },
    options: {
        type: Array,
        required: true,
        validator: arr => arr.every(o => 'value' in o && 'label' in o)
    },
    includeLabel: { type: String, default: __('Include', 'translatepress-multilingual') },
    excludeLabel: { type: String, default: __('Exclude', 'translatepress-multilingual') },
    modeLabel: { type: String, default: __('Content Restriction Mode', 'translatepress-multilingual') },
    selectLabel: { type: String, default: __('Select language', 'translatepress-multilingual') },
    addLabel: { type: String, default: __('Add', 'translatepress-multilingual') },
    emptyLabel: { type: String, default: __('No languages selected', 'translatepress-multilingual') },
})

const emit = defineEmits(['update:modelValue'])
const isDropdownOpen = ref(false)
const dropdownEl = ref(null)

if (!props.config[props.modeKey])
    props.config[props.modeKey] = props.defaultMode

if (!Array.isArray(props.config[props.languagesKey]))
    props.config[props.languagesKey] = []

const selected = computed(() => Array.isArray(props.config[props.languagesKey]) ? props.config[props.languagesKey] : [])

const availableOptions = computed(() => props.options.filter(option => !selected.value.includes(option.value)))

const selectedOptions = computed(() => selected.value
    .map(value => props.options.find(option => option.value === value))
    .filter(Boolean)
)

const selectId = computed(() => `trp-language-condition-${props.languagesKey}`)

function setMode(mode) {
    props.config[props.modeKey] = mode
}

function toggleCondition() {
    emit('update:modelValue', !props.modelValue)
}

function addLanguage(value) {

    if (!value)
        return

    props.config[props.languagesKey] = [...selected.value, value]
    isDropdownOpen.value = false
}

function removeLanguage(value) {
    props.config[props.languagesKey] = selected.value.filter(item => item !== value)
}

function closeDropdown(event) {
    if (dropdownEl.value && !dropdownEl.value.contains(event.target))
        isDropdownOpen.value = false
}

function closeOnEscape(event) {
    if (event.key === 'Escape')
        isDropdownOpen.value = false
}

onMounted(() => {
    document.addEventListener('pointerdown', closeDropdown, true)
    document.addEventListener('keyup', closeOnEscape)
})

onBeforeUnmount(() => {
    document.removeEventListener('pointerdown', closeDropdown, true)
    document.removeEventListener('keyup', closeOnEscape)
})
</script>

<template>
    <div class="trp-language-condition">
        <div class="trp-language-condition__toggle-row">
            <span v-if="label" class="trp-field__label trp-primary-text-bold">
                {{ label }}
            </span>

            <button
                type="button"
                class="trp-language-condition__toggle"
                :class="{ 'is-active': modelValue }"
                :aria-pressed="modelValue"
                @click="toggleCondition"
            >
                <span class="trp-language-condition__toggle-knob" />
            </button>
        </div>

        <div v-if="modelValue" class="trp-language-condition__body">
            <div class="trp-language-condition__mode-wrap">
                <span class="trp-language-condition__mode-label">
                    {{ modeLabel }}
                </span>

                <div class="trp-language-condition__mode" role="group" :aria-label="modeLabel">
                    <button
                        type="button"
                        :class="{ 'is-active': config[modeKey] === 'include' }"
                        @click="setMode('include')"
                    >
                        {{ includeLabel }}
                    </button>
                    <button
                        type="button"
                        :class="{ 'is-active': config[modeKey] === 'exclude' }"
                        @click="setMode('exclude')"
                    >
                        {{ excludeLabel }}
                    </button>
                </div>
            </div>

            <span class="trp-language-condition__select-label" :id="`${selectId}-label`">
                {{ selectLabel }}
            </span>

            <div
                ref="dropdownEl"
                class="trp-language-condition__dropdown"
                :class="{ 'is-open': isDropdownOpen }"
            >
                <button
                    type="button"
                    class="trp-language-condition__dropdown-trigger"
                    :aria-labelledby="`${selectId}-label`"
                    :aria-expanded="isDropdownOpen"
                    :aria-controls="selectId"
                    :disabled="availableOptions.length === 0"
                    @click="isDropdownOpen = !isDropdownOpen"
                >
                    <span>{{ selectLabel }}</span>
                    <span class="trp-language-condition__dropdown-arrow" aria-hidden="true" />
                </button>

                <div
                    v-if="isDropdownOpen"
                    :id="selectId"
                    class="trp-language-condition__dropdown-list"
                    role="listbox"
                >
                    <button
                        v-for="option in availableOptions"
                        :key="option.value"
                        type="button"
                        class="trp-language-condition__dropdown-option"
                        role="option"
                        @click="addLanguage(option.value)"
                    >
                        <img
                            v-if="option.flagUrl"
                            :src="option.flagUrl"
                            class="trp-language-condition__flag"
                            aria-hidden="true"
                            loading="lazy"
                            decoding="async"
                        />
                        <span>{{ option.label }}</span>
                    </button>
                </div>
            </div>

            <div class="trp-language-condition__chips" aria-live="polite">
                <button
                    v-for="option in selectedOptions"
                    :key="option.value"
                    type="button"
                    class="trp-language-condition__chip"
                    @click="removeLanguage(option.value)"
                >
                    <img
                        v-if="option.flagUrl"
                        :src="option.flagUrl"
                        class="trp-language-condition__flag"
                        aria-hidden="true"
                        loading="lazy"
                        decoding="async"
                    />
                    <span>{{ option.label }}</span>
                    <span aria-hidden="true">&times;</span>
                </button>
                <span v-if="selectedOptions.length === 0" class="trp-language-condition__empty">
                    {{ emptyLabel }}
                </span>
            </div>
        </div>
    </div>
</template>

<style scoped>
.trp-language-condition {
    display: flex;
    flex-direction: column;
    gap: 14px;
    width: 100%;
}

.trp-language-condition__toggle-row {
    display: flex;
    align-items: center;
    gap: 16px;
    width: 100%;
}

.trp-language-condition__toggle {
    position: relative;
    width: 36px;
    height: 19px;
    padding: 0;
    border: 1px solid #949494;
    border-radius: 28px;
    background: #fff;
    cursor: pointer;
}

.trp-language-condition__toggle.is-active {
    background: var(--trp-settings-accent-color, #0073aa);
    border-color: var(--trp-settings-accent-color, #0073aa);
}

.trp-language-condition__toggle-knob {
    position: absolute;
    left: 4px;
    top: 2px;
    width: 13px;
    height: 13px;
    border-radius: 50%;
    background: #949494;
    transition: transform 0.2s ease-in-out;
}

.trp-language-condition__toggle.is-active .trp-language-condition__toggle-knob {
    transform: translateX(14px);
    background: #fff;
}

.trp-language-condition__body {
    display: flex;
    flex-direction: column;
    gap: 12px;
    width: 100%;
}

.trp-language-condition__mode-wrap {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.trp-language-condition__mode-label,
.trp-language-condition__select-label {
    font-weight: 590;
    color: var(--trp-settings-primary-color);
}

.trp-language-condition__mode {
    display: inline-flex;
    align-self: flex-start;
    padding: 2px;
    border: 1px solid var(--trp-settings-light-gray-border-color);
    border-radius: 4px;
    background: #fff;
}

.trp-language-condition__mode button {
    min-height: 30px;
    padding: 0 12px;
    border: 0;
    border-radius: 2px;
    background: transparent;
    color: var(--trp-settings-primary-color);
    cursor: pointer;
}

.trp-language-condition__mode button.is-active {
    background: var(--trp-settings-accent-color, #0073aa);
    color: #fff;
}

.trp-language-condition__dropdown {
    position: relative;
    width: min(100%, 260px);
}

.trp-language-condition__dropdown-trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    width: 100%;
    min-height: 36px;
    padding: 0 10px;
    border: 1px solid #8c8f94;
    border-radius: 4px;
    background: #fff;
    color: #2c3338;
    cursor: pointer;
    text-align: left;
}

.trp-language-condition__dropdown-trigger:disabled {
    opacity: 0.7;
    cursor: default;
}

.trp-language-condition__dropdown-arrow {
    width: 0;
    height: 0;
    border-left: 4px solid transparent;
    border-right: 4px solid transparent;
    border-top: 5px solid #50575e;
    transition: transform 0.2s ease-in-out;
}

.trp-language-condition__dropdown.is-open .trp-language-condition__dropdown-arrow {
    transform: rotate(180deg);
}

.trp-language-condition__dropdown-list {
    position: absolute;
    z-index: 20;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    max-height: 180px;
    overflow-y: auto;
    padding: 4px;
    border: 1px solid var(--trp-settings-light-gray-border-color);
    border-radius: 4px;
    background: #fff;
    box-shadow: 0 8px 18px rgba(29, 35, 39, 0.12);
}

.trp-language-condition__dropdown-option {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    min-height: 34px;
    padding: 0 8px;
    border: 0;
    border-radius: 2px;
    background: transparent;
    color: var(--trp-settings-primary-color);
    cursor: pointer;
    text-align: left;
}

.trp-language-condition__dropdown-option:hover,
.trp-language-condition__dropdown-option:focus {
    background: #f0f6fc;
}

.trp-language-condition__chips {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    min-height: 30px;
}

.trp-language-condition__chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 28px;
    padding: 0 10px;
    border: 1px solid var(--trp-settings-light-gray-border-color);
    border-radius: 4px;
    background: #f6f7f7;
    color: var(--trp-settings-primary-color);
    cursor: pointer;
}

.trp-language-condition__flag {
    width: 18px;
    aspect-ratio: 4 / 3;
    height: auto;
    border-radius: 2px;
    object-fit: cover;
    flex: 0 0 auto;
}

.trp-language-condition__empty {
    color: #646970;
    line-height: 30px;
}
</style>

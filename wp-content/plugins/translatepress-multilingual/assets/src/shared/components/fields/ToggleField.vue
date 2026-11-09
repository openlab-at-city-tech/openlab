<script setup>
const props = defineProps({
    modelValue: Boolean,
    label: { type: String, default: '' },
    compact: { type: Boolean, default: false }
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
    <div class="trp-toggle-wrapper" :class="{ 'trp-toggle-wrapper--compact': compact }">
    <span v-if="label" class="trp-field__label trp-primary-text-bold">
      {{ label }}
    </span>

        <div class="trp-toggle-inner" @click="emit('update:modelValue', !modelValue)">
            <input
                type="checkbox"
                class="trp-toggle-input"
                :checked="modelValue"
                readonly
            />
            <span class="trp-toggle-slider" />
        </div>
    </div>
</template>

<style scoped>
.trp-toggle-wrapper {
    display: flex;
    align-items: center;
}

.trp-toggle-wrapper.trp-toggle-wrapper--compact {
    gap: 8px;
}

.trp-toggle-wrapper--compact .trp-field__label {
    width: auto;
}

.trp-toggle-inner {
    position: relative;
    display: inline-block;
    cursor: pointer;
    width: 36px;
    height: 19px;
}

.trp-toggle-input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.trp-toggle-slider {
    display: inline-block;
    position: relative;
    width: 34px;
    height: 17px;
    border-radius: 28px;
    transition: background-color 0.3s ease;
    border: 1px solid #949494;
    cursor: pointer;
}

.trp-toggle-slider::before {
    content: '';
    position: absolute;
    left: 4px;
    top: 2px;
    width: 13px;
    height: 13px;
    background-color: #949494;
    border-radius: 50%;
    transition: transform 0.2s ease-in-out;
}

.trp-toggle-input:checked + .trp-toggle-slider {
    background-color: var(--trp-settings-accent-color, #0073aa);
    border-color: var(--trp-settings-accent-color);
}

.trp-toggle-input:checked + .trp-toggle-slider::before {
    transform: translateX(14px);
    background-color: white;
}
</style>

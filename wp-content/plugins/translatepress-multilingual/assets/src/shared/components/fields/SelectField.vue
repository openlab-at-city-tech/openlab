<script setup>
const props = defineProps({
    label: { type: String, default: '' },
    modelValue: { type: String, default: '' },
    options: {
        type: Array,
        required: true,
        validator: arr => arr.every(option =>
            'value' in option &&
            'label' in option &&
            (option.disabled === undefined || typeof option.disabled === 'boolean')
        )
    },
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
    <div class="trp-select-field">
        <label v-if="label" class="trp-field__label trp-primary-text-bold">
            {{ label }}
        </label>

        <select
            class="trp-select-field__control"
            :value="modelValue"
            @change="event => emit('update:modelValue', event.target.value)"
        >
            <option
                v-for="option in options"
                :key="option.value"
                :value="option.value"
                :disabled="option.disabled"
            >
                {{ option.label }}
            </option>
        </select>
    </div>
</template>

<style scoped>
.trp-select-field {
    align-items: center;
}

.trp-select-field__control {
    box-sizing: border-box;
    width: min(100%, 260px);
    min-height: 40px;
    border: 1px solid #C3C4C7;
    border-radius: 5px;
    padding: 0 32px 0 10px;
    color: var(--trp-settings-primary-color);
    background-color: #ffffff;
}
</style>

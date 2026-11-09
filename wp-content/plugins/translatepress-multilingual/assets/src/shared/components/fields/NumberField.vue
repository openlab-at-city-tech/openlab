<script setup>
const props = defineProps({
    label       : { type: String, default: '' },
    modelValue  : { type: [Number, null], default: null },
    min         : { type: Number, default: 0, required: false },
})

const emit = defineEmits(['update:modelValue'])

function onInput (e) {
    const val = e.target.value
    emit('update:modelValue', val === '' ? props.min : Number(val))
}
</script>

<template>
    <div>
        <span v-if="label" class="trp-field__label trp-primary-text-bold">{{ label }}</span>
        <div class="trp-number__wrapper">
            <input
                type="number"
                class="trp-number-input"
                :value="modelValue ?? ''"
                :min="min"
                @input="onInput"
            />
            <span class="trp-primary-text">px</span>
        </div>
    </div>
</template>

<style scoped>
/* Remove arrows in Chrome, Safari, Edge */
.trp-number-input::-webkit-outer-spin-button,
.trp-number-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Remove arrows in Firefox */
.trp-number-input[type='number'] {
    -moz-appearance: textfield;
}

.trp-number__wrapper {
    display: flex;
    flex-direction: row;
    gap: 10px;
    align-items: center;
}

.trp-number-input {
    width: 53px;
    height: 40px;
    border: 1px solid #C3C4C7;
    border-radius: 5px;
    text-align: center;
}
</style>

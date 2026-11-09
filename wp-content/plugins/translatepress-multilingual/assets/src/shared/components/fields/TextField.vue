<script setup>
const props = defineProps({
    label: { type: String, default: '' },
    modelValue: { type: String, default: '' },
    description: { type: String, default: '' },
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
    <div class="trp-text-field">
        <label v-if="label" class="trp-field__label trp-primary-text-bold">
            {{ label }}
        </label>

        <div class="trp-text-field__control">
            <input
                type="text"
                class="trp-text-input"
                :value="modelValue"
                @input="event => emit('update:modelValue', event.target.value)"
            />

            <span
                v-if="description"
                class="trp-description-text"
                v-html="description"
            />
        </div>
    </div>
</template>

<style scoped>
.trp-text-field {
    align-items: flex-start;
}

.trp-text-field .trp-field__label {
    padding-top: 10px;
}

.trp-text-field__control {
    display: flex;
    flex-direction: column;
    gap: 8px;
    width: min(100%, 520px);
}

.trp-text-input {
    min-height: 40px;
    border: 1px solid #C3C4C7;
    border-radius: 5px;
    padding: 0 10px;
}
</style>

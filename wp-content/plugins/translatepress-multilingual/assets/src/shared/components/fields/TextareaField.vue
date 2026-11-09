<script setup>
const props = defineProps({
    label: { type: String, default: '' },
    modelValue: { type: String, default: '' },
    description: { type: String, default: '' },
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
    <div class="trp-textarea-field">
        <label v-if="label" class="trp-field__label trp-primary-text-bold">
            {{ label }}
        </label>

        <div class="trp-textarea-field__control">
            <textarea
                class="trp-textarea-input"
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
.trp-textarea-field {
    align-items: flex-start;
}

.trp-textarea-field .trp-field__label {
    padding-top: 10px;
}

.trp-textarea-field__control {
    display: flex;
    flex-direction: column;
    gap: 8px;
    width: min(100%, 520px);
}

.trp-textarea-input {
    min-height: 96px;
    border: 1px solid #C3C4C7;
    border-radius: 5px;
    padding: 10px;
    resize: vertical;
}
</style>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    label: { type: String, default: '' },
    modelValue: { type: Array, default: () => [] },
    options: {
        type: Array,
        required: true,
        validator: arr => arr.every(o => 'value' in o && 'label' in o)
    },
    columns: { type: Number, default: 0 },
})

const emit = defineEmits(['update:modelValue'])

const selected = computed(() => Array.isArray(props.modelValue) ? props.modelValue : [])

function toggleValue(value, checked) {
    const next = new Set(selected.value)

    if (checked)
        next.add(value)
    else
        next.delete(value)

    emit('update:modelValue', Array.from(next))
}
</script>

<template>
    <div class="trp-checkbox-group__wrapper">
        <span v-if="label" class="trp-field__label trp-primary-text-bold">{{ label }}</span>

        <div
            class="trp-checkbox-group"
            :class="{ 'trp-checkbox-group--grid': columns > 0 }"
            :style="columns > 0 ? { '--trp-checkbox-columns': columns } : null"
        >
            <label
                v-for="option in options"
                :key="option.value"
                class="trp-checkbox-group__option"
            >
                <input
                    type="checkbox"
                    :value="option.value"
                    :checked="selected.includes(option.value)"
                    @change="event => toggleValue(option.value, event.target.checked)"
                />
                <span>{{ option.label }}</span>
            </label>
        </div>
    </div>
</template>

<style scoped>
.trp-checkbox-group__wrapper {
    gap: 16px;
}

.trp-checkbox-group {
    display: flex;
    flex-wrap: wrap;
    gap: 14px 24px;
    align-items: flex-start;
}

.trp-checkbox-group--grid {
    display: grid;
    grid-template-columns: repeat(var(--trp-checkbox-columns), minmax(0, 1fr));
}

.trp-checkbox-group__option {
    display: inline-flex;
    align-items: flex-start;
    gap: 8px;
    min-width: 0;
    line-height: 20px;
    cursor: pointer;
}

.trp-checkbox-group__option input {
    flex: 0 0 20px;
    width: 20px;
    height: 20px;
    margin: 0;
}
</style>

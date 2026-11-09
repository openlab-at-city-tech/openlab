<template>
    <div class="trp-toggle-status-field">
        <ToggleField
            v-model="inputValue"
            :label="''"
        />
        <span class="trp-primary-text">
        {{ modelValue ? onText : offText }}
        </span>
    </div>
</template>

<script setup>
import ToggleField from './ToggleField.vue'
import { computed }  from "vue"
import { __ } from '@wordpress/i18n'

const props = defineProps({
    modelValue: Boolean,
    onText:  { type: String, default: __('Enabled', 'translatepress-multilingual') },
    offText: { type: String, default: __('Disabled', 'translatepress-multilingual') }
})
const emit = defineEmits(['update:modelValue'])

const inputValue = computed({
    get: ()  => props.modelValue,
    set: val => emit('update:modelValue', val)
});
</script>

<style scoped>
.trp-toggle-status-field { display:flex; align-items:center; gap:10px; }
</style>

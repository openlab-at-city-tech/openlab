<script setup>
import { computed }  from 'vue'
import { __ } from '@wordpress/i18n'
import NumberField from './NumberField.vue'

/*
  v-model expects an array: [ topLeft, topRight, bottomLeft, bottomRight ]
*/
const props = defineProps( {
    label      : { type : String, default : '' },
    modelValue : { type : Array, required : true }
})
const emit  = defineEmits( [ 'update:modelValue' ] )

/* build four computed bindings that proxy into the array */
// each binding is a ComputedRef<number>
const bindings = Array.from( { length : 4 }, ( _, i ) =>
    computed( {
        get : () => props.modelValue[ i ] ?? 0,
        set : val => {
            const next = [ ...props.modelValue ]

            next[ i ]  = val
            emit( 'update:modelValue', next )
        }
    })
)

const cornerLabels = [
    __('Top Left', 'translatepress-multilingual'),
    __('Top Right', 'translatepress-multilingual'),
    __('Bottom Left', 'translatepress-multilingual'),
    __('Bottom Right', 'translatepress-multilingual'),
]
</script>

<template>
    <div class="trp-field trp-field--column">
        <span class="trp-field__label trp-primary-text-bold">{{ label }}</span>
        <div class="trp-quad-grid">
            <div
                v-for="(cornerLabel, idx) in cornerLabels"
                :key="idx"
                class="trp-quad-radius-corner"
            >
                <span class="trp-primary-text trp-corner-label">{{ cornerLabel }}</span>
                <NumberField
                    :modelValue="bindings[idx].value"
                    @update:modelValue="val => bindings[idx].value = val"
                    :label="''"
                />
            </div>
        </div>
    </div>
</template>

<style scoped>
.trp-quad-grid {
    display: grid;
    grid-template-columns: repeat(2, max-content);
    gap: 12px 24px;
}

.trp-quad-radius-corner {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 8px;
}

.trp-corner-label{
    min-width: 90px;
}

</style>

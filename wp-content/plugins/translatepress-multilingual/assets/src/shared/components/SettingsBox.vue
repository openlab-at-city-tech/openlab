<script setup>
import { ref, computed, reactive, inject } from 'vue'

import ColorField         from './fields/ColorField.vue'
import NumberField        from './fields/NumberField.vue'
import ToggleField        from './fields/ToggleField.vue'
import ToggleStatusField  from './fields/ToggleStatusField.vue'
import RadioGroupField    from './fields/RadioGroupField.vue'
import QuadRadiusField    from './fields/QuadRadiusField.vue'
import CustomCssAreaField from './fields/CustomCssAreaField.vue'
import CheckboxField      from './fields/CheckboxField.vue'
import CheckboxGroupField from './fields/CheckboxGroupField.vue'
import TextField          from './fields/TextField.vue'
import TextareaField      from './fields/TextareaField.vue'
import SelectField        from './fields/SelectField.vue'
import LanguageConditionField from './fields/LanguageConditionField.vue'

const baseComponentMap = {
    color         : ColorField,
    number        : NumberField,
    toggle        : ToggleField,
    toggleStatus  : ToggleStatusField,
    radio         : RadioGroupField,
    quadNumber    : QuadRadiusField,
    customCss     : CustomCssAreaField,
    checkbox      : CheckboxField,
    checkboxGroup : CheckboxGroupField,
    text          : TextField,
    textarea      : TextareaField,
    select        : SelectField,
    languageCondition : LanguageConditionField,
}

const props = defineProps( {
    title       : { type: String, required: true },
    fields      : { type: Array, required: false, default: [] }, // [{ fields:[{key,type,label,...}] }]
    collapsible : { type: Boolean, default: false },
    scope       : { type: String, default: '' },
    config      : { type: Object, default: null },
    components  : { type: Object, default: () => ({}) },
})

const cfg = props.config || inject(props.scope)

if (!cfg) {
    throw new Error(`SettingsBox needs either a config prop or an injected scope. Scope: "${props.scope}".`)
}

const componentMap = computed(() => ({
    ...baseComponentMap,
    ...props.components
}))

const open = ref( !props.collapsible )

const bindings = reactive({})

props.fields.forEach(field => {
    if (field.type === 'separator' || bindings[field.key]) return;

    if (cfg[field.key] === undefined && field.default !== undefined)
        cfg[field.key] = field.default

    bindings[field.key] = computed({
        get: () => cfg[field.key],
        set: val => cfg[field.key] = val
    })
})

function isFieldVisible(field) {
    return field.visible ? field.visible(cfg) : true
}

function getFieldProps(field) {
    const {
        key: _key,
        type,
        layout: _layout,
        visible: _visible,
        default: _defaultValue,
        ...componentProps
    } = field

    if (type === 'languageCondition')
        componentProps.config = cfg

    if (type === 'customCss')
        componentProps.visible = true

    if (type === 'lCustomizer')
        componentProps.scope = props.scope

    return componentProps
}
</script>

<template>
    <div
        class="trp-settings-box"
        :class="{ 'trp-collapsible': props.collapsible }"
    >
        <header class="trp-header" @click="props.collapsible && (open = !open)">
            <span class="trp-title">{{ props.title }}</span>
            <svg
                v-if="props.collapsible"
                class="trp-chevron"
                :class="{ open }"
                viewBox="0 0 20 20"
                width="20" height="20"
            >
                <path d="M5 6L10 11L15 6L17 7L10 14L3 7L5 6Z" fill="#9CA1A8"/>
            </svg>
        </header>

        <section v-show="open" class="trp-body">
            <slot v-if="$slots.default" /> <!-- In case a component is injected inside SettingsBox, it will be directly rendered - bypassing the routing system.  -->

            <template v-for="(field, index) in props.fields" :key="field.key || `${field.type}-${index}`">
                <div v-if="field.type === 'separator' && isFieldVisible(field)" class="trp-separator" />
                <component
                    v-else-if="isFieldVisible(field)"
                    :is="componentMap[field.type]"
                    v-model="bindings[field.key]"
                    v-bind="getFieldProps(field)"
                    :class="[
                        'trp-field',
                        field.hasOwnProperty('layout') ? 'trp-field--column' : 'trp-field--row'
                    ]"
                />
            </template>

            <slot name="end" />
        </section>
    </div>
</template>

<style scoped>
.trp-settings-box {
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    padding: 24px;
    gap: 24px;
    background: #fff;
    border: 1px solid #e2e2e4;
    border-radius: 2px;
}

.trp-header {
    display: flex;
    justify-content: space-between;
}

.trp-settings-box.trp-collapsible .trp-header {
    cursor: pointer;
}

.trp-title {
    font-weight: 590;
    color: var(--trp-settings-primary-color);
    font-size: 16px;
}

.trp-chevron {
    transition: transform .2s ease;
}

.trp-chevron.open {
    transform: rotate(180deg);
}

.trp-body {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.trp-separator {
    height: 1px;
    width: 100%;
    background: var(--trp-settings-light-gray-border-color);
}

</style>

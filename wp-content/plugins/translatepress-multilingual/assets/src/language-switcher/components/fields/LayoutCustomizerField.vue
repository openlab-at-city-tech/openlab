<script setup>
import { reactive, watch, computed } from 'vue'
import { usePreviewViewport }        from "../../composables/usePreviewViewport"

import RadioGroupField from '../../../shared/components/fields/RadioGroupField.vue'

const positionOptions = [
    { value: 'bottom-right', label: 'Bottom Right' },
    { value: 'bottom-left',  label: 'Bottom Left'  },
    { value: 'top-right',    label: 'Top Right'    },
    { value: 'top-left',     label: 'Top Left'     }
]

const flagIconOptions = [
    { value: 'before', label: 'Before Language' },
    { value: 'after',  label: 'After Language'  },
    { value: 'hide',   label: 'Hide Icons'      }
]

// For menu scope
const flagShapeOptions = [
    { value: 'rect',     label: 'Rectangle 4:3' },
    { value: 'square',   label: 'Square 1:1'    },
    { value: 'rounded',  label: 'Rounded'   }
]

const languageNameOptions = [
    { value: 'full',  label: 'Full Names'  },
    { value: 'short', label: 'Short Names' },
    { value: 'none',  label: 'No Names'    }
]

const props = defineProps({
    label:      { type: String, default: '' },
    modelValue: {
        type: Object,
        default: () => ({
            desktop: {},
            mobile : {}
        })
    },
    scope: {
        type: String,
        required: true
    }
})
const emit = defineEmits(['update:modelValue', 'update:customValue'])

const local = reactive({
    desktop: { ...props.modelValue.desktop },
    mobile:  { ...props.modelValue.mobile  }
})

watch(
    () => local,
    (val) => { emit('update:modelValue', { desktop: { ...val.desktop }, mobile: { ...val.mobile } }) },
    { deep: true }
)

watch(
    () => props.modelValue,
    (val) => {
        if (val.desktop) Object.assign(local.desktop, val.desktop)
        if (val.mobile)  Object.assign(local.mobile,  val.mobile)
    }
)

const { selectedViewport: currentMode, setViewport } = usePreviewViewport(props.scope)

const isShortcode = props.scope === 'shortcode'
const isMenu      = props.scope === 'menu'
const isFloater   = props.scope === 'floater'

/** TODO: I think we should generate the fields dynamically in case further changes will be made to this component. */
</script>

<template>
    <div class="trp-layout-customizer-field trp-field trp-field--column">
    <span
        v-if="label"
        class="trp-field__label trp-primary-text-bold"
    >{{ label }}</span>

        <div v-if="scope === 'menu'" class="trp-settings-separator"></div>

        <!-- viewport toggle -->
        <div class="trp-lc-mode-toggle">
            <button
                :class="['trp-lc-mode-button', { active: currentMode === 'desktop' }]"
                @click="setViewport('desktop')"
                type="button"
            >
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M3 2H17C17.55 2 18 2.45 18 3V13C18 13.55 17.55 14 17 14H12V16H14
                   C14.55 16 15 16.45 15 17V18H5V17C5 16.45 5.45 16 6 16H8V14H3
                   C2.45 14 2 13.55 2 13V3C2 2.45 2.45 2 3 2ZM16 11V4H4V11H16Z" fill="#1D2327"/>
                </svg>
                <span>Desktop</span>
            </button>

            <button
                :class="['trp-lc-mode-button', { active: currentMode === 'mobile' }]"
                @click="setViewport('mobile')"
                type="button"
            >
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M6 2H14C14.55 2 15 2.45 15 3V17C15 17.55 14.55 18 14 18H6
                   C5.45 18 5 17.55 5 17V3C5 2.45 5.45 2 6 2ZM13 14V4H7V14H13Z" fill="#1D2327"/>
                </svg>
                <span>Mobile</span>
            </button>
        </div>

        <div class="trp-lc-settings-panel">
            <!-- DESKTOP -->
            <template v-if="currentMode === 'desktop'">
                <div class="trp-lc-section">
                    <!-- position / width / padding only when NOT shortcode -->
                    <template v-if="isFloater">
                        <div class="trp-lc-subfield">
                            <RadioGroupField
                                label="Switcher Position"
                                v-model="local.desktop.position"
                                :options="positionOptions"
                            />
                        </div>

                        <div class="trp-lc-subfield">
                            <RadioGroupField
                                label="Switcher Width"
                                v-model="local.desktop.width"
                                v-model:customValue="local.desktop.customWidth"
                                :options="[ { value: 'default', label: 'Default' } ]"
                            />
                        </div>

                        <div class="trp-lc-subfield">
                            <RadioGroupField
                                label="Switcher Padding"
                                v-model="local.desktop.padding"
                                v-model:customValue="local.desktop.customPadding"
                                :options="[ { value: 'default', label: 'Default' } ]"
                            />
                        </div>
                    </template>

                    <div class="trp-lc-subfield">
                        <RadioGroupField
                            label="Flag Icons Position"
                            v-model="local.desktop.flagIconPosition"
                            :options="flagIconOptions"
                        />
                    </div>


                    <div v-if="isMenu" class="trp-lc-subfield">
                        <RadioGroupField
                            label="Flag icons"
                            v-model="local.desktop.flagShape"
                            :options="flagShapeOptions"
                        />
                    </div>

                    <div class="trp-lc-subfield">
                        <RadioGroupField
                            label="Language Names"
                            v-model="local.desktop.languageNames"
                            :options="languageNameOptions"
                        />
                    </div>
                </div>
            </template>

            <!-- MOBILE -->
            <template v-else>
                <div class="trp-lc-section">
                    <template v-if="isFloater">
                        <div class="trp-lc-subfield">
                            <RadioGroupField
                                label="Switcher Position"
                                v-model="local.mobile.position"
                                :options="positionOptions"
                            />
                        </div>

                        <div class="trp-lc-subfield">
                            <RadioGroupField
                                label="Switcher Width"
                                v-model="local.mobile.width"
                                v-model:customValue="local.mobile.customWidth"
                                :options="[ { value: 'default', label: 'Default' } ]"
                            />
                        </div>

                        <div class="trp-lc-subfield">
                            <RadioGroupField
                                label="Switcher Padding"
                                v-model="local.mobile.padding"
                                v-model:customValue="local.mobile.customPadding"
                                :options="[ { value: 'default', label: 'Default' } ]"
                            />
                        </div>
                    </template>

                    <div class="trp-lc-subfield">
                        <RadioGroupField
                            label="Flag Icons Position"
                            v-model="local.mobile.flagIconPosition"
                            :options="flagIconOptions"
                        />
                    </div>

                    <div v-if="isMenu" class="trp-lc-subfield">
                        <RadioGroupField
                            label="Flag icons"
                            v-model="local.desktop.flagShape"
                            :options="flagShapeOptions"
                        />
                    </div>

                    <div class="trp-lc-subfield">
                        <RadioGroupField
                            label="Language Names"
                            v-model="local.mobile.languageNames"
                            :options="languageNameOptions"
                        />
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<style scoped>
.trp-settings-separator {
    margin-bottom: 8px;
}

.trp-layout-customizer-field {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.trp-lc-mode-toggle {
    display: flex;
    gap: 8px;
}

.trp-lc-mode-button {
    display: flex;
    gap: 4px;
    padding: 0 8px 8px 8px;
    cursor: pointer;
    background: none;
    border: none;
}

.trp-lc-mode-button.active {
    color: var(--trp-settings-accent-color);
    border-bottom: 2px solid var(--trp-settings-accent-color);
}

.trp-lc-mode-button.active svg path {
    fill: var(--trp-settings-accent-color);
}

/* panel & sections */
.trp-lc-settings-panel {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.trp-lc-section   {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.trp-lc-subfield :deep(.trp-radio-group__wrapper) { display: flex; flex-direction: column; gap: 8px; }

</style>

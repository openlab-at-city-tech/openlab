<script setup>
import { computed, ref } from 'vue'
import { __, sprintf } from '@wordpress/i18n'

const props = defineProps({
    config: {
        type: Object,
        required: true,
    },
    presets: {
        type: Array,
        required: true,
    },
    previewComponent: {
        type: [Object, Function],
        required: true,
    },
    previewProps: {
        type: Object,
        default: () => ({}),
    },
    previewConfigProp: {
        type: String,
        default: 'config',
    },
    getPreviewStyle: {
        type: Function,
        default: null,
    },
    getPreviewProps: {
        type: Function,
        default: null,
    },
    applyPreset: {
        type: Function,
        default: null,
    },
    labels: {
        type: Object,
        default: () => ({}),
    },
})

const defaultLabels = {
    confirmTitleHtml: __('Are you sure you want to apply the <strong>%s</strong> preset?', 'translatepress-multilingual'),
    confirmOverwrite: __('It will override your current settings.', 'translatepress-multilingual'),
    applyPreset: __('Apply preset', 'translatepress-multilingual'),
    cancel: __('Cancel', 'translatepress-multilingual'),
    applyPresetWithName: __('Apply %s preset', 'translatepress-multilingual'),
}

const labels = computed(() => ({
    ...defaultLabels,
    ...props.labels,
}))

const confirmPreset = ref(null)

function previewConfig(preset) {
    return {
        ...props.config,
        ...(preset.settings || {}),
    }
}

function previewStyle(preset) {
    return props.getPreviewStyle ? props.getPreviewStyle(preset.settings || {}, preset) : {}
}

function previewProps(preset) {
    if (props.getPreviewProps)
        return props.getPreviewProps(previewConfig(preset), preset)

    const nextProps = { ...props.previewProps }

    if (props.previewConfigProp)
        nextProps[props.previewConfigProp] = previewConfig(preset)

    return nextProps
}

function cardBackground(preset) {
    return preset.previewBackground || '#DBDBDB'
}

function requestApply(preset) {
    confirmPreset.value = preset
}

function cancelApply() {
    confirmPreset.value = null
}

function commitPreset() {
    if (!confirmPreset.value)
        return

    if (props.applyPreset)
        props.applyPreset(confirmPreset.value, props.config)
    else
        Object.assign(props.config, confirmPreset.value.settings || {})

    confirmPreset.value = null
}
</script>

<template>
    <div class="trp-preset-applier">
        <div
            v-for="preset in presets"
            :key="preset.id"
            class="trp-preset-card"
            :style="previewStyle(preset)"
        >
            <div
                class="trp-preview-rect"
                :style="{ background: cardBackground(preset) }"
            >
                <component
                    :is="previewComponent"
                    v-bind="previewProps(preset)"
                />

                <div
                    v-if="confirmPreset && confirmPreset.id === preset.id"
                    class="trp-confirmation-dialog"
                >
                    <p
                        class="trp-primary-text"
                        v-html="sprintf(labels.confirmTitleHtml, preset.name)"
                    />
                    <p class="trp-primary-text trp-confirmation-overwrite-warning">
                        {{ labels.confirmOverwrite }}
                    </p>
                    <div class="trp-dialog-actions">
                        <button
                            type="button"
                            class="trp-confirm-button"
                            @click="commitPreset"
                        >
                            {{ labels.applyPreset }}
                        </button>
                        <button
                            type="button"
                            class="trp-description-text trp-cancel-button"
                            @click="cancelApply"
                        >
                            {{ labels.cancel }}
                        </button>
                    </div>
                </div>
            </div>

            <button
                type="button"
                class="trp-apply-btn"
                @click="requestApply(preset)"
            >
                {{ sprintf(labels.applyPresetWithName, preset.name) }}
            </button>
        </div>
    </div>
</template>

<style scoped>
.trp-preset-applier {
    display: grid;
    gap: 16px;
    grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr));
}

.trp-preset-card {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.trp-preview-rect {
    width: 100%;
    height: 145px;
    padding: 0;
    border-radius: var(--trp-settings-radius-high);
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    overflow: hidden;
}

.trp-apply-btn {
    all: unset;
    box-sizing: border-box;
    display: block;
    align-items: center;
    width: 100%;
    color: var(--trp-settings-accent-color);
    font-size: var(--trp-settings-primary-font-size);
    font-weight: 500;
    line-height: 1.4;
    padding: 2px 0 1px;
    cursor: pointer;
    white-space: normal;
    overflow-wrap: anywhere;
}

.trp-apply-btn:hover {
    opacity: 0.8;
}

.trp-confirm-button {
    cursor: pointer;
    border-radius: var(--trp-settings-radius-medium);
    color: #ffffff;
    background: var(--trp-settings-accent-color);
    border: 1px solid var(--trp-settings-accent-color);
    padding: 5px 10px;
}

.trp-confirm-button:hover {
    background: transparent;
    color: var(--trp-settings-accent-color);
}

.trp-confirmation-dialog {
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: #fff;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid;
    text-align: center;
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 20;
    overflow-y: auto;
}

.trp-confirmation-dialog p {
    margin: 0;
}

.trp-dialog-actions {
    display: flex;
    gap: 8px;
    justify-content: center;
    align-items: center;
}

.trp-confirmation-overwrite-warning {
    color: #C94F2D;
}

.trp-cancel-button {
    background: transparent;
    border: 0;
    border-radius: var(--trp-settings-radius-medium);
    cursor: pointer;
    padding: 5px 10px;
    transition: background-color 0.2s ease, color 0.2s ease;
}

.trp-cancel-button:hover {
    background: #f0f0f1;
    color: var(--trp-settings-accent-color);
}

.trp-cancel-button:focus-visible {
    outline: 2px solid var(--trp-settings-accent-color);
    outline-offset: 2px;
}
</style>

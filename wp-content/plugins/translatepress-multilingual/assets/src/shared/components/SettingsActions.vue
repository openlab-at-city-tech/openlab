<script setup>
import { inject, ref } from "vue"
import { __ }          from "@wordpress/i18n"

const props = defineProps({
    persistenceKey: { type: String, default: 'settingsPersistence' }
})

const persistence = inject(props.persistenceKey)

if (!persistence) {
    throw new Error(`SettingsActions could not find persistence provider "${props.persistenceKey}".`)
}

const { save, revert, isDirty, saving, justSaved, errorMsg } = persistence

const confirmRevert = ref(false)

function askRevert () {
    confirmRevert.value = true
}
function cancelRevert () {
    confirmRevert.value = false
}
function doRevert () {
    revert()
    confirmRevert.value = false
}

const T = {
    saving: __('Saving...', 'translatepress-multilingual'),
    saved: __('Saved!', 'translatepress-multilingual'),
    saveChanges: __('Save changes', 'translatepress-multilingual'),
    revertTitle: __('Revert to last saved values', 'translatepress-multilingual'),
    revertChanges: __('Revert changes', 'translatepress-multilingual'),
    revertConfirmText: __('Restoring will revert to the last saved version and discard your current edits.', 'translatepress-multilingual'),
    revertBtn: __('Revert', 'translatepress-multilingual'),
    cancelBtn: __('Keep editing', 'translatepress-multilingual'),
}
</script>

<template>
    <teleport to="body">
        <div
            v-if="confirmRevert"
            class="trp-actions-overlay"
        />
    </teleport>

    <div class="trp-settings-actions">
        <button
            class="trp-submit-btn"
            type="button"
            :disabled="!isDirty || saving"
            @click="save"
        >
            <template v-if="saving">
                <span class="trp-save-spinner" />
                <span>{{ T.saving }}</span>
            </template>

            <template v-else-if="justSaved">
                <span>{{ T.saved }}</span>
            </template>

            <template v-else>
                <span>{{ T.saveChanges }}</span>
            </template>
        </button>

        <button
            class="trp-button-secondary"
            type="button"
            :disabled="!isDirty || saving"
            :title="T.revertTitle"
            @click="askRevert"
        >
            <svg
                width="14" height="14"
                viewBox="0 0 14 14"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
                style="margin-right: 6px; vertical-align: middle;"
            >
                <path
                    d="M7.1752 0.713867C10.7452 0.713867 13.3002 3.54187 13.3002 7.01387C13.3002 10.4859 10.7452 13.3139 7.1752 13.3139C4.9352 13.3139 2.9612 12.2009 1.7992 10.5209L3.6122 9.45687C4.3822 10.5069 5.6142 11.2139 7.0002 11.2139C9.3102 11.2139 11.2002 9.26087 11.2002 7.01387C11.2002 4.76687 9.3102 2.81387 7.0002 2.81387C5.6212 2.81387 4.3962 3.51387 3.6262 4.55687L4.9002 5.61387L0.700195 7.01387V2.11387L2.0232 3.21987C3.2062 1.70087 5.0752 0.713867 7.1752 0.713867Z"
                    fill="#2271B1"
                />
            </svg>
            {{ T.revertChanges }}
        </button>

        <span v-if="errorMsg" class="trp-save-ls-error">{{ errorMsg }}</span>
    </div>

    <teleport to="body">
        <transition name="fade">
            <div v-if="confirmRevert" class="trp-revert-confirm">
                <p class="trp-revert-text trp-primary-text">
                    {{ T.revertConfirmText }}
                </p>

                <div class="trp-revert-actions">
                    <button class="trp-btn-revert trp-submit-btn" @click="doRevert">
                        {{ T.revertBtn }}
                    </button>
                    <span class="trp-btn-cancel" @click="cancelRevert">
                        {{ T.cancelBtn }}
                    </span>
                </div>
            </div>
        </transition>
    </teleport>
</template>

<style scoped>
.trp-actions-overlay {
    position: fixed;
    inset: 0;
    background:rgba(0,0,0,.35);
    backdrop-filter:blur(2px);
    z-index:1000
}

.trp-revert-confirm {
    position: fixed;
    z-index: 10001;
    background: #FFFFFF;
    border: 1px solid #e2e2e4;
    border-radius: 8px;
    max-width: 350px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 15px;
}

.trp-revert-actions {
    display: flex;
    justify-content: center;
    align-items: center;
}

.trp-revert-text {
    text-align: center;
}

.trp-btn-revert {
    margin-right: 10px;
}

.trp-btn-cancel {
    cursor: pointer;
}

.trp-btn-cancel:hover {
    opacity: 0.8;
}

.trp-settings-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 24px;
}

.trp-button-secondary {
    height: 40px;
}

.trp-button-secondary:hover svg path {
    fill: white;
}

.trp-submit-btn {
    display: flex;
    align-items: center;
    gap: 8px;
}

.trp-submit-btn:disabled, .trp-button-secondary:disabled {
    opacity: 0.8;
    pointer-events: none;
}

.trp-save-spinner {
    width: 14px;
    height: 14px;
    display: inline-block;

    border: 2px solid rgba(255, 255, 255, 0.3); /* outer ring (faded) */
    border-top-color: #ffffff;                 /* spinning top highlight */
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.trp-save-ls-error {
    color: #c00;
    font-size: 13px;
    margin-left: 12px;
}
</style>

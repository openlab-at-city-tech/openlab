<script setup>
import { ref, computed } from 'vue'
import { __ }            from '@wordpress/i18n'

const SHORTCODE = '[language-switcher]'
const copiedFeedback = ref(false)

const copyButtonLabel = computed(() =>
    copiedFeedback.value
        ? __('Copied!', 'translatepress-multilingual')
        : __('Copy', 'translatepress-multilingual')
)

function copyToClipboard() {
    navigator.clipboard?.writeText(SHORTCODE)
             .then(() => {
                 copiedFeedback.value = true
                 setTimeout(() => copiedFeedback.value = false, 2000)
             })
             .catch(() => {
                 // Fallback for very old browsers
                 const textarea = document.createElement('textarea')
                 textarea.value = SHORTCODE
                 textarea.style.position = 'fixed'
                 textarea.style.opacity = '0'

                 document.body.appendChild(textarea)
                 textarea.select()

                 try {
                     document.execCommand('copy')
                 } finally {
                     document.body.removeChild(textarea)
                     copiedFeedback.value = true
                     setTimeout(() => copiedFeedback.value = false, 2000)
                 }
             })
}

const shortcodeDescription = __('Use shortcode on any page or widget. You can also add the Language Switcher Block in the WP Gutenberg Editor.', 'translatepress-multilingual')
</script>

<template>
    <div class="trp-shortcode-display">
        <input
            class="trp-shortcode-input trp-primary-text"
            :value="SHORTCODE"
            readonly
        />
        <button
            class="trp-copy-btn trp-button-secondary"
            type="button"
            @click="copyToClipboard"
        >
            <svg
                width="22"
                height="22"
                viewBox="0 0 22 22"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M14.666 7.33317V5.49984C14.666 5.01361 14.4729 4.54729 14.129 4.20347C13.7852 3.85966 13.3189 3.6665 12.8327 3.6665H5.49935C5.01312 3.6665 4.5468 3.85966 4.20299 4.20347C3.85917 4.54729 3.66602 5.01361 3.66602 5.49984V12.8332C3.66602 13.3194 3.85917 13.7857 4.20299 14.1295C4.5468 14.4733 5.01312 14.6665 5.49935 14.6665H7.33268M7.33268 9.1665C7.33268 8.68027 7.52584 8.21396 7.86965 7.87014C8.21347 7.52633 8.67979 7.33317 9.16602 7.33317H16.4993C16.9856 7.33317 17.4519 7.52633 17.7957 7.87014C18.1395 8.21396 18.3327 8.68027 18.3327 9.1665V16.4998C18.3327 16.9861 18.1395 17.4524 17.7957 17.7962C17.4519 18.14 16.9856 18.3332 16.4993 18.3332H9.16602C8.67979 18.3332 8.21347 18.14 7.86965 17.7962C7.52584 17.4524 7.33268 16.9861 7.33268 16.4998V9.1665Z"
                    stroke="#2271B1"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
            {{ copyButtonLabel }}
        </button>
    </div>

    <p class="trp-description-text">
        {{ shortcodeDescription }}
    </p>
</template>

<style scoped>
.trp-shortcode-display{
    display:flex;
    gap:12px;
    align-items:center;
}

.trp-shortcode-input{
    flex: 1;
    padding: 12px;
    height: 40px;
    border: 1px solid #C3C4C7;
    border-radius: 5px;
    background: #ffffff;
    font-family: monospace;
}

.trp-description-text {
    margin: 0;
}

.trp-shortcode-input:focus-visible, .trp-copy-btn:focus-visible {
    outline-color: var(--trp-settings-accent-color);
}

.trp-copy-btn  {
    display: flex;
    justify-content: center;
    align-content: center;
    gap: 8px;
}

.trp-copy-btn:hover svg path {
    stroke: #ffffff;
}

</style>

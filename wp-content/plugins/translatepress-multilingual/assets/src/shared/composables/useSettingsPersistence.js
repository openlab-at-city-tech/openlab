import { ref, computed, toRaw, onMounted, watch, nextTick } from 'vue'
import { __ } from '@wordpress/i18n'

const saveFailedMessage = __('Save failed', 'translatepress-multilingual')

export function useSettingsPersistence(config, {
    action,
    nonce,
    extraBody = () => ({}),
    onSaved = null,
}) {
    const savedSnapshot = ref({})

    onMounted(async () => {
        await nextTick()
        savedSnapshot.value = structuredClone(toRaw(config))
    })

    const isDirty = computed(() => {
        return JSON.stringify(config) !== JSON.stringify(savedSnapshot.value)
    })

    const saving    = ref(false)
    const justSaved = ref(false)
    const errorMsg  = ref('')

    function handleBeforeUnload(e) {
        if (!isDirty.value) return

        e.preventDefault()
        e.returnValue = ''
    }

    watch(isDirty, (dirty) => {
        if (dirty)
            window.addEventListener('beforeunload', handleBeforeUnload)
        else
            window.removeEventListener('beforeunload', handleBeforeUnload)
    }, { immediate: true })

    async function save() {
        saving.value = true
        errorMsg.value = ''

        try {
            const body = new FormData()
            body.append('action', action)
            body.append('nonce', nonce)
            body.append('config', JSON.stringify(config))

            Object.entries(extraBody()).forEach(([key, value]) => {
                body.append(key, value)
            })

            const res = await fetch(ajaxurl, {
                method: 'POST',
                credentials: 'same-origin',
                body
            })

            if (!res.ok) throw new Error(`HTTP ${res.status}`)

            const payload = await res.json()
            if (payload && payload.success === false) {
                throw new Error(payload.data || saveFailedMessage)
            }

            savedSnapshot.value = structuredClone(toRaw(config))

            if (typeof onSaved === 'function')
                onSaved(payload)

            justSaved.value = true
            setTimeout(() => { justSaved.value = false }, 2000)
        } catch (err) {
            errorMsg.value = err.message || saveFailedMessage
        } finally {
            saving.value = false
        }
    }

    function revert() {
        Object.assign(config, structuredClone(toRaw(savedSnapshot.value)))
    }

    return { save, revert, saving, justSaved, errorMsg, isDirty }
}

import { reactive, computed } from 'vue';

// This object will hold the viewport state for each scope.
const viewports = reactive({
    floater: 'desktop',
    shortcode: 'desktop'
});

/**
 * Provides reactive viewport state for a specific scope.
 *
 * @param {string} scope - The unique identifier for this viewport state (e.g., 'floater', 'shortcode').
 * @returns {{
 * selectedViewport: import('vue').ComputedRef<string>,
 * setViewport: (viewport: string) => void
 * }}
 */
export function usePreviewViewport(scope) {
    if (!scope || !viewports.hasOwnProperty(scope)) {
        console.warn(`Attempted to use usePreviewViewport with unknown or missing scope: ${scope}. Defaulting to 'desktop'.`);
        viewports[scope] = 'desktop'; // Initialize if not present
    }

    const selectedViewport = computed(() => viewports[scope]);

    const setViewport = (viewport) => {
        if (viewports.hasOwnProperty(scope)) {
            viewports[scope] = viewport;
        } else {
            console.warn(`Attempted to set viewport for unknown scope: ${scope}`);
        }
    };

    return {
        selectedViewport,
        setViewport,
    };
}
import { computed } from 'vue'
import { useSwitcherConfig, useLanguages } from './useSwitcherConfig'
import { useLayoutCustomizer } from "./layoutCustomizer/useLayoutCustomizer"

/**
 * Encapsulates all the reactive logic needed to preview the language switcher
 * for a given scope (floater, shortcode).
 *
 * @param {string} scope - One of "floater" | "shortcode"
 * @returns {{
 *   isDropdown: import('vue').ComputedRef<boolean>,
 *   displayedList: import('vue').ComputedRef<any>,
 *   switcherStyles: import('vue').ComputedRef<Record<string | number, string>>,
 *   showPoweredBy: import('vue').ComputedRef<boolean>,
 *   switcherPosition: import('vue').ComputedRef<string>
 * }}
 */
export function useSwitcherPreview(scope) {
    const cfg = useSwitcherConfig(scope)

    const { published } = useLanguages()

    const { styleVars: lcVars, switcherPosition } = useLayoutCustomizer(scope)

    const publishedArray = Object.entries(published).map(([code, settings]) => ({
        code,
        ...settings,
    }))

    const isDropdown = computed(() => cfg.type === 'dropdown')

    const isOppositeMode = computed(() => cfg.oppositeLanguage === true)

    const showPoweredBy = computed(() => cfg.showPoweredBy)

    const displayedList = computed(() => {
        if (cfg.oppositeLanguage === true)
            return publishedArray.slice(1, 2)

        const limit = isDropdown.value || scope !== 'floater' ? 4 : 2

        return publishedArray.slice(0, limit)
    })

    const bigFont = computed ( () => cfg.size === 'large' )

    const switcherStyles = computed(() => {
        const base = {
            '--bg'            : cfg.bgColor,
            '--bg-hover'      : cfg.bgHoverColor,
            '--text'          : cfg.textColor,
            '--text-hover'    : cfg.textHoverColor,
            '--border-color'  : cfg.borderColor,
            '--border-radius' : getBorderRadiusCss( cfg.borderRadius ),
            '--font-size'     : bigFont.value ? '16px' : '14px',
            '--flag-size'     : bigFont.value ? '20px' : '18px',
            '--flag-radius'   : `${cfg.flagRadius}px`,
            '--aspect-ratio'  : cfg.flagShape === 'rect' ? '4/3' : '1',
            '--transition-duration': cfg.enableTransitions ? '0.2s' : '0s'
        }

        const propertyMapScope = {
            shortcode: () => {
                return { ...base, '--border-width': cfg.borderWidth + 'px'}
            },

            floater: () => {
                return { ...base, ...lcVars.value }
            }
        }

        return propertyMapScope[scope]?.() || base;
    })

    const openOnClick = computed( () => cfg['clickLanguage'] === true );

    return {
        isDropdown,
        isOppositeMode,
        displayedList,
        switcherStyles,
        showPoweredBy,
        switcherPosition,
        openOnClick
    }
}

function getBorderRadiusCss(value) {
    if (Array.isArray(value)) {
        const [tl = 0, tr = 0, br = 0, bl = 0] = value
        return `${tl}px ${tr}px ${br}px ${bl}px`
    }

    if (typeof value === 'number') {
        return `${value}px`
    }

    return '0px'
}






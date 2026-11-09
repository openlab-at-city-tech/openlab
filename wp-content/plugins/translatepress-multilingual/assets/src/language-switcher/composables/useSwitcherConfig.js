import { inject, provide, reactive, readonly } from 'vue'

const KEYS = {
    floater:   'floater',
    shortcode: 'shortcode',
    menu:      'menu',
    languages: 'tpLanguages'
}

/**
 *
 * Retrieves a reactive config object for a specific language switcher scope
 * ("floater", "shortcode", "menu") from the Vue app context.
 *
 * This composable must be used after `provideSwitcherConfig()` has been called
 * in the app root to provide the reactive config for each scope.
 *
 * @param {string} scope - The switcher config scope to access ("floater", "shortcode", or "menu").
 * @returns {object}     - Settings object of the provided scope
 *
 * @throws Error throw an error if the requested config scope was not provided.
 *
 * @example
 * const cfg = useSwitcherConfig('floater')
 *
 */
export function useSwitcherConfig(scope) {
    const injected = inject(scope)

    if (!injected) {
        throw new Error(`Config scope "${scope}" not provided.`)
    }

    return injected
}

/**
 * Inject languages object.
 *
 * @returns {{
 *   published: Record<string,string>,
 *   default: { code: string, name: string }
 * }}
 */
export function useLanguages() {
    const langs = inject(KEYS.languages)

    if (!langs)
        throw new Error(`Languages not provided—did you forget to call provideSwitcherConfig?`)

    return langs
}

/**
 * Hydrates and provides the language switcher config + languages map
 * to the Vue app context. Called in app root.
 *
 * @param {Object} options
 * @param {Object} options.config    - { floater, shortcode, menu }
 * @param {Object} options.languages - { all, published, default }
 */
export function provideSwitcherConfig({ config, languages }) {
    const floaterState   = reactive(config.floater   || {})
    const shortcodeState = reactive(config.shortcode || {})
    const menuState      = reactive(config.menu      || {})

    const languagesState = readonly( languages || {})

    provide(KEYS.floater,   floaterState)
    provide(KEYS.shortcode, shortcodeState)
    provide(KEYS.menu,      menuState)
    provide(KEYS.languages, languagesState)
}


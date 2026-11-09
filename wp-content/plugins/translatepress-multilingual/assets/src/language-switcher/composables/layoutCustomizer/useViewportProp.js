import { computed } from 'vue'

/**
 * Returns a computed ref that always exposes the value
 * of `layoutCustomizer[currentViewport][key]`
 * with sane fallbacks.
 *
 * @param {Object}  cfg              – cfg
 * @param {Object}  viewportRef      – ref('desktop' | 'mobile')
 * @param {String}  key              – property name (e.g. 'customWidth')
 * @param {*}       fallback         – value to use when missing / 'default'
 * @param {Function} [transform]     – post-process function (optional)
 * @param {String} controlledBy      - property which dictates if custom value should be used
 */
export function useViewportProp(
    cfg,
    viewportRef,
    key,
    fallback,
    transform = v => v,
    controlledBy = undefined
) {
    return computed(() => {
        const layout = cfg.layoutCustomizer[viewportRef.value]

        if (controlledBy) {
            const controllerValue = layout[controlledBy]
            const raw = layout[key]

            const useCustom = controllerValue === 'custom' && typeof raw === 'number' && raw >= 0

            return transform(useCustom ? raw : fallback)
        }

        const raw = layout?.[key]

        const isInvalid =
                  raw === undefined ||
                  raw === null ||
                  raw === 'default' ||
                  (typeof raw === 'number' && raw <= 0)

        return transform(isInvalid ? fallback : raw)
    })
}
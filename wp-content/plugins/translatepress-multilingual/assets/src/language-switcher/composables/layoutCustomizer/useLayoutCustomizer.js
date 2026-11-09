import { useSwitcherConfig }   from '../useSwitcherConfig'
import { usePreviewViewport }  from '../usePreviewViewport'
import { useViewportProp }     from './useViewportProp'
import { computed }            from "vue"

export function useLayoutCustomizer(scope) {
    const cfg                  = useSwitcherConfig(scope)
    const { selectedViewport } = usePreviewViewport(scope)

    if (scope === 'menu') {
        const flagPos = useViewportProp( cfg, selectedViewport, 'flagIconPosition', 'before' )

        const flagShape = useViewportProp( cfg, selectedViewport, 'flagShape', 'rect' )

        const nameMode = useViewportProp( cfg, selectedViewport, 'languageNames', 'full' )

        return {
            positioning : computed( () => ({
                flagPos       : flagPos.value,
                flagShape     : flagShape.value,
                languageNames : nameMode.value
            }) )
        }
    }

    const DEFAULTS = {
        customWidth:      'auto',
        customPadding:    '10px 0',
        position:         'bottom-right',
        languageNames:    'full',
        flagIconPosition: 'before'
    }

    const width = useViewportProp(
        cfg,
        selectedViewport,
        'customWidth',
        'auto',
        n => typeof n === 'number' ? `${n}px` : n,
        'width'
    )

    const padding = useViewportProp(
        cfg, selectedViewport,
        'customPadding',
        DEFAULTS.customPadding,
        v => typeof v === 'number' ? `${v}px` : v,
        'padding'
    )

    const position = useViewportProp(
        cfg, selectedViewport,
        'position',
        DEFAULTS.position
    )

    const flagPos = useViewportProp(
        cfg, selectedViewport,
        'flagIconPosition',
        DEFAULTS.flagIconPosition
    )

    const nameMode = useViewportProp(
        cfg, selectedViewport,
        'languageNames',
        DEFAULTS.languageNames
    )

    /** For floating switcher */
    const styleVars = computed(() => {
        const padRaw = padding.value

        const edgeMap = {
            'bottom-right': { '--bottom': '0px', '--right': '14px', skip: 'bottom' },
            'bottom-left':  { '--bottom': '0px', '--left':  '14px', skip: 'bottom' },
            'top-right':    { '--top':    '0px', '--right': '14px', skip: 'top' },
            'top-left':     { '--top':    '0px', '--left':  '14px', skip: 'top' }
        }

        const { skip, ...edgeVars } = edgeMap[position.value] ?? {}

        const bw = cfg.borderWidth || 0

        const borderWidths = [
            skip === 'top'    ? '0' : `${bw}px`,
            `${bw}px`,
            skip === 'bottom' ? '0' : `${bw}px`,
            `${bw}px`
        ].join(' ')

        return {
            '--switcher-width':   width.value,
            '--switcher-padding': padRaw,
            '--border-width':     borderWidths,
            ...edgeVars
        }
    })

    const positioning = computed(() => ({
        flagPos:       flagPos.value,
        languageNames: nameMode.value
    }))

    return { styleVars, positioning, switcherPosition: position }
}
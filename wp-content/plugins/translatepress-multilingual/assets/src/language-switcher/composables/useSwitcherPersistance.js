import { useSwitcherConfig }      from './useSwitcherConfig'
import { useSettingsPersistence } from '../../shared/composables/useSettingsPersistence'

export function useSwitcherPersistence(scope) {
    const cfg = useSwitcherConfig(scope)

    return useSettingsPersistence(cfg, {
        action: 'trp_language_switcher_save',
        nonce: tpLangSwitcherData.nonce,
        extraBody: () => ({ scope }),
    })
}

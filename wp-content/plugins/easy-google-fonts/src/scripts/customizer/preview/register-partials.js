/**
 * Register Partials
 *
 * Loops through the defined settings and registers
 * any partials. Partials allow server side markup
 * to be injected into the page without requiring
 * a full page reload.
 *
 * wp.customize.Setting() represents the Model in
 * the customizer.
 *
 * @see \App\get_theme_settings_configuration()
 *
 * @return void
 *
 * @since 1.0.0
 * @version 1.0.0
 */
const {
  wp,
  egfCustomizePreview: { settings }
} = window;

/**
 * Register Partials
 *
 * @description Registers any partials with the
 *   customizer preview. The js required to
 *   initalise functionality for any injected
 *   partials need to be handled manually.
 *
 */
export const registerPartials = () => {
  wp.customize.bind('preview-ready', () => {
    for (const setting in settings.config) {
      // Check if the setting has a partial attached to it.
      if (settings.config[setting].selective_refresh) {
        const {
          container_inclusive: containerInclusive,
          fallback_refresh: fallbackRefresh,
          render_callback: renderCallback,
          selector,
          type
        } = settings.config[setting].selective_refresh;

        // Create a new partial and register it with the customizer api.
        const partial = new wp.customize.selectiveRefresh.Partial(`${settings.setting_key}[${setting}]`, {
          containerInclusive,
          fallbackRefresh,
          renderCallback,
          primarySetting: `${settings.setting_key}[${setting}]`,
          settings: [`${settings.setting_key}[${setting}]`],
          selector,
          type
        });

        const registeredPartial = wp.customize.selectiveRefresh.partial.add(partial);

        // Override the preparePlacement function as we
        // are registering the partials dynamically.
        registeredPartial.preparePlacement = function (placement) {
          if (!this._initialRenderComplete) {
            this._initialRenderComplete = true;
          } else {
            $(placement.container).addClass('customize-partial-refreshing');
          }
        };
      }
    }
  });
};

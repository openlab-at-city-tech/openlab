/**
 * Register Settings
 *
 * Registers the static settings we have defined
 * solely in the customizer and the setting is
 * dynamically saved upon submission.
 *
 * wp.customize.Setting() represents the Model in
 * the customizer.
 */
const { customize } = wp;
const { settings } = egfCustomize;

export const registerSettings = () => {
  customize.bind('ready', () => {
    registerBaseSettings();
    registerSubsettings();
  });
};

const registerBaseSettings = () => {
  const { config, setting_key, saved } = settings;
  for (const id in config) {
    const { default: default_value, transport } = config[id];
    wp.customize.add(
      new customize.Setting(`${setting_key}[${id}]`, saved[id], {
        transport,
        default: default_value,
        type: 'option'
      })
    );
  }
};

const registerSubsettings = () => {
  const { config, setting_key, saved } = settings;

  for (const id in config) {
    if (config[id].type === 'font') {
      const props = [
        'subset',
        'font_id',
        'font_name',
        'font_color',
        'font_weight',
        'font_style',
        'font_weight_style',
        'background_color',
        'stylesheet_url',
        'text_decoration',
        'text_transform',
        'line_height',
        'display',
        'font_size',
        'letter_spacing',
        'margin_top',
        'margin_right',
        'margin_bottom',
        'margin_left',
        'padding_top',
        'padding_right',
        'padding_bottom',
        'padding_left',
        'border_radius_top_left',
        'border_radius_top_right',
        'border_radius_bottom_left',
        'border_radius_bottom_right',
        'border_top_color',
        'border_top_style',
        'border_top_width',
        'border_bottom_color',
        'border_bottom_style',
        'border_bottom_width',
        'border_left_color',
        'border_left_style',
        'border_left_width',
        'border_right_color',
        'border_right_style',
        'border_right_width'
      ];

      const { default: default_value, transport } = config[id];

      props.forEach(prop => {
        customize.add(
          new customize.Setting(`${setting_key}[${id}][${prop}]`, saved[id][prop], {
            transport,
            default: default_value[prop],
            type: 'option'
          })
        );
      });
    }
  }
};

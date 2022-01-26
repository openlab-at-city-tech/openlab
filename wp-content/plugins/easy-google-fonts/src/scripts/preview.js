import { registerPartials } from './customizer/preview/register-partials';
import { enqueueStylesheet } from './customizer/preview/enqueueStylesheet';
import { setStyle } from './customizer/preview/setStyle';
const { setting_key, config, saved } = egfCustomizePreview.settings;

registerPartials();

const fontControls = Object.values(config).filter(
  ({ transport, type }) => transport === 'postMessage' && type === 'font'
);

wp.customize.bind('preview-ready', function () {
  fontControls.map(fontControl => {
    const { name } = fontControl;
    const { selector, force_styles, min_screen, max_screen } = fontControl.properties;
    const setting = `${setting_key}[${name}]`;

    const props = {
      setting,
      name,
      selector,
      force_styles,
      media_query: getMediaQuery(min_screen, max_screen),
      with_units: false
    };

    enqueueStylesheet(props);

    // Attach live preview listeners.
    setStyle({ ...props, subsetting: '[font_name]', css_rule: 'font-family' });
    setStyle({ ...props, subsetting: '[font_weight]', css_rule: 'font-weight' });
    setStyle({ ...props, subsetting: '[font_style]', css_rule: 'font-style' });
    setStyle({ ...props, subsetting: '[text_decoration]', css_rule: 'text-decoration' });
    setStyle({ ...props, subsetting: '[text_transform]', css_rule: 'text-transform' });
    setStyle({ ...props, subsetting: '[font_color]', css_rule: 'color' });
    setStyle({ ...props, subsetting: '[background_color]', css_rule: 'background-color' });
    setStyle({ ...props, subsetting: '[font_size]', css_rule: 'font-size', with_units: true });
    setStyle({ ...props, subsetting: '[line_height]', css_rule: 'line-height' });
    setStyle({ ...props, subsetting: '[letter_spacing]', css_rule: 'letter-spacing', with_units: true });
    setStyle({ ...props, subsetting: '[margin_top]', css_rule: 'margin-top', with_units: true });
    setStyle({ ...props, subsetting: '[margin_bottom]', css_rule: 'margin-bottom', with_units: true });
    setStyle({ ...props, subsetting: '[margin_left]', css_rule: 'margin-left', with_units: true });
    setStyle({ ...props, subsetting: '[margin_right]', css_rule: 'margin-right', with_units: true });
    setStyle({ ...props, subsetting: '[padding_top]', css_rule: 'padding-top', with_units: true });
    setStyle({ ...props, subsetting: '[padding_bottom]', css_rule: 'padding-bottom', with_units: true });
    setStyle({ ...props, subsetting: '[padding_left]', css_rule: 'padding-left', with_units: true });
    setStyle({ ...props, subsetting: '[padding_right]', css_rule: 'padding-right', with_units: true });
    setStyle({ ...props, subsetting: '[border_top_color]', css_rule: 'border-top-color' });
    setStyle({ ...props, subsetting: '[border_top_style]', css_rule: 'border-top-style' });
    setStyle({ ...props, subsetting: '[border_top_width]', css_rule: 'border-top-width', with_units: true });
    setStyle({ ...props, subsetting: '[border_bottom_color]', css_rule: 'border-bottom-color' });
    setStyle({ ...props, subsetting: '[border_bottom_style]', css_rule: 'border-bottom-style' });
    setStyle({ ...props, subsetting: '[border_bottom_width]', css_rule: 'border-bottom-width', with_units: true });
    setStyle({ ...props, subsetting: '[border_left_color]', css_rule: 'border-left-color' });
    setStyle({ ...props, subsetting: '[border_left_style]', css_rule: 'border-left-style' });
    setStyle({ ...props, subsetting: '[border_left_width]', css_rule: 'border-left-width', with_units: true });
    setStyle({ ...props, subsetting: '[border_right_color]', css_rule: 'border-right-color' });
    setStyle({ ...props, subsetting: '[border_right_style]', css_rule: 'border-right-style' });
    setStyle({ ...props, subsetting: '[border_right_width]', css_rule: 'border-right-width', with_units: true });
    setStyle({
      ...props,
      subsetting: '[border_radius_top_left]',
      css_rule: 'border-top-left-radius',
      with_units: true
    });
    setStyle({
      ...props,
      subsetting: '[border_radius_top_right]',
      css_rule: 'border-top-right-radius',
      with_units: true
    });
    setStyle({
      ...props,
      subsetting: '[border_radius_bottom_left]',
      css_rule: 'border-bottom-left-radius',
      with_units: true
    });
    setStyle({
      ...props,
      subsetting: '[border_radius_bottom_right]',
      css_rule: 'border-bottom-right-radius',
      with_units: true
    });
    setStyle({ ...props, subsetting: '[display]', css_rule: 'display' });
  });
});

/**
 * Get Media Query Rules
 * @param {object} min
 * @param {object} max
 */
const getMediaQuery = (min, max) => {
  let mediaQuery = { open: '', close: '' };

  if (!min.amount && !max.amount) {
    return mediaQuery;
  }

  let mediaQueryRules = [];

  if (min.amount) {
    mediaQueryRules.push(`(min-width: ${min.amount}${min.unit})`);
  }

  if (max.amount) {
    mediaQueryRules.push(`(max-width: ${max.amount}${max.unit})`);
  }

  return {
    open: `@media ${mediaQueryRules.join(' and ')} {`,
    close: '}'
  };
};

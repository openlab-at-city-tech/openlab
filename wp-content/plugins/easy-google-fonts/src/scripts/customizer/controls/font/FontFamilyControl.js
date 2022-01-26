// import Select, { components } from 'react-select';
import WindowedSelect from 'react-windowed-select';

import { _x, __ } from '@wordpress/i18n';
import { createRef, useMemo } from '@wordpress/element';
import { sanitizeFontKey } from '../../utils/sanitizeFontKey';

/**
 * Get Local Font Options
 */
const getLocalFontOptions = (subset = '') => {
  const { default_fonts } = window.egfCustomize;

  const options = Object.keys(default_fonts)
    .map(font_id => {
      const { family, variants } = default_fonts[font_id];
      return {
        label: family,
        value: font_id,
        variants
      };
    })
    .filter(() => ['all', 'latin,all', 'latin', 'latin-ext'].includes(subset));

  return [{ label: 'local-fonts', options }];
};

/**
 * Get Google Font Options
 */
const getGoogleFontOptions = (subset = '') => {
  const { egfGoogleFontsByCategory } = window;
  const fontsByCategory = Object.keys(egfGoogleFontsByCategory);

  return fontsByCategory.map(category => {
    const fonts = egfGoogleFontsByCategory[category];
    const options = fonts
      .filter(font => {
        if ('all' === subset || 'latin,all' === subset || !subset) {
          return true;
        }
        return font.subsets.includes(subset);
      })
      .map(font => {
        const { family, variants } = font;
        return {
          label: family,
          value: sanitizeFontKey(family),
          variants
        };
      });

    return { label: category, options };
  });
};

/**
 * Font Family Control Component
 * @param {*} props
 */
const FontFamilyControl = props => {
  const {
    control,
    className,
    subset,
    fontId,
    setFontId,
    setFontName,
    setFontWeight,
    setFontStyle,
    setFontWeightStyle,
    setStylesheetUrl
  } = props;

  const localFonts = getLocalFontOptions(subset);
  const googleFonts = getGoogleFontOptions(subset);
  const groupedOptions = useMemo(() => [...localFonts, ...googleFonts], []);

  /**
   * Set Font Callback
   */
  const setFont = font => {
    const { label, value, variants = [] } = font;

    const hasRegularFontWeight = variants.includes('regular');
    const newFontWeightStyle = hasRegularFontWeight ? 'regular' : variants[0];
    const newFontWeight = Number.isNaN(parseInt(newFontWeightStyle, 10)) ? 400 : parseInt(newFontWeightStyle, 10);
    const newFontStyle = newFontWeightStyle.includes('italic') ? 'italic' : 'normal';

    setFontId(value);
    setFontName(label);
    setFontWeightStyle(newFontWeightStyle);
    setFontWeight(newFontWeight);
    setFontStyle(newFontStyle);
    setStylesheetUrl('');
  };

  /**
   * Reset Font Controls
   */
  const resetDefaultFont = () => {
    const defaultSettings = control.settings.default.default;

    setFontId(defaultSettings.font_id);
    setFontName(defaultSettings.font_name);
    setFontWeightStyle(defaultSettings.font_weight_style);
    setFontWeight(defaultSettings.font_weight);
    setFontStyle(defaultSettings.font_style);
    setStylesheetUrl(defaultSettings.stylesheet_url);
  };

  /**
   * Get Font From Grouped Options
   */
  const getFontFromGroupedOptions = (fontId = '') => {
    for (let group in groupedOptions) {
      const { options } = groupedOptions[group];

      for (let font in options) {
        if (options[font].value === fontId) {
          return options[font];
        }
      }
    }

    return {};
  };

  const selectRef = createRef();

  return (
    <div className={`egf-font-family-control ${className}`}>
      <div className="components-base-control">
        <label className="components-input-control__label" onClick={() => selectRef.current.focus()}>
          {_x('Font Family', 'Font family field label for the customizer font control.', 'easy-google-fonts')}
        </label>
        <WindowedSelect
          grouped
          ref={selectRef}
          value={getFontFromGroupedOptions(fontId)}
          options={groupedOptions}
          openMenuOnFocus={true}
          closeMenuOnSelect={false}
          isSearchable={true}
          isClearable={fontId !== control.settings.default.default.font_id}
          classNamePrefix="egf-select"
          theme={theme => ({
            ...theme,
            colors: {
              ...theme.colors,
              primary: '#007cba',
              primary75: '#589dcc',
              primary50: '#91bedd',
              primary25: '#c8deed'
            }
          })}
          onChange={font => {
            if (font) {
              setFont(font);
            } else {
              resetDefaultFont();
            }
          }}
        />
      </div>
    </div>
  );
};

export default FontFamilyControl;

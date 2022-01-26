import { __, _x } from '@wordpress/i18n';
import { Platform } from '@wordpress/element';

/**
 * External dependencies
 */
import { isEmpty } from 'lodash';

const isWeb = Platform.OS === 'web';

/**
 * Font Size Units
 */
export const FONT_SIZE_UNITS = {
  px: {
    value: 'px',
    label: isWeb ? 'px' : __('Pixels (px)', 'easy-google-fonts'),
    default: '',
    a11yLabel: __('Pixels (px)', 'easy-google-fonts'),
    initial: 16,
    min: 10,
    max: 100,
    step: 1
  },
  percent: {
    value: '%',
    label: isWeb ? '%' : __('Percentage (%)', 'easy-google-fonts'),
    default: '',
    a11yLabel: __('Percent (%)', 'easy-google-fonts'),
    initial: 100,
    min: 10,
    max: 300,
    step: 0.1
  },
  em: {
    value: 'em',
    label: isWeb ? 'em' : __('Relative to parent font size (em)', 'easy-google-fonts'),
    default: '',
    a11yLabel: _x('ems', 'Relative to parent font size (em)', 'easy-google-fonts'),
    initial: 1,
    min: 0.625,
    max: 6.25,
    step: 0.01
  },
  rem: {
    value: 'rem',
    label: isWeb ? 'rem' : __('Relative to root font size (rem)', 'easy-google-fonts'),
    default: '',
    a11yLabel: _x('rems', 'Relative to root font size (rem)', 'easy-google-fonts'),
    initial: 1,
    min: 0.625,
    max: 6.25,
    step: 0.01
  }
};

/**
 * Letter Spacing Units
 */
export const LETTER_SPACING_UNITS = {
  px: {
    value: 'px',
    label: isWeb ? 'px' : __('Pixels (px)', 'easy-google-fonts'),
    default: '',
    a11yLabel: __('Pixels (px)', 'easy-google-fonts'),
    initial: 0,
    min: -5,
    max: 20,
    step: 1
  },
  em: {
    value: 'em',
    label: isWeb ? 'em' : __('Relative to parent font size (em)', 'easy-google-fonts'),
    default: '',
    a11yLabel: _x('ems', 'Relative to parent font size (em)', 'easy-google-fonts'),
    initial: 1,
    min: -0.3125,
    max: 1.25,
    step: 0.01
  },
  rem: {
    value: 'rem',
    label: isWeb ? 'rem' : __('Relative to root font size (rem)', 'easy-google-fonts'),
    default: '',
    a11yLabel: _x('rems', 'Relative to root font size (rem)', 'easy-google-fonts'),
    initial: 1,
    min: -0.313,
    max: 1.25,
    step: 0.01
  }
};

/**
 * Margin Units
 */
export const MARGIN_UNITS = {
  px: {
    value: 'px',
    label: isWeb ? 'px' : __('Pixels (px)', 'easy-google-fonts'),
    default: '',
    a11yLabel: __('Pixels (px)', 'easy-google-fonts'),
    initial: 0,
    min: 0,
    max: 400,
    step: 1
  },
  em: {
    value: 'em',
    label: isWeb ? 'em' : __('Relative to parent font size (em)', 'easy-google-fonts'),
    default: '',
    a11yLabel: _x('ems', 'Relative to parent font size (em)', 'easy-google-fonts'),
    initial: 0,
    min: 0,
    max: 25,
    step: 0.01
  },
  rem: {
    value: 'rem',
    label: isWeb ? 'rem' : __('Relative to root font size (rem)', 'easy-google-fonts'),
    default: '',
    a11yLabel: _x('rems', 'Relative to root font size (rem)', 'easy-google-fonts'),
    initial: 0,
    min: 0,
    max: 25,
    step: 0.01
  }
};

/**
 * Padding Units
 */
export const PADDING_UNITS = {
  px: {
    value: 'px',
    label: isWeb ? 'px' : __('Pixels (px)', 'easy-google-fonts'),
    default: '',
    a11yLabel: __('Pixels (px)', 'easy-google-fonts'),
    initial: 0,
    min: 0,
    max: 400,
    step: 1
  },
  em: {
    value: 'em',
    label: isWeb ? 'em' : __('Relative to parent font size (em)', 'easy-google-fonts'),
    default: '',
    a11yLabel: _x('ems', 'Relative to parent font size (em)', 'easy-google-fonts'),
    initial: 0,
    min: 0,
    max: 25,
    step: 0.01
  },
  rem: {
    value: 'rem',
    label: isWeb ? 'rem' : __('Relative to root font size (rem)', 'easy-google-fonts'),
    default: '',
    a11yLabel: _x('rems', 'Relative to root font size (rem)', 'easy-google-fonts'),
    initial: 0,
    min: 0,
    max: 25,
    step: 0.01
  }
};

/**
 * Border Units
 */
export const BORDER_UNITS = {
  px: {
    value: 'px',
    label: isWeb ? 'px' : __('Pixels (px)', 'easy-google-fonts'),
    default: '',
    a11yLabel: __('Pixels (px)', 'easy-google-fonts'),
    initial: 16,
    min: 0,
    max: 100,
    step: 1
  },
  em: {
    value: 'em',
    label: isWeb ? 'em' : __('Relative to parent font size (em)', 'easy-google-fonts'),
    default: '',
    a11yLabel: _x('ems', 'Relative to parent font size (em)', 'easy-google-fonts'),
    initial: 1,
    min: 0.625,
    max: 6.25,
    step: 0.01
  },
  rem: {
    value: 'rem',
    label: isWeb ? 'rem' : __('Relative to root font size (rem)', 'easy-google-fonts'),
    default: '',
    a11yLabel: _x('rems', 'Relative to root font size (rem)', 'easy-google-fonts'),
    initial: 1,
    min: 0.625,
    max: 6.25,
    step: 0.01
  }
};

/**
 * Border Radius Units
 */
export const BORDER_RADIUS_UNITS = {
  px: {
    value: 'px',
    label: isWeb ? 'px' : __('Pixels (px)', 'easy-google-fonts'),
    default: '',
    a11yLabel: __('Pixels (px)', 'easy-google-fonts'),
    initial: 16,
    min: 0,
    max: 100,
    step: 1
  },
  em: {
    value: 'em',
    label: isWeb ? 'em' : __('Relative to parent font size (em)', 'easy-google-fonts'),
    default: '',
    a11yLabel: _x('ems', 'Relative to parent font size (em)', 'easy-google-fonts'),
    initial: 1,
    min: 0.625,
    max: 6.25,
    step: 0.01
  },
  rem: {
    value: 'rem',
    label: isWeb ? 'rem' : __('Relative to root font size (rem)', 'easy-google-fonts'),
    default: '',
    a11yLabel: _x('rems', 'Relative to root font size (rem)', 'easy-google-fonts'),
    initial: 1,
    min: 0.625,
    max: 6.25,
    step: 0.01
  }
};

/**
 * Parses a number and unit from a value.
 *
 * @param {string}        initialValue Value to parse
 * @param {Array<Object>} units        Units to derive from.
 * @return {Array<number, string>} The extracted number and unit.
 */
export function parseUnit(initialValue, units) {
  const value = String(initialValue).trim();

  let num = parseFloat(value, 10);
  num = isNaN(num) ? '' : num;

  const unitMatch = value.match(/[\d.\-\+]*\s*(.*)/)[1];

  let unit = unitMatch !== undefined ? unitMatch : '';
  unit = unit.toLowerCase();

  if (hasUnits(units)) {
    const match = units.find(item => item.value === unit);
    unit = match?.value;
  } else {
    unit = 'px';
  }

  return [num, unit];
}

/**
 * Checks if units are defined.
 *
 * @param {any} units Units to check.
 * @return {boolean} Whether units are defined.
 */
export function hasUnits(units) {
  return !isEmpty(units) && units.length > 1 && units !== false;
}

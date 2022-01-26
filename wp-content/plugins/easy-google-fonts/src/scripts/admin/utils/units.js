import { __, _x } from '@wordpress/i18n';
import { Platform } from '@wordpress/element';

/**
 * External dependencies
 */
import { isEmpty } from 'lodash';

const isWeb = Platform.OS === 'web';

/**
 * Min Screen Units
 */
export const MIN_SCREEN_UNITS = {
  px: {
    value: 'px',
    label: isWeb ? 'px' : __('Pixels (px)', 'easy-google-fonts'),
    default: '',
    a11yLabel: __('Pixels (px)', 'easy-google-fonts'),
    initial: 16,
    min: 0,
    max: 1400,
    step: 10
  },
  em: {
    value: 'em',
    label: isWeb ? 'em' : __('Relative to parent font size (em)', 'easy-google-fonts'),
    default: '',
    a11yLabel: _x('ems', 'Relative to parent font size (em)', 'easy-google-fonts'),
    initial: 1,
    min: 0,
    max: 87.5,
    step: 0.1
  },
  rem: {
    value: 'rem',
    label: isWeb ? 'rem' : __('Relative to root font size (rem)', 'easy-google-fonts'),
    default: '',
    a11yLabel: _x('rems', 'Relative to root font size (rem)', 'easy-google-fonts'),
    initial: 1,
    min: 0,
    max: 87.5,
    step: 0.1
  }
};

/**
 * Max Screen Units
 */
export const MAX_SCREEN_UNITS = {
  px: {
    value: 'px',
    label: isWeb ? 'px' : __('Pixels (px)', 'easy-google-fonts'),
    default: '',
    a11yLabel: __('Pixels (px)', 'easy-google-fonts'),
    initial: 16,
    min: 0,
    max: 1400,
    step: 10
  },
  em: {
    value: 'em',
    label: isWeb ? 'em' : __('Relative to parent font size (em)', 'easy-google-fonts'),
    default: '',
    a11yLabel: _x('ems', 'Relative to parent font size (em)', 'easy-google-fonts'),
    initial: 1,
    min: 0,
    max: 87.5,
    step: 0.1
  },
  rem: {
    value: 'rem',
    label: isWeb ? 'rem' : __('Relative to root font size (rem)', 'easy-google-fonts'),
    default: '',
    a11yLabel: _x('rems', 'Relative to root font size (rem)', 'easy-google-fonts'),
    initial: 1,
    min: 0,
    max: 87.5,
    step: 0.1
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

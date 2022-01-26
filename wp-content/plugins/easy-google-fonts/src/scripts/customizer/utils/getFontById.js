import { sanitizeFontKey } from './sanitizeFontKey';

export const getFontById = fontId => {
  const { egfGoogleFontsByKey, egfCustomize } = window;

  if (!fontId) {
    return undefined;
  }

  const isDefaultFont = typeof egfCustomize.default_fonts[fontId] !== 'undefined';
  const isGoogleFont = typeof egfGoogleFontsByKey[fontId] !== 'undefined';

  if (!isDefaultFont && !isGoogleFont) {
    return undefined;
  }

  const font = isDefaultFont ? egfCustomize.default_fonts[fontId] : egfGoogleFontsByKey[fontId];

  return {
    ...font,
    label: font.family,
    value: sanitizeFontKey(font.family)
  };
};

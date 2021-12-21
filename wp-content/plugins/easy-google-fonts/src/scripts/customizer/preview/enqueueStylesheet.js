import $ from 'jquery';

const head = $('#egf-customizer-preview');

export const enqueueStylesheet = props => {
  const { setting } = props;

  wp.customize.preview.bind(setting, ({ fontName, fontWeightStyle }) => {
    const isDefaultFont = typeof egfCustomizePreview.default_fonts[sanitizeKey(fontName)] !== 'undefined';

    if (!fontName || isDefaultFont) {
      return;
    }

    const fontWeight = Number.isNaN(parseInt(fontWeightStyle, 10)) ? 400 : parseInt(fontWeightStyle, 10);
    const isItalic = fontWeightStyle.includes('italic');
    const isRegular = fontWeight === 400;

    let variants = '';

    if (isRegular && isItalic) {
      variants = ':ital@1';
    }

    if (isItalic && !isRegular) {
      variants = `:ital,wght@1,${fontWeight}`;
    }

    if (!isItalic && !isRegular) {
      variants = `:wght@${fontWeight}`;
    }

    const url = `https://fonts.googleapis.com/css2?family=${fontName.replaceAll(' ', '+')}${variants}&display=swap`;
    const styleId = `egf-stylesheet-${sanitizeKey(fontName)}_${sanitizeKey(fontWeightStyle)}`;
    const stylesheet = `<link id="${styleId}" rel="stylesheet" href="${url}">`;

    if ($(`#${styleId}`).length === 0) {
      $(stylesheet).insertBefore(head);
    }
  });
};

const sanitizeKey = (name = '') => name.toLowerCase().replaceAll(' ', '_');

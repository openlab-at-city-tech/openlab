import $ from 'jquery';

const head = $('#egf-customizer-preview');

export const setStyle = props => {
  const { name, setting, subsetting, selector, force_styles, media_query, css_rule, with_units } = props;

  wp.customize(`${setting}${subsetting}`, value => {
    const styleId = `egf-font-${name}-${css_rule}`;
    value.bind(to => {
      if (!to) {
        $(`#${styleId}`).remove();
        return;
      }

      let css_prop = with_units ? `${to.amount}${to.unit}` : `${to}`;

      if ('font_family' === subsetting) {
        css_prop = `"${to}"`;
      }

      let style = `
        <style id='${styleId}' type='text/css'>
          ${media_query.open}
            ${selector} {
              ${css_rule}: ${css_prop}${force_styles ? '!important' : ''};
            }
          ${media_query.close}
        </style>
      `;

      if ($(`#${styleId}`).length !== 0) {
        $(`#${styleId}`).replaceWith(style);
      } else {
        $(style).insertAfter(head);
      }
    });
  });
};

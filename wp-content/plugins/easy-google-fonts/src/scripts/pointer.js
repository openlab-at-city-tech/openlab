/**
 * External Dependancies
 */
import $ from 'jquery';

/**
 * WordPress Dependancies
 */
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';

$(function () {
  const content = `
     <h3>${__('Easy Google Fonts', 'easy-google-fonts')}</h3>
     <p>${__('Create and manage your customizer font controls in the Settings menu.', 'easy-google-fonts')}</p>
   `;

  $('#menu-settings')
    .pointer({
      content,
      close: () => {
        apiFetch({
          path: '/easy-google-fonts/v1/hide-pointer',
          method: 'POST'
        });
      }
    })
    .pointer('open');
});

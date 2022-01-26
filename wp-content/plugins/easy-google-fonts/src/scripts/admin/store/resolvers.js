// WordPress dependencies.
import { __, sprintf } from '@wordpress/i18n';
import { apiFetch } from '@wordpress/data-controls';
import { addQueryArgs } from '@wordpress/url';

import { hydrateFontControls, hydrateApiKey } from './actions';

/**
 * Font Control Retrieval Resolvers
 */
export function* getFontControls() {
  const path = addQueryArgs('/wp/v2/easy-google-fonts', {
    per_page: -1,
    order: 'asc',
    orderby: 'title',
    _fields: ['id', 'title', 'meta']
  });

  const fontControls = yield apiFetch({ path });

  if (fontControls) {
    let allFontControls = {};
    for (let fontControl of fontControls) {
      allFontControls[fontControl.id] = fontControl;
    }

    return hydrateFontControls(allFontControls);
  }

  return;
}

export function* getApiKey() {
  const path = addQueryArgs('/easy-google-fonts/v1/api_key', {});
  const apiKey = yield apiFetch({ path });

  if (apiKey) {
    return hydrateApiKey(apiKey);
  }

  return;
}

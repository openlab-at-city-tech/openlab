import { _x } from '@wordpress/i18n';
import { sanitizeFontKey } from '../utils/sanitizeFontKey';
import fallbackGoogleFonts from '../../../fonts/webfonts.json';

export const fetchGoogleFonts = () => {
  window.egfGoogleFonts = fallbackGoogleFonts;
  sortFontsByCategory();
  sortFontsByKey();
  determineAvailableLanguages();

  if (egfCustomize.api_key) {
    const url = `https://www.googleapis.com/webfonts/v1/webfonts?sort=alpha&key=${egfCustomize.api_key}`;

    fetch(url)
      .then(res => {
        if (res.status !== 200) {
          throw new Error(
            _x(
              `Unable to fetch the latest fonts from google, please ensure that you have entered a valid api key in the plugin settings page.`,
              'Google API error message displayed in the customizer browser console.',
              'easy-google-fonts'
            )
          );
        }

        return res.json();
      })
      .then(data => {
        window.egfGoogleFonts = data;
        sortFontsByCategory();
        sortFontsByKey();
        determineAvailableLanguages();
      })
      .catch(err => {
        if (egfCustomize.api_key) {
          console.log(err);
        }
      });
  }
};

const sortFontsByCategory = () => {
  window.egfGoogleFontsByCategory = window.egfGoogleFonts.items.reduce((acc, font) => {
    if (typeof acc[font.category] === 'undefined') {
      acc[font.category] = [{ ...font, label: font.family, value: sanitizeFontKey(font.family) }];
    } else {
      acc[font.category].push({ ...font, label: font.family, value: sanitizeFontKey(font.family) });
    }
    return acc;
  }, {});
};

const sortFontsByKey = () => {
  window.egfGoogleFontsByKey = window.egfGoogleFonts.items.reduce((acc, font) => {
    acc[sanitizeFontKey(font.family)] = font;
    return acc;
  }, {});
};

const determineAvailableLanguages = () => {
  const { egfGoogleFonts } = window;
  const allLanguages = egfGoogleFonts.items.reduce((languages, font) => {
    languages = [...languages, ...font.subsets];
    return languages;
  }, []);

  window.egfGoogleFontLanguages = [...new Set(allLanguages)];
};

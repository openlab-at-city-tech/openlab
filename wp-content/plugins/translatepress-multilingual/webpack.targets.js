/*
    Every webpack build target, keyed by MIX_TARGET. Each one is built in its own
    webpack run. `npm run production` (bin/build-assets.js) builds all of them, so a
    new Vue app only needs an entry here — no new npm script, no versions.sh change.
    The first key is the default when MIX_TARGET is unset (`npm run watch`).
*/
module.exports = {
    /* --- Translation Editor JS/CSS --- */
    editor: ( mix, sassOptions ) => {
        mix.js('assets/src/js/trp-editor.js', 'assets/js')
           .js('assets/src/js/trp-string-translation-editor.js', 'assets/js')
           .sass('assets/src/scss/trp-editor.scss', 'assets/css', sassOptions);
    },

    /* --- Language Switcher JS/CSS --- */
    switcher: ( mix, sassOptions ) => {
        mix.js('assets/src/language-switcher/main.js', 'assets/js/trp-lang-switcher-configurator.js')
           .sass('assets/src/language-switcher/styles/index.scss', 'assets/css/trp-lang-switcher-configurator.css', sassOptions);
    },

    /* --- Automatic Language Detection Popup Configurator JS/CSS --- */
    ald: ( mix ) => {
        mix.js('assets/src/automatic-language-detection/main.js', 'assets/js/trp-ald-popup-configurator.js')
           .sass('assets/src/automatic-language-detection/styles/index.scss', 'assets/css/trp-ald-popup-configurator.css');
    },
};

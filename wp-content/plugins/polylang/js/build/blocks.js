/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 677
(module) {

module.exports = (function() { return this["React"]; }());

/***/ },

/***/ 631
(module) {

module.exports = (function() { return this["wp"]["apiFetch"]; }());

/***/ },

/***/ 89
(module) {

module.exports = (function() { return this["wp"]["blockEditor"]; }());

/***/ },

/***/ 545
(module) {

module.exports = (function() { return this["wp"]["blocks"]; }());

/***/ },

/***/ 959
(module) {

module.exports = (function() { return this["wp"]["components"]; }());

/***/ },

/***/ 987
(module) {

module.exports = (function() { return this["wp"]["data"]; }());

/***/ },

/***/ 2
(module) {

module.exports = (function() { return this["wp"]["editor"]; }());

/***/ },

/***/ 601
(module) {

module.exports = (function() { return this["wp"]["element"]; }());

/***/ },

/***/ 873
(module) {

module.exports = (function() { return this["wp"]["hooks"]; }());

/***/ },

/***/ 75
(module) {

module.exports = (function() { return this["wp"]["i18n"]; }());

/***/ },

/***/ 672
(module) {

module.exports = (function() { return this["wp"]["notices"]; }());

/***/ },

/***/ 933
(module) {

module.exports = (function() { return this["wp"]["primitives"]; }());

/***/ }

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	const __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		const cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		const module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = (module) => {
/******/ 		const getter = module && module.__esModule ?
/******/ 			() => (module['default']) :
/******/ 			() => (module);
/******/ 		__webpack_require__.d(getter, { a: getter });
/******/ 		return getter;
/******/ 	};
/******/ 	
/******/ 	/* webpack/runtime/concatenation wrap */
/******/ 	// wrap a concatenated module body as a lazy, memoized accessor; mod is
/******/ 	// set before the body runs so re-entrant calls (require cycles) observe
/******/ 	// the partial exports like Node.js
/******/ 	__webpack_require__.cw = (body) => {
/******/ 		var mod;
/******/ 		return () => {
/******/ 			if (body) {
/******/ 				var fn = body;
/******/ 				body = 0;
/******/ 				mod = { exports: {} };
/******/ 				fn.call(mod.exports, mod, mod.exports);
/******/ 			}
/******/ 			return mod.exports;
/******/ 		};
/******/ 	};
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	// define getter/value functions for harmony exports
/******/ 	__webpack_require__.d = (exports, definition) => {
/******/ 		for(var key in definition) {
/******/ 			if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 				Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 			}
/******/ 		}
/******/ 	};
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop));
/******/ 	
/************************************************************************/
let __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";

// MODULE: ./node_modules/react/cjs/react-jsx-runtime.production.min.js
var react_jsx_runtime_production_min_namespaceFn = /*#__PURE__*/__webpack_require__.cw(function(module, exports) {
/**
 * @license React
 * react-jsx-runtime.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */
var f=(external_React_namespaceFn()),k=Symbol.for("react.element"),l=Symbol.for("react.fragment"),m=Object.prototype.hasOwnProperty,n=f.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,p={key:!0,ref:!0,__self:!0,__source:!0};
function q(c,a,g){var b,d={},e=null,h=null;void 0!==g&&(e=""+g);void 0!==a.key&&(e=""+a.key);void 0!==a.ref&&(h=a.ref);for(b in a)m.call(a,b)&&!p.hasOwnProperty(b)&&(d[b]=a[b]);if(c&&c.defaultProps)for(b in a=c.defaultProps,a)void 0===d[b]&&(d[b]=a[b]);return{$$typeof:k,type:c,key:e,ref:h,props:d,_owner:n.current}}exports.Fragment=l;exports.jsx=q;exports.jsxs=q;

});

// MODULE: ./node_modules/react/jsx-runtime.js
var jsx_runtime_namespaceFn = /*#__PURE__*/__webpack_require__.cw(function(module, exports) {


if (true) {
  module.exports = (react_jsx_runtime_production_min_namespaceFn());
} else // removed by dead control flow
{}

});

// EXTERNAL MODULE: external "React"
var external_React_namespaceFn = () => {
	return __webpack_require__(677);
};

// EXTERNAL MODULE: external {"this":["wp","blocks"]}
var external_this_wp_blocks_ = __webpack_require__(545);
// EXTERNAL MODULE: external {"this":["wp","primitives"]}
var external_this_wp_primitives_ = __webpack_require__(933);
;// ./node_modules/react/jsx-runtime.js
jsx_runtime_namespaceFn();

;// ./node_modules/@wpsyntex/polylang-react-library/build/icons/translation.js
/**
 * Translation icon - translation Dashicon.
 */

/**
 * WordPress dependencies
 */


const isPrimitivesComponents = 'undefined' !== typeof wp.primitives;
const translation = isPrimitivesComponents ? /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_primitives_.SVG, {
  width: "20",
  height: "20",
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 20 20",
  children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_primitives_.Path, {
    d: "M11 7H9.49c-.63 0-1.25.3-1.59.7L7 5H4.13l-2.39 7h1.69l.74-2H7v4H2c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2h7c1.1 0 2 .9 2 2v2zM6.51 9H4.49l1-2.93zM10 8h7c1.1 0 2 .9 2 2v7c0 1.1-.9 2-2 2h-7c-1.1 0-2-.9-2-2v-7c0-1.1.9-2 2-2zm7.25 5v-1.08h-3.17V9.75h-1.16v2.17H9.75V13h1.28c.11.85.56 1.85 1.28 2.62-.87.36-1.89.62-2.31.62-.01.02.22.97.2 1.46.84 0 2.21-.5 3.28-1.15 1.09.65 2.48 1.15 3.34 1.15-.02-.49.2-1.44.2-1.46-.43 0-1.49-.27-2.38-.63.7-.77 1.14-1.77 1.25-2.61h1.36zm-3.81 1.93c-.5-.46-.85-1.13-1.01-1.93h2.09c-.17.8-.51 1.47-1 1.93l-.04.03s-.03-.02-.04-.03z"
  })
}) : 'translation';
/* harmony default export */ const icons_translation = (translation);
;// ./js/src/blocks/language-switcher/deprecated.js
/**
 * Deprecated language switcher block.
 */

const V2Attributes = {
  layout: 'vertical',
  show_labels: 'names',
  show_flags: false,
  force_home: false,
  hide_current: false,
  hide_if_no_translation: false,
  flag_aspect_ratio: '3:2',
  flag_border_radius: 0,
  flag_width: '18px',
  flag_label_spacing: '0.3em',
  className: ''
};
const v1 = {
  attributes: {
    dropdown: {
      type: 'boolean',
      default: false
    },
    show_names: {
      type: 'boolean',
      default: true
    },
    show_flags: {
      type: 'boolean',
      default: false
    },
    force_home: {
      type: 'boolean',
      default: false
    },
    hide_current: {
      type: 'boolean',
      default: false
    },
    hide_if_no_translation: {
      type: 'boolean',
      default: false
    },
    className: {
      type: 'string',
      default: ''
    }
  },
  isEligible: attributes => 'dropdown' in attributes || 'show_names' in attributes,
  migrate(attributes) {
    const {
      dropdown,
      show_names,
      show_flags,
      force_home,
      hide_current,
      hide_if_no_translation,
      className
    } = attributes;
    return {
      ...V2Attributes,
      layout: dropdown ? 'select' : 'vertical',
      show_labels: show_names ? 'names' : '',
      show_flags: show_names ? show_flags : true,
      force_home,
      hide_current,
      hide_if_no_translation,
      className: className || ''
    };
  }
};
/* harmony default export */ const deprecated = ([v1]);
// EXTERNAL MODULE: external {"this":["wp","blockEditor"]}
var external_this_wp_blockEditor_ = __webpack_require__(89);
// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(959);
// EXTERNAL MODULE: external {"this":["wp","apiFetch"]}
var external_this_wp_apiFetch_ = __webpack_require__(631);
var external_this_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_apiFetch_);
// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(601);
;// ./node_modules/@wpsyntex/polylang-react-library/build/hooks/use-languages-list.js
/**
 * WordPress dependencies
 */



/**
 * Custom hook to get the languages list.
 *
 * @return {Array|null} The languages list, `null` if not loaded yet.
 */
const useLanguagesList = () => {
  const [languages, setLanguages] = (0,external_this_wp_element_.useState)(null);
  (0,external_this_wp_element_.useEffect)(() => {
    external_this_wp_apiFetch_default()({
      path: '/pll/v1/languages',
      method: 'GET'
    }).then(response => setLanguages(response));
  }, []);
  return languages;
};
;// ./node_modules/@wpsyntex/polylang-react-library/build/hooks/use-curated-languages.js
/**
 * WordPress dependencies
 */


/**
 * Hook to curate the languages.
 * Returns an array of languages ensuring that the current language is always the first one.
 *
 * @param {Object[]} languages       The languages.
 * @param {Object}   currentLanguage The current language.
 * @param {boolean}  reduceToOneItem Whether to reduce the languages to one item.
 * @return {Object[]} The curated languages.
 */
const useCuratedLanguages = (languages, currentLanguage, reduceToOneItem) => {
  const curatedLanguages = (0,external_this_wp_element_.useMemo)(() => {
    if (!currentLanguage) {
      return [];
    }
    if (reduceToOneItem) {
      return [currentLanguage];
    }
    return [currentLanguage, ...languages.filter(language => {
      return language.slug !== currentLanguage.slug;
    })];
  }, [languages, currentLanguage, reduceToOneItem]);
  return curatedLanguages;
};
;// ./node_modules/@wpsyntex/polylang-react-library/build/icons/submenu.js
/**
 * Submenu icon
 */

/**
 * WordPress dependencies
 */


const submenu_isPrimitivesComponents = 'undefined' !== typeof wp.primitives;
const SubmenuIcon = () => submenu_isPrimitivesComponents ? /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_primitives_.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  width: "12",
  height: "12",
  viewBox: "0 0 12 12",
  fill: "none",
  children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_primitives_.Path, {
    d: "M1.50002 4L6.00002 8L10.5 4",
    strokeWidth: "1.5"
  })
}) : 'submenu';
/* harmony default export */ const submenu = (SubmenuIcon);
;// ./js/src/blocks/components/switcher-link-element.js

/**
 * Switcher link element component.
 *
 * @param {Object}       props              Component props.
 * @param {ReactElement} props.flag         Flag to display.
 * @param {string}       props.label        Label to display.
 * @param {string}       props.labelSpacing CSS length for the space between flag and label.
 * @return {ReactElement}                   The Switcher element component.
 */
const SwitcherLinkElement = ({
  flag,
  label,
  labelSpacing
}) => {
  const hasLabel = '' !== label;
  const labelStyle = hasLabel && labelSpacing ? {
    '--pll-flag-label-spacing': labelSpacing
  } : undefined;
  return (
    /*#__PURE__*/
    // eslint-disable-next-line jsx-a11y/anchor-is-valid
    (0,react_jsx_runtime_production_min_namespaceFn().jsxs)("a", {
      href: '#',
      children: [flag, hasLabel && /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("span", {
        className: "pll-switcher-label",
        style: labelStyle,
        children: label
      })]
    })
  );
};
;// ./js/src/blocks/components/switcher-utils.js
/**
 * Returns the switcher wrapper class names.
 *
 * @param {string}  layout          The switcher layout.
 * @param {boolean} showFlags       Whether flags are displayed.
 * @param {string}  flagAspectRatio The flag aspect ratio.
 * @param {string}  [alignment]     The items alignment. Omitted when unset.
 * @return {string} The switcher class names.
 */
const getSwitcherClassName = (layout, showFlags, flagAspectRatio, alignment) => {
  const classes = ['pll-switcher', `pll-layout-${layout}`];
  if (alignment) {
    classes.push(`pll-alignment-${alignment}`);
  }
  if (showFlags) {
    classes.push(`pll-aspect-ratio-${flagAspectRatio.replace(':', '')}`);
  }
  return classes.join(' ');
};

/**
 * Gets the label for a language.
 *
 * @param {Object} language   The language object.
 * @param {string} showLabels Whether to show the names. Can be 'names', 'codes' or ''.
 * @return {string} The label for the language.
 */
const getLabel = (language, showLabels) => {
  let label = '';
  if (showLabels === 'names') {
    label = language.name;
  } else if (showLabels === 'codes') {
    label = language.slug.toUpperCase();
  }
  return label;
};

/**
 * Returns the CSS length for the space between flag and label.
 *
 * @param {string} showLabels       Whether to show labels. Can be 'names', 'codes' or ''.
 * @param {string} flagLabelSpacing CSS length for the space between flag and label.
 * @return {string|undefined} The label spacing, or undefined when labels are hidden.
 */
const getLabelSpacing = (showLabels, flagLabelSpacing) => {
  if ('' === showLabels) {
    return undefined;
  }
  return flagLabelSpacing;
};
;// ./js/src/blocks/hooks/use-memoized-flag.js
/**
 * WordPress dependencies
 */


/**
 * Strips fixed dimensions from flag HTML markup.
 *
 * @param {string} html The flag HTML markup.
 * @return {string} The flag HTML without fixed dimensions.
 */

const stripFlagDimensions = html => {
  return html.replace(/\s(?:width|height)=["'][^"']*["']/gi, '').replace(/\sstyle=["'][^"']*["']/gi, '');
};

/**
 * Returns a memoized flag element for a language.
 *
 * @param {Object}  language         The language object.
 * @param {boolean} showFlags        Whether to show the flags.
 * @param {number}  flagBorderRadius The border radius of the flags.
 * @param {string}  flagWidth        The width of the flags as a CSS length.
 * @return {ReactElement|null} The flag for the language.
 */
const useMemoizedFlag = (language, showFlags, flagBorderRadius, flagWidth) => {
  return (0,external_this_wp_element_.useMemo)(() => {
    if (!showFlags || !language?.flag) {
      return null;
    }
    return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("span", {
      className: "pll-switcher-flag",
      style: {
        '--pll-flag-border-radius': flagBorderRadius,
        '--pll-flag-width': flagWidth
      },
      dangerouslySetInnerHTML: {
        __html: stripFlagDimensions(language.flag)
      }
    });
  }, [language?.flag, showFlags, flagBorderRadius, flagWidth]);
};
;// ./js/src/blocks/components/switcher-language-link.js
/**
 * Internal dependencies
 */




/**
 * Renders a language link for the switcher preview.
 *
 * @param {Object}           props                  Component props.
 * @param {Object}           props.language         The language object.
 * @param {string}           props.showLabels       Whether to show labels. Can be 'names', 'codes' or ''.
 * @param {boolean}          props.showFlags        Whether to show the flags.
 * @param {number}           props.flagBorderRadius The border radius of the flags.
 * @param {string}           props.flagWidth        The width of the flags as a CSS length.
 * @param {string|undefined} props.labelSpacing     CSS length for the space between flag and label.
 * @return {ReactElement} The language link element.
 */

const SwitcherLanguageLink = ({
  language,
  showLabels,
  showFlags,
  flagBorderRadius,
  flagWidth,
  labelSpacing
}) => {
  const flag = useMemoizedFlag(language, showFlags, flagBorderRadius, flagWidth);
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(SwitcherLinkElement, {
    label: getLabel(language, showLabels),
    flag: flag,
    labelSpacing: labelSpacing
  });
};
// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__(987);
;// ./js/src/blocks/hooks/use-current-language-with-editor-context.js
/**
 * WordPress dependencies
 */


/**
 * Finds a language by slug.
 *
 * @param {Array}  languages The languages list.
 * @param {string} slug      The language slug.
 * @return {Object|null} The language, `null` if not found.
 */
const findLanguageBySlug = (languages, slug) => {
  if (!languages || !slug) {
    return null;
  }
  const currentLanguage = languages.find(language => {
    return language.slug === slug;
  });
  return currentLanguage ? currentLanguage : null;
};

/**
 * Resolves the current language in block editor contexts.
 *
 * The widgets editor does not load `@wordpress/editor`, so the `core/editor`
 * store may be unavailable. In that context, fall back to the current language
 * slug injected by PHP (default language when none is set).
 *
 * @param {Array} languages The languages list.
 * @return {Object|null} The current language, `null` if not found.
 */
const useCurrentLanguageWithEditorContext = languages => {
  return (0,external_this_wp_data_.useSelect)(select => {
    if (!languages) {
      return null;
    }
    const editorStore = select('core/editor');
    if (!editorStore) {
      return findLanguageBySlug(languages, pllEditorCurrentLanguageSlug // eslint-disable-line no-undef
      );
    }
    const currentPost = editorStore.getCurrentPost();
    if (!currentPost) {
      return null;
    }
    const currentLanguageSlug = currentPost.lang ?? pllEditorCurrentLanguageSlug; // eslint-disable-line no-undef

    return findLanguageBySlug(languages, currentLanguageSlug);
  }, [languages]);
};
;// ./js/src/blocks/languages-context.js
/**
 * WordPress dependencies.
 */


/**
 * Context for the languages.
 *
 * @type {React.Context<null>}
 */
const LanguagesContext = (0,external_this_wp_element_.createContext)(null);
;// ./js/src/blocks/language-switcher/rendered-switcher.js
/**
 * WordPress dependencies
 */


/**
 * External dependencies
 */


/**
 * Internal dependencies
 */





/**
 * Rendered switcher component.
 *
 * @param {Object} props            The component props.
 * @param {Object} props.attributes The block attributes.
 * @return {ReactElement} The rendered switcher.
 */

const RenderedSwitcher = ({
  attributes
}) => {
  const {
    layout,
    show_labels,
    show_flags,
    flag_aspect_ratio,
    flag_border_radius,
    flag_width,
    flag_label_spacing,
    style
  } = attributes;
  const {
    languages
  } = (0,external_this_wp_element_.useContext)(LanguagesContext);
  const currentLanguage = useCurrentLanguageWithEditorContext(languages);
  const curatedLanguages = useCuratedLanguages(languages, currentLanguage, false);
  const alignment = style?.typography?.textAlign;
  const switcherClassName = getSwitcherClassName(layout, show_flags, flag_aspect_ratio, alignment);
  const labelSpacing = getLabelSpacing(show_labels, flag_label_spacing);
  const linkProps = {
    showLabels: show_labels,
    showFlags: show_flags,
    flagBorderRadius: flag_border_radius,
    flagWidth: flag_width,
    labelSpacing
  };
  if (layout === 'dropdown') {
    const currentLanguageItem = curatedLanguages[0];
    return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("nav", {
      className: switcherClassName,
      children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)("div", {
        className: "pll-switcher-inner",
        children: [currentLanguageItem && /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(SwitcherLanguageLink, {
          language: currentLanguageItem,
          ...linkProps
        }), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("button", {
          className: "pll-submenu-toggle",
          children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(submenu, {})
        })]
      })
    });
  }
  if (layout === 'select') {
    return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("div", {
      className: switcherClassName,
      children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("select", {
        className: "pll-switcher-select",
        children: curatedLanguages.map(language => {
          return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("option", {
            value: language.slug,
            children: getLabel(language, show_labels)
          }, language.slug);
        })
      })
    });
  }
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("nav", {
    className: switcherClassName,
    children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("ul", {
      children: curatedLanguages.map(language => {
        return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("li", {
          children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(SwitcherLanguageLink, {
            language: language,
            ...linkProps
          })
        }, language.slug);
      })
    })
  });
};
// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(75);
// EXTERNAL MODULE: external {"this":["wp","notices"]}
var external_this_wp_notices_ = __webpack_require__(672);
;// ./js/src/blocks/components/switcher-controls.js
/**
 * WordPress dependencies
 */






const CSS_LENGTH_UNITS = [{
  value: 'px',
  label: 'px'
}, {
  value: 'em',
  label: 'em'
}, {
  value: 'rem',
  label: 'rem'
}, {
  value: 'vh',
  label: 'vh'
}, {
  value: 'vw',
  label: 'vw'
}];
const LABEL_SPACING_DEFAULT_VALUE = '0.3em';
const ALL_LAYOUT_OPTIONS = [{
  label: (0,external_this_wp_i18n_._x)('Horizontal', 'layout', 'polylang'),
  value: 'horizontal'
}, {
  label: (0,external_this_wp_i18n_._x)('Vertical', 'layout', 'polylang'),
  value: 'vertical'
}, {
  label: (0,external_this_wp_i18n_.__)('Dropdown', 'polylang'),
  value: 'dropdown'
}, {
  label: (0,external_this_wp_i18n_.__)('Selector', 'polylang'),
  value: 'select'
}];
const ALL_LAYOUT_TOOLBAR_CONTROLS = [{
  title: (0,external_this_wp_i18n_._x)('Horizontal', 'layout', 'polylang'),
  layout: 'horizontal'
}, {
  title: (0,external_this_wp_i18n_._x)('Vertical', 'layout', 'polylang'),
  layout: 'vertical'
}, {
  title: (0,external_this_wp_i18n_.__)('Dropdown', 'polylang'),
  layout: 'dropdown'
}];

/**
 * Switcher controls component for toolbar and inspector controls.
 *
 * @param {Object}        props               The component props.
 * @param {Object}        props.attributes    The block attributes.
 * @param {Function}      props.setAttributes The function to set the block attributes.
 * @param {Array<string>} props.layoutOptions Optional layout values to expose.
 * @return {React.ReactNode} The switcher controls component.
 */
const SwitcherControls = ({
  attributes,
  setAttributes,
  layoutOptions = ['horizontal', 'vertical', 'dropdown', 'select']
}) => {
  const {
    layout,
    show_labels,
    show_flags,
    force_home,
    hide_current,
    flag_aspect_ratio,
    flag_border_radius,
    flag_width,
    flag_label_spacing,
    hide_if_no_translation
  } = attributes;
  const {
    createWarningNotice
  } = (0,external_this_wp_data_.useDispatch)(external_this_wp_notices_.store);
  const layoutSelectOptions = ALL_LAYOUT_OPTIONS.filter(option => layoutOptions.includes(option.value));
  const layoutToolbarControls = ALL_LAYOUT_TOOLBAR_CONTROLS.filter(control => layoutOptions.includes(control.layout)).map(control => ({
    title: control.title,
    onClick: () => setAttributes({
      layout: control.layout
    })
  }));
  const labelOptions = [{
    label: (0,external_this_wp_i18n_.__)('Language names', 'polylang'),
    value: 'names'
  }, {
    label: (0,external_this_wp_i18n_.__)('Language codes', 'polylang'),
    value: 'codes'
  }];
  if (show_flags) {
    labelOptions.push({
      label: (0,external_this_wp_i18n_._x)('None', 'Labels', 'polylang'),
      value: ''
    });
  }
  const toolbarLabelControls = [{
    title: (0,external_this_wp_i18n_.__)('Language names', 'polylang'),
    onClick: () => setAttributes({
      show_labels: 'names'
    })
  }, {
    title: (0,external_this_wp_i18n_.__)('Language codes', 'polylang'),
    onClick: () => setAttributes({
      show_labels: 'codes'
    })
  }];
  if (show_flags) {
    toolbarLabelControls.push({
      title: (0,external_this_wp_i18n_._x)('None', 'Labels', 'polylang'),
      onClick: () => {
        if (!show_flags) {
          createWarningNotice((0,external_this_wp_i18n_.__)('Labels cannot be hidden if flags are not shown.', 'polylang'));
          return;
        }
        setAttributes({
          show_labels: '',
          flag_label_spacing: LABEL_SPACING_DEFAULT_VALUE
        });
      }
    });
  }
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)((react_jsx_runtime_production_min_namespaceFn().Fragment), {
    children: [/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)(external_this_wp_blockEditor_.InspectorControls, {
      children: [/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)(external_this_wp_components_.PanelBody, {
        title: (0,external_this_wp_i18n_.__)('Display', 'polylang'),
        children: [/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.SelectControl, {
          label: (0,external_this_wp_i18n_.__)('Layout', 'polylang'),
          value: layout,
          options: layoutSelectOptions,
          onChange: value => {
            setAttributes({
              layout: value
            });
            if ('select' === value) {
              setAttributes({
                show_flags: false
              });
              if ('' === show_labels) {
                setAttributes({
                  show_labels: 'names'
                });
              }
            }
          },
          __next40pxDefaultSize: true
        }), 'select' !== layout && /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.SelectControl, {
          label: (0,external_this_wp_i18n_.__)('Labels', 'polylang'),
          value: show_labels,
          options: labelOptions,
          onChange: value => {
            if ('' === value) {
              if (!show_flags) {
                createWarningNotice((0,external_this_wp_i18n_.__)('Labels cannot be hidden if flags are not shown.', 'polylang'));
                return;
              }
              setAttributes({
                show_labels: value,
                flag_label_spacing: LABEL_SPACING_DEFAULT_VALUE
              });
              return;
            }
            setAttributes({
              show_labels: value
            });
          },
          __next40pxDefaultSize: true
        })]
      }), 'select' !== layout && /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)(external_this_wp_components_.PanelBody, {
        title: (0,external_this_wp_i18n_.__)('Flags Settings', 'polylang'),
        children: [/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.ToggleControl, {
          label: (0,external_this_wp_i18n_.__)('Display flags', 'polylang'),
          checked: show_flags,
          onChange: value => {
            if ('' === show_labels && !value) {
              createWarningNotice((0,external_this_wp_i18n_.__)('Flags cannot be hidden if labels are not displayed.', 'polylang'));
              return;
            }
            setAttributes({
              show_flags: value
            });
          }
        }), show_flags && /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)((react_jsx_runtime_production_min_namespaceFn().Fragment), {
          children: [/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.RadioControl, {
            label: (0,external_this_wp_i18n_.__)('Aspect', 'polylang'),
            selected: flag_aspect_ratio,
            options: [{
              label: (0,external_this_wp_i18n_.__)('Landscape (3:2)', 'polylang'),
              value: '3:2'
            }, {
              label: (0,external_this_wp_i18n_.__)('Square (1:1)', 'polylang'),
              value: '1:1'
            }],
            onChange: value => setAttributes({
              flag_aspect_ratio: value
            })
          }), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.RangeControl, {
            label: (0,external_this_wp_i18n_.__)('Border radius', 'polylang'),
            initialPosition: 0,
            value: flag_border_radius,
            min: 0,
            max: 100,
            step: 1,
            allowReset: true,
            resetFallbackValue: 0,
            onChange: value => setAttributes({
              flag_border_radius: value
            }),
            __next40pxDefaultSize: true
          }), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.__experimentalUnitControl, {
            __next40pxDefaultSize: true,
            label: (0,external_this_wp_i18n_.__)('Size', 'polylang'),
            value: flag_width,
            units: CSS_LENGTH_UNITS,
            onChange: value => setAttributes({
              flag_width: value
            })
          }), '' !== show_labels && /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.__experimentalUnitControl, {
            __next40pxDefaultSize: true,
            label: (0,external_this_wp_i18n_.__)('Label spacing', 'polylang'),
            value: flag_label_spacing,
            units: CSS_LENGTH_UNITS,
            onChange: value => setAttributes({
              flag_label_spacing: value
            })
          })]
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)(external_this_wp_components_.PanelBody, {
        title: (0,external_this_wp_i18n_.__)('Behavior', 'polylang'),
        children: [/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.ToggleControl, {
          label: (0,external_this_wp_i18n_.__)('Force link to front page', 'polylang'),
          checked: force_home,
          onChange: value => setAttributes({
            force_home: value
          })
        }), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.ToggleControl, {
          label: (0,external_this_wp_i18n_.__)('Hide the current language', 'polylang'),
          checked: hide_current,
          onChange: value => setAttributes({
            hide_current: value
          })
        }), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.ToggleControl, {
          label: (0,external_this_wp_i18n_.__)('Hide languages with no translation', 'polylang'),
          checked: hide_if_no_translation,
          onChange: value => setAttributes({
            hide_if_no_translation: value
          })
        })]
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_blockEditor_.BlockControls, {
      children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)(external_this_wp_components_.ToolbarGroup, {
        children: [/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.ToolbarDropdownMenu, {
          label: (0,external_this_wp_i18n_.__)('Layout', 'polylang'),
          controls: layoutToolbarControls
        }), 'select' !== layout && /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.ToolbarButton, {
          icon: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("span", {
            className: "dashicons dashicons-flag"
          }),
          label: show_flags ? (0,external_this_wp_i18n_.__)('Hide flags', 'polylang') : (0,external_this_wp_i18n_.__)('Display flags', 'polylang'),
          onClick: () => {
            if (show_flags && '' === show_labels) {
              createWarningNotice((0,external_this_wp_i18n_.__)('Flags cannot be hidden if labels are not displayed.', 'polylang'));
              return;
            }
            setAttributes({
              show_flags: !show_flags
            });
          }
        }), 'select' !== layout && /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.ToolbarDropdownMenu, {
          icon: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("span", {
            className: "dashicons dashicons-editor-textcolor"
          }),
          label: (0,external_this_wp_i18n_.__)('Labels', 'polylang'),
          controls: toolbarLabelControls
        })]
      })
    })]
  });
};
;// ./js/src/blocks/language-switcher/edit.js
/**
 * Edit callback for language switcher block.
 */

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */





/**
 * Edit callback for language switcher block.
 *
 * @param {Object}   props               Block properties.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Function to set block attributes.
 * @return {ReactElement} The block content and controls.
 */

const Edit = ({
  attributes,
  setAttributes
}) => {
  const languages = useLanguagesList();
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)("div", {
    ...(0,external_this_wp_blockEditor_.useBlockProps)(),
    children: [/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(SwitcherControls, {
      attributes: attributes,
      setAttributes: setAttributes
    }), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.Disabled, {
      children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(LanguagesContext.Provider, {
        value: {
          languages
        },
        children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(RenderedSwitcher, {
          attributes: attributes
        })
      })
    })]
  });
};
;// ./src/modules/Blocks/Language_Switcher/Standard/block.json
const block_namespaceObject = /*#__PURE__*/JSON.parse('{"UU":"polylang/language-switcher"}');
;// ./js/src/blocks/language-switcher/index.js
/**
 * Register language switcher block.
 */

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




(0,external_this_wp_blocks_.registerBlockType)(block_namespaceObject.UU, {
  icon: icons_translation,
  edit: Edit,
  deprecated: deprecated
});
// EXTERNAL MODULE: external {"this":["wp","hooks"]}
var external_this_wp_hooks_ = __webpack_require__(873);
;// ./js/src/blocks/navigation-language-switcher/deprecated.js
/**
 * Deprecated navigation language switcher block.
 */

const deprecated_V2Attributes = {
  layout: 'horizontal',
  show_labels: 'names',
  show_flags: false,
  force_home: false,
  hide_current: false,
  hide_if_no_translation: false,
  flag_aspect_ratio: '3:2',
  flag_border_radius: 0,
  flag_width: '18px',
  flag_label_spacing: '0.3em',
  className: ''
};
const deprecated_v1 = {
  attributes: {
    dropdown: {
      type: 'boolean',
      default: false
    },
    show_names: {
      type: 'boolean',
      default: true
    },
    show_flags: {
      type: 'boolean',
      default: false
    },
    force_home: {
      type: 'boolean',
      default: false
    },
    hide_current: {
      type: 'boolean',
      default: false
    },
    hide_if_no_translation: {
      type: 'boolean',
      default: false
    },
    className: {
      type: 'string',
      default: ''
    }
  },
  isEligible: attributes => 'dropdown' in attributes || 'show_names' in attributes,
  migrate(attributes) {
    const {
      dropdown,
      show_names,
      show_flags,
      force_home,
      hide_current,
      hide_if_no_translation,
      className
    } = attributes;
    return {
      ...deprecated_V2Attributes,
      layout: dropdown ? 'dropdown' : 'horizontal',
      show_labels: show_names ? 'names' : '',
      show_flags: show_names ? show_flags : true,
      force_home,
      hide_current,
      hide_if_no_translation,
      className: className || ''
    };
  }
};
/* harmony default export */ const navigation_language_switcher_deprecated = ([deprecated_v1]);
// EXTERNAL MODULE: external {"this":["wp","editor"]}
var external_this_wp_editor_ = __webpack_require__(2);
;// ./node_modules/@wpsyntex/polylang-react-library/build/hooks/use-current-language.js
/**
 * WordPress dependencies
 */

// This package is not found in `@wordpress/scripts` like others (peer dependency).
// eslint-disable-next-line import/no-unresolved


/**
 * Custom hook to get the current language from the editor.
 *
 * @param {Array} languages The languages list.
 * @return {Object|null} The current language, `null` if not found.
 */
const useCurrentLanguage = languages => {
  const currentPost = (0,external_this_wp_data_.useSelect)(select => select(external_this_wp_editor_.store).getCurrentPost());
  if (!languages || !currentPost) {
    return null;
  }
  const currentLanguageSlug = currentPost.lang ?? pllEditorCurrentLanguageSlug; // eslint-disable-line no-undef

  const currentLanguage = languages.find(language => {
    return language.slug === currentLanguageSlug;
  });
  return currentLanguage ? currentLanguage : null;
};
;// ./js/src/blocks/navigation-language-switcher/rendered-switcher.js
/**
 * WordPress dependencies
 */


/**
 * External dependencies
 */


/**
 * Internal dependencies
 */




/**
 * Rendered switcher component.
 *
 * @param {Object} props            The component props.
 * @param {Object} props.attributes The block attributes.
 * @param {Object} props.context    The block context.
 * @return {ReactElement} The rendered switcher.
 */

const rendered_switcher_RenderedSwitcher = ({
  attributes,
  context
}) => {
  const {
    layout,
    show_labels,
    show_flags,
    flag_border_radius,
    flag_width,
    flag_label_spacing
  } = attributes;
  const {
    showSubmenuIcon,
    openSubmenusOnClick
  } = context;
  const {
    languages
  } = (0,external_this_wp_element_.useContext)(LanguagesContext);
  const currentLanguage = useCurrentLanguage(languages);
  const isDropdown = layout === 'dropdown';
  const languagesToRender = useCuratedLanguages(languages, currentLanguage, isDropdown);
  const labelSpacing = getLabelSpacing(show_labels, flag_label_spacing);
  const withSubmenuIcon = isDropdown && (showSubmenuIcon || openSubmenusOnClick);
  const linkProps = {
    showLabels: show_labels,
    showFlags: show_flags,
    flagBorderRadius: flag_border_radius,
    flagWidth: flag_width,
    labelSpacing
  };
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)((react_jsx_runtime_production_min_namespaceFn().Fragment), {
    children: languagesToRender.map(language => {
      return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)(external_this_wp_element_.Fragment, {
        children: [/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(SwitcherLanguageLink, {
          language: language,
          ...linkProps
        }), withSubmenuIcon && /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)("span", {
          className: "wp-block-navigation__submenu-icon",
          children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(submenu, {})
        })]
      }, language.slug);
    })
  });
};
;// ./js/src/blocks/navigation-language-switcher/edit.js
/**
 * Edit callback for navigation language switcher block.
 */

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */





/**
 * Edit callback for navigation language switcher block.
 *
 * @param {Object}   props               Block properties.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Object}   props.context       Block context.
 * @param {Function} props.setAttributes Function to set block attributes.
 * @return {ReactElement} The block content and controls.
 */

const edit_Edit = ({
  attributes,
  context,
  setAttributes
}) => {
  const languages = useLanguagesList();
  const {
    show_flags,
    flag_aspect_ratio
  } = attributes;
  const aspectRatioClass = show_flags && flag_aspect_ratio ? `pll-aspect-ratio-${flag_aspect_ratio.replace(':', '')}` : '';
  return /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsxs)("div", {
    ...(0,external_this_wp_blockEditor_.useBlockProps)({
      className: aspectRatioClass || undefined
    }),
    children: [/*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(SwitcherControls, {
      attributes: attributes,
      setAttributes: setAttributes,
      layoutOptions: ['horizontal', 'dropdown']
    }), /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(external_this_wp_components_.Disabled, {
      children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(LanguagesContext.Provider, {
        value: {
          languages
        },
        children: /*#__PURE__*/(0,react_jsx_runtime_production_min_namespaceFn().jsx)(rendered_switcher_RenderedSwitcher, {
          attributes: attributes,
          context: context
        })
      })
    })]
  });
};
;// ./src/modules/Blocks/Language_Switcher/Navigation/block.json
const Navigation_block_namespaceObject = /*#__PURE__*/JSON.parse('{"UU":"polylang/navigation-language-switcher"}');
;// ./js/src/blocks/navigation-language-switcher/menu-items-converter.js
/**
 * Menu items converter
 */

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


/**
 * Apply a callback function on each block of the blocks list.
 *
 * @param {Array}  blocks        The list of blocks to process.
 * @param {Array}  menuItems     The initial menu items from where the blocks are converted to.
 * @param {Object} blocksMapping The mapping between the menu items and their corresponding blocks.
 * @param {mapper} mapper        A callback to change the converted block by another one if necessary
 * @return {Array} Array of blocks updated.
 */
function mapBlockTree(blocks, menuItems, blocksMapping, mapper) {
  /**
   * A function to apply to each block to convert it if necessary by applying the `mapper` filter.
   *
   * @param {Object} block The block to replace or not.
   * @return {Object} The new block potentially replaced by the `mapper`.
   */
  const convertBlock = block => ({
    ...mapper(block, menuItems, blocksMapping),
    innerBlocks: mapBlockTree(block.innerBlocks, menuItems, blocksMapping, mapper)
  });
  return blocks.map(convertBlock);
}

/**
 * A filter to detect the `core/navigation-link` block not correctly converted from the language switcher menu item
 * and convert it to its corresponding `polylang/navigation-language-switcher` block.
 *
 * @callback mapper
 * @param {Object} block         The block converted from the menu item.
 * @param {Array}  menuItems     The initial menu items from where the blocks are converted to.
 * @param {Object} blocksMapping The mapping between the menu items and their corresponding blocks.
 * @return {Object} The block correctly converted.
 */
const blocksFilter = (block, menuItems, blocksMapping) => {
  if (block.name === 'core/navigation-link' && block.attributes?.url === '#pll_switcher') {
    const menuItem = menuItems.find(item => item.url === '#pll_switcher'); // Get the corresponding menu item.
    const attributes = menuItem.meta._pll_menu_item; // Get its options.
    const newBlock = (0,external_this_wp_blocks_.createBlock)(Navigation_block_namespaceObject.UU, attributes);
    blocksMapping[menuItem.id] = newBlock.clientId; // Update the blocks mapping.
    return newBlock;
  }
  return block;
};

/**
 * A filter callback hooked to `blocks.navigation.__unstableMenuItemsToBlocks`.
 *
 * @param {Array} blocks    The list of blocks to process.
 * @param {Array} menuItems The initial menu items from where the blocks are converted to.
 * @return {Array} Array of blocks updated.
 */
const menuItemsToBlocksFilter = (blocks, menuItems) => ({
  ...blocks,
  innerBlocks: mapBlockTree(blocks.innerBlocks, menuItems, blocks.mapping, blocksFilter)
});
;// ./js/src/blocks/navigation-language-switcher/index.js
/**
 * Register navigation language switcher block.
 */

/**
 * WordPress Dependencies
 */



/**
 * Internal dependencies
 */





(0,external_this_wp_blocks_.registerBlockType)(Navigation_block_namespaceObject.UU, {
  icon: icons_translation,
  transforms: {
    from: [{
      type: 'block',
      blocks: ['core/navigation-link'],
      transform: () => (0,external_this_wp_blocks_.createBlock)(Navigation_block_namespaceObject.UU)
    }]
  },
  edit: edit_Edit,
  deprecated: navigation_language_switcher_deprecated
});

/**
 * Hooks to the classic menu conversion to core/navigation block to be able to convert
 * the language switcher menu item to its corresponding block.
 */
(0,external_this_wp_hooks_.addFilter)('blocks.navigation.__unstableMenuItemsToBlocks', 'polylang/include-language-switcher', menuItemsToBlocksFilter);
;// ./js/src/blocks/index.js
/**
 * Registers Polylang blocks in the editors and enables attributes controls.
 */


})();

this.polylang = __webpack_exports__;
/******/ })()
;
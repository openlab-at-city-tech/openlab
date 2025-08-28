/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 7:
/*!*********************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/function-uncurry-this-clause.js ***!
  \*********************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var classofRaw = __webpack_require__(/*! ../internals/classof-raw */ 3325);
var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ 1109);

module.exports = function (fn) {
  // Nashorn bug:
  //   https://github.com/zloirock/core-js/issues/1128
  //   https://github.com/zloirock/core-js/issues/1130
  if (classofRaw(fn) === 'Function') return uncurryThis(fn);
};


/***/ }),

/***/ 146:
/*!************************************************************************************************************************************************!*\
  !*** ./.yarn/__virtual__/react-dom-virtual-2811c34ef5/0/cache/react-dom-npm-18.2.0-dd675bca1c-66dfc5f93e.zip/node_modules/react-dom/client.js ***!
  \************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var m = __webpack_require__(/*! react-dom */ 5206);
if (false) // removed by dead control flow
{} else {
  var i = m.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;
  exports.createRoot = function(c, o) {
    i.usingClientEntryPoint = true;
    try {
      return m.createRoot(c, o);
    } finally {
      i.usingClientEntryPoint = false;
    }
  };
  exports.hydrateRoot = function(c, h, o) {
    i.usingClientEntryPoint = true;
    try {
      return m.hydrateRoot(c, h, o);
    } finally {
      i.usingClientEntryPoint = false;
    }
  };
}


/***/ }),

/***/ 156:
/*!***************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/to-integer-or-infinity.js ***!
  \***************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var trunc = __webpack_require__(/*! ../internals/math-trunc */ 5402);

// `ToIntegerOrInfinity` abstract operation
// https://tc39.es/ecma262/#sec-tointegerorinfinity
module.exports = function (argument) {
  var number = +argument;
  // eslint-disable-next-line no-self-compare -- NaN check
  return number !== number || number === 0 ? 0 : trunc(number);
};


/***/ }),

/***/ 180:
/*!***************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/shared-key.js ***!
  \***************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var shared = __webpack_require__(/*! ../internals/shared */ 438);
var uid = __webpack_require__(/*! ../internals/uid */ 8581);

var keys = shared('keys');

module.exports = function (key) {
  return keys[key] || (keys[key] = uid(key));
};


/***/ }),

/***/ 248:
/*!*****************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/get-built-in.js ***!
  \*****************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var globalThis = __webpack_require__(/*! ../internals/global-this */ 7793);
var isCallable = __webpack_require__(/*! ../internals/is-callable */ 2120);

var aFunction = function (argument) {
  return isCallable(argument) ? argument : undefined;
};

module.exports = function (namespace, method) {
  return arguments.length < 2 ? aFunction(globalThis[namespace]) : globalThis[namespace] && globalThis[namespace][method];
};


/***/ }),

/***/ 259:
/*!*************************************!*\
  !*** external "window.wp?.plugins" ***!
  \*************************************/
/***/ ((module) => {

"use strict";
module.exports = window.wp?.plugins;

/***/ }),

/***/ 279:
/*!***********************************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/iterator-helper-throws-on-invalid-iterator.js ***!
  \***********************************************************************************************************************************************/
/***/ ((module) => {

"use strict";

// Should throw an error on invalid iterator
// https://issues.chromium.org/issues/336839115
module.exports = function (methodName, argument) {
  // eslint-disable-next-line es/no-iterator -- required for testing
  var method = typeof Iterator == 'function' && Iterator.prototype[methodName];
  if (method) try {
    method.call({ next: null }, argument).next();
  } catch (error) {
    return true;
  }
};


/***/ }),

/***/ 438:
/*!***********************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/shared.js ***!
  \***********************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var store = __webpack_require__(/*! ../internals/shared-store */ 5714);

module.exports = function (key, value) {
  return store[key] || (store[key] = value || {});
};


/***/ }),

/***/ 528:
/*!************************************!*\
  !*** ./js/src/modules/widgets.tsx ***!
  \************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var core_js_modules_es_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.iterator.constructor.js */ 5146);
/* harmony import */ var core_js_modules_es_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_iterator_for_each_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.iterator.for-each.js */ 4363);
/* harmony import */ var core_js_modules_es_iterator_for_each_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_iterator_for_each_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var react_dom_client__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react-dom/client */ 146);
/* harmony import */ var _components_TransformNotice__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../components/TransformNotice */ 9020);
/* harmony import */ var _globals_config__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../globals/config */ 5328);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! react/jsx-runtime */ 7157);
/* provided dependency */ var jQuery = __webpack_require__(/*! jquery */ 1669);






function renderTransformNotice() {
  // Only render on the block based widgets screen.
  if (_globals_config__WEBPACK_IMPORTED_MODULE_4__.CONFIG.siteInfo.classicWidgets) {
    return;
  }
  // Don't render if the user has dismissed the notice.
  if ('1' === localStorage.getItem(_components_TransformNotice__WEBPACK_IMPORTED_MODULE_3__.DISMISS_KEY)) {
    return;
  }
  const placeholders = document.querySelectorAll('[data-js="advanced-sidebar-menu/transform-notice"]');

  /**
   * This is not a priority component, so it anything fails
   * we fail silently.
   */
  try {
    placeholders.forEach(placeholder => {
      const root = (0,react_dom_client__WEBPACK_IMPORTED_MODULE_2__.createRoot)(placeholder);
      root.render(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_components_TransformNotice__WEBPACK_IMPORTED_MODULE_3__["default"], {}));
    });
  } catch (e) {
    console.error(e);
  }
}

/**
 * Modern JS for legacy widgets on the widgets screen.
 *
 * @see resources/js/advanced-sidebar-menu.js for the legacy JS.
 */
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (() => {
  // Only load on the widgets screen.
  if ('1' !== _globals_config__WEBPACK_IMPORTED_MODULE_4__.CONFIG.isWidgets) {
    return;
  }
  // Fire when each widget loads.
  jQuery(document).on('widget-added', function () {
    renderTransformNotice();
  });
});

/***/ }),

/***/ 564:
/*!*********************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/define-built-ins.js ***!
  \*********************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var defineBuiltIn = __webpack_require__(/*! ../internals/define-built-in */ 8705);

module.exports = function (target, src, options) {
  for (var key in src) defineBuiltIn(target, key, src[key], options);
  return target;
};


/***/ }),

/***/ 742:
/*!***************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/object-define-property.js ***!
  \***************************************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";

var DESCRIPTORS = __webpack_require__(/*! ../internals/descriptors */ 2465);
var IE8_DOM_DEFINE = __webpack_require__(/*! ../internals/ie8-dom-define */ 4666);
var V8_PROTOTYPE_DEFINE_BUG = __webpack_require__(/*! ../internals/v8-prototype-define-bug */ 7207);
var anObject = __webpack_require__(/*! ../internals/an-object */ 1734);
var toPropertyKey = __webpack_require__(/*! ../internals/to-property-key */ 1196);

var $TypeError = TypeError;
// eslint-disable-next-line es/no-object-defineproperty -- safe
var $defineProperty = Object.defineProperty;
// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
var $getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;
var ENUMERABLE = 'enumerable';
var CONFIGURABLE = 'configurable';
var WRITABLE = 'writable';

// `Object.defineProperty` method
// https://tc39.es/ecma262/#sec-object.defineproperty
exports.f = DESCRIPTORS ? V8_PROTOTYPE_DEFINE_BUG ? function defineProperty(O, P, Attributes) {
  anObject(O);
  P = toPropertyKey(P);
  anObject(Attributes);
  if (typeof O === 'function' && P === 'prototype' && 'value' in Attributes && WRITABLE in Attributes && !Attributes[WRITABLE]) {
    var current = $getOwnPropertyDescriptor(O, P);
    if (current && current[WRITABLE]) {
      O[P] = Attributes.value;
      Attributes = {
        configurable: CONFIGURABLE in Attributes ? Attributes[CONFIGURABLE] : current[CONFIGURABLE],
        enumerable: ENUMERABLE in Attributes ? Attributes[ENUMERABLE] : current[ENUMERABLE],
        writable: false
      };
    }
  } return $defineProperty(O, P, Attributes);
} : $defineProperty : function defineProperty(O, P, Attributes) {
  anObject(O);
  P = toPropertyKey(P);
  anObject(Attributes);
  if (IE8_DOM_DEFINE) try {
    return $defineProperty(O, P, Attributes);
  } catch (error) { /* empty */ }
  if ('get' in Attributes || 'set' in Attributes) throw new $TypeError('Accessors not supported');
  if ('value' in Attributes) O[P] = Attributes.value;
  return O;
};


/***/ }),

/***/ 789:
/*!***************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/get-method.js ***!
  \***************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var aCallable = __webpack_require__(/*! ../internals/a-callable */ 4549);
var isNullOrUndefined = __webpack_require__(/*! ../internals/is-null-or-undefined */ 8650);

// `GetMethod` abstract operation
// https://tc39.es/ecma262/#sec-getmethod
module.exports = function (V, P) {
  var func = V[P];
  return isNullOrUndefined(func) ? undefined : aCallable(func);
};


/***/ }),

/***/ 833:
/*!*****************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/define-built-in-accessor.js ***!
  \*****************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var makeBuiltIn = __webpack_require__(/*! ../internals/make-built-in */ 6742);
var defineProperty = __webpack_require__(/*! ../internals/object-define-property */ 742);

module.exports = function (target, name, descriptor) {
  if (descriptor.get) makeBuiltIn(descriptor.get, name, { getter: true });
  if (descriptor.set) makeBuiltIn(descriptor.set, name, { setter: true });
  return defineProperty.f(target, name, descriptor);
};


/***/ }),

/***/ 886:
/*!************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/get-iterator-method.js ***!
  \************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var classof = __webpack_require__(/*! ../internals/classof */ 9354);
var getMethod = __webpack_require__(/*! ../internals/get-method */ 789);
var isNullOrUndefined = __webpack_require__(/*! ../internals/is-null-or-undefined */ 8650);
var Iterators = __webpack_require__(/*! ../internals/iterators */ 5696);
var wellKnownSymbol = __webpack_require__(/*! ../internals/well-known-symbol */ 1782);

var ITERATOR = wellKnownSymbol('iterator');

module.exports = function (it) {
  if (!isNullOrUndefined(it)) return getMethod(it, ITERATOR)
    || getMethod(it, '@@iterator')
    || Iterators[classof(it)];
};


/***/ }),

/***/ 1109:
/*!**************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/function-uncurry-this.js ***!
  \**************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var NATIVE_BIND = __webpack_require__(/*! ../internals/function-bind-native */ 4235);

var FunctionPrototype = Function.prototype;
var call = FunctionPrototype.call;
// eslint-disable-next-line es/no-function-prototype-bind -- safe
var uncurryThisWithBind = NATIVE_BIND && FunctionPrototype.bind.bind(call, call);

module.exports = NATIVE_BIND ? uncurryThisWithBind : function (fn) {
  return function () {
    return call.apply(fn, arguments);
  };
};


/***/ }),

/***/ 1196:
/*!********************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/to-property-key.js ***!
  \********************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var toPrimitive = __webpack_require__(/*! ../internals/to-primitive */ 7654);
var isSymbol = __webpack_require__(/*! ../internals/is-symbol */ 9540);

// `ToPropertyKey` abstract operation
// https://tc39.es/ecma262/#sec-topropertykey
module.exports = function (argument) {
  var key = toPrimitive(argument, 'string');
  return isSymbol(key) ? key : key + '';
};


/***/ }),

/***/ 1474:
/*!***********************************************!*\
  !*** ./js/src/gutenberg/blocks/InfoPanel.tsx ***!
  \***********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var core_js_modules_es_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.iterator.constructor.js */ 5146);
/* harmony import */ var core_js_modules_es_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_iterator_map_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.iterator.map.js */ 6288);
/* harmony import */ var core_js_modules_es_iterator_map_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_iterator_map_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _globals_config__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../globals/config */ 5328);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ 6021);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/block-editor */ 6423);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_html_entities__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/html-entities */ 8259);
/* harmony import */ var _wordpress_html_entities__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/i18n */ 4833);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _info_panel_pcss__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./info-panel.pcss */ 3275);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! react/jsx-runtime */ 7157);









const titleStyles = {
  color: '#3db634',
  fontWeight: 700
};
const headingStyles = {
  margin: '16px 0 0',
  fontSize: '1.2em'
};
const InfoPanel = ({}) => {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__.InspectorControls, {
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.PanelBody, {
      title: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)("span", {
        style: titleStyles,
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Go PRO', 'advanced-sidebar-menu')
      }),
      className: _info_panel_pcss__WEBPACK_IMPORTED_MODULE_7__["default"].wrap,
      initialOpen: false,
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)("h2", {
        style: headingStyles,
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Advanced Sidebar Menu PRO', 'advanced-sidebar-menu')
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsxs)("ul", {
        children: [_globals_config__WEBPACK_IMPORTED_MODULE_2__.CONFIG.features.map(feature => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)("li", {
          children: (0,_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_5__.decodeEntities)(feature)
        }, feature)), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)("li", {
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('And so much more…', 'advanced-sidebar-menu')
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Button, {
        className: _info_panel_pcss__WEBPACK_IMPORTED_MODULE_7__["default"].button,
        href: 'https://onpointplugins.com/product/advanced-sidebar-menu-pro/?utm_source=block-upgrade&utm_campaign=gopro&utm_medium=wp-dash#buy-now-choices',
        target: '_blank',
        rel: 'noreferrer',
        isPrimary: true,
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Upgrade', 'advanced-sidebar-menu')
      })]
    })
  });
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ((0,_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.withFilters)('advanced-sidebar-menu.blocks.info-panel')(InfoPanel));

/***/ }),

/***/ 1594:
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

"use strict";
module.exports = React;

/***/ }),

/***/ 1652:
/*!*********************************************!*\
  !*** ./js/src/gutenberg/blocks/Preview.tsx ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__),
/* harmony export */   sanitizeClientId: () => (/* binding */ sanitizeClientId)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ 1594);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _globals_config__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../globals/config */ 5328);
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/server-side-render */ 4061);
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ 6021);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/block-editor */ 6423);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var dompurify__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! dompurify */ 9133);
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/hooks */ 4015);
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/i18n */ 4833);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/data */ 5705);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../helpers */ 9440);
/* harmony import */ var _pages_PagesIcon__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./pages/PagesIcon */ 5790);
/* harmony import */ var _categories_CategoriesIcon__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./categories/CategoriesIcon */ 6268);
/* harmony import */ var _NavigationIcon__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./NavigationIcon */ 1891);
/* harmony import */ var _preview_pcss__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./preview.pcss */ 6074);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! react/jsx-runtime */ 7157);















/**
 * @see \Advanced_Sidebar_Menu\Blocks\Common_Attributes::get_common_attributes
 */

/**
 * @see \Advanced_Sidebar_Menu\Blocks\Common_Attributes::get_server_side_render_attributes
 */

/**
 * Sanitize a client id for use as an HTML id.
 *
 * Must not start with a `-` or a digit.
 *
 */
const sanitizeClientId = clientId => {
  return clientId.replace(/^([\d-])/, '_$1');
};

/**
 * If we are in the widgets' area, the block is wrapped in
 * a "sidebar" block. We retrieve the id to pass along with
 * the request to use the `widget_args` within the preview.
 *
 */
const getSidebarId = clientId => {
  if (!(0,_helpers__WEBPACK_IMPORTED_MODULE_9__.isScreen)(['widgets'])) {
    return '';
  }
  const rootId = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_8__.select)('core/block-editor').getBlockRootClientId(clientId);
  if ('undefined' !== typeof rootId && '' !== rootId) {
    const ParentBlock = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_8__.select)('core/block-editor').getBlocksByClientId([rootId]);
    if (null !== ParentBlock[0] && 'undefined' !== typeof ParentBlock[0] && 'core/widget-area' === ParentBlock[0]?.name) {
      return ParentBlock[0]?.attributes?.id;
    }
  }
  return '';
};

/**
 * @notice Must use static constants, or the ServerSide requests
 *         will fire anytime something on the page is changed
 *         because the component re-renders.
 */
const Page = () => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Placeholder, {
  className: _preview_pcss__WEBPACK_IMPORTED_MODULE_13__["default"].placeholder,
  icon: _pages_PagesIcon__WEBPACK_IMPORTED_MODULE_10__["default"],
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__.__)('Advanced Sidebar - Pages', 'advanced-sidebar-menu'),
  instructions: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__.__)('No preview available', 'advanced-sidebar-menu')
});
const Category = () => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Placeholder, {
  className: _preview_pcss__WEBPACK_IMPORTED_MODULE_13__["default"].placeholder,
  icon: _categories_CategoriesIcon__WEBPACK_IMPORTED_MODULE_11__["default"],
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__.__)('Advanced Sidebar - Categories', 'advanced-sidebar-menu'),
  instructions: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__.__)('No preview available', 'advanced-sidebar-menu')
});
const Navigation = () => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Placeholder, {
  className: _preview_pcss__WEBPACK_IMPORTED_MODULE_13__["default"].placeholder,
  icon: _NavigationIcon__WEBPACK_IMPORTED_MODULE_12__["default"],
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__.__)('Advanced Sidebar - Navigation', 'advanced-sidebar-menu'),
  instructions: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__.__)('No preview available', 'advanced-sidebar-menu')
});

/**
 * @notice The styles will not display for the preview
 *         in the block inserter sidebar when Webpack
 *         is enabled because the iframe has a late init.
 */
const placeholder = block => {
  switch (block) {
    case _globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.blocks.pages.id:
      return Page;
    case _globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.blocks.categories.id:
      return Category;
    case _globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.blocks.navigation?.id:
      return Navigation;
  }
  return () => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsx)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.Fragment, {});
};

/**
 * Same as the `DefaultLoadingResponsePlaceholder` except we trigger
 * an action when the loading component is unmounted to allow
 * components to hook into when ServerSideRender has finished loading.
 *
 * @notice Using a constant to prevent a reload on every content change.
 */
const TriggerWhenLoadingFinished = ({
  children,
  attributes = {
    clientId: ''
  }
}) => {
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    // Call action when the loading component unmounts because loading is finished.
    return () => {
      // Give the preview a chance to load on WP 5.8.
      setTimeout(() => {
        (0,_wordpress_hooks__WEBPACK_IMPORTED_MODULE_6__.doAction)('advanced-sidebar-menu.blocks.preview.loading-finished', {
          values: attributes,
          clientId: attributes.clientId
        });
      }, 100);
    };
  });

  /**
   * ServerSideRender returns a <RawHTML /> filled with an error object when fetch fails.
   *
   * We throw an error, so our `ErrorBoundary` will catch it, otherwise we end up
   * with a "React objects may not be used as children" error, which means nothing.
   */
  if ('string' === typeof children?.props?.children?.errorMsg) {
    throw new Error(children?.props?.children?.errorMsg ?? 'Failed');
  }
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsxs)("div", {
    className: _preview_pcss__WEBPACK_IMPORTED_MODULE_13__["default"].spinWrap,
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsx)("div", {
      className: _preview_pcss__WEBPACK_IMPORTED_MODULE_13__["default"].spin,
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Spinner, {})
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsx)("div", {
      className: _preview_pcss__WEBPACK_IMPORTED_MODULE_13__["default"].spinContent,
      children: children
    })]
  });
};
const Preview = ({
  attributes,
  block,
  clientId
}) => {
  const blockProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__.useBlockProps)({
    className: _preview_pcss__WEBPACK_IMPORTED_MODULE_13__["default"].wrap
  });
  if ('' !== _globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.error) {
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsx)("div", {
      className: _preview_pcss__WEBPACK_IMPORTED_MODULE_13__["default"].error,
      dangerouslySetInnerHTML: {
        __html: dompurify__WEBPACK_IMPORTED_MODULE_5__["default"].sanitize(_globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.error)
      }
    });
  }
  const sanitizedClientId = sanitizeClientId(clientId);
  let attributesToSend = {
    ...attributes,
    isServerSideRenderRequest: true,
    clientId: sanitizedClientId,
    sidebarId: getSidebarId(clientId)
  };
  /**
   * Filters the attributes sent to the preview.
   *
   * @since 9.6.3
   *
   * @param {Object} attributes The attributes to send to the preview.
   * @param {string} block      The block name.
   * @param {string} clientId   The client id.
   */
  attributesToSend = (0,_wordpress_hooks__WEBPACK_IMPORTED_MODULE_6__.applyFilters)('advanced-sidebar-menu.blocks.preview.attributes', attributesToSend, block, clientId);
  return (
    /*#__PURE__*/
    // eslint-disable-next-line jsx-a11y/click-events-have-key-events,jsx-a11y/no-static-element-interactions
    (0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsx)("div", {
      ...blockProps,
      onClick: ev => {
        const element = ev.target;
        if ('A' === element.nodeName || 'SPAN' === element.nodeName && 'A' === element.parentNode?.nodeName) {
          ev.preventDefault();
        }
      },
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_14__.jsx)((_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_2___default()), {
        EmptyResponsePlaceholder: placeholder(block),
        LoadingResponsePlaceholder: TriggerWhenLoadingFinished,
        attributes: attributesToSend,
        block: block,
        httpMethod: 'POST',
        skipBlockSupportAttributes: true
      })
    })
  );
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Preview);

/***/ }),

/***/ 1669:
/*!*************************!*\
  !*** external "jQuery" ***!
  \*************************/
/***/ ((module) => {

"use strict";
module.exports = jQuery;

/***/ }),

/***/ 1734:
/*!**************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/an-object.js ***!
  \**************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var isObject = __webpack_require__(/*! ../internals/is-object */ 3763);

var $String = String;
var $TypeError = TypeError;

// `Assert: Type(argument) is Object`
module.exports = function (argument) {
  if (isObject(argument)) return argument;
  throw new $TypeError($String(argument) + ' is not an object');
};


/***/ }),

/***/ 1738:
/*!*****************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/object-define-properties.js ***!
  \*****************************************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";

var DESCRIPTORS = __webpack_require__(/*! ../internals/descriptors */ 2465);
var V8_PROTOTYPE_DEFINE_BUG = __webpack_require__(/*! ../internals/v8-prototype-define-bug */ 7207);
var definePropertyModule = __webpack_require__(/*! ../internals/object-define-property */ 742);
var anObject = __webpack_require__(/*! ../internals/an-object */ 1734);
var toIndexedObject = __webpack_require__(/*! ../internals/to-indexed-object */ 6256);
var objectKeys = __webpack_require__(/*! ../internals/object-keys */ 7101);

// `Object.defineProperties` method
// https://tc39.es/ecma262/#sec-object.defineproperties
// eslint-disable-next-line es/no-object-defineproperties -- safe
exports.f = DESCRIPTORS && !V8_PROTOTYPE_DEFINE_BUG ? Object.defineProperties : function defineProperties(O, Properties) {
  anObject(O);
  var props = toIndexedObject(Properties);
  var keys = objectKeys(Properties);
  var length = keys.length;
  var index = 0;
  var key;
  while (length > index) definePropertyModule.f(O, key = keys[index++], props[key]);
  return O;
};


/***/ }),

/***/ 1782:
/*!**********************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/well-known-symbol.js ***!
  \**********************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var globalThis = __webpack_require__(/*! ../internals/global-this */ 7793);
var shared = __webpack_require__(/*! ../internals/shared */ 438);
var hasOwn = __webpack_require__(/*! ../internals/has-own-property */ 9422);
var uid = __webpack_require__(/*! ../internals/uid */ 8581);
var NATIVE_SYMBOL = __webpack_require__(/*! ../internals/symbol-constructor-detection */ 9148);
var USE_SYMBOL_AS_UID = __webpack_require__(/*! ../internals/use-symbol-as-uid */ 5989);

var Symbol = globalThis.Symbol;
var WellKnownSymbolsStore = shared('wks');
var createWellKnownSymbol = USE_SYMBOL_AS_UID ? Symbol['for'] || Symbol : Symbol && Symbol.withoutSetter || uid;

module.exports = function (name) {
  if (!hasOwn(WellKnownSymbolsStore, name)) {
    WellKnownSymbolsStore[name] = NATIVE_SYMBOL && hasOwn(Symbol, name)
      ? Symbol[name]
      : createWellKnownSymbol('Symbol.' + name);
  } return WellKnownSymbolsStore[name];
};


/***/ }),

/***/ 1825:
/*!*****************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/require-object-coercible.js ***!
  \*****************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var isNullOrUndefined = __webpack_require__(/*! ../internals/is-null-or-undefined */ 8650);

var $TypeError = TypeError;

// `RequireObjectCoercible` abstract operation
// https://tc39.es/ecma262/#sec-requireobjectcoercible
module.exports = function (it) {
  if (isNullOrUndefined(it)) throw new $TypeError("Can't call method on " + it);
  return it;
};


/***/ }),

/***/ 1891:
/*!****************************************************!*\
  !*** ./js/src/gutenberg/blocks/NavigationIcon.tsx ***!
  \****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react/jsx-runtime */ 7157);

const NavigationIcon = /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsxs)("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 20 20",
  children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("rect", {
    x: "0",
    fill: "none",
    width: "20",
    height: "20"
  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("g", {
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("path", {
      fill: "#044567",
      d: "M19 17V2c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v15c0 .55.45 1 1 1h15c.55 0 1-.45 1-1z M4 3h13v4H4V3z M5 4h3v2H5V4zm4 0h3v2H9V4zm4 0h3v2H13V4z M4.5 9 c .28 0 .5 .22 .5 .5s-.22.5-.5.5s-.5-.22-.5-.5s.22-.5 .5-.5z M6 9h4v1H6v-1z M4.5 12 c .28 0 .5 .22 .5 .5s-.22.5-.5.5s-.5-.22-.5-.5s.22-.5 .5-.5z M6 12h4v1H6v-1z M4.5 15 c .28 0 .5 .22 .5 .5s-.22.5-.5.5s-.5-.22-.5-.5s.22-.5 .5-.5z M6 15h4v1H6v-1z M12.5 10.5 h4v1h-4v-1z M12.5 12 h4v1h-4v-1z M12.5 13.5 h4v1h-4v-1z"
    })
  })]
});
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (NavigationIcon);

/***/ }),

/***/ 1952:
/*!*******************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/iterator-close.js ***!
  \*******************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var call = __webpack_require__(/*! ../internals/function-call */ 5156);
var anObject = __webpack_require__(/*! ../internals/an-object */ 1734);
var getMethod = __webpack_require__(/*! ../internals/get-method */ 789);

module.exports = function (iterator, kind, value) {
  var innerResult, innerError;
  anObject(iterator);
  try {
    innerResult = getMethod(iterator, 'return');
    if (!innerResult) {
      if (kind === 'throw') throw value;
      return value;
    }
    innerResult = call(innerResult, iterator);
  } catch (error) {
    innerError = true;
    innerResult = error;
  }
  if (kind === 'throw') throw value;
  if (innerError) throw innerResult;
  anObject(innerResult);
  return value;
};


/***/ }),

/***/ 2013:
/*!*************************************************!*\
  !*** ./js/src/gutenberg/blocks/pages/edit.pcss ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
// extracted by mini-css-extract-plugin
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({"error":"edit__error__lZ"});

/***/ }),

/***/ 2120:
/*!****************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/is-callable.js ***!
  \****************************************************************************************************************/
/***/ ((module) => {

"use strict";

// https://tc39.es/ecma262/#sec-IsHTMLDDA-internal-slot
var documentAll = typeof document == 'object' && document.all;

// `IsCallable` abstract operation
// https://tc39.es/ecma262/#sec-iscallable
// eslint-disable-next-line unicorn/no-typeof-undefined -- required for testing
module.exports = typeof documentAll == 'undefined' && documentAll !== undefined ? function (argument) {
  return typeof argument == 'function' || argument === documentAll;
} : function (argument) {
  return typeof argument == 'function';
};


/***/ }),

/***/ 2413:
/*!************************************************************************************************************************!*\
  !*** ./.yarn/cache/react-npm-18.2.0-1eae08fee2-b562d9b569.zip/node_modules/react/cjs/react-jsx-runtime.development.js ***!
  \************************************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/**
 * @license React
 * react-jsx-runtime.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



if (true) {
  (function() {
'use strict';

var React = __webpack_require__(/*! react */ 1594);

// ATTENTION
// When adding new symbols to this file,
// Please consider also adding to 'react-devtools-shared/src/backend/ReactSymbols'
// The Symbol used to tag the ReactElement-like types.
var REACT_ELEMENT_TYPE = Symbol.for('react.element');
var REACT_PORTAL_TYPE = Symbol.for('react.portal');
var REACT_FRAGMENT_TYPE = Symbol.for('react.fragment');
var REACT_STRICT_MODE_TYPE = Symbol.for('react.strict_mode');
var REACT_PROFILER_TYPE = Symbol.for('react.profiler');
var REACT_PROVIDER_TYPE = Symbol.for('react.provider');
var REACT_CONTEXT_TYPE = Symbol.for('react.context');
var REACT_FORWARD_REF_TYPE = Symbol.for('react.forward_ref');
var REACT_SUSPENSE_TYPE = Symbol.for('react.suspense');
var REACT_SUSPENSE_LIST_TYPE = Symbol.for('react.suspense_list');
var REACT_MEMO_TYPE = Symbol.for('react.memo');
var REACT_LAZY_TYPE = Symbol.for('react.lazy');
var REACT_OFFSCREEN_TYPE = Symbol.for('react.offscreen');
var MAYBE_ITERATOR_SYMBOL = Symbol.iterator;
var FAUX_ITERATOR_SYMBOL = '@@iterator';
function getIteratorFn(maybeIterable) {
  if (maybeIterable === null || typeof maybeIterable !== 'object') {
    return null;
  }

  var maybeIterator = MAYBE_ITERATOR_SYMBOL && maybeIterable[MAYBE_ITERATOR_SYMBOL] || maybeIterable[FAUX_ITERATOR_SYMBOL];

  if (typeof maybeIterator === 'function') {
    return maybeIterator;
  }

  return null;
}

var ReactSharedInternals = React.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;

function error(format) {
  {
    {
      for (var _len2 = arguments.length, args = new Array(_len2 > 1 ? _len2 - 1 : 0), _key2 = 1; _key2 < _len2; _key2++) {
        args[_key2 - 1] = arguments[_key2];
      }

      printWarning('error', format, args);
    }
  }
}

function printWarning(level, format, args) {
  // When changing this logic, you might want to also
  // update consoleWithStackDev.www.js as well.
  {
    var ReactDebugCurrentFrame = ReactSharedInternals.ReactDebugCurrentFrame;
    var stack = ReactDebugCurrentFrame.getStackAddendum();

    if (stack !== '') {
      format += '%s';
      args = args.concat([stack]);
    } // eslint-disable-next-line react-internal/safe-string-coercion


    var argsWithFormat = args.map(function (item) {
      return String(item);
    }); // Careful: RN currently depends on this prefix

    argsWithFormat.unshift('Warning: ' + format); // We intentionally don't use spread (or .apply) directly because it
    // breaks IE9: https://github.com/facebook/react/issues/13610
    // eslint-disable-next-line react-internal/no-production-logging

    Function.prototype.apply.call(console[level], console, argsWithFormat);
  }
}

// -----------------------------------------------------------------------------

var enableScopeAPI = false; // Experimental Create Event Handle API.
var enableCacheElement = false;
var enableTransitionTracing = false; // No known bugs, but needs performance testing

var enableLegacyHidden = false; // Enables unstable_avoidThisFallback feature in Fiber
// stuff. Intended to enable React core members to more easily debug scheduling
// issues in DEV builds.

var enableDebugTracing = false; // Track which Fiber(s) schedule render work.

var REACT_MODULE_REFERENCE;

{
  REACT_MODULE_REFERENCE = Symbol.for('react.module.reference');
}

function isValidElementType(type) {
  if (typeof type === 'string' || typeof type === 'function') {
    return true;
  } // Note: typeof might be other than 'symbol' or 'number' (e.g. if it's a polyfill).


  if (type === REACT_FRAGMENT_TYPE || type === REACT_PROFILER_TYPE || enableDebugTracing  || type === REACT_STRICT_MODE_TYPE || type === REACT_SUSPENSE_TYPE || type === REACT_SUSPENSE_LIST_TYPE || enableLegacyHidden  || type === REACT_OFFSCREEN_TYPE || enableScopeAPI  || enableCacheElement  || enableTransitionTracing ) {
    return true;
  }

  if (typeof type === 'object' && type !== null) {
    if (type.$$typeof === REACT_LAZY_TYPE || type.$$typeof === REACT_MEMO_TYPE || type.$$typeof === REACT_PROVIDER_TYPE || type.$$typeof === REACT_CONTEXT_TYPE || type.$$typeof === REACT_FORWARD_REF_TYPE || // This needs to include all possible module reference object
    // types supported by any Flight configuration anywhere since
    // we don't know which Flight build this will end up being used
    // with.
    type.$$typeof === REACT_MODULE_REFERENCE || type.getModuleId !== undefined) {
      return true;
    }
  }

  return false;
}

function getWrappedName(outerType, innerType, wrapperName) {
  var displayName = outerType.displayName;

  if (displayName) {
    return displayName;
  }

  var functionName = innerType.displayName || innerType.name || '';
  return functionName !== '' ? wrapperName + "(" + functionName + ")" : wrapperName;
} // Keep in sync with react-reconciler/getComponentNameFromFiber


function getContextName(type) {
  return type.displayName || 'Context';
} // Note that the reconciler package should generally prefer to use getComponentNameFromFiber() instead.


function getComponentNameFromType(type) {
  if (type == null) {
    // Host root, text node or just invalid type.
    return null;
  }

  {
    if (typeof type.tag === 'number') {
      error('Received an unexpected object in getComponentNameFromType(). ' + 'This is likely a bug in React. Please file an issue.');
    }
  }

  if (typeof type === 'function') {
    return type.displayName || type.name || null;
  }

  if (typeof type === 'string') {
    return type;
  }

  switch (type) {
    case REACT_FRAGMENT_TYPE:
      return 'Fragment';

    case REACT_PORTAL_TYPE:
      return 'Portal';

    case REACT_PROFILER_TYPE:
      return 'Profiler';

    case REACT_STRICT_MODE_TYPE:
      return 'StrictMode';

    case REACT_SUSPENSE_TYPE:
      return 'Suspense';

    case REACT_SUSPENSE_LIST_TYPE:
      return 'SuspenseList';

  }

  if (typeof type === 'object') {
    switch (type.$$typeof) {
      case REACT_CONTEXT_TYPE:
        var context = type;
        return getContextName(context) + '.Consumer';

      case REACT_PROVIDER_TYPE:
        var provider = type;
        return getContextName(provider._context) + '.Provider';

      case REACT_FORWARD_REF_TYPE:
        return getWrappedName(type, type.render, 'ForwardRef');

      case REACT_MEMO_TYPE:
        var outerName = type.displayName || null;

        if (outerName !== null) {
          return outerName;
        }

        return getComponentNameFromType(type.type) || 'Memo';

      case REACT_LAZY_TYPE:
        {
          var lazyComponent = type;
          var payload = lazyComponent._payload;
          var init = lazyComponent._init;

          try {
            return getComponentNameFromType(init(payload));
          } catch (x) {
            return null;
          }
        }

      // eslint-disable-next-line no-fallthrough
    }
  }

  return null;
}

var assign = Object.assign;

// Helpers to patch console.logs to avoid logging during side-effect free
// replaying on render function. This currently only patches the object
// lazily which won't cover if the log function was extracted eagerly.
// We could also eagerly patch the method.
var disabledDepth = 0;
var prevLog;
var prevInfo;
var prevWarn;
var prevError;
var prevGroup;
var prevGroupCollapsed;
var prevGroupEnd;

function disabledLog() {}

disabledLog.__reactDisabledLog = true;
function disableLogs() {
  {
    if (disabledDepth === 0) {
      /* eslint-disable react-internal/no-production-logging */
      prevLog = console.log;
      prevInfo = console.info;
      prevWarn = console.warn;
      prevError = console.error;
      prevGroup = console.group;
      prevGroupCollapsed = console.groupCollapsed;
      prevGroupEnd = console.groupEnd; // https://github.com/facebook/react/issues/19099

      var props = {
        configurable: true,
        enumerable: true,
        value: disabledLog,
        writable: true
      }; // $FlowFixMe Flow thinks console is immutable.

      Object.defineProperties(console, {
        info: props,
        log: props,
        warn: props,
        error: props,
        group: props,
        groupCollapsed: props,
        groupEnd: props
      });
      /* eslint-enable react-internal/no-production-logging */
    }

    disabledDepth++;
  }
}
function reenableLogs() {
  {
    disabledDepth--;

    if (disabledDepth === 0) {
      /* eslint-disable react-internal/no-production-logging */
      var props = {
        configurable: true,
        enumerable: true,
        writable: true
      }; // $FlowFixMe Flow thinks console is immutable.

      Object.defineProperties(console, {
        log: assign({}, props, {
          value: prevLog
        }),
        info: assign({}, props, {
          value: prevInfo
        }),
        warn: assign({}, props, {
          value: prevWarn
        }),
        error: assign({}, props, {
          value: prevError
        }),
        group: assign({}, props, {
          value: prevGroup
        }),
        groupCollapsed: assign({}, props, {
          value: prevGroupCollapsed
        }),
        groupEnd: assign({}, props, {
          value: prevGroupEnd
        })
      });
      /* eslint-enable react-internal/no-production-logging */
    }

    if (disabledDepth < 0) {
      error('disabledDepth fell below zero. ' + 'This is a bug in React. Please file an issue.');
    }
  }
}

var ReactCurrentDispatcher = ReactSharedInternals.ReactCurrentDispatcher;
var prefix;
function describeBuiltInComponentFrame(name, source, ownerFn) {
  {
    if (prefix === undefined) {
      // Extract the VM specific prefix used by each line.
      try {
        throw Error();
      } catch (x) {
        var match = x.stack.trim().match(/\n( *(at )?)/);
        prefix = match && match[1] || '';
      }
    } // We use the prefix to ensure our stacks line up with native stack frames.


    return '\n' + prefix + name;
  }
}
var reentry = false;
var componentFrameCache;

{
  var PossiblyWeakMap = typeof WeakMap === 'function' ? WeakMap : Map;
  componentFrameCache = new PossiblyWeakMap();
}

function describeNativeComponentFrame(fn, construct) {
  // If something asked for a stack inside a fake render, it should get ignored.
  if ( !fn || reentry) {
    return '';
  }

  {
    var frame = componentFrameCache.get(fn);

    if (frame !== undefined) {
      return frame;
    }
  }

  var control;
  reentry = true;
  var previousPrepareStackTrace = Error.prepareStackTrace; // $FlowFixMe It does accept undefined.

  Error.prepareStackTrace = undefined;
  var previousDispatcher;

  {
    previousDispatcher = ReactCurrentDispatcher.current; // Set the dispatcher in DEV because this might be call in the render function
    // for warnings.

    ReactCurrentDispatcher.current = null;
    disableLogs();
  }

  try {
    // This should throw.
    if (construct) {
      // Something should be setting the props in the constructor.
      var Fake = function () {
        throw Error();
      }; // $FlowFixMe


      Object.defineProperty(Fake.prototype, 'props', {
        set: function () {
          // We use a throwing setter instead of frozen or non-writable props
          // because that won't throw in a non-strict mode function.
          throw Error();
        }
      });

      if (typeof Reflect === 'object' && Reflect.construct) {
        // We construct a different control for this case to include any extra
        // frames added by the construct call.
        try {
          Reflect.construct(Fake, []);
        } catch (x) {
          control = x;
        }

        Reflect.construct(fn, [], Fake);
      } else {
        try {
          Fake.call();
        } catch (x) {
          control = x;
        }

        fn.call(Fake.prototype);
      }
    } else {
      try {
        throw Error();
      } catch (x) {
        control = x;
      }

      fn();
    }
  } catch (sample) {
    // This is inlined manually because closure doesn't do it for us.
    if (sample && control && typeof sample.stack === 'string') {
      // This extracts the first frame from the sample that isn't also in the control.
      // Skipping one frame that we assume is the frame that calls the two.
      var sampleLines = sample.stack.split('\n');
      var controlLines = control.stack.split('\n');
      var s = sampleLines.length - 1;
      var c = controlLines.length - 1;

      while (s >= 1 && c >= 0 && sampleLines[s] !== controlLines[c]) {
        // We expect at least one stack frame to be shared.
        // Typically this will be the root most one. However, stack frames may be
        // cut off due to maximum stack limits. In this case, one maybe cut off
        // earlier than the other. We assume that the sample is longer or the same
        // and there for cut off earlier. So we should find the root most frame in
        // the sample somewhere in the control.
        c--;
      }

      for (; s >= 1 && c >= 0; s--, c--) {
        // Next we find the first one that isn't the same which should be the
        // frame that called our sample function and the control.
        if (sampleLines[s] !== controlLines[c]) {
          // In V8, the first line is describing the message but other VMs don't.
          // If we're about to return the first line, and the control is also on the same
          // line, that's a pretty good indicator that our sample threw at same line as
          // the control. I.e. before we entered the sample frame. So we ignore this result.
          // This can happen if you passed a class to function component, or non-function.
          if (s !== 1 || c !== 1) {
            do {
              s--;
              c--; // We may still have similar intermediate frames from the construct call.
              // The next one that isn't the same should be our match though.

              if (c < 0 || sampleLines[s] !== controlLines[c]) {
                // V8 adds a "new" prefix for native classes. Let's remove it to make it prettier.
                var _frame = '\n' + sampleLines[s].replace(' at new ', ' at '); // If our component frame is labeled "<anonymous>"
                // but we have a user-provided "displayName"
                // splice it in to make the stack more readable.


                if (fn.displayName && _frame.includes('<anonymous>')) {
                  _frame = _frame.replace('<anonymous>', fn.displayName);
                }

                {
                  if (typeof fn === 'function') {
                    componentFrameCache.set(fn, _frame);
                  }
                } // Return the line we found.


                return _frame;
              }
            } while (s >= 1 && c >= 0);
          }

          break;
        }
      }
    }
  } finally {
    reentry = false;

    {
      ReactCurrentDispatcher.current = previousDispatcher;
      reenableLogs();
    }

    Error.prepareStackTrace = previousPrepareStackTrace;
  } // Fallback to just using the name if we couldn't make it throw.


  var name = fn ? fn.displayName || fn.name : '';
  var syntheticFrame = name ? describeBuiltInComponentFrame(name) : '';

  {
    if (typeof fn === 'function') {
      componentFrameCache.set(fn, syntheticFrame);
    }
  }

  return syntheticFrame;
}
function describeFunctionComponentFrame(fn, source, ownerFn) {
  {
    return describeNativeComponentFrame(fn, false);
  }
}

function shouldConstruct(Component) {
  var prototype = Component.prototype;
  return !!(prototype && prototype.isReactComponent);
}

function describeUnknownElementTypeFrameInDEV(type, source, ownerFn) {

  if (type == null) {
    return '';
  }

  if (typeof type === 'function') {
    {
      return describeNativeComponentFrame(type, shouldConstruct(type));
    }
  }

  if (typeof type === 'string') {
    return describeBuiltInComponentFrame(type);
  }

  switch (type) {
    case REACT_SUSPENSE_TYPE:
      return describeBuiltInComponentFrame('Suspense');

    case REACT_SUSPENSE_LIST_TYPE:
      return describeBuiltInComponentFrame('SuspenseList');
  }

  if (typeof type === 'object') {
    switch (type.$$typeof) {
      case REACT_FORWARD_REF_TYPE:
        return describeFunctionComponentFrame(type.render);

      case REACT_MEMO_TYPE:
        // Memo may contain any component type so we recursively resolve it.
        return describeUnknownElementTypeFrameInDEV(type.type, source, ownerFn);

      case REACT_LAZY_TYPE:
        {
          var lazyComponent = type;
          var payload = lazyComponent._payload;
          var init = lazyComponent._init;

          try {
            // Lazy may contain any component type so we recursively resolve it.
            return describeUnknownElementTypeFrameInDEV(init(payload), source, ownerFn);
          } catch (x) {}
        }
    }
  }

  return '';
}

var hasOwnProperty = Object.prototype.hasOwnProperty;

var loggedTypeFailures = {};
var ReactDebugCurrentFrame = ReactSharedInternals.ReactDebugCurrentFrame;

function setCurrentlyValidatingElement(element) {
  {
    if (element) {
      var owner = element._owner;
      var stack = describeUnknownElementTypeFrameInDEV(element.type, element._source, owner ? owner.type : null);
      ReactDebugCurrentFrame.setExtraStackFrame(stack);
    } else {
      ReactDebugCurrentFrame.setExtraStackFrame(null);
    }
  }
}

function checkPropTypes(typeSpecs, values, location, componentName, element) {
  {
    // $FlowFixMe This is okay but Flow doesn't know it.
    var has = Function.call.bind(hasOwnProperty);

    for (var typeSpecName in typeSpecs) {
      if (has(typeSpecs, typeSpecName)) {
        var error$1 = void 0; // Prop type validation may throw. In case they do, we don't want to
        // fail the render phase where it didn't fail before. So we log it.
        // After these have been cleaned up, we'll let them throw.

        try {
          // This is intentionally an invariant that gets caught. It's the same
          // behavior as without this statement except with a better message.
          if (typeof typeSpecs[typeSpecName] !== 'function') {
            // eslint-disable-next-line react-internal/prod-error-codes
            var err = Error((componentName || 'React class') + ': ' + location + ' type `' + typeSpecName + '` is invalid; ' + 'it must be a function, usually from the `prop-types` package, but received `' + typeof typeSpecs[typeSpecName] + '`.' + 'This often happens because of typos such as `PropTypes.function` instead of `PropTypes.func`.');
            err.name = 'Invariant Violation';
            throw err;
          }

          error$1 = typeSpecs[typeSpecName](values, typeSpecName, componentName, location, null, 'SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED');
        } catch (ex) {
          error$1 = ex;
        }

        if (error$1 && !(error$1 instanceof Error)) {
          setCurrentlyValidatingElement(element);

          error('%s: type specification of %s' + ' `%s` is invalid; the type checker ' + 'function must return `null` or an `Error` but returned a %s. ' + 'You may have forgotten to pass an argument to the type checker ' + 'creator (arrayOf, instanceOf, objectOf, oneOf, oneOfType, and ' + 'shape all require an argument).', componentName || 'React class', location, typeSpecName, typeof error$1);

          setCurrentlyValidatingElement(null);
        }

        if (error$1 instanceof Error && !(error$1.message in loggedTypeFailures)) {
          // Only monitor this failure once because there tends to be a lot of the
          // same error.
          loggedTypeFailures[error$1.message] = true;
          setCurrentlyValidatingElement(element);

          error('Failed %s type: %s', location, error$1.message);

          setCurrentlyValidatingElement(null);
        }
      }
    }
  }
}

var isArrayImpl = Array.isArray; // eslint-disable-next-line no-redeclare

function isArray(a) {
  return isArrayImpl(a);
}

/*
 * The `'' + value` pattern (used in in perf-sensitive code) throws for Symbol
 * and Temporal.* types. See https://github.com/facebook/react/pull/22064.
 *
 * The functions in this module will throw an easier-to-understand,
 * easier-to-debug exception with a clear errors message message explaining the
 * problem. (Instead of a confusing exception thrown inside the implementation
 * of the `value` object).
 */
// $FlowFixMe only called in DEV, so void return is not possible.
function typeName(value) {
  {
    // toStringTag is needed for namespaced types like Temporal.Instant
    var hasToStringTag = typeof Symbol === 'function' && Symbol.toStringTag;
    var type = hasToStringTag && value[Symbol.toStringTag] || value.constructor.name || 'Object';
    return type;
  }
} // $FlowFixMe only called in DEV, so void return is not possible.


function willCoercionThrow(value) {
  {
    try {
      testStringCoercion(value);
      return false;
    } catch (e) {
      return true;
    }
  }
}

function testStringCoercion(value) {
  // If you ended up here by following an exception call stack, here's what's
  // happened: you supplied an object or symbol value to React (as a prop, key,
  // DOM attribute, CSS property, string ref, etc.) and when React tried to
  // coerce it to a string using `'' + value`, an exception was thrown.
  //
  // The most common types that will cause this exception are `Symbol` instances
  // and Temporal objects like `Temporal.Instant`. But any object that has a
  // `valueOf` or `[Symbol.toPrimitive]` method that throws will also cause this
  // exception. (Library authors do this to prevent users from using built-in
  // numeric operators like `+` or comparison operators like `>=` because custom
  // methods are needed to perform accurate arithmetic or comparison.)
  //
  // To fix the problem, coerce this object or symbol value to a string before
  // passing it to React. The most reliable way is usually `String(value)`.
  //
  // To find which value is throwing, check the browser or debugger console.
  // Before this exception was thrown, there should be `console.error` output
  // that shows the type (Symbol, Temporal.PlainDate, etc.) that caused the
  // problem and how that type was used: key, atrribute, input value prop, etc.
  // In most cases, this console output also shows the component and its
  // ancestor components where the exception happened.
  //
  // eslint-disable-next-line react-internal/safe-string-coercion
  return '' + value;
}
function checkKeyStringCoercion(value) {
  {
    if (willCoercionThrow(value)) {
      error('The provided key is an unsupported type %s.' + ' This value must be coerced to a string before before using it here.', typeName(value));

      return testStringCoercion(value); // throw (to help callers find troubleshooting comments)
    }
  }
}

var ReactCurrentOwner = ReactSharedInternals.ReactCurrentOwner;
var RESERVED_PROPS = {
  key: true,
  ref: true,
  __self: true,
  __source: true
};
var specialPropKeyWarningShown;
var specialPropRefWarningShown;
var didWarnAboutStringRefs;

{
  didWarnAboutStringRefs = {};
}

function hasValidRef(config) {
  {
    if (hasOwnProperty.call(config, 'ref')) {
      var getter = Object.getOwnPropertyDescriptor(config, 'ref').get;

      if (getter && getter.isReactWarning) {
        return false;
      }
    }
  }

  return config.ref !== undefined;
}

function hasValidKey(config) {
  {
    if (hasOwnProperty.call(config, 'key')) {
      var getter = Object.getOwnPropertyDescriptor(config, 'key').get;

      if (getter && getter.isReactWarning) {
        return false;
      }
    }
  }

  return config.key !== undefined;
}

function warnIfStringRefCannotBeAutoConverted(config, self) {
  {
    if (typeof config.ref === 'string' && ReactCurrentOwner.current && self && ReactCurrentOwner.current.stateNode !== self) {
      var componentName = getComponentNameFromType(ReactCurrentOwner.current.type);

      if (!didWarnAboutStringRefs[componentName]) {
        error('Component "%s" contains the string ref "%s". ' + 'Support for string refs will be removed in a future major release. ' + 'This case cannot be automatically converted to an arrow function. ' + 'We ask you to manually fix this case by using useRef() or createRef() instead. ' + 'Learn more about using refs safely here: ' + 'https://reactjs.org/link/strict-mode-string-ref', getComponentNameFromType(ReactCurrentOwner.current.type), config.ref);

        didWarnAboutStringRefs[componentName] = true;
      }
    }
  }
}

function defineKeyPropWarningGetter(props, displayName) {
  {
    var warnAboutAccessingKey = function () {
      if (!specialPropKeyWarningShown) {
        specialPropKeyWarningShown = true;

        error('%s: `key` is not a prop. Trying to access it will result ' + 'in `undefined` being returned. If you need to access the same ' + 'value within the child component, you should pass it as a different ' + 'prop. (https://reactjs.org/link/special-props)', displayName);
      }
    };

    warnAboutAccessingKey.isReactWarning = true;
    Object.defineProperty(props, 'key', {
      get: warnAboutAccessingKey,
      configurable: true
    });
  }
}

function defineRefPropWarningGetter(props, displayName) {
  {
    var warnAboutAccessingRef = function () {
      if (!specialPropRefWarningShown) {
        specialPropRefWarningShown = true;

        error('%s: `ref` is not a prop. Trying to access it will result ' + 'in `undefined` being returned. If you need to access the same ' + 'value within the child component, you should pass it as a different ' + 'prop. (https://reactjs.org/link/special-props)', displayName);
      }
    };

    warnAboutAccessingRef.isReactWarning = true;
    Object.defineProperty(props, 'ref', {
      get: warnAboutAccessingRef,
      configurable: true
    });
  }
}
/**
 * Factory method to create a new React element. This no longer adheres to
 * the class pattern, so do not use new to call it. Also, instanceof check
 * will not work. Instead test $$typeof field against Symbol.for('react.element') to check
 * if something is a React Element.
 *
 * @param {*} type
 * @param {*} props
 * @param {*} key
 * @param {string|object} ref
 * @param {*} owner
 * @param {*} self A *temporary* helper to detect places where `this` is
 * different from the `owner` when React.createElement is called, so that we
 * can warn. We want to get rid of owner and replace string `ref`s with arrow
 * functions, and as long as `this` and owner are the same, there will be no
 * change in behavior.
 * @param {*} source An annotation object (added by a transpiler or otherwise)
 * indicating filename, line number, and/or other information.
 * @internal
 */


var ReactElement = function (type, key, ref, self, source, owner, props) {
  var element = {
    // This tag allows us to uniquely identify this as a React Element
    $$typeof: REACT_ELEMENT_TYPE,
    // Built-in properties that belong on the element
    type: type,
    key: key,
    ref: ref,
    props: props,
    // Record the component responsible for creating this element.
    _owner: owner
  };

  {
    // The validation flag is currently mutative. We put it on
    // an external backing store so that we can freeze the whole object.
    // This can be replaced with a WeakMap once they are implemented in
    // commonly used development environments.
    element._store = {}; // To make comparing ReactElements easier for testing purposes, we make
    // the validation flag non-enumerable (where possible, which should
    // include every environment we run tests in), so the test framework
    // ignores it.

    Object.defineProperty(element._store, 'validated', {
      configurable: false,
      enumerable: false,
      writable: true,
      value: false
    }); // self and source are DEV only properties.

    Object.defineProperty(element, '_self', {
      configurable: false,
      enumerable: false,
      writable: false,
      value: self
    }); // Two elements created in two different places should be considered
    // equal for testing purposes and therefore we hide it from enumeration.

    Object.defineProperty(element, '_source', {
      configurable: false,
      enumerable: false,
      writable: false,
      value: source
    });

    if (Object.freeze) {
      Object.freeze(element.props);
      Object.freeze(element);
    }
  }

  return element;
};
/**
 * https://github.com/reactjs/rfcs/pull/107
 * @param {*} type
 * @param {object} props
 * @param {string} key
 */

function jsxDEV(type, config, maybeKey, source, self) {
  {
    var propName; // Reserved names are extracted

    var props = {};
    var key = null;
    var ref = null; // Currently, key can be spread in as a prop. This causes a potential
    // issue if key is also explicitly declared (ie. <div {...props} key="Hi" />
    // or <div key="Hi" {...props} /> ). We want to deprecate key spread,
    // but as an intermediary step, we will use jsxDEV for everything except
    // <div {...props} key="Hi" />, because we aren't currently able to tell if
    // key is explicitly declared to be undefined or not.

    if (maybeKey !== undefined) {
      {
        checkKeyStringCoercion(maybeKey);
      }

      key = '' + maybeKey;
    }

    if (hasValidKey(config)) {
      {
        checkKeyStringCoercion(config.key);
      }

      key = '' + config.key;
    }

    if (hasValidRef(config)) {
      ref = config.ref;
      warnIfStringRefCannotBeAutoConverted(config, self);
    } // Remaining properties are added to a new props object


    for (propName in config) {
      if (hasOwnProperty.call(config, propName) && !RESERVED_PROPS.hasOwnProperty(propName)) {
        props[propName] = config[propName];
      }
    } // Resolve default props


    if (type && type.defaultProps) {
      var defaultProps = type.defaultProps;

      for (propName in defaultProps) {
        if (props[propName] === undefined) {
          props[propName] = defaultProps[propName];
        }
      }
    }

    if (key || ref) {
      var displayName = typeof type === 'function' ? type.displayName || type.name || 'Unknown' : type;

      if (key) {
        defineKeyPropWarningGetter(props, displayName);
      }

      if (ref) {
        defineRefPropWarningGetter(props, displayName);
      }
    }

    return ReactElement(type, key, ref, self, source, ReactCurrentOwner.current, props);
  }
}

var ReactCurrentOwner$1 = ReactSharedInternals.ReactCurrentOwner;
var ReactDebugCurrentFrame$1 = ReactSharedInternals.ReactDebugCurrentFrame;

function setCurrentlyValidatingElement$1(element) {
  {
    if (element) {
      var owner = element._owner;
      var stack = describeUnknownElementTypeFrameInDEV(element.type, element._source, owner ? owner.type : null);
      ReactDebugCurrentFrame$1.setExtraStackFrame(stack);
    } else {
      ReactDebugCurrentFrame$1.setExtraStackFrame(null);
    }
  }
}

var propTypesMisspellWarningShown;

{
  propTypesMisspellWarningShown = false;
}
/**
 * Verifies the object is a ReactElement.
 * See https://reactjs.org/docs/react-api.html#isvalidelement
 * @param {?object} object
 * @return {boolean} True if `object` is a ReactElement.
 * @final
 */


function isValidElement(object) {
  {
    return typeof object === 'object' && object !== null && object.$$typeof === REACT_ELEMENT_TYPE;
  }
}

function getDeclarationErrorAddendum() {
  {
    if (ReactCurrentOwner$1.current) {
      var name = getComponentNameFromType(ReactCurrentOwner$1.current.type);

      if (name) {
        return '\n\nCheck the render method of `' + name + '`.';
      }
    }

    return '';
  }
}

function getSourceInfoErrorAddendum(source) {
  {
    if (source !== undefined) {
      var fileName = source.fileName.replace(/^.*[\\\/]/, '');
      var lineNumber = source.lineNumber;
      return '\n\nCheck your code at ' + fileName + ':' + lineNumber + '.';
    }

    return '';
  }
}
/**
 * Warn if there's no key explicitly set on dynamic arrays of children or
 * object keys are not valid. This allows us to keep track of children between
 * updates.
 */


var ownerHasKeyUseWarning = {};

function getCurrentComponentErrorInfo(parentType) {
  {
    var info = getDeclarationErrorAddendum();

    if (!info) {
      var parentName = typeof parentType === 'string' ? parentType : parentType.displayName || parentType.name;

      if (parentName) {
        info = "\n\nCheck the top-level render call using <" + parentName + ">.";
      }
    }

    return info;
  }
}
/**
 * Warn if the element doesn't have an explicit key assigned to it.
 * This element is in an array. The array could grow and shrink or be
 * reordered. All children that haven't already been validated are required to
 * have a "key" property assigned to it. Error statuses are cached so a warning
 * will only be shown once.
 *
 * @internal
 * @param {ReactElement} element Element that requires a key.
 * @param {*} parentType element's parent's type.
 */


function validateExplicitKey(element, parentType) {
  {
    if (!element._store || element._store.validated || element.key != null) {
      return;
    }

    element._store.validated = true;
    var currentComponentErrorInfo = getCurrentComponentErrorInfo(parentType);

    if (ownerHasKeyUseWarning[currentComponentErrorInfo]) {
      return;
    }

    ownerHasKeyUseWarning[currentComponentErrorInfo] = true; // Usually the current owner is the offender, but if it accepts children as a
    // property, it may be the creator of the child that's responsible for
    // assigning it a key.

    var childOwner = '';

    if (element && element._owner && element._owner !== ReactCurrentOwner$1.current) {
      // Give the component that originally created this child.
      childOwner = " It was passed a child from " + getComponentNameFromType(element._owner.type) + ".";
    }

    setCurrentlyValidatingElement$1(element);

    error('Each child in a list should have a unique "key" prop.' + '%s%s See https://reactjs.org/link/warning-keys for more information.', currentComponentErrorInfo, childOwner);

    setCurrentlyValidatingElement$1(null);
  }
}
/**
 * Ensure that every element either is passed in a static location, in an
 * array with an explicit keys property defined, or in an object literal
 * with valid key property.
 *
 * @internal
 * @param {ReactNode} node Statically passed child of any type.
 * @param {*} parentType node's parent's type.
 */


function validateChildKeys(node, parentType) {
  {
    if (typeof node !== 'object') {
      return;
    }

    if (isArray(node)) {
      for (var i = 0; i < node.length; i++) {
        var child = node[i];

        if (isValidElement(child)) {
          validateExplicitKey(child, parentType);
        }
      }
    } else if (isValidElement(node)) {
      // This element was passed in a valid location.
      if (node._store) {
        node._store.validated = true;
      }
    } else if (node) {
      var iteratorFn = getIteratorFn(node);

      if (typeof iteratorFn === 'function') {
        // Entry iterators used to provide implicit keys,
        // but now we print a separate warning for them later.
        if (iteratorFn !== node.entries) {
          var iterator = iteratorFn.call(node);
          var step;

          while (!(step = iterator.next()).done) {
            if (isValidElement(step.value)) {
              validateExplicitKey(step.value, parentType);
            }
          }
        }
      }
    }
  }
}
/**
 * Given an element, validate that its props follow the propTypes definition,
 * provided by the type.
 *
 * @param {ReactElement} element
 */


function validatePropTypes(element) {
  {
    var type = element.type;

    if (type === null || type === undefined || typeof type === 'string') {
      return;
    }

    var propTypes;

    if (typeof type === 'function') {
      propTypes = type.propTypes;
    } else if (typeof type === 'object' && (type.$$typeof === REACT_FORWARD_REF_TYPE || // Note: Memo only checks outer props here.
    // Inner props are checked in the reconciler.
    type.$$typeof === REACT_MEMO_TYPE)) {
      propTypes = type.propTypes;
    } else {
      return;
    }

    if (propTypes) {
      // Intentionally inside to avoid triggering lazy initializers:
      var name = getComponentNameFromType(type);
      checkPropTypes(propTypes, element.props, 'prop', name, element);
    } else if (type.PropTypes !== undefined && !propTypesMisspellWarningShown) {
      propTypesMisspellWarningShown = true; // Intentionally inside to avoid triggering lazy initializers:

      var _name = getComponentNameFromType(type);

      error('Component %s declared `PropTypes` instead of `propTypes`. Did you misspell the property assignment?', _name || 'Unknown');
    }

    if (typeof type.getDefaultProps === 'function' && !type.getDefaultProps.isReactClassApproved) {
      error('getDefaultProps is only used on classic React.createClass ' + 'definitions. Use a static property named `defaultProps` instead.');
    }
  }
}
/**
 * Given a fragment, validate that it can only be provided with fragment props
 * @param {ReactElement} fragment
 */


function validateFragmentProps(fragment) {
  {
    var keys = Object.keys(fragment.props);

    for (var i = 0; i < keys.length; i++) {
      var key = keys[i];

      if (key !== 'children' && key !== 'key') {
        setCurrentlyValidatingElement$1(fragment);

        error('Invalid prop `%s` supplied to `React.Fragment`. ' + 'React.Fragment can only have `key` and `children` props.', key);

        setCurrentlyValidatingElement$1(null);
        break;
      }
    }

    if (fragment.ref !== null) {
      setCurrentlyValidatingElement$1(fragment);

      error('Invalid attribute `ref` supplied to `React.Fragment`.');

      setCurrentlyValidatingElement$1(null);
    }
  }
}

function jsxWithValidation(type, props, key, isStaticChildren, source, self) {
  {
    var validType = isValidElementType(type); // We warn in this case but don't throw. We expect the element creation to
    // succeed and there will likely be errors in render.

    if (!validType) {
      var info = '';

      if (type === undefined || typeof type === 'object' && type !== null && Object.keys(type).length === 0) {
        info += ' You likely forgot to export your component from the file ' + "it's defined in, or you might have mixed up default and named imports.";
      }

      var sourceInfo = getSourceInfoErrorAddendum(source);

      if (sourceInfo) {
        info += sourceInfo;
      } else {
        info += getDeclarationErrorAddendum();
      }

      var typeString;

      if (type === null) {
        typeString = 'null';
      } else if (isArray(type)) {
        typeString = 'array';
      } else if (type !== undefined && type.$$typeof === REACT_ELEMENT_TYPE) {
        typeString = "<" + (getComponentNameFromType(type.type) || 'Unknown') + " />";
        info = ' Did you accidentally export a JSX literal instead of a component?';
      } else {
        typeString = typeof type;
      }

      error('React.jsx: type is invalid -- expected a string (for ' + 'built-in components) or a class/function (for composite ' + 'components) but got: %s.%s', typeString, info);
    }

    var element = jsxDEV(type, props, key, source, self); // The result can be nullish if a mock or a custom function is used.
    // TODO: Drop this when these are no longer allowed as the type argument.

    if (element == null) {
      return element;
    } // Skip key warning if the type isn't valid since our key validation logic
    // doesn't expect a non-string/function type and can throw confusing errors.
    // We don't want exception behavior to differ between dev and prod.
    // (Rendering will throw with a helpful message and as soon as the type is
    // fixed, the key warnings will appear.)


    if (validType) {
      var children = props.children;

      if (children !== undefined) {
        if (isStaticChildren) {
          if (isArray(children)) {
            for (var i = 0; i < children.length; i++) {
              validateChildKeys(children[i], type);
            }

            if (Object.freeze) {
              Object.freeze(children);
            }
          } else {
            error('React.jsx: Static children should always be an array. ' + 'You are likely explicitly calling React.jsxs or React.jsxDEV. ' + 'Use the Babel transform instead.');
          }
        } else {
          validateChildKeys(children, type);
        }
      }
    }

    if (type === REACT_FRAGMENT_TYPE) {
      validateFragmentProps(element);
    } else {
      validatePropTypes(element);
    }

    return element;
  }
} // These two functions exist to still get child warnings in dev
// even with the prod transform. This means that jsxDEV is purely
// opt-in behavior for better messages but that we won't stop
// giving you warnings if you use production apis.

function jsxWithValidationStatic(type, props, key) {
  {
    return jsxWithValidation(type, props, key, true);
  }
}
function jsxWithValidationDynamic(type, props, key) {
  {
    return jsxWithValidation(type, props, key, false);
  }
}

var jsx =  jsxWithValidationDynamic ; // we may want to special case jsxs internally to take advantage of static children.
// for now we can ship identical prod functions

var jsxs =  jsxWithValidationStatic ;

exports.Fragment = REACT_FRAGMENT_TYPE;
exports.jsx = jsx;
exports.jsxs = jsxs;
  })();
}


/***/ }),

/***/ 2463:
/*!************************************!*\
  !*** external "window.wp?.blocks" ***!
  \************************************/
/***/ ((module) => {

"use strict";
module.exports = window.wp?.blocks;

/***/ }),

/***/ 2465:
/*!****************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/descriptors.js ***!
  \****************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var fails = __webpack_require__(/*! ../internals/fails */ 6774);

// Detect IE8's incomplete defineProperty implementation
module.exports = !fails(function () {
  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
  return Object.defineProperty({}, 1, { get: function () { return 7; } })[1] !== 7;
});


/***/ }),

/***/ 2483:
/*!******************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/function-name.js ***!
  \******************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var DESCRIPTORS = __webpack_require__(/*! ../internals/descriptors */ 2465);
var hasOwn = __webpack_require__(/*! ../internals/has-own-property */ 9422);

var FunctionPrototype = Function.prototype;
// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
var getDescriptor = DESCRIPTORS && Object.getOwnPropertyDescriptor;

var EXISTS = hasOwn(FunctionPrototype, 'name');
// additional protection from minified / mangled / dropped function names
var PROPER = EXISTS && (function something() { /* empty */ }).name === 'something';
var CONFIGURABLE = EXISTS && (!DESCRIPTORS || (DESCRIPTORS && getDescriptor(FunctionPrototype, 'name').configurable));

module.exports = {
  EXISTS: EXISTS,
  PROPER: PROPER,
  CONFIGURABLE: CONFIGURABLE
};


/***/ }),

/***/ 2600:
/*!****************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/hidden-keys.js ***!
  \****************************************************************************************************************/
/***/ ((module) => {

"use strict";

module.exports = {};


/***/ }),

/***/ 2726:
/*!***************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/object-is-prototype-of.js ***!
  \***************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ 1109);

module.exports = uncurryThis({}.isPrototypeOf);


/***/ }),

/***/ 2734:
/*!***************************************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/iterator-helper-without-closing-on-early-error.js ***!
  \***************************************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var globalThis = __webpack_require__(/*! ../internals/global-this */ 7793);

// https://github.com/tc39/ecma262/pull/3467
module.exports = function (METHOD_NAME, ExpectedError) {
  var Iterator = globalThis.Iterator;
  var IteratorPrototype = Iterator && Iterator.prototype;
  var method = IteratorPrototype && IteratorPrototype[METHOD_NAME];

  var CLOSED = false;

  if (method) try {
    method.call({
      next: function () { return { done: true }; },
      'return': function () { CLOSED = true; }
    }, -1);
  } catch (error) {
    // https://bugs.webkit.org/show_bug.cgi?id=291195
    if (!(error instanceof ExpectedError)) CLOSED = false;
  }

  if (!CLOSED) return method;
};


/***/ }),

/***/ 2970:
/*!******************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/try-to-string.js ***!
  \******************************************************************************************************************/
/***/ ((module) => {

"use strict";

var $String = String;

module.exports = function (argument) {
  try {
    return $String(argument);
  } catch (error) {
    return 'Object';
  }
};


/***/ }),

/***/ 3192:
/*!************************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/object-get-own-property-symbols.js ***!
  \************************************************************************************************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";

// eslint-disable-next-line es/no-object-getownpropertysymbols -- safe
exports.f = Object.getOwnPropertySymbols;


/***/ }),

/***/ 3275:
/*!*************************************************!*\
  !*** ./js/src/gutenberg/blocks/info-panel.pcss ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
// extracted by mini-css-extract-plugin
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({"wrap":"info-panel__wrap__k_","button":"info-panel__button__Fg"});

/***/ }),

/***/ 3325:
/*!****************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/classof-raw.js ***!
  \****************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ 1109);

var toString = uncurryThis({}.toString);
var stringSlice = uncurryThis(''.slice);

module.exports = function (it) {
  return stringSlice(toString(it), 8, -1);
};


/***/ }),

/***/ 3463:
/*!*************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/object-keys-internal.js ***!
  \*************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ 1109);
var hasOwn = __webpack_require__(/*! ../internals/has-own-property */ 9422);
var toIndexedObject = __webpack_require__(/*! ../internals/to-indexed-object */ 6256);
var indexOf = (__webpack_require__(/*! ../internals/array-includes */ 9790).indexOf);
var hiddenKeys = __webpack_require__(/*! ../internals/hidden-keys */ 2600);

var push = uncurryThis([].push);

module.exports = function (object, names) {
  var O = toIndexedObject(object);
  var i = 0;
  var result = [];
  var key;
  for (key in O) !hasOwn(hiddenKeys, key) && hasOwn(O, key) && push(result, key);
  // Don't enum bug & hidden keys
  while (names.length > i) if (hasOwn(O, key = names[i++])) {
    ~indexOf(result, key) || push(result, key);
  }
  return result;
};


/***/ }),

/***/ 3490:
/*!*******************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/internal-state.js ***!
  \*******************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var NATIVE_WEAK_MAP = __webpack_require__(/*! ../internals/weak-map-basic-detection */ 9181);
var globalThis = __webpack_require__(/*! ../internals/global-this */ 7793);
var isObject = __webpack_require__(/*! ../internals/is-object */ 3763);
var createNonEnumerableProperty = __webpack_require__(/*! ../internals/create-non-enumerable-property */ 8684);
var hasOwn = __webpack_require__(/*! ../internals/has-own-property */ 9422);
var shared = __webpack_require__(/*! ../internals/shared-store */ 5714);
var sharedKey = __webpack_require__(/*! ../internals/shared-key */ 180);
var hiddenKeys = __webpack_require__(/*! ../internals/hidden-keys */ 2600);

var OBJECT_ALREADY_INITIALIZED = 'Object already initialized';
var TypeError = globalThis.TypeError;
var WeakMap = globalThis.WeakMap;
var set, get, has;

var enforce = function (it) {
  return has(it) ? get(it) : set(it, {});
};

var getterFor = function (TYPE) {
  return function (it) {
    var state;
    if (!isObject(it) || (state = get(it)).type !== TYPE) {
      throw new TypeError('Incompatible receiver, ' + TYPE + ' required');
    } return state;
  };
};

if (NATIVE_WEAK_MAP || shared.state) {
  var store = shared.state || (shared.state = new WeakMap());
  /* eslint-disable no-self-assign -- prototype methods protection */
  store.get = store.get;
  store.has = store.has;
  store.set = store.set;
  /* eslint-enable no-self-assign -- prototype methods protection */
  set = function (it, metadata) {
    if (store.has(it)) throw new TypeError(OBJECT_ALREADY_INITIALIZED);
    metadata.facade = it;
    store.set(it, metadata);
    return metadata;
  };
  get = function (it) {
    return store.get(it) || {};
  };
  has = function (it) {
    return store.has(it);
  };
} else {
  var STATE = sharedKey('state');
  hiddenKeys[STATE] = true;
  set = function (it, metadata) {
    if (hasOwn(it, STATE)) throw new TypeError(OBJECT_ALREADY_INITIALIZED);
    metadata.facade = it;
    createNonEnumerableProperty(it, STATE, metadata);
    return metadata;
  };
  get = function (it) {
    return hasOwn(it, STATE) ? it[STATE] : {};
  };
  has = function (it) {
    return hasOwn(it, STATE);
  };
}

module.exports = {
  set: set,
  get: get,
  has: has,
  enforce: enforce,
  getterFor: getterFor
};


/***/ }),

/***/ 3621:
/*!*************************************************!*\
  !*** ./js/src/gutenberg/blocks/pages/block.tsx ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   block: () => (/* binding */ block),
/* harmony export */   name: () => (/* binding */ name),
/* harmony export */   settings: () => (/* binding */ settings)
/* harmony export */ });
/* harmony import */ var _globals_config__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../globals/config */ 5328);
/* harmony import */ var _Edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Edit */ 8301);
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../helpers */ 9440);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ 4833);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _PagesIcon__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./PagesIcon */ 5790);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! react/jsx-runtime */ 7157);






/**
 * Attributes specific to the widget as well as shared
 * widget attributes.
 *
 * @see \Advanced_Sidebar_Menu\Blocks\Pages::get_attributes
 * @see \Advanced_Sidebar_Menu\Blocks\Common
 */

// Options used by basic when available from PRO.

/**
 * Attributes used for the example preview.
 * Combines some PRO and basic attributes.
 * The PRO attributes will only be sent if PRO is active.
 */
const EXAMPLE = {
  apply_current_page_parent_styles_to_parent: true,
  apply_current_page_styles_to_parent: true,
  block_style: true,
  border: true,
  border_color: '#333',
  bullet_style: 'none',
  child_page_bg_color: '#666',
  child_page_color: '#fff',
  current_page_bg_color: '#282828',
  current_page_color: '#0cc4c6',
  current_page_font_weight: 'normal',
  current_page_parent_bg_color: '#333',
  display_all: true,
  grandchild_page_bg_color: '#989898',
  grandchild_page_color: '#282828',
  grandchild_page_font_weight: 'bold',
  include_childless_parent: true,
  include_parent: true,
  levels: 2,
  parent_page_bg_color: '#666',
  parent_page_color: '#fff'
};
const block = _globals_config__WEBPACK_IMPORTED_MODULE_0__.CONFIG.blocks.pages;
const name = block.id;
const settings = {
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Advanced Sidebar - Pages', 'advanced-sidebar-menu'),
  icon: _PagesIcon__WEBPACK_IMPORTED_MODULE_4__["default"],
  category: 'widgets',
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Creates a menu of all the pages using the parent/child relationship', 'advanced-sidebar-menu'),
  example: {
    attributes: EXAMPLE
  },
  transforms: {
    from: [{
      type: 'block',
      blocks: ['core/legacy-widget'],
      isMatch: ({
        idBase,
        instance
      }) => {
        if (null === instance?.raw) {
          // Can't transform if the raw instance is not shown in REST API.
          return false;
        }
        return 'advanced_sidebar_menu' === idBase;
      },
      transform: (0,_helpers__WEBPACK_IMPORTED_MODULE_2__.transformLegacyWidget)(name)
    }]
  },
  attributes: (0,_helpers__WEBPACK_IMPORTED_MODULE_2__.translateBlockAttributes)(block.attributes),
  supports: (0,_helpers__WEBPACK_IMPORTED_MODULE_2__.getBlockSupports)(),
  edit: props => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_Edit__WEBPACK_IMPORTED_MODULE_1__["default"], {
    ...props
  }),
  save: () => null,
  apiVersion: 3
};

/***/ }),

/***/ 3724:
/*!*****************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/correct-prototype-getter.js ***!
  \*****************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var fails = __webpack_require__(/*! ../internals/fails */ 6774);

module.exports = !fails(function () {
  function F() { /* empty */ }
  F.prototype.constructor = null;
  // eslint-disable-next-line es/no-object-getprototypeof -- required for testing
  return Object.getPrototypeOf(new F()) !== F.prototype;
});


/***/ }),

/***/ 3763:
/*!**************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/is-object.js ***!
  \**************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var isCallable = __webpack_require__(/*! ../internals/is-callable */ 2120);

module.exports = function (it) {
  return typeof it == 'object' ? it !== null : isCallable(it);
};


/***/ }),

/***/ 3805:
/*!***********************************!*\
  !*** ./js/src/gutenberg/index.ts ***!
  \***********************************/
/***/ ((module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _lipemat_js_boilerplate_gutenberg__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @lipemat/js-boilerplate-gutenberg */ 8765);
/* harmony import */ var _blocks_Preview__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./blocks/Preview */ 1652);
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./helpers */ 9440);
/* harmony import */ var _components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../components/ErrorBoundary */ 5000);
/* harmony import */ var _blocks_NavigationIcon__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./blocks/NavigationIcon */ 1891);
/* module decorator */ module = __webpack_require__.hmd(module);






// @see content/plugins/advanced-sidebar-menu-pro/js/src/globals/config.ts

/**
 * Use our custom autoloader to automatically require,
 * register and add HMR support to the Gutenberg-related items.
 *
 * Will load from the specified directory recursively.
 */
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (() => {
  // Load all blocks
  (0,_lipemat_js_boilerplate_gutenberg__WEBPACK_IMPORTED_MODULE_0__.autoloadBlocks)(() => __webpack_require__(9406), module);

  // Expose helpers and Preview component to the window, so we can use them in PRO.
  if ('' === window.ADVANCED_SIDEBAR_MENU.isPro) {
    return;
  }
  window.ADVANCED_SIDEBAR_MENU.ErrorBoundary = _components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_3__["default"];
  window.ADVANCED_SIDEBAR_MENU.NavigationIcon = _blocks_NavigationIcon__WEBPACK_IMPORTED_MODULE_4__["default"];
  window.ADVANCED_SIDEBAR_MENU.Preview = _blocks_Preview__WEBPACK_IMPORTED_MODULE_1__["default"];
  window.ADVANCED_SIDEBAR_MENU.getBlockSupports = _helpers__WEBPACK_IMPORTED_MODULE_2__.getBlockSupports;
  window.ADVANCED_SIDEBAR_MENU.transformLegacyWidget = _helpers__WEBPACK_IMPORTED_MODULE_2__.transformLegacyWidget;
  window.ADVANCED_SIDEBAR_MENU.translateBlockAttributes = _helpers__WEBPACK_IMPORTED_MODULE_2__.translateBlockAttributes;

  // Translate common and preview attributes to old format for legacy PRO versions.
  // @todo Remove this when required PRO version is 9.9.0+.
  if ('1' !== window.ADVANCED_SIDEBAR_MENU.isProCommonAttr) {
    const blocks = window.ADVANCED_SIDEBAR_MENU.blocks;
    window.ADVANCED_SIDEBAR_MENU.blocks.categories.attributes = (0,_helpers__WEBPACK_IMPORTED_MODULE_2__.translateBlockAttributes)(blocks.categories.attributes);
    window.ADVANCED_SIDEBAR_MENU.blocks.categories.supports = (0,_helpers__WEBPACK_IMPORTED_MODULE_2__.getBlockSupports)();
    window.ADVANCED_SIDEBAR_MENU.blocks.pages.attributes = (0,_helpers__WEBPACK_IMPORTED_MODULE_2__.translateBlockAttributes)(blocks.pages.attributes);
    window.ADVANCED_SIDEBAR_MENU.blocks.pages.supports = (0,_helpers__WEBPACK_IMPORTED_MODULE_2__.getBlockSupports)();
    if (window.ADVANCED_SIDEBAR_MENU.blocks.navigation && blocks.navigation) {
      window.ADVANCED_SIDEBAR_MENU.blocks.navigation.attributes = (0,_helpers__WEBPACK_IMPORTED_MODULE_2__.translateBlockAttributes)(blocks.navigation.attributes);
      window.ADVANCED_SIDEBAR_MENU.blocks.navigation.supports = (0,_helpers__WEBPACK_IMPORTED_MODULE_2__.getBlockSupports)();
    }
  }
});

/***/ }),

/***/ 4015:
/*!***********************************!*\
  !*** external "window.wp?.hooks" ***!
  \***********************************/
/***/ ((module) => {

"use strict";
module.exports = window.wp?.hooks;

/***/ }),

/***/ 4028:
/*!***************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/environment-v8-version.js ***!
  \***************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var globalThis = __webpack_require__(/*! ../internals/global-this */ 7793);
var userAgent = __webpack_require__(/*! ../internals/environment-user-agent */ 5872);

var process = globalThis.process;
var Deno = globalThis.Deno;
var versions = process && process.versions || Deno && Deno.version;
var v8 = versions && versions.v8;
var match, version;

if (v8) {
  match = v8.split('.');
  // in old Chrome, versions of V8 isn't V8 = Chrome / 10
  // but their correct versions are not interesting for us
  version = match[0] > 0 && match[0] < 4 ? 1 : +(match[0] + match[1]);
}

// BrowserFS NodeJS `process` polyfill incorrectly set `.v8` to `0.0`
// so check `userAgent` even if `.v8` exists, but 0
if (!version && userAgent) {
  match = userAgent.match(/Edge\/(\d+)/);
  if (!match || match[1] >= 74) {
    match = userAgent.match(/Chrome\/(\d+)/);
    if (match) version = +match[1];
  }
}

module.exports = version;


/***/ }),

/***/ 4030:
/*!****************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/object-get-prototype-of.js ***!
  \****************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var hasOwn = __webpack_require__(/*! ../internals/has-own-property */ 9422);
var isCallable = __webpack_require__(/*! ../internals/is-callable */ 2120);
var toObject = __webpack_require__(/*! ../internals/to-object */ 8336);
var sharedKey = __webpack_require__(/*! ../internals/shared-key */ 180);
var CORRECT_PROTOTYPE_GETTER = __webpack_require__(/*! ../internals/correct-prototype-getter */ 3724);

var IE_PROTO = sharedKey('IE_PROTO');
var $Object = Object;
var ObjectPrototype = $Object.prototype;

// `Object.getPrototypeOf` method
// https://tc39.es/ecma262/#sec-object.getprototypeof
// eslint-disable-next-line es/no-object-getprototypeof -- safe
module.exports = CORRECT_PROTOTYPE_GETTER ? $Object.getPrototypeOf : function (O) {
  var object = toObject(O);
  if (hasOwn(object, IE_PROTO)) return object[IE_PROTO];
  var constructor = object.constructor;
  if (isCallable(constructor) && object instanceof constructor) {
    return constructor.prototype;
  } return object instanceof $Object ? ObjectPrototype : null;
};


/***/ }),

/***/ 4061:
/*!**********************************************!*\
  !*** external "window.wp?.serverSideRender" ***!
  \**********************************************/
/***/ ((module) => {

"use strict";
module.exports = window.wp?.serverSideRender;

/***/ }),

/***/ 4218:
/*!***********************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/iterator-close-all.js ***!
  \***********************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var iteratorClose = __webpack_require__(/*! ../internals/iterator-close */ 1952);

module.exports = function (iters, kind, value) {
  for (var i = iters.length - 1; i >= 0; i--) {
    if (iters[i] === undefined) continue;
    try {
      value = iteratorClose(iters[i].iterator, kind, value);
    } catch (error) {
      kind = 'throw';
      value = error;
    }
  }
  if (kind === 'throw') throw value;
  return value;
};


/***/ }),

/***/ 4235:
/*!*************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/function-bind-native.js ***!
  \*************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var fails = __webpack_require__(/*! ../internals/fails */ 6774);

module.exports = !fails(function () {
  // eslint-disable-next-line es/no-function-prototype-bind -- safe
  var test = (function () { /* empty */ }).bind();
  // eslint-disable-next-line no-prototype-builtins -- safe
  return typeof test != 'function' || test.hasOwnProperty('prototype');
});


/***/ }),

/***/ 4363:
/*!***********************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/modules/es.iterator.for-each.js ***!
  \***********************************************************************************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var $ = __webpack_require__(/*! ../internals/export */ 8321);
var call = __webpack_require__(/*! ../internals/function-call */ 5156);
var iterate = __webpack_require__(/*! ../internals/iterate */ 7005);
var aCallable = __webpack_require__(/*! ../internals/a-callable */ 4549);
var anObject = __webpack_require__(/*! ../internals/an-object */ 1734);
var getIteratorDirect = __webpack_require__(/*! ../internals/get-iterator-direct */ 6822);
var iteratorClose = __webpack_require__(/*! ../internals/iterator-close */ 1952);
var iteratorHelperWithoutClosingOnEarlyError = __webpack_require__(/*! ../internals/iterator-helper-without-closing-on-early-error */ 2734);

var forEachWithoutClosingOnEarlyError = iteratorHelperWithoutClosingOnEarlyError('forEach', TypeError);

// `Iterator.prototype.forEach` method
// https://tc39.es/ecma262/#sec-iterator.prototype.foreach
$({ target: 'Iterator', proto: true, real: true, forced: forEachWithoutClosingOnEarlyError }, {
  forEach: function forEach(fn) {
    anObject(this);
    try {
      aCallable(fn);
    } catch (error) {
      iteratorClose(this, 'throw', error);
    }

    if (forEachWithoutClosingOnEarlyError) return call(forEachWithoutClosingOnEarlyError, this, fn);

    var record = getIteratorDirect(this);
    var counter = 0;
    iterate(record, function (value) {
      fn(value, counter++);
    }, { IS_RECORD: true });
  }
});


/***/ }),

/***/ 4549:
/*!***************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/a-callable.js ***!
  \***************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var isCallable = __webpack_require__(/*! ../internals/is-callable */ 2120);
var tryToString = __webpack_require__(/*! ../internals/try-to-string */ 2970);

var $TypeError = TypeError;

// `Assert: IsCallable(argument) is true`
module.exports = function (argument) {
  if (isCallable(argument)) return argument;
  throw new $TypeError(tryToString(argument) + ' is not a function');
};


/***/ }),

/***/ 4666:
/*!*******************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/ie8-dom-define.js ***!
  \*******************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var DESCRIPTORS = __webpack_require__(/*! ../internals/descriptors */ 2465);
var fails = __webpack_require__(/*! ../internals/fails */ 6774);
var createElement = __webpack_require__(/*! ../internals/document-create-element */ 5682);

// Thanks to IE8 for its funny defineProperty
module.exports = !DESCRIPTORS && !fails(function () {
  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
  return Object.defineProperty(createElement('div'), 'a', {
    get: function () { return 7; }
  }).a !== 7;
});


/***/ }),

/***/ 4708:
/*!*******************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/indexed-object.js ***!
  \*******************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ 1109);
var fails = __webpack_require__(/*! ../internals/fails */ 6774);
var classof = __webpack_require__(/*! ../internals/classof-raw */ 3325);

var $Object = Object;
var split = uncurryThis(''.split);

// fallback for non-array-like ES3 and non-enumerable old V8 strings
module.exports = fails(function () {
  // throws an error in rhino, see https://github.com/mozilla/rhino/issues/346
  // eslint-disable-next-line no-prototype-builtins -- safe
  return !$Object('z').propertyIsEnumerable(0);
}) ? function (it) {
  return classof(it) === 'String' ? split(it, '') : $Object(it);
} : $Object;


/***/ }),

/***/ 4711:
/*!*************************************************!*\
  !*** ./js/src/components/transform-notice.pcss ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
// extracted by mini-css-extract-plugin
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({"wrap":"transform-notice__wrap__mq"});

/***/ }),

/***/ 4796:
/*!**************************************!*\
  !*** external "window.wp?.richText" ***!
  \**************************************/
/***/ ((module) => {

"use strict";
module.exports = window.wp?.richText;

/***/ }),

/***/ 4833:
/*!**********************************!*\
  !*** external "window.wp?.i18n" ***!
  \**********************************/
/***/ ((module) => {

"use strict";
module.exports = window.wp?.i18n;

/***/ }),

/***/ 4975:
/*!**************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/iterator-create-proxy.js ***!
  \**************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var call = __webpack_require__(/*! ../internals/function-call */ 5156);
var create = __webpack_require__(/*! ../internals/object-create */ 9365);
var createNonEnumerableProperty = __webpack_require__(/*! ../internals/create-non-enumerable-property */ 8684);
var defineBuiltIns = __webpack_require__(/*! ../internals/define-built-ins */ 564);
var wellKnownSymbol = __webpack_require__(/*! ../internals/well-known-symbol */ 1782);
var InternalStateModule = __webpack_require__(/*! ../internals/internal-state */ 3490);
var getMethod = __webpack_require__(/*! ../internals/get-method */ 789);
var IteratorPrototype = (__webpack_require__(/*! ../internals/iterators-core */ 6358).IteratorPrototype);
var createIterResultObject = __webpack_require__(/*! ../internals/create-iter-result-object */ 5676);
var iteratorClose = __webpack_require__(/*! ../internals/iterator-close */ 1952);
var iteratorCloseAll = __webpack_require__(/*! ../internals/iterator-close-all */ 4218);

var TO_STRING_TAG = wellKnownSymbol('toStringTag');
var ITERATOR_HELPER = 'IteratorHelper';
var WRAP_FOR_VALID_ITERATOR = 'WrapForValidIterator';
var NORMAL = 'normal';
var THROW = 'throw';
var setInternalState = InternalStateModule.set;

var createIteratorProxyPrototype = function (IS_ITERATOR) {
  var getInternalState = InternalStateModule.getterFor(IS_ITERATOR ? WRAP_FOR_VALID_ITERATOR : ITERATOR_HELPER);

  return defineBuiltIns(create(IteratorPrototype), {
    next: function next() {
      var state = getInternalState(this);
      // for simplification:
      //   for `%WrapForValidIteratorPrototype%.next` or with `state.returnHandlerResult` our `nextHandler` returns `IterResultObject`
      //   for `%IteratorHelperPrototype%.next` - just a value
      if (IS_ITERATOR) return state.nextHandler();
      if (state.done) return createIterResultObject(undefined, true);
      try {
        var result = state.nextHandler();
        return state.returnHandlerResult ? result : createIterResultObject(result, state.done);
      } catch (error) {
        state.done = true;
        throw error;
      }
    },
    'return': function () {
      var state = getInternalState(this);
      var iterator = state.iterator;
      state.done = true;
      if (IS_ITERATOR) {
        var returnMethod = getMethod(iterator, 'return');
        return returnMethod ? call(returnMethod, iterator) : createIterResultObject(undefined, true);
      }
      if (state.inner) try {
        iteratorClose(state.inner.iterator, NORMAL);
      } catch (error) {
        return iteratorClose(iterator, THROW, error);
      }
      if (state.openIters) try {
        iteratorCloseAll(state.openIters, NORMAL);
      } catch (error) {
        return iteratorClose(iterator, THROW, error);
      }
      if (iterator) iteratorClose(iterator, NORMAL);
      return createIterResultObject(undefined, true);
    }
  });
};

var WrapForValidIteratorPrototype = createIteratorProxyPrototype(true);
var IteratorHelperPrototype = createIteratorProxyPrototype(false);

createNonEnumerableProperty(IteratorHelperPrototype, TO_STRING_TAG, 'Iterator Helper');

module.exports = function (nextHandler, IS_ITERATOR, RETURN_HANDLER_RESULT) {
  var IteratorProxy = function Iterator(record, state) {
    if (state) {
      state.iterator = record.iterator;
      state.next = record.next;
    } else state = record;
    state.type = IS_ITERATOR ? WRAP_FOR_VALID_ITERATOR : ITERATOR_HELPER;
    state.returnHandlerResult = !!RETURN_HANDLER_RESULT;
    state.nextHandler = nextHandler;
    state.counter = 0;
    state.done = false;
    setInternalState(this, state);
  };

  IteratorProxy.prototype = IS_ITERATOR ? WrapForValidIteratorPrototype : IteratorHelperPrototype;

  return IteratorProxy;
};


/***/ }),

/***/ 5000:
/*!*********************************************!*\
  !*** ./js/src/components/ErrorBoundary.tsx ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ 1594);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _globals_config__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../globals/config */ 5328);
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/url */ 8094);
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_url__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var dompurify__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! dompurify */ 9133);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react/jsx-runtime */ 7157);





/**
 * Wrap any component in me, which may throw errors, and I will
 * prevent the entire UI from disappearing.
 *
 * Custom version special to Advanced Sidebar Menu with links to
 * support as well as debugging information.
 *
 * @link https://reactjs.org/docs/error-boundaries.html#introducing-error-boundaries
 */
class ErrorBoundary extends react__WEBPACK_IMPORTED_MODULE_0__.Component {
  constructor(props) {
    super(props);
    this.state = {
      hasError: false,
      error: null
    };
  }
  static getDerivedStateFromError() {
    // Update state, so the next render will show the fallback UI.
    return {
      hasError: true
    };
  }

  /**
   * Log information about the error when it happens.
   *
   * @notice Will log "Error: A cross-origin error was thrown. React doesn't have
   *         access to the actual error object in development" in React development
   *         mode but provides full error info in React production.
   */
  componentDidCatch(error, info) {
    console.warn('%cError caught by the Advanced Sidebar ErrorBoundary!', 'color:orange; font-size: large; font-weight: bold');
    console.warn(this.props);
    console.warn(error);
    console.warn(info);
    this.setState({
      error
    });
  }
  render() {
    if (this.state.hasError) {
      if (!_globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.siteInfo.scriptDebug) {
        return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
          className: 'components-panel__body is-opened',
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("h4", {
            style: {
              color: 'red'
            },
            children: "Something went wrong!"
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("p", {
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("button", {
              className: 'components-button is-link',
              onClick: e => {
                e.preventDefault();
                const location = (0,_wordpress_url__WEBPACK_IMPORTED_MODULE_2__.addQueryArgs)(dompurify__WEBPACK_IMPORTED_MODULE_3__["default"].sanitize(window.location.href), {
                  'script-debug': 'true'
                });
                window.open(location, '_blank', 'noopener,noreferrer');
              },
              children: "Please enable script debug"
            }), "to retrieve error information."]
          })]
        });
      }
      return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
        className: 'components-panel__body is-opened',
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("h4", {
          style: {
            color: 'red'
          },
          children: "Something went wrong!"
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("p", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("button", {
            className: 'components-button is-link',
            onClick: e => {
              e.preventDefault();
              window.open(dompurify__WEBPACK_IMPORTED_MODULE_3__["default"].sanitize(_globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.support), '_blank', 'noopener,noreferrer');
            },
            children: "Please create a support request"
          }), "\xA0with the following information:"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("ol", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("li", {
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("button", {
              className: 'components-button is-link',
              onClick: e => {
                e.preventDefault();
                window.open('https://onpointplugins.com/how-to-retrieve-console-logs-from-your-browser/', '_blank', 'noopener,noreferrer');
              },
              children: "The logs from your browser console."
            })
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("li", {
            children: "The following information."
          })]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
          style: {
            border: '2px groove',
            padding: '10px',
            width: '100%',
            overflowWrap: 'break-word'
          },
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("p", {
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("strong", {
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("em", {
                children: "Section"
              })
            }), " ", /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("br", {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("code", {
              children: this.props.section
            })]
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("p", {
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("strong", {
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("em", {
                children: "Screen"
              })
            }), " ", /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("br", {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("code", {
              children: _globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.currentScreen
            })]
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("p", {
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("strong", {
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("em", {
                children: "Message"
              })
            }), " ", /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("br", {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("code", {
              children: this.state.error?.message
            })]
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("p", {
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("strong", {
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("em", {
                children: "Block"
              })
            }), " ", /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("br", {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("code", {
              children: this.props.block
            })]
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("p", {
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("strong", {
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("em", {
                children: "Attributes"
              })
            }), " ", /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("br", {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("code", {
              children: JSON.stringify(this.props.attributes)
            })]
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("p", {
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("strong", {
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("em", {
                children: "Site Info"
              })
            }), " ", /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("br", {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("code", {
              children: JSON.stringify(_globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.siteInfo)
            })]
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("p", {
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("strong", {
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("em", {
                children: "Stack"
              })
            }), " ", /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("br", {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("code", {
              children: this.state.error?.stack
            })]
          })]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("p", {
          children: "\xA0"
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("p", {
          children: "\xA0"
        })]
      });
    }
    return this.props.children;
  }
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ErrorBoundary);

/***/ }),

/***/ 5031:
/*!**********************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/to-absolute-index.js ***!
  \**********************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var toIntegerOrInfinity = __webpack_require__(/*! ../internals/to-integer-or-infinity */ 156);

var max = Math.max;
var min = Math.min;

// Helper for a popular repeating case of the spec:
// Let integer be ? ToInteger(index).
// If integer < 0, let result be max((length + integer), 0); else let result be min(integer, length).
module.exports = function (index, length) {
  var integer = toIntegerOrInfinity(index);
  return integer < 0 ? max(integer + length, 0) : min(integer, length);
};


/***/ }),

/***/ 5098:
/*!***************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/define-global-property.js ***!
  \***************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var globalThis = __webpack_require__(/*! ../internals/global-this */ 7793);

// eslint-disable-next-line es/no-object-defineproperty -- safe
var defineProperty = Object.defineProperty;

module.exports = function (key, value) {
  try {
    defineProperty(globalThis, key, { value: value, configurable: true, writable: true });
  } catch (error) {
    globalThis[key] = value;
  } return value;
};


/***/ }),

/***/ 5146:
/*!**************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/modules/es.iterator.constructor.js ***!
  \**************************************************************************************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var $ = __webpack_require__(/*! ../internals/export */ 8321);
var globalThis = __webpack_require__(/*! ../internals/global-this */ 7793);
var anInstance = __webpack_require__(/*! ../internals/an-instance */ 6002);
var anObject = __webpack_require__(/*! ../internals/an-object */ 1734);
var isCallable = __webpack_require__(/*! ../internals/is-callable */ 2120);
var getPrototypeOf = __webpack_require__(/*! ../internals/object-get-prototype-of */ 4030);
var defineBuiltInAccessor = __webpack_require__(/*! ../internals/define-built-in-accessor */ 833);
var createProperty = __webpack_require__(/*! ../internals/create-property */ 7901);
var fails = __webpack_require__(/*! ../internals/fails */ 6774);
var hasOwn = __webpack_require__(/*! ../internals/has-own-property */ 9422);
var wellKnownSymbol = __webpack_require__(/*! ../internals/well-known-symbol */ 1782);
var IteratorPrototype = (__webpack_require__(/*! ../internals/iterators-core */ 6358).IteratorPrototype);
var DESCRIPTORS = __webpack_require__(/*! ../internals/descriptors */ 2465);
var IS_PURE = __webpack_require__(/*! ../internals/is-pure */ 5490);

var CONSTRUCTOR = 'constructor';
var ITERATOR = 'Iterator';
var TO_STRING_TAG = wellKnownSymbol('toStringTag');

var $TypeError = TypeError;
var NativeIterator = globalThis[ITERATOR];

// FF56- have non-standard global helper `Iterator`
var FORCED = IS_PURE
  || !isCallable(NativeIterator)
  || NativeIterator.prototype !== IteratorPrototype
  // FF44- non-standard `Iterator` passes previous tests
  || !fails(function () { NativeIterator({}); });

var IteratorConstructor = function Iterator() {
  anInstance(this, IteratorPrototype);
  if (getPrototypeOf(this) === IteratorPrototype) throw new $TypeError('Abstract class Iterator not directly constructable');
};

var defineIteratorPrototypeAccessor = function (key, value) {
  if (DESCRIPTORS) {
    defineBuiltInAccessor(IteratorPrototype, key, {
      configurable: true,
      get: function () {
        return value;
      },
      set: function (replacement) {
        anObject(this);
        if (this === IteratorPrototype) throw new $TypeError("You can't redefine this property");
        if (hasOwn(this, key)) this[key] = replacement;
        else createProperty(this, key, replacement);
      }
    });
  } else IteratorPrototype[key] = value;
};

if (!hasOwn(IteratorPrototype, TO_STRING_TAG)) defineIteratorPrototypeAccessor(TO_STRING_TAG, ITERATOR);

if (FORCED || !hasOwn(IteratorPrototype, CONSTRUCTOR) || IteratorPrototype[CONSTRUCTOR] === Object) {
  defineIteratorPrototypeAccessor(CONSTRUCTOR, IteratorConstructor);
}

IteratorConstructor.prototype = IteratorPrototype;

// `Iterator` constructor
// https://tc39.es/ecma262/#sec-iterator
$({ global: true, constructor: true, forced: FORCED }, {
  Iterator: IteratorConstructor
});


/***/ }),

/***/ 5156:
/*!******************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/function-call.js ***!
  \******************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var NATIVE_BIND = __webpack_require__(/*! ../internals/function-bind-native */ 4235);

var call = Function.prototype.call;
// eslint-disable-next-line es/no-function-prototype-bind -- safe
module.exports = NATIVE_BIND ? call.bind(call) : function () {
  return call.apply(call, arguments);
};


/***/ }),

/***/ 5206:
/*!***************************!*\
  !*** external "ReactDOM" ***!
  \***************************/
/***/ ((module) => {

"use strict";
module.exports = ReactDOM;

/***/ }),

/***/ 5328:
/*!**********************************!*\
  !*** ./js/src/globals/config.ts ***!
  \**********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CONFIG: () => (/* binding */ CONFIG)
/* harmony export */ });
const CONFIG = window.ADVANCED_SIDEBAR_MENU;

/***/ }),

/***/ 5402:
/*!***************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/math-trunc.js ***!
  \***************************************************************************************************************/
/***/ ((module) => {

"use strict";

var ceil = Math.ceil;
var floor = Math.floor;

// `Math.trunc` method
// https://tc39.es/ecma262/#sec-math.trunc
// eslint-disable-next-line es/no-math-trunc -- safe
module.exports = Math.trunc || function trunc(x) {
  var n = +x;
  return (n > 0 ? floor : ceil)(n);
};


/***/ }),

/***/ 5417:
/*!*******************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/inspect-source.js ***!
  \*******************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ 1109);
var isCallable = __webpack_require__(/*! ../internals/is-callable */ 2120);
var store = __webpack_require__(/*! ../internals/shared-store */ 5714);

var functionToString = uncurryThis(Function.toString);

// this helper broken in `core-js@3.4.1-3.4.4`, so we can't use `shared` helper
if (!isCallable(store.inspectSource)) {
  store.inspectSource = function (it) {
    return functionToString(it);
  };
}

module.exports = store.inspectSource;


/***/ }),

/***/ 5429:
/*!**************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/is-forced.js ***!
  \**************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var fails = __webpack_require__(/*! ../internals/fails */ 6774);
var isCallable = __webpack_require__(/*! ../internals/is-callable */ 2120);

var replacement = /#|\.prototype\./;

var isForced = function (feature, detection) {
  var value = data[normalize(feature)];
  return value === POLYFILL ? true
    : value === NATIVE ? false
    : isCallable(detection) ? fails(detection)
    : !!detection;
};

var normalize = isForced.normalize = function (string) {
  return String(string).replace(replacement, '.').toLowerCase();
};

var data = isForced.data = {};
var NATIVE = isForced.NATIVE = 'N';
var POLYFILL = isForced.POLYFILL = 'P';

module.exports = isForced;


/***/ }),

/***/ 5490:
/*!************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/is-pure.js ***!
  \************************************************************************************************************/
/***/ ((module) => {

"use strict";

module.exports = false;


/***/ }),

/***/ 5515:
/*!**************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/ordinary-to-primitive.js ***!
  \**************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var call = __webpack_require__(/*! ../internals/function-call */ 5156);
var isCallable = __webpack_require__(/*! ../internals/is-callable */ 2120);
var isObject = __webpack_require__(/*! ../internals/is-object */ 3763);

var $TypeError = TypeError;

// `OrdinaryToPrimitive` abstract operation
// https://tc39.es/ecma262/#sec-ordinarytoprimitive
module.exports = function (input, pref) {
  var fn, val;
  if (pref === 'string' && isCallable(fn = input.toString) && !isObject(val = call(fn, input))) return val;
  if (isCallable(fn = input.valueOf) && !isObject(val = call(fn, input))) return val;
  if (pref !== 'string' && isCallable(fn = input.toString) && !isObject(val = call(fn, input))) return val;
  throw new $TypeError("Can't convert object to primitive value");
};


/***/ }),

/***/ 5558:
/*!*********************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/html.js ***!
  \*********************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var getBuiltIn = __webpack_require__(/*! ../internals/get-built-in */ 248);

module.exports = getBuiltIn('document', 'documentElement');


/***/ }),

/***/ 5676:
/*!******************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/create-iter-result-object.js ***!
  \******************************************************************************************************************************/
/***/ ((module) => {

"use strict";

// `CreateIterResultObject` abstract operation
// https://tc39.es/ecma262/#sec-createiterresultobject
module.exports = function (value, done) {
  return { value: value, done: done };
};


/***/ }),

/***/ 5682:
/*!****************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/document-create-element.js ***!
  \****************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var globalThis = __webpack_require__(/*! ../internals/global-this */ 7793);
var isObject = __webpack_require__(/*! ../internals/is-object */ 3763);

var document = globalThis.document;
// typeof document.createElement is 'object' in old IE
var EXISTS = isObject(document) && isObject(document.createElement);

module.exports = function (it) {
  return EXISTS ? document.createElement(it) : {};
};


/***/ }),

/***/ 5696:
/*!**************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/iterators.js ***!
  \**************************************************************************************************************/
/***/ ((module) => {

"use strict";

module.exports = {};


/***/ }),

/***/ 5705:
/*!**********************************!*\
  !*** external "window.wp?.data" ***!
  \**********************************/
/***/ ((module) => {

"use strict";
module.exports = window.wp?.data;

/***/ }),

/***/ 5714:
/*!*****************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/shared-store.js ***!
  \*****************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var IS_PURE = __webpack_require__(/*! ../internals/is-pure */ 5490);
var globalThis = __webpack_require__(/*! ../internals/global-this */ 7793);
var defineGlobalProperty = __webpack_require__(/*! ../internals/define-global-property */ 5098);

var SHARED = '__core-js_shared__';
var store = module.exports = globalThis[SHARED] || defineGlobalProperty(SHARED, {});

(store.versions || (store.versions = [])).push({
  version: '3.45.0',
  mode: IS_PURE ? 'pure' : 'global',
  copyright: '© 2014-2025 Denis Pushkarev (zloirock.ru)',
  license: 'https://github.com/zloirock/core-js/blob/v3.45.0/LICENSE',
  source: 'https://github.com/zloirock/core-js'
});


/***/ }),

/***/ 5790:
/*!*****************************************************!*\
  !*** ./js/src/gutenberg/blocks/pages/PagesIcon.tsx ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react/jsx-runtime */ 7157);

const PagesIcon = /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsxs)("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 20 20",
  children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("rect", {
    x: "0",
    fill: "none",
    width: "20",
    height: "20"
  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("g", {
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("path", {
      fill: "#044567",
      d: "M19 17V2c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v15c0 .55.45 1 1 1h15c.55 0 1-.45 1-1z M4 3h13v4H4V3z M5 4h3v2H5V4zm4 0h3v2H9V4zm4 0h3v2H13V4z M4.5 9 c .28 0 .5 .22 .5 .5s-.22.5-.5.5s-.5-.22-.5-.5s.22-.5 .5-.5z M6 9h4v1H6v-1z M4.5 12 c .28 0 .5 .22 .5 .5s-.22.5-.5.5s-.5-.22-.5-.5s.22-.5 .5-.5z M6 12h4v1H6v-1z M4.5 15 c .28 0 .5 .22 .5 .5s-.22.5-.5.5s-.5-.22-.5-.5s.22-.5 .5-.5z M6 15h4v1H6v-1z M12 9 H15.5 L17 10.5 V15.5 H12 V9 Z M15.5 9 V10.5 H17 M12.5 12 H16.5 M12.5 13.5 H16.5"
    })
  })]
});
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (PagesIcon);

/***/ }),

/***/ 5812:
/*!***************************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/object-get-own-property-descriptor.js ***!
  \***************************************************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";

var DESCRIPTORS = __webpack_require__(/*! ../internals/descriptors */ 2465);
var call = __webpack_require__(/*! ../internals/function-call */ 5156);
var propertyIsEnumerableModule = __webpack_require__(/*! ../internals/object-property-is-enumerable */ 7300);
var createPropertyDescriptor = __webpack_require__(/*! ../internals/create-property-descriptor */ 9919);
var toIndexedObject = __webpack_require__(/*! ../internals/to-indexed-object */ 6256);
var toPropertyKey = __webpack_require__(/*! ../internals/to-property-key */ 1196);
var hasOwn = __webpack_require__(/*! ../internals/has-own-property */ 9422);
var IE8_DOM_DEFINE = __webpack_require__(/*! ../internals/ie8-dom-define */ 4666);

// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
var $getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;

// `Object.getOwnPropertyDescriptor` method
// https://tc39.es/ecma262/#sec-object.getownpropertydescriptor
exports.f = DESCRIPTORS ? $getOwnPropertyDescriptor : function getOwnPropertyDescriptor(O, P) {
  O = toIndexedObject(O);
  P = toPropertyKey(P);
  if (IE8_DOM_DEFINE) try {
    return $getOwnPropertyDescriptor(O, P);
  } catch (error) { /* empty */ }
  if (hasOwn(O, P)) return createPropertyDescriptor(!call(propertyIsEnumerableModule.f, O, P), O[P]);
};


/***/ }),

/***/ 5872:
/*!***************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/environment-user-agent.js ***!
  \***************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var globalThis = __webpack_require__(/*! ../internals/global-this */ 7793);

var navigator = globalThis.navigator;
var userAgent = navigator && navigator.userAgent;

module.exports = userAgent ? String(userAgent) : '';


/***/ }),

/***/ 5989:
/*!**********************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/use-symbol-as-uid.js ***!
  \**********************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

/* eslint-disable es/no-symbol -- required for testing */
var NATIVE_SYMBOL = __webpack_require__(/*! ../internals/symbol-constructor-detection */ 9148);

module.exports = NATIVE_SYMBOL &&
  !Symbol.sham &&
  typeof Symbol.iterator == 'symbol';


/***/ }),

/***/ 6002:
/*!****************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/an-instance.js ***!
  \****************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var isPrototypeOf = __webpack_require__(/*! ../internals/object-is-prototype-of */ 2726);

var $TypeError = TypeError;

module.exports = function (it, Prototype) {
  if (isPrototypeOf(Prototype, it)) return it;
  throw new $TypeError('Incorrect invocation');
};


/***/ }),

/***/ 6021:
/*!****************************************!*\
  !*** external "window.wp?.components" ***!
  \****************************************/
/***/ ((module) => {

"use strict";
module.exports = window.wp?.components;

/***/ }),

/***/ 6074:
/*!**********************************************!*\
  !*** ./js/src/gutenberg/blocks/preview.pcss ***!
  \**********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
// extracted by mini-css-extract-plugin
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({"wrap":"preview__wrap__BD","placeholder":"preview__placeholder__HI","error":"preview__error__Nl","spin-wrap":"preview__spin-wrap__o0","spinWrap":"preview__spin-wrap__o0","spin":"preview__spin__Oj","spin-content":"preview__spin-content__uC","spinContent":"preview__spin-content__uC"});

/***/ }),

/***/ 6256:
/*!**********************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/to-indexed-object.js ***!
  \**********************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

// toObject with fallback for non-array-like ES3 strings
var IndexedObject = __webpack_require__(/*! ../internals/indexed-object */ 4708);
var requireObjectCoercible = __webpack_require__(/*! ../internals/require-object-coercible */ 1825);

module.exports = function (it) {
  return IndexedObject(requireObjectCoercible(it));
};


/***/ }),

/***/ 6268:
/*!***************************************************************!*\
  !*** ./js/src/gutenberg/blocks/categories/CategoriesIcon.tsx ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react/jsx-runtime */ 7157);

const CategoriesIcon = /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsxs)("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 20 20",
  children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("rect", {
    x: "0",
    fill: "none",
    width: "20",
    height: "20"
  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("g", {
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)("path", {
      fill: "#044567",
      d: "M19 17V2c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v15c0 .55.45 1 1 1h15c.55 0 1-.45 1-1z M4 3h13v4H4V3z M5 4h3v2H5V4zm4 0h3v2H9V4zm4 0h3v2H13V4z M4.5 9 c .28 0 .5 .22 .5 .5s-.22.5-.5.5s-.5-.22-.5-.5s.22-.5 .5-.5z M6 9h4v1H6v-1z M4.5 12 c .28 0 .5 .22 .5 .5s-.22.5-.5.5s-.5-.22-.5-.5s.22-.5 .5-.5z M6 12h4v1H6v-1z M4.5 15 c .28 0 .5 .22 .5 .5s-.22.5-.5.5s-.5-.22-.5-.5s.22-.5 .5-.5z M6 15h4v1H6v-1z M12 10 H15.5 L17 12.5 L15.5 15 H12 V10 Z M13 12.5 m -0.6 0 a 0.6 0.6 0 1 0 1.2 0 a 0.6 0.6 0 1 0 -1.2 0"
    })
  })]
});
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (CategoriesIcon);

/***/ }),

/***/ 6288:
/*!******************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/modules/es.iterator.map.js ***!
  \******************************************************************************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var $ = __webpack_require__(/*! ../internals/export */ 8321);
var call = __webpack_require__(/*! ../internals/function-call */ 5156);
var aCallable = __webpack_require__(/*! ../internals/a-callable */ 4549);
var anObject = __webpack_require__(/*! ../internals/an-object */ 1734);
var getIteratorDirect = __webpack_require__(/*! ../internals/get-iterator-direct */ 6822);
var createIteratorProxy = __webpack_require__(/*! ../internals/iterator-create-proxy */ 4975);
var callWithSafeIterationClosing = __webpack_require__(/*! ../internals/call-with-safe-iteration-closing */ 6336);
var iteratorClose = __webpack_require__(/*! ../internals/iterator-close */ 1952);
var iteratorHelperThrowsOnInvalidIterator = __webpack_require__(/*! ../internals/iterator-helper-throws-on-invalid-iterator */ 279);
var iteratorHelperWithoutClosingOnEarlyError = __webpack_require__(/*! ../internals/iterator-helper-without-closing-on-early-error */ 2734);
var IS_PURE = __webpack_require__(/*! ../internals/is-pure */ 5490);

var MAP_WITHOUT_THROWING_ON_INVALID_ITERATOR = !IS_PURE && !iteratorHelperThrowsOnInvalidIterator('map', function () { /* empty */ });
var mapWithoutClosingOnEarlyError = !IS_PURE && !MAP_WITHOUT_THROWING_ON_INVALID_ITERATOR
  && iteratorHelperWithoutClosingOnEarlyError('map', TypeError);

var FORCED = IS_PURE || MAP_WITHOUT_THROWING_ON_INVALID_ITERATOR || mapWithoutClosingOnEarlyError;

var IteratorProxy = createIteratorProxy(function () {
  var iterator = this.iterator;
  var result = anObject(call(this.next, iterator));
  var done = this.done = !!result.done;
  if (!done) return callWithSafeIterationClosing(iterator, this.mapper, [result.value, this.counter++], true);
});

// `Iterator.prototype.map` method
// https://tc39.es/ecma262/#sec-iterator.prototype.map
$({ target: 'Iterator', proto: true, real: true, forced: FORCED }, {
  map: function map(mapper) {
    anObject(this);
    try {
      aCallable(mapper);
    } catch (error) {
      iteratorClose(this, 'throw', error);
    }

    if (mapWithoutClosingOnEarlyError) return call(mapWithoutClosingOnEarlyError, this, mapper);

    return new IteratorProxy(getIteratorDirect(this), {
      mapper: mapper
    });
  }
});


/***/ }),

/***/ 6336:
/*!*************************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/call-with-safe-iteration-closing.js ***!
  \*************************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var anObject = __webpack_require__(/*! ../internals/an-object */ 1734);
var iteratorClose = __webpack_require__(/*! ../internals/iterator-close */ 1952);

// call something on iterator step with safe closing on error
module.exports = function (iterator, fn, value, ENTRIES) {
  try {
    return ENTRIES ? fn(anObject(value)[0], value[1]) : fn(value);
  } catch (error) {
    iteratorClose(iterator, 'throw', error);
  }
};


/***/ }),

/***/ 6358:
/*!*******************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/iterators-core.js ***!
  \*******************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var fails = __webpack_require__(/*! ../internals/fails */ 6774);
var isCallable = __webpack_require__(/*! ../internals/is-callable */ 2120);
var isObject = __webpack_require__(/*! ../internals/is-object */ 3763);
var create = __webpack_require__(/*! ../internals/object-create */ 9365);
var getPrototypeOf = __webpack_require__(/*! ../internals/object-get-prototype-of */ 4030);
var defineBuiltIn = __webpack_require__(/*! ../internals/define-built-in */ 8705);
var wellKnownSymbol = __webpack_require__(/*! ../internals/well-known-symbol */ 1782);
var IS_PURE = __webpack_require__(/*! ../internals/is-pure */ 5490);

var ITERATOR = wellKnownSymbol('iterator');
var BUGGY_SAFARI_ITERATORS = false;

// `%IteratorPrototype%` object
// https://tc39.es/ecma262/#sec-%iteratorprototype%-object
var IteratorPrototype, PrototypeOfArrayIteratorPrototype, arrayIterator;

/* eslint-disable es/no-array-prototype-keys -- safe */
if ([].keys) {
  arrayIterator = [].keys();
  // Safari 8 has buggy iterators w/o `next`
  if (!('next' in arrayIterator)) BUGGY_SAFARI_ITERATORS = true;
  else {
    PrototypeOfArrayIteratorPrototype = getPrototypeOf(getPrototypeOf(arrayIterator));
    if (PrototypeOfArrayIteratorPrototype !== Object.prototype) IteratorPrototype = PrototypeOfArrayIteratorPrototype;
  }
}

var NEW_ITERATOR_PROTOTYPE = !isObject(IteratorPrototype) || fails(function () {
  var test = {};
  // FF44- legacy iterators case
  return IteratorPrototype[ITERATOR].call(test) !== test;
});

if (NEW_ITERATOR_PROTOTYPE) IteratorPrototype = {};
else if (IS_PURE) IteratorPrototype = create(IteratorPrototype);

// `%IteratorPrototype%[@@iterator]()` method
// https://tc39.es/ecma262/#sec-%iteratorprototype%-@@iterator
if (!isCallable(IteratorPrototype[ITERATOR])) {
  defineBuiltIn(IteratorPrototype, ITERATOR, function () {
    return this;
  });
}

module.exports = {
  IteratorPrototype: IteratorPrototype,
  BUGGY_SAFARI_ITERATORS: BUGGY_SAFARI_ITERATORS
};


/***/ }),

/***/ 6383:
/*!*****************************************************!*\
  !*** ./js/src/gutenberg/blocks/categories/Edit.tsx ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var core_js_modules_es_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.iterator.constructor.js */ 5146);
/* harmony import */ var core_js_modules_es_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_iterator_map_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.iterator.map.js */ 6288);
/* harmony import */ var core_js_modules_es_iterator_map_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_iterator_map_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/data */ 5705);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _globals_config__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../globals/config */ 5328);
/* harmony import */ var dompurify__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! dompurify */ 9133);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/block-editor */ 6423);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _Preview__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../Preview */ 1652);
/* harmony import */ var _block__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./block */ 8792);
/* harmony import */ var _components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../../components/ErrorBoundary */ 5000);
/* harmony import */ var _Display__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../Display */ 7934);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @wordpress/components */ 6021);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! @wordpress/i18n */ 4833);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var _InfoPanel__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../InfoPanel */ 1474);
/* harmony import */ var _SideLoad__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ../../SideLoad */ 9278);
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ../../helpers */ 9440);
/* harmony import */ var _ExcludeField__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ../ExcludeField */ 6672);
/* harmony import */ var _pages_edit_pcss__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ../pages/edit.pcss */ 2013);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! react/jsx-runtime */ 7157);


















const Edit = ({
  attributes,
  setAttributes,
  clientId,
  name
}) => {
  const taxonomy = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.useSelect)(select => {
    const type = select('core').getTaxonomy(attributes.taxonomy ?? 'category');
    return type ?? select('core').getTaxonomy('category');
  }, [attributes.taxonomy]);

  // We have a version conflict or license error.
  if ('' !== _globals_config__WEBPACK_IMPORTED_MODULE_3__.CONFIG.error) {
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.Fragment, {
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__.InspectorControls, {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)("div", {
          className: _pages_edit_pcss__WEBPACK_IMPORTED_MODULE_16__["default"].error,
          dangerouslySetInnerHTML: {
            __html: dompurify__WEBPACK_IMPORTED_MODULE_4__["default"].sanitize(_globals_config__WEBPACK_IMPORTED_MODULE_3__.CONFIG.error)
          }
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_Preview__WEBPACK_IMPORTED_MODULE_6__["default"], {
        attributes: attributes,
        block: _block__WEBPACK_IMPORTED_MODULE_7__.block.id,
        clientId: clientId
      })]
    });
  }
  const fillProps = {
    type: taxonomy,
    attributes,
    name,
    setAttributes,
    clientId
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.Fragment, {
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__.InspectorControls, {
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)("div", {
        className: 'components-panel__body is-opened',
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)("a", {
          href: _globals_config__WEBPACK_IMPORTED_MODULE_3__.CONFIG.docs.category,
          target: "_blank",
          rel: "noopener noreferrer",
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__.__)('Documentation', 'advanced-sidebar-menu')
        })
      })
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsxs)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__.InspectorControls, {
      children: [(0,_helpers__WEBPACK_IMPORTED_MODULE_14__.isScreen)(['widgets', 'site-editor', 'customize']) && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__.PanelBody, {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__.TextControl, {
          value: attributes.title ?? '',
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__.__)('Title', 'advanced-sidebar-menu'),
          onChange: title => setAttributes({
            title
          })
          // @ts-expect-error -- Not technically supported until WP 6.7
          ,
          __nextHasNoMarginBottom: true
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsxs)(_components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_8__["default"], {
        attributes: attributes,
        block: name,
        section: 'categories/Edit/general',
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsxs)(_Display__WEBPACK_IMPORTED_MODULE_9__["default"], {
          attributes: attributes,
          clientId: clientId,
          name: name,
          setAttributes: setAttributes,
          type: taxonomy,
          children: [(0,_helpers__WEBPACK_IMPORTED_MODULE_14__.isScreen)(['post']) && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__.CheckboxControl
          /* translators: Selected taxonomy plural label */, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__.sprintf)((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__.__)('Display %s on single posts', 'advanced-sidebar-menu'), taxonomy?.labels?.name.toLowerCase() ?? ''),
            checked: attributes.single,
            onChange: value => {
              setAttributes({
                single: value
              });
            }
            // @ts-expect-error -- Not technically available until WP 6.7.
            ,
            __nextHasNoMarginBottom: true
          }), (0,_helpers__WEBPACK_IMPORTED_MODULE_14__.isScreen)(['widgets', 'customize']) && attributes.single && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__.SelectControl, {
            /* translators: Selected taxonomy single label */
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__.sprintf)((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_11__.__)('Display each single post\'s %s', 'advanced-sidebar-menu'), taxonomy?.labels?.name.toLowerCase() ?? ''),
            value: attributes.new_widget,
            options: Object.keys(_globals_config__WEBPACK_IMPORTED_MODULE_3__.CONFIG.categories.displayEach).map(key => ({
              value: key,
              label: _globals_config__WEBPACK_IMPORTED_MODULE_3__.CONFIG.categories.displayEach[key]
            }))
            /* eslint-disable-next-line camelcase */,
            onChange: new_widget => setAttributes({
              new_widget
            })
          })]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsxs)("div", {
          className: 'components-panel__body is-opened',
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__.Slot, {
            name: "advanced-sidebar-menu/categories/general",
            fillProps: fillProps
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_ExcludeField__WEBPACK_IMPORTED_MODULE_15__["default"], {
            type: taxonomy,
            attributes: attributes,
            setAttributes: setAttributes
          })]
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_8__["default"], {
        attributes: attributes,
        block: name,
        section: 'categories/Edit/inspector',
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__.Slot, {
          name: "advanced-sidebar-menu/categories/inspector",
          fillProps: fillProps
        })
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__.BlockControls, {
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_8__["default"], {
        attributes: attributes,
        block: name,
        section: 'categories/Edit/block-controls',
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_10__.Slot, {
          name: "advanced-sidebar-menu/categories/block-controls",
          fillProps: fillProps
        })
      })
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_InfoPanel__WEBPACK_IMPORTED_MODULE_12__["default"], {
      clientId: clientId
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_8__["default"], {
      attributes: attributes,
      block: name,
      section: 'categories/Edit/preview',
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_Preview__WEBPACK_IMPORTED_MODULE_6__["default"], {
        attributes: attributes,
        block: _block__WEBPACK_IMPORTED_MODULE_7__.block.id,
        clientId: clientId
      })
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_SideLoad__WEBPACK_IMPORTED_MODULE_13__["default"], {
      clientId: clientId
    })]
  });
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Edit);

/***/ }),

/***/ 6423:
/*!*****************************************!*\
  !*** external "window.wp?.blockEditor" ***!
  \*****************************************/
/***/ ((module) => {

"use strict";
module.exports = window.wp?.blockEditor;

/***/ }),

/***/ 6589:
/*!********************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/copy-constructor-properties.js ***!
  \********************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var hasOwn = __webpack_require__(/*! ../internals/has-own-property */ 9422);
var ownKeys = __webpack_require__(/*! ../internals/own-keys */ 8836);
var getOwnPropertyDescriptorModule = __webpack_require__(/*! ../internals/object-get-own-property-descriptor */ 5812);
var definePropertyModule = __webpack_require__(/*! ../internals/object-define-property */ 742);

module.exports = function (target, source, exceptions) {
  var keys = ownKeys(source);
  var defineProperty = definePropertyModule.f;
  var getOwnPropertyDescriptor = getOwnPropertyDescriptorModule.f;
  for (var i = 0; i < keys.length; i++) {
    var key = keys[i];
    if (!hasOwn(target, key) && !(exceptions && hasOwn(exceptions, key))) {
      defineProperty(target, key, getOwnPropertyDescriptor(source, key));
    }
  }
};


/***/ }),

/***/ 6623:
/*!**************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/to-length.js ***!
  \**************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var toIntegerOrInfinity = __webpack_require__(/*! ../internals/to-integer-or-infinity */ 156);

var min = Math.min;

// `ToLength` abstract operation
// https://tc39.es/ecma262/#sec-tolength
module.exports = function (argument) {
  var len = toIntegerOrInfinity(argument);
  return len > 0 ? min(len, 0x1FFFFFFFFFFFFF) : 0; // 2 ** 53 - 1 == 9007199254740991
};


/***/ }),

/***/ 6672:
/*!**************************************************!*\
  !*** ./js/src/gutenberg/blocks/ExcludeField.tsx ***!
  \**************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/components */ 6021);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ 4833);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var dompurify__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! dompurify */ 9133);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! react/jsx-runtime */ 7157);




/**
 * The Exclude field shared between widgets.
 *
 * Removed and replaced via filter by the PRO version.
 */
const ExcludeField = ({
  type,
  attributes,
  setAttributes
}) => {
  const help = /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("p", {
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("br", {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("span", {
      dangerouslySetInnerHTML: {
        __html: dompurify__WEBPACK_IMPORTED_MODULE_2__["default"].sanitize((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.sprintf)(/* translators: 1: Opening anchor tag, 2: Closing anchor tag */
        (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('For more robust and intuitive exclusions use the %1$sPRO version%2$s.', 'advanced-sidebar-menu'), '<a href="https://onpointplugins.com/advanced-sidebar-menu/advanced-sidebar-menu-pro-widget-docs/advanced-sidebar-menu-pro-excluding-menu-items/?utm_source=exclude-field&utm_campaign=gopro&utm_medium=wp-dash" target="_blank" rel="noopener noreferrer">', '</a>'), {
          ADD_ATTR: ['target']
        })
      }
    })]
  });
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__.TextControl
  /* translators: Selected post type plural label */, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.sprintf)((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('%s to exclude', 'advanced-sidebar-menu'), type?.labels?.name ?? ''),
    value: attributes.exclude,
    help: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.Fragment, {
      children: [(0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('ids, comma separated', 'advanced-sidebar-menu'), help]
    }),
    onChange: value => {
      setAttributes({
        exclude: value
      });
    }
    // @ts-expect-error -- Not technically supported until WP 6.7
    ,
    __nextHasNoMarginBottom: true
  });
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ((0,_wordpress_components__WEBPACK_IMPORTED_MODULE_0__.withFilters)('advanced-sidebar-menu.blocks.exclude-field')(ExcludeField));

/***/ }),

/***/ 6742:
/*!******************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/make-built-in.js ***!
  \******************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ 1109);
var fails = __webpack_require__(/*! ../internals/fails */ 6774);
var isCallable = __webpack_require__(/*! ../internals/is-callable */ 2120);
var hasOwn = __webpack_require__(/*! ../internals/has-own-property */ 9422);
var DESCRIPTORS = __webpack_require__(/*! ../internals/descriptors */ 2465);
var CONFIGURABLE_FUNCTION_NAME = (__webpack_require__(/*! ../internals/function-name */ 2483).CONFIGURABLE);
var inspectSource = __webpack_require__(/*! ../internals/inspect-source */ 5417);
var InternalStateModule = __webpack_require__(/*! ../internals/internal-state */ 3490);

var enforceInternalState = InternalStateModule.enforce;
var getInternalState = InternalStateModule.get;
var $String = String;
// eslint-disable-next-line es/no-object-defineproperty -- safe
var defineProperty = Object.defineProperty;
var stringSlice = uncurryThis(''.slice);
var replace = uncurryThis(''.replace);
var join = uncurryThis([].join);

var CONFIGURABLE_LENGTH = DESCRIPTORS && !fails(function () {
  return defineProperty(function () { /* empty */ }, 'length', { value: 8 }).length !== 8;
});

var TEMPLATE = String(String).split('String');

var makeBuiltIn = module.exports = function (value, name, options) {
  if (stringSlice($String(name), 0, 7) === 'Symbol(') {
    name = '[' + replace($String(name), /^Symbol\(([^)]*)\).*$/, '$1') + ']';
  }
  if (options && options.getter) name = 'get ' + name;
  if (options && options.setter) name = 'set ' + name;
  if (!hasOwn(value, 'name') || (CONFIGURABLE_FUNCTION_NAME && value.name !== name)) {
    if (DESCRIPTORS) defineProperty(value, 'name', { value: name, configurable: true });
    else value.name = name;
  }
  if (CONFIGURABLE_LENGTH && options && hasOwn(options, 'arity') && value.length !== options.arity) {
    defineProperty(value, 'length', { value: options.arity });
  }
  try {
    if (options && hasOwn(options, 'constructor') && options.constructor) {
      if (DESCRIPTORS) defineProperty(value, 'prototype', { writable: false });
    // in V8 ~ Chrome 53, prototypes of some methods, like `Array.prototype.values`, are non-writable
    } else if (value.prototype) value.prototype = undefined;
  } catch (error) { /* empty */ }
  var state = enforceInternalState(value);
  if (!hasOwn(state, 'source')) {
    state.source = join(TEMPLATE, typeof name == 'string' ? name : '');
  } return value;
};

// add fake Function#toString for correct work wrapped methods / constructors with methods like LoDash isNative
// eslint-disable-next-line no-extend-native -- required
Function.prototype.toString = makeBuiltIn(function toString() {
  return isCallable(this) && getInternalState(this).source || inspectSource(this);
}, 'toString');


/***/ }),

/***/ 6774:
/*!**********************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/fails.js ***!
  \**********************************************************************************************************/
/***/ ((module) => {

"use strict";

module.exports = function (exec) {
  try {
    return !!exec();
  } catch (error) {
    return true;
  }
};


/***/ }),

/***/ 6822:
/*!************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/get-iterator-direct.js ***!
  \************************************************************************************************************************/
/***/ ((module) => {

"use strict";

// `GetIteratorDirect(obj)` abstract operation
// https://tc39.es/ecma262/#sec-getiteratordirect
module.exports = function (obj) {
  return {
    iterator: obj,
    next: obj.next,
    done: false
  };
};


/***/ }),

/***/ 7005:
/*!************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/iterate.js ***!
  \************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var bind = __webpack_require__(/*! ../internals/function-bind-context */ 8345);
var call = __webpack_require__(/*! ../internals/function-call */ 5156);
var anObject = __webpack_require__(/*! ../internals/an-object */ 1734);
var tryToString = __webpack_require__(/*! ../internals/try-to-string */ 2970);
var isArrayIteratorMethod = __webpack_require__(/*! ../internals/is-array-iterator-method */ 8030);
var lengthOfArrayLike = __webpack_require__(/*! ../internals/length-of-array-like */ 7065);
var isPrototypeOf = __webpack_require__(/*! ../internals/object-is-prototype-of */ 2726);
var getIterator = __webpack_require__(/*! ../internals/get-iterator */ 8490);
var getIteratorMethod = __webpack_require__(/*! ../internals/get-iterator-method */ 886);
var iteratorClose = __webpack_require__(/*! ../internals/iterator-close */ 1952);

var $TypeError = TypeError;

var Result = function (stopped, result) {
  this.stopped = stopped;
  this.result = result;
};

var ResultPrototype = Result.prototype;

module.exports = function (iterable, unboundFunction, options) {
  var that = options && options.that;
  var AS_ENTRIES = !!(options && options.AS_ENTRIES);
  var IS_RECORD = !!(options && options.IS_RECORD);
  var IS_ITERATOR = !!(options && options.IS_ITERATOR);
  var INTERRUPTED = !!(options && options.INTERRUPTED);
  var fn = bind(unboundFunction, that);
  var iterator, iterFn, index, length, result, next, step;

  var stop = function (condition) {
    if (iterator) iteratorClose(iterator, 'normal');
    return new Result(true, condition);
  };

  var callFn = function (value) {
    if (AS_ENTRIES) {
      anObject(value);
      return INTERRUPTED ? fn(value[0], value[1], stop) : fn(value[0], value[1]);
    } return INTERRUPTED ? fn(value, stop) : fn(value);
  };

  if (IS_RECORD) {
    iterator = iterable.iterator;
  } else if (IS_ITERATOR) {
    iterator = iterable;
  } else {
    iterFn = getIteratorMethod(iterable);
    if (!iterFn) throw new $TypeError(tryToString(iterable) + ' is not iterable');
    // optimisation for array iterators
    if (isArrayIteratorMethod(iterFn)) {
      for (index = 0, length = lengthOfArrayLike(iterable); length > index; index++) {
        result = callFn(iterable[index]);
        if (result && isPrototypeOf(ResultPrototype, result)) return result;
      } return new Result(false);
    }
    iterator = getIterator(iterable, iterFn);
  }

  next = IS_RECORD ? iterable.next : iterator.next;
  while (!(step = call(next, iterator)).done) {
    try {
      result = callFn(step.value);
    } catch (error) {
      iteratorClose(iterator, 'throw', error);
    }
    if (typeof result == 'object' && result && isPrototypeOf(ResultPrototype, result)) return result;
  } return new Result(false);
};


/***/ }),

/***/ 7065:
/*!*************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/length-of-array-like.js ***!
  \*************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var toLength = __webpack_require__(/*! ../internals/to-length */ 6623);

// `LengthOfArrayLike` abstract operation
// https://tc39.es/ecma262/#sec-lengthofarraylike
module.exports = function (obj) {
  return toLength(obj.length);
};


/***/ }),

/***/ 7101:
/*!****************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/object-keys.js ***!
  \****************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var internalObjectKeys = __webpack_require__(/*! ../internals/object-keys-internal */ 3463);
var enumBugKeys = __webpack_require__(/*! ../internals/enum-bug-keys */ 9430);

// `Object.keys` method
// https://tc39.es/ecma262/#sec-object.keys
// eslint-disable-next-line es/no-object-keys -- safe
module.exports = Object.keys || function keys(O) {
  return internalObjectKeys(O, enumBugKeys);
};


/***/ }),

/***/ 7157:
/*!**************************************************************************************************!*\
  !*** ./.yarn/cache/react-npm-18.2.0-1eae08fee2-b562d9b569.zip/node_modules/react/jsx-runtime.js ***!
  \**************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


if (false) // removed by dead control flow
{} else {
  module.exports = __webpack_require__(/*! ./cjs/react-jsx-runtime.development.js */ 2413);
}


/***/ }),

/***/ 7207:
/*!****************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/v8-prototype-define-bug.js ***!
  \****************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var DESCRIPTORS = __webpack_require__(/*! ../internals/descriptors */ 2465);
var fails = __webpack_require__(/*! ../internals/fails */ 6774);

// V8 ~ Chrome 36-
// https://bugs.chromium.org/p/v8/issues/detail?id=3334
module.exports = DESCRIPTORS && fails(function () {
  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
  return Object.defineProperty(function () { /* empty */ }, 'prototype', {
    value: 42,
    writable: false
  }).prototype !== 42;
});


/***/ }),

/***/ 7300:
/*!**********************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/object-property-is-enumerable.js ***!
  \**********************************************************************************************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";

var $propertyIsEnumerable = {}.propertyIsEnumerable;
// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
var getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;

// Nashorn ~ JDK8 bug
var NASHORN_BUG = getOwnPropertyDescriptor && !$propertyIsEnumerable.call({ 1: 2 }, 1);

// `Object.prototype.propertyIsEnumerable` method implementation
// https://tc39.es/ecma262/#sec-object.prototype.propertyisenumerable
exports.f = NASHORN_BUG ? function propertyIsEnumerable(V) {
  var descriptor = getOwnPropertyDescriptor(this, V);
  return !!descriptor && descriptor.enumerable;
} : $propertyIsEnumerable;


/***/ }),

/***/ 7654:
/*!*****************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/to-primitive.js ***!
  \*****************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var call = __webpack_require__(/*! ../internals/function-call */ 5156);
var isObject = __webpack_require__(/*! ../internals/is-object */ 3763);
var isSymbol = __webpack_require__(/*! ../internals/is-symbol */ 9540);
var getMethod = __webpack_require__(/*! ../internals/get-method */ 789);
var ordinaryToPrimitive = __webpack_require__(/*! ../internals/ordinary-to-primitive */ 5515);
var wellKnownSymbol = __webpack_require__(/*! ../internals/well-known-symbol */ 1782);

var $TypeError = TypeError;
var TO_PRIMITIVE = wellKnownSymbol('toPrimitive');

// `ToPrimitive` abstract operation
// https://tc39.es/ecma262/#sec-toprimitive
module.exports = function (input, pref) {
  if (!isObject(input) || isSymbol(input)) return input;
  var exoticToPrim = getMethod(input, TO_PRIMITIVE);
  var result;
  if (exoticToPrim) {
    if (pref === undefined) pref = 'default';
    result = call(exoticToPrim, input, pref);
    if (!isObject(result) || isSymbol(result)) return result;
    throw new $TypeError("Can't convert object to primitive value");
  }
  if (pref === undefined) pref = 'number';
  return ordinaryToPrimitive(input, pref);
};


/***/ }),

/***/ 7793:
/*!****************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/global-this.js ***!
  \****************************************************************************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var check = function (it) {
  return it && it.Math === Math && it;
};

// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
module.exports =
  // eslint-disable-next-line es/no-global-this -- safe
  check(typeof globalThis == 'object' && globalThis) ||
  check(typeof window == 'object' && window) ||
  // eslint-disable-next-line no-restricted-globals -- safe
  check(typeof self == 'object' && self) ||
  check(typeof __webpack_require__.g == 'object' && __webpack_require__.g) ||
  check(typeof this == 'object' && this) ||
  // eslint-disable-next-line no-new-func -- fallback
  (function () { return this; })() || Function('return this')();


/***/ }),

/***/ 7901:
/*!********************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/create-property.js ***!
  \********************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var DESCRIPTORS = __webpack_require__(/*! ../internals/descriptors */ 2465);
var definePropertyModule = __webpack_require__(/*! ../internals/object-define-property */ 742);
var createPropertyDescriptor = __webpack_require__(/*! ../internals/create-property-descriptor */ 9919);

module.exports = function (object, key, value) {
  if (DESCRIPTORS) definePropertyModule.f(object, key, createPropertyDescriptor(0, value));
  else object[key] = value;
};


/***/ }),

/***/ 7934:
/*!*********************************************!*\
  !*** ./js/src/gutenberg/blocks/Display.tsx ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var core_js_modules_es_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.iterator.constructor.js */ 5146);
/* harmony import */ var core_js_modules_es_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_iterator_map_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.iterator.map.js */ 6288);
/* harmony import */ var core_js_modules_es_iterator_map_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_iterator_map_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ 6021);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _globals_config__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../globals/config */ 5328);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ 4833);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! lodash */ 8110);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../components/ErrorBoundary */ 5000);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! react/jsx-runtime */ 7157);








const checkboxes = {
  /* translators: Selected taxonomy single label */
  include_parent: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Display highest level parent %s', 'advanced-sidebar-menu'),
  /* translators: Selected taxonomy single label */
  include_childless_parent: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Display menu when there is only the parent %s', 'advanced-sidebar-menu'),
  /* translators: Selected taxonomy plural label */
  display_all: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Always display child %s', 'advanced-sidebar-menu')
};
const LEVEL_OPTIONS = [{
  value: '100',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('- All -', 'advanced-sidebar-menu')
}, ...(0,lodash__WEBPACK_IMPORTED_MODULE_5__.range)(1, 11).map(n => ({
  value: n.toString(),
  label: n.toString()
}))];

/**
 * Display Options shared between widgets.
 *
 * 1. Display the highest level parent page.
 * 2. Display the menu when there is only the parent page.
 * 3. Always display child pages.
 * 5. Display levels of child pages.
 *
 */
const Display = ({
  attributes,
  setAttributes,
  type,
  name,
  clientId,
  children
}) => {
  const showLevels = _globals_config__WEBPACK_IMPORTED_MODULE_3__.CONFIG.blocks.pages.id === name && '1' === _globals_config__WEBPACK_IMPORTED_MODULE_3__.CONFIG.isPro || true === attributes.display_all;
  const fillProps = {
    type,
    attributes,
    name,
    setAttributes,
    clientId
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Display', 'advanced-sidebar-menu'),
    children: [Object.keys(checkboxes).map(item => {
      let label = type?.labels?.singular_name.toLowerCase() ?? '';
      if ('display_all' === item) {
        label = type?.labels?.name.toLowerCase() ?? '';
      }
      return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.CheckboxControl, {
        //eslint-disable-next-line @wordpress/valid-sprintf
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.sprintf)(checkboxes[item], label),
        checked: true === attributes[item],
        onChange: checked => {
          setAttributes({
            [item]: checked
          });
        }
        // @ts-expect-error -- Not technically available until WP 6.7.
        ,
        __nextHasNoMarginBottom: true
      }, item);
    }), showLevels && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
      /* translators: {select HTML input}, {post type plural label} */
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.sprintf)((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Levels of child %s to display', 'advanced-sidebar-menu'), type?.labels?.name.toLowerCase() ?? ''),
      className: 'advanced-sidebar-menu-display-select',
      value: attributes.levels.toString(),
      onChange: value => {
        setAttributes({
          levels: parseInt(value)
        });
      },
      options: LEVEL_OPTIONS
      // @ts-expect-error -- Not technically available until WP 6.7.
      ,
      __next40pxDefaultSize: true,
      __nextHasNoMarginBottom: true
    }, 'levels'), children, /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsxs)(_components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_6__["default"], {
      attributes: attributes,
      block: name,
      section: 'Display/slots',
      children: [_globals_config__WEBPACK_IMPORTED_MODULE_3__.CONFIG.blocks.pages.id === name && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Slot, {
        name: "advanced-sidebar-menu/pages/display",
        fillProps: fillProps
      }), _globals_config__WEBPACK_IMPORTED_MODULE_3__.CONFIG.blocks.categories.id === name && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Slot, {
        name: "advanced-sidebar-menu/categories/display",
        fillProps: fillProps
      })]
    })]
  });
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Display);

/***/ }),

/***/ 8030:
/*!*****************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/is-array-iterator-method.js ***!
  \*****************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var wellKnownSymbol = __webpack_require__(/*! ../internals/well-known-symbol */ 1782);
var Iterators = __webpack_require__(/*! ../internals/iterators */ 5696);

var ITERATOR = wellKnownSymbol('iterator');
var ArrayPrototype = Array.prototype;

// check on default Array iterator
module.exports = function (it) {
  return it !== undefined && (Iterators.Array === it || ArrayPrototype[ITERATOR] === it);
};


/***/ }),

/***/ 8094:
/*!*********************************!*\
  !*** external "window.wp?.url" ***!
  \*********************************/
/***/ ((module) => {

"use strict";
module.exports = window.wp?.url;

/***/ }),

/***/ 8110:
/*!*************************!*\
  !*** external "lodash" ***!
  \*************************/
/***/ ((module) => {

"use strict";
module.exports = lodash;

/***/ }),

/***/ 8259:
/*!******************************************!*\
  !*** external "window.wp?.htmlEntities" ***!
  \******************************************/
/***/ ((module) => {

"use strict";
module.exports = window.wp?.htmlEntities;

/***/ }),

/***/ 8265:
/*!**********************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/object-get-own-property-names.js ***!
  \**********************************************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";

var internalObjectKeys = __webpack_require__(/*! ../internals/object-keys-internal */ 3463);
var enumBugKeys = __webpack_require__(/*! ../internals/enum-bug-keys */ 9430);

var hiddenKeys = enumBugKeys.concat('length', 'prototype');

// `Object.getOwnPropertyNames` method
// https://tc39.es/ecma262/#sec-object.getownpropertynames
// eslint-disable-next-line es/no-object-getownpropertynames -- safe
exports.f = Object.getOwnPropertyNames || function getOwnPropertyNames(O) {
  return internalObjectKeys(O, hiddenKeys);
};


/***/ }),

/***/ 8301:
/*!************************************************!*\
  !*** ./js/src/gutenberg/blocks/pages/Edit.tsx ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var core_js_modules_es_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.iterator.constructor.js */ 5146);
/* harmony import */ var core_js_modules_es_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_iterator_map_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.iterator.map.js */ 6288);
/* harmony import */ var core_js_modules_es_iterator_map_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_iterator_map_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/block-editor */ 6423);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ 6021);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _block__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./block */ 3621);
/* harmony import */ var _Preview__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../Preview */ 1652);
/* harmony import */ var _Display__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../Display */ 7934);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/data */ 5705);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _InfoPanel__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../InfoPanel */ 1474);
/* harmony import */ var _globals_config__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../../../globals/config */ 5328);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @wordpress/i18n */ 4833);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../../../components/ErrorBoundary */ 5000);
/* harmony import */ var _SideLoad__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../../SideLoad */ 9278);
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ../../helpers */ 9440);
/* harmony import */ var _ExcludeField__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ../ExcludeField */ 6672);
/* harmony import */ var dompurify__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! dompurify */ 9133);
/* harmony import */ var _edit_pcss__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./edit.pcss */ 2013);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! react/jsx-runtime */ 7157);


















/**
 * Pages block content in the editor.
 */
const Edit = ({
  attributes,
  setAttributes,
  clientId,
  name
}) => {
  const postType = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_7__.useSelect)(select => {
    const type = select('core').getPostType(attributes.post_type ?? 'page');
    return type ?? select('core').getPostType('page');
  }, [attributes.post_type]);

  // We have a version conflict or license error.
  if ('' !== _globals_config__WEBPACK_IMPORTED_MODULE_9__.CONFIG.error) {
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.Fragment, {
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.InspectorControls, {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)("div", {
          className: _edit_pcss__WEBPACK_IMPORTED_MODULE_16__["default"].error,
          dangerouslySetInnerHTML: {
            __html: dompurify__WEBPACK_IMPORTED_MODULE_15__["default"].sanitize(_globals_config__WEBPACK_IMPORTED_MODULE_9__.CONFIG.error)
          }
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_Preview__WEBPACK_IMPORTED_MODULE_5__["default"], {
        attributes: attributes,
        block: _block__WEBPACK_IMPORTED_MODULE_4__.block.id,
        clientId: clientId
      })]
    });
  }
  const fillProps = {
    type: postType,
    attributes,
    name,
    setAttributes,
    clientId
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.Fragment, {
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.InspectorControls, {
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)("div", {
        className: 'components-panel__body is-opened',
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)("a", {
          href: _globals_config__WEBPACK_IMPORTED_MODULE_9__.CONFIG.docs.page,
          target: "_blank",
          rel: "noopener noreferrer",
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__.__)('Documentation', 'advanced-sidebar-menu')
        })
      })
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsxs)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.InspectorControls, {
      children: [(0,_helpers__WEBPACK_IMPORTED_MODULE_13__.isScreen)(['widgets', 'site-editor', 'customize']) && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.PanelBody, {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.TextControl, {
          value: attributes.title ?? '',
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__.__)('Title', 'advanced-sidebar-menu'),
          onChange: title => setAttributes({
            title
          })
          // @ts-expect-error -- Not technically supported until WP 6.7
          ,
          __nextHasNoMarginBottom: true
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsxs)(_components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_11__["default"], {
        attributes: attributes,
        block: name,
        section: 'pages/Edit/general',
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_Display__WEBPACK_IMPORTED_MODULE_6__["default"], {
          attributes: attributes,
          clientId: clientId,
          name: name,
          setAttributes: setAttributes,
          type: postType
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsxs)("div", {
          className: 'components-panel__body is-opened',
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Slot, {
            name: "advanced-sidebar-menu/pages/general",
            fillProps: fillProps
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.SelectControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_10__.__)('Order by', 'advanced-sidebar-menu'),
            value: attributes.order_by,
            labelPosition: 'side',
            options: Object.entries(_globals_config__WEBPACK_IMPORTED_MODULE_9__.CONFIG.pages.orderBy).map(([value, label]) => ({
              value,
              label
            })),
            onChange: value => {
              setAttributes({
                order_by: value
              });
            }
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_ExcludeField__WEBPACK_IMPORTED_MODULE_14__["default"], {
            type: postType,
            attributes: attributes,
            setAttributes: setAttributes
          })]
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_11__["default"], {
        attributes: attributes,
        block: name,
        section: 'pages/Edit/inspector',
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Slot, {
          name: "advanced-sidebar-menu/pages/inspector",
          fillProps: fillProps
        })
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.BlockControls, {
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_11__["default"], {
        attributes: attributes,
        block: name,
        section: 'pages/Edit/block-controls',
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Slot, {
          name: "advanced-sidebar-menu/pages/block-controls",
          fillProps: fillProps
        })
      })
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_InfoPanel__WEBPACK_IMPORTED_MODULE_8__["default"], {
      clientId: clientId
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_11__["default"], {
      attributes: attributes,
      block: name,
      section: 'pages/Edit/preview',
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_Preview__WEBPACK_IMPORTED_MODULE_5__["default"], {
        attributes: attributes,
        block: _block__WEBPACK_IMPORTED_MODULE_4__.block.id,
        clientId: clientId
      })
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_17__.jsx)(_SideLoad__WEBPACK_IMPORTED_MODULE_12__["default"], {
      clientId: clientId
    })]
  });
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Edit);

/***/ }),

/***/ 8321:
/*!***********************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/export.js ***!
  \***********************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var globalThis = __webpack_require__(/*! ../internals/global-this */ 7793);
var getOwnPropertyDescriptor = (__webpack_require__(/*! ../internals/object-get-own-property-descriptor */ 5812).f);
var createNonEnumerableProperty = __webpack_require__(/*! ../internals/create-non-enumerable-property */ 8684);
var defineBuiltIn = __webpack_require__(/*! ../internals/define-built-in */ 8705);
var defineGlobalProperty = __webpack_require__(/*! ../internals/define-global-property */ 5098);
var copyConstructorProperties = __webpack_require__(/*! ../internals/copy-constructor-properties */ 6589);
var isForced = __webpack_require__(/*! ../internals/is-forced */ 5429);

/*
  options.target         - name of the target object
  options.global         - target is the global object
  options.stat           - export as static methods of target
  options.proto          - export as prototype methods of target
  options.real           - real prototype method for the `pure` version
  options.forced         - export even if the native feature is available
  options.bind           - bind methods to the target, required for the `pure` version
  options.wrap           - wrap constructors to preventing global pollution, required for the `pure` version
  options.unsafe         - use the simple assignment of property instead of delete + defineProperty
  options.sham           - add a flag to not completely full polyfills
  options.enumerable     - export as enumerable property
  options.dontCallGetSet - prevent calling a getter on target
  options.name           - the .name of the function if it does not match the key
*/
module.exports = function (options, source) {
  var TARGET = options.target;
  var GLOBAL = options.global;
  var STATIC = options.stat;
  var FORCED, target, key, targetProperty, sourceProperty, descriptor;
  if (GLOBAL) {
    target = globalThis;
  } else if (STATIC) {
    target = globalThis[TARGET] || defineGlobalProperty(TARGET, {});
  } else {
    target = globalThis[TARGET] && globalThis[TARGET].prototype;
  }
  if (target) for (key in source) {
    sourceProperty = source[key];
    if (options.dontCallGetSet) {
      descriptor = getOwnPropertyDescriptor(target, key);
      targetProperty = descriptor && descriptor.value;
    } else targetProperty = target[key];
    FORCED = isForced(GLOBAL ? key : TARGET + (STATIC ? '.' : '#') + key, options.forced);
    // contained in target
    if (!FORCED && targetProperty !== undefined) {
      if (typeof sourceProperty == typeof targetProperty) continue;
      copyConstructorProperties(sourceProperty, targetProperty);
    }
    // add a flag to not completely full polyfills
    if (options.sham || (targetProperty && targetProperty.sham)) {
      createNonEnumerableProperty(sourceProperty, 'sham', true);
    }
    defineBuiltIn(target, key, sourceProperty, options);
  }
};


/***/ }),

/***/ 8336:
/*!**************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/to-object.js ***!
  \**************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var requireObjectCoercible = __webpack_require__(/*! ../internals/require-object-coercible */ 1825);

var $Object = Object;

// `ToObject` abstract operation
// https://tc39.es/ecma262/#sec-toobject
module.exports = function (argument) {
  return $Object(requireObjectCoercible(argument));
};


/***/ }),

/***/ 8345:
/*!**************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/function-bind-context.js ***!
  \**************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this-clause */ 7);
var aCallable = __webpack_require__(/*! ../internals/a-callable */ 4549);
var NATIVE_BIND = __webpack_require__(/*! ../internals/function-bind-native */ 4235);

var bind = uncurryThis(uncurryThis.bind);

// optional / simple context binding
module.exports = function (fn, that) {
  aCallable(fn);
  return that === undefined ? fn : NATIVE_BIND ? bind(fn, that) : function (/* ...args */) {
    return fn.apply(that, arguments);
  };
};


/***/ }),

/***/ 8445:
/*!**************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/to-string-tag-support.js ***!
  \**************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var wellKnownSymbol = __webpack_require__(/*! ../internals/well-known-symbol */ 1782);

var TO_STRING_TAG = wellKnownSymbol('toStringTag');
var test = {};

test[TO_STRING_TAG] = 'z';

module.exports = String(test) === '[object z]';


/***/ }),

/***/ 8490:
/*!*****************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/get-iterator.js ***!
  \*****************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var call = __webpack_require__(/*! ../internals/function-call */ 5156);
var aCallable = __webpack_require__(/*! ../internals/a-callable */ 4549);
var anObject = __webpack_require__(/*! ../internals/an-object */ 1734);
var tryToString = __webpack_require__(/*! ../internals/try-to-string */ 2970);
var getIteratorMethod = __webpack_require__(/*! ../internals/get-iterator-method */ 886);

var $TypeError = TypeError;

module.exports = function (argument, usingIterator) {
  var iteratorMethod = arguments.length < 2 ? getIteratorMethod(argument) : usingIterator;
  if (aCallable(iteratorMethod)) return anObject(call(iteratorMethod, argument));
  throw new $TypeError(tryToString(argument) + ' is not iterable');
};


/***/ }),

/***/ 8581:
/*!********************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/uid.js ***!
  \********************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ 1109);

var id = 0;
var postfix = Math.random();
var toString = uncurryThis(1.1.toString);

module.exports = function (key) {
  return 'Symbol(' + (key === undefined ? '' : key) + ')_' + toString(++id + postfix, 36);
};


/***/ }),

/***/ 8650:
/*!*************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/is-null-or-undefined.js ***!
  \*************************************************************************************************************************/
/***/ ((module) => {

"use strict";

// we can't use just `it == null` since of `document.all` special case
// https://tc39.es/ecma262/#sec-IsHTMLDDA-internal-slot-aec
module.exports = function (it) {
  return it === null || it === undefined;
};


/***/ }),

/***/ 8684:
/*!***********************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/create-non-enumerable-property.js ***!
  \***********************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var DESCRIPTORS = __webpack_require__(/*! ../internals/descriptors */ 2465);
var definePropertyModule = __webpack_require__(/*! ../internals/object-define-property */ 742);
var createPropertyDescriptor = __webpack_require__(/*! ../internals/create-property-descriptor */ 9919);

module.exports = DESCRIPTORS ? function (object, key, value) {
  return definePropertyModule.f(object, key, createPropertyDescriptor(1, value));
} : function (object, key, value) {
  object[key] = value;
  return object;
};


/***/ }),

/***/ 8705:
/*!********************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/define-built-in.js ***!
  \********************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var isCallable = __webpack_require__(/*! ../internals/is-callable */ 2120);
var definePropertyModule = __webpack_require__(/*! ../internals/object-define-property */ 742);
var makeBuiltIn = __webpack_require__(/*! ../internals/make-built-in */ 6742);
var defineGlobalProperty = __webpack_require__(/*! ../internals/define-global-property */ 5098);

module.exports = function (O, key, value, options) {
  if (!options) options = {};
  var simple = options.enumerable;
  var name = options.name !== undefined ? options.name : key;
  if (isCallable(value)) makeBuiltIn(value, name, options);
  if (options.global) {
    if (simple) O[key] = value;
    else defineGlobalProperty(key, value);
  } else {
    try {
      if (!options.unsafe) delete O[key];
      else if (O[key]) simple = true;
    } catch (error) { /* empty */ }
    if (simple) O[key] = value;
    else definePropertyModule.f(O, key, {
      value: value,
      enumerable: false,
      configurable: !options.nonConfigurable,
      writable: !options.nonWritable
    });
  } return O;
};


/***/ }),

/***/ 8765:
/*!**********************************************************************************************************************************************************************************************************************************!*\
  !*** ./.yarn/__virtual__/@lipemat-js-boilerplate-gutenberg-virtual-10ef7239dd/0/cache/@lipemat-js-boilerplate-gutenberg-npm-4.1.2-27d57a41e4-2d8cda4946.zip/node_modules/@lipemat/js-boilerplate-gutenberg/dist/index.module.js ***!
  \**********************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   addQueryArgs: () => (/* binding */ O),
/* harmony export */   autoload: () => (/* binding */ at),
/* harmony export */   autoloadBlocks: () => (/* binding */ it),
/* harmony export */   autoloadFormats: () => (/* binding */ ct),
/* harmony export */   autoloadPlugins: () => (/* binding */ ut),
/* harmony export */   buildQueryString: () => (/* binding */ R),
/* harmony export */   clearApplicationPassword: () => (/* binding */ X),
/* harmony export */   clearNonce: () => (/* binding */ m),
/* harmony export */   createMethods: () => (/* binding */ et),
/* harmony export */   doRequest: () => (/* binding */ tt),
/* harmony export */   doRequestWithPagination: () => (/* binding */ Z),
/* harmony export */   enableApplicationPassword: () => (/* binding */ $),
/* harmony export */   fetchHandler: () => (/* binding */ Q),
/* harmony export */   getApplicationPassword: () => (/* binding */ D),
/* harmony export */   getAuthorizationUrl: () => (/* binding */ H),
/* harmony export */   getFragment: () => (/* binding */ I),
/* harmony export */   getQueryArg: () => (/* binding */ A),
/* harmony export */   getQueryArgs: () => (/* binding */ _),
/* harmony export */   getQueryString: () => (/* binding */ x),
/* harmony export */   hasApplicationPassword: () => (/* binding */ q),
/* harmony export */   hasExternalNonce: () => (/* binding */ v),
/* harmony export */   maybeRefreshNonce: () => (/* binding */ V),
/* harmony export */   restoreNonce: () => (/* binding */ g),
/* harmony export */   restoreRootURL: () => (/* binding */ G),
/* harmony export */   setInitialNonce: () => (/* binding */ d),
/* harmony export */   setInitialRootURL: () => (/* binding */ U),
/* harmony export */   setNonce: () => (/* binding */ h),
/* harmony export */   setRootURL: () => (/* binding */ N),
/* harmony export */   usePostMeta: () => (/* binding */ ht),
/* harmony export */   useTerms: () => (/* binding */ mt),
/* harmony export */   wpapi: () => (/* binding */ nt)
/* harmony export */ });
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/blocks */ 2463);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/plugins */ 259);
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_plugins__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/data */ 5705);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/rich-text */ 4796);
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_3__);
var l="",f="";function d(t){""===f&&void 0!==t&&(l=t,f=t)}function p(){return l}function v(){return""!==l&&""!==f&&l!==f}function h(t){l=t}function m(){l=""}function g(){l=f}function y(t,e){try{var n=t()}catch(t){return e(t)}return n&&n.then?n.then(void 0,e):n}var w=function(t,e){void 0===e&&(e=!0);try{if(!e)throw t;return Promise.resolve(b(t)).then(function(t){throw"rest_cookie_invalid_nonce"===(null==t?void 0:t.code)&&v()&&(t.code="external_rest_cookie_invalid_nonce"),t||{code:"unknown_error",message:"An unknown error occurred."}})}catch(t){return Promise.reject(t)}},b=function(t){try{var e={code:"invalid_json",message:"The response is not a valid JSON response."};if(!t||void 0===t.json)throw e;return Promise.resolve(y(function(){return Promise.resolve(t.json())},function(){throw e}))}catch(t){return Promise.reject(t)}},P=function(t,e){return void 0===e&&(e=!0),Promise.resolve(y(function(){return function(t,e){return void 0===e&&(e=!0),e?204===t.status?null:void 0!==t.json?t.json():Promise.reject(t):t}(t,e)},function(t){return Promise.resolve(w(t,e))}))};function E(t,e){(null==e||e>t.length)&&(e=t.length);for(var n=0,r=Array(e);n<e;n++)r[n]=t[n];return r}function T(t,e){var n="undefined"!=typeof Symbol&&t[Symbol.iterator]||t["@@iterator"];if(n)return(n=n.call(t)).next.bind(n);if(Array.isArray(t)||(n=function(t,e){if(t){if("string"==typeof t)return E(t,e);var n={}.toString.call(t).slice(8,-1);return"Object"===n&&t.constructor&&(n=t.constructor.name),"Map"===n||"Set"===n?Array.from(t):"Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)?E(t,e):void 0}}(t))||e&&t&&"number"==typeof t.length){n&&(t=n);var r=0;return function(){return r>=t.length?{done:!0}:{done:!1,value:t[r++]}}}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}function j(){return j=Object.assign?Object.assign.bind():function(t){for(var e=1;e<arguments.length;e++){var n=arguments[e];for(var r in n)({}).hasOwnProperty.call(n,r)&&(t[r]=n[r])}return t},j.apply(null,arguments)}function k(t){return""===t.trim()?t:t.replace(/\/?$/,"/")}function A(t,e){var n;return null!=(n=_(t)[e])?n:void 0}function _(t){var e;return(null!=(e=x(t))?e:"").replace(/\+/g,"%20").split("&").reduce(function(t,e){if(""===e)return t;var n=e.split("=").filter(Boolean).map(function(t){try{return decodeURIComponent(t)}catch(e){return console.debug("Invalid URL component: "+t,e),t}}),r=n[1],o=void 0===r?"":r;return function(t,e,n){for(var r=e.length,o=r-1,i=0;i<r;i++){var u=e[i];""===u&&Array.isArray(t)&&(u=t.length.toString()),u=["__proto__","constructor","prototype"].includes(u)?u.toUpperCase():u;var c=!isNaN(Number(e[i+1]));i===o?t[u]=n:void 0===t[u]&&(t[u]=c?[]:{}),Array.isArray(t[u])&&!c&&(t[u]=j({},t[u])),t=t[u]}}(t,n[0].replace(/]/g,"").split("["),o),t},Object.create(null))}function x(t){try{var e=new URL(t,"https://ex.com").search.substring(1);return e.length>0?e:void 0}catch(e){return void console.debug("Invalid URL:",t,e)}}function O(t,e){if(void 0===e&&(e={}),0===Object.keys(e).length)return null!=t?t:"";var n="",r="";if(void 0!==t){var o;r=null!=(o=I(t))?o:"",n=t.replace(r,"");var i=t.indexOf("?");-1!==i&&(e=Object.assign(_(t),e),n=n=n.slice(0,i))}return n+"?"+R(e)+r}function I(t){var e=/^\S+?(#[^\s?]*)/.exec(t);if(null!==e&&e.length>1)return e[1]}function R(t){for(var e,n="",r=Object.entries(t);e=r.shift();){var o=e[0],i=e[1];if(null===i||"object"!=typeof i&&!Array.isArray(i))void 0!==i&&(null===i&&(i=""),n+="&"+[o,i].map(encodeURIComponent).join("="));else for(var u,c=T(Object.entries(i).reverse());!(u=c()).done;){var a=u.value;r.unshift([o+"["+a[0]+"]",a[1]])}}return n.slice(1)}var S="",B="";function U(t){B=t,S=t}function G(){""===B?(S="",S=C()):S=B}function C(){var t;if(""!==S)return k(S);var e=document.querySelector('link[rel="https://api.w.org/"]'),n=null==e?void 0:e.getAttribute("href");if("string"==typeof n)S=n;else{if(void 0===(null==(t=window.wpApiSettings)?void 0:t.root))throw new URIError("Unable to determine the root URL. Use `setInitialRootURL` to set the root URL.");S=window.wpApiSettings.root}return k(S)}function L(t,e){void 0===e&&(e=!0);var n="";return void 0===t.url?"string"==typeof t.path?((n=C()).indexOf("?")>-1&&(t.path=t.path.replace("?","&")),n+=function(t){return""===t.trim()?t:t.replace(/^\//,"")}(t.path)):n=C():n=t.url,e?M(n):n}function N(t,e){S=t.replace(/\/$/,"")+"/",""===window.location.hostname||new URL(S).hostname!==window.location.hostname?void 0!==e?h(e):v()||m():g()}var M=function(t){return void 0===A(t,"_locale")&&(t=O(t,{_locale:"user"})),t},W="";function D(){return W}function q(){return""!==W}function X(){W=""}function $(t,e){W="Basic "+btoa(t+":"+e)}var z=["data","parse"],H=function(t){return Promise.resolve(function(){try{var e=Promise.resolve(Q({path:"/",method:"GET"})).then(function(e){return e.authentication["application-passwords"]?O(e.authentication["application-passwords"].endpoints.authorization,t):{code:"application_passwords_disabled",message:"Application passwords are not enabled on this site.",data:null}})}catch(t){return t}return e&&e.then?e.then(void 0,function(t){return t}):e}())},J={Accept:"application/json, */*;q=0.1"},Y={credentials:"include"},F=!1,K=function(t){if(t.status>=200&&t.status<300)return t;throw t},Q=function(t){var e=t,n=t.data,r=t.parse,o=void 0===r||r,i=function(t,e){if(null==t)return{};var n={};for(var r in t)if({}.hasOwnProperty.call(t,r)){if(-1!==e.indexOf(r))continue;n[r]=t[r]}return n}(t,z);delete i.url,delete i.path;var u=e.body,c=j({},J,""!==p()?{"X-WP-Nonce":p()}:{},""!==D()?{Authorization:D()}:{},e.headers);void 0!==n&&(u=JSON.stringify(n),c["Content-Type"]="application/json");var a=j({},Y,i,{body:u,headers:c});return window.fetch(L(t),a).then(function(e){return Promise.resolve(e).then(K).catch(function(t){return w(t,o)}).then(function(t){return P(t,o)}).catch(function(e){return V(e,function(){return Q(t)})})},function(t){if(t&&"AbortError"===t.name)throw t;throw{code:"fetch_error",message:"You are probably offline."}})};function V(t,e){return"rest_cookie_invalid_nonce"!==t.code?Promise.reject(t):(m(),F?Promise.reject(t):(F=!0,window.fetch(L({path:"wp-admin/admin-ajax.php?action=rest-nonce"},!1)).then(K).then(function(t){return t.text()}).then(function(t){return h(t),e()}).finally(function(){F=!1})))}var Z=function(t,e,n){try{var r,o,i=function(t){return r?t:Promise.resolve(P(o)).then(function(t){var e,n;return{items:t,totalPages:parseInt(null!=(e=o.headers.get("X-WP-TotalPages"))?e:"1"),totalItems:parseInt(null!=(n=o.headers.get("X-WP-Total"))?n:"0")}})},u=function(r,i){try{var u=Promise.resolve(tt(t,e,n,!1)).then(function(t){o=t})}catch(t){return i(t)}return u&&u.then?u.then(void 0,i):u}(0,function(o){return Promise.resolve(P(o)).then(function(o){var i=V(o,function(){return Z(t,e,n)});return r=1,i})});return Promise.resolve(u&&u.then?u.then(i):i(u))}catch(t){return Promise.reject(t)}},tt=function(t,e,n,r){void 0===r&&(r=!0);try{return Promise.resolve(Q(void 0===n||"GET"===e||"OPTIONS"===e?{method:e,parse:r,path:O(t,null!=n?n:{})}:{data:n,method:e,parse:r,path:t}))}catch(t){return Promise.reject(t)}};function et(t){var e,n=""===(e=t).trim()?e:e.replace(/^\/?/,"/");return{create:function(t){return tt(n,"POST",t)},delete:function(t){return tt(k(n)+t,"DELETE",{force:!0})},get:function(t){return tt(n,"GET",t)},getById:function(t,e){return tt(k(n)+t,"GET",e)},getWithPagination:function(t){return Z(n,"GET",t)},trash:function(t){return tt(k(n)+t,"DELETE")},update:function(t){return tt(k(n)+t.id,"PATCH",t)}}}function nt(t){var e={};return["categories","comments","blocks","media","menus","menu-locations","menu-items","pages","posts","tags","taxonomies","search"].map(function(t){return e[t]=function(){return et("/wp/v2/"+t)}}),e.menuItems=function(){return et("/wp/v2/menu-items")},e.users=function(){var t=et("/wp/v2/users");return t.delete=function(t,e){return void 0===e&&(e=!1),tt("/wp/v2/users/"+t,"DELETE",{force:!0,reassign:e})},t},e.applicationPasswords=function(){return{create:function(t,e){return tt("/wp/v2/users/"+t+"/application-passwords","POST",e)},delete:function(t,e){return tt("/wp/v2/users/"+t+"/application-passwords/"+e,"DELETE")},get:function(t){return tt("/wp/v2/users/"+t+"/application-passwords","GET")},getById:function(t,e){return tt("/wp/v2/users/"+t+"/application-passwords/"+e,"GET")},introspect:function(t){return tt("/wp/v2/users/"+t+"/application-passwords/introspect","GET")},update:function(t,e,n){return tt("/wp/v2/users/"+t+"/application-passwords/"+e,"PUT",n)}}},e.menuLocations=function(){return{get:function(){return tt("/wp/v2/menu-locations","GET")},getById:function(t){return tt("/wp/v2/menu-locations/"+t,"GET")}}},e.settings=function(){return{get:function(){return tt("/wp/v2/settings","GET")},update:function(t){return tt("/wp/v2/settings","POST",t)}}},e.statuses=function(){return{get:function(t){return tt("/wp/v2/statuses","GET",null!=t?t:{})}}},e.types=function(){return{get:function(){return tt("/wp/v2/types","GET")},getById:function(t){return tt("'/wp/v2/types'/"+t,"GET")}}},void 0!==t&&Object.keys(t).map(function(n){return e[n]=t[n]}),e}function rt(t,e,n){if(!t.s){if(n instanceof ot){if(!n.s)return void(n.o=rt.bind(null,t,e));1&e&&(e=n.s),n=n.v}if(n&&n.then)return void n.then(rt.bind(null,t,e),rt.bind(null,t,2));t.s=e,t.v=n;const r=t.o;r&&r(t)}}var ot=/*#__PURE__*/function(){function t(){}return t.prototype.then=function(e,n){var r=new t,o=this.s;if(o){var i=1&o?e:n;if(i){try{rt(r,1,i(this.v))}catch(t){rt(r,2,t)}return r}return this}return this.o=function(t){try{var o=t.v;1&t.s?rt(r,1,e?e(o):o):n?rt(r,1,n(o)):rt(r,2,o)}catch(t){rt(r,2,t)}},r},t}(),it=function(n,r){at({afterReload:pt,beforeReload:ft,getContext:n,pluginModule:r,register:_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__.registerBlockType,unregister:_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__.unregisterBlockType,type:"block"})},ut=function(t,e){at({afterReload:function(){},beforeReload:function(){},getContext:t,pluginModule:e,register:_wordpress_plugins__WEBPACK_IMPORTED_MODULE_1__.registerPlugin,unregister:_wordpress_plugins__WEBPACK_IMPORTED_MODULE_1__.unregisterPlugin,type:"plugin"})},ct=function(t,e){at({afterReload:function(){},beforeReload:function(){},getContext:t,pluginModule:e,register:_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_3__.registerFormatType,unregister:_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_3__.unregisterFormatType,type:"format"})},at=function(t){var e=t.afterReload,n=t.beforeReload,r=t.getContext,o=t.pluginModule,i=t.register,u=t.unregister,c=t.type,a={},s=function(){n();var t=r();if(t){var o=[];return t.keys().forEach(function(e){var n=t(e);Boolean(n.exclude)||n!==a[e]&&(Boolean(a[n.name+"-"+c])&&u(n.name),i(n.name,n.settings),o.push(n.name),a[n.name+"-"+c]=n)}),e(o),t}},l=s();o.hot&&void 0!==(null==l?void 0:l.id)&&o.hot.accept(l.id.toString(),s)},st=null,lt=[],ft=function(){st=(0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.select)("core/block-editor").getSelectedBlockClientId()},dt=function(t,e){void 0===t&&(t=[]);var n=e.clientId,r=e.innerBlocks;t.includes(e.name)&&lt.push(n),r.forEach(function(e){return dt(t,e)})},pt=function(t){void 0===t&&(t=[]);try{return Promise.resolve((0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.dispatch)("core/block-editor").clearSelectedBlock()).then(function(){function e(){function t(){lt=[],st=null}var e=null!==st?Promise.resolve((0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.dispatch)("core/block-editor").selectBlock(st)).then(function(){}):Promise.resolve((0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.dispatch)("core/block-editor").clearSelectedBlock()).then(function(){});return e&&e.then?e.then(t):t()}(0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.select)("core/block-editor").getBlocks().forEach(function(e){return dt(t,e)});var n,r,u,c,a,s=(n=lt,r=function(t){return Promise.resolve((0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.dispatch)("core/block-editor").selectBlock(lt[t])).then(function(){})},a=-1,function t(e){try{for(;++a<n.length;)if((e=r(a))&&e.then){if(!((o=e)instanceof ot&&1&o.s))return void e.then(t,c||(c=rt.bind(null,u=new ot,2)));e=e.v}u?rt(u,1,e):u=e}catch(t){rt(u||(u=new ot),2,t)}var o}(),u);return s&&s.then?s.then(e):e()})}catch(t){return Promise.reject(t)}};function vt(t,e){var n={current:t},r={current:e};return e.some(function(t,e){return t!==r.current[e]})&&(n.current=t,r.current=e),function(){return n.current.apply(n,[].slice.call(arguments))}}function ht(t){var e=(0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.useDispatch)("core/editor").editPost,n=(0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.useSelect)(function(t){return{previous:t("core/editor").getCurrentPostAttribute("meta"),current:t("core/editor").getEditedPostAttribute("meta")}},[]),r="string"==typeof t&&""!==t,o=r?n.current[t]:n.current,i=r?n.previous[t]:n.previous,a=vt(function(n){var o;r&&e({meta:(o={},o[t]=n,o)})},[e,t,r]),s=vt(function(t,n){var r;e({meta:(r={},r[t]=n,r)})},[e]);return r?[o,a,i]:[o,s,i]}function mt(t){var e,n,r=(0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.useDispatch)("core/editor").editPost,o=(0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.useSelect)(function(e){var n=e("core").getTaxonomy(t);return n?{taxonomy:n,current:e("core/editor").getEditedPostAttribute(n.rest_base),previous:e("core/editor").getCurrentPostAttribute(n.rest_base)}:{taxonomy:null,current:null,previous:null}},[t]),i=vt(function(t){try{var e;return Promise.resolve(o.taxonomy?r(((e={})[o.taxonomy.rest_base]=t,e)):void 0)}catch(t){return Promise.reject(t)}},[o,r]);return[null!=(e=o.current)?e:[],i,null!=(n=o.previous)?n:[]]}
//# sourceMappingURL=index.module.js.map


/***/ }),

/***/ 8792:
/*!******************************************************!*\
  !*** ./js/src/gutenberg/blocks/categories/block.tsx ***!
  \******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   block: () => (/* binding */ block),
/* harmony export */   name: () => (/* binding */ name),
/* harmony export */   settings: () => (/* binding */ settings)
/* harmony export */ });
/* harmony import */ var _globals_config__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../globals/config */ 5328);
/* harmony import */ var _Edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Edit */ 6383);
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../helpers */ 9440);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ 4833);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _CategoriesIcon__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./CategoriesIcon */ 6268);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! react/jsx-runtime */ 7157);






/**
 * Attributes specific to the widget as well as shared
 * widget attributes.
 *
 * @see \Advanced_Sidebar_Menu\Blocks\Block_Abstract::get_all_attributes
 * @see \Advanced_Sidebar_Menu\Blocks\Pages::get_attributes
 */

// Options used by basic when available from PRO.

/**
 * Attributes used for the example preview.
 * Combines some PRO and basic attributes.
 * The PRO attributes will only be sent if PRO is active.
 */
const EXAMPLE = {
  'display-posts': 'all',
  'display-posts/limit': 2,
  apply_current_page_parent_styles_to_parent: true,
  apply_current_page_styles_to_parent: true,
  block_style: true,
  border: true,
  border_color: '#333',
  bullet_style: 'none',
  child_page_bg_color: '#666',
  child_page_color: '#fff',
  parent_page_bg_color: '#282828',
  parent_page_color: '#0cc4c6',
  parent_page_font_weight: 'normal',
  display_all: true,
  grandchild_page_bg_color: '#989898',
  grandchild_page_color: '#282828',
  grandchild_page_font_weight: 'bold',
  include_childless_parent: true,
  include_parent: true,
  levels: 2
};
const block = _globals_config__WEBPACK_IMPORTED_MODULE_0__.CONFIG.blocks.categories;
const name = block.id;
const settings = {
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Advanced Sidebar - Categories', 'advanced-sidebar-menu'),
  icon: _CategoriesIcon__WEBPACK_IMPORTED_MODULE_4__["default"],
  category: 'widgets',
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Creates a menu of all the categories using the parent/child relationship', 'advanced-sidebar-menu'),
  keywords: [(0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('taxonomy', 'advanced-sidebar-menu'), (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('term', 'advanced-sidebar-menu'), (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('category', 'advanced-sidebar-menu'), (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('menu', 'advanced-sidebar-menu')],
  example: {
    attributes: EXAMPLE
  },
  transforms: {
    from: [{
      type: 'block',
      blocks: ['core/legacy-widget'],
      isMatch: ({
        idBase,
        instance
      }) => {
        if (null === instance?.raw) {
          // Can't transform if the raw instance is not shown in REST API.
          return false;
        }
        return 'advanced_sidebar_menu_category' === idBase;
      },
      transform: (0,_helpers__WEBPACK_IMPORTED_MODULE_2__.transformLegacyWidget)(name)
    }]
  },
  attributes: (0,_helpers__WEBPACK_IMPORTED_MODULE_2__.translateBlockAttributes)(block.attributes),
  supports: (0,_helpers__WEBPACK_IMPORTED_MODULE_2__.getBlockSupports)(),
  edit: props => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_Edit__WEBPACK_IMPORTED_MODULE_1__["default"], {
    ...props
  }),
  save: () => null,
  apiVersion: 3
};

/***/ }),

/***/ 8836:
/*!*************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/own-keys.js ***!
  \*************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var getBuiltIn = __webpack_require__(/*! ../internals/get-built-in */ 248);
var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ 1109);
var getOwnPropertyNamesModule = __webpack_require__(/*! ../internals/object-get-own-property-names */ 8265);
var getOwnPropertySymbolsModule = __webpack_require__(/*! ../internals/object-get-own-property-symbols */ 3192);
var anObject = __webpack_require__(/*! ../internals/an-object */ 1734);

var concat = uncurryThis([].concat);

// all object keys, includes non-enumerable and symbols
module.exports = getBuiltIn('Reflect', 'ownKeys') || function ownKeys(it) {
  var keys = getOwnPropertyNamesModule.f(anObject(it));
  var getOwnPropertySymbols = getOwnPropertySymbolsModule.f;
  return getOwnPropertySymbols ? concat(keys, getOwnPropertySymbols(it)) : keys;
};


/***/ }),

/***/ 9020:
/*!***********************************************!*\
  !*** ./js/src/components/TransformNotice.tsx ***!
  \***********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DISMISS_KEY: () => (/* binding */ DISMISS_KEY),
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ 4833);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react */ 1594);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ 6021);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _transform_notice_pcss__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./transform-notice.pcss */ 4711);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react/jsx-runtime */ 7157);





const DISMISS_KEY = 'advanced-sidebar-menu/transform-notice-dismissed';

/**
 * Display a dismissible notice above widget forms to inform
 * users the widget may be transformed into a block.
 *
 * Only applies to legacy widgets which should theoretically
 * be phased out in favor of blocks.
 * This notice is intended to help facilitate the transition.
 *
 */
const TransformNotice = ({}) => {
  const [shown, setShown] = (0,react__WEBPACK_IMPORTED_MODULE_1__.useState)(true);
  if (!shown) {
    return null;
  }
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Notice, {
    className: _transform_notice_pcss__WEBPACK_IMPORTED_MODULE_3__["default"].wrap,
    politeness: 'polite',
    onDismiss: () => {
      setShown(false);
      localStorage.setItem(DISMISS_KEY, '1');
    },
    actions: [{
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Blocks are the future of Advanced Sidebar Menu.', 'advanced-sidebar-menu'),
      url: 'https://onpointplugins.com/advanced-sidebar-menu/advanced-sidebar-menu-gutenberg-blocks/#transform-widgets-to-blocks',
      variant: 'link'
    }],
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("span", {
      className: "dashicons dashicons-arrow-up-alt"
    }), "\xA0", (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('This widget may be transformed into a block.', 'advanced-sidebar-menu')]
  });
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (TransformNotice);

/***/ }),

/***/ 9133:
/*!*************************************************************************************************************!*\
  !*** ./.yarn/cache/dompurify-npm-3.2.6-8d2a7542b7-c8f8e5b087.zip/node_modules/dompurify/dist/purify.es.mjs ***!
  \*************************************************************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ purify)
/* harmony export */ });
/*! @license DOMPurify 3.2.6 | (c) Cure53 and other contributors | Released under the Apache license 2.0 and Mozilla Public License 2.0 | github.com/cure53/DOMPurify/blob/3.2.6/LICENSE */

const {
  entries,
  setPrototypeOf,
  isFrozen,
  getPrototypeOf,
  getOwnPropertyDescriptor
} = Object;
let {
  freeze,
  seal,
  create
} = Object; // eslint-disable-line import/no-mutable-exports
let {
  apply,
  construct
} = typeof Reflect !== 'undefined' && Reflect;
if (!freeze) {
  freeze = function freeze(x) {
    return x;
  };
}
if (!seal) {
  seal = function seal(x) {
    return x;
  };
}
if (!apply) {
  apply = function apply(fun, thisValue, args) {
    return fun.apply(thisValue, args);
  };
}
if (!construct) {
  construct = function construct(Func, args) {
    return new Func(...args);
  };
}
const arrayForEach = unapply(Array.prototype.forEach);
const arrayLastIndexOf = unapply(Array.prototype.lastIndexOf);
const arrayPop = unapply(Array.prototype.pop);
const arrayPush = unapply(Array.prototype.push);
const arraySplice = unapply(Array.prototype.splice);
const stringToLowerCase = unapply(String.prototype.toLowerCase);
const stringToString = unapply(String.prototype.toString);
const stringMatch = unapply(String.prototype.match);
const stringReplace = unapply(String.prototype.replace);
const stringIndexOf = unapply(String.prototype.indexOf);
const stringTrim = unapply(String.prototype.trim);
const objectHasOwnProperty = unapply(Object.prototype.hasOwnProperty);
const regExpTest = unapply(RegExp.prototype.test);
const typeErrorCreate = unconstruct(TypeError);
/**
 * Creates a new function that calls the given function with a specified thisArg and arguments.
 *
 * @param func - The function to be wrapped and called.
 * @returns A new function that calls the given function with a specified thisArg and arguments.
 */
function unapply(func) {
  return function (thisArg) {
    if (thisArg instanceof RegExp) {
      thisArg.lastIndex = 0;
    }
    for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
      args[_key - 1] = arguments[_key];
    }
    return apply(func, thisArg, args);
  };
}
/**
 * Creates a new function that constructs an instance of the given constructor function with the provided arguments.
 *
 * @param func - The constructor function to be wrapped and called.
 * @returns A new function that constructs an instance of the given constructor function with the provided arguments.
 */
function unconstruct(func) {
  return function () {
    for (var _len2 = arguments.length, args = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
      args[_key2] = arguments[_key2];
    }
    return construct(func, args);
  };
}
/**
 * Add properties to a lookup table
 *
 * @param set - The set to which elements will be added.
 * @param array - The array containing elements to be added to the set.
 * @param transformCaseFunc - An optional function to transform the case of each element before adding to the set.
 * @returns The modified set with added elements.
 */
function addToSet(set, array) {
  let transformCaseFunc = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : stringToLowerCase;
  if (setPrototypeOf) {
    // Make 'in' and truthy checks like Boolean(set.constructor)
    // independent of any properties defined on Object.prototype.
    // Prevent prototype setters from intercepting set as a this value.
    setPrototypeOf(set, null);
  }
  let l = array.length;
  while (l--) {
    let element = array[l];
    if (typeof element === 'string') {
      const lcElement = transformCaseFunc(element);
      if (lcElement !== element) {
        // Config presets (e.g. tags.js, attrs.js) are immutable.
        if (!isFrozen(array)) {
          array[l] = lcElement;
        }
        element = lcElement;
      }
    }
    set[element] = true;
  }
  return set;
}
/**
 * Clean up an array to harden against CSPP
 *
 * @param array - The array to be cleaned.
 * @returns The cleaned version of the array
 */
function cleanArray(array) {
  for (let index = 0; index < array.length; index++) {
    const isPropertyExist = objectHasOwnProperty(array, index);
    if (!isPropertyExist) {
      array[index] = null;
    }
  }
  return array;
}
/**
 * Shallow clone an object
 *
 * @param object - The object to be cloned.
 * @returns A new object that copies the original.
 */
function clone(object) {
  const newObject = create(null);
  for (const [property, value] of entries(object)) {
    const isPropertyExist = objectHasOwnProperty(object, property);
    if (isPropertyExist) {
      if (Array.isArray(value)) {
        newObject[property] = cleanArray(value);
      } else if (value && typeof value === 'object' && value.constructor === Object) {
        newObject[property] = clone(value);
      } else {
        newObject[property] = value;
      }
    }
  }
  return newObject;
}
/**
 * This method automatically checks if the prop is function or getter and behaves accordingly.
 *
 * @param object - The object to look up the getter function in its prototype chain.
 * @param prop - The property name for which to find the getter function.
 * @returns The getter function found in the prototype chain or a fallback function.
 */
function lookupGetter(object, prop) {
  while (object !== null) {
    const desc = getOwnPropertyDescriptor(object, prop);
    if (desc) {
      if (desc.get) {
        return unapply(desc.get);
      }
      if (typeof desc.value === 'function') {
        return unapply(desc.value);
      }
    }
    object = getPrototypeOf(object);
  }
  function fallbackValue() {
    return null;
  }
  return fallbackValue;
}

const html$1 = freeze(['a', 'abbr', 'acronym', 'address', 'area', 'article', 'aside', 'audio', 'b', 'bdi', 'bdo', 'big', 'blink', 'blockquote', 'body', 'br', 'button', 'canvas', 'caption', 'center', 'cite', 'code', 'col', 'colgroup', 'content', 'data', 'datalist', 'dd', 'decorator', 'del', 'details', 'dfn', 'dialog', 'dir', 'div', 'dl', 'dt', 'element', 'em', 'fieldset', 'figcaption', 'figure', 'font', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'header', 'hgroup', 'hr', 'html', 'i', 'img', 'input', 'ins', 'kbd', 'label', 'legend', 'li', 'main', 'map', 'mark', 'marquee', 'menu', 'menuitem', 'meter', 'nav', 'nobr', 'ol', 'optgroup', 'option', 'output', 'p', 'picture', 'pre', 'progress', 'q', 'rp', 'rt', 'ruby', 's', 'samp', 'section', 'select', 'shadow', 'small', 'source', 'spacer', 'span', 'strike', 'strong', 'style', 'sub', 'summary', 'sup', 'table', 'tbody', 'td', 'template', 'textarea', 'tfoot', 'th', 'thead', 'time', 'tr', 'track', 'tt', 'u', 'ul', 'var', 'video', 'wbr']);
const svg$1 = freeze(['svg', 'a', 'altglyph', 'altglyphdef', 'altglyphitem', 'animatecolor', 'animatemotion', 'animatetransform', 'circle', 'clippath', 'defs', 'desc', 'ellipse', 'filter', 'font', 'g', 'glyph', 'glyphref', 'hkern', 'image', 'line', 'lineargradient', 'marker', 'mask', 'metadata', 'mpath', 'path', 'pattern', 'polygon', 'polyline', 'radialgradient', 'rect', 'stop', 'style', 'switch', 'symbol', 'text', 'textpath', 'title', 'tref', 'tspan', 'view', 'vkern']);
const svgFilters = freeze(['feBlend', 'feColorMatrix', 'feComponentTransfer', 'feComposite', 'feConvolveMatrix', 'feDiffuseLighting', 'feDisplacementMap', 'feDistantLight', 'feDropShadow', 'feFlood', 'feFuncA', 'feFuncB', 'feFuncG', 'feFuncR', 'feGaussianBlur', 'feImage', 'feMerge', 'feMergeNode', 'feMorphology', 'feOffset', 'fePointLight', 'feSpecularLighting', 'feSpotLight', 'feTile', 'feTurbulence']);
// List of SVG elements that are disallowed by default.
// We still need to know them so that we can do namespace
// checks properly in case one wants to add them to
// allow-list.
const svgDisallowed = freeze(['animate', 'color-profile', 'cursor', 'discard', 'font-face', 'font-face-format', 'font-face-name', 'font-face-src', 'font-face-uri', 'foreignobject', 'hatch', 'hatchpath', 'mesh', 'meshgradient', 'meshpatch', 'meshrow', 'missing-glyph', 'script', 'set', 'solidcolor', 'unknown', 'use']);
const mathMl$1 = freeze(['math', 'menclose', 'merror', 'mfenced', 'mfrac', 'mglyph', 'mi', 'mlabeledtr', 'mmultiscripts', 'mn', 'mo', 'mover', 'mpadded', 'mphantom', 'mroot', 'mrow', 'ms', 'mspace', 'msqrt', 'mstyle', 'msub', 'msup', 'msubsup', 'mtable', 'mtd', 'mtext', 'mtr', 'munder', 'munderover', 'mprescripts']);
// Similarly to SVG, we want to know all MathML elements,
// even those that we disallow by default.
const mathMlDisallowed = freeze(['maction', 'maligngroup', 'malignmark', 'mlongdiv', 'mscarries', 'mscarry', 'msgroup', 'mstack', 'msline', 'msrow', 'semantics', 'annotation', 'annotation-xml', 'mprescripts', 'none']);
const text = freeze(['#text']);

const html = freeze(['accept', 'action', 'align', 'alt', 'autocapitalize', 'autocomplete', 'autopictureinpicture', 'autoplay', 'background', 'bgcolor', 'border', 'capture', 'cellpadding', 'cellspacing', 'checked', 'cite', 'class', 'clear', 'color', 'cols', 'colspan', 'controls', 'controlslist', 'coords', 'crossorigin', 'datetime', 'decoding', 'default', 'dir', 'disabled', 'disablepictureinpicture', 'disableremoteplayback', 'download', 'draggable', 'enctype', 'enterkeyhint', 'face', 'for', 'headers', 'height', 'hidden', 'high', 'href', 'hreflang', 'id', 'inputmode', 'integrity', 'ismap', 'kind', 'label', 'lang', 'list', 'loading', 'loop', 'low', 'max', 'maxlength', 'media', 'method', 'min', 'minlength', 'multiple', 'muted', 'name', 'nonce', 'noshade', 'novalidate', 'nowrap', 'open', 'optimum', 'pattern', 'placeholder', 'playsinline', 'popover', 'popovertarget', 'popovertargetaction', 'poster', 'preload', 'pubdate', 'radiogroup', 'readonly', 'rel', 'required', 'rev', 'reversed', 'role', 'rows', 'rowspan', 'spellcheck', 'scope', 'selected', 'shape', 'size', 'sizes', 'span', 'srclang', 'start', 'src', 'srcset', 'step', 'style', 'summary', 'tabindex', 'title', 'translate', 'type', 'usemap', 'valign', 'value', 'width', 'wrap', 'xmlns', 'slot']);
const svg = freeze(['accent-height', 'accumulate', 'additive', 'alignment-baseline', 'amplitude', 'ascent', 'attributename', 'attributetype', 'azimuth', 'basefrequency', 'baseline-shift', 'begin', 'bias', 'by', 'class', 'clip', 'clippathunits', 'clip-path', 'clip-rule', 'color', 'color-interpolation', 'color-interpolation-filters', 'color-profile', 'color-rendering', 'cx', 'cy', 'd', 'dx', 'dy', 'diffuseconstant', 'direction', 'display', 'divisor', 'dur', 'edgemode', 'elevation', 'end', 'exponent', 'fill', 'fill-opacity', 'fill-rule', 'filter', 'filterunits', 'flood-color', 'flood-opacity', 'font-family', 'font-size', 'font-size-adjust', 'font-stretch', 'font-style', 'font-variant', 'font-weight', 'fx', 'fy', 'g1', 'g2', 'glyph-name', 'glyphref', 'gradientunits', 'gradienttransform', 'height', 'href', 'id', 'image-rendering', 'in', 'in2', 'intercept', 'k', 'k1', 'k2', 'k3', 'k4', 'kerning', 'keypoints', 'keysplines', 'keytimes', 'lang', 'lengthadjust', 'letter-spacing', 'kernelmatrix', 'kernelunitlength', 'lighting-color', 'local', 'marker-end', 'marker-mid', 'marker-start', 'markerheight', 'markerunits', 'markerwidth', 'maskcontentunits', 'maskunits', 'max', 'mask', 'media', 'method', 'mode', 'min', 'name', 'numoctaves', 'offset', 'operator', 'opacity', 'order', 'orient', 'orientation', 'origin', 'overflow', 'paint-order', 'path', 'pathlength', 'patterncontentunits', 'patterntransform', 'patternunits', 'points', 'preservealpha', 'preserveaspectratio', 'primitiveunits', 'r', 'rx', 'ry', 'radius', 'refx', 'refy', 'repeatcount', 'repeatdur', 'restart', 'result', 'rotate', 'scale', 'seed', 'shape-rendering', 'slope', 'specularconstant', 'specularexponent', 'spreadmethod', 'startoffset', 'stddeviation', 'stitchtiles', 'stop-color', 'stop-opacity', 'stroke-dasharray', 'stroke-dashoffset', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-opacity', 'stroke', 'stroke-width', 'style', 'surfacescale', 'systemlanguage', 'tabindex', 'tablevalues', 'targetx', 'targety', 'transform', 'transform-origin', 'text-anchor', 'text-decoration', 'text-rendering', 'textlength', 'type', 'u1', 'u2', 'unicode', 'values', 'viewbox', 'visibility', 'version', 'vert-adv-y', 'vert-origin-x', 'vert-origin-y', 'width', 'word-spacing', 'wrap', 'writing-mode', 'xchannelselector', 'ychannelselector', 'x', 'x1', 'x2', 'xmlns', 'y', 'y1', 'y2', 'z', 'zoomandpan']);
const mathMl = freeze(['accent', 'accentunder', 'align', 'bevelled', 'close', 'columnsalign', 'columnlines', 'columnspan', 'denomalign', 'depth', 'dir', 'display', 'displaystyle', 'encoding', 'fence', 'frame', 'height', 'href', 'id', 'largeop', 'length', 'linethickness', 'lspace', 'lquote', 'mathbackground', 'mathcolor', 'mathsize', 'mathvariant', 'maxsize', 'minsize', 'movablelimits', 'notation', 'numalign', 'open', 'rowalign', 'rowlines', 'rowspacing', 'rowspan', 'rspace', 'rquote', 'scriptlevel', 'scriptminsize', 'scriptsizemultiplier', 'selection', 'separator', 'separators', 'stretchy', 'subscriptshift', 'supscriptshift', 'symmetric', 'voffset', 'width', 'xmlns']);
const xml = freeze(['xlink:href', 'xml:id', 'xlink:title', 'xml:space', 'xmlns:xlink']);

// eslint-disable-next-line unicorn/better-regex
const MUSTACHE_EXPR = seal(/\{\{[\w\W]*|[\w\W]*\}\}/gm); // Specify template detection regex for SAFE_FOR_TEMPLATES mode
const ERB_EXPR = seal(/<%[\w\W]*|[\w\W]*%>/gm);
const TMPLIT_EXPR = seal(/\$\{[\w\W]*/gm); // eslint-disable-line unicorn/better-regex
const DATA_ATTR = seal(/^data-[\-\w.\u00B7-\uFFFF]+$/); // eslint-disable-line no-useless-escape
const ARIA_ATTR = seal(/^aria-[\-\w]+$/); // eslint-disable-line no-useless-escape
const IS_ALLOWED_URI = seal(/^(?:(?:(?:f|ht)tps?|mailto|tel|callto|sms|cid|xmpp|matrix):|[^a-z]|[a-z+.\-]+(?:[^a-z+.\-:]|$))/i // eslint-disable-line no-useless-escape
);
const IS_SCRIPT_OR_DATA = seal(/^(?:\w+script|data):/i);
const ATTR_WHITESPACE = seal(/[\u0000-\u0020\u00A0\u1680\u180E\u2000-\u2029\u205F\u3000]/g // eslint-disable-line no-control-regex
);
const DOCTYPE_NAME = seal(/^html$/i);
const CUSTOM_ELEMENT = seal(/^[a-z][.\w]*(-[.\w]+)+$/i);

var EXPRESSIONS = /*#__PURE__*/Object.freeze({
  __proto__: null,
  ARIA_ATTR: ARIA_ATTR,
  ATTR_WHITESPACE: ATTR_WHITESPACE,
  CUSTOM_ELEMENT: CUSTOM_ELEMENT,
  DATA_ATTR: DATA_ATTR,
  DOCTYPE_NAME: DOCTYPE_NAME,
  ERB_EXPR: ERB_EXPR,
  IS_ALLOWED_URI: IS_ALLOWED_URI,
  IS_SCRIPT_OR_DATA: IS_SCRIPT_OR_DATA,
  MUSTACHE_EXPR: MUSTACHE_EXPR,
  TMPLIT_EXPR: TMPLIT_EXPR
});

/* eslint-disable @typescript-eslint/indent */
// https://developer.mozilla.org/en-US/docs/Web/API/Node/nodeType
const NODE_TYPE = {
  element: 1,
  attribute: 2,
  text: 3,
  cdataSection: 4,
  entityReference: 5,
  // Deprecated
  entityNode: 6,
  // Deprecated
  progressingInstruction: 7,
  comment: 8,
  document: 9,
  documentType: 10,
  documentFragment: 11,
  notation: 12 // Deprecated
};
const getGlobal = function getGlobal() {
  return typeof window === 'undefined' ? null : window;
};
/**
 * Creates a no-op policy for internal use only.
 * Don't export this function outside this module!
 * @param trustedTypes The policy factory.
 * @param purifyHostElement The Script element used to load DOMPurify (to determine policy name suffix).
 * @return The policy created (or null, if Trusted Types
 * are not supported or creating the policy failed).
 */
const _createTrustedTypesPolicy = function _createTrustedTypesPolicy(trustedTypes, purifyHostElement) {
  if (typeof trustedTypes !== 'object' || typeof trustedTypes.createPolicy !== 'function') {
    return null;
  }
  // Allow the callers to control the unique policy name
  // by adding a data-tt-policy-suffix to the script element with the DOMPurify.
  // Policy creation with duplicate names throws in Trusted Types.
  let suffix = null;
  const ATTR_NAME = 'data-tt-policy-suffix';
  if (purifyHostElement && purifyHostElement.hasAttribute(ATTR_NAME)) {
    suffix = purifyHostElement.getAttribute(ATTR_NAME);
  }
  const policyName = 'dompurify' + (suffix ? '#' + suffix : '');
  try {
    return trustedTypes.createPolicy(policyName, {
      createHTML(html) {
        return html;
      },
      createScriptURL(scriptUrl) {
        return scriptUrl;
      }
    });
  } catch (_) {
    // Policy creation failed (most likely another DOMPurify script has
    // already run). Skip creating the policy, as this will only cause errors
    // if TT are enforced.
    console.warn('TrustedTypes policy ' + policyName + ' could not be created.');
    return null;
  }
};
const _createHooksMap = function _createHooksMap() {
  return {
    afterSanitizeAttributes: [],
    afterSanitizeElements: [],
    afterSanitizeShadowDOM: [],
    beforeSanitizeAttributes: [],
    beforeSanitizeElements: [],
    beforeSanitizeShadowDOM: [],
    uponSanitizeAttribute: [],
    uponSanitizeElement: [],
    uponSanitizeShadowNode: []
  };
};
function createDOMPurify() {
  let window = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : getGlobal();
  const DOMPurify = root => createDOMPurify(root);
  DOMPurify.version = '3.2.6';
  DOMPurify.removed = [];
  if (!window || !window.document || window.document.nodeType !== NODE_TYPE.document || !window.Element) {
    // Not running in a browser, provide a factory function
    // so that you can pass your own Window
    DOMPurify.isSupported = false;
    return DOMPurify;
  }
  let {
    document
  } = window;
  const originalDocument = document;
  const currentScript = originalDocument.currentScript;
  const {
    DocumentFragment,
    HTMLTemplateElement,
    Node,
    Element,
    NodeFilter,
    NamedNodeMap = window.NamedNodeMap || window.MozNamedAttrMap,
    HTMLFormElement,
    DOMParser,
    trustedTypes
  } = window;
  const ElementPrototype = Element.prototype;
  const cloneNode = lookupGetter(ElementPrototype, 'cloneNode');
  const remove = lookupGetter(ElementPrototype, 'remove');
  const getNextSibling = lookupGetter(ElementPrototype, 'nextSibling');
  const getChildNodes = lookupGetter(ElementPrototype, 'childNodes');
  const getParentNode = lookupGetter(ElementPrototype, 'parentNode');
  // As per issue #47, the web-components registry is inherited by a
  // new document created via createHTMLDocument. As per the spec
  // (http://w3c.github.io/webcomponents/spec/custom/#creating-and-passing-registries)
  // a new empty registry is used when creating a template contents owner
  // document, so we use that as our parent document to ensure nothing
  // is inherited.
  if (typeof HTMLTemplateElement === 'function') {
    const template = document.createElement('template');
    if (template.content && template.content.ownerDocument) {
      document = template.content.ownerDocument;
    }
  }
  let trustedTypesPolicy;
  let emptyHTML = '';
  const {
    implementation,
    createNodeIterator,
    createDocumentFragment,
    getElementsByTagName
  } = document;
  const {
    importNode
  } = originalDocument;
  let hooks = _createHooksMap();
  /**
   * Expose whether this browser supports running the full DOMPurify.
   */
  DOMPurify.isSupported = typeof entries === 'function' && typeof getParentNode === 'function' && implementation && implementation.createHTMLDocument !== undefined;
  const {
    MUSTACHE_EXPR,
    ERB_EXPR,
    TMPLIT_EXPR,
    DATA_ATTR,
    ARIA_ATTR,
    IS_SCRIPT_OR_DATA,
    ATTR_WHITESPACE,
    CUSTOM_ELEMENT
  } = EXPRESSIONS;
  let {
    IS_ALLOWED_URI: IS_ALLOWED_URI$1
  } = EXPRESSIONS;
  /**
   * We consider the elements and attributes below to be safe. Ideally
   * don't add any new ones but feel free to remove unwanted ones.
   */
  /* allowed element names */
  let ALLOWED_TAGS = null;
  const DEFAULT_ALLOWED_TAGS = addToSet({}, [...html$1, ...svg$1, ...svgFilters, ...mathMl$1, ...text]);
  /* Allowed attribute names */
  let ALLOWED_ATTR = null;
  const DEFAULT_ALLOWED_ATTR = addToSet({}, [...html, ...svg, ...mathMl, ...xml]);
  /*
   * Configure how DOMPurify should handle custom elements and their attributes as well as customized built-in elements.
   * @property {RegExp|Function|null} tagNameCheck one of [null, regexPattern, predicate]. Default: `null` (disallow any custom elements)
   * @property {RegExp|Function|null} attributeNameCheck one of [null, regexPattern, predicate]. Default: `null` (disallow any attributes not on the allow list)
   * @property {boolean} allowCustomizedBuiltInElements allow custom elements derived from built-ins if they pass CUSTOM_ELEMENT_HANDLING.tagNameCheck. Default: `false`.
   */
  let CUSTOM_ELEMENT_HANDLING = Object.seal(create(null, {
    tagNameCheck: {
      writable: true,
      configurable: false,
      enumerable: true,
      value: null
    },
    attributeNameCheck: {
      writable: true,
      configurable: false,
      enumerable: true,
      value: null
    },
    allowCustomizedBuiltInElements: {
      writable: true,
      configurable: false,
      enumerable: true,
      value: false
    }
  }));
  /* Explicitly forbidden tags (overrides ALLOWED_TAGS/ADD_TAGS) */
  let FORBID_TAGS = null;
  /* Explicitly forbidden attributes (overrides ALLOWED_ATTR/ADD_ATTR) */
  let FORBID_ATTR = null;
  /* Decide if ARIA attributes are okay */
  let ALLOW_ARIA_ATTR = true;
  /* Decide if custom data attributes are okay */
  let ALLOW_DATA_ATTR = true;
  /* Decide if unknown protocols are okay */
  let ALLOW_UNKNOWN_PROTOCOLS = false;
  /* Decide if self-closing tags in attributes are allowed.
   * Usually removed due to a mXSS issue in jQuery 3.0 */
  let ALLOW_SELF_CLOSE_IN_ATTR = true;
  /* Output should be safe for common template engines.
   * This means, DOMPurify removes data attributes, mustaches and ERB
   */
  let SAFE_FOR_TEMPLATES = false;
  /* Output should be safe even for XML used within HTML and alike.
   * This means, DOMPurify removes comments when containing risky content.
   */
  let SAFE_FOR_XML = true;
  /* Decide if document with <html>... should be returned */
  let WHOLE_DOCUMENT = false;
  /* Track whether config is already set on this instance of DOMPurify. */
  let SET_CONFIG = false;
  /* Decide if all elements (e.g. style, script) must be children of
   * document.body. By default, browsers might move them to document.head */
  let FORCE_BODY = false;
  /* Decide if a DOM `HTMLBodyElement` should be returned, instead of a html
   * string (or a TrustedHTML object if Trusted Types are supported).
   * If `WHOLE_DOCUMENT` is enabled a `HTMLHtmlElement` will be returned instead
   */
  let RETURN_DOM = false;
  /* Decide if a DOM `DocumentFragment` should be returned, instead of a html
   * string  (or a TrustedHTML object if Trusted Types are supported) */
  let RETURN_DOM_FRAGMENT = false;
  /* Try to return a Trusted Type object instead of a string, return a string in
   * case Trusted Types are not supported  */
  let RETURN_TRUSTED_TYPE = false;
  /* Output should be free from DOM clobbering attacks?
   * This sanitizes markups named with colliding, clobberable built-in DOM APIs.
   */
  let SANITIZE_DOM = true;
  /* Achieve full DOM Clobbering protection by isolating the namespace of named
   * properties and JS variables, mitigating attacks that abuse the HTML/DOM spec rules.
   *
   * HTML/DOM spec rules that enable DOM Clobbering:
   *   - Named Access on Window (§7.3.3)
   *   - DOM Tree Accessors (§3.1.5)
   *   - Form Element Parent-Child Relations (§4.10.3)
   *   - Iframe srcdoc / Nested WindowProxies (§4.8.5)
   *   - HTMLCollection (§4.2.10.2)
   *
   * Namespace isolation is implemented by prefixing `id` and `name` attributes
   * with a constant string, i.e., `user-content-`
   */
  let SANITIZE_NAMED_PROPS = false;
  const SANITIZE_NAMED_PROPS_PREFIX = 'user-content-';
  /* Keep element content when removing element? */
  let KEEP_CONTENT = true;
  /* If a `Node` is passed to sanitize(), then performs sanitization in-place instead
   * of importing it into a new Document and returning a sanitized copy */
  let IN_PLACE = false;
  /* Allow usage of profiles like html, svg and mathMl */
  let USE_PROFILES = {};
  /* Tags to ignore content of when KEEP_CONTENT is true */
  let FORBID_CONTENTS = null;
  const DEFAULT_FORBID_CONTENTS = addToSet({}, ['annotation-xml', 'audio', 'colgroup', 'desc', 'foreignobject', 'head', 'iframe', 'math', 'mi', 'mn', 'mo', 'ms', 'mtext', 'noembed', 'noframes', 'noscript', 'plaintext', 'script', 'style', 'svg', 'template', 'thead', 'title', 'video', 'xmp']);
  /* Tags that are safe for data: URIs */
  let DATA_URI_TAGS = null;
  const DEFAULT_DATA_URI_TAGS = addToSet({}, ['audio', 'video', 'img', 'source', 'image', 'track']);
  /* Attributes safe for values like "javascript:" */
  let URI_SAFE_ATTRIBUTES = null;
  const DEFAULT_URI_SAFE_ATTRIBUTES = addToSet({}, ['alt', 'class', 'for', 'id', 'label', 'name', 'pattern', 'placeholder', 'role', 'summary', 'title', 'value', 'style', 'xmlns']);
  const MATHML_NAMESPACE = 'http://www.w3.org/1998/Math/MathML';
  const SVG_NAMESPACE = 'http://www.w3.org/2000/svg';
  const HTML_NAMESPACE = 'http://www.w3.org/1999/xhtml';
  /* Document namespace */
  let NAMESPACE = HTML_NAMESPACE;
  let IS_EMPTY_INPUT = false;
  /* Allowed XHTML+XML namespaces */
  let ALLOWED_NAMESPACES = null;
  const DEFAULT_ALLOWED_NAMESPACES = addToSet({}, [MATHML_NAMESPACE, SVG_NAMESPACE, HTML_NAMESPACE], stringToString);
  let MATHML_TEXT_INTEGRATION_POINTS = addToSet({}, ['mi', 'mo', 'mn', 'ms', 'mtext']);
  let HTML_INTEGRATION_POINTS = addToSet({}, ['annotation-xml']);
  // Certain elements are allowed in both SVG and HTML
  // namespace. We need to specify them explicitly
  // so that they don't get erroneously deleted from
  // HTML namespace.
  const COMMON_SVG_AND_HTML_ELEMENTS = addToSet({}, ['title', 'style', 'font', 'a', 'script']);
  /* Parsing of strict XHTML documents */
  let PARSER_MEDIA_TYPE = null;
  const SUPPORTED_PARSER_MEDIA_TYPES = ['application/xhtml+xml', 'text/html'];
  const DEFAULT_PARSER_MEDIA_TYPE = 'text/html';
  let transformCaseFunc = null;
  /* Keep a reference to config to pass to hooks */
  let CONFIG = null;
  /* Ideally, do not touch anything below this line */
  /* ______________________________________________ */
  const formElement = document.createElement('form');
  const isRegexOrFunction = function isRegexOrFunction(testValue) {
    return testValue instanceof RegExp || testValue instanceof Function;
  };
  /**
   * _parseConfig
   *
   * @param cfg optional config literal
   */
  // eslint-disable-next-line complexity
  const _parseConfig = function _parseConfig() {
    let cfg = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    if (CONFIG && CONFIG === cfg) {
      return;
    }
    /* Shield configuration object from tampering */
    if (!cfg || typeof cfg !== 'object') {
      cfg = {};
    }
    /* Shield configuration object from prototype pollution */
    cfg = clone(cfg);
    PARSER_MEDIA_TYPE =
    // eslint-disable-next-line unicorn/prefer-includes
    SUPPORTED_PARSER_MEDIA_TYPES.indexOf(cfg.PARSER_MEDIA_TYPE) === -1 ? DEFAULT_PARSER_MEDIA_TYPE : cfg.PARSER_MEDIA_TYPE;
    // HTML tags and attributes are not case-sensitive, converting to lowercase. Keeping XHTML as is.
    transformCaseFunc = PARSER_MEDIA_TYPE === 'application/xhtml+xml' ? stringToString : stringToLowerCase;
    /* Set configuration parameters */
    ALLOWED_TAGS = objectHasOwnProperty(cfg, 'ALLOWED_TAGS') ? addToSet({}, cfg.ALLOWED_TAGS, transformCaseFunc) : DEFAULT_ALLOWED_TAGS;
    ALLOWED_ATTR = objectHasOwnProperty(cfg, 'ALLOWED_ATTR') ? addToSet({}, cfg.ALLOWED_ATTR, transformCaseFunc) : DEFAULT_ALLOWED_ATTR;
    ALLOWED_NAMESPACES = objectHasOwnProperty(cfg, 'ALLOWED_NAMESPACES') ? addToSet({}, cfg.ALLOWED_NAMESPACES, stringToString) : DEFAULT_ALLOWED_NAMESPACES;
    URI_SAFE_ATTRIBUTES = objectHasOwnProperty(cfg, 'ADD_URI_SAFE_ATTR') ? addToSet(clone(DEFAULT_URI_SAFE_ATTRIBUTES), cfg.ADD_URI_SAFE_ATTR, transformCaseFunc) : DEFAULT_URI_SAFE_ATTRIBUTES;
    DATA_URI_TAGS = objectHasOwnProperty(cfg, 'ADD_DATA_URI_TAGS') ? addToSet(clone(DEFAULT_DATA_URI_TAGS), cfg.ADD_DATA_URI_TAGS, transformCaseFunc) : DEFAULT_DATA_URI_TAGS;
    FORBID_CONTENTS = objectHasOwnProperty(cfg, 'FORBID_CONTENTS') ? addToSet({}, cfg.FORBID_CONTENTS, transformCaseFunc) : DEFAULT_FORBID_CONTENTS;
    FORBID_TAGS = objectHasOwnProperty(cfg, 'FORBID_TAGS') ? addToSet({}, cfg.FORBID_TAGS, transformCaseFunc) : clone({});
    FORBID_ATTR = objectHasOwnProperty(cfg, 'FORBID_ATTR') ? addToSet({}, cfg.FORBID_ATTR, transformCaseFunc) : clone({});
    USE_PROFILES = objectHasOwnProperty(cfg, 'USE_PROFILES') ? cfg.USE_PROFILES : false;
    ALLOW_ARIA_ATTR = cfg.ALLOW_ARIA_ATTR !== false; // Default true
    ALLOW_DATA_ATTR = cfg.ALLOW_DATA_ATTR !== false; // Default true
    ALLOW_UNKNOWN_PROTOCOLS = cfg.ALLOW_UNKNOWN_PROTOCOLS || false; // Default false
    ALLOW_SELF_CLOSE_IN_ATTR = cfg.ALLOW_SELF_CLOSE_IN_ATTR !== false; // Default true
    SAFE_FOR_TEMPLATES = cfg.SAFE_FOR_TEMPLATES || false; // Default false
    SAFE_FOR_XML = cfg.SAFE_FOR_XML !== false; // Default true
    WHOLE_DOCUMENT = cfg.WHOLE_DOCUMENT || false; // Default false
    RETURN_DOM = cfg.RETURN_DOM || false; // Default false
    RETURN_DOM_FRAGMENT = cfg.RETURN_DOM_FRAGMENT || false; // Default false
    RETURN_TRUSTED_TYPE = cfg.RETURN_TRUSTED_TYPE || false; // Default false
    FORCE_BODY = cfg.FORCE_BODY || false; // Default false
    SANITIZE_DOM = cfg.SANITIZE_DOM !== false; // Default true
    SANITIZE_NAMED_PROPS = cfg.SANITIZE_NAMED_PROPS || false; // Default false
    KEEP_CONTENT = cfg.KEEP_CONTENT !== false; // Default true
    IN_PLACE = cfg.IN_PLACE || false; // Default false
    IS_ALLOWED_URI$1 = cfg.ALLOWED_URI_REGEXP || IS_ALLOWED_URI;
    NAMESPACE = cfg.NAMESPACE || HTML_NAMESPACE;
    MATHML_TEXT_INTEGRATION_POINTS = cfg.MATHML_TEXT_INTEGRATION_POINTS || MATHML_TEXT_INTEGRATION_POINTS;
    HTML_INTEGRATION_POINTS = cfg.HTML_INTEGRATION_POINTS || HTML_INTEGRATION_POINTS;
    CUSTOM_ELEMENT_HANDLING = cfg.CUSTOM_ELEMENT_HANDLING || {};
    if (cfg.CUSTOM_ELEMENT_HANDLING && isRegexOrFunction(cfg.CUSTOM_ELEMENT_HANDLING.tagNameCheck)) {
      CUSTOM_ELEMENT_HANDLING.tagNameCheck = cfg.CUSTOM_ELEMENT_HANDLING.tagNameCheck;
    }
    if (cfg.CUSTOM_ELEMENT_HANDLING && isRegexOrFunction(cfg.CUSTOM_ELEMENT_HANDLING.attributeNameCheck)) {
      CUSTOM_ELEMENT_HANDLING.attributeNameCheck = cfg.CUSTOM_ELEMENT_HANDLING.attributeNameCheck;
    }
    if (cfg.CUSTOM_ELEMENT_HANDLING && typeof cfg.CUSTOM_ELEMENT_HANDLING.allowCustomizedBuiltInElements === 'boolean') {
      CUSTOM_ELEMENT_HANDLING.allowCustomizedBuiltInElements = cfg.CUSTOM_ELEMENT_HANDLING.allowCustomizedBuiltInElements;
    }
    if (SAFE_FOR_TEMPLATES) {
      ALLOW_DATA_ATTR = false;
    }
    if (RETURN_DOM_FRAGMENT) {
      RETURN_DOM = true;
    }
    /* Parse profile info */
    if (USE_PROFILES) {
      ALLOWED_TAGS = addToSet({}, text);
      ALLOWED_ATTR = [];
      if (USE_PROFILES.html === true) {
        addToSet(ALLOWED_TAGS, html$1);
        addToSet(ALLOWED_ATTR, html);
      }
      if (USE_PROFILES.svg === true) {
        addToSet(ALLOWED_TAGS, svg$1);
        addToSet(ALLOWED_ATTR, svg);
        addToSet(ALLOWED_ATTR, xml);
      }
      if (USE_PROFILES.svgFilters === true) {
        addToSet(ALLOWED_TAGS, svgFilters);
        addToSet(ALLOWED_ATTR, svg);
        addToSet(ALLOWED_ATTR, xml);
      }
      if (USE_PROFILES.mathMl === true) {
        addToSet(ALLOWED_TAGS, mathMl$1);
        addToSet(ALLOWED_ATTR, mathMl);
        addToSet(ALLOWED_ATTR, xml);
      }
    }
    /* Merge configuration parameters */
    if (cfg.ADD_TAGS) {
      if (ALLOWED_TAGS === DEFAULT_ALLOWED_TAGS) {
        ALLOWED_TAGS = clone(ALLOWED_TAGS);
      }
      addToSet(ALLOWED_TAGS, cfg.ADD_TAGS, transformCaseFunc);
    }
    if (cfg.ADD_ATTR) {
      if (ALLOWED_ATTR === DEFAULT_ALLOWED_ATTR) {
        ALLOWED_ATTR = clone(ALLOWED_ATTR);
      }
      addToSet(ALLOWED_ATTR, cfg.ADD_ATTR, transformCaseFunc);
    }
    if (cfg.ADD_URI_SAFE_ATTR) {
      addToSet(URI_SAFE_ATTRIBUTES, cfg.ADD_URI_SAFE_ATTR, transformCaseFunc);
    }
    if (cfg.FORBID_CONTENTS) {
      if (FORBID_CONTENTS === DEFAULT_FORBID_CONTENTS) {
        FORBID_CONTENTS = clone(FORBID_CONTENTS);
      }
      addToSet(FORBID_CONTENTS, cfg.FORBID_CONTENTS, transformCaseFunc);
    }
    /* Add #text in case KEEP_CONTENT is set to true */
    if (KEEP_CONTENT) {
      ALLOWED_TAGS['#text'] = true;
    }
    /* Add html, head and body to ALLOWED_TAGS in case WHOLE_DOCUMENT is true */
    if (WHOLE_DOCUMENT) {
      addToSet(ALLOWED_TAGS, ['html', 'head', 'body']);
    }
    /* Add tbody to ALLOWED_TAGS in case tables are permitted, see #286, #365 */
    if (ALLOWED_TAGS.table) {
      addToSet(ALLOWED_TAGS, ['tbody']);
      delete FORBID_TAGS.tbody;
    }
    if (cfg.TRUSTED_TYPES_POLICY) {
      if (typeof cfg.TRUSTED_TYPES_POLICY.createHTML !== 'function') {
        throw typeErrorCreate('TRUSTED_TYPES_POLICY configuration option must provide a "createHTML" hook.');
      }
      if (typeof cfg.TRUSTED_TYPES_POLICY.createScriptURL !== 'function') {
        throw typeErrorCreate('TRUSTED_TYPES_POLICY configuration option must provide a "createScriptURL" hook.');
      }
      // Overwrite existing TrustedTypes policy.
      trustedTypesPolicy = cfg.TRUSTED_TYPES_POLICY;
      // Sign local variables required by `sanitize`.
      emptyHTML = trustedTypesPolicy.createHTML('');
    } else {
      // Uninitialized policy, attempt to initialize the internal dompurify policy.
      if (trustedTypesPolicy === undefined) {
        trustedTypesPolicy = _createTrustedTypesPolicy(trustedTypes, currentScript);
      }
      // If creating the internal policy succeeded sign internal variables.
      if (trustedTypesPolicy !== null && typeof emptyHTML === 'string') {
        emptyHTML = trustedTypesPolicy.createHTML('');
      }
    }
    // Prevent further manipulation of configuration.
    // Not available in IE8, Safari 5, etc.
    if (freeze) {
      freeze(cfg);
    }
    CONFIG = cfg;
  };
  /* Keep track of all possible SVG and MathML tags
   * so that we can perform the namespace checks
   * correctly. */
  const ALL_SVG_TAGS = addToSet({}, [...svg$1, ...svgFilters, ...svgDisallowed]);
  const ALL_MATHML_TAGS = addToSet({}, [...mathMl$1, ...mathMlDisallowed]);
  /**
   * @param element a DOM element whose namespace is being checked
   * @returns Return false if the element has a
   *  namespace that a spec-compliant parser would never
   *  return. Return true otherwise.
   */
  const _checkValidNamespace = function _checkValidNamespace(element) {
    let parent = getParentNode(element);
    // In JSDOM, if we're inside shadow DOM, then parentNode
    // can be null. We just simulate parent in this case.
    if (!parent || !parent.tagName) {
      parent = {
        namespaceURI: NAMESPACE,
        tagName: 'template'
      };
    }
    const tagName = stringToLowerCase(element.tagName);
    const parentTagName = stringToLowerCase(parent.tagName);
    if (!ALLOWED_NAMESPACES[element.namespaceURI]) {
      return false;
    }
    if (element.namespaceURI === SVG_NAMESPACE) {
      // The only way to switch from HTML namespace to SVG
      // is via <svg>. If it happens via any other tag, then
      // it should be killed.
      if (parent.namespaceURI === HTML_NAMESPACE) {
        return tagName === 'svg';
      }
      // The only way to switch from MathML to SVG is via`
      // svg if parent is either <annotation-xml> or MathML
      // text integration points.
      if (parent.namespaceURI === MATHML_NAMESPACE) {
        return tagName === 'svg' && (parentTagName === 'annotation-xml' || MATHML_TEXT_INTEGRATION_POINTS[parentTagName]);
      }
      // We only allow elements that are defined in SVG
      // spec. All others are disallowed in SVG namespace.
      return Boolean(ALL_SVG_TAGS[tagName]);
    }
    if (element.namespaceURI === MATHML_NAMESPACE) {
      // The only way to switch from HTML namespace to MathML
      // is via <math>. If it happens via any other tag, then
      // it should be killed.
      if (parent.namespaceURI === HTML_NAMESPACE) {
        return tagName === 'math';
      }
      // The only way to switch from SVG to MathML is via
      // <math> and HTML integration points
      if (parent.namespaceURI === SVG_NAMESPACE) {
        return tagName === 'math' && HTML_INTEGRATION_POINTS[parentTagName];
      }
      // We only allow elements that are defined in MathML
      // spec. All others are disallowed in MathML namespace.
      return Boolean(ALL_MATHML_TAGS[tagName]);
    }
    if (element.namespaceURI === HTML_NAMESPACE) {
      // The only way to switch from SVG to HTML is via
      // HTML integration points, and from MathML to HTML
      // is via MathML text integration points
      if (parent.namespaceURI === SVG_NAMESPACE && !HTML_INTEGRATION_POINTS[parentTagName]) {
        return false;
      }
      if (parent.namespaceURI === MATHML_NAMESPACE && !MATHML_TEXT_INTEGRATION_POINTS[parentTagName]) {
        return false;
      }
      // We disallow tags that are specific for MathML
      // or SVG and should never appear in HTML namespace
      return !ALL_MATHML_TAGS[tagName] && (COMMON_SVG_AND_HTML_ELEMENTS[tagName] || !ALL_SVG_TAGS[tagName]);
    }
    // For XHTML and XML documents that support custom namespaces
    if (PARSER_MEDIA_TYPE === 'application/xhtml+xml' && ALLOWED_NAMESPACES[element.namespaceURI]) {
      return true;
    }
    // The code should never reach this place (this means
    // that the element somehow got namespace that is not
    // HTML, SVG, MathML or allowed via ALLOWED_NAMESPACES).
    // Return false just in case.
    return false;
  };
  /**
   * _forceRemove
   *
   * @param node a DOM node
   */
  const _forceRemove = function _forceRemove(node) {
    arrayPush(DOMPurify.removed, {
      element: node
    });
    try {
      // eslint-disable-next-line unicorn/prefer-dom-node-remove
      getParentNode(node).removeChild(node);
    } catch (_) {
      remove(node);
    }
  };
  /**
   * _removeAttribute
   *
   * @param name an Attribute name
   * @param element a DOM node
   */
  const _removeAttribute = function _removeAttribute(name, element) {
    try {
      arrayPush(DOMPurify.removed, {
        attribute: element.getAttributeNode(name),
        from: element
      });
    } catch (_) {
      arrayPush(DOMPurify.removed, {
        attribute: null,
        from: element
      });
    }
    element.removeAttribute(name);
    // We void attribute values for unremovable "is" attributes
    if (name === 'is') {
      if (RETURN_DOM || RETURN_DOM_FRAGMENT) {
        try {
          _forceRemove(element);
        } catch (_) {}
      } else {
        try {
          element.setAttribute(name, '');
        } catch (_) {}
      }
    }
  };
  /**
   * _initDocument
   *
   * @param dirty - a string of dirty markup
   * @return a DOM, filled with the dirty markup
   */
  const _initDocument = function _initDocument(dirty) {
    /* Create a HTML document */
    let doc = null;
    let leadingWhitespace = null;
    if (FORCE_BODY) {
      dirty = '<remove></remove>' + dirty;
    } else {
      /* If FORCE_BODY isn't used, leading whitespace needs to be preserved manually */
      const matches = stringMatch(dirty, /^[\r\n\t ]+/);
      leadingWhitespace = matches && matches[0];
    }
    if (PARSER_MEDIA_TYPE === 'application/xhtml+xml' && NAMESPACE === HTML_NAMESPACE) {
      // Root of XHTML doc must contain xmlns declaration (see https://www.w3.org/TR/xhtml1/normative.html#strict)
      dirty = '<html xmlns="http://www.w3.org/1999/xhtml"><head></head><body>' + dirty + '</body></html>';
    }
    const dirtyPayload = trustedTypesPolicy ? trustedTypesPolicy.createHTML(dirty) : dirty;
    /*
     * Use the DOMParser API by default, fallback later if needs be
     * DOMParser not work for svg when has multiple root element.
     */
    if (NAMESPACE === HTML_NAMESPACE) {
      try {
        doc = new DOMParser().parseFromString(dirtyPayload, PARSER_MEDIA_TYPE);
      } catch (_) {}
    }
    /* Use createHTMLDocument in case DOMParser is not available */
    if (!doc || !doc.documentElement) {
      doc = implementation.createDocument(NAMESPACE, 'template', null);
      try {
        doc.documentElement.innerHTML = IS_EMPTY_INPUT ? emptyHTML : dirtyPayload;
      } catch (_) {
        // Syntax error if dirtyPayload is invalid xml
      }
    }
    const body = doc.body || doc.documentElement;
    if (dirty && leadingWhitespace) {
      body.insertBefore(document.createTextNode(leadingWhitespace), body.childNodes[0] || null);
    }
    /* Work on whole document or just its body */
    if (NAMESPACE === HTML_NAMESPACE) {
      return getElementsByTagName.call(doc, WHOLE_DOCUMENT ? 'html' : 'body')[0];
    }
    return WHOLE_DOCUMENT ? doc.documentElement : body;
  };
  /**
   * Creates a NodeIterator object that you can use to traverse filtered lists of nodes or elements in a document.
   *
   * @param root The root element or node to start traversing on.
   * @return The created NodeIterator
   */
  const _createNodeIterator = function _createNodeIterator(root) {
    return createNodeIterator.call(root.ownerDocument || root, root,
    // eslint-disable-next-line no-bitwise
    NodeFilter.SHOW_ELEMENT | NodeFilter.SHOW_COMMENT | NodeFilter.SHOW_TEXT | NodeFilter.SHOW_PROCESSING_INSTRUCTION | NodeFilter.SHOW_CDATA_SECTION, null);
  };
  /**
   * _isClobbered
   *
   * @param element element to check for clobbering attacks
   * @return true if clobbered, false if safe
   */
  const _isClobbered = function _isClobbered(element) {
    return element instanceof HTMLFormElement && (typeof element.nodeName !== 'string' || typeof element.textContent !== 'string' || typeof element.removeChild !== 'function' || !(element.attributes instanceof NamedNodeMap) || typeof element.removeAttribute !== 'function' || typeof element.setAttribute !== 'function' || typeof element.namespaceURI !== 'string' || typeof element.insertBefore !== 'function' || typeof element.hasChildNodes !== 'function');
  };
  /**
   * Checks whether the given object is a DOM node.
   *
   * @param value object to check whether it's a DOM node
   * @return true is object is a DOM node
   */
  const _isNode = function _isNode(value) {
    return typeof Node === 'function' && value instanceof Node;
  };
  function _executeHooks(hooks, currentNode, data) {
    arrayForEach(hooks, hook => {
      hook.call(DOMPurify, currentNode, data, CONFIG);
    });
  }
  /**
   * _sanitizeElements
   *
   * @protect nodeName
   * @protect textContent
   * @protect removeChild
   * @param currentNode to check for permission to exist
   * @return true if node was killed, false if left alive
   */
  const _sanitizeElements = function _sanitizeElements(currentNode) {
    let content = null;
    /* Execute a hook if present */
    _executeHooks(hooks.beforeSanitizeElements, currentNode, null);
    /* Check if element is clobbered or can clobber */
    if (_isClobbered(currentNode)) {
      _forceRemove(currentNode);
      return true;
    }
    /* Now let's check the element's type and name */
    const tagName = transformCaseFunc(currentNode.nodeName);
    /* Execute a hook if present */
    _executeHooks(hooks.uponSanitizeElement, currentNode, {
      tagName,
      allowedTags: ALLOWED_TAGS
    });
    /* Detect mXSS attempts abusing namespace confusion */
    if (SAFE_FOR_XML && currentNode.hasChildNodes() && !_isNode(currentNode.firstElementChild) && regExpTest(/<[/\w!]/g, currentNode.innerHTML) && regExpTest(/<[/\w!]/g, currentNode.textContent)) {
      _forceRemove(currentNode);
      return true;
    }
    /* Remove any occurrence of processing instructions */
    if (currentNode.nodeType === NODE_TYPE.progressingInstruction) {
      _forceRemove(currentNode);
      return true;
    }
    /* Remove any kind of possibly harmful comments */
    if (SAFE_FOR_XML && currentNode.nodeType === NODE_TYPE.comment && regExpTest(/<[/\w]/g, currentNode.data)) {
      _forceRemove(currentNode);
      return true;
    }
    /* Remove element if anything forbids its presence */
    if (!ALLOWED_TAGS[tagName] || FORBID_TAGS[tagName]) {
      /* Check if we have a custom element to handle */
      if (!FORBID_TAGS[tagName] && _isBasicCustomElement(tagName)) {
        if (CUSTOM_ELEMENT_HANDLING.tagNameCheck instanceof RegExp && regExpTest(CUSTOM_ELEMENT_HANDLING.tagNameCheck, tagName)) {
          return false;
        }
        if (CUSTOM_ELEMENT_HANDLING.tagNameCheck instanceof Function && CUSTOM_ELEMENT_HANDLING.tagNameCheck(tagName)) {
          return false;
        }
      }
      /* Keep content except for bad-listed elements */
      if (KEEP_CONTENT && !FORBID_CONTENTS[tagName]) {
        const parentNode = getParentNode(currentNode) || currentNode.parentNode;
        const childNodes = getChildNodes(currentNode) || currentNode.childNodes;
        if (childNodes && parentNode) {
          const childCount = childNodes.length;
          for (let i = childCount - 1; i >= 0; --i) {
            const childClone = cloneNode(childNodes[i], true);
            childClone.__removalCount = (currentNode.__removalCount || 0) + 1;
            parentNode.insertBefore(childClone, getNextSibling(currentNode));
          }
        }
      }
      _forceRemove(currentNode);
      return true;
    }
    /* Check whether element has a valid namespace */
    if (currentNode instanceof Element && !_checkValidNamespace(currentNode)) {
      _forceRemove(currentNode);
      return true;
    }
    /* Make sure that older browsers don't get fallback-tag mXSS */
    if ((tagName === 'noscript' || tagName === 'noembed' || tagName === 'noframes') && regExpTest(/<\/no(script|embed|frames)/i, currentNode.innerHTML)) {
      _forceRemove(currentNode);
      return true;
    }
    /* Sanitize element content to be template-safe */
    if (SAFE_FOR_TEMPLATES && currentNode.nodeType === NODE_TYPE.text) {
      /* Get the element's text content */
      content = currentNode.textContent;
      arrayForEach([MUSTACHE_EXPR, ERB_EXPR, TMPLIT_EXPR], expr => {
        content = stringReplace(content, expr, ' ');
      });
      if (currentNode.textContent !== content) {
        arrayPush(DOMPurify.removed, {
          element: currentNode.cloneNode()
        });
        currentNode.textContent = content;
      }
    }
    /* Execute a hook if present */
    _executeHooks(hooks.afterSanitizeElements, currentNode, null);
    return false;
  };
  /**
   * _isValidAttribute
   *
   * @param lcTag Lowercase tag name of containing element.
   * @param lcName Lowercase attribute name.
   * @param value Attribute value.
   * @return Returns true if `value` is valid, otherwise false.
   */
  // eslint-disable-next-line complexity
  const _isValidAttribute = function _isValidAttribute(lcTag, lcName, value) {
    /* Make sure attribute cannot clobber */
    if (SANITIZE_DOM && (lcName === 'id' || lcName === 'name') && (value in document || value in formElement)) {
      return false;
    }
    /* Allow valid data-* attributes: At least one character after "-"
        (https://html.spec.whatwg.org/multipage/dom.html#embedding-custom-non-visible-data-with-the-data-*-attributes)
        XML-compatible (https://html.spec.whatwg.org/multipage/infrastructure.html#xml-compatible and http://www.w3.org/TR/xml/#d0e804)
        We don't need to check the value; it's always URI safe. */
    if (ALLOW_DATA_ATTR && !FORBID_ATTR[lcName] && regExpTest(DATA_ATTR, lcName)) ; else if (ALLOW_ARIA_ATTR && regExpTest(ARIA_ATTR, lcName)) ; else if (!ALLOWED_ATTR[lcName] || FORBID_ATTR[lcName]) {
      if (
      // First condition does a very basic check if a) it's basically a valid custom element tagname AND
      // b) if the tagName passes whatever the user has configured for CUSTOM_ELEMENT_HANDLING.tagNameCheck
      // and c) if the attribute name passes whatever the user has configured for CUSTOM_ELEMENT_HANDLING.attributeNameCheck
      _isBasicCustomElement(lcTag) && (CUSTOM_ELEMENT_HANDLING.tagNameCheck instanceof RegExp && regExpTest(CUSTOM_ELEMENT_HANDLING.tagNameCheck, lcTag) || CUSTOM_ELEMENT_HANDLING.tagNameCheck instanceof Function && CUSTOM_ELEMENT_HANDLING.tagNameCheck(lcTag)) && (CUSTOM_ELEMENT_HANDLING.attributeNameCheck instanceof RegExp && regExpTest(CUSTOM_ELEMENT_HANDLING.attributeNameCheck, lcName) || CUSTOM_ELEMENT_HANDLING.attributeNameCheck instanceof Function && CUSTOM_ELEMENT_HANDLING.attributeNameCheck(lcName)) ||
      // Alternative, second condition checks if it's an `is`-attribute, AND
      // the value passes whatever the user has configured for CUSTOM_ELEMENT_HANDLING.tagNameCheck
      lcName === 'is' && CUSTOM_ELEMENT_HANDLING.allowCustomizedBuiltInElements && (CUSTOM_ELEMENT_HANDLING.tagNameCheck instanceof RegExp && regExpTest(CUSTOM_ELEMENT_HANDLING.tagNameCheck, value) || CUSTOM_ELEMENT_HANDLING.tagNameCheck instanceof Function && CUSTOM_ELEMENT_HANDLING.tagNameCheck(value))) ; else {
        return false;
      }
      /* Check value is safe. First, is attr inert? If so, is safe */
    } else if (URI_SAFE_ATTRIBUTES[lcName]) ; else if (regExpTest(IS_ALLOWED_URI$1, stringReplace(value, ATTR_WHITESPACE, ''))) ; else if ((lcName === 'src' || lcName === 'xlink:href' || lcName === 'href') && lcTag !== 'script' && stringIndexOf(value, 'data:') === 0 && DATA_URI_TAGS[lcTag]) ; else if (ALLOW_UNKNOWN_PROTOCOLS && !regExpTest(IS_SCRIPT_OR_DATA, stringReplace(value, ATTR_WHITESPACE, ''))) ; else if (value) {
      return false;
    } else ;
    return true;
  };
  /**
   * _isBasicCustomElement
   * checks if at least one dash is included in tagName, and it's not the first char
   * for more sophisticated checking see https://github.com/sindresorhus/validate-element-name
   *
   * @param tagName name of the tag of the node to sanitize
   * @returns Returns true if the tag name meets the basic criteria for a custom element, otherwise false.
   */
  const _isBasicCustomElement = function _isBasicCustomElement(tagName) {
    return tagName !== 'annotation-xml' && stringMatch(tagName, CUSTOM_ELEMENT);
  };
  /**
   * _sanitizeAttributes
   *
   * @protect attributes
   * @protect nodeName
   * @protect removeAttribute
   * @protect setAttribute
   *
   * @param currentNode to sanitize
   */
  const _sanitizeAttributes = function _sanitizeAttributes(currentNode) {
    /* Execute a hook if present */
    _executeHooks(hooks.beforeSanitizeAttributes, currentNode, null);
    const {
      attributes
    } = currentNode;
    /* Check if we have attributes; if not we might have a text node */
    if (!attributes || _isClobbered(currentNode)) {
      return;
    }
    const hookEvent = {
      attrName: '',
      attrValue: '',
      keepAttr: true,
      allowedAttributes: ALLOWED_ATTR,
      forceKeepAttr: undefined
    };
    let l = attributes.length;
    /* Go backwards over all attributes; safely remove bad ones */
    while (l--) {
      const attr = attributes[l];
      const {
        name,
        namespaceURI,
        value: attrValue
      } = attr;
      const lcName = transformCaseFunc(name);
      const initValue = attrValue;
      let value = name === 'value' ? initValue : stringTrim(initValue);
      /* Execute a hook if present */
      hookEvent.attrName = lcName;
      hookEvent.attrValue = value;
      hookEvent.keepAttr = true;
      hookEvent.forceKeepAttr = undefined; // Allows developers to see this is a property they can set
      _executeHooks(hooks.uponSanitizeAttribute, currentNode, hookEvent);
      value = hookEvent.attrValue;
      /* Full DOM Clobbering protection via namespace isolation,
       * Prefix id and name attributes with `user-content-`
       */
      if (SANITIZE_NAMED_PROPS && (lcName === 'id' || lcName === 'name')) {
        // Remove the attribute with this value
        _removeAttribute(name, currentNode);
        // Prefix the value and later re-create the attribute with the sanitized value
        value = SANITIZE_NAMED_PROPS_PREFIX + value;
      }
      /* Work around a security issue with comments inside attributes */
      if (SAFE_FOR_XML && regExpTest(/((--!?|])>)|<\/(style|title)/i, value)) {
        _removeAttribute(name, currentNode);
        continue;
      }
      /* Did the hooks approve of the attribute? */
      if (hookEvent.forceKeepAttr) {
        continue;
      }
      /* Did the hooks approve of the attribute? */
      if (!hookEvent.keepAttr) {
        _removeAttribute(name, currentNode);
        continue;
      }
      /* Work around a security issue in jQuery 3.0 */
      if (!ALLOW_SELF_CLOSE_IN_ATTR && regExpTest(/\/>/i, value)) {
        _removeAttribute(name, currentNode);
        continue;
      }
      /* Sanitize attribute content to be template-safe */
      if (SAFE_FOR_TEMPLATES) {
        arrayForEach([MUSTACHE_EXPR, ERB_EXPR, TMPLIT_EXPR], expr => {
          value = stringReplace(value, expr, ' ');
        });
      }
      /* Is `value` valid for this attribute? */
      const lcTag = transformCaseFunc(currentNode.nodeName);
      if (!_isValidAttribute(lcTag, lcName, value)) {
        _removeAttribute(name, currentNode);
        continue;
      }
      /* Handle attributes that require Trusted Types */
      if (trustedTypesPolicy && typeof trustedTypes === 'object' && typeof trustedTypes.getAttributeType === 'function') {
        if (namespaceURI) ; else {
          switch (trustedTypes.getAttributeType(lcTag, lcName)) {
            case 'TrustedHTML':
              {
                value = trustedTypesPolicy.createHTML(value);
                break;
              }
            case 'TrustedScriptURL':
              {
                value = trustedTypesPolicy.createScriptURL(value);
                break;
              }
          }
        }
      }
      /* Handle invalid data-* attribute set by try-catching it */
      if (value !== initValue) {
        try {
          if (namespaceURI) {
            currentNode.setAttributeNS(namespaceURI, name, value);
          } else {
            /* Fallback to setAttribute() for browser-unrecognized namespaces e.g. "x-schema". */
            currentNode.setAttribute(name, value);
          }
          if (_isClobbered(currentNode)) {
            _forceRemove(currentNode);
          } else {
            arrayPop(DOMPurify.removed);
          }
        } catch (_) {
          _removeAttribute(name, currentNode);
        }
      }
    }
    /* Execute a hook if present */
    _executeHooks(hooks.afterSanitizeAttributes, currentNode, null);
  };
  /**
   * _sanitizeShadowDOM
   *
   * @param fragment to iterate over recursively
   */
  const _sanitizeShadowDOM = function _sanitizeShadowDOM(fragment) {
    let shadowNode = null;
    const shadowIterator = _createNodeIterator(fragment);
    /* Execute a hook if present */
    _executeHooks(hooks.beforeSanitizeShadowDOM, fragment, null);
    while (shadowNode = shadowIterator.nextNode()) {
      /* Execute a hook if present */
      _executeHooks(hooks.uponSanitizeShadowNode, shadowNode, null);
      /* Sanitize tags and elements */
      _sanitizeElements(shadowNode);
      /* Check attributes next */
      _sanitizeAttributes(shadowNode);
      /* Deep shadow DOM detected */
      if (shadowNode.content instanceof DocumentFragment) {
        _sanitizeShadowDOM(shadowNode.content);
      }
    }
    /* Execute a hook if present */
    _executeHooks(hooks.afterSanitizeShadowDOM, fragment, null);
  };
  // eslint-disable-next-line complexity
  DOMPurify.sanitize = function (dirty) {
    let cfg = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
    let body = null;
    let importedNode = null;
    let currentNode = null;
    let returnNode = null;
    /* Make sure we have a string to sanitize.
      DO NOT return early, as this will return the wrong type if
      the user has requested a DOM object rather than a string */
    IS_EMPTY_INPUT = !dirty;
    if (IS_EMPTY_INPUT) {
      dirty = '<!-->';
    }
    /* Stringify, in case dirty is an object */
    if (typeof dirty !== 'string' && !_isNode(dirty)) {
      if (typeof dirty.toString === 'function') {
        dirty = dirty.toString();
        if (typeof dirty !== 'string') {
          throw typeErrorCreate('dirty is not a string, aborting');
        }
      } else {
        throw typeErrorCreate('toString is not a function');
      }
    }
    /* Return dirty HTML if DOMPurify cannot run */
    if (!DOMPurify.isSupported) {
      return dirty;
    }
    /* Assign config vars */
    if (!SET_CONFIG) {
      _parseConfig(cfg);
    }
    /* Clean up removed elements */
    DOMPurify.removed = [];
    /* Check if dirty is correctly typed for IN_PLACE */
    if (typeof dirty === 'string') {
      IN_PLACE = false;
    }
    if (IN_PLACE) {
      /* Do some early pre-sanitization to avoid unsafe root nodes */
      if (dirty.nodeName) {
        const tagName = transformCaseFunc(dirty.nodeName);
        if (!ALLOWED_TAGS[tagName] || FORBID_TAGS[tagName]) {
          throw typeErrorCreate('root node is forbidden and cannot be sanitized in-place');
        }
      }
    } else if (dirty instanceof Node) {
      /* If dirty is a DOM element, append to an empty document to avoid
         elements being stripped by the parser */
      body = _initDocument('<!---->');
      importedNode = body.ownerDocument.importNode(dirty, true);
      if (importedNode.nodeType === NODE_TYPE.element && importedNode.nodeName === 'BODY') {
        /* Node is already a body, use as is */
        body = importedNode;
      } else if (importedNode.nodeName === 'HTML') {
        body = importedNode;
      } else {
        // eslint-disable-next-line unicorn/prefer-dom-node-append
        body.appendChild(importedNode);
      }
    } else {
      /* Exit directly if we have nothing to do */
      if (!RETURN_DOM && !SAFE_FOR_TEMPLATES && !WHOLE_DOCUMENT &&
      // eslint-disable-next-line unicorn/prefer-includes
      dirty.indexOf('<') === -1) {
        return trustedTypesPolicy && RETURN_TRUSTED_TYPE ? trustedTypesPolicy.createHTML(dirty) : dirty;
      }
      /* Initialize the document to work on */
      body = _initDocument(dirty);
      /* Check we have a DOM node from the data */
      if (!body) {
        return RETURN_DOM ? null : RETURN_TRUSTED_TYPE ? emptyHTML : '';
      }
    }
    /* Remove first element node (ours) if FORCE_BODY is set */
    if (body && FORCE_BODY) {
      _forceRemove(body.firstChild);
    }
    /* Get node iterator */
    const nodeIterator = _createNodeIterator(IN_PLACE ? dirty : body);
    /* Now start iterating over the created document */
    while (currentNode = nodeIterator.nextNode()) {
      /* Sanitize tags and elements */
      _sanitizeElements(currentNode);
      /* Check attributes next */
      _sanitizeAttributes(currentNode);
      /* Shadow DOM detected, sanitize it */
      if (currentNode.content instanceof DocumentFragment) {
        _sanitizeShadowDOM(currentNode.content);
      }
    }
    /* If we sanitized `dirty` in-place, return it. */
    if (IN_PLACE) {
      return dirty;
    }
    /* Return sanitized string or DOM */
    if (RETURN_DOM) {
      if (RETURN_DOM_FRAGMENT) {
        returnNode = createDocumentFragment.call(body.ownerDocument);
        while (body.firstChild) {
          // eslint-disable-next-line unicorn/prefer-dom-node-append
          returnNode.appendChild(body.firstChild);
        }
      } else {
        returnNode = body;
      }
      if (ALLOWED_ATTR.shadowroot || ALLOWED_ATTR.shadowrootmode) {
        /*
          AdoptNode() is not used because internal state is not reset
          (e.g. the past names map of a HTMLFormElement), this is safe
          in theory but we would rather not risk another attack vector.
          The state that is cloned by importNode() is explicitly defined
          by the specs.
        */
        returnNode = importNode.call(originalDocument, returnNode, true);
      }
      return returnNode;
    }
    let serializedHTML = WHOLE_DOCUMENT ? body.outerHTML : body.innerHTML;
    /* Serialize doctype if allowed */
    if (WHOLE_DOCUMENT && ALLOWED_TAGS['!doctype'] && body.ownerDocument && body.ownerDocument.doctype && body.ownerDocument.doctype.name && regExpTest(DOCTYPE_NAME, body.ownerDocument.doctype.name)) {
      serializedHTML = '<!DOCTYPE ' + body.ownerDocument.doctype.name + '>\n' + serializedHTML;
    }
    /* Sanitize final string template-safe */
    if (SAFE_FOR_TEMPLATES) {
      arrayForEach([MUSTACHE_EXPR, ERB_EXPR, TMPLIT_EXPR], expr => {
        serializedHTML = stringReplace(serializedHTML, expr, ' ');
      });
    }
    return trustedTypesPolicy && RETURN_TRUSTED_TYPE ? trustedTypesPolicy.createHTML(serializedHTML) : serializedHTML;
  };
  DOMPurify.setConfig = function () {
    let cfg = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    _parseConfig(cfg);
    SET_CONFIG = true;
  };
  DOMPurify.clearConfig = function () {
    CONFIG = null;
    SET_CONFIG = false;
  };
  DOMPurify.isValidAttribute = function (tag, attr, value) {
    /* Initialize shared config vars if necessary. */
    if (!CONFIG) {
      _parseConfig({});
    }
    const lcTag = transformCaseFunc(tag);
    const lcName = transformCaseFunc(attr);
    return _isValidAttribute(lcTag, lcName, value);
  };
  DOMPurify.addHook = function (entryPoint, hookFunction) {
    if (typeof hookFunction !== 'function') {
      return;
    }
    arrayPush(hooks[entryPoint], hookFunction);
  };
  DOMPurify.removeHook = function (entryPoint, hookFunction) {
    if (hookFunction !== undefined) {
      const index = arrayLastIndexOf(hooks[entryPoint], hookFunction);
      return index === -1 ? undefined : arraySplice(hooks[entryPoint], index, 1)[0];
    }
    return arrayPop(hooks[entryPoint]);
  };
  DOMPurify.removeHooks = function (entryPoint) {
    hooks[entryPoint] = [];
  };
  DOMPurify.removeAllHooks = function () {
    hooks = _createHooksMap();
  };
  return DOMPurify;
}
var purify = createDOMPurify();


//# sourceMappingURL=purify.es.mjs.map


/***/ }),

/***/ 9148:
/*!*********************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/symbol-constructor-detection.js ***!
  \*********************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

/* eslint-disable es/no-symbol -- required for testing */
var V8_VERSION = __webpack_require__(/*! ../internals/environment-v8-version */ 4028);
var fails = __webpack_require__(/*! ../internals/fails */ 6774);
var globalThis = __webpack_require__(/*! ../internals/global-this */ 7793);

var $String = globalThis.String;

// eslint-disable-next-line es/no-object-getownpropertysymbols -- required for testing
module.exports = !!Object.getOwnPropertySymbols && !fails(function () {
  var symbol = Symbol('symbol detection');
  // Chrome 38 Symbol has incorrect toString conversion
  // `get-own-property-symbols` polyfill symbols converted to object are not Symbol instances
  // nb: Do not call `String` directly to avoid this being optimized out to `symbol+''` which will,
  // of course, fail.
  return !$String(symbol) || !(Object(symbol) instanceof Symbol) ||
    // Chrome 38-40 symbols are not inherited from DOM collections prototypes to instances
    !Symbol.sham && V8_VERSION && V8_VERSION < 41;
});


/***/ }),

/***/ 9181:
/*!*****************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/weak-map-basic-detection.js ***!
  \*****************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var globalThis = __webpack_require__(/*! ../internals/global-this */ 7793);
var isCallable = __webpack_require__(/*! ../internals/is-callable */ 2120);

var WeakMap = globalThis.WeakMap;

module.exports = isCallable(WeakMap) && /native code/.test(String(WeakMap));


/***/ }),

/***/ 9278:
/*!***************************************!*\
  !*** ./js/src/gutenberg/SideLoad.tsx ***!
  \***************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/components */ 6021);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/data */ 5705);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! lodash */ 8110);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_2__);



let firstClientId = '';
/**
 * The customizer area does not include a `PluginArea` component,
 * so our slot fills do not load.
 *
 * We can use filters, but the Fills double up each time
 * another block is added to the page.
 *
 * Track the clientId of the first block we add the Fill to
 * and only return the Fill for that block. The rest of the blocks
 * inherit the Fill from the first block via their Slots.
 */
const SideLoad = ({
  clientId,
  children
}) => {
  if (!(0,lodash__WEBPACK_IMPORTED_MODULE_2__.isEmpty)(firstClientId) && clientId !== firstClientId) {
    // Make sure block still exists.
    if (-1 !== (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.select)('core/block-editor').getBlockIndex(firstClientId)) {
      return null;
    }
  }
  firstClientId = clientId;
  return children ?? null;
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ((0,_wordpress_components__WEBPACK_IMPORTED_MODULE_0__.withFilters)('advanced-sidebar-menu.blocks.side-load')(SideLoad));

/***/ }),

/***/ 9354:
/*!************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/classof.js ***!
  \************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var TO_STRING_TAG_SUPPORT = __webpack_require__(/*! ../internals/to-string-tag-support */ 8445);
var isCallable = __webpack_require__(/*! ../internals/is-callable */ 2120);
var classofRaw = __webpack_require__(/*! ../internals/classof-raw */ 3325);
var wellKnownSymbol = __webpack_require__(/*! ../internals/well-known-symbol */ 1782);

var TO_STRING_TAG = wellKnownSymbol('toStringTag');
var $Object = Object;

// ES3 wrong here
var CORRECT_ARGUMENTS = classofRaw(function () { return arguments; }()) === 'Arguments';

// fallback for IE11 Script Access Denied error
var tryGet = function (it, key) {
  try {
    return it[key];
  } catch (error) { /* empty */ }
};

// getting tag from ES6+ `Object.prototype.toString`
module.exports = TO_STRING_TAG_SUPPORT ? classofRaw : function (it) {
  var O, tag, result;
  return it === undefined ? 'Undefined' : it === null ? 'Null'
    // @@toStringTag case
    : typeof (tag = tryGet(O = $Object(it), TO_STRING_TAG)) == 'string' ? tag
    // builtinTag case
    : CORRECT_ARGUMENTS ? classofRaw(O)
    // ES3 arguments fallback
    : (result = classofRaw(O)) === 'Object' && isCallable(O.callee) ? 'Arguments' : result;
};


/***/ }),

/***/ 9365:
/*!******************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/object-create.js ***!
  \******************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

/* global ActiveXObject -- old IE, WSH */
var anObject = __webpack_require__(/*! ../internals/an-object */ 1734);
var definePropertiesModule = __webpack_require__(/*! ../internals/object-define-properties */ 1738);
var enumBugKeys = __webpack_require__(/*! ../internals/enum-bug-keys */ 9430);
var hiddenKeys = __webpack_require__(/*! ../internals/hidden-keys */ 2600);
var html = __webpack_require__(/*! ../internals/html */ 5558);
var documentCreateElement = __webpack_require__(/*! ../internals/document-create-element */ 5682);
var sharedKey = __webpack_require__(/*! ../internals/shared-key */ 180);

var GT = '>';
var LT = '<';
var PROTOTYPE = 'prototype';
var SCRIPT = 'script';
var IE_PROTO = sharedKey('IE_PROTO');

var EmptyConstructor = function () { /* empty */ };

var scriptTag = function (content) {
  return LT + SCRIPT + GT + content + LT + '/' + SCRIPT + GT;
};

// Create object with fake `null` prototype: use ActiveX Object with cleared prototype
var NullProtoObjectViaActiveX = function (activeXDocument) {
  activeXDocument.write(scriptTag(''));
  activeXDocument.close();
  var temp = activeXDocument.parentWindow.Object;
  // eslint-disable-next-line no-useless-assignment -- avoid memory leak
  activeXDocument = null;
  return temp;
};

// Create object with fake `null` prototype: use iframe Object with cleared prototype
var NullProtoObjectViaIFrame = function () {
  // Thrash, waste and sodomy: IE GC bug
  var iframe = documentCreateElement('iframe');
  var JS = 'java' + SCRIPT + ':';
  var iframeDocument;
  iframe.style.display = 'none';
  html.appendChild(iframe);
  // https://github.com/zloirock/core-js/issues/475
  iframe.src = String(JS);
  iframeDocument = iframe.contentWindow.document;
  iframeDocument.open();
  iframeDocument.write(scriptTag('document.F=Object'));
  iframeDocument.close();
  return iframeDocument.F;
};

// Check for document.domain and active x support
// No need to use active x approach when document.domain is not set
// see https://github.com/es-shims/es5-shim/issues/150
// variation of https://github.com/kitcambridge/es5-shim/commit/4f738ac066346
// avoid IE GC bug
var activeXDocument;
var NullProtoObject = function () {
  try {
    activeXDocument = new ActiveXObject('htmlfile');
  } catch (error) { /* ignore */ }
  NullProtoObject = typeof document != 'undefined'
    ? document.domain && activeXDocument
      ? NullProtoObjectViaActiveX(activeXDocument) // old IE
      : NullProtoObjectViaIFrame()
    : NullProtoObjectViaActiveX(activeXDocument); // WSH
  var length = enumBugKeys.length;
  while (length--) delete NullProtoObject[PROTOTYPE][enumBugKeys[length]];
  return NullProtoObject();
};

hiddenKeys[IE_PROTO] = true;

// `Object.create` method
// https://tc39.es/ecma262/#sec-object.create
// eslint-disable-next-line es/no-object-create -- safe
module.exports = Object.create || function create(O, Properties) {
  var result;
  if (O !== null) {
    EmptyConstructor[PROTOTYPE] = anObject(O);
    result = new EmptyConstructor();
    EmptyConstructor[PROTOTYPE] = null;
    // add "__proto__" for Object.getPrototypeOf polyfill
    result[IE_PROTO] = O;
  } else result = NullProtoObject();
  return Properties === undefined ? result : definePropertiesModule.f(result, Properties);
};


/***/ }),

/***/ 9406:
/*!***************************************************!*\
  !*** ./js/src/gutenberg/blocks/ sync block\.tsx$ ***!
  \***************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var map = {
	"./categories/block.tsx": 8792,
	"./pages/block.tsx": 3621,
	"gutenberg/blocks/categories/block.tsx": 8792,
	"gutenberg/blocks/pages/block.tsx": 3621
};


function webpackContext(req) {
	var id = webpackContextResolve(req);
	return __webpack_require__(id);
}
function webpackContextResolve(req) {
	if(!__webpack_require__.o(map, req)) {
		var e = new Error("Cannot find module '" + req + "'");
		e.code = 'MODULE_NOT_FOUND';
		throw e;
	}
	return map[req];
}
webpackContext.keys = function webpackContextKeys() {
	return Object.keys(map);
};
webpackContext.resolve = webpackContextResolve;
module.exports = webpackContext;
webpackContext.id = 9406;

/***/ }),

/***/ 9422:
/*!*********************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/has-own-property.js ***!
  \*********************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ 1109);
var toObject = __webpack_require__(/*! ../internals/to-object */ 8336);

var hasOwnProperty = uncurryThis({}.hasOwnProperty);

// `HasOwnProperty` abstract operation
// https://tc39.es/ecma262/#sec-hasownproperty
// eslint-disable-next-line es/no-object-hasown -- safe
module.exports = Object.hasOwn || function hasOwn(it, key) {
  return hasOwnProperty(toObject(it), key);
};


/***/ }),

/***/ 9430:
/*!******************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/enum-bug-keys.js ***!
  \******************************************************************************************************************/
/***/ ((module) => {

"use strict";

// IE8- don't enum bug keys
module.exports = [
  'constructor',
  'hasOwnProperty',
  'isPrototypeOf',
  'propertyIsEnumerable',
  'toLocaleString',
  'toString',
  'valueOf'
];


/***/ }),

/***/ 9440:
/*!*************************************!*\
  !*** ./js/src/gutenberg/helpers.ts ***!
  \*************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getBlockSupports: () => (/* binding */ getBlockSupports),
/* harmony export */   isScreen: () => (/* binding */ isScreen),
/* harmony export */   transformLegacyWidget: () => (/* binding */ transformLegacyWidget),
/* harmony export */   translateBlockAttributes: () => (/* binding */ translateBlockAttributes)
/* harmony export */ });
/* harmony import */ var core_js_modules_es_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.iterator.constructor.js */ 5146);
/* harmony import */ var core_js_modules_es_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_iterator_for_each_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.iterator.for-each.js */ 4363);
/* harmony import */ var core_js_modules_es_iterator_for_each_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_iterator_for_each_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/blocks */ 2463);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _globals_config__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../globals/config */ 5328);




/**
 * Are we on one of the provided screens?
 */
const isScreen = screens => {
  return screens.includes(_globals_config__WEBPACK_IMPORTED_MODULE_3__.CONFIG.currentScreen);
};

/**
 * Transform a legacy widget to the matching block.
 *
 */
const transformLegacyWidget = name => ({
  instance
}) => {
  return (0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__.createBlock)(name, translateLegacyWidget(instance.raw));
};

/**
 * Merge the common attributes and preview attributes into the block attributes.
 *
 * @since 9.7.0
 */
function translateBlockAttributes(attributes) {
  return {
    ...attributes,
    ..._globals_config__WEBPACK_IMPORTED_MODULE_3__.CONFIG.blocks.commonAttr,
    ..._globals_config__WEBPACK_IMPORTED_MODULE_3__.CONFIG.blocks.previewAttr
  };
}

/**
 * Get block support from a common location.
 *
 * @since 9.7.0
 */
function getBlockSupports() {
  return _globals_config__WEBPACK_IMPORTED_MODULE_3__.CONFIG.blocks.blockSupport;
}

/**
 * Translate the widget's "checked" to the boolean
 * version used in the block.
 *
 */
const translateLegacyWidget = settings => {
  Object.entries(settings).forEach(([key, value]) => {
    if ('checked' === value) {
      settings[key] = true;
    }
    if ('object' === typeof value) {
      translateLegacyWidget(settings[key]);
    }
    // Old widgets used to use "0" for some defaults.
    if ('0' === value) {
      delete settings[key];
    }
  });
  return settings;
};

/***/ }),

/***/ 9540:
/*!**************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/is-symbol.js ***!
  \**************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var getBuiltIn = __webpack_require__(/*! ../internals/get-built-in */ 248);
var isCallable = __webpack_require__(/*! ../internals/is-callable */ 2120);
var isPrototypeOf = __webpack_require__(/*! ../internals/object-is-prototype-of */ 2726);
var USE_SYMBOL_AS_UID = __webpack_require__(/*! ../internals/use-symbol-as-uid */ 5989);

var $Object = Object;

module.exports = USE_SYMBOL_AS_UID ? function (it) {
  return typeof it == 'symbol';
} : function (it) {
  var $Symbol = getBuiltIn('Symbol');
  return isCallable($Symbol) && isPrototypeOf($Symbol.prototype, $Object(it));
};


/***/ }),

/***/ 9790:
/*!*******************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/array-includes.js ***!
  \*******************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var toIndexedObject = __webpack_require__(/*! ../internals/to-indexed-object */ 6256);
var toAbsoluteIndex = __webpack_require__(/*! ../internals/to-absolute-index */ 5031);
var lengthOfArrayLike = __webpack_require__(/*! ../internals/length-of-array-like */ 7065);

// `Array.prototype.{ indexOf, includes }` methods implementation
var createMethod = function (IS_INCLUDES) {
  return function ($this, el, fromIndex) {
    var O = toIndexedObject($this);
    var length = lengthOfArrayLike(O);
    if (length === 0) return !IS_INCLUDES && -1;
    var index = toAbsoluteIndex(fromIndex, length);
    var value;
    // Array#includes uses SameValueZero equality algorithm
    // eslint-disable-next-line no-self-compare -- NaN check
    if (IS_INCLUDES && el !== el) while (length > index) {
      value = O[index++];
      // eslint-disable-next-line no-self-compare -- NaN check
      if (value !== value) return true;
    // Array#indexOf ignores holes, Array#includes - not
    } else for (;length > index; index++) {
      if ((IS_INCLUDES || index in O) && O[index] === el) return IS_INCLUDES || index || 0;
    } return !IS_INCLUDES && -1;
  };
};

module.exports = {
  // `Array.prototype.includes` method
  // https://tc39.es/ecma262/#sec-array.prototype.includes
  includes: createMethod(true),
  // `Array.prototype.indexOf` method
  // https://tc39.es/ecma262/#sec-array.prototype.indexof
  indexOf: createMethod(false)
};


/***/ }),

/***/ 9919:
/*!*******************************************************************************************************************************!*\
  !*** ./.yarn/cache/core-js-npm-3.45.0-adcc91d781-118350f9f1.zip/node_modules/core-js/internals/create-property-descriptor.js ***!
  \*******************************************************************************************************************************/
/***/ ((module) => {

"use strict";

module.exports = function (bitmap, value) {
  return {
    enumerable: !(bitmap & 1),
    configurable: !(bitmap & 2),
    writable: !(bitmap & 4),
    value: value
  };
};


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			id: moduleId,
/******/ 			loaded: false,
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/global */
/******/ 	(() => {
/******/ 		__webpack_require__.g = (function() {
/******/ 			if (typeof globalThis === 'object') return globalThis;
/******/ 			try {
/******/ 				return this || new Function('return this')();
/******/ 			} catch (e) {
/******/ 				if (typeof window === 'object') return window;
/******/ 			}
/******/ 		})();
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/harmony module decorator */
/******/ 	(() => {
/******/ 		__webpack_require__.hmd = (module) => {
/******/ 			module = Object.create(module);
/******/ 			if (!module.children) module.children = [];
/******/ 			Object.defineProperty(module, 'exports', {
/******/ 				enumerable: true,
/******/ 				set: () => {
/******/ 					throw new Error('ES Modules may not assign module.exports or exports.*, Use ESM export syntax, instead: ' + module.id);
/******/ 				}
/******/ 			});
/******/ 			return module;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
/*!********************************!*\
  !*** ./js/src/block-editor.ts ***!
  \********************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _modules_widgets__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./modules/widgets */ 528);

console.debug('Advanced Sidebar - Loaded');
(0,_modules_widgets__WEBPACK_IMPORTED_MODULE_0__["default"])();

/**
 * 1. Blocks can't be lazy loaded, or they will be unavailable
 *    intermittently when developing.
 * 2. Theme Customizers must wait until the page is finished loading.
 *
 * @version 1.1.0
 */
if (typeof window.wp.element !== 'undefined' && typeof window.wp.plugins !== 'undefined') {
  (__webpack_require__(/*! ./gutenberg */ 3805)["default"])();
} else if (typeof window.wp.customize !== 'undefined') {
  window.wp.customize.bind('ready', () => {
    (__webpack_require__(/*! ./gutenberg */ 3805)["default"])();
  });
}
})();

/******/ })()
;
//# sourceMappingURL=advanced-sidebar-menu-block-editor.js.map
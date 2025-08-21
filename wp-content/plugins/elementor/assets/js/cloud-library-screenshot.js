/*! elementor - v3.30.0 - 07-07-2025 */
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "../node_modules/@babel/runtime/helpers/arrayLikeToArray.js":
/*!******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/arrayLikeToArray.js ***!
  \******************************************************************/
/***/ ((module) => {

function _arrayLikeToArray(r, a) {
  (null == a || a > r.length) && (a = r.length);
  for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e];
  return n;
}
module.exports = _arrayLikeToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/arrayWithoutHoles.js":
/*!*******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/arrayWithoutHoles.js ***!
  \*******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray.js */ "../node_modules/@babel/runtime/helpers/arrayLikeToArray.js");
function _arrayWithoutHoles(r) {
  if (Array.isArray(r)) return arrayLikeToArray(r);
}
module.exports = _arrayWithoutHoles, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/assertThisInitialized.js":
/*!***********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/assertThisInitialized.js ***!
  \***********************************************************************/
/***/ ((module) => {

function _assertThisInitialized(e) {
  if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  return e;
}
module.exports = _assertThisInitialized, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/classCallCheck.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/classCallCheck.js ***!
  \****************************************************************/
/***/ ((module) => {

function _classCallCheck(a, n) {
  if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function");
}
module.exports = _classCallCheck, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/createClass.js":
/*!*************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/createClass.js ***!
  \*************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var toPropertyKey = __webpack_require__(/*! ./toPropertyKey.js */ "../node_modules/@babel/runtime/helpers/toPropertyKey.js");
function _defineProperties(e, r) {
  for (var t = 0; t < r.length; t++) {
    var o = r[t];
    o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, toPropertyKey(o.key), o);
  }
}
function _createClass(e, r, t) {
  return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", {
    writable: !1
  }), e;
}
module.exports = _createClass, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/defineProperty.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/defineProperty.js ***!
  \****************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var toPropertyKey = __webpack_require__(/*! ./toPropertyKey.js */ "../node_modules/@babel/runtime/helpers/toPropertyKey.js");
function _defineProperty(e, r, t) {
  return (r = toPropertyKey(r)) in e ? Object.defineProperty(e, r, {
    value: t,
    enumerable: !0,
    configurable: !0,
    writable: !0
  }) : e[r] = t, e;
}
module.exports = _defineProperty, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/get.js":
/*!*****************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/get.js ***!
  \*****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var superPropBase = __webpack_require__(/*! ./superPropBase.js */ "../node_modules/@babel/runtime/helpers/superPropBase.js");
function _get() {
  return module.exports = _get = "undefined" != typeof Reflect && Reflect.get ? Reflect.get.bind() : function (e, t, r) {
    var p = superPropBase(e, t);
    if (p) {
      var n = Object.getOwnPropertyDescriptor(p, t);
      return n.get ? n.get.call(arguments.length < 3 ? e : r) : n.value;
    }
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _get.apply(null, arguments);
}
module.exports = _get, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/getPrototypeOf.js ***!
  \****************************************************************/
/***/ ((module) => {

function _getPrototypeOf(t) {
  return module.exports = _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) {
    return t.__proto__ || Object.getPrototypeOf(t);
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _getPrototypeOf(t);
}
module.exports = _getPrototypeOf, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/inherits.js":
/*!**********************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/inherits.js ***!
  \**********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var setPrototypeOf = __webpack_require__(/*! ./setPrototypeOf.js */ "../node_modules/@babel/runtime/helpers/setPrototypeOf.js");
function _inherits(t, e) {
  if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function");
  t.prototype = Object.create(e && e.prototype, {
    constructor: {
      value: t,
      writable: !0,
      configurable: !0
    }
  }), Object.defineProperty(t, "prototype", {
    writable: !1
  }), e && setPrototypeOf(t, e);
}
module.exports = _inherits, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js":
/*!***********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/interopRequireDefault.js ***!
  \***********************************************************************/
/***/ ((module) => {

function _interopRequireDefault(e) {
  return e && e.__esModule ? e : {
    "default": e
  };
}
module.exports = _interopRequireDefault, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/iterableToArray.js":
/*!*****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/iterableToArray.js ***!
  \*****************************************************************/
/***/ ((module) => {

function _iterableToArray(r) {
  if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r);
}
module.exports = _iterableToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/nonIterableSpread.js":
/*!*******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/nonIterableSpread.js ***!
  \*******************************************************************/
/***/ ((module) => {

function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
module.exports = _nonIterableSpread, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js":
/*!***************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js ***!
  \***************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/typeof.js")["default"]);
var assertThisInitialized = __webpack_require__(/*! ./assertThisInitialized.js */ "../node_modules/@babel/runtime/helpers/assertThisInitialized.js");
function _possibleConstructorReturn(t, e) {
  if (e && ("object" == _typeof(e) || "function" == typeof e)) return e;
  if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined");
  return assertThisInitialized(t);
}
module.exports = _possibleConstructorReturn, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/readOnlyError.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/readOnlyError.js ***!
  \***************************************************************/
/***/ ((module) => {

function _readOnlyError(r) {
  throw new TypeError('"' + r + '" is read-only');
}
module.exports = _readOnlyError, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/setPrototypeOf.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/setPrototypeOf.js ***!
  \****************************************************************/
/***/ ((module) => {

function _setPrototypeOf(t, e) {
  return module.exports = _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) {
    return t.__proto__ = e, t;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _setPrototypeOf(t, e);
}
module.exports = _setPrototypeOf, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/superPropBase.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/superPropBase.js ***!
  \***************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var getPrototypeOf = __webpack_require__(/*! ./getPrototypeOf.js */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js");
function _superPropBase(t, o) {
  for (; !{}.hasOwnProperty.call(t, o) && null !== (t = getPrototypeOf(t)););
  return t;
}
module.exports = _superPropBase, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/toConsumableArray.js":
/*!*******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/toConsumableArray.js ***!
  \*******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var arrayWithoutHoles = __webpack_require__(/*! ./arrayWithoutHoles.js */ "../node_modules/@babel/runtime/helpers/arrayWithoutHoles.js");
var iterableToArray = __webpack_require__(/*! ./iterableToArray.js */ "../node_modules/@babel/runtime/helpers/iterableToArray.js");
var unsupportedIterableToArray = __webpack_require__(/*! ./unsupportedIterableToArray.js */ "../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js");
var nonIterableSpread = __webpack_require__(/*! ./nonIterableSpread.js */ "../node_modules/@babel/runtime/helpers/nonIterableSpread.js");
function _toConsumableArray(r) {
  return arrayWithoutHoles(r) || iterableToArray(r) || unsupportedIterableToArray(r) || nonIterableSpread();
}
module.exports = _toConsumableArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/toPrimitive.js":
/*!*************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/toPrimitive.js ***!
  \*************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/typeof.js")["default"]);
function toPrimitive(t, r) {
  if ("object" != _typeof(t) || !t) return t;
  var e = t[Symbol.toPrimitive];
  if (void 0 !== e) {
    var i = e.call(t, r || "default");
    if ("object" != _typeof(i)) return i;
    throw new TypeError("@@toPrimitive must return a primitive value.");
  }
  return ("string" === r ? String : Number)(t);
}
module.exports = toPrimitive, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/toPropertyKey.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/toPropertyKey.js ***!
  \***************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/typeof.js")["default"]);
var toPrimitive = __webpack_require__(/*! ./toPrimitive.js */ "../node_modules/@babel/runtime/helpers/toPrimitive.js");
function toPropertyKey(t) {
  var i = toPrimitive(t, "string");
  return "symbol" == _typeof(i) ? i : i + "";
}
module.exports = toPropertyKey, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/typeof.js":
/*!********************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/typeof.js ***!
  \********************************************************/
/***/ ((module) => {

function _typeof(o) {
  "@babel/helpers - typeof";

  return module.exports = _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) {
    return typeof o;
  } : function (o) {
    return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _typeof(o);
}
module.exports = _typeof, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js":
/*!****************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js ***!
  \****************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray.js */ "../node_modules/@babel/runtime/helpers/arrayLikeToArray.js");
function _unsupportedIterableToArray(r, a) {
  if (r) {
    if ("string" == typeof r) return arrayLikeToArray(r, a);
    var t = {}.toString.call(r).slice(8, -1);
    return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? arrayLikeToArray(r, a) : void 0;
  }
}
module.exports = _unsupportedIterableToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

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
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
/*!****************************************************************!*\
  !*** ../modules/cloud-library/assets/js/preview/screenshot.js ***!
  \****************************************************************/


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _readOnlyError2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/readOnlyError */ "../node_modules/@babel/runtime/helpers/readOnlyError.js"));
var _toConsumableArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "../node_modules/@babel/runtime/helpers/toConsumableArray.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
/* global ElementorScreenshotConfig */
var Screenshot = /*#__PURE__*/function (_elementorModules$Vie) {
  function Screenshot() {
    (0, _classCallCheck2.default)(this, Screenshot);
    return _callSuper(this, Screenshot, arguments);
  }
  (0, _inherits2.default)(Screenshot, _elementorModules$Vie);
  return (0, _createClass2.default)(Screenshot, [{
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      var _ElementorScreenshotC, _ElementorScreenshotC2;
      return _objectSpread({
        empty_content_headline: 'Empty Content.',
        crop: {
          width: ((_ElementorScreenshotC = ElementorScreenshotConfig) === null || _ElementorScreenshotC === void 0 || (_ElementorScreenshotC = _ElementorScreenshotC.crop) === null || _ElementorScreenshotC === void 0 ? void 0 : _ElementorScreenshotC.width) || 1200,
          height: ((_ElementorScreenshotC2 = ElementorScreenshotConfig) === null || _ElementorScreenshotC2 === void 0 || (_ElementorScreenshotC2 = _ElementorScreenshotC2.crop) === null || _ElementorScreenshotC2 === void 0 ? void 0 : _ElementorScreenshotC2.height) || 1500
        },
        excluded_external_css_urls: ['https://kit-pro.fontawesome.com'],
        external_images_urls: ['https://i.ytimg.com' // Youtube images domain.
        ],
        timeout: 15000,
        // Wait until screenshot taken or fail in 15 secs.
        render_timeout: 5000,
        // Wait until all the element will be loaded or 5 sec and then take screenshot.
        timerLabel: null,
        timer_label: "".concat(ElementorScreenshotConfig.post_id, " - timer"),
        image_placeholder: 'data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACwAAAAAAQABAAACAkQBADs=',
        isDebug: elementorCommonConfig.isElementorDebug,
        isDebugSvg: false
      }, ElementorScreenshotConfig);
    }
  }, {
    key: "getDefaultElements",
    value: function getDefaultElements() {
      var $elementor = jQuery(ElementorScreenshotConfig.selector);
      var $sections = $elementor.find('.elementor-section-wrap > .elementor-section, .elementor > .elementor-section');
      return {
        $elementor: $elementor,
        $sections: $sections,
        $firstSection: $sections.first(),
        $notElementorElements: elementorCommon.elements.$body.find('> *:not(style, link)').not($elementor),
        $head: jQuery('head')
      };
    }
  }, {
    key: "onInit",
    value: function onInit() {
      _superPropGet(Screenshot, "onInit", this, 3)([]);
      this.log('Screenshot init', 'time');

      /**
       * Hold the timeout timer
       *
       * @type {number|null}
       */
      this.timeoutTimer = setTimeout(this.screenshotFailed.bind(this), this.getSettings('timeout'));
      return this.captureScreenshot();
    }

    /**
     * The main method for this class.
     */
  }, {
    key: "captureScreenshot",
    value: function captureScreenshot() {
      if (!this.elements.$elementor.length && !this.getSettings('kit_id')) {
        elementorCommon.helpers.consoleWarn('Screenshots: The content of this page is empty, the module will create a fake conent just for this screenshot.');
        this.createFakeContent();
      }
      this.removeUnnecessaryElements();
      this.handleIFrames();
      this.removeFirstSectionMargin();
      this.handleLinks();
      this.loadExternalCss();
      this.loadExternalImages();
      return Promise.resolve().then(this.createImage.bind(this)).then(this.createImageElement.bind(this)).then(this.cropCanvas.bind(this)).then(this.save.bind(this)).then(this.screenshotSucceed.bind(this)).catch(this.screenshotFailed.bind(this));
    }

    /**
     * Fake content for documents that dont have any content.
     */
  }, {
    key: "createFakeContent",
    value: function createFakeContent() {
      this.elements.$elementor = jQuery('<div>').css({
        height: this.getSettings('crop.height'),
        width: this.getSettings('crop.width'),
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center'
      });
      this.elements.$elementor.append(jQuery('<h1>').css({
        fontSize: '85px'
      }).html(this.getSettings('empty_content_headline')));
      document.body.prepend(this.elements.$elementor);
    }

    /**
     * CSS from another server cannot be loaded with the current dom to image library.
     * this method take all the links from another domain and proxy them.
     */
  }, {
    key: "loadExternalCss",
    value: function loadExternalCss() {
      var _this = this;
      var excludedUrls = [this.getSettings('home_url')].concat((0, _toConsumableArray2.default)(this.getSettings('excluded_external_css_urls')));
      var notSelector = excludedUrls.map(function (url) {
        return "[href^=\"".concat(url, "\"]");
      }).join(', ');
      jQuery('link').not(notSelector).each(function (index, el) {
        var $link = jQuery(el),
          $newLink = $link.clone();
        $newLink.attr('href', _this.getScreenshotProxyUrl($link.attr('href')));
        _this.elements.$head.append($newLink);
        $link.remove();
      });
    }

    /**
     * Make a proxy to images urls that has some problems with cross origin (like youtube).
     */
  }, {
    key: "loadExternalImages",
    value: function loadExternalImages() {
      var _this2 = this;
      var selector = this.getSettings('external_images_urls').map(function (url) {
        return "img[src^=\"".concat(url, "\"]");
      }).join(', ');
      jQuery(selector).each(function (index, el) {
        var $img = jQuery(el);
        $img.attr('src', _this2.getScreenshotProxyUrl($img.attr('src')));
      });
    }

    /**
     * Html to images libraries can not snapshot IFrames
     * this method convert all the IFrames to some other elements.
     */
  }, {
    key: "handleIFrames",
    value: function handleIFrames() {
      this.elements.$elementor.find('iframe').each(function (index, el) {
        var $iframe = jQuery(el),
          $iframeMask = jQuery('<div />', {
            css: {
              background: 'gray',
              width: $iframe.width(),
              height: $iframe.height()
            }
          });
        $iframe.before($iframeMask);
        $iframe.remove();
      });
    }

    /**
     * Remove all the sections that should not be in the screenshot.
     */
  }, {
    key: "removeUnnecessaryElements",
    value: function removeUnnecessaryElements() {
      var _this3 = this;
      var currentHeight = 0;

      // We need to keep all elements as for Kit we render the entire homepage
      if (this.getSettings('kit_id')) {
        return;
      }
      this.elements.$sections.filter(function (index, el) {
        var shouldBeRemoved = false;
        if (currentHeight >= _this3.getSettings('crop.height')) {
          shouldBeRemoved = true;
        }
        currentHeight += jQuery(el).outerHeight();
        return shouldBeRemoved;
      }).each(function (index, el) {
        el.remove();
      });

      // Some 3rd party plugins inject elements into the dom, so this method removes all
      // the elements that was injected, to make sure that it capture a screenshot only of the post itself.
      this.elements.$notElementorElements.remove();
    }

    /**
     * Some urls make some problems to the svg parser.
     * this method convert all the urls to just '/'.
     */
  }, {
    key: "handleLinks",
    value: function handleLinks() {
      elementorCommon.elements.$body.find('a').attr('href', '/');
    }

    /**
     * Remove unnecessary margin from the first element of the post (singles and footers).
     */
  }, {
    key: "removeFirstSectionMargin",
    value: function removeFirstSectionMargin() {
      this.elements.$firstSection.css({
        marginTop: 0
      });
    }

    /**
     * Creates a png image.
     *
     * @return {Promise<unknown>} URI containing image data
     */
  }, {
    key: "createImage",
    value: function createImage() {
      var _this4 = this;
      var pageLoadedPromise = new Promise(function (resolve) {
        window.addEventListener('load', function () {
          resolve();
        });
      });
      var timeOutPromise = new Promise(function (resolve) {
        setTimeout(function () {
          resolve();
        }, _this4.getSettings('render_timeout'));
      });
      return Promise.race([pageLoadedPromise, timeOutPromise]).then(function () {
        _this4.log('Start creating screenshot.');
        if (_this4.getSettings('isDebugSvg')) {
          domtoimage.toSvg(document.body, {
            imagePlaceholder: _this4.getSettings('image_placeholder')
          }).then(function (svg) {
            return _this4.download(svg);
          });
          return Promise.reject('Debug SVG.');
        }

        // TODO: Extract to util function.
        var isSafari = /^((?!chrome|android).)*safari/i.test(window.userAgent);

        // Safari browser has some problems with the images that dom-to-images
        // library creates, so in this specific case the screenshot uses html2canvas.
        // Note that dom-to-image creates more accurate screenshot in "not safari" browsers.
        if (isSafari) {
          _this4.log('Creating screenshot with "html2canvas"');
          return html2canvas(document.body).then(function (canvas) {
            return canvas.toDataURL('image/png');
          });
        }
        _this4.log('Creating screenshot with "dom-to-image"');
        return domtoimage.toPng(document.body, {
          imagePlaceholder: _this4.getSettings('image_placeholder')
        }).catch(function () {
          return html2canvas(document.body).then(function (canvas) {
            return canvas.toDataURL('image/png');
          });
        });
      });
    }

    /**
     * Download a uri, use for debugging the svg that created from dom to image libraries.
     *
     * @param {string} uri
     */
  }, {
    key: "download",
    value: function download(uri) {
      var $link = jQuery('<a/>', {
        href: uri,
        download: 'debugSvg.svg',
        html: 'Download SVG'
      });
      elementorCommon.elements.$body.append($link);
      $link.trigger('click');
    }

    /**
     * Creates fake image element to get the size of the image later on.
     *
     * @param {string} dataUrl
     * @return {Promise<HTMLImageElement>} Image Element
     */
  }, {
    key: "createImageElement",
    value: function createImageElement(dataUrl) {
      var image = new Image();
      image.src = dataUrl;
      return new Promise(function (resolve) {
        image.onload = function () {
          return resolve(image);
        };
      });
    }

    /**
     * Crop the image to requested sizes.
     *
     * @param {HTMLImageElement} image
     * @return {Promise<unknown>} Canvas
     */
  }, {
    key: "cropCanvas",
    value: function cropCanvas(image) {
      var width = this.getSettings('crop.width');
      var height = this.getSettings('crop.height');
      var cropCanvas = document.createElement('canvas'),
        cropContext = cropCanvas.getContext('2d'),
        ratio = width / image.width;
      cropCanvas.width = width;
      cropCanvas.height = height > image.height ? image.height : height;
      cropContext.drawImage(image, 0, 0, image.width, image.height, 0, 0, image.width * ratio, image.height * ratio);
      return Promise.resolve(cropCanvas);
    }

    /**
     * Send the image to the server.
     *
     * @param {HTMLCanvasElement} canvas
     * @return {Promise<unknown>} Screenshot URL
     */
  }, {
    key: "save",
    value: function save(canvas) {
      var _this5 = this;
      var _this$getSaveAction = this.getSaveAction(),
        key = _this$getSaveAction.key,
        action = _this$getSaveAction.action;
      var data = (0, _defineProperty2.default)((0, _defineProperty2.default)({}, key, this.getSettings(key)), "screenshot", canvas.toDataURL('image/png'));
      return new Promise(function (resolve, reject) {
        if ('kit_id' === key) {
          return resolve(data.screenshot);
        }
        elementorCommon.ajax.addRequest(action, {
          data: data,
          success: function success(url) {
            _this5.log("Screenshot created: ".concat(encodeURI(url)));
            resolve(url);
          },
          error: function error() {
            _this5.log('Failed to create screenshot.');
            reject();
          }
        });
      });
    }

    /**
     * Mark this post screenshot as failed.
     * @param {Error} e
     */
  }, {
    key: "markAsFailed",
    value: function markAsFailed(e) {
      var _this6 = this;
      return new Promise(function (resolve, reject) {
        var templateId = _this6.getSettings('template_id');
        var postId = _this6.getSettings('post_id');
        var kitId = _this6.getSettings('kit_id');
        if (kitId) {
          resolve();
        } else {
          var route = templateId ? 'template_screenshot_failed' : 'screenshot_failed';
          var data = templateId ? {
            template_id: templateId,
            error: e.message || e.toString()
          } : {
            post_id: postId
          };
          elementorCommon.ajax.addRequest(route, {
            data: data,
            success: function success() {
              _this6.log("Marked as failed.");
              resolve();
            },
            error: function error() {
              _this6.log('Failed to mark this screenshot as failed.');
              reject();
            }
          });
        }
      });
    }

    /**
     * @param {string} url
     * @return {string} Screenshot Proxy URL
     */
  }, {
    key: "getScreenshotProxyUrl",
    value: function getScreenshotProxyUrl(url) {
      return "".concat(this.getSettings('home_url'), "?screenshot_proxy&nonce=").concat(this.getSettings('nonce'), "&href=").concat(url);
    }

    /**
     * Notify that the screenshot has been succeed.
     *
     * @param {string} imageUrl
     */
  }, {
    key: "screenshotSucceed",
    value: function screenshotSucceed(imageUrl) {
      this.screenshotDone(true, imageUrl);
    }

    /**
     * Notify that the screenshot has been failed.
     *
     * @param {Error} e
     */
  }, {
    key: "screenshotFailed",
    value: function screenshotFailed(e) {
      var _this7 = this;
      this.log(e, null);
      this.markAsFailed(e).then(function () {
        return _this7.screenshotDone(false);
      });
    }

    /**
     * Final method of the screenshot.
     *
     * @param {boolean} success
     * @param {string}  imageUrl
     */
  }, {
    key: "screenshotDone",
    value: function screenshotDone(success) {
      var imageUrl = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      clearTimeout(this.timeoutTimer);
      this.timeoutTimer = null;
      var _this$getSaveAction2 = this.getSaveAction(),
        message = _this$getSaveAction2.message,
        key = _this$getSaveAction2.key;

      // Send the message to the parent window and not to the top.
      // e.g: The `Theme builder` is loaded into an iFrame so the message of the screenshot
      // should be sent to the `Theme builder` window and not to the top window.
      window.parent.postMessage({
        name: message,
        success: success,
        id: this.getSettings(key),
        imageUrl: imageUrl
      }, '*');
      this.log("Screenshot ".concat(success ? 'Succeed' : 'Failed', "."), 'timeEnd');
    }

    /**
     * Log messages for debugging.
     *
     * @param {any}     message
     * @param {string?} timerMethod
     */
  }, {
    key: "log",
    value: function log(message) {
      var timerMethod = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'timeLog';
      if (!this.getSettings('isDebug')) {
        return;
      }

      // eslint-disable-next-line no-console
      console.log('string' === typeof message ? "".concat(this.getSettings('post_id'), " - ").concat(message) : message);
      if (timerMethod) {
        // eslint-disable-next-line no-console
        console[timerMethod](this.getSettings('timer_label'));
      }
    }
  }, {
    key: "getSaveAction",
    value: function getSaveAction() {
      var config = this.getSettings();
      if (config.kit_id) {
        return {
          message: 'kit-screenshot-done',
          action: 'update_kit_preview',
          key: 'kit_id'
        };
      }
      if (config.template_id) {
        return {
          message: 'library/capture-screenshot-done',
          action: 'save_template_screenshot',
          key: 'template_id'
        };
      }
      return {
        message: 'capture-screenshot-done',
        action: 'screenshot_save',
        key: 'post_id'
      };
    }
  }]);
}(elementorModules.ViewModule);
jQuery(function () {
  new Screenshot();
});
})();

/******/ })()
;
//# sourceMappingURL=cloud-library-screenshot.js.map
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "../modules/interactions/assets/js/interactions-breakpoints.js":
/*!*********************************************************************!*\
  !*** ../modules/interactions/assets/js/interactions-breakpoints.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.getActiveBreakpoint = getActiveBreakpoint;
exports.initBreakpoints = initBreakpoints;
var RESIZE_DEBOUNCE_TIMEOUT = 100;
var breakpoints = {
  list: {},
  active: {},
  onChange: function onChange() {}
};
function getActiveBreakpoint() {
  return breakpoints.active;
}
function matchBreakpoint(width) {
  for (var label in breakpoints.list) {
    var breakpoint = breakpoints.list[label];
    if ('min' === breakpoint.direction && width >= breakpoint.value) {
      return label;
    }
    if ('max' === breakpoint.direction && breakpoint.value >= width) {
      return label;
    }
  }
  return 'desktop';
}
function attachEventListeners() {
  var timeout = null;
  var onResize = function onResize() {
    if (timeout) {
      window.clearTimeout(timeout);
      timeout = null;
    }
    timeout = window.setTimeout(function () {
      var currentBreakpoint = matchBreakpoint(window.innerWidth);
      if (currentBreakpoint === breakpoints.active) {
        return;
      }
      breakpoints.active = currentBreakpoint;
      if ('function' === typeof breakpoints.onChange) {
        breakpoints.onChange(breakpoints.active);
      }
    }, RESIZE_DEBOUNCE_TIMEOUT);
  };
  window.addEventListener('resize', onResize);
}
function getBreakpointsList() {
  var _ElementorInteraction;
  return ((_ElementorInteraction = ElementorInteractionsConfig) === null || _ElementorInteraction === void 0 ? void 0 : _ElementorInteraction.breakpoints) || {};
}
function initBreakpoints() {
  var _ref = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {},
    onChange = _ref.onChange;
  breakpoints.list = getBreakpointsList();
  breakpoints.active = matchBreakpoint(window.innerWidth);
  if ('function' === typeof onChange) {
    breakpoints.onChange = onChange;
  }
  attachEventListeners();
}

/***/ }),

/***/ "../modules/interactions/assets/js/interactions-shared-utils.js":
/*!**********************************************************************!*\
  !*** ../modules/interactions/assets/js/interactions-shared-utils.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.config = config;
exports.extractInteractionId = extractInteractionId;
exports.getAnimateFunction = getAnimateFunction;
exports.getInViewFunction = getInViewFunction;
exports.parseInteractionsData = parseInteractionsData;
exports.skipInteraction = skipInteraction;
exports.timingValueToMs = timingValueToMs;
exports.unwrapInteractionValue = unwrapInteractionValue;
exports.waitForAnimateFunction = waitForAnimateFunction;
var _typeof2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js"));
var _interactionsBreakpoints = __webpack_require__(/*! ./interactions-breakpoints.js */ "../modules/interactions/assets/js/interactions-breakpoints.js");
function config() {
  var _window$ElementorInte, _window$ElementorInte2;
  return (_window$ElementorInte = (_window$ElementorInte2 = window.ElementorInteractionsConfig) === null || _window$ElementorInte2 === void 0 ? void 0 : _window$ElementorInte2.constants) !== null && _window$ElementorInte !== void 0 ? _window$ElementorInte : {};
}
function skipInteraction(interaction) {
  var _interaction$breakpoi;
  var breakpoint = (0, _interactionsBreakpoints.getActiveBreakpoint)();
  return interaction === null || interaction === void 0 || (_interaction$breakpoi = interaction.breakpoints) === null || _interaction$breakpoi === void 0 || (_interaction$breakpoi = _interaction$breakpoi.excluded) === null || _interaction$breakpoi === void 0 ? void 0 : _interaction$breakpoi.includes(breakpoint);
}
function extractInteractionId(interaction) {
  if ('interaction-item' === (interaction === null || interaction === void 0 ? void 0 : interaction.$$type) && interaction !== null && interaction !== void 0 && interaction.value) {
    var _interaction$value$in;
    return ((_interaction$value$in = interaction.value.interaction_id) === null || _interaction$value$in === void 0 ? void 0 : _interaction$value$in.value) || null;
  }
  return null;
}
function motionFunc(name) {
  var _window, _window2;
  if ('function' !== typeof ((_window = window) === null || _window === void 0 || (_window = _window.Motion) === null || _window === void 0 ? void 0 : _window[name])) {
    return undefined;
  }
  return (_window2 = window) === null || _window2 === void 0 || (_window2 = _window2.Motion) === null || _window2 === void 0 ? void 0 : _window2[name];
}
function getAnimateFunction() {
  return motionFunc('animate');
}
function getInViewFunction() {
  return motionFunc('inView');
}
function waitForAnimateFunction(callback) {
  var maxAttempts = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 10;
  if (getAnimateFunction()) {
    callback();
    return;
  }
  if (maxAttempts > 0) {
    setTimeout(function () {
      return waitForAnimateFunction(callback, maxAttempts - 1);
    }, 100);
  }
}
function parseInteractionsData(data) {
  if ('string' === typeof data) {
    try {
      return JSON.parse(data);
    } catch (_unused) {
      return null;
    }
  }
  return data;
}
function unwrapInteractionValue(propValue) {
  var fallback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
  // Supports Elementor's typed wrapper shape: { $$type: '...', value: ... }.
  if (propValue && 'object' === (0, _typeof2.default)(propValue) && '$$type' in propValue) {
    return propValue.value;
  }
  return propValue !== null && propValue !== void 0 ? propValue : fallback;
}
function timingValueToMs(timingValue, fallbackMs) {
  if (null === timingValue || undefined === timingValue) {
    return fallbackMs;
  }
  var unwrapped = unwrapInteractionValue(timingValue);
  if ('number' === typeof unwrapped) {
    return unwrapped;
  }
  var sizeObj = unwrapInteractionValue(unwrapped);
  var size = sizeObj === null || sizeObj === void 0 ? void 0 : sizeObj.size;
  var unit = (sizeObj === null || sizeObj === void 0 ? void 0 : sizeObj.unit) || 'ms';
  if ('number' !== typeof size) {
    return fallbackMs;
  }
  if ('s' === unit) {
    return size * 1000;
  }
  return size;
}

// Expose on elementorModules for Pro and other consumers.
window.elementorModules = window.elementorModules || {};
window.elementorModules.interactions = {
  config: config,
  skipInteraction: skipInteraction,
  extractInteractionId: extractInteractionId,
  getAnimateFunction: getAnimateFunction,
  getInViewFunction: getInViewFunction,
  waitForAnimateFunction: waitForAnimateFunction,
  parseInteractionsData: parseInteractionsData,
  unwrapInteractionValue: unwrapInteractionValue,
  timingValueToMs: timingValueToMs
};

/***/ }),

/***/ "../modules/interactions/assets/js/interactions-utils.js":
/*!***************************************************************!*\
  !*** ../modules/interactions/assets/js/interactions-utils.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
Object.defineProperty(exports, "config", ({
  enumerable: true,
  get: function get() {
    return _interactionsSharedUtils.config;
  }
}));
exports.extractAnimationConfig = extractAnimationConfig;
Object.defineProperty(exports, "extractInteractionId", ({
  enumerable: true,
  get: function get() {
    return _interactionsSharedUtils.extractInteractionId;
  }
}));
exports.findElementByDataId = findElementByDataId;
Object.defineProperty(exports, "getAnimateFunction", ({
  enumerable: true,
  get: function get() {
    return _interactionsSharedUtils.getAnimateFunction;
  }
}));
Object.defineProperty(exports, "getInViewFunction", ({
  enumerable: true,
  get: function get() {
    return _interactionsSharedUtils.getInViewFunction;
  }
}));
exports.getInteractionsData = getInteractionsData;
exports.getKeyframes = getKeyframes;
exports.isFreeFrontendSupportedTrigger = isFreeFrontendSupportedTrigger;
exports.parseAnimationName = parseAnimationName;
Object.defineProperty(exports, "parseInteractionsData", ({
  enumerable: true,
  get: function get() {
    return _interactionsSharedUtils.parseInteractionsData;
  }
}));
Object.defineProperty(exports, "skipInteraction", ({
  enumerable: true,
  get: function get() {
    return _interactionsSharedUtils.skipInteraction;
  }
}));
Object.defineProperty(exports, "timingValueToMs", ({
  enumerable: true,
  get: function get() {
    return _interactionsSharedUtils.timingValueToMs;
  }
}));
Object.defineProperty(exports, "unwrapInteractionValue", ({
  enumerable: true,
  get: function get() {
    return _interactionsSharedUtils.unwrapInteractionValue;
  }
}));
Object.defineProperty(exports, "waitForAnimateFunction", ({
  enumerable: true,
  get: function get() {
    return _interactionsSharedUtils.waitForAnimateFunction;
  }
}));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _interactionsSharedUtils = __webpack_require__(/*! ./interactions-shared-utils.js */ "../modules/interactions/assets/js/interactions-shared-utils.js");
/**
 * Triggers the Core `interactions.js` / `editor-interactions.js` bundles run. Pro-only triggers
 * (e.g. hover, click) must not fall through to the load-time default path.
 */
var FREE_FRONTEND_SUPPORTED_TRIGGERS = ['load', 'scrollIn', 'scrollOut'];
function isFreeFrontendSupportedTrigger(trigger) {
  return FREE_FRONTEND_SUPPORTED_TRIGGERS.includes(trigger);
}
function getKeyframes(effect, type, direction) {
  var isIn = 'in' === type;
  var keyframes = {};
  if ('fade' === effect) {
    keyframes.opacity = isIn ? [0, 1] : [1, 0];
  }
  var config = (0, _interactionsSharedUtils.config)();
  if ('scale' === effect) {
    keyframes.scale = isIn ? [config.scaleStart, 1] : [1, config.scaleStart];
  }
  if (direction) {
    var distance = config.slideDistance;
    var movement = {
      left: {
        x: isIn ? [-distance, 0] : [0, -distance]
      },
      right: {
        x: isIn ? [distance, 0] : [0, distance]
      },
      top: {
        y: isIn ? [-distance, 0] : [0, -distance]
      },
      bottom: {
        y: isIn ? [distance, 0] : [0, distance]
      }
    };
    Object.assign(keyframes, movement[direction]);
  }
  return keyframes;
}
function parseAnimationName(name) {
  var _name$split = name.split('-'),
    _name$split2 = (0, _slicedToArray2.default)(_name$split, 8),
    trigger = _name$split2[0],
    effect = _name$split2[1],
    type = _name$split2[2],
    direction = _name$split2[3],
    duration = _name$split2[4],
    delay = _name$split2[5];
  var config = (0, _interactionsSharedUtils.config)();
  return {
    trigger: trigger,
    effect: effect,
    type: type,
    direction: direction || null,
    duration: duration ? parseInt(duration, 10) : config.defaultDuration,
    delay: delay ? parseInt(delay, 10) : config.defaultDelay,
    replay: false,
    easing: config.defaultEasing
  };
}

/**
 * Get interactions data from the script tag injected by PHP.
 * Returns array of { elementId, dataId, interactions: [...] }
 */
function getInteractionsData() {
  var scriptTag = document.getElementById('elementor-interactions-data');
  if (!scriptTag) {
    return null;
  }
  try {
    return JSON.parse(scriptTag.textContent);
  } catch (_unused) {
    return null;
  }
}
function findElementByDataId(dataId) {
  return document.querySelector("[data-interaction-id=\"".concat(dataId, "\"]"));
}
function unwrapInteractionBreakpoints(propValue) {
  var breakpointsConfig = (0, _interactionsSharedUtils.unwrapInteractionValue)(propValue, {});
  var excluded = (0, _interactionsSharedUtils.unwrapInteractionValue)(breakpointsConfig === null || breakpointsConfig === void 0 ? void 0 : breakpointsConfig.excluded, []);
  if (1 > excluded.length) {
    return {};
  }
  var breakpoints = {
    excluded: excluded.map(function (breakpoint) {
      return (0, _interactionsSharedUtils.unwrapInteractionValue)(breakpoint, '');
    })
  };
  return breakpoints;
}
function extractAnimationConfig(interaction) {
  var _payload$animation;
  if ('string' === typeof interaction) {
    return parseAnimationName(interaction);
  }
  var payload = 'interaction-item' === (interaction === null || interaction === void 0 ? void 0 : interaction.$$type) && interaction !== null && interaction !== void 0 && interaction.value ? interaction.value : interaction;
  if (!payload) {
    return null;
  }
  if (payload !== null && payload !== void 0 && (_payload$animation = payload.animation) !== null && _payload$animation !== void 0 && _payload$animation.animation_id) {
    return parseAnimationName(payload.animation.animation_id);
  }
  var trigger = (0, _interactionsSharedUtils.unwrapInteractionValue)(payload.trigger) || payload.trigger || 'load';
  var animation = payload.animation;
  animation = (0, _interactionsSharedUtils.unwrapInteractionValue)(animation);
  if (!animation) {
    return null;
  }
  var breakpoints = unwrapInteractionBreakpoints(payload.breakpoints);
  var config = (0, _interactionsSharedUtils.config)();
  var effect = (0, _interactionsSharedUtils.unwrapInteractionValue)(animation.effect) || animation.effect || 'fade';
  var type = (0, _interactionsSharedUtils.unwrapInteractionValue)(animation.type) || animation.type || 'in';
  var direction = (0, _interactionsSharedUtils.unwrapInteractionValue)(animation.direction) || animation.direction || '';
  var easing = config.defaultEasing;
  var replay = false;
  var timingConfig = (0, _interactionsSharedUtils.unwrapInteractionValue)(animation.timing_config) || animation.timing_config || {};
  var duration = (0, _interactionsSharedUtils.timingValueToMs)(timingConfig === null || timingConfig === void 0 ? void 0 : timingConfig.duration, config.defaultDuration);
  var delay = (0, _interactionsSharedUtils.timingValueToMs)(timingConfig === null || timingConfig === void 0 ? void 0 : timingConfig.delay, config.defaultDelay);
  return {
    trigger: trigger,
    breakpoints: breakpoints,
    effect: effect,
    type: type,
    direction: direction,
    duration: duration,
    delay: delay,
    easing: easing,
    replay: replay
  };
}

/***/ }),

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

/***/ "../node_modules/@babel/runtime/helpers/arrayWithHoles.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/arrayWithHoles.js ***!
  \****************************************************************/
/***/ ((module) => {

function _arrayWithHoles(r) {
  if (Array.isArray(r)) return r;
}
module.exports = _arrayWithHoles, module.exports.__esModule = true, module.exports["default"] = module.exports;

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

/***/ "../node_modules/@babel/runtime/helpers/iterableToArrayLimit.js":
/*!**********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/iterableToArrayLimit.js ***!
  \**********************************************************************/
/***/ ((module) => {

function _iterableToArrayLimit(r, l) {
  var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"];
  if (null != t) {
    var e,
      n,
      i,
      u,
      a = [],
      f = !0,
      o = !1;
    try {
      if (i = (t = t.call(r)).next, 0 === l) {
        if (Object(t) !== t) return;
        f = !1;
      } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0);
    } catch (r) {
      o = !0, n = r;
    } finally {
      try {
        if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return;
      } finally {
        if (o) throw n;
      }
    }
    return a;
  }
}
module.exports = _iterableToArrayLimit, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/nonIterableRest.js":
/*!*****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/nonIterableRest.js ***!
  \*****************************************************************/
/***/ ((module) => {

function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
module.exports = _nonIterableRest, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/slicedToArray.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/slicedToArray.js ***!
  \***************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var arrayWithHoles = __webpack_require__(/*! ./arrayWithHoles.js */ "../node_modules/@babel/runtime/helpers/arrayWithHoles.js");
var iterableToArrayLimit = __webpack_require__(/*! ./iterableToArrayLimit.js */ "../node_modules/@babel/runtime/helpers/iterableToArrayLimit.js");
var unsupportedIterableToArray = __webpack_require__(/*! ./unsupportedIterableToArray.js */ "../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js");
var nonIterableRest = __webpack_require__(/*! ./nonIterableRest.js */ "../node_modules/@babel/runtime/helpers/nonIterableRest.js");
function _slicedToArray(r, e) {
  return arrayWithHoles(r) || iterableToArrayLimit(r, e) || unsupportedIterableToArray(r, e) || nonIterableRest();
}
module.exports = _slicedToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

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
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
/*!****************************************************************!*\
  !*** ../modules/interactions/assets/js/editor-interactions.js ***!
  \****************************************************************/


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _interactionsUtils = __webpack_require__(/*! ./interactions-utils.js */ "../modules/interactions/assets/js/interactions-utils.js");
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
/**
 * @type {Record<string, Promise<void> & { cancel: () => void }>}
 */
var playingInteractionsToStop = {};
function applyAnimation(element, animConfig, animateFunc) {
  var id = element.id;
  if (playingInteractionsToStop[id]) {
    playingInteractionsToStop[id].cancel();
    delete playingInteractionsToStop[id];
  }
  var keyframes = (0, _interactionsUtils.getKeyframes)(animConfig.effect, animConfig.type, animConfig.direction);
  var options = {
    duration: animConfig.duration / 1000,
    delay: animConfig.delay / 1000,
    ease: (0, _interactionsUtils.config)().defaultEasing
  };
  var initialKeyframes = {};
  Object.keys(keyframes).forEach(function (key) {
    initialKeyframes[key] = keyframes[key][0];
  });

  // WHY - Transition can be set on elements but once it sets it destroys all animations, so we basically put it aside.
  var transition = element.style.transition;
  element.style.transition = 'none';
  animateFunc(element, initialKeyframes, {
    duration: 0
  }).then(function () {
    var animations = animateFunc(element, keyframes, options);
    playingInteractionsToStop[id] = animations;
    animations.then(function () {
      if ('out' === animConfig.type) {
        var resetValues = {
          opacity: 1,
          scale: 1,
          x: 0,
          y: 0
        };
        var resetKeyframes = {};
        Object.keys(keyframes).forEach(function (key) {
          resetKeyframes[key] = resetValues[key];
        });
        element.style.transition = transition;
        animateFunc(element, resetKeyframes, {
          duration: 0
        });
      }
      delete playingInteractionsToStop[id];
    });
  });
}
function getInteractionsData() {
  var scriptTag = document.querySelector('script[data-e-interactions="true"]');
  if (!scriptTag) {
    return [];
  }
  try {
    return JSON.parse(scriptTag.textContent || '[]');
  } catch (_unused) {
    return [];
  }
}
function findElementByInteractionId(interactionId) {
  return document.querySelector('[data-interaction-id="' + interactionId + '"]');
}
function applyInteractionsToElement(element, interactionsData) {
  var animateFunc = (0, _interactionsUtils.getAnimateFunction)();
  if (!animateFunc) {
    return;
  }
  var parsedData = (0, _interactionsUtils.parseInteractionsData)(interactionsData);
  if (!parsedData) {
    return;
  }
  var interactions = Object.values((parsedData === null || parsedData === void 0 ? void 0 : parsedData.items) || []);
  interactions.forEach(function (interaction) {
    var animConfig = (0, _interactionsUtils.extractAnimationConfig)(interaction);
    if (animConfig) {
      applyAnimation(element, animConfig, animateFunc);
    }
  });
}
var previousInteractionsData = [];
function handleInteractionsUpdate() {
  var currentInteractionsData = getInteractionsData();
  var changedItems = currentInteractionsData.filter(function (currentItem) {
    var _currentItem$interact, _previousItem$interac;
    var previousItem = previousInteractionsData.find(function (prev) {
      return prev.dataId === currentItem.dataId;
    });
    if (!previousItem) {
      return true;
    }
    var currentIds = (((_currentItem$interact = currentItem.interactions) === null || _currentItem$interact === void 0 ? void 0 : _currentItem$interact.items) || []).map(_interactionsUtils.extractInteractionId).filter(Boolean).sort().join(',');
    var prevIds = (((_previousItem$interac = previousItem.interactions) === null || _previousItem$interac === void 0 ? void 0 : _previousItem$interac.items) || []).map(_interactionsUtils.extractInteractionId).filter(Boolean).sort().join(',');
    return currentIds !== prevIds;
  });
  changedItems.forEach(function (item) {
    var _previousInteractions, _item$interactions;
    var element = findElementByInteractionId(item.dataId);
    var prevInteractions = (_previousInteractions = previousInteractionsData.find(function (prev) {
      return prev.dataId === item.dataId;
    })) === null || _previousInteractions === void 0 ? void 0 : _previousInteractions.interactions;
    if (!element || !((_item$interactions = item.interactions) !== null && _item$interactions !== void 0 && (_item$interactions = _item$interactions.items) !== null && _item$interactions !== void 0 && _item$interactions.length)) {
      return;
    }
    var prevIds = new Set(((prevInteractions === null || prevInteractions === void 0 ? void 0 : prevInteractions.items) || []).map(_interactionsUtils.extractInteractionId).filter(Boolean));
    var changedInteractions = item.interactions.items.filter(function (interaction) {
      var id = (0, _interactionsUtils.extractInteractionId)(interaction);
      return !id || !prevIds.has(id);
    });
    if (changedInteractions.length > 0) {
      applyInteractionsToElement(element, _objectSpread(_objectSpread({}, item.interactions), {}, {
        items: changedInteractions
      }));
    }
  });
  previousInteractionsData = currentInteractionsData;
}
function initEditorInteractionsHandler() {
  (0, _interactionsUtils.waitForAnimateFunction)(function () {
    var head = document.head;
    var scriptTag = null;
    var observer = null;
    function setupObserver(tag) {
      if (observer) {
        observer.disconnect();
      }
      observer = new MutationObserver(function () {
        handleInteractionsUpdate();
      });
      observer.observe(tag, {
        childList: true,
        characterData: true,
        subtree: true
      });
      handleInteractionsUpdate();
      registerWindowEvents();
    }
    var headObserver = new MutationObserver(function () {
      var foundScriptTag = document.querySelector('script[data-e-interactions="true"]');
      if (foundScriptTag && foundScriptTag !== scriptTag) {
        scriptTag = foundScriptTag;
        setupObserver(scriptTag);
        headObserver.disconnect();
      }
    });
    headObserver.observe(head, {
      childList: true,
      subtree: true
    });
    scriptTag = document.querySelector('script[data-e-interactions="true"]');
    if (scriptTag) {
      setupObserver(scriptTag);
      headObserver.disconnect();
    }
  });
}
function registerWindowEvents() {
  window.top.addEventListener('atomic/play_interactions', handlePlayInteractions);
}
function handlePlayInteractions(event) {
  var _event$detail = event.detail,
    elementId = _event$detail.elementId,
    interactionId = _event$detail.interactionId;
  var interactionsData = getInteractionsData();
  var item = interactionsData.find(function (elementItemData) {
    return elementItemData.dataId === elementId;
  });
  if (!item) {
    return;
  }
  var element = findElementByInteractionId(elementId);
  if (!element) {
    return;
  }
  var interactionsCopy = _objectSpread(_objectSpread({}, item.interactions), {}, {
    items: item.interactions.items.filter(function (interactionItem) {
      var itemId = (0, _interactionsUtils.extractInteractionId)(interactionItem);
      return itemId === interactionId;
    })
  });
  applyInteractionsToElement(element, interactionsCopy);
}
if ('loading' === document.readyState) {
  document.addEventListener('DOMContentLoaded', initEditorInteractionsHandler);
} else {
  initEditorInteractionsHandler();
}
})();

/******/ })()
;
//# sourceMappingURL=editor-interactions.js.map
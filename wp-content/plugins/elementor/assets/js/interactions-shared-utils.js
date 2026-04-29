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
var exports = __webpack_exports__;
/*!**********************************************************************!*\
  !*** ../modules/interactions/assets/js/interactions-shared-utils.js ***!
  \**********************************************************************/


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
})();

/******/ })()
;
//# sourceMappingURL=interactions-shared-utils.js.map
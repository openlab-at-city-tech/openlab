var tribe = typeof tribe === "object" ? tribe : {}; tribe["events"] = tribe["events"] || {}; tribe["events"]["icons"] =
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 38);
/******/ })
/************************************************************************/
/******/ ({

/***/ 3:
/***/ (function(module, exports) {

module.exports = React;

/***/ }),

/***/ 38:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

// EXTERNAL MODULE: external "React"
var external_React_ = __webpack_require__(3);
var external_React_default = /*#__PURE__*/__webpack_require__.n(external_React_);

// CONCATENATED MODULE: ./src/modules/icons/categories.svg
var _extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }return target;
};

function _objectWithoutProperties(obj, keys) {
  var target = {};for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;target[i] = obj[i];
  }return target;
}


/* harmony default export */ var categories = (function (_ref) {
  var _ref$styles = _ref.styles,
      styles = _ref$styles === undefined ? {} : _ref$styles,
      props = _objectWithoutProperties(_ref, ["styles"]);

  return external_React_default.a.createElement("svg", _extends({ xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 47.92 48" }, props), external_React_default.a.createElement("defs", null), external_React_default.a.createElement("title", null, "block-icon-categories"), external_React_default.a.createElement("g", { id: "Layer_2", "data-name": "Layer 2" }, external_React_default.a.createElement("g", { id: "Layer_1-2", "data-name": "Layer 1" }, external_React_default.a.createElement("path", { className: styles["cls-1"] || "cls-1", d: "M47.89 24.12a1.55 1.55 0 0 1-.25.84 1.55 1.55 0 0 0 .25-.84zM.25 25a1.55 1.55 0 0 1-.25-.88 1.55 1.55 0 0 0 .25.88zM.28 14.24A1.55 1.55 0 0 1 0 13.4a1.55 1.55 0 0 0 .28.84zM47.92 13.4a1.55 1.55 0 0 1-.25.84 1.55 1.55 0 0 0 .25-.84z" }), external_React_default.a.createElement("path", { className: styles["cls-2"] || "cls-2", d: "M23.34 37.06a5.14 5.14 0 0 0 1.2 0 5.14 5.14 0 0 1-1.2 0z" }), external_React_default.a.createElement("path", { className: styles["cls-2"] || "cls-2", d: "M23.94 41.49a9.21 9.21 0 0 1-4.15-1.32L4 31.81 1 33.4a1.69 1.69 0 0 0 0 3.1l20.67 10.94a5 5 0 0 0 4.62 0L46.9 36.49a1.69 1.69 0 0 0 0-3.1l-3-1.61-15.81 8.39a9.21 9.21 0 0 1-4.15 1.32z" }), external_React_default.a.createElement("path", { className: styles["cls-2"] || "cls-2", d: "M47.82 23.6a1.86 1.86 0 0 0-1-1.11l-3.02-1.63-10.25 5.45-5.42 2.88A9.21 9.21 0 0 1 24 30.51a9.21 9.21 0 0 1-4.15-1.32l-5.45-2.88-10.29-5.46L1 22.49a1.86 1.86 0 0 0-1 1.11 1.58 1.58 0 0 0 0 .4 1.61 1.61 0 0 0 .25.86 2 2 0 0 0 .76.69l7.37 3.95 13.25 7a4.86 4.86 0 0 0 1.71.53 5.14 5.14 0 0 0 1.2 0 4.86 4.86 0 0 0 1.71-.53l13.28-7 7.34-3.9a2 2 0 0 0 .76-.69 1.61 1.61 0 0 0 .25-.86 1.58 1.58 0 0 0-.06-.45z" }), external_React_default.a.createElement("path", { className: styles["cls-2"] || "cls-2", d: "M24.57 26.08a5.14 5.14 0 0 1-1.2 0 5.14 5.14 0 0 0 1.2 0z" }), external_React_default.a.createElement("path", { className: styles["cls-3"] || "cls-3", d: "M47.82 23.69a1.51 1.51 0 0 1 .06.43 1.51 1.51 0 0 0-.06-.43zM0 24.12a1.51 1.51 0 0 1 .06-.43 1.51 1.51 0 0 0-.06.43z" }), external_React_default.a.createElement("path", { className: styles["cls-2"] || "cls-2", d: "M47.85 12.61a1.86 1.86 0 0 0-1-1.11L26.28.56a5 5 0 0 0-4.62 0L1 11.51a1.86 1.86 0 0 0-1 1.11 1.58 1.58 0 0 0-.06.44 1.61 1.61 0 0 0 .25.86 2 2 0 0 0 .81.68l3.07 1.63 4.35 2.31L18.75 24l2.92 1.55a4.86 4.86 0 0 0 1.71.53 5.14 5.14 0 0 0 1.2 0 4.86 4.86 0 0 0 1.71-.53L29.2 24l10.26-5.44 4.35-2.31 3.1-1.65a2 2 0 0 0 .76-.69 1.61 1.61 0 0 0 .25-.86 1.58 1.58 0 0 0-.07-.44z" }), external_React_default.a.createElement("path", { className: styles["cls-3"] || "cls-3", d: "M47.85 13a1.51 1.51 0 0 1 .06.43 1.51 1.51 0 0 0-.06-.43zM0 13.4a1.51 1.51 0 0 1 .09-.4 1.51 1.51 0 0 0-.09.4z" }))));
});
// CONCATENATED MODULE: ./src/modules/icons/checkbox-on.svg
var checkbox_on_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }return target;
};

function checkbox_on_objectWithoutProperties(obj, keys) {
  var target = {};for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;target[i] = obj[i];
  }return target;
}


/* harmony default export */ var checkbox_on = (function (_ref) {
  var _ref$styles = _ref.styles,
      styles = _ref$styles === undefined ? {} : _ref$styles,
      props = checkbox_on_objectWithoutProperties(_ref, ["styles"]);

  return external_React_default.a.createElement("svg", checkbox_on_extends({ width: "26", height: "14", xmlns: "http://www.w3.org/2000/svg", xmlnsXlink: "http://www.w3.org/1999/xlink" }, props), external_React_default.a.createElement("defs", null, external_React_default.a.createElement("path", { d: "M6 0h12a6 6 0 1 1 0 12H6A6 6 0 1 1 6 0z", id: "a" }), external_React_default.a.createElement("circle", { id: "b", cx: "18", cy: "6", r: "3.333" })), external_React_default.a.createElement("g", { transform: "translate(1 1)", fill: "none", fillRule: "evenodd" }, external_React_default.a.createElement("use", { stroke: "#FFF", fill: "#11A0D2", fillRule: "nonzero", xlinkHref: "#a" }), external_React_default.a.createElement("path", { d: "M6.5 4.5v3", stroke: "#FFF", strokeLinecap: "square" }), external_React_default.a.createElement("use", { fill: "#FFF", transform: "matrix(-1 0 0 1 36 0)", xlinkHref: "#b" })));
});
// CONCATENATED MODULE: ./src/modules/icons/checkbox-off.svg
var checkbox_off_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }return target;
};

function checkbox_off_objectWithoutProperties(obj, keys) {
  var target = {};for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;target[i] = obj[i];
  }return target;
}


/* harmony default export */ var checkbox_off = (function (_ref) {
  var _ref$styles = _ref.styles,
      styles = _ref$styles === undefined ? {} : _ref$styles,
      props = checkbox_off_objectWithoutProperties(_ref, ["styles"]);

  return external_React_default.a.createElement("svg", checkbox_off_extends({ width: "26", height: "14", xmlns: "http://www.w3.org/2000/svg", xmlnsXlink: "http://www.w3.org/1999/xlink" }, props), external_React_default.a.createElement("defs", null, external_React_default.a.createElement("path", { d: "M6 0h12a6 6 0 1 1 0 12H6A6 6 0 1 1 6 0z", id: "a" }), external_React_default.a.createElement("path", { d: "M17.333 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0-1a1 1 0 1 0 0-2 1 1 0 0 0 0 2zM6 9.333a3.333 3.333 0 1 1 0-6.666 3.333 3.333 0 0 1 0 6.666z", id: "b" })), external_React_default.a.createElement("g", { transform: "translate(1 1)", fill: "none", fillRule: "evenodd" }, external_React_default.a.createElement("use", { stroke: "#545D66", fill: "#FFF", fillRule: "nonzero", xlinkHref: "#a" }), external_React_default.a.createElement("use", { fill: "#545D66", xlinkHref: "#b" })));
});
// CONCATENATED MODULE: ./src/modules/icons/classic.svg
var classic_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }return target;
};

function classic_objectWithoutProperties(obj, keys) {
  var target = {};for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;target[i] = obj[i];
  }return target;
}


/* harmony default export */ var classic = (function (_ref) {
  var _ref$styles = _ref.styles,
      styles = _ref$styles === undefined ? {} : _ref$styles,
      props = classic_objectWithoutProperties(_ref, ["styles"]);

  return external_React_default.a.createElement("svg", classic_extends({ xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 48 48" }, props), external_React_default.a.createElement("defs", null), external_React_default.a.createElement("title", null, "block-icon-classic"), external_React_default.a.createElement("g", { id: "Layer_2", "data-name": "Layer 2" }, external_React_default.a.createElement("path", { className: styles["cls-1"] || "cls-1", d: "M38.54 27.22c.37-8.18 2.79-22.7 2.79-22.7a1.52 1.52 0 0 0 0-.2 1.49 1.49 0 0 0-.52-1.13L40.7 3l-.2-.14C38.12 1.44 31.61 0 24 0 16.12 0 9.42 1.54 7.3 3a1.49 1.49 0 0 0-.63 1.22 1.47 1.47 0 0 0 0 .16v.11c.28 1.58 2.54 15.01 2.9 22.73C3.74 28.76 0 31 0 34.47 0 40.91 9 48 24 48s24-7.06 24-13.53c0-3.47-3.74-5.65-9.46-7.25zm-.22 6.35c-3 5.87-13.68 5.6-14.38 5.6s-11.3.18-14.35-5.68v-5.24c4.77 4.18 14.43 4 14.43 4s9.35.33 14.43-4z", id: "Layer_1-2", "data-name": "Layer 1" })));
});
// CONCATENATED MODULE: ./src/modules/icons/date-time.svg
var date_time_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }return target;
};

function date_time_objectWithoutProperties(obj, keys) {
  var target = {};for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;target[i] = obj[i];
  }return target;
}


/* harmony default export */ var date_time = (function (_ref) {
  var _ref$styles = _ref.styles,
      styles = _ref$styles === undefined ? {} : _ref$styles,
      props = date_time_objectWithoutProperties(_ref, ["styles"]);

  return external_React_default.a.createElement("svg", date_time_extends({ xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 48 47.98" }, props), external_React_default.a.createElement("title", null, "block-icon-date-time"), external_React_default.a.createElement("g", { "data-name": "Layer 2" }, external_React_default.a.createElement("path", { d: "M44 7.38h-4.37V3.5a3.51 3.51 0 0 0-3.5-3.5 3.51 3.51 0 0 0-3.5 3.5v3.88H15.44V3.5a3.51 3.51 0 0 0-3.5-3.5 3.51 3.51 0 0 0-3.5 3.5v3.88H4a4 4 0 0 0-4 4V44a4 4 0 0 0 4 4h40a4 4 0 0 0 4-4V11.38a4 4 0 0 0-4-4zM16.91 39.13h-4.55V24.6H6.73v-3.43A10.71 10.71 0 0 0 9 21a6 6 0 0 0 2-.74 4.87 4.87 0 0 0 1.49-1.39 5 5 0 0 0 .8-2.14h3.62zm22.37 0H22.83a9.34 9.34 0 0 1 .56-3.39 9 9 0 0 1 1.52-2.58 13.32 13.32 0 0 1 2.26-2.1q1.3-1 2.74-2 .74-.51 1.57-1A10.41 10.41 0 0 0 33 26.9a6.21 6.21 0 0 0 1.15-1.44 3.57 3.57 0 0 0 .46-1.82 3.37 3.37 0 0 0-.94-2.54 3.33 3.33 0 0 0-2.42-.91 2.93 2.93 0 0 0-1.68.46 3.46 3.46 0 0 0-1.1 1.22 5.59 5.59 0 0 0-.59 1.66 9.54 9.54 0 0 0-.18 1.81h-4.36a10.74 10.74 0 0 1 .45-3.57 8.3 8.3 0 0 1 1.54-2.88A7 7 0 0 1 27.9 17a8.73 8.73 0 0 1 3.57-.69 8.93 8.93 0 0 1 2.93.48 7.56 7.56 0 0 1 2.45 1.38 6.54 6.54 0 0 1 1.68 2.21 6.77 6.77 0 0 1 .62 2.94 7.1 7.1 0 0 1-.54 2.91 7.66 7.66 0 0 1-1.44 2.16 12.23 12.23 0 0 1-2 1.71l-2.29 1.52q-1.15.75-2.24 1.62a10.13 10.13 0 0 0-1.92 2h10.55z", "data-name": "Layer 1" })));
});
// CONCATENATED MODULE: ./src/modules/icons/featured-image.svg
var featured_image_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }return target;
};

function featured_image_objectWithoutProperties(obj, keys) {
  var target = {};for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;target[i] = obj[i];
  }return target;
}


/* harmony default export */ var featured_image = (function (_ref) {
  var _ref$styles = _ref.styles,
      styles = _ref$styles === undefined ? {} : _ref$styles,
      props = featured_image_objectWithoutProperties(_ref, ["styles"]);

  return external_React_default.a.createElement("svg", featured_image_extends({ xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 48 43.31" }, props), external_React_default.a.createElement("defs", null), external_React_default.a.createElement("title", null, "block-icon-featured-image"), external_React_default.a.createElement("g", { id: "Layer_2", "data-name": "Layer 2" }, external_React_default.a.createElement("g", { id: "Layer_1-2", "data-name": "Layer 1" }, external_React_default.a.createElement("path", { className: styles["cls-1"] || "cls-1", d: "M44 7.28h-7.68L32 1.6A5.14 5.14 0 0 0 28.48 0h-9A5.14 5.14 0 0 0 16 1.6l-4.32 5.68H4a4 4 0 0 0-4 4v28a4 4 0 0 0 4 4h40a4 4 0 0 0 4-4v-28a4 4 0 0 0-4-4zM24 38a13.3 13.3 0 1 1 13.3-13.3A13.3 13.3 0 0 1 24 38z" }), external_React_default.a.createElement("circle", { className: styles["cls-1"] || "cls-1", cx: "24", cy: "24.65", r: "8.8" }))));
});
// CONCATENATED MODULE: ./src/modules/icons/link.svg
var link_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }return target;
};

function link_objectWithoutProperties(obj, keys) {
  var target = {};for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;target[i] = obj[i];
  }return target;
}


/* harmony default export */ var icons_link = (function (_ref) {
  var _ref$styles = _ref.styles,
      styles = _ref$styles === undefined ? {} : _ref$styles,
      props = link_objectWithoutProperties(_ref, ["styles"]);

  return external_React_default.a.createElement("svg", link_extends({ width: "26", height: "15", xmlns: "http://www.w3.org/2000/svg" }, props), external_React_default.a.createElement("path", { d: "M12.6 7.576H9.227v1.732H12.6v3.742a.2.2 0 0 1-.198.2H1.918a.199.199 0 0 1-.198-.2V5.092a.2.2 0 0 1 .198-.201h10.485a.2.2 0 0 1 .198.2v2.485zm5.755-3.86l-.066.067L17.16 4.93l2.601 2.646H14.33V2.843a.797.797 0 0 0-.79-.803h-.74c-.034.003-.32.004-.856.004V.804a.797.797 0 0 0-.79-.804.8.8 0 0 0-.8.803v1.24H3.992V.804A.797.797 0 0 0 3.202 0c-.447 0-.8.36-.8.803v1.24h-.796c-.041 0-.058-.003-.075-.003H.79c-.436 0-.79.36-.79.803V3.91c0 .055.006.108.016.16v8.978a.36.36 0 0 0-.008.082v1.067c0 .443.354.803.79.803h.74a12956.843 12956.843 0 0 1 12.01 0c.437 0 .79-.36.79-.803V13.13a.36.36 0 0 0-.008-.082v-3.74h5.43l-2.599 2.643 1.192 1.215L23 8.44l-4.645-4.725z", fill: "#009FD4" }));
});
// CONCATENATED MODULE: ./src/modules/icons/organizer.svg
var organizer_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }return target;
};

function organizer_objectWithoutProperties(obj, keys) {
  var target = {};for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;target[i] = obj[i];
  }return target;
}


/* harmony default export */ var organizer = (function (_ref) {
  var _ref$styles = _ref.styles,
      styles = _ref$styles === undefined ? {} : _ref$styles,
      props = organizer_objectWithoutProperties(_ref, ["styles"]);

  return external_React_default.a.createElement("svg", organizer_extends({ xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 48 47.97" }, props), external_React_default.a.createElement("title", null, "block-icon-organizer"), external_React_default.a.createElement("g", { "data-name": "Layer 2" }, external_React_default.a.createElement("g", { "data-name": "Layer 1" }, external_React_default.a.createElement("circle", { cx: "23.98", cy: "11.99", r: "11.99" }), external_React_default.a.createElement("path", { d: "M48 43.76a4 4 0 0 0 0-.83C46.53 36 35.53 27 24 27 11.69 27 0 36.69 0 43.89a4 4 0 0 0 4 4h40a4 4 0 0 0 3.78-2.74v-.11a3.93 3.93 0 0 0 .12-.51v-.36-.29c0-.1.1-.05.1-.12z" }))));
});
// CONCATENATED MODULE: ./src/modules/icons/price.svg
var price_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }return target;
};

function price_objectWithoutProperties(obj, keys) {
  var target = {};for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;target[i] = obj[i];
  }return target;
}


/* harmony default export */ var price = (function (_ref) {
  var _ref$styles = _ref.styles,
      styles = _ref$styles === undefined ? {} : _ref$styles,
      props = price_objectWithoutProperties(_ref, ["styles"]);

  return external_React_default.a.createElement("svg", price_extends({ xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 48 48" }, props), external_React_default.a.createElement("defs", null), external_React_default.a.createElement("title", null, "block-icon-price"), external_React_default.a.createElement("g", { id: "Layer_2", "data-name": "Layer 2" }, external_React_default.a.createElement("path", { className: styles["cls-1"] || "cls-1", d: "M24 0a24 24 0 1 0 24 24A24 24 0 0 0 24 0zm3.31 37h-.1l-.37.07q-.49.1-1 .17l-.1 4.27h-4l-.1-4.28a9.15 9.15 0 0 1-5.21-2.35 9.65 9.65 0 0 1-2.59-6.26h4.84a4.52 4.52 0 0 0 1.12 3.21 5.12 5.12 0 0 0 3.94 1.63 5 5 0 0 0 1.08-.07 6.65 6.65 0 0 0 1.38-.31 4.35 4.35 0 0 0 1.3-.69 3.7 3.7 0 0 0 1-1.1 3.1 3.1 0 0 0 .38-1.56 2.66 2.66 0 0 0-.93-2.21 8.89 8.89 0 0 0-3.06-1.31L20.59 25l-.15-.06c-3.7-1.22-5.53-3-5.91-6a3.69 3.69 0 0 1-.09-.76v-.31-.14c0-3.74 3.38-6.78 7.23-7.05l.1-4.28h4l.1 4.34a12.67 12.67 0 0 1 2 .53 8.13 8.13 0 0 1 2.47 1.42A7.11 7.11 0 0 1 32.06 15a8.13 8.13 0 0 1 .78 3H28a3.91 3.91 0 0 0-1-2.38 4.07 4.07 0 0 0-2.17-1.05 6 6 0 0 0-1.32-.11 8.67 8.67 0 0 0-1 .06 9.83 9.83 0 0 0-1 .23 3.29 3.29 0 0 0-1.09.58 2.83 2.83 0 0 0-.77 1 3.13 3.13 0 0 0-.28 1.37 2.42 2.42 0 0 0 .8 1.94 6.07 6.07 0 0 0 2.54 1.07l2.13.51.89.22.47.12c.57.14 1.12.3 1.65.49l.32.12.51.2a10.76 10.76 0 0 1 1.48.72A7.06 7.06 0 0 1 34 29.55c0 3.59-3 6.6-6.69 7.45z", id: "Layer_1-2", "data-name": "Layer 1" })));
});
// CONCATENATED MODULE: ./src/modules/icons/tags.svg
var tags_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }return target;
};

function tags_objectWithoutProperties(obj, keys) {
  var target = {};for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;target[i] = obj[i];
  }return target;
}


/* harmony default export */ var tags = (function (_ref) {
  var _ref$styles = _ref.styles,
      styles = _ref$styles === undefined ? {} : _ref$styles,
      props = tags_objectWithoutProperties(_ref, ["styles"]);

  return external_React_default.a.createElement("svg", tags_extends({ xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 48 47.97" }, props), external_React_default.a.createElement("defs", null), external_React_default.a.createElement("title", null, "block-icon-tags"), external_React_default.a.createElement("g", { id: "Layer_2", "data-name": "Layer 2" }, external_React_default.a.createElement("path", { className: styles["cls-1"] || "cls-1", d: "M46.68 22.35L23 1a4 4 0 0 0-2.68-1H4a4 4 0 0 0-4 4v17.68a4 4 0 0 0 1.27 2.92l23.82 22.29a4 4 0 0 0 5.71-.24L47 28a4 4 0 0 0-.32-5.65zM12 16.5a4.5 4.5 0 1 1 4.5-4.5 4.5 4.5 0 0 1-4.5 4.5z", id: "Layer_1-2", "data-name": "Layer 1" })));
});
// CONCATENATED MODULE: ./src/modules/icons/sharing.svg
var sharing_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }return target;
};

function sharing_objectWithoutProperties(obj, keys) {
  var target = {};for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;target[i] = obj[i];
  }return target;
}


/* harmony default export */ var sharing = (function (_ref) {
  var _ref$styles = _ref.styles,
      styles = _ref$styles === undefined ? {} : _ref$styles,
      props = sharing_objectWithoutProperties(_ref, ["styles"]);

  return external_React_default.a.createElement("svg", sharing_extends({ xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 48 48" }, props), external_React_default.a.createElement("defs", null), external_React_default.a.createElement("title", null, "block-icon-share"), external_React_default.a.createElement("g", { id: "Layer_2", "data-name": "Layer 2" }, external_React_default.a.createElement("path", { className: styles["cls-1"] || "cls-1", d: "M38.87 29.75a9.11 9.11 0 0 0-7 3.32L18 26.28a8.85 8.85 0 0 0 0-4.56l13.87-6.79a9.13 9.13 0 1 0-2.08-5.8v.7L15 17.05A9.13 9.13 0 1 0 15 31l14.75 7.22v.7a9.13 9.13 0 1 0 9.13-9.13z", id: "Layer_1-2", "data-name": "Layer 1" })));
});
// CONCATENATED MODULE: ./src/modules/icons/venue.svg
var venue_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }return target;
};

function venue_objectWithoutProperties(obj, keys) {
  var target = {};for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;target[i] = obj[i];
  }return target;
}


/* harmony default export */ var venue = (function (_ref) {
  var _ref$styles = _ref.styles,
      styles = _ref$styles === undefined ? {} : _ref$styles,
      props = venue_objectWithoutProperties(_ref, ["styles"]);

  return external_React_default.a.createElement("svg", venue_extends({ xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 48 43.47" }, props), external_React_default.a.createElement("defs", null), external_React_default.a.createElement("title", null, "block-icon-venue"), external_React_default.a.createElement("g", { id: "Layer_2", "data-name": "Layer 2" }, external_React_default.a.createElement("path", { className: styles["cls-1"] || "cls-1", d: "M48 13.18C46.94 6.24 36.55 0 24 0S.94 5.8 0 13.18v30.29h7v-11h7v11h5.8V31.28h8.35v12.19h5.94v-11H41v11h7zm-41.4 11a2.06 2.06 0 0 1-2.17-1.93v-4.82A2.06 2.06 0 0 1 6.6 15.5a2.06 2.06 0 0 1 2.17 1.93v4.78a2.06 2.06 0 0 1-2.17 1.93zm8.49-2.33a2.21 2.21 0 0 1-2.17-2.11v-6.42a2.06 2.06 0 0 1 2.17-1.93 2.06 2.06 0 0 1 2.17 1.93v6.38a2.21 2.21 0 0 1-2.17 2.11zm9.06-.62h-.35a2 2 0 0 1-2-2v-7.48a1.9 1.9 0 0 1 2-1.78h.35a1.9 1.9 0 0 1 2 1.78v7.48a2 2 0 0 1-2 1.96zm8.72.62a2.21 2.21 0 0 1-2.17-2.11v-6.42a2.06 2.06 0 0 1 2.17-1.93A2.06 2.06 0 0 1 35 13.32v6.38a2.21 2.21 0 0 1-2.13 2.11zm8.49 2.33a2.06 2.06 0 0 1-2.17-1.93v-4.82a2.06 2.06 0 0 1 2.17-1.93 2.06 2.06 0 0 1 2.17 1.93v4.78a2.06 2.06 0 0 1-2.18 1.93z", id: "Layer_1-2", "data-name": "Layer 1" })));
});
// CONCATENATED MODULE: ./src/modules/icons/website.svg
var website_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }return target;
};

function website_objectWithoutProperties(obj, keys) {
  var target = {};for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;target[i] = obj[i];
  }return target;
}


/* harmony default export */ var website = (function (_ref) {
  var _ref$styles = _ref.styles,
      styles = _ref$styles === undefined ? {} : _ref$styles,
      props = website_objectWithoutProperties(_ref, ["styles"]);

  return external_React_default.a.createElement("svg", website_extends({ xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 27.24 37.68" }, props), external_React_default.a.createElement("defs", null), external_React_default.a.createElement("title", null, "block-icon-website"), external_React_default.a.createElement("g", { id: "Layer_2", "data-name": "Layer 2" }, external_React_default.a.createElement("path", { className: styles["cls-1"] || "cls-1", d: "M0 0l3.54 33.5 7.29-6.18 6 10.37 7.41-4.28-6-10.41 9-3.22z", id: "Layer_1-2", "data-name": "Layer 1" })));
});
// CONCATENATED MODULE: ./src/modules/icons/index.js
/* concated harmony reexport Categories */__webpack_require__.d(__webpack_exports__, "Categories", function() { return categories; });
/* concated harmony reexport CheckboxOn */__webpack_require__.d(__webpack_exports__, "CheckboxOn", function() { return checkbox_on; });
/* concated harmony reexport CheckboxOff */__webpack_require__.d(__webpack_exports__, "CheckboxOff", function() { return checkbox_off; });
/* concated harmony reexport Classic */__webpack_require__.d(__webpack_exports__, "Classic", function() { return classic; });
/* concated harmony reexport DateTime */__webpack_require__.d(__webpack_exports__, "DateTime", function() { return date_time; });
/* concated harmony reexport FeaturedImage */__webpack_require__.d(__webpack_exports__, "FeaturedImage", function() { return featured_image; });
/* concated harmony reexport Link */__webpack_require__.d(__webpack_exports__, "Link", function() { return icons_link; });
/* concated harmony reexport Organizer */__webpack_require__.d(__webpack_exports__, "Organizer", function() { return organizer; });
/* concated harmony reexport Price */__webpack_require__.d(__webpack_exports__, "Price", function() { return price; });
/* concated harmony reexport Tags */__webpack_require__.d(__webpack_exports__, "Tags", function() { return tags; });
/* concated harmony reexport Sharing */__webpack_require__.d(__webpack_exports__, "Sharing", function() { return sharing; });
/* concated harmony reexport Venue */__webpack_require__.d(__webpack_exports__, "Venue", function() { return venue; });
/* concated harmony reexport Website */__webpack_require__.d(__webpack_exports__, "Website", function() { return website; });














/***/ })

/******/ });
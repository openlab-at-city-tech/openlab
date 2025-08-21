/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
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
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/publicPath */
/******/ 	(() => {
/******/ 		var scriptUrl;
/******/ 		if (__webpack_require__.g.importScripts) scriptUrl = __webpack_require__.g.location + "";
/******/ 		var document = __webpack_require__.g.document;
/******/ 		if (!scriptUrl && document) {
/******/ 			if (document.currentScript && document.currentScript.tagName.toUpperCase() === 'SCRIPT')
/******/ 				scriptUrl = document.currentScript.src;
/******/ 			if (!scriptUrl) {
/******/ 				var scripts = document.getElementsByTagName("script");
/******/ 				if(scripts.length) {
/******/ 					var i = scripts.length - 1;
/******/ 					while (i > -1 && (!scriptUrl || !/^http(s?):/.test(scriptUrl))) scriptUrl = scripts[i--].src;
/******/ 				}
/******/ 			}
/******/ 		}
/******/ 		// When supporting browsers where an automatic publicPath is not supported you must specify an output.publicPath manually via configuration
/******/ 		// or pass an empty string ("") and set the __webpack_public_path__ variable from your code to use your own logic.
/******/ 		if (!scriptUrl) throw new Error("Automatic publicPath is not supported in this browser");
/******/ 		scriptUrl = scriptUrl.replace(/^blob:/, "").replace(/#.*$/, "").replace(/\?.*$/, "").replace(/\/[^\/]+$/, "/");
/******/ 		__webpack_require__.p = scriptUrl;
/******/ 	})();
/******/ 	
/************************************************************************/

;// external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// external "React"
const external_React_namespaceObject = window["React"];
var external_React_default = /*#__PURE__*/__webpack_require__.n(external_React_namespaceObject);
;// external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// ./src/images/hero/hero-cross-sell.png
const hero_cross_sell_namespaceObject = __webpack_require__.p + "1af739d41f996f2d6ff1.png";
;// ./src/components/hero/hero.jsx
var _jsxFileName = "/opt/atlassian/pipelines/agent/build/src/components/hero/hero.jsx",
  _this = undefined;



var HeroSection = function HeroSection() {
  return wp.element.createElement("div", {
    className: "cross-sell-hero",
    __self: _this,
    __source: {
      fileName: _jsxFileName,
      lineNumber: 7,
      columnNumber: 5
    }
  }, wp.element.createElement("div", {
    className: "cross-sell-wrapper cross-sell-hero__wrapper",
    __self: _this,
    __source: {
      fileName: _jsxFileName,
      lineNumber: 8,
      columnNumber: 7
    }
  }, wp.element.createElement("div", {
    className: "cross-sell-hero__image",
    __self: _this,
    __source: {
      fileName: _jsxFileName,
      lineNumber: 9,
      columnNumber: 9
    }
  }, wp.element.createElement("img", {
    src: hero_cross_sell_namespaceObject,
    alt: "WPMU DEV Plugins",
    __self: _this,
    __source: {
      fileName: _jsxFileName,
      lineNumber: 10,
      columnNumber: 11
    }
  })), wp.element.createElement("div", {
    className: "cross-sell-hero__content",
    __self: _this,
    __source: {
      fileName: _jsxFileName,
      lineNumber: 12,
      columnNumber: 9
    }
  }, wp.element.createElement("h2", {
    __self: _this,
    __source: {
      fileName: _jsxFileName,
      lineNumber: 13,
      columnNumber: 11
    }
  }, (0,external_wp_i18n_namespaceObject.__)("We heard you like plugins...😉", "plugin-cross-sell-textdomain")), wp.element.createElement("p", {
    __self: _this,
    __source: {
      fileName: _jsxFileName,
      lineNumber: 19,
      columnNumber: 11
    }
  }, (0,external_wp_i18n_namespaceObject.__)("Check out these top-rated tools for securing, optimizing and growing your site.", "plugin-cross-sell-textdomain")))));
};

;// ./src/components/hero/index.jsx

;// ./src/components/tabs/tabs.jsx
var tabs_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/components/tabs/tabs.jsx",
  tabs_this = undefined;
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }

var TabsContext = /*#__PURE__*/(0,external_React_namespaceObject.createContext)();
var Tabs = function Tabs(_ref) {
  var children = _ref.children,
    _ref$defaultActiveTab = _ref.defaultActiveTab,
    defaultActiveTab = _ref$defaultActiveTab === void 0 ? 0 : _ref$defaultActiveTab;
  var _useState = (0,external_React_namespaceObject.useState)(defaultActiveTab),
    _useState2 = _slicedToArray(_useState, 2),
    activeTab = _useState2[0],
    setActiveTab = _useState2[1];
  return wp.element.createElement(TabsContext.Provider, {
    value: {
      activeTab: activeTab,
      setActiveTab: setActiveTab
    },
    __self: tabs_this,
    __source: {
      fileName: tabs_jsxFileName,
      lineNumber: 8,
      columnNumber: 5
    }
  }, wp.element.createElement("div", {
    className: "sui-tabs sui-tabs-overflow sui-tabs-flushed cross-sell-flushed",
    __self: tabs_this,
    __source: {
      fileName: tabs_jsxFileName,
      lineNumber: 9,
      columnNumber: 7
    }
  }, children));
};
var TabsMenu = function TabsMenu(_ref2) {
  var children = _ref2.children;
  var _useContext = (0,external_React_namespaceObject.useContext)(TabsContext),
    activeTab = _useContext.activeTab,
    setActiveTab = _useContext.setActiveTab;
  return wp.element.createElement("div", {
    role: "tablist",
    className: "sui-tabs-menu cross-sell-wrapper",
    __self: tabs_this,
    __source: {
      fileName: tabs_jsxFileName,
      lineNumber: 19,
      columnNumber: 5
    }
  }, external_React_default().Children.map(children, function (child, index) {
    return /*#__PURE__*/external_React_default().cloneElement(child, {
      index: index,
      active: activeTab === index,
      onClick: function onClick() {
        return setActiveTab(index);
      }
    });
  }));
};
var Tab = function Tab(_ref3) {
  var index = _ref3.index,
    active = _ref3.active,
    onClick = _ref3.onClick,
    children = _ref3.children;
  return wp.element.createElement("button", {
    type: "button",
    role: "tab",
    className: "sui-tab-item ".concat(active ? "active" : ""),
    "aria-selected": active,
    onClick: onClick,
    __self: tabs_this,
    __source: {
      fileName: tabs_jsxFileName,
      lineNumber: 32,
      columnNumber: 3
    }
  }, children);
};
var TabsContent = function TabsContent(_ref4) {
  var children = _ref4.children;
  var _useContext2 = (0,external_React_namespaceObject.useContext)(TabsContext),
    activeTab = _useContext2.activeTab;
  return wp.element.createElement("div", {
    className: "sui-tabs-content",
    __self: tabs_this,
    __source: {
      fileName: tabs_jsxFileName,
      lineNumber: 46,
      columnNumber: 5
    }
  }, external_React_default().Children.map(children, function (child, index) {
    return /*#__PURE__*/external_React_default().cloneElement(child, {
      active: activeTab === index
    });
  }));
};
var TabPanel = function TabPanel(_ref5) {
  var active = _ref5.active,
    children = _ref5.children;
  return wp.element.createElement("div", {
    className: "sui-tab-content ".concat(active ? "active" : ""),
    hidden: !active,
    __self: tabs_this,
    __source: {
      fileName: tabs_jsxFileName,
      lineNumber: 57,
      columnNumber: 3
    }
  }, wp.element.createElement("div", {
    className: "cross-sell-wrapper",
    __self: tabs_this,
    __source: {
      fileName: tabs_jsxFileName,
      lineNumber: 58,
      columnNumber: 5
    }
  }, children));
};

;// ./src/components/tabs/index.jsx

;// ./src/icons/arrow-alt.jsx
var arrow_alt_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/icons/arrow-alt.jsx",
  arrow_alt_this = undefined;
function _objectDestructuringEmpty(t) { if (null == t) throw new TypeError("Cannot destructure " + t); }
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }

var ArrowAltIcon = function ArrowAltIcon(_ref) {
  var delegated = _extends({}, (_objectDestructuringEmpty(_ref), _ref));
  return wp.element.createElement("svg", _extends({
    width: "31",
    height: "31",
    viewBox: "0 0 31 31",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, delegated, {
    __self: arrow_alt_this,
    __source: {
      fileName: arrow_alt_jsxFileName,
      lineNumber: 5,
      columnNumber: 5
    }
  }), wp.element.createElement("path", {
    d: "M22.0781 16.76L16.9219 21.6819C16.6289 21.9456 16.1895 21.9456 15.9258 21.6526C15.6621 21.3596 15.6621 20.9202 15.9551 20.6565L19.8516 16.9358H9.89062C9.48047 16.9358 9.1875 16.6428 9.1875 16.2327C9.1875 15.8518 9.48047 15.5295 9.89062 15.5295H19.8516L15.9551 11.8381C15.6621 11.5745 15.6621 11.1057 15.9258 10.842C16.1895 10.5491 16.6582 10.5491 16.9219 10.8127L22.0781 15.7346C22.2246 15.8811 22.3125 16.0569 22.3125 16.2327C22.3125 16.4377 22.2246 16.6135 22.0781 16.76Z",
    __self: arrow_alt_this,
    __source: {
      fileName: arrow_alt_jsxFileName,
      lineNumber: 13,
      columnNumber: 7
    }
  }));
};

;// ./src/icons/arrow.jsx
var arrow_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/icons/arrow.jsx",
  arrow_this = undefined;
function arrow_objectDestructuringEmpty(t) { if (null == t) throw new TypeError("Cannot destructure " + t); }
function arrow_extends() { return arrow_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, arrow_extends.apply(null, arguments); }

var ArrowIcon = function ArrowIcon(_ref) {
  var delegated = arrow_extends({}, (arrow_objectDestructuringEmpty(_ref), _ref));
  return wp.element.createElement("svg", arrow_extends({
    width: "30",
    height: "31",
    viewBox: "0 0 30 31",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, delegated, {
    __self: arrow_this,
    __source: {
      fileName: arrow_jsxFileName,
      lineNumber: 5,
      columnNumber: 5
    }
  }), wp.element.createElement("path", {
    d: "M12.6211 10.8147C12.6523 10.7834 12.6836 10.7561 12.7148 10.7327C12.7539 10.7092 12.793 10.6858 12.832 10.6624C12.8789 10.6467 12.9258 10.635 12.9727 10.6272C13.0195 10.6116 13.0664 10.6038 13.1133 10.6038C13.168 10.6038 13.2188 10.6116 13.2656 10.6272C13.3125 10.635 13.3555 10.6467 13.3945 10.6624C13.4336 10.6858 13.4727 10.7092 13.5117 10.7327C13.5508 10.7561 13.5859 10.7834 13.6172 10.8147L17.8945 15.1155C17.9258 15.1467 17.9531 15.1819 17.9766 15.2209C18.0078 15.26 18.0312 15.2991 18.0469 15.3381C18.0703 15.3772 18.0859 15.4241 18.0938 15.4788C18.1016 15.5256 18.1055 15.5725 18.1055 15.6194C18.1055 15.6741 18.1016 15.7249 18.0938 15.7717C18.0859 15.8186 18.0703 15.8616 18.0469 15.9006C18.0312 15.9475 18.0078 15.9905 17.9766 16.0295C17.9531 16.0608 17.9258 16.092 17.8945 16.1233L13.6055 20.4124C13.543 20.4749 13.4688 20.5256 13.3828 20.5647C13.3047 20.5959 13.2188 20.6116 13.125 20.6116C13.1172 20.6116 13.1133 20.6116 13.1133 20.6116C13.0586 20.6116 13.0078 20.6077 12.9609 20.5999C12.9141 20.5842 12.8672 20.5686 12.8203 20.553C12.7812 20.5374 12.7422 20.5178 12.7031 20.4944C12.6719 20.4709 12.6406 20.4436 12.6094 20.4124L12.0938 19.9084C12.0625 19.8772 12.0352 19.842 12.0117 19.803C11.9883 19.7639 11.9688 19.7249 11.9531 19.6858C11.9297 19.6467 11.9141 19.6038 11.9062 19.5569C11.8984 19.51 11.8945 19.4592 11.8945 19.4045C11.8945 19.3577 11.8984 19.3108 11.9062 19.2639C11.9141 19.217 11.9297 19.1741 11.9531 19.135C11.9688 19.0881 11.9883 19.0491 12.0117 19.0178C12.0352 18.9788 12.0625 18.9436 12.0938 18.9124L15.3867 15.6077L12.1055 12.3264C12.0742 12.2952 12.0469 12.26 12.0234 12.2209C12 12.1819 11.9805 12.1428 11.9648 12.1038C11.9414 12.0569 11.9219 12.0139 11.9062 11.9749C11.8984 11.928 11.8945 11.8772 11.8945 11.8225C11.8945 11.7678 11.8984 11.717 11.9062 11.6702C11.9141 11.6233 11.9297 11.5803 11.9531 11.5413C11.9688 11.5022 11.9883 11.4631 12.0117 11.4241C12.0352 11.385 12.0664 11.3499 12.1055 11.3186L12.6094 10.8147H12.6211Z",
    __self: arrow_this,
    __source: {
      fileName: arrow_jsxFileName,
      lineNumber: 13,
      columnNumber: 7
    }
  }));
};

;// ./src/icons/business.jsx
var _excluded = ["fill"];
var business_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/icons/business.jsx",
  business_this = undefined;
function business_extends() { return business_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, business_extends.apply(null, arguments); }
function _objectWithoutProperties(e, t) { if (null == e) return {}; var o, r, i = _objectWithoutPropertiesLoose(e, t); if (Object.getOwnPropertySymbols) { var n = Object.getOwnPropertySymbols(e); for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]); } return i; }
function _objectWithoutPropertiesLoose(r, e) { if (null == r) return {}; var t = {}; for (var n in r) if ({}.hasOwnProperty.call(r, n)) { if (-1 !== e.indexOf(n)) continue; t[n] = r[n]; } return t; }

var BusinessIcon = function BusinessIcon(_ref) {
  var _ref$fill = _ref.fill,
    fill = _ref$fill === void 0 ? "#333333" : _ref$fill,
    delegated = _objectWithoutProperties(_ref, _excluded);
  return wp.element.createElement("svg", business_extends({
    width: "40",
    height: "34",
    viewBox: "0 0 40 34",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, delegated, {
    __self: business_this,
    __source: {
      fileName: business_jsxFileName,
      lineNumber: 5,
      columnNumber: 5
    }
  }), wp.element.createElement("path", {
    d: "M11 4.35767V6.85767H21V4.35767C21 4.10767 20.75 3.85767 20.5 3.85767H11.5C11.1875 3.85767 11 4.10767 11 4.35767ZM8 6.85767V4.35767C8 2.48267 9.5625 0.857666 11.5 0.857666H20.5C22.375 0.857666 24 2.48267 24 4.35767V6.85767H28C30.1875 6.85767 32 8.67017 32 10.8577V12.9202C31.625 12.9202 31.3125 12.8577 31 12.8577C30.3125 12.8577 29.625 12.9202 29 13.0452V10.8577C29 10.3577 28.5 9.85767 28 9.85767H22.5H9.5H4C3.4375 9.85767 3 10.3577 3 10.8577V16.8577H12H22H22.5C21.125 18.5452 20.25 20.6077 20 22.8577H14C12.875 22.8577 12 21.9827 12 20.8577V19.8577H3V26.8577C3 27.4202 3.4375 27.8577 4 27.8577H20.75C21.125 28.9827 21.75 29.9827 22.5 30.8577H4C1.75 30.8577 0 29.1077 0 26.8577V18.3577V10.8577C0 8.67017 1.75 6.85767 4 6.85767H8ZM22 23.8577C22 20.6702 23.6875 17.7327 26.5 16.1077C29.25 14.4827 32.6875 14.4827 35.5 16.1077C38.25 17.7327 40 20.6702 40 23.8577C40 27.1077 38.25 30.0452 35.5 31.6702C32.6875 33.2952 29.25 33.2952 26.5 31.6702C23.6875 30.0452 22 27.1077 22 23.8577ZM31 18.8577C30.4375 18.8577 30 19.3577 30 19.8577V23.8577C30 24.4202 30.4375 24.8577 31 24.8577H34C34.5 24.8577 35 24.4202 35 23.8577C35 23.3577 34.5 22.8577 34 22.8577H32V19.8577C32 19.3577 31.5 18.8577 31 18.8577Z",
    fill: fill,
    __self: business_this,
    __source: {
      fileName: business_jsxFileName,
      lineNumber: 13,
      columnNumber: 7
    }
  }));
};

;// ./src/icons/check.jsx
var check_excluded = ["fill"];
var check_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/icons/check.jsx",
  check_this = undefined;
function check_extends() { return check_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, check_extends.apply(null, arguments); }
function check_objectWithoutProperties(e, t) { if (null == e) return {}; var o, r, i = check_objectWithoutPropertiesLoose(e, t); if (Object.getOwnPropertySymbols) { var n = Object.getOwnPropertySymbols(e); for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]); } return i; }
function check_objectWithoutPropertiesLoose(r, e) { if (null == r) return {}; var t = {}; for (var n in r) if ({}.hasOwnProperty.call(r, n)) { if (-1 !== e.indexOf(n)) continue; t[n] = r[n]; } return t; }

var CheckIcon = function CheckIcon(_ref) {
  var _ref$fill = _ref.fill,
    fill = _ref$fill === void 0 ? "#1ABC9C" : _ref$fill,
    delegated = check_objectWithoutProperties(_ref, check_excluded);
  return wp.element.createElement("svg", check_extends({
    width: "12",
    height: "13",
    viewBox: "0 0 12 13",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, delegated, {
    __self: check_this,
    __source: {
      fileName: check_jsxFileName,
      lineNumber: 5,
      columnNumber: 5
    }
  }), wp.element.createElement("path", {
    d: "M10.2422 2.36554C10.5156 2.63898 10.7617 2.93585 10.9805 3.25616C11.1914 3.57648 11.3711 3.91632 11.5195 4.2757C11.6758 4.63507 11.7969 5.01007 11.8828 5.4007C11.9609 5.79132 12 6.19366 12 6.60773C12 7.02179 11.9609 7.42413 11.8828 7.81476C11.7969 8.20538 11.6758 8.58038 11.5195 8.93976C11.3711 9.29913 11.1914 9.63898 10.9805 9.95929C10.7617 10.2796 10.5156 10.5765 10.2422 10.8499C9.96875 11.1234 9.67188 11.3694 9.35156 11.5882C9.03125 11.7991 8.69141 11.9788 8.33203 12.1273C7.97266 12.2835 7.59766 12.4046 7.20703 12.4905C6.81641 12.5687 6.41406 12.6077 6 12.6077C5.17188 12.6077 4.39453 12.4515 3.66797 12.139C2.93359 11.8265 2.29297 11.4007 1.74609 10.8616C1.20703 10.3148 0.78125 9.67413 0.46875 8.93976C0.15625 8.2132 0 7.43585 0 6.60773C0 5.7796 0.15625 5.00226 0.46875 4.2757C0.78125 3.54132 1.20703 2.9046 1.74609 2.36554C2.29297 1.81866 2.93359 1.38898 3.66797 1.07648C4.39453 0.763977 5.17188 0.607727 6 0.607727C6.41406 0.607727 6.81641 0.64679 7.20703 0.724915C7.59766 0.810852 7.97266 0.931946 8.33203 1.0882C8.69141 1.23663 9.03125 1.41632 9.35156 1.62726C9.67188 1.84601 9.96875 2.0921 10.2422 2.36554ZM8.49609 5.5882C8.53516 5.54913 8.56641 5.50226 8.58984 5.44757C8.61328 5.39288 8.625 5.33429 8.625 5.27179C8.625 5.20929 8.61328 5.1507 8.58984 5.09601C8.56641 5.04132 8.53516 4.99445 8.49609 4.95538L8.14453 4.60382C8.10547 4.56476 8.05859 4.53351 8.00391 4.51007C7.94922 4.48663 7.89062 4.47491 7.82812 4.47491C7.76562 4.47491 7.70703 4.48663 7.65234 4.51007C7.59766 4.53351 7.55078 4.56476 7.51172 4.60382L5.0625 7.06476L4.06641 6.06866C4.01953 6.0296 3.96875 5.99835 3.91406 5.97491C3.86719 5.95148 3.8125 5.93976 3.75 5.93976C3.6875 5.93976 3.62891 5.95148 3.57422 5.97491C3.51953 5.99835 3.47266 6.0296 3.43359 6.06866L3.08203 6.42023C3.04297 6.45929 3.01172 6.50616 2.98828 6.56085C2.96484 6.61554 2.95312 6.67413 2.95312 6.73663C2.95312 6.79132 2.96484 6.84601 2.98828 6.9007C3.01172 6.95538 3.04297 7.00226 3.08203 7.04132L4.57031 8.5296C4.63281 8.5921 4.70312 8.64288 4.78125 8.68195C4.86719 8.72101 4.96094 8.74054 5.0625 8.74054C5.15625 8.74054 5.24219 8.72101 5.32031 8.68195C5.40625 8.64288 5.48047 8.5921 5.54297 8.5296L8.49609 5.5882Z",
    fill: fill,
    __self: check_this,
    __source: {
      fileName: check_jsxFileName,
      lineNumber: 13,
      columnNumber: 7
    }
  }));
};

;// ./src/icons/cloud.jsx
var cloud_excluded = ["fill"];
var cloud_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/icons/cloud.jsx",
  cloud_this = undefined;
function cloud_extends() { return cloud_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, cloud_extends.apply(null, arguments); }
function cloud_objectWithoutProperties(e, t) { if (null == e) return {}; var o, r, i = cloud_objectWithoutPropertiesLoose(e, t); if (Object.getOwnPropertySymbols) { var n = Object.getOwnPropertySymbols(e); for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]); } return i; }
function cloud_objectWithoutPropertiesLoose(r, e) { if (null == r) return {}; var t = {}; for (var n in r) if ({}.hasOwnProperty.call(r, n)) { if (-1 !== e.indexOf(n)) continue; t[n] = r[n]; } return t; }

var CloudIcon = function CloudIcon(_ref) {
  var _ref$fill = _ref.fill,
    fill = _ref$fill === void 0 ? "#333333" : _ref$fill,
    delegated = cloud_objectWithoutProperties(_ref, cloud_excluded);
  return wp.element.createElement("svg", cloud_extends({
    width: "40",
    height: "29",
    viewBox: "0 0 40 29",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, delegated, {
    __self: cloud_this,
    __source: {
      fileName: cloud_jsxFileName,
      lineNumber: 5,
      columnNumber: 5
    }
  }), wp.element.createElement("path", {
    d: "M22.125 6.48267C20.75 4.92017 18.75 3.85767 16.5 3.85767C12.3125 3.85767 9 7.23267 9 11.3577V11.4202C9 12.6702 8.1875 13.7952 7 14.2327C4.625 15.0452 3 17.2952 3 19.8577C3 23.1702 5.6875 25.8577 9 25.8577H31.5H31.6875C31.6875 25.8577 31.75 25.8577 31.8125 25.8577C34.6875 25.7327 37 23.2952 37 20.3577C37 18.1077 35.625 16.1702 33.6875 15.3577C32.4375 14.7952 31.6875 13.4827 31.9375 12.1077C31.9375 11.8577 32 11.6077 32 11.3577C32 8.92017 29.9375 6.85767 27.5 6.85767C26.8125 6.85767 26.1875 7.04517 25.625 7.29517C24.4375 7.79517 23 7.48267 22.125 6.48267ZM32 28.8577H31.5H29H9C4 28.8577 0 24.8577 0 19.8577C0 15.9827 2.5 12.6077 6 11.4202V11.3577C6 5.60767 10.6875 0.857666 16.5 0.857666C19.625 0.857666 22.5 2.29517 24.4375 4.54517C25.375 4.10767 26.375 3.85767 27.5 3.85767C31.625 3.85767 35 7.23267 35 11.3577C35 11.7952 34.9375 12.1702 34.875 12.6077C37.875 13.9202 40 16.9202 40 20.3577C40 24.9202 36.4375 28.6077 32 28.8577ZM13.9375 18.4202C13.3125 17.8577 13.3125 16.9202 13.9375 16.3577C14.5 15.7327 15.4375 15.7327 16.0625 16.3577L18.5 18.7952V10.3577C18.5 9.54517 19.125 8.85767 20 8.85767C20.8125 8.85767 21.5 9.54517 21.5 10.3577V18.7952L23.9375 16.3577C24.5 15.7327 25.4375 15.7327 26 16.3577C26.625 16.9202 26.625 17.8577 26 18.4202L21 23.4202C20.4375 24.0452 19.5 24.0452 18.9375 23.4202L13.9375 18.4202Z",
    fill: fill,
    __self: cloud_this,
    __source: {
      fileName: cloud_jsxFileName,
      lineNumber: 13,
      columnNumber: 7
    }
  }));
};

;// ./src/icons/email.jsx
var email_excluded = ["fill"];
var email_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/icons/email.jsx",
  email_this = undefined;
function email_extends() { return email_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, email_extends.apply(null, arguments); }
function email_objectWithoutProperties(e, t) { if (null == e) return {}; var o, r, i = email_objectWithoutPropertiesLoose(e, t); if (Object.getOwnPropertySymbols) { var n = Object.getOwnPropertySymbols(e); for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]); } return i; }
function email_objectWithoutPropertiesLoose(r, e) { if (null == r) return {}; var t = {}; for (var n in r) if ({}.hasOwnProperty.call(r, n)) { if (-1 !== e.indexOf(n)) continue; t[n] = r[n]; } return t; }

var EmailIcon = function EmailIcon(_ref) {
  var _ref$fill = _ref.fill,
    fill = _ref$fill === void 0 ? "#555555" : _ref$fill,
    delegated = email_objectWithoutProperties(_ref, email_excluded);
  return wp.element.createElement("svg", email_extends({
    width: "20",
    height: "21",
    viewBox: "0 0 20 21",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, delegated, {
    __self: email_this,
    __source: {
      fileName: email_jsxFileName,
      lineNumber: 5,
      columnNumber: 5
    }
  }), wp.element.createElement("path", {
    d: "M0 8.60767L10 12.3577L20 8.60767V16.1077C20 16.7978 19.7526 17.3902 19.2578 17.885C18.776 18.3668 18.1901 18.6077 17.5 18.6077H2.5C1.8099 18.6077 1.21745 18.3668 0.722656 17.885C0.240885 17.3902 0 16.7978 0 16.1077V8.60767ZM2.5 3.60767H17.5C18.1901 3.60767 18.776 3.85506 19.2578 4.34985C19.7526 4.83162 20 5.41756 20 6.10767V6.73267L10 10.4827L0 6.73267V6.10767C0 5.41756 0.240885 4.83162 0.722656 4.34985C1.21745 3.85506 1.8099 3.60767 2.5 3.60767Z",
    fill: fill,
    __self: email_this,
    __source: {
      fileName: email_jsxFileName,
      lineNumber: 13,
      columnNumber: 7
    }
  }));
};

;// ./src/icons/globe.jsx
var globe_excluded = ["fill"];
var globe_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/icons/globe.jsx",
  globe_this = undefined;
function globe_extends() { return globe_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, globe_extends.apply(null, arguments); }
function globe_objectWithoutProperties(e, t) { if (null == e) return {}; var o, r, i = globe_objectWithoutPropertiesLoose(e, t); if (Object.getOwnPropertySymbols) { var n = Object.getOwnPropertySymbols(e); for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]); } return i; }
function globe_objectWithoutPropertiesLoose(r, e) { if (null == r) return {}; var t = {}; for (var n in r) if ({}.hasOwnProperty.call(r, n)) { if (-1 !== e.indexOf(n)) continue; t[n] = r[n]; } return t; }

var GlobeIcon = function GlobeIcon(_ref) {
  var _ref$fill = _ref.fill,
    fill = _ref$fill === void 0 ? "#555555" : _ref$fill,
    delegated = globe_objectWithoutProperties(_ref, globe_excluded);
  return wp.element.createElement("svg", globe_extends({
    width: "20",
    height: "22",
    viewBox: "0 0 20 22",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, delegated, {
    __self: globe_this,
    __source: {
      fileName: globe_jsxFileName,
      lineNumber: 5,
      columnNumber: 5
    }
  }), wp.element.createElement("path", {
    d: "M10 1.10767C11.3802 1.10767 12.6758 1.36808 13.8867 1.88892C15.1107 2.40975 16.1719 3.1259 17.0703 4.03735C17.9818 4.93579 18.6979 5.99699 19.2188 7.22095C19.7396 8.43188 20 9.72746 20 11.1077C20 12.4879 19.7396 13.7834 19.2188 14.9944C18.6979 16.2183 17.9818 17.2861 17.0703 18.1975C16.1719 19.0959 15.1107 19.8056 13.8867 20.3264C12.6758 20.8472 11.3802 21.1077 10 21.1077C8.61979 21.1077 7.32422 20.8472 6.11328 20.3264C4.88932 19.8056 3.82161 19.0959 2.91016 18.1975C2.01172 17.2861 1.30208 16.2183 0.78125 14.9944C0.260417 13.7834 0 12.4879 0 11.1077C0 9.72746 0.260417 8.43188 0.78125 7.22095C1.30208 5.99699 2.01172 4.93579 2.91016 4.03735C3.82161 3.1259 4.88932 2.40975 6.11328 1.88892C7.32422 1.36808 8.61979 1.10767 10 1.10767ZM18.0859 10.1897L18.0664 10.1506C17.9753 9.38241 17.7865 8.65324 17.5 7.96313C17.2135 7.28605 16.8424 6.66105 16.3867 6.08813C15.931 5.5022 15.3971 4.98787 14.7852 4.54517C14.1862 4.10246 13.5352 3.7509 12.832 3.49048C13.2096 3.82902 13.5612 4.23267 13.8867 4.70142C14.2122 5.17017 14.4922 5.691 14.7266 6.26392C14.974 6.83683 15.1758 7.45532 15.332 8.11938C15.4753 8.78345 15.5729 9.47355 15.625 10.1897H18.0859ZM9.00391 4.44751H8.98438C8.65885 4.6298 8.33984 4.89673 8.02734 5.24829C7.72786 5.59985 7.45443 6.02303 7.20703 6.51782C6.95964 6.99959 6.7513 7.55298 6.58203 8.17798C6.41276 8.78996 6.30208 9.45402 6.25 10.1702H9.00391V4.44751ZM8.98438 12.0452L6.25 12.0647C6.30208 12.7808 6.41276 13.4449 6.58203 14.0569C6.7513 14.6689 6.95964 15.2222 7.20703 15.717C7.45443 16.1988 7.72786 16.6155 8.02734 16.967C8.33984 17.3186 8.65885 17.5855 8.98438 17.7678V12.0452ZM10.8594 17.8264V17.8459C11.1979 17.6767 11.5299 17.4228 11.8555 17.0842C12.181 16.7327 12.474 16.3095 12.7344 15.8147C12.9948 15.3199 13.2161 14.7535 13.3984 14.1155C13.5807 13.4775 13.6979 12.7874 13.75 12.0452H10.8594V17.8264ZM10.8594 10.1702L13.75 10.1897C13.6979 9.44751 13.5807 8.75741 13.3984 8.11938C13.2161 7.48136 12.9948 6.91496 12.7344 6.42017C12.474 5.91235 12.181 5.48918 11.8555 5.15063C11.5299 4.79907 11.1979 4.53866 10.8594 4.36938V10.1702ZM7.16797 3.49048L7.10938 3.51001C6.41927 3.77043 5.77474 4.12199 5.17578 4.5647C4.58984 5.00741 4.06901 5.51522 3.61328 6.08813C3.15755 6.66105 2.78646 7.29256 2.5 7.98267C2.20052 8.67277 2.00521 9.40845 1.91406 10.1897H4.375C4.41406 9.47355 4.51172 8.78345 4.66797 8.11938C4.82422 7.46834 5.01953 6.85636 5.25391 6.28345C5.5013 5.71053 5.78776 5.18319 6.11328 4.70142C6.42578 4.23267 6.77734 3.82902 7.16797 3.49048ZM1.91406 12.0452L1.93359 12.0842C2.02474 12.8525 2.21354 13.5751 2.5 14.2522C2.79948 14.9423 3.17708 15.5738 3.63281 16.1467C4.08854 16.7066 4.61589 17.2079 5.21484 17.6506C5.8138 18.0933 6.46484 18.4514 7.16797 18.7249C6.79036 18.3733 6.4388 17.9631 6.11328 17.4944C5.78776 17.0256 5.5013 16.5113 5.25391 15.9514C5.01953 15.3785 4.83073 14.7665 4.6875 14.1155C4.53125 13.4514 4.42708 12.7613 4.375 12.0452H1.91406ZM12.832 18.7249L12.8906 18.7053C13.5807 18.4319 14.2253 18.0803 14.8242 17.6506C15.4102 17.2079 15.9245 16.7001 16.3672 16.1272C16.8229 15.5543 17.2005 14.9228 17.5 14.2327C17.7865 13.5426 17.9818 12.8134 18.0859 12.0452H15.625C15.5729 12.7613 15.4753 13.4449 15.332 14.0959C15.1758 14.747 14.974 15.359 14.7266 15.9319C14.4922 16.5048 14.2122 17.0256 13.8867 17.4944C13.5612 17.9631 13.2096 18.3733 12.832 18.7249Z",
    fill: fill,
    __self: globe_this,
    __source: {
      fileName: globe_jsxFileName,
      lineNumber: 13,
      columnNumber: 7
    }
  }));
};

;// ./src/icons/layers.jsx
var layers_excluded = ["fill"];
var layers_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/icons/layers.jsx",
  layers_this = undefined;
function layers_extends() { return layers_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, layers_extends.apply(null, arguments); }
function layers_objectWithoutProperties(e, t) { if (null == e) return {}; var o, r, i = layers_objectWithoutPropertiesLoose(e, t); if (Object.getOwnPropertySymbols) { var n = Object.getOwnPropertySymbols(e); for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]); } return i; }
function layers_objectWithoutPropertiesLoose(r, e) { if (null == r) return {}; var t = {}; for (var n in r) if ({}.hasOwnProperty.call(r, n)) { if (-1 !== e.indexOf(n)) continue; t[n] = r[n]; } return t; }

var LayersIcon = function LayersIcon(_ref) {
  var _ref$fill = _ref.fill,
    fill = _ref$fill === void 0 ? "#555555" : _ref$fill,
    delegated = layers_objectWithoutProperties(_ref, layers_excluded);
  return wp.element.createElement("svg", layers_extends({
    width: "20",
    height: "22",
    viewBox: "0 0 20 22",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, delegated, {
    __self: layers_this,
    __source: {
      fileName: layers_jsxFileName,
      lineNumber: 5,
      columnNumber: 5
    }
  }), wp.element.createElement("path", {
    d: "M19.7656 8.95923L11.0156 14.0374C10.8724 14.1285 10.7161 14.2001 10.5469 14.2522C10.3906 14.2913 10.2214 14.3108 10.0391 14.3108C9.85677 14.3108 9.68099 14.2847 9.51172 14.2327C9.34245 14.1806 9.1862 14.1155 9.04297 14.0374L0.234375 8.95923C0.169271 8.92017 0.110677 8.86157 0.0585938 8.78345C0.0195312 8.70532 0 8.6272 0 8.54907C0 8.45793 0.0195312 8.3798 0.0585938 8.3147C0.110677 8.23657 0.169271 8.17798 0.234375 8.13892L8.75 3.17798C8.91927 3.08683 9.10807 3.01522 9.31641 2.96313C9.52474 2.89803 9.73958 2.86548 9.96094 2.86548C10.1823 2.86548 10.3971 2.89803 10.6055 2.96313C10.8138 3.01522 11.0091 3.09334 11.1914 3.19751L19.7656 8.13892C19.8307 8.17798 19.8828 8.23657 19.9219 8.3147C19.974 8.3798 20 8.45793 20 8.54907C20 8.64022 19.974 8.72485 19.9219 8.80298C19.8828 8.86808 19.8307 8.92017 19.7656 8.95923ZM19.7656 14.9358L16.1914 12.8655L11.0156 15.8733C10.8724 15.9514 10.7161 16.0165 10.5469 16.0686C10.3906 16.1207 10.2214 16.1467 10.0391 16.1467C9.85677 16.1467 9.68099 16.1207 9.51172 16.0686C9.34245 16.0165 9.1862 15.9514 9.04297 15.8733L3.80859 12.8655L0.234375 14.9358C0.169271 14.9749 0.110677 15.0334 0.0585938 15.1116C0.0195312 15.1767 0 15.2548 0 15.3459C0 15.4371 0.0195312 15.5217 0.0585938 15.5999C0.110677 15.665 0.169271 15.717 0.234375 15.7561L9.04297 20.8538C9.1862 20.9319 9.34245 20.9905 9.51172 21.0295C9.68099 21.0816 9.85677 21.1077 10.0391 21.1077C10.2214 21.1077 10.3906 21.0816 10.5469 21.0295C10.7161 20.9905 10.8789 20.9254 11.0352 20.8342L19.7656 15.7756C19.8307 15.7366 19.8828 15.6845 19.9219 15.6194C19.974 15.5413 20 15.4566 20 15.3655C20 15.2743 19.974 15.1962 19.9219 15.1311C19.8828 15.053 19.8307 14.9944 19.7656 14.9553V14.9358Z",
    fill: fill,
    __self: layers_this,
    __source: {
      fileName: layers_jsxFileName,
      lineNumber: 13,
      columnNumber: 7
    }
  }));
};

;// ./src/icons/review.jsx
var review_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/icons/review.jsx",
  review_this = undefined;
function review_objectDestructuringEmpty(t) { if (null == t) throw new TypeError("Cannot destructure " + t); }
function review_extends() { return review_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, review_extends.apply(null, arguments); }

var ReviewIcon = function ReviewIcon(_ref) {
  var delegated = review_extends({}, (review_objectDestructuringEmpty(_ref), _ref));
  return wp.element.createElement("svg", review_extends({
    width: "17",
    height: "35",
    viewBox: "0 0 17 35",
    xmlns: "http://www.w3.org/2000/svg"
  }, delegated, {
    __self: review_this,
    __source: {
      fileName: review_jsxFileName,
      lineNumber: 5,
      columnNumber: 5
    }
  }), wp.element.createElement("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M11.4466 34.3577C10.7799 34.3577 10.0466 34.2243 9.44657 33.891C8.84657 33.5577 8.24657 33.1577 7.77991 32.6243C8.77991 31.891 9.9799 31.491 11.1799 31.6243C12.3799 31.7577 13.5132 32.3577 14.3132 33.291C13.5132 34.0243 12.5132 34.3577 11.4466 34.3577ZM13.5799 28.7577C13.5799 27.691 13.9132 26.691 14.5799 25.8243C15.5132 26.6243 16.1132 27.7577 16.2466 28.9577C16.3799 30.1577 16.0466 31.4243 15.3132 32.4243C14.7799 31.9577 14.3799 31.4243 14.0466 30.7577C13.7132 30.1577 13.5799 29.491 13.5799 28.7577ZM6.51324 30.091C5.84658 29.891 5.24658 29.5577 4.71324 29.1577C4.17991 28.691 3.77991 28.1577 3.44658 27.491C4.57991 27.0243 5.84658 27.0243 6.97991 27.491C8.11324 27.9577 9.04657 28.8243 9.5799 29.9577C8.57991 30.291 7.51324 30.3577 6.51324 30.091ZM9.9132 25.2243C10.1799 24.2243 10.7799 23.291 11.5799 22.691C12.3132 23.691 12.5799 24.9577 12.3799 26.1577C12.1799 27.3577 11.5799 28.491 10.5799 29.2243C10.1799 28.6243 9.9132 27.9577 9.7799 27.291C9.7132 26.6243 9.7799 25.891 9.9132 25.2243ZM1.97991 24.3577C1.44658 23.891 0.979918 23.3577 0.713248 22.691C0.446582 22.0243 0.246582 21.3577 0.246582 20.691C1.44658 20.7577 2.64658 21.2243 3.51325 22.1577C4.37991 23.0243 4.84658 24.2243 4.84658 25.491C3.77991 25.4243 2.77991 25.0243 1.97991 24.3577ZM7.11324 21.4243C7.77991 20.5577 8.71324 20.0243 9.7132 19.8243C9.9132 21.0243 9.7132 22.291 9.04657 23.3577C8.37991 24.4243 7.31324 25.1577 6.11324 25.4243C5.97991 24.7577 5.97991 24.0243 6.17991 23.3577C6.37991 22.5577 6.64658 21.9577 7.11324 21.4243ZM1.17992 17.5577C0.779918 17.0243 0.513248 16.3577 0.379915 15.691C0.246582 15.0243 0.246582 14.291 0.446582 13.6243C1.64658 13.9577 2.57991 14.8243 3.17991 15.891C3.77991 16.9577 3.91325 18.2243 3.64658 19.4243C2.64658 19.091 1.77992 18.4243 1.17992 17.5577ZM6.91324 16.091C7.77991 15.491 8.77991 15.1577 9.8466 15.2243C9.7799 16.4243 9.17991 17.6243 8.24657 18.4243C7.31324 19.2243 6.11324 19.691 4.91324 19.6243C4.97991 18.891 5.17991 18.2243 5.51324 17.6243C5.84658 17.0243 6.31324 16.491 6.91324 16.091ZM1.84658 10.691C1.64658 10.0244 1.57992 9.29099 1.71325 8.62433C1.77992 7.95766 2.04658 7.29099 2.44658 6.69099C3.44658 7.42433 4.11325 8.49099 4.37991 9.69099C4.57991 10.891 4.37991 12.1577 3.71325 13.2243C2.77991 12.6244 2.17991 11.691 1.84658 10.691ZM7.77991 11.091C8.77991 10.7577 9.8466 10.8244 10.8466 11.1577C10.3799 12.291 9.44657 13.2243 8.37991 13.691C7.24657 14.1577 5.97991 14.2243 4.84658 13.7577C5.11324 13.091 5.51324 12.5577 6.04658 12.091C6.51324 11.691 7.11324 11.291 7.77991 11.091ZM4.77991 5.22433C4.57991 3.75767 5.11324 2.35767 5.97991 1.35767C6.84658 2.22433 7.31324 3.42433 7.31324 4.691C7.31324 5.95766 6.84658 7.15766 6.04658 8.02433C5.37991 7.22433 4.91324 6.291 4.77991 5.22433ZM10.5132 6.62433C11.5799 6.491 12.5799 6.69099 13.5132 7.29099C12.8466 8.35766 11.7799 9.09099 10.6466 9.35766C9.44657 9.62432 8.17991 9.42432 7.17991 8.82433C7.57991 8.22433 8.04657 7.75766 8.64657 7.35766C9.17991 6.95766 9.8466 6.75766 10.5132 6.62433ZM11.2466 1.42434C12.0466 0.757665 13.0466 0.424333 14.1132 0.357666C14.0466 1.62433 13.5799 2.75767 12.7132 3.62433C11.8466 4.491 10.6466 4.95766 9.44657 5.02433C9.44657 4.291 9.64657 3.62433 9.9799 3.02433C10.2466 2.35767 10.7132 1.82433 11.2466 1.42434Z",
    __self: review_this,
    __source: {
      fileName: review_jsxFileName,
      lineNumber: 12,
      columnNumber: 7
    }
  }));
};

;// ./src/icons/star.jsx
var star_excluded = ["fill"];
var star_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/icons/star.jsx",
  star_this = undefined;
function star_extends() { return star_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, star_extends.apply(null, arguments); }
function star_objectWithoutProperties(e, t) { if (null == e) return {}; var o, r, i = star_objectWithoutPropertiesLoose(e, t); if (Object.getOwnPropertySymbols) { var n = Object.getOwnPropertySymbols(e); for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]); } return i; }
function star_objectWithoutPropertiesLoose(r, e) { if (null == r) return {}; var t = {}; for (var n in r) if ({}.hasOwnProperty.call(r, n)) { if (-1 !== e.indexOf(n)) continue; t[n] = r[n]; } return t; }

var StarIcon = function StarIcon(_ref) {
  var _ref$fill = _ref.fill,
    fill = _ref$fill === void 0 ? "#333333" : _ref$fill,
    delegated = star_objectWithoutProperties(_ref, star_excluded);
  return wp.element.createElement("svg", star_extends({
    width: "34",
    height: "33",
    viewBox: "0 0 34 33",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, delegated, {
    __self: star_this,
    __source: {
      fileName: star_jsxFileName,
      lineNumber: 5,
      columnNumber: 5
    }
  }), wp.element.createElement("path", {
    d: "M16.9375 0.857666C17.5625 0.857666 18.0625 1.23267 18.3125 1.73267L22.625 10.5452L32.1875 11.9827C32.75 12.0452 33.1875 12.4202 33.375 12.9827C33.5625 13.5452 33.4375 14.1077 33 14.5452L26.0625 21.4202L27.6875 31.1077C27.8125 31.6702 27.5625 32.2952 27.125 32.6077C26.625 32.9202 26 32.9827 25.5 32.7327L16.9375 28.1077L8.4375 32.7327C7.875 32.9827 7.3125 32.9202 6.8125 32.6077C6.375 32.2327 6.125 31.6702 6.25 31.1077L7.875 21.4202L0.9375 14.5452C0.5 14.1077 0.375 13.5452 0.5625 12.9827C0.75 12.4827 1.1875 12.0452 1.75 11.9827L11.3125 10.5452L15.625 1.73267C15.875 1.23267 16.375 0.857666 16.9375 0.857666ZM16.9375 5.79517L13.6875 12.6077C13.4375 13.0452 13.0625 13.3577 12.5625 13.4202L5.1875 14.4827L10.5 19.7952C10.875 20.1702 11.0625 20.6702 10.9375 21.1077L9.6875 28.6077L16.25 25.1077C16.6875 24.8577 17.25 24.8577 17.6875 25.1077L24.25 28.6077L23 21.1702C22.875 20.6702 23.0625 20.1702 23.4375 19.8577L28.75 14.4827L21.375 13.4202C20.875 13.3577 20.5 13.0452 20.25 12.6077L16.9375 5.79517Z",
    fill: fill,
    __self: star_this,
    __source: {
      fileName: star_jsxFileName,
      lineNumber: 13,
      columnNumber: 7
    }
  }));
};

;// ./src/icons/star-alt.jsx
var star_alt_excluded = ["fill"];
var star_alt_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/icons/star-alt.jsx",
  star_alt_this = undefined;
function star_alt_extends() { return star_alt_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, star_alt_extends.apply(null, arguments); }
function star_alt_objectWithoutProperties(e, t) { if (null == e) return {}; var o, r, i = star_alt_objectWithoutPropertiesLoose(e, t); if (Object.getOwnPropertySymbols) { var n = Object.getOwnPropertySymbols(e); for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]); } return i; }
function star_alt_objectWithoutPropertiesLoose(r, e) { if (null == r) return {}; var t = {}; for (var n in r) if ({}.hasOwnProperty.call(r, n)) { if (-1 !== e.indexOf(n)) continue; t[n] = r[n]; } return t; }

var StarAltIcon = function StarAltIcon(_ref) {
  var _ref$fill = _ref.fill,
    fill = _ref$fill === void 0 ? "#888888" : _ref$fill,
    delegated = star_alt_objectWithoutProperties(_ref, star_alt_excluded);
  return wp.element.createElement("svg", star_alt_extends({
    width: "12",
    height: "13",
    viewBox: "0 0 12 13",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, delegated, {
    __self: star_alt_this,
    __source: {
      fileName: star_alt_jsxFileName,
      lineNumber: 5,
      columnNumber: 5
    }
  }), wp.element.createElement("path", {
    d: "M11.9883 5.14288H11.9766C11.9844 5.15851 11.9883 5.17413 11.9883 5.18976C11.9961 5.20538 12 5.22101 12 5.23663C12 5.28351 11.9883 5.32648 11.9648 5.36554C11.9492 5.4046 11.9258 5.43976 11.8945 5.47101L8.71875 7.97882L10.1367 11.9866C10.1445 12.0023 10.1484 12.0179 10.1484 12.0335C10.1562 12.0491 10.1602 12.0648 10.1602 12.0804C10.1602 12.1585 10.1289 12.2249 10.0664 12.2796C10.0117 12.3421 9.94531 12.3734 9.86719 12.3734C9.84375 12.3734 9.81641 12.3694 9.78516 12.3616C9.76172 12.3538 9.73828 12.3421 9.71484 12.3265L6 10.0999L2.28516 12.3265C2.26172 12.3421 2.23438 12.3538 2.20312 12.3616C2.17969 12.3694 2.15625 12.3734 2.13281 12.3734C2.05469 12.3734 1.98438 12.3421 1.92188 12.2796C1.86719 12.2249 1.83984 12.1585 1.83984 12.0804C1.83984 12.0648 1.83984 12.0491 1.83984 12.0335C1.84766 12.0179 1.85547 12.0023 1.86328 11.9866L3.28125 7.97882L0.105469 5.47101C0.0742188 5.43976 0.046875 5.4046 0.0234375 5.36554C0.0078125 5.32648 0 5.28351 0 5.23663C0 5.15851 0.0273438 5.0921 0.0820312 5.03741C0.136719 4.98273 0.207031 4.95538 0.292969 4.95538H4.34766L5.73047 1.04132C5.74609 0.978821 5.77734 0.931946 5.82422 0.900696C5.87891 0.861633 5.9375 0.842102 6 0.842102C6.0625 0.842102 6.11719 0.861633 6.16406 0.900696C6.21875 0.931946 6.25391 0.978821 6.26953 1.04132L7.65234 4.94366H11.707C11.7773 4.94366 11.8359 4.9632 11.8828 5.00226C11.9297 5.04132 11.9648 5.0882 11.9883 5.14288Z",
    fill: fill,
    __self: star_alt_this,
    __source: {
      fileName: star_alt_jsxFileName,
      lineNumber: 13,
      columnNumber: 7
    }
  }));
};

;// ./src/icons/support.jsx
var support_excluded = ["fill"];
var support_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/icons/support.jsx",
  support_this = undefined;
function support_extends() { return support_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, support_extends.apply(null, arguments); }
function support_objectWithoutProperties(e, t) { if (null == e) return {}; var o, r, i = support_objectWithoutPropertiesLoose(e, t); if (Object.getOwnPropertySymbols) { var n = Object.getOwnPropertySymbols(e); for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]); } return i; }
function support_objectWithoutPropertiesLoose(r, e) { if (null == r) return {}; var t = {}; for (var n in r) if ({}.hasOwnProperty.call(r, n)) { if (-1 !== e.indexOf(n)) continue; t[n] = r[n]; } return t; }

var SupportIcon = function SupportIcon(_ref) {
  var _ref$fill = _ref.fill,
    fill = _ref$fill === void 0 ? "#555555" : _ref$fill,
    delegated = support_objectWithoutProperties(_ref, support_excluded);
  return wp.element.createElement("svg", support_extends({
    width: "20",
    height: "22",
    viewBox: "0 0 20 22",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, delegated, {
    __self: support_this,
    __source: {
      fileName: support_jsxFileName,
      lineNumber: 5,
      columnNumber: 5
    }
  }), wp.element.createElement("path", {
    d: "M19.3359 7.51392L19.3164 7.45532C19.5378 8.02824 19.707 8.62069 19.8242 9.23267C19.9414 9.84465 20 10.4696 20 11.1077C20 11.7587 19.9414 12.3902 19.8242 13.0022C19.707 13.6012 19.5443 14.1676 19.3359 14.7014C19.2578 14.8707 19.1406 15.0139 18.9844 15.1311C18.8281 15.2353 18.6523 15.2874 18.457 15.2874C18.3919 15.2874 18.3333 15.2874 18.2812 15.2874C18.2292 15.2743 18.1771 15.2548 18.125 15.2288L17.832 15.1897C17.4154 15.984 16.8815 16.7066 16.2305 17.3577C15.5794 17.9957 14.8438 18.5295 14.0234 18.9592L14.1211 19.2327C14.1471 19.2847 14.1602 19.3368 14.1602 19.3889C14.1732 19.441 14.1797 19.4996 14.1797 19.5647C14.1797 19.76 14.1276 19.9358 14.0234 20.092C13.9193 20.2483 13.7956 20.359 13.6523 20.4241C13.0794 20.6454 12.487 20.8147 11.875 20.9319C11.263 21.0491 10.638 21.1077 10 21.1077C9.34896 21.1077 8.71745 21.0491 8.10547 20.9319C7.50651 20.8147 6.9401 20.6519 6.40625 20.4436C6.23698 20.3655 6.09375 20.2483 5.97656 20.092C5.8724 19.9358 5.82031 19.76 5.82031 19.5647C5.82031 19.4996 5.82031 19.441 5.82031 19.3889C5.83333 19.3368 5.85286 19.2847 5.87891 19.2327L5.91797 18.9397C5.1237 18.523 4.40104 17.9957 3.75 17.3577C3.11198 16.7066 2.57812 15.9644 2.14844 15.1311L1.875 15.2483C1.82292 15.2613 1.77083 15.2743 1.71875 15.2874C1.66667 15.3004 1.60807 15.3069 1.54297 15.3069C1.34766 15.3069 1.17188 15.2548 1.01562 15.1506C0.859375 15.0334 0.748698 14.9032 0.683594 14.76C0.46224 14.2001 0.292969 13.6142 0.175781 13.0022C0.0585938 12.3902 0 11.7587 0 11.1077C0 10.4566 0.0585938 9.83162 0.175781 9.23267C0.292969 8.62069 0.455729 8.04777 0.664062 7.51392C0.742188 7.34465 0.859375 7.20793 1.01562 7.10376C1.17188 6.98657 1.34766 6.92798 1.54297 6.92798C1.60807 6.92798 1.66667 6.93449 1.71875 6.94751C1.77083 6.94751 1.82292 6.96053 1.875 6.98657L2.16797 7.02563C2.58464 6.23136 3.11198 5.51522 3.75 4.8772C4.40104 4.22616 5.14323 3.68579 5.97656 3.2561L5.85938 2.98267C5.84635 2.93058 5.83333 2.8785 5.82031 2.82642C5.80729 2.77433 5.80078 2.71574 5.80078 2.65063C5.80078 2.45532 5.85286 2.27954 5.95703 2.12329C6.07422 1.96704 6.20443 1.85636 6.34766 1.79126C6.90755 1.56991 7.49349 1.40063 8.10547 1.28345C8.71745 1.16626 9.34896 1.10767 10 1.10767C10.651 1.10767 11.276 1.16626 11.875 1.28345C12.487 1.40063 13.0599 1.5634 13.5938 1.77173C13.763 1.84985 13.8997 1.96704 14.0039 2.12329C14.1211 2.27954 14.1797 2.45532 14.1797 2.65063C14.1797 2.71574 14.1732 2.77433 14.1602 2.82642C14.1602 2.8785 14.1471 2.93058 14.1211 2.98267L14.082 3.27563C14.8763 3.6923 15.5924 4.22616 16.2305 4.8772C16.8815 5.51522 17.4219 6.2509 17.8516 7.08423L18.125 6.96704C18.1771 6.95402 18.2292 6.941 18.2812 6.92798C18.3333 6.91496 18.3919 6.90845 18.457 6.90845C18.6523 6.90845 18.8281 6.96704 18.9844 7.08423C19.1536 7.20142 19.2708 7.34465 19.3359 7.51392ZM5.95703 12.7288L5.97656 12.7483C5.8724 12.4879 5.78776 12.2209 5.72266 11.9475C5.67057 11.6741 5.64453 11.3941 5.64453 11.1077C5.64453 10.8212 5.67057 10.5413 5.72266 10.2678C5.78776 9.99438 5.86589 9.73397 5.95703 9.48657L1.75781 7.9436L1.5625 7.78735C1.36719 8.30819 1.21094 8.84855 1.09375 9.40845C0.989583 9.95532 0.9375 10.5217 0.9375 11.1077C0.9375 11.6936 0.989583 12.26 1.09375 12.8069C1.19792 13.3538 1.34766 13.8681 1.54297 14.3499L5.95703 12.7288ZM10 2.04517C9.41406 2.04517 8.84766 2.09725 8.30078 2.20142C7.75391 2.30558 7.23958 2.45532 6.75781 2.65063L8.35938 7.08423C8.60677 6.98006 8.86719 6.90194 9.14062 6.84985C9.42708 6.78475 9.71354 6.7522 10 6.7522C10.2865 6.7522 10.5664 6.78475 10.8398 6.84985C11.1133 6.90194 11.3737 6.97355 11.6211 7.0647L13.1641 2.86548L13.3008 2.67017C12.793 2.47485 12.2591 2.32511 11.6992 2.22095C11.1523 2.10376 10.5859 2.04517 10 2.04517ZM10 20.1702C10.5859 20.1702 11.1523 20.1181 11.6992 20.0139C12.2461 19.9097 12.7604 19.76 13.2422 19.5647L11.6406 15.1311C11.3932 15.2353 11.1263 15.3199 10.8398 15.385C10.5664 15.4371 10.2865 15.4631 10 15.4631C9.71354 15.4631 9.43359 15.4371 9.16016 15.385C8.88672 15.3199 8.6263 15.2418 8.37891 15.1506L6.69922 19.5452C7.20703 19.7405 7.73438 19.8902 8.28125 19.9944C8.84115 20.1116 9.41406 20.1702 10 20.1702ZM13.1641 9.83813C13.151 9.78605 13.138 9.73397 13.125 9.68188C13.112 9.61678 13.1055 9.55168 13.1055 9.48657C13.1055 9.29126 13.1576 9.11548 13.2617 8.95923C13.3789 8.78996 13.5286 8.67277 13.7109 8.60767L13.8086 8.54907C13.6393 8.30168 13.4505 8.07381 13.2422 7.86548C13.0339 7.64412 12.7995 7.44881 12.5391 7.27954L12.5 7.39673C12.4349 7.57902 12.3177 7.72876 12.1484 7.84595C11.9922 7.95011 11.8164 8.0022 11.6211 8.0022C11.556 8.0022 11.4909 7.99569 11.4258 7.98267C11.3737 7.96965 11.3281 7.95662 11.2891 7.9436C11.0938 7.86548 10.8854 7.80688 10.6641 7.76782C10.4427 7.71574 10.2214 7.6897 10 7.6897C9.77865 7.6897 9.55729 7.71574 9.33594 7.76782C9.1276 7.80688 8.93229 7.85897 8.75 7.92407C8.6849 7.95011 8.61979 7.96965 8.55469 7.98267C8.5026 7.99569 8.44401 8.0022 8.37891 8.0022C8.18359 8.0022 8.0013 7.9436 7.83203 7.82642C7.67578 7.70923 7.5651 7.566 7.5 7.39673L7.44141 7.29907C7.19401 7.45532 6.96615 7.64412 6.75781 7.86548C6.54948 8.07381 6.35417 8.30819 6.17188 8.5686L6.28906 8.60767C6.47135 8.67277 6.61458 8.78996 6.71875 8.95923C6.83594 9.11548 6.89453 9.29126 6.89453 9.48657C6.89453 9.55168 6.88802 9.61678 6.875 9.68188C6.875 9.73397 6.86198 9.77954 6.83594 9.8186C6.75781 10.0139 6.69271 10.2222 6.64062 10.4436C6.60156 10.6519 6.58203 10.8668 6.58203 11.0881C6.58203 11.3225 6.60156 11.5504 6.64062 11.7717C6.69271 11.9801 6.75781 12.1754 6.83594 12.3577C6.84896 12.4097 6.86198 12.4683 6.875 12.5334C6.88802 12.5855 6.89453 12.6441 6.89453 12.7092C6.89453 12.9176 6.83594 13.0999 6.71875 13.2561C6.61458 13.4124 6.47135 13.523 6.28906 13.5881L6.19141 13.6467C6.36068 13.8941 6.54948 14.1285 6.75781 14.3499C6.96615 14.5582 7.20052 14.747 7.46094 14.9163L7.5 14.7991C7.5651 14.6298 7.67578 14.4866 7.83203 14.3694C8.0013 14.2522 8.18359 14.1936 8.37891 14.1936C8.44401 14.1936 8.5026 14.2001 8.55469 14.2131C8.61979 14.2262 8.67188 14.2392 8.71094 14.2522C8.90625 14.3433 9.10807 14.4084 9.31641 14.4475C9.53776 14.4866 9.76562 14.5061 10 14.5061C10.2214 14.5061 10.4362 14.4866 10.6445 14.4475C10.8659 14.4084 11.0677 14.3499 11.25 14.2717C11.3021 14.2457 11.3607 14.2262 11.4258 14.2131C11.4909 14.2001 11.556 14.1936 11.6211 14.1936C11.8164 14.1936 11.9922 14.2522 12.1484 14.3694C12.3047 14.4866 12.4219 14.6363 12.5 14.8186L12.5586 14.9163C12.806 14.747 13.0339 14.5582 13.2422 14.3499C13.4505 14.1285 13.6458 13.8876 13.8281 13.6272L13.7109 13.5881C13.5286 13.523 13.3789 13.4124 13.2617 13.2561C13.1576 13.0999 13.1055 12.9176 13.1055 12.7092C13.1055 12.6441 13.1055 12.5855 13.1055 12.5334C13.1185 12.4683 13.138 12.4163 13.1641 12.3772C13.2422 12.1819 13.3008 11.9801 13.3398 11.7717C13.3919 11.5504 13.418 11.3225 13.418 11.0881C13.418 10.8668 13.3919 10.6519 13.3398 10.4436C13.3008 10.2222 13.2422 10.0204 13.1641 9.83813ZM18.457 14.3499L18.4375 14.428C18.6328 13.9071 18.7826 13.3733 18.8867 12.8264C19.0039 12.2665 19.0625 11.6936 19.0625 11.1077C19.0625 10.5217 19.0104 9.95532 18.9062 9.40845C18.8021 8.86157 18.6523 8.34725 18.457 7.86548L14.0234 9.46704C14.1276 9.71444 14.2057 9.98136 14.2578 10.2678C14.3229 10.5413 14.3555 10.8212 14.3555 11.1077C14.3555 11.3941 14.3229 11.6741 14.2578 11.9475C14.2057 12.2209 14.1341 12.4814 14.043 12.7288L18.2422 14.2717L18.457 14.3499Z",
    fill: fill,
    __self: support_this,
    __source: {
      fileName: support_jsxFileName,
      lineNumber: 13,
      columnNumber: 7
    }
  }));
};

;// ./src/icons/settings.jsx
var settings_excluded = ["fill"];
var settings_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/icons/settings.jsx",
  settings_this = undefined;
function settings_extends() { return settings_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, settings_extends.apply(null, arguments); }
function settings_objectWithoutProperties(e, t) { if (null == e) return {}; var o, r, i = settings_objectWithoutPropertiesLoose(e, t); if (Object.getOwnPropertySymbols) { var n = Object.getOwnPropertySymbols(e); for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]); } return i; }
function settings_objectWithoutPropertiesLoose(r, e) { if (null == r) return {}; var t = {}; for (var n in r) if ({}.hasOwnProperty.call(r, n)) { if (-1 !== e.indexOf(n)) continue; t[n] = r[n]; } return t; }

var SettingsIcon = function SettingsIcon(_ref) {
  var _ref$fill = _ref.fill,
    fill = _ref$fill === void 0 ? "#555555" : _ref$fill,
    delegated = settings_objectWithoutProperties(_ref, settings_excluded);
  return wp.element.createElement("svg", settings_extends({
    width: "20",
    height: "21",
    viewBox: "0 0 20 21",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, delegated, {
    __self: settings_this,
    __source: {
      fileName: settings_jsxFileName,
      lineNumber: 5,
      columnNumber: 5
    }
  }), wp.element.createElement("path", {
    d: "M15.4492 14.8577H17.9883C18.3268 14.8577 18.6133 14.9749 18.8477 15.2092C19.082 15.4436 19.1992 15.7301 19.1992 16.0686V16.1467C19.1992 16.4853 19.082 16.7717 18.8477 17.0061C18.6133 17.2405 18.3268 17.3577 17.9883 17.3577H15.4492V17.3967C15.4492 17.7353 15.332 18.0217 15.0977 18.2561C14.8633 18.4905 14.5768 18.6077 14.2383 18.6077H14.1602C13.8216 18.6077 13.5352 18.4905 13.3008 18.2561C13.0664 18.0217 12.9492 17.7353 12.9492 17.3967V17.3577H2.91016C2.57161 17.3577 2.28516 17.2405 2.05078 17.0061C1.81641 16.7717 1.69922 16.4853 1.69922 16.1467V16.0686C1.69922 15.7301 1.81641 15.4436 2.05078 15.2092C2.28516 14.9749 2.57161 14.8577 2.91016 14.8577H12.9492V14.8186C12.9492 14.4801 13.0664 14.1936 13.3008 13.9592C13.5352 13.7249 13.8216 13.6077 14.1602 13.6077H14.2383C14.5768 13.6077 14.8633 13.7249 15.0977 13.9592C15.332 14.1936 15.4492 14.4801 15.4492 14.8186V14.8577ZM9.19922 4.85767V4.8186C9.19922 4.48006 9.31641 4.1936 9.55078 3.95923C9.78516 3.72485 10.0716 3.60767 10.4102 3.60767H10.4883C10.8268 3.60767 11.1133 3.72485 11.3477 3.95923C11.582 4.1936 11.6992 4.48006 11.6992 4.8186V4.85767H17.9883C18.3268 4.85767 18.6133 4.97485 18.8477 5.20923C19.082 5.4436 19.1992 5.73006 19.1992 6.0686V6.14673C19.1992 6.48527 19.082 6.77173 18.8477 7.0061C18.6133 7.24048 18.3268 7.35767 17.9883 7.35767H11.6992V7.39673C11.6992 7.73527 11.582 8.02173 11.3477 8.2561C11.1133 8.49048 10.8268 8.60767 10.4883 8.60767H10.4102C10.0716 8.60767 9.78516 8.49048 9.55078 8.2561C9.31641 8.02173 9.19922 7.73527 9.19922 7.39673V7.35767H2.91016C2.57161 7.35767 2.28516 7.24048 2.05078 7.0061C1.81641 6.77173 1.69922 6.48527 1.69922 6.14673V6.0686C1.69922 5.73006 1.81641 5.4436 2.05078 5.20923C2.28516 4.97485 2.57161 4.85767 2.91016 4.85767H9.19922ZM5.44922 9.85767V9.8186C5.44922 9.48006 5.56641 9.1936 5.80078 8.95923C6.03516 8.72485 6.32161 8.60767 6.66016 8.60767H6.73828C7.07682 8.60767 7.36328 8.72485 7.59766 8.95923C7.83203 9.1936 7.94922 9.48006 7.94922 9.8186V9.85767H17.9883C18.3268 9.85767 18.6133 9.97485 18.8477 10.2092C19.082 10.4436 19.1992 10.7301 19.1992 11.0686V11.1467C19.1992 11.4853 19.082 11.7717 18.8477 12.0061C18.6133 12.2405 18.3268 12.3577 17.9883 12.3577H7.94922V12.3967C7.94922 12.7353 7.83203 13.0217 7.59766 13.2561C7.36328 13.4905 7.07682 13.6077 6.73828 13.6077H6.66016C6.32161 13.6077 6.03516 13.4905 5.80078 13.2561C5.56641 13.0217 5.44922 12.7353 5.44922 12.3967V12.3577H2.91016C2.57161 12.3577 2.28516 12.2405 2.05078 12.0061C1.81641 11.7717 1.69922 11.4853 1.69922 11.1467V11.0686C1.69922 10.7301 1.81641 10.4436 2.05078 10.2092C2.28516 9.97485 2.57161 9.85767 2.91016 9.85767H5.44922Z",
    fill: fill,
    __self: settings_this,
    __source: {
      fileName: settings_jsxFileName,
      lineNumber: 13,
      columnNumber: 7
    }
  }));
};

;// ./src/icons/server.jsx
var server_excluded = ["fill"];
var server_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/icons/server.jsx",
  server_this = undefined;
function server_extends() { return server_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, server_extends.apply(null, arguments); }
function server_objectWithoutProperties(e, t) { if (null == e) return {}; var o, r, i = server_objectWithoutPropertiesLoose(e, t); if (Object.getOwnPropertySymbols) { var n = Object.getOwnPropertySymbols(e); for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]); } return i; }
function server_objectWithoutPropertiesLoose(r, e) { if (null == r) return {}; var t = {}; for (var n in r) if ({}.hasOwnProperty.call(r, n)) { if (-1 !== e.indexOf(n)) continue; t[n] = r[n]; } return t; }

var ServerIcon = function ServerIcon(_ref) {
  var _ref$fill = _ref.fill,
    fill = _ref$fill === void 0 ? "#555555" : _ref$fill,
    delegated = server_objectWithoutProperties(_ref, server_excluded);
  return wp.element.createElement("svg", server_extends({
    width: "20",
    height: "21",
    viewBox: "0 0 20 21",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, delegated, {
    __self: server_this,
    __source: {
      fileName: server_jsxFileName,
      lineNumber: 5,
      columnNumber: 5
    }
  }), wp.element.createElement("path", {
    d: "M19.0234 3.60767C19.2969 3.62069 19.5247 3.72485 19.707 3.92017C19.9023 4.10246 20 4.33032 20 4.60376V9.27173C20 9.54517 19.9023 9.77954 19.707 9.97485C19.5247 10.1702 19.2969 10.2678 19.0234 10.2678H0.976562C0.703125 10.2678 0.46875 10.1702 0.273438 9.97485C0.0911458 9.77954 0 9.54517 0 9.27173V4.60376C0 4.33032 0.0911458 4.10246 0.273438 3.92017C0.46875 3.72485 0.703125 3.62069 0.976562 3.60767H19.0234ZM2.69531 7.88501H2.71484C2.97526 7.88501 3.19661 7.79386 3.37891 7.61157C3.57422 7.42928 3.67188 7.20793 3.67188 6.94751C3.67188 6.68709 3.57422 6.46574 3.37891 6.28345C3.19661 6.08813 2.97526 5.99048 2.71484 5.99048C2.45443 5.99048 2.23307 6.08813 2.05078 6.28345C1.86849 6.46574 1.77734 6.68709 1.77734 6.94751C1.77734 7.19491 1.86198 7.40975 2.03125 7.59204C2.21354 7.77433 2.4349 7.87199 2.69531 7.88501ZM19.0234 11.9475C19.2969 11.9475 19.5247 12.0452 19.707 12.2405C19.9023 12.4358 20 12.6702 20 12.9436V17.6116C20 17.885 19.9023 18.1194 19.707 18.3147C19.5247 18.497 19.2969 18.5946 19.0234 18.6077H0.976562C0.703125 18.5946 0.46875 18.497 0.273438 18.3147C0.0911458 18.1194 0 17.885 0 17.6116V12.9436C0 12.6702 0.0911458 12.4358 0.273438 12.2405C0.46875 12.0452 0.703125 11.9475 0.976562 11.9475H19.0234ZM2.69531 16.2249H2.71484C2.97526 16.2249 3.19661 16.1337 3.37891 15.9514C3.57422 15.7561 3.67188 15.5282 3.67188 15.2678C3.67188 15.0074 3.57422 14.7861 3.37891 14.6038C3.19661 14.4215 2.97526 14.3303 2.71484 14.3303C2.45443 14.3303 2.23307 14.4215 2.05078 14.6038C1.86849 14.7861 1.77734 15.0074 1.77734 15.2678C1.77734 15.5282 1.86198 15.7496 2.03125 15.9319C2.21354 16.1142 2.4349 16.2118 2.69531 16.2249Z",
    fill: fill,
    __self: server_this,
    __source: {
      fileName: server_jsxFileName,
      lineNumber: 13,
      columnNumber: 7
    }
  }));
};

;// ./src/icons/google.jsx
var google_excluded = ["fill"];
var google_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/icons/google.jsx",
  google_this = undefined;
function google_extends() { return google_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, google_extends.apply(null, arguments); }
function google_objectWithoutProperties(e, t) { if (null == e) return {}; var o, r, i = google_objectWithoutPropertiesLoose(e, t); if (Object.getOwnPropertySymbols) { var n = Object.getOwnPropertySymbols(e); for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]); } return i; }
function google_objectWithoutPropertiesLoose(r, e) { if (null == r) return {}; var t = {}; for (var n in r) if ({}.hasOwnProperty.call(r, n)) { if (-1 !== e.indexOf(n)) continue; t[n] = r[n]; } return t; }

var GoogleIcon = function GoogleIcon(_ref) {
  var _ref$fill = _ref.fill,
    fill = _ref$fill === void 0 ? "#1a1a1a" : _ref$fill,
    delegated = google_objectWithoutProperties(_ref, google_excluded);
  return wp.element.createElement("svg", google_extends({
    width: "57",
    height: "15",
    viewBox: "0 0 57 15",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, delegated, {
    __self: google_this,
    __source: {
      fileName: google_jsxFileName,
      lineNumber: 5,
      columnNumber: 5
    }
  }), wp.element.createElement("path", {
    d: "M41.2468 8.92073L42.4261 9.71793C42.1099 10.1979 41.6814 10.591 41.1788 10.8619C40.6763 11.1328 40.1154 11.2731 39.5462 11.2703C37.5818 11.2703 36.1187 9.72893 36.1187 7.76741C36.1187 5.67965 37.5968 4.26367 39.3803 4.26367C41.1638 4.26367 42.0535 5.71164 42.3381 6.49286L42.4933 6.89105L37.8706 8.82728C38.2225 9.53023 38.7703 9.88713 39.5462 9.88713C40.3222 9.88713 40.8592 9.49903 41.2468 8.91652V8.92073ZM37.6225 7.65797L40.709 6.35733C40.5388 5.9221 40.0318 5.6123 39.4267 5.6123C38.6566 5.6123 37.5868 6.30513 37.6225 7.65797Z",
    fill: fill,
    __self: google_this,
    __source: {
      fileName: google_jsxFileName,
      lineNumber: 13,
      columnNumber: 7
    }
  }), wp.element.createElement("path", {
    d: "M33.8921 0.692627H35.381V10.9504H33.8921V0.692627Z",
    fill: fill,
    __self: google_this,
    __source: {
      fileName: google_jsxFileName,
      lineNumber: 17,
      columnNumber: 7
    }
  }), wp.element.createElement("path", {
    d: "M31.5451 4.53643H32.9825V10.766C32.9825 13.3513 31.4778 14.4162 29.7001 14.4162C28.0245 14.4162 27.0161 13.273 26.6394 12.3445L27.9573 11.7889C28.1955 12.3605 28.769 13.0365 29.7001 13.0365C30.8421 13.0365 31.5451 12.3184 31.5451 10.9756V10.4705H31.4936C31.1525 10.8915 30.4977 11.2678 29.6736 11.2678C27.9473 11.2678 26.3647 9.74152 26.3647 7.77498C26.3647 5.80845 27.9473 4.2561 29.6736 4.2561C30.496 4.2561 31.1525 4.62819 31.4936 5.03732H31.5451V4.53222V4.53643ZM31.6488 7.77751C31.6488 6.54001 30.8371 5.6384 29.803 5.6384C28.769 5.6384 27.8834 6.54001 27.8834 7.77751C27.8834 8.99986 28.7623 9.88212 29.8064 9.88212C30.8504 9.88212 31.6521 8.99649 31.6521 7.77751H31.6488Z",
    fill: fill,
    __self: google_this,
    __source: {
      fileName: google_jsxFileName,
      lineNumber: 21,
      columnNumber: 7
    }
  }), wp.element.createElement("path", {
    d: "M18.1919 7.75147C18.1919 9.77193 16.6407 11.2543 14.7377 11.2543C12.8347 11.2543 11.2852 9.76513 11.2852 7.75147C11.2852 5.72179 12.8364 4.24268 14.7385 4.24268C16.6407 4.24268 18.1919 5.72179 18.1919 7.75147ZM16.6822 7.75147C16.6822 6.48871 15.7826 5.6275 14.7377 5.6275C13.6928 5.6275 12.794 6.49292 12.794 7.75147C12.794 9.01002 13.692 9.87463 14.7377 9.87463C15.7834 9.87463 16.6798 8.99991 16.6798 7.75147H16.6822Z",
    fill: fill,
    __self: google_this,
    __source: {
      fileName: google_jsxFileName,
      lineNumber: 25,
      columnNumber: 7
    }
  }), wp.element.createElement("path", {
    d: "M25.7368 7.76961C25.7368 9.79003 24.1865 11.2725 22.2834 11.2725C20.3804 11.2725 18.8301 9.79003 18.8301 7.76961C18.8301 5.74919 20.3812 4.26587 22.2834 4.26587C24.1856 4.26587 25.7368 5.73236 25.7368 7.76961ZM24.2222 7.76961C24.2222 6.50685 23.3225 5.64565 22.2785 5.64565C21.2344 5.64565 20.3339 6.50685 20.3339 7.76961C20.3339 9.03233 21.2336 9.89353 22.2776 9.89353C23.3217 9.89353 24.2222 9.00964 24.2222 7.76961Z",
    fill: fill,
    __self: google_this,
    __source: {
      fileName: google_jsxFileName,
      lineNumber: 29,
      columnNumber: 7
    }
  }), wp.element.createElement("path", {
    d: "M5.60718 9.73383C3.44106 9.73383 1.74552 7.9609 1.74552 5.7637C1.74552 3.5665 3.44106 1.79274 5.60718 1.79274C6.59609 1.7794 7.55033 2.16204 8.26295 2.85767L9.30202 1.80369C8.41981 0.950897 7.24629 0.299316 5.60718 0.299316C2.63935 0.299316 0.14209 2.75328 0.14209 5.7637C0.14209 8.77411 2.63935 11.228 5.60718 11.228C7.20977 11.228 8.41981 10.6935 9.36593 9.69673C10.3378 8.71098 10.6374 7.32615 10.6374 6.20398C10.635 5.87515 10.6055 5.54711 10.5494 5.22324H5.60718V6.6813H9.12691C9.024 7.59386 8.73933 8.21766 8.32022 8.64279C7.81396 9.16223 7.01225 9.73723 5.60635 9.73723L5.60718 9.73383Z",
    fill: fill,
    __self: google_this,
    __source: {
      fileName: google_jsxFileName,
      lineNumber: 33,
      columnNumber: 7
    }
  }));
};

;// ./src/icons/reviews.jsx
var reviews_excluded = ["fill"];
var reviews_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/icons/reviews.jsx",
  reviews_this = undefined;
function reviews_extends() { return reviews_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, reviews_extends.apply(null, arguments); }
function reviews_objectWithoutProperties(e, t) { if (null == e) return {}; var o, r, i = reviews_objectWithoutPropertiesLoose(e, t); if (Object.getOwnPropertySymbols) { var n = Object.getOwnPropertySymbols(e); for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]); } return i; }
function reviews_objectWithoutPropertiesLoose(r, e) { if (null == r) return {}; var t = {}; for (var n in r) if ({}.hasOwnProperty.call(r, n)) { if (-1 !== e.indexOf(n)) continue; t[n] = r[n]; } return t; }

var ReviewsIcon = function ReviewsIcon(_ref) {
  var _ref$fill = _ref.fill,
    fill = _ref$fill === void 0 ? "#1a1a1a" : _ref$fill,
    delegated = reviews_objectWithoutProperties(_ref, reviews_excluded);
  return wp.element.createElement("svg", reviews_extends({
    width: "68",
    height: "12",
    viewBox: "0 0 68 12",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, delegated, {
    __self: reviews_this,
    __source: {
      fileName: reviews_jsxFileName,
      lineNumber: 5,
      columnNumber: 5
    }
  }), wp.element.createElement("path", {
    opacity: "0.4",
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M9.68162 2.98071L9.20499 3.21979C10.1342 3.97012 10.5884 4.93933 10.6728 6.11213C10.6023 8.37585 9.53419 9.83079 7.69948 10.4547C5.57977 11.0934 4.23659 10.5766 2.93685 9.25236L2.85498 9.76144L3.38341 10.312C5.22154 12.2276 8.26909 12.2905 10.1846 10.4523C12.1002 8.61416 12.1631 5.56671 10.3249 3.65107L9.68162 2.98071Z",
    fill: fill,
    __self: reviews_this,
    __source: {
      fileName: reviews_jsxFileName,
      lineNumber: 13,
      columnNumber: 7
    }
  }), wp.element.createElement("path", {
    opacity: "0.4",
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M2.5933 9.47791L2.91264 9.19899C1.58587 7.31883 1.53027 4.56624 3.34601 3.05086C4.69195 1.92759 7.36257 1.34536 9.49916 3.22656L9.65864 2.94603L9.13799 2.40336C7.29976 0.48782 4.25231 0.424921 2.33667 2.26314C0.421134 4.10127 0.358329 7.14882 2.19645 9.06436L2.5933 9.47791Z",
    fill: fill,
    __self: reviews_this,
    __source: {
      fileName: reviews_jsxFileName,
      lineNumber: 20,
      columnNumber: 7
    }
  }), wp.element.createElement("path", {
    d: "M1.44531 6.37144C1.44531 9.03296 3.60325 11.1909 6.26477 11.1909C8.92676 11.1909 11.0846 9.03296 11.0846 6.37144C11.0846 3.70945 8.92676 1.55151 6.26477 1.55151C3.60325 1.55151 1.44531 3.70945 1.44531 6.37144ZM4.79986 7.24312C4.85052 7.08279 4.79417 6.90804 4.65945 6.80738C4.30274 6.54098 3.59946 6.01577 3.1269 5.66285C3.05869 5.61191 3.0308 5.52301 3.05765 5.44218C3.0845 5.36135 3.16001 5.3069 3.24521 5.3069H5.07679C5.24727 5.3069 5.3985 5.1976 5.4521 5.03585C5.59649 4.59973 5.88348 3.73298 6.07379 3.15844C6.10054 3.07771 6.17597 3.02306 6.26107 3.02297C6.34617 3.02287 6.42187 3.07723 6.44882 3.15787C6.64112 3.73289 6.93152 4.60105 7.07734 5.03699C7.13132 5.19817 7.28226 5.3069 7.45227 5.3069C7.88555 5.3069 8.72022 5.3069 9.2885 5.3069C9.37341 5.3069 9.44893 5.36116 9.47587 5.4418C9.50282 5.52244 9.4753 5.61124 9.40738 5.66238C8.93625 6.01729 8.23259 6.54724 7.87663 6.81535C7.74267 6.9162 7.68698 7.09066 7.73755 7.25043C7.87521 7.68513 8.15128 8.5569 8.3378 9.14576C8.36379 9.22802 8.33343 9.31776 8.26275 9.36729C8.19208 9.41681 8.09749 9.41482 8.02899 9.36226C7.55568 8.9989 6.86416 8.4681 6.50204 8.19022C6.35964 8.08093 6.16155 8.08121 6.01953 8.19107C5.66091 8.46829 4.97879 8.99577 4.50946 9.35865C4.44115 9.4115 4.34638 9.41387 4.27551 9.36435C4.20464 9.31492 4.17409 9.22517 4.20009 9.14282C4.3866 8.55225 4.6623 7.67896 4.79986 7.24312Z",
    fill: fill,
    __self: reviews_this,
    __source: {
      fileName: reviews_jsxFileName,
      lineNumber: 27,
      columnNumber: 7
    }
  }), wp.element.createElement("path", {
    d: "M21.0917 9.10085L19.4816 7.02818C20.5154 6.78615 21.2381 6.11128 21.2381 4.99272V4.97361C21.2381 4.41254 21.033 3.91337 20.6864 3.56647C20.2435 3.12362 19.5501 2.85864 18.6848 2.85864H16.1305C15.8795 2.85864 15.6633 3.07273 15.6633 3.33553V9.42915C15.6633 9.69204 15.8795 9.90612 16.1305 9.90612C16.3923 9.90612 16.607 9.69146 16.607 9.42915V7.20539H18.4369L20.3316 9.66684C20.4357 9.80559 20.5762 9.90612 20.7612 9.90612C21.0002 9.90612 21.2381 9.68987 21.2381 9.4385C21.2381 9.31101 21.1838 9.20472 21.0917 9.10085ZM20.2846 5.00281L20.2847 5.02225C20.2847 5.85197 19.5845 6.33946 18.6163 6.33946H16.607V3.74401H18.6261C19.6668 3.74401 20.2846 4.21064 20.2846 5.00281Z",
    fill: fill,
    __self: reviews_this,
    __source: {
      fileName: reviews_jsxFileName,
      lineNumber: 31,
      columnNumber: 7
    }
  }), wp.element.createElement("path", {
    d: "M22.614 9.38051C22.614 9.64298 22.8303 9.85715 23.0812 9.85715H27.371C27.6143 9.85715 27.8087 9.66242 27.8087 9.41914C27.8087 9.17619 27.6144 8.98146 27.371 8.98146H23.5579V6.76637H26.8836C27.127 6.76637 27.3216 6.57198 27.3216 6.3287C27.3216 6.09751 27.127 5.89068 26.8836 5.89068H23.5579V3.73467H27.3224C27.5652 3.73467 27.7604 3.53994 27.7604 3.29665C27.7604 3.05337 27.5652 2.85864 27.3224 2.85864H23.0812C22.8304 2.85864 22.614 3.07273 22.614 3.33562V9.38051Z",
    fill: fill,
    __self: reviews_this,
    __source: {
      fileName: reviews_jsxFileName,
      lineNumber: 35,
      columnNumber: 7
    }
  }), wp.element.createElement("path", {
    d: "M31.7765 9.93522L31.8276 9.93505C32.083 9.93063 32.2421 9.7868 32.3431 9.56195L34.9868 3.45532C35.0104 3.39658 35.0223 3.33785 35.0223 3.26735C35.0223 3.02715 34.8192 2.80981 34.5551 2.80981C34.3458 2.80981 34.1789 2.96783 34.0971 3.13061C34.0962 3.13244 34.0954 3.13428 34.0945 3.1362L31.806 8.61801L29.5265 3.15564C29.4453 2.9579 29.2796 2.80981 29.0469 2.80981C28.7836 2.80981 28.5696 3.03816 28.5696 3.27669C28.5696 3.36004 28.5823 3.41928 28.6176 3.49019L31.248 9.56012C31.3397 9.78872 31.5133 9.93522 31.7765 9.93522Z",
    fill: fill,
    __self: reviews_this,
    __source: {
      fileName: reviews_jsxFileName,
      lineNumber: 39,
      columnNumber: 7
    }
  }), wp.element.createElement("path", {
    d: "M36.2712 3.2867V9.42896C36.2712 9.69185 36.4875 9.90594 36.7384 9.90594C37.0002 9.90594 37.215 9.69127 37.215 9.42896V3.2867C37.215 3.0244 37.0002 2.80981 36.7384 2.80981C36.4875 2.80981 36.2712 3.02381 36.2712 3.2867Z",
    fill: fill,
    __self: reviews_this,
    __source: {
      fileName: reviews_jsxFileName,
      lineNumber: 43,
      columnNumber: 7
    }
  }), wp.element.createElement("path", {
    d: "M43.6622 2.85881L43.6508 2.85864H39.4095C39.1589 2.85864 38.9426 3.07273 38.9426 3.33562V9.38051C38.9426 9.64298 39.1588 9.85715 39.4095 9.85715H43.6995C43.9424 9.85715 44.1371 9.66242 44.1371 9.41914C44.1371 9.17619 43.9424 8.98146 43.6995 8.98146H39.8863V6.76637H43.2117C43.4555 6.76637 43.6497 6.57198 43.6497 6.3287C43.6497 6.09751 43.4555 5.89068 43.2117 5.89068H39.8863V3.73467H43.6508C43.8937 3.73467 44.0888 3.53994 44.0888 3.29665C44.0888 3.05721 43.8997 2.86473 43.6622 2.85881Z",
    fill: fill,
    __self: reviews_this,
    __source: {
      fileName: reviews_jsxFileName,
      lineNumber: 47,
      columnNumber: 7
    }
  }), wp.element.createElement("path", {
    d: "M52.0234 9.94473H52.1035C52.3309 9.93989 52.4944 9.78404 52.5745 9.55494C52.5741 9.55569 54.7583 3.49169 54.7583 3.49169C54.7586 3.49085 54.7588 3.4901 54.7592 3.48927C54.781 3.42252 54.8122 3.34368 54.8122 3.27694C54.8122 3.03791 54.5861 2.81006 54.3352 2.81006C54.1106 2.81006 53.9534 2.95873 53.8821 3.17148C53.8825 3.17056 52.0755 8.36788 52.0755 8.36788L50.3715 3.17165C50.301 2.9594 50.1446 2.81006 49.9092 2.81006H49.8601C49.6126 2.81006 49.468 2.9594 49.3974 3.17165L47.6935 8.36772L45.8962 3.18967C45.8266 2.98084 45.6479 2.81006 45.4143 2.81006C45.1526 2.81006 44.928 3.03682 44.928 3.2867C44.928 3.35453 44.9486 3.42227 44.9711 3.48969L47.1556 9.5551C47.236 9.79522 47.3996 9.94022 47.6266 9.94473H47.7067C47.9338 9.93989 48.0977 9.78395 48.1773 9.55477L49.8604 4.55309L51.5528 9.55494C51.6328 9.78404 51.7967 9.93989 52.0234 9.94473Z",
    fill: fill,
    __self: reviews_this,
    __source: {
      fileName: reviews_jsxFileName,
      lineNumber: 51,
      columnNumber: 7
    }
  }), wp.element.createElement("path", {
    d: "M59.7361 7.9956V8.01504C59.7361 8.64486 59.1602 9.08854 58.3022 9.08854C57.459 9.08854 56.833 8.833 56.2077 8.30321L56.206 8.30162C56.1441 8.24122 56.035 8.19125 55.9136 8.19125C55.6615 8.19125 55.4558 8.3944 55.4558 8.65846C55.4558 8.81497 55.5295 8.94713 55.6371 9.02096C56.413 9.6366 57.2585 9.95447 58.273 9.95447C59.6828 9.95447 60.6896 9.1542 60.6896 7.9372V7.91734C60.6896 6.83608 59.9749 6.24614 58.3985 5.90241C58.3984 5.90241 58.3987 5.90241 58.3985 5.90241C56.9664 5.59539 56.6144 5.27517 56.6144 4.66112V4.64168C56.6144 4.06835 57.1523 3.62691 57.9802 3.62691C58.5957 3.62691 59.1352 3.79795 59.694 4.20501C59.6938 4.20485 59.6941 4.20517 59.694 4.20501C59.7771 4.26475 59.8614 4.28986 59.9694 4.28986C60.2217 4.28986 60.4265 4.08562 60.4265 3.8324C60.4265 3.65428 60.3214 3.52271 60.226 3.45104C60.2255 3.45071 60.225 3.45029 60.2245 3.44996C59.5859 2.99101 58.918 2.76099 57.9997 2.76099C56.6486 2.76099 55.6703 3.59262 55.6703 4.7101V4.72878C55.6703 5.89005 56.4046 6.43069 58.0303 6.78385C58.0301 6.78385 58.0304 6.78385 58.0303 6.78385C59.3943 7.07176 59.7361 7.39231 59.7361 7.9956Z",
    fill: fill,
    __self: reviews_this,
    __source: {
      fileName: reviews_jsxFileName,
      lineNumber: 55,
      columnNumber: 7
    }
  }), wp.element.createElement("path", {
    d: "M61.7639 8.94686L61.7556 8.94678C61.5723 8.94678 61.4417 9.0781 61.4417 9.25514V9.36427C61.4417 9.54106 61.5725 9.67821 61.7556 9.67821C61.9322 9.67821 62.0639 9.54139 62.0639 9.36427C62.0639 9.39313 62.0406 9.41641 62.0118 9.41641C62.0118 9.41641 62.0639 9.41057 62.0639 9.36185V9.25514C62.0639 9.08052 61.9367 8.95087 61.7639 8.94686ZM61.4985 9.41566C61.4956 9.41624 61.4939 9.41641 61.4939 9.41641C61.4955 9.41641 61.4969 9.41583 61.4985 9.41566Z",
    fill: fill,
    __self: reviews_this,
    __source: {
      fileName: reviews_jsxFileName,
      lineNumber: 59,
      columnNumber: 7
    }
  }), wp.element.createElement("path", {
    d: "M62.8764 6.96502V9.41337C62.8764 9.56272 62.992 9.67827 63.1408 9.67827C63.291 9.67827 63.4006 9.56305 63.4006 9.41337V6.96502C63.4006 6.81526 63.285 6.70012 63.1357 6.70012C62.9929 6.70012 62.8764 6.82227 62.8764 6.96502ZM62.8382 5.96702C62.8382 6.12712 62.9691 6.24267 63.1357 6.24267C63.3086 6.24267 63.4384 6.12679 63.4384 5.96702V5.89643C63.4384 5.72982 63.3086 5.62036 63.1357 5.62036C62.9691 5.62036 62.8381 5.72949 62.8381 5.89643L62.8382 5.96702Z",
    fill: fill,
    __self: reviews_this,
    __source: {
      fileName: reviews_jsxFileName,
      lineNumber: 63,
      columnNumber: 7
    }
  }), wp.element.createElement("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M67.1418 8.19326L67.1419 8.18142C67.1419 7.35662 66.4983 6.66748 65.6231 6.66748C64.7423 6.66748 64.0984 7.36797 64.0984 8.19218V8.20302C64.0984 9.02749 64.7368 9.71654 65.612 9.71654C66.4927 9.71654 67.1414 9.01698 67.1418 8.19326ZM66.6069 8.20302V8.19218C66.6069 7.61634 66.1776 7.14237 65.612 7.14237C65.0312 7.14237 64.6334 7.61617 64.6334 8.18142V8.19218C64.6334 8.76793 65.0576 9.23581 65.6231 9.23581C66.2035 9.23581 66.6069 8.76818 66.6069 8.20302Z",
    fill: fill,
    __self: reviews_this,
    __source: {
      fileName: reviews_jsxFileName,
      lineNumber: 67,
      columnNumber: 7
    }
  }));
};

;// ./src/icons/trustpilot.jsx
var trustpilot_excluded = ["fill"];
var trustpilot_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/icons/trustpilot.jsx",
  trustpilot_this = undefined;
function trustpilot_extends() { return trustpilot_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, trustpilot_extends.apply(null, arguments); }
function trustpilot_objectWithoutProperties(e, t) { if (null == e) return {}; var o, r, i = trustpilot_objectWithoutPropertiesLoose(e, t); if (Object.getOwnPropertySymbols) { var n = Object.getOwnPropertySymbols(e); for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]); } return i; }
function trustpilot_objectWithoutPropertiesLoose(r, e) { if (null == r) return {}; var t = {}; for (var n in r) if ({}.hasOwnProperty.call(r, n)) { if (-1 !== e.indexOf(n)) continue; t[n] = r[n]; } return t; }

var TrustPilotIcon = function TrustPilotIcon(_ref) {
  var _ref$fill = _ref.fill,
    fill = _ref$fill === void 0 ? "#1a1a1a" : _ref$fill,
    delegated = trustpilot_objectWithoutProperties(_ref, trustpilot_excluded);
  return wp.element.createElement("svg", trustpilot_extends({
    width: "57",
    height: "15",
    viewBox: "0 0 57 15",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, delegated, {
    __self: trustpilot_this,
    __source: {
      fileName: trustpilot_jsxFileName,
      lineNumber: 5,
      columnNumber: 5
    }
  }), wp.element.createElement("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M9.05649 5.7379H14.2201L10.0434 8.98287L11.6383 14.2313L7.46158 10.9864L10.4021 10.1712L10.0434 8.98287L7.46158 10.9864L3.27981 14.2313L4.87974 8.98287L0.697998 5.73257L5.86162 5.7379L7.46155 0.484131L9.05649 5.7379Z",
    fill: fill,
    __self: trustpilot_this,
    __source: {
      fileName: trustpilot_jsxFileName,
      lineNumber: 13,
      columnNumber: 7
    }
  }), wp.element.createElement("path", {
    d: "M15.2251 4.66162H20.942V5.80189H18.6941V12.2119H17.458V5.80189H15.2201V4.66162H15.2251ZM20.6977 6.74501H21.7544V7.80003H21.7743C21.8092 7.65083 21.874 7.50697 21.9687 7.36843C22.0634 7.22989 22.1781 7.09668 22.3126 6.98479C22.4472 6.86756 22.5967 6.77698 22.7612 6.70238C22.9257 6.63312 23.0951 6.59582 23.2646 6.59582C23.3942 6.59582 23.4889 6.60115 23.5387 6.60647C23.5886 6.6118 23.6384 6.62246 23.6932 6.62779V7.78937C23.6135 7.77339 23.5338 7.76273 23.449 7.75207C23.3643 7.74142 23.2845 7.73609 23.2048 7.73609C23.0154 7.73609 22.836 7.77871 22.6665 7.85864C22.497 7.93857 22.3525 8.06112 22.2279 8.21564C22.1033 8.37549 22.0036 8.56731 21.9288 8.80176C21.8541 9.03621 21.8192 9.30263 21.8192 9.60634V12.2066H20.6928V6.74501H20.6977ZM28.8718 12.2119H27.7653V11.4499H27.7454C27.6058 11.727 27.4015 11.9455 27.1274 12.1107C26.8532 12.2758 26.5741 12.3611 26.29 12.3611C25.6171 12.3611 25.1287 12.1852 24.8296 11.8282C24.5306 11.4712 24.3811 10.9331 24.3811 10.2138V6.74501H25.5075V10.0965C25.5075 10.5761 25.5922 10.9171 25.7667 11.1142C25.9361 11.3114 26.1804 11.4126 26.4894 11.4126C26.7286 11.4126 26.923 11.3753 27.0825 11.2954C27.242 11.2155 27.3716 11.1142 27.4663 10.981C27.566 10.8532 27.6357 10.6933 27.6806 10.5121C27.7255 10.331 27.7454 10.1338 27.7454 9.92072V6.75034H28.8718V12.2119ZM30.7907 10.4589C30.8256 10.8105 30.9502 11.0556 31.1645 11.1995C31.3838 11.338 31.643 11.4126 31.9471 11.4126C32.0517 11.4126 32.1714 11.402 32.3059 11.386C32.4405 11.37 32.5701 11.3327 32.6847 11.2848C32.8043 11.2368 32.899 11.1622 32.9788 11.0663C33.0536 10.9704 33.0884 10.8478 33.0835 10.6933C33.0785 10.5388 33.0236 10.4109 32.924 10.315C32.8243 10.2138 32.6997 10.1392 32.5452 10.0752C32.3907 10.0166 32.2162 9.96332 32.0168 9.92072C31.8175 9.87809 31.6181 9.83013 31.4138 9.78218C31.2044 9.73422 31.0001 9.67028 30.8057 9.60101C30.6113 9.53175 30.4369 9.43583 30.2823 9.31328C30.1278 9.19606 30.0032 9.04154 29.9135 8.85504C29.8188 8.66855 29.774 8.43943 29.774 8.16236C29.774 7.86397 29.8437 7.61886 29.9783 7.41639C30.1129 7.21391 30.2873 7.05406 30.4917 6.9315C30.701 6.80895 30.9303 6.7237 31.1845 6.67041C31.4387 6.62246 31.6829 6.59582 31.9122 6.59582C32.1763 6.59582 32.4305 6.62779 32.6698 6.6864C32.909 6.74501 33.1283 6.84092 33.3227 6.97946C33.5171 7.11267 33.6766 7.2885 33.8062 7.50164C33.9358 7.71477 34.0155 7.97586 34.0504 8.27958H32.8741C32.8193 7.99185 32.6997 7.7947 32.5053 7.69879C32.3109 7.59755 32.0866 7.54959 31.8374 7.54959C31.7577 7.54959 31.663 7.55492 31.5533 7.57091C31.4437 7.58689 31.344 7.61353 31.2443 7.65083C31.1496 7.68813 31.0698 7.74674 31.0001 7.82134C30.9353 7.89594 30.9004 7.99185 30.9004 8.1144C30.9004 8.26359 30.9502 8.38082 31.0449 8.4714C31.1396 8.56198 31.2642 8.63658 31.4187 8.70052C31.5732 8.75913 31.7477 8.81242 31.9471 8.85504C32.1464 8.89767 32.3508 8.94563 32.5601 8.99358C32.7645 9.04154 32.9638 9.10548 33.1632 9.17475C33.3626 9.24401 33.537 9.33992 33.6915 9.46248C33.846 9.58503 33.9706 9.73422 34.0653 9.91542C34.16 10.0965 34.2099 10.3257 34.2099 10.5921C34.2099 10.9171 34.1401 11.1888 34.0005 11.418C33.861 11.6418 33.6816 11.8282 33.4623 11.9668C33.243 12.1053 32.9937 12.2119 32.7246 12.2758C32.4555 12.3398 32.1863 12.3717 31.9221 12.3717C31.5982 12.3717 31.2991 12.3344 31.025 12.2545C30.7509 12.1746 30.5116 12.0574 30.3122 11.9028C30.1129 11.743 29.9534 11.5458 29.8388 11.3114C29.7241 11.0769 29.6643 10.7945 29.6543 10.4695H30.7907V10.4589ZM34.5089 6.74501H35.3612V5.10388H36.4877V6.74501H37.5044V7.64551H36.4877V10.5654C36.4877 10.6933 36.4926 10.7999 36.5026 10.8958C36.5126 10.9864 36.5375 11.0663 36.5724 11.1302C36.6073 11.1942 36.6621 11.2421 36.7369 11.2741C36.8116 11.3061 36.9063 11.3221 37.0359 11.3221C37.1157 11.3221 37.1954 11.3221 37.2752 11.3167C37.3549 11.3114 37.4347 11.3007 37.5144 11.2794V12.2119C37.3898 12.2279 37.2652 12.2385 37.1506 12.2545C37.0309 12.2705 36.9113 12.2758 36.7867 12.2758C36.4877 12.2758 36.2484 12.2439 36.069 12.1853C35.8896 12.1266 35.745 12.0361 35.6453 11.9188C35.5407 11.8016 35.4759 11.6577 35.436 11.4819C35.4011 11.3061 35.3762 11.1036 35.3712 10.8798V7.65616H34.5189V6.74501H34.5089ZM38.3019 6.74501H39.3685V7.48565H39.3885C39.548 7.16595 39.7673 6.94216 40.0514 6.80362C40.3355 6.66509 40.6395 6.59582 40.9734 6.59582C41.3771 6.59582 41.726 6.67041 42.0251 6.82494C42.3241 6.97413 42.5734 7.18194 42.7727 7.44836C42.9721 7.71477 43.1166 8.02382 43.2163 8.37549C43.316 8.72716 43.3658 9.10548 43.3658 9.5051C43.3658 9.87276 43.321 10.2297 43.2313 10.5708C43.1416 10.9171 43.007 11.2208 42.8276 11.4872C42.6481 11.7537 42.4188 11.9615 42.1397 12.1213C41.8606 12.2812 41.5366 12.3611 41.1578 12.3611C40.9934 12.3611 40.8289 12.3451 40.6644 12.3131C40.4999 12.2812 40.3404 12.2279 40.1909 12.1586C40.0414 12.0893 39.8968 11.9988 39.7722 11.8869C39.6427 11.775 39.538 11.6471 39.4483 11.5032H39.4283V14.2313H38.3019V6.74501ZM42.2394 9.48379C42.2394 9.23869 42.2095 8.99891 42.1497 8.76446C42.0899 8.53001 42.0002 8.32754 41.8806 8.14637C41.7609 7.96521 41.6114 7.82134 41.437 7.71477C41.2575 7.60821 41.0532 7.54959 40.8239 7.54959C40.3504 7.54959 39.9915 7.72543 39.7523 8.0771C39.5131 8.42877 39.3934 8.89767 39.3934 9.48379C39.3934 9.76087 39.4233 10.0166 39.4881 10.2511C39.5529 10.4855 39.6427 10.688 39.7722 10.8585C39.8968 11.029 40.0464 11.1622 40.2208 11.2581C40.3953 11.3594 40.5996 11.4073 40.8289 11.4073C41.0881 11.4073 41.3024 11.3487 41.4818 11.2368C41.6613 11.1249 41.8058 10.9757 41.9204 10.7999C42.0351 10.6187 42.1198 10.4162 42.1696 10.1871C42.2145 9.95802 42.2394 9.72357 42.2394 9.48379ZM44.2281 4.66162H45.3545V5.80189H44.2281V4.66162ZM44.2281 6.74501H45.3545V12.2119H44.2281V6.74501ZM46.3613 4.66162H47.4878V12.2119H46.3613V4.66162ZM50.9418 12.3611C50.5331 12.3611 50.1693 12.2865 49.8503 12.1426C49.5313 11.9988 49.2621 11.7963 49.0379 11.5458C48.8185 11.2901 48.6491 10.9864 48.5344 10.6347C48.4198 10.283 48.36 9.89412 48.36 9.47313C48.36 9.05752 48.4198 8.67388 48.5344 8.32221C48.6491 7.97054 48.8185 7.66682 49.0379 7.41106C49.2572 7.1553 49.5313 6.95815 49.8503 6.81428C50.1693 6.67041 50.5331 6.59582 50.9418 6.59582C51.3505 6.59582 51.7144 6.67041 52.0333 6.81428C52.3523 6.95815 52.6215 7.16062 52.8458 7.41106C53.0651 7.66682 53.2345 7.97054 53.3492 8.32221C53.4638 8.67388 53.5236 9.05752 53.5236 9.47313C53.5236 9.89412 53.4638 10.283 53.3492 10.6347C53.2345 10.9864 53.0651 11.2901 52.8458 11.5458C52.6265 11.8016 52.3523 11.9988 52.0333 12.1426C51.7144 12.2865 51.3505 12.3611 50.9418 12.3611ZM50.9418 11.4073C51.191 11.4073 51.4103 11.3487 51.5947 11.2368C51.7792 11.1249 51.9287 10.9757 52.0483 10.7945C52.1679 10.6134 52.2527 10.4056 52.3125 10.1765C52.3673 9.94732 52.3972 9.71291 52.3972 9.47313C52.3972 9.23868 52.3673 9.00957 52.3125 8.77512C52.2576 8.54067 52.1679 8.33819 52.0483 8.15703C51.9287 7.97586 51.7792 7.832 51.5947 7.7201C51.4103 7.60821 51.191 7.54959 50.9418 7.54959C50.6926 7.54959 50.4733 7.60821 50.2889 7.7201C50.1045 7.832 49.9549 7.98119 49.8353 8.15703C49.7157 8.33819 49.631 8.54067 49.5712 8.77512C49.5163 9.00957 49.4864 9.23868 49.4864 9.47313C49.4864 9.71291 49.5163 9.94732 49.5712 10.1765C49.626 10.4056 49.7157 10.6134 49.8353 10.7945C49.9549 10.9757 50.1045 11.1249 50.2889 11.2368C50.4733 11.354 50.6926 11.4073 50.9418 11.4073ZM53.8526 6.74501H54.7049V5.10388H55.8313V6.74501H56.8481V7.64551H55.8313V10.5654C55.8313 10.6933 55.8363 10.7999 55.8463 10.8958C55.8562 10.9864 55.8811 11.0663 55.916 11.1302C55.9509 11.1942 56.0058 11.2421 56.0805 11.2741C56.1553 11.3061 56.25 11.3221 56.3796 11.3221C56.4593 11.3221 56.5391 11.3221 56.6188 11.3167C56.6985 11.3114 56.7783 11.3007 56.858 11.2794V12.2119C56.7334 12.2279 56.6088 12.2385 56.4942 12.2545C56.3746 12.2705 56.255 12.2758 56.1304 12.2758C55.8313 12.2758 55.5921 12.2439 55.4126 12.1853C55.2332 12.1266 55.0887 12.0361 54.989 11.9188C54.8843 11.8016 54.8195 11.6577 54.7796 11.4819C54.7448 11.3061 54.7198 11.1036 54.7148 10.8798V7.65616H53.8625V6.74501H53.8526Z",
    fill: fill,
    __self: trustpilot_this,
    __source: {
      fileName: trustpilot_jsxFileName,
      lineNumber: 19,
      columnNumber: 7
    }
  }));
};

;// ./src/icons/index.jsx

















;// ./src/components/footer/review-item.jsx
var review_item_excluded = ["link", "logo", "ratings", "reviews"];
var review_item_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/components/footer/review-item.jsx",
  review_item_this = undefined;
function review_item_extends() { return review_item_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, review_item_extends.apply(null, arguments); }
function review_item_objectWithoutProperties(e, t) { if (null == e) return {}; var o, r, i = review_item_objectWithoutPropertiesLoose(e, t); if (Object.getOwnPropertySymbols) { var n = Object.getOwnPropertySymbols(e); for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]); } return i; }
function review_item_objectWithoutPropertiesLoose(r, e) { if (null == r) return {}; var t = {}; for (var n in r) if ({}.hasOwnProperty.call(r, n)) { if (-1 !== e.indexOf(n)) continue; t[n] = r[n]; } return t; }



var ReviewItem = function ReviewItem(_ref) {
  var link = _ref.link,
    Logo = _ref.logo,
    ratings = _ref.ratings,
    reviews = _ref.reviews,
    delegated = review_item_objectWithoutProperties(_ref, review_item_excluded);
  return wp.element.createElement("div", review_item_extends({
    className: "review"
  }, delegated, {
    __self: review_item_this,
    __source: {
      fileName: review_item_jsxFileName,
      lineNumber: 7,
      columnNumber: 5
    }
  }), wp.element.createElement("div", {
    className: "review__image",
    __self: review_item_this,
    __source: {
      fileName: review_item_jsxFileName,
      lineNumber: 8,
      columnNumber: 7
    }
  }, wp.element.createElement(ReviewIcon, {
    __self: review_item_this,
    __source: {
      fileName: review_item_jsxFileName,
      lineNumber: 9,
      columnNumber: 9
    }
  })), wp.element.createElement("div", {
    className: "review__rating",
    __self: review_item_this,
    __source: {
      fileName: review_item_jsxFileName,
      lineNumber: 11,
      columnNumber: 7
    }
  }, Logo && wp.element.createElement("a", {
    className: "review__reviews",
    href: link,
    target: "_blank",
    __self: review_item_this,
    __source: {
      fileName: review_item_jsxFileName,
      lineNumber: 13,
      columnNumber: 11
    }
  }, wp.element.createElement("span", {
    className: "review__logo",
    __self: review_item_this,
    __source: {
      fileName: review_item_jsxFileName,
      lineNumber: 14,
      columnNumber: 11
    }
  }, wp.element.createElement(Logo, {
    __self: review_item_this,
    __source: {
      fileName: review_item_jsxFileName,
      lineNumber: 15,
      columnNumber: 13
    }
  }))), ratings && wp.element.createElement("div", {
    className: "review__ratings",
    __self: review_item_this,
    __source: {
      fileName: review_item_jsxFileName,
      lineNumber: 19,
      columnNumber: 21
    }
  }, wp.element.createElement("a", {
    className: "review__reviews",
    href: link,
    target: "_blank",
    __self: review_item_this,
    __source: {
      fileName: review_item_jsxFileName,
      lineNumber: 19,
      columnNumber: 54
    }
  }, ratings))), wp.element.createElement("div", {
    className: "review__image review__image--flip",
    __self: review_item_this,
    __source: {
      fileName: review_item_jsxFileName,
      lineNumber: 21,
      columnNumber: 7
    }
  }, wp.element.createElement(ReviewIcon, {
    __self: review_item_this,
    __source: {
      fileName: review_item_jsxFileName,
      lineNumber: 22,
      columnNumber: 9
    }
  })));
};

;// ./src/utils/utm-url.js
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function utm_url_slicedToArray(r, e) { return utm_url_arrayWithHoles(r) || utm_url_iterableToArrayLimit(r, e) || utm_url_unsupportedIterableToArray(r, e) || utm_url_nonIterableRest(); }
function utm_url_nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function utm_url_unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return utm_url_arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? utm_url_arrayLikeToArray(r, a) : void 0; } }
function utm_url_arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function utm_url_iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function utm_url_arrayWithHoles(r) { if (Array.isArray(r)) return r; }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
/*
Usage :
const url = getUtmUrlWithParams('https://wpmudev.com/pricing/', {
  utm_campaign: 'homepage_banner',
  custom_param: 'value123'
});

Or simply :
const url = getUtmUrlWithParams('https://wpmudev.com/pricing/');
*/
var getUtmUrl = function getUtmUrl(url) {
  var params = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var _ref = window.wpmudev_cross_sell_data || {},
    _ref$utmSource = _ref.utmSource,
    utmSource = _ref$utmSource === void 0 ? '' : _ref$utmSource;
  var defaultParams = {
    utm_source: utmSource,
    utm_medium: 'plugin',
    utm_campaign: 'cross-sell_top-cta',
    utm_content: 'plugins-cross-sell'
  };

  // Merge default params with any overrides
  var mergedParams = _objectSpread(_objectSpread({}, defaultParams), params);
  var urlObj = new URL(url);
  Object.entries(mergedParams).forEach(function (_ref2) {
    var _ref3 = utm_url_slicedToArray(_ref2, 2),
      key = _ref3[0],
      value = _ref3[1];
    if (value != null) {
      urlObj.searchParams.set(key, value);
    }
  });
  return urlObj.toString();
};
;// ./src/components/footer/footer.jsx
var footer_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/components/footer/footer.jsx",
  footer_this = undefined;
function footer_extends() { return footer_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, footer_extends.apply(null, arguments); }





var REVIEW_ITEMS = [{
  link: "https://www.trustpilot.com/review/wpmudev.com",
  logo: TrustPilotIcon,
  ratings: (0,external_wp_i18n_namespaceObject.__)("4.9/5", "plugin-cross-sell-textdomain"),
  reviews: (0,external_wp_i18n_namespaceObject.__)("3,002", "plugin-cross-sell-textdomain")
}, {
  link: "https://www.reviews.io/company-reviews/store/wpmudev-com/",
  logo: ReviewsIcon,
  ratings: (0,external_wp_i18n_namespaceObject.__)("4.9/5", "plugin-cross-sell-textdomain"),
  reviews: (0,external_wp_i18n_namespaceObject.__)("1,746", "plugin-cross-sell-textdomain")
}, {
  link: "https://www.google.com/search?q=WPMU+DEV&stick=H4sIAAAAAAAA_-NgU1I1qLAwMzFJTkq0SLY0NDdNMjW0MqgwNk4ySbZMSjQxTjEwNbAwXsTKER7gG6rg4hoGAFY8Y0Y0AAAA&hl=en&mat=CdvqvSC1kKDMElYBNqvzOh6WSjS1JTPgbDKr_wHsjlg-PedU9JwdPCfoAnD-gp_c4yhRTs4ampJofZFKWPiClgM5THRDw4IjNwi1icSMk7AHlnFbFP31PhpLbvH6V0UYHg&authuser=0#lrd=0x8644cba8c9175b51:0x33b4c9ba43d05083,1,,,",
  logo: GoogleIcon,
  ratings: (0,external_wp_i18n_namespaceObject.__)("5/5", "plugin-cross-sell-textdomain"),
  reviews: (0,external_wp_i18n_namespaceObject.__)("592", "plugin-cross-sell-textdomain")
}];
var Footer = function Footer() {
  return wp.element.createElement("div", {
    className: "cross-sell-footer",
    __self: footer_this,
    __source: {
      fileName: footer_jsxFileName,
      lineNumber: 30,
      columnNumber: 5
    }
  }, wp.element.createElement("div", {
    className: "cross-sell-wrapper cross-sell-footer__wrapper",
    __self: footer_this,
    __source: {
      fileName: footer_jsxFileName,
      lineNumber: 31,
      columnNumber: 7
    }
  }, wp.element.createElement("div", {
    className: "cross-sell-footer__content",
    __self: footer_this,
    __source: {
      fileName: footer_jsxFileName,
      lineNumber: 32,
      columnNumber: 9
    }
  }, wp.element.createElement("div", {
    className: "cross-sell-footer__title",
    __self: footer_this,
    __source: {
      fileName: footer_jsxFileName,
      lineNumber: 33,
      columnNumber: 11
    }
  }, wp.element.createElement("h2", {
    __self: footer_this,
    __source: {
      fileName: footer_jsxFileName,
      lineNumber: 34,
      columnNumber: 13
    }
  }, (0,external_wp_i18n_namespaceObject.__)("About WPMU DEV", "plugin-cross-sell-textdomain")), wp.element.createElement("p", {
    __self: footer_this,
    __source: {
      fileName: footer_jsxFileName,
      lineNumber: 35,
      columnNumber: 13
    }
  }, (0,external_wp_i18n_namespaceObject.__)("Made for web developers, by web developers", "plugin-cross-sell-textdomain"))), wp.element.createElement("div", {
    className: "cross-sell-footer__description",
    __self: footer_this,
    __source: {
      fileName: footer_jsxFileName,
      lineNumber: 42,
      columnNumber: 11
    }
  }, wp.element.createElement("p", {
    __self: footer_this,
    __source: {
      fileName: footer_jsxFileName,
      lineNumber: 43,
      columnNumber: 13
    }
  }, (0,external_wp_i18n_namespaceObject.__)("Since 2006, our award-winning WordPress plugins, hosting, world beating support and site management tools have helped hundreds of thousands of web developers, freelancers and agencies run and grow their businesses. ", "plugin-cross-sell-textdomain"), wp.element.createElement("a", {
    href: getUtmUrl("https://wpmudev.com/about/", {
      utm_campaign: 'cross-sell_learn-more'
    }),
    target: "_blank",
    className: "cross-sell-link",
    __self: footer_this,
    __source: {
      fileName: footer_jsxFileName,
      lineNumber: 48,
      columnNumber: 15
    }
  }, (0,external_wp_i18n_namespaceObject.__)("Learn more", "plugin-cross-sell-textdomain"))))), REVIEW_ITEMS && wp.element.createElement("div", {
    className: "cross-sell-footer__reviews",
    __self: footer_this,
    __source: {
      fileName: footer_jsxFileName,
      lineNumber: 59,
      columnNumber: 11
    }
  }, REVIEW_ITEMS.map(function (item, index) {
    return wp.element.createElement(ReviewItem, footer_extends({
      key: index
    }, item, {
      __self: footer_this,
      __source: {
        fileName: footer_jsxFileName,
        lineNumber: 61,
        columnNumber: 15
      }
    }));
  })), wp.element.createElement("div", {
    className: "cross-sell-footer__credits",
    __self: footer_this,
    __source: {
      fileName: footer_jsxFileName,
      lineNumber: 65,
      columnNumber: 9
    }
  }, wp.element.createElement("p", {
    __self: footer_this,
    __source: {
      fileName: footer_jsxFileName,
      lineNumber: 66,
      columnNumber: 11
    }
  }, (0,external_wp_i18n_namespaceObject.__)("Everything Wordpress found in one place. ", "plugin-cross-sell-textdomain"), wp.element.createElement("a", {
    href: getUtmUrl("https://wpmudev.com/", {
      utm_campaign: 'cross-sell_footer-link'
    }),
    target: "_blank",
    className: "cross-sell-link",
    __self: footer_this,
    __source: {
      fileName: footer_jsxFileName,
      lineNumber: 71,
      columnNumber: 13
    }
  }, (0,external_wp_i18n_namespaceObject.__)("WPMU DEV", "plugin-cross-sell-textdomain"))))));
};

;// ./src/components/footer/index.jsx

;// ./src/components/highlights/highlights.jsx
var highlights_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/components/highlights/highlights.jsx",
  highlights_this = undefined;



var HIGHLIGHTS = [{
  title: (0,external_wp_i18n_namespaceObject.__)("Trusted by 60,000+ businesses worldwide", "plugin-cross-sell-textdomain"),
  icon: BusinessIcon
}, {
  title: (0,external_wp_i18n_namespaceObject.__)("67,233,282 plugin downloads", "plugin-cross-sell-textdomain"),
  icon: CloudIcon
}, {
  title: (0,external_wp_i18n_namespaceObject.__)("7,260+ 5 star reviews", "plugin-cross-sell-textdomain"),
  icon: StarIcon
}];
var Highlights = function Highlights() {
  return wp.element.createElement("div", {
    className: "cross-sell-wrapper",
    __self: highlights_this,
    __source: {
      fileName: highlights_jsxFileName,
      lineNumber: 24,
      columnNumber: 5
    }
  }, HIGHLIGHTS.length > 0 && wp.element.createElement("div", {
    className: "cross-sell-highlights",
    __self: highlights_this,
    __source: {
      fileName: highlights_jsxFileName,
      lineNumber: 26,
      columnNumber: 9
    }
  }, HIGHLIGHTS.map(function (highlight, index) {
    return wp.element.createElement("div", {
      className: "cross-sell-highlight",
      key: index,
      __self: highlights_this,
      __source: {
        fileName: highlights_jsxFileName,
        lineNumber: 28,
        columnNumber: 13
      }
    }, wp.element.createElement("div", {
      className: "cross-sell-highlight__icon",
      __self: highlights_this,
      __source: {
        fileName: highlights_jsxFileName,
        lineNumber: 29,
        columnNumber: 15
      }
    }, wp.element.createElement(highlight.icon, {
      __self: highlights_this,
      __source: {
        fileName: highlights_jsxFileName,
        lineNumber: 30,
        columnNumber: 17
      }
    })), wp.element.createElement("div", {
      className: "cross-sell-highlight__title",
      __self: highlights_this,
      __source: {
        fileName: highlights_jsxFileName,
        lineNumber: 32,
        columnNumber: 15
      }
    }, highlight.title));
  })));
};

;// ./src/components/highlights/index.jsx

;// ./src/components/section/section.jsx
var section_excluded = ["title", "titleUnderline", "className", "tag", "description", "children"];
var section_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/components/section/section.jsx",
  section_this = undefined;
function section_extends() { return section_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, section_extends.apply(null, arguments); }
function section_objectWithoutProperties(e, t) { if (null == e) return {}; var o, r, i = section_objectWithoutPropertiesLoose(e, t); if (Object.getOwnPropertySymbols) { var n = Object.getOwnPropertySymbols(e); for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]); } return i; }
function section_objectWithoutPropertiesLoose(r, e) { if (null == r) return {}; var t = {}; for (var n in r) if ({}.hasOwnProperty.call(r, n)) { if (-1 !== e.indexOf(n)) continue; t[n] = r[n]; } return t; }


var Section = function Section(_ref) {
  var title = _ref.title,
    titleUnderline = _ref.titleUnderline,
    _ref$className = _ref.className,
    className = _ref$className === void 0 ? "" : _ref$className,
    tag = _ref.tag,
    description = _ref.description,
    children = _ref.children,
    delegated = section_objectWithoutProperties(_ref, section_excluded);
  return wp.element.createElement("div", section_extends({
    className: "cross-sell-section ".concat(className)
  }, delegated, {
    __self: section_this,
    __source: {
      fileName: section_jsxFileName,
      lineNumber: 14,
      columnNumber: 5
    }
  }), wp.element.createElement("div", {
    className: "cross-sell-section-header-wrapper",
    __self: section_this,
    __source: {
      fileName: section_jsxFileName,
      lineNumber: 15,
      columnNumber: 7
    }
  }, (title || tag) && wp.element.createElement("div", {
    className: "cross-sell-section-header",
    __self: section_this,
    __source: {
      fileName: section_jsxFileName,
      lineNumber: 17,
      columnNumber: 11
    }
  }, title && wp.element.createElement("h2", {
    className: "cross-sell-text-center",
    __self: section_this,
    __source: {
      fileName: section_jsxFileName,
      lineNumber: 19,
      columnNumber: 15
    }
  }, title, titleUnderline && wp.element.createElement("span", {
    className: "cross-sell-section-title-underline",
    __self: section_this,
    __source: {
      fileName: section_jsxFileName,
      lineNumber: 22,
      columnNumber: 19
    }
  }, titleUnderline)), tag), description && wp.element.createElement("span", {
    className: "cross-sell-section-description",
    __self: section_this,
    __source: {
      fileName: section_jsxFileName,
      lineNumber: 32,
      columnNumber: 11
    }
  }, description)), children);
};

;// ./src/components/section/index.jsx

;// ./node_modules/.pnpm/@wpmudev+react-button@1.1.2/node_modules/@wpmudev/react-button/dist/react-button.esm.js


function react_button_esm_ownKeys(object, enumerableOnly) {
  var keys = Object.keys(object);

  if (Object.getOwnPropertySymbols) {
    var symbols = Object.getOwnPropertySymbols(object);
    enumerableOnly && (symbols = symbols.filter(function (sym) {
      return Object.getOwnPropertyDescriptor(object, sym).enumerable;
    })), keys.push.apply(keys, symbols);
  }

  return keys;
}

function _objectSpread2(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = null != arguments[i] ? arguments[i] : {};
    i % 2 ? react_button_esm_ownKeys(Object(source), !0).forEach(function (key) {
      react_button_esm_defineProperty(target, key, source[key]);
    }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : react_button_esm_ownKeys(Object(source)).forEach(function (key) {
      Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key));
    });
  }

  return target;
}

function react_button_esm_defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}

function react_button_esm_objectWithoutPropertiesLoose(source, excluded) {
  if (source == null) return {};
  var target = {};
  var sourceKeys = Object.keys(source);
  var key, i;

  for (i = 0; i < sourceKeys.length; i++) {
    key = sourceKeys[i];
    if (excluded.indexOf(key) >= 0) continue;
    target[key] = source[key];
  }

  return target;
}

function react_button_esm_objectWithoutProperties(source, excluded) {
  if (source == null) return {};

  var target = react_button_esm_objectWithoutPropertiesLoose(source, excluded);

  var key, i;

  if (Object.getOwnPropertySymbols) {
    var sourceSymbolKeys = Object.getOwnPropertySymbols(source);

    for (i = 0; i < sourceSymbolKeys.length; i++) {
      key = sourceSymbolKeys[i];
      if (excluded.indexOf(key) >= 0) continue;
      if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue;
      target[key] = source[key];
    }
  }

  return target;
}

var react_button_esm_excluded = ["label", "icon", "iconRight", "design", "color", "className", "loading"];

var Button = function Button(_ref) {
  var label = _ref.label,
      icon = _ref.icon,
      iconRight = _ref.iconRight,
      _ref$design = _ref.design,
      design = _ref$design === void 0 ? 'solid' : _ref$design,
      color = _ref.color,
      className = _ref.className,
      loading = _ref.loading,
      props = react_button_esm_objectWithoutProperties(_ref, react_button_esm_excluded);

  var loader = /*#__PURE__*/external_React_default().createElement("span", {
    className: "sui-icon-loader sui-loading",
    style: {
      position: 'relative'
    },
    "aria-hidden": "true"
  });
  var content = /*#__PURE__*/external_React_default().createElement((external_React_default()).Fragment, null, icon && !iconRight && '' !== icon && /*#__PURE__*/external_React_default().createElement("span", {
    className: 'sui-icon-' + icon,
    "aria-hidden": "true"
  }), label, icon && iconRight && '' !== icon && /*#__PURE__*/external_React_default().createElement("span", {
    className: 'sui-icon-' + icon,
    "aria-hidden": "true"
  }));
  className = "sui-button".concat(iconRight ? ' sui-button-icon-right' : '').concat(className ? ' ' + className : ''); // Set button color.

  switch (color) {
    case 'blue':
    case 'green':
    case 'red':
    case 'orange':
    case 'purple':
    case 'yellow':
    case 'white':
      className += ' sui-button-' + color;
      break;

    case 'gray':
    default:
      className += '';
      break;
  } // Set button style.


  switch (design) {
    case 'ghost':
    case 'outlined':
      className += ' sui-button-' + design;
      break;

    case 'solid':
    default:
      className += '';
      break;
  } // Set loading class.


  if (loading) {
    className += ' sui-button-onload';
  }

  var htmlTag = 'button';

  if (props.href) {
    htmlTag = 'a';
  } else if (props.htmlFor) {
    htmlTag = 'label';
  }

  return /*#__PURE__*/external_React_default().createElement(htmlTag, _objectSpread2({
    className: className,
    disabled: props.disabled || loading
  }, props), loading ? loader : content);
};



;// external ["wp","apiFetch"]
const external_wp_apiFetch_namespaceObject = window["wp"]["apiFetch"];
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_namespaceObject);
;// ./node_modules/.pnpm/@wpmudev+react-notifications@1.1.1/node_modules/@wpmudev/react-notifications/dist/react-notifications.esm.js


function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

function _defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, descriptor.key, descriptor);
  }
}

function _createClass(Constructor, protoProps, staticProps) {
  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
  if (staticProps) _defineProperties(Constructor, staticProps);
  Object.defineProperty(Constructor, "prototype", {
    writable: false
  });
  return Constructor;
}

function _defineProperty$1(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}

function _inherits(subClass, superClass) {
  if (typeof superClass !== "function" && superClass !== null) {
    throw new TypeError("Super expression must either be null or a function");
  }

  subClass.prototype = Object.create(superClass && superClass.prototype, {
    constructor: {
      value: subClass,
      writable: true,
      configurable: true
    }
  });
  Object.defineProperty(subClass, "prototype", {
    writable: false
  });
  if (superClass) _setPrototypeOf(subClass, superClass);
}

function _getPrototypeOf(o) {
  _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
    return o.__proto__ || Object.getPrototypeOf(o);
  };
  return _getPrototypeOf(o);
}

function _setPrototypeOf(o, p) {
  _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  };

  return _setPrototypeOf(o, p);
}

function _isNativeReflectConstruct() {
  if (typeof Reflect === "undefined" || !Reflect.construct) return false;
  if (Reflect.construct.sham) return false;
  if (typeof Proxy === "function") return true;

  try {
    Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {}));
    return true;
  } catch (e) {
    return false;
  }
}

function _assertThisInitialized(self) {
  if (self === void 0) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }

  return self;
}

function _possibleConstructorReturn(self, call) {
  if (call && (typeof call === "object" || typeof call === "function")) {
    return call;
  } else if (call !== void 0) {
    throw new TypeError("Derived constructors may only return object or undefined");
  }

  return _assertThisInitialized(self);
}

function _createSuper(Derived) {
  var hasNativeReflectConstruct = _isNativeReflectConstruct();

  return function _createSuperInternal() {
    var Super = _getPrototypeOf(Derived),
        result;

    if (hasNativeReflectConstruct) {
      var NewTarget = _getPrototypeOf(this).constructor;

      result = Reflect.construct(Super, arguments, NewTarget);
    } else {
      result = Super.apply(this, arguments);
    }

    return _possibleConstructorReturn(this, result);
  };
}

function react_notifications_esm_ownKeys(object, enumerableOnly) {
  var keys = Object.keys(object);

  if (Object.getOwnPropertySymbols) {
    var symbols = Object.getOwnPropertySymbols(object);
    enumerableOnly && (symbols = symbols.filter(function (sym) {
      return Object.getOwnPropertyDescriptor(object, sym).enumerable;
    })), keys.push.apply(keys, symbols);
  }

  return keys;
}

function react_notifications_esm_objectSpread2(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = null != arguments[i] ? arguments[i] : {};
    i % 2 ? react_notifications_esm_ownKeys(Object(source), !0).forEach(function (key) {
      react_notifications_esm_defineProperty(target, key, source[key]);
    }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : react_notifications_esm_ownKeys(Object(source)).forEach(function (key) {
      Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key));
    });
  }

  return target;
}

function react_notifications_esm_defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}

function react_notifications_esm_objectWithoutPropertiesLoose(source, excluded) {
  if (source == null) return {};
  var target = {};
  var sourceKeys = Object.keys(source);
  var key, i;

  for (i = 0; i < sourceKeys.length; i++) {
    key = sourceKeys[i];
    if (excluded.indexOf(key) >= 0) continue;
    target[key] = source[key];
  }

  return target;
}

function react_notifications_esm_objectWithoutProperties(source, excluded) {
  if (source == null) return {};

  var target = react_notifications_esm_objectWithoutPropertiesLoose(source, excluded);

  var key, i;

  if (Object.getOwnPropertySymbols) {
    var sourceSymbolKeys = Object.getOwnPropertySymbols(source);

    for (i = 0; i < sourceSymbolKeys.length; i++) {
      key = sourceSymbolKeys[i];
      if (excluded.indexOf(key) >= 0) continue;
      if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue;
      target[key] = source[key];
    }
  }

  return target;
}

var react_notifications_esm_excluded = ["label", "icon", "iconSize", "design", "color", "className", "loading"];

var ButtonIcon = function ButtonIcon(_ref) {
  var label = _ref.label,
      icon = _ref.icon,
      iconSize = _ref.iconSize,
      _ref$design = _ref.design,
      design = _ref$design === void 0 ? 'solid' : _ref$design,
      color = _ref.color,
      className = _ref.className,
      loading = _ref.loading,
      props = react_notifications_esm_objectWithoutProperties(_ref, react_notifications_esm_excluded);

  var loader = /*#__PURE__*/external_React_default().createElement("span", {
    className: "sui-icon-loader sui-loading",
    style: {
      position: 'relative'
    },
    "aria-hidden": "true"
  });
  var content = /*#__PURE__*/external_React_default().createElement((external_React_default()).Fragment, null, /*#__PURE__*/external_React_default().createElement("span", {
    className: "sui-icon-".concat(icon).concat(iconSize ? ' sui-' + iconSize : ''),
    "aria-hidden": "true"
  }), /*#__PURE__*/external_React_default().createElement("span", {
    className: "sui-screen-reader-text"
  }, label));
  className = "sui-button-icon ".concat(className || ''); // Set button color.

  switch (color) {
    case 'blue':
    case 'green':
    case 'red':
    case 'orange':
    case 'purple':
    case 'yellow':
    case 'white':
      className += ' sui-button-' + color;
      break;

    case 'gray':
    default:
      className += '';
      break;
  } // Set button style.


  switch (design) {
    case 'ghost':
    case 'outlined':
      className += ' sui-button-' + design;
      break;

    case 'solid':
    default:
      className += '';
      break;
  } // Set loading class.


  if (loading) {
    className += ' sui-button-onload';
  }

  var htmlTag = props.href ? 'a' : 'button';
  return /*#__PURE__*/external_React_default().createElement(htmlTag, react_notifications_esm_objectSpread2({
    className: className,
    disabled: props.disabled || loading
  }, props), loading ? loader : content);
};

var Notifications = /*#__PURE__*/function (_Component) {
  _inherits(Notifications, _Component);

  var _super = _createSuper(Notifications);

  function Notifications(props) {
    var _this;

    _classCallCheck(this, Notifications);

    _this = _super.call(this, props);

    _defineProperty$1(_assertThisInitialized(_this), "close", function () {
      _this.setState({
        hide: true
      });

      _this.props.cbFunction ? _this.props.cbFunction() : '';
    });

    _this.state = {
      hide: false
    };
    _this.close = _this.close.bind(_assertThisInitialized(_this));
    return _this;
  }

  _createClass(Notifications, [{
    key: "render",
    value: function render() {
      var _this2 = this;

      var hide = this.state.hide;
      var classMain = 'sui-notice';
      var classIcon = 'sui-notice-icon sui-md';

      switch (this.props.type) {
        case 'info':
        case 'success':
        case 'warning':
        case 'error':
        case 'upsell':
          classMain += ' sui-notice-' + this.props.type;

          if (this.props.loading) {
            classIcon += ' sui-icon-loader sui-loading';
          } else {
            classIcon += ' sui-icon-info';
          }

          break;

        default:
          if (this.props.loading) {
            classIcon += ' sui-icon-loader sui-loading';
          } else {
            classIcon += ' sui-icon-info';
          }

          break;
      }

      var lang = Object.assign({
        dismiss: 'Hide Notification'
      }, this.props.sourceLang);
      var message = /*#__PURE__*/external_React_default().createElement("div", {
        className: "sui-notice-message"
      }, /*#__PURE__*/external_React_default().createElement("span", {
        className: classIcon,
        "aria-hidden": "true"
      }), this.props.children);
      var actions = /*#__PURE__*/external_React_default().createElement("div", {
        className: "sui-notice-actions"
      }, /*#__PURE__*/external_React_default().createElement(ButtonIcon, {
        icon: "check",
        label: lang.dismiss,
        onClick: function onClick(e) {
          return _this2.close(e);
        }
      }));

      if (!hide) {
        return /*#__PURE__*/external_React_default().createElement("div", {
          className: classMain
        }, /*#__PURE__*/external_React_default().createElement("div", {
          className: "sui-notice-content"
        }, message, this.props.dismiss && actions));
      }

      return null;
    }
  }]);

  return Notifications;
}(external_React_namespaceObject.Component);



;// ./src/components/plugins/plugin-card.jsx
function plugin_card_typeof(o) { "@babel/helpers - typeof"; return plugin_card_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, plugin_card_typeof(o); }
var plugin_card_excluded = ["as", "logo", "title", "rating", "active_installs", "description", "features", "url", "active", "installed", "slug", "cta", "path"];
var plugin_card_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/components/plugins/plugin-card.jsx",
  plugin_card_this = undefined;
function plugin_card_extends() { return plugin_card_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, plugin_card_extends.apply(null, arguments); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return e; }; var t, e = {}, r = Object.prototype, n = r.hasOwnProperty, o = Object.defineProperty || function (t, e, r) { t[e] = r.value; }, i = "function" == typeof Symbol ? Symbol : {}, a = i.iterator || "@@iterator", c = i.asyncIterator || "@@asyncIterator", u = i.toStringTag || "@@toStringTag"; function define(t, e, r) { return Object.defineProperty(t, e, { value: r, enumerable: !0, configurable: !0, writable: !0 }), t[e]; } try { define({}, ""); } catch (t) { define = function define(t, e, r) { return t[e] = r; }; } function wrap(t, e, r, n) { var i = e && e.prototype instanceof Generator ? e : Generator, a = Object.create(i.prototype), c = new Context(n || []); return o(a, "_invoke", { value: makeInvokeMethod(t, r, c) }), a; } function tryCatch(t, e, r) { try { return { type: "normal", arg: t.call(e, r) }; } catch (t) { return { type: "throw", arg: t }; } } e.wrap = wrap; var h = "suspendedStart", l = "suspendedYield", f = "executing", s = "completed", y = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var p = {}; define(p, a, function () { return this; }); var d = Object.getPrototypeOf, v = d && d(d(values([]))); v && v !== r && n.call(v, a) && (p = v); var g = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(p); function defineIteratorMethods(t) { ["next", "throw", "return"].forEach(function (e) { define(t, e, function (t) { return this._invoke(e, t); }); }); } function AsyncIterator(t, e) { function invoke(r, o, i, a) { var c = tryCatch(t[r], t, o); if ("throw" !== c.type) { var u = c.arg, h = u.value; return h && "object" == plugin_card_typeof(h) && n.call(h, "__await") ? e.resolve(h.__await).then(function (t) { invoke("next", t, i, a); }, function (t) { invoke("throw", t, i, a); }) : e.resolve(h).then(function (t) { u.value = t, i(u); }, function (t) { return invoke("throw", t, i, a); }); } a(c.arg); } var r; o(this, "_invoke", { value: function value(t, n) { function callInvokeWithMethodAndArg() { return new e(function (e, r) { invoke(t, n, e, r); }); } return r = r ? r.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(e, r, n) { var o = h; return function (i, a) { if (o === f) throw Error("Generator is already running"); if (o === s) { if ("throw" === i) throw a; return { value: t, done: !0 }; } for (n.method = i, n.arg = a;;) { var c = n.delegate; if (c) { var u = maybeInvokeDelegate(c, n); if (u) { if (u === y) continue; return u; } } if ("next" === n.method) n.sent = n._sent = n.arg;else if ("throw" === n.method) { if (o === h) throw o = s, n.arg; n.dispatchException(n.arg); } else "return" === n.method && n.abrupt("return", n.arg); o = f; var p = tryCatch(e, r, n); if ("normal" === p.type) { if (o = n.done ? s : l, p.arg === y) continue; return { value: p.arg, done: n.done }; } "throw" === p.type && (o = s, n.method = "throw", n.arg = p.arg); } }; } function maybeInvokeDelegate(e, r) { var n = r.method, o = e.iterator[n]; if (o === t) return r.delegate = null, "throw" === n && e.iterator["return"] && (r.method = "return", r.arg = t, maybeInvokeDelegate(e, r), "throw" === r.method) || "return" !== n && (r.method = "throw", r.arg = new TypeError("The iterator does not provide a '" + n + "' method")), y; var i = tryCatch(o, e.iterator, r.arg); if ("throw" === i.type) return r.method = "throw", r.arg = i.arg, r.delegate = null, y; var a = i.arg; return a ? a.done ? (r[e.resultName] = a.value, r.next = e.nextLoc, "return" !== r.method && (r.method = "next", r.arg = t), r.delegate = null, y) : a : (r.method = "throw", r.arg = new TypeError("iterator result is not an object"), r.delegate = null, y); } function pushTryEntry(t) { var e = { tryLoc: t[0] }; 1 in t && (e.catchLoc = t[1]), 2 in t && (e.finallyLoc = t[2], e.afterLoc = t[3]), this.tryEntries.push(e); } function resetTryEntry(t) { var e = t.completion || {}; e.type = "normal", delete e.arg, t.completion = e; } function Context(t) { this.tryEntries = [{ tryLoc: "root" }], t.forEach(pushTryEntry, this), this.reset(!0); } function values(e) { if (e || "" === e) { var r = e[a]; if (r) return r.call(e); if ("function" == typeof e.next) return e; if (!isNaN(e.length)) { var o = -1, i = function next() { for (; ++o < e.length;) if (n.call(e, o)) return next.value = e[o], next.done = !1, next; return next.value = t, next.done = !0, next; }; return i.next = i; } } throw new TypeError(plugin_card_typeof(e) + " is not iterable"); } return GeneratorFunction.prototype = GeneratorFunctionPrototype, o(g, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), o(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, u, "GeneratorFunction"), e.isGeneratorFunction = function (t) { var e = "function" == typeof t && t.constructor; return !!e && (e === GeneratorFunction || "GeneratorFunction" === (e.displayName || e.name)); }, e.mark = function (t) { return Object.setPrototypeOf ? Object.setPrototypeOf(t, GeneratorFunctionPrototype) : (t.__proto__ = GeneratorFunctionPrototype, define(t, u, "GeneratorFunction")), t.prototype = Object.create(g), t; }, e.awrap = function (t) { return { __await: t }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, c, function () { return this; }), e.AsyncIterator = AsyncIterator, e.async = function (t, r, n, o, i) { void 0 === i && (i = Promise); var a = new AsyncIterator(wrap(t, r, n, o), i); return e.isGeneratorFunction(r) ? a : a.next().then(function (t) { return t.done ? t.value : a.next(); }); }, defineIteratorMethods(g), define(g, u, "Generator"), define(g, a, function () { return this; }), define(g, "toString", function () { return "[object Generator]"; }), e.keys = function (t) { var e = Object(t), r = []; for (var n in e) r.push(n); return r.reverse(), function next() { for (; r.length;) { var t = r.pop(); if (t in e) return next.value = t, next.done = !1, next; } return next.done = !0, next; }; }, e.values = values, Context.prototype = { constructor: Context, reset: function reset(e) { if (this.prev = 0, this.next = 0, this.sent = this._sent = t, this.done = !1, this.delegate = null, this.method = "next", this.arg = t, this.tryEntries.forEach(resetTryEntry), !e) for (var r in this) "t" === r.charAt(0) && n.call(this, r) && !isNaN(+r.slice(1)) && (this[r] = t); }, stop: function stop() { this.done = !0; var t = this.tryEntries[0].completion; if ("throw" === t.type) throw t.arg; return this.rval; }, dispatchException: function dispatchException(e) { if (this.done) throw e; var r = this; function handle(n, o) { return a.type = "throw", a.arg = e, r.next = n, o && (r.method = "next", r.arg = t), !!o; } for (var o = this.tryEntries.length - 1; o >= 0; --o) { var i = this.tryEntries[o], a = i.completion; if ("root" === i.tryLoc) return handle("end"); if (i.tryLoc <= this.prev) { var c = n.call(i, "catchLoc"), u = n.call(i, "finallyLoc"); if (c && u) { if (this.prev < i.catchLoc) return handle(i.catchLoc, !0); if (this.prev < i.finallyLoc) return handle(i.finallyLoc); } else if (c) { if (this.prev < i.catchLoc) return handle(i.catchLoc, !0); } else { if (!u) throw Error("try statement without catch or finally"); if (this.prev < i.finallyLoc) return handle(i.finallyLoc); } } } }, abrupt: function abrupt(t, e) { for (var r = this.tryEntries.length - 1; r >= 0; --r) { var o = this.tryEntries[r]; if (o.tryLoc <= this.prev && n.call(o, "finallyLoc") && this.prev < o.finallyLoc) { var i = o; break; } } i && ("break" === t || "continue" === t) && i.tryLoc <= e && e <= i.finallyLoc && (i = null); var a = i ? i.completion : {}; return a.type = t, a.arg = e, i ? (this.method = "next", this.next = i.finallyLoc, y) : this.complete(a); }, complete: function complete(t, e) { if ("throw" === t.type) throw t.arg; return "break" === t.type || "continue" === t.type ? this.next = t.arg : "return" === t.type ? (this.rval = this.arg = t.arg, this.method = "return", this.next = "end") : "normal" === t.type && e && (this.next = e), y; }, finish: function finish(t) { for (var e = this.tryEntries.length - 1; e >= 0; --e) { var r = this.tryEntries[e]; if (r.finallyLoc === t) return this.complete(r.completion, r.afterLoc), resetTryEntry(r), y; } }, "catch": function _catch(t) { for (var e = this.tryEntries.length - 1; e >= 0; --e) { var r = this.tryEntries[e]; if (r.tryLoc === t) { var n = r.completion; if ("throw" === n.type) { var o = n.arg; resetTryEntry(r); } return o; } } throw Error("illegal catch attempt"); }, delegateYield: function delegateYield(e, r, n) { return this.delegate = { iterator: values(e), resultName: r, nextLoc: n }, "next" === this.method && (this.arg = t), y; } }, e; }
function asyncGeneratorStep(n, t, e, r, o, a, c) { try { var i = n[a](c), u = i.value; } catch (n) { return void e(n); } i.done ? t(u) : Promise.resolve(u).then(r, o); }
function _asyncToGenerator(n) { return function () { var t = this, e = arguments; return new Promise(function (r, o) { var a = n.apply(t, e); function _next(n) { asyncGeneratorStep(a, r, o, _next, _throw, "next", n); } function _throw(n) { asyncGeneratorStep(a, r, o, _next, _throw, "throw", n); } _next(void 0); }); }; }
function plugin_card_slicedToArray(r, e) { return plugin_card_arrayWithHoles(r) || plugin_card_iterableToArrayLimit(r, e) || plugin_card_unsupportedIterableToArray(r, e) || plugin_card_nonIterableRest(); }
function plugin_card_nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function plugin_card_unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return plugin_card_arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? plugin_card_arrayLikeToArray(r, a) : void 0; } }
function plugin_card_arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function plugin_card_iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function plugin_card_arrayWithHoles(r) { if (Array.isArray(r)) return r; }
function plugin_card_objectWithoutProperties(e, t) { if (null == e) return {}; var o, r, i = plugin_card_objectWithoutPropertiesLoose(e, t); if (Object.getOwnPropertySymbols) { var n = Object.getOwnPropertySymbols(e); for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]); } return i; }
function plugin_card_objectWithoutPropertiesLoose(r, e) { if (null == r) return {}; var t = {}; for (var n in r) if ({}.hasOwnProperty.call(r, n)) { if (-1 !== e.indexOf(n)) continue; t[n] = r[n]; } return t; }






var PluginCard = function PluginCard(_ref) {
  var _ref$as = _ref.as,
    as = _ref$as === void 0 ? "div" : _ref$as,
    logo = _ref.logo,
    title = _ref.title,
    rating = _ref.rating,
    active_installs = _ref.active_installs,
    description = _ref.description,
    features = _ref.features,
    url = _ref.url,
    isActiveProp = _ref.active,
    isInstalledProp = _ref.installed,
    slug = _ref.slug,
    _ref$cta = _ref.cta,
    cta = _ref$cta === void 0 ? false : _ref$cta,
    path = _ref.path,
    delegated = plugin_card_objectWithoutProperties(_ref, plugin_card_excluded);
  var _useState = (0,external_React_namespaceObject.useState)(isInstalledProp),
    _useState2 = plugin_card_slicedToArray(_useState, 2),
    isInstalled = _useState2[0],
    setIsInstalled = _useState2[1];
  var _useState3 = (0,external_React_namespaceObject.useState)(isActiveProp),
    _useState4 = plugin_card_slicedToArray(_useState3, 2),
    isActive = _useState4[0],
    setIsActive = _useState4[1];
  var _useState5 = (0,external_React_namespaceObject.useState)(false),
    _useState6 = plugin_card_slicedToArray(_useState5, 2),
    loading = _useState6[0],
    setLoading = _useState6[1];
  var _useState7 = (0,external_React_namespaceObject.useState)(null),
    _useState8 = plugin_card_slicedToArray(_useState7, 2),
    notification = _useState8[0],
    setNotification = _useState8[1];
  var TagName = as;
  var showNotification = function showNotification(message) {
    var type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : "success";
    setNotification({
      message: message,
      type: type
    });
    setTimeout(function () {
      return setNotification(null);
    }, 5000); // Hide after 5 seconds
  };
  var handleInstallPlugin = /*#__PURE__*/function () {
    var _ref2 = _asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
      var response;
      return _regeneratorRuntime().wrap(function _callee$(_context) {
        while (1) switch (_context.prev = _context.next) {
          case 0:
            setLoading(true);
            _context.prev = 1;
            _context.next = 4;
            return external_wp_apiFetch_default()({
              path: window.wpmudev_cross_sell_data.restEndpointInstall,
              method: "POST",
              data: {
                plugin_slug: slug,
                current_slug: window.wpmudev_cross_sell_data.current_slug
              }
            });
          case 4:
            response = _context.sent;
            if (response.success) {
              setIsInstalled(true);
              showNotification(wp.element.createElement(external_React_namespaceObject.Fragment, {
                __self: plugin_card_this,
                __source: {
                  fileName: plugin_card_jsxFileName,
                  lineNumber: 51,
                  columnNumber: 11
                }
              }, wp.element.createElement("b", {
                __self: plugin_card_this,
                __source: {
                  fileName: plugin_card_jsxFileName,
                  lineNumber: 52,
                  columnNumber: 13
                }
              }, (0,external_wp_i18n_namespaceObject.__)(title, "plugin-cross-sell-textdomain")), (0,external_wp_i18n_namespaceObject.__)(" plugin has been installed successfully!", "plugin-cross-sell-textdomain")));
            } else {
              console.error("Installation failed:", response.message);
              showNotification((0,external_wp_i18n_namespaceObject.__)("Installation failed: ".concat(response.message), "plugin-cross-sell-textdomain"), "error");
            }
            _context.next = 12;
            break;
          case 8:
            _context.prev = 8;
            _context.t0 = _context["catch"](1);
            console.error("Error installing plugin:", _context.t0);
            showNotification((0,external_wp_i18n_namespaceObject.__)("Error installing plugin: ".concat(_context.t0.message), "plugin-cross-sell-textdomain"), "error");
          case 12:
            setLoading(false);
          case 13:
          case "end":
            return _context.stop();
        }
      }, _callee, null, [[1, 8]]);
    }));
    return function handleInstallPlugin() {
      return _ref2.apply(this, arguments);
    };
  }();
  var handleActivatePlugin = /*#__PURE__*/function () {
    var _ref3 = _asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee2() {
      var response;
      return _regeneratorRuntime().wrap(function _callee2$(_context2) {
        while (1) switch (_context2.prev = _context2.next) {
          case 0:
            setLoading(true);
            _context2.prev = 1;
            _context2.next = 4;
            return external_wp_apiFetch_default()({
              path: window.wpmudev_cross_sell_data.restEndpointActivate || "/wpmudev/v1/activate-plugin",
              method: "POST",
              data: {
                plugin_slug: slug,
                current_slug: window.wpmudev_cross_sell_data.current_slug
              }
            });
          case 4:
            response = _context2.sent;
            if (response.success) {
              setIsActive(true);
              showNotification(wp.element.createElement(external_React_namespaceObject.Fragment, {
                __self: plugin_card_this,
                __source: {
                  fileName: plugin_card_jsxFileName,
                  lineNumber: 99,
                  columnNumber: 11
                }
              }, wp.element.createElement("b", {
                __self: plugin_card_this,
                __source: {
                  fileName: plugin_card_jsxFileName,
                  lineNumber: 100,
                  columnNumber: 13
                }
              }, (0,external_wp_i18n_namespaceObject.__)(title, "plugin-cross-sell-textdomain")), (0,external_wp_i18n_namespaceObject.__)(" plugin has been activated successfully!", "plugin-cross-sell-textdomain")));
              setTimeout(function () {
                window.location.reload();
              }, 1000);
            } else {
              console.error("Activation failed:", response.message);
              showNotification((0,external_wp_i18n_namespaceObject.__)("Activation failed: ".concat(response.message), "plugin-cross-sell-textdomain"), "error");
            }
            _context2.next = 12;
            break;
          case 8:
            _context2.prev = 8;
            _context2.t0 = _context2["catch"](1);
            console.error("Error activating plugin:", _context2.t0);
            showNotification((0,external_wp_i18n_namespaceObject.__)("Error activating plugin: ".concat(_context2.t0.message), "plugin-cross-sell-textdomain"), "error");
          case 12:
            setLoading(false);
          case 13:
          case "end":
            return _context2.stop();
        }
      }, _callee2, null, [[1, 8]]);
    }));
    return function handleActivatePlugin() {
      return _ref3.apply(this, arguments);
    };
  }();
  return wp.element.createElement(external_React_namespaceObject.Fragment, {
    __self: plugin_card_this,
    __source: {
      fileName: plugin_card_jsxFileName,
      lineNumber: 134,
      columnNumber: 5
    }
  }, notification && wp.element.createElement("div", {
    className: "sui-floating-notices",
    __self: plugin_card_this,
    __source: {
      fileName: plugin_card_jsxFileName,
      lineNumber: 136,
      columnNumber: 9
    }
  }, wp.element.createElement(Notifications, {
    type: notification.type,
    __self: plugin_card_this,
    __source: {
      fileName: plugin_card_jsxFileName,
      lineNumber: 137,
      columnNumber: 11
    }
  }, wp.element.createElement("p", {
    __self: plugin_card_this,
    __source: {
      fileName: plugin_card_jsxFileName,
      lineNumber: 138,
      columnNumber: 13
    }
  }, notification.message))), wp.element.createElement(TagName, plugin_card_extends({
    className: "cross-sell-card"
  }, TagName === "a" ? {
    href: url,
    target: "_blank"
  } : {}, delegated, {
    __self: plugin_card_this,
    __source: {
      fileName: plugin_card_jsxFileName,
      lineNumber: 142,
      columnNumber: 7
    }
  }), wp.element.createElement("div", {
    className: "cross-sell-plugin-card-header",
    __self: plugin_card_this,
    __source: {
      fileName: plugin_card_jsxFileName,
      lineNumber: 147,
      columnNumber: 9
    }
  }, wp.element.createElement("div", {
    __self: plugin_card_this,
    __source: {
      fileName: plugin_card_jsxFileName,
      lineNumber: 148,
      columnNumber: 11
    }
  }, wp.element.createElement("img", {
    src: logo,
    alt: title || "Plugin logo",
    __self: plugin_card_this,
    __source: {
      fileName: plugin_card_jsxFileName,
      lineNumber: 149,
      columnNumber: 13
    }
  })), cta && wp.element.createElement(wp.element.Fragment, null, !isInstalled ? wp.element.createElement(Button, {
    label: loading ? (0,external_wp_i18n_namespaceObject.__)("Installing...", "plugin-cross-sell-textdomain") : (0,external_wp_i18n_namespaceObject.__)("Install", "plugin-cross-sell-textdomain"),
    design: "ghost",
    onClick: handleInstallPlugin,
    disabled: loading,
    __self: plugin_card_this,
    __source: {
      fileName: plugin_card_jsxFileName,
      lineNumber: 154,
      columnNumber: 17
    }
  }) : !isActive ? wp.element.createElement(Button, {
    label: loading ? (0,external_wp_i18n_namespaceObject.__)("Activating...", "plugin-cross-sell-textdomain") : (0,external_wp_i18n_namespaceObject.__)("Activate", "plugin-cross-sell-textdomain"),
    color: "blue",
    onClick: handleActivatePlugin,
    disabled: loading,
    __self: plugin_card_this,
    __source: {
      fileName: plugin_card_jsxFileName,
      lineNumber: 165,
      columnNumber: 17
    }
  }) : wp.element.createElement(Button, {
    label: (0,external_wp_i18n_namespaceObject.__)("Active", "plugin-cross-sell-textdomain"),
    design: "ghost",
    color: "green",
    style: {
      pointerEvents: "none"
    },
    __self: plugin_card_this,
    __source: {
      fileName: plugin_card_jsxFileName,
      lineNumber: 176,
      columnNumber: 17
    }
  }))), wp.element.createElement("div", {
    className: "cross-sell-plugin-card-content",
    __self: plugin_card_this,
    __source: {
      fileName: plugin_card_jsxFileName,
      lineNumber: 186,
      columnNumber: 9
    }
  }, wp.element.createElement("h2", {
    __self: plugin_card_this,
    __source: {
      fileName: plugin_card_jsxFileName,
      lineNumber: 187,
      columnNumber: 11
    }
  }, (0,external_wp_i18n_namespaceObject.__)(title, "plugin-cross-sell-textdomain")), wp.element.createElement("div", {
    className: "cross-sell-plugin-card-rating",
    __self: plugin_card_this,
    __source: {
      fileName: plugin_card_jsxFileName,
      lineNumber: 188,
      columnNumber: 11
    }
  }, wp.element.createElement("div", {
    className: "cross-sell-plugin-card-rating__stars",
    __self: plugin_card_this,
    __source: {
      fileName: plugin_card_jsxFileName,
      lineNumber: 189,
      columnNumber: 13
    }
  }, (0,external_wp_i18n_namespaceObject.__)(rating, "plugin-cross-sell-textdomain"), wp.element.createElement(StarAltIcon, {
    __self: plugin_card_this,
    __source: {
      fileName: plugin_card_jsxFileName,
      lineNumber: 191,
      columnNumber: 15
    }
  })), wp.element.createElement("div", {
    className: "cross-sell-plugin-card-rating__installs",
    __self: plugin_card_this,
    __source: {
      fileName: plugin_card_jsxFileName,
      lineNumber: 193,
      columnNumber: 13
    }
  }, (0,external_wp_i18n_namespaceObject.__)("".concat(active_installs, " active installs"), "plugin-cross-sell-textdomain"))), wp.element.createElement("span", {
    className: "cross-sell-plugin-card-description",
    __self: plugin_card_this,
    __source: {
      fileName: plugin_card_jsxFileName,
      lineNumber: 200,
      columnNumber: 11
    }
  }, (0,external_wp_i18n_namespaceObject.__)(description, "plugin-cross-sell-textdomain")), features && features.length > 0 && wp.element.createElement("div", {
    className: "cross-sell-plugin-card-features",
    __self: plugin_card_this,
    __source: {
      fileName: plugin_card_jsxFileName,
      lineNumber: 204,
      columnNumber: 13
    }
  }, features.map(function (feature, index) {
    return wp.element.createElement("div", {
      key: index,
      className: "cross-sell-plugin-card-feature",
      __self: plugin_card_this,
      __source: {
        fileName: plugin_card_jsxFileName,
        lineNumber: 206,
        columnNumber: 17
      }
    }, wp.element.createElement(CheckIcon, {
      __self: plugin_card_this,
      __source: {
        fileName: plugin_card_jsxFileName,
        lineNumber: 207,
        columnNumber: 19
      }
    }), (0,external_wp_i18n_namespaceObject.__)(feature, "plugin-cross-sell-textdomain"));
  })))));
};

;// ./src/components/plugins/plugins-free.jsx
var plugins_free_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/components/plugins/plugins-free.jsx",
  plugins_free_this = undefined;
function plugins_free_extends() { return plugins_free_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, plugins_free_extends.apply(null, arguments); }





// Access the localized free plugins data
var FREE_PLUGINS_OBJ = window.wpmudev_cross_sell_data.free_plugins || [];
var FREE_PLUGINS = Object.values(FREE_PLUGINS_OBJ);
var PluginsFree = function PluginsFree() {
  return wp.element.createElement(Section, {
    title: (0,external_wp_i18n_namespaceObject.__)("Try our other highly-rated free WordPress plugins", "plugin-cross-sell-textdomain"),
    description: (0,external_wp_i18n_namespaceObject.__)("From security to SEO to marketing, we’ve got you covered.", "plugin-cross-sell-textdomain"),
    className: "cross-sell-plugins--free",
    __self: plugins_free_this,
    __source: {
      fileName: plugins_free_jsxFileName,
      lineNumber: 12,
      columnNumber: 5
    }
  }, FREE_PLUGINS.length > 0 && wp.element.createElement("div", {
    className: "cross-sell-grid",
    __self: plugins_free_this,
    __source: {
      fileName: plugins_free_jsxFileName,
      lineNumber: 24,
      columnNumber: 9
    }
  }, FREE_PLUGINS.map(function (plugin) {
    return wp.element.createElement(PluginCard, plugins_free_extends({
      key: plugin.slug,
      cta: true
    }, plugin, {
      __self: plugins_free_this,
      __source: {
        fileName: plugins_free_jsxFileName,
        lineNumber: 26,
        columnNumber: 13
      }
    }));
  })));
};

;// ./src/components/plugins/plugins-pro.jsx
var plugins_pro_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/components/plugins/plugins-pro.jsx",
  plugins_pro_this = undefined;
function plugins_pro_extends() { return plugins_pro_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, plugins_pro_extends.apply(null, arguments); }






// Access the localized pro plugins data
var PRO_PLUGINS_OBJ = window.wpmudev_cross_sell_data.pro_plugins || [];
var PRO_PLUGINS = Object.values(PRO_PLUGINS_OBJ);
var PluginsPro = function PluginsPro() {
  return wp.element.createElement(Section, {
    title: (0,external_wp_i18n_namespaceObject.__)("Get a high-powered web-building suite, at no extra cost", "plugin-cross-sell-textdomain"),
    className: "cross-sell-plugins--pro",
    __self: plugins_pro_this,
    __source: {
      fileName: plugins_pro_jsxFileName,
      lineNumber: 13,
      columnNumber: 5
    }
  }, PRO_PLUGINS.length > 0 && wp.element.createElement("div", {
    className: "cross-sell-grid",
    __self: plugins_pro_this,
    __source: {
      fileName: plugins_pro_jsxFileName,
      lineNumber: 21,
      columnNumber: 9
    }
  }, PRO_PLUGINS.map(function (plugin) {
    return wp.element.createElement(PluginCard, plugins_pro_extends({
      key: plugin.slug,
      as: "a"
    }, plugin, {
      __self: plugins_pro_this,
      __source: {
        fileName: plugins_pro_jsxFileName,
        lineNumber: 23,
        columnNumber: 13
      }
    }));
  })), wp.element.createElement("div", {
    className: "cross-sell-text-center",
    __self: plugins_pro_this,
    __source: {
      fileName: plugins_pro_jsxFileName,
      lineNumber: 27,
      columnNumber: 7
    }
  }, wp.element.createElement("a", {
    role: "button",
    href: getUtmUrl("https://wpmudev.com/plugins/", {
      utm_campaign: 'cross-sell_plugin_all-plugins'
    }),
    target: "_blank",
    className: "sui-button sui-button-ghost",
    __self: plugins_pro_this,
    __source: {
      fileName: plugins_pro_jsxFileName,
      lineNumber: 28,
      columnNumber: 9
    }
  }, (0,external_wp_i18n_namespaceObject.__)("View all pro plugins", "plugin-cross-sell-textdomain"))));
};

;// ./src/components/plugins/index.jsx



;// ./src/cross-sell-page/free.jsx
var free_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/cross-sell-page/free.jsx",
  free_this = undefined;
function free_objectDestructuringEmpty(t) { if (null == t) throw new TypeError("Cannot destructure " + t); }
function free_extends() { return free_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, free_extends.apply(null, arguments); }


var FreeContent = function FreeContent(_ref) {
  var delegated = free_extends({}, (free_objectDestructuringEmpty(_ref), _ref));
  return wp.element.createElement(PluginsFree, free_extends({}, delegated, {
    __self: free_this,
    __source: {
      fileName: free_jsxFileName,
      lineNumber: 6,
      columnNumber: 10
    }
  }));
};

;// ./src/components/horizontal-line/line.jsx
var line_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/components/horizontal-line/line.jsx",
  line_this = undefined;

var HorizontalLine = function HorizontalLine() {
  return wp.element.createElement("hr", {
    className: "cross-sell-horizontal-line",
    __self: line_this,
    __source: {
      fileName: line_jsxFileName,
      lineNumber: 4,
      columnNumber: 10
    }
  });
};

;// ./src/components/horizontal-line/index.jsx

;// ./src/components/features/features.jsx
var features_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/components/features/features.jsx",
  features_this = undefined;





var FEATURES = [{
  icon: ServerIcon,
  title: (0,external_wp_i18n_namespaceObject.__)("Fully managed hosting", "plugin-cross-sell-textdomain"),
  description: (0,external_wp_i18n_namespaceObject.__)("Deliver lightning-fast, secure websites with a 99.9% SLA—complete with isolated environments, global server locations, and expert support.", "plugin-cross-sell-textdomain"),
  link: getUtmUrl("https://wpmudev.com/hosting/", {
    utm_campaign: 'cross-sell_service_hosting'
  })
}, {
  icon: SettingsIcon,
  title: (0,external_wp_i18n_namespaceObject.__)("Site management", "plugin-cross-sell-textdomain"),
  description: (0,external_wp_i18n_namespaceObject.__)("Take command of all your WordPress sites with one simple dashboard to automate updates, monitor performance, and generate white-label client reports.", "plugin-cross-sell-textdomain"),
  link: getUtmUrl("https://wpmudev.com/site-management/", {
    utm_campaign: 'cross-sell_service_hub'
  })
}, {
  icon: GlobeIcon,
  title: (0,external_wp_i18n_namespaceObject.__)("Domains", "plugin-cross-sell-textdomain"),
  description: (0,external_wp_i18n_namespaceObject.__)("With access to over 250 TLDs, seamless integration and free privacy protection, you can offer domain registration services at unbeatable prices.", "plugin-cross-sell-textdomain"),
  link: getUtmUrl("https://wpmudev.com/domains/", {
    utm_campaign: 'cross-sell_service_domains'
  })
}, {
  icon: LayersIcon,
  title: (0,external_wp_i18n_namespaceObject.__)("100+ template library", "plugin-cross-sell-textdomain"),
  description: (0,external_wp_i18n_namespaceObject.__)("Create beautiful sites in seconds with pre-configured site templates for you and your client projects—compatible with every plugin, theme builder and tool.", "plugin-cross-sell-textdomain"),
  link: getUtmUrl("https://wpmudev.com/site-templates/", {
    utm_campaign: 'cross-sell_service_templates'
  })
}, {
  icon: EmailIcon,
  title: (0,external_wp_i18n_namespaceObject.__)("Get Pro Email", "plugin-cross-sell-textdomain"),
  description: (0,external_wp_i18n_namespaceObject.__)("Add-on private, ad-free IMAP webmail for easily managed, auto-synced, professional emails for you and your clients with 5-50GB storage options.", "plugin-cross-sell-textdomain"),
  link: getUtmUrl("https://wpmudev.com/webmail/", {
    utm_campaign: 'cross-sell_service_email'
  })
}, {
  icon: SupportIcon,
  title: (0,external_wp_i18n_namespaceObject.__)("Unparalleled support", "plugin-cross-sell-textdomain"),
  description: (0,external_wp_i18n_namespaceObject.__)("Chat with our support team anytime—24/7, 365 days a year—with an average response time of 2 minutes. We’ll even log in and fix issues for you and your clients!", "plugin-cross-sell-textdomain"),
  link: getUtmUrl("https://wpmudev.com/get-support/", {
    utm_campaign: 'cross-sell_service_support'
  })
}];
var Features = function Features() {
  return wp.element.createElement(Section, {
    title: (0,external_wp_i18n_namespaceObject.__)("Everything you need to grow a successful agency - at an unrivaled value", "plugin-cross-sell-textdomain"),
    __self: features_this,
    __source: {
      fileName: features_jsxFileName,
      lineNumber: 74,
      columnNumber: 5
    }
  }, FEATURES.length > 0 && wp.element.createElement("div", {
    className: "cross-sell-features cross-sell-grid",
    __self: features_this,
    __source: {
      fileName: features_jsxFileName,
      lineNumber: 81,
      columnNumber: 9
    }
  }, FEATURES.map(function (feature) {
    return wp.element.createElement("a", {
      className: "cross-sell-card",
      key: feature.title,
      href: feature.link,
      target: "_blank",
      __self: features_this,
      __source: {
        fileName: features_jsxFileName,
        lineNumber: 83,
        columnNumber: 13
      }
    }, wp.element.createElement("div", {
      className: "cross-sell-feature-header",
      __self: features_this,
      __source: {
        fileName: features_jsxFileName,
        lineNumber: 89,
        columnNumber: 15
      }
    }, wp.element.createElement(feature.icon, {
      __self: features_this,
      __source: {
        fileName: features_jsxFileName,
        lineNumber: 90,
        columnNumber: 17
      }
    }), wp.element.createElement("h3", {
      __self: features_this,
      __source: {
        fileName: features_jsxFileName,
        lineNumber: 91,
        columnNumber: 17
      }
    }, feature.title), wp.element.createElement(ArrowIcon, {
      className: "cross-sell-feature-arrow",
      __self: features_this,
      __source: {
        fileName: features_jsxFileName,
        lineNumber: 92,
        columnNumber: 17
      }
    })), wp.element.createElement("p", {
      __self: features_this,
      __source: {
        fileName: features_jsxFileName,
        lineNumber: 94,
        columnNumber: 15
      }
    }, feature.description));
  })));
};

;// ./src/components/features/index.jsx

;// ./src/images/services/development.png
const development_namespaceObject = __webpack_require__.p + "f3b30d2cd7abe535ba1c.png";
;// ./src/images/services/malware.png
const malware_namespaceObject = __webpack_require__.p + "da1fd17bdf7158f7c6a7.png";
;// ./src/images/services/monitoring.png
const monitoring_namespaceObject = __webpack_require__.p + "27c3b14bfd1aaf450606.png";
;// ./src/images/services/optimization.png
const optimization_namespaceObject = __webpack_require__.p + "97bad02a1bd53924a709.png";
;// ./src/components/services/services.jsx
var services_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/components/services/services.jsx",
  services_this = undefined;









var SERVICES_ITEMS = [{
  icon: development_namespaceObject,
  title: (0,external_wp_i18n_namespaceObject.__)("On-Demand Development", "plugin-cross-sell-textdomain"),
  description: (0,external_wp_i18n_namespaceObject.__)("Need assistance with CSS or custom functionality? Our experts create scripts to solve WordPress issues and enhance your site.", "plugin-cross-sell-textdomain"),
  link: getUtmUrl("https://wpmudev.com/expert-services/", {
    utm_campaign: 'cross-sell_expert_odd'
  })
}, {
  icon: monitoring_namespaceObject,
  title: (0,external_wp_i18n_namespaceObject.__)("Proactive Monitoring", "plugin-cross-sell-textdomain"),
  description: (0,external_wp_i18n_namespaceObject.__)("We monitor WPMU DEV hosted sites 24/7 and fix them fast if they go down, you don’t have to do anything.", "plugin-cross-sell-textdomain"),
  link: getUtmUrl("https://wpmudev.com/expert-services/", {
    utm_campaign: 'cross-sell_expert_uptime'
  })
}, {
  icon: optimization_namespaceObject,
  title: (0,external_wp_i18n_namespaceObject.__)("Speed Optimization", "plugin-cross-sell-textdomain"),
  description: (0,external_wp_i18n_namespaceObject.__)("Page Speed lagging behind? Our experts will give your site a guaranteed scores of 90+ on desktop and 75+ on mobile.", "plugin-cross-sell-textdomain"),
  link: getUtmUrl("https://wpmudev.com/expert-services/", {
    utm_campaign: 'cross-sell_expert_speed'
  })
}, {
  icon: malware_namespaceObject,
  title: (0,external_wp_i18n_namespaceObject.__)("Malware Removal", "plugin-cross-sell-textdomain"),
  description: (0,external_wp_i18n_namespaceObject.__)("Need help with CSS or custom functionality? Our experts create scripts to solve WordPress issues and enhance your site.", "plugin-cross-sell-textdomain"),
  link: getUtmUrl("https://wpmudev.com/expert-services/", {
    utm_campaign: 'cross-sell_expert_security'
  })
}];
var Services = function Services() {
  return wp.element.createElement(Section, {
    title: (0,external_wp_i18n_namespaceObject.__)("Expert services", "plugin-cross-sell-textdomain"),
    className: "cross-sell-section--services",
    description: (0,external_wp_i18n_namespaceObject.__)("Hand off any site issues to our WordPress expert team’s 20+ years of expertise.", "plugin-cross-sell-textdomain"),
    tag: wp.element.createElement("span", {
      className: "sui-tag sui-tag-yellow sui-tag-sm",
      __self: services_this,
      __source: {
        fileName: services_jsxFileName,
        lineNumber: 60,
        columnNumber: 9
      }
    }, (0,external_wp_i18n_namespaceObject.__)("Add-on services", "plugin-cross-sell-textdomain")),
    __self: services_this,
    __source: {
      fileName: services_jsxFileName,
      lineNumber: 52,
      columnNumber: 5
    }
  }, SERVICES_ITEMS.length > 0 && wp.element.createElement("div", {
    className: "cross-sell-grid cross-sell-services-cards",
    __self: services_this,
    __source: {
      fileName: services_jsxFileName,
      lineNumber: 66,
      columnNumber: 9
    }
  }, SERVICES_ITEMS.map(function (service) {
    return wp.element.createElement("a", {
      key: service.title,
      className: "cross-sell-card",
      href: service.link,
      target: "_blank",
      __self: services_this,
      __source: {
        fileName: services_jsxFileName,
        lineNumber: 68,
        columnNumber: 13
      }
    }, wp.element.createElement("div", {
      className: "cross-sell-grid__item-icon",
      __self: services_this,
      __source: {
        fileName: services_jsxFileName,
        lineNumber: 74,
        columnNumber: 15
      }
    }, wp.element.createElement("img", {
      src: service.icon,
      alt: service.title,
      __self: services_this,
      __source: {
        fileName: services_jsxFileName,
        lineNumber: 75,
        columnNumber: 17
      }
    })), wp.element.createElement("h3", {
      __self: services_this,
      __source: {
        fileName: services_jsxFileName,
        lineNumber: 77,
        columnNumber: 15
      }
    }, service.title), wp.element.createElement("p", {
      __self: services_this,
      __source: {
        fileName: services_jsxFileName,
        lineNumber: 78,
        columnNumber: 15
      }
    }, service.description), wp.element.createElement(ArrowAltIcon, {
      className: "cross-sell-card-arrow-alt",
      __self: services_this,
      __source: {
        fileName: services_jsxFileName,
        lineNumber: 79,
        columnNumber: 15
      }
    }));
  })));
};

;// ./src/components/services/index.jsx

;// ./src/components/button/button.jsx
var button_excluded = ["as", "className", "children"];
var button_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/components/button/button.jsx",
  button_this = undefined;
function button_extends() { return button_extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, button_extends.apply(null, arguments); }
function button_objectWithoutProperties(e, t) { if (null == e) return {}; var o, r, i = button_objectWithoutPropertiesLoose(e, t); if (Object.getOwnPropertySymbols) { var n = Object.getOwnPropertySymbols(e); for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]); } return i; }
function button_objectWithoutPropertiesLoose(r, e) { if (null == r) return {}; var t = {}; for (var n in r) if ({}.hasOwnProperty.call(r, n)) { if (-1 !== e.indexOf(n)) continue; t[n] = r[n]; } return t; }

var button_Button = function Button(_ref) {
  var _ref$as = _ref.as,
    as = _ref$as === void 0 ? "button" : _ref$as,
    _ref$className = _ref.className,
    className = _ref$className === void 0 ? "" : _ref$className,
    children = _ref.children,
    delegated = button_objectWithoutProperties(_ref, button_excluded);
  var TagName = as;
  return wp.element.createElement(TagName, button_extends({
    className: "cross-sell-button ".concat(className)
  }, delegated, {
    __self: button_this,
    __source: {
      fileName: button_jsxFileName,
      lineNumber: 7,
      columnNumber: 5
    }
  }), children);
};

;// ./src/cross-sell-page/pro.jsx
var pro_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/cross-sell-page/pro.jsx",
  pro_this = undefined;










var ctaContent = (0,external_wp_element_namespaceObject.createInterpolateElement)((0,external_wp_i18n_namespaceObject.__)("Find your plan (From $3/m <highlight>$15/m</highlight>)", "plugin-cross-sell-textdomain"), {
  highlight: wp.element.createElement("span", {
    className: "cross-sell-highlighted-price",
    __self: undefined,
    __source: {
      fileName: pro_jsxFileName,
      lineNumber: 18,
      columnNumber: 16
    }
  })
});
var ProContent = function ProContent() {
  return wp.element.createElement(external_React_namespaceObject.Fragment, {
    __self: pro_this,
    __source: {
      fileName: pro_jsxFileName,
      lineNumber: 24,
      columnNumber: 5
    }
  }, wp.element.createElement(Section, {
    title: (0,external_wp_i18n_namespaceObject.__)("Did you know WPMU DEV Membership includes ", "plugin-cross-sell-textdomain"),
    titleUnderline: (0,external_wp_i18n_namespaceObject.__)("ALL our Pro Plugins?", "plugin-cross-sell-textdomain"),
    description: (0,external_wp_i18n_namespaceObject.__)("Plus, everything your agency needs for hosting, reselling and more.", "plugin-cross-sell-textdomain"),
    className: "cross-sell-section-membership",
    __self: pro_this,
    __source: {
      fileName: pro_jsxFileName,
      lineNumber: 25,
      columnNumber: 7
    }
  }, wp.element.createElement("div", {
    className: "cross-sell-site",
    __self: pro_this,
    __source: {
      fileName: pro_jsxFileName,
      lineNumber: 40,
      columnNumber: 9
    }
  }, wp.element.createElement(button_Button, {
    as: "a",
    href: getUtmUrl("https://wpmudev.com/pricing/", {
      utm_campaign: 'cross-sell_top-cta'
    }),
    target: "_blank",
    __self: pro_this,
    __source: {
      fileName: pro_jsxFileName,
      lineNumber: 41,
      columnNumber: 11
    }
  }, ctaContent))), wp.element.createElement(HorizontalLine, {
    __self: pro_this,
    __source: {
      fileName: pro_jsxFileName,
      lineNumber: 46,
      columnNumber: 7
    }
  }), wp.element.createElement(PluginsPro, {
    __self: pro_this,
    __source: {
      fileName: pro_jsxFileName,
      lineNumber: 47,
      columnNumber: 7
    }
  }), wp.element.createElement(HorizontalLine, {
    __self: pro_this,
    __source: {
      fileName: pro_jsxFileName,
      lineNumber: 48,
      columnNumber: 7
    }
  }), wp.element.createElement(Features, {
    __self: pro_this,
    __source: {
      fileName: pro_jsxFileName,
      lineNumber: 49,
      columnNumber: 7
    }
  }), wp.element.createElement(HorizontalLine, {
    __self: pro_this,
    __source: {
      fileName: pro_jsxFileName,
      lineNumber: 50,
      columnNumber: 7
    }
  }), wp.element.createElement(Services, {
    __self: pro_this,
    __source: {
      fileName: pro_jsxFileName,
      lineNumber: 51,
      columnNumber: 7
    }
  }), wp.element.createElement(Section, {
    title: (0,external_wp_i18n_namespaceObject.__)("Your comprehensive toolkit for WordPress website management.", "plugin-cross-sell-textdomain"),
    __self: pro_this,
    __source: {
      fileName: pro_jsxFileName,
      lineNumber: 52,
      columnNumber: 7
    }
  }, wp.element.createElement("div", {
    className: "cross-sell-site",
    __self: pro_this,
    __source: {
      fileName: pro_jsxFileName,
      lineNumber: 58,
      columnNumber: 9
    }
  }, wp.element.createElement(button_Button, {
    as: "a",
    href: getUtmUrl("https://wpmudev.com/pricing/", {
      utm_campaign: 'cross-sell_bottom-cta'
    }),
    target: "_blank",
    __self: pro_this,
    __source: {
      fileName: pro_jsxFileName,
      lineNumber: 59,
      columnNumber: 11
    }
  }, ctaContent), wp.element.createElement("span", {
    className: "cross-sell-site-text cross-sell-text-center",
    __self: pro_this,
    __source: {
      fileName: pro_jsxFileName,
      lineNumber: 62,
      columnNumber: 11
    }
  }, (0,external_wp_i18n_namespaceObject.__)("30 day guarantee: If you don’t love it, we’ll give you your money back - no questions asked.", "plugin-cross-sell-textdomain")))));
};

;// ./src/cross-sell-page/wrapper.jsx
var wrapper_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/cross-sell-page/wrapper.jsx",
  wrapper_this = undefined;








var Wrapper = function Wrapper() {
  return wp.element.createElement("div", {
    className: "wpmudev-cross-sell-page",
    __self: wrapper_this,
    __source: {
      fileName: wrapper_jsxFileName,
      lineNumber: 12,
      columnNumber: 5
    }
  }, wp.element.createElement("div", {
    className: "sui-box",
    __self: wrapper_this,
    __source: {
      fileName: wrapper_jsxFileName,
      lineNumber: 13,
      columnNumber: 7
    }
  }, wp.element.createElement("div", {
    className: "sui-box-body",
    __self: wrapper_this,
    __source: {
      fileName: wrapper_jsxFileName,
      lineNumber: 14,
      columnNumber: 9
    }
  }, wp.element.createElement(HeroSection, {
    __self: wrapper_this,
    __source: {
      fileName: wrapper_jsxFileName,
      lineNumber: 15,
      columnNumber: 11
    }
  }), wp.element.createElement(Tabs, {
    __self: wrapper_this,
    __source: {
      fileName: wrapper_jsxFileName,
      lineNumber: 16,
      columnNumber: 11
    }
  }, wp.element.createElement(TabsMenu, {
    __self: wrapper_this,
    __source: {
      fileName: wrapper_jsxFileName,
      lineNumber: 17,
      columnNumber: 13
    }
  }, wp.element.createElement(Tab, {
    __self: wrapper_this,
    __source: {
      fileName: wrapper_jsxFileName,
      lineNumber: 18,
      columnNumber: 15
    }
  }, (0,external_wp_i18n_namespaceObject.__)("Free Plugins you\u2019ll like", "plugin-cross-sell-textdomain")), wp.element.createElement(Tab, {
    __self: wrapper_this,
    __source: {
      fileName: wrapper_jsxFileName,
      lineNumber: 21,
      columnNumber: 15
    }
  }, (0,external_wp_i18n_namespaceObject.__)("Pro Plugins You\u2019ll LOVE (Bundle Deal)", "plugin-cross-sell-textdomain"))), wp.element.createElement(TabsContent, {
    __self: wrapper_this,
    __source: {
      fileName: wrapper_jsxFileName,
      lineNumber: 28,
      columnNumber: 13
    }
  }, wp.element.createElement(TabPanel, {
    __self: wrapper_this,
    __source: {
      fileName: wrapper_jsxFileName,
      lineNumber: 29,
      columnNumber: 15
    }
  }, wp.element.createElement(FreeContent, {
    __self: wrapper_this,
    __source: {
      fileName: wrapper_jsxFileName,
      lineNumber: 30,
      columnNumber: 17
    }
  })), wp.element.createElement(TabPanel, {
    __self: wrapper_this,
    __source: {
      fileName: wrapper_jsxFileName,
      lineNumber: 32,
      columnNumber: 15
    }
  }, wp.element.createElement(ProContent, {
    __self: wrapper_this,
    __source: {
      fileName: wrapper_jsxFileName,
      lineNumber: 33,
      columnNumber: 17
    }
  })))), wp.element.createElement(Highlights, {
    __self: wrapper_this,
    __source: {
      fileName: wrapper_jsxFileName,
      lineNumber: 37,
      columnNumber: 11
    }
  }), wp.element.createElement(Footer, {
    __self: wrapper_this,
    __source: {
      fileName: wrapper_jsxFileName,
      lineNumber: 38,
      columnNumber: 11
    }
  }))));
};

;// ./src/cross-sell-page/main.jsx
var main_jsxFileName = "/opt/atlassian/pipelines/agent/build/src/cross-sell-page/main.jsx";



var domElement = document.getElementById(window.wpmudev_cross_sell_data.dom_element_id);
if (domElement) {
  if (external_wp_element_namespaceObject.createRoot) {
    (0,external_wp_element_namespaceObject.createRoot)(domElement).render(wp.element.createElement(external_wp_element_namespaceObject.StrictMode, {
      __self: undefined,
      __source: {
        fileName: main_jsxFileName,
        lineNumber: 12,
        columnNumber: 7
      }
    }, wp.element.createElement(Wrapper, {
      __self: undefined,
      __source: {
        fileName: main_jsxFileName,
        lineNumber: 13,
        columnNumber: 9
      }
    })));
  } else {
    (0,external_wp_element_namespaceObject.render)(wp.element.createElement(external_wp_element_namespaceObject.StrictMode, {
      __self: undefined,
      __source: {
        fileName: main_jsxFileName,
        lineNumber: 18,
        columnNumber: 7
      }
    }, wp.element.createElement(Wrapper, {
      __self: undefined,
      __source: {
        fileName: main_jsxFileName,
        lineNumber: 19,
        columnNumber: 9
      }
    })), domElement);
  }
}
/******/ })()
;
//# sourceMappingURL=crosssellpage.js.map
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
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assets/blocks/pro-ad/pro-ad.jsx":
/*!*********************************************!*\
  !*** ./src/assets/blocks/pro-ad/pro-ad.jsx ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

(function (wpI18n, wpHooks, wpBlockEditor) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    var addFilter = wpHooks.addFilter;
    var sprintf = wpI18n.sprintf,
        __ = wpI18n.__;
    var _wpBlockEditor = wpBlockEditor,
        InspectorControls = _wpBlockEditor.InspectorControls;


    var SUPPORTED_BLOCKS = ['advgb/accordion-item', 'advgb/accordions', 'advgb/adv-tabs', 'advgb/adv-tab', 'advgb/recent-posts'];

    function advgbGetBlockTitle(name) {
        switch (name) {
            case 'advgb/accordion-item':
            case 'advgb/accordions':
                return __('Accordion', 'advanced-gutenberg');
                break;

            case 'advgb/adv-tabs':
            case 'advgb/tab':
                return __('Tabs', 'advanced-gutenberg');
                break;

            case 'advgb/recent-posts':
                return __('Content Display', 'advanced-gutenberg');
                break;

            default:
                return __('PublishPress', 'advanced-gutenberg');
                break;
        }
    }

    // Add Upgrade to Pro Ad in sidebar
    addFilter('editor.BlockEdit', 'advgb/proAd', function (BlockEdit) {
        return function (props) {
            return [React.createElement(BlockEdit, _extends({ key: 'block-edit-custom-class-name' }, props)), props.isSelected && SUPPORTED_BLOCKS.includes(props.name) && React.createElement(
                InspectorControls,
                { key: 'advgb-custom-controls' },
                React.createElement(
                    'div',
                    { className: 'components-panel__body advgb-pro-ad-wrapper' },
                    sprintf(__('Want more features in your %s blocks?', 'advanced-gutenberg'), advgbGetBlockTitle(props.name)),
                    React.createElement('br', null),
                    React.createElement(
                        'a',
                        { href: 'https://publishpress.com/links/blocks', 'class': 'advgb-pro-ad-btn', target: '_blank' },
                        __('Upgrade to Pro', 'advanced-gutenberg')
                    )
                )
            )];
        };
    });
})(wp.i18n, wp.hooks, wp.blockEditor);

/***/ }),

/***/ 0:
/*!***************************************************!*\
  !*** multi ./src/assets/blocks/pro-ad/pro-ad.jsx ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! ./src/assets/blocks/pro-ad/pro-ad.jsx */"./src/assets/blocks/pro-ad/pro-ad.jsx");


/***/ })

/******/ });
//# sourceMappingURL=pro-ad.js.map
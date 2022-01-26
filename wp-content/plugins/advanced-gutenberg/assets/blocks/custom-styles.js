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

/***/ "./src/assets/blocks/customstyles/custom-styles.jsx":
/*!**********************************************************!*\
  !*** ./src/assets/blocks/customstyles/custom-styles.jsx ***!
  \**********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

(function (wpI18n, wpHooks, wpBlocks, wpBlockEditor, wpComponents, wpCompose) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    var addFilter = wpHooks.addFilter;
    var __ = wpI18n.__;
    var hasBlockSupport = wpBlocks.hasBlockSupport;
    var _wpBlockEditor = wpBlockEditor,
        InspectorControls = _wpBlockEditor.InspectorControls;
    var SelectControl = wpComponents.SelectControl;
    var createHigherOrderComponent = wpCompose.createHigherOrderComponent;


    var SUPPORTED_BLOCKS = ['core/paragraph', 'core/heading', 'core/list', 'core/code', 'core/preformatted', 'core/table', 'core/columns', 'core/column', 'core/group', 'core/image'];

    // Register custom styles to blocks attributes
    addFilter('blocks.registerBlockType', 'advgb/registerCustomStyleClass', function (settings) {
        if (SUPPORTED_BLOCKS.includes(settings.name)) {
            settings.attributes = _extends(settings.attributes, {
                customStyle: {
                    type: 'string'
                },
                identifyColor: {
                    type: 'string'
                }
            });
        }

        return settings;
    });

    // Add option to return to default style
    if (typeof advgbBlocks.customStyles !== 'undefined' && advgbBlocks.customStyles) {
        advgbBlocks.customStyles.unshift({
            id: 0,
            label: __('Select a custom style', 'advanced-gutenberg'),
            value: '',
            identifyColor: ''
        });
    }

    // Add option to select custom styles for supported blocks
    addFilter('editor.BlockEdit', 'advgb/customStyles', function (BlockEdit) {
        return function (props) {
            return [React.createElement(BlockEdit, _extends({ key: 'block-edit-custom-class-name' }, props)), props.isSelected && SUPPORTED_BLOCKS.includes(props.name) && React.createElement(
                InspectorControls,
                { key: 'advgb-custom-controls' },
                React.createElement(
                    'div',
                    { className: 'advgb-custom-styles-wrapper' },
                    React.createElement(SelectControl, {
                        label: [__('Custom styles', 'advanced-gutenberg'), React.createElement('span', { className: 'components-panel__color-area',
                            key: 'customstyle-identify',
                            style: {
                                background: props.attributes.identifyColor,
                                verticalAlign: 'text-bottom',
                                borderRadius: '50%',
                                border: 'none',
                                width: '16px',
                                height: '16px',
                                display: 'inline-block',
                                marginLeft: '10px'
                            } })],
                        help: __('This option let you add custom style for the current block', 'advanced-gutenberg'),
                        value: props.attributes.customStyle,
                        options: advgbBlocks.customStyles.map(function (cstyle, index) {
                            if (cstyle.title) advgbBlocks.customStyles[index].label = cstyle.title;
                            if (cstyle.name) advgbBlocks.customStyles[index].value = cstyle.name;

                            return cstyle;
                        }),
                        onChange: function onChange(cstyle) {
                            var identifyColor = advgbBlocks.customStyles.filter(function (style) {
                                return style.value === cstyle;
                            })[0].identifyColor;

                            props.setAttributes({
                                customStyle: cstyle,
                                identifyColor: identifyColor,
                                backgroundColor: undefined,
                                textColor: undefined,
                                fontSize: undefined
                            });
                        }
                    })
                )
            )];
        };
    });

    // Apply custom styles on front-end
    addFilter('blocks.getSaveContent.extraProps', 'advgb/loadFrontendCustomStyles', function (extraProps, blockType, attributes) {
        if (hasBlockSupport(blockType, 'customStyle', true) && attributes.customStyle) {
            if (typeof extraProps.className === 'undefined') {
                extraProps.className = attributes.customStyle;
            } else {
                extraProps.className += ' ' + attributes.customStyle;
                extraProps.className = extraProps.className.trim();
            }
        }

        return extraProps;
    });

    var withStyleClasses = createHigherOrderComponent(function (BlockListBlock) {
        return function (props) {
            if (!SUPPORTED_BLOCKS.includes(props.name) || !hasBlockSupport(props.name, 'customStyle', true)) {
                return React.createElement(BlockListBlock, props);
            }

            var customStyle = props.attributes.customStyle;


            return React.createElement(BlockListBlock, _extends({}, props, { className: '' + customStyle }));
        };
    }, 'withStyleClasses');

    // Apply custom styles on back-end
    wp.hooks.addFilter('editor.BlockListBlock', 'advgb/loadBackendCustomStyles', withStyleClasses);
})(wp.i18n, wp.hooks, wp.blocks, wp.blockEditor, wp.components, wp.compose);

/***/ }),

/***/ 0:
/*!****************************************************************!*\
  !*** multi ./src/assets/blocks/customstyles/custom-styles.jsx ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! ./src/assets/blocks/customstyles/custom-styles.jsx */"./src/assets/blocks/customstyles/custom-styles.jsx");


/***/ })

/******/ });
//# sourceMappingURL=custom-styles.js.map
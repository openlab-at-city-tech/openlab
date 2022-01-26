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

/***/ "./src/assets/blocks/editor-sidebar/post-sidebar.jsx":
/*!***********************************************************!*\
  !*** ./src/assets/blocks/editor-sidebar/post-sidebar.jsx ***!
  \***********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

(function (wpI18n, wpPlugins, wpElement, wpData, wpComponents, wpEditPost) {
    var __ = wpI18n.__;
    var registerPlugin = wpPlugins.registerPlugin;
    var Component = wpElement.Component,
        Fragment = wpElement.Fragment;
    var select = wpData.select,
        withSelect = wpData.withSelect,
        withDispatch = wpData.withDispatch;
    var PanelBody = wpComponents.PanelBody,
        ButtonGroup = wpComponents.ButtonGroup,
        Button = wpComponents.Button;
    var PluginSidebar = wpEditPost.PluginSidebar,
        PluginSidebarMoreMenuItem = wpEditPost.PluginSidebarMoreMenuItem;
    var compose = wp.compose.compose;


    var sidebarName = "advgb-editor-sidebar";
    var sidebarTitle = __('PublishPress Blocks Settings', 'advanced-gutenberg');
    var sidebarIcon = "layout";
    var VISUAL_GUIDE_SETTINGS = [{ label: __('Inherit from global settings', 'advanced-gutenberg'), value: '' }, { label: __('Enable', 'advanced-gutenberg'), value: 'enable' }, { label: __('Disable', 'advanced-gutenberg'), value: 'disable' }];
    var EDITOR_WIDTH_SETTINGS = [{ label: __('Inherit from global settings', 'advanced-gutenberg'), value: '' }, { label: __('Original', 'advanced-gutenberg'), value: 'default' }, { label: __('Large', 'advanced-gutenberg'), value: 'large' }, { label: __('Full width', 'advanced-gutenberg'), value: 'full' }];

    var updateBodyClass = function updateBodyClass() {
        var postMetaData = select('core/editor').getEditedPostAttribute('meta');
        if (!postMetaData) return null;
        var advgb_blocks_editor_width = postMetaData.advgb_blocks_editor_width,
            advgb_blocks_columns_visual_guide = postMetaData.advgb_blocks_columns_visual_guide;

        var bodyClass = window.document.body.classList;

        bodyClass.remove('advgb-editor-width-default', 'advgb-editor-width-large', 'advgb-editor-width-full', 'advgb-editor-col-guide-enable', 'advgb-editor-col-guide-disable');

        // Editor width
        if (!!advgb_blocks_editor_width) {
            bodyClass.add('advgb-editor-width-' + advgb_blocks_editor_width);
        } else {
            // Global
            bodyClass.add('advgb-editor-width-' + advg_settings.editor_width_global);
        }

        // Columns visual guide
        if (!!advgb_blocks_columns_visual_guide) {
            bodyClass.add('advgb-editor-col-guide-' + advgb_blocks_columns_visual_guide);
        } else {
            // Global
            bodyClass.add('advgb-editor-col-guide-' + advg_settings.enable_columns_visual_guide_global);
        }
    };

    // Line below stopped working - https://github.com/WordPress/gutenberg/issues/28032#issuecomment-759723289
    // window.document.addEventListener("DOMContentLoaded", updateBodyClass);

    var AdvSidebar = function (_Component) {
        _inherits(AdvSidebar, _Component);

        function AdvSidebar() {
            _classCallCheck(this, AdvSidebar);

            return _possibleConstructorReturn(this, (AdvSidebar.__proto__ || Object.getPrototypeOf(AdvSidebar)).apply(this, arguments));
        }

        _createClass(AdvSidebar, [{
            key: 'onUpdateMeta',
            value: function onUpdateMeta(metaData) {
                var _props = this.props,
                    metaValues = _props.metaValues,
                    updateMetaField = _props.updateMetaField;

                var meta = _extends({}, metaValues, metaData);

                updateMetaField(meta);
                updateBodyClass();
            }
        }, {
            key: 'render',
            value: function render() {
                var _this2 = this;

                var _props2 = this.props,
                    columnsVisualGuide = _props2.columnsVisualGuide,
                    editorWidth = _props2.editorWidth;


                return React.createElement(
                    Fragment,
                    null,
                    React.createElement(
                        'div',
                        { className: 'advgb-editor-sidebar-note' },
                        __('These settings will override the PublishPress Blocks global settings.', 'advanced-gutenberg')
                    ),
                    React.createElement(
                        PanelBody,
                        { title: __('Editor width', 'advanced-gutenberg') },
                        React.createElement(
                            'div',
                            { className: 'advgb-editor-sidebar-note' },
                            __('Change your editor width', 'advanced-gutenberg')
                        ),
                        React.createElement(
                            ButtonGroup,
                            { className: 'advgb-button-group' },
                            EDITOR_WIDTH_SETTINGS.map(function (setting, index) {
                                return React.createElement(
                                    Button,
                                    { className: 'advgb-button',
                                        key: index,
                                        isSecondary: setting.value !== editorWidth,
                                        isPrimary: setting.value === editorWidth,
                                        onClick: function onClick() {
                                            return _this2.onUpdateMeta({ advgb_blocks_editor_width: setting.value });
                                        }
                                    },
                                    setting.label
                                );
                            })
                        )
                    ),
                    advgbBlocks.enable_advgb_blocks !== undefined && advgbBlocks.enable_advgb_blocks === '1' && React.createElement(
                        PanelBody,
                        { title: __('Columns Visual Guide', 'advanced-gutenberg'), initialOpen: false },
                        React.createElement(
                            'div',
                            { className: 'advgb-editor-sidebar-note' },
                            __('Border to materialize PublishPress Blocks Column block', 'advanced-gutenberg')
                        ),
                        React.createElement(
                            ButtonGroup,
                            { className: 'advgb-button-group' },
                            VISUAL_GUIDE_SETTINGS.map(function (setting, index) {
                                return React.createElement(
                                    Button,
                                    { className: 'advgb-button',
                                        key: index,
                                        isSecondary: setting.value !== columnsVisualGuide,
                                        isPrimary: setting.value === columnsVisualGuide,
                                        onClick: function onClick() {
                                            return _this2.onUpdateMeta({ advgb_blocks_columns_visual_guide: setting.value });
                                        }
                                    },
                                    setting.label
                                );
                            })
                        )
                    )
                );
            }
        }]);

        return AdvSidebar;
    }(Component);

    var AdvSidebarRender = compose(withDispatch(function (dispatch) {
        return {
            updateMetaField: function updateMetaField(data) {
                dispatch('core/editor').editPost({ meta: data });
            }
        };
    }), withSelect(function (select) {
        var metaValues = select('core/editor').getEditedPostAttribute('meta');

        return {
            metaValues: metaValues,
            columnsVisualGuide: metaValues.advgb_blocks_columns_visual_guide,
            editorWidth: metaValues.advgb_blocks_editor_width
        };
    }))(AdvSidebar);

    registerPlugin('advgb-editor-sidebar', {
        render: function render() {
            return React.createElement(
                Fragment,
                null,
                React.createElement(
                    PluginSidebarMoreMenuItem,
                    {
                        target: sidebarName,
                        icon: sidebarIcon
                    },
                    sidebarTitle
                ),
                React.createElement(
                    PluginSidebar,
                    {
                        name: sidebarName,
                        title: sidebarTitle,
                        icon: sidebarIcon
                    },
                    React.createElement(
                        'div',
                        { className: 'advgb-editor-sidebar-content' },
                        React.createElement(AdvSidebarRender, null)
                    )
                )
            );
        }
    });
})(wp.i18n, wp.plugins, wp.element, wp.data, wp.components, wp.editPost);

/***/ }),

/***/ 0:
/*!*****************************************************************!*\
  !*** multi ./src/assets/blocks/editor-sidebar/post-sidebar.jsx ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! ./src/assets/blocks/editor-sidebar/post-sidebar.jsx */"./src/assets/blocks/editor-sidebar/post-sidebar.jsx");


/***/ })

/******/ });
//# sourceMappingURL=post-sidebar.js.map
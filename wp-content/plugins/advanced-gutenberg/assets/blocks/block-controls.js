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

/***/ "./src/assets/blocks/0-adv-components/datetime.jsx":
/*!*********************************************************!*\
  !*** ./src/assets/blocks/0-adv-components/datetime.jsx ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

var _slicedToArray = function () { function sliceIterator(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"]) _i["return"](); } finally { if (_d) throw _e; } } return _arr; } return function (arr, i) { if (Array.isArray(arr)) { return arr; } else if (Symbol.iterator in Object(arr)) { return sliceIterator(arr, i); } else { throw new TypeError("Invalid attempt to destructure non-iterable instance"); } }; }();

exports.AdvDateTimeControl = AdvDateTimeControl;
function AdvDateTimeControl(props) {
    var _wp$components = wp.components,
        Button = _wp$components.Button,
        DateTimePicker = _wp$components.DateTimePicker,
        Popover = _wp$components.Popover,
        Tooltip = _wp$components.Tooltip;
    var _wp$element = wp.element,
        Fragment = _wp$element.Fragment,
        useState = _wp$element.useState;
    var __ = wp.i18n.__;

    var _useState = useState(false),
        _useState2 = _slicedToArray(_useState, 2),
        popupState = _useState2[0],
        setPopupState = _useState2[1];

    var togglePopup = function togglePopup() {
        setPopupState(function (state) {
            return !state;
        });
    };

    var buttonLabel = props.buttonLabel,
        dateLabel = props.dateLabel,
        date = props.date,
        onChangeDate = props.onChangeDate,
        onDateClear = props.onDateClear,
        onInvalidDate = props.onInvalidDate;


    return React.createElement(
        Fragment,
        null,
        React.createElement(
            "div",
            { className: "advgb-advcalendar-control" },
            React.createElement(
                "label",
                null,
                dateLabel
            ),
            React.createElement(
                "div",
                null,
                React.createElement(
                    Button,
                    {
                        isLink: true,
                        icon: "calendar",
                        onClick: function onClick() {
                            return setPopupState(togglePopup);
                        }
                    },
                    React.createElement(
                        Tooltip,
                        { text: __('Change date', 'advanced-gutenberg') },
                        React.createElement(
                            "span",
                            null,
                            date ? moment(date).format("MMMM DD YYYY, h:mm a") : buttonLabel
                        )
                    )
                ),
                date && React.createElement(Button, {
                    icon: "no-alt",
                    className: "advgb-advcalendar-remove-icon",
                    onClick: function onClick() {
                        return onDateClear();
                    }
                })
            )
        ),
        popupState && React.createElement(
            Popover,
            {
                className: "advgb-advcalendar-popover",
                onClose: setPopupState.bind(null, false)
            },
            React.createElement(
                "label",
                { className: "advgb-advcalendar-popover-label" },
                dateLabel,
                React.createElement(Button, {
                    icon: "no-alt",
                    className: "advgb-advcalendar-remove-icon",
                    onClick: function onClick() {
                        return setPopupState(togglePopup);
                    }
                })
            ),
            React.createElement(
                "div",
                { className: "advgb-advcalendar-popover-timezone" },
                typeof advgbBlocks.timezone !== 'undefined' && advgbBlocks.timezone.length ? advgbBlocks.timezone.replace(/_/g, ' ') + " " + __('time', 'advanced-gutenberg') : __('WordPress settings timezone', 'advanced-gutenberg')
            ),
            React.createElement(DateTimePicker, {
                currentDate: date,
                onChange: onChangeDate,
                is12Hour: true,
                isInvalidDate: onInvalidDate
            })
        )
    );
}

/***/ }),

/***/ "./src/assets/blocks/block-controls/block-controls.jsx":
/*!*************************************************************!*\
  !*** ./src/assets/blocks/block-controls/block-controls.jsx ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

var _datetime = __webpack_require__(/*! ../0-adv-components/datetime.jsx */ "./src/assets/blocks/0-adv-components/datetime.jsx");

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

(function (wpI18n, wpHooks, wpBlocks, wpBlockEditor, wpComponents, wpCompose) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    var addFilter = wpHooks.addFilter;
    var __ = wpI18n.__;
    var hasBlockSupport = wpBlocks.hasBlockSupport;
    var _wpBlockEditor = wpBlockEditor,
        InspectorControls = _wpBlockEditor.InspectorControls,
        BlockControls = _wpBlockEditor.BlockControls;
    var DateTimePicker = wpComponents.DateTimePicker,
        ToggleControl = wpComponents.ToggleControl,
        PanelBody = wpComponents.PanelBody,
        Notice = wpComponents.Notice;
    var createHigherOrderComponent = wpCompose.createHigherOrderComponent;
    var Fragment = wp.element.Fragment;

    // do not show this feature if disabled.

    if (!parseInt(advgbBlocks.block_controls)) return;

    // Blocks that are not supported
    var NON_SUPPORTED_BLOCKS = ['core/freeform', 'core/legacy-widget', 'core/widget-area', 'core/column', 'advgb/tab', 'advgb/accordion-item', 'advgb/column'];

    // Register block controls to blocks attributes
    addFilter('blocks.registerBlockType', 'advgb/blockControls', function (settings) {
        if (!NON_SUPPORTED_BLOCKS.includes(settings.name)) {
            settings.attributes = _extends(settings.attributes, {
                advgbBlockControls: {
                    type: 'array',
                    items: {
                        type: 'object'
                    },
                    default: [{
                        control: 'schedule',
                        enabled: false,
                        dateFrom: null,
                        dateTo: null,
                        recurring: false
                    }]
                }
            });
        }

        return settings;
    });

    // Add option to add dates for supported blocks
    addFilter('editor.BlockEdit', 'advgb/addBlockControls', function (BlockEdit) {
        return function (props) {
            var advgbBlockControls = props.attributes.advgbBlockControls;

            /**
             * Return current advgbBlockControls array attribute value
             *
             * @since 2.14.0
             * @param {string} control  The use case block control. e.g. 'schedule'
             * @param {string} key      The control key to modify. e.g. 'enabled'
             *
             * @return {mixed}
             */

            var currentControlKey = function currentControlKey(control, key) {
                var itemIndex = advgbBlockControls.findIndex(function (element) {
                    return element.control === control;
                });
                var newArray = [].concat(_toConsumableArray(advgbBlockControls));
                var obj = newArray[itemIndex];

                return obj[key];
            };

            /**
             * Update advgbBlockControls attribute when a key value changes
             *
             * @since 2.14.0
             * @param {string} control  The use case block control. e.g. 'schedule'
             * @param {string} key      The control key to modify. e.g. 'enabled'
             * @param {string} key      The control key value (not required for boolean keys)
             *
             * @return {void}
             */
            var changeControlKey = function changeControlKey(control, key) {
                var value = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';

                var itemIndex = advgbBlockControls.findIndex(function (element) {
                    return element.control === control;
                });
                var newArray = [].concat(_toConsumableArray(advgbBlockControls));
                var obj = newArray[itemIndex];

                newArray[itemIndex] = typeof obj[key] === 'boolean' ? _extends({}, newArray[itemIndex], _defineProperty({}, key, !obj[key])) : _extends({}, newArray[itemIndex], _defineProperty({}, key, value));

                props.setAttributes({
                    advgbBlockControls: newArray
                });
            };

            return [props.isSelected && !NON_SUPPORTED_BLOCKS.includes(props.name) && React.createElement(
                InspectorControls,
                { key: 'advgb-bc-controls' },
                React.createElement(
                    PanelBody,
                    {
                        title: __('Block Controls', 'advanced-gutenberg'),
                        icon: 'visibility',
                        initialOpen: false,
                        className: currentControlKey('schedule', 'enabled') && (currentControlKey('schedule', 'dateFrom') || currentControlKey('schedule', 'dateTo')) ? 'advgb-feature-icon-active' : ''
                    },
                    React.createElement(
                        Fragment,
                        null,
                        React.createElement(ToggleControl, {
                            label: __('Enable block schedule', 'advanced-gutenberg'),
                            help: !currentControlKey('schedule', 'enabled') ? __('Setup when to start showing and/or stop showing this block', 'advanced-gutenberg') : '',
                            checked: currentControlKey('schedule', 'enabled'),
                            onChange: function onChange() {
                                return changeControlKey('schedule', 'enabled');
                            }
                        }),
                        currentControlKey('schedule', 'enabled') && React.createElement(
                            Fragment,
                            null,
                            React.createElement(_datetime.AdvDateTimeControl, {
                                buttonLabel: __('Now', 'advanced-gutenberg'),
                                dateLabel: __('Start showing', 'advanced-gutenberg'),
                                date: currentControlKey('schedule', 'dateFrom'),
                                onChangeDate: function onChangeDate(newDate) {
                                    return changeControlKey('schedule', 'dateFrom', newDate);
                                },
                                onDateClear: function onDateClear() {
                                    return changeControlKey('schedule', 'dateFrom', null);
                                },
                                onInvalidDate: false
                            }),
                            React.createElement(_datetime.AdvDateTimeControl, {
                                buttonLabel: __('Never', 'advanced-gutenberg'),
                                dateLabel: __('Stop showing', 'advanced-gutenberg'),
                                date: !!currentControlKey('schedule', 'dateTo') ? currentControlKey('schedule', 'dateTo') : null,
                                onChangeDate: function onChangeDate(newDate) {
                                    return changeControlKey('schedule', 'dateTo', newDate);
                                },
                                onDateClear: function onDateClear() {
                                    return changeControlKey('schedule', 'dateTo', null);
                                },
                                onInvalidDate: function onInvalidDate(date) {
                                    // Disable all dates before dateFrom
                                    if (currentControlKey('schedule', 'dateFrom')) {
                                        var thisDate = new Date(date.getTime());
                                        thisDate.setHours(0, 0, 0, 0);
                                        var fromDate = new Date(currentControlKey('schedule', 'dateFrom'));
                                        fromDate.setHours(0, 0, 0, 0);
                                        return thisDate.getTime() < fromDate.getTime();
                                    }
                                }
                            }),
                            currentControlKey('schedule', 'dateFrom') > currentControlKey('schedule', 'dateTo') && React.createElement(
                                Notice,
                                {
                                    className: 'advgb-notice-sidebar',
                                    status: 'warning',
                                    isDismissible: false
                                },
                                __('"Stop showing" date should be after "Start showing" date!', 'advanced-gutenberg')
                            ),
                            currentControlKey('schedule', 'dateFrom') && currentControlKey('schedule', 'dateTo') && React.createElement(ToggleControl, {
                                label: __('Recurring', 'advanced-gutenberg'),
                                checked: currentControlKey('schedule', 'recurring'),
                                onChange: function onChange() {
                                    return changeControlKey('schedule', 'recurring');
                                },
                                help: __('If Recurring is enabled, this block will be displayed every year between the selected dates.', 'advanced-gutenberg')
                            })
                        )
                    )
                )
            ), React.createElement(BlockEdit, _extends({ key: 'block-edit-advgb-dates' }, props))];
        };
    });

    var withAttributes = createHigherOrderComponent(function (BlockListBlock) {
        return function (props) {
            if (!NON_SUPPORTED_BLOCKS.includes(props.name) && hasBlockSupport(props.name, 'advgb/blockControls', true)) {
                var advgbBlockControls = props.attributes.advgbBlockControls;
                // @TODO - Avoid having currentControlKey() duplicated. See 'blocks.registerBlockType' hook

                var currentControlKey = function currentControlKey(control, key) {
                    var itemIndex = advgbBlockControls.findIndex(function (element) {
                        return element.control === control;
                    });
                    var newArray = [].concat(_toConsumableArray(advgbBlockControls));
                    var obj = newArray[itemIndex];
                    return obj[key];
                };
                var advgbBcClass = props.isSelected === false && currentControlKey('schedule', 'enabled') && (currentControlKey('schedule', 'dateFrom') || currentControlKey('schedule', 'dateTo')) ? 'advgb-bc-editor-preview' : '';

                return React.createElement(BlockListBlock, _extends({}, props, { className: advgbBcClass, advgbBlockControls: '' + advgbBlockControls }));
            }

            return React.createElement(BlockListBlock, props);
        };
    }, 'withAttributes');

    // Apply custom styles on back-end
    wp.hooks.addFilter('editor.BlockListBlock', 'advgb/loadBackendBlockControls', withAttributes);
})(wp.i18n, wp.hooks, wp.blocks, wp.blockEditor, wp.components, wp.compose);

/***/ }),

/***/ 0:
/*!*******************************************************************!*\
  !*** multi ./src/assets/blocks/block-controls/block-controls.jsx ***!
  \*******************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! ./src/assets/blocks/block-controls/block-controls.jsx */"./src/assets/blocks/block-controls/block-controls.jsx");


/***/ })

/******/ });
//# sourceMappingURL=block-controls.js.map
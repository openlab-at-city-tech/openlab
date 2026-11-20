/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/blocks/0-adv-components/datetime.jsx"
/*!*****************************************************!*\
  !*** ./assets/blocks/0-adv-components/datetime.jsx ***!
  \*****************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AdvDateTimeControl: () => (/* binding */ AdvDateTimeControl),
/* harmony export */   AdvDaysControl: () => (/* binding */ AdvDaysControl),
/* harmony export */   AdvTimeControl: () => (/* binding */ AdvTimeControl),
/* harmony export */   AdvTimezoneControl: () => (/* binding */ AdvTimezoneControl),
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var hour_convert__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! hour-convert */ "./node_modules/hour-convert/index.js");
/* harmony import */ var hour_convert__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(hour_convert__WEBPACK_IMPORTED_MODULE_0__);
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _callSuper(t, o, e) { return o = _getPrototypeOf(o), _possibleConstructorReturn(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], _getPrototypeOf(t).constructor) : o.apply(t, e)); }
function _possibleConstructorReturn(t, e) { if (e && ("object" == _typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return _assertThisInitialized(t); }
function _assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _getPrototypeOf(t) { return _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, _getPrototypeOf(t); }
function _inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && _setPrototypeOf(t, e); }
function _setPrototypeOf(t, e) { return _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, _setPrototypeOf(t, e); }
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t.return && (u = t.return(), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }

var _wp$components = wp.components,
  ButtonGroup = _wp$components.ButtonGroup,
  Button = _wp$components.Button,
  DateTimePicker = _wp$components.DateTimePicker,
  TextControl = _wp$components.TextControl,
  CheckboxControl = _wp$components.CheckboxControl,
  Popover = _wp$components.Popover,
  Tooltip = _wp$components.Tooltip,
  SelectControl = _wp$components.SelectControl,
  Icon = _wp$components.Icon;
var _wp$element = wp.element,
  Component = _wp$element.Component,
  Fragment = _wp$element.Fragment,
  useState = _wp$element.useState;
var _wp$i18n = wp.i18n,
  __ = _wp$i18n.__,
  _x = _wp$i18n._x;
var applyFilters = wp.hooks.applyFilters;

// Unique ids to associate visible labels with their controls
var advgbDateTimeIdCounter = 0;
var advgbDateTimeId = function advgbDateTimeId(prefix) {
  advgbDateTimeIdCounter++;
  return "".concat(prefix, "-").concat(advgbDateTimeIdCounter);
};
function AdvDateTimeControl(props) {
  var _useState = useState(false),
    _useState2 = _slicedToArray(_useState, 2),
    popupState = _useState2[0],
    setPopupState = _useState2[1];
  var _useState3 = useState(function () {
      return advgbDateTimeId('advgb-advcalendar-label');
    }),
    _useState4 = _slicedToArray(_useState3, 1),
    labelId = _useState4[0];
  var togglePopup = function togglePopup() {
    setPopupState(!popupState);
  };
  var buttonLabel = props.buttonLabel,
    dateLabel = props.dateLabel,
    date = props.date,
    onChangeDate = props.onChangeDate,
    onDateClear = props.onDateClear,
    onInvalidDate = props.onInvalidDate,
    placement = props.placement,
    offset = props.offset;
  return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement("div", {
    className: "advgb-advcalendar-control",
    role: "group",
    "aria-labelledby": labelId
  }, /*#__PURE__*/React.createElement("label", {
    id: labelId
  }, dateLabel), /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement(Button, {
    isLink: true,
    icon: "calendar",
    onClick: togglePopup,
    "aria-expanded": popupState
  }, /*#__PURE__*/React.createElement(Tooltip, {
    text: __('Change date', 'advanced-gutenberg')
  }, /*#__PURE__*/React.createElement("span", null, date ? moment(date).format("MMMM DD YYYY, h:mm a") : buttonLabel))), date && /*#__PURE__*/React.createElement(Button, {
    icon: "no-alt",
    className: "advgb-advcalendar-remove-icon",
    label: __('Clear date', 'advanced-gutenberg'),
    onClick: function onClick() {
      return onDateClear();
    }
  }))), popupState && /*#__PURE__*/React.createElement(Popover, {
    className: "advgb-advcalendar-popover",
    onClose: setPopupState.bind(null, false),
    resize: false,
    placement: placement,
    offset: offset
  }, /*#__PURE__*/React.createElement("div", {
    className: "advgb-advcalendar-popover-label"
  }, dateLabel, /*#__PURE__*/React.createElement(Button, {
    icon: "no-alt",
    className: "advgb-advcalendar-remove-icon",
    label: __('Close', 'advanced-gutenberg'),
    onClick: togglePopup
  })), /*#__PURE__*/React.createElement(DateTimePicker, {
    currentDate: date,
    onChange: onChangeDate,
    is12Hour: true,
    isInvalidDate: onInvalidDate
  })));
}
AdvDateTimeControl.defaultProps = {
  placement: 'left',
  offset: 40
};
function AdvDaysControl(props) {
  var allDays = [{
    slug: 0,
    label: _x('S', 'Sunday first letter', 'advanced-gutenberg'),
    name: __('Sunday', 'advanced-gutenberg')
  }, {
    slug: 1,
    label: __('M', 'advanced-gutenberg'),
    name: __('Monday', 'advanced-gutenberg')
  }, {
    slug: 2,
    label: _x('T', 'Tuesday first letter', 'advanced-gutenberg'),
    name: __('Tuesday', 'advanced-gutenberg')
  }, {
    slug: 3,
    label: __('W', 'advanced-gutenberg'),
    name: __('Wednesday', 'advanced-gutenberg')
  }, {
    slug: 4,
    label: _x('T', 'Thursday first letter', 'advanced-gutenberg'),
    name: __('Thursday', 'advanced-gutenberg')
  }, {
    slug: 5,
    label: __('F', 'advanced-gutenberg'),
    name: __('Friday', 'advanced-gutenberg')
  }, {
    slug: 6,
    label: _x('S', 'Saturday first letter', 'advanced-gutenberg'),
    name: __('Saturday', 'advanced-gutenberg')
  }];
  var label = props.label,
    _props$days = props.days,
    days = _props$days === void 0 ? [] : _props$days,
    onChangeDays = props.onChangeDays;

  // Use a single state variable to store the selected days
  var _useState5 = useState(Array.isArray(days) ? days : []),
    _useState6 = _slicedToArray(_useState5, 2),
    selectedDays = _useState6[0],
    setSelectedDays = _useState6[1];

  /**
  * Check if the day is selected or not
  *
  * @since 3.1.2
  *
  * @param {string} Day slug e.g. 'u' for Sunday, 't' for Tuesday
  *
  * @return {bool}
  */
  function isDaySelected(slug) {
    return selectedDays.some(function (el) {
      return el === slug;
    });
  }

  /**
  * Update the selected days state when a checkbox is changed
  *
  * @since 3.1.2
  *
  * @param {string} Day slug e.g. 'u' for Sunday, 't' for Tuesday
  *
  * @return {void}
  */
  function onChangeDay(slug) {
    // Check if the day is already selected
    var isSelected = isDaySelected(slug);

    // Create a new array with the updated selection
    var updatedDays;
    if (isSelected) {
      // Remove the day from the selected days
      updatedDays = selectedDays.filter(function (el) {
        return el !== slug;
      });
    } else {
      // Add the day to the selected days
      var findDay = allDays.find(function (el) {
        return el.slug === slug;
      });
      updatedDays = [].concat(_toConsumableArray(selectedDays), [findDay.slug]);
    }

    // Remove duplicates
    var uniqueDays = _toConsumableArray(new Set(updatedDays));

    // Update the selected days state
    setSelectedDays(uniqueDays);

    // Call the onChangeDays prop to notify the parent component of the change
    if (onChangeDays) {
      onChangeDays(updatedDays);
    }
  }
  return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement("fieldset", {
    className: "advgb-checkbox-wrapper",
    style: {
      border: 0,
      padding: 0,
      marginLeft: 0,
      marginRight: 0,
      marginTop: 0,
      minWidth: 0
    }
  }, /*#__PURE__*/React.createElement("legend", {
    style: {
      padding: 0,
      marginBottom: 8,
      display: 'block'
    }
  }, label), /*#__PURE__*/React.createElement("div", {
    className: "advgb-checkbox-inline"
  }, allDays.map(function (day) {
    return /*#__PURE__*/React.createElement(CheckboxControl, {
      key: day.slug,
      label: day.label,
      "aria-label": day.name,
      checked: isDaySelected(day.slug),
      onChange: function onChange() {
        return onChangeDay(day.slug);
      }
    });
  }))));
}
var AdvTimeClass = /*#__PURE__*/function (_Component) {
  function AdvTimeClass(props) {
    var _this;
    _classCallCheck(this, AdvTimeClass);
    _this = _callSuper(this, AdvTimeClass, [props]);
    _this.state = {
      onChangeTime: null,
      currentTime: _this.props.currentTime || null,
      hours: null,
      minutes: null,
      meridian: 'am',
      onInit: true
    };
    _this.labelId = advgbDateTimeId('advgb-advtime-label');
    return _this;
  }
  _inherits(AdvTimeClass, _Component);
  return _createClass(AdvTimeClass, [{
    key: "componentWillMount",
    value: function componentWillMount() {
      var _this$state = this.state,
        onChangeTime = _this$state.onChangeTime,
        currentTime = _this$state.currentTime,
        onInit = _this$state.onInit;
      if (this.props.onChangeTime !== onChangeTime) {
        this.setState({
          onChangeTime: this.props.onChangeTime
        });
      }
      if (this.props.currentTime !== currentTime) {
        this.setState({
          currentTime: this.props.currentTime
        });
      }

      // Init
      if (currentTime && currentTime.includes(':') && onInit) {
        this.setState({
          hours: currentTime.split(':')[0],
          minutes: currentTime.split(':')[1],
          meridian: parseInt(currentTime.split(':')[0]) > 11 ? 'pm' : 'am',
          // We set > 11 because PM starts from 12:00:00
          onInit: false
        });
      }
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate(prevProps, prevState) {
      var _this$props = this.props,
        onChangeTime = _this$props.onChangeTime,
        currentTime = _this$props.currentTime;
      var _this$state2 = this.state,
        hours = _this$state2.hours,
        minutes = _this$state2.minutes,
        meridian = _this$state2.meridian;
      var prevHours = prevState.hours,
        prevMinutes = prevState.minutes,
        prevMeridian = prevState.meridian;
      if (hours !== prevHours || minutes !== prevMinutes || meridian !== prevMeridian) {
        // When clearing time
        if (!hours && !minutes) {
          return;
        }

        // Default minutes when hours selected
        if (hours && !minutes) {
          this.setState({
            minutes: '00'
          });
        }

        // Default hours when minutes selected
        if (minutes && !hours) {
          this.setState({
            hours: '01'
          });
        }

        /* Convert 12-hours to 24-hours.
         * e.g. 12:00 AM becomes 00:00:00
         *      12:00 PM becomes 12:00:00
         *      01:00 PM becomes 13:00:00
         */
        var savedTime = "".concat(this.appendZero(hour_convert__WEBPACK_IMPORTED_MODULE_0___default().to24Hour({
          hour: parseInt(hours),
          meridiem: meridian
        })), ":").concat(minutes, ":00");

        // Value saved in source/attribute
        this.props.onChangeTime(savedTime);
      }
    }

    /**
     * Append zero to one digit numbers. e.g. 9 becomes '09'
     *
     * @since 3.1.2
     *
     * @param {string value Hours or minutes
     *
     * @return {string}
     */
  }, {
    key: "appendZero",
    value: function appendZero(value) {
      if (isNaN(value)) {
        return;
      }
      var res = parseInt(value);
      return res > 9 ? res : "0".concat(res);
    }
  }, {
    key: "render",
    value: function render() {
      var _this2 = this;
      var _this$state3 = this.state,
        onChangeTime = _this$state3.onChangeTime,
        currentTime = _this$state3.currentTime,
        hours = _this$state3.hours,
        minutes = _this$state3.minutes,
        meridian = _this$state3.meridian;

      // Make sure hours are valid. 01: min, 12: max
      var handleChangeHours = function handleChangeHours(event) {
        var value = Math.max(1, Math.min(12, Number(event.target.value)));
        _this2.setState({
          hours: _this2.appendZero(value)
        });
      };

      // Allow to use up/down keys for hours
      var handleKeyDownHours = function handleKeyDownHours(event) {
        var value = Number(event.target.value);
        if (event.key === 'ArrowUp' && value < 12) {
          _this2.setState({
            hours: _this2.appendZero(value + 1)
          });
        } else if (event.key === 'ArrowDown' && value > 1) {
          _this2.setState({
            hours: _this2.appendZero(value - 1)
          });
        }
      };

      // Make sure minutes are valid. 01: min, 59: max
      var handleChangeMinutes = function handleChangeMinutes(event) {
        var value = Math.max(0, Math.min(59, Number(event.target.value)));
        _this2.setState({
          minutes: _this2.appendZero(value)
        });
      };

      // Allow to use up/down keys for minutes
      var handleKeyDownMinutes = function handleKeyDownMinutes(event) {
        var value = Number(event.target.value);
        if (event.key === 'ArrowUp' && value < 59) {
          _this2.setState({
            minutes: _this2.appendZero(value + 1)
          });
        } else if (event.key === 'ArrowDown' && value > 0) {
          _this2.setState({
            minutes: _this2.appendZero(value - 1)
          });
        }
      };
      return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement("div", {
        className: "advgb-advtime-control",
        role: "group",
        "aria-labelledby": this.labelId
      }, /*#__PURE__*/React.createElement("label", {
        id: this.labelId
      }, this.props.label), /*#__PURE__*/React.createElement("div", {
        className: "advgb-advtime-hours-minutes"
      }, /*#__PURE__*/React.createElement("input", {
        type: "text",
        value: hours ? this.appendZero(hour_convert__WEBPACK_IMPORTED_MODULE_0___default().to12Hour(parseInt(hours)).hour) : '',
        onChange: handleChangeHours,
        onKeyDown: handleKeyDownHours,
        placeholder: "--",
        "aria-label": __('Hours', 'advanced-gutenberg')
      }), /*#__PURE__*/React.createElement("span", {
        "aria-hidden": "true"
      }, ":"), /*#__PURE__*/React.createElement("input", {
        type: "text",
        value: minutes ? minutes : '',
        onChange: handleChangeMinutes,
        onKeyDown: handleKeyDownMinutes,
        placeholder: "--",
        "aria-label": __('Minutes', 'advanced-gutenberg')
      })), /*#__PURE__*/React.createElement(ButtonGroup, {
        className: "advgb-advtime-meridian",
        role: "group",
        "aria-label": __('AM or PM', 'advanced-gutenberg')
      }, /*#__PURE__*/React.createElement(Button, {
        variant: meridian === null || meridian === 'am' ? 'primary' : 'secondary',
        "aria-pressed": meridian === null || meridian === 'am',
        onClick: function onClick() {
          _this2.setState({
            meridian: 'am'
          });
        },
        disabled: hours && minutes ? false : true
      }, __('AM', 'advanced-gutenberg')), /*#__PURE__*/React.createElement(Button, {
        variant: meridian === 'pm' ? 'primary' : 'secondary',
        "aria-pressed": meridian === 'pm',
        onClick: function onClick() {
          _this2.setState({
            meridian: 'pm'
          });
        },
        disabled: hours && minutes ? false : true
      }, __('PM', 'advanced-gutenberg'))), hours && minutes && /*#__PURE__*/React.createElement(Button, {
        className: "advgb-advtime-remove-icon",
        icon: "no-alt",
        label: __('Clear time', 'advanced-gutenberg'),
        onClick: function onClick() {
          _this2.props.onTimeClear();
          _this2.setState({
            hours: null,
            minutes: null,
            meridian: 'am'
          });
        }
      })));
    }
  }]);
}(Component);
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (AdvTimeClass);
function AdvTimeControl(props) {
  var label = props.label,
    currentTime = props.currentTime,
    onChangeTime = props.onChangeTime,
    onTimeClear = props.onTimeClear;
  return /*#__PURE__*/React.createElement(AdvTimeClass, {
    label: label,
    currentTime: currentTime,
    onChangeTime: onChangeTime,
    onTimeClear: onTimeClear
  });
}
function AdvTimezoneControl(props) {
  var label = props.label,
    defaultTimezone = props.defaultTimezone;
  return /*#__PURE__*/React.createElement(Fragment, null, applyFilters('advgb.timezoneControl', /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement("div", {
    className: "advgb-promo-overlay-area",
    style: {
      marginTop: 10,
      marginBottom: 10
    }
  }, /*#__PURE__*/React.createElement("div", {
    className: "advgb-blur"
  }, /*#__PURE__*/React.createElement("span", {
    "aria-hidden": "true"
  }, label), /*#__PURE__*/React.createElement(SelectControl, {
    label: label,
    hideLabelFromVision: true,
    value: defaultTimezone,
    options: [{
      label: defaultTimezone,
      value: defaultTimezone
    }],
    disabled: true
  })), /*#__PURE__*/React.createElement("div", {
    class: "advgb-pro-overlay-wrap"
  }, /*#__PURE__*/React.createElement("div", {
    class: "advgb-pro-small-overlay-text advgb-tooltips ppb-tooltips-library click",
    "data-toggle": "ppbtooltip",
    "data-placement": "top"
  }, /*#__PURE__*/React.createElement("span", {
    class: "advgb-promo-text"
  }, /*#__PURE__*/React.createElement("span", {
    class: "dashicons dashicons-lock",
    style: {
      color: 'initial'
    }
  }), " ", __('Pro feature', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
    class: "tooltip-text"
  }, /*#__PURE__*/React.createElement("p", null, __('PublishPress Blocks Pro supports Timezone configuration.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
    class: "clickable",
    href: "https://publishpress.com/links/blocks",
    target: "_blank"
  }, __('Upgrade to Pro', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null))))), /*#__PURE__*/React.createElement(React.Fragment, null)), props));
}

/***/ },

/***/ "./assets/blocks/0-adv-components/utils.jsx"
/*!**************************************************!*\
  !*** ./assets/blocks/0-adv-components/utils.jsx ***!
  \**************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getOptionSlugs: () => (/* binding */ getOptionSlugs),
/* harmony export */   getOptionSuggestions: () => (/* binding */ getOptionSuggestions),
/* harmony export */   getOptionTitles: () => (/* binding */ getOptionTitles)
/* harmony export */ });
/**
 * Generate option title suggestions
 *
 * @since 3.1.1
 * @param options Available options as objects with slug and title. e.g. [{slug: 'subscriber', title: 'Subscriber'}, {slug: 'new_customer', title: 'New Customer'}]
 *
 * @return {array}  Option slugs. e.g. ['subscriber','new_customer']
 */
var getOptionSuggestions = function getOptionSuggestions(options) {
  return options.map(function (item) {
    return item.title;
  });
};

/**
 * Match option slugs with its option titles
 * to display as field value (but NOT saved!).
 *
 * @since 3.1.1
 * @param slugs     Option slugs. e.g. ['subscriber','new_customer'] or [82, 92]
 * @param options   Available options as objects with slug and title. e.g. [{slug: 'subscriber', title: 'Subscriber'}, {slug: 'new_customer', title: 'New Customer'}]
 *
 * @return {array}  Option titles. e.g. ['Subscriber','New Customer']
 */
var getOptionTitles = function getOptionTitles(slugs, options) {
  var field_value = [];
  if (options !== null) {
    field_value = slugs.map(function (option_slug) {
      var find_option = options.find(function (item) {
        return item.slug === option_slug;
      });
      if (find_option === undefined || !find_option) {
        return option_slug; // It should return false but creates empty selections
      }
      return find_option.title;
    });
  }
  return field_value;
};

/**
 * Match option titles with its slugs, and save slugs
 *
 * @since 3.1.1
 * @param slugs     Option slugs. e.g. ['subscriber','new_customer']
 * @param options   Available options as objects with slug and title. e.g. [{slug: 'subscriber', title: 'Subscriber'}, {slug: 'new_customer', title: 'New Customer'}]
 *
 * @return {array}  Option slugs. e.g. ['subscriber','new_customer']
 */
var getOptionSlugs = function getOptionSlugs(slugs, options) {
  var slugs_array = [];
  slugs.map(function (option_title) {
    var matching_slug = options.find(function (item) {
      return item.title === option_title;
    });
    if (matching_slug !== undefined) {
      slugs_array.push(matching_slug.slug);
    }
  });
  return slugs_array;
};

/***/ },

/***/ "./node_modules/hour-convert/index.js"
/*!********************************************!*\
  !*** ./node_modules/hour-convert/index.js ***!
  \********************************************/
(module) {



module.exports = {

    /**
     * Convert 24-hour time to 12-hour format.
     *
     * @param {number} hour Hour to convert (0-23)
     * @return {object} { hour, meridiem } (meridian is also returned for backwards compatibility)
     */
    to12Hour: function to12Hour(hour) {
        var meridiem = hour < 12 ? 'am' : 'pm';
        return {
            hour: ((hour + 11) % 12 + 1),
            meridiem: meridiem,
            meridian: meridiem
        };
    },

    /**
     * Convert 12-hour time to 24-hour format.
     *
     * @param {object} time Time object
     * @param {number} time.hour Hour to convert (1-12)
     * @param {string} time.meridiem Hour meridiem (am/pm). 'time.meridian' is
     *  supported for backwards compatibility.
     * @return {number}
     */
    to24Hour: function to24Hour(time) {
        var meridiem = time.meridiem || time.meridian;
        return (meridiem === 'am' ? 0 : 12) + (time.hour % 12);
    }
};


/***/ }

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
/******/ 		// Check if module exists (development only)
/******/ 		if (__webpack_modules__[moduleId] === undefined) {
/******/ 			var e = new Error("Cannot find module '" + moduleId + "'");
/******/ 			e.code = 'MODULE_NOT_FOUND';
/******/ 			throw e;
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
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!********************************************************!*\
  !*** ./assets/pages/block-controls/preset-manager.jsx ***!
  \********************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _blocks_0_adv_components_datetime_jsx__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../blocks/0-adv-components/datetime.jsx */ "./assets/blocks/0-adv-components/datetime.jsx");
/* harmony import */ var _blocks_0_adv_components_utils_jsx__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../blocks/0-adv-components/utils.jsx */ "./assets/blocks/0-adv-components/utils.jsx");
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _regeneratorRuntime() { "use strict"; var r = _regenerator(), e = r.m(_regeneratorRuntime), t = (Object.getPrototypeOf ? Object.getPrototypeOf(e) : e.__proto__).constructor; function n(r) { var e = "function" == typeof r && r.constructor; return !!e && (e === t || "GeneratorFunction" === (e.displayName || e.name)); } var o = { throw: 1, return: 2, break: 3, continue: 3 }; function a(r) { var e, t; return function (n) { e || (e = { stop: function stop() { return t(n.a, 2); }, catch: function _catch() { return n.v; }, abrupt: function abrupt(r, e) { return t(n.a, o[r], e); }, delegateYield: function delegateYield(r, o, a) { return e.resultName = o, t(n.d, _regeneratorValues(r), a); }, finish: function finish(r) { return t(n.f, r); } }, t = function t(r, _t, o) { n.p = e.prev, n.n = e.next; try { return r(_t, o); } finally { e.next = n.n; } }), e.resultName && (e[e.resultName] = n.v, e.resultName = void 0), e.sent = n.v, e.next = n.n; try { return r.call(this, e); } finally { n.p = e.prev, n.n = e.next; } }; } return (_regeneratorRuntime = function _regeneratorRuntime() { return { wrap: function wrap(e, t, n, o) { return r.w(a(e), t, n, o && o.reverse()); }, isGeneratorFunction: n, mark: r.m, awrap: function awrap(r, e) { return new _OverloadYield(r, e); }, AsyncIterator: _regeneratorAsyncIterator, async: function async(r, e, t, o, u) { return (n(e) ? _regeneratorAsyncGen : _regeneratorAsync)(a(r), e, t, o, u); }, keys: _regeneratorKeys, values: _regeneratorValues }; })(); }
function _regeneratorValues(e) { if (null != e) { var t = e["function" == typeof Symbol && Symbol.iterator || "@@iterator"], r = 0; if (t) return t.call(e); if ("function" == typeof e.next) return e; if (!isNaN(e.length)) return { next: function next() { return e && r >= e.length && (e = void 0), { value: e && e[r++], done: !e }; } }; } throw new TypeError(_typeof(e) + " is not iterable"); }
function _regeneratorKeys(e) { var n = Object(e), r = []; for (var t in n) r.unshift(t); return function e() { for (; r.length;) if ((t = r.pop()) in n) return e.value = t, e.done = !1, e; return e.done = !0, e; }; }
function _regeneratorAsync(n, e, r, t, o) { var a = _regeneratorAsyncGen(n, e, r, t, o); return a.next().then(function (n) { return n.done ? n.value : a.next(); }); }
function _regeneratorAsyncGen(r, e, t, o, n) { return new _regeneratorAsyncIterator(_regenerator().w(r, e, t, o), n || Promise); }
function _regeneratorAsyncIterator(t, e) { function n(r, o, i, f) { try { var c = t[r](o), u = c.value; return u instanceof _OverloadYield ? e.resolve(u.v).then(function (t) { n("next", t, i, f); }, function (t) { n("throw", t, i, f); }) : e.resolve(u).then(function (t) { c.value = t, i(c); }, function (t) { return n("throw", t, i, f); }); } catch (t) { f(t); } } var r; this.next || (_regeneratorDefine2(_regeneratorAsyncIterator.prototype), _regeneratorDefine2(_regeneratorAsyncIterator.prototype, "function" == typeof Symbol && Symbol.asyncIterator || "@asyncIterator", function () { return this; })), _regeneratorDefine2(this, "_invoke", function (t, o, i) { function f() { return new e(function (e, r) { n(t, i, e, r); }); } return r = r ? r.then(f, f) : f(); }, !0); }
function _regenerator() { /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/babel/babel/blob/main/packages/babel-helpers/LICENSE */ var e, t, r = "function" == typeof Symbol ? Symbol : {}, n = r.iterator || "@@iterator", o = r.toStringTag || "@@toStringTag"; function i(r, n, o, i) { var c = n && n.prototype instanceof Generator ? n : Generator, u = Object.create(c.prototype); return _regeneratorDefine2(u, "_invoke", function (r, n, o) { var i, c, u, f = 0, p = o || [], y = !1, G = { p: 0, n: 0, v: e, a: d, f: d.bind(e, 4), d: function d(t, r) { return i = t, c = 0, u = e, G.n = r, a; } }; function d(r, n) { for (c = r, u = n, t = 0; !y && f && !o && t < p.length; t++) { var o, i = p[t], d = G.p, l = i[2]; r > 3 ? (o = l === n) && (u = i[(c = i[4]) ? 5 : (c = 3, 3)], i[4] = i[5] = e) : i[0] <= d && ((o = r < 2 && d < i[1]) ? (c = 0, G.v = n, G.n = i[1]) : d < l && (o = r < 3 || i[0] > n || n > l) && (i[4] = r, i[5] = n, G.n = l, c = 0)); } if (o || r > 1) return a; throw y = !0, n; } return function (o, p, l) { if (f > 1) throw TypeError("Generator is already running"); for (y && 1 === p && d(p, l), c = p, u = l; (t = c < 2 ? e : u) || !y;) { i || (c ? c < 3 ? (c > 1 && (G.n = -1), d(c, u)) : G.n = u : G.v = u); try { if (f = 2, i) { if (c || (o = "next"), t = i[o]) { if (!(t = t.call(i, u))) throw TypeError("iterator result is not an object"); if (!t.done) return t; u = t.value, c < 2 && (c = 0); } else 1 === c && (t = i.return) && t.call(i), c < 2 && (u = TypeError("The iterator does not provide a '" + o + "' method"), c = 1); i = e; } else if ((t = (y = G.n < 0) ? u : r.call(n, G)) !== a) break; } catch (t) { i = e, c = 1, u = t; } finally { f = 1; } } return { value: t, done: y }; }; }(r, o, i), !0), u; } var a = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} t = Object.getPrototypeOf; var c = [][n] ? t(t([][n]())) : (_regeneratorDefine2(t = {}, n, function () { return this; }), t), u = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(c); function f(e) { return Object.setPrototypeOf ? Object.setPrototypeOf(e, GeneratorFunctionPrototype) : (e.__proto__ = GeneratorFunctionPrototype, _regeneratorDefine2(e, o, "GeneratorFunction")), e.prototype = Object.create(u), e; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, _regeneratorDefine2(u, "constructor", GeneratorFunctionPrototype), _regeneratorDefine2(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = "GeneratorFunction", _regeneratorDefine2(GeneratorFunctionPrototype, o, "GeneratorFunction"), _regeneratorDefine2(u), _regeneratorDefine2(u, o, "Generator"), _regeneratorDefine2(u, n, function () { return this; }), _regeneratorDefine2(u, "toString", function () { return "[object Generator]"; }), (_regenerator = function _regenerator() { return { w: i, m: f }; })(); }
function _regeneratorDefine2(e, r, n, t) { var i = Object.defineProperty; try { i({}, "", {}); } catch (e) { i = 0; } _regeneratorDefine2 = function _regeneratorDefine(e, r, n, t) { function o(r, n) { _regeneratorDefine2(e, r, function (e) { return this._invoke(r, n, e); }); } r ? i ? i(e, r, { value: n, enumerable: !t, configurable: !t, writable: !t }) : e[r] = n : (o("next", 0), o("throw", 1), o("return", 2)); }, _regeneratorDefine2(e, r, n, t); }
function _OverloadYield(e, d) { this.v = e, this.k = d; }
function asyncGeneratorStep(n, t, e, r, o, a, c) { try { var i = n[a](c), u = i.value; } catch (n) { return void e(n); } i.done ? t(u) : Promise.resolve(u).then(r, o); }
function _asyncToGenerator(n) { return function () { var t = this, e = arguments; return new Promise(function (r, o) { var a = n.apply(t, e); function _next(n) { asyncGeneratorStep(a, r, o, _next, _throw, "next", n); } function _throw(n) { asyncGeneratorStep(a, r, o, _next, _throw, "throw", n); } _next(void 0); }); }; }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _callSuper(t, o, e) { return o = _getPrototypeOf(o), _possibleConstructorReturn(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], _getPrototypeOf(t).constructor) : o.apply(t, e)); }
function _possibleConstructorReturn(t, e) { if (e && ("object" == _typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return _assertThisInitialized(t); }
function _assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _getPrototypeOf(t) { return _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, _getPrototypeOf(t); }
function _inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && _setPrototypeOf(t, e); }
function _setPrototypeOf(t, e) { return _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, _setPrototypeOf(t, e); }


(function (wpI18n, wpComponents, wpElement, wpData) {
  var __ = wpI18n.__,
    sprintf = wpI18n.sprintf;
  var Card = wpComponents.Card,
    CardBody = wpComponents.CardBody,
    CardHeader = wpComponents.CardHeader,
    Button = wpComponents.Button,
    TextControl = wpComponents.TextControl,
    TextareaControl = wpComponents.TextareaControl,
    SelectControl = wpComponents.SelectControl,
    ToggleControl = wpComponents.ToggleControl,
    RangeControl = wpComponents.RangeControl,
    FormTokenField = wpComponents.FormTokenField,
    Modal = wpComponents.Modal,
    Notice = wpComponents.Notice,
    Spinner = wpComponents.Spinner,
    PanelBody = wpComponents.PanelBody,
    BaseControl = wpComponents.BaseControl,
    RadioControl = wpComponents.RadioControl,
    DateTimePicker = wpComponents.DateTimePicker;
  var Component = wpElement.Component,
    Fragment = wpElement.Fragment,
    createRef = wpElement.createRef;
  var useSelect = wpData.useSelect,
    useDispatch = wpData.useDispatch;
  var advgbConfirmIdCounter = 0;

  /**
   * Inline confirmation shown in the existing tooltip box.
   * Keyboard accessible: trigger exposes aria-expanded/aria-controls, focus moves into the
   * confirmation (Cancel), Esc or Cancel closes it and returns focus to the trigger.
   */
  var AdvgbConfirmInline = /*#__PURE__*/function (_Component) {
    function AdvgbConfirmInline(props) {
      var _this;
      _classCallCheck(this, AdvgbConfirmInline);
      _this = _callSuper(this, AdvgbConfirmInline, [props]);
      advgbConfirmIdCounter++;
      _this.id = "advgb-confirm-".concat(advgbConfirmIdCounter);
      _this.state = {
        open: false
      };
      _this.wrapperRef = createRef();
      _this.cancelRef = createRef();
      _this.open = _this.open.bind(_this);
      _this.close = _this.close.bind(_this);
      _this.onKeyDown = _this.onKeyDown.bind(_this);
      _this.onDocumentMouseDown = _this.onDocumentMouseDown.bind(_this);
      return _this;
    }
    _inherits(AdvgbConfirmInline, _Component);
    return _createClass(AdvgbConfirmInline, [{
      key: "componentWillUnmount",
      value: function componentWillUnmount() {
        document.removeEventListener('mousedown', this.onDocumentMouseDown);
      }
    }, {
      key: "getTrigger",
      value: function getTrigger() {
        return this.wrapperRef.current ? this.wrapperRef.current.querySelector("[aria-controls=\"".concat(this.id, "\"]")) : null;
      }
    }, {
      key: "open",
      value: function open() {
        var _this2 = this;
        this.setState({
          open: true
        }, function () {
          document.addEventListener('mousedown', _this2.onDocumentMouseDown);
          if (_this2.cancelRef.current) {
            _this2.cancelRef.current.focus();
          }
        });
      }
    }, {
      key: "close",
      value: function close() {
        var _this3 = this;
        var returnFocus = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
        document.removeEventListener('mousedown', this.onDocumentMouseDown);
        this.setState({
          open: false
        }, function () {
          var trigger = _this3.getTrigger();
          if (returnFocus && trigger) {
            trigger.focus();
          }
        });
      }
    }, {
      key: "onDocumentMouseDown",
      value: function onDocumentMouseDown(e) {
        if (this.wrapperRef.current && !this.wrapperRef.current.contains(e.target)) {
          this.close(false);
        }
      }
    }, {
      key: "onKeyDown",
      value: function onKeyDown(e) {
        if (this.state.open && (e.key === 'Escape' || e.key === 'Esc')) {
          // Do not let Esc close a parent Modal
          e.stopPropagation();
          e.preventDefault();
          this.close(true);
        }
      }
    }, {
      key: "render",
      value: function render() {
        var _this4 = this;
        var _this$props = this.props,
          _this$props$className = _this$props.className,
          className = _this$props$className === void 0 ? '' : _this$props$className,
          _this$props$placement = _this$props.placement,
          placement = _this$props$placement === void 0 ? 'top' : _this$props$placement,
          renderTrigger = _this$props.renderTrigger,
          question = _this$props.question,
          confirmText = _this$props.confirmText,
          confirmLabel = _this$props.confirmLabel,
          onConfirm = _this$props.onConfirm;
        var open = this.state.open;
        var questionId = "".concat(this.id, "-question");
        return /*#__PURE__*/React.createElement("div", {
          ref: this.wrapperRef,
          className: "ppb-tooltips-library advgb-confirm-inline ".concat(className, " ").concat(open ? 'is-active' : ''),
          "data-toggle": "ppbtooltip",
          "data-placement": placement,
          onKeyDown: this.onKeyDown
        }, renderTrigger({
          'aria-expanded': open,
          'aria-controls': this.id,
          onClick: function onClick(e) {
            e.preventDefault();
            open ? _this4.close(false) : _this4.open();
          }
        }), /*#__PURE__*/React.createElement("div", {
          className: "tooltip-text",
          id: this.id,
          role: "group",
          "aria-labelledby": questionId
        }, open && /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("span", {
          id: questionId
        }, question), /*#__PURE__*/React.createElement("br", null), /*#__PURE__*/React.createElement(Button, {
          isLink: true,
          isSmall: true,
          isDestructive: true,
          label: confirmLabel,
          onClick: function onClick() {
            _this4.close(false);
            onConfirm();
          }
        }, /*#__PURE__*/React.createElement("strong", null, confirmText)), /*#__PURE__*/React.createElement("span", {
          "aria-hidden": "true"
        }, " | "), /*#__PURE__*/React.createElement(Button, {
          isLink: true,
          isSmall: true,
          ref: this.cancelRef,
          onClick: function onClick() {
            return _this4.close(true);
          }
        }, __('No, Cancel.', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null)));
      }
    }]);
  }(Component);
  var ScheduleControl = function ScheduleControl(_ref) {
    var index = _ref.index,
      schedule = _ref.schedule,
      _onChange = _ref.onChange,
      onRemove = _ref.onRemove,
      getTimezoneLabel = _ref.getTimezoneLabel,
      getTimezoneSlug = _ref.getTimezoneSlug,
      canRemove = _ref.canRemove;
    return /*#__PURE__*/React.createElement("div", {
      style: {
        marginBottom: 5,
        padding: 5,
        border: '1px solid #ddd',
        borderRadius: 4
      }
    }, /*#__PURE__*/React.createElement("div", {
      style: {
        display: 'flex',
        justifyContent: 'space-between',
        marginBottom: 16
      }
    }, /*#__PURE__*/React.createElement("h4", null, sprintf(__('Schedule #%d', 'advanced-gutenberg'), index + 1)), canRemove && /*#__PURE__*/React.createElement(Button, {
      isDestructive: true,
      isLink: true,
      onClick: onRemove
    }, __('Remove', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement(_blocks_0_adv_components_datetime_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvDateTimeControl, {
      buttonLabel: __('Now', 'advanced-gutenberg'),
      dateLabel: __('Start showing', 'advanced-gutenberg'),
      date: schedule.dateFrom,
      onChangeDate: function onChangeDate(newDate) {
        return _onChange('dateFrom', newDate);
      },
      onDateClear: function onDateClear() {
        return _onChange('dateFrom', null);
      },
      onInvalidDate: false
    }), /*#__PURE__*/React.createElement(_blocks_0_adv_components_datetime_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvDateTimeControl, {
      buttonLabel: __('Never', 'advanced-gutenberg'),
      dateLabel: __('Stop showing', 'advanced-gutenberg'),
      date: schedule.dateTo || null,
      onChangeDate: function onChangeDate(newDate) {
        return _onChange('dateTo', newDate);
      },
      onDateClear: function onDateClear() {
        return _onChange('dateTo', null);
      },
      onInvalidDate: function onInvalidDate(date) {
        if (schedule.dateFrom) {
          var thisDate = new Date(date.getTime());
          thisDate.setHours(0, 0, 0, 0);
          var fromDate = new Date(schedule.dateFrom);
          fromDate.setHours(0, 0, 0, 0);
          return thisDate.getTime() < fromDate.getTime();
        }
        return false;
      }
    }), schedule.dateFrom && schedule.dateTo && /*#__PURE__*/React.createElement(ToggleControl, {
      label: __('Recurring', 'advanced-gutenberg'),
      checked: schedule.recurring || false,
      onChange: function onChange() {
        return _onChange('recurring', !schedule.recurring);
      },
      help: __('If Recurring is enabled, this block will be displayed every year between the selected dates.', 'advanced-gutenberg')
    }), /*#__PURE__*/React.createElement(_blocks_0_adv_components_datetime_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvDaysControl, {
      label: __('On these days (optional)', 'advanced-gutenberg'),
      days: schedule.days || [],
      onChangeDays: function onChangeDays(value) {
        return _onChange('days', value);
      }
    }), /*#__PURE__*/React.createElement("label", {
      style: {
        marginBottom: 8,
        display: 'block'
      }
    }, __('Between these times (optional)', 'advanced-gutenberg')), /*#__PURE__*/React.createElement(_blocks_0_adv_components_datetime_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvTimeControl, {
      label: __('From', 'advanced-gutenberg'),
      currentTime: schedule.timeFrom || null,
      onChangeTime: function onChangeTime(newTime) {
        return _onChange('timeFrom', newTime);
      },
      onTimeClear: function onTimeClear() {
        return _onChange('timeFrom', null);
      }
    }), /*#__PURE__*/React.createElement(_blocks_0_adv_components_datetime_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvTimeControl, {
      label: __('To', 'advanced-gutenberg'),
      currentTime: schedule.timeTo || null,
      onChangeTime: function onChangeTime(newTime) {
        return _onChange('timeTo', newTime);
      },
      onTimeClear: function onTimeClear() {
        return _onChange('timeTo', null);
      }
    }), schedule.timeFrom && schedule.timeTo && '01/01/2020T' + schedule.timeFrom >= '01/01/2020T' + schedule.timeTo && /*#__PURE__*/React.createElement(Notice, {
      className: "advgb-notice-sidebar",
      status: "warning",
      isDismissible: false
    }, __('"To" time should be after "From" time!', 'advanced-gutenberg')), /*#__PURE__*/React.createElement(_blocks_0_adv_components_datetime_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvTimezoneControl, {
      label: __('Timezone', 'advanced-gutenberg'),
      defaultTimezone: getTimezoneLabel(),
      value: schedule.timezone || getTimezoneSlug(),
      onChangeTimezone: function onChangeTimezone(value) {
        return _onChange('timezone', value);
      }
    }));
  };
  var PresetManager = /*#__PURE__*/function (_Component2) {
    function PresetManager(props) {
      var _this5;
      _classCallCheck(this, PresetManager);
      _this5 = _callSuper(this, PresetManager, [props]);
      _this5.state = {
        presets: [],
        currentPreset: _this5.getDefaultPreset(),
        editingPreset: null,
        showModal: false,
        modalMode: 'create',
        // 'create' or 'edit',
        loading: false,
        saving: false,
        error: null,
        deleting: false,
        deletingPresetId: null,
        lastAction: null // 'created', 'cancelled', 'deleted', 'saved', 'loaded'
      };
      _this5.messageContainerRef = React.createRef();
      _this5.messageHeadingRef = React.createRef();
      _this5.handleModalClose = _this5.handleModalClose.bind(_this5);
      _this5.createNewPreset = _this5.createNewPreset.bind(_this5);
      _this5.editPreset = _this5.editPreset.bind(_this5);
      _this5.savePreset = _this5.savePreset.bind(_this5);
      _this5.deletePreset = _this5.deletePreset.bind(_this5);
      _this5.addControlSet = _this5.addControlSet.bind(_this5);
      _this5.removeControlSet = _this5.removeControlSet.bind(_this5);
      _this5.addRuleToSet = _this5.addRuleToSet.bind(_this5);
      _this5.removeRuleFromSet = _this5.removeRuleFromSet.bind(_this5);
      _this5.updateRuleData = _this5.updateRuleData.bind(_this5);
      return _this5;
    }
    _inherits(PresetManager, _Component2);
    return _createClass(PresetManager, [{
      key: "componentDidMount",
      value: function componentDidMount() {
        this.setState({
          loading: true
        });
        this.initializePresets();
        this.setupDataSync();
      }
    }, {
      key: "componentWillUnmount",
      value: function componentWillUnmount() {
        if (this.dataUnsubscribe && window.AdvGBPresetData) {
          window.AdvGBPresetData.unsubscribe(this.dataUnsubscribe);
        }
      }
    }, {
      key: "setupDataSync",
      value: function setupDataSync() {
        var _this6 = this;
        // Sync with global data changes
        if (window.AdvGBPresetData && window.AdvGBPresetData.subscribe) {
          this.dataUnsubscribe = window.AdvGBPresetData.subscribe(function (presets) {
            _this6.setState({
              presets: Array.isArray(presets) ? presets : [],
              // Clear editing state if the current preset was deleted
              editingPreset: _this6.state.editingPreset && presets.find(function (p) {
                return p.id === _this6.state.editingPreset.id;
              }) ? _this6.state.editingPreset : null,
              currentPreset: _this6.state.currentPreset && presets.find(function (p) {
                return p.id === _this6.state.currentPreset.id;
              }) ? _this6.state.currentPreset : null
            });
          });
        }
      }
    }, {
      key: "createSamplePresets",
      value: function () {
        var _createSamplePresets = _asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
          var response;
          return _regeneratorRuntime().wrap(function _callee$(_context) {
            while (1) switch (_context.prev = _context.next) {
              case 0:
                this.setState({
                  loading: true,
                  error: null
                });
                _context.prev = 1;
                _context.next = 4;
                return wp.apiFetch({
                  path: '/advgb/v1/sample-presets',
                  method: 'POST'
                });
              case 4:
                response = _context.sent;
                if (response.success) {
                  if (window.AdvGBPresetData) {
                    window.AdvGBPresetData.updatePresets(response.presets, 'add');
                  }
                  this.setState({
                    presets: response.presets,
                    lastAction: 'installed',
                    loading: false
                  });
                }
                _context.next = 12;
                break;
              case 8:
                _context.prev = 8;
                _context.t0 = _context["catch"](1);
                console.error('Failed to create sample presets:', _context.t0);
                this.setState({
                  error: __('Failed to create sample presets', 'advanced-gutenberg'),
                  loading: false
                });
              case 12:
              case "end":
                return _context.stop();
            }
          }, _callee, this, [[1, 8]]);
        }));
        function createSamplePresets() {
          return _createSamplePresets.apply(this, arguments);
        }
        return createSamplePresets;
      }()
    }, {
      key: "initializePresets",
      value: function () {
        var _initializePresets = _asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee2() {
          var presets;
          return _regeneratorRuntime().wrap(function _callee2$(_context2) {
            while (1) switch (_context2.prev = _context2.next) {
              case 0:
                _context2.prev = 0;
                _context2.next = 3;
                return this.waitForGlobalData();
              case 3:
                _context2.next = 5;
                return this.loadPresetsFromSources();
              case 5:
                presets = _context2.sent;
                this.setState({
                  presets: presets,
                  loading: false,
                  currentPreset: this.getDefaultPreset()
                });
                _context2.next = 13;
                break;
              case 9:
                _context2.prev = 9;
                _context2.t0 = _context2["catch"](0);
                console.error('Failed to initialize presets:', _context2.t0);
                this.setState({
                  loading: false,
                  error: __('Failed to load presets', 'advanced-gutenberg')
                });
              case 13:
              case "end":
                return _context2.stop();
            }
          }, _callee2, this, [[0, 9]]);
        }));
        function initializePresets() {
          return _initializePresets.apply(this, arguments);
        }
        return initializePresets;
      }()
    }, {
      key: "waitForGlobalData",
      value: function waitForGlobalData() {
        return new Promise(function (resolve) {
          var checkData = function checkData() {
            if (window.AdvGBPresetData || window.advgb_block_controls_vars && window.advgb_block_controls_vars.presets) {
              resolve();
            } else {
              setTimeout(checkData, 100);
            }
          };
          checkData();
        });
      }
    }, {
      key: "loadPresetsFromSources",
      value: function () {
        var _loadPresetsFromSources = _asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee3() {
          var response, globalPresets, _globalPresets;
          return _regeneratorRuntime().wrap(function _callee3$(_context3) {
            while (1) switch (_context3.prev = _context3.next) {
              case 0:
                _context3.prev = 0;
                _context3.next = 3;
                return wp.apiFetch({
                  path: '/advgb/v1/presets'
                });
              case 3:
                response = _context3.sent;
                if (!Array.isArray(response)) {
                  _context3.next = 6;
                  break;
                }
                return _context3.abrupt("return", response);
              case 6:
                if (!(window.AdvGBPresetData && typeof window.AdvGBPresetData.getAllPresets === 'function')) {
                  _context3.next = 10;
                  break;
                }
                globalPresets = window.AdvGBPresetData.getAllPresets();
                if (!Array.isArray(globalPresets)) {
                  _context3.next = 10;
                  break;
                }
                return _context3.abrupt("return", globalPresets);
              case 10:
                return _context3.abrupt("return", []);
              case 13:
                _context3.prev = 13;
                _context3.t0 = _context3["catch"](0);
                console.warn('Failed to load presets:', _context3.t0);

                // Try global data as fallback
                if (!(window.AdvGBPresetData && typeof window.AdvGBPresetData.getAllPresets === 'function')) {
                  _context3.next = 20;
                  break;
                }
                _globalPresets = window.AdvGBPresetData.getAllPresets();
                if (!Array.isArray(_globalPresets)) {
                  _context3.next = 20;
                  break;
                }
                return _context3.abrupt("return", _globalPresets);
              case 20:
                return _context3.abrupt("return", []);
              case 21:
              case "end":
                return _context3.stop();
            }
          }, _callee3, null, [[0, 13]]);
        }));
        function loadPresetsFromSources() {
          return _loadPresetsFromSources.apply(this, arguments);
        }
        return loadPresetsFromSources;
      }()
    }, {
      key: "loadPresets",
      value: function loadPresets() {
        if (window.AdvGBPresetData) {
          this.setState({
            presets: window.AdvGBPresetData.getAllPresets(),
            loading: false
          });
        }
      }
    }, {
      key: "getDefaultPreset",
      value: function getDefaultPreset() {
        var defaultpreset = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
        return {
          id: null,
          default: defaultpreset,
          title: '',
          controlSets: [],
          created: null,
          modified: null
        };
      }
    }, {
      key: "savePreset",
      value: function () {
        var _savePreset = _asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee4(presetData) {
          var _this7 = this;
          var response, newPreset, updatedPresets;
          return _regeneratorRuntime().wrap(function _callee4$(_context4) {
            while (1) switch (_context4.prev = _context4.next) {
              case 0:
                this.setState({
                  saving: true,
                  error: null
                });
                _context4.prev = 1;
                _context4.next = 4;
                return wp.apiFetch({
                  path: '/advgb/v1/presets',
                  method: 'POST',
                  data: presetData
                });
              case 4:
                response = _context4.sent;
                if (!response.success) {
                  _context4.next = 11;
                  break;
                }
                _context4.next = 8;
                return this.loadPresets();
              case 8:
                newPreset = response.presets.find(function (p) {
                  return p.id === response.id;
                });
                if (newPreset) {
                  updatedPresets = response.presets || [];
                  if (window.AdvGBPresetData) {
                    window.AdvGBPresetData.updatePresets(updatedPresets);
                  }
                  this.setState({
                    editingPreset: null,
                    currentPreset: null,
                    saving: false,
                    showModal: false,
                    lastAction: 'saved'
                  }, function () {
                    // Scroll to top after state update
                    _this7.scrollToMessage();
                  });
                } else {
                  this.setState({
                    saving: false,
                    showModal: false,
                    lastAction: 'saved'
                  }, function () {
                    _this7.scrollToMessage();
                  });
                }
                return _context4.abrupt("return", response);
              case 11:
                _context4.next = 17;
                break;
              case 13:
                _context4.prev = 13;
                _context4.t0 = _context4["catch"](1);
                this.setState({
                  error: _context4.t0.message,
                  saving: false,
                  lastAction: 'error'
                }, function () {
                  _this7.scrollToMessage();
                });
                throw _context4.t0;
              case 17:
              case "end":
                return _context4.stop();
            }
          }, _callee4, this, [[1, 13]]);
        }));
        function savePreset(_x) {
          return _savePreset.apply(this, arguments);
        }
        return savePreset;
      }()
    }, {
      key: "deletePreset",
      value: function () {
        var _deletePreset = _asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee5(presetId) {
          var _this8 = this;
          var response;
          return _regeneratorRuntime().wrap(function _callee5$(_context5) {
            while (1) switch (_context5.prev = _context5.next) {
              case 0:
                this.setState({
                  deleting: true,
                  deletingPresetId: presetId
                });
                _context5.prev = 1;
                _context5.next = 4;
                return wp.apiFetch({
                  path: "/advgb/v1/presets/".concat(presetId),
                  method: 'DELETE'
                });
              case 4:
                response = _context5.sent;
                if (!response.success) {
                  _context5.next = 11;
                  break;
                }
                if (window.AdvGBPresetData) {
                  window.AdvGBPresetData.updatePresets(response.presets, 'delete');
                }
                if (this.state.editingPreset && this.state.editingPreset.id === presetId) {
                  this.setState({
                    editingPreset: null,
                    currentPreset: this.getDefaultPreset()
                  });
                }
                this.setState({
                  deleting: false,
                  deletingPresetId: null,
                  currentPreset: null,
                  editingPreset: null,
                  lastAction: 'deleted'
                }, function () {
                  _this8.scrollToMessage();
                });
                _context5.next = 11;
                return this.loadPresets();
              case 11:
                _context5.next = 16;
                break;
              case 13:
                _context5.prev = 13;
                _context5.t0 = _context5["catch"](1);
                this.setState({
                  deleting: false,
                  deletingPresetId: null,
                  error: _context5.t0.message
                }, function () {
                  _this8.scrollToMessage();
                });
              case 16:
              case "end":
                return _context5.stop();
            }
          }, _callee5, this, [[1, 13]]);
        }));
        function deletePreset(_x2) {
          return _deletePreset.apply(this, arguments);
        }
        return deletePreset;
      }()
    }, {
      key: "createNewPreset",
      value: function createNewPreset() {
        var _this9 = this;
        this.setState({
          currentPreset: this.getDefaultPreset(false),
          editingPreset: null,
          modalMode: 'create',
          showModal: true,
          lastAction: 'creating'
        }, function () {
          _this9.scrollToMessage();
        });
      }
    }, {
      key: "editPreset",
      value: function editPreset(preset) {
        var _this0 = this;
        this.setState({
          currentPreset: _objectSpread({}, preset),
          modalMode: 'edit',
          editingPreset: preset,
          showModal: true,
          lastAction: 'editing'
        }, function () {
          _this0.scrollToMessage();
        });
      }
    }, {
      key: "scrollToMessage",
      value: function scrollToMessage() {
        var _this1 = this;
        setTimeout(function () {
          if (_this1.messageContainerRef.current) {
            _this1.messageContainerRef.current.scrollIntoView({
              behavior: 'smooth',
              block: 'start'
            });
          }

          // Move focus to the result heading so keyboard/screen reader users land on the outcome
          if (['saved', 'deleted', 'cancelled', 'installed'].includes(_this1.state.lastAction) && _this1.messageHeadingRef.current) {
            _this1.messageHeadingRef.current.focus();
          }
        }, 100);
      }
    }, {
      key: "getStatusMessage",
      value: function getStatusMessage(lastAction) {
        switch (lastAction) {
          case 'saved':
            return __('Preset saved successfully!', 'advanced-gutenberg');
          case 'deleted':
            return __('Preset deleted.', 'advanced-gutenberg');
          case 'cancelled':
            return __('Edit cancelled.', 'advanced-gutenberg');
          case 'installed':
            return __('Preset samples created successfully!', 'advanced-gutenberg');
          default:
            return '';
        }
      }
    }, {
      key: "toggleControlSet",
      value: function toggleControlSet(setIndex) {
        var currentPreset = this.state.currentPreset;
        var newControlSets = _toConsumableArray(currentPreset.controlSets);
        var isCurrentlyExpanded = newControlSets[setIndex].expanded !== false;
        newControlSets[setIndex].expanded = !isCurrentlyExpanded;
        this.setState({
          currentPreset: _objectSpread(_objectSpread({}, currentPreset), {}, {
            controlSets: newControlSets
          })
        });
      }
    }, {
      key: "toggleRule",
      value: function toggleRule(setIndex, ruleIndex) {
        var currentPreset = this.state.currentPreset;
        var newControlSets = _toConsumableArray(currentPreset.controlSets);
        var isCurrentlyExpanded = newControlSets[setIndex].rules[ruleIndex].expanded !== false;
        newControlSets[setIndex].rules[ruleIndex].expanded = !isCurrentlyExpanded;
        this.setState({
          currentPreset: _objectSpread(_objectSpread({}, currentPreset), {}, {
            controlSets: newControlSets
          })
        });
      }
    }, {
      key: "isControlSetExpanded",
      value: function isControlSetExpanded(setIndex) {
        var currentPreset = this.state.currentPreset;
        if (!currentPreset.controlSets || !currentPreset.controlSets[setIndex]) {
          return true;
        }
        return currentPreset.controlSets[setIndex].expanded !== false;
      }
    }, {
      key: "isRuleExpanded",
      value: function isRuleExpanded(setIndex, ruleIndex) {
        var currentPreset = this.state.currentPreset;
        if (!currentPreset.controlSets || !currentPreset.controlSets[setIndex] || !currentPreset.controlSets[setIndex].rules || !currentPreset.controlSets[setIndex].rules[ruleIndex]) {
          return true;
        }
        return currentPreset.controlSets[setIndex].rules[ruleIndex].expanded !== false;
      }
    }, {
      key: "addControlSet",
      value: function addControlSet() {
        var newControlSet = {
          id: Date.now().toString(),
          rules: [],
          expanded: true
        };
        var updatedControlSets = [newControlSet].concat(_toConsumableArray(this.state.currentPreset.controlSets));
        this.setState({
          currentPreset: _objectSpread(_objectSpread({}, this.state.currentPreset), {}, {
            controlSets: updatedControlSets
          })
        });
      }
    }, {
      key: "removeControlSet",
      value: function removeControlSet(setIndex) {
        var currentPreset = this.state.currentPreset;
        var newControlSets = currentPreset.controlSets.filter(function (_, index) {
          return index !== setIndex;
        });
        this.setState({
          currentPreset: _objectSpread(_objectSpread({}, currentPreset), {}, {
            controlSets: newControlSets
          })
        });
      }
    }, {
      key: "addRuleToSet",
      value: function addRuleToSet(setIndex, ruleType) {
        var newRule = this.createRuleByType(ruleType);
        var updatedControlSets = _toConsumableArray(this.state.currentPreset.controlSets);
        updatedControlSets[setIndex].rules.unshift(newRule);
        this.setState({
          currentPreset: _objectSpread(_objectSpread({}, this.state.currentPreset), {}, {
            controlSets: updatedControlSets
          })
        });
      }
    }, {
      key: "createRuleByType",
      value: function createRuleByType(ruleType) {
        var baseRule = {
          id: Date.now(),
          type: ruleType,
          enabled: true,
          expanded: true
        };
        switch (ruleType) {
          case 'schedule':
            return _objectSpread(_objectSpread({}, baseRule), {}, {
              schedules: []
            });
          case 'user_role':
            return _objectSpread(_objectSpread({}, baseRule), {}, {
              roles: [],
              approach: 'include'
            });
          case 'device_type':
            return _objectSpread(_objectSpread({}, baseRule), {}, {
              devices: []
            });
          case 'device_width':
            return _objectSpread(_objectSpread({}, baseRule), {}, {
              min_width: '',
              max_width: ''
            });
          case 'browser_device':
            return _objectSpread(_objectSpread({}, baseRule), {}, {
              browsers: [],
              approach: 'include'
            });
          case 'operating_system':
            return _objectSpread(_objectSpread({}, baseRule), {}, {
              systems: [],
              approach: 'include'
            });
          case 'cookie':
            return _objectSpread(_objectSpread({}, baseRule), {}, {
              name: '',
              condition: '=',
              value: '',
              approach: 'include'
            });
          case 'user_meta':
            return _objectSpread(_objectSpread({}, baseRule), {}, {
              key: '',
              condition: '=',
              value: '',
              approach: 'include'
            });
          case 'post_meta':
            return _objectSpread(_objectSpread({}, baseRule), {}, {
              key: '',
              condition: '=',
              value: '',
              approach: 'include'
            });
          case 'query_string':
            return _objectSpread(_objectSpread({}, baseRule), {}, {
              queries: [],
              logic: 'all',
              approach: 'include'
            });
          case 'capabilities':
            return _objectSpread(_objectSpread({}, baseRule), {}, {
              capabilities: [],
              approach: 'include'
            });
          case 'archive':
            return _objectSpread(_objectSpread({}, baseRule), {}, {
              taxonomies: [],
              approach: 'include'
            });
          case 'page':
            return _objectSpread(_objectSpread({}, baseRule), {}, {
              pages: [],
              approach: 'include'
            });
          default:
            return baseRule;
        }
      }
    }, {
      key: "getAvailableRuleTypes",
      value: function getAvailableRuleTypes() {
        return [{
          value: 'schedule',
          label: __('Schedule', 'advanced-gutenberg')
        }, {
          value: 'user_role',
          label: __('User Roles', 'advanced-gutenberg')
        }, {
          value: 'device_type',
          label: __('Device Type', 'advanced-gutenberg')
        }, {
          value: 'device_width',
          label: __('Device Width', 'advanced-gutenberg')
        }, {
          value: 'browser_device',
          label: __('Browser', 'advanced-gutenberg')
        }, {
          value: 'operating_system',
          label: __('Operating System', 'advanced-gutenberg')
        }, {
          value: 'cookie',
          label: __('Cookie', 'advanced-gutenberg')
        }, {
          value: 'user_meta',
          label: __('User Meta', 'advanced-gutenberg')
        }, {
          value: 'post_meta',
          label: __('Post Meta', 'advanced-gutenberg')
        }, {
          value: 'query_string',
          label: __('Query String', 'advanced-gutenberg')
        }, {
          value: 'capabilities',
          label: __('Capabilities', 'advanced-gutenberg')
        }, {
          value: 'archive',
          label: __('Archive', 'advanced-gutenberg')
        }, {
          value: 'page',
          label: __('Page', 'advanced-gutenberg')
        }];
      }
    }, {
      key: "formatDate",
      value: function formatDate(dateString) {
        if (!dateString) return __('Never modified', 'advanced-gutenberg');
        try {
          var date = new Date(dateString);
          if (isNaN(date.getTime())) {
            return __('Invalid date', 'advanced-gutenberg');
          }

          // Format: Sep 12, 2025 2:30 PM
          return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
          });
        } catch (error) {
          console.error('Date formatting error:', error);
          return __('Invalid date', 'advanced-gutenberg');
        }
      }
    }, {
      key: "getRuleTypeLabel",
      value: function getRuleTypeLabel(ruleType) {
        var ruleTypes = this.getAvailableRuleTypes();
        var ruleTypeObj = ruleTypes.find(function (type) {
          return type.value === ruleType;
        });
        return ruleTypeObj ? ruleTypeObj.label : ruleType;
      }
    }, {
      key: "renderPresetList",
      value: function renderPresetList() {
        var _this10 = this;
        var _this$state = this.state,
          presets = _this$state.presets,
          editingPreset = _this$state.editingPreset;
        return /*#__PURE__*/React.createElement("div", {
          className: "advgb-presets-list"
        }, (!presets || presets.length === 0) && /*#__PURE__*/React.createElement("div", {
          className: "advgb-empty-presets-message"
        }, /*#__PURE__*/React.createElement("h3", null, __('No presets yet', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, __('You have not created any preset.', 'advanced-gutenberg'))), presets && presets.length > 0 && presets.map(function (preset) {
          var isActive = editingPreset && editingPreset.id === preset.id;
          var itemClasses = ['advgb-preset-item', isActive && 'active'].filter(Boolean).join(' ');
          return /*#__PURE__*/React.createElement("div", {
            key: preset.id,
            className: itemClasses,
            onClick: function onClick() {
              return _this10.editPreset(preset);
            }
          }, /*#__PURE__*/React.createElement("div", {
            className: "advgb-preset-content"
          }, /*#__PURE__*/React.createElement("span", {
            className: "advgb-preset-title",
            title: preset.title
          }, preset.title)), /*#__PURE__*/React.createElement("div", {
            className: "advgb-preset-list-actions"
          }, /*#__PURE__*/React.createElement(Button, {
            isSmall: true,
            icon: "edit",
            label: sprintf(/* translators: %s: preset title */
            __('Edit: %s', 'advanced-gutenberg'), preset.title),
            "aria-current": isActive ? 'true' : undefined,
            onClick: function onClick(e) {
              e.stopPropagation();
              _this10.editPreset(preset);
            }
          })));
        }));
      }
    }, {
      key: "renderPresetForm",
      value: function renderPresetForm() {
        var _this11 = this;
        var _this$state2 = this.state,
          currentPreset = _this$state2.currentPreset,
          saving = _this$state2.saving,
          deleting = _this$state2.deleting;
        if (!currentPreset) return null;
        return /*#__PURE__*/React.createElement("div", {
          className: "advgb-preset-form"
        }, /*#__PURE__*/React.createElement("div", {
          className: "advgb-preset-header"
        }, /*#__PURE__*/React.createElement("div", {
          className: "advgb-preset-title-section"
        }, /*#__PURE__*/React.createElement(TextControl, {
          label: __('Preset Title', 'advanced-gutenberg'),
          value: currentPreset.title || '',
          onChange: function onChange(title) {
            return _this11.setState({
              currentPreset: _objectSpread(_objectSpread({}, currentPreset), {}, {
                title: title
              })
            });
          },
          placeholder: __('Enter preset name', 'advanced-gutenberg'),
          className: "advgb-title-input"
        }))), /*#__PURE__*/React.createElement("div", {
          className: "advgb-control-sets-container"
        }, /*#__PURE__*/React.createElement("div", {
          className: "advgb-control-sets-header"
        }, /*#__PURE__*/React.createElement("h3", null, __('Control Sets', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", {
          className: "advgb-control-sets-description"
        }, __('Show the block if any control set applies. Each set can contain multiple rules.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("button", {
          type: "button",
          className: "button button-secondary",
          onClick: this.addControlSet
        }, __('Add Control Set', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("div", {
          className: "advgb-control-sets"
        }, (currentPreset.controlSets || []).map(function (controlSet, index) {
          return /*#__PURE__*/React.createElement(Fragment, {
            key: controlSet.id || index
          }, index > 0 && /*#__PURE__*/React.createElement("div", {
            className: "advgb-set-separator"
          }, /*#__PURE__*/React.createElement("span", {
            className: "advgb-separator-text"
          }, __('OR', 'advanced-gutenberg'))), _this11.renderControlSet(controlSet, index));
        }))), /*#__PURE__*/React.createElement("div", {
          className: "advgb-preset-actions"
        }, currentPreset.id && /*#__PURE__*/React.createElement(AdvgbConfirmInline, {
          className: "advgb-delete-preset-tooltip",
          placement: "top",
          question: __('Are you sure you want to delete preset?', 'advanced-gutenberg'),
          confirmText: __('Yes, Delete Preset.', 'advanced-gutenberg'),
          confirmLabel: __('Delete Preset', 'advanced-gutenberg'),
          onConfirm: function onConfirm() {
            return _this11.deletePreset(currentPreset.id);
          },
          renderTrigger: function renderTrigger(triggerProps) {
            return /*#__PURE__*/React.createElement("button", _extends({
              type: "button",
              className: "advgb-delete-preset-btn button button-secondary advgb-destructive-button",
              disabled: deleting || saving
            }, triggerProps), deleting ? /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement(Spinner, null), __('Deleting...', 'advanced-gutenberg')) : __('Delete Preset', 'advanced-gutenberg'));
          }
        }), /*#__PURE__*/React.createElement("button", {
          onClick: function onClick() {
            return _this11.savePreset(_this11.state.currentPreset);
          },
          disabled: saving || !currentPreset.title,
          className: "advgb-save-btn button button-primary"
        }, saving ? /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement(Spinner, null), __('Saving...', 'advanced-gutenberg')) : __('Save Preset', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("button", {
          className: "button button-secondary",
          onClick: this.handleModalClose,
          disabled: saving
        }, __('Cancel', 'advanced-gutenberg'))));
      }
    }, {
      key: "renderControlSet",
      value: function renderControlSet(controlSet, setIndex) {
        var _this12 = this;
        var isExpanded = this.isControlSetExpanded(setIndex);
        return /*#__PURE__*/React.createElement("div", {
          className: "advgb-control-set",
          key: controlSet.id || setIndex
        }, /*#__PURE__*/React.createElement("div", {
          className: "advgb-control-set-header"
        }, /*#__PURE__*/React.createElement("button", {
          type: "button",
          className: "advgb-set-info advgb-preset-clickable-area",
          onClick: function onClick() {
            return _this12.toggleControlSet(setIndex);
          },
          "aria-expanded": isExpanded,
          "aria-controls": "advgb-control-set-rules-".concat(setIndex)
        }, /*#__PURE__*/React.createElement("span", {
          className: "advgb-set-title-row"
        }, /*#__PURE__*/React.createElement("span", {
          className: "dashicons dashicons-arrow-".concat(isExpanded ? 'down' : 'right'),
          "aria-hidden": "true"
        }), /*#__PURE__*/React.createElement("span", {
          className: "title-row-text"
        }, /*#__PURE__*/React.createElement("span", {
          className: "advgb-set-title"
        }, __('Control Set', 'advanced-gutenberg'), " ", setIndex + 1), /*#__PURE__*/React.createElement("span", {
          className: "advgb-set-description"
        }, __('Show the block if any rule applies. Rules are evaluated with AND logic.', 'advanced-gutenberg'))))), /*#__PURE__*/React.createElement("div", {
          className: "advgb-set-actions"
        }, /*#__PURE__*/React.createElement(SelectControl, {
          label: sprintf(/* translators: %d: control set number */
          __('Add Rule to Control Set %d', 'advanced-gutenberg'), setIndex + 1),
          hideLabelFromVision: true,
          value: "",
          options: [{
            value: '',
            label: __('Add Rule', 'advanced-gutenberg')
          }].concat(_toConsumableArray(this.getAvailableRuleTypes())),
          onChange: function onChange(ruleType) {
            if (ruleType) {
              _this12.addRuleToSet(setIndex, ruleType);
            }
          },
          className: "advgb-add-rule-select"
        }), /*#__PURE__*/React.createElement(AdvgbConfirmInline, {
          placement: "left",
          question: __('Are you sure you want to delete set?', 'advanced-gutenberg'),
          confirmText: __('Yes, Delete Set.', 'advanced-gutenberg'),
          confirmLabel: __('Delete Set', 'advanced-gutenberg'),
          onConfirm: function onConfirm() {
            return _this12.removeControlSet(setIndex);
          },
          renderTrigger: function renderTrigger(triggerProps) {
            return /*#__PURE__*/React.createElement(Button, _extends({
              isLink: true,
              isSmall: true,
              isDestructive: true,
              icon: "trash",
              label: sprintf(/* translators: %d: control set number */
              __('Delete Control Set %d', 'advanced-gutenberg'), setIndex + 1)
            }, triggerProps));
          }
        }))), isExpanded && /*#__PURE__*/React.createElement("div", {
          className: "advgb-control-set-rules",
          id: "advgb-control-set-rules-".concat(setIndex)
        }, (controlSet.rules || []).map(function (rule, ruleIndex) {
          return /*#__PURE__*/React.createElement("div", {
            key: rule.id || ruleIndex,
            className: "advgb-rule-container"
          }, ruleIndex > 0 && /*#__PURE__*/React.createElement("div", {
            className: "advgb-rule-separator"
          }, /*#__PURE__*/React.createElement("span", {
            className: "advgb-separator-text"
          }, __('AND', 'advanced-gutenberg'))), _this12.renderControlRule(rule, setIndex, ruleIndex));
        }), (!controlSet.rules || controlSet.rules.length === 0) && /*#__PURE__*/React.createElement("div", {
          className: "advgb-no-rules"
        }, /*#__PURE__*/React.createElement("p", null, __('No rules added yet. Add rules to define when this block should be visible.', 'advanced-gutenberg')))));
      }
    }, {
      key: "renderControlRule",
      value: function renderControlRule(rule, setIndex, ruleIndex) {
        var _this13 = this;
        var isExpanded = this.isRuleExpanded(setIndex, ruleIndex);
        return /*#__PURE__*/React.createElement("div", {
          className: "advgb-control-rule"
        }, /*#__PURE__*/React.createElement("div", {
          className: "advgb-rule-header"
        }, /*#__PURE__*/React.createElement("button", {
          type: "button",
          className: "advgb-rule-type advgb-preset-clickable-area",
          onClick: function onClick() {
            return _this13.toggleRule(setIndex, ruleIndex);
          },
          "aria-expanded": isExpanded,
          "aria-controls": "advgb-rule-content-".concat(setIndex, "-").concat(ruleIndex)
        }, /*#__PURE__*/React.createElement("span", {
          className: "dashicons dashicons-arrow-".concat(isExpanded ? 'down' : 'right'),
          "aria-hidden": "true"
        }), /*#__PURE__*/React.createElement("span", {
          className: "advgb-rule-type-icon",
          "aria-hidden": "true"
        }, "\uD83D\uDCCB"), /*#__PURE__*/React.createElement("span", {
          className: "advgb-rule-type-label"
        }, this.getRuleTypeLabel(rule.type))), /*#__PURE__*/React.createElement("div", {
          className: "advgb-rule-actions"
        }, /*#__PURE__*/React.createElement(AdvgbConfirmInline, {
          placement: "left",
          question: __('Are you sure you want to remove rule?', 'advanced-gutenberg'),
          confirmText: __('Yes, Remove Rule.', 'advanced-gutenberg'),
          confirmLabel: __('Remove Rule', 'advanced-gutenberg'),
          onConfirm: function onConfirm() {
            return _this13.removeRuleFromSet(setIndex, ruleIndex);
          },
          renderTrigger: function renderTrigger(triggerProps) {
            return /*#__PURE__*/React.createElement(Button, _extends({
              isLink: true,
              isSmall: true,
              isDestructive: true,
              icon: "trash",
              label: sprintf(/* translators: %s: rule type */
              __('Remove Rule: %s', 'advanced-gutenberg'), _this13.getRuleTypeLabel(rule.type))
            }, triggerProps));
          }
        }))), isExpanded && /*#__PURE__*/React.createElement("div", {
          className: "advgb-rule-content",
          id: "advgb-rule-content-".concat(setIndex, "-").concat(ruleIndex)
        }, this.renderRuleConfiguration(rule, setIndex, ruleIndex)));
      }
    }, {
      key: "renderRuleConfiguration",
      value: function renderRuleConfiguration(rule, setIndex, ruleIndex) {
        switch (rule.type) {
          case 'schedule':
            return this.renderScheduleConfig(rule, setIndex, ruleIndex);
          case 'user_role':
            return this.renderUserRoleConfig(rule, setIndex, ruleIndex);
          case 'device_type':
            return this.renderDeviceTypeConfig(rule, setIndex, ruleIndex);
          case 'device_width':
            return this.renderDeviceWidthConfig(rule, setIndex, ruleIndex);
          case 'browser_device':
            return this.renderBrowserDeviceConfig(rule, setIndex, ruleIndex);
          case 'operating_system':
            return this.renderOperatingSystemConfig(rule, setIndex, ruleIndex);
          case 'cookie':
            return this.renderCookieConfig(rule, setIndex, ruleIndex);
          case 'user_meta':
            return this.renderUserMetaConfig(rule, setIndex, ruleIndex);
          case 'post_meta':
            return this.renderPostMetaConfig(rule, setIndex, ruleIndex);
          case 'query_string':
            return this.renderQueryStringConfig(rule, setIndex, ruleIndex);
          case 'capabilities':
            return this.renderCapabilitiesConfig(rule, setIndex, ruleIndex);
          case 'archive':
            return this.renderArchiveConfig(rule, setIndex, ruleIndex);
          case 'page':
            return this.renderPageConfig(rule, setIndex, ruleIndex);
          default:
            return /*#__PURE__*/React.createElement("p", null, __('Configuration for this rule type is missing.', 'advanced-gutenberg'));
        }
      }
    }, {
      key: "updateRuleData",
      value: function updateRuleData(setIndex, ruleIndex, key, value) {
        var currentPreset = this.state.currentPreset;
        var newControlSets = _toConsumableArray(currentPreset.controlSets);
        newControlSets[setIndex].rules[ruleIndex][key] = value;
        this.setState({
          currentPreset: _objectSpread(_objectSpread({}, currentPreset), {}, {
            controlSets: newControlSets
          })
        });
      }
    }, {
      key: "removeRuleFromSet",
      value: function removeRuleFromSet(setIndex, ruleIndex) {
        var currentPreset = this.state.currentPreset;
        var newControlSets = _toConsumableArray(currentPreset.controlSets);
        newControlSets[setIndex].rules.splice(ruleIndex, 1);
        this.setState({
          currentPreset: _objectSpread(_objectSpread({}, currentPreset), {}, {
            controlSets: newControlSets
          })
        });
      }
    }, {
      key: "handleModalClose",
      value: function handleModalClose() {
        var _this14 = this;
        this.setState({
          showModal: false,
          currentPreset: null,
          editingPreset: null,
          modalMode: 'create',
          lastAction: 'cancelled'
        }, function () {
          _this14.scrollToMessage();
        });
      }
    }, {
      key: "getBrowserOptions",
      value: function getBrowserOptions() {
        return [{
          slug: 'chrome',
          title: 'Chrome'
        }, {
          slug: 'firefox',
          title: 'Firefox'
        }, {
          slug: 'safari',
          title: 'Safari'
        }, {
          slug: 'edge',
          title: 'Edge'
        }, {
          slug: 'opera',
          title: 'Opera'
        }, {
          slug: 'internet explorer',
          title: 'Internet Explorer'
        }];
      }
    }, {
      key: "getOperatingSystemOptions",
      value: function getOperatingSystemOptions() {
        return [{
          slug: 'windows',
          title: 'Windows'
        }, {
          slug: 'mac',
          title: 'macOS'
        }, {
          slug: 'linux',
          title: 'Linux'
        }, {
          slug: 'android',
          title: 'Android'
        }, {
          slug: 'ios',
          title: 'iOS'
        }, {
          slug: 'chrome os',
          title: 'Chrome OS'
        }];
      }
    }, {
      key: "getConditionOptions",
      value: function getConditionOptions() {
        return [{
          label: '=',
          value: '='
        }, {
          label: '!=',
          value: '!='
        }, {
          label: '<',
          value: '<'
        }, {
          label: '>',
          value: '>'
        }, {
          label: '<=',
          value: '<='
        }, {
          label: '>=',
          value: '>='
        }, {
          label: 'contains',
          value: 'contains'
        }, {
          label: 'begins with',
          value: 'beginsWith'
        }, {
          label: 'ends with',
          value: 'endsWith'
        }, {
          label: 'does not contain',
          value: 'doesNotContain'
        }, {
          label: 'does not begin with',
          value: 'doesNotBeginWith'
        }, {
          label: 'does not end with',
          value: 'doesNotEndWith'
        }, {
          label: 'is null',
          value: 'null'
        }, {
          label: 'is not null',
          value: 'notNull'
        }];
      }
    }, {
      key: "getUserRoles",
      value: function getUserRoles() {
        return typeof advgb_block_controls_vars.user_roles !== 'undefined' ? advgb_block_controls_vars.user_roles : [];
      }
    }, {
      key: "getCapabilitiesOptions",
      value: function getCapabilitiesOptions() {
        var capabilities = typeof advgb_block_controls_vars.capabilities !== 'undefined' ? advgb_block_controls_vars.capabilities : [];
        return capabilities.map(function (cap) {
          return {
            slug: cap,
            title: cap.split('_').map(function (word) {
              return word.charAt(0).toUpperCase() + word.slice(1);
            }).join(' ')
          };
        });
      }
    }, {
      key: "getArchiveOptions",
      value: function getArchiveOptions() {
        return [{
          slug: 'category',
          title: __('Category Archives', 'advanced-gutenberg')
        }, {
          slug: 'tag',
          title: __('Tag Archives', 'advanced-gutenberg')
        }, {
          slug: 'date',
          title: __('Date Archives', 'advanced-gutenberg')
        }, {
          slug: 'author',
          title: __('Author Archives', 'advanced-gutenberg')
        }];
      }
    }, {
      key: "getPageOptions",
      value: function getPageOptions() {
        return typeof advgb_block_controls_vars.page !== 'undefined' ? advgb_block_controls_vars.page : [];
      }
    }, {
      key: "getTimezoneLabel",
      value: function getTimezoneLabel() {
        return __('WordPress settings timezone', 'advanced-gutenberg');
      }
    }, {
      key: "getTimezoneSlug",
      value: function getTimezoneSlug() {
        return typeof advgbBlocks.timezone !== 'undefined' && advgbBlocks.timezone.length ? advgbBlocks.timezone : 'UTC';
      }
    }, {
      key: "renderScheduleConfig",
      value: function renderScheduleConfig(rule, setIndex, ruleIndex) {
        var _this15 = this;
        var schedules = rule.schedules && rule.schedules.length > 0 ? rule.schedules : [{
          dateFrom: null,
          dateTo: null,
          recurring: false,
          days: [],
          timeFrom: null,
          timeTo: null,
          timezone: this.getTimezoneSlug()
        }];
        return /*#__PURE__*/React.createElement(Fragment, null, schedules.map(function (schedule, scheduleIndex) {
          return /*#__PURE__*/React.createElement(ScheduleControl, {
            key: scheduleIndex,
            index: scheduleIndex,
            schedule: schedule,
            onChange: function onChange(key, value) {
              var updatedSchedules = _toConsumableArray(schedules);
              updatedSchedules[scheduleIndex][key] = value;
              _this15.updateRuleData(setIndex, ruleIndex, 'schedules', updatedSchedules);
            },
            onRemove: function onRemove() {
              if (schedules.length > 1) {
                var updatedSchedules = schedules.filter(function (_, idx) {
                  return idx !== scheduleIndex;
                });
                _this15.updateRuleData(setIndex, ruleIndex, 'schedules', updatedSchedules);
              }
            },
            getTimezoneLabel: function getTimezoneLabel() {
              return _this15.getTimezoneLabel();
            },
            getTimezoneSlug: function getTimezoneSlug() {
              return _this15.getTimezoneSlug();
            },
            canRemove: schedules.length > 1
          });
        }), /*#__PURE__*/React.createElement("div", {
          style: {
            marginBottom: 16
          }
        }, /*#__PURE__*/React.createElement(Button, {
          style: {
            width: '100%',
            display: 'block'
          },
          className: "button button-secondary",
          onClick: function onClick() {
            var newSchedule = {
              dateFrom: null,
              dateTo: null,
              recurring: false,
              days: [],
              timeFrom: null,
              timeTo: null,
              timezone: _this15.getTimezoneSlug()
            };
            _this15.updateRuleData(setIndex, ruleIndex, 'schedules', [].concat(_toConsumableArray(schedules), [newSchedule]));
          }
        }, __('Add Another Schedule', 'advanced-gutenberg'))));
      }
    }, {
      key: "renderUserRoleConfig",
      value: function renderUserRoleConfig(rule, setIndex, ruleIndex) {
        var _this16 = this;
        return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(SelectControl, {
          label: __('Approach', 'advanced-gutenberg'),
          value: rule.approach || 'public',
          options: [{
            value: 'public',
            label: __('Show to everyone', 'advanced-gutenberg')
          }, {
            value: 'hidden',
            label: __('Hide from everyone', 'advanced-gutenberg')
          }, {
            value: 'login',
            label: __('Show to logged in users', 'advanced-gutenberg')
          }, {
            value: 'logout',
            label: __('Show to logged out users', 'advanced-gutenberg')
          }, {
            value: 'include',
            label: __('Show to selected user roles', 'advanced-gutenberg')
          }, {
            value: 'exclude',
            label: __('Hide from selected user roles', 'advanced-gutenberg')
          }],
          onChange: function onChange(value) {
            return _this16.updateRuleData(setIndex, ruleIndex, 'approach', value);
          }
        }), (rule.approach === 'include' || rule.approach === 'exclude') && /*#__PURE__*/React.createElement(FormTokenField, {
          multiple: true,
          label: __('Select user roles', 'advanced-gutenberg'),
          placeholder: __('Search roles', 'advanced-gutenberg'),
          suggestions: (0,_blocks_0_adv_components_utils_jsx__WEBPACK_IMPORTED_MODULE_1__.getOptionSuggestions)(this.getUserRoles()),
          maxSuggestions: 10,
          value: (0,_blocks_0_adv_components_utils_jsx__WEBPACK_IMPORTED_MODULE_1__.getOptionTitles)(rule.roles || [], this.getUserRoles()),
          onChange: function onChange(value) {
            _this16.updateRuleData(setIndex, ruleIndex, 'roles', (0,_blocks_0_adv_components_utils_jsx__WEBPACK_IMPORTED_MODULE_1__.getOptionSlugs)(value, _this16.getUserRoles()));
          },
          __experimentalExpandOnFocus: true
        }));
      }
    }, {
      key: "renderDeviceTypeConfig",
      value: function renderDeviceTypeConfig(rule, setIndex, ruleIndex) {
        var _this17 = this;
        var devices = rule.devices || [];
        return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement("div", {
          style: {
            paddingLeft: '17%'
          }
        }, ['desktop', 'tablet', 'mobile', 'robot'].map(function (deviceType) {
          return /*#__PURE__*/React.createElement(ToggleControl, {
            key: deviceType,
            label: __(deviceType.charAt(0).toUpperCase() + deviceType.slice(1), 'advanced-gutenberg'),
            checked: devices.includes(deviceType),
            onChange: function onChange() {
              var newDevices = devices.includes(deviceType) ? devices.filter(function (d) {
                return d !== deviceType;
              }) : [].concat(_toConsumableArray(devices), [deviceType]);
              _this17.updateRuleData(setIndex, ruleIndex, 'devices', newDevices);
            }
          });
        })));
      }
    }, {
      key: "renderDeviceWidthConfig",
      value: function renderDeviceWidthConfig(rule, setIndex, ruleIndex) {
        var _this18 = this;
        return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(TextControl, {
          type: "number",
          label: __('Minimum width (px)', 'advanced-gutenberg'),
          value: rule.min_width || '',
          onChange: function onChange(value) {
            return _this18.updateRuleData(setIndex, ruleIndex, 'min_width', value);
          },
          placeholder: __('No minimum', 'advanced-gutenberg')
        }), /*#__PURE__*/React.createElement(TextControl, {
          type: "number",
          label: __('Maximum width (px)', 'advanced-gutenberg'),
          value: rule.max_width || '',
          onChange: function onChange(value) {
            return _this18.updateRuleData(setIndex, ruleIndex, 'max_width', value);
          },
          placeholder: __('No maximum', 'advanced-gutenberg')
        }));
      }
    }, {
      key: "renderBrowserDeviceConfig",
      value: function renderBrowserDeviceConfig(rule, setIndex, ruleIndex) {
        var _this19 = this;
        return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(FormTokenField, {
          multiple: true,
          label: __('Select Browsers', 'advanced-gutenberg'),
          placeholder: __('Search browsers', 'advanced-gutenberg'),
          suggestions: (0,_blocks_0_adv_components_utils_jsx__WEBPACK_IMPORTED_MODULE_1__.getOptionSuggestions)(this.getBrowserOptions()),
          maxSuggestions: 10,
          value: (0,_blocks_0_adv_components_utils_jsx__WEBPACK_IMPORTED_MODULE_1__.getOptionTitles)(rule.browsers || [], this.getBrowserOptions()),
          onChange: function onChange(value) {
            _this19.updateRuleData(setIndex, ruleIndex, 'browsers', (0,_blocks_0_adv_components_utils_jsx__WEBPACK_IMPORTED_MODULE_1__.getOptionSlugs)(value, _this19.getBrowserOptions()));
          },
          __experimentalExpandOnFocus: true
        }), /*#__PURE__*/React.createElement(SelectControl, {
          label: __('Approach', 'advanced-gutenberg'),
          value: rule.approach || 'include',
          options: [{
            label: __('Show to selected browsers', 'advanced-gutenberg'),
            value: 'include'
          }, {
            label: __('Hide from selected browsers', 'advanced-gutenberg'),
            value: 'exclude'
          }],
          onChange: function onChange(value) {
            return _this19.updateRuleData(setIndex, ruleIndex, 'approach', value);
          }
        }));
      }
    }, {
      key: "renderOperatingSystemConfig",
      value: function renderOperatingSystemConfig(rule, setIndex, ruleIndex) {
        var _this20 = this;
        return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(FormTokenField, {
          multiple: true,
          label: __('Select Operating Systems', 'advanced-gutenberg'),
          placeholder: __('Search operating systems', 'advanced-gutenberg'),
          suggestions: (0,_blocks_0_adv_components_utils_jsx__WEBPACK_IMPORTED_MODULE_1__.getOptionSuggestions)(this.getOperatingSystemOptions()),
          maxSuggestions: 10,
          value: (0,_blocks_0_adv_components_utils_jsx__WEBPACK_IMPORTED_MODULE_1__.getOptionTitles)(rule.systems || [], this.getOperatingSystemOptions()),
          onChange: function onChange(value) {
            _this20.updateRuleData(setIndex, ruleIndex, 'systems', (0,_blocks_0_adv_components_utils_jsx__WEBPACK_IMPORTED_MODULE_1__.getOptionSlugs)(value, _this20.getOperatingSystemOptions()));
          },
          __experimentalExpandOnFocus: true
        }), /*#__PURE__*/React.createElement(SelectControl, {
          label: __('Approach', 'advanced-gutenberg'),
          value: rule.approach || 'include',
          options: [{
            label: __('Show to selected OS', 'advanced-gutenberg'),
            value: 'include'
          }, {
            label: __('Hide from selected OS', 'advanced-gutenberg'),
            value: 'exclude'
          }],
          onChange: function onChange(value) {
            return _this20.updateRuleData(setIndex, ruleIndex, 'approach', value);
          }
        }));
      }
    }, {
      key: "renderCookieConfig",
      value: function renderCookieConfig(rule, setIndex, ruleIndex) {
        var _this21 = this;
        return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(TextControl, {
          label: __('Cookie Name', 'advanced-gutenberg'),
          value: rule.name || '',
          onChange: function onChange(value) {
            return _this21.updateRuleData(setIndex, ruleIndex, 'name', value);
          }
        }), /*#__PURE__*/React.createElement(SelectControl, {
          label: __('Condition', 'advanced-gutenberg'),
          value: rule.condition || '=',
          options: this.getConditionOptions(),
          onChange: function onChange(value) {
            return _this21.updateRuleData(setIndex, ruleIndex, 'condition', value);
          }
        }), /*#__PURE__*/React.createElement(TextControl, {
          label: __('Value', 'advanced-gutenberg'),
          value: rule.value || '',
          onChange: function onChange(value) {
            return _this21.updateRuleData(setIndex, ruleIndex, 'value', value);
          }
        }), /*#__PURE__*/React.createElement(SelectControl, {
          label: __('Approach', 'advanced-gutenberg'),
          value: rule.approach || 'include',
          options: [{
            label: __('Show when condition matches', 'advanced-gutenberg'),
            value: 'include'
          }, {
            label: __('Hide when condition matches', 'advanced-gutenberg'),
            value: 'exclude'
          }],
          onChange: function onChange(value) {
            return _this21.updateRuleData(setIndex, ruleIndex, 'approach', value);
          }
        }));
      }
    }, {
      key: "renderUserMetaConfig",
      value: function renderUserMetaConfig(rule, setIndex, ruleIndex) {
        var _this22 = this;
        return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(TextControl, {
          label: __('Meta Key', 'advanced-gutenberg'),
          value: rule.key || '',
          onChange: function onChange(value) {
            return _this22.updateRuleData(setIndex, ruleIndex, 'key', value);
          }
        }), /*#__PURE__*/React.createElement(SelectControl, {
          label: __('Condition', 'advanced-gutenberg'),
          value: rule.condition || '=',
          options: this.getConditionOptions(),
          onChange: function onChange(value) {
            return _this22.updateRuleData(setIndex, ruleIndex, 'condition', value);
          }
        }), /*#__PURE__*/React.createElement(TextControl, {
          label: __('Value', 'advanced-gutenberg'),
          value: rule.value || '',
          onChange: function onChange(value) {
            return _this22.updateRuleData(setIndex, ruleIndex, 'value', value);
          }
        }), /*#__PURE__*/React.createElement(SelectControl, {
          label: __('Approach', 'advanced-gutenberg'),
          value: rule.approach || 'include',
          options: [{
            label: __('Show when condition matches', 'advanced-gutenberg'),
            value: 'include'
          }, {
            label: __('Hide when condition matches', 'advanced-gutenberg'),
            value: 'exclude'
          }],
          onChange: function onChange(value) {
            return _this22.updateRuleData(setIndex, ruleIndex, 'approach', value);
          }
        }));
      }

      // Post Meta Configuration
    }, {
      key: "renderPostMetaConfig",
      value: function renderPostMetaConfig(rule, setIndex, ruleIndex) {
        var _this23 = this;
        return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(TextControl, {
          label: __('Meta Key', 'advanced-gutenberg'),
          value: rule.key || '',
          onChange: function onChange(value) {
            return _this23.updateRuleData(setIndex, ruleIndex, 'key', value);
          }
        }), /*#__PURE__*/React.createElement(SelectControl, {
          label: __('Condition', 'advanced-gutenberg'),
          value: rule.condition || '=',
          options: this.getConditionOptions(),
          onChange: function onChange(value) {
            return _this23.updateRuleData(setIndex, ruleIndex, 'condition', value);
          }
        }), /*#__PURE__*/React.createElement(TextControl, {
          label: __('Value', 'advanced-gutenberg'),
          value: rule.value || '',
          onChange: function onChange(value) {
            return _this23.updateRuleData(setIndex, ruleIndex, 'value', value);
          }
        }), /*#__PURE__*/React.createElement(SelectControl, {
          label: __('Approach', 'advanced-gutenberg'),
          value: rule.approach || 'include',
          options: [{
            label: __('Show when condition matches', 'advanced-gutenberg'),
            value: 'include'
          }, {
            label: __('Hide when condition matches', 'advanced-gutenberg'),
            value: 'exclude'
          }],
          onChange: function onChange(value) {
            return _this23.updateRuleData(setIndex, ruleIndex, 'approach', value);
          }
        }));
      }
    }, {
      key: "renderQueryStringConfig",
      value: function renderQueryStringConfig(rule, setIndex, ruleIndex) {
        var _this24 = this;
        var queriesValue = Array.isArray(rule.queries) ? rule.queries.join('\n') : rule.queries || '';
        return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(TextareaControl, {
          label: __('Query Parameters', 'advanced-gutenberg'),
          help: __('Enter query parameter names, one per line', 'advanced-gutenberg'),
          value: queriesValue,
          onChange: function onChange(value) {
            _this24.updateRuleData(setIndex, ruleIndex, 'queries', value);
          },
          placeholder: __('utm_source\nutm_medium\nref', 'advanced-gutenberg')
        }), /*#__PURE__*/React.createElement(SelectControl, {
          label: __('Logic', 'advanced-gutenberg'),
          value: rule.logic || 'all',
          options: [{
            label: __('All parameters must be present', 'advanced-gutenberg'),
            value: 'all'
          }, {
            label: __('Any parameter must be present', 'advanced-gutenberg'),
            value: 'any'
          }],
          onChange: function onChange(value) {
            return _this24.updateRuleData(setIndex, ruleIndex, 'logic', value);
          }
        }), /*#__PURE__*/React.createElement(SelectControl, {
          label: __('Approach', 'advanced-gutenberg'),
          value: rule.approach || 'include',
          options: [{
            label: __('Show when condition matches', 'advanced-gutenberg'),
            value: 'include'
          }, {
            label: __('Hide when condition matches', 'advanced-gutenberg'),
            value: 'exclude'
          }],
          onChange: function onChange(value) {
            return _this24.updateRuleData(setIndex, ruleIndex, 'approach', value);
          }
        }));
      }
    }, {
      key: "renderCapabilitiesConfig",
      value: function renderCapabilitiesConfig(rule, setIndex, ruleIndex) {
        var _this25 = this;
        return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(FormTokenField, {
          multiple: true,
          label: __('Select Capabilities', 'advanced-gutenberg'),
          placeholder: __('Search capabilities', 'advanced-gutenberg'),
          suggestions: (0,_blocks_0_adv_components_utils_jsx__WEBPACK_IMPORTED_MODULE_1__.getOptionSuggestions)(this.getCapabilitiesOptions()),
          maxSuggestions: 10,
          value: (0,_blocks_0_adv_components_utils_jsx__WEBPACK_IMPORTED_MODULE_1__.getOptionTitles)(rule.capabilities || [], this.getCapabilitiesOptions()),
          onChange: function onChange(value) {
            _this25.updateRuleData(setIndex, ruleIndex, 'capabilities', (0,_blocks_0_adv_components_utils_jsx__WEBPACK_IMPORTED_MODULE_1__.getOptionSlugs)(value, _this25.getCapabilitiesOptions()));
          },
          __experimentalExpandOnFocus: true
        }), /*#__PURE__*/React.createElement(SelectControl, {
          label: __('Approach', 'advanced-gutenberg'),
          value: rule.approach || 'include',
          options: [{
            label: __('Show to users with selected capabilities', 'advanced-gutenberg'),
            value: 'include'
          }, {
            label: __('Hide from users with selected capabilities', 'advanced-gutenberg'),
            value: 'exclude'
          }],
          onChange: function onChange(value) {
            return _this25.updateRuleData(setIndex, ruleIndex, 'approach', value);
          }
        }));
      }
    }, {
      key: "renderArchiveConfig",
      value: function renderArchiveConfig(rule, setIndex, ruleIndex) {
        var _this26 = this;
        return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(FormTokenField, {
          multiple: true,
          label: __('Select Archive Types', 'advanced-gutenberg'),
          placeholder: __('Search archive types', 'advanced-gutenberg'),
          suggestions: (0,_blocks_0_adv_components_utils_jsx__WEBPACK_IMPORTED_MODULE_1__.getOptionSuggestions)(this.getArchiveOptions()),
          maxSuggestions: 10,
          value: (0,_blocks_0_adv_components_utils_jsx__WEBPACK_IMPORTED_MODULE_1__.getOptionTitles)(rule.archives || [], this.getArchiveOptions()),
          onChange: function onChange(value) {
            _this26.updateRuleData(setIndex, ruleIndex, 'archives', (0,_blocks_0_adv_components_utils_jsx__WEBPACK_IMPORTED_MODULE_1__.getOptionSlugs)(value, _this26.getArchiveOptions()));
          },
          __experimentalExpandOnFocus: true
        }), /*#__PURE__*/React.createElement(SelectControl, {
          label: __('Approach', 'advanced-gutenberg'),
          value: rule.approach || 'include',
          options: [{
            label: __('Show on selected archives', 'advanced-gutenberg'),
            value: 'include'
          }, {
            label: __('Hide from selected archives', 'advanced-gutenberg'),
            value: 'exclude'
          }],
          onChange: function onChange(value) {
            return _this26.updateRuleData(setIndex, ruleIndex, 'approach', value);
          }
        }));
      }
    }, {
      key: "renderPageConfig",
      value: function renderPageConfig(rule, setIndex, ruleIndex) {
        var _this27 = this;
        return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(FormTokenField, {
          multiple: true,
          label: __('Select Pages', 'advanced-gutenberg'),
          placeholder: __('Search pages', 'advanced-gutenberg'),
          suggestions: (0,_blocks_0_adv_components_utils_jsx__WEBPACK_IMPORTED_MODULE_1__.getOptionSuggestions)(this.getPageOptions()),
          maxSuggestions: 10,
          value: (0,_blocks_0_adv_components_utils_jsx__WEBPACK_IMPORTED_MODULE_1__.getOptionTitles)(rule.pages || [], this.getPageOptions()),
          onChange: function onChange(value) {
            _this27.updateRuleData(setIndex, ruleIndex, 'pages', (0,_blocks_0_adv_components_utils_jsx__WEBPACK_IMPORTED_MODULE_1__.getOptionSlugs)(value, _this27.getPageOptions()));
          },
          __experimentalExpandOnFocus: true
        }), /*#__PURE__*/React.createElement(SelectControl, {
          label: __('Approach', 'advanced-gutenberg'),
          value: rule.approach || 'include',
          options: [{
            label: __('Show on selected pages', 'advanced-gutenberg'),
            value: 'include'
          }, {
            label: __('Hide from selected pages', 'advanced-gutenberg'),
            value: 'exclude'
          }],
          onChange: function onChange(value) {
            return _this27.updateRuleData(setIndex, ruleIndex, 'approach', value);
          }
        }));
      }
    }, {
      key: "renderContextualMessage",
      value: function renderContextualMessage(lastAction) {
        var _this28 = this;
        var presets = this.state.presets;
        var title,
          description,
          icon,
          showFeatures = false,
          showStats = false;
        var hasPresets = presets && presets.length > 0;
        switch (lastAction) {
          case 'cancelled':
            title = __('Edit Cancelled', 'advanced-gutenberg');
            description = __('Edit cancelled. Select another preset to edit or create a new one.', 'advanced-gutenberg');
            icon = 'dismiss';
            break;
          case 'saved':
            title = __('Preset Saved!', 'advanced-gutenberg');
            description = __('Preset saved successfully! Choose another preset to edit or create a new one.', 'advanced-gutenberg');
            icon = 'yes-alt';
            break;
          case 'deleted':
            title = __('Preset Deleted', 'advanced-gutenberg');
            description = __('Preset deleted. Select another preset to edit or create a new one.', 'advanced-gutenberg');
            icon = 'trash';
            break;
          case 'error':
            title = __('Action Completed', 'advanced-gutenberg');
            description = __('Operation completed. You can continue editing presets or create new ones.', 'advanced-gutenberg');
            icon = 'info';
            break;
          case 'loaded':
            title = __('Select a Preset to Edit', 'advanced-gutenberg');
            description = __('Choose a preset from the list to edit its rules, or create a new preset to get started.', 'advanced-gutenberg');
            icon = 'admin-settings';
            break;
          case 'empty':
            title = __('No Presets Yet', 'advanced-gutenberg');
            description = __('Create your first preset to start managing block visibility rules.', 'advanced-gutenberg');
            icon = 'welcome-add-page';
            showFeatures = true;
            break;
          case 'installed':
            title = __('Samples Created!', 'advanced-gutenberg');
            description = __('Preset samples created successfully! Choose a preset to edit or create a new one.', 'advanced-gutenberg');
            icon = 'portfolio';
            showStats = true;
            break;
          default:
            title = __('Manage Your Presets', 'advanced-gutenberg');
            description = __('Select a preset to edit or create a new one to control block visibility.', 'advanced-gutenberg');
            icon = 'admin-generic';
            showStats = true;
        }
        return /*#__PURE__*/React.createElement(React.Fragment, null, icon && /*#__PURE__*/React.createElement("div", {
          className: "advgb-guidance-icon"
        }, /*#__PURE__*/React.createElement("span", {
          className: "dashicons dashicons-".concat(icon),
          "aria-hidden": "true"
        })), /*#__PURE__*/React.createElement("h3", {
          ref: this.messageHeadingRef,
          tabIndex: "-1"
        }, title), /*#__PURE__*/React.createElement("p", null, description), showStats && this.renderPresetStats(), showFeatures && this.renderFeatureGrid(), /*#__PURE__*/React.createElement("div", {
          className: "advgb-guidance-actions"
        }, /*#__PURE__*/React.createElement("button", {
          className: "button button-primary",
          onClick: function onClick() {
            return _this28.createNewPreset();
          }
        }, __('Create New Preset', 'advanced-gutenberg')), !hasPresets && /*#__PURE__*/React.createElement("button", {
          className: "button button-secondary",
          onClick: function onClick() {
            return _this28.createSamplePresets();
          }
        }, __('Generate Samples', 'advanced-gutenberg'))));
      }
    }, {
      key: "renderPresetStats",
      value: function renderPresetStats() {
        var presets = this.state.presets;
        var totalPresets = (presets === null || presets === void 0 ? void 0 : presets.length) || 0;
        var totalRules = (presets === null || presets === void 0 ? void 0 : presets.reduce(function (count, preset) {
          var _preset$controlSets;
          return count + (((_preset$controlSets = preset.controlSets) === null || _preset$controlSets === void 0 ? void 0 : _preset$controlSets.reduce(function (setCount, set) {
            var _set$rules;
            return setCount + (((_set$rules = set.rules) === null || _set$rules === void 0 ? void 0 : _set$rules.length) || 0);
          }, 0)) || 0);
        }, 0)) || 0;
        return /*#__PURE__*/React.createElement("div", {
          className: "advgb-preset-stats-overview"
        }, /*#__PURE__*/React.createElement("div", {
          className: "advgb-preset-stat-card"
        }, /*#__PURE__*/React.createElement("span", {
          className: "advgb-preset-stat-number"
        }, totalPresets), /*#__PURE__*/React.createElement("span", {
          className: "advgb-preset-stat-label"
        }, __('Total Presets', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("div", {
          className: "advgb-preset-stat-card"
        }, /*#__PURE__*/React.createElement("span", {
          className: "advgb-preset-stat-number"
        }, totalRules), /*#__PURE__*/React.createElement("span", {
          className: "advgb-preset-stat-label"
        }, __('Rules Created', 'advanced-gutenberg'))));
      }
    }, {
      key: "renderFeatureGrid",
      value: function renderFeatureGrid() {
        var controls = [{
          icon: 'clock',
          title: __('Schedule', 'advanced-gutenberg'),
          description: __('Show blocks during a specific date and time range.', 'advanced-gutenberg')
        }, {
          icon: 'admin-users',
          title: __('User roles', 'advanced-gutenberg'),
          description: __('Show blocks to selected user roles or logged-out visitors.', 'advanced-gutenberg')
        }, {
          icon: 'smartphone',
          title: __('Device type', 'advanced-gutenberg'),
          description: __('Target desktop, tablet, or mobile devices.', 'advanced-gutenberg')
        }, {
          icon: 'editor-expand',
          title: __('Device width', 'advanced-gutenberg'),
          description: __('Set minimum and maximum browser widths.', 'advanced-gutenberg')
        }, {
          icon: 'category',
          title: __('Term archives', 'advanced-gutenberg'),
          description: __('Target category, tag, and taxonomy archive pages.', 'advanced-gutenberg')
        }, {
          icon: 'admin-page',
          title: __('Pages', 'advanced-gutenberg'),
          description: __('Target individual pages and other singular content.', 'advanced-gutenberg')
        }, {
          icon: 'desktop',
          title: __('Browser & device', 'advanced-gutenberg'),
          description: __('Target specific browsers and device platforms.', 'advanced-gutenberg')
        }];
        return /*#__PURE__*/React.createElement("div", {
          className: "advgb-preset-features-grid"
        }, controls.map(function (control) {
          return /*#__PURE__*/React.createElement("div", {
            className: "advgb-preset-feature-card",
            key: control.icon
          }, /*#__PURE__*/React.createElement("div", {
            className: "advgb-preset-feature-icon"
          }, /*#__PURE__*/React.createElement("span", {
            className: "dashicons dashicons-".concat(control.icon)
          })), /*#__PURE__*/React.createElement("h4", null, control.title), /*#__PURE__*/React.createElement("p", null, control.description));
        }));
      }
    }, {
      key: "render",
      value: function render() {
        var _this29 = this;
        var _this$state3 = this.state,
          showModal = _this$state3.showModal,
          loading = _this$state3.loading,
          error = _this$state3.error,
          currentPreset = _this$state3.currentPreset,
          editingPreset = _this$state3.editingPreset,
          deleting = _this$state3.deleting,
          presets = _this$state3.presets,
          lastAction = _this$state3.lastAction;
        var _this$props$isModal = this.props.isModal,
          isModal = _this$props$isModal === void 0 ? false : _this$props$isModal;
        var hasActiveForm = currentPreset && !currentPreset.default || editingPreset;
        var hasPresets = presets && presets.length > 0;
        if (loading) {
          return /*#__PURE__*/React.createElement("div", {
            className: "advgb-preset-loading-container"
          }, /*#__PURE__*/React.createElement("div", {
            className: "advgb-preset-loading-spinner"
          }, /*#__PURE__*/React.createElement("div", {
            className: "advgb-spinner"
          }, /*#__PURE__*/React.createElement("div", {
            className: "advgb-spinner-circle"
          })), /*#__PURE__*/React.createElement("p", null, __('Loading presets...', 'advanced-gutenberg'))));
        }
        if (error) {
          return /*#__PURE__*/React.createElement(Notice, {
            status: "error"
          }, error);
        }
        var content = /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("div", {
          className: "screen-reader-text",
          role: "status",
          "aria-live": "polite"
        }, this.getStatusMessage(lastAction)), /*#__PURE__*/React.createElement("div", {
          className: "advgb-preset-sidebar-header",
          ref: this.messageContainerRef
        }, !hasActiveForm && /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("button", {
          className: "button button-secondary",
          onClick: function onClick() {
            return _this29.createNewPreset();
          }
        }, __('Add New Preset', 'advanced-gutenberg')), !hasPresets && /*#__PURE__*/React.createElement("button", {
          className: "button button-primary",
          onClick: function onClick() {
            return _this29.createSamplePresets();
          }
        }, __('Generate Sample Preset', 'advanced-gutenberg')))), /*#__PURE__*/React.createElement("div", {
          className: "advgb-preset-manager ".concat(deleting ? 'deleting' : '')
        }, deleting && /*#__PURE__*/React.createElement("div", {
          className: "advgb-preset-deleting-overlay"
        }, /*#__PURE__*/React.createElement("div", {
          className: "advgb-preset-deleting-message"
        }, /*#__PURE__*/React.createElement(Spinner, null))), (hasPresets || hasActiveForm) && /*#__PURE__*/React.createElement("div", {
          className: "advgb-preset-sidebar"
        }, this.renderPresetList()), /*#__PURE__*/React.createElement("div", {
          className: "advgb-preset-editor"
        }, (hasPresets || lastAction == 'creating') && hasActiveForm && this.renderPresetForm(), !hasPresets && lastAction !== 'creating' && /*#__PURE__*/React.createElement("div", {
          className: "advgb-welcome-message"
        }, /*#__PURE__*/React.createElement("div", {
          className: "advgb-welcome-icon"
        }, /*#__PURE__*/React.createElement("svg", {
          width: "48px",
          height: "48px",
          viewBox: "0 0 155.00 155.00",
          fill: "none",
          xmlns: "http://www.w3.org/2000/svg",
          stroke: "#2271b1"
        }, /*#__PURE__*/React.createElement("g", {
          id: "SVGRepo_bgCarrier",
          "stroke-width": "0"
        }), /*#__PURE__*/React.createElement("g", {
          id: "SVGRepo_tracerCarrier",
          "stroke-linecap": "round",
          "stroke-linejoin": "round"
        }), /*#__PURE__*/React.createElement("g", {
          id: "SVGRepo_iconCarrier"
        }, " ", /*#__PURE__*/React.createElement("path", {
          d: "M106.407 96.8913C111.542 102.976 114.23 109.624 114.119 117.272C113.966 127.809 108.553 135.741 100.947 142.254C92.0832 149.843 81.3711 153.044 69.9069 153.943C67.396 154.087 64.8793 154.095 62.3675 153.968C61.2825 153.947 60.2075 153.756 59.1817 153.401C55.071 151.912 54.4462 148.867 57.7062 146.002C60.9065 143.191 64.3602 140.658 67.443 137.729C69.8054 135.468 71.9448 132.984 73.8318 130.313C75.8297 127.501 76.0056 124.257 74.7671 120.518C72.7928 121.437 70.9019 122.169 69.1639 123.165C65.5199 125.253 63.4643 125.166 60.3381 122.422C57.3006 119.755 54.3156 117.028 51.2938 114.343C50.8442 113.992 50.3743 113.668 49.8866 113.373C48.0535 115.067 46.3496 116.757 44.523 118.301C42.1025 120.348 39.7016 120.401 38.131 118.589C36.6588 116.893 36.901 114.651 39.0282 112.471C40.5377 110.923 42.2626 109.584 43.5372 108.461C40.1098 104.278 36.6555 100.848 34.1719 96.8237C31.2696 92.1178 34.8565 87.9231 37.0979 83.3189C32.8192 83.0504 29.3006 83.8341 26.4724 86.0033C23.7734 88.1917 21.3394 90.6881 19.2199 93.4416C17.1669 95.975 15.57 98.8767 13.7474 101.599C12.0291 104.166 10.2688 106.864 6.65102 106.491C4.14644 106.233 0.654067 101.721 0.422379 98.33C-0.102691 90.599 0.881819 83.0753 4.49168 76.0867C11.1049 63.2881 21.523 54.9657 35.0947 50.5131C40.7812 48.6478 46.5098 49.2418 52.3098 51.0087C52.6314 50.6385 53.0088 50.2697 53.3107 49.8476C68.9736 27.9581 90.3183 14.0634 115.597 5.81196C124.206 3.00217 132.916 0.444402 142.093 0.21009C144.385 0.151676 146.679 -0.0235928 148.97 0.00266074C151.958 0.036134 153.448 1.09614 153.327 4.01488C153.172 9.66322 152.644 15.2951 151.744 20.8736C148.056 41.8521 137.967 59.8443 124.863 76.254C119.763 82.6415 114.02 88.5177 108.563 94.621C107.918 95.3403 107.232 96.0243 106.407 96.8913ZM55.4163 106.628C58.8292 109.34 61.3148 111.468 63.9867 113.323C64.4408 113.537 64.9369 113.646 65.4389 113.644C65.9408 113.641 66.4358 113.527 66.8877 113.308C71.1231 111.063 75.4543 108.915 79.3969 106.215C93.4392 96.5986 106.143 85.4257 116.822 72.1336C122.616 64.9224 127.917 57.3167 133.55 49.7426L100.91 19.4316C99.8323 19.9355 98.7827 20.4966 97.7652 21.1125C91.3987 25.3374 84.9081 29.3961 78.7602 33.9196C71.8687 38.9892 66.0371 45.1843 60.8311 52.0129C52.1969 63.3367 47.1136 76.5599 40.7248 89.0828C40.0895 90.3299 40.4459 92.8417 41.3464 93.9568C43.6901 96.8598 46.5925 99.3126 49.7416 102.406C54.6641 97.3035 58.9959 92.5352 63.6454 88.0938C66.324 85.689 69.196 83.5087 72.2323 81.575C73.8285 80.4907 75.8664 80.1212 77.3268 81.7864C78.8068 83.4745 77.5322 85.1277 76.4919 86.4844C75.2622 88.0169 73.9298 89.4642 72.504 90.8162C69.5505 93.7356 66.5904 96.6465 63.5476 99.4701C60.9951 101.843 58.3212 104.081 55.4163 106.628ZM147.141 6.3889C133.175 6.41712 120.848 11.6094 108.28 15.8349L108.056 16.7045L136.962 41.0731C141.915 33.2233 145.713 20.2816 147.141 6.3889ZM77.9707 141.64C89.7309 139.425 98.8422 133.46 103.962 122.528C107.375 115.232 104.991 108.423 100.968 101.949C94.1966 107.118 87.7199 112.064 81.2445 117.008C86.3449 126.009 83.7327 133.936 77.9707 141.64ZM48.698 56.4944C31.8839 53.7377 8.64104 76.4733 10.1486 92.0817C17.0146 81.239 25.8253 74.7064 39.084 77.0567L48.698 56.4944Z",
          fill: "#2271b1"
        }), " ", /*#__PURE__*/React.createElement("path", {
          d: "M14.6051 140.579C15.1033 139.678 15.6382 138.038 16.7054 136.898C20.7301 132.601 24.9024 128.441 29.0774 124.288C30.3034 123.068 31.8543 122.299 33.4499 123.601C35.1327 124.974 34.3963 126.615 33.402 127.973C29.989 132.633 26.5682 137.293 22.9616 141.802C21.8888 143.034 20.5253 143.979 18.9947 144.55C16.7225 145.446 14.5257 143.644 14.6051 140.579Z",
          fill: "#2271b1"
        }), " ", /*#__PURE__*/React.createElement("path", {
          d: "M51.9876 123.87C54.4075 123.936 56.1665 126.118 55.0901 127.808C52.3487 132.062 49.3371 136.136 46.074 140.004C44.905 141.404 42.8061 141.206 41.3214 139.866C40.6426 139.292 40.1985 138.488 40.0735 137.607C39.9485 136.727 40.1513 135.831 40.6434 135.09C41.5342 133.607 42.5933 132.232 43.7998 130.991C45.587 129.088 47.4753 127.273 49.4167 125.529C50.22 124.897 51.0807 124.342 51.9876 123.87Z",
          fill: "#2271b1"
        }), " ", /*#__PURE__*/React.createElement("path", {
          d: "M30.3682 105.409C29.9062 106.43 29.3605 107.411 28.7366 108.341C25.689 112.145 22.5946 115.911 19.4534 119.638C18.8631 120.274 18.1765 120.813 17.4187 121.234C15.8501 122.199 14.2552 122.197 12.9904 120.769C11.6836 119.294 11.9737 117.656 13.2838 116.387C17.5723 112.23 21.91 108.121 26.3337 104.109C26.912 103.584 28.1433 103.499 28.9893 103.655C29.4973 103.749 29.8465 104.705 30.3682 105.409Z",
          fill: "#2271b1"
        }), " ", /*#__PURE__*/React.createElement("path", {
          d: "M99.4671 35.7334C103.729 35.6749 107.015 37.4181 110.313 40.1649C113.732 43.0121 116.286 46.049 117.548 50.2935C120.461 60.111 114.697 67.338 105.35 68.6599C97.775 69.731 91.3495 66.6114 86.6757 60.6814C82.672 55.6 82.5638 49.6871 85.3407 44.0052C88.1407 38.2721 93.1846 35.7944 99.4671 35.7334ZM90.4707 51.4625C91.5654 53.3514 92.3511 55.7595 93.9821 57.2317C95.6715 58.7564 98.1281 59.5118 100.344 60.3545C103.255 61.4624 105.775 60.2889 107.753 58.2294C109.668 56.2374 109.739 53.7354 108.803 51.1869C107.329 47.1688 103.891 45.1524 100.438 43.3337C97.3373 41.7008 94.3949 42.6774 92.6274 45.7353C91.7262 47.2915 91.3232 49.1358 90.4707 51.4625Z",
          fill: "#2271b1"
        }), " "))), /*#__PURE__*/React.createElement("h3", null, __('Welcome to Block Control Presets', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, __('This screen allows you to create powerful visibility rules for your blocks.', 'advanced-gutenberg'), /*#__PURE__*/React.createElement("br", null), " ", __('These preset rules are available when you\'re writing posts.', 'advanced-gutenberg')), this.renderFeatureGrid(), /*#__PURE__*/React.createElement("div", {
          className: "advgb-guidance-actions"
        }, /*#__PURE__*/React.createElement("button", {
          className: "button button-primary",
          onClick: function onClick() {
            return _this29.createNewPreset();
          }
        }, __('Create Your First Preset', 'advanced-gutenberg')), !hasPresets && /*#__PURE__*/React.createElement("button", {
          className: "button button-secondary",
          onClick: function onClick() {
            return _this29.createSamplePresets();
          }
        }, __('Generate Sample Presets', 'advanced-gutenberg')))), hasPresets && !hasActiveForm && /*#__PURE__*/React.createElement("div", {
          className: "advgb-guidance-message",
          "data-action": lastAction
        }, this.renderContextualMessage(lastAction)))));
        if (isModal) {
          return /*#__PURE__*/React.createElement(Modal, {
            title: __('Block Control Presets', 'advanced-gutenberg'),
            onRequestClose: this.handleModalClose,
            isDismissible: true,
            shouldCloseOnClickOutside: false,
            shouldCloseOnEsc: true,
            isFullScreen: true,
            className: "advgb-preset-modal"
          }, content);
        }
        return content;
      }
    }]);
  }(Component);
  window.AdvGBPresetManager = PresetManager;
})(wp.i18n, wp.components, wp.element, wp.data);
})();

/******/ })()
;
//# sourceMappingURL=preset-manager.js.map
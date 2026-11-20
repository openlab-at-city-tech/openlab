/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/blocks/0-adv-components/datetime.jsx"
/*!*****************************************************!*\
  !*** ./assets/blocks/0-adv-components/datetime.jsx ***!
  \*****************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
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

"use strict";
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

"use strict";


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


/***/ },

/***/ "./node_modules/classnames/index.js"
/*!******************************************!*\
  !*** ./node_modules/classnames/index.js ***!
  \******************************************/
(module, exports) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	Copyright (c) 2018 Jed Watson.
	Licensed under the MIT License (MIT), see
	http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;

	function classNames () {
		var classes = '';

		for (var i = 0; i < arguments.length; i++) {
			var arg = arguments[i];
			if (arg) {
				classes = appendClass(classes, parseValue(arg));
			}
		}

		return classes;
	}

	function parseValue (arg) {
		if (typeof arg === 'string' || typeof arg === 'number') {
			return arg;
		}

		if (typeof arg !== 'object') {
			return '';
		}

		if (Array.isArray(arg)) {
			return classNames.apply(null, arg);
		}

		if (arg.toString !== Object.prototype.toString && !arg.toString.toString().includes('[native code]')) {
			return arg.toString();
		}

		var classes = '';

		for (var key in arg) {
			if (hasOwn.call(arg, key) && arg[key]) {
				classes = appendClass(classes, key);
			}
		}

		return classes;
	}

	function appendClass (value, newClass) {
		if (!newClass) {
			return value;
		}
	
		if (value) {
			return value + ' ' + newClass;
		}
	
		return value + newClass;
	}

	if ( true && module.exports) {
		classNames.default = classNames;
		module.exports = classNames;
	} else if (true) {
		// register as 'classnames', consistent with npm package name
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = (function () {
			return classNames;
		}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else // removed by dead control flow
{}
}());


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
  !*** ./assets/pages/block-controls/block-controls.jsx ***!
  \********************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
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
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
var classnames = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
var _require = __webpack_require__(/*! ../../blocks/0-adv-components/datetime.jsx */ "./assets/blocks/0-adv-components/datetime.jsx"),
  AdvDateTimeControl = _require.AdvDateTimeControl,
  AdvDaysControl = _require.AdvDaysControl,
  AdvTimeControl = _require.AdvTimeControl,
  AdvTimezoneControl = _require.AdvTimezoneControl;
var _require2 = __webpack_require__(/*! ../../blocks/0-adv-components/utils.jsx */ "./assets/blocks/0-adv-components/utils.jsx"),
  getOptionSuggestions = _require2.getOptionSuggestions,
  getOptionTitles = _require2.getOptionTitles,
  getOptionSlugs = _require2.getOptionSlugs;
(function (wpI18n, wpHooks, wpBlocks, wpBlockEditor, wpComponents, wpCompose, wpElement) {
  wpBlockEditor = wp.blockEditor || wp.editor;
  var addFilter = wpHooks.addFilter;
  var sprintf = wpI18n.sprintf,
    __ = wpI18n.__;
  var hasBlockSupport = wpBlocks.hasBlockSupport;
  var _wpBlockEditor = wpBlockEditor,
    InspectorControls = _wpBlockEditor.InspectorControls,
    BlockControls = _wpBlockEditor.BlockControls;
  var DateTimePicker = wpComponents.DateTimePicker,
    ToggleControl = wpComponents.ToggleControl,
    PanelBody = wpComponents.PanelBody,
    Notice = wpComponents.Notice,
    FormTokenField = wpComponents.FormTokenField,
    SelectControl = wpComponents.SelectControl,
    TextControl = wpComponents.TextControl,
    TextareaControl = wpComponents.TextareaControl,
    RadioControl = wpComponents.RadioControl,
    Button = wpComponents.Button,
    TabPanel = wpComponents.TabPanel,
    Modal = wpComponents.Modal;
  var createHigherOrderComponent = wpCompose.createHigherOrderComponent;
  var Component = wpElement.Component,
    Fragment = wpElement.Fragment;

  // do not show this feature if disabled.
  if (!parseInt(advgbBlocks.block_controls)) return;

  // Blocks that are not supported
  var NON_SUPPORTED_BLOCKS = ['core/freeform', 'core/legacy-widget', 'core/widget-area', 'core/column', 'advgb/tab', 'advgb/column'];
  var getGlobalControls = function getGlobalControls() {
    return typeof advgb_block_controls_vars.controls !== 'undefined' && Object.keys(advgb_block_controls_vars.controls).length > 0 ? advgb_block_controls_vars.controls : [];
  };

  /**
   * Check if schedule settings is legacy schedule
   * @param {*} scheduleControl
   * @returns
   */
  var isLegacyScheduleFormat = function isLegacyScheduleFormat(scheduleControl) {
    return scheduleControl && (scheduleControl.hasOwnProperty('dateFrom') || scheduleControl.hasOwnProperty('dateTo') || scheduleControl.hasOwnProperty('days') || scheduleControl.hasOwnProperty('recurring'));
  };

  /**
   * Check if a control is enabled
   *
   * @since 3.1.0
   * @param {string} control  The use case block control. e.g. 'schedule'
   *
   * @return {bool}
   */
  var isControlEnabled = function isControlEnabled(control) {
    return typeof control !== 'undefined' && control;
  };

  /**
   * Check if at least one control is enabled per block instance
   *
   * @since 3.1.1
   * @param {string} controlAttrs     Controls attributes. e.g. advgbBlockControls or props.attributes @TODO Figure out a way to NOT require controlAttrs as param due is the same always
   *
   * @return {bool}
   */
  var isAnyControlEnabledBlock = function isAnyControlEnabledBlock(controlAttrs) {
    var globalControls = getGlobalControls();
    var counter = 0;
    var blockControls = []; // Controls enabled in block instance

    // Get enabled global controls (in Settings)
    Object.keys(globalControls).forEach(function (item) {
      if (isControlEnabled(advgb_block_controls_vars.controls[item])) {
        blockControls.push(item);
      }
    });

    // Get counter for enabled controls in block instance
    blockControls.forEach(function (item) {
      if (currentControlKey(controlAttrs, item, 'enabled')) {
        counter++;
      }
    });

    // Check for preset controls
    if (currentControlKey(controlAttrs, 'presets', 'enabled') && currentControlKey(controlAttrs, 'presets', 'selected') && currentControlKey(controlAttrs, 'presets', 'selected').length > 0) {
      counter++;
    }
    return counter > 0 ? true : false;
  };

  /**
   * Check if at least one control is enabled globally (in Settings)
   *
   * @since 3.1.0
   *
   * @return {bool}
   */
  var isAnyControlEnabledGlobal = function isAnyControlEnabledGlobal() {
    var globalControls = getGlobalControls();
    var counter = 0;
    Object.keys(globalControls).map(function (item) {
      if (isControlEnabled(advgb_block_controls_vars.controls[item])) {
        counter++;
      }
    });
    return counter > 0 ? true : false;
  };

  /**
   * Return single controls array attribute value
   *
   * @since 3.1.0
   * @param {string} controlAttrs     Controls attributes. e.g. advgbBlockControls or props.attributes @TODO Figure out a way to NOT require controlAttrs as param due is the same always
   * @param {string} control          The use case block control. e.g. 'schedule'
   * @param {string} key              The control key to check. e.g. 'enabled'
   *
   * @return {mixed}
   */
  var currentControlKey = function currentControlKey(controlAttrs, control, key) {
    // Check if advgbBlockControls attribute exists
    var controlsAdded = typeof controlAttrs !== 'undefined' && controlAttrs.length ? true : false;

    // Check if control exists in advgbBlockControls array
    var controlExists = controlsAdded && controlAttrs.some(function (element) {
      return element.control === control;
    }) ? true : false;
    if (controlExists) {
      var itemIndex = controlAttrs.findIndex(function (element) {
        return element.control === control;
      });
      if (itemIndex < 0) {
        return false;
      }
      var newArray = _toConsumableArray(controlAttrs);
      var obj = newArray[itemIndex];

      // Special handling for schedule control because of legacy format
      if (control === 'schedule') {
        // Check for legacy format
        if (isLegacyScheduleFormat(obj)) {
          if (key === 'schedules') {
            // Convert legacy to new format for the first time
            return [{
              dateFrom: obj.dateFrom || null,
              dateTo: obj.dateTo || null,
              recurring: obj.recurring || false,
              days: obj.days || [],
              timeFrom: obj.timeFrom || null,
              timeTo: obj.timeTo || null,
              timezone: obj.timezone || null
            }];
          } else if (key === 'enabled') {
            return obj.enabled || false;
          }
          return obj[key]; // Return legacy property
        }
        // New format handling
        if (key === 'schedules') {
          return obj.schedules || [];
        } else if (key === 'enabled') {
          return obj.enabled || false;
        }

        // For other keys, return from first schedule if exists
        if (obj.schedules && obj.schedules.length > 0) {
          return obj.schedules[0][key];
        }
        return null;
      }
      return obj[key];
    }
    return null;
  };
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
    }, __('Remove', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement(AdvDateTimeControl, {
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
    }), /*#__PURE__*/React.createElement(AdvDateTimeControl, {
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
    }), /*#__PURE__*/React.createElement(AdvDaysControl, {
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
    }, __('Between these times (optional)', 'advanced-gutenberg')), /*#__PURE__*/React.createElement(AdvTimeControl, {
      label: __('From', 'advanced-gutenberg'),
      currentTime: schedule.timeFrom || null,
      onChangeTime: function onChangeTime(newTime) {
        return _onChange('timeFrom', newTime);
      },
      onTimeClear: function onTimeClear() {
        return _onChange('timeFrom', null);
      }
    }), /*#__PURE__*/React.createElement(AdvTimeControl, {
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
    }, __('"To" time should be after "From" time!', 'advanced-gutenberg')), /*#__PURE__*/React.createElement(AdvTimezoneControl, {
      label: __('Timezone', 'advanced-gutenberg'),
      defaultTimezone: getTimezoneLabel(),
      value: schedule.timezone || getTimezoneSlug(),
      onChangeTimezone: function onChangeTimezone(value) {
        return _onChange('timezone', value);
      }
    }));
  };

  // Add non supported blocks according to Block controls
  if (typeof advgb_block_controls_vars !== 'undefined' && typeof advgb_block_controls_vars.non_supported !== 'undefined' && advgb_block_controls_vars.non_supported.length > 0) {
    // Merge dynamically disabled blocks
    NON_SUPPORTED_BLOCKS = [].concat(_toConsumableArray(NON_SUPPORTED_BLOCKS), _toConsumableArray(advgb_block_controls_vars.non_supported));
    // Remove duplicated values
    NON_SUPPORTED_BLOCKS = _toConsumableArray(new Set(NON_SUPPORTED_BLOCKS));
  }

  // Register block controls to blocks attributes
  addFilter('blocks.registerBlockType', 'advgb/blockControls', function (settings) {
    if (!NON_SUPPORTED_BLOCKS.includes(settings.name) && isAnyControlEnabledGlobal()) {
      settings.attributes = _extends(settings.attributes, {
        advgbBlockControls: {
          type: 'array',
          items: {
            type: 'object'
          },
          default: []
        }
      });
    }
    return settings;
  });
  var withEditControls = createHigherOrderComponent(function (BlockEdit) {
    return /*#__PURE__*/function (_Component) {
      function BlockControlsEdit(props) {
        var _this;
        _classCallCheck(this, BlockControlsEdit);
        _this = _callSuper(this, BlockControlsEdit, [props]);
        _this.state = {
          taxModOptions: [],
          // Store modified taxonomy options to decide if selected tax is for "all terms" or "selected terms"
          termOptions: [],
          // Store term options with slug (id) and title
          searchTermWord: '',
          // Updated when searching terms
          initArchive: true,
          // When true, trigger initArchiveControl()
          updateTaxLabels: false // When true, update taxonomy option labels
        };
        _this.isPost = _this.isPost.bind(_this);
        return _this;
      }

      /**
       * Get filtered term slugs based on selected taxonomies
       *
       * @since 3.1.1
       * @param {array} taxonomies Array of taxonomies slugs
       *
       * @return {array}
       */
      _inherits(BlockControlsEdit, _Component);
      return _createClass(BlockControlsEdit, [{
        key: "getTermSlugs",
        value: function getTermSlugs(taxonomies) {
          var _this2 = this;
          var allTaxonomies = this.getTaxonomies();
          var terms = [];
          taxonomies.forEach(function (tax) {
            var allTaxonomies = _this2.getTaxonomies();
            var itemIndex = allTaxonomies.findIndex(function (element) {
              return element.slug === tax;
            });
            allTaxonomies[itemIndex].terms.forEach(function (term) {
              terms.push(term.slug);
            });
          });
          return terms;
        }

        /**
         * Get taxonomies
         *
         * @since 3.1.1
         *
         * @return {array}
         */
      }, {
        key: "getTaxonomies",
        value: function getTaxonomies() {
          return typeof advgb_block_controls_vars.taxonomies !== 'undefined' && advgb_block_controls_vars.taxonomies.length > 0 ? advgb_block_controls_vars.taxonomies : [];
        }

        /**
         * Get all the available user roles from the site
         *
         * @since 3.1.0
         *
         * @return {array}
         */
      }, {
        key: "getUserRoles",
        value: function getUserRoles() {
          return typeof advgb_block_controls_vars.user_roles !== 'undefined' && advgb_block_controls_vars.user_roles.length > 0 ? advgb_block_controls_vars.user_roles : [];
        }

        /**
         * Get filtered terms based on selected taxonomies
         *
         * @since 3.1.1
         * @param {array} taxonomies Taxonomies slugs
         *
         * @return {array}
         */
      }, {
        key: "getTerms",
        value: function getTerms(taxonomies) {
          var _this3 = this;
          var allTaxonomies = this.getTaxonomies();
          var terms = [];
          taxonomies.forEach(function (tax) {
            var allTaxonomies = _this3.getTaxonomies();
            var itemIndex = allTaxonomies.findIndex(function (element) {
              return element.slug === tax;
            });
            allTaxonomies[itemIndex].terms.forEach(function (term) {
              terms.push({
                slug: term.slug,
                title: "".concat(term.title, " (").concat(allTaxonomies[itemIndex].title, ")")
              });
            });
          });
          return terms;
        }

        /**
         * Get pages
         *
         * @since 3.1.1
         *
         * @return {array}
         */
      }, {
        key: "getPages",
        value: function getPages() {
          return typeof advgb_block_controls_vars.page !== 'undefined' && advgb_block_controls_vars.page.length > 0 ? advgb_block_controls_vars.page : [];
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
          }, {
            label: 'in',
            value: 'in'
          }, {
            label: 'not in',
            value: 'notIn'
          }, {
            label: 'between',
            value: 'between'
          }, {
            label: 'not between',
            value: 'notBetween'
          }];
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

        /**
         * Update advgbBlockControls attribute when a key value changes
         *
         * @since 2.14.0
         * @param {string} control  The use case block control. e.g. 'schedule'
         * @param {string} key      The control key to modify. e.g. 'enabled'
         * @param {string} value    The control key value (not required for boolean keys)
         *
         * @return {void}
         */
      }, {
        key: "changeControlKey",
        value: function changeControlKey(control, key) {
          var value = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';
          var _this$props = this.props,
            attributes = _this$props.attributes,
            setAttributes = _this$props.setAttributes;
          var advgbBlockControls = attributes.advgbBlockControls;

          // Control objects to add  when enabled for the first time
          var scheduleControl = {
            control: 'schedule',
            enabled: true,
            schedules: [{
              dateFrom: null,
              dateTo: null,
              recurring: false,
              days: [],
              timeFrom: null,
              timeTo: null,
              timezone: this.getTimezoneSlug()
            }]
          };
          var userRoleControl = {
            control: 'user_role',
            enabled: true,
            roles: [],
            approach: 'public'
          };
          var archiveControl = {
            control: 'archive',
            enabled: true,
            taxonomies: [],
            approach: 'exclude'
          };
          var pageControl = {
            control: 'page',
            enabled: true,
            pages: [],
            approach: 'exclude'
          };
          var deviceTypeControl = {
            control: 'device_type',
            enabled: true,
            devices: []
          };
          var deviceWidthControl = {
            control: 'device_width',
            enabled: true,
            min_width: '',
            max_width: ''
          };
          var browserDeviceControl = {
            control: 'browser_device',
            enabled: true,
            browsers: [],
            approach: 'include'
          };
          var operatingSystemControl = {
            control: 'operating_system',
            enabled: true,
            systems: [],
            approach: 'include'
          };
          var cookieControl = {
            control: 'cookie',
            enabled: true,
            name: '',
            condition: '=',
            value: '',
            approach: 'include'
          };
          var userMetaControl = {
            control: 'user_meta',
            enabled: true,
            key: '',
            condition: '=',
            value: '',
            approach: 'include'
          };
          var postMetaControl = {
            control: 'post_meta',
            enabled: true,
            key: '',
            condition: '=',
            value: '',
            approach: 'include'
          };
          var queryStringControl = {
            control: 'query_string',
            enabled: true,
            queries: [],
            logic: 'all',
            approach: 'include'
          };
          var capabilitiesControl = {
            control: 'capabilities',
            enabled: true,
            capabilities: [],
            approach: 'include'
          };
          var presetsControl = {
            control: 'presets',
            enabled: true,
            selected: [],
            logic: 'any'
          };
          var metaControl = {
            control: 'meta',
            activeTab: 'custom'
          };

          // Check if advgbBlockControls attribute exists
          var controlsAdded = typeof advgbBlockControls !== 'undefined' && advgbBlockControls.length ? true : false;
          // Check if control exists in advgbBlockControls array
          var controlExists = controlsAdded && advgbBlockControls.some(function (element) {
            return element.control === control;
          }) ? true : false;
          if (controlExists) {
            var itemIndex = advgbBlockControls.findIndex(function (element) {
              return element.control === control;
            });

            // No control found (this check seems not necessary but is here to prevent an unlikely error)
            if (itemIndex < 0) {
              return false;
            }
            var newArray = _toConsumableArray(advgbBlockControls);
            var obj = newArray[itemIndex];

            // Special handling for schedule control because of legacy format
            if (control === 'schedule') {
              // Handle legacy format conversion
              if (isLegacyScheduleFormat(obj)) {
                // Convert to new format
                var legacySchedule = {
                  dateFrom: obj.dateFrom || null,
                  dateTo: obj.dateTo || null,
                  recurring: obj.recurring || false,
                  days: obj.days || [],
                  timeFrom: obj.timeFrom || null,
                  timeTo: obj.timeTo || null,
                  timezone: obj.timezone || this.getTimezoneSlug()
                };
                newArray[itemIndex] = {
                  control: 'schedule',
                  enabled: obj.enabled,
                  schedules: [legacySchedule]
                };

                // Now handle the key update
                if (key !== 'enabled' && key !== 'schedules') {
                  newArray[itemIndex].schedules[0][key] = value;
                } else {
                  newArray[itemIndex][key] = value;
                }
                setAttributes({
                  advgbBlockControls: newArray
                });
                return;
              }

              // Handle new format updates
              if (key === 'schedules') {
                newArray[itemIndex] = _objectSpread(_objectSpread({}, newArray[itemIndex]), {}, {
                  schedules: value
                });
              } else if (key === 'enabled') {
                newArray[itemIndex] = _objectSpread(_objectSpread({}, newArray[itemIndex]), {}, {
                  enabled: typeof value === 'boolean' ? value : !obj.enabled
                });
              } else {
                // Update specific property in first schedule
                var schedules = _toConsumableArray(obj.schedules || []);
                if (schedules.length === 0) {
                  schedules.push({
                    dateFrom: null,
                    dateTo: null,
                    recurring: false,
                    days: [],
                    timeFrom: null,
                    timeTo: null,
                    timezone: this.getTimezoneSlug()
                  });
                }
                schedules[0][key] = value;
                newArray[itemIndex] = _objectSpread(_objectSpread({}, newArray[itemIndex]), {}, {
                  schedules: schedules
                });
              }
              setAttributes({
                advgbBlockControls: newArray
              });
            } else {
              newArray[itemIndex] = typeof obj[key] === 'boolean' ? _objectSpread(_objectSpread({}, newArray[itemIndex]), {}, _defineProperty({}, key, !obj[key])) : _objectSpread(_objectSpread({}, newArray[itemIndex]), {}, _defineProperty({}, key, value));
            }
            setAttributes({
              advgbBlockControls: newArray
            });
          } else if (controlsAdded && !controlExists) {
            // Add a new control object when other controls already exists
            var controlToAdd = {};
            switch (control) {
              case 'schedule':
                controlToAdd[control] = scheduleControl;
                break;
              case 'user_role':
                setAttributes({
                  advgbBlockControls: [].concat(_toConsumableArray(advgbBlockControls), [userRoleControl])
                });
                break;
              case 'archive':
                setAttributes({
                  advgbBlockControls: [].concat(_toConsumableArray(advgbBlockControls), [archiveControl])
                });
                break;
              case 'page':
                setAttributes({
                  advgbBlockControls: [].concat(_toConsumableArray(advgbBlockControls), [pageControl])
                });
                break;
              case 'device_type':
                setAttributes({
                  advgbBlockControls: [].concat(_toConsumableArray(advgbBlockControls), [deviceTypeControl])
                });
                break;
              case 'device_width':
                setAttributes({
                  advgbBlockControls: [].concat(_toConsumableArray(advgbBlockControls), [deviceWidthControl])
                });
                break;
              case 'browser_device':
                setAttributes({
                  advgbBlockControls: [].concat(_toConsumableArray(advgbBlockControls), [browserDeviceControl])
                });
                break;
              case 'operating_system':
                setAttributes({
                  advgbBlockControls: [].concat(_toConsumableArray(advgbBlockControls), [operatingSystemControl])
                });
                break;
              case 'cookie':
                setAttributes({
                  advgbBlockControls: [].concat(_toConsumableArray(advgbBlockControls), [cookieControl])
                });
                break;
              case 'user_meta':
                setAttributes({
                  advgbBlockControls: [].concat(_toConsumableArray(advgbBlockControls), [userMetaControl])
                });
                break;
              case 'post_meta':
                setAttributes({
                  advgbBlockControls: [].concat(_toConsumableArray(advgbBlockControls), [postMetaControl])
                });
                break;
              case 'query_string':
                setAttributes({
                  advgbBlockControls: [].concat(_toConsumableArray(advgbBlockControls), [queryStringControl])
                });
                break;
              case 'capabilities':
                setAttributes({
                  advgbBlockControls: [].concat(_toConsumableArray(advgbBlockControls), [capabilitiesControl])
                });
                break;
              case 'presets':
                setAttributes({
                  advgbBlockControls: [].concat(_toConsumableArray(advgbBlockControls), [presetsControl])
                });
                break;
              case 'meta':
                setAttributes({
                  advgbBlockControls: [].concat(_toConsumableArray(advgbBlockControls), [metaControl])
                });
                break;
            }
            if (controlToAdd[control]) {
              setAttributes({
                advgbBlockControls: [].concat(_toConsumableArray(advgbBlockControls), [controlToAdd[control]])
              });
            }
          } else {
            // Add the first control object attribute
            var _controlToAdd = {};
            switch (control) {
              case 'schedule':
                _controlToAdd[control] = scheduleControl;
                break;
              case 'user_role':
                setAttributes({
                  advgbBlockControls: [userRoleControl]
                });
                break;
              case 'archive':
                setAttributes({
                  advgbBlockControls: [archiveControl]
                });
                break;
              case 'page':
                setAttributes({
                  advgbBlockControls: [pageControl]
                });
                break;
              case 'device_type':
                setAttributes({
                  advgbBlockControls: [deviceTypeControl]
                });
                break;
              case 'device_width':
                setAttributes({
                  advgbBlockControls: [deviceWidthControl]
                });
                break;
              case 'browser_device':
                setAttributes({
                  advgbBlockControls: [browserDeviceControl]
                });
                break;
              case 'operating_system':
                setAttributes({
                  advgbBlockControls: [operatingSystemControl]
                });
                break;
              case 'cookie':
                setAttributes({
                  advgbBlockControls: [cookieControl]
                });
                break;
              case 'user_meta':
                setAttributes({
                  advgbBlockControls: [userMetaControl]
                });
                break;
              case 'post_meta':
                setAttributes({
                  advgbBlockControls: [postMetaControl]
                });
                break;
              case 'query_string':
                setAttributes({
                  advgbBlockControls: [queryStringControl]
                });
                break;
              case 'capabilities':
                setAttributes({
                  advgbBlockControls: [capabilitiesControl]
                });
                break;
              case 'presets':
                setAttributes({
                  advgbBlockControls: [presetsControl]
                });
                break;
              case 'meta':
                setAttributes({
                  advgbBlockControls: [metaControl]
                });
                break;
            }
            if (_controlToAdd[control]) {
              setAttributes({
                advgbBlockControls: [_controlToAdd[control]]
              });
            }
          }
        }

        /**
         * Update archive control in advgbBlockControls attribute when taxonomies value changes
         *
         * @since 3.1.2
         *
         * @param {string} topic 'taxonomies' or 'terms'
         * @param {string} slugs The taxonomy slugs or term ids to insert/modify. e.g. ['category','post_tag'] or [82,161,99] or ['all_<taxonomy_slug>']
         *
         * @return {void}
         */
      }, {
        key: "changeArchiveControl",
        value: function changeArchiveControl(topic, slugs) {
          var _this4 = this;
          var _this$props2 = this.props,
            attributes = _this$props2.attributes,
            setAttributes = _this$props2.setAttributes;
          var advgbBlockControls = attributes.advgbBlockControls;
          var taxArray = [];
          var controlIndex = advgbBlockControls.findIndex(function (element) {
            return element.control === 'archive';
          });

          // No control found (this check seems not necessary but is here to prevent an unlikely error)
          if (controlIndex < 0) {
            return false;
          }
          var newArray = _toConsumableArray(advgbBlockControls);
          if (topic === 'taxonomies') {
            // Check each taxonomy and its terms
            slugs.forEach(function (item) {
              // Get terms from current taxonomy (item)
              var taxIndex = newArray[controlIndex].taxonomies.findIndex(function (element) {
                return element.tax === item;
              });
              if (taxIndex === -1) {
                // The last selected taxonomy
                taxArray.push({
                  tax: item,
                  terms: [],
                  all: true
                });
              } else {
                // Existing taxonomy
                var terms = newArray[controlIndex].taxonomies[taxIndex].terms.length ? newArray[controlIndex].taxonomies[taxIndex].terms : [];
                var approach = terms.length ? 'select' : 'all';
                taxArray.push({
                  tax: item,
                  terms: terms,
                  all: terms.length ? false : true
                });
              }
            });
            newArray[controlIndex] = _objectSpread(_objectSpread({}, newArray[controlIndex]), {}, _defineProperty({}, 'taxonomies', taxArray));
            setAttributes({
              advgbBlockControls: newArray
            });
          } else if (topic === 'terms') {
            var terms = {};
            var taxonomies = this.currentArchiveControl('taxonomies');

            // Check each term id (item). slug means the id
            slugs.forEach(function (item) {
              // Find the current term in termOptions state to use its tax later
              var option = _this4.state.termOptions.find(function (el) {
                return el.slug === item;
              });
              if (terms[option.tax] === undefined) {
                terms[option.tax] = [];
              }

              // Get taxonomy from current term (item)
              var taxIndex = newArray[controlIndex].taxonomies.findIndex(function (element) {
                return element.tax === option.tax;
              });

              /* Taxonomy for this term is selected? Is a bit reduntant but let's make sure
               * Then include the term.
               */
              if (taxonomies.includes(option.tax)) {
                terms[option.tax].push(item);
              }
            });

            // Update taxonomies with at least one term selected
            Object.keys(terms).forEach(function (tax) {
              // Get taxonomy from current tax
              var taxIndex = newArray[controlIndex].taxonomies.findIndex(function (element) {
                return element.tax === tax;
              });
              if (taxIndex >= 0) {
                newArray[controlIndex].taxonomies[taxIndex] = {
                  tax: tax,
                  terms: terms[tax],
                  all: terms[tax].length ? false : true
                };
              }
            });

            // Include taxonomies with no terms selected (empty terms[option.tax] array)
            taxonomies.forEach(function (tax) {
              if (!Object.keys(terms).includes(tax)) {
                // Get taxonomy from current tax
                var taxIndex = newArray[controlIndex].taxonomies.findIndex(function (element) {
                  return element.tax === tax;
                });
                if (taxIndex >= 0) {
                  newArray[controlIndex].taxonomies[taxIndex] = {
                    tax: tax,
                    terms: [],
                    all: true
                  };
                }
              }
            });
            setAttributes({
              advgbBlockControls: newArray
            });
          } else {
            // Nothing to do here
          }
        }

        /**
         * Return merged taxonomies or terms
         *
         * @since 3.1.2
         *
         * @param {string} topic 'taxonomies' or 'terms'
         *
         * @return {array} An single array with all the selected terms or taxonomies ['category','post_tag'] or [99,182,42]
         */
      }, {
        key: "currentArchiveControl",
        value: function currentArchiveControl(topic) {
          var _this$props3 = this.props,
            attributes = _this$props3.attributes,
            setAttributes = _this$props3.setAttributes;
          var advgbBlockControls = attributes.advgbBlockControls;
          var result = [];

          /* Get all the taxonomy objects.
           * e.g.
           * [
           *     { "tax": "post_tag", "terms": [220,221]},
           *     { "tax": "category", "terms": []}
           * ]
           */
          var taxonomies = currentControlKey(advgbBlockControls, 'archive', 'taxonomies').length ? currentControlKey(advgbBlockControls, 'archive', 'taxonomies') : [];
          if (topic === 'taxonomies') {
            taxonomies.forEach(function (item) {
              result.push(item.tax);
            });
          } else if (topic === 'terms') {
            taxonomies.forEach(function (item) {
              item.terms.forEach(function (el) {
                result.push(el); // term id
              });
            });
          } else {
            // Nothing to do here
          }
          return result;
        }

        /**
         * Execute when taxonomy selection changes
         *
         * @since 3.1.1
         *
         * @return {void}
         */
      }, {
        key: "taxonomiesChanged",
        value: function taxonomiesChanged() {
          var _this5 = this;
          var attributes = this.props.attributes;
          var advgbBlockControls = attributes.advgbBlockControls;
          var currentTerms = !!currentControlKey(advgbBlockControls, 'archive', 'terms') ? currentControlKey(advgbBlockControls, 'archive', 'terms') : [];
          var taxonomies = !!currentControlKey(advgbBlockControls, 'archive', 'taxonomies') ? currentControlKey(advgbBlockControls, 'archive', 'taxonomies') : [];
          if (currentTerms.length) {
            var result = [];
            currentTerms.forEach(function (slug) {
              var itemIndex = _this5.state.termOptions.findIndex(function (item) {
                return item.slug === slug;
              });

              /* Get only the terms that belongs to selected taxonomies
               * and skip the ones that belongs to the deleted taxonomy
               */
              if (taxonomies.includes(_this5.state.termOptions[itemIndex].tax)) {
                result.push(_this5.state.termOptions[itemIndex].slug);
              }
            });
            this.changeControlKey('archive', 'terms', result);
          }

          /* Remove term options from non-selected taxonomies.
           * Case scenario: the terms from the last removed taxonomy.
           */
          this.setState({
            termOptions: this.state.termOptions.filter(function (item) {
              return _this5.currentArchiveControl('taxonomies').includes(item.tax);
            })
          });

          // Update tax label options to "All <taxonomy> terms" or "Selected <taxonomy> terms"
          this.modifyTaxLabels();
        }

        /**
         * Get selected terms on first load
         *
         * @since 3.1.1
         *
         * @return {void}
         */
      }, {
        key: "initArchiveControl",
        value: function initArchiveControl() {
          var _this6 = this;
          var advgbBlockControls = this.props.attributes.advgbBlockControls;
          wp.apiFetch({
            path: wp.url.addQueryArgs('advgb/v1/terms', {
              taxonomies: this.currentArchiveControl('taxonomies'),
              ids: this.currentArchiveControl('terms')
            })
          }).then(function (list) {
            // Update tax label options to "All <taxonomy> terms" or "Selected <taxonomy> terms"
            _this6.modifyTaxLabels();
            _this6.setState({
              termOptions: list,
              initArchive: false,
              updateTaxLabels: false
            });
          });
        }

        /**
         * Initial taxonomy labels to allow "All <taxonomy> terms" "Selected <taxonomy> terms" visual indicator
         *
         * @since 3.1.2
         *
         * @return {array}
         */
      }, {
        key: "iniTaxLabels",
        value: function iniTaxLabels() {
          var result = [];
          this.getTaxonomies().forEach(function (item) {
            /* Item example
             *  {
             *      "slug": "category",
             *      "title": "All Category terms",
             *      "singular": "Category"
             *  }
             */
            result.push({
              slug: item.slug,
              title: sprintf(__('All %s terms', 'advanced-gutenberg'), item.title),
              singular: item.title
            });
          });
          return result;
        }

        /**
         * Modify taxonomy labels. Very similar to iniTaxLabels()
         *
         * @since 3.1.2
         *
         * @return {array}
         */
      }, {
        key: "modifyTaxLabels",
        value: function modifyTaxLabels() {
          var advgbBlockControls = this.props.attributes.advgbBlockControls;

          /* Get all selected taxonomy objects.
           * e.g.
           * [
           *     { "tax": "post_tag", "terms": [220,221]},
           *     { "tax": "category", "terms": []}
           * ]
           */
          var taxonomies = currentControlKey(advgbBlockControls, 'archive', 'taxonomies').length ? currentControlKey(advgbBlockControls, 'archive', 'taxonomies') : [];

          // Copy whole state
          var options = _toConsumableArray(this.state.taxModOptions);
          options.forEach(function (item, index) {
            var tax = taxonomies.find(function (el) {
              return item.slug === el.tax;
            });

            // Copy option to modify
            var option = _objectSpread({}, options[index]);
            // Update title value
            option.title = sprintf(tax === undefined || !tax.terms.length ? __('All %s terms', 'advanced-gutenberg') : __('Selected %s terms', 'advanced-gutenberg'), option.singular);
            // Add option back to the state
            options[index] = option;
          });

          // Save
          this.setState({
            taxModOptions: options,
            updateTaxLabels: false
          });
        }

        /**
         * Search terms based on search
         *
         * @since 3.1.1
         *
         * @return {void}
         */
      }, {
        key: "searchTerms",
        value: function searchTerms() {
          var _this7 = this;
          var _this$state = this.state,
            termOptions = _this$state.termOptions,
            searchTermWord = _this$state.searchTermWord;
          var advgbBlockControls = this.props.attributes.advgbBlockControls;
          wp.apiFetch({
            /*/ To get taxonomies
            path: wp.url.addQueryArgs( 'wp/v2/taxonomies', { context: 'edit' } )*/

            path: wp.url.addQueryArgs('advgb/v1/terms', {
              search: searchTermWord,
              taxonomies: this.currentArchiveControl('taxonomies')
            })
          }).then(function (list) {
            /*/ To get taxonomies
            Object.keys(list).forEach( (item) => {
                options.push( {
                    label: list[item].name,
                    value: list[item].slug
                } );
            });*/

            // Merge selected terms with results from fetch
            var options = [].concat(_toConsumableArray(termOptions), _toConsumableArray(list));

            // Remove duplicated values
            options = Array.from(new Set(options.map(function (a) {
              return a.slug;
            }))).map(function (slug) {
              return options.find(function (a) {
                return a.slug === slug;
              });
            });
            _this7.setState({
              termOptions: options
            });
          });
        }

        /**
         * Check if we're in post edit screen
         *
         * @since 3.1.2
         *
         * @return {bool}
         */
      }, {
        key: "isPost",
        value: function isPost() {
          var editorStore = wp.data.select('core/editor');
          if (!editorStore) {
            return false;
          }
          var postId = editorStore.getCurrentPostId();

          // In Site Editor, this returns a string like "twentytwentyfive//home"
          // For real posts, it's a number
          return !!postId && typeof postId === 'number';
        }

        /**
         * Get the timezone label from site settings stored in advgbBlocks object
         *
         * @since 3.1.4
         *
         * @return {bool}
         */
      }, {
        key: "getTimezoneLabel",
        value: function getTimezoneLabel() {
          return typeof advgbBlocks.timezone !== 'undefined' && advgbBlocks.timezone.length ? advgbBlocks.timezone.replace(/_/g, ' ') : __('WordPress settings timezone', 'advanced-gutenberg');
        }

        /**
         * Get the timezone slug from site settings stored in advgbBlocks object
         *
         * @since 3.1.4
         *
         * @return {bool}
         */
      }, {
        key: "getTimezoneSlug",
        value: function getTimezoneSlug() {
          return typeof advgbBlocks.timezone !== 'undefined' && advgbBlocks.timezone.length ? advgbBlocks.timezone : 'UTC';
        }
      }, {
        key: "renderTabContent",
        value: function renderTabContent(tabName) {
          var attributes = this.props.attributes;
          var advgbBlockControls = attributes.advgbBlockControls;
          switch (tabName) {
            case 'custom':
              return this.renderCustomControls(advgbBlockControls);
            case 'presets':
              return this.renderPresetControls(advgbBlockControls);
            default:
              return null;
          }
        }
      }, {
        key: "componentDidMount",
        value: function componentDidMount() {
          var _this8 = this;
          this.setState({
            taxModOptions: this.iniTaxLabels()
          });

          // Subscribe to preset data changes
          if (window.AdvGBPresetData) {
            this.presetDataSubscription = function (updatedPresets) {
              _this8.forceUpdate();
            };
            window.AdvGBPresetData.subscribe(this.presetDataSubscription);
          }
        }
      }, {
        key: "componentWillUnmount",
        value: function componentWillUnmount() {
          // Unsubscribe from preset data changes
          if (window.AdvGBPresetData && this.presetDataSubscription) {
            window.AdvGBPresetData.unsubscribe(this.presetDataSubscription);
          }
        }
      }, {
        key: "componentDidUpdate",
        value: function componentDidUpdate(prevProps, prevState) {
          var _this$props4 = this.props,
            attributes = _this$props4.attributes,
            isSelected = _this$props4.isSelected,
            name = _this$props4.name;
          var advgbBlockControls = attributes.advgbBlockControls;
          var prevBlockControls = prevProps.attributes.advgbBlockControls;
          var _this$state2 = this.state,
            searchTermWord = _this$state2.searchTermWord,
            initArchive = _this$state2.initArchive;
          var prevTermWord = prevState.searchTermWord;

          // Get human readable selected terms on block selection the first time
          if (!this.isPost() && !NON_SUPPORTED_BLOCKS.includes(name) && isSelected && initArchive && isControlEnabled(advgb_block_controls_vars.controls.archive) && currentControlKey(advgbBlockControls, 'archive', 'enabled') && this.currentArchiveControl('taxonomies').length && this.currentArchiveControl('terms').length) {
            this.initArchiveControl();
          }

          // Search terms
          if (searchTermWord !== prevTermWord && searchTermWord.length > 2) {
            this.searchTerms();
          }

          // Update available terms and remove terms which taxonomy has been removed
          if (!this.isPost() && isControlEnabled(advgb_block_controls_vars.controls.archive) && currentControlKey(advgbBlockControls, 'archive', 'enabled') && (currentControlKey(prevBlockControls, 'archive', 'taxonomies') !== currentControlKey(advgbBlockControls, 'archive', 'taxonomies') // This trigger works when taxo changes, but not terms
          || this.state.updateTaxLabels // Trigger when terms changes
          )) {
            this.taxonomiesChanged();
          }
        }
      }, {
        key: "renderCustomControls",
        value: function renderCustomControls(advgbBlockControls) {
          var _this9 = this;
          var query_string = currentControlKey(advgbBlockControls, 'query_string', 'queries');
          var queriesValue = Array.isArray(query_string) ? query_string.join('\n') : query_string || '';
          return /*#__PURE__*/React.createElement(Fragment, null, isControlEnabled(advgb_block_controls_vars.controls.schedule) && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(ToggleControl, {
            label: __('Schedule', 'advanced-gutenberg'),
            help: currentControlKey(advgbBlockControls, 'schedule', 'enabled') ? __('Choose when to start showing and/or stop showing this block.', 'advanced-gutenberg') : '',
            checked: currentControlKey(advgbBlockControls, 'schedule', 'enabled'),
            onChange: function onChange() {
              return _this9.changeControlKey('schedule', 'enabled');
            }
          }), currentControlKey(advgbBlockControls, 'schedule', 'enabled') && /*#__PURE__*/React.createElement(Fragment, null, (currentControlKey(advgbBlockControls, 'schedule', 'schedules') || []).map(function (schedule, index) {
            return /*#__PURE__*/React.createElement(ScheduleControl, {
              key: index,
              index: index,
              schedule: schedule,
              onChange: function onChange(key, value) {
                var schedules = _toConsumableArray(currentControlKey(advgbBlockControls, 'schedule', 'schedules') || []);
                schedules[index][key] = value;
                _this9.changeControlKey('schedule', 'schedules', schedules);
              },
              onRemove: function onRemove() {
                var schedules = _toConsumableArray(currentControlKey(advgbBlockControls, 'schedule', 'schedules') || []);
                if (schedules.length > 1) {
                  schedules.splice(index, 1);
                  _this9.changeControlKey('schedule', 'schedules', schedules);
                }
              },
              getTimezoneLabel: _this9.getTimezoneLabel,
              getTimezoneSlug: _this9.getTimezoneSlug,
              canRemove: (currentControlKey(advgbBlockControls, 'schedule', 'schedules') || []).length > 1
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
            isSecondary: true,
            onClick: function onClick() {
              var currentSchedules = currentControlKey(advgbBlockControls, 'schedule', 'schedules') || [];
              var newSchedule = {
                dateFrom: null,
                dateTo: null,
                recurring: false,
                days: [],
                timeFrom: null,
                timeTo: null,
                timezone: _this9.getTimezoneSlug()
              };
              _this9.changeControlKey('schedule', 'schedules', [].concat(_toConsumableArray(currentSchedules), [newSchedule]));
            }
          }, __('Add Another Schedule', 'advanced-gutenberg'))))), isControlEnabled(advgb_block_controls_vars.controls.user_role) && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(ToggleControl, {
            label: __('User roles', 'advanced-gutenberg'),
            help: currentControlKey(advgbBlockControls, 'user_role', 'enabled') ? __('Choose which users can see this block.', 'advanced-gutenberg') : '',
            checked: currentControlKey(advgbBlockControls, 'user_role', 'enabled'),
            onChange: function onChange() {
              return _this9.changeControlKey('user_role', 'enabled');
            }
          }), currentControlKey(advgbBlockControls, 'user_role', 'enabled') && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement("div", {
            className: "advgb-revert-mb"
          }, /*#__PURE__*/React.createElement(SelectControl, {
            value: currentControlKey(advgbBlockControls, 'user_role', 'approach'),
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
              label: __('Show to the selected user roles', 'advanced-gutenberg')
            }, {
              value: 'exclude',
              label: __('Hide from the selected user roles', 'advanced-gutenberg')
            }],
            onChange: function onChange(value) {
              return _this9.changeControlKey('user_role', 'approach', value);
            }
          })), (currentControlKey(advgbBlockControls, 'user_role', 'approach') === 'include' || currentControlKey(advgbBlockControls, 'user_role', 'approach') === 'exclude') && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(FormTokenField, {
            multiple: true,
            label: __('Select user roles', 'advanced-gutenberg'),
            placeholder: __('Search', 'advanced-gutenberg'),
            suggestions: getOptionSuggestions(this.getUserRoles()),
            maxSuggestions: 10,
            value: getOptionTitles(!!currentControlKey(advgbBlockControls, 'user_role', 'roles') ? currentControlKey(advgbBlockControls, 'user_role', 'roles') : [], this.getUserRoles()),
            onChange: function onChange(value) {
              _this9.changeControlKey('user_role', 'roles', getOptionSlugs(value, _this9.getUserRoles()));
            },
            __experimentalExpandOnFocus: true
          }), (currentControlKey(advgbBlockControls, 'user_role', 'approach') === 'include' || currentControlKey(advgbBlockControls, 'user_role', 'approach') === 'exclude') && !currentControlKey(advgbBlockControls, 'user_role', 'roles').length && /*#__PURE__*/React.createElement(Notice, {
            className: "advgb-notice-sidebar",
            status: "warning",
            isDismissible: false,
            style: {
              marginBottom: 30
            }
          }, __('Please select at least one user role.', 'advanced-gutenberg'))))), isControlEnabled(advgb_block_controls_vars.controls.device_width) && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(ToggleControl, {
            label: __('Device Width', 'advanced-gutenberg'),
            help: currentControlKey(advgbBlockControls, 'device_width', 'enabled') ? __('Set minimum and maximum screen widths for this block to display.', 'advanced-gutenberg') : '',
            checked: currentControlKey(advgbBlockControls, 'device_width', 'enabled'),
            onChange: function onChange() {
              return _this9.changeControlKey('device_width', 'enabled');
            }
          }), currentControlKey(advgbBlockControls, 'device_width', 'enabled') && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(TextControl, {
            type: "number",
            label: __('Minimum width (px)', 'advanced-gutenberg'),
            value: currentControlKey(advgbBlockControls, 'device_width', 'min_width') || '',
            onChange: function onChange(value) {
              return _this9.changeControlKey('device_width', 'min_width', value);
            },
            placeholder: __('No minimum', 'advanced-gutenberg')
          }), /*#__PURE__*/React.createElement(TextControl, {
            type: "number",
            label: __('Maximum width (px)', 'advanced-gutenberg'),
            value: currentControlKey(advgbBlockControls, 'device_width', 'max_width') || '',
            onChange: function onChange(value) {
              return _this9.changeControlKey('device_width', 'max_width', value);
            },
            placeholder: __('No maximum', 'advanced-gutenberg')
          }))), isControlEnabled(advgb_block_controls_vars.controls.device_type) && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(ToggleControl, {
            label: __('Device Type', 'advanced-gutenberg'),
            help: currentControlKey(advgbBlockControls, 'device_type', 'enabled') ? __('Choose which devices this block should be visible on.', 'advanced-gutenberg') : '',
            checked: currentControlKey(advgbBlockControls, 'device_type', 'enabled'),
            onChange: function onChange() {
              return _this9.changeControlKey('device_type', 'enabled');
            }
          }), currentControlKey(advgbBlockControls, 'device_type', 'enabled') && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement("div", {
            style: {
              paddingLeft: '17%'
            }
          }, /*#__PURE__*/React.createElement(ToggleControl, {
            label: __('Desktop', 'advanced-gutenberg'),
            checked: currentControlKey(advgbBlockControls, 'device_type', 'devices').includes('desktop'),
            onChange: function onChange() {
              var devices = currentControlKey(advgbBlockControls, 'device_type', 'devices');
              var newDevices = devices.includes('desktop') ? devices.filter(function (d) {
                return d !== 'desktop';
              }) : [].concat(_toConsumableArray(devices), ['desktop']);
              _this9.changeControlKey('device_type', 'devices', newDevices);
            }
          }), /*#__PURE__*/React.createElement(ToggleControl, {
            label: __('Tablet', 'advanced-gutenberg'),
            checked: currentControlKey(advgbBlockControls, 'device_type', 'devices').includes('tablet'),
            onChange: function onChange() {
              var devices = currentControlKey(advgbBlockControls, 'device_type', 'devices');
              var newDevices = devices.includes('tablet') ? devices.filter(function (d) {
                return d !== 'tablet';
              }) : [].concat(_toConsumableArray(devices), ['tablet']);
              _this9.changeControlKey('device_type', 'devices', newDevices);
            }
          }), /*#__PURE__*/React.createElement(ToggleControl, {
            label: __('Mobile', 'advanced-gutenberg'),
            checked: currentControlKey(advgbBlockControls, 'device_type', 'devices').includes('mobile'),
            onChange: function onChange() {
              var devices = currentControlKey(advgbBlockControls, 'device_type', 'devices');
              var newDevices = devices.includes('mobile') ? devices.filter(function (d) {
                return d !== 'mobile';
              }) : [].concat(_toConsumableArray(devices), ['mobile']);
              _this9.changeControlKey('device_type', 'devices', newDevices);
            }
          }), /*#__PURE__*/React.createElement(ToggleControl, {
            label: __('Bot', 'advanced-gutenberg'),
            checked: currentControlKey(advgbBlockControls, 'device_type', 'devices').includes('robot'),
            onChange: function onChange() {
              var devices = currentControlKey(advgbBlockControls, 'device_type', 'devices');
              var newDevices = devices.includes('robot') ? devices.filter(function (d) {
                return d !== 'robot';
              }) : [].concat(_toConsumableArray(devices), ['robot']);
              _this9.changeControlKey('device_type', 'devices', newDevices);
            }
          })))), isControlEnabled(advgb_block_controls_vars.controls.browser_device) && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(ToggleControl, {
            label: __('Browser', 'advanced-gutenberg'),
            help: currentControlKey(advgbBlockControls, 'browser_device', 'enabled') ? __('Choose which browsers can see this block.', 'advanced-gutenberg') : '',
            checked: currentControlKey(advgbBlockControls, 'browser_device', 'enabled'),
            onChange: function onChange() {
              return _this9.changeControlKey('browser_device', 'enabled');
            }
          }), currentControlKey(advgbBlockControls, 'browser_device', 'enabled') && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(FormTokenField, {
            multiple: true,
            label: __('Select Browsers', 'advanced-gutenberg'),
            placeholder: __('Search browsers', 'advanced-gutenberg'),
            suggestions: getOptionSuggestions(this.getBrowserOptions()),
            maxSuggestions: 10,
            value: getOptionTitles(currentControlKey(advgbBlockControls, 'browser_device', 'browsers') || [], this.getBrowserOptions()),
            onChange: function onChange(value) {
              _this9.changeControlKey('browser_device', 'browsers', getOptionSlugs(value, _this9.getBrowserOptions()));
            },
            __experimentalExpandOnFocus: true
          }), /*#__PURE__*/React.createElement(SelectControl, {
            label: __('Approach', 'advanced-gutenberg'),
            value: currentControlKey(advgbBlockControls, 'browser_device', 'approach'),
            options: [{
              label: __('Show to selected browsers', 'advanced-gutenberg'),
              value: 'include'
            }, {
              label: __('Hide from selected browsers', 'advanced-gutenberg'),
              value: 'exclude'
            }],
            onChange: function onChange(value) {
              return _this9.changeControlKey('browser_device', 'approach', value);
            }
          }))), isControlEnabled(advgb_block_controls_vars.controls.operating_system) && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(ToggleControl, {
            label: __('Operating System', 'advanced-gutenberg'),
            help: currentControlKey(advgbBlockControls, 'operating_system', 'enabled') ? __('Choose which operating systems can see this block.', 'advanced-gutenberg') : '',
            checked: currentControlKey(advgbBlockControls, 'operating_system', 'enabled'),
            onChange: function onChange() {
              return _this9.changeControlKey('operating_system', 'enabled');
            }
          }), currentControlKey(advgbBlockControls, 'operating_system', 'enabled') && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(FormTokenField, {
            multiple: true,
            label: __('Select Operating Systems', 'advanced-gutenberg'),
            placeholder: __('Search operating systems', 'advanced-gutenberg'),
            suggestions: getOptionSuggestions(this.getOperatingSystemOptions()),
            maxSuggestions: 10,
            value: getOptionTitles(currentControlKey(advgbBlockControls, 'operating_system', 'systems') || [], this.getOperatingSystemOptions()),
            onChange: function onChange(value) {
              _this9.changeControlKey('operating_system', 'systems', getOptionSlugs(value, _this9.getOperatingSystemOptions()));
            },
            __experimentalExpandOnFocus: true
          }), /*#__PURE__*/React.createElement(SelectControl, {
            label: __('Approach', 'advanced-gutenberg'),
            value: currentControlKey(advgbBlockControls, 'operating_system', 'approach'),
            options: [{
              label: __('Show to selected OS', 'advanced-gutenberg'),
              value: 'include'
            }, {
              label: __('Hide from selected OS', 'advanced-gutenberg'),
              value: 'exclude'
            }],
            onChange: function onChange(value) {
              return _this9.changeControlKey('operating_system', 'approach', value);
            }
          }))), isControlEnabled(advgb_block_controls_vars.controls.cookie) && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(ToggleControl, {
            label: __('Cookie', 'advanced-gutenberg'),
            help: currentControlKey(advgbBlockControls, 'cookie', 'enabled') ? __('Show or hide block based on cookie values.', 'advanced-gutenberg') : '',
            checked: currentControlKey(advgbBlockControls, 'cookie', 'enabled'),
            onChange: function onChange() {
              return _this9.changeControlKey('cookie', 'enabled');
            }
          }), currentControlKey(advgbBlockControls, 'cookie', 'enabled') && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(TextControl, {
            label: __('Cookie Name', 'advanced-gutenberg'),
            value: currentControlKey(advgbBlockControls, 'cookie', 'name') || '',
            onChange: function onChange(value) {
              return _this9.changeControlKey('cookie', 'name', value);
            }
          }), /*#__PURE__*/React.createElement(SelectControl, {
            label: __('Condition', 'advanced-gutenberg'),
            value: currentControlKey(advgbBlockControls, 'cookie', 'condition') || '=',
            options: this.getConditionOptions(),
            onChange: function onChange(value) {
              return _this9.changeControlKey('cookie', 'condition', value);
            }
          }), /*#__PURE__*/React.createElement(TextControl, {
            label: __('Value', 'advanced-gutenberg'),
            value: currentControlKey(advgbBlockControls, 'cookie', 'value') || '',
            onChange: function onChange(value) {
              return _this9.changeControlKey('cookie', 'value', value);
            }
          }), /*#__PURE__*/React.createElement(SelectControl, {
            label: __('Approach', 'advanced-gutenberg'),
            value: currentControlKey(advgbBlockControls, 'cookie', 'approach'),
            options: [{
              label: __('Show block when condition matches', 'advanced-gutenberg'),
              value: 'include'
            }, {
              label: __('Hide block when condition matches', 'advanced-gutenberg'),
              value: 'exclude'
            }],
            onChange: function onChange(value) {
              return _this9.changeControlKey('cookie', 'approach', value);
            }
          }))), isControlEnabled(advgb_block_controls_vars.controls.user_meta) && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(ToggleControl, {
            label: __('User Meta', 'advanced-gutenberg'),
            help: currentControlKey(advgbBlockControls, 'user_meta', 'enabled') ? __('Show or hide block based on user meta values.', 'advanced-gutenberg') : '',
            checked: currentControlKey(advgbBlockControls, 'user_meta', 'enabled'),
            onChange: function onChange() {
              return _this9.changeControlKey('user_meta', 'enabled');
            }
          }), currentControlKey(advgbBlockControls, 'user_meta', 'enabled') && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(TextControl, {
            label: __('Meta Key', 'advanced-gutenberg'),
            value: currentControlKey(advgbBlockControls, 'user_meta', 'key') || '',
            onChange: function onChange(value) {
              return _this9.changeControlKey('user_meta', 'key', value);
            }
          }), /*#__PURE__*/React.createElement(SelectControl, {
            label: __('Condition', 'advanced-gutenberg'),
            value: currentControlKey(advgbBlockControls, 'user_meta', 'condition') || '=',
            options: this.getConditionOptions(),
            onChange: function onChange(value) {
              return _this9.changeControlKey('user_meta', 'condition', value);
            }
          }), /*#__PURE__*/React.createElement(TextControl, {
            label: __('Value', 'advanced-gutenberg'),
            value: currentControlKey(advgbBlockControls, 'user_meta', 'value') || '',
            onChange: function onChange(value) {
              return _this9.changeControlKey('user_meta', 'value', value);
            }
          }), /*#__PURE__*/React.createElement(SelectControl, {
            label: __('Approach', 'advanced-gutenberg'),
            value: currentControlKey(advgbBlockControls, 'user_meta', 'approach'),
            options: [{
              label: __('Show block when condition matches', 'advanced-gutenberg'),
              value: 'include'
            }, {
              label: __('Hide block when condition matches', 'advanced-gutenberg'),
              value: 'exclude'
            }],
            onChange: function onChange(value) {
              return _this9.changeControlKey('user_meta', 'approach', value);
            }
          }))), isControlEnabled(advgb_block_controls_vars.controls.post_meta) && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(ToggleControl, {
            label: __('Post Meta', 'advanced-gutenberg'),
            help: currentControlKey(advgbBlockControls, 'post_meta', 'enabled') ? __('Show or hide block based on post meta values.', 'advanced-gutenberg') : '',
            checked: currentControlKey(advgbBlockControls, 'post_meta', 'enabled'),
            onChange: function onChange() {
              return _this9.changeControlKey('post_meta', 'enabled');
            }
          }), currentControlKey(advgbBlockControls, 'post_meta', 'enabled') && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(TextControl, {
            label: __('Meta Key', 'advanced-gutenberg'),
            value: currentControlKey(advgbBlockControls, 'post_meta', 'key') || '',
            onChange: function onChange(value) {
              return _this9.changeControlKey('post_meta', 'key', value);
            }
          }), /*#__PURE__*/React.createElement(SelectControl, {
            label: __('Condition', 'advanced-gutenberg'),
            value: currentControlKey(advgbBlockControls, 'post_meta', 'condition') || '=',
            options: this.getConditionOptions(),
            onChange: function onChange(value) {
              return _this9.changeControlKey('post_meta', 'condition', value);
            }
          }), /*#__PURE__*/React.createElement(TextControl, {
            label: __('Value', 'advanced-gutenberg'),
            value: currentControlKey(advgbBlockControls, 'post_meta', 'value') || '',
            onChange: function onChange(value) {
              return _this9.changeControlKey('post_meta', 'value', value);
            }
          }), /*#__PURE__*/React.createElement(SelectControl, {
            label: __('Approach', 'advanced-gutenberg'),
            value: currentControlKey(advgbBlockControls, 'post_meta', 'approach'),
            options: [{
              label: __('Show block when condition matches', 'advanced-gutenberg'),
              value: 'include'
            }, {
              label: __('Hide block when condition matches', 'advanced-gutenberg'),
              value: 'exclude'
            }],
            onChange: function onChange(value) {
              return _this9.changeControlKey('post_meta', 'approach', value);
            }
          }))), isControlEnabled(advgb_block_controls_vars.controls.query_string) && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(ToggleControl, {
            label: __('Query String', 'advanced-gutenberg'),
            help: currentControlKey(advgbBlockControls, 'query_string', 'enabled') ? __('Show or hide block based on URL query parameters.', 'advanced-gutenberg') : '',
            checked: currentControlKey(advgbBlockControls, 'query_string', 'enabled'),
            onChange: function onChange() {
              return _this9.changeControlKey('query_string', 'enabled');
            }
          }), currentControlKey(advgbBlockControls, 'query_string', 'enabled') && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(TextareaControl, {
            label: __('Query Parameters', 'advanced-gutenberg'),
            help: __('Enter query parameter names, one per line', 'advanced-gutenberg'),
            value: queriesValue,
            onChange: function onChange(value) {
              _this9.changeControlKey('query_string', 'queries', value);
            },
            placeholder: __('utm_source\nutm_medium\nref', 'advanced-gutenberg')
          }), /*#__PURE__*/React.createElement(SelectControl, {
            label: __('Logic', 'advanced-gutenberg'),
            value: currentControlKey(advgbBlockControls, 'query_string', 'logic') || 'all',
            options: [{
              label: __('All parameters must be present', 'advanced-gutenberg'),
              value: 'all'
            }, {
              label: __('Any parameter must be present', 'advanced-gutenberg'),
              value: 'any'
            }],
            onChange: function onChange(value) {
              return _this9.changeControlKey('query_string', 'logic', value);
            }
          }), /*#__PURE__*/React.createElement(SelectControl, {
            label: __('Approach', 'advanced-gutenberg'),
            value: currentControlKey(advgbBlockControls, 'query_string', 'approach'),
            options: [{
              label: __('Show block when condition matches', 'advanced-gutenberg'),
              value: 'include'
            }, {
              label: __('Hide block when condition matches', 'advanced-gutenberg'),
              value: 'exclude'
            }],
            onChange: function onChange(value) {
              return _this9.changeControlKey('query_string', 'approach', value);
            }
          }))), isControlEnabled(advgb_block_controls_vars.controls.capabilities) && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(ToggleControl, {
            label: __('Capabilities', 'advanced-gutenberg'),
            help: currentControlKey(advgbBlockControls, 'capabilities', 'enabled') ? __('Show or hide block based on user capabilities.', 'advanced-gutenberg') : '',
            checked: currentControlKey(advgbBlockControls, 'capabilities', 'enabled'),
            onChange: function onChange() {
              return _this9.changeControlKey('capabilities', 'enabled');
            }
          }), currentControlKey(advgbBlockControls, 'capabilities', 'enabled') && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(FormTokenField, {
            multiple: true,
            label: __('Select Capabilities', 'advanced-gutenberg'),
            placeholder: __('Search capabilities', 'advanced-gutenberg'),
            suggestions: getOptionSuggestions(this.getCapabilitiesOptions()),
            maxSuggestions: 10,
            value: getOptionTitles(currentControlKey(advgbBlockControls, 'capabilities', 'capabilities') || [], this.getCapabilitiesOptions()),
            onChange: function onChange(value) {
              _this9.changeControlKey('capabilities', 'capabilities', getOptionSlugs(value, _this9.getCapabilitiesOptions()));
            },
            __experimentalExpandOnFocus: true
          }), /*#__PURE__*/React.createElement(SelectControl, {
            label: __('Approach', 'advanced-gutenberg'),
            value: currentControlKey(advgbBlockControls, 'capabilities', 'approach'),
            options: [{
              label: __('Show to users with selected capabilities', 'advanced-gutenberg'),
              value: 'include'
            }, {
              label: __('Hide from users with selected capabilities', 'advanced-gutenberg'),
              value: 'exclude'
            }],
            onChange: function onChange(value) {
              return _this9.changeControlKey('capabilities', 'approach', value);
            }
          }))), !this.isPost() &&
          /*#__PURE__*/
          // Disabled in post edit
          React.createElement(Fragment, null, isControlEnabled(advgb_block_controls_vars.controls.archive) && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(ToggleControl, {
            label: __('Term archives', 'advanced-gutenberg'),
            help: currentControlKey(advgbBlockControls, 'archive', 'enabled') ? __('Choose on which taxonomies and terms archive pages your blocks can be displayed.', 'advanced-gutenberg') : '',
            checked: currentControlKey(advgbBlockControls, 'archive', 'enabled'),
            onChange: function onChange() {
              return _this9.changeControlKey('archive', 'enabled');
            }
          }), currentControlKey(advgbBlockControls, 'archive', 'enabled') && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement("div", {
            className: "advgb-revert-mb--disabled",
            style: {
              marginBottom: 20
            }
          }, /*#__PURE__*/React.createElement(SelectControl, {
            value: currentControlKey(advgbBlockControls, 'archive', 'approach'),
            options: [{
              value: 'include',
              label: __('Show for selected terms', 'advanced-gutenberg')
            }, {
              value: 'exclude',
              label: __('Hide for selected terms', 'advanced-gutenberg')
            }],
            onChange: function onChange(value) {
              return _this9.changeControlKey('archive', 'approach', value);
            }
          })), /*#__PURE__*/React.createElement(FormTokenField, {
            multiple: true,
            label: __('Select taxonomies', 'advanced-gutenberg'),
            placeholder: __('Search taxonomies', 'advanced-gutenberg'),
            suggestions: getOptionSuggestions(this.state.taxModOptions || this.getTaxonomies()),
            maxSuggestions: 10,
            value: getOptionTitles(this.currentArchiveControl('taxonomies'), this.state.taxModOptions || this.getTaxonomies()),
            onChange: function onChange(value) {
              _this9.changeArchiveControl('taxonomies', getOptionSlugs(value, _this9.state.taxModOptions || _this9.getTaxonomies()));
            },
            __experimentalExpandOnFocus: true
          }), currentControlKey(advgbBlockControls, 'archive', 'taxonomies').length > 0 && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(FormTokenField, {
            multiple: true,
            label: __('Filter terms', 'advanced-gutenberg'),
            placeholder: __('Search terms', 'advanced-gutenberg'),
            suggestions: getOptionSuggestions(this.state.termOptions),
            maxSuggestions: 10,
            value: getOptionTitles(this.currentArchiveControl('terms'), this.state.termOptions),
            onChange: function onChange(value) {
              _this9.changeArchiveControl('terms', getOptionSlugs(value, _this9.state.termOptions));
              _this9.setState({
                updateTaxLabels: true
              });
            },
            onInputChange: function onInputChange(value) {
              _this9.setState({
                searchTermWord: value
              });
            },
            __experimentalShowHowTo: false
          }), /*#__PURE__*/React.createElement("div", {
            className: "advgb-revert-mb--disabled components-form-token-field__help",
            style: {
              marginBottom: 20
            }
          }, __('Use this filter to apply only to some terms.', 'advanced-gutenberg'))))), isControlEnabled(advgb_block_controls_vars.controls.page) && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(ToggleControl, {
            label: __('Pages', 'advanced-gutenberg'),
            help: currentControlKey(advgbBlockControls, 'page', 'enabled') ? __('Choose in which pages this block can be displayed.', 'advanced-gutenberg') : '',
            checked: currentControlKey(advgbBlockControls, 'page', 'enabled'),
            onChange: function onChange() {
              return _this9.changeControlKey('page', 'enabled');
            }
          }), currentControlKey(advgbBlockControls, 'page', 'enabled') && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement("div", {
            className: "advgb-revert-mb"
          }, /*#__PURE__*/React.createElement(SelectControl, {
            value: currentControlKey(advgbBlockControls, 'page', 'approach'),
            options: [{
              value: 'include',
              label: __('Show on the selected pages', 'advanced-gutenberg')
            }, {
              value: 'exclude',
              label: __('Hide on the selected pages', 'advanced-gutenberg')
            }],
            onChange: function onChange(value) {
              return _this9.changeControlKey('page', 'approach', value);
            }
          })), (currentControlKey(advgbBlockControls, 'page', 'approach') === 'include' || currentControlKey(advgbBlockControls, 'page', 'approach') === 'exclude') && /*#__PURE__*/React.createElement(FormTokenField, {
            multiple: true,
            label: __('Select pages', 'advanced-gutenberg'),
            placeholder: __('Search', 'advanced-gutenberg'),
            suggestions: getOptionSuggestions(this.getPages()),
            maxSuggestions: 10,
            value: getOptionTitles(!!currentControlKey(advgbBlockControls, 'page', 'pages') ? currentControlKey(advgbBlockControls, 'page', 'pages') : [], this.getPages()),
            onChange: function onChange(value) {
              _this9.changeControlKey('page', 'pages', getOptionSlugs(value, _this9.getPages()));
            },
            __experimentalExpandOnFocus: true
          })))));
        }
      }, {
        key: "renderPresetControls",
        value: function renderPresetControls(advgbBlockControls) {
          var _this0 = this;
          return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement("div", {
            className: "advgb-preset-buttons"
          }, /*#__PURE__*/React.createElement(Button, {
            isPrimary: true,
            onClick: function onClick() {
              _this0.setState({
                showPresetModal: true,
                modalMode: 'create'
              });
            }
          }, __('Manage Presets', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement(FormTokenField, {
            multiple: true,
            label: __('Select Presets', 'advanced-gutenberg'),
            placeholder: __('Search presets', 'advanced-gutenberg'),
            suggestions: getOptionSuggestions(this.getPresetOptions()),
            maxSuggestions: 10,
            value: getOptionTitles(currentControlKey(advgbBlockControls, 'presets', 'selected') || [], this.getPresetOptions()),
            onChange: function onChange(value) {
              _this0.changeControlKey('presets', 'selected', getOptionSlugs(value, _this0.getPresetOptions()));
              if (!_this0.props.attributes.advgbBlockControls.find(function (control) {
                return control.control === 'presets';
              })) {
                _this0.changeControlKey('presets', 'enabled', true);
              }
            },
            __experimentalExpandOnFocus: true
          }), /*#__PURE__*/React.createElement(SelectControl, {
            label: __('Preset Logic', 'advanced-gutenberg'),
            value: currentControlKey(advgbBlockControls, 'presets', 'logic') || 'any',
            options: [{
              label: __('Show if ANY preset matches', 'advanced-gutenberg'),
              value: 'any'
            }, {
              label: __('Show if ALL presets match', 'advanced-gutenberg'),
              value: 'all'
            }],
            onChange: function onChange(value) {
              return _this0.changeControlKey('presets', 'logic', value);
            }
          }), this.state.showPresetModal && this.renderPresetModal());
        }
      }, {
        key: "renderPresetModal",
        value: function renderPresetModal() {
          var _this1 = this;
          return /*#__PURE__*/React.createElement(Modal, {
            title: __('Block Control Presets', 'advanced-gutenberg'),
            onRequestClose: function onRequestClose() {
              return _this1.setState({
                showPresetModal: false
              });
            },
            className: "advgb-preset-modal",
            isDismissible: true,
            isFullScreen: true,
            shouldCloseOnClickOutside: false,
            shouldCloseOnEsc: true
          }, window.AdvGBPresetManager && wp.element.createElement(window.AdvGBPresetManager, {
            isModal: false
          }));
        }
      }, {
        key: "getPresetOptions",
        value: function getPresetOptions() {
          var presets = window.AdvGBPresetData ? window.AdvGBPresetData.getAllPresets() : [];
          return presets.map(function (preset) {
            return {
              slug: preset.id,
              title: preset.title
            };
          });
        }
      }, {
        key: "getActiveTab",
        value: function getActiveTab() {
          var attributes = this.props.attributes;
          var advgbBlockControls = attributes.advgbBlockControls;
          return currentControlKey(advgbBlockControls, 'meta', 'activeTab') || 'custom';
        }
      }, {
        key: "setActiveTab",
        value: function setActiveTab(tabName) {
          this.changeControlKey('meta', 'activeTab', tabName);
        }
      }, {
        key: "render",
        value: function render() {
          var _this10 = this;
          var _this$props5 = this.props,
            attributes = _this$props5.attributes,
            setAttributes = _this$props5.setAttributes;
          var advgbBlockControls = attributes.advgbBlockControls;
          return [this.props.isSelected && !NON_SUPPORTED_BLOCKS.includes(this.props.name) && isAnyControlEnabledGlobal() && /*#__PURE__*/React.createElement(InspectorControls, {
            key: "advgb-bc-controls"
          }, /*#__PURE__*/React.createElement(PanelBody, {
            title: __('Block Controls', 'advanced-gutenberg'),
            icon: "visibility",
            initialOpen: false,
            className: isAnyControlEnabledBlock(advgbBlockControls) ? 'advgb-feature-icon-active' : ''
          }, /*#__PURE__*/React.createElement(TabPanel, {
            className: "advgb-block-controls-tabs",
            activeClass: "active-tab",
            initialTabName: this.getActiveTab(),
            onSelect: function onSelect(tabName) {
              return _this10.setActiveTab(tabName);
            },
            tabs: [{
              name: 'custom',
              title: __('Custom Rules', 'advanced-gutenberg'),
              className: 'tab-custom'
            }, {
              name: 'presets',
              title: __('Presets', 'advanced-gutenberg'),
              className: 'tab-presets'
            }]
          }, function (tab) {
            return _this10.renderTabContent(tab.name);
          }))), /*#__PURE__*/React.createElement(BlockEdit, _extends({
            key: "block-edit-advgb-dates"
          }, this.props))];
        }
      }]);
    }(Component);
  }, 'withEditControls');

  // Add option to add controls for supported blocks
  addFilter('editor.BlockEdit', 'advgb/addBlockControls', withEditControls);
  var withAttributes = createHigherOrderComponent(function (BlockListBlock) {
    return function (props) {
      if (!NON_SUPPORTED_BLOCKS.includes(props.name) && hasBlockSupport(props.name, 'advgb/blockControls', true) && isAnyControlEnabledGlobal()) {
        var advgbBlockControls = props.attributes.advgbBlockControls;
        var advgbBcClass = props.isSelected === false && isAnyControlEnabledBlock(advgbBlockControls) ? 'advgb-bc-editor-preview' : '';
        return /*#__PURE__*/React.createElement(BlockListBlock, _extends({}, props, {
          className: classnames(props.className, advgbBcClass),
          advgbBlockControls: "".concat(advgbBlockControls)
        }));
      }
      return /*#__PURE__*/React.createElement(BlockListBlock, props);
    };
  }, 'withAttributes');

  // Apply attributes and CSS classes on backend
  addFilter('editor.BlockListBlock', 'advgb/loadBackendBlockControls', withAttributes);
})(wp.i18n, wp.hooks, wp.blocks, wp.blockEditor, wp.components, wp.compose, wp.element);
})();

/******/ })()
;
//# sourceMappingURL=block-controls.js.map
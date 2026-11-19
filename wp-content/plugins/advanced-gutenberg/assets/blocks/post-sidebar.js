/******/ (() => { // webpackBootstrap
/*!*******************************************************!*\
  !*** ./assets/blocks/editor-sidebar/post-sidebar.jsx ***!
  \*******************************************************/
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
(function (wpI18n, wpPlugins, wpElement, wpData, wpComponents) {
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
  var compose = wp.compose.compose;
  var PluginSidebar = wp.editor.PluginSidebar || wp.editPost.PluginSidebar;
  var PluginSidebarMoreMenuItem = wp.editor.PluginSidebarMoreMenuItem || wp.editPost.PluginSidebarMoreMenuItem;
  var sidebarName = "advgb-editor-sidebar";
  var sidebarTitle = __('PublishPress Blocks Settings', 'advanced-gutenberg');
  var sidebarIcon = "layout";
  var VISUAL_GUIDE_SETTINGS = [{
    label: __('Inherit from global settings', 'advanced-gutenberg'),
    value: ''
  }, {
    label: __('Enable', 'advanced-gutenberg'),
    value: 'enable'
  }, {
    label: __('Disable', 'advanced-gutenberg'),
    value: 'disable'
  }];
  var EDITOR_WIDTH_SETTINGS = [{
    label: __('Inherit from global settings', 'advanced-gutenberg'),
    value: ''
  }, {
    label: __('Original', 'advanced-gutenberg'),
    value: 'default'
  }, {
    label: __('Large', 'advanced-gutenberg'),
    value: 'large'
  }, {
    label: __('Full width', 'advanced-gutenberg'),
    value: 'full'
  }];
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
  var AdvSidebar = /*#__PURE__*/function (_Component) {
    function AdvSidebar() {
      _classCallCheck(this, AdvSidebar);
      return _callSuper(this, AdvSidebar, arguments);
    }
    _inherits(AdvSidebar, _Component);
    return _createClass(AdvSidebar, [{
      key: "onUpdateMeta",
      value: function onUpdateMeta(metaData) {
        var _this$props = this.props,
          metaValues = _this$props.metaValues,
          updateMetaField = _this$props.updateMetaField;
        var meta = _objectSpread(_objectSpread({}, metaValues), metaData);
        updateMetaField(meta);
        updateBodyClass();
      }
    }, {
      key: "render",
      value: function render() {
        var _this = this;
        var _this$props2 = this.props,
          columnsVisualGuide = _this$props2.columnsVisualGuide,
          editorWidth = _this$props2.editorWidth;
        return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement("div", {
          className: "advgb-editor-sidebar-note"
        }, __('These settings will override the PublishPress Blocks global settings.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement(PanelBody, {
          title: __('Editor width', 'advanced-gutenberg')
        }, /*#__PURE__*/React.createElement("div", {
          className: "advgb-editor-sidebar-note"
        }, __('Change your editor width', 'advanced-gutenberg')), /*#__PURE__*/React.createElement(ButtonGroup, {
          className: "advgb-button-group",
          "aria-label": __('Editor width', 'advanced-gutenberg'),
          role: "group"
        }, EDITOR_WIDTH_SETTINGS.map(function (setting, index) {
          return /*#__PURE__*/React.createElement(Button, {
            className: "advgb-button",
            key: index,
            isSecondary: setting.value !== editorWidth,
            isPrimary: setting.value === editorWidth,
            "aria-pressed": setting.value === editorWidth,
            onClick: function onClick() {
              return _this.onUpdateMeta({
                advgb_blocks_editor_width: setting.value
              });
            }
          }, setting.label);
        }))), advgbBlocks.enable_advgb_blocks !== undefined && advgbBlocks.enable_advgb_blocks === '1' && /*#__PURE__*/React.createElement(PanelBody, {
          title: __('Columns Visual Guide', 'advanced-gutenberg'),
          initialOpen: false
        }, /*#__PURE__*/React.createElement("div", {
          className: "advgb-editor-sidebar-note"
        }, __('Border to materialize PublishPress Blocks Column block', 'advanced-gutenberg')), /*#__PURE__*/React.createElement(ButtonGroup, {
          className: "advgb-button-group",
          "aria-label": __('Columns Visual Guide', 'advanced-gutenberg'),
          role: "group"
        }, VISUAL_GUIDE_SETTINGS.map(function (setting, index) {
          return /*#__PURE__*/React.createElement(Button, {
            className: "advgb-button",
            key: index,
            isSecondary: setting.value !== columnsVisualGuide,
            isPrimary: setting.value === columnsVisualGuide,
            "aria-pressed": setting.value === columnsVisualGuide,
            onClick: function onClick() {
              return _this.onUpdateMeta({
                advgb_blocks_columns_visual_guide: setting.value
              });
            }
          }, setting.label);
        }))));
      }
    }]);
  }(Component);
  var AdvSidebarRender = compose(withDispatch(function (dispatch) {
    return {
      updateMetaField: function updateMetaField(data) {
        dispatch('core/editor').editPost({
          meta: data
        });
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
      return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(PluginSidebarMoreMenuItem, {
        target: sidebarName,
        icon: sidebarIcon
      }, sidebarTitle), /*#__PURE__*/React.createElement(PluginSidebar, {
        name: sidebarName,
        title: sidebarTitle,
        icon: sidebarIcon
      }, /*#__PURE__*/React.createElement("div", {
        className: "advgb-editor-sidebar-content"
      }, /*#__PURE__*/React.createElement(AdvSidebarRender, null))));
    }
  });
})(wp.i18n, wp.plugins, wp.element, wp.data, wp.components);
/******/ })()
;
//# sourceMappingURL=post-sidebar.js.map
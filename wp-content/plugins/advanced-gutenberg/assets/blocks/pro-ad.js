/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/blocks/pro-ad/accordion-promo.jsx"
/*!**************************************************!*\
  !*** ./assets/blocks/pro-ad/accordion-promo.jsx ***!
  \**************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./shared/index.jsx */ "./assets/blocks/pro-ad/shared/index.jsx");

;

// Accordion blocks promo
(0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.addFilter)('editor.BlockEdit', 'advgb/advAccordionsPro', function (BlockEdit) {
  return function (props) {
    return /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement(BlockEdit, props), props.isSelected && props.name === 'advgb/accordions' && /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.InspectorControls, null, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PanelBody, {
      title: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Theme Settings', 'advanced-gutenberg'),
      className: "advgb-pro-icon",
      initialOpen: false
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.SelectControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Preset', 'advanced-gutenberg'),
      value: "default",
      options: [{
        label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Default', 'advanced-gutenberg'),
        value: 'default'
      }]
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Notice, {
      className: "advgb-notice-sidebar",
      status: "warning",
      isDismissible: false
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Selecting or resetting a preset will override your current design configuration.', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-wrap"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-text advgb-tooltips ppb-tooltips-library click",
      "data-toggle": "ppbtooltip",
      "data-placement": "top"
    }, /*#__PURE__*/React.createElement("span", {
      className: "advgb-promo-text"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Pro', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
      className: "tooltip-text"
    }, /*#__PURE__*/React.createElement("p", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('PublishPress Blocks Pro supports several pre-built designs for accordions.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
      className: "clickable",
      href: _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PROMO_LINK,
      target: "_blank"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null)))))), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PanelBody, {
      title: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Accordion Settings', 'advanced-gutenberg'),
      initialOpen: false,
      className: "advgb-pro-icon"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.RangeControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Font Size', 'advanced-gutenberg'),
      value: "",
      min: 7,
      max: 50,
      beforeIcon: "editor-textcolor",
      allowReset: true
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.SelectControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Font Family', 'advanced-gutenberg'),
      value: "",
      options: [{
        label: 'Default',
        value: ''
      }]
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.SelectControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Font Weight + Style', 'advanced-gutenberg'),
      value: "",
      options: [{
        label: 'Default',
        value: ''
      }]
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.SelectControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Text Transform', 'advanced-gutenberg'),
      value: "",
      options: [{
        label: 'Default',
        value: ''
      }]
    })), /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-wrap"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-text advgb-tooltips ppb-tooltips-library click",
      "data-toggle": "ppbtooltip",
      "data-placement": "top"
    }, /*#__PURE__*/React.createElement("span", {
      className: "advgb-promo-text"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Pro', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
      className: "tooltip-text"
    }, /*#__PURE__*/React.createElement("p", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('PublishPress Blocks Pro supports Google Fonts in accordions.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
      className: "clickable",
      href: _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PROMO_LINK,
      target: "_blank"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null))))))), /*#__PURE__*/React.createElement(React.Fragment, null)));
  };
});

/***/ },

/***/ "./assets/blocks/pro-ad/button-promo.jsx"
/*!***********************************************!*\
  !*** ./assets/blocks/pro-ad/button-promo.jsx ***!
  \***********************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./shared/index.jsx */ "./assets/blocks/pro-ad/shared/index.jsx");

;

// Button block promo
(0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.addFilter)('editor.BlockEdit', 'advgb/advButtonPro', function (BlockEdit) {
  return function (props) {
    return /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement(BlockEdit, props), props.isSelected && props.name === 'advgb/button' && /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.InspectorControls, null, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PanelBody, {
      title: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Font', 'advanced-gutenberg'),
      initialOpen: false,
      className: "advgb-pro-icon"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvFontControl, null)), /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-wrap"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-text advgb-tooltips ppb-tooltips-library click",
      "data-toggle": "ppbtooltip",
      "data-placement": "top"
    }, /*#__PURE__*/React.createElement("span", {
      className: "advgb-promo-text"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Pro', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
      className: "tooltip-text"
    }, /*#__PURE__*/React.createElement("p", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('PublishPress Blocks Pro supports Google Fonts in buttons.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
      className: "clickable",
      href: _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PROMO_LINK,
      target: "_blank"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null)))))), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PanelBody, {
      title: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Link rel', 'advanced-gutenberg'),
      initialOpen: false,
      className: "advgb-pro-icon"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.ToggleControl, {
      label: "noreferrer",
      checked: props.attributes.noreferrer
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.ToggleControl, {
      label: "nofollow",
      checked: props.attributes.nofollow
    })), /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-wrap"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-text advgb-tooltips ppb-tooltips-library click",
      "data-toggle": "ppbtooltip",
      "data-placement": "top"
    }, /*#__PURE__*/React.createElement("span", {
      className: "advgb-promo-text"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Pro', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
      className: "tooltip-text"
    }, /*#__PURE__*/React.createElement("p", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('PublishPress Blocks Pro includes nofollow and noreferrer support.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
      className: "clickable",
      href: _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PROMO_LINK,
      target: "_blank"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null))))))), /*#__PURE__*/React.createElement(React.Fragment, null)));
  };
});

/***/ },

/***/ "./assets/blocks/pro-ad/core-blocks-promo.jsx"
/*!****************************************************!*\
  !*** ./assets/blocks/pro-ad/core-blocks-promo.jsx ***!
  \****************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./shared/index.jsx */ "./assets/blocks/pro-ad/shared/index.jsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }

;

// core blocks promo
(0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.addFilter)('editor.BlockEdit', 'advgb/coreBlocks', function (BlockEdit) {
  return function (props) {
    return /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement(BlockEdit, _extends({
      key: "block-edit-fonts"
    }, props)), advgbProAdI18n.fontStylesEnabled && props.isSelected && _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.CORE_SUPPORTED_BLOCKS.includes(props.name) && /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.InspectorControls, null, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PanelBody, {
      title: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Font Settings', 'advanced-gutenberg'),
      initialOpen: false,
      className: "advgb-pro-icon"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.SelectControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Font Family', 'advanced-gutenberg'),
      value: "",
      options: [{
        label: 'Default',
        value: ''
      }]
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.SelectControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Font Weight + Style', 'advanced-gutenberg'),
      value: "",
      options: [{
        label: 'Default',
        value: ''
      }]
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.SelectControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Text Transform', 'advanced-gutenberg'),
      value: "",
      options: [{
        label: 'Default',
        value: ''
      }]
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.ToggleControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Force styling', 'advanced-gutenberg'),
      help: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('This adds !important to CSS in frontend to avoid your font being overridden', 'advanced-gutenberg'),
      checked: false,
      onChange: ""
    })), /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-wrap"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-text advgb-tooltips ppb-tooltips-library click",
      "data-toggle": "ppbtooltip",
      "data-placement": "top"
    }, /*#__PURE__*/React.createElement("span", {
      className: "advgb-promo-text"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Pro', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
      className: "tooltip-text"
    }, /*#__PURE__*/React.createElement("p", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('PublishPress Blocks Pro supports Google Fonts for paragraphs and headings.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
      className: "clickable",
      href: _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PROMO_LINK,
      target: "_blank"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null))))))));
  };
});

/***/ },

/***/ "./assets/blocks/pro-ad/count-up-promo.jsx"
/*!*************************************************!*\
  !*** ./assets/blocks/pro-ad/count-up-promo.jsx ***!
  \*************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./shared/index.jsx */ "./assets/blocks/pro-ad/shared/index.jsx");

;

// Count Up block promo
(0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.addFilter)('editor.BlockEdit', 'advgb/countUpPro', function (BlockEdit) {
  return function (props) {
    return /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement(BlockEdit, props), props.isSelected && props.name === 'advgb/count-up' && /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.BlockControls, null, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AlignmentToolbar, {
      value: props.attributes.align
    })), /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-wrap"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-text advgb-tooltips ppb-tooltips-library click",
      "data-toggle": "ppbtooltip",
      "data-placement": "top"
    }, /*#__PURE__*/React.createElement("span", {
      className: "advgb-promo-text"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Pro', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
      className: "tooltip-text"
    }, /*#__PURE__*/React.createElement("p", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('PublishPress Blocks Pro support Text alignment for Count Up block.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
      className: "clickable",
      href: _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PROMO_LINK,
      target: "_blank"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null)))))), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.InspectorControls, null, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PanelBody, {
      title: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Header & Description', 'advanced-gutenberg'),
      className: "advgb-pro-icon"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.RangeControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Header size', 'advanced-gutenberg'),
      value: props.attributes.headerSize || '',
      min: 10,
      max: 100,
      beforeIcon: "editor-textcolor",
      allowReset: true
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.RangeControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Description size', 'advanced-gutenberg'),
      value: props.attributes.descriptionSize || '',
      min: 10,
      max: 100,
      beforeIcon: "editor-textcolor",
      allowReset: true
    })), /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-wrap"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-text advgb-tooltips ppb-tooltips-library click",
      "data-toggle": "ppbtooltip",
      "data-placement": "top"
    }, /*#__PURE__*/React.createElement("span", {
      className: "advgb-promo-text"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Pro', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
      className: "tooltip-text"
    }, /*#__PURE__*/React.createElement("p", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('PublishPress Blocks Pro supports Custom header and description size for Count Up block.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
      className: "clickable",
      href: _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PROMO_LINK,
      target: "_blank"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null)))))))));
  };
});

/***/ },

/***/ "./assets/blocks/pro-ad/general-promo.jsx"
/*!************************************************!*\
  !*** ./assets/blocks/pro-ad/general-promo.jsx ***!
  \************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./shared/index.jsx */ "./assets/blocks/pro-ad/shared/index.jsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }

;

// Add Upgrade to Pro Ad in sidebar
(0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.addFilter)('editor.BlockEdit', 'advgb/proAd', function (BlockEdit) {
  return function (props) {
    return [props.isSelected && _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.SUPPORTED_BLOCKS.includes(props.name) && /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.InspectorControls, {
      key: "advgb-custom-controls"
    }, /*#__PURE__*/React.createElement("div", {
      className: "components-panel__body advgb-pro-ad-wrapper"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.sprintf)((0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Want more features in your %s blocks?', 'advanced-gutenberg'), (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.advgbGetBlockTitle)(props.name)), /*#__PURE__*/React.createElement("br", null), /*#__PURE__*/React.createElement("a", {
      href: _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PROMO_LINK,
      className: "advgb-pro-ad-btn",
      target: "_blank"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'advanced-gutenberg')))), /*#__PURE__*/React.createElement(BlockEdit, _extends({
      key: "block-edit-custom-class-name"
    }, props))];
  };
});

/***/ },

/***/ "./assets/blocks/pro-ad/image-promo.jsx"
/*!**********************************************!*\
  !*** ./assets/blocks/pro-ad/image-promo.jsx ***!
  \**********************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./shared/index.jsx */ "./assets/blocks/pro-ad/shared/index.jsx");

;

// Image block promo
(0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.addFilter)('editor.BlockEdit', 'advgb/advImagePro', function (BlockEdit) {
  return function (props) {
    return /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement(BlockEdit, props), props.isSelected && props.name === 'advgb/image' && /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.InspectorControls, null, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PanelBody, {
      title: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Content Tags', 'advanced-gutenberg'),
      className: "advgb-pro-icon"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.SelectControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Title tag', 'advanced-gutenberg'),
      value: props.attributes.titleTag,
      options: [{
        label: 'h1',
        value: 'h1'
      }, {
        label: 'h2',
        value: 'h2'
      }, {
        label: 'h3',
        value: 'h3'
      }, {
        label: 'h4',
        value: 'h4'
      }, {
        label: 'h5',
        value: 'h5'
      }, {
        label: 'h6',
        value: 'h6'
      }, {
        label: 'p',
        value: 'p'
      }, {
        label: 'div',
        value: 'div'
      }]
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.SelectControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Subtitle tag', 'advanced-gutenberg'),
      value: props.attributes.subtitleTag,
      options: [{
        label: 'h1',
        value: 'h1'
      }, {
        label: 'h2',
        value: 'h2'
      }, {
        label: 'h3',
        value: 'h3'
      }, {
        label: 'h4',
        value: 'h4'
      }, {
        label: 'h5',
        value: 'h5'
      }, {
        label: 'h6',
        value: 'h6'
      }, {
        label: 'p',
        value: 'p'
      }, {
        label: 'div',
        value: 'div'
      }]
    })), /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-wrap"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-text advgb-tooltips ppb-tooltips-library click",
      "data-toggle": "ppbtooltip",
      "data-placement": "top"
    }, /*#__PURE__*/React.createElement("span", {
      className: "advgb-promo-text"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Pro', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
      className: "tooltip-text"
    }, /*#__PURE__*/React.createElement("p", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('PublishPress Blocks Pro supports different heading sizes for the image text.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
      className: "clickable",
      href: _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PROMO_LINK,
      target: "_blank"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null))))))), /*#__PURE__*/React.createElement(React.Fragment, null)));
  };
});

/***/ },

/***/ "./assets/blocks/pro-ad/image-slider-promo.jsx"
/*!*****************************************************!*\
  !*** ./assets/blocks/pro-ad/image-slider-promo.jsx ***!
  \*****************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./shared/index.jsx */ "./assets/blocks/pro-ad/shared/index.jsx");

;

// Images Slider block promo
(0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.addFilter)('editor.BlockEdit', 'advgb/imagesSliderPro', function (BlockEdit) {
  return function (props) {
    return /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement(BlockEdit, props), props.isSelected && props.name === 'advgb/images-slider' && /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.InspectorControls, null, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PanelBody, {
      title: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Autoplay', 'advanced-gutenberg'),
      initialOpen: false,
      className: "advgb-pro-icon"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.ToggleControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Autoplay', 'advanced-gutenberg'),
      checked: props.attributes.autoplay
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.RangeControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Autoplay Speed', 'advanced-gutenberg'),
      help: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Change interval between slides in miliseconds.', 'advanced-gutenberg'),
      min: 1000,
      max: 20000,
      value: props.attributes.autoplaySpeed
    })), /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-wrap"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-text advgb-tooltips ppb-tooltips-library click",
      "data-toggle": "ppbtooltip",
      "data-placement": "top"
    }, /*#__PURE__*/React.createElement("span", {
      className: "advgb-promo-text"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Pro', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
      className: "tooltip-text"
    }, /*#__PURE__*/React.createElement("p", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('PublishPress Blocks Pro supports AutoPlay and custom speed for slides.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
      className: "clickable",
      href: _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PROMO_LINK,
      target: "_blank"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null)))))), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PanelBody, {
      title: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Title', 'advanced-gutenberg'),
      initialOpen: false,
      className: "advgb-pro-icon"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.SelectControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Tag', 'advanced-gutenberg'),
      value: props.attributes.titleTag,
      options: [{
        label: 'h1',
        value: 'h1'
      }, {
        label: 'h2',
        value: 'h2'
      }, {
        label: 'h3',
        value: 'h3'
      }, {
        label: 'h4',
        value: 'h4'
      }, {
        label: 'h5',
        value: 'h5'
      }, {
        label: 'h6',
        value: 'h6'
      }, {
        label: 'p',
        value: 'p'
      }, {
        label: 'div',
        value: 'div'
      }]
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvFontControl, null), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.ToggleControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Display', 'advanced-gutenberg'),
      help: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Disable this if you want to include the Title as alt attribute without displaying the title over the image.', 'advanced-gutenberg'),
      checked: props.attributes.titleShow
    })), /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-wrap"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-text advgb-tooltips ppb-tooltips-library click",
      "data-toggle": "ppbtooltip",
      "data-placement": "top"
    }, /*#__PURE__*/React.createElement("span", {
      className: "advgb-promo-text"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Pro', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
      className: "tooltip-text"
    }, /*#__PURE__*/React.createElement("p", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('PublishPress Blocks Pro supports multiple style options for slide titles.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
      className: "clickable",
      href: _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PROMO_LINK,
      target: "_blank"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null))))), /*#__PURE__*/React.createElement(React.Fragment, null))), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PanelBody, {
      title: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Text', 'advanced-gutenberg'),
      initialOpen: false,
      className: "advgb-pro-icon"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.SelectControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Tag', 'advanced-gutenberg'),
      value: props.attributes.textTag,
      options: [{
        label: 'h1',
        value: 'h1'
      }, {
        label: 'h2',
        value: 'h2'
      }, {
        label: 'h3',
        value: 'h3'
      }, {
        label: 'h4',
        value: 'h4'
      }, {
        label: 'h5',
        value: 'h5'
      }, {
        label: 'h6',
        value: 'h6'
      }, {
        label: 'p',
        value: 'p'
      }, {
        label: 'div',
        value: 'div'
      }]
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvFontControl, null)), /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-wrap"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-text advgb-tooltips ppb-tooltips-library click",
      "data-toggle": "ppbtooltip",
      "data-placement": "top"
    }, /*#__PURE__*/React.createElement("span", {
      className: "advgb-promo-text"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Pro', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
      className: "tooltip-text"
    }, /*#__PURE__*/React.createElement("p", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('PublishPress Blocks Pro supports multiple style options for slide text.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
      className: "clickable",
      href: _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PROMO_LINK,
      target: "_blank"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null))))))), /*#__PURE__*/React.createElement(React.Fragment, null)));
  };
});

/***/ },

/***/ "./assets/blocks/pro-ad/list-promo.jsx"
/*!*********************************************!*\
  !*** ./assets/blocks/pro-ad/list-promo.jsx ***!
  \*********************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./shared/index.jsx */ "./assets/blocks/pro-ad/shared/index.jsx");

;

// List block promo
(0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.addFilter)('editor.BlockEdit', 'advgb/listPro', function (BlockEdit) {
  return function (props) {
    return /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement(BlockEdit, props), props.isSelected && props.name === 'advgb/list' && /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.InspectorControls, null, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PanelBody, {
      title: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Colors', 'advanced-gutenberg'),
      initialOpen: false,
      className: "advgb-pro-icon"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvColorControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Text Color', 'advanced-gutenberg'),
      value: props.attributes.textColor
    })), /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-wrap"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-text advgb-tooltips ppb-tooltips-library click",
      "data-toggle": "ppbtooltip",
      "data-placement": "top"
    }, /*#__PURE__*/React.createElement("span", {
      className: "advgb-promo-text"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Pro', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
      className: "tooltip-text"
    }, /*#__PURE__*/React.createElement("p", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('PublishPress Blocks Pro supports custom colors for the list text.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
      className: "clickable",
      href: _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PROMO_LINK,
      target: "_blank"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null))))))), /*#__PURE__*/React.createElement(React.Fragment, null)));
  };
});

/***/ },

/***/ "./assets/blocks/pro-ad/recent-posts-promo.jsx"
/*!*****************************************************!*\
  !*** ./assets/blocks/pro-ad/recent-posts-promo.jsx ***!
  \*****************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./shared/index.jsx */ "./assets/blocks/pro-ad/shared/index.jsx");

;

// Recent posts block promo
var recentPostsProSidebar = (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.createHigherOrderComponent)(function (BlockEdit) {
  return function (props) {
    return /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement(BlockEdit, props), props.isSelected && props.name === 'advgb/recent-posts' && /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.InspectorControls, null, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PanelBody, {
      title: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Slider View Autoplay', 'advanced-gutenberg'),
      initialOpen: false,
      className: "advgb-pro-icon"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.RangeControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Autoplay Speed', 'advanced-gutenberg'),
      help: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Change interval between slides in miliseconds.', 'advanced-gutenberg'),
      min: 1000,
      max: 20000,
      value: ""
    })), /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-wrap"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-text advgb-tooltips ppb-tooltips-library click",
      "data-toggle": "ppbtooltip",
      "data-placement": "top"
    }, /*#__PURE__*/React.createElement("span", {
      className: "advgb-promo-text"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Pro', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
      className: "tooltip-text"
    }, /*#__PURE__*/React.createElement("p", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('PublishPress Blocks Pro supports autoplay speed control for the slider view.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
      className: "clickable",
      href: _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PROMO_LINK,
      target: "_blank"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null)))))), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PanelBody, {
      title: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Title', 'advanced-gutenberg'),
      initialOpen: false,
      className: "advgb-pro-icon"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvFontControl, null), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.RangeControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Line height (em)', 'advanced-gutenberg'),
      step: 0.1,
      value: "",
      allowReset: true
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvColorControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Color', 'advanced-gutenberg'),
      value: ""
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvColorControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Color (hover)', 'advanced-gutenberg'),
      value: ""
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.RecentPostsEditTitle, null)), /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-wrap"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-text advgb-tooltips ppb-tooltips-library click",
      "data-toggle": "ppbtooltip",
      "data-placement": "top"
    }, /*#__PURE__*/React.createElement("span", {
      className: "advgb-promo-text"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Pro', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
      className: "tooltip-text"
    }, /*#__PURE__*/React.createElement("p", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('PublishPress Blocks Pro supports multiple style options for the content display.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
      className: "clickable",
      href: _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PROMO_LINK,
      target: "_blank"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null))))), /*#__PURE__*/React.createElement(React.Fragment, null))), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PanelBody, {
      title: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Featured Image', 'advanced-gutenberg'),
      initialOpen: false,
      className: "advgb-pro-icon"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.ToggleControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Custom image size', 'advanced-gutenberg'),
      checked: false
    }))), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.CheckboxControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('auto', 'advanced-gutenberg'),
      checked: true,
      className: "advgb-single-checkbox"
    }), /*#__PURE__*/React.createElement("div", {
      className: "advgb-controls-title"
    }, /*#__PURE__*/React.createElement("span", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Image width', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("div", {
      className: "advgb-unit-wrapper",
      key: "unit"
    }, ['px', '%'].map(function (unit, uIdx) {
      return /*#__PURE__*/React.createElement("span", {
        className: "advgb-unit ".concat('px' === unit ? 'selected' : ''),
        key: uIdx
      }, unit);
    }))), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.RangeControl, {
      value: "",
      min: 0,
      max: 2000,
      help: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Uncheck "auto" to set a custom width', 'advanced-gutenberg')
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.CheckboxControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('auto', 'advanced-gutenberg'),
      checked: true,
      className: "advgb-single-checkbox"
    }), /*#__PURE__*/React.createElement("div", {
      className: "advgb-controls-title"
    }, /*#__PURE__*/React.createElement("span", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Image height', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("div", {
      className: "advgb-unit-wrapper",
      key: "unit"
    }, "px")), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.RangeControl, {
      value: "",
      min: 100,
      max: 2000,
      disabled: true,
      help: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Uncheck "auto" to set a custom height', 'advanced-gutenberg')
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.ToggleControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Force custom size', 'advanced-gutenberg'),
      help: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('This adds !important to CSS in frontend to avoid custom size being overridden. Disable if breaks the layout.', 'advanced-gutenberg'),
      checked: false
    })), /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-wrap"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-text advgb-tooltips ppb-tooltips-library click",
      "data-toggle": "ppbtooltip",
      "data-placement": "top"
    }, /*#__PURE__*/React.createElement("span", {
      className: "advgb-promo-text"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Pro', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
      className: "tooltip-text"
    }, /*#__PURE__*/React.createElement("p", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('PublishPress Blocks Pro supports custom image options.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
      className: "clickable",
      href: _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PROMO_LINK,
      target: "_blank"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null))))), /*#__PURE__*/React.createElement(React.Fragment, null))), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PanelBody, {
      title: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Info', 'advanced-gutenberg'),
      initialOpen: false,
      className: "advgb-pro-icon"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvFontControl, null), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvColorControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Color', 'advanced-gutenberg'),
      value: ""
    })), /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-wrap"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-text advgb-tooltips ppb-tooltips-library click",
      "data-toggle": "ppbtooltip",
      "data-placement": "top"
    }, /*#__PURE__*/React.createElement("span", {
      className: "advgb-promo-text"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Pro', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
      className: "tooltip-text"
    }, /*#__PURE__*/React.createElement("p", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('PublishPress Blocks Pro supports multiple style options for the info display.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
      className: "clickable",
      href: _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PROMO_LINK,
      target: "_blank"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null))))), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvColorControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Color for links', 'advanced-gutenberg'),
      value: ""
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvColorControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Color for links (hover)', 'advanced-gutenberg'),
      value: ""
    }))), /*#__PURE__*/React.createElement(React.Fragment, null))), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PanelBody, {
      title: !props.attributes.postType || props.attributes.postType === 'post' ? (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Tags and Categories', 'advanced-gutenberg') : (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Taxonomies', 'advanced-gutenberg'),
      initialOpen: false,
      className: "advgb-pro-icon"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvFontControl, null), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvColorControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Color', 'advanced-gutenberg'),
      value: ""
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvColorControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Color (hover)', 'advanced-gutenberg'),
      value: ""
    })), /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-wrap"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-text advgb-tooltips ppb-tooltips-library click",
      "data-toggle": "ppbtooltip",
      "data-placement": "top"
    }, /*#__PURE__*/React.createElement("span", {
      className: "advgb-promo-text"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Pro', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
      className: "tooltip-text"
    }, /*#__PURE__*/React.createElement("p", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('PublishPress Blocks Pro supports multiple style options for the taxonomy display.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
      className: "clickable",
      href: _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PROMO_LINK,
      target: "_blank"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null))))), /*#__PURE__*/React.createElement(React.Fragment, null)), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PanelBody, {
      title: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Content', 'advanced-gutenberg'),
      initialOpen: false,
      className: "advgb-pro-icon"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvFontControl, null), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvColorControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Color', 'advanced-gutenberg'),
      value: ""
    })), /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-wrap"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-text advgb-tooltips ppb-tooltips-library click",
      "data-toggle": "ppbtooltip",
      "data-placement": "top"
    }, /*#__PURE__*/React.createElement("span", {
      className: "advgb-promo-text"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Pro', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
      className: "tooltip-text"
    }, /*#__PURE__*/React.createElement("p", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('PublishPress Blocks Pro supports multiple style options for the content display.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
      className: "clickable",
      href: _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PROMO_LINK,
      target: "_blank"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null)))))), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PanelBody, {
      title: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Read more link', 'advanced-gutenberg'),
      initialOpen: false,
      className: "advgb-pro-icon"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvFontControl, null), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.SelectControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Text Decoration', 'advanced-gutenberg'),
      value: "default",
      options: [{
        label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Default', 'advanced-gutenberg'),
        value: 'default'
      }]
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.SelectControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Text Decoration (hover)', 'advanced-gutenberg'),
      value: "default",
      options: [{
        label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Default', 'advanced-gutenberg'),
        value: 'default'
      }]
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvColorControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Text Color', 'advanced-gutenberg'),
      value: ""
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvColorControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Text Color (hover)', 'advanced-gutenberg'),
      value: ""
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvColorControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Background Color', 'advanced-gutenberg'),
      value: ""
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvColorControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Background Color (hover)', 'advanced-gutenberg'),
      value: ""
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.SelectControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Border style', 'advanced-gutenberg'),
      value: "none",
      options: [{
        label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Default', 'advanced-gutenberg'),
        value: 'none'
      }]
    })), /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-wrap"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-text advgb-tooltips ppb-tooltips-library click",
      "data-toggle": "ppbtooltip",
      "data-placement": "top"
    }, /*#__PURE__*/React.createElement("span", {
      className: "advgb-promo-text"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Pro', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
      className: "tooltip-text"
    }, /*#__PURE__*/React.createElement("p", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('PublishPress Blocks Pro supports multiple style options for the read more display.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
      className: "clickable",
      href: _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PROMO_LINK,
      target: "_blank"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null))))), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvColorControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Border Color', 'advanced-gutenberg'),
      value: ""
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AdvColorControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Border Color (hover)', 'advanced-gutenberg'),
      value: ""
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.RangeControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Border width', 'advanced-gutenberg'),
      value: "",
      min: 0,
      max: 10
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.RangeControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Border radius', 'advanced-gutenberg'),
      value: 0,
      min: 0,
      max: 100
    }))), /*#__PURE__*/React.createElement(React.Fragment, null)), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.ToggleControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Enable padding', 'advanced-gutenberg'),
      checked: false
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.RangeControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Padding top', 'advanced-gutenberg'),
      value: "",
      min: 0,
      max: 50
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.RangeControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Padding right', 'advanced-gutenberg'),
      value: "",
      min: 0,
      max: 50
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.RangeControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Padding bottom', 'advanced-gutenberg'),
      value: "",
      min: 0,
      max: 50
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.RangeControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Padding left', 'advanced-gutenberg'),
      value: "",
      min: 0,
      max: 50
    }))), /*#__PURE__*/React.createElement(React.Fragment, null))), /*#__PURE__*/React.createElement(React.Fragment, null))), /*#__PURE__*/React.createElement(React.Fragment, null)));
  };
}, 'recentPostsProSidebar');
(0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.addFilter)('editor.BlockEdit', 'advgb/recentPostsPro', recentPostsProSidebar);

/***/ },

/***/ "./assets/blocks/pro-ad/shared/index.jsx"
/*!***********************************************!*\
  !*** ./assets/blocks/pro-ad/shared/index.jsx ***!
  \***********************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AdvColorControl: () => (/* binding */ AdvColorControl),
/* harmony export */   AdvFontControl: () => (/* binding */ AdvFontControl),
/* harmony export */   AlignmentToolbar: () => (/* binding */ AlignmentToolbar),
/* harmony export */   BaseControl: () => (/* binding */ BaseControl),
/* harmony export */   BaseLabel: () => (/* binding */ BaseLabel),
/* harmony export */   BlockControls: () => (/* binding */ BlockControls),
/* harmony export */   Button: () => (/* binding */ Button),
/* harmony export */   CORE_SUPPORTED_BLOCKS: () => (/* binding */ CORE_SUPPORTED_BLOCKS),
/* harmony export */   CheckboxControl: () => (/* binding */ CheckboxControl),
/* harmony export */   ColorIndicator: () => (/* binding */ ColorIndicator),
/* harmony export */   ColorPalette: () => (/* binding */ ColorPalette),
/* harmony export */   Fragment: () => (/* binding */ Fragment),
/* harmony export */   InspectorControls: () => (/* binding */ InspectorControls),
/* harmony export */   Notice: () => (/* binding */ Notice),
/* harmony export */   PROMO_LINK: () => (/* binding */ PROMO_LINK),
/* harmony export */   PanelBody: () => (/* binding */ PanelBody),
/* harmony export */   RangeControl: () => (/* binding */ RangeControl),
/* harmony export */   RecentPostsEditTitle: () => (/* binding */ RecentPostsEditTitle),
/* harmony export */   SUPPORTED_BLOCKS: () => (/* binding */ SUPPORTED_BLOCKS),
/* harmony export */   SelectControl: () => (/* binding */ SelectControl),
/* harmony export */   ToggleControl: () => (/* binding */ ToggleControl),
/* harmony export */   __: () => (/* binding */ __),
/* harmony export */   addFilter: () => (/* binding */ addFilter),
/* harmony export */   advgbGetBlockTitle: () => (/* binding */ advgbGetBlockTitle),
/* harmony export */   createHigherOrderComponent: () => (/* binding */ createHigherOrderComponent),
/* harmony export */   sprintf: () => (/* binding */ sprintf)
/* harmony export */ });
// assets/blocks/pro-ad/shared/index.jsx

// WordPress dependencies
var __ = wp.i18n.__;

var addFilter = wp.hooks.addFilter;

var Fragment = wp.element.Fragment;

var createHigherOrderComponent = wp.compose.createHigherOrderComponent;

var sprintf = wp.i18n.sprintf;

// Block Editor components

var wpBlockEditor = wp.blockEditor || wp.editor;
var InspectorControls = wpBlockEditor.InspectorControls,
  ColorPalette = wpBlockEditor.ColorPalette,
  BlockControls = wpBlockEditor.BlockControls,
  AlignmentToolbar = wpBlockEditor.AlignmentToolbar;

// WordPress components

var _wp$components = wp.components,
  PanelBody = _wp$components.PanelBody,
  SelectControl = _wp$components.SelectControl,
  ToggleControl = _wp$components.ToggleControl,
  RangeControl = _wp$components.RangeControl,
  Notice = _wp$components.Notice,
  Button = _wp$components.Button,
  CheckboxControl = _wp$components.CheckboxControl,
  ColorIndicator = _wp$components.ColorIndicator,
  BaseControl = _wp$components.BaseControl;

// Base label helper

var BaseLabel = BaseControl.VisualLabel ? BaseControl.VisualLabel : "span";

// Shared constants
var PROMO_LINK = "https://publishpress.com/links/blocks-menu";
var SUPPORTED_BLOCKS = ['advgb/accordion-item', 'advgb/accordions', 'advgb/adv-tabs', 'advgb/adv-tab', 'advgb/recent-posts', 'advgb/images-slider', 'advgb/button', 'advgb/list', 'advgb/count-up', 'advgb/testimonial', 'advgb/image'];
var CORE_SUPPORTED_BLOCKS = ['core/paragraph', 'core/heading'];

// Shared components
function AdvFontControl(props) {
  return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(SelectControl, {
    label: __('Font Family', 'advanced-gutenberg'),
    value: "",
    options: [{
      label: 'Default',
      value: ''
    }]
  }), /*#__PURE__*/React.createElement(SelectControl, {
    label: __('Font Weight + Style', 'advanced-gutenberg'),
    value: "",
    options: [{
      label: 'Default',
      value: ''
    }]
  }), /*#__PURE__*/React.createElement(SelectControl, {
    label: __('Text Transform', 'advanced-gutenberg'),
    value: "",
    options: [{
      label: 'Default',
      value: ''
    }]
  }));
}
function AdvColorControl(props) {
  var label = props.label,
    value = props.value,
    onChange = props.onChange;
  return /*#__PURE__*/React.createElement(BaseControl, {
    className: "editor-color-palette-control block-editor-color-palette-control"
  }, /*#__PURE__*/React.createElement(BaseLabel, {
    className: "components-base-control__label"
  }, label, value && /*#__PURE__*/React.createElement(ColorIndicator, {
    colorValue: value
  })), /*#__PURE__*/React.createElement(ColorPalette, {
    className: "editor-color-palette-control__color-palette block-editor-color-palette-control__color-palette",
    value: value
  }));
}

// Shared utility functions
function advgbGetBlockTitle(name) {
  switch (name) {
    case 'advgb/accordion-item':
    case 'advgb/accordions':
      return __('Accordion', 'advanced-gutenberg');
      // removed by dead control flow

    case 'advgb/adv-tabs':
    case 'advgb/tab':
      return __('Tabs', 'advanced-gutenberg');
      // removed by dead control flow

    case 'advgb/recent-posts':
      return __('Content Display', 'advanced-gutenberg');
      // removed by dead control flow

    case 'advgb/images-slider':
      return __('Images Slider', 'advanced-gutenberg');
      // removed by dead control flow

    case 'advgb/button':
      return __('Button', 'advanced-gutenberg');
      // removed by dead control flow

    case 'advgb/list':
      return __('List', 'advanced-gutenberg');
      // removed by dead control flow

    case 'advgb/count-up':
      return __('Count Up', 'advanced-gutenberg');
      // removed by dead control flow

    case 'advgb/testimonial':
      return __('Testimonial', 'advanced-gutenberg');
      // removed by dead control flow

    case 'advgb/image':
      return __('Image', 'advanced-gutenberg');
      // removed by dead control flow

    default:
      return __('PublishPress', 'advanced-gutenberg');
      // removed by dead control flow

  }
}
function RecentPostsEditTitle(props) {
  var tabSelected = 'desktop';
  return /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement("div", {
    className: "advgb-recent-posts-responsive-items"
  }, ['desktop', 'tablet', 'mobile'].map(function (device, index) {
    var itemClasses = ["advgb-recent-posts-responsive-item", tabSelected === device && 'is-selected'].filter(Boolean).join(' ');
    return /*#__PURE__*/React.createElement("div", {
      className: itemClasses,
      key: index
    }, device);
  })), /*#__PURE__*/React.createElement("div", {
    className: "advgb-recent-posts-select-layout on-inspector"
  }, tabSelected === 'desktop' && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(RangeControl, {
    label: __('Font Size', 'advanced-gutenberg'),
    value: "",
    min: 10,
    max: 100,
    beforeIcon: "editor-textcolor",
    allowReset: true
  }), /*#__PURE__*/React.createElement(ToggleControl, {
    label: __('Hide', 'advanced-gutenberg'),
    checked: false
  })), tabSelected === 'tablet' && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(RangeControl, {
    label: __('Font Size', 'advanced-gutenberg'),
    value: "",
    min: 10,
    max: 100,
    beforeIcon: "editor-textcolor",
    allowReset: true
  }), /*#__PURE__*/React.createElement(ToggleControl, {
    label: __('Hide', 'advanced-gutenberg'),
    checked: false
  })), tabSelected === 'mobile' && /*#__PURE__*/React.createElement(Fragment, null, /*#__PURE__*/React.createElement(RangeControl, {
    label: __('Font Size', 'advanced-gutenberg'),
    value: "",
    min: 10,
    max: 100,
    beforeIcon: "editor-textcolor",
    allowReset: true
  }), /*#__PURE__*/React.createElement(ToggleControl, {
    label: __('Hide', 'advanced-gutenberg'),
    checked: false
  }))));
}

/***/ },

/***/ "./assets/blocks/pro-ad/tabs-promo.jsx"
/*!*********************************************!*\
  !*** ./assets/blocks/pro-ad/tabs-promo.jsx ***!
  \*********************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./shared/index.jsx */ "./assets/blocks/pro-ad/shared/index.jsx");

;

// Tab blocks promo
(0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.addFilter)('editor.BlockEdit', 'advgb/advTabsPro', function (BlockEdit) {
  return function (props) {
    return /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement(BlockEdit, props), props.isSelected && props.name === 'advgb/adv-tabs' && /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.InspectorControls, null, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PanelBody, {
      title: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Theme Settings', 'advanced-gutenberg'),
      className: "advgb-pro-icon",
      initialOpen: false
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.SelectControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Preset', 'advanced-gutenberg'),
      value: "default",
      options: [{
        label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Default', 'advanced-gutenberg'),
        value: 'default'
      }]
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Notice, {
      className: "advgb-notice-sidebar",
      status: "warning",
      isDismissible: false
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Selecting or resetting a preset will override your current design configuration.', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-wrap"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-text advgb-tooltips ppb-tooltips-library click",
      "data-toggle": "ppbtooltip",
      "data-placement": "top"
    }, /*#__PURE__*/React.createElement("span", {
      className: "advgb-promo-text"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Pro', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
      className: "tooltip-text"
    }, /*#__PURE__*/React.createElement("p", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('PublishPress Blocks Pro supports several pre-built designs for tabs.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
      className: "clickable",
      href: _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PROMO_LINK,
      target: "_blank"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null)))))), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PanelBody, {
      title: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Tab Settings', 'advanced-gutenberg'),
      className: "advgb-pro-icon",
      initialOpen: false
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.SelectControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Font Family', 'advanced-gutenberg'),
      value: "",
      options: [{
        label: 'Default',
        value: ''
      }]
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.SelectControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Font Weight + Style', 'advanced-gutenberg'),
      value: "",
      options: [{
        label: 'Default',
        value: ''
      }]
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.SelectControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Text Transform', 'advanced-gutenberg'),
      value: "",
      options: [{
        label: 'Default',
        value: ''
      }]
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.RangeControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Font Size', 'advanced-gutenberg'),
      value: "",
      min: 7,
      max: 100,
      beforeIcon: "editor-textcolor",
      allowReset: true
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.RangeControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Spacing below', 'advanced-gutenberg'),
      help: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('You can set a negative spacing below that depends on border width', 'advanced-gutenberg'),
      value: 8,
      min: -30,
      max: 30,
      beforeIcon: "arrow-down-alt",
      allowReset: true
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.RangeControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Spacing on the right', 'advanced-gutenberg'),
      value: 8,
      min: -30,
      max: 30,
      beforeIcon: "arrow-right-alt",
      allowReset: true
    })), /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-wrap"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-text advgb-tooltips ppb-tooltips-library click",
      "data-toggle": "ppbtooltip",
      "data-placement": "top"
    }, /*#__PURE__*/React.createElement("span", {
      className: "advgb-promo-text"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Pro', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
      className: "tooltip-text"
    }, /*#__PURE__*/React.createElement("p", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('PublishPress Blocks Pro supports Google Fonts in tabs.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
      className: "clickable",
      href: _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PROMO_LINK,
      target: "_blank"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null))))))), /*#__PURE__*/React.createElement(React.Fragment, null)));
  };
});

/***/ },

/***/ "./assets/blocks/pro-ad/testimonial-promo.jsx"
/*!****************************************************!*\
  !*** ./assets/blocks/pro-ad/testimonial-promo.jsx ***!
  \****************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./shared/index.jsx */ "./assets/blocks/pro-ad/shared/index.jsx");

;

// Testimonial block promo
(0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.addFilter)('editor.BlockEdit', 'advgb/testimonialPro', function (BlockEdit) {
  return function (props) {
    return /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement(BlockEdit, props), props.isSelected && props.name === 'advgb/testimonial' && /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.BlockControls, null, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.AlignmentToolbar, {
      value: props.attributes.align
    })), /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-wrap"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-text advgb-tooltips ppb-tooltips-library click",
      "data-toggle": "ppbtooltip",
      "data-placement": "top"
    }, /*#__PURE__*/React.createElement("span", {
      className: "advgb-promo-text"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Pro', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
      className: "tooltip-text"
    }, /*#__PURE__*/React.createElement("p", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('PublishPress Blocks Pro supports text alignment for testimonials.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
      className: "clickable",
      href: _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PROMO_LINK,
      target: "_blank"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null)))))), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.InspectorControls, null, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PanelBody, {
      title: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Content Size', 'advanced-gutenberg'),
      className: "advgb-pro-icon"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-promo-overlay-area"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-blur"
    }, /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.RangeControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Name size', 'advanced-gutenberg'),
      value: props.attributes.nameSize || '',
      min: 10,
      max: 100,
      beforeIcon: "editor-textcolor",
      allowReset: true
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.RangeControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Position size', 'advanced-gutenberg'),
      value: props.attributes.positionSize || '',
      min: 10,
      max: 100,
      beforeIcon: "editor-textcolor",
      allowReset: true
    }), /*#__PURE__*/React.createElement(_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.RangeControl, {
      label: (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Description size', 'advanced-gutenberg'),
      value: props.attributes.descriptionSize || '',
      min: 10,
      max: 100,
      beforeIcon: "editor-textcolor",
      allowReset: true
    })), /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-wrap"
    }, /*#__PURE__*/React.createElement("div", {
      className: "advgb-pro-overlay-text advgb-tooltips ppb-tooltips-library click",
      "data-toggle": "ppbtooltip",
      "data-placement": "top"
    }, /*#__PURE__*/React.createElement("span", {
      className: "advgb-promo-text"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Pro', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
      className: "tooltip-text"
    }, /*#__PURE__*/React.createElement("p", null, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('PublishPress Blocks Pro supports custom sizes for names, job positions, and descriptions.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
      className: "clickable",
      href: _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.PROMO_LINK,
      target: "_blank"
    }, (0,_shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("i", null)))))))));
  };
});

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
/*!*****************************************!*\
  !*** ./assets/blocks/pro-ad/pro-ad.jsx ***!
  \*****************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _shared_index_jsx__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./shared/index.jsx */ "./assets/blocks/pro-ad/shared/index.jsx");
/* harmony import */ var _general_promo_jsx__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./general-promo.jsx */ "./assets/blocks/pro-ad/general-promo.jsx");
/* harmony import */ var _core_blocks_promo_jsx__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./core-blocks-promo.jsx */ "./assets/blocks/pro-ad/core-blocks-promo.jsx");
/* harmony import */ var _accordion_promo_jsx__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./accordion-promo.jsx */ "./assets/blocks/pro-ad/accordion-promo.jsx");
/* harmony import */ var _tabs_promo_jsx__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./tabs-promo.jsx */ "./assets/blocks/pro-ad/tabs-promo.jsx");
/* harmony import */ var _recent_posts_promo_jsx__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./recent-posts-promo.jsx */ "./assets/blocks/pro-ad/recent-posts-promo.jsx");
/* harmony import */ var _button_promo_jsx__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./button-promo.jsx */ "./assets/blocks/pro-ad/button-promo.jsx");
/* harmony import */ var _list_promo_jsx__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./list-promo.jsx */ "./assets/blocks/pro-ad/list-promo.jsx");
/* harmony import */ var _count_up_promo_jsx__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./count-up-promo.jsx */ "./assets/blocks/pro-ad/count-up-promo.jsx");
/* harmony import */ var _testimonial_promo_jsx__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./testimonial-promo.jsx */ "./assets/blocks/pro-ad/testimonial-promo.jsx");
/* harmony import */ var _image_promo_jsx__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./image-promo.jsx */ "./assets/blocks/pro-ad/image-promo.jsx");
/* harmony import */ var _image_slider_promo_jsx__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./image-slider-promo.jsx */ "./assets/blocks/pro-ad/image-slider-promo.jsx");












})();

/******/ })()
;
//# sourceMappingURL=pro-ad.js.map
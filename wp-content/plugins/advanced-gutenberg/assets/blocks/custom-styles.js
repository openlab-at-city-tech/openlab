/******/ (() => { // webpackBootstrap
/*!******************************************************!*\
  !*** ./assets/blocks/customstyles/custom-styles.jsx ***!
  \******************************************************/
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
(function (wpI18n, wpHooks, wpBlocks, wpBlockEditor, wpComponents, wpCompose) {
  wpBlockEditor = wp.blockEditor || wp.editor;
  var addFilter = wpHooks.addFilter;
  var __ = wpI18n.__;
  var hasBlockSupport = wpBlocks.hasBlockSupport;
  var _wpBlockEditor = wpBlockEditor,
    InspectorControls = _wpBlockEditor.InspectorControls;
  var SelectControl = wpComponents.SelectControl;
  var createHigherOrderComponent = wpCompose.createHigherOrderComponent;

  // Register custom styles to blocks attributes
  addFilter('blocks.registerBlockType', 'advgb/registerCustomStyleClass', function (settings) {
    settings.attributes = _extends(settings.attributes, {
      customStyle: {
        type: 'string'
      },
      identifyColor: {
        type: 'string'
      }
    });
    return settings;
  });

  // Add option to return to default style
  if (typeof advgbBlocksCustomStyles.customStyles !== 'undefined' && advgbBlocksCustomStyles.customStyles) {
    advgbBlocksCustomStyles.customStyles.unshift({
      id: 0,
      label: __('Select a block style', 'advanced-gutenberg'),
      value: '',
      identifyColor: ''
    });
  }

  // Add option to select custom styles for supported blocks
  addFilter('editor.BlockEdit', 'advgb/customStyles', function (BlockEdit) {
    return function (props) {
      return [props.isSelected && /*#__PURE__*/React.createElement(InspectorControls, {
        key: "advgb-custom-controls"
      }, /*#__PURE__*/React.createElement("div", {
        className: "advgb-custom-styles-wrapper"
      }, /*#__PURE__*/React.createElement(SelectControl, {
        label: [__('Block styles', 'advanced-gutenberg'), /*#__PURE__*/React.createElement("span", {
          className: 'components-panel__color-area',
          key: "customstyle-identify",
          style: {
            background: props.attributes.identifyColor,
            verticalAlign: 'text-bottom',
            borderRadius: '50%',
            border: 'none',
            width: '16px',
            height: '16px',
            display: 'inline-block',
            marginLeft: '10px'
          }
        })],
        help: __('This option let you add custom styles for the current block.', 'advanced-gutenberg'),
        value: props.attributes.customStyle,
        options: advgbBlocksCustomStyles.customStyles.map(function (cstyle, index) {
          if (cstyle.title) advgbBlocksCustomStyles.customStyles[index].label = cstyle.title;
          if (cstyle.name) advgbBlocksCustomStyles.customStyles[index].value = cstyle.name;
          return cstyle;
        }),
        onChange: function onChange(cstyle) {
          var identifyColor = advgbBlocksCustomStyles.customStyles.filter(function (style) {
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
      }))), /*#__PURE__*/React.createElement(BlockEdit, _extends({
        key: "block-edit-custom-class-name"
      }, props))];
    };
  });

  // Apply custom styles on front-end
  addFilter('blocks.getSaveContent.extraProps', 'advgb/loadFrontendCustomStyles', function (extraProps, blockType, attributes) {
    if (hasBlockSupport(blockType, 'customStyle', true) && attributes.customStyle && typeof attributes.customStyle !== 'undefined') {
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
      if (!hasBlockSupport(props.name, 'customStyle', true)) {
        return /*#__PURE__*/React.createElement(BlockListBlock, props);
      }
      var customStyle = props.attributes.customStyle;
      if (customStyle && typeof customStyle !== 'undefined') {
        return /*#__PURE__*/React.createElement(BlockListBlock, _extends({}, props, {
          className: "".concat(customStyle)
        }));
      } else {
        return /*#__PURE__*/React.createElement(BlockListBlock, props);
      }
    };
  }, 'withStyleClasses');

  // Apply custom styles on back-end
  wp.hooks.addFilter('editor.BlockListBlock', 'advgb/loadBackendCustomStyles', withStyleClasses);
})(wp.i18n, wp.hooks, wp.blocks, wp.blockEditor, wp.components, wp.compose);
/******/ })()
;
//# sourceMappingURL=custom-styles.js.map
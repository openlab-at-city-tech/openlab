window["wp"] = window["wp"] || {}; window["wp"]["reusableBlocks"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 414);
/******/ })
/************************************************************************/
/******/ ({

/***/ 0:
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["element"]; }());

/***/ }),

/***/ 1:
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["i18n"]; }());

/***/ }),

/***/ 11:
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["coreData"]; }());

/***/ }),

/***/ 19:
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["url"]; }());

/***/ }),

/***/ 2:
/***/ (function(module, exports) {

(function() { module.exports = window["lodash"]; }());

/***/ }),

/***/ 28:
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["notices"]; }());

/***/ }),

/***/ 3:
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["components"]; }());

/***/ }),

/***/ 4:
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["data"]; }());

/***/ }),

/***/ 414:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "store", function() { return /* reexport */ store; });
__webpack_require__.d(__webpack_exports__, "ReusableBlocksMenuItems", function() { return /* reexport */ reusable_blocks_menu_items; });

// NAMESPACE OBJECT: ./packages/reusable-blocks/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, "__experimentalConvertBlockToStatic", function() { return __experimentalConvertBlockToStatic; });
__webpack_require__.d(actions_namespaceObject, "__experimentalConvertBlocksToReusable", function() { return __experimentalConvertBlocksToReusable; });
__webpack_require__.d(actions_namespaceObject, "__experimentalDeleteReusableBlock", function() { return __experimentalDeleteReusableBlock; });
__webpack_require__.d(actions_namespaceObject, "__experimentalSetEditingReusableBlock", function() { return __experimentalSetEditingReusableBlock; });

// NAMESPACE OBJECT: ./packages/reusable-blocks/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "__experimentalIsEditingReusableBlock", function() { return __experimentalIsEditingReusableBlock; });

// EXTERNAL MODULE: external ["wp","blockEditor"]
var external_wp_blockEditor_ = __webpack_require__(5);

// EXTERNAL MODULE: external ["wp","coreData"]
var external_wp_coreData_ = __webpack_require__(11);

// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__(4);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: external ["wp","blocks"]
var external_wp_blocks_ = __webpack_require__(8);

// EXTERNAL MODULE: external ["wp","i18n"]
var external_wp_i18n_ = __webpack_require__(1);

// CONCATENATED MODULE: ./packages/reusable-blocks/build-module/store/controls.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


/**
 * Convert a reusable block to a static block effect handler
 *
 * @param {string} clientId Block ID.
 * @return {Object} control descriptor.
 */

function controls_convertBlockToStatic(clientId) {
  return {
    type: 'CONVERT_BLOCK_TO_STATIC',
    clientId
  };
}
/**
 * Convert a static block to a reusable block effect handler
 *
 * @param {Array}  clientIds Block IDs.
 * @param {string} title     Reusable block title.
 * @return {Object} control descriptor.
 */

function controls_convertBlocksToReusable(clientIds, title) {
  return {
    type: 'CONVERT_BLOCKS_TO_REUSABLE',
    clientIds,
    title
  };
}
/**
 * Deletes a reusable block.
 *
 * @param {string} id Reusable block ID.
 * @return {Object} control descriptor.
 */

function deleteReusableBlock(id) {
  return {
    type: 'DELETE_REUSABLE_BLOCK',
    id
  };
}
const controls = {
  CONVERT_BLOCK_TO_STATIC: Object(external_wp_data_["createRegistryControl"])(registry => ({
    clientId
  }) => {
    const oldBlock = registry.select(external_wp_blockEditor_["store"]).getBlock(clientId);
    const reusableBlock = registry.select('core').getEditedEntityRecord('postType', 'wp_block', oldBlock.attributes.ref);
    const newBlocks = Object(external_wp_blocks_["parse"])(Object(external_lodash_["isFunction"])(reusableBlock.content) ? reusableBlock.content(reusableBlock) : reusableBlock.content);
    registry.dispatch(external_wp_blockEditor_["store"]).replaceBlocks(oldBlock.clientId, newBlocks);
  }),
  CONVERT_BLOCKS_TO_REUSABLE: Object(external_wp_data_["createRegistryControl"])(registry => async function ({
    clientIds,
    title
  }) {
    const reusableBlock = {
      title: title || Object(external_wp_i18n_["__"])('Untitled Reusable block'),
      content: Object(external_wp_blocks_["serialize"])(registry.select(external_wp_blockEditor_["store"]).getBlocksByClientId(clientIds)),
      status: 'publish'
    };
    const updatedRecord = await registry.dispatch('core').saveEntityRecord('postType', 'wp_block', reusableBlock);
    const newBlock = Object(external_wp_blocks_["createBlock"])('core/block', {
      ref: updatedRecord.id
    });
    registry.dispatch(external_wp_blockEditor_["store"]).replaceBlocks(clientIds, newBlock);

    registry.dispatch(store).__experimentalSetEditingReusableBlock(newBlock.clientId, true);
  }),
  DELETE_REUSABLE_BLOCK: Object(external_wp_data_["createRegistryControl"])(registry => async function ({
    id
  }) {
    const reusableBlock = registry.select('core').getEditedEntityRecord('postType', 'wp_block', id); // Don't allow a reusable block with a temporary ID to be deleted

    if (!reusableBlock) {
      return;
    } // Remove any other blocks that reference this reusable block


    const allBlocks = registry.select(external_wp_blockEditor_["store"]).getBlocks();
    const associatedBlocks = allBlocks.filter(block => Object(external_wp_blocks_["isReusableBlock"])(block) && block.attributes.ref === id);
    const associatedBlockClientIds = associatedBlocks.map(block => block.clientId); // Remove the parsed block.

    if (associatedBlockClientIds.length) {
      registry.dispatch(external_wp_blockEditor_["store"]).removeBlocks(associatedBlockClientIds);
    }

    await registry.dispatch('core').deleteEntityRecord('postType', 'wp_block', id);
  })
};
/* harmony default export */ var store_controls = (controls);
//# sourceMappingURL=controls.js.map
// CONCATENATED MODULE: ./packages/reusable-blocks/build-module/store/actions.js
/**
 * Internal dependencies
 */

/**
 * Returns a generator converting a reusable block into a static block.
 *
 * @param {string} clientId The client ID of the block to attach.
 */

function* __experimentalConvertBlockToStatic(clientId) {
  yield controls_convertBlockToStatic(clientId);
}
/**
 * Returns a generator converting one or more static blocks into a reusable block.
 *
 * @param {string[]} clientIds The client IDs of the block to detach.
 * @param {string}   title     Reusable block title.
 */

function* __experimentalConvertBlocksToReusable(clientIds, title) {
  yield controls_convertBlocksToReusable(clientIds, title);
}
/**
 * Returns a generator deleting a reusable block.
 *
 * @param {string} id The ID of the reusable block to delete.
 */

function* __experimentalDeleteReusableBlock(id) {
  yield deleteReusableBlock(id);
}
/**
 * Returns an action descriptor for SET_EDITING_REUSABLE_BLOCK action.
 *
 * @param {string}  clientId  The clientID of the reusable block to target.
 * @param {boolean} isEditing Whether the block should be in editing state.
 * @return {Object} Action descriptor.
 */

function __experimentalSetEditingReusableBlock(clientId, isEditing) {
  return {
    type: 'SET_EDITING_REUSABLE_BLOCK',
    clientId,
    isEditing
  };
}
//# sourceMappingURL=actions.js.map
// CONCATENATED MODULE: ./packages/reusable-blocks/build-module/store/reducer.js
/**
 * WordPress dependencies
 */

function isEditingReusableBlock(state = {}, action) {
  if ((action === null || action === void 0 ? void 0 : action.type) === 'SET_EDITING_REUSABLE_BLOCK') {
    return { ...state,
      [action.clientId]: action.isEditing
    };
  }

  return state;
}
/* harmony default export */ var reducer = (Object(external_wp_data_["combineReducers"])({
  isEditingReusableBlock
}));
//# sourceMappingURL=reducer.js.map
// CONCATENATED MODULE: ./packages/reusable-blocks/build-module/store/selectors.js
/**
 * Returns true if reusable block is in the editing state.
 *
 * @param {Object} state    Global application state.
 * @param {number} clientId the clientID of the block.
 * @return {boolean} Whether the reusable block is in the editing state.
 */
function __experimentalIsEditingReusableBlock(state, clientId) {
  return state.isEditingReusableBlock[clientId];
}
//# sourceMappingURL=selectors.js.map
// CONCATENATED MODULE: ./packages/reusable-blocks/build-module/store/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */





const STORE_NAME = 'core/reusable-blocks';
/**
 * Store definition for the reusable blocks namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */

const store = Object(external_wp_data_["createReduxStore"])(STORE_NAME, {
  actions: actions_namespaceObject,
  controls: store_controls,
  reducer: reducer,
  selectors: selectors_namespaceObject
});
Object(external_wp_data_["register"])(store);
//# sourceMappingURL=index.js.map
// EXTERNAL MODULE: external ["wp","element"]
var external_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external ["wp","components"]
var external_wp_components_ = __webpack_require__(3);

// EXTERNAL MODULE: external ["wp","primitives"]
var external_wp_primitives_ = __webpack_require__(6);

// CONCATENATED MODULE: ./packages/icons/build-module/library/reusable-block.js


/**
 * WordPress dependencies
 */

const reusable_block_reusableBlock = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M7 7.2h8.2L13.5 9l1.1 1.1 3.6-3.6-3.5-4-1.1 1 1.9 2.3H7c-.9 0-1.7.3-2.3.9-1.4 1.5-1.4 4.2-1.4 5.6v.2h1.5v-.3c0-1.1 0-3.5 1-4.5.3-.3.7-.5 1.2-.5zm13.8 4V11h-1.5v.3c0 1.1 0 3.5-1 4.5-.3.3-.7.5-1.3.5H8.8l1.7-1.7-1.1-1.1L5.9 17l3.5 4 1.1-1-1.9-2.3H17c.9 0 1.7-.3 2.3-.9 1.5-1.4 1.5-4.2 1.5-5.6z"
}));
/* harmony default export */ var reusable_block = (reusable_block_reusableBlock);
//# sourceMappingURL=reusable-block.js.map
// EXTERNAL MODULE: external ["wp","notices"]
var external_wp_notices_ = __webpack_require__(28);

// CONCATENATED MODULE: ./packages/reusable-blocks/build-module/components/reusable-blocks-menu-items/reusable-block-convert-button.js


/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */


/**
 * Menu control to convert block(s) to reusable block.
 *
 * @param {Object}   props              Component props.
 * @param {string[]} props.clientIds    Client ids of selected blocks.
 * @param {string}   props.rootClientId ID of the currently selected top-level block.
 * @return {import('@wordpress/element').WPComponent} The menu control or null.
 */

function ReusableBlockConvertButton({
  clientIds,
  rootClientId
}) {
  const [isModalOpen, setIsModalOpen] = Object(external_wp_element_["useState"])(false);
  const [title, setTitle] = Object(external_wp_element_["useState"])('');
  const canConvert = Object(external_wp_data_["useSelect"])(select => {
    var _getBlocksByClientId;

    const {
      canUser
    } = select(external_wp_coreData_["store"]);
    const {
      getBlocksByClientId,
      canInsertBlockType
    } = select(external_wp_blockEditor_["store"]);
    const blocks = (_getBlocksByClientId = getBlocksByClientId(clientIds)) !== null && _getBlocksByClientId !== void 0 ? _getBlocksByClientId : [];
    const isReusable = blocks.length === 1 && blocks[0] && Object(external_wp_blocks_["isReusableBlock"])(blocks[0]) && !!select(external_wp_coreData_["store"]).getEntityRecord('postType', 'wp_block', blocks[0].attributes.ref);

    const _canConvert = // Hide when this is already a reusable block.
    !isReusable && // Hide when reusable blocks are disabled.
    canInsertBlockType('core/block', rootClientId) && blocks.every(block => // Guard against the case where a regular block has *just* been converted.
    !!block && // Hide on invalid blocks.
    block.isValid && // Hide when block doesn't support being made reusable.
    Object(external_wp_blocks_["hasBlockSupport"])(block.name, 'reusable', true)) && // Hide when current doesn't have permission to do that.
    !!canUser('create', 'blocks');

    return _canConvert;
  }, [clientIds]);
  const {
    __experimentalConvertBlocksToReusable: convertBlocksToReusable
  } = Object(external_wp_data_["useDispatch"])(store);
  const {
    createSuccessNotice,
    createErrorNotice
  } = Object(external_wp_data_["useDispatch"])(external_wp_notices_["store"]);
  const onConvert = Object(external_wp_element_["useCallback"])(async function (reusableBlockTitle) {
    try {
      await convertBlocksToReusable(clientIds, reusableBlockTitle);
      createSuccessNotice(Object(external_wp_i18n_["__"])('Reusable block created.'), {
        type: 'snackbar'
      });
    } catch (error) {
      createErrorNotice(error.message, {
        type: 'snackbar'
      });
    }
  }, [clientIds]);

  if (!canConvert) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockSettingsMenuControls"], null, ({
    onClose
  }) => Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(external_wp_components_["MenuItem"], {
    icon: reusable_block,
    onClick: () => {
      setIsModalOpen(true);
    }
  }, Object(external_wp_i18n_["__"])('Add to Reusable blocks')), isModalOpen && Object(external_wp_element_["createElement"])(external_wp_components_["Modal"], {
    title: Object(external_wp_i18n_["__"])('Create Reusable block'),
    closeLabel: Object(external_wp_i18n_["__"])('Close'),
    onRequestClose: () => {
      setIsModalOpen(false);
      setTitle('');
    },
    overlayClassName: "reusable-blocks-menu-items__convert-modal"
  }, Object(external_wp_element_["createElement"])("form", {
    onSubmit: event => {
      event.preventDefault();
      onConvert(title);
      setIsModalOpen(false);
      setTitle('');
      onClose();
    }
  }, Object(external_wp_element_["createElement"])(external_wp_components_["TextControl"], {
    label: Object(external_wp_i18n_["__"])('Name'),
    value: title,
    onChange: setTitle
  }), Object(external_wp_element_["createElement"])(external_wp_components_["Flex"], {
    className: "reusable-blocks-menu-items__convert-modal-actions",
    justify: "flex-end"
  }, Object(external_wp_element_["createElement"])(external_wp_components_["FlexItem"], null, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    variant: "secondary",
    onClick: () => {
      setIsModalOpen(false);
      setTitle('');
    }
  }, Object(external_wp_i18n_["__"])('Cancel'))), Object(external_wp_element_["createElement"])(external_wp_components_["FlexItem"], null, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    variant: "primary",
    type: "submit"
  }, Object(external_wp_i18n_["__"])('Save'))))))));
}
//# sourceMappingURL=reusable-block-convert-button.js.map
// EXTERNAL MODULE: external ["wp","url"]
var external_wp_url_ = __webpack_require__(19);

// CONCATENATED MODULE: ./packages/reusable-blocks/build-module/components/reusable-blocks-menu-items/reusable-blocks-manage-button.js


/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */



function ReusableBlocksManageButton({
  clientId
}) {
  const {
    isVisible
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      getBlock
    } = select(external_wp_blockEditor_["store"]);
    const {
      canUser
    } = select(external_wp_coreData_["store"]);
    const reusableBlock = getBlock(clientId);
    return {
      isVisible: !!reusableBlock && Object(external_wp_blocks_["isReusableBlock"])(reusableBlock) && !!canUser('update', 'blocks', reusableBlock.attributes.ref)
    };
  }, [clientId]);
  const {
    __experimentalConvertBlockToStatic: convertBlockToStatic
  } = Object(external_wp_data_["useDispatch"])(store);

  if (!isVisible) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockSettingsMenuControls"], null, Object(external_wp_element_["createElement"])(external_wp_components_["MenuItem"], {
    href: Object(external_wp_url_["addQueryArgs"])('edit.php', {
      post_type: 'wp_block'
    })
  }, Object(external_wp_i18n_["__"])('Manage Reusable blocks')), Object(external_wp_element_["createElement"])(external_wp_components_["MenuItem"], {
    onClick: () => convertBlockToStatic(clientId)
  }, Object(external_wp_i18n_["__"])('Convert to regular blocks')));
}

/* harmony default export */ var reusable_blocks_manage_button = (ReusableBlocksManageButton);
//# sourceMappingURL=reusable-blocks-manage-button.js.map
// CONCATENATED MODULE: ./packages/reusable-blocks/build-module/components/reusable-blocks-menu-items/index.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




function ReusableBlocksMenuItems({
  clientIds,
  rootClientId
}) {
  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(ReusableBlockConvertButton, {
    clientIds: clientIds,
    rootClientId: rootClientId
  }), clientIds.length === 1 && Object(external_wp_element_["createElement"])(reusable_blocks_manage_button, {
    clientId: clientIds[0]
  }));
}

/* harmony default export */ var reusable_blocks_menu_items = (Object(external_wp_data_["withSelect"])(select => {
  const {
    getSelectedBlockClientIds
  } = select(external_wp_blockEditor_["store"]);
  return {
    clientIds: getSelectedBlockClientIds()
  };
})(ReusableBlocksMenuItems));
//# sourceMappingURL=index.js.map
// CONCATENATED MODULE: ./packages/reusable-blocks/build-module/components/index.js

//# sourceMappingURL=index.js.map
// CONCATENATED MODULE: ./packages/reusable-blocks/build-module/index.js
/**
 * WordPress dependencies
 */




//# sourceMappingURL=index.js.map

/***/ }),

/***/ 5:
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blockEditor"]; }());

/***/ }),

/***/ 6:
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["primitives"]; }());

/***/ }),

/***/ 8:
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blocks"]; }());

/***/ })

/******/ });
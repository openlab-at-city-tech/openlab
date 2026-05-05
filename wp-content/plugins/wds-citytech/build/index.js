/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/components/post-sharing-options/PostSharingOptionsContent.js":
/*!**************************************************************************!*\
  !*** ./src/components/post-sharing-options/PostSharingOptionsContent.js ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PostSharingChoice: () => (/* binding */ PostSharingChoice),
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./style.scss */ "./src/components/post-sharing-options/style.scss");

/* global openlabBlocksPostVisibility */




const PostSharingChoice = ({
  instanceId,
  value,
  label,
  info,
  ...props
}) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
  className: "components-radio-control__option"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
  type: "radio",
  name: `editor-post-visibility__setting-${instanceId}`,
  value: value,
  id: `editor-post-${value}-${instanceId}`,
  "aria-describedby": `editor-post-${value}-${instanceId}-description`,
  className: "components-radio-control__input",
  ...props
}), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
  htmlFor: `editor-post-${value}-${instanceId}`,
  className: "components-radio-control__label"
}, label), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", {
  id: `editor-post-${value}-${instanceId}-description`,
  className: "components-radio-control__option-description"
}, info));
const PostSharingOptionsContent = ({
  instanceId = 'post-sharing-options'
}) => {
  const {
    blogPublic,
    shareOnlyWithGroup
  } = openlabBlocksPostVisibility;
  const {
    editPost
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.useDispatch)('core/editor');
  const blogPublicInt = parseInt(blogPublic);
  const {
    postVisibility
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.useSelect)(select => {
    const postMeta = select('core/editor').getEditedPostAttribute('meta');
    const defaultVisibility = blogPublicInt >= 0 ? 'default' : 'members-only';
    return {
      postVisibility: postMeta.openlab_post_visibility || defaultVisibility
    };
  }, [blogPublicInt]);
  if (blogPublicInt < -1) {
    return null;
  }
  const onChange = value => {
    editPost({
      meta: {
        'openlab_post_visibility': value
      }
    });
  };
  const publicOverrideString = 'This will override the Public visibility setting above.';
  const visibilityOptions = [{
    value: 'group-members-only',
    label: 'Site members only',
    info: shareOnlyWithGroup + ' ' + publicOverrideString
  }, {
    value: 'members-only',
    label: 'OpenLab members only',
    info: 'Only logged-in OpenLab members can see this post. ' + publicOverrideString
  }];
  if (blogPublicInt >= 0) {
    visibilityOptions.push({
      value: 'default',
      label: 'Everyone',
      info: 'Everyone who can view this site can see this post.'
    });
  }
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("fieldset", {
    className: "components-radio-control"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.VisuallyHidden, {
    as: "legend"
  }, "Sharing"), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, "Control who can see this post."), visibilityOptions.map(option => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(PostSharingChoice, {
    key: option.value,
    instanceId: instanceId,
    value: option.value,
    label: option.label,
    info: option.info,
    onChange: event => onChange(event.target.value),
    checked: postVisibility === option.value
  })));
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (PostSharingOptionsContent);

/***/ }),

/***/ "./src/components/post-sharing-options/index.js":
/*!******************************************************!*\
  !*** ./src/components/post-sharing-options/index.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_edit_post__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/edit-post */ "@wordpress/edit-post");
/* harmony import */ var _wordpress_edit_post__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_edit_post__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/plugins */ "@wordpress/plugins");
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_plugins__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _PostSharingOptionsContent__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./PostSharingOptionsContent */ "./src/components/post-sharing-options/PostSharingOptionsContent.js");





const PostSharingOptions = () => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_edit_post__WEBPACK_IMPORTED_MODULE_1__.PluginDocumentSettingPanel, {
  name: "post-sharing-options",
  title: "More visibility options",
  className: "post-sharing-options"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_PostSharingOptionsContent__WEBPACK_IMPORTED_MODULE_4__["default"], null));
const OpenlabPostVisibilityPlugin = () => {
  const isSiteEditor = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_3__.useSelect)(select => {
    const editSite = select('core/edit-site');
    return !!editSite;
  }, []);
  return !isSiteEditor && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(PostSharingOptions, null);
};
const registerPostVisibility = () => {
  (0,_wordpress_plugins__WEBPACK_IMPORTED_MODULE_2__.registerPlugin)('post-sharing-options', {
    render: OpenlabPostVisibilityPlugin,
    icon: 'visibility'
  });
};
wp.domReady(registerPostVisibility);

/***/ }),

/***/ "./src/components/pre-publication-privacy/index.js":
/*!*********************************************************!*\
  !*** ./src/components/pre-publication-privacy/index.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/plugins */ "@wordpress/plugins");
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_plugins__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _post_sharing_options_PostSharingOptionsContent__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../post-sharing-options/PostSharingOptionsContent */ "./src/components/post-sharing-options/PostSharingOptionsContent.js");
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./style.scss */ "./src/components/pre-publication-privacy/style.scss");
/* harmony import */ var _warning_icon__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./warning-icon */ "./src/components/pre-publication-privacy/warning-icon.js");

/* global openlabBlocksPostVisibility */









const PrePublicationPrivacy = () => {
  const {
    blogPublic,
    prePubShareOnlyWithGroup
  } = openlabBlocksPostVisibility;
  const blogPublicInt = parseInt(blogPublic);
  const {
    postVisibility
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_4__.useSelect)(select => {
    const postMeta = select('core/editor').getEditedPostAttribute('meta');
    const defaultVisibility = blogPublicInt >= 0 ? 'default' : 'members-only';
    return {
      postVisibility: postMeta.openlab_post_visibility || defaultVisibility
    };
  }, [blogPublicInt]);
  const getBlogPublicMessage = () => {
    switch (blogPublicInt) {
      case 1:
        return (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('You are publishing on a public site. It may be included in web search results.', 'wds-citytech');
      case 0:
        return (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('You are publishing on a public site. Search engines are asked not to include this site in web search results.', 'wds-citytech');
      case -1:
        return (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('You are publishing on an OpenLab only site. It is visible only to registered members of City Tech OpenLab.', 'wds-citytech');
      case -2:
        return (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('You are publishing on a private site. It is visible only to registered members of this site.', 'wds-citytech');
      case -3:
        return (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('You are publishing on a hidden site. It is visible only to site administrators.', 'wds-citytech');
      default:
        return '';
    }
  };
  const getPostVisibilityMessage = () => {
    if (blogPublicInt <= -2) {
      return (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Everyone who can view this site can see this post.', 'wds-citytech');
    }
    switch (postVisibility) {
      case 'default':
      default:
        return (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Everyone who can view this site can see this post. You can change the post visibility settings below.', 'wds-citytech');
      case 'group-members-only':
        return prePubShareOnlyWithGroup;
      case 'members-only':
        return (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Only logged-in OpenLab members can see this post. This will override the site visibility setting. You can change the post visibility settings below.', 'wds-citytech');
    }
  };
  const getVisibilityOptionsPanel = () => {
    if (blogPublicInt >= -1) {
      return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__.PluginPrePublishPanel, {
        className: "openlab-pre-publication-visibility-panel",
        initialOpen: true,
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('More visibility options', 'wds-citytech'),
        icon: (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_warning_icon__WEBPACK_IMPORTED_MODULE_8__["default"], null)
      }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_post_sharing_options_PostSharingOptionsContent__WEBPACK_IMPORTED_MODULE_6__["default"], {
        instanceId: "pre-publish-sharing-options"
      }));
    }
    return null;
  };
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__.PluginPrePublishPanel, {
    className: "openlab-pre-publication-privacy-panel",
    icon: (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_warning_icon__WEBPACK_IMPORTED_MODULE_8__["default"], null),
    initialOpen: true,
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__.__)('Visibility Status Check', 'wds-citytech')
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("strong", null, "Site:"), " ", getBlogPublicMessage()), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("strong", null, "Post:"), " ", getPostVisibilityMessage())), getVisibilityOptionsPanel());
};
(0,_wordpress_plugins__WEBPACK_IMPORTED_MODULE_2__.registerPlugin)('openlab-pre-publication-privacy', {
  render: PrePublicationPrivacy
});

/***/ }),

/***/ "./src/components/pre-publication-privacy/warning-icon.js":
/*!****************************************************************!*\
  !*** ./src/components/pre-publication-privacy/warning-icon.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

const WarningIcon = () => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("svg", {
  className: "warning-icon",
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
  fillRule: "evenodd",
  clipRule: "evenodd",
  d: "M12.218 5.377a.25.25 0 0 0-.436 0l-7.29 12.96a.25.25 0 0 0 .218.373h14.58a.25.25 0 0 0 .218-.372l-7.29-12.96Zm-1.743-.735c.669-1.19 2.381-1.19 3.05 0l7.29 12.96a1.75 1.75 0 0 1-1.525 2.608H4.71a1.75 1.75 0 0 1-1.525-2.608l7.29-12.96ZM12.75 17.46h-1.5v-1.5h1.5v1.5Zm-1.5-3h1.5v-5h-1.5v5Z"
}));
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (WarningIcon);

/***/ }),

/***/ "./src/index.js":
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _components_post_sharing_options__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components/post-sharing-options */ "./src/components/post-sharing-options/index.js");
/* harmony import */ var _components_pre_publication_privacy__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/pre-publication-privacy */ "./src/components/pre-publication-privacy/index.js");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./store */ "./src/store.js");
//import './blocks/openlab-help'
//import './blocks/openlab-support'




wp.domReady(() => {
  const markVisibilityPanel = () => {
    const buttons = document.querySelectorAll('.editor-post-publish-panel__prepublish .components-panel__body-toggle');
    buttons.forEach(button => {
      if (button.textContent.trim().startsWith('Visibility')) {
        const panelBody = button.closest('.components-panel__body');
        if (panelBody) {
          panelBody.classList.add('openlab-hide-visibility');
        }
      }
    });
  };
  markVisibilityPanel();

  // In case the prepublish panel mounts later
  const observer = new MutationObserver(markVisibilityPanel);
  observer.observe(document.body, {
    childList: true,
    subtree: true
  });
});

/***/ }),

/***/ "./src/store.js":
/*!**********************!*\
  !*** ./src/store.js ***!
  \**********************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   STORE_NAME: () => (/* binding */ STORE_NAME)
/* harmony export */ });
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__);

const DEFAULT_STATE = {
  prePublishPanelStatus: {
    postSharingOptions: 'closed',
    prePublicationPrivacy: 'open'
  }
};
const actions = {
  setPrePublishPanelStatus(panelKey, panelStatus) {
    return {
      type: 'SET_PRE_PUBLISH_PANEL_STATUS',
      panelKey: panelKey,
      panelStatus: panelStatus
    };
  }
};
const reducer = (state = DEFAULT_STATE, action) => {
  switch (action.type) {
    case 'SET_PRE_PUBLISH_PANEL_STATUS':
      return {
        ...state,
        prePublishPanelStatus: {
          ...state.prePublishPanelStatus,
          [action.panelKey]: action.panelStatus
        }
      };
    default:
      return state;
  }
};
const selectors = {
  isPanelOpen(state, panelKey) {
    return state.openPanel === panelKey;
  }
};
const STORE_NAME = 'wds-citytech';
(0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.register)((0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.createReduxStore)(STORE_NAME, {
  reducer,
  actions,
  selectors
}));

/***/ }),

/***/ "./src/components/post-sharing-options/style.scss":
/*!********************************************************!*\
  !*** ./src/components/post-sharing-options/style.scss ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/components/pre-publication-privacy/style.scss":
/*!***********************************************************!*\
  !*** ./src/components/pre-publication-privacy/style.scss ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

module.exports = window["React"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/data":
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["data"];

/***/ }),

/***/ "@wordpress/edit-post":
/*!**********************************!*\
  !*** external ["wp","editPost"] ***!
  \**********************************/
/***/ ((module) => {

module.exports = window["wp"]["editPost"];

/***/ }),

/***/ "@wordpress/editor":
/*!********************************!*\
  !*** external ["wp","editor"] ***!
  \********************************/
/***/ ((module) => {

module.exports = window["wp"]["editor"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "@wordpress/plugins":
/*!*********************************!*\
  !*** external ["wp","plugins"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["plugins"];

/***/ })

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
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
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
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"index": 0,
/******/ 			"./style-index": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = globalThis["webpackChunkwds_citytech"] = globalThis["webpackChunkwds_citytech"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["./style-index"], () => (__webpack_require__("./src/index.js")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;
//# sourceMappingURL=index.js.map
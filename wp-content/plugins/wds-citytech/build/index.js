/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/components/post-sharing-options.js":
/*!************************************************!*\
  !*** ./src/components/post-sharing-options.js ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_edit_post__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/edit-post */ "@wordpress/edit-post");
/* harmony import */ var _wordpress_edit_post__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_edit_post__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/plugins */ "@wordpress/plugins");
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_plugins__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__);





const PostSharingOptions = ({}) => {
  const {
    currentGroupTypeSiteLabel,
    shareOnlyWithGroup,
    siteIsPublic
  } = openlabBlocksPostVisibility;
  if (!siteIsPublic) {
    return null;
  }
  const {
    editPost
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_4__.useDispatch)('core/editor');
  const onChange = value => {
    editPost({
      meta: {
        'openlab_post_visibility': value
      }
    });
  };
  const {
    postVisibility
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_4__.useSelect)(select => {
    const postMeta = select('core/editor').getEditedPostAttribute('meta');
    return {
      postVisibility: postMeta['openlab_post_visibility'] || 'default'
    };
  });
  const publicOverrideString = 'This will override the Public visibility setting above.';
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_edit_post__WEBPACK_IMPORTED_MODULE_2__.PluginDocumentSettingPanel, {
    name: "post-sharing-options",
    title: "More visibility options",
    className: "post-sharing-options"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("fieldset", {
    className: "editor-post-visibility__fieldset"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.VisuallyHidden, {
    as: "legend"
  }, "Sharing"), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, 'Control who can see this post.'), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(PostSharingChoice, {
    instanceId: "post-sharing-options",
    value: "group-members-only",
    label: "Site Members",
    info: shareOnlyWithGroup + ' ' + publicOverrideString,
    onChange: event => onChange(event.target.value),
    checked: postVisibility === 'group-members-only'
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(PostSharingChoice, {
    instanceId: "post-sharing-options",
    value: "members-only",
    label: "OpenLab members only",
    info: 'Only logged-in OpenLab members can see this post. ' + publicOverrideString,
    onChange: event => onChange(event.target.value),
    checked: postVisibility === 'members-only'
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(PostSharingChoice, {
    instanceId: "post-sharing-options",
    value: "default",
    label: "Everyone",
    info: "Everyone who can view this site can see this post.",
    onChange: event => onChange(event.target.value),
    checked: postVisibility === 'default'
  })));
};
function PostSharingChoice({
  instanceId,
  value,
  label,
  info,
  ...props
}) {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "editor-post-visibility__choice"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "radio",
    name: `editor-post-visibility__setting-${instanceId}`,
    value: value,
    id: `editor-post-${value}-${instanceId}`,
    "aria-describedby": `editor-post-${value}-${instanceId}-description`,
    className: "editor-post-visibility__radio",
    ...props
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
    htmlFor: `editor-post-${value}-${instanceId}`,
    className: "editor-post-visibility__label"
  }, label), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", {
    id: `editor-post-${value}-${instanceId}-description`,
    className: "editor-post-visibility__info"
  }, info));
}
(0,_wordpress_plugins__WEBPACK_IMPORTED_MODULE_3__.registerPlugin)('post-sharing-options', {
  render: PostSharingOptions
});

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
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _components_post_sharing_options__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components/post-sharing-options */ "./src/components/post-sharing-options.js");
//import './blocks/openlab-help'
//import './blocks/openlab-support'


})();

/******/ })()
;
//# sourceMappingURL=index.js.map
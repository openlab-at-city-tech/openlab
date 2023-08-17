/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ (function(module) {

module.exports = window["React"];

/***/ }),

/***/ "@elementor/editor-app-bar":
/*!*****************************************************************!*\
  !*** external ["__UNSTABLE__elementorPackages","editorAppBar"] ***!
  \*****************************************************************/
/***/ (function(module) {

module.exports = window["__UNSTABLE__elementorPackages"]["editorAppBar"];

/***/ }),

/***/ "@elementor/editor-documents":
/*!********************************************************************!*\
  !*** external ["__UNSTABLE__elementorPackages","editorDocuments"] ***!
  \********************************************************************/
/***/ (function(module) {

module.exports = window["__UNSTABLE__elementorPackages"]["editorDocuments"];

/***/ }),

/***/ "@elementor/icons":
/*!**********************************************************!*\
  !*** external ["__UNSTABLE__elementorPackages","icons"] ***!
  \**********************************************************/
/***/ (function(module) {

module.exports = window["__UNSTABLE__elementorPackages"]["icons"];

/***/ }),

/***/ "@elementor/ui":
/*!*******************************************************!*\
  !*** external ["__UNSTABLE__elementorPackages","ui"] ***!
  \*******************************************************/
/***/ (function(module) {

module.exports = window["__UNSTABLE__elementorPackages"]["ui"];

/***/ }),

/***/ "@wordpress/api-fetch":
/*!**********************************!*\
  !*** external ["wp","apiFetch"] ***!
  \**********************************/
/***/ (function(module) {

module.exports = window["wp"]["apiFetch"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ (function(module) {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "@wordpress/url":
/*!*****************************!*\
  !*** external ["wp","url"] ***!
  \*****************************/
/***/ (function(module) {

module.exports = window["wp"]["url"];

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
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
!function() {
/*!***********************************************************************!*\
  !*** ./node_modules/@elementor/editor-site-navigation/dist/index.mjs ***!
  \***********************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "extendIconsMap": function() { return /* binding */ extendIconsMap; }
/* harmony export */ });
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_editor_documents__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-documents */ "@elementor/editor-documents");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/url */ "@wordpress/url");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _elementor_editor_app_bar__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @elementor/editor-app-bar */ "@elementor/editor-app-bar");
// src/icons-map.ts

var initialIconsMap = {
  page: _elementor_icons__WEBPACK_IMPORTED_MODULE_0__.PageTemplateIcon,
  section: _elementor_icons__WEBPACK_IMPORTED_MODULE_0__.SectionTemplateIcon,
  container: _elementor_icons__WEBPACK_IMPORTED_MODULE_0__.ContainerTemplateIcon,
  "wp-page": _elementor_icons__WEBPACK_IMPORTED_MODULE_0__.PageTypeIcon,
  "wp-post": _elementor_icons__WEBPACK_IMPORTED_MODULE_0__.PostTypeIcon
};
var iconsMap = { ...initialIconsMap };
function extendIconsMap(additionalIcons) {
  Object.assign(iconsMap, additionalIcons);
}
function getIconsMap() {
  return iconsMap;
}

// src/components/top-bar/recently-edited.tsx





// src/components/top-bar/indicator.tsx


function Indicator({ title, status }) {
  return /* @__PURE__ */ react__WEBPACK_IMPORTED_MODULE_1__.createElement(Tooltip, { title }, /* @__PURE__ */ react__WEBPACK_IMPORTED_MODULE_1__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Stack, { direction: "row", alignItems: "center", spacing: 2 }, /* @__PURE__ */ react__WEBPACK_IMPORTED_MODULE_1__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, { variant: "body2", sx: { maxWidth: "120px" }, noWrap: true }, title), status.value !== "publish" && /* @__PURE__ */ react__WEBPACK_IMPORTED_MODULE_1__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, { variant: "body2", sx: { fontStyle: "italic" } }, "(", status.label, ")")));
}
function Tooltip(props) {
  return /* @__PURE__ */ react__WEBPACK_IMPORTED_MODULE_1__.createElement(
    _elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Tooltip,
    {
      PopperProps: {
        sx: {
          "&.MuiTooltip-popper .MuiTooltip-tooltip.MuiTooltip-tooltipPlacementBottom": {
            mt: 7
          }
        }
      },
      ...props
    }
  );
}

// src/hooks/use-recent-posts.ts



var endpointPath = "/elementor/v1/site-navigation/recent-posts";
function useRecentPosts(documentId) {
  const [recentPosts, setRecentPosts] = (0,react__WEBPACK_IMPORTED_MODULE_1__.useState)([]);
  const [isLoading, setIsLoading] = (0,react__WEBPACK_IMPORTED_MODULE_1__.useState)(false);
  (0,react__WEBPACK_IMPORTED_MODULE_1__.useEffect)(() => {
    if (documentId) {
      setIsLoading(true);
      fetchRecentlyEditedPosts(documentId).then((posts) => {
        setRecentPosts(posts);
        setIsLoading(false);
      });
    }
  }, [documentId]);
  return {
    isLoading,
    recentPosts
  };
}
async function fetchRecentlyEditedPosts(documentId) {
  const queryParams = {
    posts_per_page: 5,
    post__not_in: documentId
  };
  return await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_4__({
    path: (0,_wordpress_url__WEBPACK_IMPORTED_MODULE_5__.addQueryArgs)(endpointPath, queryParams)
  }).then((response) => response).catch(() => []);
}

// src/components/top-bar/recently-edited.tsx


// src/components/top-bar/chip-doc-type.tsx



var iconsMap2 = getIconsMap();
function DocTypeChip({ postType, docType, label }) {
  const color = "elementor_library" === postType ? "global" : "primary";
  const Icon = iconsMap2[docType] || _elementor_icons__WEBPACK_IMPORTED_MODULE_0__.PostTypeIcon;
  return /* @__PURE__ */ react__WEBPACK_IMPORTED_MODULE_1__.createElement(
    _elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Chip,
    {
      size: "medium",
      variant: "standard",
      label,
      color,
      icon: /* @__PURE__ */ react__WEBPACK_IMPORTED_MODULE_1__.createElement(Icon, null),
      sx: { ml: 3 }
    }
  );
}

// src/components/top-bar/post-list-item.tsx




// src/hooks/use-reverse-html-entities.ts

function useReverseHtmlEntities(escapedHTML = "") {
  return (0,react__WEBPACK_IMPORTED_MODULE_1__.useMemo)(() => {
    const textarea = document.createElement("textarea");
    textarea.innerHTML = escapedHTML;
    const { value } = textarea;
    textarea.remove();
    return value;
  }, [escapedHTML]);
}

// src/components/top-bar/post-list-item.tsx
function PostListItem({ post, closePopup }) {
  const navigateToDocument = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_3__.useNavigateToDocument)();
  const postTitle = useReverseHtmlEntities(post.title);
  return /* @__PURE__ */ react__WEBPACK_IMPORTED_MODULE_1__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.MenuItem, { dense: true, sx: { width: "100%" }, onClick: () => {
    closePopup();
    navigateToDocument(post.id);
  } }, postTitle, /* @__PURE__ */ react__WEBPACK_IMPORTED_MODULE_1__.createElement(DocTypeChip, { postType: post.type.post_type, docType: post.type.doc_type, label: post.type.label }));
}

// src/components/top-bar/create-post-list-item.tsx



// src/hooks/use-create-page.ts


var endpointPath2 = "/elementor/v1/site-navigation/add-new-post";
function useCreatePage() {
  const [isLoading, setIsLoading] = (0,react__WEBPACK_IMPORTED_MODULE_1__.useState)(false);
  return {
    create: () => {
      setIsLoading(true);
      return addNewPage().then((newPost) => newPost).finally(() => setIsLoading(false));
    },
    isLoading
  };
}
async function addNewPage() {
  return await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_4__({
    path: endpointPath2,
    method: "POST",
    data: { post_type: "page" }
  });
}

// src/components/top-bar/create-post-list-item.tsx



function CreatePostListItem({ closePopup }) {
  const { create, isLoading } = useCreatePage();
  const navigateToDocument = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_3__.useNavigateToDocument)();
  return /* @__PURE__ */ react__WEBPACK_IMPORTED_MODULE_1__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.MenuItem, { dense: true, size: "small", color: "inherit", component: "div", onClick: async () => {
    const { id } = await create();
    closePopup();
    navigateToDocument(id);
  } }, /* @__PURE__ */ react__WEBPACK_IMPORTED_MODULE_1__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.ListItemIcon, null, isLoading ? /* @__PURE__ */ react__WEBPACK_IMPORTED_MODULE_1__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.CircularProgress, null) : /* @__PURE__ */ react__WEBPACK_IMPORTED_MODULE_1__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_0__.PlusIcon, null)), (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)("Add new page", "elementor"));
}

// src/components/top-bar/recently-edited.tsx
function RecentlyEdited() {
  const activeDocument = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_3__.useActiveDocument)();
  const hostDocument = (0,_elementor_editor_documents__WEBPACK_IMPORTED_MODULE_3__.useHostDocument)();
  const document2 = activeDocument && activeDocument.type.value !== "kit" ? activeDocument : hostDocument;
  const { recentPosts } = useRecentPosts(document2?.id);
  const popupState = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.usePopupState)({
    variant: "popover",
    popupId: "elementor-v2-top-bar-recently-edited"
  });
  const documentTitle = useReverseHtmlEntities(document2?.title);
  if (!document2) {
    return null;
  }
  return /* @__PURE__ */ react__WEBPACK_IMPORTED_MODULE_1__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Box, { sx: { cursor: "default" } }, /* @__PURE__ */ react__WEBPACK_IMPORTED_MODULE_1__.createElement(
    _elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Button,
    {
      color: "inherit",
      size: "small",
      endIcon: /* @__PURE__ */ react__WEBPACK_IMPORTED_MODULE_1__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_0__.ChevronDownIcon, { fontSize: "small" }),
      ...(0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.bindTrigger)(popupState)
    },
    /* @__PURE__ */ react__WEBPACK_IMPORTED_MODULE_1__.createElement(
      Indicator,
      {
        title: documentTitle,
        status: document2.status
      }
    )
  ), /* @__PURE__ */ react__WEBPACK_IMPORTED_MODULE_1__.createElement(
    _elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Menu,
    {
      MenuListProps: { component: "div" },
      PaperProps: { sx: { mt: 4, minWidth: 314 } },
      ...(0,_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.bindMenu)(popupState)
    },
    /* @__PURE__ */ react__WEBPACK_IMPORTED_MODULE_1__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.ListSubheader, { sx: { fontSize: 12, fontStyle: "italic", pl: 4 }, component: "div", id: "nested-list-subheader" }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)("Recent", "elementor")),
    recentPosts.map((post) => /* @__PURE__ */ react__WEBPACK_IMPORTED_MODULE_1__.createElement(PostListItem, { key: post.id, post, closePopup: popupState.close })),
    recentPosts.length === 0 && /* @__PURE__ */ react__WEBPACK_IMPORTED_MODULE_1__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Typography, { variant: "caption", sx: { color: "grey.500", fontStyle: "italic", p: 4 }, component: "div", "aria-label": void 0 }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)("There are no other pages or templates on this site yet.", "elementor")),
    /* @__PURE__ */ react__WEBPACK_IMPORTED_MODULE_1__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Divider, null),
    /* @__PURE__ */ react__WEBPACK_IMPORTED_MODULE_1__.createElement(CreatePostListItem, { closePopup: popupState.close })
  ));
}

// src/init.ts

function init() {
  registerTopBarMenuItems();
}
function registerTopBarMenuItems() {
  (0,_elementor_editor_app_bar__WEBPACK_IMPORTED_MODULE_7__.injectIntoPageIndication)({
    id: "document-recently-edited",
    filler: RecentlyEdited
  });
}

// src/index.ts
init();


}();
(window.__UNSTABLE__elementorPackages = window.__UNSTABLE__elementorPackages || {}).editorSiteNavigation = __webpack_exports__;
/******/ })()
;
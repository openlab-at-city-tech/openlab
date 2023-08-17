/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/src/site-templates/api.js":
/*!******************************************!*\
  !*** ./assets/src/site-templates/api.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getSiteTemplates: () => (/* binding */ getSiteTemplates)
/* harmony export */ });
/* harmony import */ var _util__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./util */ "./assets/src/site-templates/util.js");
/**
 * Internal dependencies
 */

const {
  endpoint,
  perPage,
  categoryMap
} = window.SiteTemplatePicker;
const currentGroupType = window.CBOXOL_Group_Create.new_group_type;
async function getSiteTemplates(category) {
  let page = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 1;
  let templateCategory;
  if (!category) {
    templateCategory = [0];
    for (var catId in categoryMap) {
      if (-1 !== categoryMap[catId].indexOf(currentGroupType)) {
        templateCategory.push(catId);
      }
    }
  } else {
    templateCategory = category;
  }
  const query = (0,_util__WEBPACK_IMPORTED_MODULE_0__.buildQueryString)({
    _fields: ['id', 'title', 'excerpt', 'featured_media', 'template_category', 'site_id', 'image', 'categories'],
    template_category: templateCategory,
    order: 'desc',
    per_page: Number(perPage),
    page
  });
  const response = await fetch(endpoint + '?' + query);
  const items = await response.json();
  if (!response.ok) {
    throw new Error(items.message);
  }
  const totalPages = Number(response.headers.get('X-WP-TotalPages'));
  const templates = items.map(item => {
    return {
      id: item.id,
      title: item.title.rendered,
      excerpt: item.excerpt.rendered,
      image: item.image,
      categories: item.categories,
      siteId: item.site_id
    };
  });
  return {
    templates,
    prev: page > 1 ? page - 1 : null,
    next: totalPages > page ? page + 1 : null
  };
}

/***/ }),

/***/ "./assets/src/site-templates/util.js":
/*!*******************************************!*\
  !*** ./assets/src/site-templates/util.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   buildQueryString: () => (/* binding */ buildQueryString)
/* harmony export */ });
/**
 * Generates URL-encoded query string using input query data.
 *
 * It is intended to behave equivalent as PHP's `http_build_query`, configured
 * with encoding type PHP_QUERY_RFC3986 (spaces as `%20`).
 *
 * @url @wordpress/url
 * @example
 * ```js
 * const queryString = buildQueryString( {
 *    simple: 'is ok',
 *    arrays: [ 'are', 'fine', 'too' ],
 *    objects: {
 *       evenNested: {
 *          ok: 'yes',
 *       },
 *    },
 * } );
 * // "simple=is%20ok&arrays%5B0%5D=are&arrays%5B1%5D=fine&arrays%5B2%5D=too&objects%5BevenNested%5D%5Bok%5D=yes"
 * ```
 *
 * @param {Record<string,*>} data Data to encode.
 *
 * @return {string} Query string.
 */
function buildQueryString(data) {
  let string = '';
  const stack = Object.entries(data);
  let pair;
  while (pair = stack.shift()) {
    let [key, value] = pair;

    // Support building deeply nested data, from array or object values.
    const hasNestedData = Array.isArray(value) || value && value.constructor === Object;
    if (hasNestedData) {
      // Push array or object values onto the stack as composed of their
      // original key and nested index or key, retaining order by a
      // combination of Array#reverse and Array#unshift onto the stack.
      const valuePairs = Object.entries(value).reverse();
      for (const [member, memberValue] of valuePairs) {
        stack.unshift([`${key}[${member}]`, memberValue]);
      }
    } else if (value !== undefined) {
      // Null is treated as special case, equivalent to empty string.
      if (value === null) {
        value = '';
      }
      string += '&' + [key, value].map(encodeURIComponent).join('=');
    }
  }

  // Loop will concatenate with leading `&`, but it's only expected for all
  // but the first query parameter. This strips the leading `&`, while still
  // accounting for the case that the string may in-fact be empty.
  return string.substr(1);
}

/***/ }),

/***/ "./assets/src/site-templates/site-template-picker.scss":
/*!*************************************************************!*\
  !*** ./assets/src/site-templates/site-template-picker.scss ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


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
/*!*****************************************************!*\
  !*** ./assets/src/site-templates/site-templates.js ***!
  \*****************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./api */ "./assets/src/site-templates/api.js");
/* harmony import */ var _site_template_picker_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./site-template-picker.scss */ "./assets/src/site-templates/site-template-picker.scss");
/**
 * Internal dependencies
 */


const templateCategories = document.querySelector('#site-template-categories');
const templatePicker = document.querySelector('.site-template-picker');
const templatePanel = document.querySelector('.panel-template-picker');
const templatePagination = document.querySelector('.site-template-pagination');
const templateToClone = document.querySelector('[name="source_blog"]');
const setupSiteToggle = document.querySelector('#set-up-site-toggle');
const siteType = document.querySelectorAll('[name="new_or_old"]');
const messages = window.SiteTemplatePicker.messages;
const defaultMap = window.SiteTemplatePicker.defaultMap;
const currentGroupType = window.CBOXOL_Group_Create?.new_group_type || null;

// Cache default template. Usually it's group type site template.
const defaultTemplateForGroupType = currentGroupType && defaultMap.hasOwnProperty(currentGroupType) ? defaultMap[currentGroupType] : 0;
const defaultTemplate = defaultTemplateForGroupType ? defaultTemplateForGroupType.toString() : templateToClone.value;
function renderTemplate(_ref) {
  let {
    id,
    siteId,
    title,
    excerpt,
    image,
    categories
  } = _ref;
  return `
	<button type="button" class="site-template-component" data-template-id="${id}" data-template-site-id="${siteId}">
		<div class="site-template-component__image">
			${image ? `<img src="${image}" alt="${title}">` : `<svg fill="currentColor" width="24" height="24" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path></svg>`}
		</div>
		<div class="site-template-component__meta">
			<span class="site-template-component__category">${categories.join(', ')}</span>
			<div class="site-template-component__name">${title}</div>
			<div class="site-template-component__description">${excerpt}</div>
		</div>
	</button>
	`;
}
function updateTemplates(category, page) {
  templatePicker.innerHTML = `<p>${messages.loading}</p>`;
  (0,_api__WEBPACK_IMPORTED_MODULE_0__.getSiteTemplates)(category, page).then(_ref2 => {
    let {
      templates,
      prev,
      next
    } = _ref2;
    if (!templates.length) {
      templatePicker.innerHTML = `<p>${messages.noResults}</p>`;
      return;
    }
    const compiled = templates.map(template => renderTemplate(template)).join('');
    templatePicker.innerHTML = compiled;

    // Restore template to default value.
    setSelectedTemplateId(defaultTemplate);
    updatePagination(prev, next);
    if (templates.length > 1) {
      const toggleStatus = setupSiteToggle ? setupSiteToggle.checked : true;
      togglePanel(toggleStatus);
    }
  });
}
function updatePagination(prev, next) {
  const prevBtn = templatePagination.querySelector('.prev');
  const nextBtn = templatePagination.querySelector('.next');
  const isVisible = templatePagination.classList.contains('hidden');
  const hide = !prev && !next && !isVisible;

  // Hide pagination if we have only one page.
  if (hide) {
    templatePagination.classList.add('hidden');
  }

  // Button are enabled later if we have pages.
  prevBtn.disabled = true;
  nextBtn.disabled = true;
  if (prev) {
    prevBtn.dataset.page = prev;
    prevBtn.disabled = false;
  }
  if (next) {
    nextBtn.dataset.page = next;
    nextBtn.disabled = false;
  }
}
function togglePanel() {
  let display = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  if (display) {
    // Don't unhide if there's 0 or 1 templates to show.
    const templates = templatePicker.querySelectorAll('.site-template-component');
    if (templates.length > 1) {
      templatePanel.classList.remove('hidden');
    }
    return;
  }
  templatePanel.classList.add('hidden');

  // Restore template to default value.
  setSelectedTemplateId(defaultTemplate);
}
function setSelectedTemplateId(selectedId) {
  const templates = templatePicker.querySelectorAll('.site-template-component');
  templates.forEach(template => {
    const templateId = template.dataset.templateId;
    if (templateId === selectedId) {
      template.classList.add('is-selected');
      templateToClone.value = template.dataset.templateSiteId;
    } else {
      template.classList.remove('is-selected');
    }
  });
}
templateCategories.addEventListener('change', function (event) {
  const category = event.target.value !== '0' ? event.target.value : null;
  templatePicker.innerHTML = `<p>${messages.loading}</p>`;
  updateTemplates(category);
});
templatePicker.addEventListener('click', function (event) {
  const target = event.target.closest('.site-template-component');
  if (!target) {
    return;
  }
  setSelectedTemplateId(target.dataset.templateId);
});
templatePicker.addEventListener('mouseover', function (event) {
  const template = event.target.closest('.site-template-component');
  if (!template) {
    return;
  }

  // Not using toggle since this event does bubble.
  if (!template.classList.contains('has-hover')) {
    template.classList.add('has-hover');
  }
});
templatePicker.addEventListener('mouseout', function (event) {
  const template = event.target.closest('.site-template-component');
  if (!template) {
    return;
  }

  // Not using toggle since this event does bubble.
  if (template.classList.contains('has-hover')) {
    template.classList.remove('has-hover');
  }
});
templatePagination.addEventListener('click', function (event) {
  const target = event.target.closest('.btn');
  if (!target) {
    return;
  }
  const category = templateCategories.value !== '0' ? templateCategories.value : null;
  const page = target.dataset.page ? Number(target.dataset.page) : null;
  updateTemplates(category, page);
});
siteType.forEach(typeSelect => {
  typeSelect.addEventListener('change', event => togglePanel(event.target.value === 'new'));
});
if (setupSiteToggle) {
  setupSiteToggle.addEventListener('change', event => togglePanel(event.target.checked));
  if (setupSiteToggle.checked) {
    // Display the panel.
    togglePanel(true);
  }
} else {
  // If the setupSiteToggle doesn't exist, it means that sites are required for this group type.
  togglePanel(true);
}

// Prefetch templates.
updateTemplates();
})();

/******/ })()
;
//# sourceMappingURL=site-templates.js.map
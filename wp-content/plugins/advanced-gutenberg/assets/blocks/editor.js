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
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assets/js/editor.jsx":
/*!**********************************!*\
  !*** ./src/assets/js/editor.jsx ***!
  \**********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

if (typeof wp !== 'undefined' && typeof wp.domReady !== 'undefined') {
    wp.domReady(function () {

        if (advgb_blocks_vars.blocks.active_blocks === 'undefined' || advgb_blocks_vars.blocks.active_blocks.length === 0) {
            // No Block Access defined for this role, so we stop the process here
            return;
        }

        var gutenberg_init_function = null;
        if (typeof window._wpLoadBlockEditor !== 'undefined') {
            gutenberg_init_function = window._wpLoadBlockEditor;
        }

        if (gutenberg_init_function !== null) {
            // Wait for Gutenberg editor to be ready
            gutenberg_init_function.then(function () {
                if (advgb_blocks_vars.original_settings.allowedBlockTypes !== true) {
                    // allowed_block_types filter has been used, in this case we do nothing as we don't know why blocks have been filtered
                    return;
                }

                var list_blocks = [];
                var granted_blocks = [];
                var missing_block = false;
                // Retrieve all registered blocks
                var blocks = wp.blocks.getBlockTypes();
                var savedBlocks = {
                    active_blocks: Object.values(advgb_blocks_vars.blocks.active_blocks),
                    inactive_blocks: Object.values(advgb_blocks_vars.blocks.inactive_blocks)
                };

                for (var block in blocks) {
                    var blockItemIcon = '';
                    var blockItem = {
                        name: blocks[block].name,
                        icon: blocks[block].icon.src,
                        title: blocks[block].title,
                        category: blocks[block].category,
                        parent: blocks[block].parent
                    };

                    var savedIcon = !!blocks[block].icon.src ? blocks[block].icon.src : blocks[block].icon;

                    if (blocks[block].icon.foreground !== undefined) blockItem.iconColor = blocks[block].icon.foreground;

                    if (typeof savedIcon === 'function') {
                        if (typeof savedIcon.prototype !== 'undefined') {
                            blockItem.icon = wp.element.renderToString(wp.element.createElement(savedIcon));
                            blockItem.icon = blockItem.icon.replace(/stopcolor/g, 'stop-color');
                            blockItem.icon = blockItem.icon.replace(/stopopacity/g, 'stop-opacity');
                        } else {
                            blockItemIcon = wp.element.createElement(wp.components.Dashicon, { icon: 'block-default' });
                            blockItem.icon = wp.element.renderToString(blockItemIcon);
                        }
                    } else if ((typeof savedIcon === 'undefined' ? 'undefined' : _typeof(savedIcon)) === 'object') {
                        blockItem.icon = wp.element.renderToString(savedIcon);
                        blockItem.icon = blockItem.icon.replace(/stopcolor/g, 'stop-color');
                        blockItem.icon = blockItem.icon.replace(/stopopacity/g, 'stop-opacity');
                    } else if (typeof savedIcon === 'string') {
                        blockItemIcon = wp.element.createElement(wp.components.Dashicon, { icon: savedIcon });
                        blockItem.icon = wp.element.renderToString(blockItemIcon);
                    }
                    list_blocks.push(blockItem);

                    // Compare current block with the list of blocks we have
                    if (savedBlocks.active_blocks.indexOf(blocks[block].name) >= 0) {
                        // Block is active
                        granted_blocks.push(blocks[block].name);
                    } else if (savedBlocks.inactive_blocks.indexOf(blocks[block].name) >= 0) {
                        // Block is inactive
                    } else {
                        // This block is not in our database yet, but by default we allow the usage
                        granted_blocks.push(blocks[block].name);
                        missing_block = true;
                    }
                }

                //console.log('missing_block: ' + missing_block);

                if (missing_block) {
                    if (console !== undefined && console.error !== undefined) {
                        // Let's output as log instead of error
                        console.log('Reloading editor by PublishPress Blocks plugin');
                    }
                    // Replace original allowed block settings by our modified list
                    var new_settings = advgb_blocks_vars.original_settings;

                    // Unregister core blocks to avoid registering twice later through wp.editPost.initializeEditor
                    var core_blocks = ['core/paragraph', 'core/image', 'core/heading', 'core/list', 'core/quote', 'core/archives', 'core/audio', 'core/button', 'core/buttons', 'core/calendar', 'core/categories', 'core/code', 'core/columns', 'core/column', 'core/cover', 'core/embed', 'core/group', 'core/freeform', 'core/html', 'core/media-text', 'core/latest-comments', 'core/latest-posts', 'core/missing', 'core/more', 'core/nextpage', 'core/page-list', 'core/preformatted', 'core/pullquote', 'core/rss', 'core/search', 'core/separator', 'core/block', 'core/social-links', 'core/social-link', 'core/spacer', 'core/table', 'core/tag-cloud', 'core/text-columns', 'core/verse', 'core/video', 'core/site-logo', 'core/site-tagline', 'core/site-title', 'core/query', 'core/post-template', 'core/query-title', 'core/query-pagination', 'core/query-pagination-next', 'core/query-pagination-numbers', 'core/query-pagination-previous', 'core/post-title', 'core/post-content', 'core/post-date', 'core/post-excerpt', 'core/post-featured-image', 'core/post-terms', 'core/loginout', 'core/gallery', 'core/shortcode', 'core/file', 'core/pattern', 'core/navigation', 'core/navigation-link', 'core/navigation-submenu', 'core/template-part', 'core/post-author', 'core/post-navigation-link', 'core/post-comments', 'core/term-description'];

                    core_blocks.forEach(function (element) {
                        if (wp.data.select('core/blocks').getBlockType(element)) {
                            wp.blocks.unregisterBlockType(element);
                        }
                    });

                    new_settings.allowedBlockTypes = granted_blocks;
                    var target = document.getElementById('editor');

                    // Initialize again the editor
                    wp.editPost.initializeEditor('editor', advgb_blocks_vars.post_type, advgb_blocks_vars.post_id, new_settings, window._wpGutenbergDefaultPost);

                    var list_categories = wp.blocks.getCategories();

                    try {
                        // Use this ajax query to update the block list in db
                        jQuery.ajax({
                            url: advgb_blocks_vars.ajaxurl,
                            method: 'POST',
                            data: {
                                action: 'advgb_update_blocks_list',
                                blocksList: JSON.stringify(list_blocks),
                                categoriesList: JSON.stringify(list_categories),
                                nonce: advgb_blocks_vars.nonce
                            },
                            success: function success(data) {
                                //console.log(data);
                            }
                        });
                    } catch (e) {
                        //console.log(e);
                    }
                }
            });
        }
    });
}

/***/ }),

/***/ 0:
/*!****************************************!*\
  !*** multi ./src/assets/js/editor.jsx ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! ./src/assets/js/editor.jsx */"./src/assets/js/editor.jsx");


/***/ })

/******/ });
//# sourceMappingURL=editor.js.map
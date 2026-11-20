/******/ (() => { // webpackBootstrap
/*!**************************************************!*\
  !*** ./assets/pages/block-usage/block-usage.jsx ***!
  \**************************************************/
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t.return && (u = t.return(), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
(function (wpI18n, wpHooks, wpBlocks, wpBlockEditor, wpComponents, wpCompose, wpElement) {
  var render = wpElement.render,
    useState = wpElement.useState,
    useEffect = wpElement.useEffect,
    useRef = wpElement.useRef,
    useMemo = wpElement.useMemo;
  var PanelBody = wpComponents.PanelBody,
    Button = wpComponents.Button,
    Card = wpComponents.Card,
    Spinner = wpComponents.Spinner,
    Flex = wpComponents.Flex,
    FlexItem = wpComponents.FlexItem,
    ProgressBar = wpComponents.ProgressBar,
    Notice = wpComponents.Notice,
    FormTokenField = wpComponents.FormTokenField;
  var __ = wpI18n.__,
    sprintf = wpI18n.sprintf;

  // IndexedDB setup
  var DB_NAME = 'publishpress_blocks';
  var STORE_NAME = 'block_usage_data';
  var DB_VERSION = 1;
  var EXCLUDED_BLOCKS = ['advgb/accordion'];
  var openDatabase = function openDatabase() {
    return new Promise(function (resolve, reject) {
      var request = indexedDB.open(DB_NAME, DB_VERSION);
      request.onerror = function (event) {
        console.error('Database error:', event.target.error);
        reject(event.target.error);
      };
      request.onsuccess = function (event) {
        resolve(event.target.result);
      };
      request.onupgradeneeded = function (event) {
        var db = event.target.result;
        if (!db.objectStoreNames.contains(STORE_NAME)) {
          db.createObjectStore(STORE_NAME, {
            keyPath: 'key'
          });
        }
      };
    });
  };
  var getFromCache = function getFromCache(key) {
    return openDatabase().then(function (db) {
      return new Promise(function (resolve, reject) {
        var transaction = db.transaction([STORE_NAME], 'readonly');
        var store = transaction.objectStore(STORE_NAME);
        var request = store.get(key);
        request.onerror = function () {
          return reject('Error reading from cache');
        };
        request.onsuccess = function () {
          return resolve(request.result ? request.result.value : null);
        };
      });
    }).catch(function (error) {
      console.error('Cache error:', error);
      return null;
    });
  };
  var saveToCache = function saveToCache(key, value) {
    return openDatabase().then(function (db) {
      return new Promise(function (resolve, reject) {
        var transaction = db.transaction([STORE_NAME], 'readwrite');
        var store = transaction.objectStore(STORE_NAME);
        var request = store.put({
          key: key,
          value: value
        });
        request.onerror = function () {
          return reject('Error saving to cache');
        };
        request.onsuccess = function () {
          return resolve();
        };
      });
    }).catch(function (error) {
      console.error('Cache error:', error);
      throw error;
    });
  };
  var clearCache = function clearCache() {
    return openDatabase().then(function (db) {
      return new Promise(function (resolve, reject) {
        var transaction = db.transaction([STORE_NAME], 'readwrite');
        var store = transaction.objectStore(STORE_NAME);
        var request = store.clear();
        request.onerror = function () {
          return reject('Error clearing cache');
        };
        request.onsuccess = function () {
          return resolve();
        };
      });
    }).catch(function (error) {
      console.error('Cache error:', error);
      throw error;
    });
  };
  var getBlocks = function getBlocks() {
    if (typeof wp.blocks === 'undefined') {
      return {
        blocks: [],
        categories: []
      };
    }

    // Register core blocks if available
    if (wp.blockLibrary && typeof wp.blockLibrary.registerCoreBlocks === 'function') {
      wp.blockLibrary.registerCoreBlocks();
    }
    var allBlocks = wp.blocks.getBlockTypes();
    var allCategories = wp.blocks.getCategories();
    var listBlocks = [];

    // Get the category titles from localized data if available
    var localizedCategories = (window.advgb_block_usage_data || {}).blockCategories || [];
    var categoryTitleMap = localizedCategories.reduce(function (map, cat) {
      map[cat.slug] = cat.title;
      return map;
    }, {});

    // Get blocks saved in advgb_block_usage_data.saved_blocks option
    var _ref = window.advgb_block_usage_data || {},
      _ref$saved_blocks = _ref.saved_blocks,
      saved_blocks = _ref$saved_blocks === void 0 ? [] : _ref$saved_blocks;
    if (saved_blocks.length > 0) {
      var diff_blocks = saved_blocks.filter(function (blocksA) {
        return !allBlocks.some(function (blocksB) {
          return blocksA.name === blocksB.name;
        });
      });
      if (diff_blocks.length > 0) {
        allBlocks = [].concat(_toConsumableArray(allBlocks), _toConsumableArray(diff_blocks));
      }
    }

    // Force activate blocks (like widget blocks)
    var force_activate_blocks = [{
      'name': 'core/widget-group',
      'icon': 'block-default',
      'title': 'Widget Group',
      'category': 'widgets'
    }];
    allBlocks = [].concat(_toConsumableArray(allBlocks), force_activate_blocks);

    // Additional blocks to include
    var include_blocks = [{
      'name': 'core/legacy-widget',
      'icon': 'block-default',
      'title': 'Legacy Widget',
      'category': 'widgets'
    }];
    allBlocks = [].concat(_toConsumableArray(allBlocks), include_blocks);

    // Process all blocks to standardize the format
    allBlocks.forEach(function (block) {
      // Skip excluded blocks
      if (EXCLUDED_BLOCKS.includes(block.name)) {
        return;
      }
      var blockItemIcon = '';
      var blockItem = {
        name: block.name,
        icon: block.icon.src || block.icon,
        title: block.title,
        category: block.category || 'common',
        parent: block.parent,
        description: block.description || ''
      };

      // Use the localized category title if available
      if (categoryTitleMap[blockItem.category]) {
        blockItem.categoryTitle = categoryTitleMap[blockItem.category];
      }
      var savedIcon = !!block.icon.src ? block.icon.src : block.icon;
      if (block.icon.foreground !== undefined) blockItem.iconColor = block.icon.foreground;
      if (typeof savedIcon === 'function') {
        if (typeof savedIcon.prototype !== 'undefined') {
          blockItem.icon = wp.element.renderToString(wp.element.createElement(savedIcon));
          blockItem.icon = blockItem.icon.replace(/stopcolor/g, 'stop-color');
          blockItem.icon = blockItem.icon.replace(/stopopacity/g, 'stop-opacity');
        } else {
          blockItemIcon = wp.element.createElement(wp.components.Dashicon, {
            icon: 'block-default'
          });
          blockItem.icon = wp.element.renderToString(blockItemIcon);
        }
      } else if (_typeof(savedIcon) === 'object') {
        blockItem.icon = wp.element.renderToString(savedIcon);
        blockItem.icon = blockItem.icon.replace(/stopcolor/g, 'stop-color');
        blockItem.icon = blockItem.icon.replace(/stopopacity/g, 'stop-opacity');
      } else if (typeof savedIcon === 'string' && !savedIcon.includes('<span') // Merged blocks icons from 'advgb_blocks_list' are stored as html
      && !savedIcon.includes('<svg') // Merged blocks icons from 'advgb_blocks_list' are stored as html
      ) {
        blockItemIcon = wp.element.createElement(wp.components.Dashicon, {
          icon: savedIcon
        });
        blockItem.icon = wp.element.renderToString(blockItemIcon);
      } else {
        blockItem.icon = savedIcon; // Pure html for merged blocks icons from 'advgb_blocks_list'
      }
      listBlocks.push(blockItem);
    });

    // Remove duplicated blocks by block name
    var uniqueNames = [];
    var i = listBlocks.length;
    while (i--) {
      var name = listBlocks[i].name;
      if (uniqueNames.includes(name)) {
        listBlocks.splice(i, 1);
      } else {
        uniqueNames.push(name);
      }
    }

    // Sort categories to show "advgb-category" first
    allCategories.sort(function (a, b) {
      if (a.slug === 'advgb-category') return -1;
      if (b.slug === 'advgb-category') return 1;
      return 0;
    });

    // Add any missing categories from localized data
    localizedCategories.forEach(function (localizedCat) {
      if (!allCategories.some(function (cat) {
        return cat.slug === localizedCat.slug;
      })) {
        allCategories.push({
          slug: localizedCat.slug,
          title: localizedCat.title
        });
      }
    });
    return {
      blocks: listBlocks,
      categories: allCategories
    };
  };
  var WelcomeIntro = function WelcomeIntro(_ref2) {
    var onScanClick = _ref2.onScanClick,
      loadingAll = _ref2.loadingAll,
      data = _ref2.data;
    var totalBlocks = Object.keys(data.usage || {}).length;
    var totalPosts = data.posts && data.posts.length || 0;
    var lastScanDate = data.lastScanDate;
    return /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-welcome-intro"
    }, /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-welcome-card"
    }, /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-welcome-icon"
    }, /*#__PURE__*/React.createElement("span", {
      className: "dashicons dashicons-block-default"
    })), /*#__PURE__*/React.createElement("h2", null, __('Welcome to Block Usage', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, __('This screen allows you to search for and find any usage of blocks on your site.', 'advanced-gutenberg')), lastScanDate && /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-stats-overview"
    }, /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-stat-card"
    }, /*#__PURE__*/React.createElement("span", {
      className: "pp-blocks-usage-stat-number"
    }, totalBlocks), /*#__PURE__*/React.createElement("span", {
      className: "pp-blocks-usage-stat-label"
    }, __('Blocks Found', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-stat-card"
    }, /*#__PURE__*/React.createElement("span", {
      className: "pp-blocks-usage-stat-number"
    }, totalPosts), /*#__PURE__*/React.createElement("span", {
      className: "pp-blocks-usage-stat-label"
    }, __('Posts Scanned', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-stat-card"
    }, /*#__PURE__*/React.createElement("span", {
      className: "pp-blocks-usage-stat-label"
    }, __('Last Scan', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("span", {
      className: "pp-blocks-usage-stat-number",
      style: {
        fontSize: '0.9em'
      }
    }, new Date(lastScanDate).toLocaleDateString()))), /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-features-grid"
    }, /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-feature-card"
    }, /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-feature-icon"
    }, /*#__PURE__*/React.createElement("span", {
      className: "dashicons dashicons-search"
    })), /*#__PURE__*/React.createElement("h3", null, __('Scan Content', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, __('Find all blocks used across your posts, pages, and custom post types', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-feature-card"
    }, /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-feature-icon"
    }, /*#__PURE__*/React.createElement("span", {
      className: "dashicons dashicons-analytics"
    })), /*#__PURE__*/React.createElement("h3", null, __('Usage Analytics', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, __('See detailed statistics about block usage and locations', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-feature-card"
    }, /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-feature-icon"
    }, /*#__PURE__*/React.createElement("span", {
      className: "dashicons dashicons-admin-links"
    })), /*#__PURE__*/React.createElement("h3", null, __('Quick Navigation', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("p", null, __('Jump directly to posts containing specific blocks for editing', 'advanced-gutenberg')))), /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-welcome-scan-button"
    }, /*#__PURE__*/React.createElement("button", {
      className: "button button-secondary",
      onClick: onScanClick,
      disabled: loadingAll
    }, loadingAll ? /*#__PURE__*/React.createElement(Spinner, null) : __('Scan Block Usage', 'advanced-gutenberg')))));
  };
  var Sidebar = React.memo(function (_ref3) {
    var selected = _ref3.selected,
      data = _ref3.data,
      onClose = _ref3.onClose,
      canEditPosts = _ref3.canEditPosts;
    var _useState = useState(null),
      _useState2 = _slicedToArray(_useState, 2),
      expandedPost = _useState2[0],
      setExpandedPost = _useState2[1];
    var _data$usage = data.usage,
      usage = _data$usage === void 0 ? {} : _data$usage,
      _data$posts = data.posts,
      posts = _data$posts === void 0 ? [] : _data$posts;
    var selectedName = selected && selected.name ? selected.name : null;
    var blockData = usage[selectedName] || {
      posts: {},
      total: 0
    };
    var postIds = Object.keys(blockData.posts);

    // Get posts that contain this block
    var blockPosts = useMemo(function () {
      return posts.filter(function (post) {
        return postIds.includes(post.post_id.toString());
      });
    }, [posts, postIds]);

    // Group posts by title (for multiple instances in same post)
    var groupedPosts = useMemo(function () {
      var groups = {};
      blockPosts.forEach(function (post) {
        var postUsage = blockData.posts[post.post_id] || {};
        var count = postUsage.count || 0;
        var scanned = postUsage.scanned || '';
        var key = "".concat(post.post_title, "-").concat(post.post_id);
        if (!groups[key]) {
          groups[key] = _objectSpread(_objectSpread({}, post), {}, {
            totalCount: 0,
            scanned: scanned
          });
        }
        groups[key].totalCount += count;
      });
      return Object.values(groups);
    }, [blockPosts, blockData]);
    var groupBlocksByName = function groupBlocksByName(blocks) {
      var grouped = {};
      blocks.forEach(function (block) {
        if (!block.name) return;
        var key = block.name;
        if (!grouped[key]) {
          grouped[key] = _objectSpread(_objectSpread({}, block), {}, {
            count: 0,
            innerBlocks: []
          });
        }
        grouped[key].count += 1;
        if (block.innerBlocks && block.innerBlocks.length > 0) {
          var _grouped$key$innerBlo;
          (_grouped$key$innerBlo = grouped[key].innerBlocks).push.apply(_grouped$key$innerBlo, _toConsumableArray(block.innerBlocks));
        }
      });
      return Object.values(grouped);
    };
    var renderBlockItem = function renderBlockItem(block) {
      var depth = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 0;
      if (!block.name) return null;
      var groupedInnerBlocks = block.innerBlocks && block.innerBlocks.length > 0 ? groupBlocksByName(block.innerBlocks) : [];
      return /*#__PURE__*/React.createElement("div", {
        key: block.clientId || "".concat(block.name, "-").concat(depth, "-").concat(Math.random().toString(36).substr(2, 9)),
        className: "pp-blocks-usage-post-block-item",
        style: {
          marginLeft: "".concat(depth * 15, "px")
        }
      }, /*#__PURE__*/React.createElement("div", {
        className: "pp-blocks-usage-block-name"
      }, block.icon && /*#__PURE__*/React.createElement("span", {
        className: "block-icon",
        style: block.iconColor ? {
          color: block.iconColor
        } : {}
      }, typeof block.icon === 'string' && !block.icon.includes('<') ? /*#__PURE__*/React.createElement("span", {
        className: "dashicons dashicons-".concat(block.icon)
      }) : /*#__PURE__*/React.createElement("span", {
        dangerouslySetInnerHTML: {
          __html: block.icon
        }
      })), block.name, block.count > 1 && /*#__PURE__*/React.createElement("span", {
        className: "pp-blocks-usage-block-count"
      }, "\xD7", block.count)), groupedInnerBlocks.map(function (innerBlock) {
        return renderBlockItem(innerBlock, depth + 1);
      }));
    };
    return /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-sidebar",
      ref: useRef(null)
    }, /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-sidebar-header"
    }, /*#__PURE__*/React.createElement("h2", null, /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-tile-main"
    }, /*#__PURE__*/React.createElement("span", null, selected.title)), /*#__PURE__*/React.createElement("span", {
      className: "pp-blocks-usage-panel-subtitle"
    }, groupedPosts.length, " ", __('locations', 'advanced-gutenberg'), " | ", blockData.total, " ", __('instances', 'advanced-gutenberg')))), /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-sidebar-content"
    }, /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-block-meta"
    }, /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("strong", null, __('Name:', 'advanced-gutenberg')), " ", selected.title), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("strong", null, __('Description:', 'advanced-gutenberg')), " ", selected.description), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("strong", null, __('Category:', 'advanced-gutenberg')), " ", selected.category)), groupedPosts.length > 0 ? /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-usage-stats"
    }, /*#__PURE__*/React.createElement("span", null, /*#__PURE__*/React.createElement("strong", null, __('Locations:', 'advanced-gutenberg')), " ", groupedPosts.length), /*#__PURE__*/React.createElement("span", null, /*#__PURE__*/React.createElement("strong", null, __('Total instances:', 'advanced-gutenberg')), " ", blockData.total)), /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-post-list"
    }, groupedPosts.map(function (post) {
      var isExpanded = expandedPost === post.post_id;
      var postTypeLabel = post.post_type;
      return /*#__PURE__*/React.createElement("div", {
        key: post.post_id,
        className: "pp-blocks-usage-post-item ".concat(isExpanded ? 'expanded' : '')
      }, /*#__PURE__*/React.createElement("div", {
        className: "pp-blocks-usage-post-header",
        onClick: function onClick() {
          return setExpandedPost(isExpanded ? null : post.post_id);
        }
      }, /*#__PURE__*/React.createElement("div", {
        className: "pp-blocks-usage-post-title"
      }, post.post_title), /*#__PURE__*/React.createElement("div", {
        className: "pp-blocks-usage-post-meta"
      }, /*#__PURE__*/React.createElement("span", {
        className: "pp-blocks-usage-post-type"
      }, postTypeLabel)), /*#__PURE__*/React.createElement(Button, {
        size: "small",
        icon: isExpanded ? 'arrow-up-alt2' : 'arrow-down-alt2',
        className: "pp-blocks-usage-expand-button",
        label: sprintf(/* translators: %s: post title */
        __('Show blocks in %s', 'advanced-gutenberg'), post.post_title),
        "aria-expanded": isExpanded,
        onClick: function onClick(e) {
          e.stopPropagation();
          setExpandedPost(isExpanded ? null : post.post_id);
        }
      })), isExpanded && /*#__PURE__*/React.createElement("div", {
        className: "pp-blocks-usage-post-content"
      }, /*#__PURE__*/React.createElement("div", {
        className: "pp-blocks-usage-post-actions"
      }, post.edit_link && canEditPosts && /*#__PURE__*/React.createElement(Button, {
        variant: "secondary",
        isSmall: true,
        href: post.edit_link,
        target: "_blank"
      }, __('Edit Post', 'advanced-gutenberg')), /*#__PURE__*/React.createElement(Button, {
        variant: "secondary",
        size: "small",
        href: post.view_link,
        target: "_blank"
      }, __('View Post', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("div", {
        className: "pp-blocks-usage-post-blocks"
      }, /*#__PURE__*/React.createElement("h4", null, __('Blocks in this post:', 'advanced-gutenberg')), post.blocks && post.blocks.length > 0 ? groupBlocksByName(post.blocks).map(function (block) {
        return renderBlockItem(block);
      }) : /*#__PURE__*/React.createElement("p", null, __('No blocks found in this post.', 'advanced-gutenberg')))));
    }))) : /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-no-results"
    }, data.lastScanDate ? /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("em", null, __('This block was not found in any posts.', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("p", null, __('Last scan:', 'advanced-gutenberg'), " ", new Date(data.lastScanDate).toLocaleString())) : /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("em", null, __('No scan history available for this block.', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("p", null, __('Click the Scan button to check for usage.', 'advanced-gutenberg'))))));
  });
  var App = function App() {
    var _ref4 = window.advgb_block_usage_data || {},
      _ref4$ajaxUrl = _ref4.ajaxUrl,
      ajaxUrl = _ref4$ajaxUrl === void 0 ? '' : _ref4$ajaxUrl,
      _ref4$nonce = _ref4.nonce,
      nonce = _ref4$nonce === void 0 ? '' : _ref4$nonce,
      initialData = _ref4.initialData,
      currentUser = _ref4.currentUser;
    var _useState3 = useState(function () {
        return getBlocks();
      }),
      _useState4 = _slicedToArray(_useState3, 2),
      blocksData = _useState4[0],
      setBlocksData = _useState4[1];
    var _useState5 = useState(initialData),
      _useState6 = _slicedToArray(_useState5, 2),
      data = _useState6[0],
      setData = _useState6[1];
    var _useState7 = useState(false),
      _useState8 = _slicedToArray(_useState7, 2),
      loadingAll = _useState8[0],
      setLoadingAll = _useState8[1];
    var _useState9 = useState(false),
      _useState0 = _slicedToArray(_useState9, 2),
      loadingClearAll = _useState0[0],
      setLoadingClearAll = _useState0[1];
    var _useState1 = useState(null),
      _useState10 = _slicedToArray(_useState1, 2),
      selected = _useState10[0],
      setSelected = _useState10[1];
    var _useState11 = useState(false),
      _useState12 = _slicedToArray(_useState11, 2),
      showEmptyBlocks = _useState12[0],
      setShowEmptyBlocks = _useState12[1];
    var _useState13 = useState(['post', 'page']),
      _useState14 = _slicedToArray(_useState13, 2),
      selectedPostTypes = _useState14[0],
      setSelectedPostTypes = _useState14[1];
    var _useState15 = useState({
        current: 0,
        total: 0,
        status: '',
        completed: false
      }),
      _useState16 = _slicedToArray(_useState15, 2),
      scanProgress = _useState16[0],
      setScanProgress = _useState16[1];
    var _useState17 = useState(null),
      _useState18 = _slicedToArray(_useState17, 2),
      dbError = _useState18[0],
      setDbError = _useState18[1];
    var _useState19 = useState(false),
      _useState20 = _slicedToArray(_useState19, 2),
      initialLoadComplete = _useState20[0],
      setInitialLoadComplete = _useState20[1];

    // Enable header scan button when component mounts
    useEffect(function () {
      if (initialLoadComplete) {
        var headerButton = document.getElementById('header-scan-button');
        if (headerButton) {
          headerButton.disabled = false;
          headerButton.style.opacity = '1';
          headerButton.style.cursor = 'pointer';
          headerButton.addEventListener('click', function (e) {
            e.preventDefault();
            scanAll();
          });
        }
      }
    }, [initialLoadComplete]);

    // Load data from IndexedDB on initial render
    useEffect(function () {
      Promise.all([getFromCache('block_usage_data'), getFromCache('block_usage_settings')]).then(function (_ref5) {
        var _ref6 = _slicedToArray(_ref5, 2),
          cachedData = _ref6[0],
          cachedSettings = _ref6[1];
        if (cachedData) {
          setData(cachedData);
        }
        if (cachedSettings) {
          setShowEmptyBlocks(cachedSettings.showEmptyBlocks !== false);
          if (cachedSettings.lastSelectedBlock) {
            var lastSelected = blocksData.blocks.find(function (b) {
              return b.name === cachedSettings.lastSelectedBlock;
            });
            if (lastSelected) {
              setSelected(lastSelected);
            }
          }
          if (cachedSettings.selectedPostTypes) {
            setSelectedPostTypes(cachedSettings.selectedPostTypes);
          }
        }
      }).catch(function (error) {
        console.error('Failed to load data from IndexedDB:', error);
        setDbError(__('Failed to load data from local storage. Please refresh the page.', 'advanced-gutenberg'));
      }).finally(function () {
        setInitialLoadComplete(true);
      });
    }, [blocksData.blocks]);
    var postAjax = function postAjax(action) {
      var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      var body = new URLSearchParams(_objectSpread({
        action: action,
        nonce: nonce
      }, data));
      return fetch(ajaxUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: body
      }).then(function (r) {
        return r.json();
      });
    };
    var handleToggleChange = function handleToggleChange(value) {
      setShowEmptyBlocks(value);
      saveToCache('block_usage_settings', {
        showEmptyBlocks: value,
        lastSelectedBlock: selected && selected.name ? selected.name : '',
        selectedPostTypes: selectedPostTypes
      }).catch(function (error) {
        console.error('Failed to save showEmptyBlocks setting:', error);
      });
    };
    var handlePostTypeChange = function handlePostTypeChange(newPostTypes) {
      if (!window.advgb_block_usage_data.isProActive) {
        return;
      }
      setSelectedPostTypes(newPostTypes);
      saveToCache('block_usage_settings', {
        showEmptyBlocks: showEmptyBlocks,
        lastSelectedBlock: selected && selected.name ? selected.name : '',
        selectedPostTypes: newPostTypes
      }).catch(function (error) {
        console.error('Failed to save post types:', error);
      });
    };
    var categories = useMemo(function () {
      var cats = {};

      // First create the advgb-category if it exists
      var advgbCategory = blocksData.categories.find(function (cat) {
        return cat.slug === 'advgb-category';
      });
      if (advgbCategory) {
        cats[advgbCategory.slug] = {
          title: advgbCategory.title,
          blocks: []
        };
      }

      // Then add all other categories
      blocksData.categories.forEach(function (cat) {
        if (cat.slug !== 'advgb-category') {
          cats[cat.slug] = {
            title: cat.title,
            blocks: []
          };
        }
      });

      // Assign blocks to categories
      blocksData.blocks.forEach(function (block) {
        var catSlug = block.category || 'common';
        if (!cats[catSlug]) {
          cats[catSlug] = {
            title: block.categoryTitle || catSlug,
            blocks: []
          };
        }
        cats[catSlug].blocks.push(block);
      });
      return cats;
    }, [blocksData.categories, blocksData.blocks]);
    var filteredCategories = useMemo(function () {
      if (showEmptyBlocks) return categories;
      var filtered = {};
      Object.entries(categories).forEach(function (_ref7) {
        var _ref8 = _slicedToArray(_ref7, 2),
          slug = _ref8[0],
          category = _ref8[1];
        var filteredBlocks = category.blocks.filter(function (bt) {
          return data.usage[bt.name] && Object.keys(data.usage[bt.name].posts).length > 0;
        });
        if (filteredBlocks.length > 0) {
          filtered[slug] = _objectSpread(_objectSpread({}, category), {}, {
            blocks: filteredBlocks
          });
        }
      });
      return filtered;
    }, [categories, data.usage, showEmptyBlocks]);
    var categoryStats = useMemo(function () {
      var stats = {};
      Object.entries(filteredCategories).forEach(function (_ref9) {
        var _ref0 = _slicedToArray(_ref9, 2),
          slug = _ref0[0],
          category = _ref0[1];
        var totalBlocks = category.blocks.length;
        var totalLocations = 0;
        var totalInstances = 0;
        category.blocks.forEach(function (block) {
          var blockData = data.usage[block.name];
          if (blockData && blockData.posts) {
            totalLocations += Object.keys(blockData.posts).length;
            totalInstances += blockData.total || 0;
          }
        });
        stats[slug] = {
          blocks: totalBlocks,
          locations: totalLocations,
          instances: totalInstances
        };
      });
      return stats;
    }, [filteredCategories, data.usage]);

    // Check if we should show the welcome intro
    var shouldShowWelcomeIntro = useMemo(function () {
      // Show intro if no scan data exists or if there are no blocks with usage data
      var hasScanData = data.lastScanDate && Object.keys(data.usage).length > 0;
      var hasBlocksWithUsage = Object.values(data.usage).some(function (blockData) {
        return blockData.posts && Object.keys(blockData.posts).length > 0;
      });
      return !hasScanData || !hasBlocksWithUsage;
    }, [data]);
    useEffect(function () {
      if (!selected && blocksData.blocks.length > 0 && initialLoadComplete) {
        var firstCategory = Object.values(filteredCategories)[0];
        if (firstCategory && firstCategory.blocks.length > 0) {
          setSelected(firstCategory.blocks[0]);
        }
      }
    }, [filteredCategories, blocksData.blocks, initialLoadComplete]);
    var scanAll = function scanAll() {
      setLoadingAll(true);
      setScanProgress({
        current: 0,
        total: 0,
        status: __('Initializing scan...', 'advanced-gutenberg'),
        completed: false
      });
      var offset = 0;
      var batchSize = 20;
      var newData = {
        usage: {},
        posts: [],
        lastScanDate: ''
      };
      var totalPosts = 0;
      var hasMorePosts = true;
      var processBatch = function processBatch() {
        if (!hasMorePosts) {
          return Promise.resolve();
        }
        setScanProgress(function (prev) {
          return _objectSpread(_objectSpread({}, prev), {}, {
            status: sprintf(__('Processing posts (%1$s/%2$s)', 'advanced-gutenberg'), offset.toLocaleString(), totalPosts.toLocaleString())
          });
        });
        return postAjax('pp_blocks-usage_scan_batch', {
          offset: offset,
          batch_size: batchSize,
          post_types: selectedPostTypes
        }).then(function (batchResponse) {
          if (!batchResponse.success) {
            throw new Error(__('Batch scan failed', 'advanced-gutenberg'));
          }
          if (batchResponse.data.total) {
            totalPosts = batchResponse.data.total;
          }

          // Merge usage data
          Object.entries(batchResponse.data.usage || {}).forEach(function (_ref1) {
            var _ref10 = _slicedToArray(_ref1, 2),
              blockName = _ref10[0],
              blockData = _ref10[1];
            if (!newData.usage[blockName]) {
              newData.usage[blockName] = {
                posts: {},
                total: 0
              };
            }
            Object.entries(blockData.posts || {}).forEach(function (_ref11) {
              var _ref12 = _slicedToArray(_ref11, 2),
                postId = _ref12[0],
                entry = _ref12[1];
              newData.usage[blockName].posts[postId] = entry;
            });
            newData.usage[blockName].total += blockData.total || 0;
          });

          // Merge posts
          newData.posts = [].concat(_toConsumableArray(newData.posts), _toConsumableArray(batchResponse.data.posts || []));
          newData.lastScanDate = batchResponse.data.lastScanDate || newData.lastScanDate;
          offset += batchResponse.data.processed || 0;
          hasMorePosts = offset < totalPosts;
          setScanProgress(function (prev) {
            return _objectSpread(_objectSpread({}, prev), {}, {
              current: offset,
              total: totalPosts
            });
          });

          // Update local state with the new data
          setData(newData);
          return saveToCache('block_usage_data', newData).catch(function (error) {
            console.error('Failed to save batch to IndexedDB:', error);
          }).then(function () {
            return new Promise(function (resolve) {
              return setTimeout(resolve, 100);
            });
          }).then(processBatch);
        });
      };
      processBatch().then(function () {
        setScanProgress(function (prev) {
          return _objectSpread(_objectSpread({}, prev), {}, {
            completed: true,
            status: __('Scan completed successfully!', 'advanced-gutenberg')
          });
        });
      }).catch(function (error) {
        console.error('Scan error:', error);
        setScanProgress(function (prev) {
          return _objectSpread(_objectSpread({}, prev), {}, {
            status: __('Scan failed: ', 'advanced-gutenberg') + error.message
          });
        });
      }).finally(function () {
        setTimeout(function () {
          setLoadingAll(false);
          setScanProgress({
            current: 0,
            total: 0,
            status: '',
            completed: false
          });
        }, 2000);
      });
    };
    var clearAllData = function clearAllData() {
      setLoadingClearAll(true);
      clearCache().then(function () {
        setData({
          usage: {},
          posts: [],
          lastScanDate: ''
        });
        setShowEmptyBlocks(false);
        setSelected(null);

        // Clear settings as well
        return saveToCache('block_usage_settings', {
          showEmptyBlocks: false,
          lastSelectedBlock: ''
        });
      }).catch(function (error) {
        console.error('Failed to clear data:', error);
        setDbError(__('Failed to clear data. Please try again.', 'advanced-gutenberg'));
      }).finally(function () {
        setLoadingClearAll(false);
      });
    };
    var handleDetailsClick = function handleDetailsClick(bt) {
      var isSameSelected = selected && selected.name === bt.name;
      var newSelected = isSameSelected ? null : bt;
      setSelected(newSelected);

      // Save the last selected block
      if (newSelected) {
        saveToCache('block_usage_settings', {
          showEmptyBlocks: showEmptyBlocks,
          lastSelectedBlock: newSelected.name
        }).catch(function (error) {
          console.error('Failed to save last selected block:', error);
        });
      }
    };
    if (!initialLoadComplete) {
      return /*#__PURE__*/React.createElement("div", {
        className: "pp-blocks-usage-wrapper"
      }, /*#__PURE__*/React.createElement("div", {
        className: "advgb-block-feature-loading-msg",
        style: {
          display: 'block'
        }
      }, __('Loading...', 'advanced-gutenberg'), " ", /*#__PURE__*/React.createElement(Spinner, null)));
    }
    var proHtml = '';
    var wrapperCalss = '';
    var blurClass = '';
    if (!window.advgb_block_usage_data.isProActive) {
      wrapperCalss = 'advgb-promo-overlay-area';
      blurClass = 'advgb-blur';
      proHtml = /*#__PURE__*/React.createElement("div", {
        className: "advgb-pro-small-overlay-text"
      }, /*#__PURE__*/React.createElement("a", {
        className: "advgb-pro-link clickable",
        href: window.advgb_block_usage_data.promoLink,
        target: "_blank",
        rel: "noopener noreferrer"
      }, /*#__PURE__*/React.createElement("span", {
        className: "dashicons dashicons-lock"
      }), window.advgb_block_usage_data.proText));
    }
    return /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-wrapper ".concat(selected ? 'has-sidebar' : '')
    }, dbError && /*#__PURE__*/React.createElement(Notice, {
      status: "error",
      onRemove: function onRemove() {
        return setDbError(null);
      }
    }, dbError), /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-header"
    }, /*#__PURE__*/React.createElement(Flex, {
      justify: "space-between",
      align: "center"
    }, /*#__PURE__*/React.createElement(FlexItem, null, /*#__PURE__*/React.createElement("div", {
      className: "".concat(wrapperCalss, " pp-blocks-usage-controls")
    }, proHtml, window.advgb_block_usage_data && window.advgb_block_usage_data.postTypes && /*#__PURE__*/React.createElement("div", {
      className: "".concat(blurClass, " pp-blocks-usage-post-type-filter")
    }, /*#__PURE__*/React.createElement(FormTokenField, {
      label: __('Limit Scan to Post Types:', 'advanced-gutenberg'),
      value: selectedPostTypes,
      suggestions: Object.keys(window.advgb_block_usage_data.postTypes),
      onChange: function onChange(newPostTypes) {
        return handlePostTypeChange(newPostTypes);
      },
      displayTransform: function displayTransform(postType) {
        return window.advgb_block_usage_data.postTypes[postType] || postType;
      },
      tokenizeOnSpace: false,
      __experimentalExpandOnFocus: true,
      __experimentalShowHowTo: false,
      style: {
        minWidth: '300px',
        marginRight: '12px'
      }
    }), /*#__PURE__*/React.createElement(Button, {
      style: {
        marginTop: '13px'
      },
      className: "button",
      onClick: scanAll,
      disabled: loadingAll
    }, loadingAll ? /*#__PURE__*/React.createElement(Spinner, null) : __('Filter', 'advanced-gutenberg'))))), /*#__PURE__*/React.createElement(FlexItem, null, /*#__PURE__*/React.createElement("div", {
      className: "advgb-toggle-wrapper"
    }, __('Show Empty Blocks', 'advanced-gutenberg'), /*#__PURE__*/React.createElement("div", {
      className: "advgb-switch-button"
    }, /*#__PURE__*/React.createElement("label", {
      className: "switch"
    }, /*#__PURE__*/React.createElement("input", {
      type: "checkbox",
      name: "toggle_empty_blocks",
      id: "toggle_empty_blocks",
      "aria-label": __('Show Empty Blocks', 'advanced-gutenberg'),
      checked: showEmptyBlocks,
      onChange: function onChange(e) {
        return handleToggleChange(e.target.checked);
      }
    }), /*#__PURE__*/React.createElement("span", {
      className: "slider",
      "aria-hidden": "true"
    }))))))), loadingAll && /*#__PURE__*/React.createElement("div", {
      className: "scan-progress-container"
    }, /*#__PURE__*/React.createElement("div", {
      className: "scan-progress"
    }, /*#__PURE__*/React.createElement("p", {
      role: "status",
      "aria-live": "polite"
    }, scanProgress.status), /*#__PURE__*/React.createElement("div", {
      className: "progress-wrap"
    }, /*#__PURE__*/React.createElement(ProgressBar, {
      value: scanProgress.total > 0 ? scanProgress.current / scanProgress.total * 100 : 0,
      "aria-label": __('Scan progress', 'advanced-gutenberg')
    })))), data.lastScanDate && /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-last-scan"
    }, /*#__PURE__*/React.createElement("span", null, __('Last scan:', 'advanced-gutenberg'), " ", new Date(data.lastScanDate).toLocaleString()), /*#__PURE__*/React.createElement("div", {
      className: "ppb-tooltips-library",
      "data-toggle": "ppbtooltip",
      "data-placement": "left"
    }, !loadingClearAll && /*#__PURE__*/React.createElement("span", {
      className: "dashicons dashicons-editor-help",
      style: {
        verticalAlign: 'sub',
        lineHeight: 'inherit'
      }
    }), /*#__PURE__*/React.createElement("button", {
      className: "button button-secondary advgb-destructive-button",
      onClick: clearAllData,
      disabled: loadingAll || loadingClearAll,
      style: {
        marginLeft: '8px'
      }
    }, loadingClearAll ? /*#__PURE__*/React.createElement(Spinner, null) : __('Clear All Data', 'advanced-gutenberg')), !loadingClearAll && /*#__PURE__*/React.createElement("span", {
      className: "tooltip-text"
    }, /*#__PURE__*/React.createElement("span", null, __('Scan data is stored in your browser to improve performance and handle large datasets. This keeps your WordPress database clean. Click to permanently delete all stored data.', 'advanced-gutenberg')), /*#__PURE__*/React.createElement("i", null)))), /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-container"
    }, shouldShowWelcomeIntro ? /*#__PURE__*/React.createElement(WelcomeIntro, {
      onScanClick: scanAll,
      loadingAll: loadingAll,
      data: data
    }) : /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("div", {
      className: "pp-blocks-usage-categories"
    }, Object.entries(filteredCategories).map(function (_ref13) {
      var _ref14 = _slicedToArray(_ref13, 2),
        slug = _ref14[0],
        _ref14$ = _ref14[1],
        title = _ref14$.title,
        blocks = _ref14$.blocks;
      var stats = categoryStats[slug] || {
        blocks: 0,
        locations: 0,
        instances: 0
      };
      var pluralize = function pluralize(count, singular, plural) {
        return count === 1 ? singular : plural;
      };
      var categoryTitle = /*#__PURE__*/React.createElement("span", {
        className: "pp-blocks-usage-category-title"
      }, /*#__PURE__*/React.createElement("span", {
        className: "category-name"
      }, title), /*#__PURE__*/React.createElement("span", {
        className: "category-stats"
      }, "(", stats.blocks, " ", pluralize(stats.blocks, __('block', 'advanced-gutenberg'), __('blocks', 'advanced-gutenberg')), ", ", stats.locations, " ", pluralize(stats.locations, __('location', 'advanced-gutenberg'), __('locations', 'advanced-gutenberg')), ", ", stats.instances, " ", pluralize(stats.instances, __('instance', 'advanced-gutenberg'), __('instances', 'advanced-gutenberg')), ")"));
      return /*#__PURE__*/React.createElement(PanelBody, {
        key: slug,
        title: categoryTitle,
        initialOpen: true
      }, /*#__PURE__*/React.createElement("div", {
        className: "pp-blocks-usage-block-grid"
      }, blocks.map(function (bt) {
        var hasData = data.usage[bt.name] && Object.keys(data.usage[bt.name].posts).length > 0;
        var blockData = data.usage[bt.name] || {
          posts: {},
          total: 0
        };
        var postCount = Object.keys(blockData.posts).length;
        var useCount = blockData.total;
        var firstBlockPost = Object.values(blockData.posts)[0] || {};
        var lastScanned = firstBlockPost.scanned || '';
        return /*#__PURE__*/React.createElement(Card, {
          key: bt.name,
          className: "pp-blocks-usage-block-tile ".concat(selected && selected.name === bt.name ? 'active' : ''),
          onClick: function onClick() {
            return handleDetailsClick(bt);
          }
        }, /*#__PURE__*/React.createElement("div", {
          className: "pp-blocks-usage-tile-main"
        }, bt.icon && /*#__PURE__*/React.createElement("span", {
          className: "block-icon",
          style: bt.iconColor ? {
            color: bt.iconColor
          } : {}
        }, typeof bt.icon === 'string' && !bt.icon.includes('<') ? /*#__PURE__*/React.createElement("span", {
          className: "dashicons dashicons-".concat(bt.icon)
        }) : /*#__PURE__*/React.createElement("span", {
          dangerouslySetInnerHTML: {
            __html: bt.icon
          }
        })), /*#__PURE__*/React.createElement("span", null, bt.title)), hasData ? /*#__PURE__*/React.createElement("div", {
          className: "pp-blocks-usage-tile-counts"
        }, /*#__PURE__*/React.createElement("div", null, __('Locations:', 'advanced-gutenberg'), " ", postCount), /*#__PURE__*/React.createElement("div", null, __('Instances:', 'advanced-gutenberg'), " ", useCount)) : /*#__PURE__*/React.createElement("div", {
          className: "pp-blocks-usage-tile-counts"
        }, /*#__PURE__*/React.createElement("div", {
          className: "no-scan"
        }, data.lastScanDate ? __('Not found in any posts', 'advanced-gutenberg') : __('No scan history', 'advanced-gutenberg'))), /*#__PURE__*/React.createElement("div", {
          className: "pp-blocks-usage-tile-actions"
        }, /*#__PURE__*/React.createElement(Button, {
          variant: "secondary",
          size: "small",
          "aria-label": sprintf(/* translators: %s: block name */
          __('Details: %s', 'advanced-gutenberg'), bt.title),
          "aria-pressed": !!(selected && selected.name === bt.name),
          onClick: function onClick(e) {
            e.stopPropagation();
            handleDetailsClick(bt);
          }
        }, __('Details', 'advanced-gutenberg'))));
      })));
    })), selected && /*#__PURE__*/React.createElement(Sidebar, {
      key: selected.name,
      selected: selected,
      data: data,
      onClose: function onClose() {
        return setSelected(null);
      },
      canEditPosts: currentUser && currentUser.canEditPosts || false
    }))));
  };

  // Only render the app if wp.blocks is available
  if (typeof wp !== 'undefined' && typeof wp.blocks !== 'undefined') {
    render(/*#__PURE__*/React.createElement(App, null), document.getElementById('advgb-block-usage-app'));
  } else {
    var appContainer = document.getElementById('advgb-block-usage-app');
    if (appContainer) {
      appContainer.innerHTML = '<p>' + __('Error Loading blocks data...', 'advanced-gutenberg') + '</p>';
    }
  }
})(wp.i18n, wp.hooks, wp.blocks, wp.blockEditor, wp.components, wp.compose, wp.element);
/******/ })()
;
//# sourceMappingURL=block-usage.js.map
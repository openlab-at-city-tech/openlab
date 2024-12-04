(() => {
    "use strict";
    var __webpack_modules__ = [ (__unused_webpack_module, __webpack_exports__, __webpack_require__) => {
        __webpack_require__.r(__webpack_exports__);
        var _wordpress_dom_ready__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(1);
        var _wordpress_dom_ready__WEBPACK_IMPORTED_MODULE_0___default = __webpack_require__.n(_wordpress_dom_ready__WEBPACK_IMPORTED_MODULE_0__);
        var _modules_focusable__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(2);
        var _modules_collapsable_menu__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(3);
        var _modules_menu__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(4);
        var _modules_toggle__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(5);
        var _modules_focus_redirect__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(6);
        var _modules_popup__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(7);
        var _modules_to_top__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(8);
        var _modules_preloader__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(9);
        var _modules_create_scroll_reveal__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(10);
        var _modules_form__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(11);
        var _modules_theme_switch__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(12);
        var _modules_sticky__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(15);
        var _modules_particles__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(14);
        new _modules_preloader__WEBPACK_IMPORTED_MODULE_8__["default"];
        _wordpress_dom_ready__WEBPACK_IMPORTED_MODULE_0___default()((function() {
            "use strict";
            new _modules_collapsable_menu__WEBPACK_IMPORTED_MODULE_2__["default"];
            new _modules_menu__WEBPACK_IMPORTED_MODULE_3__["default"];
            new _modules_toggle__WEBPACK_IMPORTED_MODULE_4__["default"];
            new _modules_form__WEBPACK_IMPORTED_MODULE_10__["default"];
            new _modules_focus_redirect__WEBPACK_IMPORTED_MODULE_5__["default"];
            new _modules_popup__WEBPACK_IMPORTED_MODULE_6__["default"];
            new _modules_to_top__WEBPACK_IMPORTED_MODULE_7__["default"];
            new _modules_create_scroll_reveal__WEBPACK_IMPORTED_MODULE_9__["default"];
            new _modules_theme_switch__WEBPACK_IMPORTED_MODULE_11__["default"];
            new _modules_sticky__WEBPACK_IMPORTED_MODULE_12__["default"];
            new _modules_particles__WEBPACK_IMPORTED_MODULE_13__["default"];
        }));
    }, module => {
        module.exports = window["wp"]["domReady"];
    }, (__unused_webpack_module, __webpack_exports__, __webpack_require__) => {
        __webpack_require__.r(__webpack_exports__);
        __webpack_require__.d(__webpack_exports__, {
            isNodeVisible: () => isNodeVisible,
            queryFocusable: () => queryFocusable,
            queryFocusableAll: () => queryFocusableAll
        });
        function _toConsumableArray(arr) {
            return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread();
        }
        function _nonIterableSpread() {
            throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
        }
        function _unsupportedIterableToArray(o, minLen) {
            if (!o) return;
            if (typeof o === "string") return _arrayLikeToArray(o, minLen);
            var n = Object.prototype.toString.call(o).slice(8, -1);
            if (n === "Object" && o.constructor) n = o.constructor.name;
            if (n === "Map" || n === "Set") return Array.from(o);
            if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
        }
        function _iterableToArray(iter) {
            if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter);
        }
        function _arrayWithoutHoles(arr) {
            if (Array.isArray(arr)) return _arrayLikeToArray(arr);
        }
        function _arrayLikeToArray(arr, len) {
            if (len == null || len > arr.length) len = arr.length;
            for (var i = 0, arr2 = new Array(len); i < len; i++) {
                arr2[i] = arr[i];
            }
            return arr2;
        }
        if (window.jQuery) {
            jQuery.extend(jQuery.expr[":"], {
                focusable: function focusable(el) {
                    return jQuery(el).is("a, button, :input, [tabindex]");
                }
            });
        }
        var focusableSelectors = 'a, button:not([disabled]):not([aria-hidden="true"]), input:not([disabled]):not([type="hidden"]):not([aria-hidden="true"]), [tabindex]';
        function queryFocusable(selector) {
            var dom = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : document;
            if (!selector) {
                return undefined;
            }
            if (selector.indexOf(":focusable") === -1) {
                return dom.querySelector(selector);
            }
            selector = selector.replaceAll(":focusable", "").trim();
            if (selector) {
                var _dom$querySelector;
                return (_dom$querySelector = dom.querySelector(selector)) === null || _dom$querySelector === void 0 ? void 0 : _dom$querySelector.querySelector(focusableSelectors);
            }
            return dom.querySelector(focusableSelectors);
        }
        function isNodeVisible(node) {
            if (!(node instanceof Element)) {
                return false;
            }
            var s = getComputedStyle(node);
            return !(s.getPropertyValue("display") === "none" || s.getPropertyValue("visibility") === "hidden");
        }
        function visibleNodes(nodes) {
            return _toConsumableArray(nodes).filter((function(n) {
                return isNodeVisible(n);
            }));
        }
        function travelNodes(el, fn) {
            for (var i = 0; i < el.childNodes.length; i++) {
                var child = el.childNodes[i];
                if (fn(child) !== false) {
                    travelNodes(child, fn);
                }
            }
        }
        function isFocusAble(node) {
            if (!(node instanceof Element)) {
                return false;
            }
            if (node.tagName === "button" || node.tagName === "input") {
                return node.getAttribute("disabled") === null;
            }
            if (node.tagName === "a") {
                return node.getAttribute("href") !== null;
            }
            return node.tabIndex >= 0;
        }
        function queryFocusableAll() {
            var dom = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : document;
            var focusable = [];
            travelNodes(dom, (function(el) {
                if (!isNodeVisible(el)) {
                    return false;
                }
                if (isFocusAble(el)) {
                    focusable.push(el);
                }
            }));
            return focusable;
        }
    }, (__unused_webpack_module, __webpack_exports__, __webpack_require__) => {
        __webpack_require__.r(__webpack_exports__);
        __webpack_require__.d(__webpack_exports__, {
            default: () => __WEBPACK_DEFAULT_EXPORT__
        });
        function _createForOfIteratorHelper(o, allowArrayLike) {
            var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"];
            if (!it) {
                if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") {
                    if (it) o = it;
                    var i = 0;
                    var F = function F() {};
                    return {
                        s: F,
                        n: function n() {
                            if (i >= o.length) return {
                                done: true
                            };
                            return {
                                done: false,
                                value: o[i++]
                            };
                        },
                        e: function e(_e) {
                            throw _e;
                        },
                        f: F
                    };
                }
                throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
            }
            var normalCompletion = true, didErr = false, err;
            return {
                s: function s() {
                    it = it.call(o);
                },
                n: function n() {
                    var step = it.next();
                    normalCompletion = step.done;
                    return step;
                },
                e: function e(_e2) {
                    didErr = true;
                    err = _e2;
                },
                f: function f() {
                    try {
                        if (!normalCompletion && it["return"] != null) it["return"]();
                    } finally {
                        if (didErr) throw err;
                    }
                }
            };
        }
        function _unsupportedIterableToArray(o, minLen) {
            if (!o) return;
            if (typeof o === "string") return _arrayLikeToArray(o, minLen);
            var n = Object.prototype.toString.call(o).slice(8, -1);
            if (n === "Object" && o.constructor) n = o.constructor.name;
            if (n === "Map" || n === "Set") return Array.from(o);
            if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
        }
        function _arrayLikeToArray(arr, len) {
            if (len == null || len > arr.length) len = arr.length;
            for (var i = 0, arr2 = new Array(len); i < len; i++) {
                arr2[i] = arr[i];
            }
            return arr2;
        }
        function _defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value" in descriptor) descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }
        function _createClass(Constructor, protoProps, staticProps) {
            if (protoProps) _defineProperties(Constructor.prototype, protoProps);
            if (staticProps) _defineProperties(Constructor, staticProps);
            Object.defineProperty(Constructor, "prototype", {
                writable: false
            });
            return Constructor;
        }
        function _classCallCheck(instance, Constructor) {
            if (!(instance instanceof Constructor)) {
                throw new TypeError("Cannot call a class as a function");
            }
        }
        var CollapsableMenu = _createClass((function CollapsableMenu() {
            _classCallCheck(this, CollapsableMenu);
            var submenus = document.querySelectorAll(".kenta-collapsable-menu.collapsable .menu-item-has-children, .kenta-collapsable-menu.collapsable .page_item_has_children");
            var _iterator = _createForOfIteratorHelper(submenus), _step;
            try {
                var _loop = function _loop() {
                    var item = _step.value;
                    var submenu = item.querySelector("& > .sub-menu, & > .children");
                    var toggle = item.querySelector("& > a .kenta-dropdown-toggle");
                    if (toggle && submenu) {
                        toggle.addEventListener("click", (function(ev) {
                            ev.preventDefault();
                            ev.stopPropagation();
                            if (toggle.classList.contains("active")) {
                                toggle.classList.remove("active");
                            } else {
                                toggle.classList.add("active");
                            }
                            if (submenu.classList.contains("open")) {
                                submenu.classList.remove("open");
                            } else {
                                submenu.classList.add("open");
                            }
                        }));
                    }
                };
                for (_iterator.s(); !(_step = _iterator.n()).done; ) {
                    _loop();
                }
            } catch (err) {
                _iterator.e(err);
            } finally {
                _iterator.f();
            }
        }));
        const __WEBPACK_DEFAULT_EXPORT__ = CollapsableMenu;
    }, (__unused_webpack_module, __webpack_exports__, __webpack_require__) => {
        __webpack_require__.r(__webpack_exports__);
        __webpack_require__.d(__webpack_exports__, {
            default: () => __WEBPACK_DEFAULT_EXPORT__
        });
        function _defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value" in descriptor) descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }
        function _createClass(Constructor, protoProps, staticProps) {
            if (protoProps) _defineProperties(Constructor.prototype, protoProps);
            if (staticProps) _defineProperties(Constructor, staticProps);
            Object.defineProperty(Constructor, "prototype", {
                writable: false
            });
            return Constructor;
        }
        function _classCallCheck(instance, Constructor) {
            if (!(instance instanceof Constructor)) {
                throw new TypeError("Cannot call a class as a function");
            }
        }
        var Menu = _createClass((function Menu() {
            _classCallCheck(this, Menu);
            document.querySelectorAll(".sf-menu li").forEach((function(menuItem) {
                var timer = null;
                menuItem.addEventListener("mouseover", (function() {
                    if (timer) {
                        clearTimeout(timer);
                    }
                    menuItem.classList.add("sfHover");
                }));
                menuItem.addEventListener("mouseleave", (function() {
                    timer = setTimeout((function() {
                        menuItem.classList.remove("sfHover");
                    }), 300);
                }));
            }));
        }));
        const __WEBPACK_DEFAULT_EXPORT__ = Menu;
    }, (__unused_webpack_module, __webpack_exports__, __webpack_require__) => {
        __webpack_require__.r(__webpack_exports__);
        __webpack_require__.d(__webpack_exports__, {
            default: () => __WEBPACK_DEFAULT_EXPORT__
        });
        var _focusable__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(2);
        function _defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value" in descriptor) descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }
        function _createClass(Constructor, protoProps, staticProps) {
            if (protoProps) _defineProperties(Constructor.prototype, protoProps);
            if (staticProps) _defineProperties(Constructor, staticProps);
            Object.defineProperty(Constructor, "prototype", {
                writable: false
            });
            return Constructor;
        }
        function _classCallCheck(instance, Constructor) {
            if (!(instance instanceof Constructor)) {
                throw new TypeError("Cannot call a class as a function");
            }
        }
        var Toggle = _createClass((function Toggle() {
            _classCallCheck(this, Toggle);
            var scrollBarWidth = window.innerWidth - document.documentElement.clientWidth;
            if (scrollBarWidth > 0) {
                document.body.style.setProperty("--scrollbar-width", "".concat(scrollBarWidth, "px"));
            }
            document.querySelectorAll("[data-toggle-target]").forEach((function(el) {
                var _el$classList, _el$classList2;
                if (el !== null && el !== void 0 && (_el$classList = el.classList) !== null && _el$classList !== void 0 && _el$classList.contains("kenta-toggleable")) {
                    return;
                }
                el === null || el === void 0 ? void 0 : (_el$classList2 = el.classList) === null || _el$classList2 === void 0 ? void 0 : _el$classList2.add("kenta-toggleable");
                el.addEventListener("click", (function() {
                    var _el$dataset, _el$dataset2, _el$dataset3, _target$classList, _target$classList4;
                    var target = (0, _focusable__WEBPACK_IMPORTED_MODULE_0__.queryFocusable)((_el$dataset = el.dataset) === null || _el$dataset === void 0 ? void 0 : _el$dataset.toggleTarget);
                    var showFocus = (0, _focusable__WEBPACK_IMPORTED_MODULE_0__.queryFocusable)((_el$dataset2 = el.dataset) === null || _el$dataset2 === void 0 ? void 0 : _el$dataset2.toggleShowFocus);
                    var hiddenFocus = (0, _focusable__WEBPACK_IMPORTED_MODULE_0__.queryFocusable)((_el$dataset3 = el.dataset) === null || _el$dataset3 === void 0 ? void 0 : _el$dataset3.toggleHiddenFocus);
                    if (!target) {
                        return;
                    }
                    if (target !== null && target !== void 0 && (_target$classList = target.classList) !== null && _target$classList !== void 0 && _target$classList.contains("active")) {
                        var _target$classList2;
                        target === null || target === void 0 ? void 0 : (_target$classList2 = target.classList) === null || _target$classList2 === void 0 ? void 0 : _target$classList2.remove("active");
                    } else {
                        var _target$classList3;
                        target === null || target === void 0 ? void 0 : (_target$classList3 = target.classList) === null || _target$classList3 === void 0 ? void 0 : _target$classList3.add("active");
                    }
                    if (target !== null && target !== void 0 && (_target$classList4 = target.classList) !== null && _target$classList4 !== void 0 && _target$classList4.contains("active")) {
                        var _document$body, _document$body$classL;
                        (_document$body = document.body) === null || _document$body === void 0 ? void 0 : (_document$body$classL = _document$body.classList) === null || _document$body$classL === void 0 ? void 0 : _document$body$classL.add("kenta-modal-visible");
                        if (showFocus) {
                            setTimeout((function() {
                                return showFocus.focus();
                            }), 100);
                        }
                    } else {
                        setTimeout((function() {
                            var _document$body2, _document$body2$class;
                            (_document$body2 = document.body) === null || _document$body2 === void 0 ? void 0 : (_document$body2$class = _document$body2.classList) === null || _document$body2$class === void 0 ? void 0 : _document$body2$class.remove("kenta-modal-visible");
                        }), 300);
                        if (hiddenFocus) {
                            setTimeout((function() {
                                return hiddenFocus.focus();
                            }), 100);
                        }
                    }
                }));
            }));
        }));
        const __WEBPACK_DEFAULT_EXPORT__ = Toggle;
    }, (__unused_webpack_module, __webpack_exports__, __webpack_require__) => {
        __webpack_require__.r(__webpack_exports__);
        __webpack_require__.d(__webpack_exports__, {
            default: () => __WEBPACK_DEFAULT_EXPORT__
        });
        var _focusable__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(2);
        function _defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value" in descriptor) descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }
        function _createClass(Constructor, protoProps, staticProps) {
            if (protoProps) _defineProperties(Constructor.prototype, protoProps);
            if (staticProps) _defineProperties(Constructor, staticProps);
            Object.defineProperty(Constructor, "prototype", {
                writable: false
            });
            return Constructor;
        }
        function _classCallCheck(instance, Constructor) {
            if (!(instance instanceof Constructor)) {
                throw new TypeError("Cannot call a class as a function");
            }
        }
        var FocusRedirect = _createClass((function FocusRedirect() {
            _classCallCheck(this, FocusRedirect);
            document.querySelectorAll("[data-redirect-focus]").forEach((function(el) {
                var _el$dataset;
                var target = document.querySelector(el === null || el === void 0 ? void 0 : (_el$dataset = el.dataset) === null || _el$dataset === void 0 ? void 0 : _el$dataset.redirectFocus);
                if (!target) {
                    return;
                }
                el.addEventListener("keydown", (function(ev) {
                    var tabKey = ev.keyCode === 9;
                    var shiftKey = ev.shiftKey;
                    var focusable = (0, _focusable__WEBPACK_IMPORTED_MODULE_0__.queryFocusableAll)(el);
                    var first = focusable[0];
                    var last = focusable[focusable.length - 1];
                    var active = document.activeElement;
                    if (tabKey && !shiftKey && active.isSameNode(last)) {
                        ev.preventDefault();
                        target.focus();
                    }
                    if (tabKey && shiftKey && active.isSameNode(first)) {
                        ev.preventDefault();
                        target.focus();
                    }
                }));
                target.addEventListener("keydown", (function(ev) {
                    if (!(0, _focusable__WEBPACK_IMPORTED_MODULE_0__.isNodeVisible)(el)) {
                        return;
                    }
                    var tabKey = ev.keyCode === 9;
                    var shiftKey = ev.shiftKey;
                    var focusable = (0, _focusable__WEBPACK_IMPORTED_MODULE_0__.queryFocusableAll)(el);
                    var first = focusable[0];
                    var last = focusable[focusable.length - 1];
                    if (tabKey && !shiftKey) {
                        ev.preventDefault();
                        first.focus();
                    }
                    if (tabKey && shiftKey) {
                        ev.preventDefault();
                        last.focus();
                    }
                }));
            }));
        }));
        const __WEBPACK_DEFAULT_EXPORT__ = FocusRedirect;
    }, (__unused_webpack_module, __webpack_exports__, __webpack_require__) => {
        __webpack_require__.r(__webpack_exports__);
        __webpack_require__.d(__webpack_exports__, {
            default: () => __WEBPACK_DEFAULT_EXPORT__
        });
        function _defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value" in descriptor) descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }
        function _createClass(Constructor, protoProps, staticProps) {
            if (protoProps) _defineProperties(Constructor.prototype, protoProps);
            if (staticProps) _defineProperties(Constructor, staticProps);
            Object.defineProperty(Constructor, "prototype", {
                writable: false
            });
            return Constructor;
        }
        function _classCallCheck(instance, Constructor) {
            if (!(instance instanceof Constructor)) {
                throw new TypeError("Cannot call a class as a function");
            }
        }
        var Popup = _createClass((function Popup() {
            _classCallCheck(this, Popup);
            document.querySelectorAll("[data-popup-target]").forEach((function(el) {
                var _el$dataset;
                var target = el.getElementsByClassName(el === null || el === void 0 ? void 0 : (_el$dataset = el.dataset) === null || _el$dataset === void 0 ? void 0 : _el$dataset.popupTarget)[0];
                if (!target) {
                    return;
                }
                var show = function show() {
                    var _target$classList;
                    target === null || target === void 0 ? void 0 : (_target$classList = target.classList) === null || _target$classList === void 0 ? void 0 : _target$classList.add("show");
                };
                var hide = function hide() {
                    var _target$classList2;
                    target === null || target === void 0 ? void 0 : (_target$classList2 = target.classList) === null || _target$classList2 === void 0 ? void 0 : _target$classList2.remove("show");
                };
                el.addEventListener("focusin", show);
                el.addEventListener("focusout", hide);
                el.addEventListener("mouseover", show);
                el.addEventListener("mouseleave", hide);
            }));
        }));
        const __WEBPACK_DEFAULT_EXPORT__ = Popup;
    }, (__unused_webpack_module, __webpack_exports__, __webpack_require__) => {
        __webpack_require__.r(__webpack_exports__);
        __webpack_require__.d(__webpack_exports__, {
            default: () => __WEBPACK_DEFAULT_EXPORT__
        });
        function _defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value" in descriptor) descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }
        function _createClass(Constructor, protoProps, staticProps) {
            if (protoProps) _defineProperties(Constructor.prototype, protoProps);
            if (staticProps) _defineProperties(Constructor, staticProps);
            Object.defineProperty(Constructor, "prototype", {
                writable: false
            });
            return Constructor;
        }
        function _classCallCheck(instance, Constructor) {
            if (!(instance instanceof Constructor)) {
                throw new TypeError("Cannot call a class as a function");
            }
        }
        var ToTop = _createClass((function ToTop() {
            _classCallCheck(this, ToTop);
            var scrollTopBtn = document.getElementById("scroll-top");
            if (!scrollTopBtn) {
                return;
            }
            window.addEventListener("scroll", (function() {
                var offset = document.documentElement && document.documentElement.scrollTop || document.body.scrollTop;
                if (offset) {
                    scrollTopBtn.classList.add("active");
                } else {
                    scrollTopBtn.classList.remove("active");
                }
            }));
            scrollTopBtn.addEventListener("click", (function(ev) {
                window.scrollTo(0, 0);
                ev.preventDefault();
            }));
        }));
        const __WEBPACK_DEFAULT_EXPORT__ = ToTop;
    }, (__unused_webpack_module, __webpack_exports__, __webpack_require__) => {
        __webpack_require__.r(__webpack_exports__);
        __webpack_require__.d(__webpack_exports__, {
            default: () => __WEBPACK_DEFAULT_EXPORT__
        });
        var _wordpress_dom_ready__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(1);
        var _wordpress_dom_ready__WEBPACK_IMPORTED_MODULE_0___default = __webpack_require__.n(_wordpress_dom_ready__WEBPACK_IMPORTED_MODULE_0__);
        function _classCallCheck(instance, Constructor) {
            if (!(instance instanceof Constructor)) {
                throw new TypeError("Cannot call a class as a function");
            }
        }
        function _defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value" in descriptor) descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }
        function _createClass(Constructor, protoProps, staticProps) {
            if (protoProps) _defineProperties(Constructor.prototype, protoProps);
            if (staticProps) _defineProperties(Constructor, staticProps);
            Object.defineProperty(Constructor, "prototype", {
                writable: false
            });
            return Constructor;
        }
        var Preloader = function() {
            function Preloader() {
                var _this = this;
                _classCallCheck(this, Preloader);
                var wrap = document.querySelector(".kenta-preloader-wrap");
                if (wrap) {
                    var _document, _document$body, _document$body$classL;
                    _wordpress_dom_ready__WEBPACK_IMPORTED_MODULE_0___default()((function() {
                        return _this.hidePreloader();
                    }));
                    if ((_document = document) !== null && _document !== void 0 && (_document$body = _document.body) !== null && _document$body !== void 0 && (_document$body$classL = _document$body.classList) !== null && _document$body$classL !== void 0 && _document$body$classL.contains("elementor-editor-active")) {
                        setTimeout((function() {
                            return _this.hidePreloader();
                        }), 300);
                    }
                }
            }
            _createClass(Preloader, [ {
                key: "hidePreloader",
                value: function hidePreloader() {
                    this.fadeOut(document.querySelector(".kenta-preloader-wrap > div"), 200);
                    this.fadeOut(document.querySelector(".kenta-preloader-wrap"), 400);
                }
            }, {
                key: "fadeOut",
                value: function fadeOut(el, speed) {
                    var _getComputedStyle;
                    if (!el) {
                        return;
                    }
                    var opacity = Number.parseFloat((_getComputedStyle = getComputedStyle(el)) === null || _getComputedStyle === void 0 ? void 0 : _getComputedStyle.getPropertyValue("opacity"));
                    if (isNaN(opacity) || opacity === 0) {
                        return;
                    }
                    var step = opacity / ((speed !== null && speed !== void 0 ? speed : 1e3) / 25);
                    var timer = setInterval((function() {
                        opacity = opacity - step;
                        if (opacity <= 0) {
                            clearInterval(timer);
                            opacity = 0;
                            el.style.display = "none";
                            el.style.visibility = "hidden";
                        }
                        el.style.opacity = opacity;
                    }), 25);
                }
            } ]);
            return Preloader;
        }();
        const __WEBPACK_DEFAULT_EXPORT__ = Preloader;
    }, (__unused_webpack_module, __webpack_exports__, __webpack_require__) => {
        __webpack_require__.r(__webpack_exports__);
        __webpack_require__.d(__webpack_exports__, {
            default: () => __WEBPACK_DEFAULT_EXPORT__
        });
        function _defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value" in descriptor) descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }
        function _createClass(Constructor, protoProps, staticProps) {
            if (protoProps) _defineProperties(Constructor.prototype, protoProps);
            if (staticProps) _defineProperties(Constructor, staticProps);
            Object.defineProperty(Constructor, "prototype", {
                writable: false
            });
            return Constructor;
        }
        function _classCallCheck(instance, Constructor) {
            if (!(instance instanceof Constructor)) {
                throw new TypeError("Cannot call a class as a function");
            }
        }
        var CreateScrollReveal = _createClass((function CreateScrollReveal() {
            var _document$body$datase;
            _classCallCheck(this, CreateScrollReveal);
            if (window.ScrollReveal === undefined) {
                return;
            }
            var options = JSON.parse((_document$body$datase = document.body.dataset) === null || _document$body$datase === void 0 ? void 0 : _document$body$datase.kentaScrollReveal);
            ScrollReveal().reveal(".kenta-scroll-reveal", options);
            ScrollReveal().reveal(".kenta-scroll-reveal-widget", options);
        }));
        const __WEBPACK_DEFAULT_EXPORT__ = CreateScrollReveal;
    }, (__unused_webpack_module, __webpack_exports__, __webpack_require__) => {
        __webpack_require__.r(__webpack_exports__);
        __webpack_require__.d(__webpack_exports__, {
            default: () => __WEBPACK_DEFAULT_EXPORT__
        });
        function _toConsumableArray(arr) {
            return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread();
        }
        function _nonIterableSpread() {
            throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
        }
        function _unsupportedIterableToArray(o, minLen) {
            if (!o) return;
            if (typeof o === "string") return _arrayLikeToArray(o, minLen);
            var n = Object.prototype.toString.call(o).slice(8, -1);
            if (n === "Object" && o.constructor) n = o.constructor.name;
            if (n === "Map" || n === "Set") return Array.from(o);
            if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
        }
        function _iterableToArray(iter) {
            if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter);
        }
        function _arrayWithoutHoles(arr) {
            if (Array.isArray(arr)) return _arrayLikeToArray(arr);
        }
        function _arrayLikeToArray(arr, len) {
            if (len == null || len > arr.length) len = arr.length;
            for (var i = 0, arr2 = new Array(len); i < len; i++) {
                arr2[i] = arr[i];
            }
            return arr2;
        }
        function _defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value" in descriptor) descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }
        function _createClass(Constructor, protoProps, staticProps) {
            if (protoProps) _defineProperties(Constructor.prototype, protoProps);
            if (staticProps) _defineProperties(Constructor, staticProps);
            Object.defineProperty(Constructor, "prototype", {
                writable: false
            });
            return Constructor;
        }
        function _classCallCheck(instance, Constructor) {
            if (!(instance instanceof Constructor)) {
                throw new TypeError("Cannot call a class as a function");
            }
        }
        var Form = _createClass((function Form() {
            _classCallCheck(this, Form);
            var inputs = _toConsumableArray(document.querySelectorAll(".comment-form, .kenta-inner-label")).reduce((function(result, parent) {
                return [].concat(_toConsumableArray(result), _toConsumableArray(parent.querySelectorAll("input,textarea")));
            }), []).filter((function(input) {
                return input.type !== "hidden" && input.type !== "checkbox" && input.type !== "submit";
            }));
            var inputWrapper = function inputWrapper(el) {
                if (!el) {
                    return null;
                }
                if (el.querySelector("label")) {
                    return el;
                }
                return inputWrapper(el.parentNode);
            };
            var renderEmptiness = function renderEmptiness() {
                inputs.map((function(input) {
                    var wrapper = inputWrapper(input.parentNode);
                    if (wrapper) {
                        wrapper.classList.remove("kenta-not-empty-field");
                    }
                    if (!input.value) {
                        return;
                    }
                    if (input.value.trim().length > 0) {
                        if (wrapper) {
                            wrapper.classList.add("kenta-not-empty-field");
                        }
                    }
                }));
            };
            setTimeout((function() {
                renderEmptiness();
            }));
            inputs.map((function(input) {
                return input.addEventListener("input", renderEmptiness);
            }));
        }));
        const __WEBPACK_DEFAULT_EXPORT__ = Form;
    }, (__unused_webpack_module, __webpack_exports__, __webpack_require__) => {
        __webpack_require__.r(__webpack_exports__);
        __webpack_require__.d(__webpack_exports__, {
            default: () => __WEBPACK_DEFAULT_EXPORT__
        });
        var js_cookie__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(13);
        var _particles__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(14);
        function _createForOfIteratorHelper(o, allowArrayLike) {
            var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"];
            if (!it) {
                if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") {
                    if (it) o = it;
                    var i = 0;
                    var F = function F() {};
                    return {
                        s: F,
                        n: function n() {
                            if (i >= o.length) return {
                                done: true
                            };
                            return {
                                done: false,
                                value: o[i++]
                            };
                        },
                        e: function e(_e) {
                            throw _e;
                        },
                        f: F
                    };
                }
                throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
            }
            var normalCompletion = true, didErr = false, err;
            return {
                s: function s() {
                    it = it.call(o);
                },
                n: function n() {
                    var step = it.next();
                    normalCompletion = step.done;
                    return step;
                },
                e: function e(_e2) {
                    didErr = true;
                    err = _e2;
                },
                f: function f() {
                    try {
                        if (!normalCompletion && it["return"] != null) it["return"]();
                    } finally {
                        if (didErr) throw err;
                    }
                }
            };
        }
        function _unsupportedIterableToArray(o, minLen) {
            if (!o) return;
            if (typeof o === "string") return _arrayLikeToArray(o, minLen);
            var n = Object.prototype.toString.call(o).slice(8, -1);
            if (n === "Object" && o.constructor) n = o.constructor.name;
            if (n === "Map" || n === "Set") return Array.from(o);
            if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
        }
        function _arrayLikeToArray(arr, len) {
            if (len == null || len > arr.length) len = arr.length;
            for (var i = 0, arr2 = new Array(len); i < len; i++) {
                arr2[i] = arr[i];
            }
            return arr2;
        }
        function _classCallCheck(instance, Constructor) {
            if (!(instance instanceof Constructor)) {
                throw new TypeError("Cannot call a class as a function");
            }
        }
        function _defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value" in descriptor) descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }
        function _createClass(Constructor, protoProps, staticProps) {
            if (protoProps) _defineProperties(Constructor.prototype, protoProps);
            if (staticProps) _defineProperties(Constructor, staticProps);
            Object.defineProperty(Constructor, "prototype", {
                writable: false
            });
            return Constructor;
        }
        var ThemeSwitch = function() {
            function ThemeSwitch() {
                var _document$documentEle, _document$documentEle2, _document$documentEle3;
                _classCallCheck(this, ThemeSwitch);
                var blogId = (_document$documentEle = document.documentElement.dataset) === null || _document$documentEle === void 0 ? void 0 : _document$documentEle.kentaBlogId;
                var active = (_document$documentEle2 = document.documentElement.dataset) === null || _document$documentEle2 === void 0 ? void 0 : _document$documentEle2.kentaTheme;
                var isPersistent = ((_document$documentEle3 = document.documentElement.dataset) === null || _document$documentEle3 === void 0 ? void 0 : _document$documentEle3.saveColorScheme) === "yes";
                var mode = js_cookie__WEBPACK_IMPORTED_MODULE_0__["default"].get(blogId + "-color-mode");
                if (mode && isPersistent && mode !== active) {
                    document.documentElement.setAttribute("data-kenta-theme", mode);
                }
                var switches = document.getElementsByClassName("kenta-theme-switch");
                var _iterator = _createForOfIteratorHelper(switches), _step;
                try {
                    for (_iterator.s(); !(_step = _iterator.n()).done; ) {
                        var switchBtn = _step.value;
                        if (switchBtn.classList.contains("kenta-theme-switch-initialized")) {
                            continue;
                        }
                        switchBtn.classList.add("kenta-theme-switch-initialized");
                        switchBtn.addEventListener("click", this.toggle.bind(this));
                    }
                } catch (err) {
                    _iterator.e(err);
                } finally {
                    _iterator.f();
                }
            }
            _createClass(ThemeSwitch, [ {
                key: "toggle",
                value: function toggle() {
                    var _document$documentEle4, _document$documentEle5;
                    var blogId = (_document$documentEle4 = document.documentElement.dataset) === null || _document$documentEle4 === void 0 ? void 0 : _document$documentEle4.kentaBlogId;
                    var current = (_document$documentEle5 = document.documentElement.dataset) === null || _document$documentEle5 === void 0 ? void 0 : _document$documentEle5.kentaTheme;
                    if (current === "dark") {
                        this.setLightMode(blogId);
                    } else {
                        this.setDarkMode(blogId);
                    }
                    if (wp.customize) {
                        wp.customize.preview.send("kenta-theme-changed", current === "dark" ? "light" : "dark");
                    }
                    new _particles__WEBPACK_IMPORTED_MODULE_1__["default"];
                }
            }, {
                key: "setLightMode",
                value: function setLightMode(blogId) {
                    var _document$documentEle6;
                    this.setGlobalTransition();
                    document.documentElement.setAttribute("data-kenta-theme", "light");
                    if (((_document$documentEle6 = document.documentElement.dataset) === null || _document$documentEle6 === void 0 ? void 0 : _document$documentEle6.saveColorScheme) === "yes") {
                        js_cookie__WEBPACK_IMPORTED_MODULE_0__["default"].set(blogId + "-color-mode", "light", {
                            expires: 365
                        });
                    }
                }
            }, {
                key: "setDarkMode",
                value: function setDarkMode(blogId) {
                    var _document$documentEle7;
                    this.setGlobalTransition();
                    document.documentElement.setAttribute("data-kenta-theme", "dark");
                    if (((_document$documentEle7 = document.documentElement.dataset) === null || _document$documentEle7 === void 0 ? void 0 : _document$documentEle7.saveColorScheme) === "yes") {
                        js_cookie__WEBPACK_IMPORTED_MODULE_0__["default"].set(blogId + "-color-mode", "dark", {
                            expires: 365
                        });
                    }
                }
            }, {
                key: "setGlobalTransition",
                value: function setGlobalTransition() {
                    document.body.classList.add("transition-force-none");
                    setTimeout((function() {
                        document.body.classList.remove("transition-force-none");
                    }), 50);
                }
            } ]);
            return ThemeSwitch;
        }();
        const __WEBPACK_DEFAULT_EXPORT__ = ThemeSwitch;
    }, (__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {
        __webpack_require__.r(__webpack_exports__);
        __webpack_require__.d(__webpack_exports__, {
            default: () => api
        });
        /*! js-cookie v3.0.5 | MIT */        function assign(target) {
            for (var i = 1; i < arguments.length; i++) {
                var source = arguments[i];
                for (var key in source) {
                    target[key] = source[key];
                }
            }
            return target;
        }
        var defaultConverter = {
            read: function(value) {
                if (value[0] === '"') {
                    value = value.slice(1, -1);
                }
                return value.replace(/(%[\dA-F]{2})+/gi, decodeURIComponent);
            },
            write: function(value) {
                return encodeURIComponent(value).replace(/%(2[346BF]|3[AC-F]|40|5[BDE]|60|7[BCD])/g, decodeURIComponent);
            }
        };
        function init(converter, defaultAttributes) {
            function set(name, value, attributes) {
                if (typeof document === "undefined") {
                    return;
                }
                attributes = assign({}, defaultAttributes, attributes);
                if (typeof attributes.expires === "number") {
                    attributes.expires = new Date(Date.now() + attributes.expires * 864e5);
                }
                if (attributes.expires) {
                    attributes.expires = attributes.expires.toUTCString();
                }
                name = encodeURIComponent(name).replace(/%(2[346B]|5E|60|7C)/g, decodeURIComponent).replace(/[()]/g, escape);
                var stringifiedAttributes = "";
                for (var attributeName in attributes) {
                    if (!attributes[attributeName]) {
                        continue;
                    }
                    stringifiedAttributes += "; " + attributeName;
                    if (attributes[attributeName] === true) {
                        continue;
                    }
                    stringifiedAttributes += "=" + attributes[attributeName].split(";")[0];
                }
                return document.cookie = name + "=" + converter.write(value, name) + stringifiedAttributes;
            }
            function get(name) {
                if (typeof document === "undefined" || arguments.length && !name) {
                    return;
                }
                var cookies = document.cookie ? document.cookie.split("; ") : [];
                var jar = {};
                for (var i = 0; i < cookies.length; i++) {
                    var parts = cookies[i].split("=");
                    var value = parts.slice(1).join("=");
                    try {
                        var found = decodeURIComponent(parts[0]);
                        jar[found] = converter.read(value, found);
                        if (name === found) {
                            break;
                        }
                    } catch (e) {}
                }
                return name ? jar[name] : jar;
            }
            return Object.create({
                set,
                get,
                remove: function(name, attributes) {
                    set(name, "", assign({}, attributes, {
                        expires: -1
                    }));
                },
                withAttributes: function(attributes) {
                    return init(this.converter, assign({}, this.attributes, attributes));
                },
                withConverter: function(converter) {
                    return init(assign({}, this.converter, converter), this.attributes);
                }
            }, {
                attributes: {
                    value: Object.freeze(defaultAttributes)
                },
                converter: {
                    value: Object.freeze(converter)
                }
            });
        }
        var api = init(defaultConverter, {
            path: "/"
        });
    }, (__unused_webpack_module, __webpack_exports__, __webpack_require__) => {
        __webpack_require__.r(__webpack_exports__);
        __webpack_require__.d(__webpack_exports__, {
            default: () => __WEBPACK_DEFAULT_EXPORT__
        });
        function _createForOfIteratorHelper(o, allowArrayLike) {
            var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"];
            if (!it) {
                if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") {
                    if (it) o = it;
                    var i = 0;
                    var F = function F() {};
                    return {
                        s: F,
                        n: function n() {
                            if (i >= o.length) return {
                                done: true
                            };
                            return {
                                done: false,
                                value: o[i++]
                            };
                        },
                        e: function e(_e) {
                            throw _e;
                        },
                        f: F
                    };
                }
                throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
            }
            var normalCompletion = true, didErr = false, err;
            return {
                s: function s() {
                    it = it.call(o);
                },
                n: function n() {
                    var step = it.next();
                    normalCompletion = step.done;
                    return step;
                },
                e: function e(_e2) {
                    didErr = true;
                    err = _e2;
                },
                f: function f() {
                    try {
                        if (!normalCompletion && it["return"] != null) it["return"]();
                    } finally {
                        if (didErr) throw err;
                    }
                }
            };
        }
        function _unsupportedIterableToArray(o, minLen) {
            if (!o) return;
            if (typeof o === "string") return _arrayLikeToArray(o, minLen);
            var n = Object.prototype.toString.call(o).slice(8, -1);
            if (n === "Object" && o.constructor) n = o.constructor.name;
            if (n === "Map" || n === "Set") return Array.from(o);
            if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
        }
        function _arrayLikeToArray(arr, len) {
            if (len == null || len > arr.length) len = arr.length;
            for (var i = 0, arr2 = new Array(len); i < len; i++) {
                arr2[i] = arr[i];
            }
            return arr2;
        }
        function _classCallCheck(instance, Constructor) {
            if (!(instance instanceof Constructor)) {
                throw new TypeError("Cannot call a class as a function");
            }
        }
        function _defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value" in descriptor) descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }
        function _createClass(Constructor, protoProps, staticProps) {
            if (protoProps) _defineProperties(Constructor.prototype, protoProps);
            if (staticProps) _defineProperties(Constructor, staticProps);
            Object.defineProperty(Constructor, "prototype", {
                writable: false
            });
            return Constructor;
        }
        var Particles = function() {
            function Particles() {
                _classCallCheck(this, Particles);
                if (!window.particlesJS) {
                    return;
                }
                var allCanvas = document.getElementsByClassName("kenta-particles-canvas");
                var _iterator = _createForOfIteratorHelper(allCanvas), _step;
                try {
                    for (_iterator.s(); !(_step = _iterator.n()).done; ) {
                        var _canvas$dataset;
                        var canvas = _step.value;
                        var config = JSON.parse((_canvas$dataset = canvas.dataset) === null || _canvas$dataset === void 0 ? void 0 : _canvas$dataset.kentaParticles);
                        particlesJS(canvas.id, this.overrideConfig(canvas, config));
                    }
                } catch (err) {
                    _iterator.e(err);
                } finally {
                    _iterator.f();
                }
            }
            _createClass(Particles, [ {
                key: "overrideConfig",
                value: function overrideConfig(canvas, config) {
                    var _canvas$dataset2, _canvas$dataset3, _canvas$dataset4, _canvas$dataset5, _canvas$dataset6, _canvas$dataset7, _canvas$dataset8;
                    var detect_on = (_canvas$dataset2 = canvas.dataset) === null || _canvas$dataset2 === void 0 ? void 0 : _canvas$dataset2.kentaParticleDetectOn;
                    var quantity = Number((_canvas$dataset3 = canvas.dataset) === null || _canvas$dataset3 === void 0 ? void 0 : _canvas$dataset3.kentaParticleQuantity);
                    var speed = Number((_canvas$dataset4 = canvas.dataset) === null || _canvas$dataset4 === void 0 ? void 0 : _canvas$dataset4.kentaParticleSpeed);
                    var size = Number((_canvas$dataset5 = canvas.dataset) === null || _canvas$dataset5 === void 0 ? void 0 : _canvas$dataset5.kentaParticleSize);
                    var shape = (_canvas$dataset6 = canvas.dataset) === null || _canvas$dataset6 === void 0 ? void 0 : _canvas$dataset6.kentaParticleShape;
                    var particle_color = this.getColorValue((_canvas$dataset7 = canvas.dataset) === null || _canvas$dataset7 === void 0 ? void 0 : _canvas$dataset7.kentaParticleColor);
                    var line_color = this.getColorValue(((_canvas$dataset8 = canvas.dataset) === null || _canvas$dataset8 === void 0 ? void 0 : _canvas$dataset8.kentaParticleLineColor) || particle_color);
                    if ("default" !== detect_on && "" !== detect_on && undefined !== detect_on) {
                        config.interactivity.detect_on = detect_on;
                    }
                    if ("" !== size && undefined !== size && size > 0) {
                        config.particles.size.value = size;
                    }
                    if ("" !== quantity && undefined !== quantity && quantity > 0) {
                        config.particles.number.value = quantity;
                    }
                    if ("" !== particle_color && undefined !== particle_color) {
                        config.particles.color.value = particle_color;
                    }
                    if ("" !== line_color && undefined !== line_color) {
                        if (config.particles.line_linked) {
                            config.particles.line_linked.color = line_color;
                        } else {
                            if (config.particles.links) {
                                config.particles.links.color = line_color;
                            } else {
                                config.particles.links = {
                                    color: line_color
                                };
                            }
                        }
                    }
                    if ("default" !== shape && "" !== shape && undefined !== shape) {
                        config.particles.shape.type = shape;
                    }
                    if ("" !== speed && undefined !== speed && speed > 0) {
                        config.particles.move.speed = speed;
                    }
                    config.background = {};
                    config.fullScreen = {
                        enable: false
                    };
                    return config;
                }
            }, {
                key: "getColorValue",
                value: function getColorValue(color) {
                    if (!color || color === "" || color === "__INITIAL_VALUE__") {
                        return "";
                    }
                    if (color.indexOf("var") > -1) {
                        var value = getComputedStyle(document.documentElement).getPropertyValue(color.replace(/var\(/, "").replace(/\)/, "")).trim().replace(/\s/g, "");
                        if (value.indexOf("#") === -1 && value.indexOf("rgb") === -1) {
                            return "rgb(".concat(value, ")");
                        }
                        return value;
                    }
                    return color;
                }
            } ]);
            return Particles;
        }();
        const __WEBPACK_DEFAULT_EXPORT__ = Particles;
    }, (__unused_webpack_module, __webpack_exports__, __webpack_require__) => {
        __webpack_require__.r(__webpack_exports__);
        __webpack_require__.d(__webpack_exports__, {
            default: () => __WEBPACK_DEFAULT_EXPORT__
        });
        function _createForOfIteratorHelper(o, allowArrayLike) {
            var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"];
            if (!it) {
                if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") {
                    if (it) o = it;
                    var i = 0;
                    var F = function F() {};
                    return {
                        s: F,
                        n: function n() {
                            if (i >= o.length) return {
                                done: true
                            };
                            return {
                                done: false,
                                value: o[i++]
                            };
                        },
                        e: function e(_e) {
                            throw _e;
                        },
                        f: F
                    };
                }
                throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
            }
            var normalCompletion = true, didErr = false, err;
            return {
                s: function s() {
                    it = it.call(o);
                },
                n: function n() {
                    var step = it.next();
                    normalCompletion = step.done;
                    return step;
                },
                e: function e(_e2) {
                    didErr = true;
                    err = _e2;
                },
                f: function f() {
                    try {
                        if (!normalCompletion && it["return"] != null) it["return"]();
                    } finally {
                        if (didErr) throw err;
                    }
                }
            };
        }
        function _unsupportedIterableToArray(o, minLen) {
            if (!o) return;
            if (typeof o === "string") return _arrayLikeToArray(o, minLen);
            var n = Object.prototype.toString.call(o).slice(8, -1);
            if (n === "Object" && o.constructor) n = o.constructor.name;
            if (n === "Map" || n === "Set") return Array.from(o);
            if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
        }
        function _arrayLikeToArray(arr, len) {
            if (len == null || len > arr.length) len = arr.length;
            for (var i = 0, arr2 = new Array(len); i < len; i++) {
                arr2[i] = arr[i];
            }
            return arr2;
        }
        function _classCallCheck(instance, Constructor) {
            if (!(instance instanceof Constructor)) {
                throw new TypeError("Cannot call a class as a function");
            }
        }
        function _defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value" in descriptor) descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }
        function _createClass(Constructor, protoProps, staticProps) {
            if (protoProps) _defineProperties(Constructor.prototype, protoProps);
            if (staticProps) _defineProperties(Constructor, staticProps);
            Object.defineProperty(Constructor, "prototype", {
                writable: false
            });
            return Constructor;
        }
        var Sticky = function() {
            function Sticky($) {
                var _this = this;
                _classCallCheck(this, Sticky);
                if (!window.hcSticky) {
                    return;
                }
                var stickyElements = document.getElementsByClassName("kenta-sticky");
                var _iterator = _createForOfIteratorHelper(stickyElements), _step;
                try {
                    var _loop = function _loop() {
                        var el = _step.value;
                        var header = _this.parent(el, ".kenta-site-header");
                        var isTransparentHeader = header.classList.contains("kenta-transparent-header");
                        var isAdminBarShowing = header.classList.contains("kenta-header-with-admin-bar");
                        var args = {
                            stickTo: ".kenta-site-wrap",
                            stickyClass: "kenta-is-sticky",
                            onStart: function onStart() {
                                if (isTransparentHeader) {
                                    header.classList.remove("kenta-transparent-header");
                                }
                            },
                            onStop: function onStop() {
                                if (isTransparentHeader) {
                                    header.classList.add("kenta-transparent-header");
                                }
                            }
                        };
                        new window.hcSticky(el, isAdminBarShowing ? Object.assign(args, {
                            top: 32,
                            responsive: {
                                782: {
                                    top: 46
                                }
                            }
                        }) : args);
                    };
                    for (_iterator.s(); !(_step = _iterator.n()).done; ) {
                        _loop();
                    }
                } catch (err) {
                    _iterator.e(err);
                } finally {
                    _iterator.f();
                }
            }
            _createClass(Sticky, [ {
                key: "parent",
                value: function parent(el, selector, filter) {
                    el = el.parentElement;
                    var matchesSelector = el.matches || el.webkitMatchesSelector || el.mozMatchesSelector || el.msMatchesSelector;
                    while (el && !matchesSelector.call(el, selector)) {
                        if (!filter) {
                            return el;
                        } else {
                            if (matchesSelector.call(el, filter)) {
                                return el;
                            }
                        }
                        el = el.parentElement;
                    }
                    return el;
                }
            } ]);
            return Sticky;
        }();
        const __WEBPACK_DEFAULT_EXPORT__ = Sticky;
    }, (__unused_webpack_module, __webpack_exports__, __webpack_require__) => {
        __webpack_require__.r(__webpack_exports__);
    }, (__unused_webpack_module, __webpack_exports__, __webpack_require__) => {
        __webpack_require__.r(__webpack_exports__);
    }, (__unused_webpack_module, __webpack_exports__, __webpack_require__) => {
        __webpack_require__.r(__webpack_exports__);
    }, (__unused_webpack_module, __webpack_exports__, __webpack_require__) => {
        __webpack_require__.r(__webpack_exports__);
    }, (__unused_webpack_module, __webpack_exports__, __webpack_require__) => {
        __webpack_require__.r(__webpack_exports__);
    } ];
    var __webpack_module_cache__ = {};
    function __webpack_require__(moduleId) {
        var cachedModule = __webpack_module_cache__[moduleId];
        if (cachedModule !== undefined) {
            return cachedModule.exports;
        }
        var module = __webpack_module_cache__[moduleId] = {
            exports: {}
        };
        __webpack_modules__[moduleId](module, module.exports, __webpack_require__);
        return module.exports;
    }
    __webpack_require__.m = __webpack_modules__;
    (() => {
        var deferred = [];
        __webpack_require__.O = (result, chunkIds, fn, priority) => {
            if (chunkIds) {
                priority = priority || 0;
                for (var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
                deferred[i] = [ chunkIds, fn, priority ];
                return;
            }
            var notFulfilled = Infinity;
            for (var i = 0; i < deferred.length; i++) {
                var [chunkIds, fn, priority] = deferred[i];
                var fulfilled = true;
                for (var j = 0; j < chunkIds.length; j++) {
                    if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key => __webpack_require__.O[key](chunkIds[j])))) {
                        chunkIds.splice(j--, 1);
                    } else {
                        fulfilled = false;
                        if (priority < notFulfilled) notFulfilled = priority;
                    }
                }
                if (fulfilled) {
                    deferred.splice(i--, 1);
                    var r = fn();
                    if (r !== undefined) result = r;
                }
            }
            return result;
        };
    })();
    (() => {
        __webpack_require__.n = module => {
            var getter = module && module.__esModule ? () => module["default"] : () => module;
            __webpack_require__.d(getter, {
                a: getter
            });
            return getter;
        };
    })();
    (() => {
        __webpack_require__.d = (exports, definition) => {
            for (var key in definition) {
                if (__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
                    Object.defineProperty(exports, key, {
                        enumerable: true,
                        get: definition[key]
                    });
                }
            }
        };
    })();
    (() => {
        __webpack_require__.o = (obj, prop) => Object.prototype.hasOwnProperty.call(obj, prop);
    })();
    (() => {
        __webpack_require__.r = exports => {
            if (typeof Symbol !== "undefined" && Symbol.toStringTag) {
                Object.defineProperty(exports, Symbol.toStringTag, {
                    value: "Module"
                });
            }
            Object.defineProperty(exports, "__esModule", {
                value: true
            });
        };
    })();
    (() => {
        var installedChunks = {
            1: 0,
            6: 0,
            7: 0,
            5: 0,
            9: 0,
            8: 0
        };
        __webpack_require__.O.j = chunkId => installedChunks[chunkId] === 0;
        var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
            var [chunkIds, moreModules, runtime] = data;
            var moduleId, chunkId, i = 0;
            if (chunkIds.some((id => installedChunks[id] !== 0))) {
                for (moduleId in moreModules) {
                    if (__webpack_require__.o(moreModules, moduleId)) {
                        __webpack_require__.m[moduleId] = moreModules[moduleId];
                    }
                }
                if (runtime) var result = runtime(__webpack_require__);
            }
            if (parentChunkLoadingFunction) parentChunkLoadingFunction(data);
            for (;i < chunkIds.length; i++) {
                chunkId = chunkIds[i];
                if (__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
                    installedChunks[chunkId][0]();
                }
                installedChunks[chunkId] = 0;
            }
            return __webpack_require__.O(result);
        };
        var chunkLoadingGlobal = self["webpackChunkkenta"] = self["webpackChunkkenta"] || [];
        chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
        chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
    })();
    __webpack_require__.O(undefined, [ 6, 7, 5, 9, 8 ], (() => __webpack_require__(0)));
    __webpack_require__.O(undefined, [ 6, 7, 5, 9, 8 ], (() => __webpack_require__(16)));
    __webpack_require__.O(undefined, [ 6, 7, 5, 9, 8 ], (() => __webpack_require__(17)));
    __webpack_require__.O(undefined, [ 6, 7, 5, 9, 8 ], (() => __webpack_require__(18)));
    __webpack_require__.O(undefined, [ 6, 7, 5, 9, 8 ], (() => __webpack_require__(19)));
    var __webpack_exports__ = __webpack_require__.O(undefined, [ 6, 7, 5, 9, 8 ], (() => __webpack_require__(20)));
    __webpack_exports__ = __webpack_require__.O(__webpack_exports__);
})();
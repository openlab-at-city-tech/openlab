(() => {
    "use strict";
    var __webpack_modules__ = {
        59: (__unused_webpack_module, __webpack_exports__, __webpack_require__) => {
            __webpack_require__.r(__webpack_exports__);
            __webpack_require__.d(__webpack_exports__, {
                default: () => __WEBPACK_DEFAULT_EXPORT__
            });
            var NoticeDismiss = {
                init: function init($) {
                    $(".kenta-theme-notice .kenta-notice-dismiss").click((function() {
                        var $notice = $(this).parents(".is-dismissible");
                        var dismiss_url = $notice.attr("data-dismiss-url");
                        if (dismiss_url) {
                            $.ajax({
                                url: dismiss_url,
                                complete: function complete() {
                                    $notice.hide();
                                }
                            });
                        }
                    }));
                }
            };
            const __WEBPACK_DEFAULT_EXPORT__ = NoticeDismiss;
        },
        60: (__unused_webpack_module, __webpack_exports__, __webpack_require__) => {
            __webpack_require__.r(__webpack_exports__);
            __webpack_require__.d(__webpack_exports__, {
                default: () => __WEBPACK_DEFAULT_EXPORT__
            });
            var StarterSites = {
                init: function init($) {
                    $(".kenta-install-cmp-action").click((function(ev) {
                        if (!window.KentaAdmin) {
                            return;
                        }
                        ev.preventDefault();
                        var $btn = $(this);
                        var text = $btn.html();
                        $(".kenta-install-cmp-action").attr("disabled", "disabled");
                        $btn.html('<span class="loader"></span><span>Processing</span>');
                        $.ajax({
                            url: window.KentaAdmin.install_cmp_url,
                            complete: function complete() {
                                var redirect = $btn.attr("data-redirect");
                                if (redirect) {
                                    window.location.href = redirect;
                                } else {
                                    $btn.html(text);
                                    $(".kenta-install-cmp-action").removeAttr("disabled");
                                }
                            }
                        });
                    }));
                }
            };
            const __WEBPACK_DEFAULT_EXPORT__ = StarterSites;
        },
        58: module => {
            module.exports = window["jQuery"];
        }
    };
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
    var __webpack_exports__ = {};
    (() => {
        __webpack_require__.r(__webpack_exports__);
        var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(58);
        var jquery__WEBPACK_IMPORTED_MODULE_0___default = __webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);
        var _admin_dismiss_notices__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(59);
        var _admin_starter_sites__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(60);
        jquery__WEBPACK_IMPORTED_MODULE_0___default()((function($) {
            _admin_dismiss_notices__WEBPACK_IMPORTED_MODULE_1__["default"].init($);
            _admin_starter_sites__WEBPACK_IMPORTED_MODULE_2__["default"].init($);
        }));
    })();
})();
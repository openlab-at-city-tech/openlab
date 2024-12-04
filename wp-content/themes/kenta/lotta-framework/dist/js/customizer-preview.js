(() => {
    var __webpack_modules__ = {
        334: (__unused_webpack_module, __webpack_exports__, __webpack_require__) => {
            "use strict";
            __webpack_require__.r(__webpack_exports__);
            var webfontloader__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(308);
            var webfontloader__WEBPACK_IMPORTED_MODULE_0___default = __webpack_require__.n(webfontloader__WEBPACK_IMPORTED_MODULE_0__);
            function _defineProperty(obj, key, value) {
                if (key in obj) {
                    Object.defineProperty(obj, key, {
                        value,
                        enumerable: true,
                        configurable: true,
                        writable: true
                    });
                } else {
                    obj[key] = value;
                }
                return obj;
            }
            function _typeof(obj) {
                "@babel/helpers - typeof";
                return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(obj) {
                    return typeof obj;
                } : function(obj) {
                    return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
                }, _typeof(obj);
            }
            var CSS_INITIAL_VALUE = "__INITIAL_VALUE__";
            if (!window.Lotta) {
                window.Lotta = {};
            }
            window.LottaCss = {
                breakpoints: Lotta.breakpoints,
                parse: function parse(css_output) {
                    var beauty = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
                    var parse_css = "";
                    var tablet_output = {};
                    var mobile_output = {};
                    var eol = beauty ? "\n" : "";
                    if (_typeof(css_output) !== "object" || Object.keys(css_output).length <= 0) {
                        return parse_css;
                    }
                    Object.keys(css_output).forEach((function(selector) {
                        var properties = css_output[selector];
                        if (!properties || Object.keys(properties).length <= 0) {
                            return;
                        }
                        var temp_parse_css = selector + "{" + eol;
                        var temp_tablet_output = {};
                        var temp_mobile_output = {};
                        var properties_added = 0;
                        Object.keys(properties).forEach((function(property) {
                            var value = properties[property];
                            if (_typeof(value) === "object") {
                                temp_tablet_output[property] = value.tablet;
                                temp_mobile_output[property] = value.mobile;
                                value = value.desktop;
                            }
                            if (!value || CSS_INITIAL_VALUE === value) {
                                return;
                            }
                            properties_added++;
                            temp_parse_css += property + ":" + value + ";" + eol;
                        }));
                        temp_parse_css += "}";
                        if (Object.keys(temp_tablet_output).length > 0) {
                            tablet_output[selector] = temp_tablet_output;
                        }
                        if (Object.keys(temp_mobile_output).length > 0) {
                            mobile_output[selector] = temp_mobile_output;
                        }
                        if (properties_added > 0) {
                            parse_css += temp_parse_css;
                        }
                    }));
                    var tablet_css = this.parse(tablet_output, beauty);
                    if (tablet_css !== "") {
                        tablet_css = "@media (max-width: " + this.breakpoints.tablet + ") {" + eol + tablet_css + eol + "}" + eol;
                    }
                    var mobile_css = this.parse(mobile_output, beauty);
                    if (mobile_css !== "") {
                        mobile_css = "@media (max-width: " + this.breakpoints.mobile + ") {" + eol + mobile_css + eol + "}" + eol;
                    }
                    return parse_css + tablet_css + mobile_css;
                },
                valueMapper: function valueMapper(value, map) {
                    return map[value] || value;
                },
                getResponsiveValue: function getResponsiveValue(value) {
                    var device = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
                    var previous = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
                    if (!device || device === "null") {
                        return value;
                    }
                    value = _defineProperty({}, device, value);
                    return _typeof(previous) === "object" ? Object.assign({}, previous, value) : value;
                },
                dimensions: function dimensions(value) {
                    var _this = this;
                    var selector = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : "margin";
                    if (!value.desktop) {
                        value = {
                            null: value
                        };
                    }
                    var spacingCss = {};
                    Object.keys(value).forEach((function(device) {
                        var data = value[device];
                        var top = data["top"] || "0";
                        var right = data["right"] || "0";
                        var bottom = data["bottom"] || "0";
                        var left = data["left"] || "0";
                        spacingCss[selector] = _this.getResponsiveValue("".concat(top, " ").concat(right, " ").concat(bottom, " ").concat(left), device, spacingCss[selector]);
                    }));
                    return spacingCss;
                },
                background: function background(_background) {
                    var _this2 = this;
                    if (!_background.desktop) {
                        _background = {
                            null: _background
                        };
                    }
                    var backgroundCss = {};
                    Object.keys(_background).forEach((function(device) {
                        var data = _background[device];
                        if (data["type"] === "color") {
                            if (!data["color"] || data["color"] === "inherit" || data["color"] === CSS_INITIAL_VALUE) {
                                return;
                            }
                            backgroundCss["background-color"] = _this2.getResponsiveValue(data["color"], device, backgroundCss["background-color"]);
                            backgroundCss["background-image"] = _this2.getResponsiveValue("none", device, backgroundCss["background-image"]);
                        } else if (data["type"] === "gradient") {
                            backgroundCss["background-image"] = _this2.getResponsiveValue(data["gradient"], device, backgroundCss["background-image"]);
                        } else if (data["type"] === "image") {
                            var image = data["image"] || {};
                            if (image["color"]) {
                                backgroundCss["background-color"] = _this2.getResponsiveValue(image["color"], device, backgroundCss["background-color"]);
                            }
                            if (image["size"]) {
                                backgroundCss["background-size"] = _this2.getResponsiveValue(image["size"], device, backgroundCss["background-size"]);
                            }
                            if (image["repeat"]) {
                                backgroundCss["background-repeat"] = _this2.getResponsiveValue(image["repeat"], device, backgroundCss["background-repeat"]);
                            }
                            if (image["attachment"]) {
                                backgroundCss["background-attachment"] = _this2.getResponsiveValue(image["attachment"], device, backgroundCss["background-attachment"]);
                            }
                            if (image["source"] && image["source"]["url"]) {
                                backgroundCss["background-image"] = _this2.getResponsiveValue("url(" + image["source"]["url"] + ")", device, backgroundCss["background-image"]);
                                if (image["source"]["x"] && image["source"]["y"]) {
                                    var x = image["source"]["x"] * 100;
                                    var y = image["source"]["y"] * 100;
                                    backgroundCss["background-position"] = _this2.getResponsiveValue("".concat(x, "% ").concat(y, "%"), device, backgroundCss["background-position"]);
                                }
                            }
                        }
                    }));
                    return backgroundCss;
                },
                border: function border(_border) {
                    var _this3 = this;
                    var selector = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : "border";
                    if (!_border.desktop) {
                        _border = {
                            null: _border
                        };
                    }
                    var borderCss = {};
                    Object.keys(_border).forEach((function(device) {
                        var data = _border[device];
                        var value = "none";
                        var style = data["style"] || "";
                        var width = (data["width"] || "0") + "px";
                        var color = data["color"] || "";
                        var hover = data["hover"] || "";
                        if (style !== "" && style !== CSS_INITIAL_VALUE) {
                            if (style !== "none") {
                                value = "".concat(width, " ").concat(style, " ").concat(color === CSS_INITIAL_VALUE ? "var(--lotta-border-initial-color)" : color);
                            }
                            borderCss[selector] = _this3.getResponsiveValue(value, device, borderCss[selector]);
                        }
                        if (color !== CSS_INITIAL_VALUE) {
                            borderCss["--lotta-border-initial-color"] = _this3.getResponsiveValue(color, device, borderCss["--lotta-border-initial-color"]);
                            borderCss["--lotta-border-".concat(selector, "-initial-color")] = _this3.getResponsiveValue(color, device, borderCss["--lotta-border-".concat(selector, "-initial-color")]);
                        }
                        if (hover !== CSS_INITIAL_VALUE) {
                            borderCss["--lotta-border-hover-color"] = _this3.getResponsiveValue(hover, device, borderCss["--lotta-border-hover-color"]);
                            borderCss["--lotta-border-".concat(selector, "-hover-color")] = _this3.getResponsiveValue(color, device, borderCss["--lotta-border-".concat(selector, "-hover-color")]);
                        }
                    }));
                    return borderCss;
                },
                filters: function filters(filter) {
                    var _this4 = this;
                    if (filter === "__INITIAL_VALUE__" || filter === undefined || filter === null) {
                        return {};
                    }
                    if (!filter.desktop) {
                        filter = {
                            null: filter
                        };
                    }
                    var filterCss = {};
                    Object.keys(filter).forEach((function(device) {
                        var data = filter[device];
                        var value = "none";
                        var enable = (data["enable"] || "") === "yes";
                        var blur = data["blur"] || 0;
                        var contrast = data["contrast"] || 100;
                        var brightness = data["brightness"] || 100;
                        var saturate = data["saturate"] || 100;
                        var hue = data["hue"] || 0;
                        if (enable) {
                            value = "brightness( ".concat(brightness, "% ) contrast( ").concat(contrast, "% ) saturate( ").concat(saturate, "% ) blur( ").concat(blur, "px ) hue-rotate( ").concat(hue, "deg )");
                        }
                        filterCss["filter"] = _this4.getResponsiveValue(value, device, filterCss["filter"]);
                    }));
                    return filterCss;
                },
                shadow: function shadow(_shadow) {
                    var _this5 = this;
                    var selector = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : "box-shadow";
                    if (!_shadow.desktop) {
                        _shadow = {
                            null: _shadow
                        };
                    }
                    var shadowCss = {};
                    Object.keys(_shadow).forEach((function(device) {
                        var data = _shadow[device];
                        if (data === CSS_INITIAL_VALUE) {
                            return;
                        }
                        var value = "none";
                        var enable = (data["enable"] || "") === "yes";
                        var h = data["horizontal"] || "0";
                        var v = data["vertical"] || "0";
                        var blur = data["blur"] || "0";
                        var spread = data["spread"] || "0";
                        var color = data["color"] || "";
                        if (enable) {
                            value = "".concat(color, " ").concat(h, " ").concat(v, " ").concat(blur, " ").concat(spread);
                        }
                        shadowCss[selector] = _this5.getResponsiveValue(value, device, shadowCss[selector]);
                    }));
                    return shadowCss;
                },
                typography: function typography(_typography) {
                    var custom = Lotta.customizer.settings.custom_fonts;
                    var system = Lotta.customizer.settings.system_fonts;
                    var google = Lotta.customizer.settings.google_fonts;
                    var family = _typography["family"] || "inherit";
                    var variant = _typography["variant"] || "400";
                    if (system[family]) {
                        if (system[family]["s"] && system[family]["s"]) {
                            family = system[family]["s"];
                        }
                    }
                    if (google[family]) {
                        var variants = google[family]["v"] || [];
                        family = google[family]["f"] || family;
                        variant = variants.indexOf(variant) !== -1 ? variant : variants[0] || "400";
                        webfontloader__WEBPACK_IMPORTED_MODULE_0___default().load({
                            google: {
                                families: [ family ]
                            }
                        });
                    }
                    if (custom[family]) {
                        var _custom$family$v;
                        var font = custom[family];
                        variant = (_custom$family$v = custom[family]["v"]) !== null && _custom$family$v !== void 0 ? _custom$family$v : "400";
                        if (custom[family]["s"]) {
                            family = custom[family]["f"] + "," + custom[family]["s"];
                        } else {
                            family = custom[family]["f"];
                        }
                        this.addDynamicStyle("lotta-preview-dynamic-custom-fonts-loader", this.fontFacesCss([ font ]));
                    }
                    return {
                        "font-family": family,
                        "font-weight": variant,
                        "font-size": _typography["fontSize"] || "",
                        "line-height": _typography["lineHeight"] || "",
                        "letter-spacing": _typography["letterSpacing"] || "",
                        "text-transform": _typography["textTransform"] || "",
                        "text-decoration": _typography["textDecoration"] || ""
                    };
                },
                colors: function colors(_colors, maps) {
                    var _this6 = this;
                    var css = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
                    Object.keys(maps).forEach((function(color) {
                        if (_colors[color]) {
                            if (_colors[color] !== CSS_INITIAL_VALUE && _colors[color] !== "") {
                                var selectors = maps[color];
                                if (!Array.isArray(selectors)) {
                                    selectors = [ selectors ];
                                }
                                selectors.forEach((function(selector) {
                                    css[maps[color]] = _this6.getColorValue(_colors[color]);
                                }));
                            }
                        }
                    }));
                    return css;
                },
                getColorValue: function getColorValue(color) {
                    if (!color || color === CSS_INITIAL_VALUE) {
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
                },
                fontFacesCss: function fontFacesCss(fonts) {
                    var parse_css = "";
                    fonts.forEach((function(font) {
                        parse_css += "@font-face {";
                        parse_css += "font-family: '".concat(font["f"], "';");
                        parse_css += "font-weight: '".concat(font["v"], "';");
                        font["u"].forEach((function(src) {
                            if (src.indexOf(".otf") !== -1) {
                                parse_css += "src: url('".concat(src, '\') format("opentype");');
                            } else if (src.indexOf(".ttf") !== -1) {
                                parse_css += "src: url('".concat(src, '\') format("truetype");');
                            } else if (src.indexOf(".woff2") !== -1) {
                                parse_css += "src: url('".concat(src, '\') format("woff2");');
                            } else if (src.indexOf(".woff") !== -1) {
                                parse_css += "src: url('".concat(src, '\') format("woff");');
                            }
                        }));
                        parse_css += "}";
                    }));
                    return parse_css;
                },
                addDynamicStyle: function addDynamicStyle(id, style) {
                    jQuery("style#" + id).remove();
                    jQuery("head").append('<style id="' + id + '">' + style + "</style>");
                }
            };
        },
        308: (module, exports, __webpack_require__) => {
            var __WEBPACK_AMD_DEFINE_RESULT__;
            (function() {
                function aa(a, b, c) {
                    return a.call.apply(a.bind, arguments);
                }
                function ba(a, b, c) {
                    if (!a) throw Error();
                    if (2 < arguments.length) {
                        var d = Array.prototype.slice.call(arguments, 2);
                        return function() {
                            var c = Array.prototype.slice.call(arguments);
                            Array.prototype.unshift.apply(c, d);
                            return a.apply(b, c);
                        };
                    }
                    return function() {
                        return a.apply(b, arguments);
                    };
                }
                function p(a, b, c) {
                    p = Function.prototype.bind && -1 != Function.prototype.bind.toString().indexOf("native code") ? aa : ba;
                    return p.apply(null, arguments);
                }
                var q = Date.now || function() {
                    return +new Date;
                };
                function ca(a, b) {
                    this.a = a;
                    this.o = b || a;
                    this.c = this.o.document;
                }
                var da = !!window.FontFace;
                function t(a, b, c, d) {
                    b = a.c.createElement(b);
                    if (c) for (var e in c) c.hasOwnProperty(e) && ("style" == e ? b.style.cssText = c[e] : b.setAttribute(e, c[e]));
                    d && b.appendChild(a.c.createTextNode(d));
                    return b;
                }
                function u(a, b, c) {
                    a = a.c.getElementsByTagName(b)[0];
                    a || (a = document.documentElement);
                    a.insertBefore(c, a.lastChild);
                }
                function v(a) {
                    a.parentNode && a.parentNode.removeChild(a);
                }
                function w(a, b, c) {
                    b = b || [];
                    c = c || [];
                    for (var d = a.className.split(/\s+/), e = 0; e < b.length; e += 1) {
                        for (var f = !1, g = 0; g < d.length; g += 1) if (b[e] === d[g]) {
                            f = !0;
                            break;
                        }
                        f || d.push(b[e]);
                    }
                    b = [];
                    for (e = 0; e < d.length; e += 1) {
                        f = !1;
                        for (g = 0; g < c.length; g += 1) if (d[e] === c[g]) {
                            f = !0;
                            break;
                        }
                        f || b.push(d[e]);
                    }
                    a.className = b.join(" ").replace(/\s+/g, " ").replace(/^\s+|\s+$/, "");
                }
                function y(a, b) {
                    for (var c = a.className.split(/\s+/), d = 0, e = c.length; d < e; d++) if (c[d] == b) return !0;
                    return !1;
                }
                function ea(a) {
                    return a.o.location.hostname || a.a.location.hostname;
                }
                function z(a, b, c) {
                    function d() {
                        m && e && f && (m(g), m = null);
                    }
                    b = t(a, "link", {
                        rel: "stylesheet",
                        href: b,
                        media: "all"
                    });
                    var e = !1, f = !0, g = null, m = c || null;
                    da ? (b.onload = function() {
                        e = !0;
                        d();
                    }, b.onerror = function() {
                        e = !0;
                        g = Error("Stylesheet failed to load");
                        d();
                    }) : setTimeout((function() {
                        e = !0;
                        d();
                    }), 0);
                    u(a, "head", b);
                }
                function A(a, b, c, d) {
                    var e = a.c.getElementsByTagName("head")[0];
                    if (e) {
                        var f = t(a, "script", {
                            src: b
                        }), g = !1;
                        f.onload = f.onreadystatechange = function() {
                            g || this.readyState && "loaded" != this.readyState && "complete" != this.readyState || (g = !0, 
                            c && c(null), f.onload = f.onreadystatechange = null, "HEAD" == f.parentNode.tagName && e.removeChild(f));
                        };
                        e.appendChild(f);
                        setTimeout((function() {
                            g || (g = !0, c && c(Error("Script load timeout")));
                        }), d || 5e3);
                        return f;
                    }
                    return null;
                }
                function B() {
                    this.a = 0;
                    this.c = null;
                }
                function C(a) {
                    a.a++;
                    return function() {
                        a.a--;
                        D(a);
                    };
                }
                function E(a, b) {
                    a.c = b;
                    D(a);
                }
                function D(a) {
                    0 == a.a && a.c && (a.c(), a.c = null);
                }
                function F(a) {
                    this.a = a || "-";
                }
                F.prototype.c = function(a) {
                    for (var b = [], c = 0; c < arguments.length; c++) b.push(arguments[c].replace(/[\W_]+/g, "").toLowerCase());
                    return b.join(this.a);
                };
                function G(a, b) {
                    this.c = a;
                    this.f = 4;
                    this.a = "n";
                    var c = (b || "n4").match(/^([nio])([1-9])$/i);
                    c && (this.a = c[1], this.f = parseInt(c[2], 10));
                }
                function fa(a) {
                    return H(a) + " " + (a.f + "00") + " 300px " + I(a.c);
                }
                function I(a) {
                    var b = [];
                    a = a.split(/,\s*/);
                    for (var c = 0; c < a.length; c++) {
                        var d = a[c].replace(/['"]/g, "");
                        -1 != d.indexOf(" ") || /^\d/.test(d) ? b.push("'" + d + "'") : b.push(d);
                    }
                    return b.join(",");
                }
                function J(a) {
                    return a.a + a.f;
                }
                function H(a) {
                    var b = "normal";
                    "o" === a.a ? b = "oblique" : "i" === a.a && (b = "italic");
                    return b;
                }
                function ga(a) {
                    var b = 4, c = "n", d = null;
                    a && ((d = a.match(/(normal|oblique|italic)/i)) && d[1] && (c = d[1].substr(0, 1).toLowerCase()), 
                    (d = a.match(/([1-9]00|normal|bold)/i)) && d[1] && (/bold/i.test(d[1]) ? b = 7 : /[1-9]00/.test(d[1]) && (b = parseInt(d[1].substr(0, 1), 10))));
                    return c + b;
                }
                function ha(a, b) {
                    this.c = a;
                    this.f = a.o.document.documentElement;
                    this.h = b;
                    this.a = new F("-");
                    this.j = !1 !== b.events;
                    this.g = !1 !== b.classes;
                }
                function ia(a) {
                    a.g && w(a.f, [ a.a.c("wf", "loading") ]);
                    K(a, "loading");
                }
                function L(a) {
                    if (a.g) {
                        var b = y(a.f, a.a.c("wf", "active")), c = [], d = [ a.a.c("wf", "loading") ];
                        b || c.push(a.a.c("wf", "inactive"));
                        w(a.f, c, d);
                    }
                    K(a, "inactive");
                }
                function K(a, b, c) {
                    if (a.j && a.h[b]) if (c) a.h[b](c.c, J(c)); else a.h[b]();
                }
                function ja() {
                    this.c = {};
                }
                function ka(a, b, c) {
                    var d = [], e;
                    for (e in b) if (b.hasOwnProperty(e)) {
                        var f = a.c[e];
                        f && d.push(f(b[e], c));
                    }
                    return d;
                }
                function M(a, b) {
                    this.c = a;
                    this.f = b;
                    this.a = t(this.c, "span", {
                        "aria-hidden": "true"
                    }, this.f);
                }
                function N(a) {
                    u(a.c, "body", a.a);
                }
                function O(a) {
                    return "display:block;position:absolute;top:-9999px;left:-9999px;font-size:300px;width:auto;height:auto;line-height:normal;margin:0;padding:0;font-variant:normal;white-space:nowrap;font-family:" + I(a.c) + ";" + ("font-style:" + H(a) + ";font-weight:" + (a.f + "00") + ";");
                }
                function P(a, b, c, d, e, f) {
                    this.g = a;
                    this.j = b;
                    this.a = d;
                    this.c = c;
                    this.f = e || 3e3;
                    this.h = f || void 0;
                }
                P.prototype.start = function() {
                    var a = this.c.o.document, b = this, c = q(), d = new Promise((function(d, e) {
                        function f() {
                            q() - c >= b.f ? e() : a.fonts.load(fa(b.a), b.h).then((function(a) {
                                1 <= a.length ? d() : setTimeout(f, 25);
                            }), (function() {
                                e();
                            }));
                        }
                        f();
                    })), e = null, f = new Promise((function(a, d) {
                        e = setTimeout(d, b.f);
                    }));
                    Promise.race([ f, d ]).then((function() {
                        e && (clearTimeout(e), e = null);
                        b.g(b.a);
                    }), (function() {
                        b.j(b.a);
                    }));
                };
                function Q(a, b, c, d, e, f, g) {
                    this.v = a;
                    this.B = b;
                    this.c = c;
                    this.a = d;
                    this.s = g || "BESbswy";
                    this.f = {};
                    this.w = e || 3e3;
                    this.u = f || null;
                    this.m = this.j = this.h = this.g = null;
                    this.g = new M(this.c, this.s);
                    this.h = new M(this.c, this.s);
                    this.j = new M(this.c, this.s);
                    this.m = new M(this.c, this.s);
                    a = new G(this.a.c + ",serif", J(this.a));
                    a = O(a);
                    this.g.a.style.cssText = a;
                    a = new G(this.a.c + ",sans-serif", J(this.a));
                    a = O(a);
                    this.h.a.style.cssText = a;
                    a = new G("serif", J(this.a));
                    a = O(a);
                    this.j.a.style.cssText = a;
                    a = new G("sans-serif", J(this.a));
                    a = O(a);
                    this.m.a.style.cssText = a;
                    N(this.g);
                    N(this.h);
                    N(this.j);
                    N(this.m);
                }
                var R = {
                    D: "serif",
                    C: "sans-serif"
                }, S = null;
                function T() {
                    if (null === S) {
                        var a = /AppleWebKit\/([0-9]+)(?:\.([0-9]+))/.exec(window.navigator.userAgent);
                        S = !!a && (536 > parseInt(a[1], 10) || 536 === parseInt(a[1], 10) && 11 >= parseInt(a[2], 10));
                    }
                    return S;
                }
                Q.prototype.start = function() {
                    this.f.serif = this.j.a.offsetWidth;
                    this.f["sans-serif"] = this.m.a.offsetWidth;
                    this.A = q();
                    U(this);
                };
                function la(a, b, c) {
                    for (var d in R) if (R.hasOwnProperty(d) && b === a.f[R[d]] && c === a.f[R[d]]) return !0;
                    return !1;
                }
                function U(a) {
                    var b = a.g.a.offsetWidth, c = a.h.a.offsetWidth, d;
                    (d = b === a.f.serif && c === a.f["sans-serif"]) || (d = T() && la(a, b, c));
                    d ? q() - a.A >= a.w ? T() && la(a, b, c) && (null === a.u || a.u.hasOwnProperty(a.a.c)) ? V(a, a.v) : V(a, a.B) : ma(a) : V(a, a.v);
                }
                function ma(a) {
                    setTimeout(p((function() {
                        U(this);
                    }), a), 50);
                }
                function V(a, b) {
                    setTimeout(p((function() {
                        v(this.g.a);
                        v(this.h.a);
                        v(this.j.a);
                        v(this.m.a);
                        b(this.a);
                    }), a), 0);
                }
                function W(a, b, c) {
                    this.c = a;
                    this.a = b;
                    this.f = 0;
                    this.m = this.j = !1;
                    this.s = c;
                }
                var X = null;
                W.prototype.g = function(a) {
                    var b = this.a;
                    b.g && w(b.f, [ b.a.c("wf", a.c, J(a).toString(), "active") ], [ b.a.c("wf", a.c, J(a).toString(), "loading"), b.a.c("wf", a.c, J(a).toString(), "inactive") ]);
                    K(b, "fontactive", a);
                    this.m = !0;
                    na(this);
                };
                W.prototype.h = function(a) {
                    var b = this.a;
                    if (b.g) {
                        var c = y(b.f, b.a.c("wf", a.c, J(a).toString(), "active")), d = [], e = [ b.a.c("wf", a.c, J(a).toString(), "loading") ];
                        c || d.push(b.a.c("wf", a.c, J(a).toString(), "inactive"));
                        w(b.f, d, e);
                    }
                    K(b, "fontinactive", a);
                    na(this);
                };
                function na(a) {
                    0 == --a.f && a.j && (a.m ? (a = a.a, a.g && w(a.f, [ a.a.c("wf", "active") ], [ a.a.c("wf", "loading"), a.a.c("wf", "inactive") ]), 
                    K(a, "active")) : L(a.a));
                }
                function oa(a) {
                    this.j = a;
                    this.a = new ja;
                    this.h = 0;
                    this.f = this.g = !0;
                }
                oa.prototype.load = function(a) {
                    this.c = new ca(this.j, a.context || this.j);
                    this.g = !1 !== a.events;
                    this.f = !1 !== a.classes;
                    pa(this, new ha(this.c, a), a);
                };
                function qa(a, b, c, d, e) {
                    var f = 0 == --a.h;
                    (a.f || a.g) && setTimeout((function() {
                        var a = e || null, m = d || null || {};
                        if (0 === c.length && f) L(b.a); else {
                            b.f += c.length;
                            f && (b.j = f);
                            var h, l = [];
                            for (h = 0; h < c.length; h++) {
                                var k = c[h], n = m[k.c], r = b.a, x = k;
                                r.g && w(r.f, [ r.a.c("wf", x.c, J(x).toString(), "loading") ]);
                                K(r, "fontloading", x);
                                r = null;
                                if (null === X) if (window.FontFace) {
                                    var x = /Gecko.*Firefox\/(\d+)/.exec(window.navigator.userAgent), xa = /OS X.*Version\/10\..*Safari/.exec(window.navigator.userAgent) && /Apple/.exec(window.navigator.vendor);
                                    X = x ? 42 < parseInt(x[1], 10) : xa ? !1 : !0;
                                } else X = !1;
                                X ? r = new P(p(b.g, b), p(b.h, b), b.c, k, b.s, n) : r = new Q(p(b.g, b), p(b.h, b), b.c, k, b.s, a, n);
                                l.push(r);
                            }
                            for (h = 0; h < l.length; h++) l[h].start();
                        }
                    }), 0);
                }
                function pa(a, b, c) {
                    var d = [], e = c.timeout;
                    ia(b);
                    var d = ka(a.a, c, a.c), f = new W(a.c, b, e);
                    a.h = d.length;
                    b = 0;
                    for (c = d.length; b < c; b++) d[b].load((function(b, d, c) {
                        qa(a, f, b, d, c);
                    }));
                }
                function ra(a, b) {
                    this.c = a;
                    this.a = b;
                }
                ra.prototype.load = function(a) {
                    function b() {
                        if (f["__mti_fntLst" + d]) {
                            var c = f["__mti_fntLst" + d](), e = [], h;
                            if (c) for (var l = 0; l < c.length; l++) {
                                var k = c[l].fontfamily;
                                void 0 != c[l].fontStyle && void 0 != c[l].fontWeight ? (h = c[l].fontStyle + c[l].fontWeight, 
                                e.push(new G(k, h))) : e.push(new G(k));
                            }
                            a(e);
                        } else setTimeout((function() {
                            b();
                        }), 50);
                    }
                    var c = this, d = c.a.projectId, e = c.a.version;
                    if (d) {
                        var f = c.c.o;
                        A(this.c, (c.a.api || "https://fast.fonts.net/jsapi") + "/" + d + ".js" + (e ? "?v=" + e : ""), (function(e) {
                            e ? a([]) : (f["__MonotypeConfiguration__" + d] = function() {
                                return c.a;
                            }, b());
                        })).id = "__MonotypeAPIScript__" + d;
                    } else a([]);
                };
                function sa(a, b) {
                    this.c = a;
                    this.a = b;
                }
                sa.prototype.load = function(a) {
                    var b, c, d = this.a.urls || [], e = this.a.families || [], f = this.a.testStrings || {}, g = new B;
                    b = 0;
                    for (c = d.length; b < c; b++) z(this.c, d[b], C(g));
                    var m = [];
                    b = 0;
                    for (c = e.length; b < c; b++) if (d = e[b].split(":"), d[1]) for (var h = d[1].split(","), l = 0; l < h.length; l += 1) m.push(new G(d[0], h[l])); else m.push(new G(d[0]));
                    E(g, (function() {
                        a(m, f);
                    }));
                };
                function ta(a, b) {
                    a ? this.c = a : this.c = ua;
                    this.a = [];
                    this.f = [];
                    this.g = b || "";
                }
                var ua = "https://fonts.googleapis.com/css";
                function va(a, b) {
                    for (var c = b.length, d = 0; d < c; d++) {
                        var e = b[d].split(":");
                        3 == e.length && a.f.push(e.pop());
                        var f = "";
                        2 == e.length && "" != e[1] && (f = ":");
                        a.a.push(e.join(f));
                    }
                }
                function wa(a) {
                    if (0 == a.a.length) throw Error("No fonts to load!");
                    if (-1 != a.c.indexOf("kit=")) return a.c;
                    for (var b = a.a.length, c = [], d = 0; d < b; d++) c.push(a.a[d].replace(/ /g, "+"));
                    b = a.c + "?family=" + c.join("%7C");
                    0 < a.f.length && (b += "&subset=" + a.f.join(","));
                    0 < a.g.length && (b += "&text=" + encodeURIComponent(a.g));
                    return b;
                }
                function ya(a) {
                    this.f = a;
                    this.a = [];
                    this.c = {};
                }
                var za = {
                    latin: "BESbswy",
                    "latin-ext": "çöüğş",
                    cyrillic: "йяЖ",
                    greek: "αβΣ",
                    khmer: "កខគ",
                    Hanuman: "កខគ"
                }, Aa = {
                    thin: "1",
                    extralight: "2",
                    "extra-light": "2",
                    ultralight: "2",
                    "ultra-light": "2",
                    light: "3",
                    regular: "4",
                    book: "4",
                    medium: "5",
                    "semi-bold": "6",
                    semibold: "6",
                    "demi-bold": "6",
                    demibold: "6",
                    bold: "7",
                    "extra-bold": "8",
                    extrabold: "8",
                    "ultra-bold": "8",
                    ultrabold: "8",
                    black: "9",
                    heavy: "9",
                    l: "3",
                    r: "4",
                    b: "7"
                }, Ba = {
                    i: "i",
                    italic: "i",
                    n: "n",
                    normal: "n"
                }, Ca = /^(thin|(?:(?:extra|ultra)-?)?light|regular|book|medium|(?:(?:semi|demi|extra|ultra)-?)?bold|black|heavy|l|r|b|[1-9]00)?(n|i|normal|italic)?$/;
                function Da(a) {
                    for (var b = a.f.length, c = 0; c < b; c++) {
                        var d = a.f[c].split(":"), e = d[0].replace(/\+/g, " "), f = [ "n4" ];
                        if (2 <= d.length) {
                            var g;
                            var m = d[1];
                            g = [];
                            if (m) for (var m = m.split(","), h = m.length, l = 0; l < h; l++) {
                                var k;
                                k = m[l];
                                if (k.match(/^[\w-]+$/)) {
                                    var n = Ca.exec(k.toLowerCase());
                                    if (null == n) k = ""; else {
                                        k = n[2];
                                        k = null == k || "" == k ? "n" : Ba[k];
                                        n = n[1];
                                        if (null == n || "" == n) n = "4"; else var r = Aa[n], n = r ? r : isNaN(n) ? "4" : n.substr(0, 1);
                                        k = [ k, n ].join("");
                                    }
                                } else k = "";
                                k && g.push(k);
                            }
                            0 < g.length && (f = g);
                            3 == d.length && (d = d[2], g = [], d = d ? d.split(",") : g, 0 < d.length && (d = za[d[0]]) && (a.c[e] = d));
                        }
                        a.c[e] || (d = za[e]) && (a.c[e] = d);
                        for (d = 0; d < f.length; d += 1) a.a.push(new G(e, f[d]));
                    }
                }
                function Ea(a, b) {
                    this.c = a;
                    this.a = b;
                }
                var Fa = {
                    Arimo: !0,
                    Cousine: !0,
                    Tinos: !0
                };
                Ea.prototype.load = function(a) {
                    var b = new B, c = this.c, d = new ta(this.a.api, this.a.text), e = this.a.families;
                    va(d, e);
                    var f = new ya(e);
                    Da(f);
                    z(c, wa(d), C(b));
                    E(b, (function() {
                        a(f.a, f.c, Fa);
                    }));
                };
                function Ga(a, b) {
                    this.c = a;
                    this.a = b;
                }
                Ga.prototype.load = function(a) {
                    var b = this.a.id, c = this.c.o;
                    b ? A(this.c, (this.a.api || "https://use.typekit.net") + "/" + b + ".js", (function(b) {
                        if (b) a([]); else if (c.Typekit && c.Typekit.config && c.Typekit.config.fn) {
                            b = c.Typekit.config.fn;
                            for (var e = [], f = 0; f < b.length; f += 2) for (var g = b[f], m = b[f + 1], h = 0; h < m.length; h++) e.push(new G(g, m[h]));
                            try {
                                c.Typekit.load({
                                    events: !1,
                                    classes: !1,
                                    async: !0
                                });
                            } catch (l) {}
                            a(e);
                        }
                    }), 2e3) : a([]);
                };
                function Ha(a, b) {
                    this.c = a;
                    this.f = b;
                    this.a = [];
                }
                Ha.prototype.load = function(a) {
                    var b = this.f.id, c = this.c.o, d = this;
                    b ? (c.__webfontfontdeckmodule__ || (c.__webfontfontdeckmodule__ = {}), c.__webfontfontdeckmodule__[b] = function(b, c) {
                        for (var g = 0, m = c.fonts.length; g < m; ++g) {
                            var h = c.fonts[g];
                            d.a.push(new G(h.name, ga("font-weight:" + h.weight + ";font-style:" + h.style)));
                        }
                        a(d.a);
                    }, A(this.c, (this.f.api || "https://f.fontdeck.com/s/css/js/") + ea(this.c) + "/" + b + ".js", (function(b) {
                        b && a([]);
                    }))) : a([]);
                };
                var Y = new oa(window);
                Y.a.c.custom = function(a, b) {
                    return new sa(b, a);
                };
                Y.a.c.fontdeck = function(a, b) {
                    return new Ha(b, a);
                };
                Y.a.c.monotype = function(a, b) {
                    return new ra(b, a);
                };
                Y.a.c.typekit = function(a, b) {
                    return new Ga(b, a);
                };
                Y.a.c.google = function(a, b) {
                    return new Ea(b, a);
                };
                var Z = {
                    load: p(Y.load, Y)
                };
                true ? !(__WEBPACK_AMD_DEFINE_RESULT__ = function() {
                    return Z;
                }.call(exports, __webpack_require__, exports, module), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)) : 0;
            })();
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
        "use strict";
        __webpack_require__.r(__webpack_exports__);
        var _preview_async_css__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(334);
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
        var makeShortcutFor = function makeShortcutFor(item) {
            if (_toConsumableArray(item.children).find((function(e) {
                return e.matches(".lotta-customizer-shortcut");
            }))) {
                return;
            }
            var shortcut = document.createElement("a");
            shortcut.classList.add("lotta-customizer-shortcut");
            shortcut.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13.89 3.39l2.71 2.72c.46.46.42 1.24.03 1.64l-8.01 8.02-5.56 1.16 1.16-5.58s7.6-7.63 7.99-8.03c.39-.39 1.22-.39 1.68.07zm-2.73 2.79l-5.59 5.61 1.11 1.11 5.54-5.65zm-2.97 8.23l5.58-5.6-1.07-1.08-5.59 5.6z"></path></svg>';
            shortcut.addEventListener("click", (function(e) {
                e.preventDefault();
                e.stopPropagation();
                wp.customize.preview.send("lotta-initiate-deep-link", item.dataset.shortcutLocation);
            }));
            item.appendChild(shortcut);
        };
        var makeAllShortcuts = function makeAllShortcuts() {
            _toConsumableArray(document.querySelectorAll("[data-shortcut-location]")).map((function(el) {
                return makeShortcutFor(el);
            }));
        };
        if (wp.customize) {
            wp.customize.bind("preview-ready", (function() {
                makeAllShortcuts();
            }));
            if (wp.customize.selectiveRefresh) {
                wp.customize.selectiveRefresh.bind("partial-content-rendered", (function() {
                    makeAllShortcuts();
                }));
            }
        }
    })();
})();
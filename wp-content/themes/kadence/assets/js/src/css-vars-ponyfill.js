/*!
 * css-vars-ponyfill
 * v2.3.2
 * https://jhildenbiddle.github.io/css-vars-ponyfill/
 * (c) 2018-2020 John Hildenbiddle <http://hildenbiddle.com>
 * MIT license
 */
!(function (e, t) {
    "object" == typeof exports && "undefined" != typeof module ? (module.exports = t()) : "function" == typeof define && define.amd ? define(t) : ((e = e || self).cssVars = t());
})(this, function () {
    "use strict";
    function e() {
        return (e =
            Object.assign ||
            function (e) {
                for (var t = 1; t < arguments.length; t++) {
                    var r = arguments[t];
                    for (var n in r) Object.prototype.hasOwnProperty.call(r, n) && (e[n] = r[n]);
                }
                return e;
            }).apply(this, arguments);
    }
    function t(e) {
        return (
            (function (e) {
                if (Array.isArray(e)) return r(e);
            })(e) ||
            (function (e) {
                if ("undefined" != typeof Symbol && Symbol.iterator in Object(e)) return Array.from(e);
            })(e) ||
            (function (e, t) {
                if (!e) return;
                if ("string" == typeof e) return r(e, t);
                var n = Object.prototype.toString.call(e).slice(8, -1);
                "Object" === n && e.constructor && (n = e.constructor.name);
                if ("Map" === n || "Set" === n) return Array.from(e);
                if ("Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return r(e, t);
            })(e) ||
            (function () {
                throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
            })()
        );
    }
    function r(e, t) {
        (null == t || t > e.length) && (t = e.length);
        for (var r = 0, n = new Array(t); r < t; r++) n[r] = e[r];
        return n;
    }
    function n(e) {
        var t = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {},
            r = { mimeType: t.mimeType || null, onBeforeSend: t.onBeforeSend || Function.prototype, onSuccess: t.onSuccess || Function.prototype, onError: t.onError || Function.prototype, onComplete: t.onComplete || Function.prototype },
            n = Array.isArray(e) ? e : [e],
            o = Array.apply(null, Array(n.length)).map(function (e) {
                return null;
            });
        function a() {
            var e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : "",
                t = "<" === e.trim().charAt(0);
            return !t;
        }
        function s(e, t) {
            r.onError(e, n[t], t);
        }
        function c(e, t) {
            var a = r.onSuccess(e, n[t], t);
            (e = !1 === a ? "" : a || e), (o[t] = e), -1 === o.indexOf(null) && r.onComplete(o);
        }
        var i = document.createElement("a");
        n.forEach(function (e, t) {
            if ((i.setAttribute("href", e), (i.href = String(i.href)), Boolean(document.all && !window.atob) && i.host.split(":")[0] !== location.host.split(":")[0])) {
                if (i.protocol === location.protocol) {
                    var n = new XDomainRequest();
                    n.open("GET", e),
                        (n.timeout = 0),
                        (n.onprogress = Function.prototype),
                        (n.ontimeout = Function.prototype),
                        (n.onload = function () {
                            a(n.responseText) ? c(n.responseText, t) : s(n, t);
                        }),
                        (n.onerror = function (e) {
                            s(n, t);
                        }),
                        setTimeout(function () {
                            n.send();
                        }, 0);
                } else console.warn("Internet Explorer 9 Cross-Origin (CORS) requests must use the same protocol (".concat(e, ")")), s(null, t);
            } else {
                var o = new XMLHttpRequest();
                o.open("GET", e),
                    r.mimeType && o.overrideMimeType && o.overrideMimeType(r.mimeType),
                    r.onBeforeSend(o, e, t),
                    (o.onreadystatechange = function () {
                        4 === o.readyState && (200 === o.status && a(o.responseText) ? c(o.responseText, t) : s(o, t));
                    }),
                    o.send();
            }
        });
    }
    function o(e) {
        var t = /\/\*[\s\S]+?\*\//g,
            r = /(?:@import\s*)(?:url\(\s*)?(?:['"])([^'"]*)(?:['"])(?:\s*\))?(?:[^;]*;)/g,
            o = {
                rootElement: e.rootElement || document,
                include: e.include || 'style,link[rel="stylesheet"]',
                exclude: e.exclude || null,
                filter: e.filter || null,
                skipDisabled: !1 !== e.skipDisabled,
                useCSSOM: e.useCSSOM || !1,
                onBeforeSend: e.onBeforeSend || Function.prototype,
                onSuccess: e.onSuccess || Function.prototype,
                onError: e.onError || Function.prototype,
                onComplete: e.onComplete || Function.prototype,
            },
            s = Array.apply(null, o.rootElement.querySelectorAll(o.include)).filter(function (e) {
                return (t = e), (r = o.exclude), !(t.matches || t.matchesSelector || t.webkitMatchesSelector || t.mozMatchesSelector || t.msMatchesSelector || t.oMatchesSelector).call(t, r);
                var t, r;
            }),
            c = Array.apply(null, Array(s.length)).map(function (e) {
                return null;
            });
        function i() {
            if (-1 === c.indexOf(null)) {
                var e = c.join("");
                o.onComplete(e, c, s);
            }
        }
        function u(e, t, r, a) {
            var s = o.onSuccess(e, r, a);
            (function e(t, r, a, s) {
                var c = arguments.length > 4 && void 0 !== arguments[4] ? arguments[4] : [],
                    i = arguments.length > 5 && void 0 !== arguments[5] ? arguments[5] : [],
                    u = l(t, a, i);
                u.rules.length
                    ? n(u.absoluteUrls, {
                          onBeforeSend: function (e, t, n) {
                              o.onBeforeSend(e, r, t);
                          },
                          onSuccess: function (e, t, n) {
                              var a = o.onSuccess(e, r, t),
                                  s = l((e = !1 === a ? "" : a || e), t, i);
                              return (
                                  s.rules.forEach(function (t, r) {
                                      e = e.replace(t, s.absoluteRules[r]);
                                  }),
                                  e
                              );
                          },
                          onError: function (n, o, l) {
                              c.push({ xhr: n, url: o }), i.push(u.rules[l]), e(t, r, a, s, c, i);
                          },
                          onComplete: function (n) {
                              n.forEach(function (e, r) {
                                  t = t.replace(u.rules[r], e);
                              }),
                                  e(t, r, a, s, c, i);
                          },
                      })
                    : s(t, c);
            })((e = void 0 !== s && !1 === Boolean(s) ? "" : s || e), r, a, function (e, n) {
                null === c[t] &&
                    (n.forEach(function (e) {
                        return o.onError(e.xhr, r, e.url);
                    }),
                    !o.filter || o.filter.test(e) ? (c[t] = e) : (c[t] = ""),
                    i());
            });
        }
        function l(e, n) {
            var o = arguments.length > 2 && void 0 !== arguments[2] ? arguments[2] : [],
                s = {};
            return (
                (s.rules = (e.replace(t, "").match(r) || []).filter(function (e) {
                    return -1 === o.indexOf(e);
                })),
                (s.urls = s.rules.map(function (e) {
                    return e.replace(r, "$1");
                })),
                (s.absoluteUrls = s.urls.map(function (e) {
                    return a(e, n);
                })),
                (s.absoluteRules = s.rules.map(function (e, t) {
                    var r = s.urls[t],
                        o = a(s.absoluteUrls[t], n);
                    return e.replace(r, o);
                })),
                s
            );
        }
        s.length
            ? s.forEach(function (e, t) {
                  var r = e.getAttribute("href"),
                      s = e.getAttribute("rel"),
                      l = "LINK" === e.nodeName && r && s && -1 !== s.toLowerCase().indexOf("stylesheet"),
                      f = !1 !== o.skipDisabled && e.disabled,
                      d = "STYLE" === e.nodeName;
                  if (l && !f)
                      n(r, {
                          mimeType: "text/css",
                          onBeforeSend: function (t, r, n) {
                              o.onBeforeSend(t, e, r);
                          },
                          onSuccess: function (n, o, s) {
                              var c = a(r);
                              u(n, t, e, c);
                          },
                          onError: function (r, n, a) {
                              (c[t] = ""), o.onError(r, e, n), i();
                          },
                      });
                  else if (d && !f) {
                      var p = e.textContent;
                      o.useCSSOM &&
                          (p = Array.apply(null, e.sheet.cssRules)
                              .map(function (e) {
                                  return e.cssText;
                              })
                              .join("")),
                          u(p, t, e, location.href);
                  } else (c[t] = ""), i();
              })
            : o.onComplete("", []);
    }
    function a(e, t) {
        var r = document.implementation.createHTMLDocument(""),
            n = r.createElement("base"),
            o = r.createElement("a");
        return r.head.appendChild(n), r.body.appendChild(o), (n.href = t || document.baseURI || (document.querySelector("base") || {}).href || location.href), (o.href = e), o.href;
    }
    var s = c;
    function c(e, t, r) {
        e instanceof RegExp && (e = i(e, r)), t instanceof RegExp && (t = i(t, r));
        var n = u(e, t, r);
        return n && { start: n[0], end: n[1], pre: r.slice(0, n[0]), body: r.slice(n[0] + e.length, n[1]), post: r.slice(n[1] + t.length) };
    }
    function i(e, t) {
        var r = t.match(e);
        return r ? r[0] : null;
    }
    function u(e, t, r) {
        var n,
            o,
            a,
            s,
            c,
            i = r.indexOf(e),
            u = r.indexOf(t, i + 1),
            l = i;
        if (i >= 0 && u > 0) {
            for (n = [], a = r.length; l >= 0 && !c; )
                l == i ? (n.push(l), (i = r.indexOf(e, l + 1))) : 1 == n.length ? (c = [n.pop(), u]) : ((o = n.pop()) < a && ((a = o), (s = u)), (u = r.indexOf(t, l + 1))), (l = i < u && i >= 0 ? i : u);
            n.length && (c = [a, s]);
        }
        return c;
    }
    function l(t) {
        var r = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {},
            n = { preserveStatic: !0, removeComments: !1 },
            o = e({}, n, r),
            a = [];
        function c(e) {
            throw new Error("CSS parse error: ".concat(e));
        }
        function i(e) {
            var r = e.exec(t);
            if (r) return (t = t.slice(r[0].length)), r;
        }
        function u() {
            return i(/^{\s*/);
        }
        function l() {
            return i(/^}/);
        }
        function f() {
            i(/^\s*/);
        }
        function d() {
            if ((f(), "/" === t[0] && "*" === t[1])) {
                for (var e = 2; t[e] && ("*" !== t[e] || "/" !== t[e + 1]); ) e++;
                if (!t[e]) return c("end of comment is missing");
                var r = t.slice(2, e);
                return (t = t.slice(e + 2)), { type: "comment", comment: r };
            }
        }
        function p() {
            for (var e, t = []; (e = d()); ) t.push(e);
            return o.removeComments ? [] : t;
        }
        function m() {
            for (f(); "}" === t[0]; ) c("extra closing bracket");
            var e = i(/^(("(?:\\"|[^"])*"|'(?:\\'|[^'])*'|[^{])+)/);
            if (e)
                return e[0]
                    .trim()
                    .replace(/\/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*\/+/g, "")
                    .replace(/"(?:\\"|[^"])*"|'(?:\\'|[^'])*'/g, function (e) {
                        return e.replace(/,/g, "‌");
                    })
                    .split(/\s*(?![^(]*\)),\s*/)
                    .map(function (e) {
                        return e.replace(/\u200C/g, ",");
                    });
        }
        function v() {
            if ("@" === t[0]) return k();
            i(/^([;\s]*)+/);
            var e = /\/\*[^*]*\*+([^/*][^*]*\*+)*\//g,
                r = i(/^(\*?[-#/*\\\w]+(\[[0-9a-z_-]+\])?)\s*/);
            if (r) {
                if (((r = r[0].trim()), !i(/^:\s*/))) return c("property missing ':'");
                var n = i(/^((?:\/\*.*?\*\/|'(?:\\'|.)*?'|"(?:\\"|.)*?"|\((\s*'(?:\\'|.)*?'|"(?:\\"|.)*?"|[^)]*?)\s*\)|[^};])+)/),
                    o = { type: "declaration", property: r.replace(e, ""), value: n ? n[0].replace(e, "").trim() : "" };
                return i(/^[;\s]*/), o;
            }
        }
        function h() {
            if (!u()) return c("missing '{'");
            for (var e, t = p(); (e = v()); ) t.push(e), (t = t.concat(p()));
            return l() ? t : c("missing '}'");
        }
        function y() {
            f();
            for (var e, t = []; (e = i(/^((\d+\.\d+|\.\d+|\d+)%?|[a-z]+)\s*/)); ) t.push(e[1]), i(/^,\s*/);
            if (t.length) return { type: "keyframe", values: t, declarations: h() };
        }
        function g() {
            var e = i(/^@([-\w]+)?keyframes\s*/);
            if (e) {
                var t = e[1];
                if (!(e = i(/^([-\w]+)\s*/))) return c("@keyframes missing name");
                var r,
                    n = e[1];
                if (!u()) return c("@keyframes missing '{'");
                for (var o = p(); (r = y()); ) o.push(r), (o = o.concat(p()));
                return l() ? { type: "keyframes", name: n, vendor: t, keyframes: o } : c("@keyframes missing '}'");
            }
        }
        function b() {
            if (i(/^@page */)) return { type: "page", selectors: m() || [], declarations: h() };
        }
        function S() {
            var e = i(/@(top|bottom|left|right)-(left|center|right|top|middle|bottom)-?(corner)?\s*/);
            if (e) return { type: "page-margin-box", name: "".concat(e[1], "-").concat(e[2]) + (e[3] ? "-".concat(e[3]) : ""), declarations: h() };
        }
        function E() {
            if (i(/^@font-face\s*/)) return { type: "font-face", declarations: h() };
        }
        function w() {
            var e = i(/^@supports *([^{]+)/);
            if (e) return { type: "supports", supports: e[1].trim(), rules: M() };
        }
        function C() {
            if (i(/^@host\s*/)) return { type: "host", rules: M() };
        }
        function x() {
            var e = i(/^@media([^{]+)*/);
            if (e) return { type: "media", media: (e[1] || "").trim(), rules: M() };
        }
        function A() {
            var e = i(/^@custom-media\s+(--[^\s]+)\s*([^{;]+);/);
            if (e) return { type: "custom-media", name: e[1].trim(), media: e[2].trim() };
        }
        function O() {
            var e = i(/^@([-\w]+)?document *([^{]+)/);
            if (e) return { type: "document", document: e[2].trim(), vendor: e[1] ? e[1].trim() : null, rules: M() };
        }
        function j() {
            var e = i(/^@(import|charset|namespace)\s*([^;]+);/);
            if (e) return { type: e[1], name: e[2].trim() };
        }
        function k() {
            if ((f(), "@" === t[0])) {
                var e = j() || E() || x() || g() || w() || O() || A() || C() || b() || S();
                if (e && !o.preserveStatic) {
                    var r = !1;
                    if (e.declarations)
                        r = e.declarations.some(function (e) {
                            return /var\(/.test(e.value);
                        });
                    else
                        r = (e.keyframes || e.rules || []).some(function (e) {
                            return (e.declarations || []).some(function (e) {
                                return /var\(/.test(e.value);
                            });
                        });
                    return r ? e : {};
                }
                return e;
            }
        }
        function _() {
            if (!o.preserveStatic) {
                var e = s("{", "}", t);
                if (e) {
                    var r = /:(?:root|host)(?![.:#(])/.test(e.pre) && /--\S*\s*:/.test(e.body),
                        n = /var\(/.test(e.body);
                    if (!r && !n) return (t = t.slice(e.end + 1)), {};
                }
            }
            var a = m() || [],
                i = o.preserveStatic
                    ? h()
                    : h().filter(function (e) {
                          var t =
                                  a.some(function (e) {
                                      return /:(?:root|host)(?![.:#(])/.test(e);
                                  }) && /^--\S/.test(e.property),
                              r = /var\(/.test(e.value);
                          return t || r;
                      });
            return a.length || c("selector missing"), { type: "rule", selectors: a, declarations: i };
        }
        function M(e) {
            if (!e && !u()) return c("missing '{'");
            for (var r, n = p(); t.length && (e || "}" !== t[0]) && (r = k() || _()); ) r.type && n.push(r), (n = n.concat(p()));
            return e || l() ? n : c("missing '}'");
        }
        return { type: "stylesheet", stylesheet: { rules: M(!0), errors: a } };
    }
    function f(t) {
        var r = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {},
            n = { parseHost: !1, store: {}, onWarning: function () {} },
            o = e({}, n, r),
            a = new RegExp(":".concat(o.parseHost ? "host" : "root", "$"));
        return (
            "string" == typeof t && (t = l(t, o)),
            t.stylesheet.rules.forEach(function (e) {
                "rule" === e.type &&
                    e.selectors.some(function (e) {
                        return a.test(e);
                    }) &&
                    e.declarations.forEach(function (e, t) {
                        var r = e.property,
                            n = e.value;
                        r && 0 === r.indexOf("--") && (o.store[r] = n);
                    });
            }),
            o.store
        );
    }
    function d(e) {
        var t = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : "",
            r = arguments.length > 2 ? arguments[2] : void 0,
            n = {
                charset: function (e) {
                    return "@charset " + e.name + ";";
                },
                comment: function (e) {
                    return 0 === e.comment.indexOf("__CSSVARSPONYFILL") ? "/*" + e.comment + "*/" : "";
                },
                "custom-media": function (e) {
                    return "@custom-media " + e.name + " " + e.media + ";";
                },
                declaration: function (e) {
                    return e.property + ":" + e.value + ";";
                },
                document: function (e) {
                    return "@" + (e.vendor || "") + "document " + e.document + "{" + o(e.rules) + "}";
                },
                "font-face": function (e) {
                    return "@font-face{" + o(e.declarations) + "}";
                },
                host: function (e) {
                    return "@host{" + o(e.rules) + "}";
                },
                import: function (e) {
                    return "@import " + e.name + ";";
                },
                keyframe: function (e) {
                    return e.values.join(",") + "{" + o(e.declarations) + "}";
                },
                keyframes: function (e) {
                    return "@" + (e.vendor || "") + "keyframes " + e.name + "{" + o(e.keyframes) + "}";
                },
                media: function (e) {
                    return "@media " + e.media + "{" + o(e.rules) + "}";
                },
                namespace: function (e) {
                    return "@namespace " + e.name + ";";
                },
                page: function (e) {
                    return "@page " + (e.selectors.length ? e.selectors.join(", ") : "") + "{" + o(e.declarations) + "}";
                },
                "page-margin-box": function (e) {
                    return "@" + e.name + "{" + o(e.declarations) + "}";
                },
                rule: function (e) {
                    var t = e.declarations;
                    if (t.length) return e.selectors.join(",") + "{" + o(t) + "}";
                },
                supports: function (e) {
                    return "@supports " + e.supports + "{" + o(e.rules) + "}";
                },
            };
        function o(e) {
            for (var o = "", a = 0; a < e.length; a++) {
                var s = e[a];
                r && r(s);
                var c = n[s.type](s);
                c && ((o += c), c.length && s.selectors && (o += t));
            }
            return o;
        }
        return o(e.stylesheet.rules);
    }
    function p(e, t) {
        e.rules.forEach(function (r) {
            r.rules
                ? p(r, t)
                : r.keyframes
                ? r.keyframes.forEach(function (e) {
                      "keyframe" === e.type && t(e.declarations, r);
                  })
                : r.declarations && t(r.declarations, e);
        });
    }
    c.range = u;
    function m(t) {
        var r = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {},
            n = { preserveStatic: !0, preserveVars: !1, variables: {}, onWarning: function () {} },
            o = e({}, n, r);
        return (
            "string" == typeof t && (t = l(t, o)),
            p(t.stylesheet, function (e, t) {
                for (var r = 0; r < e.length; r++) {
                    var n = e[r],
                        a = n.type,
                        s = n.property,
                        c = n.value;
                    if ("declaration" === a)
                        if (o.preserveVars || !s || 0 !== s.indexOf("--")) {
                            if (-1 !== c.indexOf("var(")) {
                                var i = h(c, o);
                                i !== n.value && ((i = v(i)), o.preserveVars ? (e.splice(r, 0, { type: a, property: s, value: i }), r++) : (n.value = i));
                            }
                        } else e.splice(r, 1), r--;
                }
            }),
            d(t)
        );
    }
    function v(e) {
        return (
            (e.match(/calc\(([^)]+)\)/g) || []).forEach(function (t) {
                var r = "calc".concat(t.split("calc").join(""));
                e = e.replace(t, r);
            }),
            e
        );
    }
    function h(e) {
        var t = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {},
            r = arguments.length > 2 ? arguments[2] : void 0;
        if (-1 === e.indexOf("var(")) return e;
        var n = s("(", ")", e);
        function o(e) {
            var n = e.split(",")[0].replace(/[\s\n\t]/g, ""),
                o = (e.match(/(?:\s*,\s*){1}(.*)?/) || [])[1],
                a = Object.prototype.hasOwnProperty.call(t.variables, n) ? String(t.variables[n]) : void 0,
                s = a || (o ? String(o) : void 0),
                c = r || e;
            return a || t.onWarning('variable "'.concat(n, '" is undefined')), s && "undefined" !== s && s.length > 0 ? h(s, t, c) : "var(".concat(c, ")");
        }
        if (n) {
            if ("var" === n.pre.slice(-3)) {
                var a = 0 === n.body.trim().length;
                return a ? (t.onWarning("var() must contain a non-whitespace string"), e) : n.pre.slice(0, -3) + o(n.body) + h(n.post, t);
            }
            return n.pre + "(".concat(h(n.body, t), ")") + h(n.post, t);
        }
        return -1 !== e.indexOf("var(") && t.onWarning('missing closing ")" in the value "'.concat(e, '"')), e;
    }
    var y = "undefined" != typeof window,
        g = y && window.CSS && window.CSS.supports && window.CSS.supports("(--a: 0)"),
        b = { group: 0, job: 0 },
        S = {
            rootElement: y ? document : null,
            shadowDOM: !1,
            include: "style,link[rel=stylesheet]",
            exclude: "",
            variables: {},
            onlyLegacy: !0,
            preserveStatic: !0,
            preserveVars: !1,
            silent: !1,
            updateDOM: !0,
            updateURLs: !0,
            watch: null,
            onBeforeSend: function () {},
            onError: function () {},
            onWarning: function () {},
            onSuccess: function () {},
            onComplete: function () {},
            onFinally: function () {},
        },
        E = {
            cssComments: /\/\*[\s\S]+?\*\//g,
            cssKeyframes: /@(?:-\w*-)?keyframes/,
            cssMediaQueries: /@media[^{]+\{([\s\S]+?})\s*}/g,
            cssUrls: /url\((?!['"]?(?:data|http|\/\/):)['"]?([^'")]*)['"]?\)/g,
            cssVarDeclRules: /(?::(?:root|host)(?![.:#(])[\s,]*[^{]*{\s*[^}]*})/g,
            cssVarDecls: /(?:[\s;]*)(-{2}\w[\w-]*)(?:\s*:\s*)([^;]*);/g,
            cssVarFunc: /var\(\s*--[\w-]/,
            cssVars: /(?:(?::(?:root|host)(?![.:#(])[\s,]*[^{]*{\s*[^;]*;*\s*)|(?:var\(\s*))(--[^:)]+)(?:\s*[:)])/,
        },
        w = { dom: {}, job: {}, user: {} },
        C = !1,
        x = null,
        A = 0,
        O = null,
        j = !1;
    function k() {
        var r = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : {},
            n = "cssVars(): ",
            a = e({}, S, r);
        function s(e, t, r, o) {
            !a.silent && window.console && console.error("".concat(n).concat(e, "\n"), t), a.onError(e, t, r, o);
        }
        function c(e) {
            !a.silent && window.console && console.warn("".concat(n).concat(e)), a.onWarning(e);
        }
        function i(e) {
            a.onFinally(Boolean(e), g, N() - a.__benchmark);
        }
        if (y) {
            if (a.watch) return (a.watch = S.watch), _(a), void k(a);
            if ((!1 === a.watch && x && (x.disconnect(), (x = null)), !a.__benchmark)) {
                if (C === a.rootElement) return void M(r);
                if (
                    ((a.__benchmark = N()),
                    (a.exclude = [x ? '[data-cssvars]:not([data-cssvars=""])' : '[data-cssvars="out"]', a.exclude]
                        .filter(function (e) {
                            return e;
                        })
                        .join(",")),
                    (a.variables = R(a.variables)),
                    !x)
                ) {
                    var u = Array.apply(null, a.rootElement.querySelectorAll('[data-cssvars="out"]'));
                    if (
                        (u.forEach(function (e) {
                            var t = e.getAttribute("data-cssvars-group");
                            (t ? a.rootElement.querySelector('[data-cssvars="src"][data-cssvars-group="'.concat(t, '"]')) : null) || e.parentNode.removeChild(e);
                        }),
                        A)
                    ) {
                        var p = a.rootElement.querySelectorAll('[data-cssvars]:not([data-cssvars="out"])');
                        p.length < A && ((A = p.length), (w.dom = {}));
                    }
                }
            }
            if ("loading" !== document.readyState)
                if (g && a.onlyLegacy) {
                    var v = !1;
                    if (a.updateDOM) {
                        var h = a.rootElement.host || (a.rootElement === document ? document.documentElement : a.rootElement);
                        Object.keys(a.variables).forEach(function (e) {
                            var t = a.variables[e];
                            (v = v || t !== getComputedStyle(h).getPropertyValue(e)), h.style.setProperty(e, t);
                        });
                    }
                    i(v);
                } else
                    !j && (a.shadowDOM || a.rootElement.shadowRoot || a.rootElement.host)
                        ? o({
                              rootElement: S.rootElement,
                              include: S.include,
                              exclude: a.exclude,
                              skipDisabled: !1,
                              onSuccess: function (e, t, r) {
                                  return (e = ((e = e.replace(E.cssComments, "").replace(E.cssMediaQueries, "")).match(E.cssVarDeclRules) || []).join("")) || !1;
                              },
                              onComplete: function (e, t, r) {
                                  f(e, { store: w.dom, onWarning: c }), (j = !0), k(a);
                              },
                          })
                        : ((C = a.rootElement),
                          o({
                              rootElement: a.rootElement,
                              include: a.include,
                              exclude: a.exclude,
                              skipDisabled: !1,
                              onBeforeSend: a.onBeforeSend,
                              onError: function (e, t, r) {
                                  var n = e.responseURL || D(r, location.href),
                                      o = e.statusText ? "(".concat(e.statusText, ")") : "Unspecified Error" + (0 === e.status ? " (possibly CORS related)" : "");
                                  s("CSS XHR Error: ".concat(n, " ").concat(e.status, " ").concat(o), t, e, n);
                              },
                              onSuccess: function (e, t, r) {
                                  var n = "LINK" === t.tagName,
                                      o = "STYLE" === t.tagName && e !== t.textContent,
                                      s = a.onSuccess(e, t, r);
                                  return (e = void 0 !== s && !1 === Boolean(s) ? "" : s || e), a.updateURLs && (n || o) && (e = L(e, r)), e;
                              },
                              onComplete: function (r, n) {
                                  var o = arguments.length > 2 && void 0 !== arguments[2] ? arguments[2] : [],
                                      u = e({}, w.dom, w.user);
                                  if (
                                      ((w.job = {}),
                                      o.forEach(function (e, t) {
                                          var r = n[t];
                                          if (E.cssVars.test(r))
                                              try {
                                                  var o = l(r, { preserveStatic: a.preserveStatic, removeComments: !0 });
                                                  f(o, { parseHost: Boolean(a.rootElement.host), store: w.dom, onWarning: c }), (e.__cssVars = { tree: o });
                                              } catch (t) {
                                                  s(t.message, e);
                                              }
                                      }),
                                      e(w.job, w.dom),
                                      a.updateDOM ? (e(w.user, a.variables), e(w.job, w.user)) : (e(w.job, w.user, a.variables), e(u, a.variables)),
                                      b.job > 0 &&
                                          Boolean(
                                              Object.keys(w.job).length > Object.keys(u).length ||
                                                  Boolean(
                                                      Object.keys(u).length &&
                                                          Object.keys(w.job).some(function (e) {
                                                              return w.job[e] !== u[e];
                                                          })
                                                  )
                                          ))
                                  )
                                      V(a.rootElement), k(a);
                                  else {
                                      var p = [],
                                          v = [],
                                          h = !1;
                                      if (
                                          (a.updateDOM && b.job++,
                                          o.forEach(function (t, r) {
                                              var o = !t.__cssVars;
                                              if (t.__cssVars)
                                                  try {
                                                      m(t.__cssVars.tree, e({}, a, { variables: w.job, onWarning: c }));
                                                      var i = d(t.__cssVars.tree);
                                                      if (a.updateDOM) {
                                                          var u = n[r],
                                                              l = E.cssVarFunc.test(u);
                                                          if ((t.getAttribute("data-cssvars") || t.setAttribute("data-cssvars", "src"), i.length && l)) {
                                                              var f = t.getAttribute("data-cssvars-group") || ++b.group,
                                                                  y = i.replace(/\s/g, ""),
                                                                  g = a.rootElement.querySelector('[data-cssvars="out"][data-cssvars-group="'.concat(f, '"]')) || document.createElement("style");
                                                              (h = h || E.cssKeyframes.test(i)),
                                                                  a.preserveStatic && (t.sheet.disabled = !0),
                                                                  g.hasAttribute("data-cssvars") || g.setAttribute("data-cssvars", "out"),
                                                                  y === t.textContent.replace(/\s/g, "")
                                                                      ? ((o = !0), g && g.parentNode && (t.removeAttribute("data-cssvars-group"), g.parentNode.removeChild(g)))
                                                                      : y !== g.textContent.replace(/\s/g, "") &&
                                                                        ([t, g].forEach(function (e) {
                                                                            e.setAttribute("data-cssvars-job", b.job), e.setAttribute("data-cssvars-group", f);
                                                                        }),
                                                                        (g.textContent = i),
                                                                        p.push(i),
                                                                        v.push(g),
                                                                        g.parentNode || t.parentNode.insertBefore(g, t.nextSibling));
                                                          }
                                                      } else t.textContent.replace(/\s/g, "") !== i && p.push(i);
                                                  } catch (e) {
                                                      s(e.message, t);
                                                  }
                                              o && t.setAttribute("data-cssvars", "skip"), t.hasAttribute("data-cssvars-job") || t.setAttribute("data-cssvars-job", b.job);
                                          }),
                                          (A = a.rootElement.querySelectorAll('[data-cssvars]:not([data-cssvars="out"])').length),
                                          a.shadowDOM)
                                      )
                                          for (var y, g = [a.rootElement].concat(t(a.rootElement.querySelectorAll("*"))), S = 0; (y = g[S]); ++S)
                                              if (y.shadowRoot && y.shadowRoot.querySelector("style")) {
                                                  var x = e({}, a, { rootElement: y.shadowRoot });
                                                  k(x);
                                              }
                                      a.updateDOM && h && T(a.rootElement), (C = !1), a.onComplete(p.join(""), v, JSON.parse(JSON.stringify(w.job)), N() - a.__benchmark), i(v.length);
                                  }
                              },
                          }));
            else
                document.addEventListener("DOMContentLoaded", function e(t) {
                    k(r), document.removeEventListener("DOMContentLoaded", e);
                });
        }
    }
    function _(e) {
        function t(e) {
            var t = e.hasAttribute("disabled"),
                r = (e.sheet || {}).disabled;
            return t || r;
        }
        function r(e) {
            return "LINK" === e.tagName && -1 !== (e.getAttribute("rel") || "").indexOf("stylesheet") && !t(e);
        }
        function n(e) {
            return Array.apply(null, e).some(function (e) {
                var n = 1 === e.nodeType && e.hasAttribute("data-cssvars"),
                    o =
                        (function (e) {
                            return "STYLE" === e.tagName && !t(e);
                        })(e) && E.cssVars.test(e.textContent);
                return !n && (r(e) || o);
            });
        }
        window.MutationObserver &&
            (x && (x.disconnect(), (x = null)),
            (x = new MutationObserver(function (t) {
                t.some(function (t) {
                    var o,
                        a = !1;
                    return (
                        "attributes" === t.type
                            ? (a = r(t.target))
                            : "childList" === t.type &&
                              (a =
                                  n(t.addedNodes) ||
                                  ((o = t.removedNodes),
                                  Array.apply(null, o).some(function (t) {
                                      var r = 1 === t.nodeType,
                                          n = r && "out" === t.getAttribute("data-cssvars"),
                                          o = r && "src" === t.getAttribute("data-cssvars"),
                                          a = o;
                                      if (o || n) {
                                          var s = t.getAttribute("data-cssvars-group"),
                                              c = e.rootElement.querySelector('[data-cssvars-group="'.concat(s, '"]'));
                                          o && (V(e.rootElement), (w.dom = {})), c && c.parentNode.removeChild(c);
                                      }
                                      return a;
                                  }))),
                        a
                    );
                }) && k(e);
            })).observe(document.documentElement, { attributes: !0, attributeFilter: ["disabled", "href"], childList: !0, subtree: !0 }));
    }
    function M(e) {
        var t = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : 100;
        clearTimeout(O),
            (O = setTimeout(function () {
                (e.__benchmark = null), k(e);
            }, t));
    }
    function T(e) {
        var t = ["animation-name", "-moz-animation-name", "-webkit-animation-name"].filter(function (e) {
            return getComputedStyle(document.body)[e];
        })[0];
        if (t) {
            for (var r = e.getElementsByTagName("*"), n = [], o = 0, a = r.length; o < a; o++) {
                var s = r[o];
                "none" !== getComputedStyle(s)[t] && ((s.style[t] += "__CSSVARSPONYFILL-KEYFRAMES__"), n.push(s));
            }
            document.body.offsetHeight;
            for (var c = 0, i = n.length; c < i; c++) {
                var u = n[c].style;
                u[t] = u[t].replace("__CSSVARSPONYFILL-KEYFRAMES__", "");
            }
        }
    }
    function L(e, t) {
        return (
            (e.replace(E.cssComments, "").match(E.cssUrls) || []).forEach(function (r) {
                var n = r.replace(E.cssUrls, "$1"),
                    o = D(n, t);
                e = e.replace(r, r.replace(n, o));
            }),
            e
        );
    }
    function R() {
        var e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : {},
            t = /^-{2}/;
        return Object.keys(e).reduce(function (r, n) {
            return (r[t.test(n) ? n : "--".concat(n.replace(/^-+/, ""))] = e[n]), r;
        }, {});
    }
    function D(e) {
        var t = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : location.href,
            r = document.implementation.createHTMLDocument(""),
            n = r.createElement("base"),
            o = r.createElement("a");
        return r.head.appendChild(n), r.body.appendChild(o), (n.href = t), (o.href = e), o.href;
    }
    function N() {
        return y && (window.performance || {}).now ? window.performance.now() : new Date().getTime();
    }
    function V(e) {
        Array.apply(null, e.querySelectorAll('[data-cssvars="skip"],[data-cssvars="src"]')).forEach(function (e) {
            return e.setAttribute("data-cssvars", "");
        });
    }
    return (
        (k.reset = function () {
            for (var e in ((b.job = 0), (b.group = 0), (C = !1), x && (x.disconnect(), (x = null)), (A = 0), (O = null), (j = !1), w)) w[e] = {};
        }),
        k
    );
});

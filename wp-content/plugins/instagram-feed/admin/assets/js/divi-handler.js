!(function (e) {
    var t = {};

    function n(r) {
        if (t[r]) return t[r].exports;
        var o = (t[r] = {i: r, l: !1, exports: {}});
        return e[r].call(o.exports, o, o.exports, n), (o.l = !0), o.exports;
    }

    (n.m = e),
        (n.c = t),
        (n.d = function (e, t, r) {
            n.o(e, t) || Object.defineProperty(e, t, {configurable: !1, enumerable: !0, get: r});
        }),
        (n.n = function (e) {
            var t =
                e && e.__esModule
                    ? function () {
                        return e.default;
                    }
                    : function () {
                        return e;
                    };
            return n.d(t, "a", t), t;
        }),
        (n.o = function (e, t) {
            return Object.prototype.hasOwnProperty.call(e, t);
        }),
        (n.p = "/"),
        n((n.s = 0));
})([
    function (e, t, n) {
        n(1), (e.exports = n(2));
    },
    function (e, t, n) {
        "use strict";
    },
    function (e, t, n) {
        "use strict";
        Object.defineProperty(t, "__esModule", {value: !0});
        var r = n(3),
            o = n.n(r),
            i = n(4);
        o()(window).on("et_builder_api_ready", function (e, t) {
            t.registerModules(i.a);
        });
    },
    function (e, t) {
        e.exports = jQuery;
    },
    function (e, t, n) {
        "use strict";
        var r = n(5);
        t.a = [r.a];
    },
    function (e, t, n) {
        "use strict";
        var r = n(6),
            o = n.n(r),
            i = n(7),
            a = n.n(i);

        function c(e) {
            return (c =
                "function" === typeof Symbol && "symbol" === typeof Symbol.iterator
                    ? function (e) {
                        return typeof e;
                    }
                    : function (e) {
                        return e && "function" === typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e;
                    })(e);
        }

        function u(e, t) {
            for (var n = 0; n < t.length; n++) {
                var r = t[n];
                (r.enumerable = r.enumerable || !1), (r.configurable = !0), "value" in r && (r.writable = !0), Object.defineProperty(e, r.key, r);
            }
        }

        function s(e, t) {
            return !t || ("object" !== c(t) && "function" !== typeof t)
                ? (function (e) {
                    if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
                    return e;
                })(e)
                : t;
        }

        var p = (function (e) {
            function t(e) {
                var n;
                return (
                    (function (e, t) {
                        if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function");
                    })(this, t),
                        ((n = s(this, (t.__proto__ || Object.getPrototypeOf(t)).call(this, e))).state = {feed: null}),
                        n
                );
            }

            var n, i, c;
            return (
                (function (e, t) {
                    if ("function" !== typeof t && null !== t) throw new TypeError("Super expression must either be null or a function");
                    (e.prototype = Object.create(t && t.prototype, {
                        constructor: {
                            value: e,
                            enumerable: !1,
                            writable: !0,
                            configurable: !0
                        }
                    })), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : (e.__proto__ = t));
                })(t, r["Component"]),
                    (n = t),
                    (c = [
                        {
                            key: "propTypes",
                            get: function () {
                                return {feed_id: a.a.number};
                            },
                        },
                    ]),
                (i = [
                    {
                        key: "componentDidUpdate",
                        value: function (e) {
                            e.feed_id !== this.props.feed_id && this.componentDidMount();
                        },
                    },
                    {
                        key: "componentDidMount",
                        value: function () {
                            var e = this,
                                t = new FormData();
                            null !== this.props.feed_id && void 0 !== this.props.feed_id && 0 !== this.props.feed_id
                                ? (t.append("nonce", sb_divi_builder.nonce),
                                    t.append("action", "sb_instagramfeed_divi_preview"),
                                    t.append("feed_id", this.props.feed_id),
                                    fetch(sb_divi_builder.ajax_handler, {
                                        method: "POST",
                                        cache: "no-cache",
                                        credentials: "same-origin",
                                        headers: {
                                            "Content-Type": "application/x-www-form-urlencoded",
                                            "Cache-Control": "no-cache"
                                        },
                                        body: new URLSearchParams(t),
                                    })
                                        .then(function (e) {
                                            return e.json();
                                        })
                                        .then(
                                            function (t) {
                                                e.setState({feed: t.data}), window.sbi_init();
                                            },
                                            function (t) {
                                                e.setState({});
                                            }
                                        ))
                                : this.setState({feed: sb_divi_builder.feed_splash});
                        },
                    },
                    {
                        key: "render",
                        value: function () {
                            var e = this.state.feed;
                            return void 0 === this.props.feed_id || null === this.props.feed_id || 0 == this.props.feed_id
                                ? o.a.createElement("div", null, o.a.createElement("div", {dangerouslySetInnerHTML: {__html: sb_divi_builder.feed_splash}}))
                                : o.a.createElement("div", null, o.a.createElement("div", {dangerouslySetInnerHTML: {__html: e}}));
                        },
                    },
                ]) && u(n.prototype, i),
                c && u(n, c),
                    t
            );
        })();
        Object.defineProperty(p, "slug", {
            configurable: !0,
            enumerable: !0,
            writable: !0,
            value: "sb_instagram_feed"
        }), (t.a = p);
    },
    function (e, t) {
        e.exports = React;
    },
    function (e, t, n) {
        e.exports = n(8)();
    },
    function (e, t, n) {
        "use strict";
        var r = n(9);

        function o() {
        }

        function i() {
        }

        (i.resetWarningCache = o),
            (e.exports = function () {
                function e(e, t, n, o, i, a) {
                    if (a !== r) {
                        var c = new Error("Calling PropTypes validators directly is not supported by the `prop-types` package. Use PropTypes.checkPropTypes() to call them. Read more at http://fb.me/use-check-prop-types");
                        throw ((c.name = "Invariant Violation"), c);
                    }
                }

                function t() {
                    return e;
                }

                e.isRequired = e;
                var n = {
                    array: e,
                    bigint: e,
                    bool: e,
                    func: e,
                    number: e,
                    object: e,
                    string: e,
                    symbol: e,
                    any: e,
                    arrayOf: t,
                    element: e,
                    elementType: e,
                    instanceOf: t,
                    node: e,
                    objectOf: t,
                    oneOf: t,
                    oneOfType: t,
                    shape: t,
                    exact: t,
                    checkPropTypes: i,
                    resetWarningCache: o,
                };
                return (n.PropTypes = n), n;
            });
    },
    function (e, t, n) {
        "use strict";
        e.exports = "SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED";
    },
]);
jQuery("body").find(".sbi_lightbox, .sbi_lightboxOverlay").remove();

function docReady(fn) {
    // see if DOM is already available
    if (
        document.readyState === "complete" ||
        document.readyState === "interactive"
    ) {
        // call on next available tick
        setTimeout(fn, 1);
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
}

docReady(function () {
    document.body.addEventListener("click", function (event) {
        // Check if the clicked element has the specific class
        if (
            event.target &&
            (event.target.classList.contains("sbi-feed-block-link") || event.target.classList.contains("sbi-feed-block-cta-btn"))
        ) {
            const href = event.target.getAttribute("href");
            window.open(href, "_blank");
        }
    });
});
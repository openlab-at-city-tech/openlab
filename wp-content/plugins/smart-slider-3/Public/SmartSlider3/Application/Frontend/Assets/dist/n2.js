(function(w){var window = w;

window._N2 = window._N2 || {
    _r: [],
    _d: [],
    r: function () {
        this._r.push(arguments);
    },
    d: function () {
        this._d.push(arguments);
    }
};

var document = w.document,
    html = document.documentElement,
    body, // Body is not available at this point
    undefined,
    setTimeout = w.setTimeout,
    clearTimeout = w.clearTimeout,
    _N2 = window._N2,
    _requestAnimationFrame = w.requestAnimationFrame,
    /**
     *
     * @param {string} tagName
     * @param {string|[]} [classes]
     * @param {{}} [attributes]
     * @param {{}} [data]
     * @return {HTMLElement}
     */
    _CreateElement = function (tagName, classes = null, attributes = null, data = null) {

        const element = document.createElement(tagName);

        if (classes) {
            if (typeof classes === 'string') {
                _NodeAddClass(element, classes);
            } else {
                _NodeAddClasses(element, classes);
            }
        }

        if (attributes) {
            _NodeSetAttributes(element, attributes);
        }

        if (data) {
            _NodeSetDatas(element, data);
        }

        return element;
    },
    /**
     *
     * @param {HTMLElement} appendTo
     * @param {string} tagName
     * @param {string|[]} [classes]
     * @param {{}} [attributes]
     * @param {{}} [data]
     * @return {HTMLElement}
     */
    _CreateElementIn = function (appendTo, tagName, classes, attributes, data) {
        const element = _CreateElement(tagName, classes, attributes, data);
        if (appendTo) {
            appendTo.appendChild(element);
        }
        return element;
    },

    /**
     *
     * @param {string} tagName
     * @param {string} text
     * @param {string|[]} [classes]
     * @param {{}} [attributes]
     * @param {{}} [data]
     * @return {HTMLDivElement}
     */
    _CreateElementText = function (tagName, text, classes, attributes, data) {
        const element = _CreateElement(tagName, classes, attributes, data);
        element.innerHTML = text;
        return element;
    },

    /**
     *
     * @param {HTMLElement} appendTo
     * @param {string} tagName
     * @param {string} text
     * @param {string|[]} [classes]
     * @param {{}} [attributes]
     * @param {{}} [data]
     * @return {HTMLDivElement}
     */
    _CreateElementTextIn = function (appendTo, tagName, text, classes, attributes, data) {
        const element = _CreateElementIn(appendTo, tagName, classes, attributes, data);
        element.innerHTML = text;
        return element;
    },

    /**
     *
     * @param {string|[]} [classes]
     * @param {{}} [attributes]
     * @param {{}} [data]
     * @return {HTMLDivElement}
     */
    _CreateElementDiv = function (classes, attributes, data) {
        return _CreateElement('div', classes, attributes, data);
    },

    /**
     *
     * @param {HTMLElement} appendTo
     * @param {string|[]} [classes]
     * @param {{}} [attributes]
     * @param {{}} [data]
     * @return {HTMLDivElement}
     */
    _CreateElementDivIn = function (appendTo, classes, attributes, data) {
        return _CreateElementIn(appendTo, 'div', classes, attributes, data);
    },
    /**
     *
     * @param {string} text
     * @param {string|[]} [classes]
     * @param {{}} [attributes]
     * @param {{}} [data]
     * @return {HTMLDivElement}
     */
    _CreateElementDivText = function (text, classes, attributes, data) {
        return _CreateElementText('div', text, classes, attributes, data);
    },

    /**
     *
     * @param {HTMLElement} appendTo
     * @param {string} text
     * @param {string|[]} [classes]
     * @param {{}} [attributes]
     * @param {{}} [data]
     * @return {HTMLDivElement}
     */
    _CreateElementDivTextIn = function (appendTo, text, classes, attributes, data) {
        return _CreateElementTextIn(appendTo, 'div', text, classes, attributes, data);
    },
    /**
     *
     * @param {string} label
     * @param {string} href
     * @param {string|[]} [classes]
     * @param {{}} [attributes]
     * @param {{}} [data]
     * @return {HTMLAnchorElement}
     */
    _CreateElementLink = function (label, href, classes, attributes, data) {
        const element = _CreateElement('a', classes, attributes, data);
        _NodeSetAttribute(element, 'href', href);
        element.innerHTML = label;
        return element;
    },

    /**
     *
     * @param {HTMLElement} appendTo
     * @param {string} label
     * @param {string} href
     * @param {string|[]} [classes]
     * @param {{}} [attributes]
     * @param {{}} [data]
     * @return {HTMLAnchorElement}
     */
    _CreateElementLinkIn = function (appendTo, label, href, classes, attributes, data) {
        const element = _CreateElementIn(appendTo, 'a', classes, attributes, data);
        _NodeSetAttribute(element, 'href', href);
        element.innerHTML = label;
        return element;
    },

    _Assign = Object.assign,
    _AssignRecursive = function (target, varArgs) {
        var to = Object(target);

        for (var index = 1; index < arguments.length; index++) {
            var nextSource = arguments[index];

            if (nextSource !== null && nextSource !== undefined) {
                for (var nextKey in nextSource) {
                    if (nextSource[nextKey] !== null && Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
                        if (typeof nextSource[nextKey] === 'object') {

                            if (nextSource[nextKey].constructor === Object || Array.isArray(nextSource[nextKey])) {
                                // Merge only plain objects or arrays
                                if (Array.isArray(nextSource[nextKey])) {
                                    to[nextKey] = []; // array value always overrides previous value
                                } else if (typeof to[nextKey] !== 'object' || Array.isArray(to[nextKey])) {
                                    to[nextKey] = {}; // always override if target is not object or target is an array.
                                }

                                to[nextKey] = _AssignRecursive(to[nextKey], nextSource[nextKey]);
                            } else {

                                to[nextKey] = nextSource[nextKey];
                            }
                        } else {
                            to[nextKey] = nextSource[nextKey];
                        }
                    }
                }
            }
        }
        return to;
    },
    _NodeGetAttribute = function (node, attribute) {
        return node.getAttribute(attribute);
    },
    _NodeSetAttribute = function (node, attribute, value) {
        node.setAttribute(attribute, value);
    },
    _NodeSetAttributes = function (node, attributes) {
        for (var k in attributes) {
            _NodeSetAttribute(node, k, attributes[k]);
        }
    },
    _NodeRemoveAttribute = function (node, name) {
        node.removeAttribute(name);
    },
    _NodeListSetAttribute = function (nodeList, attribute, value) {
        nodeList.forEach(function (node) {
            _NodeSetAttribute(node, attribute, value);
        });
    },
    _NodeListSetAttributes = function (nodeList, attribute) {
        nodeList.forEach(function (node) {
            _NodeSetAttributes(node, attribute);
        });
    },
    _NodeListAddClass = function (nodeList, className) {
        nodeList.forEach(function (node) {
            _NodeAddClass(node, className);
        });
    },
    _NodeListRemoveClass = function (nodeList, className) {
        nodeList.forEach(function (node) {
            _NodeRemoveClass(node, className);
        });
    },
    _NodeGetData = function (node, property) {
        return node.dataset[property];
    },
    _NodeSetData = function (node, property, value) {
        node.dataset[property] = value;
    },
    _NodeSetDatas = function (node, datas) {
        for (let k in datas) {
            _NodeSetData(node, k, datas[k]);
        }
    },
    _NodeRemoveData = function (node, property) {
        delete node.dataset[property];
    },
    _NodeGetStyle = function (node, property) {
        return window.getComputedStyle(node).getPropertyValue(property);
    },
    _NodeSetStyle = function (node, property, value) {
        node.style.setProperty(property, value);
    },
    _NodeSetStyles = function (node, styles) {
        for (var k in styles) {
            _NodeSetStyle(node, k, styles[k]);
        }
    },
    _NodeRemoveStyle = function (node, property) {
        node.style.removeProperty(property);
    },
    _NodeRemoveStyles = function (node, styles) {
        styles.forEach(function (style) {
            _NodeRemoveStyle(node, style);
        });
    },
    _NodeListSetStyle = function (nodeList, property, value) {
        nodeList.forEach(function (node) {
            _NodeSetStyle(node, property, value);
        });
    },
    _NodeListSetStyles = function (nodeList, styles) {
        nodeList.forEach(function (node) {
            _NodeSetStyles(node, styles);
        });
    },
    _NodeListRemoveStyle = function (nodeList, property) {
        nodeList.forEach(function (node) {
            _NodeRemoveStyle(node, property);
        });
    },
    _NodeRemove = function (node) {
        if (node && node.parentNode) {
            node.parentNode.removeChild(node);
        }
    },
    _NodeListRemove = function (nodeList) {
        nodeList.forEach(function (node) {
            _NodeRemove(node);
        });
    },
    _NodeAddClass = function (node, className) {
        node.classList.add(className);
    },
    _NodeAddClasses = function (node, classNames) {
        classNames.forEach(function (className) {
            node.classList.add(className);
        });
    },
    _NodeRemoveClass = function (node, className) {
        node.classList.remove(className);
    },
    _NodeRemoveClasses = function (node, classNames) {

        classNames.forEach(function (className) {
            node.classList.remove(className);
        });
    },
    _NodeToggleClass = function (node, className, state) {
        if (state) {
            _NodeAddClass(node, className);
        } else {
            _NodeRemoveClass(node, className);
        }
    },
    _NodeAppendTo = function (node, target) {
        target.appendChild(node);
    },
    _NodePrependTo = function (node, target) {

        if (target.childNodes.length) {
            _NodeInsertBefore(node, target.childNodes[0]);
        } else {
            target.appendChild(node);
        }
    },
    _NodeInsertAfter = function (node, target) {

        if (target.nextSibling) {
            _NodeInsertBefore(node, /** @var {Element} */ target.nextSibling);
        } else {
            target.parentNode.appendChild(node);
        }
    },
    _NodeInsertBefore = function (node, target) {

        target.parentNode.insertBefore(node, target);
    },
    _dispatchEvent = function (target, event) {
        return target.dispatchEvent(event);
    },
    _dispatchEventSimple = function (target, eventName, options) {
        options = _Assign({
            bubbles: true,
            cancelable: true
        }, options);
        return _dispatchEvent(target, new Event(eventName, options));
    },
    _dispatchEventSimpleNoBubble = function (target, eventName) {
        return _dispatchEvent(target, new Event(eventName, {
            bubbles: false,
            cancelable: false
        }));
    },
    _dispatchCustomEventNoBubble = function (target, eventName, detail) {
        return _dispatchEvent(target, new CustomEvent(eventName, {
            bubbles: false,
            cancelable: false,
            detail: detail
        }));
    },
    _removeEventListener = function (target, type, listener, options) {
        target.removeEventListener(type, listener, options);
    },
    _removeEventListeners = function (eventListeners) {
        eventListeners.forEach(function (removeCallback) {
            removeCallback()
        });
        eventListeners.splice(0, eventListeners.length);
    },
    _addEventListenerWithRemover = function (target, type, listener, options) {
        options = options || {};
        target.addEventListener(type, listener, options);

        return target.removeEventListener.bind(target, type, listener, options);
    },
    _addEventListener = function (target, type, listener, options) {
        options = options || {};
        target.addEventListener(type, listener, options);
    },
    _addEventListenerOnce = function (target, type, listener) {
        target.addEventListener(type, listener, {
            once: true
        });
    },
    _dispatchPrivateEvent = function (target, event, data) {
        if (target.ssEvent && target.ssEvent[event]) {
            var listeners = target.ssEvent[event];
            listeners.forEach(function (listener) {
                listener(data);
            })
        }
    },
    _addPrivateEventListener = function (target, type, listener) {
        if (!target.ssEvent) {
            target.ssEvent = {};
        }
        if (!target.ssEvent[type]) {
            target.ssEvent[type] = [];
        }
        target.ssEvent[type].push(listener);
    },
    _addPrivateEventListenerWithRemover = function (target, type, listener) {
        _addPrivateEventListener(target, type, listener);

        return _removePrivateEventListener.bind(target, type, listener);
    },
    _removePrivateEventListener = function (type, listener) {
        if (this.ssEvent && this.ssEvent[type]) {
            var listeners = this.ssEvent[type];
            for (var i = listeners.length - 1; i >= 0; i--) {
                if (listeners[i] === listener) {
                    listeners.splice(i, 1);
                }
            }
        }
    },
    _getScrollTop = function () {
        return html.scrollTop;
    },
    _setScrollTop = function (top) {
        html.scrollTop = top;
    },
    _getScrollLeft = function () {
        return html.scrollLeft;
    },
    _getWidth = function (element) {
        return element.getBoundingClientRect().width;
    },
    _getHeight = function (element) {
        return element.getBoundingClientRect().height;
    },
    _getOffsetTop = function (element) {
        return document.scrollingElement.scrollTop + element.getBoundingClientRect().top;
    },
    _ready = function (fn) {
        if (document.readyState === "complete" || document.readyState === "interactive") {
            fn();
        } else {
            if (Document && Document.prototype && Document.prototype.addEventListener && Document.prototype.addEventListener !== document.addEventListener) {
                const fn2 = () => {
                    fn();

                    fn = () => {
                    };
                };

                document.addEventListener("DOMContentLoaded", fn2);

                document.addEventListener("readystatechange", () => {
                    if (document.readyState === "complete" || document.readyState === "interactive") {
                        fn2();
                    }
                });


                /**
                 * WP Rocket overwrites the document.addEventListener which prevents sliders to show.
                 * In that case we use the prototype method to use the native code.
                 */
                Document.prototype.addEventListener.call(document, "DOMContentLoaded", fn2);
            } else {
                document.addEventListener("DOMContentLoaded", fn);
            }
        }
    },
    _animateScroll = function (element, targetTop, duration, doneCallback) {
        if (Math.abs(element.scrollTop - targetTop) < 1) {
            /**
             * Android Chrome has a jumping bug if we change too small scrollTop
             * @see SSDEV-2524
             */
            if (doneCallback) {
                doneCallback();
            }
        } else {
            duration = Math.max(300, duration || 300);

            var currentScrollTop = element.scrollTop,
                diff = targetTop - currentScrollTop,
                startTimestamp = performance.now(),
                _raf = function (timestamp) {
                    var progress = Math.min(1, (timestamp - startTimestamp) / duration);
                    if (progress < 0.5) {
                        progress = 2 * progress * progress;
                    } else {
                        progress = -1 + (4 - 2 * progress) * progress;
                    }

                    element.scrollTop = currentScrollTop + progress * diff;
                    if (progress < 1) {
                        requestAnimationFrame(_raf);
                    } else if (doneCallback) {
                        doneCallback();
                    }
                };
            _raf(startTimestamp);
        }
    },
    _CreateSVGElement = function (tagName, attributes, parentNode) {
        var element = document.createElementNS("http://www.w3.org/2000/svg", tagName);
        if (attributes) {
            _SVGSetAttributes(element, attributes);

            if (parentNode) {
                parentNode.appendChild(element);
            }
        }
        return element;
    },
    _SVGSetAttribute = function (element, name, value) {
        element.setAttributeNS(null, name, value);
    },
    _SVGSetAttributes = function (element, attributes) {
        for (var k in attributes) {
            _SVGSetAttribute(element, k, attributes[k]);
        }
    },
    _ucFirst = function (s) {
        return s.charAt(0).toUpperCase() + s.slice(1);
    },
    _requestIdleCallback = navigator.userAgent.indexOf("+http://www.google.com/bot.html") > -1 ? function (cb) {
        cb();
    } : window.requestIdleCallback || function (cb) {
        return setTimeout(cb, 1);
    },
    _cancelIdleCallback = window.cancelIdleCallback || function (id) {
        clearTimeout(id);
    },
    _strip_tags = function (input, allowed) {
        allowed = (((allowed || '') + '')
            .toLowerCase()
            .match(/<[a-z][a-z0-9]*>/g) || [])
            .join('') // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
        var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
            commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi
        return (input + '').replace(commentsAndPhpTags, '')
            .replace(tags, function ($0, $1) {
                return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : ''
            })
    },
    _filter_allowed_html = function (input, extraTags = '') {
        return _filter_js_events(_strip_tags(input, '<a><span><sub><sup><em><i><var><cite><b><strong><small><bdo><br><img><picture><source><u><del><bdi><ins>' + extraTags));
    },
    _filter_js_events = function (input) {
        var blacklist = [
                'onclick',
                'onfocus',
                'ondrag',
                'onmouse',
                'onwheel',
                'onscroll',
                'ontouch',
                'onload',
                'onerror'
            ],
            blacklistRegExp = new RegExp(blacklist.join("|"), 'gi');
        return input.replace(blacklistRegExp, 'not-allowed');
    }

_ready(function () {
    body = document.body;
});(function () {
    "use strict";
    var a = {},
        resolves = {};

    window.n2Slow = (navigator.userAgent.indexOf("Chrome-Lighthouse") > -1 && navigator.userAgent.indexOf("Android") > -1);
    /**
     * Scheduler splits the dependency loader into smaller dynamic chunks to prevent total blocking time.
     */
    var scheduleList = [],
        scheduler = false;

    function schedulerStart() {
        scheduler = true;

        _requestIdleCallback(schedulerProcess, {
            timeout: 2000
        });
    }

    function schedulerProcess() {

        var startTime = performance.now(),
            _scheduleList = scheduleList;

        scheduleList = []

        for (var i = _scheduleList.length - 1; i >= 0; i--) {
            _scheduleList.pop().call();

            if (performance.now() - startTime > 7) break;
        }

        if (!window.n2Slow && _scheduleList.length) {
            window.n2Slow = true;
        }

        _scheduleList.unshift.apply(_scheduleList, scheduleList);
        scheduleList = _scheduleList;

        if (scheduleList.length) {
            /**
             * Continue at the next idle period
             */
            _requestIdleCallback(schedulerProcess, {
                timeout: 2000
            });
        } else {
            /**
             * Scheduler finished
             */
            scheduler = false;
        }
    }

    function schedule(callback) {
        scheduleList.unshift(callback);

        if (!scheduler) {
            schedulerStart();
        }
    }

    var startDate = new Date();
    // Poll to see if jQuery is ready
    var waitForJQuery = function () {
        if (window.jQuery) {
            var $ = window.jQuery;
            _N2.d('$', function () {
                return $;
            });
        } else {
            setTimeout(waitForJQuery, 20);

            if ((new Date).getTime() - startDate.getTime() > 1000) {
                var script = _CreateElement('script');
                // If there is no jQuery on the page in 1 second, we will load one from CDN
                script.src = _N2._jQueryFallback;
                //document.getElementsByTagName('head')[0].appendChild(script);
            }
        }
    };

    function createPromise(name) {
        if (name && a[name] === undefined) {
            a[name] = new Promise(function (resolve) {
                resolves[name] = resolve;
            });

            if (name === '$') {
                /**
                 * Well, we still need jQuery
                 */
                waitForJQuery();
            }

            return true;
        }

        return false;
    }

    function _N2D(name, dependencies, fn) {
        /**
         *
         * @type {Promise[]}
         */
        var dependencyPromises = [];

        if (createPromise(name) || resolves[name]) {

            if (typeof dependencies === 'function') {
                fn = dependencies;
                dependencies = [];
            } else {
                if (typeof dependencies === 'string') {
                    dependencies = [dependencies];
                }
            }

            dependencies = dependencies || [];

            if (dependencies.length) {
                for (var i = 0; i < dependencies.length; i++) {
                    createPromise(dependencies[i]);
                    dependencyPromises.push(a[dependencies[i]]);
                }
            }

            Promise.all(dependencyPromises)
                .then((function (resolve) {
                    if (typeof fn === 'function') {
                        _N2[name] = fn.call(_N2);
                    } else {
                        _N2[name] = true;
                    }

                    resolve();
                }).bind(this, resolves[name]));

            delete resolves[name];
        }
    }

    function _N2R(dependencies, fn) {
        var dependencyPromises = [];

        if (fn === undefined) {
            fn = dependencies;
            dependencies = [];
        } else {
            if (typeof dependencies === 'string') {
                dependencies = [dependencies];
            }
        }

        dependencies = dependencies || [];

        if (dependencies) {
            for (var i = 0; i < dependencies.length; i++) {
                createPromise(dependencies[i]);
                dependencyPromises.push(a[dependencies[i]]);
            }
        }

        Promise.all(dependencyPromises)
            .then(function () {
                fn.call(_N2);
            });
    }

    if (window.N2DISABLESCHEDULER) {
        _N2.d = _N2D;
        _N2.r = _N2R;
    } else {
        _N2.d = function (name, dependencies, fn) {
            schedule(_N2D.bind(this, name, dependencies, fn));
        };

        _N2.r = function (dependencies, fn) {
            schedule(_N2R.bind(this, dependencies, fn));
        };
    }
    for (var i = 0; i < this._N2._d.length; i++) {
        _N2.d.apply(this, this._N2._d[i]);
    }

    for (var j = 0; j < this._N2._r.length; j++) {
        _N2.r.apply(this, this._N2._r[j]);
    }
}).call(window);window.NextendThrottle = function (func, wait) {
    wait || (wait = 250);
    var last,
        deferTimer;
    return function () {
        var context = this,
            now = +new Date,
            args = arguments;
        if (last && now < last + wait) {
            // hold on to it
            clearTimeout(deferTimer);
            deferTimer = setTimeout(function () {
                last = now;
                func.apply(context, args);
            }, wait);
        } else {
            last = now;
            func.apply(context, args);
        }
    };
};window.NextendDeBounce = function (func, wait, immediate) {
    var timeout;
    return function () {
        var context = this, args = arguments;
        var later = function () {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
};_N2.r('nextend-frontend', function () {
    _ready(function () {
        _N2.d('documentReady');
    });

    if (document.readyState === 'complete') {
        _N2.d('windowLoad');
    } else {

        /**
         * We are trying to manage a bug in Safari: SSDEV-3335
         * @see https://bugs.webkit.org/show_bug.cgi?id=231156
         */
        let interval;
        const ua = navigator.userAgent;
        if (ua.indexOf('Safari') > 0 && ua.indexOf('Chrome') === -1) {
            interval = setInterval(function () {
                if (document.readyState === 'interactive' || document.readyState === 'complete') {
                    _N2.d('windowLoad');
                    clearInterval(interval);
                }
            }, 2000);
        }

        _addEventListenerOnce(window, 'load', function () {
            _N2.d('windowLoad');
            clearInterval(interval);
        });
    }
});/**
 * ResizeObserver Polyfill
 */
(function (factory) {
    if (!window.ResizeObserver) {
        window.ResizeObserver = factory();
    }
}(
    (function () {
        'use strict';

        /**
         * A collection of shims that provide minimal functionality of the ES6 collections.
         *
         * These implementations are not meant to be used outside of the ResizeObserver
         * modules as they cover only a limited range of use cases.
         */
        /* eslint-disable require-jsdoc, valid-jsdoc */
        var MapShim = (function () {
            if (typeof Map !== 'undefined') {
                return Map;
            }

            /**
             * Returns index in provided array that matches the specified key.
             *
             * @param {Array<Array>} arr
             * @param {*} key
             * @returns {number}
             */
            function getIndex(arr, key) {
                var result = -1;
                arr.some(function (entry, index) {
                    if (entry[0] === key) {
                        result = index;
                        return true;
                    }
                    return false;
                });
                return result;
            }

            return /** @class */ (function () {
                function class_1() {
                    this.__entries__ = [];
                }

                Object.defineProperty(class_1.prototype, "size", {
                    /**
                     * @returns {boolean}
                     */
                    get: function () {
                        return this.__entries__.length;
                    },
                    enumerable: true,
                    configurable: true
                });
                /**
                 * @param {*} key
                 * @returns {*}
                 */
                class_1.prototype.get = function (key) {
                    var index = getIndex(this.__entries__, key);
                    var entry = this.__entries__[index];
                    return entry && entry[1];
                };
                /**
                 * @param {*} key
                 * @param {*} value
                 * @returns {void}
                 */
                class_1.prototype.set = function (key, value) {
                    var index = getIndex(this.__entries__, key);
                    if (~index) {
                        this.__entries__[index][1] = value;
                    } else {
                        this.__entries__.push([key, value]);
                    }
                };
                /**
                 * @param {*} key
                 * @returns {void}
                 */
                class_1.prototype.delete = function (key) {
                    var entries = this.__entries__;
                    var index = getIndex(entries, key);
                    if (~index) {
                        entries.splice(index, 1);
                    }
                };
                /**
                 * @param {*} key
                 * @returns {void}
                 */
                class_1.prototype.has = function (key) {
                    return !!~getIndex(this.__entries__, key);
                };
                /**
                 * @returns {void}
                 */
                class_1.prototype.clear = function () {
                    this.__entries__.splice(0);
                };
                /**
                 * @param {Function} callback
                 * @param {*} [ctx=null]
                 * @returns {void}
                 */
                class_1.prototype.forEach = function (callback, ctx) {
                    if (ctx === void 0) {
                        ctx = null;
                    }
                    for (var _i = 0, _a = this.__entries__; _i < _a.length; _i++) {
                        var entry = _a[_i];
                        callback.call(ctx, entry[1], entry[0]);
                    }
                };
                return class_1;
            }());
        })();

        /**
         * Detects whether window and document objects are available in current environment.
         */
        var isBrowser = typeof window !== 'undefined' && typeof document !== 'undefined' && window.document === document;

        // Returns global object of a current environment.
        var global$1 = (function () {
            if (typeof global !== 'undefined' && global.Math === Math) {
                return global;
            }
            if (typeof self !== 'undefined' && self.Math === Math) {
                return self;
            }
            if (typeof window !== 'undefined' && window.Math === Math) {
                return window;
            }
            // eslint-disable-next-line no-new-func
            return Function('return this')();
        })();

        /**
         * A shim for the requestAnimationFrame which falls back to the setTimeout if
         * first one is not supported.
         *
         * @returns {number} Requests' identifier.
         */
        var requestAnimationFrame$1 = (function () {
            if (typeof requestAnimationFrame === 'function') {
                // It's required to use a bounded function because IE sometimes throws
                // an "Invalid calling object" error if rAF is invoked without the global
                // object on the left hand side.
                return requestAnimationFrame.bind(global$1);
            }
            return function (callback) {
                return setTimeout(function () {
                    return callback(Date.now());
                }, 1000 / 60);
            };
        })();

        // Defines minimum timeout before adding a trailing call.
        var trailingTimeout = 2;

        /**
         * Creates a wrapper function which ensures that provided callback will be
         * invoked only once during the specified delay period.
         *
         * @param {Function} callback - Function to be invoked after the delay period.
         * @param {number} delay - Delay after which to invoke callback.
         * @returns {Function}
         */
        function throttle(callback, delay) {
            var leadingCall = false, trailingCall = false, lastCallTime = 0;

            /**
             * Invokes the original callback function and schedules new invocation if
             * the "proxy" was called during current request.
             *
             * @returns {void}
             */
            function resolvePending() {
                if (leadingCall) {
                    leadingCall = false;
                    callback();
                }
                if (trailingCall) {
                    proxy();
                }
            }

            /**
             * Callback invoked after the specified delay. It will further postpone
             * invocation of the original function delegating it to the
             * requestAnimationFrame.
             *
             * @returns {void}
             */
            function timeoutCallback() {
                requestAnimationFrame$1(resolvePending);
            }

            /**
             * Schedules invocation of the original function.
             *
             * @returns {void}
             */
            function proxy() {
                var timeStamp = Date.now();
                if (leadingCall) {
                    // Reject immediately following calls.
                    if (timeStamp - lastCallTime < trailingTimeout) {
                        return;
                    }
                    // Schedule new call to be in invoked when the pending one is resolved.
                    // This is important for "transitions" which never actually start
                    // immediately so there is a chance that we might miss one if change
                    // happens amids the pending invocation.
                    trailingCall = true;
                } else {
                    leadingCall = true;
                    trailingCall = false;
                    setTimeout(timeoutCallback, delay);
                }
                lastCallTime = timeStamp;
            }

            return proxy;
        }

        // Minimum delay before invoking the update of observers.
        var REFRESH_DELAY = 20;
        // A list of substrings of CSS properties used to find transition events that
        // might affect dimensions of observed elements.
        var transitionKeys = ['top', 'right', 'bottom', 'left', 'width', 'height', 'size', 'weight'];
        // Check if MutationObserver is available.
        var mutationObserverSupported = typeof MutationObserver !== 'undefined';
        /**
         * Singleton controller class which handles updates of ResizeObserver instances.
         */
        var ResizeObserverController = /** @class */ (function () {
            /**
             * Creates a new instance of ResizeObserverController.
             *
             * @private
             */
            function ResizeObserverController() {
                /**
                 * Indicates whether DOM listeners have been added.
                 *
                 * @private {boolean}
                 */
                this.connected_ = false;
                /**
                 * Tells that controller has subscribed for Mutation Events.
                 *
                 * @private {boolean}
                 */
                this.mutationEventsAdded_ = false;
                /**
                 * Keeps reference to the instance of MutationObserver.
                 *
                 * @private {MutationObserver}
                 */
                this.mutationsObserver_ = null;
                /**
                 * A list of connected observers.
                 *
                 * @private {Array<ResizeObserverSPI>}
                 */
                this.observers_ = [];
                this.onTransitionEnd_ = this.onTransitionEnd_.bind(this);
                this.refresh = throttle(this.refresh.bind(this), REFRESH_DELAY);
            }

            /**
             * Adds observer to observers list.
             *
             * @param {ResizeObserverSPI} observer - Observer to be added.
             * @returns {void}
             */
            ResizeObserverController.prototype.addObserver = function (observer) {
                if (!~this.observers_.indexOf(observer)) {
                    this.observers_.push(observer);
                }
                // Add listeners if they haven't been added yet.
                if (!this.connected_) {
                    this.connect_();
                }
            };
            /**
             * Removes observer from observers list.
             *
             * @param {ResizeObserverSPI} observer - Observer to be removed.
             * @returns {void}
             */
            ResizeObserverController.prototype.removeObserver = function (observer) {
                var observers = this.observers_;
                var index = observers.indexOf(observer);
                // Remove observer if it's present in registry.
                if (~index) {
                    observers.splice(index, 1);
                }
                // Remove listeners if controller has no connected observers.
                if (!observers.length && this.connected_) {
                    this.disconnect_();
                }
            };
            /**
             * Invokes the update of observers. It will continue running updates insofar
             * it detects changes.
             *
             * @returns {void}
             */
            ResizeObserverController.prototype.refresh = function () {
                var changesDetected = this.updateObservers_();
                // Continue running updates if changes have been detected as there might
                // be future ones caused by CSS transitions.
                if (changesDetected) {
                    this.refresh();
                }
            };
            /**
             * Updates every observer from observers list and notifies them of queued
             * entries.
             *
             * @private
             * @returns {boolean} Returns "true" if any observer has detected changes in
             *      dimensions of it's elements.
             */
            ResizeObserverController.prototype.updateObservers_ = function () {
                // Collect observers that have active observations.
                var activeObservers = this.observers_.filter(function (observer) {
                    return observer.gatherActive(), observer.hasActive();
                });
                // Deliver notifications in a separate cycle in order to avoid any
                // collisions between observers, e.g. when multiple instances of
                // ResizeObserver are tracking the same element and the callback of one
                // of them changes content dimensions of the observed target. Sometimes
                // this may result in notifications being blocked for the rest of observers.
                activeObservers.forEach(function (observer) {
                    return observer.broadcastActive();
                });
                return activeObservers.length > 0;
            };
            /**
             * Initializes DOM listeners.
             *
             * @private
             * @returns {void}
             */
            ResizeObserverController.prototype.connect_ = function () {
                // Do nothing if running in a non-browser environment or if listeners
                // have been already added.
                if (!isBrowser || this.connected_) {
                    return;
                }
                // Subscription to the "Transitionend" event is used as a workaround for
                // delayed transitions. This way it's possible to capture at least the
                // final state of an element.
                document.addEventListener('transitionend', this.onTransitionEnd_);
                window.addEventListener('resize', this.refresh);
                if (mutationObserverSupported) {
                    this.mutationsObserver_ = new MutationObserver(this.refresh);
                    this.mutationsObserver_.observe(document, {
                        attributes: true,
                        childList: true,
                        characterData: true,
                        subtree: true
                    });
                } else {
                    document.addEventListener('DOMSubtreeModified', this.refresh);
                    this.mutationEventsAdded_ = true;
                }
                this.connected_ = true;
            };
            /**
             * Removes DOM listeners.
             *
             * @private
             * @returns {void}
             */
            ResizeObserverController.prototype.disconnect_ = function () {
                // Do nothing if running in a non-browser environment or if listeners
                // have been already removed.
                if (!isBrowser || !this.connected_) {
                    return;
                }
                document.removeEventListener('transitionend', this.onTransitionEnd_);
                window.removeEventListener('resize', this.refresh);
                if (this.mutationsObserver_) {
                    this.mutationsObserver_.disconnect();
                }
                if (this.mutationEventsAdded_) {
                    document.removeEventListener('DOMSubtreeModified', this.refresh);
                }
                this.mutationsObserver_ = null;
                this.mutationEventsAdded_ = false;
                this.connected_ = false;
            };
            /**
             * "Transitionend" event handler.
             *
             * @private
             * @param {TransitionEvent} event
             * @returns {void}
             */
            ResizeObserverController.prototype.onTransitionEnd_ = function (_a) {
                var _b = _a.propertyName, propertyName = _b === void 0 ? '' : _b;
                // Detect whether transition may affect dimensions of an element.
                var isReflowProperty = transitionKeys.some(function (key) {
                    return !!~propertyName.indexOf(key);
                });
                if (isReflowProperty) {
                    this.refresh();
                }
            };
            /**
             * Returns instance of the ResizeObserverController.
             *
             * @returns {ResizeObserverController}
             */
            ResizeObserverController.getInstance = function () {
                if (!this.instance_) {
                    this.instance_ = new ResizeObserverController();
                }
                return this.instance_;
            };
            /**
             * Holds reference to the controller's instance.
             *
             * @private {ResizeObserverController}
             */
            ResizeObserverController.instance_ = null;
            return ResizeObserverController;
        }());

        /**
         * Defines non-writable/enumerable properties of the provided target object.
         *
         * @param {Object} target - Object for which to define properties.
         * @param {Object} props - Properties to be defined.
         * @returns {Object} Target object.
         */
        var defineConfigurable = (function (target, props) {
            for (var _i = 0, _a = Object.keys(props); _i < _a.length; _i++) {
                var key = _a[_i];
                Object.defineProperty(target, key, {
                    value: props[key],
                    enumerable: false,
                    writable: false,
                    configurable: true
                });
            }
            return target;
        });

        /**
         * Returns the global object associated with provided element.
         *
         * @param {Object} target
         * @returns {Object}
         */
        var getWindowOf = (function (target) {
            // Assume that the element is an instance of Node, which means that it
            // has the "ownerDocument" property from which we can retrieve a
            // corresponding global object.
            var ownerGlobal = target && target.ownerDocument && target.ownerDocument.defaultView;
            // Return the local global object if it's not possible extract one from
            // provided element.
            return ownerGlobal || global$1;
        });

        // Placeholder of an empty content rectangle.
        var emptyRect = createRectInit(0, 0, 0, 0);

        /**
         * Converts provided string to a number.
         *
         * @param {number|string} value
         * @returns {number}
         */
        function toFloat(value) {
            return parseFloat(value) || 0;
        }

        /**
         * Extracts borders size from provided styles.
         *
         * @param {CSSStyleDeclaration} styles
         * @param {...string} positions - Borders positions (top, right, ...)
         * @returns {number}
         */
        function getBordersSize(styles) {
            var positions = [];
            for (var _i = 1; _i < arguments.length; _i++) {
                positions[_i - 1] = arguments[_i];
            }
            return positions.reduce(function (size, position) {
                var value = styles['border-' + position + '-width'];
                return size + toFloat(value);
            }, 0);
        }

        /**
         * Extracts paddings sizes from provided styles.
         *
         * @param {CSSStyleDeclaration} styles
         * @returns {Object} Paddings box.
         */
        function getPaddings(styles) {
            var positions = ['top', 'right', 'bottom', 'left'];
            var paddings = {};
            for (var _i = 0, positions_1 = positions; _i < positions_1.length; _i++) {
                var position = positions_1[_i];
                var value = styles['padding-' + position];
                paddings[position] = toFloat(value);
            }
            return paddings;
        }

        /**
         * Calculates content rectangle of provided SVG element.
         *
         * @param {SVGGraphicsElement} target - Element content rectangle of which needs
         *      to be calculated.
         * @returns {DOMRectInit}
         */
        function getSVGContentRect(target) {
            var bbox = target.getBBox();
            return createRectInit(0, 0, bbox.width, bbox.height);
        }

        /**
         * Calculates content rectangle of provided HTMLElement.
         *
         * @param {HTMLElement} target - Element for which to calculate the content rectangle.
         * @returns {DOMRectInit}
         */
        function getHTMLElementContentRect(target) {
            // Client width & height properties can't be
            // used exclusively as they provide rounded values.
            var clientWidth = target.clientWidth, clientHeight = target.clientHeight;
            // By this condition we can catch all non-replaced inline, hidden and
            // detached elements. Though elements with width & height properties less
            // than 0.5 will be discarded as well.
            //
            // Without it we would need to implement separate methods for each of
            // those cases and it's not possible to perform a precise and performance
            // effective test for hidden elements. E.g. even jQuery's ':visible' filter
            // gives wrong results for elements with width & height less than 0.5.
            if (!clientWidth && !clientHeight) {
                return emptyRect;
            }
            var styles = getWindowOf(target).getComputedStyle(target);
            var paddings = getPaddings(styles);
            var horizPad = paddings.left + paddings.right;
            var vertPad = paddings.top + paddings.bottom;
            // Computed styles of width & height are being used because they are the
            // only dimensions available to JS that contain non-rounded values. It could
            // be possible to utilize the getBoundingClientRect if only it's data wasn't
            // affected by CSS transformations let alone paddings, borders and scroll bars.
            var width = toFloat(styles.width), height = toFloat(styles.height);
            // Width & height include paddings and borders when the 'border-box' box
            // model is applied (except for IE).
            if (styles.boxSizing === 'border-box') {
                // Following conditions are required to handle Internet Explorer which
                // doesn't include paddings and borders to computed CSS dimensions.
                //
                // We can say that if CSS dimensions + paddings are equal to the "client"
                // properties then it's either IE, and thus we don't need to subtract
                // anything, or an element merely doesn't have paddings/borders styles.
                if (Math.round(width + horizPad) !== clientWidth) {
                    width -= getBordersSize(styles, 'left', 'right') + horizPad;
                }
                if (Math.round(height + vertPad) !== clientHeight) {
                    height -= getBordersSize(styles, 'top', 'bottom') + vertPad;
                }
            }
            // Following steps can't be applied to the document's root element as its
            // client[Width/Height] properties represent viewport area of the window.
            // Besides, it's as well not necessary as the <html> itself neither has
            // rendered scroll bars nor it can be clipped.
            if (!isDocumentElement(target)) {
                // In some browsers (only in Firefox, actually) CSS width & height
                // include scroll bars size which can be removed at this step as scroll
                // bars are the only difference between rounded dimensions + paddings
                // and "client" properties, though that is not always true in Chrome.
                var vertScrollbar = Math.round(width + horizPad) - clientWidth;
                var horizScrollbar = Math.round(height + vertPad) - clientHeight;
                // Chrome has a rather weird rounding of "client" properties.
                // E.g. for an element with content width of 314.2px it sometimes gives
                // the client width of 315px and for the width of 314.7px it may give
                // 314px. And it doesn't happen all the time. So just ignore this delta
                // as a non-relevant.
                if (Math.abs(vertScrollbar) !== 1) {
                    width -= vertScrollbar;
                }
                if (Math.abs(horizScrollbar) !== 1) {
                    height -= horizScrollbar;
                }
            }
            return createRectInit(paddings.left, paddings.top, width, height);
        }

        /**
         * Checks whether provided element is an instance of the SVGGraphicsElement.
         *
         * @param {Element} target - Element to be checked.
         * @returns {boolean}
         */
        var isSVGGraphicsElement = (function () {
            // Some browsers, namely IE and Edge, don't have the SVGGraphicsElement
            // interface.
            if (typeof SVGGraphicsElement !== 'undefined') {
                return function (target) {
                    return target instanceof getWindowOf(target).SVGGraphicsElement;
                };
            }
            // If it's so, then check that element is at least an instance of the
            // SVGElement and that it has the "getBBox" method.
            // eslint-disable-next-line no-extra-parens
            return function (target) {
                return (target instanceof getWindowOf(target).SVGElement &&
                    typeof target.getBBox === 'function');
            };
        })();

        /**
         * Checks whether provided element is a document element (<html>).
         *
         * @param {Element} target - Element to be checked.
         * @returns {boolean}
         */
        function isDocumentElement(target) {
            return target === getWindowOf(target).document.documentElement;
        }

        /**
         * Calculates an appropriate content rectangle for provided html or svg element.
         *
         * @param {Element} target - Element content rectangle of which needs to be calculated.
         * @returns {DOMRectInit}
         */
        function getContentRect(target) {
            if (!isBrowser) {
                return emptyRect;
            }
            if (isSVGGraphicsElement(target)) {
                return getSVGContentRect(target);
            }
            return getHTMLElementContentRect(target);
        }

        /**
         * Creates rectangle with an interface of the DOMRectReadOnly.
         * Spec: https://drafts.fxtf.org/geometry/#domrectreadonly
         *
         * @param {DOMRectInit} rectInit - Object with rectangle's x/y coordinates and dimensions.
         * @returns {DOMRectReadOnly}
         */
        function createReadOnlyRect(_a) {
            var x = _a.x, y = _a.y, width = _a.width, height = _a.height;
            // If DOMRectReadOnly is available use it as a prototype for the rectangle.
            var Constr = typeof DOMRectReadOnly !== 'undefined' ? DOMRectReadOnly : Object;
            var rect = Object.create(Constr.prototype);
            // Rectangle's properties are not writable and non-enumerable.
            defineConfigurable(rect, {
                x: x, y: y, width: width, height: height,
                top: y,
                right: x + width,
                bottom: height + y,
                left: x
            });
            return rect;
        }

        /**
         * Creates DOMRectInit object based on the provided dimensions and the x/y coordinates.
         * Spec: https://drafts.fxtf.org/geometry/#dictdef-domrectinit
         *
         * @param {number} x - X coordinate.
         * @param {number} y - Y coordinate.
         * @param {number} width - Rectangle's width.
         * @param {number} height - Rectangle's height.
         * @returns {DOMRectInit}
         */
        function createRectInit(x, y, width, height) {
            return {x: x, y: y, width: width, height: height};
        }

        /**
         * Class that is responsible for computations of the content rectangle of
         * provided DOM element and for keeping track of it's changes.
         */
        var ResizeObservation = /** @class */ (function () {
            /**
             * Creates an instance of ResizeObservation.
             *
             * @param {Element} target - Element to be observed.
             */
            function ResizeObservation(target) {
                /**
                 * Broadcasted width of content rectangle.
                 *
                 * @type {number}
                 */
                this.broadcastWidth = 0;
                /**
                 * Broadcasted height of content rectangle.
                 *
                 * @type {number}
                 */
                this.broadcastHeight = 0;
                /**
                 * Reference to the last observed content rectangle.
                 *
                 * @private {DOMRectInit}
                 */
                this.contentRect_ = createRectInit(0, 0, 0, 0);
                this.target = target;
            }

            /**
             * Updates content rectangle and tells whether it's width or height properties
             * have changed since the last broadcast.
             *
             * @returns {boolean}
             */
            ResizeObservation.prototype.isActive = function () {
                var rect = getContentRect(this.target);
                this.contentRect_ = rect;
                return (rect.width !== this.broadcastWidth ||
                    rect.height !== this.broadcastHeight);
            };
            /**
             * Updates 'broadcastWidth' and 'broadcastHeight' properties with a data
             * from the corresponding properties of the last observed content rectangle.
             *
             * @returns {DOMRectInit} Last observed content rectangle.
             */
            ResizeObservation.prototype.broadcastRect = function () {
                var rect = this.contentRect_;
                this.broadcastWidth = rect.width;
                this.broadcastHeight = rect.height;
                return rect;
            };
            return ResizeObservation;
        }());

        var ResizeObserverEntry = /** @class */ (function () {
            /**
             * Creates an instance of ResizeObserverEntry.
             *
             * @param {Element} target - Element that is being observed.
             * @param {DOMRectInit} rectInit - Data of the element's content rectangle.
             */
            function ResizeObserverEntry(target, rectInit) {
                var contentRect = createReadOnlyRect(rectInit);
                // According to the specification following properties are not writable
                // and are also not enumerable in the native implementation.
                //
                // Property accessors are not being used as they'd require to define a
                // private WeakMap storage which may cause memory leaks in browsers that
                // don't support this type of collections.
                defineConfigurable(this, {target: target, contentRect: contentRect});
            }

            return ResizeObserverEntry;
        }());

        var ResizeObserverSPI = /** @class */ (function () {
            /**
             * Creates a new instance of ResizeObserver.
             *
             * @param {ResizeObserverCallback} callback - Callback function that is invoked
             *      when one of the observed elements changes it's content dimensions.
             * @param {ResizeObserverController} controller - Controller instance which
             *      is responsible for the updates of observer.
             * @param {ResizeObserver} callbackCtx - Reference to the public
             *      ResizeObserver instance which will be passed to callback function.
             */
            function ResizeObserverSPI(callback, controller, callbackCtx) {
                /**
                 * Collection of resize observations that have detected changes in dimensions
                 * of elements.
                 *
                 * @private {Array<ResizeObservation>}
                 */
                this.activeObservations_ = [];
                /**
                 * Registry of the ResizeObservation instances.
                 *
                 * @private {Map<Element, ResizeObservation>}
                 */
                this.observations_ = new MapShim();
                if (typeof callback !== 'function') {
                    throw new TypeError('The callback provided as parameter 1 is not a function.');
                }
                this.callback_ = callback;
                this.controller_ = controller;
                this.callbackCtx_ = callbackCtx;
            }

            /**
             * Starts observing provided element.
             *
             * @param {Element} target - Element to be observed.
             * @returns {void}
             */
            ResizeObserverSPI.prototype.observe = function (target) {
                if (!arguments.length) {
                    throw new TypeError('1 argument required, but only 0 present.');
                }
                // Do nothing if current environment doesn't have the Element interface.
                if (typeof Element === 'undefined' || !(Element instanceof Object)) {
                    return;
                }
                if (!(target instanceof getWindowOf(target).Element)) {
                    throw new TypeError('parameter 1 is not of type "Element".');
                }
                var observations = this.observations_;
                // Do nothing if element is already being observed.
                if (observations.has(target)) {
                    return;
                }
                observations.set(target, new ResizeObservation(target));
                this.controller_.addObserver(this);
                // Force the update of observations.
                this.controller_.refresh();
            };
            /**
             * Stops observing provided element.
             *
             * @param {Element} target - Element to stop observing.
             * @returns {void}
             */
            ResizeObserverSPI.prototype.unobserve = function (target) {
                if (!arguments.length) {
                    throw new TypeError('1 argument required, but only 0 present.');
                }
                // Do nothing if current environment doesn't have the Element interface.
                if (typeof Element === 'undefined' || !(Element instanceof Object)) {
                    return;
                }
                if (!(target instanceof getWindowOf(target).Element)) {
                    throw new TypeError('parameter 1 is not of type "Element".');
                }
                var observations = this.observations_;
                // Do nothing if element is not being observed.
                if (!observations.has(target)) {
                    return;
                }
                observations.delete(target);
                if (!observations.size) {
                    this.controller_.removeObserver(this);
                }
            };
            /**
             * Stops observing all elements.
             *
             * @returns {void}
             */
            ResizeObserverSPI.prototype.disconnect = function () {
                this.clearActive();
                this.observations_.clear();
                this.controller_.removeObserver(this);
            };
            /**
             * Collects observation instances the associated element of which has changed
             * it's content rectangle.
             *
             * @returns {void}
             */
            ResizeObserverSPI.prototype.gatherActive = function () {
                var _this = this;
                this.clearActive();
                this.observations_.forEach(function (observation) {
                    if (observation.isActive()) {
                        _this.activeObservations_.push(observation);
                    }
                });
            };
            /**
             * Invokes initial callback function with a list of ResizeObserverEntry
             * instances collected from active resize observations.
             *
             * @returns {void}
             */
            ResizeObserverSPI.prototype.broadcastActive = function () {
                // Do nothing if observer doesn't have active observations.
                if (!this.hasActive()) {
                    return;
                }
                var ctx = this.callbackCtx_;
                // Create ResizeObserverEntry instance for every active observation.
                var entries = this.activeObservations_.map(function (observation) {
                    return new ResizeObserverEntry(observation.target, observation.broadcastRect());
                });
                this.callback_.call(ctx, entries, ctx);
                this.clearActive();
            };
            /**
             * Clears the collection of active observations.
             *
             * @returns {void}
             */
            ResizeObserverSPI.prototype.clearActive = function () {
                this.activeObservations_.splice(0);
            };
            /**
             * Tells whether observer has active observations.
             *
             * @returns {boolean}
             */
            ResizeObserverSPI.prototype.hasActive = function () {
                return this.activeObservations_.length > 0;
            };
            return ResizeObserverSPI;
        }());

        // Registry of internal observers. If WeakMap is not available use current shim
        // for the Map collection as it has all required methods and because WeakMap
        // can't be fully polyfilled anyway.
        var observers = typeof WeakMap !== 'undefined' ? new WeakMap() : new MapShim();
        /**
         * ResizeObserver API. Encapsulates the ResizeObserver SPI implementation
         * exposing only those methods and properties that are defined in the spec.
         */
        var ResizeObserver = /** @class */ (function () {
            /**
             * Creates a new instance of ResizeObserver.
             *
             * @param {ResizeObserverCallback} callback - Callback that is invoked when
             *      dimensions of the observed elements change.
             */
            function ResizeObserver(callback) {
                if (!(this instanceof ResizeObserver)) {
                    throw new TypeError('Cannot call a class as a function.');
                }
                if (!arguments.length) {
                    throw new TypeError('1 argument required, but only 0 present.');
                }
                var controller = ResizeObserverController.getInstance();
                var observer = new ResizeObserverSPI(callback, controller, this);
                observers.set(this, observer);
            }

            return ResizeObserver;
        }());
        // Expose public methods of ResizeObserver.
        [
            'observe',
            'unobserve',
            'disconnect'
        ].forEach(function (method) {
            ResizeObserver.prototype[method] = function () {
                var _a;
                return (_a = observers.get(this))[method].apply(_a, arguments);
            };
        });

        var index = (function () {
            // Export existing implementation if available.
            if (typeof global$1.ResizeObserver !== 'undefined') {
                return global$1.ResizeObserver;
            }
            return ResizeObserver;
        })();

        return index;

    })));_N2.StringHelper = {
    capitalize: function (s) {
        return s.charAt(0).toUpperCase() + s.slice(1);
    }
};(function () {


    var isIterable = function (value) {
            return Symbol.iterator in Object(value);
        },
        fallbackTicker = {
            add: function (callback) {
                requestAnimationFrame(callback);
            },
            remove: function () {

            }
        };

    _N2.___Ticker = _N2.___Ticker || fallbackTicker;

    class TickQueue {
        constructor() {
            this.___active = false;
            this.___wrappers = new Set();

            this.___onTickCallback = this.___onTick.bind(this);
        }

        add(wrapper) {
            this.___wrappers.add(wrapper);

            if (!this.___active) {
                this.___active = true;
                _N2.___Ticker.add(this.___onTickCallback);
            }
        }

        ___onTick() {

            for (let wrapper of this.___wrappers) {
                if (wrapper.render) {
                    wrapper.render();
                } else {
                    wrapper();
                }
            }

            this.___wrappers.clear();

            this.___active = false;
            _N2.___Ticker.remove(this.___onTickCallback);
        }
    }

    var tickQueue = new TickQueue(),
        defaultValues = {},
        getTranslate;

    if (/Safari/i.test(navigator.userAgent) && !/Chrom[ei]/i.test(navigator.userAgent)) {
        /**
         * @see https://bugs.webkit.org/show_bug.cgi?id=223784
         * @see https://bugs.webkit.org/show_bug.cgi?id=230753
         */
        getTranslate = function (x, y, z, force3D) {
            var transform = '';

            if (force3D) {
                if (x || y || z) {
                    return "translate3d(" + (x || 0) + "px, " + (y || 0) + "px, " + (z || 0) + "px) ";
                }
            }

            if (x || y) {
                transform += "translate(" + (x || 0) + "px, " + (y || 0) + "px) ";
            }

            if (z) {
                transform += "translateZ(" + z + "px) ";
            }

            return transform;
        };
    } else {
        getTranslate = function (x, y, z, force3D) {
            if (x || y || z) {
                return "translate3d(" + (x || 0) + "px, " + (y || 0) + "px, " + (z || 0) + "px) ";
            }

            return '';
        };
    }


    class StyleMiddleWare {

        constructor(target, values) {
            if (!isIterable(target)) {
                this.___target = [target];
            } else {
                this.___target = Array.from(target);
            }

            this.___properties = _Assign({}, values);

            this.___changed = new Set();
        }

        get scale() {
            return this.scaleX;
        }

        set scale(scale) {
            this.scaleX = scale;
            this.scaleY = scale;
        }

        render() {
            for (let prop of this.___changed) {
                var value = this.___properties[prop];
                switch (prop) {
                    case 'transform':
                        value = this.___getTransform();
                        break;
                    case 'filter':
                        value = this.___getFilter();
                        break;
                    case 'n2AutoAlpha':
                        /**
                         * When n2AutoAlpha disable/enabled, we must sync the related attribute
                         */
                        if (this.opacity === 0) {
                            if (!this.___forceHidden) {
                                this.___forceHidden = true;
                                this.___setAttribute('data-force-hidden', '');
                            }
                        } else {
                            if (this.___forceHidden) {
                                this.___forceHidden = false;
                                this.___removeAttribute('data-force-hidden');
                            }
                        }
                        continue;
                    case 'opacity':
                        if (this.___properties.n2AutoAlpha) {
                            /**
                             * When n2AutoAlpha enabled, we must sync the related attribute
                             */
                            if (value === 0) {
                                if (!this.___forceHidden) {
                                    this.___forceHidden = true;
                                    this.___setAttribute('data-force-hidden', '');
                                }
                            } else {
                                if (this.___forceHidden) {
                                    this.___forceHidden = false;
                                    this.___removeAttribute('data-force-hidden');
                                }
                            }
                        }
                        break;
                    case 'width':
                    case 'height':
                    case 'perspective':
                        if (typeof value === 'number') {
                            value = value + 'px';
                        }
                        break;
                }
                for (let target of this.___target) {
                    target.style.setProperty(prop, value);
                }
            }

            this.___changed.clear();
        }

        ___getTransform() {
            let {
                    xP,
                    yP,
                    x,
                    y,
                    z,
                    xAbs,
                    yAbs,
                    xPAbs,
                    yPAbs,
                    parallaxX,
                    parallaxY,
                    parallaxRotationX,
                    parallaxRotationY,
                    layerRotation,
                    rotationZ,
                    rotationY,
                    rotationX,
                    scaleX,
                    scaleY,
                    scaleZ,
                    skewX,
                    skewY,
                    transformPerspective,
                    force3D
                } = this.___properties,
                transform = "";

            if (transformPerspective) {
                transform += "perspective(" + transformPerspective + 'px) ';
            }

            if (xP || yP) {
                transform += "translate(" + (xP || 0) + "%, " + (yP || 0) + "%) ";
            }

            if (xAbs || yAbs) {
                transform += "translate(" + (xAbs || 0) + "px, " + (yAbs || 0) + "px) ";
            }

            if (xPAbs || yPAbs) {
                transform += "translate(" + (xPAbs || 0) + "%, " + (yPAbs || 0) + "%) ";
            }

            transform += getTranslate(x, y, z, force3D);

            if (skewX) {
                transform += "skewX(" + skewX + "deg) ";
            }

            if (skewY) {
                transform += "skewY(" + skewY + "deg) ";
            }

            if (layerRotation) {
                transform += "rotate(" + layerRotation + "deg) ";
            }

            if (rotationZ) {
                transform += "rotate(" + rotationZ + "deg) ";
            }
            if (rotationY) {
                transform += "rotateY(" + rotationY + "deg) ";
            }
            if (rotationX) {
                transform += "rotateX(" + rotationX + "deg) ";
            }
        

            if (scaleX === undefined) {
                scaleX = 1;
            }
            if (scaleY === undefined) {
                scaleY = 1;
            }
            if (scaleX !== 1 || scaleY !== 1) {
                transform += "scale(" + scaleX + ", " + scaleY + ") ";
            }

            if (scaleZ === undefined) {
                scaleZ = 1;
            }
            if (scaleZ !== 1) {
                transform += "scaleZ(" + scaleZ + ") ";
            }

            return transform || "translate3d(0, 0, 0)";
        }

        ___getFilter() {
            let {
                    n2blur
                } = this.___properties,
                filter = "";

            if (n2blur > 0.1) {
                filter = "blur(" + n2blur + "px) ";
            }

            return filter || 'none';
        }

        ___setAttribute(name, value) {
            for (let target of this.___target) {
                (target.relatedLayer || target).setAttribute(name, value);
            }
        }

        ___removeAttribute(name) {
            for (let target of this.___target) {
                (target.relatedLayer || target).removeAttribute(name);
            }
        }

        setValues(values) {
            for (var propertyName in values) {
                this[propertyName] = values[propertyName];
            }
        }
    }


    class StyleMiddleWareGroup {
        constructor(targets, values) {
            this.___smws = [];

            for (var i = 0; i < targets.length; i++) {
                if (targets[i]) {
                    this.___smws.push(MW.___getSMW(targets[i], values));
                }
            }
        }

        setValues(values) {
            for (var i = 0; i < this.___smws.length; i++) {
                this.___smws[i].setValues(values);
            }
        }
    }

    var propertyGroups = {},
        defineGroupProp = function (prop) {
            Object.defineProperty(StyleMiddleWareGroup.prototype, prop, {
                get: function () {
                    return this.___smws[0][prop];
                },
                set: function (value) {
                    if (value instanceof Function) {
                        value = value();
                    }

                    for (var i = 0; i < this.___smws.length; i++) {
                        this.___smws[i][prop] = value;
                    }
                }
            });
        },
        defineProperty = function (prop, value, changes) {
            if (!Object.getOwnPropertyDescriptor(StyleMiddleWare.prototype, prop)) {

                if (value === undefined) {
                    value = '';
                }
                if (changes === undefined) {
                    changes = prop;
                }
                if (propertyGroups[changes] === undefined) {
                    propertyGroups[changes] = [];
                }
                propertyGroups[changes].push(prop);

                defaultValues[prop] = value;

                Object.defineProperty(StyleMiddleWare.prototype, prop, {
                    get: function () {
                        if (this.___properties[prop] === undefined) {
                            this.___properties[prop] = defaultValues[prop];
                        }
                        return this.___properties[prop];
                    },
                    set: function (value) {
                        if (value instanceof Function) {
                            value = value();
                        }
                        if (this.___properties[prop] !== value) {
                            this.___properties[prop] = value;

                            this.___changed.add(changes);
                            tickQueue.add(this);
                        }
                    }
                });

                defineGroupProp(prop);
            }
        },
        defineAlias = function (alias, prop) {
            Object.defineProperty(StyleMiddleWare.prototype, alias, {
                get: function () {
                    return this[prop];
                },
                set: function (value) {
                    this[prop] = value;
                }
            });
            defineGroupProp(alias);
        };

    defineProperty('property');
    defineProperty('display');
    defineProperty('z-index', 1);
    defineProperty('overflow', 'visible');
    defineProperty('overflow-x', 'visible');
    defineProperty('backface-visibility', 'visible');
    defineProperty('transform-origin', '50% 50% 0');
    defineProperty('opacity', 1);
    defineProperty('width', 0);
    defineProperty('height', 0);
    defineProperty('justify-content');
    defineProperty('background');
    defineProperty('color');
    defineProperty('will-change', '');
    defineProperty('stroke-dasharray', '');
    defineProperty('visibility');
    defineProperty('perspective', 0);
    defineProperty('transform-style');
    defineProperty('cursor', '');
    defineProperty('top');
    defineProperty('right');
    defineProperty('bottom');
    defineProperty('left');

    var transform = 'transform';
    defineProperty('force3D', 0, transform);
    defineProperty('transformPerspective', 0, transform);
    defineProperty('xP', 0, transform);
    defineProperty('yP', 0, transform);
    defineProperty('x', 0, transform);
    defineProperty('y', 0, transform);
    defineProperty('z', 0, transform);
    defineProperty('xAbs', 0, transform);
    defineProperty('yAbs', 0, transform);
    defineProperty('xPAbs', 0, transform);
    defineProperty('yPAbs', 0, transform);
    defineProperty('scaleX', 1, transform);
    defineProperty('scaleY', 1, transform);
    defineProperty('scaleZ', 1, transform);
    defineProperty('rotationZ', 0, transform);
    defineProperty('rotationX', 0, transform);
    defineProperty('rotationY', 0, transform);
    defineProperty('skewX', 0, transform);
    defineProperty('skewY', 0, transform);
    defineProperty('layerRotation', 0, transform);

    defineProperty('n2blur', 0, 'filter');
    defineProperty('n2AutoAlpha', 0);

    defineAlias('zIndex', 'z-index');
    defineAlias('backfaceVisibility', 'backface-visibility');
    defineAlias('transformOrigin', 'transform-origin');
    defineAlias('justifyContent', 'justify-content');
    defineAlias('transformStyle', 'transform-style');
    defineAlias('overflowX', 'overflow-x');
    defineAlias('clipPath', 'clip-path');

    var ___addClass = 0,
        ___removeClass = 1,
        actions = {
            0: function (className) {
                this.classList.add(className);
            },
            1: function (className) {
                this.classList.remove(className);
            }
        };

    class UniversalMiddleWare {

        constructor(target,) {
            if (!isIterable(target)) {
                this.___target = [target];
            } else {
                this.___target = Array.from(target);
            }

            this.___queue = [];
        }

        addClass(className) {
            this.___queue.push([___addClass, className]);
            tickQueue.add(this);
        }

        removeClass(className) {
            this.___queue.push([___removeClass, className]);
            tickQueue.add(this);
        }

        render() {
            for (var i = 0; i < this.___queue.length; i++) {
                var action = this.___queue[i];

                for (let target of this.___target) {
                    actions[action[0]].apply(target, action.splice(1));
                }
            }

            this.___queue = [];
        }
    }

    var MW = _N2.MW = {
        ___defineProperty: defineProperty,
        /**
         * @param target
         * @param values
         * @return {StyleMiddleWare}
         * @private
         */
        ___getSMW: function (target, values) {
            if (!target.smw) {
                target.smw = new StyleMiddleWare(target, values);
            }

            return target.smw;
        },
        /**
         *
         * @param targets
         * @param values
         * @return {StyleMiddleWare[]}
         * @private
         */
        ___getSMWs: function (targets, values) {

            var SMWs = [];

            for (var i = 0; i < targets.length; i++) {
                SMWs.push(MW.___getSMW(targets[i], values));
            }

            return SMWs;
        },

        /**
         *
         * @param targets
         * @param values
         * @return {StyleMiddleWareGroup}
         * @private
         */
        ___getSMWGroup: function (targets, values) {

            return new StyleMiddleWareGroup(targets, values);
        },
        ___setValues: function (targets, values) {
            targets.forEach(function (target) {
                target.setValues(values);
            })
        },
        /**
         *
         * @param {StyleMiddleWare} smw
         * @param {{}[]}properties
         * @private
         */
        ___clearProperties: function (smw, properties) {

            properties.forEach(function (propertyGroup) {
                if (propertyGroups[propertyGroup]) {
                    propertyGroups[propertyGroup].forEach(function (property) {
                        smw[property] = defaultValues[property];
                    });
                }
            });
        },
        ___addClass: function (target, className) {
            tickQueue.add(function () {
                target.className.add(className);
            });
        },
        ___removeClass: function (target, className) {
            tickQueue.add(function () {
                target.className.remove(className);
            });
        },
        ___addCallback: function (callback) {
            tickQueue.add(callback);
        },
        /**
         * @param target
         * @return {UniversalMiddleWare}
         * @private
         */
        ___getUMW: function (target) {
            if (!target.umw) {
                target.umw = new UniversalMiddleWare(target);
            }

            return target.umw;
        },
        flush() {
            tickQueue.___onTick();
        }
    };
})();customElements.define('ss3-force-full-width', class extends HTMLElement {
    connectedCallback() {
        if (this.isConnected) {
            /**
             * DOMContentLoaded event might not get fired at this point, so we have to prepare the body variable to be able to use it here.
             */
            body = body || document.body;

            this.___isRTL = html.getAttribute('dir') === 'rtl';

            this._width = 0;
            this._offset = Number.MAX_SAFE_INTEGER;

            this.___SMW = _N2.MW.___getSMW(this, {
                x: Number.MAX_SAFE_INTEGER,
                opacity: 0
            });

            var overflowX = _NodeGetData(this, 'overflowX');
            if (overflowX && overflowX !== 'none') {
                document.querySelectorAll(overflowX)
                    .forEach(function (element) {
                        _N2.MW.___getSMW(element).overflowX = 'hidden';
                    });
            }

            var fullWidthTo = body;

            if (window.ssForceFullHorizontalSelector) {
                fullWidthTo = window.ssForceFullHorizontalSelector;
            } else {

                var fullWidthToSelector = _NodeGetData(this, 'horizontalSelector');

                if (fullWidthToSelector && fullWidthToSelector !== 'body') {
                    try {
                        fullWidthTo = this.closest(fullWidthToSelector);
                    } catch (e) {
                    }
                }
            }
            if (fullWidthTo === body) {

                var parentSelectors = [
                    '.elementor-section-stretched',
                    '.siteorigin-panels-stretch[data-stretch-type="full-stretched"]',
                    '.siteorigin-panels-stretch[data-stretch-type="full-stretched-padded"]',
                    '.themify_builder_row.fullwidth',
                    '.vce-row[data-vce-stretch-content="true"]'
                ];
                for (var i = 0; i < parentSelectors.length; i++) {
                    var closest = this.closest(parentSelectors[i]);
                    if (closest) {
                        fullWidthTo = closest;
                        break;
                    }
                }
            }

            this.fullWidthTo = fullWidthTo;

            this.resizeObserver = new ResizeObserver(this.doResize.bind(this));

            this.resizeObserver.observe(this.parentNode);

            window.addEventListener('resize', this.doResize.bind(this));
        }
    }

    doResize() {

        var adjustOffset = 0,
            customWidth = 0;

        if (this.fullWidthTo) {
            var rect = this.fullWidthTo.getBoundingClientRect();
            customWidth = rect.width;
            if (!this.___isRTL) {
                adjustOffset = rect.left;
            } else {
                adjustOffset = -customWidth + rect.right;
            }
        }

        var windowWidth = customWidth > 0 ? customWidth : body.clientWidth,
            cs = window.getComputedStyle(this.parentNode),
            offset,
            floating;

        if (!this.___isRTL) {
            offset = -this.parentNode.getBoundingClientRect().left - parseInt(cs.getPropertyValue('padding-left')) - parseInt(cs.getPropertyValue('border-left-width')) + adjustOffset
        } else {
            offset = windowWidth - this.parentNode.getBoundingClientRect().right - parseInt(cs.getPropertyValue('padding-right')) - parseInt(cs.getPropertyValue('border-right-width')) + adjustOffset
        }
        floating = offset % 1;
        offset += floating;
        windowWidth -= Math.floor(floating);

        if (this._width - windowWidth <= 0 || this._width - windowWidth > 1
            || this._offset - offset < -1 || this._offset - offset >= 0) {

            if (this._offset !== offset) {
                this.___SMW.x = offset;
                this._offset = offset;
                if (offset !== 0) {
                    this.classList.add('n2-ss-no-bga-fixed');
                }
            }
            if (this._width !== windowWidth) {
                this.___SMW.width = windowWidth;
                this._width = windowWidth;
            }
        }

        this.setVisible && this.setVisible();
    }

    setVisible() {

        this.___SMW.opacity = 1;

        delete this.setVisible;
    }
});customElements.define('ss3-loader', class extends HTMLElement {
    connectedCallback() {

        this.___promises = [];

    }

    set display(display) {
        if (this.___display !== display) {
            this.___display = display;
            this.style.display = display;
        }
    }

    show() {
        this.display = 'grid';
    }

    addPromise(promise) {

        this.___promises.push(promise);
        this.syncStyle();

        promise.finally(this.removePromise.bind(this, promise));
    }

    removePromise(promise) {

        var index = this.___promises.indexOf(promise);
        if (index > -1) {
            this.___promises.splice(index, 1);
            this.syncStyle();
        }
    }

    syncStyle() {

        if (this.___promises.length) {
            if (!this.___timeout) {
                this.___timeout = setTimeout(this.show.bind(this), 100);
            }
        } else {

            if (this.___timeout) {
                clearTimeout(this.___timeout);
                delete this.___timeout;
            }
            this.display = '';
        }
    }
});

var isIpad13 = navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1;
window.n2const = {
    devicePixelRatio: window.devicePixelRatio || 1,
    isFirefox: /Firefox/i.test(navigator.userAgent),
    isIOS: /iPad|iPhone|iPod/.test(navigator.platform) || isIpad13,
    isMobile: /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Silk/i.test(navigator.userAgent) || isIpad13,
    isPhone: (/Android/i.test(navigator.userAgent) && /mobile/i.test(navigator.userAgent)) || /webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent),
    isSamsungBrowser: navigator.userAgent.match(/SamsungBrowser/i),
    isBot: /bot|googlebot|crawler|spider|robot|crawling|Google Search Console/i.test(navigator.userAgent),
    isLighthouse: navigator.userAgent.indexOf("Chrome-Lighthouse") > -1,
    lightboxMobileNewTab: 1,
    isVideoAutoplayAllowed: function () {
        var isAllowed = !!(navigator.platform.match(/(Win|Mac)/) || !(/Mobi/.test(navigator.userAgent)) || ('playsInline' in _CreateElement('video') || ('webkit-playsinline' in _CreateElement('video'))) || (navigator.userAgent.match(/Chrom(e|ium)\/([0-9]+)\./) && parseInt(navigator.userAgent.match(/Chrom(e|ium)\/([0-9]+)\./)[2]) >= 53) || navigator.userAgent.match(/Android.*(Firefox|Edge|Opera)/));
        window.n2const.isVideoAutoplayAllowed = function () {
            return isAllowed;
        };
        return isAllowed;
    },
    isWaybackMachine: function () {
        var isWaybackMachine = typeof window.__wm !== 'undefined';
        window.n2const.isWaybackMachine = function () {
            return isWaybackMachine;
        };
        return isWaybackMachine;
    },
    setLocation: function (l) {
        if (typeof window.zajax_goto === 'function') {
            /**
             * @url https://wordpress.org/plugins/zajax-ajax-navigation/
             */
            window.zajax_goto(l);
        } else {
            window.location = l;
        }
    },
    isParentSameOrigin: function () {
        try {
            parent.document;
            return true;
        } catch (e) {
        }

        return false;
    },
    activeElementBlur: function () {
        if (document.activeElement) {
            document.activeElement.blur();
        }
    },
    getScrollbarSize: function () {
        var div = _CreateElementDiv();
        div.style.visibility = 'hidden';
        div.style.overflow = 'scroll';
        body.appendChild(div);
        var size = div.offsetHeight - div.clientHeight;
        body.removeChild(div);

        n2const.getScrollbarSize = function () {
            return size;
        };

        return size;
    },
    fonts: new Promise(function (resolve) {
        if (!('fonts' in document)) {
            _N2.r('windowLoad', resolve);
        } else {
            document.fonts.ready.then(resolve);

            /**
             * Safari sometimes does not resolve the document.fonts.ready promise, so we need a fallback.
             * @see SSDEV-3335
             * @see https://bugs.webkit.org/show_bug.cgi?id=231156
             */

            const ua = navigator.userAgent;
            if (ua.indexOf('Safari') > 0 && ua.indexOf('Chrome') === -1) {
                _N2.r('windowLoad', resolve);
            }
        }
    })
};

window.n2const.isTablet = (function () {
    if (!window.n2const.isPhone) {
        return /Android|iPad|tablet|Silk/i.test(navigator.userAgent) || isIpad13;
    }
    return false;
})();

window.n2const.rtl = (function () {
    window.n2const.isRTL = function () {
        return window.n2const.rtl.isRtl;
    };


    if (html.getAttribute('dir') === 'rtl') {
        return {
            isRtl: true,
            marginLeft: 'marginRight',
            marginRight: 'marginLeft',
            'margin-left': 'margin-right',
            'margin-right': 'margin-left',
            left: 'right',
            right: 'left',
            modifier: -1
        };
    }

    return {
        isRtl: false,
        marginLeft: 'marginLeft',
        marginRight: 'marginRight',
        'margin-left': 'margin-left',
        'margin-right': 'margin-right',
        left: 'left',
        right: 'right',
        modifier: 1
    };
})();

_N2._triggerResize = (function () {
    var delay = 100,
        timeout = null;

    return function () {
        if (timeout) {
            clearTimeout(timeout);
        }
        timeout = setTimeout(function () {
            _dispatchEventSimple(window, 'resize');
            timeout = null;
        }, delay);
    };
})();

_N2._shouldPreventClick = false;

_N2._preventClick = function () {
    if (!_N2._shouldPreventClick) {
        _N2._shouldPreventClick = true;
        setTimeout(function () {
            _N2._shouldPreventClick = false;
        }, 300);
    }
};_N2.d('ImagesLoaded', function () {

    /**
     *
     * @memberOf _N2
     *
     * @param el
     * @return {Promise<unknown[]>}
     * @constructor
     */
    function ImagesLoaded(el) {

        var images, promises = [];

        if (el.tagName === 'IMG') {
            images = [el];
        } else {
            images = el.querySelectorAll('img');
        }

        for (var i = 0; i < images.length; i++) {
            var image = images[i];
            image.loading = 'eager';

            if (image.complete) {
                if (!image.naturalWidth) {
                    /**
                     * Firefox reports 0 naturalWidth for SVGs, so we mimic loading
                     */
                    promises.push(new Promise((function (resolve) {
                        setTimeout(resolve, 16);
                    }).bind(image)));
                }
            } else {
                promises.push(new Promise((function (resolve, reject) {
                    this.addEventListener('load', function () {
                        resolve();
                    });
                    this.addEventListener('error', function () {
                        reject();
                    });
                }).bind(image)));
            }
        }

        return Promise.all(promises);
    }

    return ImagesLoaded;
});_N2.d('UniversalPointer', function () {
    var pointerEvents = !!window.PointerEvent,
        touchEvents = !!window.TouchEvent;

    function UniversalClickContext(el, handler) {
        this.el = el;
        this.handler = handler;

        this.preventMouse = false;
        this.timeouts = [];
        this.localListeners = [];
        this.globalListeners = [];
    }

    UniversalClickContext.prototype.addTimeout = function (timeout) {
        this.timeouts.push(timeout);
    };

    UniversalClickContext.prototype.clearTimeouts = function () {

        for (var i = 0; i < this.timeouts.length; i++) {
            clearTimeout(this.timeouts[i]);
        }

        this.timeouts = [];
    };

    UniversalClickContext.prototype.click = function (e) {

        if (this.currentTarget !== undefined) {
            /**
             * For complex events, we need to fix the currentTarget property
             * @type {{currentTarget: *, target: *}}
             */
            e = {
                currentTarget: this.currentTarget,
                target: this.el
            };
        }

        this.handler.call(this.el, e);

        this.clear();
    };

    UniversalClickContext.prototype.clear = function () {

        for (var i = 0; i < this.localListeners.length; i++) {
            this.localListeners[i][0].removeEventListener(this.localListeners[i][1], this.localListeners[i][2], this.localListeners[i][3]);
        }
    };

    UniversalClickContext.prototype.addGlobalEventListener = function (type, listener, options) {

        this.globalListeners.push(_addEventListenerWithRemover(this.el, type, listener, options));
    };

    UniversalClickContext.prototype.addLocalEventListener = function (el, type, listener, options) {
        this.localListeners.push([el, type, listener, options]);
        el.addEventListener(type, listener, options);
    };

    UniversalClickContext.prototype.remove = function () {

        this.clear();

        this.clearTimeouts();

        for (var i = 0; i < this.globalListeners.length; i++) {
            this.globalListeners[i]();
        }
        delete this.globalListeners;
    };

    UniversalClickContext.prototype.startComplexInteraction = function (currentTarget) {
        this.clearTimeouts();
        this.preventMouse = true;
        this.currentTarget = currentTarget;
    };

    UniversalClickContext.prototype.endComplexInteraction = function () {
        delete this.currentTarget;

        this.addTimeout(setTimeout((function () {
            this.preventMouse = false;
        }).bind(this), 1000));
    };

    function UniversalClick(element, handler, options) {
        var context = this.context = new UniversalClickContext(element, handler);

        context.addGlobalEventListener('click', function (e) {
            if (!context.preventMouse) {
                context.click(e);
            }
        });

        if (pointerEvents) {
            context.addGlobalEventListener('pointerdown', function (downEvent) {
                if (!downEvent.isPrimary) return;

                context.startComplexInteraction(downEvent.currentTarget);

                context.addLocalEventListener(html, 'pointerup', function (upEvent) {
                    if (!upEvent.isPrimary) return;

                    if (downEvent.pointerId === upEvent.pointerId) {
                        if (Math.abs(upEvent.clientX - downEvent.clientX) < 10 && Math.abs(upEvent.clientY - downEvent.clientY) < 10) {
                            context.click(upEvent);
                        } else {
                            context.clear();
                        }
                        context.endComplexInteraction();
                    }
                });
            });
        } else {

            if (touchEvents) {
                context.addGlobalEventListener('touchstart', function (downEvent) {
                    context.clearTimeouts();

                    context.startComplexInteraction(downEvent.currentTarget);

                    context.addLocalEventListener(html, 'touchend', function (upEvent) {
                        if (Math.abs(upEvent.changedTouches[0].clientX - downEvent.changedTouches[0].clientX) < 10 && Math.abs(upEvent.changedTouches[0].clientY - downEvent.changedTouches[0].clientY) < 10) {
                            context.click(upEvent);
                        } else {
                            context.clear();
                        }

                        context.endComplexInteraction();

                    }, {passive: true});
                }, {passive: true});
            }
        }
    }

    UniversalClick.prototype.remove = function () {
        this.context.remove();
        delete this.context;
    };

    _N2.UniversalClick = UniversalClick;

    function UniversalEnterContext(el, handler, leaveOnSecond) {
        this.el = el;
        this.handler = handler;
        this.leaveOnSecond = leaveOnSecond;
        this.preventMouse = false;
        this.isActive = false;
        this.timeouts = [];
        this.localListeners = [];
        this.globalListeners = [];
    }

    UniversalEnterContext.prototype.enter = function (e) {
        if (this.leaveOnSecond && this.isActive) {
            this.leave();
            return false;
        }

        this.handler.apply(this.el, arguments);
        this.isActive = true;
        return true;
    };

    UniversalEnterContext.prototype.leave = function () {
        this.clearTimeouts();

        for (var i = 0; i < this.localListeners.length; i++) {
            this.localListeners[i][0].removeEventListener(this.localListeners[i][1], this.localListeners[i][2], this.localListeners[i][3]);
        }

        this.isActive = false;

        _dispatchEventSimpleNoBubble(this.el, 'universalleave');
    };

    UniversalEnterContext.prototype.testLeave = function (target) {
        if (!this.el === target && this.el.contains(target)) {
            this.leave();
        }
    };

    UniversalEnterContext.prototype.addTimeout = function (timeout) {
        this.timeouts.push(timeout);
    };

    UniversalEnterContext.prototype.clearTimeouts = function () {

        for (var i = 0; i < this.timeouts.length; i++) {
            clearTimeout(this.timeouts[i]);
        }

        this.timeouts = [];
    };

    UniversalEnterContext.prototype.addGlobalEventListener = function (type, listener, options) {

        this.globalListeners.push(_addEventListenerWithRemover(this.el, type, listener, options));
    };

    UniversalEnterContext.prototype.remove = function () {
        if (this.isActive) {
            this.leave();
        }

        this.clearTimeouts();

        for (var i = 0; i < this.globalListeners.length; i++) {
            this.globalListeners[i]();
        }
        delete this.globalListeners;
    };

    UniversalEnterContext.prototype.addLocalEventListener = function (el, type, listener, options) {
        this.localListeners.push([el, type, listener, options]);
        el.addEventListener(type, listener, options);
    };

    function UniversalEnter(element, handler, options) {
        options = _Assign({
            leaveOnSecond: false
        }, options);

        var context = this.context = new UniversalEnterContext(element, handler, options.leaveOnSecond);

        if (pointerEvents) {
            context.addGlobalEventListener('pointerenter', function (e) {
                if (!e.isPrimary) return;

                context.clearTimeouts();

                if (context.enter(e)) {

                    if (e.pointerType !== 'mouse') {
                        context.addLocalEventListener(html, 'pointerdown', function (e) {
                            if (!e.isPrimary) return;

                            context.testLeave(e.target);
                        });

                        context.addTimeout(setTimeout(function () {
                            context.leave();
                        }, 5000));
                    }
                }

            });

            context.addGlobalEventListener('pointerleave', function (e) {
                if (!e.isPrimary) return;

                if (e.pointerType === 'mouse') {
                    context.leave();
                }
            });
        } else {

            context.addGlobalEventListener('mouseenter', function (e) {
                if (!context.preventMouse) {
                    context.enter(e);
                }
            });

            context.addGlobalEventListener('mouseleave', function () {
                if (!context.preventMouse) {
                    context.leave();
                }
            });

            if (touchEvents) {
                context.addGlobalEventListener('touchstart', function (e) {
                    context.preventMouse = true;
                    context.clearTimeouts();

                    if (context.enter(e)) {
                        context.addLocalEventListener(html, 'touchstart', function (e) {
                            context.testLeave(e.target);
                        });

                        context.addTimeout(setTimeout(function () {
                            context.leave();
                            context.preventMouse = false;
                        }, 5000));
                    }
                }, {passive: true});
            }
        }
    }

    UniversalEnter.prototype.remove = function () {
        this.context.remove();
        delete this.context;
    };

    _N2.UniversalEnter = UniversalEnter;

});/*
 * Event Burrito is a touch / mouse / pointer event unifier
 * https://github.com/wilddeer/Event-Burrito
 * Copyright Oleg Korsunsky | http://wd.dizaina.net/
 *
 * MIT License
 */
_N2.d('EventBurrito', function () {

    var clickTolerance = 10,
        noop = function () {
            return true;
        },
        isDragStarted = false; //Allow one drag at the same time

    /**
     * @memberOf _N2
     *
     * @param _this
     * @param options
     * @returns {{getClicksAllowed: function(): boolean, kill: kill}}
     * @constructor
     */
    function EventBurrito(_this, options) {

        var o = {
            preventDefault: true,
            preventScroll: false,
            mouse: true,
            axis: 'x',
            start: noop,
            move: noop,
            end: noop,
            click: noop
        };

        //merge user options into defaults
        _Assign(o, options);

        /**
         * Force IOS to use legacy touch events
         * @see https://bugs.webkit.org/show_bug.cgi?id=207687
         */
        var support = {
                pointerEvents: !!(!(n2const.isIOS && window.TouchEvent) && (window.PointerEvent || window.PointerEventsPolyfill))
            },
            start = {},
            diff = {},
            listenersToRemoveCBs = [],
            interactionListenersToRemoveCBs,
            isScrolling,
            isRealScrolling,
            eventType,
            clicksAllowed = true, //flag allowing default click actions (e.g. links)
            eventModel = (support.pointerEvents ? 1 : 0),
            events = [
                ['touchstart', 'touchmove', 'touchend', 'touchcancel'], //touch events
                ['pointerdown', 'pointermove', 'pointerup', 'pointercancel', 'pointerleave'], //pointer events
                ['mousedown', 'mousemove', 'mouseup', '', 'mouseleave'] //mouse events
            ],
            //some checks for different event types
            checks = [
                //touch events
                function (e) {
                    //skip the event if it's multitouch or pinch move
                    return (e.touches && e.touches.length > 1) || (e.scale && e.scale !== 1);
                },
                //pointer events
                function (e) {
                    //Skip it, if:
                    //1. event is not primary (other pointers during multitouch),
                    //2. left mouse button is not pressed,
                    //3. mouse drag is disabled and event is not touch
                    return !e.isPrimary || (e.buttons && e.buttons !== 1) || (!o.mouse && e.pointerType !== 'touch' && e.pointerType !== 'pen');
                },
                //mouse events
                function (e) {
                    //skip the event if left mouse button is not pressed
                    //in IE7-8 `buttons` is not defined, in IE9 LMB is 0
                    return (e.buttons && e.buttons !== 1);
                }
            ],
            checkTarget = function (target, event) {
                var tagName = target.tagName;
                return tagName === 'INPUT'
                    || tagName === 'TEXTAREA'
                    || tagName === 'SELECT'
                    || tagName === 'BUTTON'
                    || tagName === 'VIDEO'
                    || target.classList.contains('n2_container_scrollable')
                    || target.closest('.n2_container_scrollable');
            };

        function preventDefault(event) {
            event.preventDefault ? event.preventDefault() : event.returnValue = false;
        }

        function getDiff(event) {
            diff = {
                x: (eventType ? event.clientX : event.touches[0].clientX) - start.x,
                y: (eventType ? event.clientY : event.touches[0].clientY) - start.y,
                time: Date.now()
            };
        }

        function tStart(e, eType) {
            if (e.isPrimary !== undefined && !e.isPrimary) return;
            if (isDragStarted) return;

            interactionListenersToRemoveCBs = [];

            clicksAllowed = true;
            eventType = eType; //leak event type

            if (checks[eventType](e)) return;
            if (checkTarget(e.target, e)) return;

            if (e.type === 'pointerdown' && e.pointerType === 'mouse' && e.target.closest('.n2-ss-text')) {
                var removeEventListener = _addEventListenerWithRemover(e.target, 'click', function (newEvent) {
                    removeEventListener();
                    if (Math.abs(e.clientX - newEvent.clientX) < clickTolerance && Math.abs(e.clientY - newEvent.clientY) < clickTolerance) {
                        _dispatchEventSimple(e.target, 'n2click');
                    }
                });
                setTimeout(removeEventListener, 2000);
                return;
            }

            isDragStarted = true;

            //attach event listeners to the document, so that the slider
            //will continue to receive events wherever the pointer is
            if (eventType !== 0) {
                interactionListenersToRemoveCBs.push(_addEventListenerWithRemover(document, events[eventType][1], tMove, {
                    passive: false,
                    capture: true
                }));
            }

            interactionListenersToRemoveCBs.push(_addEventListenerWithRemover(document, events[eventType][2], tEnd, {
                passive: false,
                capture: true
            }));
            interactionListenersToRemoveCBs.push(_addEventListenerWithRemover(document, events[eventType][3], tEnd, {
                passive: false,
                capture: true
            }));
            interactionListenersToRemoveCBs.push(_addEventListenerWithRemover(body, events[eventType][4], tEndDocumentLeave, {
                passive: false,
                capture: true
            }));

            //fixes WebKit's cursor while dragging
            //if (o.preventDefault && eventType) preventDefault(event);

            //remember starting time and position
            start = {
                x: eventType ? e.clientX : e.touches[0].clientX,
                y: eventType ? e.clientY : e.touches[0].clientY,

                time: Date.now()
            };

            //reset
            isScrolling = undefined;
            isRealScrolling = false;
            diff = {x: 0, y: 0};

            o.start(e, start);

            tMove(e);
        }

        function tMove(event) {
            if (event.isPrimary !== undefined && !event.isPrimary) return;
            //if user is trying to scroll vertically -- do nothing
            if (o.axis === 'x') {
                if ((!o.preventScroll && isScrolling) || checks[eventType](event)) return;
            }
            if (checkTarget(event.target, event)) return;

            getDiff(event);

            if (Math.abs(diff.x) > clickTolerance || Math.abs(diff.y) > clickTolerance) {
                clicksAllowed = false; //if there was a move -- deny all the clicks before next tStart
            }

            //check whether the user is trying to scroll vertically
            if (isScrolling === undefined && eventType !== 2) {
                isScrolling = (Math.abs(diff.x) < Math.abs(diff.y)) && !o.preventScroll;
                if (isScrolling) {
                    return;
                }
            }

            var needPrevent = o.move(event, start, diff, isRealScrolling);
            if (needPrevent) {
                if (o.preventDefault) {
                    preventDefault(event); //Prevent scrolling
                }
            }
        }

        function tEndDocumentLeave(event) {
            if (event.target === event.currentTarget) {
                tEnd(event);
            }
        }

        function tEnd(event) {
            if (event.isPrimary !== undefined && !event.isPrimary) return;
            eventType && getDiff(event);

            //IE likes to focus links after touchend.
            //Since we don't want to disable link outlines completely for accessibility reasons,
            //we just blur it after touch and disable the outline for `:active` links in css.
            //This way the outline will remain visible when using keyboard.
            !clicksAllowed && event.target && event.target.blur && event.target.blur();


            for (var i = interactionListenersToRemoveCBs.length - 1; i >= 0; i--) {
                interactionListenersToRemoveCBs[i]();
            }
            interactionListenersToRemoveCBs = null;

            if (n2const.isFirefox) {
                isRealScrolling = false; // We are unable to prevent scroll on pointer events, so we need to switch slides when scroll happens during the interaction.
            }

            o.end(event, start, diff, isRealScrolling);
            isRealScrolling = false;
            isDragStarted = false;
        }

        function init() {
            //bind scroll
            listenersToRemoveCBs.push(_addEventListenerWithRemover(document, 'scroll', function () {
                if (window.nextendScrollFocus === undefined || !window.nextendScrollFocus) {
                    isRealScrolling = true;
                }
            }));

            if (eventModel === 1) {
                if (o.axis === 'y') {
                    _this.style.touchAction = 'pan-x';
                } else {
                    _this.style.touchAction = 'pan-y';
                }
            }

            //bind touchstart
            listenersToRemoveCBs.push(_addEventListenerWithRemover(_this, events[eventModel][0], function (e) {
                tStart(e, eventModel);
            }, {passive: false, capture: true}));

            if (eventModel === 0) {
                listenersToRemoveCBs.push(_addEventListenerWithRemover(_this, events[0][1], function (e) {
                    tMove(e, 0);
                }, {passive: false, capture: true}));
            }

            //prevent stuff from dragging when using mouse
            listenersToRemoveCBs.push(_addEventListenerWithRemover(_this, 'dragstart', preventDefault));

            //bind mousedown if necessary
            if (o.mouse && eventModel === 0) {
                listenersToRemoveCBs.push(_addEventListenerWithRemover(_this, events[2][0], function (e) {
                    tStart(e, 2);
                }));
            }

            //No clicking during touch
            listenersToRemoveCBs.push(_addEventListenerWithRemover(_this, 'click', function (event) {
                clicksAllowed ? o.click(event) : preventDefault(event);
            }));
        }

        init();

        //expose the API
        return {
            supportsPointerEvents: support.pointerEvents,
            getClicksAllowed: function () {
                return clicksAllowed;
            },
            kill: function () {
                for (var i = listenersToRemoveCBs.length - 1; i >= 0; i--) {
                    listenersToRemoveCBs[i]();
                }
            }
        }
    }

    return EventBurrito;
});(function () {

    var isTicking = false,
        lastTick = -1,
        ticks = new Set(),
        postTickCallbacks = new Set(),
        requestAnimationFrame = window.requestAnimationFrame || (function () {
            var timeLast = 0;

            return function (callback) {
                var timeCurrent = (new Date()).getTime(),
                    timeDelta = Math.max(0, 16 - (timeCurrent - timeLast));
                timeLast = timeCurrent + timeDelta;

                return setTimeout(function () {
                    callback(timeCurrent + timeDelta);
                }, timeDelta);
            };
        })();

    function tickStart(time) {
        lastTick = time;

        if (isTicking) {
            lastTick = time;
            requestAnimationFrame(tick);
        }
    }


    function tick(time) {

        if (isTicking && ticks.size === 0 && postTickCallbacks.size === 0) {
            lastTick = -1;
            isTicking = false;
        } else if (lastTick !== -1) {
            var delta = (time - lastTick) / 1000;
            if (delta !== 0) {

                ticks.forEach(function (callback) {
                    callback(delta);
                });

                postTickCallbacks.forEach(function (callback) {
                    callback();
                });
            }
        }

        continueTick(time);
    }

    function continueTick(time) {

        if (isTicking) {
            lastTick = time;
            requestAnimationFrame(tick);
        }
    }

    _N2.___Ticker = {
        addP: function (callback) {
            ticks.add(callback);

            if (!isTicking) {
                isTicking = true;
                requestAnimationFrame(tickStart);
            }
        },
        removeP: function (callback) {
            ticks.delete(callback);
        },
        add: function (callback) {
            postTickCallbacks.add(callback);

            if (!isTicking) {
                isTicking = true;
                requestAnimationFrame(tickStart);
            }
        },
        remove: function (callback) {
            postTickCallbacks.delete(callback);
        }
    };
})();_N2.d('Animation', function () {

    /**
     * @memberOf _N2
     *
     * @param toParams
     * @constructor
     */
    function Animation(toParams) {
        this._tickCallback = null;
        this._progress = 0;
        this._delayTimeout = false;
        this._delay = 0;
        this._duration = 4;
        this._timeScale = 1.0;
        this._isPlaying = false;
        this._startTime = 0;
        this._eventCallbacks = {};
        this._immediateRender = true;
        this._timeline = null;
        this._isCompleted = false;
        this._isStarted = false;
        this._isReversed = false;

        this.toParams = toParams;

        this.initParameters()
    }

    Animation.prototype.initParameters = function () {
        this.parseParameters(this.toParams);

        if (typeof this.toParams !== 'object') {
            this.paused(false);
        }
    };

    Animation.prototype.parseParameters = function (params) {
        if (params) {
            if (params.delay) {
                this.delay(params.delay);
                delete params.delay;
            }
            if (params.duration !== undefined) {
                this.duration(params.duration);
                delete params.duration;
            }
            if (params.onComplete) {
                this.eventCallback('onComplete', params.onComplete);
                delete params.onComplete;
            }
            if (params.onStart) {
                this.eventCallback('onStart', params.onStart);
                delete params.onStart;
            }
            if (params.onUpdate) {
                this.eventCallback('onUpdate', params.onUpdate);
                delete params.onUpdate;
            }
            if (params.immediateRender) {
                this._immediateRender = params.immediateRender;
                delete params.immediateRender;
            }
            if (params.paused) {
                this.paused(true);
                delete params.paused;
            }
        }
    };

    Animation.prototype.setTimeline = function (timeline) {
        this._timeline = timeline;
    };

    Animation.prototype._tick = function (delta) {
        var pr = this._progress;
        if (!this._isReversed) {
            this._progress += delta / this._duration * this._timeScale;
            if (pr == 0 || !this._isStarted) {
                this._onStart();
            } else {
                if (this._progress >= 1) {
                    this._progress = 1;
                    this._isPlaying = false;
                    _N2.___Ticker.removeP(this.getTickCallback());
                    this._onUpdate();
                    this._onComplete();
                } else {
                    this._onUpdate();
                }
            }
        } else {
            this._progress -= delta / this._duration * this._timeScale;
            if (pr == 1 || !this._isStarted) {
                this._onReverseStart();
            } else {
                if (this._progress <= 0) {
                    this._progress = 0;
                    this._isPlaying = false;
                    _N2.___Ticker.removeP(this.getTickCallback());
                    this._onUpdate();
                    this._onReverseComplete();
                } else {
                    this._onUpdate();
                }
            }
        }
    };

    Animation.prototype._onStart = function () {
        this._isStarted = true;
        this._isCompleted = false;
        this._dispatch('onStart');
        this._onUpdate();
    };

    Animation.prototype._onUpdate = function () {

        this._dispatch('onUpdate');
    };

    Animation.prototype._onComplete = function () {
        this._isCompleted = true;
        this._onUpdate();
        this._dispatch('onComplete');
    };

    Animation.prototype._onReverseComplete = function () {
        this._isCompleted = true;
        this._isReversed = false;
        this._onUpdate();
        this._dispatch('onReverseComplete');
    };

    Animation.prototype._onReverseStart = function () {
        this._isStarted = true;
        this._isCompleted = false;
        this._dispatch('onReverseStart');
        this._onUpdate();
    };

    Animation.prototype.getTickCallback = function () {
        if (!this._tickCallback) {
            var that = this;
            this._tickCallback = function () {
                that._tick.apply(that, arguments);
            };
        }
        return this._tickCallback;
    };

    Animation.prototype._clearDelayTimeout = function () {
        if (this._delayTimeout) {
            clearTimeout(this._delayTimeout);
            this._delayTimeout = false;
        }
    };

    Animation.prototype._timeToProgress = function (time) {
        return time / this._duration * this._timeScale;
    };


    Animation.prototype.delay = function () {
        if (arguments.length > 0) {
            var delay = parseFloat(arguments[0]);
            if (isNaN(delay) || delay == Infinity || !delay) {
                delay = 0;
            }
            this._delay = Math.max(0, delay);
            return this;
        }
        return this._delay;
    };

    Animation.prototype.duration = function () {
        if (arguments.length > 0) {
            var duration = parseFloat(arguments[0]);
            if (isNaN(duration) || duration == Infinity || !duration) {
                duration = 0;
            }
            this._duration = Math.max(0, duration);
            return this;
        }
        return this._duration;
    };

    Animation.prototype.eventCallback = function (type) {
        if (arguments.length > 3) {
            this._eventCallbacks[type] = [arguments[1], arguments[2], arguments[3]];
        } else if (arguments.length > 2) {
            this._eventCallbacks[type] = [arguments[1], arguments[2], this];
        } else if (arguments.length > 1) {
            this._eventCallbacks[type] = [arguments[1], [], this];
        }
        return this._eventCallbacks[type];
    };

    Animation.prototype.pause = function () {
        this._isPlaying = false;
        _N2.___Ticker.removeP(this.getTickCallback());
        if (arguments.length > 0) {
            if (arguments[0] != null) {
                this.progress(this._timeToProgress(arguments[0]));
            }
        }
        return this;
    };

    Animation.prototype.paused = function () {
        if (arguments.length > 0) {
            if (arguments[0]) {
                if (this._isPlaying) {
                    this.pause();
                }
            } else {
                if (!this._isPlaying) {
                    this.play();
                }
            }
            return this;
        }
        return !this._isPlaying;
    };

    Animation.prototype.play = function () {
        var startDelay = true;
        if (arguments.length > 0) {
            if (arguments[0] != null) {
                startDelay = false;
                this._progress = this._timeToProgress(arguments[0]);
            }
        }

        this._play(startDelay);
    };

    Animation.prototype._play = function (startDelay) {

        if (this._progress < 1) {
            if (this._progress == 0 && startDelay && this._delay > 0) {
                if (!this._delayTimeout) {
                    var that = this;
                    this._delayTimeout = setTimeout(function () {
                        that.__play.apply(that, arguments);
                    }, this._delay * 1000);
                }
            } else {
                this.__play();
            }
        } else if (!this._isCompleted) {
            if (!this._isReversed) {
                this._onComplete();
            } else {
                this._onReverseComplete();
            }
        }
    };

    Animation.prototype.__play = function () {
        this._clearDelayTimeout();
        if (!this._isPlaying) {
            _N2.___Ticker.addP(this.getTickCallback());
            this._isPlaying = true;
        }
    };

    Animation.prototype.progress = function () {
        if (arguments.length > 0) {
            var progress = parseFloat(arguments[0]);
            if (isNaN(progress)) {
                progress = 0;
            }
            progress = Math.min(1, Math.max(0, progress));

            if (1 || this._progress != progress) {
                this._progress = progress;
                if (!this._isPlaying) {
                    if (!this._isStarted) {
                        this._onStart();
                    }
                    this._onUpdate();
                }
            }
            return this;
        }
        return this._progress;
    };

    Animation.prototype.reverse = function () {
        this._isReversed = true;
        if (this.progress() != 0) {
            this.play();
        }
    };

    Animation.prototype.restart = function () {
        if (arguments.length > 0) {
            if (arguments[0]) {
                // restart with delay
                this.pause(0);
                this.play();
                return this;
            }
        }
        this.play(0);
        return this;
    };

    Animation.prototype.seek = function (time) {
        if (time != null) {
            this._progress = this._timeToProgress(arguments[0]);
            if (!this._isPlaying) {
                this._onUpdate();
            }
        }
    };

    Animation.prototype.startTime = function () {
        if (arguments.length > 0) {
            var startTime = parseFloat(arguments[0]);
            if (isNaN(startTime)) {
                startTime = 0;
            }
            this._startTime = Math.max(0, startTime);
            return this;
        }
        return this._startTime;
    };

    Animation.prototype.timeScale = function () {
        if (arguments.length > 0) {
            var timeScale = parseFloat(arguments[0]);
            if (isNaN(timeScale)) {
                timeScale = 1;
            }
            timeScale = Math.max(0.01, timeScale);

            if (this._timeScale != timeScale) {
                this._timeScale = timeScale;
            }
            return this;
        }
        return this._timeScale;
    };

    Animation.prototype._dispatch = function (type) {
        if (typeof this._eventCallbacks[type] == 'object') {
            this._eventCallbacks[type][0].apply(this._eventCallbacks[type][2], this._eventCallbacks[type][1]);
        }
    };

    Animation.prototype.totalDuration = function () {
        if (arguments.length > 0) {
            var totalDuration = parseFloat(arguments[0]);
            if (isNaN(totalDuration)) {
                totalDuration = 0;
            }
            totalDuration = Math.max(0, totalDuration);

            this.timeScale(this._duration / totalDuration);
            return this;
        }

        return this._duration * this._timeScale;
    };

    Animation.prototype.reset = function () {
        this._isCompleted = false;
        this._isStarted = false;
        this.progress(0);
    };

    return Animation;
});_N2.d('Tween', function () {
    var MODE = {
            FROMTO: 2,
            TO: 3
        },
        isIterable = function (value) {
            return Symbol.iterator in Object(value);
        }

    /**
     * @memberOf _N2
     *
     * @param target
     * @param duration
     * @constructor
     */
    function Tween(target, duration) {
        this.ease = 'linear';
        this._tweenContainer = null;
        this._setContainer = null;
        this._roundProps = {};
        var fromParams, toParams;
        switch (arguments.length) {
            case 4:
                fromParams = arguments[2];
                toParams = arguments[3];
                this._mode = MODE.FROMTO;
                break;
            default:
                this._mode = MODE.TO;
                fromParams = {};
                toParams = arguments[2];
        }

        if (!isIterable(target)) {
            target = [target];
        }

        this._target = target;

        this.fromParams = fromParams;

        _N2.Animation.call(this, toParams);

        this.parseParameters({
            duration: duration
        });

        if (this._mode === MODE.FROMTO && this._immediateRender) {
            if (this._tweenContainer === null) {
                this._makeTweenContainer(this.fromParams, this.toParams);
            }
            for (var k in this._tweenContainer) {
                var tween = this._tweenContainer[k];
                this._target.forEach(function (targetSMW) {
                    targetSMW[k] = tween.unit ? tween.startValue + tween.unit : tween.startValue
                });
            }
            for (var k in this._setContainer) {
                var tween = this._setContainer[k];
                this._target.forEach(function (targetSMW) {
                    targetSMW[k] = tween.unit ? tween.endValue + tween.unit : tween.endValue
                });
            }
        }
    }

    Tween.prototype = Object.create(_N2.Animation.prototype);
    Tween.prototype.constructor = Tween;

    Tween.prototype.initParameters = function () {

        this.parseParameters(this.fromParams);

        _N2.Animation.prototype.initParameters.apply(this, arguments);
    };

    Tween.prototype.parseParameters = function (params) {
        if (params) {
            if (params.ease) {
                this.ease = params.ease;
                delete params.ease;
            }

            _N2.Animation.prototype.parseParameters.apply(this, arguments);
        }
    };

    Tween.prototype._onStart = function () {
        if (this._tweenContainer === null) {
            this._makeTweenContainer(this.fromParams, this.toParams);
        }

        for (var k in this._setContainer) {
            var tween = this._setContainer[k];

            this._target.forEach(function (targetSMW) {
                targetSMW[k] = tween.unit ? tween.endValue + tween.unit : tween.endValue
            });
        }

        _N2.Animation.prototype._onStart.call(this);
    };

    Tween.prototype._onUpdate = function () {
        for (var k in this._tweenContainer) {
            var tween = this._tweenContainer[k],
                progress = _N2.Easings[this.ease] ? _N2.Easings[this.ease](this._progress) : this._progress,
                value = tween.startValue + tween.range * progress;

            if (this._roundProps[k]) {
                value = Math.round(((value * 10) | 0) / 10);
            }

            this._target.forEach(function (targetSMW) {
                targetSMW[k] = tween.unit ? value + tween.unit : value
            });
        }
        _N2.Animation.prototype._onUpdate.call(this);
    };

    Tween.prototype.initRoundProps = function (data) {
        var s = data.split(',');
        for (var i = 0; i < s.length; i++) {
            this._roundProps[s[i]] = true;
        }
    };

    function parseUnit(str) {
        var out = [str, '']

        str = String(str)
        var num = parseFloat(str);
        if (!isNaN(num)) {
            out[0] = num;
            out[1] = str.match(/[\d.\-\+]*\s*(.*)/)[1] || '';
        }
        return out
    }

    function makeTransitionData(element, property, startValue, endValue) {
        if (startValue === undefined) {
            startValue = element[0][property];
        }
        if (endValue === undefined) {
            endValue = element[0][property];
        }

        startValue = parseUnit(startValue);
        endValue = parseUnit(endValue);

        var range = 0;
        if (endValue[1] !== '' && startValue[1] !== endValue[1]) {
            startValue[0] = 0;
            startValue[1] = endValue[1];
        }

        if (typeof startValue[0] === 'number' && typeof endValue[0] === 'number') {
            range = endValue[0] - startValue[0];
        }

        return {
            startValue: startValue[0],
            endValue: endValue[0],
            unit: endValue[1],
            range: range
        }
    }

    Tween.prototype._makeTweenContainer = function (from, to) {

        if (from.snap !== undefined) {
            this.initRoundProps(from.snap);
            delete from.snap;
        }

        this._setContainer = {};
        this._tweenContainer = {};

        if (to.snap !== undefined) {
            this.initRoundProps(to.snap);
            delete to.snap;
        }

        for (var k in to) {
            var container = makeTransitionData(this._target, k, from[k], to[k]);
            if (container.range == 0) {
                this._setContainer[k] = container;
            } else {
                this._tweenContainer[k] = container;
            }
        }
    };

    Tween.to = function (element, duration, to) {
        var tween = new Tween(element, duration, to);
        if (to.paused === undefined || !to.paused) {
            tween.play();
        }
        return tween;
    };

    Tween.fromTo = function (element, duration, from, to) {
        var tween = new Tween(element, duration, from, to);
        if (to.paused === undefined || !to.paused) {
            tween.play();
        }
        return tween;
    };

    _N2.___Tween = Tween;

    return Tween;
});_N2.d('Timeline', function () {

    /**
     * @memberOf _N2
     *
     * @param params
     * @constructor
     */
    function Timeline(params) {
        this.originalParams = _Assign({}, params);
        this._tweens = [];
        _N2.Animation.call(this, params);
        this._duration = 0;
    }

    Timeline.prototype = Object.create(_N2.Animation.prototype);
    Timeline.prototype.constructor = Timeline;

    Timeline.prototype._onUpdate = function () {
        if (this.tweensContainer) {

            for (var i = 0; i < this.tweensContainer.length; i++) {
                var tweenContainer = this.tweensContainer[i];
                var currentProgress = Math.min(1, (this._progress - tweenContainer.startProgress) / (tweenContainer.endProgress - tweenContainer.startProgress));
                if (tweenContainer.tween._isCompleted && currentProgress <= tweenContainer.endProgress) {
                    tweenContainer.tween.reset();
                }

                if (!tweenContainer.tween._isStarted && currentProgress >= 0 && tweenContainer.tween.progress() == 0) {
                    tweenContainer.tween._onStart();
                }
                if (tweenContainer.tween._isStarted) {
                    if (currentProgress == 1 && !tweenContainer.tween._isCompleted) {
                        tweenContainer.tween.progress(currentProgress);
                        tweenContainer.tween._onComplete();
                    } else if (currentProgress >= 0 && currentProgress < 1) {
                        tweenContainer.tween.progress(currentProgress);
                    } else if (currentProgress < 0 && tweenContainer.tween.progress() != 0) {
                        tweenContainer.tween.progress(0);
                    }
                }
            }
        }
        _N2.Animation.prototype._onUpdate.call(this);
    };

    Timeline.prototype.addTween = function (tween) {
        tween.pause();
        tween.setTimeline(this);
        var position = 0;
        if (arguments.length > 1) {
            position = this._parsePosition(arguments[1]);
        } else {
            position = this._parsePosition();
        }

        var delay = tween.delay();
        if (delay > 0) {
            position += delay;
            tween.delay(0);
        }

        tween.startTime(position);
        this._tweens.push(tween);
        var duration = tween.totalDuration() + position;
        if (duration > this._duration) {
            this._duration = duration;
        }
        this.makeCache();
    };

    Timeline.prototype.clear = function () {
        if (!this.paused()) {
            this.pause();
        }
        Timeline.call(this, this.originalParams);
    };

    Timeline.prototype.add = function (tween, position) {
        this.addTween(tween, position);
    };

    Timeline.prototype.set = function (element, to, position) {
        this.addTween(_N2.___Tween.to(element, 0.05, to), position);
    };

    Timeline.prototype.to = function (element, duration, to, position) {
        to.paused = true;
        this.addTween(_N2.___Tween.to(element, duration, to), position);
    };

    Timeline.prototype.fromTo = function (element, duration, from, to, position) {
        to.paused = true;
        this.addTween(_N2.___Tween.fromTo(element, duration, from, to), position);
    };

    Timeline.prototype._play = function () {
        if (this._progress == 0) {

            for (var i = 0; i < this._tweens.length; i++) {
                this._tweens[i].pause(0);

            }
        }
        _N2.Animation.prototype._play.apply(this, arguments);
    };

    Timeline.prototype._parsePosition = function () {
        var positionString = '+=0';
        if (arguments.length > 0 && arguments[0] !== undefined && !isNaN(arguments[0])) {
            positionString = arguments[0];
        }
        var position = 0;

        switch (typeof positionString) {
            case 'string':
                switch (positionString.substr(0, 2)) {
                    case'+=':
                        position = this.duration() + parseFloat(positionString.substr(2));
                        break;
                    case'-=':
                        position = this.duration() - parseFloat(positionString.substr(2));
                        break;
                }
                break;
            default:
                position = parseFloat(positionString);
        }

        return Math.max(0, position);
    };

    Timeline.prototype.makeCache = function () {
        var totalDuration = this.totalDuration();
        this.tweensContainer = [];
        for (var i = 0; i < this._tweens.length; i++) {
            var tween = this._tweens[i];

            var startProgress = tween.startTime() / totalDuration,
                endProgress = (tween.startTime() + tween.totalDuration()) / totalDuration;
            this.tweensContainer.push({
                tween: tween,
                startProgress: startProgress,
                endProgress: endProgress,
                range: endProgress - startProgress
            });
        }
    };

    _N2.___Timeline = Timeline;

    return Timeline;
});_N2.d('Easings', function () {
    var baseEasings = {
        Sine: function (p) {
            return 1 - Math.cos(p * Math.PI / 2);
        },
        Circ: function (p) {
            return 1 - Math.sqrt(1 - p * p);
        },
        Elastic: function (p) {
            return p === 0 || p === 1 ? p :
                -Math.pow(2, 8 * (p - 1)) * Math.sin(((p - 1) * 80 - 7.5) * Math.PI / 15);
        },
        Back: function (p) {
            return p * p * (3 * p - 2);
        },
        Bounce: function (p) {
            var pow2,
                bounce = 4;

            while (p < ((pow2 = Math.pow(2, --bounce)) - 1) / 11) {
            }
            return 1 / Math.pow(4, 3 - bounce) - 7.5625 * Math.pow((pow2 * 3 - 2) / 22 - p, 2);
        }
    };

    ["Quad", "Cubic", "Quart", "Quint", "Expo"].forEach(function (name, i) {
        baseEasings[name] = function (p) {
            return Math.pow(p, i + 2);
        };
    });

    var Easing = {};
    for (var name in baseEasings) {
        (function (name, easeIn) {
            Easing["easeIn" + name] = easeIn;
            Easing["easeOut" + name] = function (p) {
                return 1 - easeIn(1 - p);
            };
            Easing["easeInOut" + name] = function (p) {
                return p < 0.5 ?
                    easeIn(p * 2) / 2 :
                    1 - easeIn(p * -2 + 2) / 2;
            };
        })(name, baseEasings[name]);
    }

    return Easing;
});
_N2.d('nextend-frontend');_N2.d('n2');})(window);
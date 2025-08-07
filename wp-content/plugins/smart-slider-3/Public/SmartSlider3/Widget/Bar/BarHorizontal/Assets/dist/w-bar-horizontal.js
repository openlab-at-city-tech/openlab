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
});_N2.d('SmartSliderWidgetBarHorizontal', 'SmartSliderWidget', function () {
    "use strict";

    /**
     * @memberOf _N2
     *
     * @param slider
     * @param parameters
     *
     * @constructor
     * @augments {_N2.SmartSliderWidget}
     */
    function SmartSliderWidgetBarHorizontal(slider, parameters) {

        this.parameters = parameters;

        _N2.SmartSliderWidget.prototype.constructor.call(this, slider, 'bar', '.nextend-bar');
    }

    SmartSliderWidgetBarHorizontal.prototype = Object.create(_N2.SmartSliderWidget.prototype);
    SmartSliderWidgetBarHorizontal.prototype.constructor = SmartSliderWidgetBarHorizontal;

    SmartSliderWidgetBarHorizontal.prototype.onStart = function (parameters) {

        this.tween = null;

        this.innerBar = this.widget.querySelector('div');

        this.___innerBarSMW = _N2.MW.___getSMW(this.innerBar);

        var event = 'click';
        if (this.slider.hasTouch()) {
            event = 'n2click';
        }

        _addEventListener(this.widget, 'click', (function (e) {
            var element = this.slider.sliderElement.querySelector('.n2-ss-slide-active .n2-ss-layers-container');
            const isLink = e.target?.href;
            if (element && !isLink) {
                _dispatchEventSimple(element, event);
            }
        }).bind(this));


        this.slider.stages.done('BeforeShow', this.onBeforeShow.bind(this));
    };

    SmartSliderWidgetBarHorizontal.prototype.hasContent = function (slide) {

        if (this.parameters.showTitle && (slide.getTitle() !== '' || this.parameters.slideCount)) {
            return true;
        } else if (this.parameters.showDescription && (slide.getDescription() !== '' || this.parameters.slideCount)) {
            return true;
        }

        return false;
    };

    SmartSliderWidgetBarHorizontal.prototype.onBeforeShow = function () {

        this.onSliderSwitchTo({
            detail: {
                targetSlide: this.slider.currentSlide
            }
        });

        if (this.parameters.animate) {
            _addEventListener(this.slider.sliderElement, 'mainAnimationStart', this.onSliderSwitchToAnimateStart.bind(this));
        } else {
            _addEventListener(this.slider.sliderElement, 'SlideWillChange', this.onSliderSwitchTo.bind(this));
        }
    };

    SmartSliderWidgetBarHorizontal.prototype.renderBarContent = function (slide) {
        var html = '';
        if (this.parameters.showTitle && (slide.getTitle() !== '' || this.parameters.slideCount)) {
            var title;
            if (this.parameters.slideCount) {
                title = slide.index + 1;
            } else {
                title = slide.getTitle();
            }
            html += '<span class="' + this.parameters.fontTitle + '">' + title + '</span>';
        }
        if (this.parameters.showDescription && (slide.getDescription() !== '' || this.parameters.slideCount)) {
            var description;
            if (this.parameters.slideCount) {
                description = slide.slider.slides.length;
            } else {
                description = slide.getDescription();
            }
            html += '<span class="' + this.parameters.fontDescription + '">' + (html === '' ? '' : this.parameters.separator) + description + '</span>';
        }

        if (html === '') {
            return '<span class="' + this.parameters.fontDescription + '">&nbsp;</span>';
        }
        return html;
    };

    SmartSliderWidgetBarHorizontal.prototype.onSliderSwitchTo = function (e) {

        var targetSlide = e.detail.targetSlide.slides[0],
            hasContent = this.hasContent(targetSlide);

        if (hasContent) {
            this.innerBar.innerHTML = this.renderBarContent(targetSlide);
            this.setCursor(targetSlide.hasLink());
        }

        this.slider.widgets.setState('hide.bar', !hasContent);
    };

    SmartSliderWidgetBarHorizontal.prototype.onSliderSwitchToAnimateStart = function () {

        if (this.___innerBarOriginalOpacity === undefined) {
            this.___innerBarOriginalOpacity = _NodeGetStyle(this.innerBar, 'opacity');
            this.___innerBarSMW.opacity = this.___innerBarOriginalOpacity;
        }

        var promise = new Promise((function (resolve) {
            if (this.tween) {
                this.tween.pause();
            }
            _N2.___Tween.to(this.___innerBarSMW, 0.3, {
                opacity: 0,
                onComplete: function () {
                    resolve();
                }
            });
        }).bind(this));

        _addEventListenerOnce(this.slider.sliderElement, 'mainAnimationComplete', this.onSliderSwitchToAnimateEnd.bind(this, promise));
    };

    SmartSliderWidgetBarHorizontal.prototype.onSliderSwitchToAnimateEnd = function (promise, e) {

        var targetSlide = this.slider.slides[e.detail.currentSlideIndex];
        promise.then((function () {
            var hasContent = this.hasContent(targetSlide);
            if (hasContent) {
                var innerBar = this.innerBar.cloneNode(true);
                _NodeRemove(this.innerBar);

                this.setCursor(targetSlide.hasLink());
                innerBar.innerHTML = this.renderBarContent(targetSlide);
                this.widget.appendChild(innerBar);
                this.innerBar = innerBar;
                this.___innerBarSMW = _N2.MW.___getSMW(this.innerBar);
                this.___innerBarSMW.opacity = 0;

                this.tween = _N2.___Tween.to(this.___innerBarSMW, 0.3, {
                    opacity: this.___innerBarOriginalOpacity
                });
            }

            this.slider.widgets.setState('hide.bar', !hasContent);
        }).bind(this));
    };

    SmartSliderWidgetBarHorizontal.prototype.setCursor = function (hasLink) {

        this.___innerBarSMW.cursor = hasLink ? 'pointer' : 'inherit';
    };

    return SmartSliderWidgetBarHorizontal;
});})(window);
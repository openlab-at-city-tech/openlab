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
});_N2.d('SmartSliderBackgrounds', function () {

    /**
     * @memberOf _N2
     *
     * @param slider
     * @constructor
     */
    function SmartSliderBackgrounds(slider) {
        this.device = null;

        this.slider = slider;
        this.hasFixed = false;

        this.lazyLoad = parseInt(slider.parameters.lazyLoad);
        this.lazyLoadNeighbor = parseInt(slider.parameters.lazyLoadNeighbor);

        this.promise = new Promise((function (resolve) {
            this.resolve = resolve;
        }).bind(this));

        this.slider.stages.done('Resized', this.onResized.bind(this));
        this.slider.stages.done('StarterSlide', this.onStarterSlide.bind(this));
    }

    SmartSliderBackgrounds.prototype.preLoad = function (arrayOfPromises) {

        Promise.all(arrayOfPromises)
            .then(this.resolve);
    };

    /**
     *
     * @returns {_N2.SmartSliderSlideBackground[]}
     */
    SmartSliderBackgrounds.prototype.getBackgroundImages = function () {
        var images = [];
        for (var i = 0; i < this.slider.realSlides.length; i++) {
            images.push(this.slider.realSlides[i].background);
        }
        return images;
    };

    SmartSliderBackgrounds.prototype.onResized = function () {
        this.onSlideDeviceChanged(this.slider.responsive.getDeviceMode());

        _addEventListener(this.slider.sliderElement, 'SliderDevice', (function (e) {
            this.onSlideDeviceChanged(e.detail.device);
        }).bind(this));

    };

    SmartSliderBackgrounds.prototype.onStarterSlide = function () {
        if (this.lazyLoad === 1) {
            this.preLoadSlides = this.preloadSlidesLazyNeighbor;

            this.preLoad(this.preLoadSlides(this.slider.getVisibleSlides(this.slider.currentSlide)));
        } else if (this.lazyLoad === 2) { // delayed
            this.preLoadSlides = this._preLoadSlides;
            this.slider.stages.done('SlidesReady', (function () {
                _N2.r('windowLoad', this.preLoadAll.bind(this));
            }).bind(this));

            this.preLoad(this.preLoadSlides(this.slider.getVisibleSlides(this.slider.currentSlide)));
        } else {
            this.preLoadSlides = this._preLoadSlides;

            this.preLoad(this.preLoadAll());
        }

        _addEventListener(this.slider.sliderElement, 'visibleSlidesChanged', this.onVisibleSlidesChanged.bind(this));
    };

    SmartSliderBackgrounds.prototype.onVisibleSlidesChanged = function () {

        if (this.lazyLoad === 1 || this.lazyLoad === 2) {
            this.preLoadSlides(this.slider.getVisibleSlides());
        }
    };

    SmartSliderBackgrounds.prototype.onSlideDeviceChanged = function (device) {
        this.device = device;
        for (var i = 0; i < this.slider.visibleRealSlides.length; i++) {
            if (this.slider.visibleRealSlides[i].background) {
                this.slider.visibleRealSlides[i].background.onSlideDeviceChanged(device);
            }
        }
    };

    SmartSliderBackgrounds.prototype.preLoadAll = function () {
        var promises = [];
        for (var i = 0; i < this.slider.visibleRealSlides.length; i++) {
            promises.push(this.slider.visibleRealSlides[i].preLoad());
        }
        return promises;
    };

    SmartSliderBackgrounds.prototype._preLoadSlides = function (slides) {
        var promises = [];

        for (var i = 0; i < slides.length; i++) {
            promises.push(slides[i].preLoad());
        }

        return promises;
    };

    SmartSliderBackgrounds.prototype.preloadSlidesLazyNeighbor = function (slides) {
        var promises = this._preLoadSlides(slides);

        if (this.lazyLoadNeighbor) {
            var j = 0,
                previousSlide = slides[0].getPrevious(),
                nextSlide = slides[slides.length - 1].getNext();

            while (j < this.lazyLoadNeighbor) {
                if (previousSlide) {
                    promises.push(previousSlide.preLoad());
                    previousSlide = previousSlide.getPrevious();
                }
                if (nextSlide) {
                    promises.push(nextSlide.preLoad());
                    nextSlide = nextSlide.getNext();
                }
                j++;
            }
        }

        if (this.slider.stages.resolved('Visible')) {
            for (var i = 0; i < promises.length; i++) {
                this.slider.___loaderElement.addPromise(promises[i]);
            }
        }

        return promises;
    };

    return SmartSliderBackgrounds;
});function fixCriticalCSS(id) {
    var criticalCSSElement = document.getElementById(id);
    if (criticalCSSElement && criticalCSSElement.sheet) {
        var sheet = criticalCSSElement.sheet, i;
        for (i = sheet.cssRules.length - 1; i >= 0; i--) {
            if (sheet.cssRules[i].selectorText && sheet.cssRules[i].selectorText.includes('div#n2-ss-')) {
                sheet.deleteRule(i);
            }
        }
    }
}

/**
 * Fix for LiteSpeed cache Critical CSS feature
 * @see SSDEV-2913
 */
fixCriticalCSS('litespeed-optm-css-rules');
/**
 * Fix for WPRocket  Optimize CSS Delivery feature
 * @see SSDEV-2984
 */
fixCriticalCSS('rocket-critical-css');_N2.d('SmartSliderLoad', function () {

    var startedAt = performance.now();

    /**
     * @memberOf _N2
     *
     * @param {_N2.SmartSliderAbstract} slider
     * @constructor
     */
    function SmartSliderLoad(slider) {

        /**
         * @type {_N2.SmartSliderAbstract}
         */
        this.slider = slider;
    }

    SmartSliderLoad.prototype.start = function () {

        this.showSlider();
    };

    SmartSliderLoad.prototype.loadLayerImages = function () {

        var promises = [];
        this.slider.sliderElement.querySelectorAll('.n2-ss-layers-container')
            .forEach(function (el) {
                _N2.ImagesLoaded(el)
                promises.push(_N2.ImagesLoaded(el));
            });

        return Promise.all(promises);
    };

    SmartSliderLoad.prototype.showSlider = function () {

        this.slider.stages.done('ResizeFirst', this.stage1.bind(this));
    };

    SmartSliderLoad.prototype.stage1 = function () {

        this.slider.responsive.isReadyToResize = true;

        this.stage2();
    };

    SmartSliderLoad.prototype.stage2 = function () {
        requestAnimationFrame((function () {
            this.slider.responsive.doResize();

            this.slider.finalizeStarterSlide();

            var promise = Promise.all([this.slider.backgrounds.promise, this.loadLayerImages(), this.slider.stages.get('Fonts').getPromise()])
                    .finally(this.stage3.bind(this)),
                loaderTimeout = setTimeout((function () {
                    this.slider.___loaderElement.addPromise(promise);
                }).bind(this), Math.max(1, this.slider.parameters.loadingTime - (performance.now() - startedAt)));

            promise.finally(clearTimeout.bind(null, loaderTimeout));
        }).bind(this));
    };

    SmartSliderLoad.prototype.stage3 = function () {

        /**
         * We need to do an another resize as the images are loaded.
         */
        this.slider.responsive.doResize();
        this.slider.stages.resolve('LayerAnimations')
    

        this.slider.stages.done('LayerAnimations', this.stage4.bind(this));
    };

    SmartSliderLoad.prototype.stage4 = function () {

        this.slider.stages.resolve('BeforeShow');
        this.slider.widgets.onReady();

        _N2.MW.___addCallback((function () {
            _NodeAddClass(this.slider.responsive.alignElement, 'n2-ss-align-visible');

            _NodeAddClass(this.slider.sliderElement, 'n2-ss-loaded');
            _NodeRemoveClass(this.slider.sliderElement, 'n2notransition');

            requestAnimationFrame(function () {
                _dispatchEventSimple(window, 'scroll'); // To force other sliders to recalculate the scroll position
            });

            _requestIdleCallback(this.slider.startVisibilityCheck.bind(this.slider), {
                timeout: 2000
            });
        }).bind(this));

        this.slider.stages.resolve('Show');
    };

    return SmartSliderLoad;
});_N2.d('SmartSliderPlugins', function () {

    function Plugins(slider) {
        this.slider = slider;
        this.plugins = {};
    }

    Plugins.prototype.add = function (name, plugin) {
        this.plugins[name] = new plugin(this.slider);
    };

    Plugins.prototype.get = function (name) {
        return this.plugins[name] || false;
    };

    var plugins = {},
        sliders = [];

    /**
     * @memberOf _N2
     * @alias SmartSliderPlugins
     */
    return {
        addPlugin: function (name, plugin) {
            for (var i = 0; i < sliders.length; i++) {
                sliders[i].plugins.add(name, plugin);
            }
            plugins[name] = plugin;
        },
        addSlider: function (slider) {
            if (slider.plugins === undefined) {
                slider.plugins = new Plugins(slider);
                for (var k in plugins) {
                    slider.plugins.add(k, plugins[k]);
                }
            }
            sliders.push(slider);
        }
    };
});_N2.d('ScrollTracker', function () {

    /**
     * @memberOf _N2
     *
     * @todo Might be able to refactor with IntersectionObserver
     *
     * @constructor
     */
    function ScrollTracker() {
        this.started = false;
        this.items = [];
        this.onScrollCallback = this.onScroll.bind(this);
    }

    /**
     *
     * @param {Element} el
     * @param mode
     * @param onVisible
     * @param onHide
     */
    ScrollTracker.prototype.add = function (el, mode, onVisible, onHide) {
        var item = {
            el: el,
            mode: mode,
            onVisible: onVisible,
            onHide: onHide,
            state: 'unknown'
        };
        this.items.push(item);
        this._onScroll(item, Math.max(html.clientHeight, window.innerHeight));

        if (!this.started) {
            this.start();
        }
    };

    ScrollTracker.prototype.start = function () {
        if (!this.started) {
            window.addEventListener('scroll', this.onScrollCallback, {
                capture: true,
                passive: true
            });
            this.started = true;
        }
    };

    ScrollTracker.prototype.onScroll = function (e) {
        var viewHeight = Math.max(html.clientHeight, window.innerHeight);

        for (var i = 0; i < this.items.length; i++) {
            this._onScroll(this.items[i], viewHeight);
        }
    };

    ScrollTracker.prototype._onScroll = function (item, viewHeight) {
        var rect = item.el.closest('.n2-section-smartslider').getBoundingClientRect(),
            isBigPlayer = rect.height > viewHeight * 0.7,
            isVisible = true;

        if (item.mode === 'partly-visible') {
            if ((isBigPlayer && (rect.bottom < 0 || rect.top >= rect.height))) {
                isVisible = false;
            } else if (!isBigPlayer && (rect.bottom - rect.height < 0 || rect.top - viewHeight + rect.height >= 0)) {
                isVisible = false;
            }
        } else if (item.mode === 'not-visible') {
            isVisible = rect.top - viewHeight < 0 && rect.top + rect.height > 0;
        }

        if (isVisible === false) {
            if (item.state !== 'hidden') {
                if (typeof item.onHide === 'function') {
                    item.onHide();
                }
                item.state = 'hidden';
            }
        } else {
            if (item.state !== 'visible') {
                if (typeof item.onVisible === 'function') {
                    item.onVisible();
                }
                item.state = 'visible';
            }
        }

    };

    return new ScrollTracker();
});_N2.d('SmartSliderApi', function () {

    /**
     * @memberOf _N2
     *
     * @constructor
     */
    function SmartSliderApi() {
        this.sliders = {};
        this.readys = {};
        this.eventListeners = {};

    }

    SmartSliderApi.prototype.makeReady = function (id, slider) {
        this.sliders[id] = slider;
        if (this.readys[id] !== undefined) {
            for (var i = 0; i < this.readys[id].length; i++) {
                this.readys[id][i].call(slider, slider, slider.sliderElement);
            }
        }
    };

    SmartSliderApi.prototype.ready = function (id, callback) {
        if (this.sliders[id] !== undefined) {
            callback.call(this.sliders[id], this.sliders[id], this.sliders[id].sliderElement);
        } else {
            if (this.readys[id] === undefined) {
                this.readys[id] = [];
            }
            this.readys[id].push(callback);
        }
    };

    SmartSliderApi.prototype.on = function (eventName, callback) {
        if (this.eventListeners[eventName] === undefined) {
            this.eventListeners[eventName] = [];
        }
        this.eventListeners[eventName].push(callback);
    };

    SmartSliderApi.prototype.off = function (eventName, callback) {
        if (this.eventListeners[eventName] !== undefined) {
            for (var i = this.eventListeners[eventName].length - 1; i >= 0; i--) {
                if (this.eventListeners[eventName][i] === callback) {
                    this.eventListeners[eventName].splice(i, 1);
                }
            }
        }
    };

    SmartSliderApi.prototype.dispatch = function (eventName, slider) {
        if (this.eventListeners[eventName] !== undefined && this.eventListeners[eventName].length) {
            for (var i = this.eventListeners[eventName].length - 1; i >= 0; i--) {
                if (this.eventListeners[eventName][i]) {
                    this.eventListeners[eventName][i].call(slider, slider);
                }
            }
        }
    };

    SmartSliderApi.prototype.trigger = function (el, eventName, e) {
        if (e) {
            e.preventDefault();
        }

        var parts = eventName.split(','),
            slide = el.closest('.n2-ss-slide,.n2-ss-static-slide'),
            lastEvent = slide.ssLastEvent;

        if (!el.ssResetEvents) {
            el.ssResetEvents = 1;

            _addEventListener(slide, 'layerAnimationPlayIn', (function (slide) {
                slide.ssLastEvent = '';
            }).bind(this, slide));
        }

        var match = parts.length - 1;
        for (var i = 0; i < parts.length; i++) {

            if (parts[i] === lastEvent) {
                match = i;
            }
        }
        if (match === parts.length - 1) {
            eventName = parts[0];
        } else {
            eventName = parts[match + 1];
        }

        slide.ssLastEvent = eventName;
        _dispatchEventSimpleNoBubble(slide, 'ss' + eventName);
    };

    SmartSliderApi.prototype.applyAction = function (e, action) {

        if (this.isClickAllowed(e)) {
            var el = e.currentTarget,
                ss = this.findSliderByElement(el);
            ss[action].apply(ss, Array.prototype.slice.call(arguments, 2));
        }
    };

    SmartSliderApi.prototype.applyActionWithClick = function (e) {

        if (this.isClickAllowed(e)) {
            if (!_N2._shouldPreventClick) {
                e.preventDefault();
                this.applyAction.apply(this, arguments);
            }
        }
    };

    SmartSliderApi.prototype.isClickAllowed = function (e) {

        var closest = e.target.closest('a:not([href="#"]), *[onclick]:not([onclick=""]), *[data-n2click]:not([data-n2click=""]), *[data-n2-lightbox]');
        /**
         * Check for nested click events
         */
        return !closest || e.currentTarget === closest || !e.currentTarget.contains(closest);
    };

    SmartSliderApi.prototype.openUrl = function (e, target) {
        if (this.isClickAllowed(e)) {
            var href = _NodeGetData(e.currentTarget, 'href');
            if (target === undefined) {
                target = _NodeGetData(e.currentTarget, 'target');
            }

            if (target === '_blank') {
                var w = window.open();
                w.opener = null;
                w.location = href;
            } else {
                n2const.setLocation(href);
            }
        }
    };

    SmartSliderApi.prototype.openUrlKeyDown = function (e, target) {
        if ((e.code === 'Enter' || e.code === 'Space') && e.target.matches(':not(input,select,textarea)')) {
            this.openUrl(e, target);
        }
    };

    var scroll = {
        focusOffsetTop: 0,
        to: function (targetTop) {
            var scrollElement = document.querySelector("html, body, .n2_iframe_application__content");
            if (_NodeGetStyle(html, 'scroll-behavior') === 'smooth') {
                scrollElement.scrollTop = targetTop;
            } else {
                _animateScroll(scrollElement, targetTop, window.n2ScrollSpeed || 400);
            }
        },
        top: function () {
            scroll.to(0);
        },
        bottom: function () {
            scroll.to(body.scrollHeight - window.innerHeight);
        },
        before: function (el) {
            scroll.to(_getOffsetTop(el) - window.innerHeight);
        },
        after: function (el) {
            scroll.to(_getOffsetTop(el) + _getHeight(el) - scroll.focusOffsetTop);
        },
        next: function (el, selector) {
            var els = document.querySelectorAll(selector),
                nextI = -1;
            els.forEach(function (slider, i) {
                if (el === slider || slider.contains(el)) {
                    nextI = i + 1;
                    return false;
                }
            });
            if (nextI !== -1 && nextI <= els.length) {
                scroll.element(els[nextI]);
            }
        },
        previous: function (el, selector) {
            var els = document.querySelectorAll(selector),
                prevI = -1;
            els.forEach(function (slider, i) {
                if (el === slider || slider.contains(el)) {
                    prevI = i - 1;
                    return false;
                }
            });
            if (prevI >= 0) {
                scroll.element(els[prevI]);
            }
        },
        element: function (element) {
            if (typeof element === 'string') {
                element = document.querySelector(element);
            }
            scroll.to(_getOffsetTop(element) - scroll.focusOffsetTop);
        }
    };

    SmartSliderApi.prototype.scroll = function (e, fnName) {

        if (this.isClickAllowed(e)) {
            e.preventDefault();

            var slider = this.findSliderByElement(e.target);
            if (slider) {
                scroll.focusOffsetTop = slider.responsive.focusOffsetTop;
                e.currentTarget.blur();
            }

            scroll[fnName].apply(window, Array.prototype.slice.call(arguments, 2));
        }
    };

    SmartSliderApi.prototype.findSliderByElement = function (el) {
        el = el.closest('.n2-ss-slider');
        if (el) {
            return el.ss;
        }
        return null;
    }

    window.n2ss = new SmartSliderApi();

    return window.n2ss;
});
_N2.d('SmartSliderAbstract', function () {

    /**
     * @memberOf _N2
     *
     * @param elementID
     * @param parameters
     * @constructor
     */
    function SmartSliderAbstract(elementID, parameters) {

        /**
         * @type {_N2.EditorAbstract}
         */
        this.editor = null;

        this.elementID = elementID;

        if (window[elementID] && window[elementID] instanceof SmartSliderAbstract) {

            if (window[elementID].__sliderElement && !body.contains(window[elementID].__sliderElement)) {
                // Slider element might get removed even before shown. Fix for Elementor Popup
            } else if (window[elementID].sliderElement === undefined) {
                console.error('Slider [#' + elementID + '] inited multiple times');
                return;
            } else if (body.contains(window[elementID].sliderElement)) {
                console.error('Slider [#' + elementID + '] embedded multiple times');
                return;
            }
        }

        /**
         * @type {_N2.Stages}
         */
        this.stages = new _N2.Stages();
        _N2.d('#' + elementID, (function () {
            return this;
        }).bind(this));

        this.isAdmin = !!parameters.admin;

        _N2.SmartSliderPlugins.addSlider(this);

        this.id = parseInt(elementID.replace('n2-ss-', ''));

        // Register our object to a global variable
        window[elementID] = this;

        if (parameters.isDelayed !== undefined && parameters.isDelayed) {
            _ready((function () {
                this.waitForExists(elementID, parameters);
            }).bind(this));
        } else {
            this.waitForExists(elementID, parameters);
        }

    }

    SmartSliderAbstract.prototype.kill = function () {
        this.killed = true;

        var marginElement = this.sliderElement.closest('.n2-ss-margin');
        if (marginElement) {
            _NodeRemove(marginElement);
        } else {
            _N2.r('documentReady', (function () {
                _NodeRemove(this.sliderElement.closest('.n2-ss-margin'));
            }).bind(this));
        }

        var alignElement = this.sliderElement.closest('.n2-ss-align');
        if (alignElement) {
            _NodeRemove(alignElement);
        } else {
            _N2.r('documentReady', (function () {
                _NodeRemove(this.sliderElement.closest('.n2-ss-align'));
            }).bind(this));
        }

        /**
         * If the killed slider has a dependency we force them to show
         */
        n2ss.makeReady(this.id, this);
    };

    SmartSliderAbstract.prototype.waitForExists = function (id, parameters) {

        var promise = new Promise(function (resolve) {
            var existsCheck = function () {
                var element = document.getElementById(id) || document.getElementById(id + '_t');
                if (element) {
                    resolve(element);
                } else {
                    setTimeout(existsCheck, 500);
                }
            };

            existsCheck();
        });

        promise.then(this.onSliderExists.bind(this, id, parameters));
    };

    var lazySliders = [];

    function lazySliderLoad(elementToObserver, callback) {
        if (!!window.IntersectionObserver) {
            var observer = new IntersectionObserver((function (entries, observer) {
                for (var i = 0; i < entries.length; i++) {
                    var entry = entries[i];
                    if (entry.isIntersecting) {
                        callback();

                        observer.disconnect();
                    }
                }
            }).bind(this), {rootMargin: "200px 0px 200px 0px"});

            observer.observe(elementToObserver);

        } else {

            lazySliders.push({
                element: elementToObserver,
                callback: callback
            });

            if (lazySliders.length === 1) {

                var listeners = [],
                    lazySliderCheckScroll = function () {
                        var height = window.innerHeight * 1.4;
                        for (var i = 0; i < lazySliders.length; i++) {
                            if (lazySliders[i].element.getBoundingClientRect().y < height) {
                                var callback = lazySliders[i].callback;
                                lazySliders.splice(i, 1);
                                i--;
                                callback();
                            }
                        }

                        if (lazySliders.length === 0) {
                            _removeEventListeners(listeners);

                            _N2.SmartSliderApi.off('SliderResize', lazySliderCheckScroll);
                        }
                    }

                listeners.push(_addEventListenerWithRemover(window, 'resize', lazySliderCheckScroll, {
                    capture: true
                }));
                listeners.push(_addEventListenerWithRemover(window, 'scroll', lazySliderCheckScroll, {
                    capture: true,
                    passive: true
                }));

                _N2.SmartSliderApi.on('SliderResize', lazySliderCheckScroll);

                lazySliderCheckScroll();
            }
        }
    }

    SmartSliderAbstract.prototype.onSliderExists = function (id, parameters, sliderElement) {

        this.__sliderElement = sliderElement;

        this.___sectionElement = sliderElement.closest('.n2-section-smartslider');
        this.___loaderElement = this.___sectionElement.querySelector('ss3-loader');

        this.stages.resolve('Exists');

        if (sliderElement.tagName === 'TEMPLATE') {

            var parentNode = sliderElement.parentNode;
            parentNode.removeChild(sliderElement);

            var type = _NodeGetData(sliderElement, 'loadingType'),
                rocketElement = sliderElement.content.children[0],
                rocketLoad = (function () {

                    parentNode.appendChild(rocketElement);
                    _NodeRemoveStyle(this.___sectionElement, 'height');

                    this.waitForDimension(rocketElement, parameters);

                    _dispatchCustomEventNoBubble(window, 'n2Rocket', {
                        sliderElement: rocketElement
                    });
                }).bind(this);

            if (type === 'afterOnLoad') {
                _N2.r('windowLoad', lazySliderLoad.bind(this, this.___sectionElement, rocketLoad));
            } else if (type === 'afterDelay') {
                setTimeout(rocketLoad, _NodeGetData(sliderElement, 'loadingDelay'));
            } else {
                rocketLoad();
            }
        } else {

            this.waitForDimension(sliderElement, parameters);
        }
    };

    SmartSliderAbstract.prototype.waitForDimension = function (sliderElement, parameters) {

        if (n2const.isRTL()) {
            _NodeSetStyles(sliderElement, {
                '--ss-fs': 'flex-end',
                '--ss-fe': 'flex-start',
                '--ss-r': 'row-reverse',
                '--ss-rr': 'row'
            });
        }

        var resizeObserver = new ResizeObserver((function () {
            resizeObserver.disconnect();
            this.onSliderHasDimension(sliderElement, parameters);
        }).bind(this));

        resizeObserver.observe(sliderElement);
    };

    SmartSliderAbstract.prototype.onSliderHasDimension = function (sliderElement, parameters) {
        this.stages.resolve('HasDimension');
        this.killed = false;
        this.isVisible = true;

        /**
         * @type {_N2.SmartSliderResponsive}
         */
        this.responsive = false;
        this.mainAnimationLastChangeTime = 0;

        /**
         * @type {_N2.FrontendSliderSlide}
         */
        this.currentSlide = null;
        this.currentRealSlide = null;

        /**
         *
         * @type {_N2.FrontendSliderStaticSlide[]}
         */
        this.staticSlides = [];

        /**
         * @type {_N2.FrontendSliderSlideAbstract[]}
         */
        this.slides = [];
        /**
         * @type {_N2.FrontendSliderSlide[]}
         */
        this.visibleRealSlides = [];
        /**
         * @type {_N2.FrontendSliderSlideAbstract[]}
         */
        this.visibleSlides = [];


        sliderElement.ss = this;
        this.sliderElement = sliderElement;

        this.needBackgroundWrap = false;

        /**
         * Block carousel in vertical touch + revert
         * @type {boolean}
         */
        this.blockCarousel = false;

        this.parameters = _Assign({
            plugins: [],
            admin: false,
            playWhenVisible: 1,
            playWhenVisibleAt: 0.5,
            perspective: 1000,
            callbacks: '',
            autoplay: {},
            blockrightclick: false,
            maintainSession: 0,
            align: 'normal',
            controls: {
                touch: 'horizontal',
                keyboard: false,
                mousewheel: false,
                blockCarouselInteraction: 1
            },
            hardwareAcceleration: true,
            layerMode: {
                playOnce: 0,
                playFirstLayer: 1,
                mode: 'skippable',
                inAnimation: 'mainInEnd'
            },
            parallax: {
                enabled: 0,
                mobile: 0,
                horizontal: 'mouse',
                vertical: 'mouse',
                origin: 'enter'
            },
            mainanimation: {},
            randomize: {
                randomize: 0,
                randomizeFirst: 0
            },
            responsive: {},
            lazyload: {
                enabled: 0
            },
            postBackgroundAnimations: false,
            initCallbacks: false,
            titles: [],
            descriptions: [],
            alias: {
                id: 0,
                smoothScroll: 0,
                slideSwitch: 0
            }
        }, parameters);

        this.stages.resolve('Parameters');

        this.disabled = {
            layerAnimations: false,
            layerSplitTextAnimations: false,
            backgroundAnimations: false,
            postBackgroundAnimations: false,
            webGLBackgroundAnimationImageSmoothing: false
        };

        if (n2const.isSamsungBrowser) {
            this.disabled.layerSplitTextAnimations = true;

            this.disabled.postBackgroundAnimations = true;
            if (this.parameters.postBackgroundAnimations) {
                _NodeRemoveClass(this.sliderElement, 'n2-ss-feature-post-bg-loader');
            }
        }


        n2ss.makeReady(this.id, this);

        if (this.isAdmin) {
            this.changeTo = function () {
            };
        }

        this.load = new _N2.SmartSliderLoad(this);

        this.backgrounds = new _N2.SmartSliderBackgrounds(this);

        this.initSlides();

        if (typeof this.parameters.initCallbacks === 'function') {
            this.parameters.initCallbacks.call(this);
        }

        this.stages.done('VisibleSlides', this.onSlidesReady.bind(this));

        requestAnimationFrame(this.initUI.bind(this));
    };

    SmartSliderAbstract.prototype.onSlidesReady = function () {
        this.stages.resolve('SlidesReady');
    };

    SmartSliderAbstract.prototype.initUI = function () {
        //Prepare linked list of slides
        for (var i = 0; i < this.realSlides.length; i++) {
            this.realSlides[i].setNext(this.realSlides[i + 1 > this.realSlides.length - 1 ? 0 : i + 1]);
        }

        this.widgets = new _N2.SmartSliderWidgets(this);

        var isHover = false,
            hoverTimeout;

        new _N2.UniversalEnter(this.sliderElement, (function (e) {
            if (!e.target.closest('.n2-full-screen-widget')) {
                clearTimeout(hoverTimeout);
                isHover = true;

                _NodeAddClass(this.sliderElement, 'n2-hover');
                this.widgets.setState('hover', true);

                _addEventListenerOnce(this.sliderElement, 'universalleave', (function (e) {
                    e.stopPropagation();
                    hoverTimeout = setTimeout((function () {
                        isHover = false;
                        _NodeRemoveClass(this.sliderElement, 'n2-hover');
                        this.widgets.setState('hover', false);
                    }).bind(this), 1000);
                }).bind(this));
            }
        }).bind(this));

        if (!this.parameters.carousel) {
            this.initNotCarousel();
        }

        this.initHideArrow();

        this.controls = {};

        if (this.parameters.blockrightclick) {
            _addEventListener(this.sliderElement, "contextmenu", function (e) {
                e.preventDefault();
            });
        }

        this.initMainAnimation();
        this.initResponsiveMode();

        if (this.killed) {
            return;
        }

        _addEventListener(this.sliderElement, 'touchstart', (function () {
            _NodeRemoveClass(this.sliderElement, 'n2-has-hover');
        }).bind(this), {
            passive: true,
            once: true
        });


        this.initControls();

        this.stages.resolve('UIReady');

        if (!this.isAdmin) {
            var eventName = 'click';
            if (this.hasTouch()) {
                eventName = 'n2click';
            }
            this.sliderElement.querySelectorAll('[data-n2click="url"]').forEach(function (el) {
                _addEventListener(el, eventName, function (e) {
                    n2ss.openUrl(e);
                });
                _addEventListener(el, 'mousedown', function (e) {
                    if (e.button === 1) {
                        e.preventDefault();
                        n2ss.openUrl(e, '_blank');
                    }
                });
                _addEventListener(el, 'keydown', function (e) {
                    n2ss.openUrlKeyDown(e);
                });
            });
        }

        this.load.start();

        _addEventListener(this.sliderElement, 'keydown', function (event) {
            if (event.code === 'Space' || event.code === 'Enter' || event.code === 'NumpadEnter') {
                if (event.target.matches('[role="button"],[tabindex]') && event.target.matches(':not(a,input,select,textarea)')) {
                    event.preventDefault();

                    event.target.click();
                    _dispatchEventSimpleNoBubble(event.target, 'n2Activate');
                }
            }
        });
        _addEventListener(this.sliderElement, 'mouseleave', function (e) {
            e.currentTarget.blur();
        });

        if (window.jQuery) {
            /**
             * TwentySeventeen theme registers for focus event to fix the scroll position offset caused by its fixed top menu. Unsubscribe them all!
             */
            window.jQuery(this.sliderElement).find('[tabindex]').off('focus');
        }
    };

    SmartSliderAbstract.prototype.initSlides = function () {

        var slides = this.sliderElement.querySelectorAll('.n2-ss-slide'),
            i;
        for (i = 0; i < slides.length; i++) {
            this.slides.push(this.createSlide(slides[i], i));
        }

        for (i = 0; i < this.slides.length; i++) {
            this.slides[i].init();
            if (+_NodeGetData(this.slides[i].element, 'first') === 1) {
                this.originalRealStarterSlide = this.slides[i];
            }
        }

        /**
         * @type {_N2.FrontendSliderSlide[]}
         */
        this.realSlides = this.slides;

        /**
         * @type {_N2.FrontendSliderSlideAbstract[]}
         */
        this.visibleSlides = this.slides;

        this.initSlidesEnd();
    };

    SmartSliderAbstract.prototype.initSlidesEnd = function () {

        this.afterRawSlidesReady();

        this.stages.resolve('RawSlides');

        this.randomize(this.realSlides);

        this.stages.resolve('RawSlidesOrdered');

        this.___initStaticSlides();
    };

    SmartSliderAbstract.prototype.___initStaticSlides = function () {

        var staticSlides = this.sliderElement.querySelectorAll('.n2-ss-static-slide');
        for (var i = 0; i < staticSlides.length; i++) {
            this.staticSlides.push(new _N2.FrontendSliderStaticSlide(this, staticSlides[i]));
        }
    };

    SmartSliderAbstract.prototype.createSlide = function (slideElement, i) {
        return new _N2.FrontendSliderSlide(this, slideElement, i);
    };

    SmartSliderAbstract.prototype.afterRawSlidesReady = function () {

    };

    /**
     *
     * @param {string} eventName
     * @param [parameters]
     */
    SmartSliderAbstract.prototype.__$dispatchEvent = function (eventName, parameters) {

        n2console.sliderTrigger(eventName);

        _dispatchCustomEventNoBubble(this.sliderElement, eventName, parameters);
    };

    /**
     *
     * @param {string} eventName
     * @param [parameters]
     */
    SmartSliderAbstract.prototype.publicDispatchEvent = function (eventName, parameters) {

        this.__$dispatchEvent(eventName, parameters);

        _N2.SmartSliderApi.dispatch(eventName, this);
    };

    /**
     * Returns the sliders which should be visible if the given relativeSlide would be active
     * if relativeSlide is undefined, then use the currentSlide as the relativeSlide
     * @param relativeSlide
     * @returns {_N2.FrontendSliderSlide[]}
     */
    SmartSliderAbstract.prototype.getVisibleSlides = function (relativeSlide) {
        if (relativeSlide === undefined) {
            relativeSlide = this.currentSlide;
        }
        return [relativeSlide];
    };

    SmartSliderAbstract.prototype.getActiveSlides = function (relativeSlide) {
        return this.getVisibleSlides(relativeSlide);
    };

    SmartSliderAbstract.prototype.findSlideBackground = function (slide) {

        return this.sliderElement.querySelector('.n2-ss-slide-background[data-public-id="' + _NodeGetData(slide.element, 'slidePublicId') + '"]');
    };

    SmartSliderAbstract.prototype.getRealIndex = function (index) {
        return index;
    };

    /**
     * This is the last stage where we can change the StarterSlide.
     */
    SmartSliderAbstract.prototype.finalizeStarterSlide = function () {

        var realStarterSlide = this.originalRealStarterSlide;

        if (this.isAdmin) {

            this.finalizeStarterSlideComplete(realStarterSlide);

        } else if (this.parameters.randomize.randomizeFirst) {

            realStarterSlide = this.visibleRealSlides[Math.floor(Math.random() * this.visibleRealSlides.length)];

            this.finalizeStarterSlideComplete(realStarterSlide);
        } else {

            if (window['ss' + this.id] !== undefined) {
                if (typeof window['ss' + this.id] === 'object') {
                    window['ss' + this.id].done(this.overrideStarterSlideIndex.bind(this))
                } else {
                    this.overrideStarterSlideIndex(window['ss' + this.id]);
                }
            } else {

                if (this.parameters.maintainSession && window.localStorage !== undefined) {

                    var sessionIndex = window.localStorage.getItem('ss-' + this.id);
                    this.overrideStarterSlideIndex(sessionIndex);
                } else {
                    this.finalizeStarterSlideComplete(realStarterSlide);
                }
            }

            if (this.parameters.maintainSession && window.localStorage !== undefined) {
                _addEventListener(this.sliderElement, 'mainAnimationComplete', (function (e) {
                    window.localStorage.setItem('ss-' + this.id, e.detail.currentSlideIndex);
                }).bind(this));
            }
        }
    };

    SmartSliderAbstract.prototype.overrideStarterSlideIndex = function (forceActiveRealSlideIndex) {

        var realStarterSlide;

        if (forceActiveRealSlideIndex !== null && this.realSlides[forceActiveRealSlideIndex]) {
            realStarterSlide = this.realSlides[forceActiveRealSlideIndex];
        }

        this.finalizeStarterSlideComplete(realStarterSlide);
    };

    SmartSliderAbstract.prototype.finalizeStarterSlideComplete = function (realStarterSlide) {

        if (realStarterSlide === undefined || !realStarterSlide.isVisible) {
            realStarterSlide = this.visibleRealSlides[0];
        }

        if (realStarterSlide !== undefined) {
            this.finalizeStarterSlideComplete2(realStarterSlide);
        } else {
            this.hide();

            _addEventListenerOnce(this.sliderElement, 'SliderResize', (function () {
                this.finalizeStarterSlideComplete(realStarterSlide);
            }).bind(this));
        }
    };

    SmartSliderAbstract.prototype.finalizeStarterSlideComplete2 = function (realStarterSlide) {

        if (realStarterSlide !== this.originalRealStarterSlide && this.originalRealStarterSlide !== undefined) {

            this.originalRealStarterSlide.unsetActive();
        }

        this.responsive.onStarterSlide(realStarterSlide);

        this.stages.resolve('StarterSlide');
    };

    SmartSliderAbstract.prototype.randomize = function (slides) {

        if (this.parameters.randomize.randomize) {
            this.shuffleSlides(slides);
        }
    };

    SmartSliderAbstract.prototype.shuffleSlides = function (slides) {

        slides.sort(function () {
            return 0.5 - Math.random();
        });
        var containerElement = slides[0].element.parentNode;
        for (var i = 0; i < slides.length; i++) {
            containerElement.appendChild(slides[i].element);
            slides[i].setIndex(i);
        }
    };

    SmartSliderAbstract.prototype.started = function (fn) {
        this.stages.done('UIReady', fn.bind(this));
    };

    SmartSliderAbstract.prototype.ready = function (fn) {
        this.stages.done('Show', fn.bind(this));
    };

    SmartSliderAbstract.prototype.startVisibilityCheck = function () {

        if (!this.isAdmin && this.parameters.playWhenVisible && window.IntersectionObserver) {

            var playWhenVisibleAt = this.parameters.playWhenVisibleAt,
                min = playWhenVisibleAt / 2,
                max = 1 - playWhenVisibleAt / 2;

            var observerVisibleAt = new IntersectionObserver((function (entries) {
                if (entries[0].isIntersecting) {
                    this._markVisible();
                }
            }).bind(this), {
                rootMargin: '' + (-50 * playWhenVisibleAt) + '% 0px'
            });

            observerVisibleAt.observe(this.sliderElement);

            /**
             * When the slider height is smaller than the top and bottom part of the non-intersected area, they would never turn into visible
             */

            var observerSmallSliders = new IntersectionObserver((function (entries) {
                if (entries[0].isIntersecting) {
                    this._markVisible();
                }
            }).bind(this), {
                threshold: [min, max]
            });

            observerSmallSliders.observe(this.sliderElement);

            this.___visibleListeners = [
                observerVisibleAt.disconnect.bind(observerVisibleAt),
                observerSmallSliders.disconnect.bind(observerSmallSliders),
                _addEventListenerWithRemover(this.sliderElement, 'pointerover', this._markVisible.bind(this))
            ];
        } else {
            this.stages.resolve('Visible');
        }
    };

    SmartSliderAbstract.prototype._markVisible = function () {

        if (this.___visibleListeners) {
            _removeEventListeners(this.___visibleListeners);
        }

        this.stages.resolve('Visible');
    };

    SmartSliderAbstract.prototype.visible = function (fn) {
        this.stages.done('Visible', fn.bind(this));
    };

    SmartSliderAbstract.prototype.isPlaying = function () {

        return this.mainAnimation.getState() !== 'ended';
    };

    SmartSliderAbstract.prototype.focus = function (isSystem) {
        var needFocus = false;

        if (this.responsive.parameters.focusUser && !isSystem) {
            needFocus = true;
        }

        if (needFocus) {
            /**
             * .getBoundingClientRect() adjusted by the _getScrollTop()
             */
            var scrollTop = _getScrollTop(),
                focusOffsetTop = this.responsive.focusOffsetTop,
                focusOffsetBottom = this.responsive.focusOffsetBottom,
                windowHeight = window.innerHeight,
                sliderBoundingClientRect = this.sliderElement.getBoundingClientRect(),
                topLine = sliderBoundingClientRect.top - focusOffsetTop,
                bottomLine = windowHeight - sliderBoundingClientRect.bottom - focusOffsetBottom,
                focusEdge = this.responsive.parameters.focusEdge,
                edge = '';

            if (focusEdge === 'top-force') {
                edge = 'top';
            } else if (focusEdge === 'bottom-force') {
                edge = 'bottom';
            } else {

                if (topLine <= 0 && bottomLine <= 0) {
                    // Slider is taller than the screen and the the slider on the screen
                } else if (topLine > 0 && bottomLine > 0) {
                    // Do nothing, slider is shorter than the screen and the the slider on the screen
                } else {
                    if (topLine < 0) {
                        if (focusEdge === 'top') {
                            edge = 'top';
                        } else if (focusEdge === 'bottom') {
                            edge = 'bottom';
                        } else {
                            if (-topLine <= bottomLine) {
                                edge = 'top';
                            } else {
                                edge = 'bottom';
                            }
                        }
                    } else if (bottomLine < 0) {
                        if (focusEdge === 'top') {
                            edge = 'top';
                        } else if (focusEdge === 'bottom') {
                            edge = 'bottom';
                        } else {
                            if (-bottomLine <= topLine) {
                                edge = 'bottom';
                            } else {
                                edge = 'top';
                            }
                        }
                    }
                }
            }

            var targetTop = scrollTop;

            if (edge === 'top') {
                // scroll to the top edge
                targetTop = scrollTop - focusOffsetTop + sliderBoundingClientRect.top;
            } else if (edge === 'bottom') {
                // scroll to the bottom edge
                targetTop = scrollTop + focusOffsetBottom + sliderBoundingClientRect.bottom - windowHeight;
            }
            targetTop = Math.round(targetTop);

            if (targetTop !== scrollTop) {
                return this._scrollTo(targetTop, Math.abs(scrollTop - targetTop));
            }
        }

        return true;
    };

    SmartSliderAbstract.prototype._scrollTo = function (targetTop, duration) {

        var $hadSmoothScroll = false;

        if (_NodeGetStyle(html, 'scroll-behavior') === 'smooth') {
            /**
             * We need to disable the native smooth scroll for the duration of our scroll animation.
             */
            _NodeSetStyle(html, 'scroll-behavior', '');
            $hadSmoothScroll = true;
        }
        window.nextendScrollFocus = true;

        return (new Promise(function (resolve) {

            _animateScroll(document.scrollingElement, targetTop, duration, resolve);
        })).then(function () {
            if ($hadSmoothScroll) {
                _NodeSetStyle(html, 'scroll-behavior', 'smooth');
            }

            window.nextendScrollFocus = false;
        });
    };

    /**
     * Change is carousel if:
     * - first slide -> -1 slide not exists
     * - last slide -> +1 slide not exists
     */
    SmartSliderAbstract.prototype.isChangeCarousel = function (direction) {
        if (direction === 'next') {
            return this.currentSlide.index + 1 >= this.slides.length;
        } else if (direction === 'previous') {
            return this.currentSlide.index - 1 < 0;
        }

        return false;
    };

    SmartSliderAbstract.prototype.initNotCarousel = function () {
        this.realSlides[0].setPrevious(false);
        this.realSlides[this.realSlides.length - 1].setNext(false);
    };

    SmartSliderAbstract.prototype.initHideArrow = function () {

        var hideOrShowArrows = (function (slide) {
            this.widgets.setState('nonCarouselFirst', !this.getUIPreviousSlide(slide));
            this.widgets.setState('nonCarouselLast', !this.getUINextSlide(slide));
        }).bind(this);

        this.stages.done('StarterSlide', (function () {
            hideOrShowArrows(this.currentSlide);

            _addEventListener(this.sliderElement, 'SliderResize', (function () {
                hideOrShowArrows(this.currentSlide);
            }).bind(this));
        }).bind(this));

        _addEventListener(this.sliderElement, 'SlideWillChange', function (e) {
            hideOrShowArrows(e.detail.targetSlide);
        });
    };

    SmartSliderAbstract.prototype.next = function (isSystem, customAnimation) {
        var nextSlide = this.currentSlide.getNext();
        if (nextSlide && this.getUINextSlide(this.currentSlide)) {
            return this.changeTo(nextSlide.index, false, isSystem, customAnimation);
        }
        return false;
    };

    SmartSliderAbstract.prototype.previous = function (isSystem, customAnimation) {
        var previousSlide = this.getUIPreviousSlide(this.currentSlide);
        if (previousSlide) {
            return this.changeTo(previousSlide.index, true, isSystem, customAnimation);
        }
        return false;
    };

    SmartSliderAbstract.prototype.isChangePossible = function (direction) {
        var targetIndex = false;
        if (direction === 'next') {
            var nextSlide = this.currentSlide.getNext();
            if (nextSlide) {
                targetIndex = nextSlide.index;
            }
        } else if (direction === 'previous') {
            var previousSlide = this.currentSlide.getPrevious();
            if (previousSlide) {
                targetIndex = previousSlide.index;
            }
        }

        if (targetIndex !== false && targetIndex !== this.currentSlide.index) {
            return true;
        }
        return false;
    };

    SmartSliderAbstract.prototype.nextCarousel = function (isSystem, customAnimation) {
        if (!this.parameters.carousel) {
            /**
             * When the Carousel option is disabled, we should stop on the last slide.
             * @see SSDEV-3744
             */
            return this.next(isSystem, customAnimation);
        }

        if (!this.next(isSystem, customAnimation)) {
            return this.changeTo(this.getFirstSlide().index, false, isSystem, customAnimation)
        }
        return true;
    };

    SmartSliderAbstract.prototype.getFirstSlide = function () {
        if (this.slides[0].isVisible) {
            return this.slides[0];
        }
        return this.slides[0].getNext();
    };

    SmartSliderAbstract.prototype.getSlideCount = function () {
        var i = 0;
        for (var j = 0; j < this.slides.length; j++) {
            if (this.slides[j].isVisible) {
                i++;
            }
        }
        return i;
    };

    SmartSliderAbstract.prototype.directionalChangeToReal = function (nextSlideIndex) {
        this.directionalChangeTo(nextSlideIndex);
    };

    SmartSliderAbstract.prototype.directionalChangeTo = function (nextSlideIndex) {
        if (nextSlideIndex > this.currentSlide.index) {
            this.changeTo(nextSlideIndex, false);
        } else {
            this.changeTo(nextSlideIndex, true);
        }
    };

    SmartSliderAbstract.prototype.changeTo = function (nextSlideIndex, reversed, isSystem, customAnimation) {
        nextSlideIndex = parseInt(nextSlideIndex);

        if (nextSlideIndex !== this.currentSlide.index) {
            if (!this.slides[nextSlideIndex].isVisible) {
                console.error('this slide is not visible on this device');
                return false;
            }
            this.__$dispatchEvent('SlideWillChange', {
                targetSlide: this.slides[nextSlideIndex]
            });
            this.__$dispatchEvent('SlideLoading');

            var time = performance.now();

            Promise.all([
                Promise.all(this.backgrounds.preLoadSlides(this.getVisibleSlides(this.slides[nextSlideIndex]))),
                this.focus(isSystem)
            ]).then((function () {
                if (nextSlideIndex !== this.currentSlide.index) {
                    if (this.mainAnimationLastChangeTime < time) {
                        this.mainAnimationLastChangeTime = time;
                        // If the current main animation haven't finished yet or the preferred next slide is the same as our current slide we have nothing to do
                        var state = this.mainAnimation.getState();
                        if (state === 'ended') {

                            if (isSystem === undefined) {
                                isSystem = false;
                            }

                            var animation = this.mainAnimation;
                            if (customAnimation !== undefined) {
                                animation = customAnimation;
                            }

                            this._changeTo(nextSlideIndex, reversed, isSystem, customAnimation);

                            animation.changeTo(this.currentSlide, this.slides[nextSlideIndex], reversed, isSystem);

                            this._changeCurrentSlide(nextSlideIndex);

                        } else if (state === 'initAnimation' || state === 'playing') {
                            if (this.__fastChangeRemoveCallback) {
                                this.__fastChangeRemoveCallback();
                            }
                            this.__fastChangeRemoveCallback = _addEventListenerWithRemover(this.sliderElement, 'mainAnimationComplete', (function () {
                                this.changeTo.call(this, nextSlideIndex, reversed, isSystem, customAnimation);
                            }).bind(this), {
                                once: true
                            });
                            this.mainAnimation.timeScale(this.mainAnimation.timeScale() * 2);
                        }
                    }

                    this.__$dispatchEvent('SlideLoaded');
                }
            }).bind(this));
            return true;
        }
        return false;
    };

    SmartSliderAbstract.prototype.setCurrentRealSlide = function (currentSlide) {

        this.currentRealSlide = this.currentSlide = currentSlide;
    };

    SmartSliderAbstract.prototype._changeCurrentSlide = function (index) {

        this.setCurrentRealSlide(this.slides[index]);

        this.__$dispatchEvent('sliderChangeCurrentSlide');
    };

    SmartSliderAbstract.prototype._changeTo = function (nextSlideIndex, reversed, isSystem, customAnimation) {

    };

    SmartSliderAbstract.prototype.revertTo = function (nextSlideIndex, originalNextSlideIndex) {

        this.slides[originalNextSlideIndex].unsetActive();

        this.slides[nextSlideIndex].setActive();

        this._changeCurrentSlide(nextSlideIndex);

        this.__$dispatchEvent('SlideWillChange', {
            targetSlide: this.slides[nextSlideIndex]
        });
    };

    SmartSliderAbstract.prototype.forceSetActiveSlide = function (slide) {
        slide.setActive();
    };

    SmartSliderAbstract.prototype.forceUnsetActiveSlide = function (slide) {
        slide.unsetActive();
    };

    SmartSliderAbstract.prototype.updateInsideSlides = function (slides) {
        for (var i = 0; i < this.slides.length; i++) {
            this.slides[i].setInside(slides.indexOf(this.slides[i]) >= 0);
        }
        n2console.trace('Inside Slides updated: ' + slides.length);
    };

    /**
     *
     * @param {Element} element
     * @returns {_N2.FrontendSlideControls}
     */
    SmartSliderAbstract.prototype.findSlideByElement = function (element) {
        var i;
        for (i = 0; i < this.realSlides.length; i++) {
            if (this.realSlides[i].element.contains(element)) {
                return this.realSlides[i];
            }
        }

        for (i = 0; i < this.staticSlides.length; i++) {
            if (this.staticSlides[i].element.contains(element)) {
                return this.staticSlides[i];
            }
        }

        return null;
    };

    SmartSliderAbstract.prototype.findSlideIndexByElement = function (element) {
        var slide = this.findSlideByElement(element);
        if (slide) {
            return slide;
        }
        return -1;
    };

    SmartSliderAbstract.prototype.initMainAnimation = function () {
        /**
         * @type {_N2.SmartSliderMainAnimationAbstract}
         */
        this.mainAnimation = false;
    };

    SmartSliderAbstract.prototype.initResponsiveMode = function () {
    };

    SmartSliderAbstract.prototype.hasTouch = function () {
        return this.parameters.controls.touch != '0';
    };

    SmartSliderAbstract.prototype.initControls = function () {
        if (!this.parameters.admin) {
            if (this.hasTouch()) {
                switch (this.parameters.controls.touch) {
                    case 'vertical':
                        new _N2.SmartSliderControlTouchVertical(this);
                        break;
                    case 'horizontal':
                        new _N2.SmartSliderControlTouchHorizontal(this);
                        break;
                }
            }

            if (this.parameters.controls.keyboard) {
                if (this.controls.touch !== undefined) {
                    new _N2.SmartSliderControlKeyboard(this, this.controls.touch.axis);
                } else {
                    new _N2.SmartSliderControlKeyboard(this, 'horizontal');
                }
            }

            if (this.parameters.controls.mousewheel) {
                new _N2.SmartSliderControlMouseWheel(this, this.parameters.controls.mousewheel);
            }

            this.controlAutoplay = new _N2.SmartSliderControlAutoplay(this, this.parameters.autoplay);

            if (_N2.SmartSliderControlFullscreen.isSupported) {
                this.controlFullscreen = new _N2.SmartSliderControlFullscreen(this);
            }

            if (this.parameters.alias.id) {
                new _N2.SmartSliderControlAlias(this, this.parameters.alias);
            }

        }
    };

    SmartSliderAbstract.prototype.getSlideIndex = function (index) {
        return index;
    };

    SmartSliderAbstract.prototype.slideToID = function (id, direction, isSystem) {
        for (var i = 0; i < this.realSlides.length; i++) {
            if (+this.realSlides[i].id === id) {
                return this.slide(this.getSlideIndex(i), direction, isSystem);
            }
        }

        var sliderElement = document.querySelector('[data-id="' + id + '"]');
        if (sliderElement) {
            sliderElement = sliderElement.closest('.n2-ss-slider');
        }

        if (sliderElement) {
            if (this.id === sliderElement.ss.id) {
                return true;
            }

            _animateScroll(document.scrollingElement, _getOffsetTop(sliderElement), 400);
            return sliderElement.ss.slideToID(id, direction, true);
        }
    };

    SmartSliderAbstract.prototype.slide = function (index, direction, isSystem) {
        if (index >= 0 && index < this.slides.length) {
            if (direction === undefined) {
                if (this.parameters.carousel) {
                    if (this.currentSlide.index === this.slides.length - 1 && index === 0) {
                        return this.next(isSystem);
                    } else {
                        if (this.currentSlide.index > index) {
                            return this.changeTo(index, true, isSystem);
                        } else {
                            return this.changeTo(index, false, isSystem);
                        }
                    }
                } else {
                    if (this.currentSlide.index > index) {
                        return this.changeTo(index, true, isSystem);
                    } else {
                        return this.changeTo(index, false, isSystem);
                    }
                }
            } else {
                return this.changeTo(index, !direction, isSystem);
            }
        }
        return false;
    };

    SmartSliderAbstract.prototype.hide = function () {
        if (this.isVisible) {
            _NodeAddClass(this.responsive.alignElement, 'n2-ss-slider-has-no-slide');
            if (this.load.placeholder) {
                _NodeAddClass(this.load.placeholder, 'n2-ss-slider-has-no-slide');
            }
            this.isVisible = false;
        }
    };

    SmartSliderAbstract.prototype.show = function () {
        if (!this.isVisible) {
            _NodeAddClass(this.responsive.alignElement, 'n2-ss-slider-has-no-slide');
            if (this.load.placeholder) {
                _NodeRemoveClass(this.load.placeholder, 'n2-ss-slider-has-no-slide');
            }
            _dispatchEventSimple(window, 'scroll');
            this.isVisible = true;
        }
    };

    SmartSliderAbstract.prototype.startAutoplay = function () {
        if (this.controlAutoplay !== undefined) {
            this.controlAutoplay.setState('pausedSecondary', 0);
            return true;
        }
        return false;
    };

    SmartSliderAbstract.prototype.pauseAutoplay = function () {
        if (this.controlAutoplay !== undefined) {
            this.controlAutoplay.setState('pausedSecondary', 1);
            return true;
        }
        return false;
    };

    SmartSliderAbstract.prototype.getAnimationAxis = function () {
        return 'horizontal';
    };

    SmartSliderAbstract.prototype.getDirectionPrevious = function () {
        if (n2const.isRTL() && this.getAnimationAxis() === 'horizontal') {
            return 'next';
        }
        return 'previous';
    };

    SmartSliderAbstract.prototype.getDirectionNext = function () {
        if (n2const.isRTL() && this.getAnimationAxis() === 'horizontal') {
            return 'previous';
        }
        return 'next';
    };

    SmartSliderAbstract.prototype.previousWithDirection = function () {
        return this[this.getDirectionPrevious()]();
    };

    SmartSliderAbstract.prototype.nextWithDirection = function () {
        return this[this.getDirectionNext()]();
    };

    /**
     *
     * @param {_N2.FrontendSliderSlide} slide
     * @returns {_N2.FrontendSliderSlide|boolean}
     */
    SmartSliderAbstract.prototype.getUIPreviousSlide = function (slide) {
        return slide.getPrevious();
    };

    /**
     *
     * @param {_N2.FrontendSliderSlide} slide
     * @returns {_N2.FrontendSliderSlide|boolean}
     */
    SmartSliderAbstract.prototype.getUINextSlide = function (slide) {
        return slide.getNext();
    };

    SmartSliderAbstract.prototype.getHorizontalTouchDimension = function () {
        return this.responsive.resizeContext.sliderWidth;
    };

    SmartSliderAbstract.prototype.getVerticalTouchDimension = function () {
        return this.responsive.resizeContext.sliderHeight;
    };

    return SmartSliderAbstract;
});
_N2.d('Stages', function () {

    /**
     * @memberOf _N2
     *
     * @constructor
     */
    function Stages() {
        this.stages = {};
    }

    /**
     * @param name
     * @returns {_N2.Stage}
     */
    Stages.prototype.get = function (name) {
        if (this.stages[name] === undefined) {
            this.stages[name] = new Stage(name);
        }
        return this.stages[name];
    };

    Stages.prototype.resolve = function (name) {
        this.get(name).resolve();
    };

    /**
     *
     * @param {[]|string}names
     * @param callback
     */
    Stages.prototype.done = function (names, callback) {
        var promise;
        if (typeof names === 'string') {
            promise = this.get(names).getPromise();
        } else {
            var promises = [];
            for (var i = 0; i < names.length; i++) {
                promises.push(this.get(names[i]).getPromise());
            }
            promise = Promise.all(promises);
        }
        promise.then(callback);
    };

    Stages.prototype.resolved = function (name) {
        return this.get(name).resolved();
    };

    /**
     *
     * @param name
     * @constructor
     * @memberOf _N2
     */
    function Stage(name) {
        this.n = name;
        this._isResolved = false;

        this._promise = new Promise((function (resolve, reject) {
            this._resolve = resolve;
            this._reject = reject;
        }).bind(this));
        this._promise.then((function () {
            this._isResolved = true;
        }).bind(this));
    }

    Stage.prototype.getPromise = function () {
        return this._promise;
    };

    Stage.prototype.resolve = function () {
        if (!this.resolved()) {
            n2console.stageStart(this.n);
            this._resolve();
            n2console.stageEnd(this.n);
        }
    };

    Stage.prototype.done = function (callback) {

        this._promise.then(callback);
    };

    Stage.prototype.resolved = function () {
        return this._isResolved;
    };

    return Stages;
});_N2.d('SmartSliderWidget', function () {
    "use strict";

    /**
     * @memberOf _N2
     *
     * @param slider
     * @param key
     * @param selector
     * @constructor
     */
    function SmartSliderWidget(slider, key, selector) {

        this.slider = slider;

        this.key = key;

        this.selector = selector;

        this.slider.started(this.register.bind(this));
    }


    SmartSliderWidget.prototype.register = function () {

        if (!this.slider.widgets.has(this.key)) {

            this.widget = this.slider.sliderElement.querySelector(this.selector);

            if (this.widget) {
                this.slider.widgets.register(this.key, this);

                this.onStart();
            }
        }
    };

    SmartSliderWidget.prototype.onStart = function () {
    };

    SmartSliderWidget.prototype.isVisible = function () {
        var rect = this.widget.getBoundingClientRect();
        return !!(rect.width && rect.height);
    };

    SmartSliderWidget.prototype.getWidth = function () {

        return this.widget.getBoundingClientRect().width;
    }

    SmartSliderWidget.prototype.getHeight = function () {

        return this.widget.getBoundingClientRect().height;
    }

    return SmartSliderWidget;
});_N2.d('SmartSliderWidgets', function () {

    /**
     * @memberOf _N2
     *
     * @param {_N2.SmartSliderAbstract} slider
     * @constructor
     */
    function SmartSliderWidgets(slider) {
        this.slider = slider;

        this.sliderElement = slider.sliderElement;

        /**
         * @type {{}}
         */
        this.controls = {
            previous: undefined,
            next: undefined,
            bullet: undefined,
            autoplay: undefined,
            indicator: undefined,
            bar: undefined,
            thumbnail: undefined,
            shadow: undefined,
            fullscreen: undefined,
            html: undefined
        };

        this._controlDimensionRegexp = new RegExp('^(' + Object.keys(this.controls).join('|') + ')(width|height)$', 'i');

        this.excludedSlides = {};
        this.states = {
            hover: false,
            nonCarouselFirst: false,
            nonCarouselLast: false,
            currentSlideIndex: -1,
            singleSlide: false
        };
    }

    SmartSliderWidgets.prototype.register = function (name, control) {
        this.controls[name] = control;
    };

    SmartSliderWidgets.prototype.has = function (name) {
        return this.controls[name] !== undefined;
    };

    SmartSliderWidgets.prototype.setState = function (name, value) {
        if (this.states[name] != value) {
            this.states[name] = value;

            var parts = name.split('.');
            switch (parts[0]) {
                case 'hide':
                    this.onStateChangeSingle(parts[1]);
                    break;
                case 'nonCarouselFirst':
                    this.onStateChangeSingle(this.slider.getDirectionPrevious());
                    break;
                case 'nonCarouselLast':
                    this.onStateChangeSingle(this.slider.getDirectionNext());
                    break;
                default:
                    this.onStateChangeAll();
                    break;
            }
        }
    };

    SmartSliderWidgets.prototype.onStateChangeAll = function () {
        for (var controlKey in this.controls) {
            this.onStateChangeSingle(controlKey);
        }
    };

    SmartSliderWidgets.prototype.onStateChangeSingle = function (controlName) {
        if (this.controls[controlName]) {
            var state = true;

            if (this.controls[controlName].widget.classList.contains('n2-ss-widget-display-hover')) {
                state = this.states.hover;
            }

            if (state) {
                if (controlName === this.slider.getDirectionPrevious() && this.states.nonCarouselFirst) {
                    state = false;
                } else if (controlName === this.slider.getDirectionNext() && this.states.nonCarouselLast) {
                    state = false;
                }
            }

            if (state) {
                var key = controlName + '-' + (this.states.currentSlideIndex + 1);
                if (this.excludedSlides[key]) {
                    state = false;
                }
            }

            if (state && this.states['hide.' + controlName] !== undefined && this.states['hide.' + controlName]) {
                state = false;
            }

            if (state && this.states['singleSlide']) {
                if (controlName === 'previous' || controlName === 'next' || controlName === 'bullet' || controlName === 'autoplay' || controlName === 'indicator') {
                    state = false;
                }
            }

            this.controls[controlName].widget.classList.toggle('n2-ss-widget-hidden', !state);
        }
    };

    SmartSliderWidgets.prototype.getVerticalsHeight = function () {

        var h = 0;
        if (this._verticals) {
            for (var i = 0; i < this._verticals.length; i++) {
                h += this._verticals[i].offsetHeight;
            }
        }

        return h;
    };

    SmartSliderWidgets.prototype.onReady = function () {

        this.advancedElement = this.slider.sliderElement.querySelector('.n2-ss-slider-controls-advanced');
        this.requiredVariables = this.advancedElement ? (_NodeGetData(this.advancedElement, 'variables') || '').split(',') : [];

        _addEventListener(this.slider.sliderElement, 'visibleSlidesChanged', (function () {
            this.setState('singleSlide', this.slider.visibleSlides.length <= 1);
        }).bind(this));


        this.setState('singleSlide', this.slider.visibleSlides.length <= 1);

        this._verticals = this.sliderElement.querySelectorAll('.n2-ss-widget[data-position="above"]:not(.nextend-shadow),.n2-ss-widget[data-position="below"]:not(.nextend-shadow)');

        var hasExcluded = false;
        for (var controlName in this.controls) {
            if (this.controls[controlName] !== undefined) {
                var exclude = _NodeGetData(this.controls[controlName].widget, 'excludeSlides');
                if (exclude !== undefined) {
                    var excludedSlides = exclude.split(',');
                    for (var i = excludedSlides.length - 1; i >= 0; i--) {
                        var parts = excludedSlides[i].split('-');
                        if (parts.length === 2) {
                            var start = parseInt(parts[0]),
                                end = parseInt(parts[1]);
                            if (start <= end) {
                                for (var j = start; j <= end; j++) {
                                    excludedSlides.push(j);
                                }
                            }
                        } else {
                            excludedSlides[i] = parseInt(excludedSlides[i]);
                        }
                    }
                    if (excludedSlides.length > 0) {
                        for (var i = 0; i < excludedSlides.length; i++) {
                            this.excludedSlides[controlName + '-' + excludedSlides[i]] = true;
                        }
                        hasExcluded = true;
                    }
                }
            }
        }
        if (hasExcluded) {

            var refreshSlideIndex = (function (e) {
                this.setState('currentSlideIndex', e.detail.targetSlide.index);
            }).bind(this);

            refreshSlideIndex({
                detail: {
                    targetSlide: this.slider.currentRealSlide
                }
            });

            _addEventListener(this.slider.sliderElement, 'SlideWillChange', refreshSlideIndex);
        }

        if (this.requiredVariables.length && this.advancedElement) {

            this.flushAdvancedVariables();

            _addEventListener(this.slider.sliderElement, 'SliderResize', this.flushAdvancedVariables.bind(this));
        }

        this.onStateChangeAll();

        this.slider.stages.resolve('WidgetsReady');


        if (this.advancedElement) {
            this.slider.stages.done('BeforeShow', (function () {
                _NodeAddClass(this.advancedElement, 'n2-ss-slider-controls-advanced--ready');
            }).bind(this));
        }
    };

    SmartSliderWidgets.prototype.flushAdvancedVariables = function () {

        var variables = {};

        for (var i = 0; i < this.requiredVariables.length; i++) {

            variables[this.requiredVariables[i]] = this.getAdvancedVariable(this.requiredVariables[i]);
        }

        for (var variableName in variables) {
            _NodeSetStyle(this.advancedElement, '--' + variableName, parseInt(variables[variableName]));
        }
    };

    SmartSliderWidgets.prototype.getAdvancedVariable = function (variableName) {

        var dimensions = this.slider.responsive.resizeContext;

        switch (variableName) {
            case 'outerwidth':
                return _getWidth(this.sliderElement.parentNode);
            case 'outerheight':
                return _getHeight(this.sliderElement.parentNode);
            case 'width':
                return dimensions.sliderWidth;
            case 'height':
                return dimensions.sliderHeight;
            case 'canvaswidth':
                return dimensions.slideWidth;
            case 'canvasheight':
                return dimensions.slideHeight;
            case 'panewidth':
            case 'paneWidth':
                if (dimensions.paneWidth || dimensions.panewidth) {
                    return dimensions.paneWidth || dimensions.panewidth;
                }
                break;
        }

        var match = variableName.match(this._controlDimensionRegexp);
        if (match) {
            var widget = this.controls[match[1]];

            if (widget) {
                switch (match[2]) {
                    case 'width':
                        return widget.getWidth();
                    case 'height':
                        return widget.getHeight();
                }
            }
        }

        return 0;
    };

    SmartSliderWidgets.prototype.onAdvancedVariableWidgetChanged = function (widget) {

        if (this.advancedElement && (_NodeGetData(this.advancedElement, 'variables') || '').match(widget)) {
            this.slider.stages.done('BeforeShow', (function () {
                this.flushAdvancedVariables();
            }).bind(this));
        }
    };

    return SmartSliderWidgets;
});_N2.d('SmartSliderMainAnimationAbstract', function () {

    /**
     * @memberOf _N2
     *
     * @param slider
     * @param parameters
     * @constructor
     */
    function SmartSliderMainAnimationAbstract(slider, parameters) {

        this.state = 'ended';
        this.isTouch = false;
        this.isReverseAllowed = true;
        this.isReverseEnabled = false;
        this.reverseSlideIndex = null;
        this.isNoAnimation = false;

        this.slider = slider;

        this.parameters = _Assign({
            duration: 1500,
            ease: 'easeInOutQuint'
        }, parameters);

        this.parameters.duration = Math.max(0.01, this.parameters.duration / 1000);

        this.sliderElement = slider.sliderElement;

        this.timeline = new _N2.___Timeline({
            paused: true
        });

        _addEventListener(this.sliderElement, 'mainAnimationStart', (function (e) {
            this._revertCurrentSlideIndex = e.detail.previousSlideIndex;
            this._revertNextSlideIndex = e.detail.currentSlideIndex;
        }).bind(this));


        this.slider.stages.done('ResponsiveStart', this.init.bind(this));
    }

    SmartSliderMainAnimationAbstract.prototype.init = function () {
        /**
         * @type {_N2.SmartSliderResponsive}
         */
        this.responsive = this.slider.responsive;
    };

    SmartSliderMainAnimationAbstract.prototype.enableReverseMode = function () {
        this.isReverseEnabled = true;

        this.reverseTimeline = new _N2.___Timeline({
            paused: true
        });

        _dispatchCustomEventNoBubble(this.slider.sliderElement, 'reverseModeEnabled', {
            reverseSlideIndex: this.reverseSlideIndex
        });
    };

    SmartSliderMainAnimationAbstract.prototype.disableReverseMode = function () {
        this.isReverseEnabled = false;
    };

    SmartSliderMainAnimationAbstract.prototype.setTouch = function (direction) {
        this.isTouch = direction;
    };

    SmartSliderMainAnimationAbstract.prototype.setTouchProgress = function (progress) {

        if (this.parameters.duration < 0.3) {
            if (progress < 0) {
                progress = -1;
            } else if (progress > 0) {
                progress = 1;
            }
        }

        if (this.state !== 'ended') {
            if (this.isReverseEnabled) {
                if (progress === 0) {
                    this.reverseTimeline.progress(0);
                    this.timeline.progress(progress, false);
                } else if (progress >= 0 && progress <= 1) {
                    this.reverseTimeline.progress(0);
                    this.timeline.progress(progress);
                } else if (progress < 0 && progress >= -1) {
                    this.timeline.progress(0);
                    this.reverseTimeline.progress(Math.abs(progress));
                }
            } else {
                if (progress <= 0) {
                    this.timeline.progress(Math.max(progress, 0.000001), false);
                } else if (progress >= 0 && progress <= 1) {
                    this.timeline.progress(progress);
                }
            }
        }
    };


    SmartSliderMainAnimationAbstract.prototype.setTouchEnd = function (hasDirection, progress, duration) {
        if (this.state !== 'ended') {
            if (this.isReverseEnabled) {
                this._setTouchEndWithReverse(hasDirection, progress, duration);
            } else {
                this._setTouchEnd(hasDirection, progress, duration);
            }
        }
    };

    SmartSliderMainAnimationAbstract.prototype._setTouchEnd = function (hasDirection, progress, duration) {
        if (hasDirection && progress > 0) {
            this.fixTouchDuration(this.timeline, progress, duration);
            this.timeline.play();
        } else {
            this.revertCB(this.timeline);
            this.fixTouchDuration(this.timeline, 1 - progress, duration);
            this.timeline.reverse();

            this.willRevertTo(this._revertCurrentSlideIndex, this._revertNextSlideIndex);
        }
    };

    SmartSliderMainAnimationAbstract.prototype._setTouchEndWithReverse = function (hasDirection, progress, duration) {
        if (hasDirection) {
            if (progress < 0 && this.reverseTimeline.totalDuration() > 0) {
                this.fixTouchDuration(this.reverseTimeline, progress, duration);
                this.reverseTimeline.play();

                this.willRevertTo(this.reverseSlideIndex, this._revertNextSlideIndex);
            } else {

                this.willCleanSlideIndex(this.reverseSlideIndex);
                this.fixTouchDuration(this.timeline, progress, duration);
                this.timeline.play();
            }
        } else {
            if (progress < 0) {
                this.revertCB(this.reverseTimeline);
                this.fixTouchDuration(this.reverseTimeline, 1 - progress, duration);
                this.reverseTimeline.reverse();
            } else {
                this.revertCB(this.timeline);
                this.fixTouchDuration(this.timeline, 1 - progress, duration);
                this.timeline.reverse();
            }

            this.willCleanSlideIndex(this.reverseSlideIndex);

            this.willRevertTo(this._revertCurrentSlideIndex, this._revertNextSlideIndex);
        }
    };

    SmartSliderMainAnimationAbstract.prototype.fixTouchDuration = function (timeline, progress, duration) {
        var totalDuration = timeline.totalDuration(),
            modifiedDuration = Math.max(totalDuration / 3, Math.min(totalDuration, duration / Math.abs(progress) / 1000));
        if (modifiedDuration !== totalDuration) {
            timeline.totalDuration(modifiedDuration);
        }
    };

    SmartSliderMainAnimationAbstract.prototype.getState = function () {
        return this.state;
    };

    SmartSliderMainAnimationAbstract.prototype.timeScale = function () {
        if (arguments.length > 0) {
            this.timeline.timeScale(arguments[0]);
            return this;
        }
        return this.timeline.timeScale();
    };

    SmartSliderMainAnimationAbstract.prototype.changeTo = function (currentSlide, nextSlide, reversed, isSystem) {

        this._initAnimation(currentSlide, nextSlide, reversed);

        this.state = 'initAnimation';

        this.timeline.paused(true);
        this.timeline.eventCallback('onStart', this.onChangeToStart.bind(this), [currentSlide, nextSlide, isSystem]);
        this.timeline.eventCallback('onComplete', this.onChangeToComplete.bind(this), [currentSlide, nextSlide, isSystem]);
        this.timeline.eventCallback('onReverseComplete', null);

        this.revertCB = (function (timeline) {
            timeline.eventCallback('onReverseComplete', this.onReverseChangeToComplete.bind(this), [nextSlide, currentSlide, isSystem]);
        }).bind(this);
        if (!this.isTouch) {
            this.timeline.play();
        }
    
    };


    SmartSliderMainAnimationAbstract.prototype.willRevertTo = function (slideIndex, originalNextSlideIndex) {

        _dispatchCustomEventNoBubble(this.slider.sliderElement, 'mainAnimationWillRevertTo', {
            slideIndex: slideIndex,
            originalNextSlideIndex: originalNextSlideIndex
        });

        _addEventListenerOnce(this.sliderElement, 'mainAnimationComplete', this.revertTo.bind(this, slideIndex, originalNextSlideIndex));
    };


    SmartSliderMainAnimationAbstract.prototype.revertTo = function (slideIndex, originalNextSlideIndex) {
        this.slider.revertTo(slideIndex, originalNextSlideIndex);

        // Cancel the pre-initialized layer animations on the original next slide.
        _dispatchEventSimpleNoBubble(this.slider.slides[originalNextSlideIndex].element, 'mainAnimationStartInCancel');
    };


    SmartSliderMainAnimationAbstract.prototype.willCleanSlideIndex = function (slideIndex) {

        _addEventListenerOnce(this.sliderElement, 'mainAnimationComplete', this.cleanSlideIndex.bind(this, slideIndex));
    };

    SmartSliderMainAnimationAbstract.prototype.cleanSlideIndex = function () {

    };

    /**
     * @abstract
     * @param currentSlide
     * @param nextSlide
     * @param reversed
     * @private
     */
    SmartSliderMainAnimationAbstract.prototype._initAnimation = function (currentSlide, nextSlide, reversed) {
        this.slider.updateInsideSlides([currentSlide, nextSlide]);
    };

    SmartSliderMainAnimationAbstract.prototype.onChangeToStart = function (previousSlide, currentSlide, isSystem) {

        this.state = 'playing';

        var parameters = {
            mainAnimation: this,
            previousSlideIndex: previousSlide.index,
            currentSlideIndex: currentSlide.index,
            isSystem: isSystem
        };

        this.slider.__$dispatchEvent('mainAnimationStart', parameters);

        previousSlide.__$dispatchEvent('mainAnimationStartOut', parameters);
        currentSlide.__$dispatchEvent('mainAnimationStartIn', parameters);
    };

    SmartSliderMainAnimationAbstract.prototype.onChangeToComplete = function (previousSlide, currentSlide, isSystem) {

        var parameters = {
            mainAnimation: this,
            previousSlideIndex: previousSlide.index,
            currentSlideIndex: currentSlide.index,
            isSystem: isSystem
        };

        this.clearTimelines();

        this.disableReverseMode();

        previousSlide.__$dispatchEvent('mainAnimationCompleteOut', parameters);
        currentSlide.__$dispatchEvent('mainAnimationCompleteIn', parameters);

        this.state = 'ended';

        this.slider.updateInsideSlides([currentSlide]);

        if (!isSystem) {
            currentSlide.focus();
        }

        this.slider.__$dispatchEvent('mainAnimationComplete', parameters);
    };

    SmartSliderMainAnimationAbstract.prototype.onReverseChangeToComplete = function (previousSlide, currentSlide, isSystem) {
        SmartSliderMainAnimationAbstract.prototype.onChangeToComplete.apply(this, arguments);
    };

    SmartSliderMainAnimationAbstract.prototype.clearTimelines = function () {
        // When the animation done, clear the timeline
        this.revertCB = function () {
        };
        this.timeline.clear();
        this.timeline.timeScale(1);

    };

    SmartSliderMainAnimationAbstract.prototype.getEase = function () {
        if (this.isTouch) {
            return 'linear';
        }
        return this.parameters.ease;
    };

    return SmartSliderMainAnimationAbstract;
});_N2.d('SmartSliderControlAlias', function () {
    "use strict";

    /**
     * @memberOf _N2
     *
     * @param {_N2.SmartSliderAbstract} slider
     * @param parameters
     * @constructor
     */
    function SmartSliderControlAlias(slider, parameters) {

        this.___slider = slider;
        this.___sliderElement = slider.sliderElement;
        this.___sliderSection = slider.___sectionElement;

        this.___alias = _NodeGetData(this.___sliderSection, 'alias');

        this.___parameters = _Assign({
            id: 0,
            smoothScroll: 0,
            slideSwitch: 0,
            scroll: 1
        }, parameters);

        if (this.___alias) {
            if (this.___parameters.smoothScroll) {
                _NodeSetStyle(html, 'scroll-behavior', 'smooth');
            }

            if (this.___parameters.slideSwitch) {
                this.switchOnLoad();

                _addEventListener(window, 'hashchange', (function () {
                    var anchorTarget = this.getAnchor();
                    if (anchorTarget && Number.isInteger(anchorTarget) && anchorTarget > 0) {

                        if (this.___slider.responsive.parameters.focusUser === 1) {
                            /**
                             * We need to disable the Scroll to Slider feature, before we switch slides with anchor and then we need to enable it again.
                             */
                            this.___slider.responsive.parameters.focusUser = 0;
                            this.switchToSlide(anchorTarget - 1);
                            this.___slider.responsive.parameters.focusUser = 1;
                        } else {
                            this.switchToSlide(anchorTarget - 1);
                        }


                        this.replaceHash();
                    }
                }).bind(this));
            }
        }
    }

    /**
     *
     * @return {boolean|number} SlideIndex if any, true if slider anchor, false if not anchor found
     */
    SmartSliderControlAlias.prototype.getAnchor = function () {
        var hash = window.location.hash.substr(1);

        if (hash) {
            if (hash === this.___alias) {
                return true;
            } else if (this.___parameters.slideSwitch && hash.indexOf(this.___alias) === 0) {
                var slideIndex = +hash.substr(this.___alias.length + 1);
                if (slideIndex > 0) {
                    return slideIndex;
                }
            }
        }

        return false;
    };

    /**
     * Switch to slide on page load if there is a # anchor or query parameter using the alias
     */
    SmartSliderControlAlias.prototype.switchOnLoad = function () {

        var anchorTarget = this.getAnchor();
        if (anchorTarget && Number.isInteger(anchorTarget) && anchorTarget > 0) {
            var slideIndex = anchorTarget - 1,
                slider = window['n2-ss-' + this.___slider.id];

            if (slider) {

                if (slider.stages.resolved('StarterSlide')) {
                    slider.stages.done('BeforeShow', (function () {
                        this.switchToSlide(slideIndex);
                    }).bind(this));
                } else {
                    window['ss' + this.___slider.id] = slideIndex;
                }
            } else {
                window['ss' + this.___slider.id] = slideIndex;
            }


            if (this.___slider.parameters.maintainSession && window.localStorage !== undefined && slideIndex <= this.___slider.visibleSlides.length) {
                window.localStorage.setItem('ss-' + this.___slider.id, slideIndex);
            }

            this.replaceHash();
        }
    };

    /**
     * Replace slide index hash with slider hash to able to change slides again.
     */
    SmartSliderControlAlias.prototype.replaceHash = function () {
        var hash = '#' + this.___alias;

        if (history.replaceState) {
            history.replaceState(null, null, hash);
        } else {
            location.hash = hash;
        }
    };

    /**
     *
     * @param slide - switch to this slide
     */
    SmartSliderControlAlias.prototype.switchToSlide = function (slide) {
        this.___slider.slide(slide);
    };

    return SmartSliderControlAlias;
});_N2.d('SmartSliderControlAutoplay', function () {
    "use strict";

    /**
     * @memberOf _N2
     *
     * @param {_N2.SmartSliderAbstract} slider
     * @param parameters
     * @constructor
     */
    function SmartSliderControlAutoplay(slider, parameters) {
        /**
         * @type {_N2.SmartSliderAbstract}
         */
        this.slider = slider;

        this.state = {
            enabled: 1,
            paused: 1,
            pausedSecondary: 0,
            mainAnimationPlaying: 0,
            wait: 0
        };

        this._listeners = [];

        /**
         * @type {_N2.SmartSliderControlAutoplayWait}
         */
        this.wait = new _N2.SmartSliderControlAutoplayWait(this);

        this._currentCount = 1;

        this.autoplayToSlide = 0;
        this.autoplayToSlideIndex = -1;

        this.parameters = _Assign({
            enabled: 0,
            start: 1,
            duration: 8000,
            autoplayLoop: 0,
            allowReStart: 0,
            pause: {
                mouse: 'enter',
                click: true,
                mediaStarted: true
            },
            resume: {
                click: 0,
                mouse: 0,
                mediaEnded: true
            },
            interval: 1,
            intervalModifier: 'loop',
            intervalSlide: 'current'
        }, parameters);

        this.clickHandled = false;

        slider.controls.autoplay = this;

        if (this.parameters.enabled) {
            this.parameters.duration /= 1000;

            this.slider.visible(this.onReady.bind(this));

        } else {
            this.disable();
        }
    }

    SmartSliderControlAutoplay.prototype.preventClickHandle = function () {

        this.clickHandled = true;

        setTimeout((function () {
            this.clickHandled = false;
        }).bind(this), 300);

    };

    SmartSliderControlAutoplay.prototype.onReady = function () {

        this.___allowSlideChange = true;

        var obj = {
            _progress: 0
        };
        this.timeline = _N2.___Tween.to(obj, this.getSlideDuration(this.slider.currentSlide.index), {
            _progress: 1,
            paused: true,
            onComplete: this.next.bind(this)
        });

        var sliderElement = this.slider.sliderElement;

        this._listeners.push(_addEventListenerWithRemover(sliderElement, 'SlideLoading', (function () {
            this.wait.add('load');
        }).bind(this)));

        this._listeners.push(_addEventListenerWithRemover(sliderElement, 'SlideLoaded', (function (e) {
            this.wait.resolve('load');
        }).bind(this)));


        this._listeners.push(_addEventListenerWithRemover(sliderElement, 'BeforeCurrentSlideChange', (function () {
            this.wait.resolveWeak();
            this.setState('mainAnimationPlaying', 1)
        }).bind(this)));

        this._listeners.push(_addEventListenerWithRemover(sliderElement, 'mainAnimationStart', (function () {
            this._currentCount++;
            this.setState('mainAnimationPlaying', 1);
            this.wait.resolveWeak();
        }).bind(this)));

        this._listeners.push(_addEventListenerWithRemover(sliderElement, 'mainAnimationComplete', (function (e) {
            this.timeline.duration(this.getSlideDuration(e.detail.currentSlideIndex));
            this.___allowSlideChange = true;
            this.timeline.pause(0, false);

            this.setState('mainAnimationPlaying', 0);
        }).bind(this)));

        this._listeners.push(_addEventListenerWithRemover(sliderElement, 'autoplayPause', (function (e) {
            this.setState('paused', 1);
        }).bind(this)));

        this._listeners.push(_addEventListenerWithRemover(sliderElement, 'autoplayResume', (function (e) {
            if (this.state.paused || (parseInt(this.parameters.start) === 0 && parseInt(this.state.paused) === 0)) {
                this._currentCount = 1;
            }
            this.setState('pausedSecondary', 0);
            this.setState('paused', 0);

            var progress = e.detail.progress;
            if (progress !== undefined) {
                this.timeline.progress(progress);
            }
        }).bind(this)));

        this._listeners.push(_addEventListenerWithRemover(sliderElement, 'CurrentSlideChanged', (function (e) {

            this.timeline.duration(this.getSlideDuration(e.detail.currentSlide.index));
            this.___allowSlideChange = true;
            this.timeline.pause(0, false);

            this.setState('mainAnimationPlaying', 0);
        }).bind(this)));

        this.initClick(this.parameters.pause.click, this.parameters.resume.click);
        this.initHover(this.parameters.pause.mouse, this.parameters.resume.mouse);
        this.initMedia(this.parameters.pause.mediaStarted, this.parameters.resume.mediaEnded);


        this.slider.stages.resolve('AutoplayReady');

        this.slider.__$dispatchEvent('autoplay', {progress: 0});

        if (!this.parameters.start) {
            this.setState('pausedSecondary', 1);
        }

        this.setState('paused', 0);
    };

    SmartSliderControlAutoplay.prototype.setState = function (name, value) {
        if (this.state[name] !== value) {
            this.state[name] = value;

            if (this.timeline !== undefined) {
                if (this.state.enabled && !this.state.paused && !this.state.pausedSecondary && !this.state.wait && !this.state.mainAnimationPlaying) {
                    if (this.timeline.paused()) {
                        this.timeline.play();
                    }
                    if (this.isPaused === undefined || this.isPaused) {
                        this.isPaused = false;
                        this.slider.__$dispatchEvent('autoplayStarted');
                    }
                } else {
                    if (!this.timeline.paused()) {
                        this.timeline.pause();
                    }
                    if (!this.state.mainAnimationPlaying) {
                        if (this.isPaused === undefined || !this.isPaused) {
                            this.isPaused = true;
                            this.slider.__$dispatchEvent('autoplayPaused');
                        }
                    }
                }
            }
        }
    };

    SmartSliderControlAutoplay.prototype.initClick = function (pause, resume) {
        if (pause || resume) {

            this.universalClick = new _N2.UniversalClick(this.slider.sliderElement, (function (e) {
                if (!this.clickHandled) {
                    if (this.state.pausedSecondary) {
                        if (resume) {
                            this.setState('pausedSecondary', 0);
                        }
                    } else {
                        if (pause) {
                            this.setState('pausedSecondary', 1);
                        }
                    }
                }
            }).bind(this));
        }
    };

    SmartSliderControlAutoplay.prototype.initHover = function (pause, resume) {
        if (pause || resume) {
            var preventMouseEnter = false;

            this._listeners.push(_addEventListenerWithRemover(this.slider.sliderElement, 'touchend', function () {
                preventMouseEnter = true;
                setTimeout(function () {
                    preventMouseEnter = false;
                }, 300)
            }));

            this._listeners.push(_addEventListenerWithRemover(this.slider.sliderElement, 'mouseenter', (function (e) {
                if (this.state.pausedSecondary) {
                    if (resume === 'enter') {
                        this.setState('pausedSecondary', 0);
                    }
                } else {
                    if (!preventMouseEnter && pause === 'enter') {
                        this.setState('pausedSecondary', 1);
                    }
                }
            }).bind(this)));

            this._listeners.push(_addEventListenerWithRemover(this.slider.sliderElement, 'mouseleave', (function (e) {
                if (this.state.pausedSecondary) {
                    if (resume === 'leave') {
                        this.setState('pausedSecondary', 0);
                    }
                } else {
                    if (pause === 'leave') {
                        this.setState('pausedSecondary', 1);
                    }
                }
            }).bind(this)));
        }
    };
    SmartSliderControlAutoplay.prototype.initMedia = function (pause, resume) {
        var sliderElement = this.slider.sliderElement;
        if (pause) {
            this._listeners.push(_addEventListenerWithRemover(sliderElement, 'mediaStarted', (function (e) {
                this.wait.add(e.detail.id);
            }).bind(this)));
            this._listeners.push(_addEventListenerWithRemover(sliderElement, 'mediaEnded', (function (e) {
                this.wait.resolve(e.detail.id);
            }).bind(this)));
        } else if (resume) {
            this._listeners.push(_addEventListenerWithRemover(sliderElement, 'mediaEnded', (function () {
                this.setState('pausedSecondary', 0);
            }).bind(this)));
        }
    };

    SmartSliderControlAutoplay.prototype.enableProgress = function () {
        if (this.timeline) {
            this.timeline.eventCallback('onUpdate', (function () {
                this.slider.__$dispatchEvent('autoplay', {progress: this.timeline.progress()});
            }).bind(this));
        }
    };

    SmartSliderControlAutoplay.prototype.next = function () {

        if (this.___allowSlideChange) {
            this.___allowSlideChange = false;

            this.timeline.pause();

            if (!this.parameters.autoplayLoop) {
                switch (this.parameters.intervalModifier) {
                    case 'slide':
                        this.slideSwitchingSlideCount();
                        break;
                    case 'slideindex':
                        this.slideSwitchingIndex();
                        break;
                    default:
                        this.slideSwitchingLoop();
                        break;
                }

                /**
                 * We have reached the maximum slides in the autoplay so disable it completely
                 */
                if (this.autoplayToSlide > 0 && this._currentCount >= this.autoplayToSlide) {
                    this.limitAutoplay();
                }

                /**
                 * Stop only when Finish Autoplay is set to Slide index and there are no hidden slides.
                 */
                if (this.autoplayToSlideIndex >= 0 && this.slider.slides.length === this.slider.visibleSlides.length) {

                    if (this.autoplayToSlideIndex === this.slider.currentRealSlide.index + 2 || (this.autoplayToSlideIndex === 1 && this.slider.currentRealSlide.index + this.autoplayToSlideIndex === this.slider.slides.length)) {
                        this.limitAutoplay();
                    }
                }
            }
            this.slider.nextCarousel(true);
        }
    };

    SmartSliderControlAutoplay.prototype.slideSwitchingLoop = function () {
        this.autoplayToSlide = this.parameters.interval * this.slider.visibleSlides.length - 1;
        if (this.parameters.intervalSlide === 'next') {
            this.autoplayToSlide++;
        }
    };

    SmartSliderControlAutoplay.prototype.slideSwitchingSlideCount = function () {
        this.autoplayToSlide = this.parameters.interval;
    };

    SmartSliderControlAutoplay.prototype.slideSwitchingIndex = function () {
        var interval = Math.max(1, this.parameters.interval);

        /**
         * If the specified slide index is bigger than the total slide count, stop on the first slide.
         */
        if (interval > this.slider.slides.length) {
            interval = 1;
        }
        this.autoplayToSlideIndex = interval;
    };

    SmartSliderControlAutoplay.prototype.limitAutoplay = function () {
        if (!this.parameters.allowReStart) {
            this.disable();
        } else {
            this._currentCount = 0;
            this.setState('paused', 1);
        }
    };

    SmartSliderControlAutoplay.prototype.disable = function () {
        this.setState('enabled', 0);

        _removeEventListeners(this._listeners);
        if (this.universalClick) {
            this.universalClick.remove();
            delete this.universalClick;
        }

        this.slider.stages.resolve('AutoplayDestroyed');
    };

    SmartSliderControlAutoplay.prototype.getSlideDuration = function (index) {
        var slide = this.slider.realSlides[this.slider.getRealIndex(index)],
            duration = slide.minimumSlideDuration;

        if (parseFloat(slide.minimumSlideDuration) === 0) {
            duration = this.parameters.duration;
        }
        return duration;
    };

    return SmartSliderControlAutoplay;
});_N2.d('SmartSliderControlFullscreen', function () {
    "use strict";

    /**
     * @memberOf _N2
     *
     * @param {_N2.SmartSliderAbstract} slider
     * @param direction
     * @param parameters
     * @constructor
     */
    function SmartSliderControlFullscreen(slider, direction, parameters) {

        this.slider = slider;

        this.responsive = this.slider.responsive;

        this._type = this.responsive.parameters.type;

        this._upscale = this.responsive.parameters.upscale;

        this.___isFullScreen = false;

        this.___sliderElement = this.slider.sliderElement;

        this.___fullParent = this.___sliderElement.closest('.n2-section-smartslider');

        _addEventListener(document, SmartSliderControlFullscreen.event, this.fullScreenChange.bind(this));
    }

    SmartSliderControlFullscreen.isSupported = !!(document.exitFullscreen || document.webkitExitFullscreen);

    if (SmartSliderControlFullscreen.isSupported) {
        SmartSliderControlFullscreen.requestFullscreen = html.requestFullscreen || html.webkitRequestFullscreen;
        SmartSliderControlFullscreen.exitFullscreen = document.exitFullscreen || document.webkitExitFullscreen;
        SmartSliderControlFullscreen.event = html.requestFullscreen ? 'fullscreenchange' : (html.webkitRequestFullscreen ? 'webkitfullscreenchange' : null);
    }

    SmartSliderControlFullscreen.prototype.switchState = function () {
        this.___isFullScreen = !this.___isFullScreen;
        if (this.___isFullScreen) {
            this._fullScreen();
        } else {
            this._normalScreen();
        }
    };

    SmartSliderControlFullscreen.prototype.requestFullscreen = function () {
        if (!this.___isFullScreen) {
            this.___isFullScreen = true;
            this._fullScreen();
            return true;
        }
        return false;
    };

    SmartSliderControlFullscreen.prototype.exitFullscreen = function () {
        if (this.___isFullScreen) {
            this.___isFullScreen = false;
            this._normalScreen();
            return true;
        }
        return false;
    };

    SmartSliderControlFullscreen.prototype._fullScreen = function () {

        this.responsive.___isFullScreen = true;
        this.responsive.parameters.type = 'fullpage';
        this.___sliderElement.dataset.responsive = this.responsive.parameters.type;
        this.responsive.parameters.upscale = true;
        this._marginLeft = this.responsive.containerElement.style.marginLeft;
        this._marginRight = this.responsive.containerElement.style.marginRight;
        _NodeSetStyles(this.responsive.containerElement, {
            marginLeft: 0,
            marginRight: 0
        });

        _NodeSetStyles(this.___fullParent, {
            width: '100%',
            height: '100%',
            'background-color': window.getComputedStyle(body).backgroundColor
        });

        SmartSliderControlFullscreen.requestFullscreen.call(this.___fullParent);
    };

    SmartSliderControlFullscreen.prototype._normalScreen = function () {
        SmartSliderControlFullscreen.exitFullscreen.call(document);
    };

    SmartSliderControlFullscreen.prototype.fullScreenChange = function () {

        if (this.isDocumentInFullScreenMode()) {
            this.slider.__$dispatchEvent('n2FullScreen');
            _NodeAddClass(html, 'n2-in-fullscreen');
            _NodeAddClass(this.slider.sliderElement, 'n2-ss-slider--fullscreen');
            this.___isFullScreen = true;

            if (this._type === 'auto') {
                _NodeSetStyle(this.slider.responsive.alignElement, 'max-width', 'none');
            }

            _dispatchEventSimple(window, 'resize'); //needed for Safari
        } else {
            this.responsive.___isFullScreen = false;
            this.responsive.parameters.type = this._type;
            this.___sliderElement.dataset.responsive = this._type;
            this.responsive.parameters.upscale = this._upscale;
            _NodeSetStyles(this.responsive.containerElement, {
                marginLeft: this._marginLeft,
                marginRight: this._marginRight
            });
            _NodeSetStyles(this.___fullParent, {
                width: '',
                height: '',
                'background-color': ''
            });
            _NodeRemoveClass(this.slider.sliderElement, 'n2-ss-slider--fullscreen');
            _NodeRemoveClass(html, 'n2-in-fullscreen');

            if (this._type === 'auto') {
                _NodeRemoveStyle(this.slider.responsive.alignElement, 'max-width');
            }

            _dispatchEventSimple(window, 'resize');
            this.___isFullScreen = false;
            this.slider.__$dispatchEvent('n2ExitFullScreen');
        }
    };

    SmartSliderControlFullscreen.prototype.isDocumentInFullScreenMode = function () {

        return document.fullscreenElement || document.webkitIsFullScreen;
    };


    return SmartSliderControlFullscreen;
});_N2.d('SmartSliderControlKeyboard', function () {
    "use strict";

    var keyboardManager;

    function KeyboardManager() {
        /**
         * @type {SmartSliderControlKeyboard[]}
         */
        this.controls = [];
        document.addEventListener('keydown', this.onKeyDown.bind(this));
        document.addEventListener('mousemove', this.onMouseMove.bind(this), {
            capture: true
        });
    }

    KeyboardManager.prototype.onMouseMove = function (e) {
        this.mouseEvent = e;
    };

    /**
     * @param {SmartSliderControlKeyboard} control
     */
    KeyboardManager.prototype.addControl = function (control) {
        this.controls.push(control);
    };

    KeyboardManager.prototype.onKeyDown = function (e) {
        if (e.target.tagName.match(/BODY|DIV|IMG/) && !e.target.isContentEditable) {
            var sliderElement;

            if (this.mouseEvent) {
                sliderElement = this.findSlider(document.elementFromPoint(this.mouseEvent.clientX, this.mouseEvent.clientY));
                if (sliderElement) {
                    _dispatchCustomEventNoBubble(sliderElement, 'SliderKeyDown', {
                        e: e
                    });
                    return;
                }
            }

            if (document.activeElement !== body) {
                sliderElement = this.findSlider(document.activeElement);
                if (sliderElement) {
                    _dispatchCustomEventNoBubble(sliderElement, 'SliderKeyDown', {
                        e: e
                    });
                    return;
                }
            }

            for (var i = 0; i < this.controls.length; i++) {
                this.controls[i].onKeyDown(e);
            }
        }
    };

    /**
     *
     * @param {Element} element
     * @return {null|Element}
     */
    KeyboardManager.prototype.findSlider = function (element) {

        if (!element) {
            return null;
        }

        if (!element.classList.contains('n2-ss-slider')) {
            return element.closest('.n2-ss-slider');
        }

        return element;
    };

    /**
     * @memberOf _N2
     *
     * @param slider
     * @param direction
     * @param parameters
     * @constructor
     */
    function SmartSliderControlKeyboard(slider, direction, parameters) {

        this.slider = slider;

        this.parameters = _Assign({}, parameters);

        if (direction === 'vertical') {
            this.parseEvent = SmartSliderControlKeyboard.prototype.parseEventVertical;
        } else {
            this.parseEvent = SmartSliderControlKeyboard.prototype.parseEventHorizontal;
        }

        if (!keyboardManager) {
            keyboardManager = new KeyboardManager();
        }

        keyboardManager.addControl(this);

        _addEventListener(this.slider.sliderElement, 'SliderKeyDown', (function (e) {
            this.onKeyDown(e.detail.e);
        }).bind(this));

        slider.controls.keyboard = this;
    }

    SmartSliderControlKeyboard.prototype.isSliderOnScreen = function () {
        var rect = this.slider.sliderElement.getBoundingClientRect(),
            center = rect.height / 2;

        return rect.top + center >= 0 && rect.top + center <= window.innerHeight;
    };

    SmartSliderControlKeyboard.prototype.onKeyDown = function (e) {

        if (!e.defaultPrevented && this.isSliderOnScreen()) {
            if (this.parseEvent.call(this, e)) {
                e.preventDefault();
            }
        }
    };

    SmartSliderControlKeyboard.prototype.parseEventHorizontal = function (e) {
        switch (e.code) {
            case 'ArrowRight': // right arrow
                n2const.activeElementBlur();
                this.slider[n2const.isRTL() ? 'previous' : 'next']();
                return true;
            case 'ArrowLeft': // left arrow
                n2const.activeElementBlur();
                this.slider[n2const.isRTL() ? 'next' : 'previous']();
                return true;
            default:
                return false;
        }
    };

    SmartSliderControlKeyboard.prototype.parseEventVertical = function (e) {
        switch (e.code) {
            case 'ArrowDown': // down arrow

                if (!this.slider.isChangeCarousel('next') || !this.slider.parameters.controls.blockCarouselInteraction) {

                    n2const.activeElementBlur();

                    this.slider.next();

                    return true;
                }

                return false;
            case 'ArrowUp': // up arrow

                if (!this.slider.isChangeCarousel('previous') || !this.slider.parameters.controls.blockCarouselInteraction) {

                    n2const.activeElementBlur();

                    this.slider.previous();

                    return true;
                }

                return false;
            default:
                return false;
        }
    };

    return SmartSliderControlKeyboard;
});_N2.d('SmartSliderControlMouseWheel', function () {
    "use strict";

    /**
     * @memberOf _N2
     *
     * @param slider
     * @param mode
     * @constructor
     */
    function SmartSliderControlMouseWheel(slider, mode) {

        this.preventScroll = {
            local: false,
            curve: false,
            curveGlobal: false,
            global: false,
            localTimeout: false,
            curveTimeout: false,
            curveGlobalTimeout: false,
            globalTimeout: false
        };

        this.maxDelta = 0;

        this.slider = slider;

        if (mode === 2) {
            this.___axis = 'deltaX';
        } else {
            this.___axis = 'deltaY';
        }

        document.addEventListener('wheel', this.onGlobalMouseWheel.bind(this), {
            passive: false
        });

        slider.controls.mouseWheel = this;
    }

    SmartSliderControlMouseWheel.prototype.hasScrollableParentVertical = function (isUp, elementToCheck) {

        while (elementToCheck && elementToCheck !== this.slider.sliderElement) {

            if (elementToCheck.scrollHeight > elementToCheck.clientHeight) {
                var overflowY = window.getComputedStyle(elementToCheck).overflowY;

                if (overflowY === 'scroll' || overflowY === 'auto') {
                    if (isUp) {
                        if (elementToCheck.scrollTop > 0) {
                            return true;
                        }
                    } else {
                        if (elementToCheck.scrollTop + elementToCheck.clientHeight < elementToCheck.scrollHeight) {
                            return true;
                        }
                    }
                }
            }

            elementToCheck = elementToCheck.parentNode;
        }

        return false;
    };

    SmartSliderControlMouseWheel.prototype.hasScrollableParentHorizontal = function (elementToCheck) {

        while (elementToCheck && elementToCheck !== this.slider.sliderElement) {

            var overflowX = window.getComputedStyle(elementToCheck).overflowX;

            if (overflowX === 'scroll' || overflowX === 'auto') {
                if (elementToCheck.scrollWidth > elementToCheck.offsetWidth) {
                    return true;
                }
            }

            elementToCheck = elementToCheck.parentNode;
        }

        return false;
    };

    SmartSliderControlMouseWheel.prototype.onGlobalMouseWheel = function (e) {

        if (e.target.closest('.n2-ss-slider') && e[this.___axis] !== 0) {

            this.onCurveEvent(e);

            if (this.preventScroll.local || this.preventScroll.curve || Math.abs(e[this.___axis]) < this.maxDelta / 2) {
                e.preventDefault();
            } else {
                if (this.preventScroll.global) {
                    e.preventDefault();
                }
                if (this.slider.sliderElement === e.target || this.slider.sliderElement.contains(e.target)) {
                    if (this.___axis === 'deltaY') {
                        if (!e.shiftKey && !this.hasScrollableParentVertical(e.deltaY < 0, e.target)) {
                            this.onMouseWheel(e);
                        }
                    } else if (this.___axis === 'deltaX') {
                        if (!this.hasScrollableParentHorizontal(e.target)) {
                            this.onMouseWheel(e);
                        }
                    }
                }
            }
        }
    };

    SmartSliderControlMouseWheel.prototype.onMouseWheel = function (e) {

        if (e[this.___axis] < 0) {
            if (!this.slider.isChangeCarousel('previous') || !this.slider.parameters.controls.blockCarouselInteraction) {

                this.slider.previous();

                e.preventDefault();

                this.startCurveWatcher(e);
                this.local();
                this.global();
            }
        } else if (e[this.___axis] > 0) {
            if (!this.slider.isChangeCarousel('next') || !this.slider.parameters.controls.blockCarouselInteraction) {

                this.slider.next();

                e.preventDefault();

                this.startCurveWatcher(e);
                this.local();
                this.global();
            }
        }
    };

    SmartSliderControlMouseWheel.prototype.startCurveWatcher = function (e) {

        if (this.preventScroll.curve !== false) {
            clearTimeout(this.preventScroll.curveTimeout);
        }

        if (!this.preventScroll.curveGlobal) {
            this.dynamicDelta = false;
            this.lastDelta = e[this.___axis];
            this.preventScroll.curveGlobal = true;

            this.preventScroll.curveGlobalTimeout = setTimeout((function () {
                this.preventScroll.curveGlobal = false;
                this.maxDelta = 0;
            }).bind(this), 500);
        }

        this.preventScroll.curve = true;

        this.preventScroll.curveTimeout = setTimeout(this.releaseCurveLock.bind(this), 1500);
    };

    SmartSliderControlMouseWheel.prototype.onCurveEvent = function (e) {
        if (this.preventScroll.curveGlobal) {

            if (!this.dynamicDelta && this.lastDelta !== e[this.___axis]) {
                this.lastDelta = e[this.___axis];
                this.dynamicDelta = true;
            }

            var absdeltaY = Math.abs(e[this.___axis]);
            if (this.preventScroll.curve && this.maxDelta / 2 > absdeltaY) {
                // It seems like curve is going to down. We can allow new scroll change action
                this.releaseCurveLock();
            }

            this.maxDelta = Math.max(this.maxDelta, absdeltaY);


            if (this.preventScroll.curveGlobalTimeout) {
                clearTimeout(this.preventScroll.curveGlobalTimeout);
            }

            this.preventScroll.curveGlobalTimeout = setTimeout((function () {
                this.preventScroll.curveGlobal = false;
                this.maxDelta = 0;
            }).bind(this), 500);
        }
    };

    SmartSliderControlMouseWheel.prototype.releaseCurveLock = function () {
        this.preventScroll.curve = false;
        clearTimeout(this.preventScroll.curveTimeout);
    };

    SmartSliderControlMouseWheel.prototype.local = function () {

        if (this.preventScroll.local !== false) {
            clearTimeout(this.preventScroll.localTimeout);
        }

        this.preventScroll.local = true;

        this.preventScroll.localTimeout = setTimeout(function () {
            this.preventScroll.local = false;
            if (!this.dynamicDelta) {
                this.releaseCurveLock();
            }
        }.bind(this), 1000);
    };

    SmartSliderControlMouseWheel.prototype.global = function () {

        if (this.preventScroll.global !== false) {
            clearTimeout(this.preventScroll.globalTimeout);
        }

        this.preventScroll.global = true;

        this.preventScroll.globalTimeout = setTimeout(function () {
            this.preventScroll.global = false;
        }.bind(this), 1000);
    };

    return SmartSliderControlMouseWheel;
});_N2.d('SmartSliderControlTouch', function () {
    "use strict";

    var minDistance = 10;

    /**
     * @memberOf _N2
     *
     * @param slider
     * @constructor
     * @abstract
     */
    function SmartSliderControlTouch(slider) {

            this.slider = slider;

            /**
             * true if the drag will update the progress of the animation during interaction
             * false if the drag translated into swipe at the end of the interaction
             * @type {boolean}
             */
            this.interactiveDrag = true;

            this.preventMultipleTap = false;

            this._animation = slider.mainAnimation;

            this.swipeElement = this.slider.sliderElement.querySelector('.n2_ss__touch_element');

            slider.controls.touch = this;

            slider.stages.done('StarterSlide', this.onStarterSlide.bind(this));

            _addEventListener(slider.sliderElement, 'visibleSlidesChanged', this.onVisibleSlidesChanged.bind(this));
    }

    SmartSliderControlTouch.prototype.onStarterSlide = function () {

        this.initTouch();

        _addEventListener(this.slider.sliderElement, 'sliderChangeCurrentSlide', this.updatePanDirections.bind(this));
    };

    SmartSliderControlTouch.prototype.onVisibleSlidesChanged = function () {

        if (this.slider.visibleSlides.length > 1) {
            _NodeSetStyles(this.swipeElement, {
                cursor: 'grab',
                userSelect: 'none'
            });
        } else {
            _NodeRemoveStyles(this.swipeElement, ['cursor', 'userSelect']);
        }
    };

    SmartSliderControlTouch.prototype.initTouch = function () {
        if (this._animation.isNoAnimation) {
            this.interactiveDrag = false;
        }

        this.eventBurrito = _N2.EventBurrito(this.swipeElement, {
            mouse: true,
            axis: this.axis === 'horizontal' ? 'x' : 'y',
            start: this._start.bind(this),
            move: this._move.bind(this),
            end: this._end.bind(this)
        });

        this.updatePanDirections();

        this.cancelKineticScroll = (function () {
            this.kineticScrollCancelled = true;
        }).bind(this);
    };

    SmartSliderControlTouch.prototype._start = function (event) {

        this.currentInteraction = {
            type: event.type === 'pointerdown' ? 'pointer' : (event.type === 'touchstart' ? 'touch' : 'mouse'),
            state: _Assign({}, this.state),
            action: 'unknown',
            distance: [],
            distanceY: [],
            percent: 0,
            progress: 0,
            scrollTop: _getScrollTop(),
            animationStartDirection: 'unknown',
            hadDirection: false,
            startDistance: 0
        };
        this.logDistance(0, 0);
    };

    SmartSliderControlTouch.prototype._move = function (event, start, diff, isRealScrolling) {
        if (!isRealScrolling || this.currentInteraction.action !== 'unknown') {

            this.currentInteraction.direction = this.measure(diff);
            var distance = this.get(diff);

            if (this.currentInteraction.hadDirection || Math.abs(distance) > minDistance || Math.abs(diff.y) > minDistance) {
                this.logDistance(distance, diff.y);

                if (this.currentInteraction.percent < 1) {
                    this.setTouchProgress(distance, diff.y);
                }

                if (this.currentInteraction.type === 'touch' && event.cancelable) {
                    if (this.currentInteraction.action === 'switch' || this.currentInteraction.action === 'hold') {
                        this.currentInteraction.hadDirection = true;
                    }
                }
            }

            if (this.currentInteraction.action === 'switch') {
                return true;
            }
        }

        return false;
    };

    SmartSliderControlTouch.prototype._end = function (event, start, diff, isRealScrolling) {
        if (this.currentInteraction.action === 'switch') {
            var hasDirection = isRealScrolling ? 0 : this.measureRealDirection();

            if (this.interactiveDrag) {
                var progress = this._animation.timeline.progress();
                if (progress < 1) {
                    this._animation.setTouchEnd(hasDirection, this.currentInteraction.progress, diff.time);
                }

                // Switch back the animation into the original mode when our touch is ended
                this._animation.setTouch(false);
            } else {
                if (hasDirection) {
                    this.callAction(this.currentInteraction.animationStartDirection)
                }
            }

            _NodeRemoveClass(this.swipeElement, 'n2-grabbing');
        }

        this.onEnd();

        delete this.currentInteraction;

        if (Math.abs(diff.x) < 10 && Math.abs(diff.y) < 10) {
            this.onTap(event);
        } else {
            _N2._preventClick();
        }
    };

    SmartSliderControlTouch.prototype.onEnd = function () {

        if (this.currentInteraction.action === 'scroll' && this.currentInteraction.type === 'pointer') {
            var firstDistance = this.currentInteraction.distanceY[0],
                lastDistance = this.currentInteraction.distanceY[this.currentInteraction.distanceY.length - 1];

            /**
             * Simple kinetic scroll implementation
             */
            var amplitude = (firstDistance.d - lastDistance.d) / (lastDistance.t - firstDistance.t) * 10,
                timestamp = Date.now(),
                kineticScroll = (function () {
                    requestAnimationFrame((function () {
                        var elapsed, delta;
                        if (!this.kineticScrollCancelled && amplitude) {
                            elapsed = Date.now() - timestamp;
                            delta = amplitude * Math.exp(-elapsed / 325);
                            if (delta > 1 || delta < -1) {
                                _setScrollTop(_getScrollTop() + delta);
                                kineticScroll();

                                return;
                            }
                        }

                        this.onEndKineticScroll();
                    }).bind(this));
                }).bind(this);

            this.kineticScrollCancelled = false;
            kineticScroll();
            document.addEventListener('pointerdown', this.cancelKineticScroll);
        }
    };
    SmartSliderControlTouch.prototype.onEndKineticScroll = function () {

        delete this.kineticScrollCancelled;
        document.removeEventListener('pointerdown', this.cancelKineticScroll);

        _NodeSetStyle(html, 'scroll-behavior', '');
    };

    SmartSliderControlTouch.prototype.setTouchProgress = function (distance, distanceY) {

        this.recognizeSwitchInteraction();

        if (this.currentInteraction.startDistance === 0) {
            if (distance < 0) {
                this.currentInteraction.startDistance = distance + 1;
            } else {
                this.currentInteraction.startDistance = distance - 1;
            }
        }

        var progress,
            percent = this.getPercent(distance - this.currentInteraction.startDistance);
        this.currentInteraction.percent = percent;

        if (this.currentInteraction.action === 'switch') {
            if (this.interactiveDrag) {
                switch (this.currentInteraction.animationStartDirection) {
                    case 'up':
                        progress = percent * -1;
                        break;
                    case 'down':
                        progress = percent;
                        break;
                    case 'left':
                        progress = percent * -1;
                        break;
                    case 'right':
                        progress = percent;
                        break;
                }

                this.currentInteraction.progress = progress;

                this._animation.setTouchProgress(progress);
            }
        } else if (this.currentInteraction.action === 'unknown' || this.currentInteraction.action === 'scroll') {
            this.startScrollInteraction(distanceY);
        }
    };

    SmartSliderControlTouch.prototype.startScrollInteraction = function (distanceY) {

        if (this.axis === 'vertical') {

            /**
             * Scroll is not allowed in fullscreen mode
             */
            if (!this.slider.controlFullscreen || !this.slider.controlFullscreen.___isFullScreen) {

                this.currentInteraction.action = 'scroll';

                if (this.currentInteraction.type === 'pointer') {
                    /**
                     * Pointer events do not scroll if the touch-action CSS property defined which
                     * is required for Edge.
                     * @see https://blogs.windows.com/msedgedev/2017/12/07/better-precision-touchpad-experience-ptp-pointer-events/
                     */
                    _NodeSetStyle(html, 'scroll-behavior', 'auto');

                    _setScrollTop(Math.max(0, this.currentInteraction.scrollTop - distanceY));
                }
            }
        }
    };

    SmartSliderControlTouch.prototype.recognizeSwitchInteraction = function () {
        if (this.currentInteraction.action === 'unknown' && this.slider.visibleSlides.length > 1) {
            if (this._animation.state === 'ended') {
                var direction = this.currentInteraction.direction;
                if (direction !== 'unknown') {
                    /**
                     * This direction is allowed to change slides
                     */
                    if (this.currentInteraction.state[direction]) {

                        this.currentInteraction.animationStartDirection = direction;

                        if (this.interactiveDrag) {
                            // Force the main animation into touch mode horizontal/vertical
                            this._animation.setTouch(this.axis);

                            var isChangePossible = this.callAction(direction, false);
                            if (!isChangePossible) {
                                // Prevent scroll enabled, but carousel not. Do not allow to scroll
                            }
                        }

                        this.currentInteraction.action = 'switch';
                        _NodeAddClass(this.swipeElement, 'n2-grabbing');
                    }
                }
            } else if (this._animation.state === 'playing') {
                this.currentInteraction.action = 'hold';
            }
        }
    };

    SmartSliderControlTouch.prototype.logDistance = function (realDistance, realDistanceY) {
        if (this.currentInteraction.distance.length > 3) {
            this.currentInteraction.distance.shift();
            this.currentInteraction.distanceY.shift();
        }

        this.currentInteraction.distance.push({
            d: realDistance,
            t: Date.now()
        });

        this.currentInteraction.distanceY.push({
            d: realDistanceY,
            t: Date.now()
        });
    };

    SmartSliderControlTouch.prototype.measureRealDirection = function () {
        var firstDistance = this.currentInteraction.distance[0],
            lastDistance = this.currentInteraction.distance[this.currentInteraction.distance.length - 1];

        if ((lastDistance.d >= 0 && firstDistance.d > lastDistance.d) || (lastDistance.d < 0 && firstDistance.d < lastDistance.d)) {
            return 0;
        }
        return 1;
    };

    SmartSliderControlTouch.prototype.onTap = function (e) {
        if (!this.preventMultipleTap) {
            _dispatchEventSimple(e.target, 'n2click');

            this.preventMultipleTap = true;
            setTimeout((function () {
                this.preventMultipleTap = false;
            }).bind(this), 500);
        }
    };

    /**
     * @abstract
     */
    SmartSliderControlTouch.prototype.updatePanDirections = function () {

    };

    SmartSliderControlTouch.prototype.setState = function (newStates, doAction) {
        if (typeof arguments[0] !== 'object') {
            newStates = {};
            newStates[arguments[0]] = arguments[1];
            doAction = arguments[2];
        }

        var isChanged = false;
        for (var k in newStates) {
            if (this.state[k] !== newStates[k]) {
                this.state[k] = newStates[k];
                isChanged = true;
            }
        }

        if (isChanged && doAction && this.eventBurrito.supportsPointerEvents) {

            this.syncTouchAction();
        }
    };

    return SmartSliderControlTouch;
});_N2.d('SmartSliderControlTouchHorizontal', 'SmartSliderControlTouch', function () {
    "use strict";

    /**
     * @memberOf _N2
     *
     * @constructor
     * @augments _N2.SmartSliderControlTouch
     */
    function SmartSliderControlTouchHorizontal() {

        this.state = {
            left: false,
            right: false
        };

        this.axis = 'horizontal';

        _N2.SmartSliderControlTouch.prototype.constructor.apply(this, arguments);
    }

    SmartSliderControlTouchHorizontal.prototype = Object.create(_N2.SmartSliderControlTouch.prototype);
    SmartSliderControlTouchHorizontal.prototype.constructor = SmartSliderControlTouchHorizontal;

    SmartSliderControlTouchHorizontal.prototype.callAction = function (direction, isSystem) {
        switch (direction) {
            case 'left':
                return this.slider[n2const.isRTL() ? 'previous' : 'next'].call(this.slider, isSystem);
            case 'right':
                return this.slider[n2const.isRTL() ? 'next' : 'previous'].call(this.slider, isSystem);
        }

        return false;
    };

    SmartSliderControlTouchHorizontal.prototype.measure = function (diff) {
        if ((!this.currentInteraction.hadDirection && Math.abs(diff.x) < 10) || diff.x === 0 || Math.abs(diff.x) < Math.abs(diff.y)) return 'unknown';
        return diff.x < 0 ? 'left' : 'right';
    };

    SmartSliderControlTouchHorizontal.prototype.get = function (diff) {
        return diff.x;
    };

    SmartSliderControlTouchHorizontal.prototype.getPercent = function (distance) {
        return Math.max(-0.99999, Math.min(0.99999, distance / this.slider.getHorizontalTouchDimension()))
    };

    SmartSliderControlTouchHorizontal.prototype.updatePanDirections = function () {
        var currentSlideIndex = this.slider.currentSlide.index,
            nextSlideAllowed = currentSlideIndex + 1 < this.slider.slides.length,
            previousSlideAllowed = currentSlideIndex - 1 >= 0;

        if (this.slider.parameters.carousel) {
            nextSlideAllowed = true;
            previousSlideAllowed = true;
        }

        if (n2const.isRTL() && this.slider.getAnimationAxis() !== 'vertical') {
            this.setState({
                right: nextSlideAllowed,
                left: previousSlideAllowed
            }, true);
        } else {
            this.setState({
                right: previousSlideAllowed,
                left: nextSlideAllowed
            }, true);
        }
    };

    SmartSliderControlTouchHorizontal.prototype.syncTouchAction = function () {
        var touchAction = this.state.left || this.state.right ? 'pan-y' : '';

        _NodeSetStyle(this.swipeElement, 'touch-action', touchAction);

        if (window.PointerEventsPolyfill) {
            _NodeSetAttribute(this.swipeElement, 'touch-action', touchAction);
        }
    };


    return SmartSliderControlTouchHorizontal;
});_N2.d('SmartSliderControlTouchVertical', 'SmartSliderControlTouch', function () {
    "use strict";

    /**
     * @memberOf _N2
     *
     * @constructor
     * @augments _N2.SmartSliderControlTouch
     */
    function SmartSliderControlTouchVertical() {

        this.state = {
            up: false,
            down: false
        };

        this.action = {
            up: 'next',
            down: 'previous'
        };

        this.axis = 'vertical';

        _N2.SmartSliderControlTouch.prototype.constructor.apply(this, arguments);
    }

    SmartSliderControlTouchVertical.prototype = Object.create(_N2.SmartSliderControlTouch.prototype);
    SmartSliderControlTouchVertical.prototype.constructor = SmartSliderControlTouchVertical;

    SmartSliderControlTouchVertical.prototype.callAction = function (direction, isSystem) {
        switch (direction) {
            case 'up':
                return this.slider.next.call(this.slider, isSystem);
            case 'down':
                return this.slider.previous.call(this.slider, isSystem);
        }

        return false;
    };

    SmartSliderControlTouchVertical.prototype.measure = function (diff) {
        if ((!this.currentInteraction.hadDirection && Math.abs(diff.y) < 1) || diff.y == 0 || Math.abs(diff.y) < Math.abs(diff.x)) return 'unknown';
        return diff.y < 0 ? 'up' : 'down';
    };

    SmartSliderControlTouchVertical.prototype.get = function (diff) {
        return diff.y;
    };

    SmartSliderControlTouchVertical.prototype.getPercent = function (distance) {
        return Math.max(-0.99999, Math.min(0.99999, distance / this.slider.getVerticalTouchDimension()))
    };

    SmartSliderControlTouchVertical.prototype.updatePanDirections = function () {

        this.setState({
            down: !this.slider.isChangeCarousel('previous') || !this.slider.parameters.controls.blockCarouselInteraction,
            up: !this.slider.isChangeCarousel('next') || !this.slider.parameters.controls.blockCarouselInteraction
        }, true);
    };

    SmartSliderControlTouchVertical.prototype.syncTouchAction = function () {
        var touchAction = this.state.up || this.state.down ? 'pan-x' : '';

        _NodeSetStyle(this.swipeElement, 'touch-action', touchAction);

        if (window.PointerEventsPolyfill) {
            _NodeSetAttribute(this.swipeElement, 'touch-action', touchAction);
        }
    };

    SmartSliderControlTouchVertical.prototype._start = function (event) {

        this.slider.blockCarousel = true;

        _N2.SmartSliderControlTouch.prototype._start.apply(this, arguments);
    };

    SmartSliderControlTouchVertical.prototype.onEnd = function (event) {

        _N2.SmartSliderControlTouch.prototype.onEnd.apply(this, arguments);

        this.slider.blockCarousel = false;
    };


    return SmartSliderControlTouchVertical;
});_N2.d('SmartSliderControlAutoplayWait', function () {
    "use strict";

    /**
     * @memberOf _N2
     *
     * @param {_N2.SmartSliderControlAutoplay} autoplay
     * @constructor
     */
    function SmartSliderControlAutoplayWait(autoplay) {
        /**
         * @private
         * @type {_N2.SmartSliderControlAutoplay}
         */
        this.autoplay = autoplay;

        /**
         * @private
         * @type {{}}
         */
        this.waits = {};
    }

    SmartSliderControlAutoplayWait.Strong = ['lightbox', 'load'];

    /**
     * @public
     * @param id
     */
    SmartSliderControlAutoplayWait.prototype.add = function (id) {
        this.waits[id] = 1;
        this._refresh();
    };

    /**
     * @public
     * @param id
     */
    SmartSliderControlAutoplayWait.prototype.resolve = function (id) {
        delete this.waits[id];
        this._refresh();
    };

    /**
     * @public
     */
    SmartSliderControlAutoplayWait.prototype.resolveWeak = function () {
        var newWaits = {};
        for (var wait in this.waits) {
            if (this.waits[wait] === 1 && SmartSliderControlAutoplayWait.Strong.indexOf(wait) !== -1) {
                newWaits[wait] = 1;
            }
        }

        this.waits = newWaits;
        this._refresh();
    };

    /**
     * @public
     */
    SmartSliderControlAutoplayWait.prototype.resolveAll = function () {
        this.waits = {};
        this._refresh();
    };

    /**
     * @private
     */
    SmartSliderControlAutoplayWait.prototype._refresh = function () {
        var isWaiting = false;
        for (var k in this.waits) {
            if (this.waits[k]) {
                isWaiting = true;
                break;
            }
        }
        this.autoplay.setState('wait', isWaiting);
    };

    return SmartSliderControlAutoplayWait;
});_N2.d('SmartSliderSlideBackgroundColor', function () {

    /**
     * @memberOf _N2
     *
     * @param {_N2.SmartSliderSlideBackground} background
     * @param el
     * @constructor
     */
    function SmartSliderSlideBackgroundColor(background, el) {
        this.el = el;
    }

    SmartSliderSlideBackgroundColor.prototype.getLoadPromise = function () {
        return true;
    };

    return SmartSliderSlideBackgroundColor;
});_N2.d('SmartSliderSlideBackgroundImage', function () {

    /**
     * @memberOf _N2
     *
     * @param {_N2.FrontendSliderSlide} slide
     * @param {_N2.SmartSliderBackgrounds} manager
     * @param {_N2.SmartSliderSlideBackground} background
     * @param {NodeList} imageElements
     * @constructor
     */
    function SmartSliderSlideBackgroundImage(slide, manager, background, imageElements) {

        this.slide = slide;
        this.manager = manager;
        this.background = background;

        this._promise = new Promise((function (resolve) {
            this._resolve = resolve;
        }).bind(this));

        /**
         * @type {Node[]}
         */
        this.imageElements = Array.from(imageElements);
    }

    SmartSliderSlideBackgroundImage.prototype.getLoadPromise = function () {
        return this._promise;
    };

    SmartSliderSlideBackgroundImage.prototype.preLoad = function () {
        this.blur = _NodeGetData(this.imageElements[0], 'blur');
        const mode = _NodeGetData(this.imageElements[0].parentElement, 'mode');

        const blurableElements = [];

        if (this.blur) {
            blurableElements.push({
                item: this.imageElements[this.imageElements.length - 1],
                blur: this.blur
            })
        }

        if (mode === 'blurfit') {
            this.blurFitMode = _NodeGetData(this.imageElements[0], 'blurfitmode');
            this.globalBlurFit = _NodeGetData(this.imageElements[0], 'globalblur');
            this.editorBlur = _NodeGetData(this.imageElements[0], 'bgblur');

            blurableElements.push({
                item: this.imageElements[0],
                blur: this.blurFitMode ? this.globalBlurFit : this.editorBlur
            })
        }

        if (blurableElements.length) {
            blurableElements.forEach(function (el) {
                _NodeSetStyles(el.item, {
                    margin: (-2 * el.blur) + 'px',
                    padding: (2 * el.blur) + 'px'
                });
                _NodeSetStyle(el.item.children[0], 'filter', 'blur(' + el.blur + 'px)');
            });
        }

        var img = this.imageElements[0].querySelector('img');
        /**
         * Remove loading if added
         * @type {string}
         */
        img.loading = 'eager';

        if (img.complete) {
            this.onImageLoaded(img);
        } else {
            img.addEventListener('load', this.onImageLoaded.bind(this, img));
            img.addEventListener('error', this.onImageLoaded.bind(this, img));
        }
    };

    SmartSliderSlideBackgroundImage.prototype.onImageLoaded = function (img) {

        if (typeof img.decode === 'function') {
            img.decode()
                .then(this._resolve.bind(this))
                .catch((function (encodingError) {
                    console.error(encodingError);
                    this._resolve();
                }).bind(this));
            /**
             * Safari sometimes do not resolve the promise returned by decode().
             * We give browsers 50ms to complete the decode and if they are not ready, we resolve our promise.
             */
            setTimeout(this._resolve.bind(this), 50);
        } else {
            this._resolve();
        }
    };

    SmartSliderSlideBackgroundImage.prototype.fadeOut = function () {
        _N2.___Tween.to(_N2.MW.___getSMWs(this.imageElements), 0.3, {
            opacity: 0
        });
    };

    SmartSliderSlideBackgroundImage.prototype.onSlideDeviceChanged = function () {

    }

    return SmartSliderSlideBackgroundImage;
});_N2.d('SmartSliderSlideBackground', function () {

    /**
     * @memberOf _N2
     *
     * @param {_N2.FrontendSliderSlide} slide
     * @param {Element} element
     * @param {_N2.SmartSliderBackgrounds} manager
     * @constructor
     */
    function SmartSliderSlideBackground(slide, element, manager) {

        this.types = this.types || {
            color: 'SmartSliderSlideBackgroundColor',
            image: 'SmartSliderSlideBackgroundImage',
            video: 'SmartSliderSlideBackgroundVideo'
        };

        this.width = 0;
        this.height = 0;

        this.slide = slide;

        this.element = element;

        this.manager = manager;

        this.readyPromise = new Promise((function (resolve) {
            this.readyPromiseResolve = resolve;
        }).bind(this));

        this.promise = new Promise((function (resolve) {
            this.resolve = (function () {
                resolve();
                delete this.resolve;
            }).bind(this);
        }).bind(this));
    }

    SmartSliderSlideBackground.prototype.preloadOnce = function () {
        if (!this.elements) {

            /**
             * @type {{color: boolean|_N2.SmartSliderSlideBackgroundColor, image: boolean|_N2.SmartSliderSlideBackgroundImage, video: boolean|_N2.SmartSliderSlideBackgroundVideo}}
             */
            this.elements = {
                color: false,
                image: false,
                video: false
            };

            this.currentSrc = '';

            this.mode = _NodeGetData(this.element, 'mode');

            this.opacity = _NodeGetData(this.element, 'opacity');

            /*
            @see https://bugs.chromium.org/p/chromium/issues/detail?id=1181291

            this.template = this.element.querySelector('template');
            if (this.template) {
                this.template.replaceWith(this.template.content);
            }
             */

            var images = this.element.querySelectorAll('.n2-ss-slide-background-image');
            if (images.length) {
                this.elements.image = new _N2[this.types.image](this.slide, this.manager, this, images);
                this.elements.image.preLoad();
            }

            var color = this.element.querySelector('.n2-ss-slide-background-color');
            if (color) {
                this.elements.color = new _N2[this.types.color](this, color);
            }

            this.readyPromiseResolve();
            delete this.readyPromiseResolve;
            delete this.readyPromise;


            /**
             *
             * @type {Promise[]}
             */
            var promises = [];
            for (var k in this.elements) {
                if (this.elements[k]) {
                    promises.push(this.elements[k].getLoadPromise());
                }
            }

            Promise.all(promises)
                .then(this.resolve);
        }
    }

    SmartSliderSlideBackground.prototype.onReady = function (callback) {
        if (this.readyPromise) {
            this.readyPromise.then(callback);
        } else {
            callback();
        }
    }

    SmartSliderSlideBackground.prototype.preLoad = function () {

        this.preloadOnce();

        return this.promise;
    };

    SmartSliderSlideBackground.prototype.fadeOut = function () {
        if (this.hasImage()) {
            this.elements.image.fadeOut();
        }
    };

    SmartSliderSlideBackground.prototype.hasColor = function () {
        return this.elements && this.elements.color;
    };

    SmartSliderSlideBackground.prototype.hasImage = function () {
        return this.elements && this.elements.image;
    };

    SmartSliderSlideBackground.prototype.hasVideo = function () {
        return this.elements && this.elements.video;
    };

    SmartSliderSlideBackground.prototype.hasBackground = function () {
        return this.elements && (this.elements.color || this.elements.image || this.elements.video);
    };

    SmartSliderSlideBackground.prototype.onSlideDeviceChanged = function (device) {
        if (this.hasImage()) {
            this.elements.image.onSlideDeviceChanged(device);
        }
    };

    return SmartSliderSlideBackground;
});_N2.d('FrontendComponentCommon', ['FrontendComponent'], function () {

    /**
     * @memberOf _N2
     *
     * @param {_N2.FrontendComponentSectionSlide} slide
     * @param parent
     * @param layer
     * @param $children
     *
     * @constructor
     * @augments {_N2.FrontendComponent}
     */
    function FrontendComponentCommon(slide, parent, layer, $children) {
        this.wraps = {};

        _N2.FrontendComponent.prototype.constructor.apply(this, arguments);
    }

    FrontendComponentCommon.prototype = Object.create(_N2.FrontendComponent.prototype);
    FrontendComponentCommon.prototype.constructor = FrontendComponentCommon;

    FrontendComponentCommon.prototype.init = function (children) {

        this.stateCBs = [];
        this.state = {
            InComplete: false
        };


        var mask = this.layer.querySelector(':scope > .n2-ss-layer-mask');
        if (mask) {
            this.wraps.mask = mask;
        }

        switch (_NodeGetData(this.layer, 'pm')) {
            case 'absolute':
                this.placement = new _N2.FrontendPlacementAbsolute(this);
                break;
            case 'normal':
                this.placement = new _N2.FrontendPlacementNormal(this);
                break;
            case 'content':
                this.placement = new _N2.FrontendPlacementContent(this);
                break;
            default:
                this.placement = new _N2.FrontendPlacementDefault(this);
                break;
        }

        _N2.FrontendComponent.prototype.init.call(this, children);
    };

    FrontendComponentCommon.prototype.setState = function (name, value) {
        this.state[name] = value;
        for (var i = 0; i < this.stateCBs.length; i++) {
            this.stateCBs[i].call(this, this.state);
        }
    };

    FrontendComponentCommon.prototype.addStateCallback = function (cb) {
        this.stateCBs.push(cb);
        cb.call(this, this.state);
    };

    FrontendComponentCommon.prototype.start = function () {
        this.placement.start();

        _N2.FrontendComponent.prototype.start.call(this);

        var rotation = parseFloat(this.get('rotation'));
        if (rotation) {
            _N2.MW.___getSMW(this.layer).layerRotation = rotation;
        }
    };

    FrontendComponentCommon.prototype.onDeviceChange = function (device) {
        _N2.FrontendComponent.prototype.onDeviceChange.call(this, device);

        for (var i = 0; i < this.children.length; i++) {
            this.children[i].onDeviceChange(device)
        }
        this.placement.onDeviceChange(device);

        this.onAfterDeviceChange(device);

    };

    FrontendComponentCommon.prototype.onAfterDeviceChange = function (device) {

    };

    FrontendComponentCommon.prototype.onResize = function (ratios, dimensions) {

        _N2.FrontendComponent.prototype.onResize.apply(this, arguments);

        this.placement.onResize(ratios, dimensions);
    };

    FrontendComponentCommon.prototype.hasLayerAnimation = function () {
        return this.animationManager !== undefined;
    };

    FrontendComponentCommon.prototype.addWrap = function (key, el) {
        if (this.wraps[key] === undefined) {
            this.wraps[key] = el;
        }
        return el;
    };

    FrontendComponentCommon.prototype.getContents = function () {
        return [];
    };

    return FrontendComponentCommon;
});_N2.d('FrontendComponent', function () {

    /**
     * @memberOf _N2
     *
     * @param {_N2.FrontendComponentSectionSlide} slide
     * @param parent
     * @param layer
     * @param children
     * @constructor
     */
    function FrontendComponent(slide, parent, layer, children) {
        this.device = '';
        this.children = [];

        /**
         * @type {_N2.FrontendComponentSectionSlide}
         */
        this.slide = slide;
        this.parent = parent;
        this.layer = layer;
        layer.layer = this;

        this.isVisible = true;

        this.init(children);
    }

    FrontendComponent.prototype.init = function (children) {

        if (children) {
            for (var i = 0; i < children.length; i++) {
                var child = children[i];
                switch (_NodeGetData(child, 'sstype')) {
                    case 'content':
                        this.children.push(new _N2.FrontendComponentContent(this.slide, this, child));
                        break;
                    case 'row':
                        this.children.push(new _N2.FrontendComponentRow(this.slide, this, child));
                        break;
                    case 'col':
                        this.children.push(new _N2.FrontendComponentCol(this.slide, this, child));
                        break;
                    default:
                        this.children.push(new _N2.FrontendComponentLayer(this.slide, this, child));
                        break;
                }
            }
        }
    };

    FrontendComponent.prototype.start = function () {
        for (var i = 0; i < this.children.length; i++) {
            this.children[i].start()
        }
    };

    FrontendComponent.prototype.onDeviceChange = function (device) {
        this.device = device;
    };

    FrontendComponent.prototype.onResize = function (ratios, dimensions) {

        for (var i = 0; i < this.children.length; i++) {
            this.children[i].onResize(ratios, dimensions)
        }
    };

    FrontendComponent.prototype.getDevice = function (property, def) {
        var value = _NodeGetData(this.layer, this.device + property);
        if (value !== undefined) {
            return value;
        }
        if (this.device !== 'desktopportrait') {
            return _NodeGetData(this.layer, 'desktopportrait' + property);
        }
        if (def !== undefined) {
            return def;
        }
        return 0;
    };

    FrontendComponent.prototype.get = function (property) {
        return _NodeGetData(this.layer, property);
    };

    return FrontendComponent;
});_N2.d('FrontendSlideControls', function () {

    /**
     * @memberOf _N2
     *
     * @param {_N2.SmartSliderAbstract} slider
     * @param {Element} el
     *
     * @constructor
     */
    function FrontendSlideControls(slider, el) {
        /**
         * @type {_N2.SmartSliderAbstract}
         */
        this.slider = slider;

        this.element = el;

        this.element.ssSlide = this;

        /**
         *
         * @type {_N2.SlideStatus}
         */
        this.status = new _N2.SlideStatus();
    }

    FrontendSlideControls.prototype.isCurrentlyEdited = function () {
        return this._isCurrentlyEdited;
    };

    FrontendSlideControls.prototype.is = function (slideObject) {
        return this === slideObject;
    };

    FrontendSlideControls.prototype.__$dispatchEvent = function (eventName, parameters) {

        _dispatchCustomEventNoBubble(this.element, eventName, parameters);
    };

    FrontendSlideControls.prototype.isVisibleWhen = function (whenActiveSlide) {
        return true;
    };

    FrontendSlideControls.prototype.isActiveWhen = function (whenActiveSlide) {
        return true;
    };

    FrontendSlideControls.prototype.isStatic = function () {
        return false;
    };

    return FrontendSlideControls;
});_N2.d('FrontendPlacement', function () {

    /**
     * @memberOf _N2
     *
     * @param  {_N2.FrontendComponent} layer
     * @constructor
     */
    function FrontendPlacement(layer) {
        /**
         *
         * @type {_N2.FrontendComponent}
         */
        this.layer = layer;

        this.linked = [];
    }

    FrontendPlacement.prototype.start = function () {

    };

    FrontendPlacement.prototype.onDeviceChange = function (mode) {

    };

    function parseStylePosition(value) {

        if (value.match(/[0-9]+px$/)) {
            return parseInt(value);
        }

        return false;
    }

    FrontendPlacement.prototype.___initAbsoluteResize = function () {
        if (this.linked.length) {
            var element = this.layer.layer;

            this._sizePosition = {
                left: element.offsetLeft,
                top: element.offsetTop,
                width: element.offsetWidth,
                height: element.offsetHeight
            };
        }
    }

    FrontendPlacement.prototype.onResize = function (ratios, dimensions) {

        if (this.linked.length) {

            this.___initAbsoluteResize();

            for (var i = 0; i < this.linked.length; i++) {
                this.linked[i].onResizeLinked(ratios, dimensions)
            }
        }
    };

    FrontendPlacement.prototype.addLinked = function (childPlacement) {

        this.linked.push(childPlacement);
    };

    FrontendPlacement.prototype.isVisible = function () {

        return +_NodeGetData(this.layer.layer, 'hide' + this.layer.device) !== 1;
    }

    FrontendPlacement.prototype.getPositionSize = function () {

        return _Assign({}, this._sizePosition);
    };

    return FrontendPlacement;
});_N2.d('FrontendSliderSlide', ['FrontendSliderSlideAbstract'], function () {

    /**
     * @memberOf _N2
     *
     * @param {_N2.SmartSliderAbstract} slider
     * @param {Element} el
     * @param {int} index
     * @constructor
     * @augments _N2.FrontendSliderSlideAbstract
     */
    function FrontendSliderSlide(slider, el, index) {

        /**
         * @type {_N2.FrontendSliderSlideAbstract[]}
         */
        this.slides = [this];

        this.playCount = 0;

        _N2.FrontendSliderSlideAbstract.prototype.constructor.apply(this, arguments);

        this.id = _NodeGetData(el, 'id');

        this._slideFocus = el.querySelector('.n2-ss-slide--focus');

        this.focusableElements = Array.from(el.querySelectorAll('a[href]:not([href=""]),link,button,input:not([type="hidden"]),select,textarea,audio[controls],video[controls],[tabindex]:not([tabindex="-1"])'));
        var tabindex = _NodeGetAttribute(el, 'tabindex');
        if (tabindex === '0' || +tabindex > 0) {
            this.focusableElements.push(el);
        }
        this.disableFocus();

        /**
         *
         * @type {boolean|_N2.SmartSliderSlideBackground|_N2.SmartSliderSlideBackgroundAdmin}
         */
        this.background = false;

        if (!slider.parameters.admin) {
            this.minimumSlideDuration = +_NodeGetData(el, 'slideDuration');
            if (!this.minimumSlideDuration) {
                this.minimumSlideDuration = 0;
            }
        } else {
            this.minimumSlideDuration = 0;
        }

        this._isCurrentlyEdited = this.slider.parameters.admin && el.classList.contains('n2-ss-currently-edited-slide');

        if (!this.isCurrentlyEdited()) {

            this.component = new _N2.FrontendComponentSectionSlide(this, slider, el.querySelector('.n2-ss-layer[data-sstype="slide"]'));
            this.layer = this.component.layer;
        } else {

            this.layer = el.querySelector('.n2-ss-layer[data-sstype="slide"]');
            /**
             * Edited slide must be always visible
             */
            _addEventListener(slider.sliderElement, 'SliderDeviceOrientation', (function () {
                this.slider.visibleRealSlides.push(this);
                this.isVisible = true;
                this.slider.responsive.visibleRealSlidesChanged = true;
                this.__$dispatchEvent('Visible');
            }).bind(this));
        }
    }

    FrontendSliderSlide.prototype = Object.create(_N2.FrontendSliderSlideAbstract.prototype);
    FrontendSliderSlide.prototype.constructor = FrontendSliderSlide;

    /**
     * When the slide turns into non-inside we can safely reset the layer animations
     *
     * @param isInside
     * @private
     */
    FrontendSliderSlide.prototype._setInside = function (isInside) {
        if (this.isInside !== isInside) {
            this.isInside = isInside;
        }
    };

    /**
     * Only Firefox and Chrome supports it.
     * @see https://developer.mozilla.org/en-US/docs/Web/API/HTMLOrForeignElement/focus
     * @type {boolean}
     */
    var supportsPreventScrollOption = false;
    try {
        var focusElem = _CreateElement("div");
        focusElem.focus(
            Object.defineProperty({}, "preventScroll", {
                get: function () {
                    supportsPreventScrollOption = true;
                }
            })
        );
    } catch (e) {
    }


    FrontendSliderSlide.prototype.focus = function () {
        if (supportsPreventScrollOption) {
            this._slideFocus.focus({
                preventScroll: true
            });
        }
    };

    FrontendSliderSlide.prototype.allowFocus = function () {
        for (var i = 0; i < this.focusableElements.length; i++) {
            _NodeSetAttribute(this.focusableElements[i], 'tabindex', 0);
        }
        _NodeRemoveAttribute(this.element, 'aria-hidden');
    };

    FrontendSliderSlide.prototype.disableFocus = function () {
        for (var i = 0; i < this.focusableElements.length; i++) {
            _NodeSetAttribute(this.focusableElements[i], 'tabindex', -1);
        }
        _NodeSetAttribute(this.element, 'aria-hidden', 'true');
    };

    FrontendSliderSlide.prototype.init = function () {

        var imageElement = this.slider.findSlideBackground(this);

        if (imageElement) {
            if (this.slider.isAdmin) {
                this.background = new _N2.SmartSliderSlideBackgroundAdmin(this, imageElement, this.slider.backgrounds);
            } else {
                this.background = new _N2.SmartSliderSlideBackground(this, imageElement, this.slider.backgrounds);
            }
        }

        this.element.ssSlideBackground = this.background;
    };

    FrontendSliderSlide.prototype.onDeviceChange = function (device) {
        if (_NodeGetData(this.element, 'hide' + _ucFirst(device))) {
            if (this.isVisible !== false) {
                this.isVisible = false;
                this.slider.responsive.visibleRealSlidesChanged = true;
                this.__$dispatchEvent('Hidden');

            }
        } else {
            this.slider.visibleRealSlides.push(this);

            if (this.isVisible !== true) {
                this.isVisible = true;
                this.slider.responsive.visibleRealSlidesChanged = true;
                this.__$dispatchEvent('Visible');
            }
        }
    };

    FrontendSliderSlide.prototype.hasLayers = function () {
        return this.component.children.length > 0;
    };

    FrontendSliderSlide.prototype.hasBackgroundVideo = function () {
        return this.background.hasVideo();
    };

    FrontendSliderSlide.prototype.hasLink = function () {
        return !!_NodeGetData(this.element, 'haslink');
    };

    return FrontendSliderSlide;
});_N2.d('FrontendSliderSlideAbstract', ['FrontendSlideControls'], function () {

    /**
     * @memberOf _N2
     *
     * @param {_N2.SmartSliderAbstract} slider
     * @param {Element} el
     * @param {int} index
     * @constructor
     * @augments _N2.FrontendSlideControls
     */
    function FrontendSliderSlideAbstract(slider, el, index) {

        _N2.FrontendSlideControls.prototype.constructor.call(this, slider, el);

        /**
         * @type {_N2.FrontendSliderSlideAbstract[]}
         */
        this.slides = this.slides || [];

        /**
         * @type {_N2.FrontendSliderSlideAbstract}
         */
        this.group = this;

        this.originalIndex = index;
        this.index = index;
        this.localIndex = index;
        this.groupIndex = 0;

        this.isVisible = true;

        this.isInside = -1;

    }

    for (var k in _N2.FrontendSlideControls.prototype) {
        FrontendSliderSlideAbstract.prototype[k] = _N2.FrontendSlideControls.prototype[k];
    }

    FrontendSliderSlideAbstract.prototype.setIndex = function (index) {
        for (var i = 0; i < this.slides.length; i++) {
            this.slides[i]._setIndex(index);
        }
    };

    FrontendSliderSlideAbstract.prototype._setIndex = function (index) {
        this.localIndex = this.index = index;
    };

    FrontendSliderSlideAbstract.prototype.preLoad = function () {
        var promises = [];
        for (var i = 0; i < this.slides.length; i++) {
            promises.push(this.slides[i]._preLoad());
        }
        return Promise.all(promises);
    };

    FrontendSliderSlideAbstract.prototype._preLoad = function () {

        /**
         * Used by iframe layers
         */
        this.element.querySelectorAll('[data-lazysrc]')
            .forEach(function (el) {
                _NodeSetAttribute(el, 'src', _NodeGetData(el, 'lazysrc'));
            });

        if (this.background) {
            return this.background.preLoad();
        }

        return true;
    };

    /**
     * Linked list
     * @param previousSlide
     */
    FrontendSliderSlideAbstract.prototype.setPrevious = function (previousSlide) {
        this.previousSlide = previousSlide;
    };

    FrontendSliderSlideAbstract.prototype.getPrevious = function () {

        var slide = this;
        do {
            slide = slide.previousSlide;
        } while (slide && slide !== this && !slide.isVisible);

        return slide;
    };

    /**
     * Linked list
     * @param nextSlide
     */
    FrontendSliderSlideAbstract.prototype.setNext = function (nextSlide) {
        this.nextSlide = nextSlide;
        if (nextSlide) {
            nextSlide.setPrevious(this);
        }
    };

    FrontendSliderSlideAbstract.prototype.getNext = function () {

        var slide = this;
        do {
            slide = slide.nextSlide;
        } while (slide && slide !== this && !slide.isVisible);

        return slide;
    };

    FrontendSliderSlideAbstract.prototype.getTitle = function () {
        return _NodeGetData(this.slides[0].element, 'title') || '';
    };

    FrontendSliderSlideAbstract.prototype.getDescription = function () {
        return _NodeGetData(this.slides[0].element, 'description') || '';
    };

    FrontendSliderSlideAbstract.prototype.getThumbnail = function () {
        var thumbnailElement = this.slides[0].element.querySelector('.n2-ss-slide-thumbnail');
        if (thumbnailElement) {
            return _NodeGetAttribute(thumbnailElement, 'src');
        }

        return '';
    };

    FrontendSliderSlideAbstract.prototype.hasLink = function () {
        return false;
    };

    FrontendSliderSlideAbstract.prototype.setActive = function () {
        this.allowFocus();
        _NodeAddClass(this.element, 'n2-ss-slide-active');
    };

    FrontendSliderSlideAbstract.prototype.unsetActive = function () {
        this.disableFocus();
        _NodeRemoveClass(this.element, 'n2-ss-slide-active');
    };

    FrontendSliderSlideAbstract.prototype.setInside = function (isInside) {

        for (var i = 0; i < this.slides.length; i++) {
            this.slides[i]._setInside(isInside);
        }
    };

    FrontendSliderSlideAbstract.prototype._setInside = function (isInside) {

    };

    FrontendSliderSlideAbstract.prototype.focus = function () {

    };

    FrontendSliderSlideAbstract.prototype.allowFocus = function () {
    };

    FrontendSliderSlideAbstract.prototype.disableFocus = function () {
    };

    FrontendSliderSlideAbstract.prototype.isVisibleWhen = function (whenActiveSlide) {
        return this.slider.getVisibleSlides(whenActiveSlide).indexOf(this) !== -1;
    };

    FrontendSliderSlideAbstract.prototype.isActiveWhen = function (whenActiveSlide) {
        return this.slider.getActiveSlides(whenActiveSlide).indexOf(this) !== -1;
    };

    return FrontendSliderSlideAbstract;
});_N2.d('SlideStatus', function () {
    var s = {
        NOT_INITIALIZED: -1,
        INITIALIZED: 0,
        READY_TO_START: 1,
        PLAYING: 2,
        ENDED: 3,
        SUSPENDED: 4
    };

    /**
     * @memberOf _N2
     *
     * @constructor
     */
    function SlideStatus() {
        this.status = s.NOT_INITIALIZED;
    }

    SlideStatus.prototype.set = function (key) {
        this.status = s[key];
    };

    SlideStatus.prototype.is = function (key) {
        return this.status === s[key];
    };

    return SlideStatus;
});_N2.d('FrontendSliderStaticSlide', ['FrontendSlideControls'], function () {

    /**
     * @memberOf _N2
     *
     * @constructor
     * @augments _N2.FrontendSlideControls
     *
     * @param slider
     * @param {Element} el
     */
    function FrontendSliderStaticSlide(slider, el) {

        _N2.FrontendSlideControls.prototype.constructor.call(this, slider, el);

        /**
         * @type {_N2.FrontendSlideControls[]}
         */
        this.slides = [this];

        this.isVisible = true;

        this._isCurrentlyEdited = this.slider.parameters.admin && el.classList.contains('n2-ss-currently-edited-slide');

        if (!this.isCurrentlyEdited()) {
            this.component = new _N2.FrontendComponentSectionSlide(this, slider, el.querySelector('.n2-ss-layer[data-sstype="slide"]'));
            this.layer = this.component.layer;
        } else {
            this.layer = el.querySelector('.n2-ss-layer[data-sstype="slide"]');
        }
    }

    for (var k in _N2.FrontendSlideControls.prototype) {
        FrontendSliderStaticSlide.prototype[k] = _N2.FrontendSlideControls.prototype[k];
    }

    FrontendSliderStaticSlide.prototype.isStatic = function () {
        return true;
    };

    FrontendSliderStaticSlide.prototype.onDeviceChange = function (device) {
        if (_NodeGetData(this.element, 'hide' + _ucFirst(device))) {
            if (this.isVisible !== false) {
                this.isVisible = false;
                this.__$dispatchEvent('Hidden');
            }
        } else {
            if (this.isVisible !== true) {
                this.isVisible = true;

                /**
                 * Slide was not visible at start
                 */
                if (this.status.is('INITIALIZED')) {
                    this.playIn();
                }

                this.__$dispatchEvent('Visible');
            }
        }
    };

    return FrontendSliderStaticSlide;
});_N2.d('FrontendPlacementAbsolute', ['FrontendPlacement'], function () {

    class AbsoluteManager {

        constructor() {
            this.___placements = new Set();
            this.___invalidated = new Set();

            this.___onTickCallback = this.___onTick.bind(this);
        }


        /**
         *
         * @param {FrontendPlacementAbsolute} placement
         */
        add(placement) {
            this.___placements.add(placement);
        }

        /**
         *
         * @param {FrontendPlacementAbsolute} placement
         */
        invalidate(placement) {
            this.___invalidated.add(placement);

            if (this.___invalidated.size === 1) {
                _N2.___Ticker.add(this.___onTickCallback);
            }
        }

        ___onTick() {

            for (let placement of this.___invalidated) {
                placement.___initAbsoluteResize();
            }

            for (let placement of this.___invalidated) {
                placement.onResizeSize();
            }

            for (let placement of this.___invalidated) {
                placement.onResizePosition();
            }

            this.___invalidated.clear();

            _N2.___Ticker.remove(this.___onTickCallback);
        }
    }

    var absoluteManager = new AbsoluteManager();

    /**
     * @memberOf _N2
     *
     * @param layer
     * @constructor
     */
    function FrontendPlacementAbsolute(layer) {
        this.parentLayer = false;
        this.parentLayerPlacement = false;
        _N2.FrontendPlacement.prototype.constructor.apply(this, arguments);
    }

    FrontendPlacementAbsolute.prototype = Object.create(_N2.FrontendPlacement.prototype);
    FrontendPlacementAbsolute.prototype.constructor = FrontendPlacementAbsolute;

    FrontendPlacementAbsolute.prototype.start = function () {
        var parentID = this.layer.get('parentid');
        if (parentID) {
            var parentElement = document.getElementById(parentID);
            if (parentElement) {
                this.parentLayer = parentElement.layer;
                this.parentLayerPlacement = this.parentLayer.placement;
                this.parentLayerPlacement.addLinked(this);
                this.onResize = function () {
                };
            }
        }

        absoluteManager.add(this);
    };

    FrontendPlacementAbsolute.prototype.isSingleAxis = function () {

        if (this.layer.parent instanceof _N2.FrontendComponentSectionSlide) {
            if (this.parentLayer) {
                if (this.parentLayer.placement instanceof _N2.FrontendPlacementAbsolute) {
                    return false;
                }
            } else {
                return false;
            }
        }

        return true;
    };


    FrontendPlacementAbsolute.prototype.___initAbsoluteResize = function () {

        _N2.FrontendPlacement.prototype.___initAbsoluteResize.apply(this, arguments);

        var parentNode = this.layer.layer.parentNode;

        this.___parentNodeData = {
            width: parentNode.offsetWidth,
            height: parentNode.offsetHeight
        };

    };

    FrontendPlacementAbsolute.prototype.onResizeSize = function () {
        var layerSMW = _N2.MW.___getSMW(this.layer.layer),
            ratioPositionHorizontal = this.___ratios.slideW,
            ratioPositionVertical = this.___ratios.slideH;

        if (this.isSingleAxis()) {
            ratioPositionVertical = ratioPositionHorizontal;
        }

        var ratioSizeHorizontal = ratioPositionHorizontal,
            ratioSizeVertical = ratioPositionVertical;

        if (!parseInt(this.layer.get('responsivesize'))) {
            ratioSizeHorizontal = ratioSizeVertical = 1;
        }

        var width = this.getWidth(ratioSizeHorizontal),
            widthNumber = typeof width === 'number',
            height = this.getHeight(ratioSizeVertical),
            heightNumber = typeof height === 'number';

        if (this._sizePosition) {
            if (widthNumber) {
                this._sizePosition.width = width;
                if (!heightNumber) {
                    /**
                     * If layer's height is auto, we must force a layout to get the height of the layer
                     * for the child layer's linked calculation.
                     */
                    _NodeSetStyle(this.layer.layer, 'width', width + 'px');
                    this._sizePosition.height = this.layer.layer.offsetHeight;
                }
            }
            if (heightNumber) {
                this._sizePosition.height = height;
            }
        }

        layerSMW.width = width + (widthNumber ? 'px' : '');
        layerSMW.height = height + (heightNumber ? 'px' : '');
    };

    FrontendPlacementAbsolute.prototype.onResizePosition = function () {

        var ratios = this.___ratios,
            layer = this.layer.layer,
            layerSMW = _N2.MW.___getSMW(layer),
            ratioPositionHorizontal = ratios.slideW,
            ratioPositionVertical = ratios.slideH;

        if (this.isSingleAxis()) {
            ratioPositionVertical = ratioPositionHorizontal;
        }

        if (!parseInt(this.layer.get('responsiveposition'))) {
            ratioPositionHorizontal = ratioPositionVertical = 1;
        }


        var left = this.layer.getDevice('left') * ratioPositionHorizontal,
            top = this.layer.getDevice('top') * ratioPositionVertical,
            align = this.layer.getDevice('align'),
            valign = this.layer.getDevice('valign');

        if (this.parentLayerPlacement && this.parentLayerPlacement.isVisible()) {
            var parentPositionSize = this.parentLayerPlacement.getPositionSize(),
                parentAlignPosition = {left: 0, top: 0};

            if (this.parentLayerPlacement instanceof _N2.FrontendPlacementAbsolute) {
                var parentSMW = _N2.MW.___getSMW(this.parentLayerPlacement.layer.layer);

                parentPositionSize.left += parentSMW.xAbs + parentSMW.xPAbs / 100 * parentPositionSize.width;
                parentPositionSize.top += parentSMW.yAbs + parentSMW.yPAbs / 100 * parentPositionSize.height;
            }

            switch (this.layer.getDevice('parentalign')) {
                case 'right':
                    parentAlignPosition.left = parentPositionSize.left + parentPositionSize.width;
                    break;
                case 'center':
                    parentAlignPosition.left = parentPositionSize.left + parentPositionSize.width / 2;
                    break;
                default:
                    parentAlignPosition.left = parentPositionSize.left;
            }

            switch (this.layer.getDevice('parentvalign')) {
                case 'bottom':
                    parentAlignPosition.top = parentPositionSize.top + parentPositionSize.height;
                    break;
                case 'middle':
                    parentAlignPosition.top = parentPositionSize.top + parentPositionSize.height / 2;
                    break;
                default:
                    parentAlignPosition.top = parentPositionSize.top;
            }

            switch (align) {
                case 'right':
                    layerSMW.xAbs = Math.round(parentAlignPosition.left + left);
                    layerSMW.xPAbs = -100;
                    break;
                case 'center':
                    layerSMW.xAbs = Math.round(parentAlignPosition.left + left);
                    layerSMW.xPAbs = -50;
                    break;
                default:
                    layerSMW.xAbs = Math.round(parentAlignPosition.left + left);
                    layerSMW.xPAbs = 0;
                    break;
            }

            switch (valign) {
                case 'bottom':
                    layerSMW.yAbs = Math.round(parentAlignPosition.top + top);
                    layerSMW.yPAbs = -100;
                    break;
                case 'middle':
                    layerSMW.yAbs = Math.round(parentAlignPosition.top + top);
                    layerSMW.yPAbs = -50;
                    break;
                default:
                    layerSMW.yAbs = Math.round(parentAlignPosition.top + top);
                    layerSMW.yPAbs = 0;
                    break;
            }

        } else {
            var parentWidth,
                parentHeight;

            switch (align) {
                case 'right':
                    if (this.layer.slide.isStatic || !(this.layer.parent instanceof _N2.FrontendComponentSectionSlide)) {
                        /**
                         * Nested or static absolute
                         * @type {number}
                         */
                        parentWidth = this.___parentNodeData.width;
                    } else {
                        parentWidth = this.___dimensions.slideWidth;
                    }
                    layerSMW.xAbs = Math.round(parentWidth + left);
                    layerSMW.xPAbs = -100;
                    break;
                case 'center':
                    if (this.layer.slide.isStatic || !(this.layer.parent instanceof _N2.FrontendComponentSectionSlide)) {
                        /**
                         * Nested or static absolute
                         * @type {number}
                         */
                        parentWidth = this.___parentNodeData.width;
                    } else {
                        parentWidth = this.___dimensions.slideWidth;
                    }

                    layerSMW.xAbs = Math.round(parentWidth / 2 + left);
                    layerSMW.xPAbs = -50;
                    break;
                default:
                    layerSMW.xAbs = Math.round(left);
                    layerSMW.xPAbs = 0;
                    break;
            }

            switch (valign) {
                case 'bottom':
                    if (this.layer.slide.isStatic || !(this.layer.parent instanceof _N2.FrontendComponentSectionSlide)) {
                        /**
                         * Nested or static absolute
                         * @type {number}
                         */
                        parentHeight = this.___parentNodeData.height;
                    } else {
                        parentHeight = this.___dimensions.slideHeight;
                    }

                    layerSMW.yAbs = Math.round(parentHeight + top);
                    layerSMW.yPAbs = -100;
                    break;
                case 'middle':
                    if (this.layer.slide.isStatic || !(this.layer.parent instanceof _N2.FrontendComponentSectionSlide)) {
                        /**
                         * Nested or static absolute
                         * @type {number}
                         */
                        parentHeight = this.___parentNodeData.height;
                    } else {
                        parentHeight = this.___dimensions.slideHeight;
                    }

                    layerSMW.yAbs = Math.round(parentHeight / 2 + top);
                    layerSMW.yPAbs = -50;
                    break;
                default:
                    layerSMW.yAbs = Math.round(top);
                    layerSMW.yPAbs = 0;
                    break;
            }
        }
    }

    FrontendPlacementAbsolute.prototype.onResize = function (ratios, dimensions) {

        if (!this.isVisible()) return;

        this.___ratios = ratios;
        this.___dimensions = dimensions;

        absoluteManager.invalidate(this);

        _N2.FrontendPlacement.prototype.onResize.apply(this, arguments);
    };

    FrontendPlacementAbsolute.prototype.onResizeLinked = function (ratios, dimensions) {
        FrontendPlacementAbsolute.prototype.onResize.call(this, ratios, dimensions);
    };


    FrontendPlacementAbsolute.prototype.getWidth = function (ratio) {
        var width = this.layer.getDevice('width');
        if (this.isDimensionPropertyAccepted(width)) {
            return width;
        }
        return width * ratio
    };

    FrontendPlacementAbsolute.prototype.getHeight = function (ratio) {
        var height = this.layer.getDevice('height');
        if (this.isDimensionPropertyAccepted(height)) {
            return height;
        }
        return height * ratio
    };

    FrontendPlacementAbsolute.prototype.isDimensionPropertyAccepted = function (value) {

        return (value + '').match(/[0-9]+%/) || value === 'auto';
    };

    return FrontendPlacementAbsolute;
});_N2.d('FrontendPlacementContent', ['FrontendPlacement'], function () {

    /**
     * @memberOf _N2
     *
     * @param layer
     * @constructor
     */
    function FrontendPlacementContent(layer) {
        _N2.FrontendPlacement.prototype.constructor.apply(this, arguments);
    }

    FrontendPlacementContent.prototype = Object.create(_N2.FrontendPlacement.prototype);
    FrontendPlacementContent.prototype.constructor = FrontendPlacementContent;

    return FrontendPlacementContent;
});_N2.d('FrontendPlacementDefault', ['FrontendPlacement'], function () {

    /**
     * @memberOf _N2
     *
     * @param layer
     * @constructor
     */
    function FrontendPlacementDefault(layer) {
        _N2.FrontendPlacement.prototype.constructor.apply(this, arguments);
    }

    FrontendPlacementDefault.prototype = Object.create(_N2.FrontendPlacement.prototype);
    FrontendPlacementDefault.prototype.constructor = FrontendPlacementDefault;

    return FrontendPlacementDefault;
});_N2.d('FrontendPlacementNormal', ['FrontendPlacement'], function () {

    /**
     * @memberOf _N2
     *
     * @param layer
     * @constructor
     */
    function FrontendPlacementNormal(layer) {
        _N2.FrontendPlacement.prototype.constructor.apply(this, arguments);
    }

    FrontendPlacementNormal.prototype = Object.create(_N2.FrontendPlacement.prototype);
    FrontendPlacementNormal.prototype.constructor = FrontendPlacementNormal;

    return FrontendPlacementNormal;
});_N2.d('FrontendComponentCol', ['FrontendComponentCommon'], function () {

    /**
     * @memberOf _N2
     *
     * @param slide
     * @param parent
     * @param el
     * @constructor
     */
    function FrontendComponentCol(slide, parent, el) {

        this.content = el.querySelector('.n2-ss-layer-col');

        _N2.FrontendComponentCommon.prototype.constructor.call(this, slide, parent, el, this.content.querySelectorAll(':scope > .n2-ss-layer'));
    }

    FrontendComponentCol.prototype = Object.create(_N2.FrontendComponentCommon.prototype);
    FrontendComponentCol.prototype.constructor = FrontendComponentCol;

    FrontendComponentCol.prototype.getContents = function () {
        return [this.content];
    };

    return FrontendComponentCol;
});_N2.d('FrontendComponentContent', ['FrontendComponentCommon'], function () {

    /**
     * @memberOf _N2
     *
     * @param slide
     * @param parent
     * @param el
     * @constructor
     *
     * @augments {_N2.FrontendComponentCommon}
     */
    function FrontendComponentContent(slide, parent, el) {

        this.content = el.querySelector('.n2-ss-section-main-content');

        _N2.FrontendComponentCommon.prototype.constructor.call(this, slide, parent, el, this.content.querySelectorAll(':scope > .n2-ss-layer'));
    }

    FrontendComponentContent.prototype = Object.create(_N2.FrontendComponentCommon.prototype);
    FrontendComponentContent.prototype.constructor = FrontendComponentContent;

    FrontendComponentContent.prototype.getContents = function () {
        return [this.content];
    };

    return FrontendComponentContent;
});_N2.d('FrontendComponentLayer', ['FrontendComponentCommon'], function () {

    /**
     * @memberOf _N2
     *
     * @param slide
     * @param parent
     * @param el
     * @constructor
     */
    function FrontendComponentLayer(slide, parent, el) {
        _N2.FrontendComponentCommon.prototype.constructor.call(this, slide, parent, el);

        var container = el;
        if (this.wraps.mask) {
            container = this.wraps.mask;
        }

        this.item = container.querySelectorAll(':scope > *');
    }

    FrontendComponentLayer.prototype = Object.create(_N2.FrontendComponentCommon.prototype);
    FrontendComponentLayer.prototype.constructor = FrontendComponentLayer;

    FrontendComponentLayer.prototype.getContents = function () {
        return this.item;
    };

    return FrontendComponentLayer;
});_N2.d('FrontendComponentRow', ['FrontendComponentCommon'], function () {

    /**
     * @memberOf _N2
     *
     * @param slide
     * @param parent
     * @param el
     * @constructor
     */
    function FrontendComponentRow(slide, parent, el) {

        this.row = el.querySelector('.n2-ss-layer-row');
        this.rowInner = el.querySelector('.n2-ss-layer-row-inner');

        _N2.FrontendComponentCommon.prototype.constructor.call(this, slide, parent, el, this.rowInner.querySelectorAll(':scope > .n2-ss-layer'));
    }

    FrontendComponentRow.prototype = Object.create(_N2.FrontendComponentCommon.prototype);
    FrontendComponentRow.prototype.constructor = FrontendComponentRow;

    FrontendComponentRow.prototype.getContents = function () {
        return [this.row];
    };

    return FrontendComponentRow;
});_N2.d('FrontendComponentSectionSlide', ['FrontendComponent'], function () {

    /**
     * @memberOf _N2
     *
     * @param {_N2.FrontendSliderSlideAbstract} realSlide
     * @param  {_N2.SmartSliderAbstract} slider
     * @param el
     *
     * @constructor
     *
     * @augments {_N2.FrontendComponent}
     */
    function FrontendComponentSectionSlide(realSlide, slider, el) {
        /**
         * @type {_N2.FrontendSliderSlideAbstract}
         */
        this.realSlide = realSlide;

        /**
         * @type {_N2.SmartSliderAbstract}
         */
        this.slider = slider;

        this.element = realSlide.element;

        this.layer = el;

        this.isStatic = realSlide.isStatic();

        _N2.FrontendComponent.prototype.constructor.call(this, this, this, el, el.querySelectorAll(':scope > .n2-ss-layer'));

        _addEventListener(slider.sliderElement, 'SliderDeviceOrientation', (function (e) {
            this.onDeviceChange(e.detail.device.toLowerCase());
        }).bind(this));

        _addEventListener(slider.sliderElement, 'SliderResize', (function (e) {
            this.onResize(e.detail.ratios, e.detail.responsive.resizeContext);
        }).bind(this));

        this.start();
    }

    FrontendComponentSectionSlide.prototype = Object.create(_N2.FrontendComponent.prototype);
    FrontendComponentSectionSlide.prototype.constructor = FrontendComponentSectionSlide;

    FrontendComponentSectionSlide.prototype.onDeviceChange = function (device) {
        _N2.FrontendComponent.prototype.onDeviceChange.call(this, device);

        for (var i = 0; i < this.children.length; i++) {
            this.children[i].onDeviceChange(device)
        }

        this.realSlide.onDeviceChange(device);
    };

    return FrontendComponentSectionSlide;
});_N2.d('SmartSliderResponsive', function () {

    let scrollingTimeout,
        isScrolling = false;

    document.addEventListener('scroll', function () {
        if (scrollingTimeout) {
            clearTimeout(scrollingTimeout);
        }
        isScrolling = true;
        scrollingTimeout = setTimeout(function () {
            isScrolling = false;
        }, 300);
    }, {
        capture: true,
        passive: true
    });

    /**
     * @memberOf _N2
     *
     * @param {_N2.SmartSliderAbstract} slider
     * @param parameters
     * @constructor
     */
    function SmartSliderResponsive(slider, parameters) {
        this.state = {
            StarterSlide: false
        };

        this.isVisible = true;

        this.isResetActiveSlideEarly = this.isResetActiveSlideEarly || false;

        this.focusOffsetTop = 0;

        this.focusOffsetBottom = 0;

        /**
         * Used to calculate editor notice about slide height calculation.
         * @type {number}
         */
        this.minimumSlideHeight = 0;

        this.___isFullScreen = false;

        this.visibleRealSlidesChanged = true;

        this.___windowInnerWidth = -1;
        this.___windowInnerHeight = -1;
        this.___windowProcessedInnerWidth = -1;
        this.___windowProcessedInnerHeight = -1;

        this.filters = {
            SliderWidth: [],
            SliderHeight: [],
            SlideHeight: [],
            SliderVerticalCSS: []
        };

        this.parameters = _Assign({
            mediaQueries: {},
            hideOn: {
                desktopLandscape: 0,
                desktopPortrait: 0,
                tabletLandscape: 0,
                tabletPortrait: 0,
                mobileLandscape: 0,
                mobilePortrait: 0
            },

            onResizeEnabled: true,
            type: 'auto',
            focusUser: 1,
            focusEdge: 'auto',

            enabledDevices: {
                desktopLandscape: 1,
                desktopPortrait: 0,
                mobileLandscape: 0,
                mobilePortrait: 0,
                tabletLandscape: 0,
                tabletPortrait: 0
            },
            breakpoints: [],
            sizes: {
                desktopPortrait: {
                    width: 1200,
                    height: 600,
                    max: 10000,
                    min: 40
                }
            },
            ratioToDevice: {
                Portrait: {
                    tablet: 0,
                    mobile: 0
                },
                Landscape: {
                    tablet: 0,
                    mobile: 0
                }
            },

            overflowHiddenPage: 0,
            focus: {
                offsetTop: '',
                offsetBottom: ''
            }
        }, parameters);

        this.mediaQueries = {};

        for (var device in this.parameters.mediaQueries) {
            if (this.parameters.mediaQueries[device]) {
                this.mediaQueries[device] = window.matchMedia(this.parameters.mediaQueries[device].join(','));
            }
        }

        this.parameters.hideOn = window.ssOverrideHideOn || this.parameters.hideOn;

        this.doThrottledResize = NextendThrottle(this.doResize.bind(this), 50);

        /**
         * @type {_N2.SmartSliderAbstract}
         */
        this.slider = slider;
        this.sliderElement = slider.sliderElement;

        this.___isLegacyFontScaleEnabled = _NodeGetData(this.sliderElement, 'ssLegacyFontScale') === '1';

        this.addFilter('SliderWidth', this.filterSliderWidthHorizontalSpacing.bind(this));
    }

    SmartSliderResponsive.DeviceMode = {
        unknown: 0,
        desktoplandscape: 1,
        desktopportrait: 2,
        tabletlandscape: 3,
        tabletportrait: 4,
        mobilelandscape: 5,
        mobileportrait: 6
    };

    SmartSliderResponsive._DeviceMode = {
        0: 'unknown',
        1: 'desktopLandscape',
        2: 'desktopPortrait',
        3: 'tabletLandscape',
        4: 'tabletPortrait',
        5: 'mobileLandscape',
        6: 'mobilePortrait'
    };

    SmartSliderResponsive._DeviceGroup = {
        'desktopLandscape': 'desktop',
        'desktopPortrait': 'desktop',
        'tabletLandscape': 'tablet',
        'tabletPortrait': 'tablet',
        'mobileLandscape': 'mobile',
        'mobilePortrait': 'mobile'
    };

    SmartSliderResponsive.prototype.init = function () {

        this.base = this.parameters.base;

    };

    SmartSliderResponsive.prototype.setDeviceID = function (deviceID) {

        /**
         * @type {number}
         */
        this.deviceID = deviceID;

        /**
         * @type {string}
         */
        this.device = SmartSliderResponsive._DeviceMode[deviceID];
    };

    SmartSliderResponsive.prototype.start = function () {

        this.slider.stages.done('ResizeFirst', (function () {
            n2const.fonts.then((function () {
                this.slider.stages.resolve('Fonts');
            }).bind(this));
        }).bind(this));


        this.normalizeTimeout = null;

        this.setDeviceID(SmartSliderResponsive.DeviceMode.unknown);

        this.ratios = {
            slideW: 1,
            slideH: 1
        };

        /**
         * @type {{Left: [], Right: []}}
         */
        this.horizontalSpacingControls = {
            right: [],
            left: []
        };

        this.horizontalSpacing = {
            right: 0,
            left: 0
        };

        this.staticSizes = {
            paddingTop: 0,
            paddingRight: 0,
            paddingBottom: 0,
            paddingLeft: 0
        };


        this.alignElement = this.slider.sliderElement.closest('.n2-ss-align');
        this.___sectionElement = this.slider.___sectionElement;

        /**
         * @type {Element}
         */
        this.containerElementPadding = this.sliderElement.parentNode;
        /**
         * @type {Element}
         */
        this.containerElement = this.containerElementPadding.parentNode;


        if (!this.slider.isAdmin && this.parameters.overflowHiddenPage) {
            _NodeListSetStyle([html, body], 'overflow', 'hidden');
        }

        _N2._smallestZoom = 320;

        this.initFocusOffsetObserver();

        this.slider.stages.resolve('ResponsiveStart');

        this.init();

        if (this.parameters.onResizeEnabled) {

            _addEventListener(window, 'resize', this.onResize.bind(this));

            this.lastROWidth = 0;
            var observedElement = this.containerElement.parentNode,
                observer = new ResizeObserver((function (entries) {
                    this.processResizeObserverRect(entries[0].contentRect);
                }).bind(this));

            requestAnimationFrame((function () {
                this.processResizeObserverRect(observedElement.getBoundingClientRect());

                observer.observe(observedElement);
            }).bind(this));
        } else {
            this.onResize();
        }
    };

    SmartSliderResponsive.prototype.processResizeObserverRect = function (contentRect) {

        this.isVisible = !!contentRect.width;
        if (this.lastROWidth !== contentRect.width) {
            this.lastROWidth = contentRect.width;
            this.internalResize();
        }
    };

    SmartSliderResponsive.prototype.internalResize = function () {
        this.onResize();
    };

    SmartSliderResponsive.prototype.getDeviceMode = function () {
        return SmartSliderResponsive._DeviceMode[this.deviceID];
    };

    SmartSliderResponsive.prototype.getDeviceGroup = function () {
        return SmartSliderResponsive._DeviceGroup[this.getDeviceMode()];
    };

    SmartSliderResponsive.prototype.onResize = function (e) {

        this.___windowInnerWidth = window.innerWidth;
        this.___windowInnerHeight = window.innerHeight;

        if (!this.slider.mainAnimation || this.slider.mainAnimation.getState() !== 'playing') {
            this._onResize(e);
        } else if (!this.___delayedResizeAdded) {
            this.___delayedResizeAdded = true;
            _addEventListenerOnce(this.sliderElement, 'mainAnimationComplete', this._onResize.bind(this, e));
        }
    };

    SmartSliderResponsive.prototype._onResize = function (e) {
        this.doResize(e);
        delete this.___delayedResizeAdded;
    };


    SmartSliderResponsive.prototype.doNormalizedResize = function () {
        if (this.normalizeTimeout) {
            clearTimeout(this.normalizeTimeout);
        }

        this.normalizeTimeout = setTimeout(this.doResize.bind(this), 10);
    };

    /**
     * @returns {number}
     */
    SmartSliderResponsive.prototype.identifyDeviceID = function () {

        for (var device in this.mediaQueries) {
            if (this.mediaQueries[device].matches) {
                return SmartSliderResponsive.DeviceMode[device];
            }
        }

        return SmartSliderResponsive.DeviceMode.desktopportrait;
    };

    SmartSliderResponsive.prototype.initFocusOffsetObserver = function () {

        if (this.parameters.focus.offsetTop !== '') {
            var offsetTopElements;
            try {
                offsetTopElements = document.querySelectorAll(this.parameters.focus.offsetTop);
            } catch (e) {
                console.error('The Top CSS selector: "' + this.parameters.focus.offsetTop + '" used in the slider is invalid!');
            }
            if (offsetTopElements && offsetTopElements.length) {
                var topResizeObserver = new ResizeObserver((function (entries) {

                    this.focusOffsetTop = 0;

                    for (var i = 0; i < entries.length; i++) {
                        this.focusOffsetTop += entries[i].target.getBoundingClientRect().height;
                    }

                    this.___refreshVerticalOffset();
                }).bind(this));

                for (var i = 0; i < offsetTopElements.length; i++) {
                    topResizeObserver.observe(offsetTopElements[i]);
                }
            }

        }

        if (this.parameters.focus.offsetBottom !== '') {
            var offsetBottomElements;
            try {
                offsetBottomElements = document.querySelectorAll(this.parameters.focus.offsetBottom);
            } catch (e) {
                console.error('The Bottom CSS selector: "' + this.parameters.focus.offsetBottom + '" used in the slider is invalid!');
            }

            if (offsetBottomElements && offsetBottomElements.length) {
                var bottomResizeObserver = new ResizeObserver((function (entries) {

                    this.focusOffsetBottom = 0;

                    for (var i = 0; i < entries.length; i++) {
                        this.focusOffsetBottom += entries[i].target.getBoundingClientRect().height;
                    }

                    this.___refreshVerticalOffset();
                }).bind(this));

                for (var i = 0; i < offsetBottomElements.length; i++) {
                    bottomResizeObserver.observe(offsetBottomElements[i]);
                }
            }

        }
    };
    SmartSliderResponsive.prototype.___refreshVerticalOffset = function () {

        _NodeSetStyle(this.sliderElement, '--subtract-vertical-offset', ((window.n2OffsetTop || this.focusOffsetTop) + (window.n2OffsetBottom || this.focusOffsetBottom)) + 'px');

        this.doResize();
    };

    SmartSliderResponsive.prototype.doResize = function (e) {
        if (!this.slider.isAdmin && isScrolling && this.___windowProcessedInnerWidth === this.___windowInnerWidth) {
            const diff = Math.abs(this.___windowProcessedInnerHeight - this.___windowInnerHeight);
            /**
             * During scroll, the topbar height might change on IOS so we can skip this resize.
             */
            if (diff > 0 && diff < 100) {
                return;
            }
        }

        this.___windowProcessedInnerWidth = this.___windowInnerWidth;
        this.___windowProcessedInnerHeight = this.___windowInnerHeight;

        var identifiedDeviceID = this.identifyDeviceID();


        if (this.parameters.hideOn[SmartSliderResponsive._DeviceMode[identifiedDeviceID]]) {
            _NodeAddClass(this.___sectionElement, 'n2-section-smartslider--hidden');
            /**
             * The slider hidden on this device;
             */
            return false;
        }

        _NodeRemoveClass(this.___sectionElement, 'n2-section-smartslider--hidden');

        if (!this.isVisible) {
            /**
             * The slider is not visible, so there is nothing to resize.
             */
            return false;
        }

        var isDeviceChanged = false,
            lastDevice = this.device;

        if (this.deviceID !== identifiedDeviceID) {

            this.setDeviceID(identifiedDeviceID);

            if (lastDevice) {
                _NodeRemoveClass(this.sliderElement, 'n2-ss-' + lastDevice);
            }
            _NodeSetData(this.sliderElement, 'deviceMode', this.device);
            _NodeAddClass(this.sliderElement, 'n2-ss-' + this.device);

            this.slider.__$dispatchEvent('SliderDevice', {
                lastDevice: lastDevice,
                device: this.device,
                group: SmartSliderResponsive._DeviceGroup[this.device]
            });

            isDeviceChanged = true;

            this.slider.stages.resolve('Device');
        }

        if (isDeviceChanged) {

            this.slider.visibleRealSlides = [];

            this.slider.publicDispatchEvent('SliderDeviceOrientation', {
                slider: this.slider,
                lastDevice: lastDevice,
                device: this.device,
                group: SmartSliderResponsive._DeviceGroup[this.device]
            });

            this.slider.stages.resolve('DeviceOrientation');

            this.finalizeVisibleSlidesStage1();

        }

        if (this.slider.visibleRealSlides.length) {

            if (this.slider.isVisible || this.visibleRealSlidesChanged) {
                for (var i = 0; i < this.slider.realSlides.length; i++) {
                    _N2.MW.___getSMW(this.slider.realSlides[i].element).setValues({
                        width: '',
                        height: ''
                    });
                    _NodeSetStyles(this.slider.realSlides[i].element, {
                        width: '',
                        height: ''
                    });
                }

                this.resizeStage1Width();
                this.slider.__$dispatchEvent('SliderResizeHorizontal');
                this.resizeStage2Height();
            }
        } else {
            this.parameters.hideOn[SmartSliderResponsive._DeviceMode[identifiedDeviceID]] = true;
            _NodeAddClass(this.___sectionElement, 'n2-section-smartslider--hidden');
            /**
             * The slider hidden on this device;
             */
            return false;
        }
    };

    SmartSliderResponsive.prototype.resizeStage1Width = function () {
        this.resizeContext = {};
    };

    SmartSliderResponsive.prototype.resizeStage2Height = function () {

        this.finalizeVisibleSlidesStage2();

        this.calculateResponsiveValues();

        this.slider.stages.resolve('ResizeFirst');

        this.triggerResize();
    };

    SmartSliderResponsive.prototype.calculateResponsiveValues = function () {

        this.ratios = {
            slideW: this.resizeContext.slideWidth / this.base.slideWidth,
            slideH: this.resizeContext.slideHeight / this.base.slideHeight
        };

        if (this.___isLegacyFontScaleEnabled) {
            _NodeListSetStyle(this.sliderElement.querySelectorAll('.n2-ss-layer[data-sstype="slide"] > .n2-ss-layer[data-pm="absolute"][data-adaptivefont="0"]'), 'font-size', 'calc(' + (this.ratios.slideW * 16) + 'px * var(--ssfont-scale, 1))');
        }

        for (var i = 0; i < this.slider.realSlides.length; i++) {
            _N2.MW.___getSMW(this.slider.realSlides[i].element).setValues({
                width: this.resizeContext.slideSelfWidth,
                height: this.resizeContext.slideSelfHeight
            });
        }
    };

    SmartSliderResponsive.prototype.onStarterSlide = function (realStarterSlide) {
        this.state.StarterSlide = true;

        this.calibrateActiveSlide(realStarterSlide);
        delete this.targetCurrentSlide;
    };

    SmartSliderResponsive.prototype.finalizeVisibleSlidesStage1 = function () {

        if (this.visibleRealSlidesChanged) {

            /**
             * We must sort the visibleRealSlides to match the order with the other slides array.
             * Without sorting, they might get out of sync when randomize slides enabled.
             */
            this.slider.visibleRealSlides.sort(function (a, b) {
                return a.index - b.index;
            });

            this.updateVisibleSlides();

            this.slider.__$dispatchEvent('visibleRealSlidesChanged');
            this.slider.stages.resolve('VisibleRealSlides');


            if (this.isResetActiveSlideEarly) {
                this.calibrateActiveSlide();
            }
        }
    };

    SmartSliderResponsive.prototype.updateVisibleSlides = function () {

        this.slider.visibleSlides = this.slider.visibleRealSlides;
    };

    SmartSliderResponsive.prototype.calibrateActiveSlide = function (targetRealSlide) {
        if (this.state.StarterSlide) {
            if (this.slider.visibleSlides.length > 0) {
                var targetCurrentSlide = targetRealSlide || this.slider.currentRealSlide;
                if (!targetCurrentSlide.isVisible) {
                    targetCurrentSlide = targetCurrentSlide.getNext();
                    if (!targetCurrentSlide) {
                        targetCurrentSlide = this.slider.currentSlide.getPrevious();
                    }
                }

                this.resetActiveRealSlide(targetCurrentSlide);
            }
        }
    };

    /**
     * When slider resized, we must ensure that the proper slides are visible and active
     * @param targetRealSlide
     */
    SmartSliderResponsive.prototype.resetActiveRealSlide = function (targetRealSlide) {
        var oldSlide,
            isRealChange = targetRealSlide && targetRealSlide !== this.slider.currentRealSlide,
            currentSlide;

        if (isRealChange) {
            this.slider.__$dispatchEvent('BeforeCurrentSlideChange', targetRealSlide);

            oldSlide = this.slider.currentSlide;
            if (oldSlide) {
                this.slider.forceUnsetActiveSlide(oldSlide);
            }

            this.slider.setCurrentRealSlide(targetRealSlide);

            currentSlide = this.slider.currentSlide;

            this.targetCurrentSlide = currentSlide;

            this.slider.forceSetActiveSlide(currentSlide);

            this.slider.__$dispatchEvent('SlideForceChange', {
                oldSlide: oldSlide,
                currentSlide: currentSlide
            });
        } else {
            currentSlide = this.slider.currentSlide;
        }

        this.slider.updateInsideSlides([currentSlide]);
    };

    SmartSliderResponsive.prototype.finalizeVisibleSlidesStage2 = function () {

        if (this.visibleRealSlidesChanged) {
            this.visibleRealSlidesChanged = false;
            if (!this.isResetActiveSlideEarly) {
                this.calibrateActiveSlide();
            }

            this.triggerVisibleSlidesChanged();

            if (this.targetCurrentSlide !== undefined) {

                this.slider.__$dispatchEvent('SlideWillChange', {
                    targetSlide: this.targetCurrentSlide
                });

                this.slider.__$dispatchEvent('CurrentSlideChanged', {
                    currentSlide: this.targetCurrentSlide
                });

                if (this.slider.stages.resolved('Visible')) {
                    this.slider.playSlide(this.targetCurrentSlide);
                }

                delete this.targetCurrentSlide;
            }
        }
    };

    SmartSliderResponsive.prototype.triggerVisibleSlidesChanged = function () {
        this.slider.__$dispatchEvent('visibleSlidesChanged');
        this.slider.stages.resolve('VisibleSlides');

        if (this.slider.visibleRealSlides.length) {
            if (!this.slider.isVisible) {
                this.slider.show();
            }
        } else {
            if (this.slider.isVisible) {
                this.slider.hide();
            }
        }
    };

    SmartSliderResponsive.prototype.getNormalizedModeString = function () {

        return SmartSliderResponsive._DeviceMode[this.deviceID];
    };

    SmartSliderResponsive.prototype.triggerResize = function () {

        this.slider.publicDispatchEvent('SliderResize', {
            ratios: this.ratios,
            responsive: this
        });
        this.slider.stages.resolve('Resized');
    };

    SmartSliderResponsive.prototype.getVerticalOffsetHeight = function () {

        if (this.___isFullScreen) {
            return 0;
        }

        return this.slider.widgets.getVerticalsHeight();
    };

    SmartSliderResponsive.prototype.addHorizontalSpacingControl = function (side, widget) {
        this.horizontalSpacingControls[side].push(widget);

        if (this.slider.stages.resolved('ResizeFirst')) {
            this.doNormalizedResize();
        }
    };

    SmartSliderResponsive.prototype.filterSliderWidthHorizontalSpacing = function (sliderWidth) {

        this.horizontalSpacing = {
            right: 0,
            left: 0
        };

        for (var side in this.horizontalSpacingControls) {
            var controls = this.horizontalSpacingControls[side];
            for (var i = controls.length - 1; i >= 0; i--) {
                var control = controls[i];
                if (control.isVisible()) {
                    control.refreshSliderSize(sliderWidth);
                    this.horizontalSpacing[side] += control.getSize();
                }
            }
        }

        _NodeSetStyles(this.containerElementPadding, {
            paddingLeft: this.horizontalSpacing.left + 'px',
            paddingRight: this.horizontalSpacing.right + 'px',
        });

        return sliderWidth - this.horizontalSpacing.left - this.horizontalSpacing.right;
    };

    /**
     * @param {string} tag
     * @param {function} callback
     */
    SmartSliderResponsive.prototype.addFilter = function (tag, callback) {
        this.filters[tag].push(callback);
    };

    /**
     * @param {string} tag
     * @param {function} callback
     */
    SmartSliderResponsive.prototype.removeFilter = function (tag, callback) {
        this.filters[tag].push(callback);
    };

    SmartSliderResponsive.prototype.applyFilter = function (tag, value) {
        for (var i = 0; i < this.filters[tag].length; i++) {
            value = this.filters[tag][i].call(this, value);
        }
        return value;
    };

    return SmartSliderResponsive;
});
_N2.d('FrontendItemVimeo', function () {

    /**
     * @memberOf _N2
     *
     * @param slider
     * @param id
     * @param sliderid
     * @param parameters
     * @param hasImage
     * @param start
     * @constructor
     */
    function FrontendItemVimeo(slider, id, sliderid, parameters, hasImage, start) {
        this.state = {
            slideVisible: false,
            visible: false,
            scroll: false,
            slide: false,
            InComplete: false,
            play: false,
            continuePlay: false
        };
        this.promise = new Promise((function (resolve) {
            this._resolve = resolve;
        }).bind(this));

        this.slider = slider;
        this.playerId = id;
        this.playerElement = document.getElementById(this.playerId);
        this.cover = this.playerElement.querySelector('.n2_ss_video_player__cover');
        this.hasImage = hasImage;

        this.start = start;

        this.parameters = _Assign({
            vimeourl: "//vimeo.com/144598279",
            privateid: "",
            autoplay: "0",
            ended: "",
            reset: "0",
            title: "1",
            byline: "1",
            portrait: "0",
            loop: "0",
            color: "00adef",
            volume: "-1",
            dnt: "0"
        }, parameters);


        if (parseInt(this.parameters.autoplay) === 1) {
            if (navigator.userAgent.toLowerCase().indexOf("android") > -1) {
                this.parameters.volume = 0;
            } else if (n2const.isIOS) {
                this.parameters.autoplay = 0;
                try {
                    if ('playsInline' in _CreateElement('video')) {
                        this.parameters.autoplay = 1;
                        this.parameters.volume = 0;
                    }
                } catch (e) {
                }
            }
        }

        if (!n2const.isLighthouse) {
            _N2.r('windowLoad', this.whenLoaded.bind(this));
        }
    }

    FrontendItemVimeo.prototype.whenLoaded = function () {

        if (parseInt(this.parameters.autoplay) === 1 || !this.hasImage || n2const.isMobile) {
            this.ready(this.initVimeoPlayer.bind(this));
        } else {
            this.ready((function () {
                var startCallback = (function (e) {
                        _removeEventListeners(eventListeners);
                        e.preventDefault();
                        e.stopPropagation();

                        this.initVimeoPlayer();
                        this.safePlay();
                    }).bind(this),
                    eventListeners = [
                        _addEventListenerWithRemover(this.playerElement, 'click', startCallback),
                        _addEventListenerWithRemover(this.playerElement, 'n2click', startCallback)
                    ];
            }).bind(this));
        }
    };

    var promise;

    FrontendItemVimeo.prototype.ready = function (callback) {
        if (!promise) {
            promise = new Promise(function (resolve) {
                var script = _CreateElement('script');
                script.onload = resolve;
                script.src = "https://player.vimeo.com/api/player.js";
                document.head.appendChild(script);
            });
        }
        promise.then(callback);
    };

    FrontendItemVimeo.prototype.initVimeoPlayer = function () {

        var playerFrame = _CreateElement('iframe');
        playerFrame.className = 'intrinsic-ignore';
        playerFrame.allow = 'fullscreen; autoplay; encrypted-media';
        playerFrame.id = this.playerId + '-frame';
        playerFrame.src = 'https://player.vimeo.com/video/' + this.parameters.vimeocode + '?autoplay=0&_video&title=' + this.parameters.title + '&byline=' + this.parameters.byline + "&background=" + this.parameters.background + '&portrait=' + this.parameters.portrait + '&color=' + this.parameters.color + '&loop=' + this.parameters.loop + (this.parameters.quality == '-1' ? '' : '&quality=' + this.parameters.quality) + '&dnt=' + this.parameters['privacy-enhanced'] + (this.parameters.privateid !== '' ? '&h=' + this.parameters.privateid : '') + '&playsinline=1';
        playerFrame.webkitAllowFullScreen = true;
        playerFrame.allowFullScreen = true;
        if (this.parameters['iframe-title'] !== undefined && this.parameters['iframe-title'] !== '') {
            playerFrame.title = this.parameters['iframe-title'];
        }
        _NodeSetStyles(playerFrame, {
            position: 'absolute',
            top: 0,
            left: 0,
            width: '100%',
            height: '100%'
        });
        this.playerElement.prepend(playerFrame);

        this.player = new Vimeo.Player(playerFrame, {autoplay: false});
        this.promise = this.player.ready();

        this.slider.stages.done('BeforeShow', (function () {
            this.promise.then(this.onReady.bind(this));
        }).bind(this));
    };

    FrontendItemVimeo.prototype.onReady = function () {
        var volume = parseFloat(this.parameters.volume);
        if (volume >= 0) {
            this.setVolume(volume);
        }

        /**
         * @type {_N2.FrontendSlideControls}
         */
        this.slide = this.slider.findSlideByElement(this.playerElement);

        this.isStatic = this.slide.isStatic();

        var layer = this.playerElement.closest(".n2-ss-layer");
        this.layer = layer.layer;


        var resizeObserver = new ResizeObserver((function (entries) {
            var contentRect = entries[0].contentRect;
            if (contentRect.width && contentRect.height) {
                if (!this.state.visible) {
                    this.setState('visible', true, true);
                }
            } else {
                if (this.state.visible) {
                    var isPlaying = this.state.play;
                    this.setState('visible', false, true);
                    if (isPlaying) {
                        this.setState('continuePlay', true);
                    }
                }
            }
        }).bind(this));

        resizeObserver.observe(this.layer.layer);

        if (this.slide.isVisible) {
            this.setState('slideVisible', true, true);
        }

        _addEventListener(this.slide.element, 'Hidden', (function () {
            var isPlaying = this.state.play;
            this.setState('slideVisible', false, true);
            if (isPlaying) {
                this.setState('continuePlay', true);
            }
        }).bind(this));

        _addEventListener(this.slide.element, 'Visible', (function () {
            this.setState('slideVisible', true, true);
        }).bind(this));

        if (this.cover) {
            if (n2const.isMobile) {
                _addEventListenerOnce(this.cover, 'click', this.safePlay.bind(this));
            }
            _addEventListenerOnce(layer, 'n2play', (function () {
                _N2.___Tween.to(_N2.MW.___getSMW(this.cover), 0.3, {
                    opacity: 0,
                    onComplete: (function () {
                        _NodeRemove(this.cover);
                    }).bind(this)
                });
            }).bind(this));
        }

        this.player.on('play', (function () {
            if (!this.isStatic) {
                _dispatchCustomEventNoBubble(this.slider.sliderElement, 'mediaStarted', {id: this.playerId});
            }
            _dispatchEventSimpleNoBubble(layer, 'n2play');
        }).bind(this));

        this.player.on('pause', (function () {
            _dispatchEventSimpleNoBubble(layer, 'n2pause');
            if (this.state.continuePlay) {
                this.setState('continuePlay', false);
                this.setState('play', true);
            } else {
                this.setState('play', false);
            }
        }).bind(this));

        this.player.on('ended', (function () {
            if (!this.isStatic) {
                _dispatchCustomEventNoBubble(this.slider.sliderElement, 'mediaEnded', {id: this.playerId});
            }
            _dispatchEventSimpleNoBubble(layer, 'n2stop');
            this.setState('play', false);

            if (this.parameters.ended === 'next' && this.parameters.loop == 0) {

                if (document.fullscreenElement || document.webkitFullscreenElement) {
                    (document.exitFullscreen || document.webkitExitFullscreen).call(document);
                }

                this.slider.next(true);
            }

        }).bind(this));

        if (!this.isStatic) {
            //pause video when slide changed

            _addEventListener(this.slider.sliderElement, 'CurrentSlideChanged', (function (e) {
                this.onCurrentSlideChange(e.detail.currentSlide);
            }).bind(this));

            _addEventListener(this.slider.sliderElement, 'mainAnimationStart', (function (e) {
                this.onCurrentSlideChange(this.slider.slides[e.detail.currentSlideIndex]);
            }).bind(this));
        }

        if (this.parameters['scroll-pause'] !== '') {
            _N2.ScrollTracker.add(this.playerElement, this.parameters['scroll-pause'], (function () {
                this.setState('scroll', true, true);
            }).bind(this), (function () {
                this.setState('continuePlay', true);
                this.setState('scroll', false, true);
            }).bind(this));
        } else {
            this.setState('scroll', true, true);
        }

        if (this.slide.isActiveWhen()) {
            this.setState('slide', true, true);
        }

        if (parseInt(this.parameters.autoplay) === 1) {
            this.slider.visible(this.initAutoplay.bind(this));
        }

        this._resolve();
    };

    FrontendItemVimeo.prototype.onCurrentSlideChange = function (currentSlide) {
        if (this.slide.isActiveWhen(currentSlide)) {
            if (this.parameters.autoplay == 1) {
                this.setState('play', true);
            }
            this.setState('slide', true, true);
        } else {
            if (parseInt(this.parameters.reset)) {
                this.reset();
            }
            this.setState('slide', true, true);
        }
    };

    FrontendItemVimeo.prototype.initAutoplay = function () {
        this.setState('InComplete', true, true);
    

        if (!this.isStatic) {
            //change slide
            _addEventListener(this.slider.sliderElement, "mainAnimationComplete", (function (e) {
                if (this.slide.isActiveWhen(this.slider.slides[e.detail.currentSlideIndex])) {
                    this.setState('play', true);
                    this.setState('slide', true, true);
                } else {
                    this.setState('slide', false, true);
                }
            }).bind(this));

            if (this.slide.isActiveWhen()) {
                this.setState('play', true);
                this.setState('slide', true, true);
            }
        } else {
            this.setState('play', true);
            this.setState('slide', true, true);
        }
    };

    FrontendItemVimeo.prototype.setState = function (name, value, doAction) {
        doAction = doAction || false;

        this.state[name] = value;

        if (doAction) {
            if (this.state.slideVisible && this.state.visible && this.state.play && this.state.slide && this.state.InComplete && this.state.scroll && this.layer.isVisible) {
                this.play();
            } else {
                this.pause();
            }

            if (this.state.slideVisible && this.state.visible && this.state.slide && this.layer.isVisible) {
                _NodeRemoveAttribute(this.player.element, 'tabindex');
            } else {
                _NodeSetAttribute(this.player.element, 'tabindex', '-1');
            }
        }
    };

    FrontendItemVimeo.prototype.play = function () {
        _dispatchCustomEventNoBubble(this.slider.sliderElement, 'mediaStarted', {id: this.playerId});

        if (this.start != 0) {
            this.safeSetCurrentTime(this.start);
        }
        this.safePlay();

        this.player.getCurrentTime().then((function (seconds) {
            if (seconds < this.start && this.start != 0) {
                this.safeSetCurrentTime(this.start);
            }
            this.safePlay();
        }).bind(this)).catch((function (error) {
            this.safePlay();
        }).bind(this));
    };

    FrontendItemVimeo.prototype.pause = function () {
        this.safePause();
    };

    FrontendItemVimeo.prototype.reset = function () {
        this.safeSetCurrentTime(this.start);
    };

    FrontendItemVimeo.prototype.setVolume = function (volume) {
        this.safeCallback((function () {
            this.promise = this.player.setVolume(volume);
        }).bind(this));
    };

    FrontendItemVimeo.prototype.safeSetCurrentTime = function (time) {
        this.safeCallback((function () {
            this.promise = this.player.setCurrentTime(time);
        }).bind(this));
    };

    FrontendItemVimeo.prototype.safePlay = function () {
        this.safeCallback((function () {
            this.promise = this.player.getPaused();

            this.safeCallback((function (paused) {
                if (paused) {
                    this.promise = this.player.play();
                    if (this.promise && Promise !== undefined) {
                        this.promise.catch((function (e) {
                            if (e.name === 'NotAllowedError') {
                                // Chrome: https://developers.google.com/web/updates/2017/09/autoplay-policy-changes
                                // Firefox: https://hacks.mozilla.org/2019/02/firefox-66-to-block-automatically-playing-audible-video-and-audio/
                                var autoplayFallbackcallback = (function () {
                                        _removeEventListeners(eventListeners);
                                        if (this.promise !== false) {
                                            this.safePlay();
                                        }
                                    }).bind(this),
                                    eventListeners = [
                                        _addEventListenerWithRemover(body, 'click', autoplayFallbackcallback),
                                        _addEventListenerWithRemover(body, 'n2click', autoplayFallbackcallback),

                                    ];
                            }
                        }).bind(this));
                    }
                }
            }).bind(this));
        }).bind(this));
    };

    FrontendItemVimeo.prototype.safePause = function () {
        this.safeCallback((function () {
            this.promise = this.player.getPaused();

            this.safeCallback((function (paused) {
                if (!paused) {
                    this.promise = this.player.pause();
                }
            }).bind(this));

        }).bind(this));
    };

    FrontendItemVimeo.prototype.safeCallback = function (callback) {
        if (this.promise && Promise !== undefined) {
            this.promise
                .then(callback)
                .catch(callback);
        } else {
            callback();
        }
    };

    return FrontendItemVimeo;
});_N2.d('FrontendItemYouTube', function () {

    /**
     * @memberOf _N2
     *
     * @param slider
     * @param id
     * @param parameters
     * @param hasImage
     * @constructor
     */
    function FrontendItemYouTube(slider, id, parameters, hasImage) {
        this.listeners = {
            play: [],
            autoplay: []
        };

        this.state = {
            slideVisible: false,
            visible: false,
            scroll: false,
            slide: false,
            InComplete: false,
            play: false,
            continuePlay: false
        };
        this.promise = new Promise((function (resolve) {
            this._resolve = resolve;
        }).bind(this));
        this.slider = slider;
        this.playerId = id;
        this.playerElement = document.getElementById(this.playerId);
        this.cover = this.playerElement.querySelector('.n2_ss_video_player__cover');
        this.hasImage = hasImage;

        this.parameters = _Assign({
            youtubeurl: "//www.youtube.com/watch?v=3PPtkRU7D74",
            youtubecode: "3PPtkRU7D74",
            center: 0,
            autoplay: 1,
            ended: '',
            related: "1",
            volume: "-1",
            loop: 0,
            modestbranding: 1,
            reset: 0,
            query: [],
            playsinline: 1
        }, parameters);

        if (!n2const.isLighthouse) {
            _N2.r('windowLoad', this.whenLoaded.bind(this));
        }

        this.shouldPlayWhenReady = false;

        this.hasAutoplayFallback = false;
    }

    FrontendItemYouTube.prototype.whenLoaded = function () {

        if (parseInt(this.parameters.autoplay) === 1 || !this.hasImage || n2const.isMobile) {
            this.ready(this.initYoutubePlayer.bind(this));
        } else {
            var startCallback = (function (e) {
                    _removeEventListeners(eventListeners);
                    e.preventDefault();
                    e.stopPropagation();
                    this.ready((function () {
                        this.promise.then(this.play.bind(this));
                        this.initYoutubePlayer();
                    }).bind(this));
                }).bind(this),
                eventListeners = [
                    _addEventListenerWithRemover(this.playerElement, 'click', startCallback),
                    _addEventListenerWithRemover(this.playerElement, 'n2click', startCallback)
                ];
        }
    };

    var promise;

    FrontendItemYouTube.prototype.ready = function (callback) {

        if (!promise) {
            promise = new Promise(function (resolve) {
                var check;
                if (window._EPYT_ !== undefined) {
                    /**
                     * Fix for https://wordpress.org/plugins/youtube-embed-plus/
                     */
                    check = function () {
                        if (window._EPADashboard_.initStarted === true) {
                            resolve();
                        } else {
                            setTimeout(check, 100);
                        }
                    };
                } else {
                    check = function () {
                        if (window.YT !== undefined && window.YT.loaded) {
                            resolve();
                        } else {
                            setTimeout(check, 100);
                        }
                    };
                }
                check();
            });

            if (window.YT === undefined) {
                var script = _CreateElement('script');
                script.src = "https://www.youtube.com/iframe_api";
                document.head.appendChild(script);
            }
        }
        promise.then(callback);
    };

    FrontendItemYouTube.prototype.fadeOutCover = function () {
        if (this.coverFadedOut === undefined && this.cover) {
            this.coverFadedOut = true;
            _N2.___Tween.to(_N2.MW.___getSMW(this.cover), 0.3, {
                opacity: 0,
                onComplete: (function () {
                    _NodeRemove(this.cover);
                }).bind(this)
            });
        }
    };

    FrontendItemYouTube.prototype.initYoutubePlayer = function () {
        var layerElement = this.layerElement = this.playerElement.closest(".n2-ss-layer");
        /**
         * @type {_N2.FrontendComponent}
         */
        this.layer = layerElement.layer;
        if (this.cover) {
            if (n2const.isMobile) {
                this.listeners.play.push(_addEventListenerWithRemover(this.cover, 'click', () => {
                    /**
                     * If the cover image has been tapped in mobile view before the YouTube API is ready,
                     * then we should start the video automatically as soon as the YouTube API is ready.
                     * @type {boolean}
                     */
                    this.shouldPlayWhenReady = true;
                    _removeEventListeners(this.listeners.play);
                }));
            }
        }

        /**
         * @type {_N2.FrontendSlideControls}
         */
        this.slide = this.slider.findSlideByElement(this.playerElement);

        this.isStatic = this.slide.isStatic();

        var vars = {
            enablejsapi: 1,
            origin: window.location.protocol + "//" + window.location.host,
            wmode: "opaque",
            rel: 1 - this.parameters.related,
            start: this.parameters.start,
            end: this.parameters.end,
            modestbranding: this.parameters.modestbranding,
            playsinline: this.parameters.playsinline
        };

        if (parseInt(this.parameters.autoplay) === 1) {
            if (navigator.userAgent.toLowerCase().indexOf("android") > -1) {
                this.parameters.volume = 0;
            } else if (n2const.isIOS) {
                this.parameters.autoplay = 0;
                try {
                    if ('playsInline' in _CreateElement('video')) {
                        this.parameters.autoplay = 1;
                        this.parameters.volume = 0;

                        vars.playsinline = 1;
                    }
                } catch (e) {
                }
            }
        }

        if (n2const.isIOS && this.parameters.controls) {
            vars.use_native_controls = 1;
        }

        if (this.parameters.center == 1) {
            vars.controls = 0;
        }
        if (this.parameters.controls != 1) {
            vars.autohide = 1;
            vars.controls = 0;
        }

        if (+(navigator.platform.toUpperCase().indexOf('MAC') >= 0 && navigator.userAgent.search("Firefox") > -1)) {
            vars.html5 = 1;
        }

        for (var k in this.parameters.query) {
            if (this.parameters.query.hasOwnProperty(k)) {
                vars[k] = this.parameters.query[k];
            }
        }

        var data = {
            videoId: this.parameters.youtubecode,
            wmode: 'opaque',
            playerVars: vars,
            events: {
                onReady: this.onReady.bind(this),
                onStateChange: (function (state) {
                    switch (state.data) {
                        case YT.PlayerState.PLAYING:
                            if (!this.isStatic) {
                                if (this.slide.isActiveWhen(this.slider.currentSlide)) {
                                    _dispatchCustomEventNoBubble(this.slider.sliderElement, 'mediaStarted', {id: this.playerId});
                                }
                            }
                            _dispatchEventSimpleNoBubble(layerElement, 'n2play');

                            _removeEventListeners(this.listeners.autoplay);
                            break;
                        case YT.PlayerState.PAUSED:
                            _dispatchEventSimpleNoBubble(layerElement, 'n2pause');
                            if (this.state.continuePlay) {
                                this.setState('continuePlay', false);
                                this.setState('play', true);
                            } else {
                                this.setState('play', false);
                            }
                            break;
                        case YT.PlayerState.ENDED:
                            if (this.parameters.loop == 1) {
                                this.player.seekTo(this.parameters.start);
                                this.player.playVideo();
                            } else {
                                if (!this.isStatic) {
                                    _dispatchCustomEventNoBubble(this.slider.sliderElement, 'mediaEnded', {id: this.playerId});
                                }
                                _dispatchEventSimpleNoBubble(layerElement, 'n2stop');
                                this.setState('play', false);

                                if (this.parameters.ended === 'next') {
                                    if (document.fullscreenElement || document.webkitFullscreenElement) {
                                        (document.exitFullscreen || document.webkitExitFullscreen).call(document);
                                    }

                                    this.slider.next(true);
                                }
                            }
                            break;

                    }
                }).bind(this)
            }
        };

        if (this.parameters['privacy-enhanced'] || (window.jQuery && window.jQuery.fn.revolution)) {
            data.host = 'https://www.youtube-nocookie.com';
        }

        this.player = new YT.Player(this.playerId + '-frame', data);
        if (this.parameters.center == 1) {
            _NodeSetStyle(this.playerElement.parentNode, 'overflow', 'hidden');

            this.onResize();

            _addEventListener(this.slider.sliderElement, 'SliderResize', this.onResize.bind(this))
        }
    };

    FrontendItemYouTube.prototype.onReady = function () {

        if (this.cover) {
            if (n2const.isMobile) {
                _addEventListenerOnce(this.cover, 'click', this.play.bind(this));
            }
            _addEventListenerOnce(this.layerElement, 'n2play', this.fadeOutCover.bind(this));
        }

        _removeEventListeners(this.listeners.play);
        if (this.shouldPlayWhenReady) {
            this.play();
        }

        this.slider.stages.done('BeforeShow', this.onBeforeShow.bind(this));
    };

    FrontendItemYouTube.prototype.onBeforeShow = function () {

        var volume = parseFloat(this.parameters.volume);
        if (volume > 0) {
            this.setVolume(volume);
        } else if (volume !== -1) {
            this.player.mute();
        }


        var resizeObserver = new ResizeObserver((function (entries) {
            var contentRect = entries[0].contentRect;
            if (contentRect.width && contentRect.height) {
                if (!this.state.visible) {
                    this.setState('visible', true, true);
                }
            } else {
                if (this.state.visible) {
                    var isPlaying = this.state.play;
                    this.setState('visible', false, true);
                    if (isPlaying) {
                        this.setState('continuePlay', true);
                    }
                }
            }
        }).bind(this));
        resizeObserver.observe(this.layer.layer);

        if (this.slide.isVisible) {
            this.setState('slideVisible', true, true);
        }

        _addEventListener(this.slide.element, 'Hidden', (function () {
            var isPlaying = this.state.play;
            this.setState('slideVisible', false, true);
            if (isPlaying) {
                this.setState('continuePlay', true);
            }
        }).bind(this));

        _addEventListener(this.slide.element, 'Visible', (function () {
            this.setState('slideVisible', true, true);
        }).bind(this));


        if (this.slide.isActiveWhen()) {
            this.setState('slide', true, true);
        }

        if (this.parameters.autoplay == 1) {
            this.slider.visible(this.initAutoplay.bind(this));
        }

        if (!this.isStatic) {
            //pause video when slide changed

            _addEventListener(this.slider.sliderElement, 'CurrentSlideChanged', (function (e) {
                this.onCurrentSlideChange(e.detail.currentSlide);
            }).bind(this));

            _addEventListener(this.slider.sliderElement, 'mainAnimationStart', (function (e) {
                this.onCurrentSlideChange(this.slider.slides[e.detail.currentSlideIndex]);
            }).bind(this));

            if (parseInt(this.parameters.reset)) {
                _addEventListener(this.slider.sliderElement, 'mainAnimationComplete', (function (e) {
                    if (!this.slide.isActiveWhen(this.slider.slides[e.detail.currentSlideIndex])) {
                        if (this.player.getCurrentTime() !== 0) {
                            this.player.seekTo(this.parameters.start);
                        }
                    }
                }).bind(this));
            }
        }
        this._resolve();

        if (this.parameters['scroll-pause'] !== '') {
            _N2.ScrollTracker.add(this.playerElement, this.parameters['scroll-pause'], (function () {
                this.setState('scroll', true, true);
            }).bind(this), (function () {
                this.setState('continuePlay', true);
                this.setState('scroll', false, true);
            }).bind(this));
        } else {
            this.setState('scroll', true, true);
        }
    };

    FrontendItemYouTube.prototype.onCurrentSlideChange = function (currentSlide) {

        var isActive = this.slide.isActiveWhen(currentSlide);
        if (isActive && this.parameters.autoplay == 1) {
            this.setState('play', true);
        }
        this.setState('slide', isActive, true);
    };

    FrontendItemYouTube.prototype.onResize = function () {
        var controls = 100,
            rect = this.playerElement.parentNode.getBoundingClientRect(),
            width = rect.width,
            height = rect.height + controls,
            aspectRatio = 16 / 9,
            styles = {
                width: width + 'px',
                height: height + 'px',
                'margin-top': 0
            };
        styles[n2const.rtl['margin-left']] = 0;
        if (width / height > aspectRatio) {
            styles.height = width * aspectRatio + 'px';
            styles['margin-top'] = (height - width * aspectRatio) / 2 + 'px';
        } else {
            styles.width = height * aspectRatio + 'px';
            styles[n2const.rtl['margin-left']] = (width - height * aspectRatio) / 2 + 'px';
        }
        _NodeSetStyles(this.playerElement, styles);
    };

    FrontendItemYouTube.prototype.initAutoplay = function () {
        this.setState('InComplete', true, true);
    

        if (!this.isStatic) {
            //change slide
            _addEventListener(this.slider.sliderElement, 'mainAnimationComplete', (function (e) {
                if (this.slide.isActiveWhen(this.slider.slides[e.detail.currentSlideIndex])) {
                    this.setState('play', true);
                    this.setState('slide', true, true);
                } else {
                    this.setState('slide', false, true);
                }
            }).bind(this));

            if (this.slide.isActiveWhen()) {
                this.setState('play', true);
                this.setState('slide', true, true);
            }
        } else {
            this.setState('play', true);
            this.setState('slide', true, true);
        }
    };

    FrontendItemYouTube.prototype.setState = function (name, value, doAction) {
        doAction = doAction || false;

        this.state[name] = value;

        if (doAction) {
            if (this.state.slideVisible && this.state.visible && this.state.play && this.state.slide && this.state.InComplete && this.state.scroll) {
                this.play();
            } else {
                this.pause();
            }
        }
    };

    FrontendItemYouTube.prototype.play = function () {
        if (this.isStopped()) {
            this.player.playVideo();
            if (this.player.getPlayerState() === YT.PlayerState.PLAYING) {
                if (this.coverFadedOut === undefined) {
                    setTimeout(this.fadeOutCover.bind(this), 200);
                }
                _dispatchCustomEventNoBubble(this.slider.sliderElement, 'mediaStarted', {id: this.playerId});
            }


            if (parseInt(this.parameters.autoplay) === 1 && !this.hasAutoplayFallback) {
                if (this.player.getPlayerState() === YT.PlayerState.CUED) {
                    this.hasAutoplayFallback = true;
                    // Chrome: https://developers.google.com/web/updates/2017/09/autoplay-policy-changes
                    // Firefox: https://hacks.mozilla.org/2019/02/firefox-66-to-block-automatically-playing-audible-video-and-audio/
                    var autoplayFallbackcallback = (function () {
                        _removeEventListeners(this.listeners.autoplay);
                        this.play();
                    }).bind(this);

                    this.listeners.autoplay = [
                        _addEventListenerWithRemover(body, 'click', autoplayFallbackcallback),
                        _addEventListenerWithRemover(body, 'n2click', autoplayFallbackcallback),

                    ];
                }
            }
        }
    };

    FrontendItemYouTube.prototype.pause = function () {
        if (!this.isStopped()) {
            this.player.pauseVideo();
        }
    };

    FrontendItemYouTube.prototype.stop = function () {
        this.player.stopVideo();
    };

    FrontendItemYouTube.prototype.isStopped = function () {
        var state = this.player.getPlayerState();
        switch (state) {
            case -1:
            case 2:
            case 5:
                return true;
            default:
                return false;
        }
    };

    FrontendItemYouTube.prototype.setVolume = function (volume) {
        this.player.setVolume(volume * 100);
    };

    return FrontendItemYouTube;
});_N2.d('smartslider-frontend');})(window);
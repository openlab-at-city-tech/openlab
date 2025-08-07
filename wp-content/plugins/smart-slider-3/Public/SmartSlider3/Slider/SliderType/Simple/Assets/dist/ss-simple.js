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
});_N2.d('SmartSliderMainAnimationSimple', ['SmartSliderMainAnimationAbstract'], function () {

    /**
     * @memberOf _N2
     * @param slider
     * @param parameters
     * @constructor
     */
    function SmartSliderMainAnimationSimple(slider, parameters) {

        this.postBackgroundAnimation = false;
        this._currentBackgroundAnimation = false;
        this.reverseSlideIndex = null;

        parameters = _Assign({
            delay: 0,
            type: 'horizontal',
            shiftedBackgroundAnimation: 'auto'
        }, parameters);
        parameters.delay /= 1000;

        if (parameters.duration < 300) {
            parameters.type = 'no';
        }

        _N2.SmartSliderMainAnimationAbstract.prototype.constructor.call(this, slider, parameters);

        switch (this.parameters.type) {
            case 'no':
                this.animation = this._mainAnimationNo;
                this.isNoAnimation = true;
                break;
            case 'fade':
                this.animation = this._mainAnimationFade;
                break;
            case 'crossfade':
                this.animation = this._mainAnimationCrossFade;
                break;
            case 'vertical':
                if (slider.backgrounds.hasFixed) {
                    this.animation = this._mainAnimationFade;
                } else {
                    this.animation = this._mainAnimationVertical;
                }
                break;
            case 'vertical-reversed':
                if (slider.backgrounds.hasFixed) {
                    this.animation = this._mainAnimationFade;
                } else {
                    this.animation = this._mainAnimationVerticalReversed;
                }
                break;
            case 'horizontal-reversed':
                this.animation = this._mainAnimationHorizontalReversed;
                break;
            default:
                this.animation = this._mainAnimationHorizontal;
                break;
        }
    }

    SmartSliderMainAnimationSimple.prototype = Object.create(_N2.SmartSliderMainAnimationAbstract.prototype);
    SmartSliderMainAnimationSimple.prototype.constructor = SmartSliderMainAnimationSimple;

    SmartSliderMainAnimationSimple.prototype.changeTo = function (currentSlide, nextSlide, reversed, isSystem) {
        if (this.postBackgroundAnimation) {
            this.postBackgroundAnimation.prepareToSwitchSlide(currentSlide, nextSlide);
        }

        _N2.SmartSliderMainAnimationAbstract.prototype.changeTo.apply(this, arguments);
    };

    /**
     * Used to hide non active slides
     * @param slide
     */
    SmartSliderMainAnimationSimple.prototype.setActiveSlide = function (slide) {
        for (var i = 0; i < this.slider.slides.length; i++) {
            if (this.slider.slides[i] !== slide) {
                this.hideSlide(this.slider.slides[i]);
            }
        }
    };

    /**
     * Hides the slide, but not the usual way. Simply positions them outside of the slider area.
     * If we use the visibility or display property to hide we would end up corrupted YouTube api.
     * If opacity 0 might also work, but that might need additional resource from the browser
     * @param slide
     * @public
     */
    SmartSliderMainAnimationSimple.prototype.hideSlide = function (slide) {

        _N2.MW.___setValues(slide.SMWs, {x: -100000 * n2const.rtl.modifier});
    };

    SmartSliderMainAnimationSimple.prototype.showSlide = function (slide) {

        _N2.MW.___setValues(slide.SMWs, {x: 0, y: 0});
    };

    SmartSliderMainAnimationSimple.prototype.cleanSlideIndex = function (slideIndex) {
        this.hideSlide(this.slider.slides[slideIndex]);
    };


    SmartSliderMainAnimationSimple.prototype.revertTo = function (slideIndex, originalNextSlideIndex) {

        _N2.MW.___setValues(this.slider.slides[originalNextSlideIndex].SMWs, {zIndex: ''});

        this.hideSlide(this.slider.slides[originalNextSlideIndex]);

        _N2.SmartSliderMainAnimationAbstract.prototype.revertTo.apply(this, arguments);
    };

    SmartSliderMainAnimationSimple.prototype._initAnimation = function (currentSlide, nextSlide, reversed) {

        this.animation(currentSlide, nextSlide, reversed);
    };

    SmartSliderMainAnimationSimple.prototype.onBackwardChangeToComplete = function (previousSlide, currentSlide, isSystem) {
        this.reverseSlideIndex = null;
        this.onChangeToComplete(previousSlide, currentSlide, isSystem);
    };

    SmartSliderMainAnimationSimple.prototype.onChangeToComplete = function (previousSlide, currentSlide, isSystem) {
        if (this.reverseSlideIndex !== null) {
            _dispatchEventSimpleNoBubble(this.slider.slides[this.reverseSlideIndex].element, 'mainAnimationStartInCancel');
            this.reverseSlideIndex = null;
        }
        this.hideSlide(previousSlide);

        _N2.SmartSliderMainAnimationAbstract.prototype.onChangeToComplete.apply(this, arguments);
    };

    SmartSliderMainAnimationSimple.prototype.onReverseChangeToComplete = function (previousSlide, currentSlide, isSystem) {

        this.hideSlide(previousSlide);

        _N2.SmartSliderMainAnimationAbstract.prototype.onReverseChangeToComplete.apply(this, arguments);
    };

    SmartSliderMainAnimationSimple.prototype._mainAnimationNo = function (currentSlide, nextSlide) {

        this.parameters.delay = 0;
        this.parameters.duration = 0.1;

        this._mainAnimationFade(currentSlide, nextSlide);
    };

    SmartSliderMainAnimationSimple.prototype._mainAnimationFade = function (currentSlide, nextSlide) {

        _N2.MW.___setValues(currentSlide.SMWs, {zIndex: 23});

        this.showSlide(nextSlide);

        currentSlide.unsetActive();
        nextSlide.setActive();

        var adjustedTiming = this.adjustMainAnimation();

        if (this.parameters.shiftedBackgroundAnimation !== 0) {
            var needShift = false,
                resetShift = false;
            if (this.parameters.shiftedBackgroundAnimation === 'auto') {
                if (currentSlide.hasLayers()) {
                    needShift = true;
                } else {
                    resetShift = true;
                }
            } else {
                needShift = true;
            }

            if (this._currentBackgroundAnimation && needShift) {
                var shift = adjustedTiming.outDuration - adjustedTiming.extraDelay;
                if (shift > 0) {
                    this.timeline.shiftChildren(shift);
                }
                if (this._currentBackgroundAnimation.shiftedPreSetup) {
                    this._currentBackgroundAnimation._preSetup();
                }
            } else if (resetShift) {
                if (adjustedTiming.extraDelay > 0) {
                    this.timeline.shiftChildren(adjustedTiming.extraDelay);
                }
                if (this._currentBackgroundAnimation.shiftedPreSetup) {
                    this._currentBackgroundAnimation._preSetup();
                }
            }
        } else if (this._currentBackgroundAnimation !== false) {
            adjustedTiming.outDelay += 0.1;
        }

        var currentSMWs = [currentSlide.SMWs[0]];
        if (!this._currentBackgroundAnimation && currentSlide.SMWs[1]) {
            currentSMWs.push(currentSlide.SMWs[1]);
        }

        this.timeline.fromTo(currentSMWs, adjustedTiming.outDuration, {
            opacity: 1
        }, {
            opacity: 0,
            ease: this.getEase()
        }, adjustedTiming.outDelay);

        var nextSlideSMW = _N2.MW.___getSMW(nextSlide.element);
        this.timeline.fromTo(nextSlideSMW, adjustedTiming.inDuration, {
            opacity: 0
        }, {
            opacity: 1,
            ease: this.getEase()
        }, adjustedTiming.inDelay);

        if (!this._currentBackgroundAnimation && nextSlide.background) {
            _N2.MW.___getSMW(nextSlide.background.element).opacity = 1;
        }

        _addEventListenerOnce(this.sliderElement, 'mainAnimationComplete', (function (e) {

            var currentSlide = this.slider.slides[e.detail.previousSlideIndex];

            _N2.MW.___setValues(currentSlide.SMWs, {zIndex: '', opacity: 1});
        }).bind(this));

        this.slider.updateInsideSlides([currentSlide, nextSlide]);
    };

    SmartSliderMainAnimationSimple.prototype._mainAnimationCrossFade = function (currentSlide, nextSlide) {

        _N2.MW.___setValues(currentSlide.SMWs, {zIndex: 23});

        this.showSlide(nextSlide);

        currentSlide.unsetActive();
        nextSlide.setActive();

        var adjustedTiming = this.adjustMainAnimation();

        if (this.parameters.shiftedBackgroundAnimation != 0) {
            var needShift = false,
                resetShift = false;
            if (this.parameters.shiftedBackgroundAnimation == 'auto') {
                if (currentSlide.hasLayers()) {
                    needShift = true;
                } else {
                    resetShift = true;
                }
            } else {
                needShift = true;
            }

            if (this._currentBackgroundAnimation && needShift) {
                var shift = adjustedTiming.outDuration - adjustedTiming.extraDelay;
                if (shift > 0) {
                    this.timeline.shiftChildren(shift);
                }
                if (this._currentBackgroundAnimation.shiftedPreSetup) {
                    this._currentBackgroundAnimation._preSetup();
                }
            } else if (resetShift) {
                if (adjustedTiming.extraDelay > 0) {
                    this.timeline.shiftChildren(adjustedTiming.extraDelay);
                }
                if (this._currentBackgroundAnimation.shiftedPreSetup) {
                    this._currentBackgroundAnimation._preSetup();
                }
            }
        } else if (this._currentBackgroundAnimation !== false) {
            adjustedTiming.outDelay += 0.1;
        }

        var currentSMWs = [currentSlide.SMWs[0]];
        if (!this._currentBackgroundAnimation && currentSlide.SMWs[1]) {
            currentSMWs.push(currentSlide.SMWs[1]);
        }

        this.timeline.fromTo(currentSMWs, adjustedTiming.outDuration, {
            opacity: 1
        }, {
            opacity: 0,
            ease: this.getEase()
        }, adjustedTiming.outDelay);

        var nextSMWs = [nextSlide.SMWs[0]];
        if (!this._currentBackgroundAnimation && nextSlide.SMWs[1]) {
            nextSMWs.push(nextSlide.SMWs[1]);
        }
        this.timeline.fromTo(nextSMWs, adjustedTiming.inDuration, {
            opacity: 0
        }, {
            opacity: 1,
            ease: this.getEase()
        }, adjustedTiming.inDelay);

        _addEventListenerOnce(this.sliderElement, 'mainAnimationComplete', (function (e) {

            var currentSlide = this.slider.slides[e.detail.previousSlideIndex],
                nextSlide = this.slider.slides[e.detail.currentSlideIndex];

            _N2.MW.___setValues(currentSlide.SMWs, {zIndex: '', opacity: 1});
            _N2.MW.___setValues(nextSlide.SMWs, {opacity: 1});
        }).bind(this));

        this.slider.updateInsideSlides([currentSlide, nextSlide]);
    };

    SmartSliderMainAnimationSimple.prototype._mainAnimationHorizontal = function (currentSlide, nextSlide, reversed) {
        this.__mainAnimationDirection(currentSlide, nextSlide, 'horizontal', reversed);
    };

    SmartSliderMainAnimationSimple.prototype._mainAnimationVertical = function (currentSlide, nextSlide, reversed) {
        this.showSlide(nextSlide);
        this.__mainAnimationDirection(currentSlide, nextSlide, 'vertical', reversed);
    };

    SmartSliderMainAnimationSimple.prototype._mainAnimationHorizontalReversed = function (currentSlide, nextSlide, reversed) {
        this.__mainAnimationDirection(currentSlide, nextSlide, 'horizontal', !reversed);
    };

    SmartSliderMainAnimationSimple.prototype._mainAnimationVerticalReversed = function (currentSlide, nextSlide, reversed) {
        this.showSlide(nextSlide);
        this.__mainAnimationDirection(currentSlide, nextSlide, 'vertical', !reversed);
    };

    SmartSliderMainAnimationSimple.prototype.__mainAnimationDirection = function (currentSlide, nextSlide, direction, reversed) {

        var currentSMWs = currentSlide.SMWs,
            nextSMWs = nextSlide.SMWs,
            insideSlides = [currentSlide, nextSlide],
            property = '',
            propertyValue = 0,
            originalPropertyValue = 0;

        if (direction === 'horizontal') {
            property = 'x';
            originalPropertyValue = propertyValue = Math.floor(this.slider.responsive.resizeContext.slideOuterWidth);

            if (n2const.rtl.isRtl) {
                reversed = !reversed;
            }
        } else if (direction === 'vertical') {
            property = 'y';
            originalPropertyValue = propertyValue = Math.floor(this.slider.responsive.resizeContext.slideOuterHeight);
        }

        if (reversed) {
            propertyValue *= -1;
        }

        var nextSlideFrom = {},
            nextSlideTo = {
                snap: 'x,y',
                ease: this.getEase()
            },
            currentSlideTo = {
                snap: 'x,y',
                ease: this.getEase()
            };

        nextSlideFrom[property] = propertyValue;

        currentSlideTo[property] = -propertyValue;


        _N2.MW.___setValues(currentSMWs, {zIndex: 23});

        var _nextProp = {zIndex: 23}
        _nextProp[property] = propertyValue;
        _N2.MW.___setValues(nextSMWs, _nextProp);

        currentSlide.unsetActive();
        nextSlide.setActive();

        var adjustedTiming = this.adjustMainAnimation();
        nextSlideTo[property] = 0;

        this.timeline.fromTo(nextSMWs, adjustedTiming.inDuration, nextSlideFrom, nextSlideTo, adjustedTiming.inDelay);

        if (this.parameters.shiftedBackgroundAnimation != 0) {
            var needShift = false,
                resetShift = false;
            if (this.parameters.shiftedBackgroundAnimation === 'auto') {
                if (currentSlide.hasLayers()) {
                    needShift = true;
                } else {
                    resetShift = true;
                }
            } else {
                needShift = true;
            }

            if (this._currentBackgroundAnimation && needShift) {
                var shift = adjustedTiming.outDuration - adjustedTiming.extraDelay;
                if (shift > 0) {
                    this.timeline.shiftChildren(shift);
                }
                if (this._currentBackgroundAnimation.shiftedPreSetup) {
                    this._currentBackgroundAnimation._preSetup();
                }
            } else if (resetShift) {
                if (adjustedTiming.extraDelay > 0) {
                    this.timeline.shiftChildren(adjustedTiming.extraDelay);
                }
                if (this._currentBackgroundAnimation.shiftedPreSetup) {
                    this._currentBackgroundAnimation._preSetup();
                }
            }
        } else if (this._currentBackgroundAnimation !== false) {
            adjustedTiming.outDelay += 0.1;
        }

        this.timeline.to(currentSMWs, adjustedTiming.outDuration, currentSlideTo, adjustedTiming.outDelay);

        if (this.isTouch && this.isReverseAllowed) {

            var reverseSlide;
            if (reversed) {
                if (!this.slider.blockCarousel || !this.slider.isChangeCarousel('next')) {
                    reverseSlide = currentSlide.getNext();
                }
            } else {
                if (!this.slider.blockCarousel || !this.slider.isChangeCarousel('previous')) {
                    reverseSlide = currentSlide.getPrevious();
                }
            }

            if (reverseSlide && reverseSlide !== nextSlide) {

                this.reverseSlideIndex = reverseSlide.index;
                this.enableReverseMode();

                insideSlides.push(reverseSlide);

                if (direction === 'vertical') {
                    this.showSlide(reverseSlide);
                }

                var reverseSMWs = reverseSlide.SMWs;

                var _reverseProp = {}
                _reverseProp[property] = propertyValue;
                _N2.MW.___setValues(reverseSMWs, _reverseProp);

                var reversedInFrom = {},
                    reversedInProperties = {
                        snap: 'x,y',
                        ease: this.getEase()
                    },
                    reversedOutFrom = {},
                    reversedOutProperties = {
                        snap: 'x,y',
                        ease: this.getEase()
                    };

                reversedInProperties[property] = 0;
                reversedInFrom[property] = -propertyValue;
                reversedOutProperties[property] = propertyValue;
                reversedOutFrom[property] = 0;

                reverseSlide.__$dispatchEvent('mainAnimationStartIn', {
                    mainAnimation: this,
                    previousSlideIndex: currentSlide.index,
                    currentSlideIndex: reverseSlide.index,
                    isSystem: false
                });

                this.reverseTimeline.paused(true);
                this.reverseTimeline.eventCallback('onComplete', this.onBackwardChangeToComplete.bind(this), [currentSlide, reverseSlide, false]);

                this.reverseTimeline.fromTo(reverseSMWs, adjustedTiming.inDuration, reversedInFrom, reversedInProperties, adjustedTiming.inDelay);
                this.reverseTimeline.fromTo(currentSMWs, adjustedTiming.inDuration, reversedOutFrom, reversedOutProperties, adjustedTiming.inDelay);
            } else {
                this.reverseSlideIndex = null;
            }
        }


        _addEventListenerOnce(this.sliderElement, 'mainAnimationComplete', (function (e) {

            var currentSlide = this.slider.slides[e.detail.previousSlideIndex],
                nextSlide = this.slider.slides[e.detail.currentSlideIndex];

            _N2.MW.___setValues(currentSlide.SMWs, {zIndex: ''});
            _N2.MW.___setValues(nextSlide.SMWs, {zIndex: ''});
        }).bind(this));

        this.slider.updateInsideSlides(insideSlides);
    };

    SmartSliderMainAnimationSimple.prototype.getExtraDelay = function () {
        return 0;
    };

    SmartSliderMainAnimationSimple.prototype.adjustMainAnimation = function () {
        var duration = this.parameters.duration,
            delay = this.parameters.delay,
            backgroundAnimationDuration = this.timeline.totalDuration(),
            extraDelay = this.getExtraDelay();

        if (backgroundAnimationDuration > 0) {
            /**
             * The WordPress free version without GSAP, the .set() implementation adds a 0.05 duration .to tween.
             * @see SSDEV-3127
             */
            backgroundAnimationDuration -= 0.05;
        

            var totalMainAnimationDuration = duration + delay;
            if (totalMainAnimationDuration > backgroundAnimationDuration) {
                duration = duration * backgroundAnimationDuration / totalMainAnimationDuration;
                delay = delay * backgroundAnimationDuration / totalMainAnimationDuration;
                if (delay < extraDelay) {
                    duration -= (extraDelay - delay);
                    delay = extraDelay;
                }
            } else {
                return {
                    inDuration: duration,
                    outDuration: duration,
                    inDelay: backgroundAnimationDuration - duration,
                    outDelay: extraDelay,
                    extraDelay: extraDelay
                }
            }
        } else {
            delay += extraDelay;
        }
        return {
            inDuration: duration,
            outDuration: duration,
            inDelay: delay,
            outDelay: delay,
            extraDelay: extraDelay
        }
    };

    SmartSliderMainAnimationSimple.prototype.hasBackgroundAnimation = function () {
        return false;
    };

    return SmartSliderMainAnimationSimple;
});_N2.d('SmartSliderResponsiveSimple', ['SmartSliderResponsive'], function () {

    /**
     * @memberOf _N2
     *
     * @augments _N2.SmartSliderResponsive
     * @constructor
     */
    function SmartSliderResponsiveSimple() {
        this.round = 1;
        _N2.SmartSliderResponsive.prototype.constructor.apply(this, arguments);

        this.___isFullPageConstrainRatio = this.sliderElement.classList.contains('n2-ss-full-page--constrain-ratio');

        /**
         * @type {_N2.SmartSliderMainAnimationSimple}
         */
        this.mainAnimation = this.slider.mainAnimation;
    }

    SmartSliderResponsiveSimple.prototype = Object.create(_N2.SmartSliderResponsive.prototype);
    SmartSliderResponsiveSimple.prototype.constructor = SmartSliderResponsiveSimple;

    SmartSliderResponsiveSimple.prototype.init = function () {

        _N2.SmartSliderResponsive.prototype.init.call(this);

        this._cacheEl = {
            'slider': this.sliderElement.querySelector('.n2-ss-slider-wrapper-inside') || this.sliderElement,
            'n2-ss-slider-2': this.sliderElement.querySelector('.n2-ss-slider-2'),
            'n2-ss-slider-3': this.sliderElement.querySelector('.n2-ss-slider-3')
        };

        /**
         * Safari 13 fix
         * @see https://bugs.webkit.org/show_bug.cgi?id=207630
         */
        this._cacheEl['n2-ss-slider-3'].addEventListener('scroll', function (e) {
            e.currentTarget.scrollTop = 0;
            e.currentTarget.scrollLeft = 0;
        }, {
            capture: true
        });
    };

    SmartSliderResponsiveSimple.prototype.calculateResponsiveValues = function () {

        var slideElement = this.slider.visibleRealSlides[0].element,
            slideSelfRect = slideElement.getBoundingClientRect();

        this.resizeContext.slideSelfWidth = slideSelfRect.width;
        this.resizeContext.slideSelfHeight = slideSelfRect.height;

        var slideRect = slideElement.querySelector('.n2-ss-layers-container').getBoundingClientRect();

        this.resizeContext.slideWidth = slideRect.width;
        this.resizeContext.slideHeight = slideRect.height;

        /**
         * Used by the touch drag
         */
        var slider = this._cacheEl['slider'].getBoundingClientRect();
        this.resizeContext.sliderWidth = slider.width;
        this.resizeContext.sliderHeight = slider.height;

        /**
         * Used by the main animation
         */
        var slideOuterRect = this._cacheEl['n2-ss-slider-3'].getBoundingClientRect();
        this.resizeContext.slideOuterWidth = slideOuterRect.width;
        this.resizeContext.slideOuterHeight = slideOuterRect.height;

        _N2.SmartSliderResponsive.prototype.calculateResponsiveValues.call(this);

        if (this.___isFullPageConstrainRatio) {
            var horizontalClipPath = ((this.resizeContext.sliderWidth - this.resizeContext.slideWidth) / -2) + 'px',
                verticalClipPath = ((this.resizeContext.sliderHeight - this.resizeContext.slideHeight) / -2) + 'px';
            _N2.MW.___getSMW(this._cacheEl['slider'])['--ss-clip-path'] = 'inset(' + verticalClipPath + ' ' + horizontalClipPath + ' ' + verticalClipPath + ' ' + horizontalClipPath + ')';
        }
    }

    SmartSliderResponsiveSimple.prototype.onStarterSlide = function (realStarterSlide) {

        this.slider.slides.forEach(function (slide) {

            slide.SMWs = [
                _N2.MW.___getSMW(slide.element, {
                    x: '-10000px'
                })
            ];

            if (slide.background) {
                slide.SMWs.push(_N2.MW.___getSMW(slide.background.element, {
                    x: '-10000px'
                }));
            }
        });

        _N2.SmartSliderResponsive.prototype.onStarterSlide.apply(this, arguments);

        /**
         * ReRender classes when the currentSlide available
         */
        this.mainAnimation.setActiveSlide(this.slider.currentSlide);
    };

    return SmartSliderResponsiveSimple;
});_N2.d('SmartSliderSimple', ['SmartSliderAbstract'], function () {

    /**
     * @memberOf _N2
     *
     * @param elementID
     * @param parameters
     * @augments _N2.SmartSliderAbstract
     * @constructor
     */
    function SmartSliderSimple(elementID, parameters) {

        this.type = 'simple';

        _N2.SmartSliderAbstract.prototype.constructor.call(this, elementID, _Assign({
            bgAnimations: 0,
            carousel: 1
        }, parameters));
    }

    SmartSliderSimple.prototype = Object.create(_N2.SmartSliderAbstract.prototype);
    SmartSliderSimple.prototype.constructor = SmartSliderSimple;

    SmartSliderSimple.prototype.initResponsiveMode = function () {

        this.responsive = new _N2.SmartSliderResponsiveSimple(this, this.parameters.responsive);
        this.responsive.start();

        _N2.SmartSliderAbstract.prototype.initResponsiveMode.call(this);
    };

    SmartSliderSimple.prototype.initMainAnimation = function () {

        if (!this.disabled.backgroundAnimations && this.parameters.bgAnimations) {
            this.mainAnimation = new _N2.SmartSliderFrontendBackgroundAnimation(this, this.parameters.mainanimation, this.parameters.bgAnimations);
        } else {
            this.mainAnimation = new _N2.SmartSliderMainAnimationSimple(this, this.parameters.mainanimation);
        }
    };

    SmartSliderSimple.prototype.afterRawSlidesReady = function () {
        if (this.parameters.postBackgroundAnimations && this.parameters.postBackgroundAnimations.slides) {
            for (var i = 0; i < this.slides.length; i++) {
                this.slides[i].postBackgroundAnimation = this.parameters.postBackgroundAnimations.slides[i];
            }
            delete this.parameters.postBackgroundAnimations.slides;
        }

        if (this.parameters.bgAnimations && this.parameters.bgAnimations.slides) {
            for (var j = 0; j < this.slides.length; j++) {
                this.slides[j].backgroundAnimation = this.parameters.bgAnimations.slides[j];
            }
            delete this.parameters.bgAnimations.slides;
        }
    };

    SmartSliderSimple.prototype.forceSetActiveSlide = function (slide) {

        slide.setActive();

        this.mainAnimation.showSlide(slide);
    };

    SmartSliderSimple.prototype.forceUnsetActiveSlide = function (slide) {
        slide.unsetActive();

        this.mainAnimation.hideSlide(slide);
    };

    SmartSliderSimple.prototype.getAnimationAxis = function () {
        switch (this.mainAnimation.parameters.type) {
            case 'vertical':
            case 'vertical-reversed':
                return 'vertical';
        }

        return 'horizontal';
    };

    return SmartSliderSimple;

});_N2.d('ss-simple',['SmartSliderSimple','SmartSliderResponsiveSimple','SmartSliderMainAnimationSimple']);})(window);
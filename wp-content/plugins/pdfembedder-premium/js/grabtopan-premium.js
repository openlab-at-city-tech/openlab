
var pdfembGrabToPan = (function GrabToPanClosure() {

    /**
     * Construct a GrabToPan instance for a given HTML element.
     * @param options.element {Element}
     * @param options.ignoreTarget {function} optional. See `ignoreTarget(node)`
     * @param options.onActiveChanged {function(boolean)} optional. Called
     *  when grab-to-pan is (de)activated. The first argument is a boolean that
     *  shows whether grab-to-pan is activated.
     */
    function GrabToPan(options) {
        this.element = options.element;
        this.document = options.element.ownerDocument;
        if (typeof options.ignoreTarget === 'function') {
            this.ignoreTarget = options.ignoreTarget;
        }
        this.onActiveChanged = options.onActiveChanged;

        // Bind the contexts to ensure that `this` always points to
        // the GrabToPan instance.
        this.activate = this.activate.bind(this);
        this.deactivate = this.deactivate.bind(this);
        this.toggle = this.toggle.bind(this);
        this._onmousedown = this._onmousedown.bind(this);
        this._onmousemove = this._onmousemove.bind(this);
        this._ontouchstart = this._ontouchstart.bind(this);
        this._ontouchmove = this._ontouchmove.bind(this);
        this._ontouchend = this._ontouchend.bind(this);
        this._onmousewheel = this._onmousewheel.bind(this);
        this._endPan = this._endPan.bind(this);

        this._ondocumenttouchmove = this._ondocumenttouchmove.bind(this);

        this._onkeyboardpan = this._onkeyboardpan.bind(this);
        this.element.addEventListener("pdfembKeyboardPan", this._onkeyboardpan, false);

        this.preventDocTouchScroll = false;

        // This overlay will be inserted in the document when the mouse moves during
        // a grab operation, to ensure that the cursor has the desired appearance.
        var overlay = this.overlay = document.createElement('div');
        overlay.className = 'grab-to-pan-grabbing';
    }
    GrabToPan.prototype = {
        /**
         * Class name of element which can be grabbed
         */
        CSS_CLASS_GRAB: 'grab-to-pan-grab',

        /**
         * Bind a mousedown event to the element to enable grab-detection.
         */
        activate: function GrabToPan_activate() {
            if (!this.active) {
                this.active = true;
                this.element.addEventListener('mousedown', this._onmousedown, true);

                this.element.addEventListener('mousewheel', this._onmousewheel);
                this.element.addEventListener('wheel', this._onmousewheel);
                this.element.addEventListener('DOMMouseScroll', this._onmousewheel);


                this.element.addEventListener('touchstart', this._ontouchstart);

                // jQuery(document).on('touchmove', this._ondocumenttouchmove);
                document.addEventListener('touchmove', this._ondocumenttouchmove);


                this.element.classList.add(this.CSS_CLASS_GRAB);
                if (this.onActiveChanged) {
                    this.onActiveChanged(true);
                }
            }
        },

        /**
         * Removes all events. Any pending pan session is immediately stopped.
         */
        deactivate: function GrabToPan_deactivate() {
            if (this.active) {
                this.active = false;
                this.element.removeEventListener('mousedown', this._onmousedown, true);
                this._endPan();

                // jQuery(document).off('touchmove', this._ondocumenttouchmove);
                document.removeEventListener('touchmove', this._ondocumenttouchmove, false);


                this.element.classList.remove(this.CSS_CLASS_GRAB);
                if (this.onActiveChanged) {
                    this.onActiveChanged(false);
                }
            }
        },

        toggle: function GrabToPan_toggle() {
            if (this.active) {
                this.deactivate();
            } else {
                this.activate();
            }
        },

        /**
         * Whether to not pan if the target element is clicked.
         * Override this method to change the default behaviour.
         *
         * @param node {Element} The target of the event
         * @return {boolean} Whether to not react to the click event.
         */
        ignoreTarget: function GrabToPan_ignoreTarget(node) {
            // Use matchesSelector to check whether the clicked element
            // is (a child of) an input element / link
            return node[matchesSelector](
                'a[href], a[href] *, input, textarea, button, button *, select, option, .pdfembTextLayer > div'
            );
        },

        /**
         * @private
         */
        _onmousedown: function GrabToPan__onmousedown(event) {
            if (event.button !== 0 || this.ignoreTarget(event.target)) {
                return;
            }
            if (event.originalTarget) {
                try {
                    /* jshint expr:true */
                    var ottn = event.originalTarget.tagName;
                } catch (e) {
                    // Mozilla-specific: element is a scrollbar (XUL element)
                    return;
                }
            }

            this.scrollLeftStart = this.element.scrollLeft;
            this.scrollTopStart = this.element.scrollTop;
            this.clientXStart = event.clientX;
            this.clientYStart = event.clientY;
            this.document.addEventListener('mousemove', this._onmousemove, true);
            this.document.addEventListener('mouseup', this._endPan, true);
            // When a scroll event occurs before a mousemove, assume that the user
            // dragged a scrollbar (necessary for Opera Presto, Safari and IE)
            // (not needed for Chrome/Firefox)
            this.element.addEventListener('scroll', this._endPan, true);
            event.preventDefault();
            event.stopPropagation();
            this.document.documentElement.classList.add(this.CSS_CLASS_GRABBING);

            var focusedElement = document.activeElement;
            if (focusedElement && !focusedElement.contains(event.target)) {
                focusedElement.blur();
            }
        },

        /**
         * @private
         */
        _onmousemove: function GrabToPan__onmousemove(event) {
            this.element.removeEventListener('scroll', this._endPan, true);
            if (isLeftMouseReleased(event)) {
                this._endPan();
                return;
            }
            var xDiff = event.clientX - this.clientXStart;
            var yDiff = event.clientY - this.clientYStart;
            this.element.scrollTop = this.scrollTopStart - yDiff;
            this.element.scrollLeft = this.scrollLeftStart - xDiff;
            if (!this.overlay.parentNode) {
                document.body.appendChild(this.overlay);
            }
        },

        /**
         * @private
         */
        _ontouchstart: function GrabToPan__ontouchstart(event) {
            this.scrollLeftStart = this.element.scrollLeft;
            this.scrollTopStart = this.element.scrollTop;
            this.clientXStart = event.touches[0].clientX;
            this.clientYStart = event.touches[0].clientY;
            this.distStart = this._calcTouchDistance(event);

            this.scaledForMagnification = false;


            this.document.addEventListener('touchmove', this._ontouchmove);
            this.document.addEventListener('touchend', this._ontouchend);



            if (event.touches.length != 1) {
                // Prevent default for zooms
                event.preventDefault();
                event.stopPropagation();
            }

            this.document.documentElement.classList.add(this.CSS_CLASS_GRABBING);

            var focusedElement = document.activeElement;
            if (focusedElement && !focusedElement.contains(event.target)) {
                focusedElement.blur();
            }

            // Tell viewer that touch has occurred so it can display the 'hover' toolbar if needed

            this.touchtapmaybe = event.touches.length == 1;
        },

        _calcTouchDistance: function GrabToPan_calcTouchDistance(event) {
            var dist = NaN;
            if (event.touches && event.touches.length>= 2) {
                dist = Math.sqrt(Math.pow(event.touches[0].screenX - event.touches[1].screenX, 2)
                    + Math.pow(event.touches[0].screenY - event.touches[1].screenY, 2));
            }
            return dist;
        },

        _calcTouchCenter: function GrabToPan_calcTouchCenter(event) {
            var x =0, y = 0;
            if (event.touches && event.touches.length>= 2) {
                x = (event.touches[0].clientX + event.touches[1].clientX) / 2;
                y = (event.touches[0].clientY + event.touches[1].clientY) / 2;
            }
            return Array(x,y);
        },

        /**
         * @private
         */
        _ontouchmove: function GrabToPan__ontouchmove(event) {
            var preventDefault = true;
            var xDiff = event.touches[0].clientX - this.clientXStart;
            var yDiff = event.touches[0].clientY - this.clientYStart;

            if (event.touches.length == 1) {
                this.element.scrollTop = this.scrollTopStart - yDiff;
                this.element.scrollLeft = this.scrollLeftStart - xDiff;

                if ((this.element.scrollTop == this.scrollTopStart && yDiff != 0)
                    /* || (this.element.scrollLeft == this.scrollLeftStart && xDiff != 0) */) {
                    preventDefault = false;
                }
            }
            else {
                this.touchtapmaybe = false;
            }

            if (!this.overlay.parentNode) {
                document.body.appendChild(this.overlay);
            }

            if (xDiff != 0 || yDiff != 0) {
                this.touchtapmaybe = false;
            }

            // Look at distance between fingers - for zoom
            var newdist = this._calcTouchDistance(event);

            if (preventDefault) {
                event.preventDefault();
                this.preventDocTouchScroll = true;
            }
            else {
                this.preventDocTouchScroll = false;
            }

            if (isNaN(this.distStart)) {
                this.distStart = newdist;
            }
            else if (!isNaN(newdist) && this.distStart > 0 && newdist > 0) {
                var mag = (50 + newdist) / (50 + this.distStart);

                if (mag > 1.5) {
                    mag = 1.5;
                }
                if (mag < 0.75) {
                    mag = 0.75;
                }

                if (mag != 1) {
                    this.scaledForMagnification = true;
                    var evt = document.createEvent("Events");
                    evt.initEvent('pdfembMagnify', true, true); //true for can bubble, true for cancelable
                    evt.magnification = mag;
                    evt.gtpelement = this.element;

                    var centre = this._calcTouchCenter(event);
                    evt.centreLeft = centre[0];
                    evt.centreTop = centre[1];

                    this.element.dispatchEvent(evt);
                }

                this.distStart = newdist;
            }

        },

        _ondocumenttouchmove: function GrabToPan__ondocumenttouchmove(event) {
            // Mainly for Safari on iPhone/iPad where document seems to scroll behind the embed
            if (this.preventDocTouchScroll) {
                event.preventDefault();
                event.stopPropagation();
                return false;
            }
        },

            /**
         * @private
         */
        _onmousewheel: function GrabToPan__onmousewheel(event) {
            this.element.removeEventListener('scroll', this._endPan, true);

            var MOUSE_WHEEL_DELTA_FACTOR = 0.5;

            if (event.deltaMode) {
                // if 0, means measured in pixels
                if (event.deltaMode == 1) { //Measured in lines
                    MOUSE_WHEEL_DELTA_FACTOR = 10;
                }
                if (event.deltaMode == 2) { // Measured in pages
                    MOUSE_WHEEL_DELTA_FACTOR = 1000;
                }
            }

            var ticks = event.deltaY ? -event.deltaY  // 'wheel'
                : ( event.wheelDelta ? event.wheelDelta  // 'mousewheel'
                        : -event.detail // 'DOMMouseScroll'
                );

            this.scrollLeftStart = this.element.scrollLeft;
            this.scrollTopStart = this.element.scrollTop;
            var yDiff = ticks * MOUSE_WHEEL_DELTA_FACTOR;
            this.element.scrollTop = this.scrollTopStart - yDiff;

            if (!this.overlay.parentNode) {
                document.body.appendChild(this.overlay);
            }

            if (this.element.scrollTop != this.scrollTopStart || yDiff == 0) {
                event.preventDefault();
                return false;
            }

        },

        _ontouchend: function GrabToPan_ontouchEnd(event) {
             this._endPan();

             if (this.scaledForMagnification) {
                 var evt = document.createEvent("Events")
                 evt.initEvent('pdfembMagnify', true, true); //true for can bubble, true for cancelable
                 evt.magnification = -1;
                 evt.gtpelement = this.element;
                 this.element.dispatchEvent(evt);
             }

             if (this.touchtapmaybe) {
                 // It was a tap!

                 var evt2 = document.createEvent("Events")
                 evt2.initEvent('pdfembTouchTapped', true, true); //true for can bubble, true for cancelable
                 this.element.dispatchEvent(evt2);
             }
        },

        /**
         * @private
         */
        _endPan: function GrabToPan__endPan() {
            this.element.removeEventListener('scroll', this._endPan, true);
            this.document.removeEventListener('mousemove', this._onmousemove, true);
            this.document.removeEventListener('mouseup', this._endPan, true);
            this.document.removeEventListener('touchmove', this._ontouchmove, false);
            this.document.removeEventListener('touchend', this._ontouchend, false);

            this.preventDocTouchScroll = false;

            if (this.overlay.parentNode) {
                this.overlay.parentNode.removeChild(this.overlay);
            }

        },

        _onkeyboardpan: function GrabToPan_onkeyboardpan(event) {
            // Left 37, Up 38, Right 39, Down 40
            var keyCode = event.detail.keyCode;

            if (keyCode == 37 || keyCode == 39) {
                var size = this.element.offsetWidth / 7;
                if (size < 5) { size = 5; }
                var xDiff = keyCode == 37 ? size : -size;
                this.element.scrollLeft -= xDiff;
            }

            if (keyCode == 38 || keyCode == 40) {
                var size = this.element.offsetHeight / 7;
                if (size < 5) { size = 5; }
                var yDiff = keyCode == 38 ? size : -size;
                this.element.scrollTop -= yDiff;
            }

            if (!this.overlay.parentNode) {
                document.body.appendChild(this.overlay);
            }
        }
    };

    // Get the correct (vendor-prefixed) name of the matches method.
    var matchesSelector;
    ['webkitM', 'mozM', 'msM', 'oM', 'm'].some(function(prefix) {
        var name = prefix + 'atches';
        if (name in document.documentElement) {
            matchesSelector = name;
        }
        name += 'Selector';
        if (name in document.documentElement) {
            matchesSelector = name;
        }
        return matchesSelector; // If found, then truthy, and [].some() ends.
    });

    // Browser sniffing because it's impossible to feature-detect
    // whether event.which for onmousemove is reliable
    var isNotIEorIsIE10plus = !document.documentMode || document.documentMode > 9;
    var chrome = window.chrome;
    var isChrome15OrOpera15plus = chrome && (chrome.webstore || chrome.app);
    //                                       ^ Chrome 15+       ^ Opera 15+
    var isSafari6plus = /Apple/.test(navigator.vendor) &&
        /Version\/([6-9]\d*|[1-5]\d+)/.test(navigator.userAgent);

    /**
     * Whether the left mouse is not pressed.
     * @param event {MouseEvent}
     * @return {boolean} True if the left mouse button is not pressed.
     *                   False if unsure or if the left mouse button is pressed.
     */
    function isLeftMouseReleased(event) {
        if ('buttons' in event && isNotIEorIsIE10plus) {
            // http://www.w3.org/TR/DOM-Level-3-Events/#events-MouseEvent-buttons
            // Firefox 15+
            // Internet Explorer 10+
            return !(event.buttons | 1);
        }
        if (isChrome15OrOpera15plus || isSafari6plus) {
            // Chrome 14+
            // Opera 15+
            // Safari 6.0+
            return event.which === 0;
        }
    }

    return GrabToPan;
})();

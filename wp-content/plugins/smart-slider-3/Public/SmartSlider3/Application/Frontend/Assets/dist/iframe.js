if (!window.n2SSIframeLoader) {
    (function () {
        var frames = [];
        window.addEventListener("message", function (e) {
            for (var i = 0; i < frames.length; i++) {
                if (frames[i] && frames[i].match(e.source)) {
                    frames[i].message(e.data);
                }
            }
        });

        function S(frame, i) {
            this.i = i;
            this.frame = frame;

            /**
             * Firefox uses wrong window and document value in Divi (probably ReactJS related)
             */
            this.window = frame.ownerDocument.defaultView;
            this.document = this.window.document;

            this.___isRTL = this.document.documentElement.getAttribute('dir') === 'rtl';

            this._width = 0;

            this.verticalOffsetTop = [];
            this.verticalOffsetBottom = [];
        }

        S.prototype.match = function (w) {
            if (w === (this.frame.contentWindow || this.frame.contentDocument)) {
                this.frameContent = this.frame.contentWindow || this.frame.contentDocument;
                return true;
            }

            return false;
        };

        S.prototype.message = function (data) {
            switch (data["key"]) {
                case "setLocation":
                    if (typeof this.window.zajax_goto === 'function') {
                        /**
                         * @url https://wordpress.org/plugins/zajax-ajax-navigation/
                         */
                        this.window.zajax_goto(data.location);
                    } else {
                        this.window.location = data.location;
                    }
                    break;
                case "ready":
                    this.frameContent.postMessage({
                        key: "ackReady",
                        windowInnerHeight: window.innerHeight
                    }, "*");
                    break;
                case "option":
                    switch (data.name) {
                        case 'forceFullWidth':

                            this.document.body.style.overflowX = 'hidden';


                            this.resizeForceFullWidth();
                            this.resizeForceFullWidthCallback = this.resizeForceFullWidth.bind(this);
                            window.addEventListener('resize', this.resizeForceFullWidthCallback);

                            this.fullWidthTo = this.document.querySelector('.edit-post-visual-editor,.fl-responsive-preview .fl-builder-content');
                            this.watchWidth();

                            break;
                        case 'fullPage':
                            this.resizeFullPage();

                            this.resizeFullPageCallback = this.resizeFullPage.bind(this);
                            window.addEventListener('resize', this.resizeFullPageCallback);
                            break;
                        case 'focusOffsetTop':
                            this.verticalOffsetTop = this.document.querySelectorAll(data.value);
                            break;
                        case 'focusOffsetBottom':
                            this.verticalOffsetBottom = this.document.querySelectorAll(data.value);
                            break;
                        case 'margin':
                            this.frame.parentNode.style.margin = data.value;
                            break;
                        case 'height':
                            this.frame.style.height = data.value + 'px';
                            requestAnimationFrame((function () {
                                this.opacity = 1;
                            }).bind(this.frame.style));
                            break;
                    }
                    break;
            }
        };

        S.prototype.exists = function () {
            if (this.frame.isConnected) {
                return true;
            }

            frames[this.i] = false;

            if (this.observer) {
                this.observer.unobserve(this.fullWidthTo);
                delete this.observer;
            }

            if (this.resizeForceFullWidthCallback) {
                window.removeEventListener('resize', this.resizeForceFullWidthCallback);
            }

            if (this.resizeFullPageCallback) {
                window.removeEventListener('resize', this.resizeFullPageCallback);
            }

            return false;
        };

        S.prototype.watchWidth = function () {
            if (this.fullWidthTo) {
                var width = 0;
                this.observer = new ResizeObserver((function (entries) {
                    var entry = entries[0];
                    if (width !== entry.contentRect.width) {
                        width = entry.contentRect.width;
                        this.resizeForceFullWidth();
                    }
                }).bind(this));

                this.observer.observe(this.fullWidthTo);
            }
        };

        S.prototype.resizeForceFullWidth = function () {
            if (this.exists()) {
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

                var windowWidth = customWidth > 0 ? customWidth : this.document.body.clientWidth,
                    cs = window.getComputedStyle(this.frame.parentNode),
                    offset;

                if (this._width - windowWidth <= 0 || this._width - windowWidth > 1) {

                    if (!this.___isRTL) {
                        offset = -this.frame.parentNode.getBoundingClientRect().left - parseInt(cs.getPropertyValue('padding-left')) - parseInt(cs.getPropertyValue('border-left-width')) + adjustOffset;
                    } else {
                        offset = windowWidth - this.frame.parentNode.getBoundingClientRect().right - parseInt(cs.getPropertyValue('padding-right')) - parseInt(cs.getPropertyValue('border-right-width')) + adjustOffset
                    }

                    if (this._offset !== offset) {
                        this.frame.style.transform = 'translateX(' + offset + 'px)';
                        this._offset = offset;
                    }
                    if (this._width !== windowWidth) {
                        this.frame.style.width = windowWidth + 'px';
                        this._width = windowWidth;
                    }
                }
            }
        };

        S.prototype.resizeFullPage = function (e) {
            if (this.exists()) {
                var clientHeight = window.innerHeight,
                    offsetTop = 0,
                    offsetBottom = 0,
                    i;
                if (window.parent !== window) {
                    /**
                     * Divi auto adjusting its iframe and we end up in a forever resizing loop. Let's maximize the slider height to the screen height,
                     * when we detect that our frame is inside and outer frame.
                     * @type {number}
                     */
                    clientHeight = Math.min(clientHeight, window.screen.height);
                }

                for (i = 0; i < this.verticalOffsetTop.length; i++) {
                    offsetTop -= this.verticalOffsetTop[i].offsetHeight;
                }

                for (i = 0; i < this.verticalOffsetBottom.length; i++) {
                    offsetBottom -= this.verticalOffsetBottom[i].offsetHeight;
                }
                this.frameContent.postMessage({
                    key: "fullpage",
                    height: clientHeight,
                    offsetTop: offsetTop,
                    offsetBottom: offsetBottom
                }, "*");
            }
        };

        S.prototype.reset = function () {

            if (this.resizeForceFullWidthCallback) {
                window.removeEventListener('resize', this.resizeForceFullWidthCallback);
                delete this.resizeForceFullWidthCallback;
            }

            if (this.resizeFullPageCallback) {
                window.removeEventListener('resize', this.resizeFullPageCallback);
                delete this.resizeFullPageCallback;
            }

            if (this.observer) {
                this.observer.disconnect();
                delete this.observer;
            }

            this.frame.parentNode.style.margin = '0px';
            this.frame.style.height = 'auto';
            this.opacity = 1;
            this.frame.style.transform = 'none';
            this.frame.style.width = '100%';
        };

        window.n2SSIframeLoader = function (iframe) {
            var frameObject = new S(iframe, frames.length);
            frames.push(frameObject);

            return frameObject;
        }
    })();
}
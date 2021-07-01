/*jslint browser: true*/
/*global cmtt_data, console, document, jQuery*/

var CM_Tooltip = {};

/*
 *
 * @use jQuery
 * @use document
 * @param {type} opts
 * @returns {CM_Tooltip.gtooltip.methods}
 */
CM_Tooltip.gtooltip = function (opts) {
    "use strict";
    var tooltipWrapper, tooltipWrapperInit, tooltipTop, tooltipContainer, tooltipBottom, tooltipButtonClose, h, w, id, alpha, ie, tooltipApi, closeButtonClicked, tooltipWrapperClicked;

    tooltipWrapper = null;
    tooltipWrapperInit = null;
    id = 'tt';
    alpha = 0;
    ie = document.all ? true : false;

    tooltipApi = {
        create: function (switchElement) {
            closeButtonClicked = false;
            tooltipWrapperClicked = false;
            if (tooltipWrapperInit === null) {
                tooltipWrapper = document.getElementById('tt');
                if (tooltipWrapper === null) {
                    tooltipWrapper = document.createElement('div');
                }
                jQuery(tooltipWrapper).html(''); //reset the content if it's there

                tooltipWrapper.setAttribute('id', id);
                tooltipWrapper.setAttribute('role', 'tooltip');
                tooltipWrapper.setAttribute('aria-hidden', true);

                tooltipTop = document.createElement('div');
                tooltipTop.setAttribute('id', id + 'top');

                tooltipContainer = document.createElement('div');
                tooltipContainer.setAttribute('id', id + 'cont');
                tooltipContainer.style.padding = opts.padding;
                tooltipContainer.style.backgroundColor = opts.background;
                tooltipContainer.style.color = opts.foreground;
                tooltipContainer.style.borderWidth = opts.borderWidth;
                tooltipContainer.style.borderStyle = opts.borderStyle;
                tooltipContainer.style.borderColor = opts.borderColor;
                tooltipContainer.style.borderRadius = opts.borderRadius;
                tooltipContainer.style.fontSize = opts.fontSize;

                tooltipBottom = document.createElement('div');
                tooltipBottom.setAttribute('id', id + 'bot');

                if (opts.close_button !== false) {
                    if ( opts.close_button_mobile === true && CM_Tools.Modernizr.touch || opts.close_button_mobile === false ) {
                        tooltipButtonClose = document.createElement('span');
                        tooltipButtonClose.setAttribute('id', id + '-btn-close');
                        tooltipButtonClose.setAttribute('class', 'dashicons ' + opts.close_symbol);
                        tooltipButtonClose.setAttribute('aria-label', 'Close the tooltip');
                        tooltipTop.appendChild(tooltipButtonClose);
                    }
                    
                }
                tooltipWrapper.appendChild(tooltipTop);
                tooltipWrapper.appendChild(tooltipContainer);
                tooltipWrapper.appendChild(tooltipBottom);

                document.body.appendChild(tooltipWrapper);

                tooltipWrapper.style.opacity = 0;
                tooltipWrapper.style.filter = 'alpha(opacity=0)';

                /*
                 * If the tooltip is not clickable we shouldn't be able to hover it
                 */
                if (opts.clickable !== false) {
                    jQuery(tooltipWrapper).on('mouseover', function () {
                        clearTimeout(CM_Tooltip.timeoutId);
                        if (closeButtonClicked) {
                            clearTimeout(CM_Tooltip.delayId);
                            tooltipWrapper.style.display = 'none';
                        }
                        if (jQuery(this).is(':animated') && !closeButtonClicked) {
                            jQuery(this).stop().fadeTo(opts.timer, (opts.endalpha / 100)).show();
                        }
                    });
                }

                if (opts.clickable !== false) {
                    jQuery(tooltipWrapper).on('mouseleave', function () {
                        clearTimeout(CM_Tooltip.delayId);
                        if (!jQuery(this).is(':animated')) {
                            tooltipApi.hide();
                        }
                    });
                }

                jQuery(tooltipWrapper).on('click touchstart', function (e) {
                    if (jQuery(e.target).parents('.cmtt-audio-player').length < 1) {
                        closeButtonClicked = jQuery(e.target).attr('id') === 'tt-btn-close';
                        // opts.only_on_button = opts.mobile;

                        if (opts.only_on_button && closeButtonClicked) {
                            tooltipApi.hide();
                        }
                    }
                });

                jQuery('body').on('click touchstart', function (e) {
                    tooltipWrapperClicked = jQuery(e.target).parents('#tt').length;
                    var tooltipVisible = jQuery('#tt').is(':visible');
                    if (tooltipVisible && opts.touch_anywhere && !tooltipWrapperClicked) {
                        tooltipApi.hide();
                    }
                });

                if (opts.clickable !== false) {
                    jQuery(document).keyup(function (e) {
                        if (e.keyCode === 27) { // escape key maps to keycode `27`
                            tooltipApi.hide();
                        }
                    });
                }
                tooltipWrapperInit = 1;
            }
        },
        show: function (content, switchElement) {
            /*
             * Create the tooltip
             */
            this.create(switchElement);

            clearTimeout(CM_Tooltip.timeoutId);
            document.onmousemove = this.pos;

            CM_Tooltip.delayId = setTimeout(function () {
                if (switchElement && jQuery(switchElement).hasClass('transparent')) {
                    tooltipContainer.style.backgroundColor = 'transparent';
                } else {
                    tooltipContainer.style.backgroundColor = opts.background;
                }

                tooltipContainer.innerHTML = content;

                CM_Tooltip.parseAudioPlayer();

                tooltipWrapper.style.display = 'block';
                tooltipWrapper.style.width = 'auto';
                tooltipWrapper.style.maxWidth = opts.maxw + 'px';
                tooltipWrapper.setAttribute('aria-hidden', false);

                if (!switchElement && ie) {
                    tooltipTop.style.display = 'block';
                    tooltipBottom.style.display = 'block';
                }

                h = parseInt(tooltipWrapper.offsetHeight, 10) + opts.top;

                jQuery(tooltipWrapper).stop().fadeTo(opts.timer, (opts.endalpha / 100));
            }, opts.delay);
        },
        pos: function (e) {
            /*
             * Common part
             */
            var u, l, topShift, leftShift, screenWidth, screenHeight, horizontalOffscreen, verticalOffscreenBot, verticalOffscreenTop, fullWidth = false;
            h = parseInt(tooltipWrapper.offsetHeight, 10) + opts.top;

            if (typeof e.pageX === 'undefined' && e.type === 'touchstart') {
                u = e.originalEvent.touches[0].pageY;
                l = e.originalEvent.touches[0].pageX;
            } else
            {
                u = ie ? event.clientY + document.documentElement.scrollTop : e.pageY;
                l = ie ? event.clientX + document.documentElement.scrollLeft : e.pageX;
            }
            topShift = (u - h) > 28 ? (u - h / 2) : 28;

            leftShift = (l + opts.left - 5);
            screenWidth = jQuery(window).width();

            tooltipWrapper.style.right = 'none';
            tooltipWrapper.style.left = 'none';

            /*
             * Check the vertical offscreen
             */

            horizontalOffscreen = (screenWidth - leftShift) < opts.minw;

            if (horizontalOffscreen)
            {
                tooltipWrapper.style.width = 'auto';
                tooltipWrapper.style.left = null;
                tooltipWrapper.style.right = 0 + 'px';
                /*
                 * Recalculate the height
                 */
                h = parseInt(tooltipWrapper.offsetHeight, 10) + opts.top;
                fullWidth = true;
                topShift -= h / 2 + 10;
            } else
            {
                tooltipWrapper.style.width = 'auto';
                tooltipWrapper.style.left = leftShift + 'px';
                tooltipWrapper.style.right = null;
            }

            /*
             * Check the vertical offscreen
             */
            screenHeight = jQuery(window).height();

            var docViewTop = jQuery(window).scrollTop();
            var docViewBottom = docViewTop + screenHeight;

            var elemTop = topShift;
            var elemBottom = elemTop + h;

            if (jQuery('#wpadminbar').length)
            {
                docViewTop += jQuery('#wpadminbar').height();
            }

            verticalOffscreenBot = elemBottom > docViewBottom;
            verticalOffscreenTop = elemTop < docViewTop;

            if (verticalOffscreenBot)
            {
                topShift -= ((elemBottom - docViewBottom) + 1);
            }
            if (verticalOffscreenTop)
            {
                if (fullWidth)
                {
                    topShift += h + 20;
                } else
                {
                    topShift += ((docViewTop - elemTop) + 1);
                }
            }

            tooltipWrapper.style.top = topShift + 'px';

            /*
             * If the tooltip has to be clickable we have to turnoff it's repositioning 'feature'
             */
            if (opts.clickable) {
                document.onmousemove = null;
            }
            /*
             * Touch devices should not fire this
             */
            if (CM_Tools.Modernizr.touch)
            {
                document.onmousemove = null;
            }
        },
        fade: function (d) {
            if (!tooltipWrapperInit) {
                return;
            }
            var i, a = alpha;
            tooltipWrapper.setAttribute('aria-hidden', true);
            if ((a !== opts.endalpha && d === 1) || (a !== 0 && d === -1)) {
                i = opts.speed;
                if (opts.endalpha - a < opts.speed && d === 1) {
                    i = opts.endalpha - a;
                } else if (alpha < opts.speed && d === -1) {
                    i = a;
                }
                alpha = a + (i * d);
                tooltipWrapper.style.opacity = alpha * 0.01;
                tooltipWrapper.style.filter = 'alpha(opacity=' + alpha + ')';
            } else {
                clearInterval(tooltipWrapper.timer);
                if (d === -1) {
                    tooltipWrapper.style.display = 'none';
                }
            }
            jQuery(tooltipContainer).html('');
        },
        hide: function () {
            if (!tooltipWrapperInit) {
                return;
            }
            tooltipWrapper.setAttribute('aria-hidden', true);
            jQuery(tooltipWrapper).stop().fadeOut(opts.timer).fadeTo(0, 0);

            jQuery('#tt audio').each(function () {
                this.pause(); // Stop playing
                this.currentTime = 0; // Reset time
            });
            jQuery('#tt iframe').remove();
        }
    };
    return tooltipApi;
};

CM_Tooltip.glossaryTip = null;

/*
 * Inside this closure we use $ instead of jQuery in case jQuery is reinitialized again before document.ready()
 */
(function ($) {
    "use strict";
    $.fn.glossaryTooltip = function (options) {
        var opts = {
            top: 3,
            left: 23,
            maxw: 400,
            minw: 200,
            speed: 10,
            timer: 0,
            delay: 0,
            endalpha: 95,
            borderStyle: 'none',
            borderWidth: '0px',
            borderColor: '#000',
            borderRadius: '6px',
            padding: '2px 12px 3px 7px',
            clickable: true,
            close_button: true,
            close_button_mobile: true,
            only_on_button: true,
            placement: 'horizontal'
        };
        opts = $.extend({}, opts, options);
        CM_Tooltip.glossaryTip = CM_Tooltip.gtooltip(opts);

        if (this.length)
        {
            if (this[0].tagName === 'A' && CM_Tools.Modernizr.touch)
            {
                $(document).on('click', '[data-cmtooltip]', function (e) {
                    e.preventDefault();
                });
            }

            opts.mobile = 0;

            return this.each(function () {
                var tooltipContent, tooltipContentMaybeHash, $inputCopy;

                tooltipContentMaybeHash = $(this).data('cmtooltip');
                if (typeof window.cmtt_data.cmtooltip_definitions !== 'undefined' && typeof window.cmtt_data.cmtooltip_definitions[tooltipContentMaybeHash] !== 'undefined') {
                    tooltipContent = jQuery('<div>' + window.cmtt_data.cmtooltip_definitions[tooltipContentMaybeHash] + '</div>').html();
                } else {
                    tooltipContent = tooltipContentMaybeHash;
                }

                if (this.tagName === 'A' && CM_Tools.Modernizr.touch)
                {
                    opts.mobile = 1;
                    /*
                     * Add link at the bottom of the tooltip
                     */
                    if (window.cmtt_data !== undefined && window.cmtt_data.mobile_support === "1") {
                        $inputCopy = $(this).clone();
                        $inputCopy.removeAttr('data-cmtooltip');
                        tooltipContent += $('<div class="mobile-link"/>').append($inputCopy)[0].outerHTML;
                    }

                    /*
                     * Proper support for touch devices
                     */
                    $(this).on('touchstart', function (e) {
                        CM_Tooltip.glossaryTip.show(tooltipContent, this);

                        setTimeout(function () {
                            CM_Tooltip.glossaryTip.pos(e);
                        }, opts.delay + 100);

                        e.preventDefault();
                        return false;
                    });

                    $(this).on('touchmove', function (e) {
                        e.preventDefault();
                        return false;
                    });

                    $(this).on('touchend', function (e) {
                        e.preventDefault();
                        return false;
                    });

                    /*
                     * Support for touch+mouse devices
                     */
                    $(this).on('click', function (e) {
                        CM_Tooltip.glossaryTip.show(tooltipContent, this);
                        setTimeout(function () {
                            CM_Tooltip.glossaryTip.pos(e);
                        }, opts.delay + 100);
                        return false;
                    });
                } else
                {
                    /*
                     * Display tooltips on click and not on hover
                     */
                    if (window.cmtt_data !== undefined && window.cmtt_data.tooltip_on_click === "1") {
                        $(this).on('click', function (e) {
                            CM_Tooltip.glossaryTip.show(tooltipContent, this);
                            setTimeout(function () {
                                CM_Tooltip.glossaryTip.pos(e);
                            }, opts.delay + 100);
                            return false;
                        });
                    } else
                    {
                        /*
                         * Display tooltips on hover
                         */
                        $(this).on('mouseenter focusin', function (e) {
                            clearTimeout(CM_Tooltip.timeoutId);
                            CM_Tooltip.glossaryTip.show(tooltipContent, this);
                            //CM_Tooltip.glossaryTip.pos( e );
                        }).on('mouseleave focusout', function () {
                            if (opts.close_on_moveout) {
                                clearTimeout(CM_Tooltip.delayId);
                                CM_Tooltip.timeoutId = setTimeout(function () {
                                    CM_Tooltip.glossaryTip.hide();
                                }, opts.timer);
                            }
                        });
                    }
                }
            });
        }
    };

    $(document).ready(function () {

        setTimeout(function () {
            $(document).trigger('glossaryTooltipReady');
        }, 5);

        $(document).ajaxComplete(function () {
            $(document).trigger('glossaryTooltipReady');
        });

        $(document).on("glossaryTooltipReady", function () {
            if (window.cmtt_data !== undefined && window.cmtt_data.cmtooltip) {

                /*
                 * Mobile detected and tooltips hidden
                 */
                if (CM_Tools.Modernizr.touch && window.cmtt_data.mobile_disable_tooltips === "1") {
                    return;
                }
    
                /*
                 * Disable tooltips on desktops
                 */
                if (!CM_Tools.Modernizr.touch && window.cmtt_data.desktop_disable_tooltips === "1") {
                    return;
                }

                if (window.cmtt_data !== undefined && window.cmtt_data.tooltip_on_click === "1") {
                    /*
                     * If tooltips are opened onclick they have to be clickable
                     */
                    window.cmtt_data.cmtooltip.clickable = true;
                }
                
                if(CM_Tools.Modernizr.touch){
                    /*
                     * If tooltips are opened on a touch device they have to be clickable
                     */
                    window.cmtt_data.cmtooltip.clickable = true;
                }

                $("[data-cmtooltip]").glossaryTooltip(window.cmtt_data.cmtooltip);
            }
        });

        /*
         * Update tooltips on Datatables redraw
         */
        if (typeof jQuery.fn.dataTable !== 'undefined')
        {
            jQuery(document).on("draw.dt", function () {
                jQuery(document).trigger('glossaryTooltipReady');
            });
        }

        /*
         * CM Tooltip Custom Code
         */
        $('.cmtt-post-tags').on('click', 'a', function () {
            var $this, $url, $parent, $tempform;
            $this = $(this);
            $parent = $this.closest('.cmtt-post-tags');
            $url = $parent.data('glossary-url');
            $tempform = $('<form/>', {
                'action': $url,
                'method': 'post'
            });
            $tempform.append($('<input type="hidden" name="gtags[]" value="' + $this.data('tagid') + '" />'));
            $parent.append($tempform);
            $tempform.submit();
        });

        /*
         * CM Tooltip Custom Code
         */
        $('.cmtt-taxonomy-single').on('click', 'a', function () {
            var $this, $url, $parent, $tempform;
            $this = $(this);
            $parent = $this.closest('.cmtt-taxonomy-single');
            $url = $parent.data('glossary-url');
            $tempform = $('<form/>', {
                'action': $url,
                'method': 'post'
            });
            $tempform.append($('<input type="hidden" name="' + $this.data('taxonomy-name') + '" value="' + $this.data('tagid') + '" />'));
            $parent.append($tempform);
            $tempform.submit();
        });

        CM_Tooltip.parseAudioPlayer = function () {
        };
        CM_Tooltip.parseAudioPlayer();

        /*
         * Sharing Box
         */
        CM_Tooltip.shareBox = function () {

            /*
             * We will assume that if we don't have the box we don't need this scripts
             */
            if ($(".cmtt-social-box").length === 0) {
                return;
            }

            /*
             * We will assume that if we have one type of button we have them all
             */
            if ($(".twitter-share-button").length === 0) {
                return;
            }

            if (typeof (twttr) !== 'undefined' && typeof (twttr.widgets) !== 'undefined') {
                twttr.widgets.load();
            } else {
                $.getScript('//platform.twitter.com/widgets.js');
            }

            //Linked-in
            if (typeof (IN) !== 'undefined') {
                IN.parse();
            } else {
                $.getScript("//platform.linkedin.com/in.js");
            }

            (function () {
                var po = document.createElement('script');
                po.type = 'text/javascript';
                po.async = true;
                po.src = '//apis.google.com/js/plusone.js';
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(po, s);
            })();

            (function (d, s, id) {
                if (typeof window.fbAsyncInit === 'undefined')
                {
                    window.fbAsyncInit = function () {
                        // Don't init the FB as it needs API_ID just parse the likebox
                        FB.XFBML.parse();
                    };

                    var js, fjs = d.getElementsByTagName(s)[0];
                    if (d.getElementById(id))
                        return;
                    js = d.createElement(s);
                    js.id = id;
                    js.src = "//connect.facebook.net/en_US/all.js";
                    fjs.parentNode.insertBefore(js, fjs);
                }
            }(document, 'script', 'facebook-jssdk'));
        };
        CM_Tooltip.shareBox();

    });
}(jQuery));
var CM_Tooltip = {};

/*jslint browser: true*/
/*global cmtt_data, cmtt_listnav_data, console, document, jQuery*/

/*
 *
 * @use jQuery
 * @use document
 * @param {type} opts
 * @returns {CM_Tooltip.gtooltip.methods}
 */
CM_Tooltip.gtooltip = function (opts) {
    "use strict";
    var tooltipWrapper, tooltipTop, tooltipContainer, tooltipBottom, h, id, alpha, ie, tooltipApi;

    tooltipWrapper = null;
    id = 'tt';
    alpha = 0;
    ie = document.all ? true : false;

    tooltipApi = {
        create: function (switchElement) {
            if (tooltipWrapper === null) {
                tooltipWrapper = document.createElement('div');
                tooltipWrapper.setAttribute('id', id);

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

                tooltipWrapper.appendChild(tooltipTop);
                tooltipWrapper.appendChild(tooltipContainer);
                tooltipWrapper.appendChild(tooltipBottom);

                document.body.appendChild(tooltipWrapper);

                tooltipWrapper.style.opacity = 0;
                tooltipWrapper.style.filter = 'alpha(opacity=0)';

                /*
                 * If the tooltip is not clickable we shouldn't be able to hover it
                 */
                if (opts.clickable !== 0) {
                    jQuery(tooltipWrapper).mouseover(function () {
                        clearTimeout(CM_Tooltip.timeoutId);
                        if (jQuery(this).is(':animated')) {
                            jQuery(this).stop().fadeTo(tooltipWrapper.timer, (opts.endalpha / 100));
                        }
                    });
                }

                jQuery(tooltipWrapper).mouseleave(function () {
                    if (!jQuery(this).is(':animated')) {
                        tooltipApi.hide();
                    }
                });

                jQuery(tooltipWrapper).click(function (e) {
                    if (jQuery(e.target).parents('.cmtt-audio-player').length < 1)
                    {
                        tooltipApi.hide();
                    }
                });
            }
        },
        show: function (content, switchElement) {
            /*
             * Create the tooltip
             */
            this.create(switchElement);

            clearTimeout(CM_Tooltip.timeoutId);

            document.onmousemove = this.pos;

            if (switchElement && jQuery(switchElement).hasClass('transparent'))
            {
                tooltipContainer.style.backgroundColor = 'transparent';
            }
            else
            {
                tooltipContainer.style.backgroundColor = opts.background;
            }

            tooltipContainer.innerHTML = content;

            tooltipWrapper.style.display = 'block';
            tooltipWrapper.style.width = 'auto';
            tooltipWrapper.style.maxWidth = opts.maxw+'px';

            if (!switchElement && ie) {
                tooltipTop.style.display = 'block';
                tooltipBottom.style.display = 'block';
            }

            h = parseInt(tooltipWrapper.offsetHeight, 10) + opts.top;

            jQuery(tooltipWrapper).stop().fadeTo(tooltipWrapper.timer, (opts.endalpha / 100));
        },
        pos: function (e) {
            var u, l, x, leftShift, screenWidth, testNumber;
            u = ie ? event.clientY + document.documentElement.scrollTop : e.pageY;
            l = ie ? event.clientX + document.documentElement.scrollLeft : e.pageX;
            x = (u - h) > 28 ? (u - h / 2) : 28;

            leftShift = (l + opts.left - 5);
            screenWidth = jQuery(window).width();

            tooltipWrapper.style.right = 'none';
            tooltipWrapper.style.left = 'none';

            testNumber = (screenWidth - leftShift) < opts.minw;

            if (testNumber )
            {
                tooltipWrapper.style.width = 'auto';
                tooltipWrapper.style.left = null;
                tooltipWrapper.style.right = 0 + 'px';
                h = parseInt(tooltipWrapper.offsetHeight, 10) + opts.top;
                x = u - h;
            }
            else
            {
                tooltipWrapper.style.width = 'auto';
                tooltipWrapper.style.left = leftShift + 'px';
                tooltipWrapper.style.right = null;
            }

            tooltipWrapper.style.top = x + 'px';

            /*
             * If the tooltip has to be clickable we have to turnoff it's repositioning 'feature'
             */
            if (opts.clickable) {
                document.onmousemove = null;
            }
        },
        fade: function (d) {
            var i, a = alpha;
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
        },
        hide: function () {
            jQuery(tooltipWrapper).stop().fadeOut(tooltipWrapper.timer).fadeTo(0, 0);
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
            timer: 500,
            endalpha: 95,
            borderStyle: 'none',
            borderWidth: '0px',
            borderColor: '#000',
            borderRadius: '6px',
            padding: '2px 12px 3px 7px',
            clickable: true

        };
        opts = $.extend({}, opts, options);
        CM_Tooltip.glossaryTip = CM_Tooltip.gtooltip(opts);

        if (this.length)
        {
            return this.each(function () {
                var tooltipContent;

                tooltipContent = $(this).data('cmtooltip');

                $(this).mouseenter(function () {
                    clearTimeout(CM_Tooltip.timeoutId);
                    CM_Tooltip.glossaryTip.show(tooltipContent, this);
                }).mouseleave(function () {
                    CM_Tooltip.timeoutId = setTimeout(function () {
                        CM_Tooltip.glossaryTip.hide();
                    }, options.timer);
                });
            });
        }
    };

    $(document).ready(function () {

        setTimeout(function () {
            $(document).trigger('glossaryTooltipReady');
        }, 5);

        $(document).on("glossaryTooltipReady", function () {
            if (window.cmtt_data !== undefined && window.cmtt_data.tooltip) {
                $("[data-cmtooltip]").glossaryTooltip(window.cmtt_data.tooltip);
            }
        });
    });
}(jQuery));
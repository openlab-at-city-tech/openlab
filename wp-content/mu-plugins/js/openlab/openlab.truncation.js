/**
 * OpenLab search dropdowns
 */


if (window.OpenLab === undefined) {
    var OpenLab = {};
}

var truncationResizeTimer = {};

OpenLab.truncation = (function ($) {

    return{
        init: function () {

            if ($('.truncate-on-the-fly').length) {
                setTimeout(function () {

                    OpenLab.truncation.truncateOnTheFly(true);

                }, 600);
            }

        },
        truncateOnTheFly: function (onInit, loadDelay) {

            if (onInit === undefined) {
                var onInit = false;
            }

            if (loadDelay === undefined) {
                var loadDelay = false;
            }

            $('.truncate-on-the-fly').each(function () {

                var thisElem = $(this);

                if (!loadDelay && thisElem.hasClass('load-delay')) {
                    return true;
                }

                var originalCopy = thisElem.parent().find('.original-copy').html();
                thisElem.html(originalCopy);

                var truncationBaseValue = thisElem.data('basevalue');
                var truncationBaseWidth = thisElem.data('basewidth');

                if (truncationBaseWidth === 'calculate') {

                    var sizerContainer = OpenLab.truncation.truncateSizerContainer(thisElem);
                    var static_w = 0;

                    sizerContainer.find('.truncate-static').each(function () {
                        static_w += $(this).width();
                    });

                    var available_w = sizerContainer.width() - static_w - 20;

                    if (available_w > 0) {
                        truncationBaseWidth = available_w;
                    } else {
                        truncationBaseWidth = 0;
                    }
                }

                var container_w = thisElem.parent().width();

                if (thisElem.data('link')) {

                    var omissionText = 'See More';

                    //for screen reader only append
                    //provides screen reader with addtional information in-link
                    if (thisElem.data('includename')) {

                        var nameTrunc = thisElem.data('includename');

                        //if the groupname is truncated, let's use that
                        var srprovider = thisElem.closest('.truncate-combo').find('[data-srprovider]');

                        if (srprovider.length) {
                            nameTrunc = srprovider.text();
                        }

                        omissionText = omissionText + ' <div class="sr-only sr-only-groupname">' + nameTrunc + '</div>';

                    }

                    var thisOmission = '<a href="' + thisElem.data('link') + '">' + omissionText + '</a>';
                } else {
                    var thisOmission = '';
                }

                if (container_w < truncationBaseWidth) {
                    var truncationValue = truncationBaseValue - (Math.round(((truncationBaseWidth - container_w) / truncationBaseWidth) * 100));
                    thisElem.find('.omission').remove();

                    if (!onInit) {
                        OpenLab.truncation.truncateMainAction(thisElem, truncationValue, thisOmission);
                    }

                } else {

                    if (thisElem.data('basewidth') === 'calculate') {

                        var sizerContainer_w = sizerContainer.width();
                        var sizerContainer_h = sizerContainer.height();

                        sizerContainer.css({
                            'white-space': 'nowrap'
                        });

                        var sizerContainerNoWrap_w = sizerContainer.width();
                        var sizerContainerNoWrap_h = sizerContainer.height();

                        sizerContainer.css({
                            'white-space': 'normal'
                        });

                        if (sizerContainerNoWrap_w <= sizerContainer_w && sizerContainer_h === sizerContainerNoWrap_h) {
                            OpenLab.truncation.truncateReveal(thisElem);
                            return;
                        }

                        if (truncationBaseWidth < container_w) {

                            for (var looper = 0; looper < (truncationBaseValue + 1); looper++) {

                                if (thisElem.data('html')) {

                                    var truncationValue = looper;

                                    var myString = new HTMLString.String(thisElem.find('p').html());
                                    var sliceValue = Math.abs(myString.length() - truncationValue);
                                    var truncatedString = myString.slice(0, sliceValue);
                                    thisElem.find('p').html(truncatedString.html() + '<span class="omission">&hellip; ' + thisOmission + '</span>');

                                } else {
                                    var truncationValue = truncationBaseValue - looper;
                                    OpenLab.truncation.truncateMainAction(thisElem, truncationValue, thisOmission);
                                }

                                sizerContainer.css({
                                    'white-space': 'nowrap'
                                });

                                sizerContainerNoWrap_w = sizerContainer.width();

                                sizerContainer.css({
                                    'white-space': 'normal'
                                });

                                //recalculate sizes
                                sizerContainer_w = sizerContainer.width();
                                sizerContainer_h = sizerContainer.height();

                                if (sizerContainerNoWrap_w <= sizerContainer_w && sizerContainer_h === sizerContainerNoWrap_h) {

                                    break;

                                }

                            }

                        }

                    } else {

                        var truncationValue = truncationBaseValue;

                        if (!onInit) {
                            OpenLab.truncation.truncateMainAction(thisElem, truncationValue, thisOmission);
                        }

                    }

                }

                if (onInit) {
                    OpenLab.truncation.truncateMainAction(thisElem, truncationValue, thisOmission);
                }

                OpenLab.truncation.truncateReveal(thisElem);
            });
        },
        truncateMainAction: function (thisElem, truncationValue, thisOmission) {

            if (thisElem.data('minvalue')) {
                if (truncationValue < thisElem.data('minvalue')) {
                    truncationValue = thisElem.data('minvalue');
                }
            }

            if (truncationValue > 10) {
                thisElem.succinct({
                    size: truncationValue,
                    omission: '<span class="omission">&hellip; ' + thisOmission + '</span>'
                });

                //if we have an included groupname in the screen reader only link text
                //let's truncate it as well
                if (thisElem.data('srprovider')) {
                    var srLink = thisElem.closest('.truncate-combo').find('.sr-only-groupname');
                    srLink.text(thisElem.text());
                }

            } else {
                thisElem.html('<span class="omission">' + thisOmission + '</span>');
            }

        },
        truncateSizerContainer: function (thisElem) {

            var thisContainer = thisElem.closest('.truncate-sizer');
            var breakpoints = ['lg', 'md', 'sm', 'xs', 'xxs'];

            for (var i = 0; i < breakpoints.length; i++) {

                var breakpoint = breakpoints[i];
                var checkContainer = thisElem.closest('.truncate-sizer-' + breakpoint);

                if (checkContainer.length && OpenLab.truncation.isBreakpoint(breakpoint)) {
                    thisContainer = checkContainer;
                }

            }

            return thisContainer;

        },
        truncateReveal: function (thisElem) {
            thisElem.animate({
                opacity: '1.0'
            });

            $('.truncate-obfuscate')
                    .css({
                        'opacity': 0
                    })
                    .removeClass('invisible')
                    .animate({
                        'opacity': 1
                    }, 700);

            $(document).trigger('truncate-obfuscate-removed', thisElem);
        }
    }
})(jQuery, OpenLab);

(function ($) {
    $(document).ready(function () {

        OpenLab.truncation.init();

    });

    $(window).on('resize', function (e) {
        
        clearTimeout(truncationResizeTimer);
        truncationResizeTimer = setTimeout(function () {
            
            if ($('.truncate-on-the-fly').length) {

                $('.trucate-obfuscate').css('opacity', 0);
                OpenLab.truncation.truncateOnTheFly(false);
            }

        }, 250);

    });

    $(document).on('truncate-obfuscate-removed', function (e, thisElem) {

        $(thisElem).closest('.menu-loading').removeClass('menu-loading');

    });

})(jQuery);
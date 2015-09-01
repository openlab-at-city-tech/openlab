/**
 * OpenLab search dropdowns
 */

(function ($) {

    if (window.OpenLab === undefined) {
        var OpenLab = {};
    }

    var resizeTimer;

    OpenLab.nav = {
        backgroundCont: {},
        plusHeight: 66,
        init: function () {

            OpenLab.nav.backgroundCont = $('#behind_menu_background');

            OpenLab.nav.directToggleAction();
            OpenLab.nav.backgroundAction();
            OpenLab.nav.mobileAnchorLinks();
            OpenLab.nav.hoverFixes();
            
            OpenLab.nav.hyphenateInit();

        },
        hyphenateInit: function () {
            Hyphenator.config(
                    {onhyphenationdonecallback: onHyphenationDone = function (context) {
                            return undefined;
                        },
                        useCSS3hyphenation: true
                    }
            );
            Hyphenator.run();
        },
        hoverFixes: function () {
            //fixing hover issues on mobile
            if (OpenLab.nav.isBreakpoint('xs') || OpenLab.nav.isBreakpoint('sm')) {
                $('.mobile-no-hover').bind('touchend', function () {
                    OpenLab.nav.fixHoverOnMobile($(this));
                })
            }
        },
        directToggleAction: function () {

            //if there is no direct toggle, we're done
            if (!$('.direct-toggle').length) {
                return false;
            }

            var directToggle = $('.direct-toggle');

            directToggle.on('click', function (e) {
                directToggle.removeClass('active')
                e.stopImmediatePropagation();

                var thisElem = $(this);

                thisElem.addClass('active');
                if (!thisElem.hasClass('in-action')) {

                    directToggle.removeClass('in-action');
                    thisElem.addClass('in-action');

                    var thisTarget = $(this).data('target');
                    var thisTargetElem = $(thisTarget);

                    if (thisTargetElem.is(':visible')) {

                        OpenLab.nav.hideNavMenu(thisElem, thisTargetElem);

                    } else {

                        directToggle.each(function () {
                            var thisElem = $(this);
                            var thisToggleTarget = thisElem.data('target');

                            if ($(thisToggleTarget).is(':visible')) {

                                OpenLab.nav.hideNavMenu(thisElem, thisToggleTarget);

                            }
                        });

                        OpenLab.nav.showNavMenu(thisElem, thisTargetElem);

                    }
                }
            });
        },
        hideNavMenu: function (thisElem, thisToggleTarget, thisAnchor) {
            var plusHeight = OpenLab.nav.plusHeight;

            if (thisElem.attr('data-plusheight')) {
                plusHeight = parseInt(thisElem.data('plusheight'));
            }

            var thisTargetElem_h = $(thisToggleTarget).height();
            thisTargetElem_h += plusHeight;

            OpenLab.nav.backgroundCont.removeClass('active').animate({
                'opacity': 0,
                'top': '-=' + thisTargetElem_h + 'px'
            }, 50, function () {
                $(this).hide();
            });
            $(thisToggleTarget).slideUp(700, function () {
                thisElem.removeClass('in-action');
                thisElem.removeClass('active');

                if (thisAnchor) {
                    $.smoothScroll({
                        scrollTarget: thisAnchor
                    });
                }

            });
        },
        showNavMenu: function (thisElem, thisTargetElem) {
            var plusHeight = OpenLab.nav.plusHeight;

            if (thisElem.attr('data-plusheight')) {
                plusHeight = parseInt(thisElem.data('plusheight'));
            }

            thisTargetElem.slideDown(700, function () {

                var thisTargetElem_h = thisTargetElem.height();
                thisTargetElem_h += plusHeight;

                thisElem.removeClass('in-action');

                OpenLab.nav.backgroundCont.addClass('active').show()
                        .css({
                            'top': '+=' + thisTargetElem_h + 'px'
                        })
                        .animate({
                            'opacity': 0.42,
                        }, 500);

                //for customSelect
                $('.custom-select').each(function () {
                    var customSelect_h = $(this).find('.customSelect').outerHeight();
                    var customSelect_w = $(this).find('.customSelect').outerWidth();
                    $(this).find('select').css({
                        'height': customSelect_h + 'px',
                        'width': customSelect_w + 'px'
                    });
                })
            });
        },
        backgroundAction: function () {

            OpenLab.nav.backgroundCont.on('click', function () {

                var thisElem = $(this);
                var currentActiveButton = $('.direct-toggle.active');
                var targetToClose = currentActiveButton.data('target');

                OpenLab.nav.hideNavMenu(currentActiveButton, targetToClose);

            });

        },
        mobileAnchorLinks: function () {
            if ($('.mobile-anchor-link').length) {
                $('.mobile-anchor-link').find('a').on('click', function (e) {
                    e.preventDefault();
                    var thisElem = $(this);
                    var thisAnchor = thisElem.attr('href');

                    var currentActiveButton = $('.direct-toggle.active');
                    var background = $('#behind_menu_background');
                    var targetToClose = currentActiveButton.data('target');

                    OpenLab.nav.hideNavMenu(currentActiveButton, targetToClose, thisAnchor);

                });
            }
        },
        isBreakpoint: function (alias) {
            return $('.device-' + alias).is(':visible');
        },
        fixHoverOnMobile: function (thisElem) {
            thisElem.trigger('click');
        }
    };

    $(document).ready(function () {

        OpenLab.nav.init();

    });

    $(window).on('resize', function (e) {

        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            
            OpenLab.nav.hoverFixes();

        }, 250);

    });

})(jQuery);
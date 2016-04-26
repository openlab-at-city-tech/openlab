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
        backgroundTopStart: 0,
        plusHeight: 66,
        init: function () {

            OpenLab.nav.backgroundCont = $('#behind_menu_background');

            //get starting position of mobile menu background
            OpenLab.nav.backgroundTopStart = OpenLab.nav.backgroundCont.css('top');

            OpenLab.nav.directToggleAction();
            OpenLab.nav.backgroundAction();
            OpenLab.nav.mobileAnchorLinks();
            OpenLab.nav.hoverFixes();
            OpenLab.nav.tabindexNormalizer();

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
        tabindexNormalizer: function () {

            //find tabindices in the adminbar greater than 1 and re-set
            $('#wpadminbar [tabindex]').each(function () {

                var thisElem = $(this);
                if (parseInt(thisElem.attr('tabindex')) > 0) {
                    thisElem.attr('tabindex', 0);
                }

            });

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

                    var thisTarget = thisElem.data('target');
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
        directToggleResizeHandler: function () {

            //if there is no direct toggle, we're done
            if (!$('.direct-toggle').length) {
                return false;
            }

            //reset mobile menu background position
            OpenLab.nav.backgroundCont.css({
                'top': OpenLab.nav.backgroundTopStart
            })

            var directToggle = $('.direct-toggle');

            directToggle.each(function () {
                var thisElem = $(this);
                var thisToggleTarget = thisElem.data('target');

                if (!OpenLab.nav.isBreakpoint('xs') && !OpenLab.nav.isBreakpoint('xxs')) {
                    //on background only elems, reset inline display value
                    if (thisElem.data('backgroundonly') && thisElem.data('backgroundonly') === true) {
                        $(thisToggleTarget).css({
                            'display': ''
                        });
                    }
                }

                if (thisElem.hasClass('active')) {

                    OpenLab.nav.hideNavMenu(thisElem, thisToggleTarget, false, true);

                }
            });

        },
        hideNavMenu: function (thisElem, thisToggleTarget, thisAnchor, triggerBackgroundOnlyCheck) {
            var plusHeight = OpenLab.nav.plusHeight;
            var backgroundOnly = false;

            //handle missing arguments
            if (typeof thisAnchor === 'undefined') {
                var thisAnchor = false;
            }
            if (typeof triggerBackgroundOnlyCheck === 'undefined') {
                var triggerBackgroundOnlyCheck = false;
            }

            //background only acheck
            if (thisElem.data('backgroundonly') && thisElem.data('backgroundonly') === true) {
                backgroundOnly = true;
            }

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

            //if background only, we're done
            if (backgroundOnly && triggerBackgroundOnlyCheck) {
                thisElem.removeClass('in-action');
                thisElem.removeClass('active');
                $(thisToggleTarget).css({
                    'display': ''
                });
                return false;
            }

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
            OpenLab.nav.directToggleResizeHandler();

        }, 250);

    });

})(jQuery);
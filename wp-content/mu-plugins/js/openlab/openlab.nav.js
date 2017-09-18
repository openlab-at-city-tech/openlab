/**
 * OpenLab search dropdowns
 */

if (window.OpenLab === undefined) {
    var OpenLab = {};
}

var navResizeTimer = {};

OpenLab.nav = (function ($) {
    return{
        backgroundCont: {},
        backgroundTopStart: 0,
        plusHeight: 66,
        init: function () {

            OpenLab.nav.loginformInit();

            OpenLab.nav.backgroundCont = $('#behind_menu_background');

            //get starting position of mobile menu background
            OpenLab.nav.backgroundTopStart = OpenLab.nav.backgroundCont.css('top');

            OpenLab.nav.removeDefaultScreenReaderShortcut();
            OpenLab.nav.directToggleAction();
            OpenLab.nav.backgroundAction();
            OpenLab.nav.mobileAnchorLinks();
            OpenLab.nav.hoverFixes();
            OpenLab.nav.tabindexNormalizer();
            OpenLab.nav.focusActions();
            OpenLab.nav.blurActions();

            OpenLab.nav.hyphenateInit();

        },
        loginformInit: function () {

            var loginform = utilityVars.loginForm;

            $("#wp-admin-bar-bp-login").append(loginform);

            $("#wp-admin-bar-bp-login > a").click(function () {

                if (!$(this).hasClass('login-click')) {
                    $(this).closest('#wp-admin-bar-bp-login').addClass('login-form-active');
                }

                $(".ab-submenu #sidebar-login-form").toggle(400, function () {
                    $(".ab-submenu #dropdown-user-login").focus();
                    if ($(this).hasClass('login-click')) {
                        $(this).closest('#wp-admin-bar-bp-login').removeClass('login-form-active');
                    }
                    $(this).toggleClass("login-click");
                });

                OpenLab.nav.blurActions();
                return false;
            });
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

            //add tabindex to mol icon menus
            $('#wp-admin-bar-invites, #wp-admin-bar-messages, #wp-admin-bar-activity, #wp-admin-bar-my-account, #wp-admin-bar-top-logout, #wp-admin-bar-bp-register, #wp-admin-bar-bp-login').attr('tabindex', '0');

        },
        focusActions: function () {

            //active menupop for keyboard users
            var adminbar = $('#wpadminbar');

            adminbar.find('li.menupop').on('focus', function (e) {

                var el = $(this);

                if (el.parent().is('#wp-admin-bar-root-default') && !el.hasClass('hover')) {
                    e.preventDefault();
                    adminbar.find('li.menupop.hover').removeClass('hover');
                    el.addClass('hover');
                } else if (!el.hasClass('hover')) {
                    e.stopPropagation();
                    e.preventDefault();
                    el.addClass('hover');
                } else if (!$(e.target).closest('div').hasClass('ab-sub-wrapper')) {
                    e.stopPropagation();
                    e.preventDefault();
                    el.removeClass('hover');
                }
            });

            var skipToAdminbar = $('#skipToAdminbar');
            var skipTarget = skipToAdminbar.attr('href');

            skipToAdminbar.on('click', function () {

                if (skipTarget === '#wp-admin-bar-bp-login') {
                    $(skipTarget).find('> a').click();
                } else if (skipTarget === '#wp-admin-bar-my-openlab') {
                    $(skipTarget).closest('.menupop').addClass('hover');
                    $('wp-admin-bar-my-openlab-default').focus();
                }

            });

        },
        blurActions: function () {

            var adminbar = $('#wpadminbar');

            //make sure the menu closes when we leave
            adminbar.find('.exit a').each(function () {

                var actionEl = $(this);

                actionEl.off('blur').on('blur', function (e) {
                    var el = $(this);

                    el.closest('.menupop').removeClass('hover');

                    //special case for login button
                    if (el.closest('#wp-admin-bar-bp-login').length) {
                        el.closest('#wp-admin-bar-bp-login').find('> a').click();
                    }

                });


            });

        },
        removeDefaultScreenReaderShortcut: function () {

            $('#wpadminbar .screen-reader-shortcut').remove();

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
                    if (thisElem.data('backgroundonly') && (thisElem.data('backgroundonly') === true || thisElem.data('backgroundonly') === 1)) {
                        $(thisToggleTarget).css({
                            'display': ''
                        });
                    }
                }

                if (thisElem.hasClass('active')) {
                    console.log('hiding menu via directToggleResizeHandler');
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
            if (thisElem.data('backgroundonly') && (thisElem.data('backgroundonly') === true || thisElem.data('backgroundonly') === 1)) {
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
        },
        userNameAdjustments: function (reset) {
            //this function exists purely to deal with weirdness surrounding overflow-x and overflow-y

            if (typeof reset === 'undefined') {
                reset = false;
            }

            var targetElem = $('#wp-admin-bar-blogs-and-admin-centered');

            if (reset) {
                targetElem.css({
                    'max-width': 'none',
                    'overflow': 'hidden',
                    'float': 'none'
                });

                return true;

            }

            var targetElem_w = targetElem.outerWidth();

            targetElem.css({
                'max-width': targetElem_w + 'px',
                'overflow': 'visible',
                'float': 'right  '
            });

            //on mobile remove the dropdown
            if (OpenLab.nav.isBreakpoint('xs') || OpenLab.nav.isBreakpoint('xxs')) {
                targetElem.find('#wp-admin-bar-my-account').removeClass('menupop');
                targetElem.find('.ab-sub-wrapper').addClass('hidden');
            } else {
                targetElem.find('#wp-admin-bar-my-account').addClass('menupop');
                targetElem.find('.ab-sub-wrapper').removeClass('hidden');
            }

        },
        isBreakpoint: function (alias) {
            return $('.device-' + alias).is(':visible');
        },
    }
})(jQuery, OpenLab);

(function ($) {

    var windowWidth = $(window).width();

    $(document).ready(function () {

        OpenLab.nav.init();

    });

    $(window).on('resize', function (e) {

        OpenLab.nav.userNameAdjustments(true);

        clearTimeout(navResizeTimer);
        navResizeTimer = setTimeout(function () {

            //checking to see if this is truly a resize event
            if ($(window).width() != windowWidth) {

                windowWidth = $(window).width();

                OpenLab.nav.hoverFixes();
                OpenLab.nav.directToggleResizeHandler();

            }

        }, 250);

    });

    $(document).on('truncate-obfuscate-removed', function (e, thisElem) {

        if ($(thisElem).closest('.ab-top-menu').attr('id') === 'wp-admin-bar-blogs-and-admin-centered') {
            setTimeout(function () {
                OpenLab.nav.userNameAdjustments();
            }, 50);
        }

    });

})(jQuery);
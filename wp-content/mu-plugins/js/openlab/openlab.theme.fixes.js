/**
 * Any client-side theme fixes go here (for group site themes; excludes OpenLab custom theme)
 */

/**
 * Twentyfourteen
 * Makes the header relative until scrolling, to fix issue with header going behind admin bar
 */

if (window.OpenLab === undefined) {
    var OpenLab = {};
}

OpenLab.fixes = (function ($) {
    return{
        init: function () {

            if ($('body').hasClass('masthead-fixed')) {
                OpenLab.fixes.fixMasthead();
            }

        },
        fixMasthead: function () {

            //this is so that the on scroll function won't fire on themes that don't need it to
            if (!$('body').hasClass('masthead-fixing')) {
                $('body').addClass('masthead-fixing');
            }

            //get adminbar height
            var adminBar_h = $('#wpadminbar').outerHeight();
            var scrollTrigger = Math.ceil(adminBar_h / 2);

            //if were below the scrollTrigger, remove the fixed class, otherwise make sure it's there
            if (OpenLab.fixes.getCurrentScroll() <= scrollTrigger) {
                $('body').removeClass('masthead-fixed');
            } else {
                $('body').addClass('masthead-fixed');
            }

        },
        getCurrentScroll: function () {
            var currentScroll = window.pageYOffset || document.documentElement.scrollTop;

            return currentScroll;
        }
    }
})(jQuery, OpenLab);

(function ($) {

    $(document).ready(function () {
        OpenLab.fixes.init();
    });

    $(window).scroll(function () {

        if ($('body').hasClass('masthead-fixing')) {
            OpenLab.fixes.fixMasthead();
        }
    });

})(jQuery);
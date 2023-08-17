/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

(function ($) {

    // Site title and description.
    wp.customize('blogname', function (value) {
        value.bind(function (to) {
            $('.site-title a').text(to);
        });
    });
    wp.customize('blogdescription', function (value) {
        value.bind(function (to) {
            $('.site-description').text(to);
        });
    });

    // Header text color.
    wp.customize('header_textcolor', function (value) {
        value.bind(function (to) {
            if ('blank' === to) {
                $('.site-title, .site-description').css({
                    'clip': 'rect(1px, 1px, 1px, 1px)',
                    'position': 'absolute',
                    'display': 'none'
                });
            } else {
                $('.site-title, .site-description').css({
                    'clip': 'auto',
                    'position': 'relative',
                    'display': 'block'
                });
                $('.site-title a, #masthead ul.twp-social-icons.twp-social-icons-white a').css({
                    'color': to
                });
                $('#masthead .twp-menu-icon.twp-white-menu-icon span:before, #masthead .twp-menu-icon.twp-white-menu-icon span:after').css({
                    'background-color': to
                });
            }
        });
    });
})(jQuery);

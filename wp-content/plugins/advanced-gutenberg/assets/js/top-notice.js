(function ($) {
    $(window).on('load resize', function() {
        if(window.outerWidth > 600) {
            $wpBarHeight  = $('#wpadminbar').outerHeight();
            $headerHeight = $('.pp-version-notice-bold-purple').outerHeight();
            $wrapper      = $('.ju-main-wrapper');
            $wrapper.css(
                'marginTop', ($wpBarHeight + $headerHeight - 32) + 'px'
            );
        }
    });
}(jQuery));

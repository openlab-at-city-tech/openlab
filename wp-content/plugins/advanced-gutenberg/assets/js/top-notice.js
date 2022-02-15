(function ($) {

    $( document ).ready(function() {
        
        // Need to be inside the document ready. See https://github.com/publishpress/PublishPress-Blocks/pull/704
        $( window ).on('resize', advgbTopNotice);

        // Call to set the notice on the first load
        advgbTopNotice();

    });

    function advgbTopNotice() {
       
        $wrapper = $('.ju-main-wrapper');

        if( window.outerWidth > 600) {
            $wpBarHeight  = $('#wpadminbar').outerHeight();
            $headerHeight = $('.pp-version-notice-bold-purple').outerHeight();
            
            $wrapper.css(
                'marginTop', ($wpBarHeight + $headerHeight - 32) + 'px'
            );
        } else {
            $wrapper.css(
                'marginTop', 'initial'
            );
        }
    }
    
}(jQuery));

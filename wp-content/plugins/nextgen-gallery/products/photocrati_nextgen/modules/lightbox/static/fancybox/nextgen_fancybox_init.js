jQuery(function($) {
    var nextgen_fancybox_init = function() {
    		var selector = nextgen_lightbox_filter_selector($, $(".ngg-fancybox"));
    		
        selector.fancybox({
            titlePosition: 'inside',
            // Needed for twenty eleven
            onComplete: function() {
                $('#fancybox-wrap').css('z-index', 10000);
            }
        });
    };
    $(window).bind('refreshed', nextgen_fancybox_init);
    nextgen_fancybox_init();
});

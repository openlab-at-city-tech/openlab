jQuery(function($) {
    var nextgen_fancybox_init = function() {
        var selector = nextgen_lightbox_filter_selector($, $(".ngg-fancybox"));

        window.addEventListener(
            "click",
            e => {
                let $target = $(e.target);
                if ($target.is(selector) || $target.parents('a').is(selector)) {
                    e.preventDefault();
                    $(selector).fancybox({
                        titlePosition: 'inside',
                        // Needed for twenty eleven
                        onComplete: function() {
                            $('#fancybox-wrap').css('z-index', 10000);
                        }
                    })
                    $target.trigger('click.fb');
                    
                    e.stopPropagation();
                }
            },
            true
        )
    };
    $(window).on('refreshed', nextgen_fancybox_init);
    nextgen_fancybox_init();
});

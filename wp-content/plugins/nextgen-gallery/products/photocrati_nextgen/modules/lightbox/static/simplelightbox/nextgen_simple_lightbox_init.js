jQuery(function($) {

    var selector = null;

    var nextgen_simplebox_options = {
        history: false,
        animationSlide: false,
        animationSpeed: 100,
        captionSelector: 'self'
    };

    var nextgen_simplelightbox_init = function() {
        selector = nextgen_lightbox_filter_selector($, $(".ngg-simplelightbox"));
        selector.simpleLightbox(nextgen_simplebox_options);
    };

    nextgen_simplelightbox_init();

    $(window).bind('refreshed', function() {
        selector = nextgen_lightbox_filter_selector($, $(".ngg-simplelightbox"));
        var gallery = selector.simpleLightbox(nextgen_simplebox_options);
        gallery.refresh();

    });
});
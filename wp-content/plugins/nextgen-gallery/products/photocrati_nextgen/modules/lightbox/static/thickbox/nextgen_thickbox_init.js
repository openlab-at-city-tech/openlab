if (typeof window.thickboxL10n == 'undefined') {
    if (typeof nextgen_thickbox_i18n == 'undefined') {
        // for backwards compatibility, nextgen_thickbox_i18n may not exist and we should just use the English defaults
        window.thickboxL10n = {
            loadingAnimation: photocrati_ajax.wp_includes_url + '/wp-includes/js/thickbox/loadingAnimation.gif',
            closeImage: photocrati_ajax.wp_includes_url + '/wp-includes/js/thickbox/tb-close.png',
            next: 'Next &gt;',
            prev: '&lt; Prev',
            image: 'Image',
            of: 'of',
            close: 'Close',
            noiframes: 'This feature requires inline frames. You have iframes disabled or your browser does not support them.'
        };
    } else {
        window.thickboxL10n = {
            loadingAnimation: photocrati_ajax.wp_includes_url + '/wp-includes/js/thickbox/loadingAnimation.gif',
            closeImage: photocrati_ajax.wp_includes_url + '/wp-includes/js/thickbox/tb-close.png',
            next: nextgen_thickbox_i18n.next,
            prev: nextgen_thickbox_i18n.prev,
            image: nextgen_thickbox_i18n.image,
            of: nextgen_thickbox_i18n.of,
            close: nextgen_thickbox_i18n.close,
            noiframes: nextgen_thickbox_i18n.noiframes
        };
    }
}

jQuery(function($) {
    var selector = nextgen_lightbox_filter_selector($, $([]));
    selector.addClass('thickbox');
});
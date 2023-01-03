jQuery(document).ready(function ( $ ) {
    $('.advgb-image-block:not(.advgb-lightbox)').find('.advgb-image-overlay').each( function() {
        if( $(this).attr('href') === undefined ) {
            return;
        }
        var url = $(this).attr('href');
        var target = $(this).attr('target');
        if( url.length && target.length ) {
            $(this).siblings().addClass('advgb-image-text--linkable').on( 'click', function() {
                window.open(url, target);
            });
        }
    });
});

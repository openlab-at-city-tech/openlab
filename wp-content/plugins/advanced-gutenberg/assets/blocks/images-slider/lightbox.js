jQuery(document).ready(function ( $ ) {
    var galGroup = 1;
    $('.advgb-images-slider-lightbox').each(function () {
        $(this).find('.advgb-image-slider-item').colorbox({
            title: function () {
                return $(this).find('.advgb-image-slider-title').text();
            },
            maxWidth: '90%',
            maxHeight: '85%',
            fixed: true,
            className: 'advgb_lightbox',
            rel: 'gallery-' + galGroup,
            href: function () {
                return $(this).find('img').attr('src');
            }
        });
        galGroup++;
    });

});
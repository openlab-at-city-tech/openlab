jQuery(document).ready(function ( $ ) {
    $('.advgb-images-slider-lightbox .advgb-image-slider-item').colorbox({
        title: function () {
            return $(this).find('.advgb-image-slider-title').text();
        },
        maxWidth: '90%',
        maxHeight: '85%',
        fixed: true,
        className: 'advgb_lightbox',
        href: function () {
            return $(this).find('img').attr('src');
        }
    })
});
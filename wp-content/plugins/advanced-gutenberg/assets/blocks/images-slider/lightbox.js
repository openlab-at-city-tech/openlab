jQuery(document).ready(function ( $ ) {
    var a11y = window.advgbA11y || null;
    var galGroup = 1;
    $('.advgb-images-slider-lightbox').each(function () {
        var settings = {
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
            },
            onComplete: function () {
                $('#cboxLoadedContent .cboxPhoto').attr('alt', $(this).find('img').attr('alt') || '');
            }
        };

        $(this).find('.advgb-image-slider-item').colorbox(
            a11y ? a11y.withColorbox(settings, 'image') : settings
        );
        galGroup++;
    });

});

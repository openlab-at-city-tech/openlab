jQuery(document).ready(function ($) {
    var galGroup = 1;
    var galImage = null;
    var galClassName = '';

    // Caption overlay image
    if (parseInt(advgb.imageCaption) === 2) {
        galClassName = 'advgb_lightbox advgb_caption_overlay';
    } else {
        galClassName = 'advgb_lightbox';
    }

    $('.wp-block-gallery').each(function () {
        // Add lightbox for images
        $(this).find('.blocks-gallery-item, .wp-block-image').colorbox({
            title: function () {
                if (parseInt(advgb.imageCaption)) {
                    var imgCap = $(this).find('figcaption').text() || $(this).find('img').attr('alt');
                    return imgCap;
                }

                return null;
            },
            maxWidth: '90%',
            maxHeight: '85%',
            fixed: true,
            className: galClassName,
            rel: 'gallery' + galGroup,
            photo: true,
            href: function () {
                if($(this).find('a').length) {
                    // Link to: Media File or Attachment
                    galImage = $(this).find('a');
                    if(galImage.attr('href').indexOf('.jpg') > 0 || galImage.attr('href').indexOf('.png') > 0 || galImage.attr('href').indexOf('.gif') > 0 || galImage.attr('href').indexOf('.jpeg') > 0 || galImage.attr('href').indexOf('.webp') > 0) {
                        // Link to: Media File
                        return $(this).find('a').attr('href');
                    } else {
                        // Link to: Attachment
                        return $(this).find('img').attr('src');
                    }
                } else {
                    // Link to: None
                    return $(this).find('img').attr('src');
                }
            },
            onComplete: function () {
                $('.cboxPhoto')
                    .attr('alt', $(this).find('img').attr('alt'))
                    .attr('title', $(this).find('img').attr('title'));
            }
        });
        galGroup++;
    });
});

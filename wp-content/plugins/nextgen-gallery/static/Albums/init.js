jQuery(function($) {

    // The ngg-album-desc element is only used for compact albums
    const height = $('.ngg-album-desc').height();
    if (height) {
        shave('.ngg-album-desc', height);
    }

    $('.ngg-albumoverview').each(function(){
        $(this).css('opacity', 1.0);
    });
});
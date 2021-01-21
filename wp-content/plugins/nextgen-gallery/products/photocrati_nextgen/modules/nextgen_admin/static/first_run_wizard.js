jQuery('#ngg-video-wizard-invoker').on('click', (e) => {
    e.preventDefault();
    jQuery('#ngg-wizard-video').modal();
    let frame = jQuery('#ngg-wizard-video iframe')
    let videoSrc = frame.attr('src');
    let playVideoSrc = `${videoSrc}?autoplay=1`
    frame.attr('src', playVideoSrc)

    jQuery('#ngg-wizard-video').on(jQuery.modal.CLOSE, function(){
        frame.attr('src', videoSrc);
    });
    return false;
});

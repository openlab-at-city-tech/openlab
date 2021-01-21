(function($) {
    $(function() {
        $.each(window.galleries, function (index, gallery) {
            if (gallery.display_type === 'photocrati-nextgen_basic_slideshow') {
                var settings = gallery.display_settings;
                var fadeValue = (settings.transition_style == "fade") ? true : false;
                $('.ngg-galleryoverview.ngg-slideshow[data-gallery-id="' + gallery.ID + '"]').slick({
                    autoplay: Number(settings.autoplay) ? true : false,
                    arrows: Number(settings.arrows) ? true : false,
                    draggable: false,
                    dots: false,
                    fade: fadeValue,
                    autoplaySpeed: settings.interval,
                    speed: settings.transition_speed,
                    pauseOnHover: Number(settings.pauseonhover) ? true : false
                });
            }
        });
    });
})(jQuery);
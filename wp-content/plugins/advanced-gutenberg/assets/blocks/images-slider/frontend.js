jQuery(document).ready(function ( $ ) {
    $("a.advgb-image-slider-overlay").attr("aria-label","image link");
    $(".advgb-images-slider-block .advgb-images-slider:not(.slick-initialized)").slick({
        dots: true,
        adaptiveHeight: true,
    });
});

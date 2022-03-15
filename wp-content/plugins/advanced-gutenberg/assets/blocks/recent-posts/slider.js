jQuery(document).ready(function($){
    $(".advgb-recent-posts-block.slider-view").find(".advgb-recent-posts:not(.slick-initialized)").each(function() {
        $(this).slick({
            dots: true,
            adaptiveHeight: true,
        });
        $(this).slick("slickSetOption", "autoplay", $(this).parent().hasClass("slider-autoplay"), true);
    });
});

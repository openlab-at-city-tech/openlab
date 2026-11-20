jQuery(document).ready(function($){
    var a11y = window.advgbA11y || null;
    var l10n = (a11y && a11y.l10n) || {};

    $(".advgb-recent-posts-block.slider-view").find(".advgb-recent-posts:not(.slick-initialized)").each(function() {
        $(this).slick({
            dots: true,
            adaptiveHeight: true,
        });
        $(this).slick("slickSetOption", "autoplay", $(this).parent().hasClass("slider-autoplay"), true);

        if (a11y) {
            a11y.slider($(this), {
                label: l10n.recentPosts || 'Recent posts'
            });
        }
    });
});

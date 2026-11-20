jQuery(document).ready(function ($) {
    var a11y = window.advgbA11y || null;
    var l10n = (a11y && a11y.l10n) || {};

    $(".advgb-woo-products.slider-view").on("init", function(event){
      $(this).find("div.woocommerce, ul.products").removeClass("columns-1 columns-2 columns-3 columns-4");
    });
    var $sliders = $(".advgb-woo-products.slider-view .products:not(.slick-initialized)");
    $sliders.slick({
        dots: true,
        adaptiveHeight: true,
    });

    if (a11y) {
        $sliders.each(function () {
            a11y.slider($(this), {
                label: l10n.products || 'Products'
            });
        });
    }
});

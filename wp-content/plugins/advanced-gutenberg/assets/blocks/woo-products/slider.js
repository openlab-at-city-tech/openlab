jQuery(document).ready(function ($) {
    $(".advgb-woo-products.slider-view").on("init", function(event){
      $(this).find("div.woocommerce, ul.products").removeClass("columns-1 columns-2 columns-3 columns-4");
    });
    $(".advgb-woo-products.slider-view .products:not(.slick-initialized)").slick({
        dots: true,
        adaptiveHeight: true,
    })
});

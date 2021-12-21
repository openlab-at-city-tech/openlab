jQuery(document).ready(function ($) {
    document.addEventListener("DOMSubtreeModified", function(){

        setTimeout(function(){
            // Advanced Image
            $(".widget_block .advgb-images-slider-block .advgb-images-slider:not(.slick-initialized)").slick({
                dots: true,
                adaptiveHeight: true,
            });

            $('.widget_block .advgb-lightbox').colorbox({
                title: function () {
                    return $(this).find('.advgb-image-title').text();
                },
                maxWidth: '90%',
                maxHeight: '85%',
                fixed: true,
                className: 'advgb_lightbox',
                href: function () {
                    return $(this).data('image');
                }
            });

            // Recent Posts
            $(".widget_block .advgb-recent-posts-block.slider-view").find(".advgb-recent-posts:not(.slick-initialized)").each(function() {
                $(this).slick({
                    dots: true,
                    adaptiveHeight: true,
                });
                $(this).slick("slickSetOption", "autoplay", $(this).parent().hasClass("slider-autoplay"), true);
            });

            $(".widget_block .masonry-view .advgb-recent-posts").each(function() {
                $(this).isotope({
                    itemSelector: ".advgb-recent-post",
                    percentPosition: true
                });
            });
            /*$(window).on("load resize", function(){
                $(".widget_block .masonry-view .advgb-recent-posts").isotope();
            });*/

            // Woo Products
            $(".widget_block .advgb-woo-products.slider-view").on("init", function(event){
              $(this).find("div.woocommerce, ul.products").removeClass("columns-1 columns-2 columns-3 columns-4");
            });

            $(".widget_block .advgb-woo-products.slider-view .products:not(.slick-initialized)").slick({
                dots: true,
                adaptiveHeight: true,
            });

            // Images Slider
            var galGroup = 1;
            $('.widget_block .advgb-images-slider-lightbox').each(function () {
                $(this).find('.advgb-image-slider-item').colorbox({
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
                    }
                });
                galGroup++;
            });

            // Testimonial
            $(".widget_block .advgb-testimonial.slider-view:not(.slick-initialized)").each(function () {
                var wrapper = $(this).closest('.advgb-testimonial-wrapper');
                if(wrapper.length === 0) {
                    jQuery(`.advgb-testimonial.slider-view:not(.slick-initialized)`).slick({
                        infinite: true,
                        centerMode: true,
                        centerPadding: '40px',
                        slidesToShow: 3,
                    });
                } else {
                    var col = parseInt( wrapper.data( 'col' ) ),
                        scrollItems = parseInt( wrapper.data( 'scroll' ) ),
                        centerMode = wrapper.data( 'center' ),
                        pauseOnHover = wrapper.data( 'pause' ),
                        autoPlay = wrapper.data( 'autoplay' ),
                        autoPlaySpeed = parseInt( wrapper.data( 'apspeed' ) ),
                        loop = wrapper.data( 'loop' ),
                        speed = parseInt( wrapper.data( 'speed' ) ),
                        dotsShown = wrapper.data( 'dots' ),
                        arrowsShown = wrapper.data( 'arrows' );
                    if (!arrowsShown) {
                        wrapper.find( '.advgb-slider-arrow' ).hide();
                    }

                    $( this ).slick( {
                        infinite: loop,
                        slidesToShow: col,
                        slidesToScroll: Math.min( scrollItems, col ),
                        centerMode: centerMode,
                        pauseOnHover: pauseOnHover,
                        autoplay: autoPlay,
                        autoplaySpeed: autoPlaySpeed,
                        dots: dotsShown,
                        arrows: arrowsShown,
                        speed: speed,
                        prevArrow: wrapper.find( '.advgb-slider-prev' ),
                        nextArrow: wrapper.find( '.advgb-slider-next' )
                    } )
                }
            });
        }, 1000);
    });
});

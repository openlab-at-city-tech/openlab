jQuery(document).ready(function ($) {
    $(".advgb-testimonial.slider-view").each(function () {
        var wrapper = $(this).closest('.advgb-testimonial-wrapper');
        if(wrapper.length === 0) {
            jQuery(`.advgb-testimonial.slider-view`).slick({
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
    })
});
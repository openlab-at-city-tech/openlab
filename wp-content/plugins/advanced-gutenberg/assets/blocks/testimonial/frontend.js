jQuery(document).ready(function ($) {
    var a11y = window.advgbA11y || null;
    var l10n = (a11y && a11y.l10n) || {};

    function enhance($slider) {
        if (a11y) {
            a11y.slider($slider, {
                label: l10n.testimonials || 'Testimonials'
            });
        }
    }

    $(".advgb-testimonial.slider-view:not(.slick-initialized)").each(function () {
        var wrapper = $(this).closest('.advgb-testimonial-wrapper');
        if(wrapper.length === 0) {
            var $legacy = jQuery(`.advgb-testimonial.slider-view:not(.slick-initialized)`);
            $legacy.slick({
                infinite: true,
                centerMode: true,
                centerPadding: '40px',
                slidesToShow: 3,
            });
            $legacy.each(function () {
                enhance($(this));
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

            // Name the custom arrow buttons before slick picks them up.
            wrapper.find( '.advgb-slider-prev' ).attr( {
                type: 'button',
                'aria-label': l10n.previousTestimonials || 'Previous testimonials'
            } );
            wrapper.find( '.advgb-slider-next' ).attr( {
                type: 'button',
                'aria-label': l10n.nextTestimonials || 'Next testimonials'
            } );
            wrapper.find( '.advgb-slider-arrow svg' ).attr( {
                'aria-hidden': 'true',
                focusable: 'false'
            } );

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
            } );

            enhance( $( this ) );
        }
    })
});

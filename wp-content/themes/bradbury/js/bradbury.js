jQuery(document).ready(function($) { 
	
    $(function () {

        $('.sf-menu').superfish({
            'speed': 'fast',
            'delay' : 0,
            'animation': {
                'height': 'show'
            }
        });

    });

    $(".site-toggle-anchor").click(function(){
        $("#site-mobile-menu").toggleClass("is-visible");
        $(".site-toggle-label").toggleClass("is-visible");
        $(".site-toggle-icon").toggleClass("is-visible");

    });

    $(".sub-menu-toggle").click(function(){
        $(this).next().toggleClass("is-visible");
        $(this).toggleClass("is-visible");
    });

	jQuery(".site-flexslider").flexslider({
	        selector: ".site-slideshow-list > .site-slideshow-item",
		animation: "slide",
		animationLoop: true,
	        initDelay: 500,
		smoothHeight: false,
		slideshow: false,
		slideshowSpeed: 5000,
		pauseOnAction: true,
		pauseOnHover: false,
	        controlNav: false,
		directionNav: true,
		useCSS: true,
		touch: false,
	        animationSpeed: 600,
	    allowOneSlide: false,
		rtl: false,
		reverse: false,
		start: function(slider) { slider.addClass('loaded'); }
	});

});
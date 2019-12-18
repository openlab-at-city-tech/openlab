jQuery( document ).ready( function( $ ) {
	
	
	// Add class to elements when they're in view
	function isScrolledIntoView(elem) {
		var docViewTop = $(window).scrollTop(),
			docViewBottom = docViewTop + $(window).height();

		var elemTop = $( elem ).offset().top,
			elemBuffer = $( window ).width > 600 ? 200 : 50,
			elemBottom = elemTop + elemBuffer;

		return ( elemBottom <= docViewBottom );
	}
	
	function fadeInSpotted() {
		$( '.tracker' ).each( function () {
			$( this ).addClass( 'will-spot' );
			if ( $( this ).offset().top < $( window ).height() ) {
				$( this ).addClass( 'spotted' );
			}
		} );
	}
	
	if ( $( '.tracker' ).length ) {
		$( window ).on( 'load', function () {
			fadeInSpotted();
		} );
		$( window ).scroll( function () {
			$( '.tracker' ).each( function () {
				if ( isScrolledIntoView( this ) === true ) {
					$( this ).addClass( 'spotted' );
				}
			} );
		} );
	}
	
	
	//Masonry blocks
	$container = $( '.posts' );
	
	$container.css({ 'opacity': 0 });
	
	function masonryInit(){
		$container.masonry({
			itemSelector: '.post-preview',
			percentPosition: true,
			transitionDuration: 0,
		} );
	}

	$container.imagesLoaded().done( function(){
		masonryInit();
		fadeInSpotted();
		$container.animate({ opacity: 1.0 }, 500 );
	});
	
	$( document ).ready( function() { 
		setTimeout( function() { masonryInit(); }, 500 ); 
	} );

	$( window ).resize( function () {
		masonryInit();
	} );
	
	
	// Parallax effect on the fade blocks
	var scroll = window.requestAnimationFrame ||
				 window.webkitRequestAnimationFrame ||
				 window.mozRequestAnimationFrame ||
				 window.msRequestAnimationFrame ||
				 window.oRequestAnimationFrame ||
				 // IE Fallback, you can even fallback to onscroll
				 function(callback){ window.setTimeout(callback, 1000/60) };
				 
	function loop(){
	
		var windowOffset = window.pageYOffset;
		
		if ( windowOffset < $( window ).outerHeight() ) {
            $( '.fade-block' ).css({ 
				'transform': 'translateY( ' + Math.ceil ( windowOffset * 0.25 ) + 'px)',
				'opacity': 1 - ( windowOffset * 0.002 )
			});
        }
	
		scroll( loop )
	
	}
	loop();


	// Toggle navigation
	$( '.nav-toggle' ).on( 'click', function(){	
		$( this ).add( '.site-nav' ).toggleClass( 'active' );
		$( 'body' ).toggleClass( 'lock-screen' );
	} );
	
        			
	// Resize videos after their container
	var vidSelector = ".post iframe, .post object, .post video, .widget-content iframe, .widget-content object, .widget-content iframe";	
	var resizeVideo = function(sSel) {
		$( sSel ).each(function() {
			var $video = $(this),
				$container = $video.parent(),
				iTargetWidth = $container.width();

			if ( !$video.attr("data-origwidth") ) {
				$video.attr("data-origwidth", $video.attr("width"));
				$video.attr("data-origheight", $video.attr("height"));
			}

			var ratio = iTargetWidth / $video.attr("data-origwidth");

			$video.css("width", iTargetWidth + "px");
			$video.css("height", ( $video.attr("data-origheight") * ratio ) + "px");
		});
	};

	resizeVideo( vidSelector );

	$( window ).resize( function() {
		resizeVideo( vidSelector );
	} );
	
	
	// Smooth scroll to anchor links
	$('a[href*="#"]')
	// Remove links that don't actually link to anything
	.not('[href="#"]')
	.not('[href="#0"]')
	.not( '.skip-link' )
	.click(function(event) {
		// On-page links
		if ( location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname ) {
			// Figure out element to scroll to
			var target = $(this.hash);
			target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
			// Does a scroll target exist?
			if (target.length) {
				// Only prevent default if animation is actually gonna happen
				event.preventDefault();
					$('html, body').animate({
					scrollTop: target.offset().top
				}, 1000 );
			}
		}
	})
	
	
	// If the website has a custom logo, adjust the site-nav top
	// padding to make sure it is consistent with image dimensions
	if ( $( 'body').hasClass( 'wp-custom-logo' ) ) {

		var headerHeight = $( '.site-header' ).outerHeight();
		$( '.site-nav' ).css( 'padding-top', headerHeight + 'px' );

		$( window ).resize( function() {
			var headerHeight = $( '.site-header' ).outerHeight();
			$( '.site-nav' ).css( 'padding-top', headerHeight + 'px' );
		} );

	}

	
	// Intercept the Jetpack Load More button when it's reinserted
	$( document ).bind( 'DOMNodeInserted', function( e ) {
		var $target = $( e.target );
		if ( $target.is( '#infinite-handle' ) ) {
			$target.hide();
		}
	} );
	
	
	// Triggers re-layout on Jetpack infinite scroll
	infinite_count = 0;
    $( document.body ).on( 'post-load', function() {

        infinite_count = infinite_count + 1;
		
		// Target the new items and hide them
		var $selector = $( '#infinite-view-' + infinite_count ),
        	$elements = $selector.find( '.post-preview' );
			
		$elements.hide();

		// When images are loaded, show them again
        $elements.imagesLoaded().done( function(){
            $container.append( $elements );
			$elements.show();
			$container.masonry( 'appended', $elements );
			
			// Prepare for fade-in animation on scroll
			$elements.each( function( index ) {
				if ( $( this ).offset().top < ( $( window ).height() + $( window ).scrollTop() ) ) {
					$( this ).addClass( 'jetpack-fade-in' );
				} else {
					$( this ).addClass( 'will-spot' ).removeClass( 'spotted' );
				}
				
			} );
			
			setTimeout( function() { 
				masonryInit();
			}, 500 ); 
			
			// Show the load more button again
			$( '#infinite-handle' ).fadeIn();
			
        });

    });
	
});
( function( $ ) {

	if ( $().isotope ) {
		var $container = $( '#portfolio, #blog' );

		if ( $container.length ) {
			$container.imagesLoaded( function() {
				$container.isotope( {
					itemSelector: '.type-project, .type-post',
					layoutMode: 'masonry'
				} );
			} );

			$( '.filter-area span' ).on( 'click', function( e ) {
				e.preventDefault();
				$container.isotope( {
					filter: $( this ).attr( 'data-filter' )
				} );
				$( '.filter-area span' ).removeClass( 'active' );
				$( this ).addClass( 'active' );
			} );

			$container.infinitescroll( {
				navSelector: '#next-projects',
				nextSelector: '#next-projects a',
				itemSelector: '#portfolio .type-project'
			},
			function( newElements ) {
				var $newElems = $( newElements ).css( { opacity: 0 } );
				$newElems.imagesLoaded( function() {
					$newElems.animate( { opacity: 1 } );
					$container.isotope( 'appended', $newElems, true );
				} );
			} );
		}
	}

	$( '[data-fancybox]' ).fancybox( {
		infobar : false,
		transitionEffect : "slide",
		buttons : [
			'close'
		]
	} );

	$( '.hentry' ).fitVids();

	$( '.menu-toggle' ).on( 'click', function() {
		$( this ).toggleClass( 'toggled-on' );
		$( '.top-menu' ).slideToggle();
	} );

	function mobileMenu( menu ) {
		menu.find( '.menu-item-has-children > a' ).after( '<span class="dropdown-toggle"></span>' );
		menu.find( '.current-menu-ancestor > .dropdown-toggle' ).addClass( 'toggle-on' );
		menu.find( '.current-menu-ancestor > .sub-menu' ).addClass( 'toggled-on' );
		menu.find( '.dropdown-toggle' ).click( function() {
			$( this ).toggleClass( 'toggle-on' );
			$( this ).next( '.children, .sub-menu' ).slideToggle( 'toggled-on' );
		} );
	}
	mobileMenu( $( '.top-menu' ) );

} )( jQuery );
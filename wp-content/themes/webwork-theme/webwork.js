(function($){
	var $searchToggle, $sidebar, $wrapper;

	toggleExplore = function() {
		$searchToggle.toggleClass( 'active' );
		if ($(".nav-toggle").hasClass("active")) {
			$(".nav-toggle").removeClass("active");
			$(".mobile-menu").slideToggle();
		}

		$sidebar.toggleClass( 'sidebar-visible' );
		$wrapper.toggleClass( 'inactive' );
	}

	$(document).ready(function() {
		$searchToggle = $( '.search-toggle' );
		$sidebar = $( '.ww-sidebar' );
		$wrapper = $( '.wrapper' );

		// Professional programming practices.
		setTimeout( function() {
			$searchToggle.unbind( 'click' );
			$searchToggle.on( 'click', function() {
				toggleExplore();
			} );
		}, 500 );

		$wrapper.on( 'click', function( e ) {
			if ( $wrapper.hasClass( 'inactive' ) ) {
				e.preventDefault();
				toggleExplore();
			}
		} );

		// Delegated listener for filter changes.
		$( 'body' ).on( 'webworkFilterChange', function() {
			toggleExplore();
		} );
	});
}(jQuery))

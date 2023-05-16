(function($){
	var $searchToggle, $sidebar, $wrapper;

	toggleExplore = function() {
		if ( $searchToggle.is( ':visible' ) ) {
			if ( $searchToggle.hasClass("active") ) {
				$searchToggle.removeClass( 'active' );
				$sidebar.removeClass( 'sidebar-visible' );
				$wrapper.removeClass( 'inactive' );
			} else {
				$searchToggle.addClass( 'active' );
				$sidebar.addClass( 'sidebar-visible' );
				$wrapper.addClass( 'inactive' );
			}
		}
	}

	$(document).ready(function() {
		$searchToggle = $( '.search-toggle' );

		$sidebar = $( '.ww-sidebar' );
		if ( ! $sidebar.length ) {
			$sidebar= $( '.sidebar' );
		}

		$wrapper = $( '.wrapper' );

		// Professional programming practices.
		setTimeout( function() {
			$searchToggle.off( 'click' );
			$searchToggle.on( 'click', function() {
				toggleExplore();
			} );
		}, 500 );

		$wrapper.on( 'click', function( e ) {
			if ( 'A' !== e.target.tagName && $wrapper.hasClass( 'inactive' ) ) {
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

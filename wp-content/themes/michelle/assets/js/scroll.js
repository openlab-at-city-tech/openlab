/**
 * Scrolling body class.
 *
 * This is actually being enqueued inline in `WebManDesign\Michelle\Assets\Scripts::enqueue_inline_scroll()`.
 * Keeping this file (and its minified version) for reference.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.12
 * @version  1.3.0
 */

( function() {
	'use strict';

	let
		lastScrollTop = window.scrollY,
		ticking       = false;

	function michelleScroll() {
		const scrolledY = window.scrollY;

		if ( scrolledY < lastScrollTop ) {
			document.body.classList.add( 'has-scrolled-up' );
		} else {
			document.body.classList.remove( 'has-scrolled-up' );
		}

		if ( scrolledY > 1 ) {
			document.body.classList.add( 'has-scrolled' );
		} else {
			document.body.classList.remove( 'has-scrolled' );
			document.body.classList.remove( 'has-scrolled-up' );
		}

		lastScrollTop = scrolledY;
	}

	michelleScroll();

	window.addEventListener( 'scroll', function( e ) {
		if ( ! ticking ) {
			window.requestAnimationFrame( function() {
				michelleScroll();
				ticking = false;
			} );
			ticking = true;
		}
	} );
} )();

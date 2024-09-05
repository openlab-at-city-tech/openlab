/**
 * JavaScript code for the scroll buttons of the "Scroll" mode of the Responsive Tables module.
 *
 * @package TablePress
 * @subpackage Responsive Tables
 * @author Tobias Bäthge
 * @since 2.2.0
 */

/* jshint strict: global */
'use strict';

// Add event listeners to scroll buttons.
document.querySelectorAll( '.tablepress-scroll-button-left' ).forEach( ( button ) => {
	button.addEventListener( 'click', function() {
		this.nextElementSibling.scrollBy( 'rtl' === document.dir ? 200 : -200, 0 );
	} );
} );
document.querySelectorAll( '.tablepress-scroll-button-right' ).forEach( ( button ) => {
	button.addEventListener( 'click', function() {
		this.previousElementSibling.scrollBy( 'rtl' === document.dir ? -200 : 200, 0 );
	} );
} );

// Update visibility of scroll buttons.
const updateScrollButtons = () => {
	document.querySelectorAll( '.tablepress-scroll-buttons-wrapper' ).forEach( ( wrapper ) => {
		const wrapperClassList = wrapper.classList;
		const scrollWrapper = wrapper.children[1];

		if ( wrapperClassList.contains( 'tablepress-scroll-buttons-wrapper-visible' ) ) {
			if ( scrollWrapper.scrollWidth <= scrollWrapper.offsetWidth + 60 ) {
				wrapperClassList.remove( 'tablepress-scroll-buttons-wrapper-visible' );
			}
		} else {
			// eslint-disable-next-line no-lonely-if
			if ( scrollWrapper.scrollWidth > scrollWrapper.offsetWidth ) {
				wrapperClassList.add( 'tablepress-scroll-buttons-wrapper-visible' );
			}
		}
	} );
};

// Check if scroll buttons should be visible on window resize and debounce the event handler.
function debounce( func, wait ) {
	let timeout;
	return function ( ...args ) {
		const context = this;
		clearTimeout( timeout );
		timeout = setTimeout( () => {
			func.apply( context, args );
		}, wait );
	};
}
window.addEventListener( 'resize', debounce( updateScrollButtons, 50 ) );

// Initial check if scroll buttons should be visible.
updateScrollButtons();

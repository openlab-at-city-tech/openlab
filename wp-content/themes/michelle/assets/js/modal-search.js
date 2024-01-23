/**
 * Search form modal toggling.
 *
 * This is actually being enqueued inline in `WebManDesign\Michelle\Header\Component::get_search_form()`.
 * Keeping this file (and its minified version) for reference.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.13
 * @version  1.3.7
 */

( function() {
	'use strict';

	const
		container = document.getElementById( 'search-form-modal' ),
		modal     = document.getElementById( 'modal-search' );

	if ( ! modal ) {
		container.style.display = 'none';
		return;
	}

	const
		button      = document.getElementById( 'modal-search-toggle' ),
		searchField = container.querySelector( '[type=search]' );

	function michelleToggleSearch() {
		container.classList.toggle( 'toggled' );
		document.documentElement.classList.toggle( 'lock-scroll' );

		if ( -1 !== container.className.indexOf( 'toggled' ) ) {
			button.setAttribute( 'aria-expanded', 'true' );

			if ( searchField ) {
				searchField.focus();
			}
		} else {
			button.setAttribute( 'aria-expanded', 'false' );
		}
	}

	button.onclick = function() {
		michelleToggleSearch();
	};

	/**
	 * Trap focus inside search modal.
	 * Code adapted from Twenty Twenty-One theme.
	 */
	document.addEventListener( 'keydown', function( event ) {
		if ( ! container.classList.contains( 'toggled' ) ) {
			return;
		}

		const
			selectors = 'a, button, input:not([type=hidden]), select',
			elements  = container.querySelectorAll( selectors ),
			firstEl   = elements[0],
			lastEl    = elements[ elements.length - 1 ],
			activeEl  = document.activeElement,
			tabKey    = ( 9 === event.keyCode ),
			escKey    = ( 27 === event.keyCode ),
			shiftKey  = event.shiftKey;

		if ( escKey ) {
			event.preventDefault();
			michelleToggleSearch();
			button.focus();
		}

		if (
			! shiftKey
			&& tabKey
			&& lastEl === activeEl
		) {
			event.preventDefault();
			firstEl.focus();
		}

		if (
			shiftKey
			&& tabKey
			&& firstEl === activeEl
		) {
			event.preventDefault();
			lastEl.focus();
		}

		if (
			tabKey
			&& firstEl === lastEl
		) {
			event.preventDefault();
		}
	} );
} )();

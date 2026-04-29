/**
 * Glossary Tooltips - shows term definitions on hover/focus/tap
 */
( function() {
	'use strict';

	var tooltip = null;
	var hideTimeout = null;
	var activeElement = null;

	function createTooltip() {
		if ( tooltip ) {
			return tooltip;
		}
		tooltip = document.createElement( 'div' );
		tooltip.className = 'epkb-glossary-tooltip';
		tooltip.innerHTML = '<div class="epkb-glossary-tooltip__term"></div><div class="epkb-glossary-tooltip__definition"></div>';
		document.body.appendChild( tooltip );

		// Keep tooltip visible when hovering over it
		tooltip.addEventListener( 'mouseenter', function() {
			clearHideTimeout();
		} );
		tooltip.addEventListener( 'mouseleave', function() {
			hideTooltip();
		} );

		return tooltip;
	}

	function showTooltip( el ) {
		clearHideTimeout();
		activeElement = el;

		var tip = createTooltip();
		var term = el.getAttribute( 'data-glossary-term' ) || '';
		var definition = el.getAttribute( 'data-glossary-definition' ) || '';

		tip.querySelector( '.epkb-glossary-tooltip__term' ).textContent = term;
		tip.querySelector( '.epkb-glossary-tooltip__definition' ).textContent = definition;

		// Remove position classes before measuring
		tip.classList.remove( 'epkb-glossary-tooltip--above', 'epkb-glossary-tooltip--below', 'epkb-glossary-tooltip--visible' );

		// Make visible for measurement
		tip.style.left = '0px';
		tip.style.top = '0px';
		tip.style.display = 'block';

		var rect = el.getBoundingClientRect();
		var tipRect = tip.getBoundingClientRect();
		var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
		var scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

		// Determine position: above or below
		var spaceAbove = rect.top;
		var posClass;
		var top;

		if ( spaceAbove >= tipRect.height + 8 ) {
			// Position above
			top = rect.top + scrollTop - tipRect.height - 8;
			posClass = 'epkb-glossary-tooltip--above';
		} else {
			// Position below
			top = rect.bottom + scrollTop + 8;
			posClass = 'epkb-glossary-tooltip--below';
		}

		var left = rect.left + scrollLeft;

		// Keep within viewport horizontally
		var maxLeft = document.documentElement.clientWidth - tipRect.width - 8;
		if ( left > maxLeft ) {
			left = maxLeft;
		}
		if ( left < 8 ) {
			left = 8;
		}

		tip.style.top = top + 'px';
		tip.style.left = left + 'px';
		tip.classList.add( posClass );
		tip.classList.add( 'epkb-glossary-tooltip--visible' );
	}

	function hideTooltip() {
		if ( tooltip ) {
			tooltip.classList.remove( 'epkb-glossary-tooltip--visible' );
		}
		activeElement = null;
	}

	function hideTooltipWithDelay() {
		hideTimeout = setTimeout( hideTooltip, 150 );
	}

	function clearHideTimeout() {
		if ( hideTimeout ) {
			clearTimeout( hideTimeout );
			hideTimeout = null;
		}
	}

	function init() {
		var terms = document.querySelectorAll( '.epkb-glossary-term' );
		if ( ! terms.length ) {
			return;
		}

		var isTouchDevice = ( 'ontouchstart' in window ) || navigator.maxTouchPoints > 0;

		terms.forEach( function( el ) {

			// Desktop: hover
			el.addEventListener( 'mouseenter', function() {
				showTooltip( el );
			} );
			el.addEventListener( 'mouseleave', function() {
				hideTooltipWithDelay();
			} );

			// Keyboard: focus/blur
			el.addEventListener( 'focus', function() {
				showTooltip( el );
			} );
			el.addEventListener( 'blur', function() {
				hideTooltipWithDelay();
			} );

			// Mobile: tap toggle
			if ( isTouchDevice ) {
				el.addEventListener( 'click', function( e ) {
					e.preventDefault();
					if ( activeElement === el ) {
						hideTooltip();
					} else {
						showTooltip( el );
					}
				} );
			}
		} );

		// Close on Escape key
		document.addEventListener( 'keydown', function( e ) {
			if ( e.key === 'Escape' ) {
				hideTooltip();
			}
		} );

		// Close on tap elsewhere (mobile)
		if ( isTouchDevice ) {
			document.addEventListener( 'touchstart', function( e ) {
				if ( ! tooltip ) {
					return;
				}
				if ( ! e.target.closest( '.epkb-glossary-term' ) && ! e.target.closest( '.epkb-glossary-tooltip' ) ) {
					hideTooltip();
				}
			} );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();

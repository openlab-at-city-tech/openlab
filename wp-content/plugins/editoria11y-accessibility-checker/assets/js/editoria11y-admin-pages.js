/**
 * Accessibility shim for Freemius SDK admin chrome.
 *
 * Companion to FreemiusAccessibilityShim.php. Normalizes the
 * `.fs-close` dismiss control across all Freemius render paths
 * (sticky admin notices and modal forms) so it is keyboard-operable
 * and announced correctly by assistive technologies.
 *
 * Per-element transforms:
 *   - Sets role="button" so the element is announced as a button
 *     regardless of underlying tag (<div>, <a>, <span>).
 *   - Sets tabindex="0" so keyboard users can reach it. Replaces any
 *     positive tabindex (e.g. tabindex="3" in resend-key.php) which
 *     is a WCAG 2.4.3 anti-pattern that distorts tab order.
 *   - Adds aria-label sourced from the visible <span> child, the
 *     element's title attribute, or "Dismiss" as a final fallback.
 *   - Marks the decorative <i class="dashicons"> child aria-hidden
 *     so AT doesn't announce "Dashicon dashicons-no" before the
 *     accessible name.
 *   - Binds keydown so Enter and Space activate the existing click
 *     handler that Freemius binds via jQuery delegation.
 *
 * We intentionally don't replace the <div>/<a> with a real <button>:
 * Freemius's click delegation listens on `.fs-close` regardless of
 * tag, and a tag swap risks breaking handlers we don't control. The
 * transforms above achieve WCAG conformance without that risk.
 *
 * MutationObserver scope is document.body because modal forms
 * (license activation, deactivation feedback, opt-in) build their
 * markup as strings and inject them on user action — those nodes
 * don't exist at DOMContentLoaded and a one-shot sweep would miss
 * them. The observer is idempotent; nodes are flagged with a
 * data attribute after first pass so re-renders don't re-bind.
 */
(function () {
	'use strict';

	var SELECTOR = '.fs-close';
	var FLAG     = 'data-ed11yA11yShimmed';

	function accessibleName( el ) {
		var span = el.querySelector( 'span' );
		if ( span && span.textContent.trim() ) {
			return span.textContent.trim();
		}
		var titled = el.getAttribute( 'title' ) || el.querySelector( '[title]' );
		if ( typeof titled === 'string' && titled ) {
			return titled;
		}
		if ( titled && titled.getAttribute ) {
			var t = titled.getAttribute( 'title' );
			if ( t ) {
				return t;
			}
		}
		return 'Dismiss';
	}

	function normalize( el ) {
		if ( el.getAttribute( FLAG ) ) {
			return;
		}
		el.setAttribute( FLAG, '1' );

		// Real <button>s already convey button semantics; skip role
		// assignment but still ensure focus + activation behave.
		if ( 'BUTTON' !== el.tagName ) {
			el.setAttribute( 'role', 'button' );
		}

		// Override any positive tabindex (anti-pattern). Allow -1 to
		// stand if the SDK ever sets it intentionally; coerce missing
		// or positive values to 0.
		var ti = el.getAttribute( 'tabindex' );
		var tn = ti === null ? null : parseInt( ti, 10 );
		if ( null === tn || tn > 0 ) {
			el.setAttribute( 'tabindex', '0' );
		}

		if ( ! el.getAttribute( 'aria-label' ) ) {
			el.setAttribute( 'aria-label', accessibleName( el ) );
		}

		// Hide decorative dashicons from the accessibility tree so
		// the accessible name is the sole announcement.
		var icons = el.querySelectorAll( 'i.dashicons, .dashicons' );
		for ( var i = 0; i < icons.length; i++ ) {
			icons[ i ].setAttribute( 'aria-hidden', 'true' );
		}

		// Hide the visible <span> from AT once we've copied its text
		// into aria-label, so AT doesn't announce the name twice.
		// Sighted users still see it.
		var labelSpan = el.querySelector( 'span' );
		if ( labelSpan && ! labelSpan.hasAttribute( 'aria-hidden' ) ) {
			labelSpan.setAttribute( 'aria-hidden', 'true' );
		}

		// Keyboard activation. Freemius's existing click delegation
		// fires off the synthetic click; we don't reimplement the
		// dismissal logic here.
		el.addEventListener( 'keydown', function ( ev ) {
			if ( 'Enter' === ev.key || ' ' === ev.key || 'Spacebar' === ev.key ) {
				ev.preventDefault();
				el.click();
			}
		} );
	}

	function sweep( root ) {
		if ( ! root ) {
			return;
		}
		if ( root.matches && root.matches( SELECTOR ) ) {
			normalize( root );
		}
		if ( root.querySelectorAll ) {
			var found = root.querySelectorAll( SELECTOR );
			for ( var i = 0; i < found.length; i++ ) {
				normalize( found[ i ] );
			}
		}
	}

	function init() {
		sweep( document.body );

		var observer = new MutationObserver( function ( mutations ) {
			for ( var i = 0; i < mutations.length; i++ ) {
				var added = mutations[ i ].addedNodes;
				for ( var j = 0; j < added.length; j++ ) {
					if ( added[ j ].nodeType === Node.ELEMENT_NODE ) {
						sweep( added[ j ] );
					}
				}
			}
		} );
		observer.observe( document.body, { childList: true, subtree: true } );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
})();

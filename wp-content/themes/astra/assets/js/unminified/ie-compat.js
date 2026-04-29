/**
 * IE Compatibility Polyfills
 *
 * Loaded only when the `astra_enable_ie_compatibility` filter returns true.
 * Contains polyfills removed from the main theme JS for IE9-11 support.
 *
 * @since 4.12.6
 */

// Element.matches() polyfill for IE9+.
if ( ! Element.prototype.matches ) {
	Element.prototype.matches =
		Element.prototype.matchesSelector ||
		Element.prototype.mozMatchesSelector ||
		Element.prototype.msMatchesSelector ||
		Element.prototype.oMatchesSelector ||
		Element.prototype.webkitMatchesSelector ||
		function( s ) {
			var matches = ( this.document || this.ownerDocument ).querySelectorAll( s ),
				i = matches.length;
			while ( --i >= 0 && matches.item( i ) !== this ) {}
			return i > -1;
		};
}

// CustomEvent() constructor for IE9-11.
( function() {
	if ( typeof window.CustomEvent === 'function' ) return false;
	function CustomEvent( event, params ) {
		params = params || { bubbles: false, cancelable: false, detail: undefined };
		var evt = document.createEvent( 'CustomEvent' );
		evt.initCustomEvent( event, params.bubbles, params.cancelable, params.detail );
		return evt;
	}
	CustomEvent.prototype = window.Event.prototype;
	window.CustomEvent = CustomEvent;
} )();

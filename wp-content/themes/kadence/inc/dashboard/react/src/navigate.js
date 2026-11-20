/**
 * Loads a URL in the current tab.
 *
 * @param {string} url URL to load.
 *
 * @since 1.6.0
 */
export default function navigate( url ) {
	window.location.assign( url );
}

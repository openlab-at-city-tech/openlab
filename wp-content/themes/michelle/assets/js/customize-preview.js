/**
 * Customize preview scripts.
 *
 * No need for dynamic change of 'blogname' and 'blogdescription' as these are being
 * treated as partial refresh (so there is an edit icon displayed in customizer).
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.11
 */

/**
 * Theme mods.
 *
 * Customizer scripts use jQuery.
 * @requires  jQuery
 */
( function( $ ) {
	'use strict';

	wp.customize( 'background_color', function( value ) {
		value
			.bind( function( to ) {
				$( '#michelle-inline-css' )
					.append( ':root{ --background_color:' + to + '; }' );
			} );
	} );

} )( jQuery );

/**
 * Customizer live preview helper functions.
 *
 * @see  WebManDesign\Michelle\Customize\RGBA::customize_preview()
 */
( function( window ) {
	'use strict';

	window.michelle = window.michelle || {};

	/**
	 * Theme customizer preview helper functions.
	 *
	 * @since  1.0.0
	 */
	window.michelle.Customize = {

		/**
		 * Convert hex color into RGB array.
		 *
		 * @since    1.0.0
		 * @version  1.3.11
		 */
		hexToRgbArray : function( $hex = '' ) {
			const $rgb = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec( $hex );

			return ( $rgb ) ? ( [
				parseInt( $rgb[1], 16 ),
				parseInt( $rgb[2], 16 ),
				parseInt( $rgb[3], 16 )
			] ) : ( [] );
		},

		/**
		 * Convert hex color into RGB string.
		 *
		 * RGB values are separated with comma.
		 *
		 * @since  1.0.0
		 */
		hexToRgb : function( $hex = '' ) {
			return this.hexToRgbArray( $hex ).join();
		}

	}
} )( window );

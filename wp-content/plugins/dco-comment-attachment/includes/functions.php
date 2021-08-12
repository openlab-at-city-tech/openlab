<?php
/**
 * Helpers functions
 *
 * @package DCO_Comment_Attachment
 * @author Denis Yanchevskiy
 * @copyright 2019
 * @license GPLv2+
 *
 * @since 1.1.1
 */

if ( ! function_exists( 'dco_ca' ) ) {
	/**
	 * Gets the current instance of the specified class.
	 *
	 * @since 1.1.1
	 *
	 * @param string $suffix The suffix of the class name.
	 * @return object|bool The instance of the specified class or false if it is not exists.
	 */
	function dco_ca( $suffix = '' ) {
		$name = 'dco_ca' . $suffix;

		return isset( $GLOBALS[ $name ] ) ? $GLOBALS[ $name ] : false;
	}
}

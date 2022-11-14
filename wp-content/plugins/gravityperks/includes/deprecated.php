<?php

if ( ! function_exists( 'print_rr' ) ) {
	/**
	 * @deprecated 2.0
	 */
	function print_rr( $array ) {
		echo '<pre>';
		print_r( $array );
		echo '</pre>';
	}
}

if ( ! function_exists( 'gwget' ) ) {
	/**
	 * @deprecated 2.0
	 */
	function gwget( $name ) {
		return gwar( $_GET, $name );
	}
}

if ( ! function_exists( 'gwpost' ) ) {
	/**
	 * @deprecated 2.0
	 */
	function gwpost( $name ) {
		return gwar( $_POST, $name );
	}
}

if ( ! function_exists( 'gwar' ) ) {
	/**
	 * @deprecated 2.0
	 */
	function gwar( $array, $name ) {
		return isset( $array[ $name ] ) ? $array[ $name ] : '';
	}
}

if ( ! function_exists( 'gwars' ) ) {
	/**
	 * @deprecated 2.0
	 */
	function gwars( $array, $name ) {
		$names = explode( '/', $name );
		$val   = $array;
		foreach ( $names as $current_name ) {
			$val = gwar( $val, $current_name );
		}
		return $val;
	}
}

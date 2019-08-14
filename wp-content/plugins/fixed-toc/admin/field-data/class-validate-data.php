<?php
/**
 * Validate data when saving.
 *
 * @since 3.0.0
 */
class Fixedtoc_Validate_Data {
	/*
	 * Int value.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param mixed $val.
	 * @return int
	 */
	public static function intval_base10( $val ) {
		return intval( $val, 10 );
	}
	
	/*
	 * Float value.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param mixed $val.
	 * @return int
	 */
	public static function floatval( $val ) {
		return floatval( $val );
	}
	
	/*
	 * Strip tags.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param mixed $val.
	 * @return int
	 */
	public static function strip_tags( $val ) {
		return strip_tags( $val );
	}
	
}
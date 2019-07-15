<?php
/**
 * Generate an page datum
 *
 * @since 3.1.0
 *
 * @see Fixedtoc_Datum
 */
class Fixedtoc_Datum_page extends Fixedtoc_Datum {

	/**
	 *
	 * @since 3.1.0
	 */
	public function set_name() {
		$this->name = 'page';
	}

	/**
	 *
	 * @since 3.1.0
	 */
	public function set_value( Fixedtoc_Data $obj_data ) {
		if ( preg_match( '/data\-page(\s*)=(\s*)(\"|\')(.+?)(\"|\')/i', $obj_data->get_match(), $match ) ) {
			$this->value = trim( $match[4] );
		}
	}

}
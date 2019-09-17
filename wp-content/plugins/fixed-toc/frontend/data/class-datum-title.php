<?php
/**
 * Generate an title datum
 *
 * @since 3.0.0
 *
 * @see Fixedtoc_Datum
 */

class Fixedtoc_Datum_Title extends Fixedtoc_Datum {
	/**
	 * @since 3.0.0
	 */
	public function set_name() {
		$this->name = 'title';
	}
	
	/**
	 * @since 3.0.0
	 */
	public function set_value( Fixedtoc_Data $obj_data ) {
		$title_attr = $this->get_title_attr( $obj_data );
		if ( $title_attr ) {
			$this->value = $title_attr;
		} else{
			$datum = $obj_data->get_datum();
			$this->value = $datum['origin_title'];
		}		
	}
	
	/**
	 * Get the title attribute.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @param object $obj_data
	 * @return string
	 */
	private function get_title_attr( $obj_data ) {
		if ( preg_match( '/title(\s*)=(\s*)(\"|\')(.+?)(\"|\')/i', $obj_data->get_match(), $match ) ) {
			return trim( $match[4] );
		}	
	}
}
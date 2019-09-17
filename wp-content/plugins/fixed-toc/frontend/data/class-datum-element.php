<?php
/**
 * Generate a element datum
 *
 * @since 3.0.0
 */

class Fixedtoc_Datum_Element extends Fixedtoc_Datum {
	/**
	 * @since 3.0.0
	 */
	public function set_name() {
		$this->name = 'element';
	}
	
	/**
	 * @since 3.0.0
	 */
	public function set_value( Fixedtoc_Data $obj_data ) {
		$this->value = $obj_data->get_match();
	}	
}
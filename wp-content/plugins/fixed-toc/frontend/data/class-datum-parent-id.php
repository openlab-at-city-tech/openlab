<?php
/**
 * Generate an parent id datum
 *
 * @since 3.0.0
 *
 * @see Fixedtoc_Datum
 */
class Fixedtoc_Datum_Parent_Id extends Fixedtoc_Datum {
	/**
	 * @since 3.0.0
	 */
	public function set_name() {
		$this->name = 'parent_id';
	}
	
	/**
	 * @since 3.0.0
	 */
	public function set_value( Fixedtoc_Data $obj_data ) {
		$data = $obj_data->get_data();
		if ( empty( $data ) ) {
			return;
		}
		
		$reverse_data = array_reverse( $data );
		$current_heading = $this->get_heading( $obj_data->get_match() );
		
		foreach ( $reverse_data as $datum ) {
			if ( $this->get_heading( $datum['element'] ) < $current_heading ) {
				$this->value = $datum['id'];
				return;
			}
		}
		
		$this->value = false;
	}
	
	/**
	 * Get heading
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @param string $element
	 * @return int
	 */
	private function get_heading( $element ) {
		return (int) substr( $element, 2, 1 );
	}
}
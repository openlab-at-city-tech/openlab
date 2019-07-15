<?php
/**
 * Generate an id datum
 *
 * @since 3.0.0
 *
 * @see Fixedtoc_Datum
 */
class Fixedtoc_Datum_Id extends Fixedtoc_Datum {
	/**
	 * @since 3.0.0
	 */
	public function set_name() {
		$this->name = 'id';
	}
	
	/**
	 * @since 3.0.0
	 */
	public function set_value( Fixedtoc_Data $obj_data ) {
		if ( fixedtoc_is_true( 'convert_title_to_id' ) ) {
			// Convert title to id
			$id = ( $this->convert_title_to_id( $obj_data ) );
		} else {
			// Default id that auto increase suffix num
			$id = 'ftoc-heading-' . ( $obj_data->get_index() + 1 );
		}
		
		// Customize id
		$customize_id = $this->get_id_attr( $obj_data );
		
		$id = isset( $customize_id ) && $customize_id ? $customize_id : $id;
		
		// Filter duplicate id
		$this->value = $this->filter_duplicate_id( $id, $obj_data->get_data() );
	}

	/**
	 * Convert title to id
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @param object $obj_data
	 * @return string
	 */
	private function convert_title_to_id( $obj_data ) {
		$datum = $obj_data->get_datum();
		$prefix = fixedtoc_get_val( 'general_id_prefix' );
		$title = $prefix ? $prefix . '-' . $datum['origin_title'] : $datum['origin_title'];
		return sanitize_title( $title );
	}

	/**
	 * Get id attribute
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @param object $obj_data
	 * @return string
	 */
	private function get_id_attr( $obj_data ) {
// 		if ( preg_match( '/id(\s*)=(\s*)(\"|\')(.+?)(\"|\')/i', $obj_data->get_match(), $match ) ) {
		if ( preg_match( '/(<h[^>]*?)(\s)id(\s*)=(\s*)(\"|\')(.+?)(\"|\')((.*?)>)/i', $obj_data->get_match(), $match ) ) {
			return strtolower( sanitize_html_class( trim( $match[6] ) ) );
		}
	}

	/**
	 * Filter duplicate id
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @param string $id
	 * @param array $data
	 * @return string
	 */
	private function filter_duplicate_id( $id, $data ) {
		if ( empty( $data ) || count( $data ) <= 1 ) {
			return $id;
		}
		
		$duplicate_id = '';
		$num = 1;
		foreach ( $data as $datum ) {
			$datum_id = isset( $datum['id'] ) ? $datum['id'] : false;
			if ( empty( $datum_id ) ) {
				continue;
			}
			
			if ( $datum_id == $id ) {
				$duplicate_id = $id;
				continue;
			}
			
			if ( $duplicate_id && 0 === strpos( $datum_id, $duplicate_id ) ) {
				if ( preg_match( '/\d+$/i', $datum_id, $match ) ) {
					$num = $match[0];
				}
			}
		}
		
		return $duplicate_id ? $id . '-' . ++$num : $id;
	}
	
}
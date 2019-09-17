<?php
/**
 * Generate an original title datum
 *
 * @since 3.0.0
 *
 * @see Fixedtoc_Datum
 */

class Fixedtoc_Datum_Origin_Title extends Fixedtoc_Datum {
	private $excludes;
	
	/**
	 * @since 3.0.0
	 */
	public function set_name() {
		$this->name = 'origin_title';
	}
	
	/**
	 * @since 3.0.0
	 */
	public function set_value( Fixedtoc_Data $obj_data ) {
		$title = trim( strip_tags( $obj_data->get_match() ) );
		$this->value = $this->filter_title( $title );
	}
	
	/**
	 * Filter title.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @param object $obj_data
	 * @return string
	 */
	private function filter_title( $title ) {
		if ( empty( $title ) ) {
			return '';
		}
		
		if ( is_null( $this->excludes ) ) {
			$exclude = trim( fixedtoc_get_val( 'general_exclude_keywords' ) );
			if ( empty( $exclude ) ) {
				return $title;
			}

			$excludes = explode( "\n", $exclude );
			$excludes = array_map( array( $this, 'sanitize_keyword' ), $excludes );
			$excludes = array_filter( $excludes );
			$this->excludes = $excludes;		
		}
		
		if ( $this->excludes && in_array( $title, $this->excludes ) ) {
			return '';
		}
		
		return $title;

	}
	
	public function sanitize_keyword( $keyword ) {
		return trim( $keyword );
	}
}
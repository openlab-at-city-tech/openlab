<?php
/**
 * Style data of location
 *
 * @since 3.0.0
 */
class Fixedtoc_Style_Data_Location extends Fixedtoc_Style_Data {
	/**
	 * Create data
	 *
	 * @since 3.0.0
	 * @access protected
	 */
	protected function create_data() {
		$this->offset_y();
	}
	
	/**
	 * Offerset Y
	 *
	 * @since 3.0.0
	 * @access private
	 */
	private function offset_y() {
		$offset_y = (int) fixedtoc_get_val( 'location_vertical_offset' );
		if ( empty( $offset_y ) ) {
			return;
		}
		$position = fixedtoc_get_val( 'location_fixed_position' );
		if ( 'middle-left' == $position || 'middle-right' == $position ) {
			return;
		}
		
		$selectors = array(
			$this->offset_y_selector_model( $position, 'trigger' ),
			$this->offset_y_selector_model( $position, 'contents' )
		);
		
		if ( 'bottom-left' == $position || 'bottom-right' == $position ) {
			$property = 'bottom';
		} else {
			$property = 'top';
		}
		
		$this->add_datum( $selectors, array(
			$property => $offset_y . 'px'
		) );
	}

	/**
	 * Offerset Y selector model
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @param string $position
	 * @param string $target
	 * @return string
	 */
	private function offset_y_selector_model( $position, $target ) {
		return "#ftwp-container.ftwp-fixed-to-post.ftwp-{$position} #ftwp-{$target}";
	}
}
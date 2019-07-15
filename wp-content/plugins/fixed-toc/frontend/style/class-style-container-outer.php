<?php
/**
 * Style data of container outer.
 *
 * @since 3.0.0
 */
class Fixedtoc_Style_Data_Container_Outer extends Fixedtoc_Style_Data {
	/**
	 * Create data
	 *
	 * @since 3.0.0
	 * @access protected
	 */
	protected function create_data() {
		$this->size();
	}
	
	/**
	 * Size
	 *
	 * @since 3.0.0
	 * @access private
	 */
	private function size() {
			// Height
			$height = (int) fixedtoc_get_val( 'contents_height_in_post' );
			$height = $height && ! fixedtoc_is_true( 'contents_collapse_init' ) ? $height . 'px' : 'auto';
			$this->add_datum( '.ftwp-in-post#ftwp-container-outer', array(
				'height' => $height
			) );

			// Width
			if ( fixedtoc_is_true( 'float_in_post' ) ) {
				$width = (int) fixedtoc_get_val( 'contents_width_in_post' );
				$width = $width ? $width . 'px' : 'auto';
				$side = fixedtoc_get_val( 'contents_float_in_post' );
				$this->add_datum( ".ftwp-in-post#ftwp-container-outer.ftwp-float-$side", array(
					'width' => $width
				) );
			}		
	}
}
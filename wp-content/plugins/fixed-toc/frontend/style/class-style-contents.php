<?php
/**
 * Style data of Contents
 *
 * @since 3.0.0
 */
class Fixedtoc_Style_Data_Contents extends Fixedtoc_Style_Data {
	/**
	 * Create data
	 *
	 * @since 3.0.0
	 * @access protected
	 */
	protected function create_data() {
		$this->fixed_to_post();
		
		// In post
		if ( fixedtoc_is_true( 'in_post' ) ) {
			$this->in_post();
		}
	}
	
	/**
	 * Fixed to post
	 *
	 * @since 3.0.0
	 * @access private
	 */
	private function fixed_to_post() {
//		$font_size = (int) fixedtoc_get_val( 'contents_font_size' );
		$width = (int) fixedtoc_get_val( 'contents_fixed_width' );
		$height = (int) fixedtoc_get_val( 'contents_fixed_height' );
		$this->add_datum( '#ftwp-container.ftwp-wrap #ftwp-contents', array(
			'width' => $width ? $width . 'px' : 'auto',
			'height' => $height ? $height . 'px' : 'auto'
//			'font-size' => $font_size . 'px'
		) );		
	}
	
	/**
	 * In post
	 *
	 * @since 3.0.0
	 * @access private
	 */
	private function in_post() {
		$height = (int) fixedtoc_get_val( 'contents_height_in_post' );
		$height = $height && ! fixedtoc_is_true( 'contents_collapse_init' ) ? $height . 'px' : 'auto';
		$this->add_datum( '.ftwp-in-post#ftwp-container-outer #ftwp-contents', array(
			'height' => $height
		) );

		if ( fixedtoc_is_true( 'float_in_post' ) ) {
			$width = (int) fixedtoc_get_val( 'contents_width_in_post' );
			$width = $width ? $width . 'px' : 'auto';
			$side = fixedtoc_get_val( 'contents_float_in_post' );
			$this->add_datum( ".ftwp-in-post#ftwp-container-outer.ftwp-float-$side #ftwp-contents", array(
				'width' => $width
			) );
		}		
	}
}
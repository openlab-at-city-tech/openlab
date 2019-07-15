<?php
/**
 * Style data of header
 *
 * @since 3.0.0
 */
class Fixedtoc_Style_Data_Header extends Fixedtoc_Style_Data {
	/**
	 * Create data
	 *
	 * @since 3.0.0
	 * @access protected
	 */
	protected function create_data() {
		$this->font();
	}
	
	/**
	 * Font
	 *
	 * @since 3.0.0
	 * @access private
	 */	
	private function font() {
		$font_size = (int) fixedtoc_get_val( 'contents_header_font_size' );
		$font_family = fixedtoc_get_val( 'contents_header_font_family' );
		if ( 'customize' == $font_family ) {
			$customize_font_family = fixedtoc_get_val( 'contents_header_customize_font_family' );
			$font_family = $customize_font_family ? $customize_font_family : $font_family;
		}
		$this->add_datum( '#ftwp-container.ftwp-wrap #ftwp-header', array(
			'font-size' => $font_size . 'px',
			'font-family' => $font_family
		) );
		
		$font_bold = fixedtoc_get_val( 'contents_header_font_bold' ) ? 'bold' : 'normal';
		$this->add_datum( '#ftwp-container.ftwp-wrap #ftwp-header-title', array(
			'font-weight' => $font_bold
		) );		
	}
}
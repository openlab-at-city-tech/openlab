<?php
/**
 * Style data of list
 *
 * @since 3.0.0
 */
class Fixedtoc_Style_Data_List extends Fixedtoc_Style_Data {
	/**
	 * Font size
	 *
	 * @since 3.0.0
	 * @access private
	 * @var int
	 */
	private $font_size;
	
	/**
	 * Create data
	 *
	 * @since 3.0.0
	 * @access protected
	 */
	protected function create_data() {
		$this->font_size = (int) fixedtoc_get_val( 'contents_list_font_size' );
		
		$this->font();
		$this->list_style_type_size();
		if ( fixedtoc_is_true( 'strong_first_list' ) ) {
			$this->strong_first_list();
		}
	}
	
	/**
	 * Font
	 *
	 * @since 3.0.0
	 * @access private
	 */
	private function font() {
		$font_family = fixedtoc_get_val( 'contents_list_font_family' );
		if ( 'customize' == $font_family ) {
			$customize_font_family = fixedtoc_get_val( 'contents_list_customize_font_family' );
			$font_family = $customize_font_family ? $customize_font_family : $font_family;
		}
		$this->add_datum( '#ftwp-container.ftwp-wrap #ftwp-list', array(
			'font-size' => $this->font_size . 'px',
			'font-family' => $font_family
		) );		
	}
	
	/**
	 * List style type size
	 *
	 * @since 3.0.0
	 * @access private
	 */
	private function list_style_type_size() {
		if ( 'decimal' == fixedtoc_get_val( 'contents_list_style_type' ) ) {
			$this->add_datum( '#ftwp-container #ftwp-list.ftwp-liststyle-decimal .ftwp-anchor::before', array(
				'font-size' => $this->font_size . 'px'
			) );
		} else {
			$list_icon_font_size = 0.4 * $this->font_size;
			$this->add_datum( '#ftwp-container.ftwp-wrap #ftwp-list .ftwp-anchor::before', array(
				'font-size' => $list_icon_font_size . 'px'
			) );
		}		
	}
	
	/**
	 * Strong first list
	 *
	 * @since 3.0.0
	 * @access private
	 */
	private function strong_first_list() {
		$strong_font_size = 1.1 * $this->font_size;
		$this->add_datum( '#ftwp-container #ftwp-list.ftwp-strong-first>.ftwp-item>.ftwp-anchor .ftwp-text', array(
			'font-size' => $strong_font_size . 'px'
		) );
		
		if ( 'decimal' == fixedtoc_get_val( 'contents_list_style_type' ) ) {
			$this->add_datum( '#ftwp-container #ftwp-list.ftwp-strong-first.ftwp-liststyle-decimal>.ftwp-item>.ftwp-anchor::before', array(
				'font-size' => $strong_font_size . 'px'
			) );
		} else {
			$list_icon_font_size = 0.5 * $this->font_size;
			$this->add_datum( '#ftwp-container.ftwp-wrap #ftwp-list.ftwp-strong-first>.ftwp-item>.ftwp-anchor::before', array(
				'font-size' => $list_icon_font_size . 'px'
			) );
		}
	}
}
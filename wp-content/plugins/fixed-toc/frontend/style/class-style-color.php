<?php
/**
 * Style data of color scheme
 *
 * @since 3.0.0
 */
class Fixedtoc_Style_Data_Color_scheme extends Fixedtoc_Style_Data {
	/**
	 * Font size
	 *
	 * @since 3.0.0
	 * @access private
	 * @var int
	 */
	private $opacity = 0.95;
	
	/**
	 * Create data
	 *
	 * @since 3.0.0
	 * @access protected
	 */
	protected function create_data() {
		$this->trigger();
		$this->contents();
		$this->contents_header();
		$this->contents_list();
		$this->target_hint();
		$this->effect();
	}
	
	/**
	 * Trigger
	 *
	 * @since 3.0.0
	 * @access private
	 */
	private function trigger() {
		$trigger_color 										= fixedtoc_get_val( 'color_button' );
		$trigger_bg_color 								= fixedtoc_get_val( 'color_button_bg' );
		$trigger_bg_color_rgba 						= $this->object_style->hex2rgba( $trigger_bg_color, $this->opacity );
		$this->add_datum( '#ftwp-container.ftwp-wrap #ftwp-trigger', array(
			'color' => $trigger_color,
			'background' => $trigger_bg_color_rgba
		) );
		
		// Border color
		if ( 'none' != fixedtoc_get_val( 'trigger_border_width' ) ) {
			$trigger_border_color 						= fixedtoc_get_val( 'color_button_border' );
			$trigger_border_color_rgba 				= $this->object_style->hex2rgba( $trigger_border_color, $this->opacity );
			$this->add_datum( '#ftwp-container.ftwp-wrap #ftwp-trigger', array(
				'border-color' => $trigger_border_color_rgba
			) );
		}
	}
	
	/**
	 * Contents
	 *
	 * @since 3.0.0
	 * @access private
	 */
	private function contents() {
		if ( 'none' != fixedtoc_get_val( 'contents_border_width' ) ) {
			$contents_border_color 						= fixedtoc_get_val( 'color_contents_border' );
			$contents_border_color_rgba 			= $this->object_style->hex2rgba( $contents_border_color, $this->opacity );
			$this->add_datum( '#ftwp-container.ftwp-wrap #ftwp-contents', array(
				'border-color' => $contents_border_color_rgba
			) );
		}		
	}
	
	/**
	 * Header
	 *
	 * @since 3.0.0
	 * @access private
	 */
	private function contents_header() {
		$header_color 										= fixedtoc_get_val( 'color_contents_header' );
		$header_bg_color 									= fixedtoc_get_val( 'color_contents_header_bg' );
		$header_bg_color_rgba 						= $this->object_style->hex2rgba( $header_bg_color, $this->opacity );
		$this->add_datum( '#ftwp-container.ftwp-wrap #ftwp-header', array(
			'color' => $header_color,
			'background' => $header_bg_color_rgba
		) );
		$this->add_datum( '#ftwp-container.ftwp-wrap #ftwp-contents:hover #ftwp-header', array(
			'background' => $header_bg_color
		) );
	}
	
	/**
	 * List
	 *
	 * @since 3.0.0
	 * @access private
	 */
	private function contents_list() {
		$list_bg_color 										= fixedtoc_get_val( 'color_contents_list_bg' );
		$list_bg_color_rgba 							= $this->object_style->hex2rgba( $list_bg_color, $this->opacity );
		$list_link_color 									= fixedtoc_get_val( 'color_contents_list_link' );
		$list_hover_link_color 						= fixedtoc_get_val( 'color_contents_list_hover_link' );
		$list_active_link_color 					= fixedtoc_get_val( 'color_contents_list_active_link' );
		$list_active_link_bg_color 				= fixedtoc_get_val( 'color_contents_list_active_link_bg' );
		$list_active_link_bg_color_rgba 	= $this->object_style->hex2rgba( $list_active_link_bg_color, $this->opacity );
		
		$this->add_datum( '#ftwp-container.ftwp-wrap #ftwp-list', array(
			'color' => $list_link_color,
			'background' => $list_bg_color_rgba
		) );
		
		$this->add_datum( '#ftwp-container.ftwp-wrap #ftwp-contents:hover #ftwp-list', array(
			'background' => $list_bg_color
		) );
		
		$this->add_datum( '#ftwp-container.ftwp-wrap #ftwp-list .ftwp-anchor:hover', array(
			'color' => $list_hover_link_color
		) );
		
		$this->add_datum( array(
			'#ftwp-container.ftwp-wrap #ftwp-list .ftwp-anchor:focus',
			'#ftwp-container.ftwp-wrap #ftwp-list .ftwp-active',
			'#ftwp-container.ftwp-wrap #ftwp-list .ftwp-active:hover'
		), array(
			'color' => $list_active_link_color,
			'background' => ( 'none' == fixedtoc_get_val( 'effects_active_link' ) ) ? $list_active_link_bg_color_rgba : ''
		) );
		
		$this->add_datum( '#ftwp-container.ftwp-wrap #ftwp-list .ftwp-text::before', array(
			'background' => $list_active_link_bg_color_rgba
		) );
	}
	
	/**
	 * Target hint
	 *
	 * @since 3.0.0
	 * @access private
	 */
	private function target_hint() {
		$target_hint_bg_color 						= fixedtoc_get_val( 'color_target_hint' );
		$target_hint_color_rgba 					= $this->object_style->hex2rgba( $target_hint_bg_color, $this->opacity );
		
		$this->add_datum( '.ftwp-heading-target::before', array(
			'background' => $target_hint_color_rgba
		) );
	}
	
	/**
	 * Effect
	 *
	 * @since 3.0.0
	 * @access private
	 */
	private function effect() {
		$effect = fixedtoc_get_val( 'effects_active_link' );
		$list_bg_color 										= fixedtoc_get_val( 'color_contents_list_bg' );
		$list_bg_color_rgba 							= $this->object_style->hex2rgba( $list_bg_color, $this->opacity );
		$list_active_link_bg_color 				= fixedtoc_get_val( 'color_contents_list_active_link_bg' );
		$list_active_link_bg_color_rgba 	= $this->object_style->hex2rgba( $list_active_link_bg_color, $this->opacity );
		
		if ( 'fade' == $effect ) {
			$this->add_datum( array(
				'#ftwp-container #ftwp-list.ftwp-effect-fade .ftwp-anchor.ftwp-active',
				'#ftwp-container #ftwp-list.ftwp-effect-fade .ftwp-anchor:focus'
			), array(
				'background' => $list_active_link_bg_color_rgba
			) );
		}
		
		if ( 'radial-in' == $effect || 'rectangle-in' == $effect || 'shutter-in' == $effect ) {
			$this->add_datum( '#ftwp-container.ftwp-wrap #ftwp-list .ftwp-text::before', array(
				'background' => $list_bg_color_rgba
			));
			
			$this->add_datum( array(
				"#ftwp-container #ftwp-list.ftwp-effect-$effect .ftwp-anchor.ftwp-active",
				"#ftwp-container #ftwp-list.ftwp-effect-$effect .ftwp-anchor:focus"
			), array(
				'background' => $list_active_link_bg_color_rgba
			) );
		}
		
		if ( 'round-corners' == $effect ) {
			$this->add_datum( '#ftwp-container.ftwp-wrap #ftwp-list .ftwp-text::before', array(
				'background' => $list_bg_color_rgba
			) );
			
			$this->add_datum( array(
				'#ftwp-container #ftwp-list.ftwp-effect-round-corners .ftwp-anchor.ftwp-active .ftwp-text::before',
				'#ftwp-container #ftwp-list.ftwp-effect-round-corners .ftwp-anchor:focus .ftwp-text::before'
			), array(
				'background' => $list_active_link_bg_color_rgba
			) );
		}
		
		if ( 'border-fade' == $effect ) {
			$this->add_datum( array(
				'#ftwp-container #ftwp-list.ftwp-effect-border-fade .ftwp-anchor.ftwp-active',
				'ftwp-container #ftwp-list.ftwp-effect-border-fade .ftwp-anchor:focus'
			), array(
				'box-shadow' => "inset 0 0 0 2px $list_active_link_bg_color_rgba"
			) );
		}
	}
}
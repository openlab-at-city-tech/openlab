<?php
/**
 * Style data of trigger
 *
 * @since 3.0.0
 */
class Fixedtoc_Style_Data_Trigger extends Fixedtoc_Style_Data {
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
		$this->normal();
		$this->with_border();
		$this->with_circle_border();
	}
	
	/**
	 * Normal
	 *
	 * @since 3.0.0
	 * @access private
	 */
	private function normal() {
		$size = (int) fixedtoc_get_val( 'trigger_size' );
		$this->font_size = 0.6 * $size;
		
		$this->add_datum( '#ftwp-container.ftwp-wrap #ftwp-trigger', array(
			'width' => $size . 'px',
			'height' => $size . 'px',
			'font-size' => $this->font_size . 'px'
		) );		
	}
	
	/**
	 * With border
	 *
	 * @since 3.0.0
	 * @access private
	 */	
	private function with_border() {
		$border = fixedtoc_get_val( 'trigger_border_width' );
		switch ( $border ) {
			case 'thin': { $border_font_size = $this->font_size - 1/2; break; }
			case 'medium': { $border_font_size = $this->font_size - 2/2; break; }
			case 'bold': { $border_font_size = $this->font_size - 5/2; break; }
			default: $border_font_size = 0;
		}
		if ( $border_font_size ) {
			$this->add_datum( "#ftwp-container #ftwp-trigger.ftwp-border-$border", array(
				'font-size' => $border_font_size . 'px'
			) );
		}		
	}
	
	/**
	 * ith circle shape and border
	 *
	 * @since 3.0.0
	 * @access private
	 */	
	private function with_circle_border() {
		$shape = fixedtoc_get_val( 'trigger_shape' );
		$border = fixedtoc_get_val( 'trigger_border_width' );
		if ( 'circle' == $shape ) {
			$circle_font_size = 0.9 * $this->font_size;

			switch ( $border ) {
				case 'thin': { $circle_font_size = $circle_font_size - 1/2; break; }
				case 'medium': { $circle_font_size = $circle_font_size - 2/2; break; }
				case 'bold': { $circle_font_size = $circle_font_size - 5/2; break; }
			}			
			$this->add_datum( "#ftwp-container.ftwp-wrap .ftwp-shape-circle.ftwp-border-$border .ftwp-trigger-icon", array(
				'font-size' => $circle_font_size . 'px'
			) );
		}		
	}
}
<?php
/**
 * Location section field data.
 *
 * @since 3.0.0
 */
class Fixedtoc_Location_Debug_Section_Data extends Fixedtoc_Field_Section_Data {
	
	/*
	 * Create section data.
	 *
	 * @since 3.0.0
	 * @access protected
	 */
	protected function create_section_data() {
		$this->fixed_position();
		$this->horizontal_offset();
		$this->vertical_offset();
	}
	
	/*
	 * Fixed Position.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function fixed_position() {
		$this->section_data['location_fixed_position'] = array(
			'name' 					=> 'location_fixed_position',
			'label' 				=> __( 'Position', 'fixedtoc' ),
			'default' 			=> 'middle-right',
			'type' 					=> 'select',
			'choices'				=> array(
												'top-right' 			=> __( 'Top Right' , 'fixedtoc' ),
												'middle-right' 		=> __( 'Middle Right' , 'fixedtoc' ),
												'bottom-right' 		=> __( 'Bottom Right' , 'fixedtoc' ),
												'top-left' 				=> __( 'Top Left' , 'fixedtoc' ),
												'middle-left' 		=> __( 'Middle Left' , 'fixedtoc' ),
												'bottom-left' 		=> __( 'Bottom Left' , 'fixedtoc' )
												),
			'des'						=> '',
			'transport'			=> 'postMessage'
		);
	}
	
	/*
	 * Horizontal offset.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function horizontal_offset() {
		$this->section_data['location_horizontal_offset'] = array(
			'name' 								=> 'location_horizontal_offset',
			'label' 							=> __( 'Horizontal Offset', 'fixedtoc' ),
			'default' 						=> 10,
			'type' 								=> 'number',
			'meta_input_attrs'		=> array(
																'class' => 'small-text'
															),
			'sanitize'						=> array( 'Fixedtoc_Validate_Data', 'intval_base10' ),
			'des'									=> __( 'Unit: px', 'fixedtoc' ),
			'transport'						=> 'postMessage'
		);
	}	
	
	/*
	 * Vertical offset.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function vertical_offset() {
		$this->section_data['location_vertical_offset'] = array(
			'name' 								=> 'location_vertical_offset',
			'label' 							=> __( 'Vertical Offset', 'fixedtoc' ),
			'default' 						=> 0,
			'type' 								=> 'number',
			'meta_input_attrs'		=> array(
																'class' => 'small-text'
															),
			'sanitize'						=> array( 'Fixedtoc_Validate_Data', 'intval_base10' ),
			'des'									=> __( 'Unit: px', 'fixedtoc' ),
			'transport'						=> 'postMessage'
		);
	}

}
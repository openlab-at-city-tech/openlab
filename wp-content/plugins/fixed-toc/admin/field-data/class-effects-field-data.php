<?php
/**
 * Effecys section field data.
 *
 * @since 3.0.0
 */
class Fixedtoc_Field_Effects_Section_Data extends Fixedtoc_Field_Section_Data {
	
	/*
	 * Create section data.
	 *
	 * @since 3.0.0
	 * @access protected
	 */
	protected function create_section_data() {
		$this->in_out();
		$this->active_link();
	}
	
	/*
	 * In/out Effects.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function in_out() {
		$this->section_data['effects_in_out'] = array(
			'name' 					=> 'effects_in_out',
			'label' 				=> __( 'In/Out', 'fixedtoc' ),
			'default' 			=> 'zoom',
			'type' 					=> 'select',
			'choices'				=> array(
													'none'			=> __( 'None' , 'fixedtoc' ),
													'fade'			=> __( 'Fade' , 'fixedtoc' ),
													'zoom'			=> __( 'Zoom' , 'fixedtoc' )
												),
			'sanitize'			=> '',
			'des'						=> __( 'Select how the TOC show in and hide out.', 'fixedtoc' ),
			'transport'		=> 'refresh'
		);
	}
	
	/*
	 * Active link Effects.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function active_link() {
		$this->section_data['effects_active_link'] = array(
			'name' 					=> 'effects_active_link',
			'label' 				=> __( 'Active Link', 'fixedtoc' ),
			'default' 			=> 'bounce-to-right',
			'type' 					=> 'select',
			'choices'				=> array(
													'none'											=> __( 'None' , 'fixedtoc' ),
													'fade'											=> __( 'Fade' , 'fixedtoc' ),
													'sweep-to-right'						=> __( 'Sweep To Right' , 'fixedtoc' ),
													'sweep-to-left'							=> __( 'Sweep To Left' , 'fixedtoc' ),			  
													'bounce-to-right'						=> __( 'Bounce To Right' , 'fixedtoc' ),
													'bounce-to-left'						=> __( 'Bounce To Left' , 'fixedtoc' ),
													'radial-in'									=> __( 'Radial In' , 'fixedtoc' ),
													'radial-out'								=> __( 'Radial Out' , 'fixedtoc' ),
													'rectangle-in'							=> __( 'Rectangle In' , 'fixedtoc' ),
													'rectangle-out'							=> __( 'Rectangle Out' , 'fixedtoc' ),			  
													'shutter-in'								=> __( 'Shutter In Horizontal' , 'fixedtoc' ),
													'shutter-out'								=> __( 'Shutter Out Horizontal' , 'fixedtoc' ),
													'underline-from-right'			=> __( 'Underline From Right' , 'fixedtoc' ),
													'underline-from-left'				=> __( 'Underline From Left' , 'fixedtoc' ),
													'underline-from-center'			=> __( 'Underline From Center' , 'fixedtoc' ),
													'reveal-underline'					=> __( 'Underline Reveal' , 'fixedtoc' ),
													'reveal-rightline'					=> __( 'Rightline Reveal' , 'fixedtoc' ),
													'reveal-leftline'						=> __( 'Leftline Reveal' , 'fixedtoc' ),
													'round-corners'							=> __( 'Round Corners' , 'fixedtoc' ),
													'border-fade'								=> __( 'Border Fade' , 'fixedtoc' ),
												),
			'sanitize'			=> '',
			'des'						=> '',
			'transport'			=> 'postMessage'
		);
	}

}
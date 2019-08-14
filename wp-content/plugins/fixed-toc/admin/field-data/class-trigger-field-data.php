<?php
/**
 * Debug section field data.
 *
 * @since 3.0.0
 */
class Fixedtoc_Field_Trigger_Section_Data extends Fixedtoc_Field_Section_Data {
	
	/*
	 * Create section data.
	 *
	 * @since 3.0.0
	 * @access protected
	 */
	protected function create_section_data() {
		$this->icon();
		$this->size();
		$this->shape();
		$this->border_width();
		$this->initial_visibility();
	}
	
	/*
	 * Icon.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function icon() {
		$this->section_data['trigger_icon'] = array(
			'name' 					=> 'trigger_icon',
			'label' 				=> __( 'Icon', 'fixedtoc' ),
			'default' 			=> 'number',
			'type' 					=> 'select',
			'choices'				=> array(
												'number' 				=> __( 'List Number' , 'fixedtoc' ),
												'bullet' 				=> __( 'List Bullet' , 'fixedtoc' ),
												'menu' 					=> __( 'Menu' , 'fixedtoc' ),
												'ellipsis' 			=> __( 'Ellipsis' , 'fixedtoc' ),
												'vellipsis' 		=> __( 'Ellipsis Vertical' , 'fixedtoc' ),
												'none' 					=> __( 'None' , 'fixedtoc' )
												),
			'des'						=> '',
			'transport'			=> 'postMessage'
		);
	}
	
	/*
	 * Size.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function size() {
		$this->section_data['trigger_size'] = array(
			'name' 					=> 'trigger_size',
			'label' 				=> __( 'Size', 'fixedtoc' ),
			'default' 			=> 50,
			'type' 					=> 'range',
			'input_attrs'		=> array( 
													'min'			=> 25,
													'max'			=> 70,
													'step'		=> 1,
												),
			'sanitize'			=> 'absint',
			'des'						=> '',
			'transport'			=> 'postMessage'
		);
	}
	
	/*
	 * Shape.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function shape() {
		$this->section_data['trigger_shape'] = array(
			'name' 					=> 'trigger_shape',
			'label' 				=> __( 'Shape', 'fixedtoc' ),
			'default' 			=> 'round',
			'type' 					=> 'select',
			'choices'				=> $this->obj_field_data->get_shape_choices( true ),
			'sanitize'			=> '',
			'des'						=> '',
			'transport'			=> 'postMessage'
		);
	}
	
	/*
	 * Border width.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function border_width() {
		$this->section_data['trigger_border_width'] = array(
			'name' 					=> 'trigger_border_width',
			'label' 				=> __( 'Border', 'fixedtoc' ),
			'default' 			=> 'medium',
			'type' 					=> 'select',
			'choices'				=> $this->obj_field_data->get_border_width_choices(),
			'sanitize'			=> '',
			'des'						=> '',
			'transport'			=> 'postMessage'
		);
	}
	
	/*
	 * Initial visibility.
	 *
	 * @since 3.1.4
	 * @access private
	 *
	 * @return void
	 */
	private function initial_visibility() {
		$this->section_data['trigger_initial_visibility'] = array(
			'name' 					=> 'trigger_initial_visibility',
			'label' 				=> __( 'Initial Visibility', 'fixedtoc' ),
			'default' 			=> 'show',
			'type' 					=> 'radio',
			'choices'				=> array(
				'show' 				=> __( 'Show' , 'fixedtoc' ),
				'hide' 				=> __( 'Hide' , 'fixedtoc' )
			),
			'sanitize'			=> '',
			'des'						=> __( 'Show: Display the trigger button and hide the contents at initial state.<br>Hide: Hide the trigger button and display the contents at initial state.' , 'fixedtoc' ),
			'transport'			=> 'postMessage'
		);
	}
	
}
<?php
/**
 * Contents List section field data.
 *
 * @since 3.0.0
 */
class Fixedtoc_Field_Contents_List_Section_Data extends Fixedtoc_Field_Section_Data {
	
	/*
	 * Create section data.
	 *
	 * @since 3.0.0
	 * @access protected
	 */
	protected function create_section_data() {
	    $this->font_size();
		$this->font_family();
		$this->customize_font_family();
		$this->list_style_type();
		$this->nested_list();
		$this->enable_collapse_expand();
		$this->show_sub_icon();
		$this->accordion();
		$this->colexp_init_state();
		$this->strong_1st();
	}
	
	/*
	 * Font size.
	 *
	 * @since 3.1.11
	 * @access private
	 *
	 * @return void
	 */
	private function font_size() {
	    $this->section_data['contents_list_font_size'] = array(
	        'name' 								=> 'contents_list_font_size',
	        'label' 							=> __( 'Font Size', 'fixedtoc' ),
	        'default' 						=> 14,
	        'type' 								=> 'number',
	        'input_attrs'					=> array(
	            'class' => 'small-text',
	            'min'   => '1',
	            'max'   => '100',
	            'placeholder' => '14',
	        ),
	        'widget_input_attrs'	=> array(
	            'class' => 'small-text',
	            'min'   => '1',
	            'max'   => '100',
	            'placeholder' => '22',
	        ),
	        'des'                          => __( 'Unit: px. Only accept for numbers.', 'fixedtoc' ),
	        'sanitize'						=> 'absint',
	        'transport'						=> 'postMessage'
	    );
	}
	
	/*
	 * Font family.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function font_family() {
		$this->section_data['contents_list_font_family'] = array(
			'name' 								=> 'contents_list_font_family',
			'label' 							=> __( 'Font Family', 'fixedtoc' ),
			'default' 						=> 'inherit',
			'type' 								=> 'select',
			'widget_input_attrs'	=> array(
																'class' => 'widefat'
															),
			'choices'							=> $this->obj_field_data->get_font_family_choices(),
			'sanitize'						=> '',
			'des'									=> '',
			'transport'						=> 'postMessage'
		);
	}
	
	/*
	 * Customize font family.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function customize_font_family() {
		$this->section_data['contents_list_customize_font_family'] = array(
			'name' 								=> 'contents_list_customize_font_family',
			'label' 							=> '',
			'default' 						=> '',
			'type' 								=> 'text',
			'input_attrs'					=> array(
																'class' => 'regular-text'
															),
			'widget_input_attrs'	=> array(
																'class' => 'widefat'
															),
			'sanitize'						=> 'sanitize_text_field',
			'des'									=> '',
			'transport'						=> 'postMessage'
		);
	}
	
	/*
	 * List style type.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function list_style_type() {
		$this->section_data['contents_list_style_type'] = array(
			'name' 								=> 'contents_list_style_type',
			'label' 							=> __( 'List Stype Type', 'fixedtoc' ),
			'default' 						=> 'decimal',
			'type' 								=> 'select',
			'widget_input_attrs'	=> array(
																'class' => 'widefat'
															),
			'choices'							=> $this->obj_field_data->get_list_style_type_choices(),
			'sanitize'						=> '',
			'des'									=> '',
			'transport'						=> 'postMessage'
		);
	}
	
	/*
	 * Nested list.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function nested_list() {
		$this->section_data['contents_list_nested'] = array(
			'name' 					=> 'contents_list_nested',
			'label' 				=> __( 'Nested List', 'fixedtoc' ),
			'default' 			=> '1',
			'type' 					=> 'checkbox',
			'sanitize'			=> '',
			'des'						=> '',
			'transport'			=> 'refresh'
		);
	}	
	
	/*
	 * Enable collapse/expand
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function enable_collapse_expand() {
		$this->section_data['contents_list_colexp'] = array(
			'name' 					=> 'contents_list_colexp',
			'label' 				=> __( 'Enable Collapse/Expand Sub List', 'fixedtoc' ),
			'default' 			=> '1',
			'type' 					=> 'checkbox',
			'sanitize'			=> '',
			'des'						=> '',
			'transport'		=> 'refresh'
		);
	}
	
	/*
	 * Display sub list icon.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function show_sub_icon() {
		$this->section_data['contents_list_sub_icon'] = array(
			'name' 					=> 'contents_list_sub_icon',
			'label' 				=> __( 'Show Sub List Icon', 'fixedtoc' ),
			'default' 			=> '1',
			'type' 					=> 'checkbox',
			'sanitize'			=> '',
			'des'						=> '',
			'transport'		=> 'refresh'
		);
	}
	
	/*
	 * Accordion list.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function accordion() {
		$this->section_data['contents_list_accordion'] = array(
			'name' 					=> 'contents_list_accordion',
			'label' 				=> __( 'Accordion List', 'fixedtoc' ),
			'default' 			=> '',
			'type' 					=> 'checkbox',
			'sanitize'			=> '',
			'des'						=> __( 'Only keeping the current sub list to expand and collapse others.', 'fixedtoc' ),
			'transport'		=> 'refresh'
		);
	}
	
	/*
	 * Collapse/expand init state.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function colexp_init_state() {
		$this->section_data['contents_list_colexp_init_state'] = array(
			'name' 					=> 'contents_list_colexp_init_state',
			'label' 				=> __( 'Initial Collapse/Expand State', 'fixedtoc' ),
			'default' 			=> 'expand_1st',
			'type' 					=> 'select',
			'choices'				=> array(
													'expand_all' 				=> __( 'Expand All' , 'fixedtoc' ),
													'expand_1st' 				=> __( 'Only Expand 1st Level' , 'fixedtoc' ),
													'collapse_all' 			=> __( 'Collapse All' , 'fixedtoc' )
												),
			'sanitize'			=> '',
			'des'						=> '',
			'transport'		=> 'refresh'
		);
	}
	
	/*
	 * Auto collapse other list item.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function strong_1st() {
		$this->section_data['contents_list_strong_1st'] = array(
			'name' 					=> 'contents_list_strong_1st',
			'label' 				=> __( 'Strong 1st Level Item', 'fixedtoc' ),
			'default' 			=> '1',
			'type' 					=> 'checkbox',
			'sanitize'			=> '',
			'des'						=> '',
			'transport'			=> 'postMessage'
		);
	}

}
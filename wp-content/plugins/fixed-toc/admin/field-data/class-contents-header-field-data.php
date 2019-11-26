<?php
/**
 * Contents header section field data.
 *
 * @since 3.0.0
 */
class Fixedtoc_Field_Contents_Header_Section_Data extends Fixedtoc_Field_Section_Data {
	
	/*
	 * Create section data.
	 *
	 * @since 3.0.0
	 * @access protected
	 */
	protected function create_section_data() {
		$this->title();
		$this->font_size();
		$this->font_family();
		$this->customize_font_family();
		$this->font_bold();
		$this->title_tag();
	}
	
	/*
	 * Title.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function title() {
		$this->section_data['contents_header_title'] = array(
			'name' 								=> 'contents_header_title',
			'label' 							=> __( 'Title', 'fixedtoc' ),
			'default' 						=> 'Contents',
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
	 * Font size.
	 *
	 * @since 3.1.11
	 * @access private
	 *
	 * @return void
	 */
	private function font_size() {
	    $this->section_data['contents_header_font_size'] = array(
	        'name' 								=> 'contents_header_font_size',
	        'label' 							=> __( 'Font Size', 'fixedtoc' ),
	        'default' 						=> 22,
	        'type' 								=> 'number',
	        'input_attrs'					=> array(
	            'class' => 'small-text',
	            'min'   => '1',
	            'max'   => '100',
	            'placeholder' => '22',
	        ),
	        'widget_input_attrs'	=> array(
	            'class' => 'small-text',
	            'min'   => '1',
	            'max'   => '100',
	            'placeholder' => '22',
	        ),
	        'des'                          => __( 'Unit: px. Only accept for numbers', 'fixedtoc' ),
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
		$this->section_data['contents_header_font_family'] = array(
			'name' 								=> 'contents_header_font_family',
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
		$this->section_data['contents_header_customize_font_family'] = array(
			'name' 								=> 'contents_header_customize_font_family',
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
	 * Font bold.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function font_bold() {
		$this->section_data['contents_header_font_bold'] = array(
			'name' 					=> 'contents_header_font_bold',
			'label' 				=> __( 'Font Bold', 'fixedtoc' ),
			'default' 			=> '1',
			'type' 					=> 'checkbox',
			'des'						=> '',
			'transport'			=> 'postMessage'
		);
	}
	
	/*
	 * Set the heading tag of the title.
	 *
	 * @since 3.1.11
	 * @access private
	 *
	 * @return void
	 */
	private function title_tag() {
	    $this->section_data['contents_header_title_tag'] = array(
	        'name' 								=> 'contents_header_title_tag',
	        'label' 							=> __( 'Heading Tag', 'fixedtoc' ),
	        'default' 						=> 'h3',
	        'type' 								=> 'select',
	        'widget_input_attrs'	=> array(
	            'class' => 'widefat'
	        ),
	       'choices'                       => array(
	           'h1' => 'H1',
	           'h2' => 'H2',
	           'h3' => 'H3',
	           'h4' => 'H4',
	           'h5' => 'H5',
	           'h6' => 'H6',
	       ),
	        'sanitize'						=> '',
	        'des'							=> __( 'To fit the page structure for SEO.', 'fixedtoc' ),
	        'transport'						=> 'postMessage'
	    );
	}

}
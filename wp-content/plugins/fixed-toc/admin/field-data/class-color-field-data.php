<?php
/**
 * Color scheme section field data.
 *
 * @since 3.0.0
 */
class Fixedtoc_Field_Color_Section_Data extends Fixedtoc_Field_Section_Data {
	
	/*
	 * Create section data.
	 *
	 * @since 3.0.0
	 * @access protected
	 */
	protected function create_section_data() {
		$this->button();
		$this->button_bg();
		$this->contents_header();
		$this->contents_header_bg();
		$this->button_border();
		$this->contents_border();
		$this->contents_list_bg();
		$this->contents_list_link();
		$this->contents_list_hover_link();
		$this->contents_list_active_link();
		$this->contents_list_active_link_bg();
		$this->target_hint();
	}
	
	/*
	 * Trigger button
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function button() {
		$this->section_data['color_button'] = array(
			'name' 					=> 'color_button',
			'label' 				=> __( 'Trigger Button Color', 'fixedtoc' ),
			'default' 			=> '#333',
			'type' 					=> 'color',
			'sanitize'			=> 'sanitize_hex_color',
			'transport'			=> 'postMessage'
		);
	}
	
	/*
	 * Trigger button background
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function button_bg() {
		$this->section_data['color_button_bg'] = array(
			'name' 					=> 'color_button_bg',
			'label' 				=> __( 'Trigger Button Background Color', 'fixedtoc' ),
			'default' 			=> '#f3f3f3',
			'type' 					=> 'color',
			'sanitize'			=> 'sanitize_hex_color',
			'transport'			=> 'postMessage'
		);
	}
	
	/*
	 * Trigger button border color
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function button_border() {
		$this->section_data['color_button_border'] = array(
			'name' 					=> 'color_button_border',
			'label' 				=> __( 'Trigger Button Border Color', 'fixedtoc' ),
			'default' 			=> '#333',
			'type' 					=> 'color',
			'sanitize'			=> 'sanitize_hex_color',
			'transport'			=> 'postMessage'
		);
	}
	
	/*
	 * Contents border color
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function contents_border() {
		$this->section_data['color_contents_border'] = array(
			'name' 					=> 'color_contents_border',
			'label' 				=> __( 'Contents Border Color', 'fixedtoc' ),
			'default' 			=> '#333',
			'type' 					=> 'color',
			'sanitize'			=> 'sanitize_hex_color',
			'transport'			=> 'postMessage'
		);
	}
	
	/*
	 * Contents header
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function contents_header() {
		$this->section_data['color_contents_header'] = array(
			'name' 					=> 'color_contents_header',
			'label' 				=> __( 'Contents Header Color', 'fixedtoc' ),
			'default' 			=> '#333',
			'type' 					=> 'color',
			'sanitize'			=> 'sanitize_hex_color',
			'transport'			=> 'postMessage'
		);
	}
	
	/*
	 * Contents header background
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function contents_header_bg() {
		$this->section_data['color_contents_header_bg'] = array(
			'name' 					=> 'color_contents_header_bg',
			'label' 				=> __( 'Contents Header Background Color', 'fixedtoc' ),
			'default' 			=> '#f3f3f3',
			'type' 					=> 'color',
			'sanitize'			=> 'sanitize_hex_color',
			'transport'			=> 'postMessage'
		);
	}	
	
	/*
	 * Contents list background
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function contents_list_bg() {
		$this->section_data['color_contents_list_bg'] = array(
			'name' 					=> 'color_contents_list_bg',
			'label' 				=> __( 'Contents List Background Color', 'fixedtoc' ),
			'default' 			=> '#f3f3f3',
			'type' 					=> 'color',
			'sanitize'			=> 'sanitize_hex_color',
			'transport'			=> 'postMessage'
		);
	}
	
	/*
	 * Contents list link
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function contents_list_link() {
		$this->section_data['color_contents_list_link'] = array(
			'name' 					=> 'color_contents_list_link',
			'label' 				=> __( 'Contents Link Color', 'fixedtoc' ),
			'default' 			=> '#333',
			'type' 					=> 'color',
			'sanitize'			=> 'sanitize_hex_color',
			'transport'			=> 'postMessage'
		);
	}
	
	/*
	 * Contents list link:hover
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function contents_list_hover_link() {
		$this->section_data['color_contents_list_hover_link'] = array(
			'name' 					=> 'color_contents_list_hover_link',
			'label' 				=> __( 'Contents Link Hover Color', 'fixedtoc' ),
			'default' 			=> '#00A368',
			'type' 					=> 'color',
			'sanitize'			=> 'sanitize_hex_color',
			'transport'			=> 'postMessage'
		);
	}
	
	/*
	 * Contents list active link
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function contents_list_active_link() {
		$this->section_data['color_contents_list_active_link'] = array(
			'name' 					=> 'color_contents_list_active_link',
			'label' 				=> __( 'Contents Link Active Color', 'fixedtoc' ),
			'default' 			=> '#fff',
			'type' 					=> 'color',
			'sanitize'			=> 'sanitize_hex_color',
			'transport'			=> 'postMessage'
		);
	}
	
	/*
	 * Contents list active link background
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function contents_list_active_link_bg() {
		$this->section_data['color_contents_list_active_link_bg'] = array(
			'name' 					=> 'color_contents_list_active_link_bg',
			'label' 				=> __( 'Contents Link Active Background Color', 'fixedtoc' ),
			'default' 			=> '#dd3333',
			'type' 					=> 'color',
			'sanitize'			=> 'sanitize_hex_color',
			'transport'			=> 'postMessage'
		);
	}
	
	/*
	 * Target hint
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function target_hint() {
		$this->section_data['color_target_hint'] = array(
			'name' 					=> 'color_target_hint',
			'label' 				=> __( 'Target Hint Color', 'fixedtoc' ),
			'default' 			=> '#dd3333',
			'type' 					=> 'color',
			'sanitize'			=> 'sanitize_hex_color',
			'des'						=> __( 'Browser will scroll to the target heading then show a short color hint when clicking a contetns link.', 										'fixedtoc' ),
			'transport'			=> 'postMessage'
		);
	}

}
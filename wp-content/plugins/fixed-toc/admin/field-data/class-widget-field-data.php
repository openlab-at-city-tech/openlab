<?php
/**
 * Contents Widget section field data.
 *
 * @since 3.0.0
 */
class Fixedtoc_Field_Widget_Section_Data extends Fixedtoc_Field_Section_Data {
	/*
	 * Create section data.
	 *
	 * @since 3.0.0
	 * @access protected
	 */
	protected function create_section_data() {
		$this->fixed();
	}
	
	/*
	 * Fixed widget.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function fixed() {
		$this->section_data['widget_fixed'] = array(
			'name' 					=> 'widget_fixed',
			'label' 				=> __( 'Fixed The Widget', 'fixedtoc' ),
			'default' 			=> '1',
			'type' 					=> 'checkbox',
			'des'						=> ''
		);
	}
}
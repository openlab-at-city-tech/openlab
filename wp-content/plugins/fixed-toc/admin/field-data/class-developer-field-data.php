<?php
/**
 * Developer section field data.
 *
 * @since 3.0.0
 */
class Fixedtoc_Field_Developer_Section_Data extends Fixedtoc_Field_Section_Data {
	
	/*
	 * Create section data.
	 *
	 * @since 3.0.0
	 * @access protected
	 */
	protected function create_section_data() {
		$this->developer_debug();
	}
	
	private function developer_debug() {
		$this->section_data['developer_debug'] = array(
			'name' 					=> 'developer_debug',
			'label' 				=> __( 'Debug', 'fixedtoc' ),
			'default' 			=> false,
			'type' 					=> 'checkbox',
			'des'						=> __( 'Enable Debug mode.<br>Use the uncompressed version of CSS and JavaScript files, console log, etc.', 'fixedtoc' )
		);
	}

}
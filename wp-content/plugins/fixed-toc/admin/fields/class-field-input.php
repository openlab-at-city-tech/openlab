<?php
/**
 * Normal input field
 *
 * @since 3.0.0
 */
class Fixedtoc_Field_Input extends Fixedtoc_Field {
	/*
	 * Get html code.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return string
	 */	
	public function get_html() {
		$type 					= isset( $this->args['type'] ) && $this->args['type'] ? $this->args['type'] : 'text';
		$name 					= $this->args['name'];
		$value 					= $this->args['value'];
		$extra_attrs 		= $this->get_extra_attrs();
		
		return '<input type="' . esc_attr( $type ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '"' . $extra_attrs . '>';
	}
}
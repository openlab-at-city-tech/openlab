<?php
/**
 * Color field
 *
 * @since 3.0.0
 */
class Fixedtoc_Field_Color extends Fixedtoc_Field {
	/*
	 * Get html code.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return string
	 */	
	public function get_html() {
		$name 					= esc_attr( $this->args['name'] );
		$default 				= esc_attr( $this->args['default'] );
		$value 					= esc_attr( $this->args['value'] );
		$extra_attrs 		= $this->get_extra_attrs();
		
		return "<input type=\"input\" name=\"$name\" class=\"fixedtoc-color-field\" data-default-color=\"$default\" value=\"$value\"{$extra_attrs}>";
	}
}
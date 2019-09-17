<?php
/**
 * Checkbox field
 *
 * @since 3.0.0
 */
class Fixedtoc_Field_Checkbox extends Fixedtoc_Field {
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
		$value 					= (bool) $this->args['value'];
		$extra_attrs 		= $this->get_extra_attrs();
		$disabled				= isset( $this->args['input_attrs']['disabled'] ) && $this->args['input_attrs']['disabled'] ? ' disabled' : '';
		$checked 				= checked( $value, true, false );
		
		$html = '<fieldset>';
		$html .= "<input type=\"hidden\" name=\"$name\" value=\"0\"{$disabled}>";
		$html .= "<input type=\"checkbox\" name=\"{$name}\" value=\"1\"$checked{$extra_attrs}>";
		$html .= '</fieldset>';
		
		return $html;
	}
}
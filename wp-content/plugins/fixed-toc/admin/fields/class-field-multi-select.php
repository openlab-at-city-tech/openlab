<?php
/**
 * Multiple select field
 *
 * @since 3.0.0
 */
class Fixedtoc_Field_Multi_Select extends Fixedtoc_Field {
	
	/*
	 * Get html code.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return string
	 */	
	public function get_html() {
		$choices		= $this->args['choices'];
		if ( ! $choices ) {
			return '';
		}		
		
		$name 					= esc_attr( $this->args['name'] );
		$value 					= (array) $this->args['value'];
		$extra_attrs 		= $this->get_extra_attrs();
		$disabled				= isset( $this->args['input_attrs']['disabled'] ) && $this->args['input_attrs']['disabled'] ? ' disabled' : '';
		
		$html =  "<input type=\"hidden\" name=\"$name\" value=\"\"{$disabled}><select name=\"{$name}[]\" multiple{$extra_attrs}>";
		foreach ( $choices as $val => $text ) {
			$val = esc_attr( $val );
			$text = sanitize_text_field( $text );
			$selected = selected( true, in_array( $val, $value ), false );
			$html .= "<option value=\"$val\"$selected>$text</option>";
		}
		$html .= '</select>';
		
		return $html;
	}
}
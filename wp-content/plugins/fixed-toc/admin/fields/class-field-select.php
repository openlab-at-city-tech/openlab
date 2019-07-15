<?php
/**
 * Select field
 *
 * @since 3.0.0
 */
class Fixedtoc_Field_Select extends Fixedtoc_Field {
	
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
		$value 					= $this->args['value'];
		$extra_attrs 		= $this->get_extra_attrs();
		
		$html =  "<select name=\"$name\"{$extra_attrs}>";
		foreach ( $choices as $val => $text ) {
			$val = esc_attr( $val );
			$text = sanitize_text_field( $text );
			$selected = selected( $val, esc_attr( $value ), false );
			$html .= "<option value=\"$val\"$selected>$text</option>";
		}
		$html .= '</select>';
		
		return $html;
	}
}
<?php
/**
 * Radio field
 *
 * @since 3.0.0
 */
class Fixedtoc_Field_Radio extends Fixedtoc_Field {
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
		
		if ( isset( $this->args['input_attrs']['id'] ) ) {
			unset( $this->args['input_attrs']['id'] );
		}
		
		$name 					= esc_attr( $this->args['name'] );
		$value 					= $this->args['value'];
		$extra_attrs 		= $this->get_extra_attrs();
		
		$html = '<fieldset>';
		foreach ( $choices as $val => $label ) {
			$label = esc_html( $label );
			$checked = checked( $val, $value, false );
			$html .= "<label><input type=\"radio\" name=\"$name\" value=\"$val\"$checked{$extra_attrs}>$label</label><br>";
		}
		$html .= '</fieldset>';
		
		return substr( $html, 0, -4 );
	}
}
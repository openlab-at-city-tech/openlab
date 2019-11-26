<?php
/**
 * Multiplae checkbox field
 *
 * @since 3.0.0
 */
class Fixedtoc_Field_Multi_Checkbox extends Fixedtoc_Field {
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
		$value 					= (array) $this->args['value'];
		$choices				= $this->args['choices'];
		$extra_attrs 		= $this->get_extra_attrs();
		$disabled				= isset( $this->args['input_attrs']['disabled'] ) && $this->args['input_attrs']['disabled'] ? ' disabled' : '';
		
		$html = '<fieldset>';
		$html .= "<input type=\"hidden\" name=\"$name\" value=\"\"{$disabled}>";
		foreach ( $choices as $val => $label ) {
			$label = esc_html( $label );
			$val = esc_attr( $val );
			$checked = checked( true, in_array( $val, $value ), false );
			$html .= "<label><input type=\"checkbox\" name=\"{$name}[]\" value=\"$val\"$checked{$extra_attrs}> $label</label><br>";
		}
		$html .= '</fieldset>';
		
		return $html;
	}
}
<?php
/**
 * Form fields factory
 *
 * @since 3.0.0
 */
class Fixedtoc_Fields_Factory {
	/**
	 * An object of Fixedtoc_Field
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @var object
	 */	
	private $field_obj;
	
	/*
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param array $args
	 */
	public function __construct( $args ) {
		$type = isset( $args['type'] ) ? $args['type'] : 'text';
		switch ( $type ) {
			case 'select'							: { $obj_name = 'Fixedtoc_Field_Select'; break; }
			case 'multi_select'				: { $obj_name = 'Fixedtoc_Field_Multi_Select'; break; }
			case 'textarea'						: { $obj_name = 'Fixedtoc_Field_Textarea'; break; }
			case 'color'							: { $obj_name = 'Fixedtoc_Field_Color'; break; }
			case 'radio'							: { $obj_name = 'Fixedtoc_Field_Radio'; break; }
			case 'checkbox'						: { $obj_name = 'Fixedtoc_Field_Checkbox'; break; }
			case 'multi_checkbox'			: { $obj_name = 'Fixedtoc_Field_Multi_Checkbox'; break; }
			default										: 	$obj_name = 'Fixedtoc_Field_Input';
		}
		
		$this->field_obj = new $obj_name( $args );
	}

	/*
	 * Get html code.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return string
	 */	
	public function get_html() {
		return $this->field_obj->get_html();
	}
}


/**
 * Load field files
 *
 * @since 3.0.0
 */
require_once 'abstract-field.php';
require_once 'class-field-input.php';
require_once 'class-field-select.php';
require_once 'class-field-multi-select.php';
require_once 'class-field-textarea.php';
require_once 'class-field-color.php';
require_once 'class-field-radio.php';
require_once 'class-field-checkbox.php';
require_once 'class-field-multi-checkbox.php';
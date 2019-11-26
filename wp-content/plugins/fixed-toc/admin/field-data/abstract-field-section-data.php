<?php
/**
 * Section Field Data.
 *
 * @since 3.0.0
 */
abstract class Fixedtoc_Field_Section_Data {
	/**
	 * Section data
	 *
	 * @since 3.0.0
	 * @access protected
	 * @var array
	 */
	protected $section_data = array();

	/**
	 * The field data object
	 *
	 * @since 3.0.0
	 * @access protected
	 * @var object
	 */
	protected $obj_field_data;

	/*
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param object $obj_field_data
	 */
	public function __construct( $obj_field_data ) {
		$this->obj_field_data = $obj_field_data;
		$this->create_section_data();
	}
	
	/*
	 * Create section data.
	 *
	 * @since 3.0.0
	 * @access protected
	 */
	abstract protected function create_section_data();
	
	/*
	 * Get the section data.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_section_data() {
		return $this->section_data;
	}
}
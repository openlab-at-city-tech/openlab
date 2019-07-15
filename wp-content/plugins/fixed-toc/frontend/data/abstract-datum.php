<?php
/**
 * Datum supper class.
 *
 * @since 3.0.0
 */

abstract class Fixedtoc_Datum {
	/**
	 * The name for a datum.
	 *
	 * @since 3.0.0
	 * @access proteced
	 * @var string
	 */
	protected $name;	
	
	/**
	 * Value for a datum.
	 *
	 * @since 3.0.0
	 * @access proteced
	 * @var string
	 */
	protected $value;
	
	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param object $obj_data an Instance of Fixedtoc_Data.
	 */
	public function __construct() {}
	
	/**
	 * Set the name.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return void
	 */
	abstract public function set_name();
	
	/**
	 * Set the value.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param object $obj_data an Instance of Fixedtoc_Data.
	 * @return void
	 */
	abstract public function set_value( Fixedtoc_Data $obj_data );
	
	/**
	 * Get the name.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Get the value.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function get_value() {
		return $this->value;
	}
}
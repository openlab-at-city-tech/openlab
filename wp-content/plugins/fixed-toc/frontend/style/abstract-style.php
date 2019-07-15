<?php
/**
 * Abstract class of inline style data
 *
 * @since 3.0.0
 */
abstract class Fixedtoc_Style_Data {
	/**
	 * Object of Fixedtoc_Inline_Style
	 *
	 * @since 3.0.0
	 * @access protected
	 * @var object
	 */
	protected $object_style;
	
	/**
	 * Data of CSS
	 *
	 * @since 3.0.0
	 * @access protected
	 * @var array
	 */
	protected $data = array();
	
	/**
	 * Constructor
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function __construct( Fixedtoc_Inline_Style $object_style ) {
		$this->object_style = $object_style;
		$this->create_data();
	}
	
	/**
	 * Add datum
	 *
	 * @since 3.0.0
	 * @access protected
	 *
	 * @param string|array $selectors.
	 * @param array $declaration.
	 * @return array
	 */
	protected function add_datum( $selectors, $declaration ) {
		$this->data[] = array(
			'selectors' => $selectors,
			'declaration' => $declaration
		);
	}
	
	/**
	 * Get data
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return array.
	 */
	public function get_data() {
		return $this->data;
	}
	
	/**
	 * Create data
	 *
	 * @since 3.0.0
	 * @access protected
	 */
	abstract protected function create_data();
}
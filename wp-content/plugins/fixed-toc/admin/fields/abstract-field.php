<?php
/**
 * Form field abstract class
 *
 * @since 3.0.0
 */
abstract class Fixedtoc_Field {
	/**
	 * Arguments
	 *
	 * @since 3.0.0
	 * @access protected
	 *
	 * @var array
	 */	
	protected $args = array();
	
	/*
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param array $args
	 */
	public function __construct( $args ) {
		$this->args = $args;
	}
	
	/*
	 * Get html code.
	 *
	 * @since 3.0.0
	 * @access protected
	 *
	 * @return string
	 */
	protected function get_extra_attrs() {
		$attrs 					= isset( $this->args['input_attrs'] ) ? (array) $this->args['input_attrs'] : array();
		$extra_attrs 		= '';
		foreach ( $attrs as $n => $v ) {
			if ( is_bool( $v ) ) {
				if ( $v ) {
					$extra_attrs .= ' ' . $n;
				}
			} else {
				$extra_attrs .= ' ' . $n . '="' . esc_attr( $v ) . '"';
			}
		}
		
		return $extra_attrs;
	}
	
	/*
	 * Get html code.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return string
	 */
	abstract public function get_html();
}
<?php

namespace Imagely\NGG\Admin;

/**
 * Requirements Notice class for NextGEN Gallery.
 *
 * Handles display of requirement notices in the admin interface.
 */
class RequirementsNotice {

	/**
	 * Notice name.
	 *
	 * @var string
	 */
	protected $_name;

	/**
	 * Notice data.
	 *
	 * @var array
	 */
	protected $_data;

	/**
	 * Notice callback.
	 *
	 * @var callable
	 */
	protected $_callback;

	/**
	 * Constructor.
	 *
	 * @param string   $name     Notice name.
	 * @param callable $callback Notice callback.
	 * @param array    $data     Notice data.
	 */
	public function __construct( $name, $callback, $data ) {
		$this->_name     = $name;
		$this->_data     = $data;
		$this->_callback = $callback;
	}

	/**
	 * Checks if the notice is renderable.
	 *
	 * @return bool Always returns true.
	 */
	public function is_renderable() {
		return true;
	}

	/**
	 * Checks if the notice is dismissable.
	 *
	 * @return bool Whether the notice can be dismissed.
	 */
	public function is_dismissable() {
		return isset( $this->_data['dismissable'] ) ? $this->_data['dismissable'] : true;
	}

	/**
	 * Renders the notice content.
	 *
	 * @return string The notice message.
	 */
	public function render() {
		return $this->_data['message'];
	}

	/**
	 * Gets the MVC template for the notice.
	 *
	 * @return string The template path.
	 */
	public function get_mvc_template() {
		return 'photocrati-nextgen_admin#requirement_notice';
	}

	/**
	 * Gets the notice name.
	 *
	 * @return string The notice name.
	 */
	public function get_name() {
		return $this->_name;
	}

	/**
	 * Runs the notice callback.
	 *
	 * @return bool The callback result.
	 */
	public function run_callback() {
		if ( is_callable( $this->_callback ) ) {
			return call_user_func( $this->_callback );
		} else {
			return false;
		}
	}

	/**
	 * Gets the CSS class for the notice.
	 *
	 * @return string The CSS class.
	 */
	public function get_css_class() {
		$prefix = 'notice notice-';
		if ( $this->is_dismissable() ) {
			return $prefix . 'warning';
		} else {
			return $prefix . 'error';
		}
	}

	/**
	 * Gets the notice message.
	 *
	 * @return string The notice message.
	 */
	public function get_message() {
		return empty( $this->_data['message'] ) ? '' : $this->_data['message'];
	}
}

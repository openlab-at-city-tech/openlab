<?php

namespace Imagely\NGG\Admin;

class RequirementsNotice {

	protected $_name;
	protected $_data;
	protected $_callback;

	/**
	 * @param string   $name
	 * @param callable $callback
	 * @param array    $data
	 */
	public function __construct( $name, $callback, $data ) {
		$this->_name     = $name;
		$this->_data     = $data;
		$this->_callback = $callback;
	}

	/**
	 * @return bool
	 */
	public function is_renderable() {
		return true;
	}

	/**
	 * @return bool
	 */
	public function is_dismissable() {
		return isset( $this->_data['dismissable'] ) ? $this->_data['dismissable'] : true;
	}

	/**
	 * @return string
	 */
	public function render() {
		return $this->_data['message'];
	}

	/**
	 * @return string
	 */
	public function get_mvc_template() {
		return 'photocrati-nextgen_admin#requirement_notice';
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->_name;
	}

	/**
	 * @return bool
	 */
	public function run_callback() {
		if ( is_callable( $this->_callback ) ) {
			return call_user_func( $this->_callback );
		} else {
			return false;
		}
	}

	/**
	 * @return string
	 */
	public function get_css_class() {
		$prefix = 'notice notice-';
		if ( $this->is_dismissable() ) {
			return $prefix . 'warning';
		} else {
			return $prefix . 'error';
		}
	}

	public function get_message() {
		return empty( $this->_data['message'] ) ? '' : $this->_data['message'];
	}
}

<?php

namespace Imagely\NGG\Admin\Notifications;

class Wrapper {

	public $_name;
	public $_data;

	public function __construct( $name, $data ) {
		$this->_name = $name;
		$this->_data = $data;
	}

	public function is_renderable() {
		return true;
	}

	public function is_dismissable() {
		return true;
	}

	public function render() {
		return $this->_data['message'];
	}
}

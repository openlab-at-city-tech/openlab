<?php

/*
@package TaskFreak
@since 0.1
@version 1.0

*/

class tzn_controller {

	protected $_mode;

	public function __construct() {
		$this->_mode = 'echo';
	}
	
	protected function call($file) {
		include TFK_ROOT_PATH.'inc/controllers/'.$file;
	}
	
	protected function view_inc($file) {
		include TFK_ROOT_PATH.'inc/views/'.$file;
	}
	
	protected function view($file) {
		if ($this->_mode == 'return') {
			$this->_view = $file;
		} else {
			include TFK_ROOT_PATH.'inc/views/'.$file;
		}
	}
		
	protected function view_front() {
		if (isset($this->_view)) {	
			ob_start();
			include TFK_ROOT_PATH.'inc/views/'.$this->_view;
			return ob_get_clean();
		}
	}

}
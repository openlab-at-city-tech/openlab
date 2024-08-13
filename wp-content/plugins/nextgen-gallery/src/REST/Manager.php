<?php

namespace Imagely\NGG\REST;

use Imagely\NGG\REST\Admin\AttachToPost;
use Imagely\NGG\REST\Admin\Block;

class Manager {

	public static function rest_api_init() {
		$block = new Block();
		$block->register_routes();

		$atp = new AttachToPost();
		$atp->register_routes();
	}
}

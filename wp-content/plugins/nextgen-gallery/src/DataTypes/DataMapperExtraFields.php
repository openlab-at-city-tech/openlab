<?php

namespace Imagely\NGG\DataTypes;

use Imagely\NGG\DataMapper\{WPModel, WPPostDriver};

class DataMapperExtraFields extends WPModel {

	public function get_mapper() {
		return new WPPostDriver( 'extra_fields' );
	}
}

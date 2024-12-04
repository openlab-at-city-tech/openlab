<?php
/**
 * Created by PhpStorm.
 * User: Extend Studio
 * Date: 3/19/2019
 * Time: 5:35 PM
 */

namespace ColibriWP\Theme\Customizer;

class Formatter {

	public static function sanitizeControlValue( $control_type, $value ) {
		switch ( $control_type ) {
			case 'switch':
				$value = ! ! intval( $value );
				break;
		}

		return $value;
	}
}

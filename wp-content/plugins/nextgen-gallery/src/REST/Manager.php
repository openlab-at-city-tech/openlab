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

	/**
	 * This entire method exists because WordPress' rest_url() bungles provided URL path including GET parameters that
	 * already include a question mark ?suchAs=this&other=things when site permalinks are not enabled.
	 *
	 * @param string $path
	 * @param array  $parameters Array of key => value pairs to include in the URL
	 * @param bool   $show_template When TRUE this returns ?key={key}&thing={thing} for JS frontends to substitute
	 * @return string
	 */
	public static function get_url( $path, $parameters = [], $show_template = false ) {
		global $wp_rewrite;

		if ( $wp_rewrite->using_index_permalinks() ) {
			$first_separator = '?';
		} else {
			$first_separator = '&';
		}

		$second_separator = '&';
		$parameter_string = '';

		if ( ! empty( $parameters ) ) {
			$first_separator_added = false;

			foreach ( $parameters as $key => $value ) {

				if ( ! $first_separator_added ) {
					$parameter_string .= $first_separator;
				} else {
					$parameter_string .= $second_separator;
				}

				if ( $show_template ) {
					$parameter_string .= "{$key}=" . '{' . $key . '}';
				} else {
					$parameter_string .= "{$key}={$value}";
				}

				$first_separator_added = true;
			}
		}

		return rest_url( $path . $parameter_string );
	}
}

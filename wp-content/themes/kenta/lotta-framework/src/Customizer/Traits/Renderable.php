<?php

namespace LottaFramework\Customizer\Traits;

use LottaFramework\Utils;

trait Renderable {

	/**
	 * @var array
	 */
	protected $attributes = [];

	/**
	 * @param $name
	 * @param $attr
	 * @param $value
	 */
	protected function add_render_attribute( $name, $attr, $value ) {
		if ( ! isset( $this->attributes[ $name ] ) ) {
			$this->attributes[ $name ] = [];
		}

		$this->attributes[ $name ][ $attr ] = $value;
	}

	/**
	 * @param $name
	 *
	 * @return string
	 */
	protected function render_attribute_string( $name ) {
		if ( ! isset( $this->attributes[ $name ] ) ) {
			return '';
		}

		return Utils::render_attribute_string( $this->attributes[ $name ] );
	}

	/**
	 * @param $name
	 */
	protected function print_attribute_string( $name ) {
		echo $this->render_attribute_string( $name );
	}
}


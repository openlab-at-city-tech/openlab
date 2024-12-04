<?php

namespace LottaFramework\Async;

use LottaFramework\Utils;

class Css {

	/**
	 * Generate dynamic css script
	 *
	 * @param $id
	 * @param $css
	 *
	 * @return string
	 */
	public function dynamic( $id, $css ) {
		return "LottaCss.addDynamicStyle('$id', LottaCss.parse($css))";
	}

	/**
	 * Generate value mapper script
	 */
	public function valueMapper( $maps, $selector = 'value' ) {
		$maps = wp_json_encode( $maps );

		return "LottaCss.valueMapper($selector,$maps)";
	}

	/**
	 * @param $script
	 *
	 * @return string
	 */
	public function unescape( $script ) {
		return '!!!' . $script;
	}

	/**
	 * Encode async css
	 *
	 * @param $css
	 *
	 * @return mixed|string
	 */
	public function encode( $css ) {

		if ( is_array( $css ) ) {
			$css_obj = [];

			foreach ( $css as $property => $value ) {

				if ( is_string( $value ) && Utils::str_starts_with( $value, '!!!' ) ) {
					$value = substr( $value, 3 );
				} else if ( $value !== 'value' ) {
					if ( is_array( $value ) ) {
						$value = $this->encode( $value );
					} else {
						$value = wp_json_encode( $value );
					}
				}

				$css_obj[] = "\"$property\":$value";
			}

			$css = '{' . implode( ',', $css_obj ) . '}';
		}

		return $css;
	}

	/**
	 * @param string $selector
	 *
	 * @return string
	 */
	public function dimensions( $selector = 'margin' ) {
		return "LottaCss.dimensions(value,'$selector')";
	}

	/**
	 * @return string
	 */
	public function background() {
		return "LottaCss.background(value)";
	}

	/**
	 * @param string $selector
	 *
	 * @return string
	 */
	public function border( $selector = 'border' ) {
		return "LottaCss.border(value, '$selector')";
	}

	/**
	 * @param string $selector
	 *
	 * @return string
	 */
	public function shadow( $selector = 'box-shadow' ) {
		return "LottaCss.shadow(value, '$selector')";
	}

	/**
	 * @return string
	 */
	public function filters() {
		return "LottaCss.filters(value)";
	}

	/**
	 * @return string
	 */
	public function typography() {
		return "LottaCss.typography(value)";
	}

	/**
	 * @param $maps
	 * @param array $css
	 *
	 * @return string
	 */
	public function colors( $maps, $css = [] ) {
		$maps = wp_json_encode( $maps );
		$css  = wp_json_encode( $css );

		return "LottaCss.colors(value,$maps,$css)";
	}
}
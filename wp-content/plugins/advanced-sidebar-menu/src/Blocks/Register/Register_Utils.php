<?php

namespace Advanced_Sidebar_Menu\Blocks\Register;

use Advanced_Sidebar_Menu\Blocks\Block_Abstract;
use Advanced_Sidebar_Menu\Traits\Singleton;

/**
 * Utilities for registering blocks and attributes.
 *
 * @author OnPoint Plugins
 * @since  9.7.0
 *
 * @phpstan-import-type ATTR_SHAPE from Block_Abstract
 * @phpstan-import-type JS_ATTR_SHAPE from JS_Attribute
 */
class Register_Utils {
	use Singleton;

	/**
	 * Convert an array of attributes to PHP attributes.
	 *
	 * @template T of string
	 *
	 * @phpstan-param array<T, ATTR_SHAPE|Attribute> $attributes
	 *
	 * @param array<string, ATTR_SHAPE|Attribute>    $attributes - Array of attributes.
	 *
	 * @phpstan-return array<T, ATTR_SHAPE>
	 * @return array<string, ATTR_SHAPE> - Array of PHP-shaped attributes.
	 */
	public function translate_attributes_to_php( array $attributes ): array {
		return \array_map( function( $attribute ) {
			if ( $attribute instanceof Attribute ) {
				return $attribute->to_php_attribute();
			}
			return $attribute;
		}, $attributes );
	}


	/**
	 * Convert an array of attributes to JavaScript attributes.
	 *
	 * @template T of string
	 *
	 * @phpstan-param array<T, ATTR_SHAPE|Attribute> $attributes
	 *
	 * @param array<string, ATTR_SHAPE|Attribute>    $attributes - Array of attributes.
	 *
	 * @phpstan-return array<T, JS_ATTR_SHAPE>
	 * @return array<string, JS_ATTR_SHAPE> - Array of JavaScript-shaped attributes.
	 */
	public function translate_attributes_to_js( array $attributes ): array {
		return \array_map( function( $attribute ) {
			if ( $attribute instanceof Attribute ) {
				return JS_Attribute::from( $attribute )->to_js_attribute();
			}
			return JS_Attribute::from( Attribute::factory( $attribute ) )->to_js_attribute();
		}, $attributes );
	}
}

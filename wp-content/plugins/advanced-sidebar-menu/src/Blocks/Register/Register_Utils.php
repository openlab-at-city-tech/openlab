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
}

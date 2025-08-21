<?php

namespace Advanced_Sidebar_Menu\Blocks\Register;

use Advanced_Sidebar_Menu\Blocks\Block_Abstract;

/**
 * Rules an Attribute class must follow.
 *
 * @note   Do not change this interface without bumping a major version.
 *
 * @author OnPoint Plugins
 * @since  9.7.0
 *
 * @phpstan-import-type ATTR_SHAPE from Block_Abstract
 */
interface AttributeRules {
	public const TYPE_ARRAY   = 'array';
	public const TYPE_BOOLEAN = 'boolean';
	public const TYPE_INTEGER = 'integer';
	public const TYPE_NULL    = 'null';
	public const TYPE_NUMBER  = 'number';
	public const TYPE_OBJECT  = 'object';
	public const TYPE_STRING  = 'string';


	/**
	 * Define the type of the property.
	 *
	 * @link     https://developer.wordpress.org/block-editor/reference-guides/block-api/block-attributes/#type-validation
	 *
	 * @phpstan-param self::TYPE_*|(list<self::TYPE_*>) $type
	 *
	 * @formatter:off
	 *
	 * @param string|array $type - Data type of the property.
	 *
	 * @formatter:on
	 *
	 * @return static
	 */
	public function type( $type );


	/**
	 * Set the default value for this attribute.
	 *
	 * @param mixed $value Default value.
	 *
	 * @return static
	 */
	public function default( $value );


	/**
	 * Set the enum values for this attribute.
	 *
	 * @param list<string|int|bool> $values Enum values.
	 *
	 * @return static
	 */
	public function enum( array $values );


	/**
	 * Convert the attribute to a PHP array format.
	 *
	 * @phpstan-return ATTR_SHAPE
	 *
	 * @return array - Attribute is a standard array format.
	 */
	public function to_php_attribute(): array;


	/**
	 * Load the attribute.
	 *
	 * @phpstan-param ATTR_SHAPE $attribute
	 *
	 * @param array              $attribute - Attribute is a standard array format.
	 *
	 * @return static
	 */
	public static function factory( array $attribute );
}

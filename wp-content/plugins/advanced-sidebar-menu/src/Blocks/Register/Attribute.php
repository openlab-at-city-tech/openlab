<?php

namespace Advanced_Sidebar_Menu\Blocks\Register;

use Advanced_Sidebar_Menu\Blocks\Block_Abstract;

/**
 * Work with a block attribute in an OOP way.
 *
 * @author OnPoint Plugins
 * @since  9.7.0
 *
 * @phpstan-import-type ATTR_SHAPE from Block_Abstract
 *
 */
class Attribute implements AttributeRules, \JsonSerializable {
	/**
	 * Data type of the property.
	 *
	 * @phpstan-var self::TYPE_*|(list<self::TYPE_*>)
	 * @var string|array
	 */
	public $type;

	/**
	 * List of possible values for the property.
	 *
	 * @var list<string|int|bool>
	 */
	public array $enum;

	/**
	 * Default value for this attribute.
	 *
	 * @phsptan-var string|int|bool|array|object|null
	 * @var mixed
	 */
	public $default;


	/**
	 * Load the attribute.
	 *
	 * @phpstan-param ATTR_SHAPE $attribute
	 *
	 * @param array              $attribute - Attribute is a standard array format.
	 */
	final protected function __construct( array $attribute ) {
		$this->type( $attribute['type'] );

		if ( isset( $attribute['enum'] ) ) {
			$this->enum( $attribute['enum'] );
		}
		if ( isset( $attribute['default'] ) ) {
			$this->default( $attribute['default'] );
		}
	}


	/**
	 * Define the type of the property.
	 *
	 * @link     https://developer.wordpress.org/block-editor/reference-guides/block-api/block-attributes/#type-validation
	 *
	 * @phpstan-param self::TYPE_*|(list<self::TYPE_*>) $type
	 *
	 * @formatter:off
	 *
	 * @param string|array $type - Block JSON attribute type.
	 *
	 * @formatter:on
	 *
	 * @return static
	 */
	public function type( $type ) {
		$this->type = $type;
		return $this;
	}


	/**
	 * Define the list of possible values for the property.
	 *
	 * @link https://developer.wordpress.org/block-editor/reference-guides/block-api/block-attributes/#enum-validation
	 *
	 * @param list<string|int|bool> $values - List of possible values for this attribute.
	 *
	 * @return static
	 */
	public function enum( array $values ) {
		$allowed = \array_intersect( (array) $this->type, [ self::TYPE_STRING, self::TYPE_INTEGER ] );
		foreach ( $values as $item ) {
			$comparison = fn( $result, $type ) => $result || $this->is_value_of_type( $item, $type );
			if ( true !== \array_reduce( $allowed, $comparison, false ) ) {
				// translators: %s is the type(s) of the attribute.
				_doing_it_wrong( __METHOD__, esc_html( \sprintf( __( 'Enum values must be of type %s.', 'advanced-sidebar-menu' ), \implode( ',', (array) $this->type ) ) ), '9.7.0' );
			}
		}

		$this->enum = $values;
		return $this;
	}


	/**
	 * Default value for this attribute.
	 *
	 * @link https://developer.wordpress.org/block-editor/reference-guides/block-api/block-attributes/#default-value
	 *
	 * @param mixed $value - Default value for this attribute.
	 *
	 * @return static
	 */
	public function default( $value ) {
		$comparison = fn( $result, $type ) => $result || $this->is_value_of_type( $value, $type );
		if ( true !== \array_reduce( (array) $this->type, $comparison, false ) ) {
			// translators: %s is the type of the attribute.
			_doing_it_wrong( __METHOD__, esc_html( \sprintf( __( 'The default value must be of type %s.', 'advanced-sidebar-menu' ), \implode( ',', (array) $this->type ) ) ), '9.7.0' );
		}

		$this->default = $value;
		return $this;
	}


	/**
	 * Convert the attribute to a PHP array format.
	 *
	 * @phpstan-return ATTR_SHAPE
	 *
	 * @return array - Attribute is a standard array format.
	 */
	public function to_array(): array {
		$attribute = [
			'type' => $this->type,
		];
		if ( isset( $this->default ) ) {
			$attribute['default'] = $this->default;
		}
		if ( isset( $this->enum ) ) {
			$attribute['enum'] = $this->enum;
		}
		return $attribute;
	}


	/**
	 * Check if a value is of the specified type.
	 *
	 * @param mixed  $value The value to check.
	 * @param string $type  The type to check against.
	 *
	 * @return bool Whether the value matches the type.
	 */
	protected function is_value_of_type( $value, string $type ): bool {
		// @phpstan-ignore lipemat.noSwitch (switch to match when PHP 8.0+ is required)
		switch ( true ) {
			case self::TYPE_STRING === $type:
				return \is_string( $value );
			case self::TYPE_NUMBER === $type:
			case self::TYPE_INTEGER === $type:
				return \is_int( $value );
			case self::TYPE_BOOLEAN === $type:
				return \is_bool( $value );
			case self::TYPE_ARRAY === $type:
			case self::TYPE_OBJECT === $type:
				return \is_array( $value );
			default:
				return false;
		}
	}


	/**
	 * Convert the attribute to JSON format.
	 *
	 * @phpstan-return ATTR_SHAPE
	 *
	 * @return array - Attribute is a standard array format.
	 */
	public function jsonSerialize(): array {
		return $this->to_array();
	}


	/**
	 * Create a new instance of the attribute.
	 *
	 * @phpstan-param ATTR_SHAPE $attribute
	 *
	 * @param array              $attribute - Attribute is a standard array format.
	 *
	 * @return static
	 */
	public static function factory( array $attribute ) {
		return new static( $attribute );
	}
}

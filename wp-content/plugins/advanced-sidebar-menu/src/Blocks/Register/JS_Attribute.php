<?php
//phpcs:disable PHPCompatibility.Classes.NewClasses.attributeFound, Universal.Classes.DisallowFinalClass.FinalClassFound,LipePlugin.CodeAnalysis.PrivateInClass.Found

namespace Advanced_Sidebar_Menu\Blocks\Register;

/**
 * Block attribute compressed to pass to JavaScript.
 *
 * Locked down to final and private to guarantee the signature between PHP
 * and JavaScript is not disrupted.
 *
 * @author OnPoint Plugins
 * @since  9.7.0
 *
 * @phpstan-type JS_ATTR_SHAPE array{
 *     t: JS_Attribute::TYPE_*|list<JS_Attribute::TYPE_*>,
 *     d?: 'string'|'int'|'bool'|'array'|'object'|null,
 *     e?: list<string|int|bool>,
 * }
 *
 */
final class JS_Attribute {
	public const TYPE_ARRAY   = 'a';
	public const TYPE_BOOLEAN = 'b';
	public const TYPE_INTEGER = 'i';
	public const TYPE_NULL    = 'u';
	public const TYPE_NUMBER  = 'n';
	public const TYPE_OBJECT  = 'o';
	public const TYPE_STRING  = 's';

	/**
	 * Block register attribute.
	 *
	 * @var Attribute
	 */
	private Attribute $attribute;


	/**
	 * JS_Attribute constructor.
	 *
	 * @param Attribute $attribute - Block attribute.
	 */
	private function __construct( Attribute $attribute ) {
		$this->attribute = $attribute;
	}


	/**
	 * Transform the type of the attribute to a shortcut.
	 *
	 * @phpstan-return self::TYPE_*|(list<self::TYPE_*>)
	 * @return array|string
	 */
	private function type() {
		if ( \is_array( $this->attribute->type ) ) {
			return \array_map( function( $type ) {
				return $this->get_type_shortcut( $type );
			}, $this->attribute->type );
		}

		return $this->get_type_shortcut( $this->attribute->type );
	}


	/**
	 * Get the type shortcut for the attribute.
	 *
	 * @phpstan-param Attribute::TYPE_* $type
	 *
	 * @param string                    $type - Block JSON attribute type.
	 *
	 * @return self::TYPE_*
	 */
	private function get_type_shortcut( string $type ): string {
		// @phpstan-ignore lipemat.noSwitch (Convert to match when PHP 8.0 is minimum)
		switch ( true ) {
			case AttributeRules::TYPE_ARRAY === $type:
				return self::TYPE_ARRAY;
			case AttributeRules::TYPE_OBJECT === $type:
				return self::TYPE_OBJECT;
			case AttributeRules::TYPE_BOOLEAN === $type:
				return self::TYPE_BOOLEAN;
			case AttributeRules::TYPE_INTEGER === $type:
				return self::TYPE_INTEGER;
			case AttributeRules::TYPE_NUMBER === $type:
				return self::TYPE_NUMBER;
			case AttributeRules::TYPE_NULL === $type:
				return self::TYPE_NULL;
			default:
				return self::TYPE_STRING;
		}
	}


	/**
	 * Convert the attribute to a JavaScript array format.
	 *
	 * @phpstan-return JS_ATTR_SHAPE
	 * @return array
	 */
	public function to_js_attribute(): array {
		$attributes = [
			't' => $this->type(),
		];
		if ( isset( $this->attribute->default ) ) {
			$attributes['d'] = $this->attribute->default;
		}
		if ( isset( $this->attribute->enum ) ) {
			$attributes['e'] = $this->attribute->enum;
		}
		return $attributes;
	}


	/**
	 * Create a new JS_Attribute instance from an Attribute.
	 *
	 * @param Attribute $attribute - Block attribute.
	 *
	 * @return static
	 */
	public static function from( Attribute $attribute ): self {
		return new self( $attribute );
	}
}

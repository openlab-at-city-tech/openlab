<?php
/**
 * @license GPL-3.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace TEC\Common\StellarWP\Models\Contracts;

use RuntimeException;

interface Model {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string,mixed> $attributes Attributes.
	 */
	public function __construct( array $attributes = [] );

	/**
	 * Fills the model with an array of attributes.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string,mixed> $attributes Attributes.
	 *
	 * @return Model
	 */
	public function fill( array $attributes ) : Model;

	/**
	 * Returns an attribute from the model.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Attribute name.
	 *
	 * @return mixed
	 *
	 * @throws RuntimeException
	 */
	public function getAttribute( string $key );

	/**
	 * Returns the attributes that have been changed since last sync.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function getDirty() : array;

	/**
	 * Returns the model's original attribute values.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $key Attribute name.
	 *
	 * @return mixed|array
	 */
	public function getOriginal( string $key = null );

	/**
	 * Determines if the model has the given property.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Property name.
	 *
	 * @return bool
	 */
	public function hasProperty( string $key ) : bool;

	/**
	 * Determines if a given attribute is clean.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $attribute Attribute name.
	 *
	 * @return bool
	 */
	public function isClean( string $attribute = null ) : bool;

	/**
	 * Determines if a given attribute is dirty.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $attribute Attribute name.
	 *
	 * @return bool
	 */
	public function isDirty( string $attribute = null ) : bool;

	/**
	 * Validates an attribute to a PHP type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key   Attribute name.
	 * @param mixed  $value Attribute value.
	 *
	 * @return bool
	 */
	public function isPropertyTypeValid( string $key, $value ) : bool;

	/**
	 * Returns the property keys.
	 *
	 * @since 1.0.0
	 *
	 * @return int[]|string[]
	 */
	public static function propertyKeys() : array;

	/**
	 * Sets an attribute on the model.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key   Attribute name.
	 * @param mixed  $value Attribute value.
	 *
	 * @return Model
	 */
	public function setAttribute( string $key, $value ) : Model;

	/**
	 * Syncs the original attributes with the current.
	 *
	 * @since 1.0.0
	 *
	 * @return Model
	 */
	public function syncOriginal() : Model;

	/**
	 * Dynamically retrieves attributes on the model.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Attribute name.
	 *
	 * @return mixed
	 */
	public function __get( string $key );

	/**
	 * Determines if an attribute exists on the model.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Attribute name.
	 *
	 * @return bool
	 */
	public function __isset( string $key );

	/**
	 * Dynamically sets attributes on the model.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key   Attribute name.
	 * @param mixed  $value Attribute value.
	 *
	 * @return void
	 */
	public function __set( string $key, $value );
}

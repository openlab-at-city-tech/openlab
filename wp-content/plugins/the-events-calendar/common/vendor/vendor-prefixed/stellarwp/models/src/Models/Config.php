<?php

namespace TEC\Common\StellarWP\Models;

use InvalidArgumentException;
use TEC\Common\StellarWP\Models\Exceptions\ReadOnlyPropertyException;
use Throwable;

class Config {
	/**
	 * @var ?string
	 */
	protected static $hookPrefix;

	/**
	 * @var class-string<Throwable>
	 */
	protected static $invalidArgumentException = InvalidArgumentException::class;

	/**
	 * @var class-string<ReadOnlyPropertyException>
	 */
	protected static $readOnlyPropertyException = ReadOnlyPropertyException::class;

	/**
	 * Gets the hook prefix.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function getHookPrefix(): string {
		if ( ! static::$hookPrefix ) {
			throw new \RuntimeException(
				sprintf(
					'You must provide a hook prefix via %1$s before using the stellarwp/models library.',
					__CLASS__ . '::setHookPrefix()'
				)
			);
		}

		return static::$hookPrefix;
	}

	/**
	 * Gets the InvalidArgumentException class.
	 *
	 * @since 1.0.0
	 *
	 * @return class-string<Throwable>
	 */
	public static function getInvalidArgumentException(): string {
		return static::$invalidArgumentException;
	}

	/**
	 * Gets the ReadOnlyPropertyException class.
	 *
	 * @since 2.0.0
	 *
	 * @return class-string<ReadOnlyPropertyException>
	 */
	public static function getReadOnlyPropertyException(): string {
		return static::$readOnlyPropertyException;
	}

	/**
	 * Resets the class back to default.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function reset() {
		static::$hookPrefix = null;
		static::$invalidArgumentException = InvalidArgumentException::class;
		static::$readOnlyPropertyException = ReadOnlyPropertyException::class;
	}

	/**
	 * Sets the hook prefix.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook_prefix
	 *
	 * @return void
	 */
	public static function setHookPrefix( string $hook_prefix ) {
		if ( ! empty( static::$hookPrefix ) ) {
			throw new \RuntimeException(
				sprintf(
					'The %1$s has already been called and set to %2$s.',
					__CLASS__ . '::setHookPrefix()',
					static::$hookPrefix
				)
			);
		}

		$sanitized_prefix = preg_replace( '/[^a-z0-9_-]/', '', $hook_prefix );

		if ( $sanitized_prefix !== $hook_prefix ) {
			throw new \InvalidArgumentException( 'Hook prefix must only contain lowercase letters, numbers, "_", or "-".' );
		}

		static::$hookPrefix = $hook_prefix;
	}

	/**
	 * Allow for overriding the InvalidArgumentException class.
	 *
	 * @since 1.0.0
	 *
	 * @param string $class
	 *
	 * @return void
	 */
	public static function setInvalidArgumentException( string $class ) {
		if ( ! is_a( $class, InvalidArgumentException::class, true ) ) {
			throw new \InvalidArgumentException( 'The provided InvalidArgumentException class must be or must extend ' . InvalidArgumentException::class . '.' );
		}

		static::$invalidArgumentException = $class;
	}

	/**
	 * Allow for overriding the ReadOnlyPropertyException class.
	 *
	 * @since 2.0.0
	 *
	 * @param string $class
	 *
	 * @return void
	 */
	public static function setReadOnlyPropertyException( string $class ) {
		if ( ! is_a( $class, ReadOnlyPropertyException::class, true ) ) {
			throw new \InvalidArgumentException( 'The provided ReadOnlyPropertyException class must be or must extend ' . ReadOnlyPropertyException::class . '.' );
		}

		static::$readOnlyPropertyException = $class;
	}

	/**
	 * Convenience method for throwing the InvalidArgumentException.
	 *
	 * @since 2.0.0
	 *
	 * @param string $message
	 *
	 * @return never
	 * @throws Throwable
	 */
	public static function throwInvalidArgumentException( string $message ): void {
		$exceptionClass = static::$invalidArgumentException;
		throw new $exceptionClass( $message );
	}

	/**
	 * Convenience method for throwing the ReadOnlyPropertyException.
	 *
	 * @since 2.0.0
	 *
	 * @param ModelProperty $property
	 * @param string        $message
	 *
	 * @return never
	 * @throws ReadOnlyPropertyException
	 */
	public static function throwReadOnlyPropertyException( ModelProperty $property, string $message ): void {
		$exceptionClass = static::$readOnlyPropertyException;
		throw new $exceptionClass( $property, $message );
	}
}

<?php

namespace PDFEmbedder\Helpers;

use BadMethodCallException;
use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Trait Macroable allows to add static methods to any class.
 * Copied from Illuminate\Support\Traits\Macroable.
 *
 * @since 4.7.0
 */
trait Macroable {

	/**
	 * The registered string macros.
	 *
	 * @since 4.7.0
	 *
	 * @var array
	 */
	protected static $macros = [];

	/**
	 * Register a custom macro.
	 *
	 * @since 4.7.0
	 *
	 * @param string          $name  Macro name.
	 * @param object|callable $macro Macro executable code.
	 *
	 * @return void
	 */
	public static function macro( string $name, $macro ) {

		static::$macros[ $name ] = $macro;
	}

	/**
	 * Mix another object into the class.
	 *
	 * @since 4.7.0
	 *
	 * @param object $mixin   Object to mix in.
	 * @param bool   $replace Whether to replace the macro.
	 *
	 * @throws ReflectionException When reflection can't process the mixin.
	 */
	public static function mixin( $mixin, bool $replace = true ) {

		$methods = ( new ReflectionClass( $mixin ) )->getMethods(
			ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED
		);

		foreach ( $methods as $method ) {
			if ( $replace || ! static::has_macro( $method->name ) ) {
				static::macro( $method->name, $method->invoke( $mixin ) );
			}
		}
	}

	/**
	 * Checks if macro is registered.
	 *
	 * @since 4.7.0
	 *
	 * @param string $name Macro name.
	 */
	public static function has_macro( string $name ): bool {

		return isset( static::$macros[ $name ] );
	}

	/**
	 * Flush the existing macros.
	 *
	 * @since 4.7.0
	 */
	public static function flush_macros() {

		static::$macros = [];
	}

	/**
	 * Dynamically handle calls to the class.
	 *
	 * @since 4.7.0
	 *
	 * @param string $method     Method name that was called.
	 * @param array  $parameters Method parameters that were passed to it.
	 *
	 * @return mixed
	 *
	 * @throws BadMethodCallException When method does not exist.
	 */
	public static function __callStatic( string $method, array $parameters ) {

		if ( ! static::has_macro( $method ) ) {
			throw new BadMethodCallException(
                sprintf(
                    'Method %s::%s does not exist.',
                    static::class,
					$method // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
                )
            );
		}

		$macro = static::$macros[ $method ];

		if ( $macro instanceof Closure ) {
			$macro = $macro->bindTo( null, static::class );
		}

		return $macro( ...$parameters );
	}

	/**
	 * Dynamically handle calls to the class.
	 *
	 * @since 4.7.0
	 *
	 * @param string $method     Method name that was called.
	 * @param array  $parameters Method parameters that were passed to it.
	 *
	 * @return mixed
	 *
	 * @throws BadMethodCallException When method does not exist.
	 */
	public function __call( string $method, array $parameters ) {

		if ( ! static::has_macro( $method ) ) {
			throw new BadMethodCallException(
                sprintf(
                    'Method %s::%s does not exist.',
                    static::class,
                    $method // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
                )
            );
		}

		$macro = static::$macros[ $method ];

		if ( $macro instanceof Closure ) {
			$macro = $macro->bindTo( $this, static::class );
		}

		return $macro( ...$parameters );
	}
}

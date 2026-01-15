<?php

declare(strict_types=1);

namespace TEC\Common\StellarWP\Models\Exceptions;

use RuntimeException;
use TEC\Common\StellarWP\Models\ModelProperty;
use Throwable;

/**
 * Exception thrown when attempting to modify a readonly property.
 *
 * @since 2.0.0
 */
class ReadOnlyPropertyException extends RuntimeException {
	/**
	 * The property that caused the exception.
	 *
	 * @since 2.0.0
	 *
	 * @var ModelProperty
	 */
	private $property;

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param ModelProperty  $property
	 * @param string         $message
	 * @param int            $code
	 * @param Throwable|null $previous
	 */
	public function __construct( ModelProperty $property, string $message = '', int $code = 0, Throwable $previous = null ) {
		parent::__construct( $message, $code, $previous );
		$this->property = $property;
	}

	/**
	 * Get the property that caused the exception.
	 *
	 * @since 2.0.0
	 *
	 * @return ModelProperty
	 */
	public function getProperty(): ModelProperty {
		return $this->property;
	}
}

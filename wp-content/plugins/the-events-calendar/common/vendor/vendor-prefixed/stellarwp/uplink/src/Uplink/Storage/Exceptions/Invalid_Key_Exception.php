<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */ declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Storage\Exceptions;

use InvalidArgumentException;

/**
 * Thrown when a Storage object is passed an invalid key.
 */
final class Invalid_Key_Exception extends InvalidArgumentException {

}

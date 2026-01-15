<?php

/**
 * Shepherd's functions.
 *
 * @package \StellarWP\Shepherd
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Shepherd;

use RuntimeException;
use TEC\Common\StellarWP\Shepherd\Config;
/**
 * Get the Shepherd's Regulator instance.
 *
 * @since 0.0.1
 *
 * @return Regulator The Shepherd's regulator.
 *
 * @throws RuntimeException If Shepherd is not registered.
 */
function shepherd(): Regulator
{
    if (!Provider::is_registered()) {
        throw new RuntimeException('Shepherd is not registered.');
    }
    static $shepherd = null;
    if (null !== $shepherd) {
        return $shepherd;
    }
    $shepherd = Config::get_container()->get(Regulator::class);
    return $shepherd;
}
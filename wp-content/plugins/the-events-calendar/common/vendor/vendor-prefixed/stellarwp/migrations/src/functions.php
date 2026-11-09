<?php

/**
 * Migrations's functions.
 *
 * @package \StellarWP\Migrations
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Migrations;

/**
 * Get the Migrations's Provider instance.
 *
 * @since 0.0.1
 *
 * @return Provider The Migrations's Provider.
 */
function migrations(): Provider
{
    /** @var Provider|null $migrations */
    static $migrations = null;
    if (null !== $migrations) {
        return $migrations;
    }
    $migrations = Config::get_container()->get(Provider::class);
    return $migrations;
}
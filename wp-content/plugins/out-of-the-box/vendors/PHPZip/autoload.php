<?php
namespace PHPZip;

/**
 * @internal
 */
function autoload($name)
{
    // If the name doesn't start with "PHPZip\", then its not once of our classes.
    if (\substr_compare($name, "PHPZip\\", 0, 6) !== 0) return;

    // Take the "PHPZip\" prefix off.
    $stem = \substr($name, 6);

    // Convert "\" and "_" to path separators.
    $pathified_stem = \str_replace(array("\\", "_"), '/', $stem);

    $path = __DIR__ . "/" . $pathified_stem . ".php";
    if (\is_file($path)) {
        require_once $path;
    }
}

\spl_autoload_register('PHPZip\autoload');

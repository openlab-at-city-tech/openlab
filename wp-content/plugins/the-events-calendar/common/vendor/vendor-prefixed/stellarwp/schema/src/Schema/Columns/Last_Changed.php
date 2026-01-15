<?php

/**
 * The interface for the last changed column.
 *
 * @since 3.0.0
 *
 * @package \TEC\Common\StellarWP\Schema\Columns
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Schema\Columns;

use TEC\Common\StellarWP\Schema\Columns\Datetime_Column;
/**
 * Class Last_Changed
 *
 * @since 3.0.0
 *
 * @package \TEC\Common\StellarWP\Schema\Columns
 */
class Last_Changed extends Datetime_Column
{
    /**
     * The default value of the column.
     *
     * @var string
     */
    protected $default = 'CURRENT_TIMESTAMP';
    /**
     * The on update value of the column.
     *
     * @var ?string
     */
    protected ?string $on_update = 'CURRENT_TIMESTAMP';
}
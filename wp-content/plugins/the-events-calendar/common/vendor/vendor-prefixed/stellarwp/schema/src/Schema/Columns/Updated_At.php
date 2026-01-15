<?php

/**
 * The interface for the updated at column.
 *
 * @since 3.0.0
 *
 * @package \TEC\Common\StellarWP\Schema\Columns
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Schema\Columns;

use TEC\Common\StellarWP\Schema\Columns\Datetime_Column;
/**
 * Class Updated_At
 *
 * @since 3.0.0
 *
 * @package \TEC\Common\StellarWP\Schema\Columns
 */
class Updated_At extends Datetime_Column
{
    /**
     * Whether the column can be null.
     *
     * @var bool
     */
    protected bool $nullable = true;
    /**
     * The on update value of the column.
     *
     * @var ?string
     */
    protected ?string $on_update = 'CURRENT_TIMESTAMP';
}
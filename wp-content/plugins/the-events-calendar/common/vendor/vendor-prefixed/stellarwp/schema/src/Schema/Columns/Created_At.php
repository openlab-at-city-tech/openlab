<?php

/**
 * The interface for the created at column.
 *
 * @since 3.0.0
 *
 * @package \TEC\Common\StellarWP\Schema\Columns
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Schema\Columns;

use TEC\Common\StellarWP\Schema\Columns\Datetime_Column;
/**
 * Class Created_At
 *
 * @since 3.0.0
 *
 * @package \TEC\Common\StellarWP\Schema\Columns
 */
class Created_At extends Datetime_Column
{
    /**
     * The default value of the column.
     *
     * @var string
     */
    protected $default = 'CURRENT_TIMESTAMP';
}
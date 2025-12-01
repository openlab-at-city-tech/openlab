<?php

/**
 * The class for the primary key.
 *
 * @since 3.0.0
 *
 * @package \TEC\Common\StellarWP\Schema\Indexes
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Schema\Indexes;

use TEC\Common\StellarWP\Schema\Indexes\Contracts\Abstract_Index;
/**
 * Class Primary_Key
 *
 * @since 3.0.0
 *
 * @package \TEC\Common\StellarWP\Schema\Indexes
 */
class Primary_Key extends Abstract_Index
{
    /**
     * The type of the primary key.
     *
     * @var string
     */
    protected string $type = self::TYPE_PRIMARY;
}
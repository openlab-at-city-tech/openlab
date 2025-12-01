<?php

/**
 * The class for the classic index.
 *
 * @since 3.0.0
 *
 * @package \TEC\Common\StellarWP\Schema\Indexes
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Schema\Indexes;

use TEC\Common\StellarWP\Schema\Indexes\Contracts\Abstract_Index;
/**
 * Class Classic_Index
 *
 * @since 3.0.0
 *
 * @package \TEC\Common\StellarWP\Schema\Indexes
 */
class Classic_Index extends Abstract_Index
{
    /**
     * The type of the classic index.
     *
     * @var string
     */
    protected string $type = self::TYPE_INDEX;
}
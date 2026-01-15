<?php

/**
 * Abstract for Custom Tables.
 *
 * @since TDB
 *
 * @package \TEC\Common\StellarWP\Shepherd\Abstracts
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Shepherd\Abstracts;

use TEC\Common\StellarWP\Schema\Tables\Contracts\Table;
use TEC\Common\StellarWP\DB\DB;
use TEC\Common\StellarWP\Shepherd\Config;
use TEC\Common\StellarWP\Shepherd\Tables\Utility\Safe_Dynamic_Prefix;
/**
 * Class Table_Abstract
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Shepherd\Abstracts
 */
abstract class Table_Abstract extends Table
{
    /**
     * Constructor.
     *
     * @since 0.0.1
     */
    public function __construct()
    {
        $this->db = DB::class;
        $this->container = Config::get_container();
    }
    /**
     * Returns the base table name.
     *
     * This method is overridden to use the hook prefix.
     *
     * @since 0.0.1
     *
     * @return string The base table name.
     */
    public static function base_table_name(): string
    {
        $container = Config::get_container();
        return sprintf(static::$base_table_name, $container->get(Safe_Dynamic_Prefix::class)->get());
    }
    /**
     * The schema slug.
     *
     * This method is overridden to use the hook prefix.
     *
     * @since 0.0.1
     *
     * @return string The schema slug.
     */
    public static function get_schema_slug(): string
    {
        return sprintf(static::$schema_slug, Config::get_hook_prefix());
    }
    /**
     * Returns the base table name without the dynamic prefix.
     *
     * @since 0.0.1
     *
     * @return string The base table name without the dynamic prefix.
     */
    public static function raw_base_table_name(): string
    {
        return static::$base_table_name;
    }
}
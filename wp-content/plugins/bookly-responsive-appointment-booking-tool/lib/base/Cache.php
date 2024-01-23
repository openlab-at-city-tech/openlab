<?php
namespace Bookly\Lib\Base;

abstract class Cache
{
    /** @var array */
    protected static $cache = array();

    /**
     * Check whether key exists in cache.
     *
     * @param string $key
     * @return bool
     */
    public static function hasInCache( $key )
    {
        $called_class = get_called_class();

        return isset ( self::$cache[ $called_class ] ) && array_key_exists( $key, self::$cache[ $called_class ] );
    }

    /**
     * Put in cache.
     *
     * @param string $key
     * @param mixed $value
     */
    public static function putInCache( $key, $value )
    {
        $called_class = get_called_class();

        self::$cache[ $called_class ][ $key ] = $value;
    }

    /**
     * Get from cache.
     *
     * @param string $key
     * @param null   $default
     * @return mixed
     */
    public static function getFromCache( $key, $default = null )
    {
        $called_class = get_called_class();

        if ( static::hasInCache( $key ) ) {
            return self::$cache[ $called_class ][ $key ];
        }

        return $default;
    }

    /**
     * Delete from cache.
     *
     * @param string $key
     */
    public static function deleteFromCache( $key )
    {
        $called_class = get_called_class();

        unset ( self::$cache[ $called_class ][ $key ] );
    }

    /**
     * Clear all data from cache.
     */
    public static function clearCache()
    {
        $called_class = get_called_class();
        unset ( self::$cache[ $called_class ] );
    }

    /**
     * Drop cache.
     */
    public static function dropCache()
    {
        self::$cache = array();
    }
}
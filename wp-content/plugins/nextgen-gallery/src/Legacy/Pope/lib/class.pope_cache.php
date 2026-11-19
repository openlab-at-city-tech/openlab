<?php

class C_Pope_Cache
{
	static $_instance = NULL;
    static $_driver = NULL;
    static $key_prefix = array('pope');
	static $enabled = TRUE; // the cache is not used at all
	static $do_not_lookup = FALSE; // no lookups are made to the cache
	static $force_update = FALSE; // force the cache to be updated
	protected $_queue = array();

	static function &get_instance($driver='C_Pope_Cache_Memory')
	{
		if (!isset(self::$_instance))
            self::$_instance = new C_Pope_Cache($driver);

		return self::$_instance;
	}

    function __construct($driver = 'C_Pope_Cache_Memory')
    {
        if (is_null(self::$_driver))
            self::set_driver($driver);
    }

    static function set_driver($class_name)
    {
        self::$_driver = $class_name;
    }

    static function add_key_prefix($prefix)
    {
        self::$key_prefix[] = $prefix;
        $driver = self::$_driver;
	    return call_user_func("{$driver}::add_key_prefix", $prefix);
    }

	static function get($params, $default=NULL)
	{
		if (!self::$enabled)
            return $default;
		$cache = self::get_instance();
		$key = $cache->get_key_from_params($params);
		if (self::$do_not_lookup)
            return $default;
		else
            return $cache->lookup($key, $default);
	}

	static function set($params, $value)
	{
		if (self::$enabled)
        {
			$cache = self::get_instance();
			$key   = $cache->get_key_from_params($params);
			$cache->update($key, $value);
		}
	}

	function get_key_from_params($params)
	{
        return md5(json_encode($params));
	}

    function lookup($key, $default=NULL)
    {
        $driver = self::$_driver;
	    return call_user_func("{$driver}::lookup", $key, $default);
    }

    function update($key, $value)
    {
        $driver = self::$_driver;
	    return call_user_func("{$driver}::update", $key, $value);
    }

    function flush()
    {
        $driver = self::$_driver;
	    return call_user_func("{$driver}::flush");
    }

}

interface I_Pope_Cache_Driver
{
    public static function add_key_prefix($prefix);
    public static function flush();
    public static function lookup($key, $default = NULL);
    public static function update($key, $value);
}

/**
 * The default driver: keeps entries in memory for the current request only.
 *
 * POPE used to persist this cache in one file in the system temp directory
 * (`sys_get_temp_dir()/pope_cache`, or POPE_CACHE_DIR when defined). That file was keyed
 * only by module ids and versions and was shared by every site and system user on the host,
 * so one install could read the cache, and with it the module file paths, that another
 * install had written (nextgen-gallery#1248). Rebuilding the cache per request costs about
 * a millisecond, on requests that boot POPE only. POPE_CACHE_DIR is now ignored.
 */
class C_Pope_Cache_Memory implements I_Pope_Cache_Driver
{
    static $cache = array();

    public static function add_key_prefix($prefix)
    {
    }

    public static function lookup($key, $default=NULL)
    {
        if (!empty(self::$cache[$key]))
            return self::$cache[$key];
        else
            return $default;
    }

    public static function update($key, $value)
    {
        self::$cache[$key] = $value;
    }

    public static function flush()
    {
        self::$cache = array();
    }
}

/**
 * @deprecated Kept (nextgen-gallery#1248) so code that names the class keeps working. It no longer touches
 *             the filesystem; it is the in-memory driver.
 */
class C_Pope_Cache_MultiFile extends C_Pope_Cache_Memory
{
    static $initialized      = FALSE;
    static $cache_dir        = NULL;
    static $use_cache_subdir = TRUE;

    public static function initialize()
    {
    }

    public static function set_cache_dir()
    {
    }

    public static function join_paths()
    {
        $args = func_get_args();
        foreach ($args as &$arg) {
            if (is_array($arg))
                $arg = implode(DIRECTORY_SEPARATOR, $arg);
        }
        return implode(DIRECTORY_SEPARATOR, $args);
    }
}

/**
 * @deprecated Kept (nextgen-gallery#1248) so code that names the class keeps working. It no longer reads or
 *             writes `pope_cache` in the temp directory; it is the in-memory driver.
 */
class C_Pope_Cache_SingleFile extends C_Pope_Cache_Memory
{
    static $initialized  = FALSE;
    static $cache_dir    = NULL;
    static $writepending = FALSE;

    public static function initialize()
    {
    }

    public static function set_cache_dir()
    {
    }

    public static function join_paths()
    {
        $args = func_get_args();
        foreach ($args as &$arg) {
            if (is_array($arg))
                $arg = implode(DIRECTORY_SEPARATOR, $arg);
        }
        return implode(DIRECTORY_SEPARATOR, $args);
    }
}

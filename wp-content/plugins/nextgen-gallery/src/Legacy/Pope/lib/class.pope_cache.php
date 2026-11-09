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

	static function &get_instance($driver='C_Pope_Cache_SingleFile')
	{
		if (!isset(self::$_instance))
            self::$_instance = new C_Pope_Cache($driver);

		return self::$_instance;
	}

    function __construct($driver = 'C_Pope_Cache_SingleFile')
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

class C_Pope_Cache_MultiFile implements I_Pope_Cache_Driver
{
    static $initialized     = FALSE;
    static $cache_dir        = NULL;
    static $use_cache_subdir = TRUE;

    public static function initialize()
    {
        if (self::$initialized)
            return;

        if (is_null(self::$cache_dir))
            self::set_cache_dir();

        self::$initialized = TRUE;
    }

    public static function add_key_prefix($prefix)
    {
        self::set_cache_dir();
    }

    public static function lookup($key, $default=NULL)
    {
        self::initialize();

        $filename = self::get_filename_from_key($key);

        if (@file_exists($filename))
            return json_decode(@file_get_contents($filename));
        else
            return $default;
    }

    public static function update($key, $value)
    {
        self::initialize();

        // TODO: log/warn users their cache dir can't be used
        if (@file_exists(self::$cache_dir) && !@is_dir(self::$cache_dir))
            return;

        $filename = self::get_filename_from_key($key);

        if (@file_exists($filename) && C_Pope_Cache::$force_update == FALSE)
            return;

        @file_put_contents($filename, json_encode($value));
    }

    public static function flush()
    {
        self::initialize();

        $dir = self::$cache_dir;
        if (@file_exists($dir) && @is_dir($dir))
        {
            foreach (@scandir($dir) as $file) {
                if ($file == '.' || $file == '..')
                    continue;
                $file = self::join_paths($dir, $file);
                if (is_dir($file) && self::$use_cache_subdir)
                {
                    self::flush($dir);
                }
                else {
                    if (!self::$use_cache_subdir && strpos(basename($file), implode('_', C_Pope_Cache::$key_prefix) . '_') === 0)
                        @unlink($file);
                    elseif (self::$use_cache_subdir)
                        @unlink($file);
                }
            }
        }
    }

    public static function set_cache_dir()
    {
        if (defined('POPE_CACHE_DIR'))
        {
            self::$cache_dir = POPE_CACHE_DIR;
            if (!@file_exists(self::$cache_dir))
                @mkdir(self::$cache_dir, 0777, TRUE);
        }
        else {
            if (self::$use_cache_subdir)
            {
                self::$cache_dir = self::join_paths(sys_get_temp_dir(), C_Pope_cache::$key_prefix);
                if (!@file_exists(self::$cache_dir))
                {
                    // if we can't create a subdirectory we fallback to prefixing filenames (eg /tmp/pope_$key)
                    $mkdir_result = @mkdir(self::$cache_dir, 0777, TRUE);
                    if (FALSE === $mkdir_result)
                    {
                        self::$use_cache_subdir = FALSE;
                        self::$cache_dir = self::join_paths(sys_get_temp_dir());
                    }
                }
            }
            else {
                self::$cache_dir = self::join_paths(sys_get_temp_dir());
            }
        }

        $func = function_exists('wp_is_writable') ? 'wp_is_writable' : 'is_writable';
        if (!@$func(self::$cache_dir))
            C_Pope_Cache::$enabled = FALSE;
    }

    public static function get_filename_from_key($key)
    {
        if (self::$use_cache_subdir)
            $filename = self::join_paths(self::$cache_dir, $key);
        else
            $filename = self::join_paths(self::$cache_dir) . DIRECTORY_SEPARATOR . implode('_', C_Pope_Cache::$key_prefix) . '_' . $key;
        return $filename;
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

function C_Pope_Cache_SingleFile_Shutdown()
{
    $filename = C_Pope_Cache_SingleFile::get_filename();
    @file_put_contents(
        $filename,
        json_encode(C_Pope_Cache_SingleFile::$cache)
    );
}

class C_Pope_Cache_SingleFile implements I_Pope_Cache_Driver
{
    static $initialized  = FALSE;
    static $cache_dir    = NULL;
    static $cache        = array();
    static $writepending = FALSE;

    public static function initialize()
    {
        if (self::$initialized)
            return;

        if (is_null(self::$cache_dir))
            self::set_cache_dir();

        $filename = self::get_filename();
        if (@file_exists($filename))
            self::$cache = json_decode(@file_get_contents($filename), TRUE);

        if (!is_array(self::$cache))
            self::$cache = array();

        register_shutdown_function('C_Pope_Cache_SingleFile_Shutdown');

        self::$initialized = TRUE;
    }

    public static function add_key_prefix($prefix)
    {
        self::set_cache_dir();
    }

    public static function lookup($key, $default=NULL)
    {
        self::initialize();

        if (!empty(self::$cache[$key]))
            return self::$cache[$key];
        else
            return $default;
    }

    public static function update($key, $value)
    {
        self::initialize();

        $dupe = FALSE;

        if (!empty(self::$cache[$key])) {
            if (self::$cache[$key] == $value)
                $dupe = TRUE;
        }

        if ($dupe == TRUE && C_Pope_Cache::$force_update == FALSE)
            return;

        self::$cache[$key] = $value;
        self::$writepending = TRUE;
    }

    public static function flush()
    {
        self::initialize();
        $filename = self::get_filename();
        if (@file_exists($filename))
            @unlink($filename);
    }

    public static function get_filename()
    {
        if (count(C_Pope_Cache::$key_prefix) == 1)
            C_Pope_Cache::add_key_prefix('cache');
        $filename = implode('_', C_Pope_Cache::$key_prefix);
        return self::join_paths(self::$cache_dir, $filename);
    }

    public static function set_cache_dir()
    {
        if (defined('POPE_CACHE_DIR'))
        {
            self::$cache_dir = POPE_CACHE_DIR;
            if (!@file_exists(self::$cache_dir))
                @mkdir(self::$cache_dir, 0777, TRUE);
        }
        else {
            self::$cache_dir = self::join_paths(sys_get_temp_dir());
        }

        $func = function_exists('wp_is_writable') ? 'wp_is_writable' : 'is_writable';
        if (!@$func(self::$cache_dir))
            C_Pope_Cache::$enabled = FALSE;
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

